<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Account;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Estatísticas gerais para o dashboard
     */
    public function dashboardStats()
    {
        $user = Auth::user();
        $userId = $user->id;

        // Total de contas
        $totalAccounts = Account::where('user_id', $userId)->count();

        // Saldo total
        $totalBalance = Account::where('user_id', $userId)->sum('balance');

        // Receitas e despesas do mês atual
        $currentMonth = now()->format('Y-m');
        $monthIncome = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$currentMonth])
            ->sum('amount');
        
        $monthExpenses = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$currentMonth])
            ->sum('amount');

        // Transações recentes
        $recentTransactions = Transaction::with(['account', 'category'])
            ->where('user_id', $userId)
            ->latest('date')
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_accounts' => $totalAccounts,
                'total_balance' => (float) $totalBalance,
                'month_income' => (float) $monthIncome,
                'month_expenses' => (float) $monthExpenses,
                'recent_transactions' => $recentTransactions,
                'net_worth_change' => (float) ($monthIncome - $monthExpenses)
            ]
        ]);
    }

    /**
     * Resumo de saldos de todas as contas
     */
    public function balanceSummary()
    {
        $accounts = Account::with('transactions')
            ->where('user_id', Auth::id())
            ->get()
            ->map(function ($account) {
                return [
                    'id' => $account->id,
                    'name' => $account->name,
                    'type' => $account->type,
                    'current_balance' => (float) $account->balance,
                    'last_transaction' => $account->transactions->sortByDesc('date')->first(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $accounts
        ]);
    }

    /**
     * Fluxo de caixa (entradas vs saídas)
     */
    public function cashFlowReport(Request $request)
    {
        $request->validate([
            'period' => 'sometimes|in:monthly,quarterly,yearly',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from'
        ]);

        $period = $request->input('period', 'monthly');
        $userId = Auth::id();

        if ($request->has('date_from') && $request->has('date_to')) {
            $dateFrom = Carbon::parse($request->date_from);
            $dateTo = Carbon::parse($request->date_to);
        } else {
            // Padrão: últimos 12 meses
            $dateTo = now();
            $dateFrom = now()->subYear();
        }

        $report = [];
        $currentPeriod = $dateFrom->copy();

        while ($currentPeriod <= $dateTo) {
            $label = $currentPeriod->format($period === 'yearly' ? 'Y' : ($period === 'quarterly' ? 'Y-m' : 'Y-m'));

            if (!isset($report[$label])) {
                $report[$label] = [
                    'period' => $label,
                    'income' => 0,
                    'expenses' => 0,
                    'balance' => 0
                ];

                if ($period === 'monthly') {
                    $currentPeriod->addMonth();
                } elseif ($period === 'quarterly') {
                    $currentPeriod->addQuarter();
                } else {
                    $currentPeriod->addYear();
                }
            }
        }

        // Query para obter as transações no período
        $transactions = Transaction::where('user_id', $userId)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->whereIn('type', ['income', 'expense'])
            ->get();

        foreach ($transactions as $transaction) {
            $periodKey = Carbon::parse($transaction->date)->format(
                $period === 'yearly' ? 'Y' : ($period === 'quarterly' ? 'Y-m' : 'Y-m')
            );

            if (isset($report[$periodKey])) {
                if ($transaction->type === 'income') {
                    $report[$periodKey]['income'] += $transaction->amount;
                } else {
                    $report[$periodKey]['expenses'] += $transaction->amount;
                }
                
                $report[$periodKey]['balance'] = $report[$periodKey]['income'] - $report[$periodKey]['expenses'];
            }
        }

        // Converte para array indexado
        $reportData = array_values($report);

        return response()->json([
            'success' => true,
            'data' => [
                'report' => $reportData,
                'period' => $period,
                'date_from' => $dateFrom->format('Y-m-d'),
                'date_to' => $dateTo->format('Y-m-d')
            ]
        ]);
    }

    /**
     * Despesas por categoria
     */
    public function expensesByCategory(Request $request)
    {
        $request->validate([
            'limit' => 'sometimes|integer|min:1|max:20',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from'
        ]);

        $limit = $request->input('limit', 10);
        $userId = Auth::id();

        $query = Transaction::with('category')
            ->where('user_id', $userId)
            ->where('type', 'expense');

        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('date', [
                $request->date_from, 
                $request->date_to
            ]);
        } else {
            // Padrão: mês atual
            $query->whereMonth('date', now()->month)
                 ->whereYear('date', now()->year);
        }

        $categories = $query->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->take($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'category_id' => $item->category_id,
                    'category_name' => $item->category ? $item->category->name : 'Sem Categoria',
                    'total' => (float) $item->total,
                    'percentage' => 0 // Será calculado depois
                ];
            });

        // Calcula o total para porcentagens
        $totalExpenses = $categories->sum('total');
        
        if ($totalExpenses > 0) {
            $categories = $categories->map(function ($item) use ($totalExpenses) {
                $item['percentage'] = round(($item['total'] / $totalExpenses) * 100, 2);
                return $item;
            });
        }

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Comparativo entre receitas e despesas
     */
    public function incomeVsExpenses(Request $request)
    {
        $request->validate([
            'period' => 'sometimes|in:monthly,quarterly,yearly',
            'months' => 'sometimes|integer|min:1|max:24'
        ]);

        $period = $request->input('period', 'monthly');
        $months = $request->input('months', 12);
        $userId = Auth::id();

        $results = [];
        $now = now();
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $start = $now->copy()->subMonths($i)->startOfMonth();
            $end = $now->copy()->subMonths($i)->endOfMonth();
            
            $label = $start->format('Y-m');
            
            $income = Transaction::where('user_id', $userId)
                ->where('type', 'income')
                ->whereBetween('date', [$start, $end])
                ->sum('amount');
            
            $expenses = Transaction::where('user_id', $userId)
                ->where('type', 'expense')
                ->whereBetween('date', [$start, $end])
                ->sum('amount');
            
            $results[] = [
                'period' => $label,
                'income' => (float) $income,
                'expenses' => (float) $expenses,
                'balance' => (float) ($income - $expenses)
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * Resumo mensal (para gráficos)
     */
    public function monthlySummary()
    {
        $userId = Auth::id();
        $months = 6; // Últimos 6 meses por padrão
        $results = [];
        $now = now();
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $start = $month->startOfMonth();
            $end = $month->endOfMonth();
            
            $income = Transaction::where('user_id', $userId)
                ->where('type', 'income')
                ->whereBetween('date', [$start, $end])
                ->sum('amount');
            
            $expenses = Transaction::where('user_id', $userId)
                ->where('type', 'expense')
                ->whereBetween('date', [$start, $end])
                ->sum('amount');
            
            $results[] = [
                'month' => $month->format('Y-m'),
                'month_name' => $month->format('F Y'),
                'income' => (float) $income,
                'expenses' => (float) $expenses,
                'balance' => (float) ($income - $expenses)
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * Contas a vencer (próximas despesas)
     */
    public function upcomingBills()
    {
        $userId = Auth::id();
        $daysAhead = 30; // Próximos 30 dias
        
        $start = now();
        $end = now()->addDays($daysAhead);
        
        $upcoming = Transaction::with(['account', 'category'])
            ->where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'title' => $transaction->title,
                    'amount' => (float) $transaction->amount,
                    'date' => $transaction->date->format('Y-m-d'),
                    'days_remaining' => now()->diffInDays($transaction->date, false),
                    'account' => $transaction->account->only(['id', 'name']),
                    'category' => $transaction->category ? $transaction->category->only(['id', 'name']) : null
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $upcoming
        ]);
    }
}
