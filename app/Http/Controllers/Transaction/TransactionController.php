<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TransactionController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $transactions = Transaction::with(['account', 'category'])
            ->where('user_id', Auth::id())
            ->latest('date')
            ->paginate(20);

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $accounts = Account::where('user_id', Auth::id())
            ->where('is_active', true)
            ->get();

        $categories = Category::where('user_id', Auth::id())
            ->orWhereNull('user_id')
            ->get();

        return view('transactions.create', compact('accounts', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateTransaction($request);

        $transaction = Transaction::create($validated + ['user_id' => Auth::id()]);

        return redirect()->route('transactions.show', $transaction)
            ->with('success', 'Transaction created successfully.');
    }

    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        $accounts = Account::where('user_id', Auth::id())
            ->where('is_active', true)
            ->get();

        $categories = Category::where('user_id', Auth::id())
            ->orWhereNull('user_id')
            ->get();

        return view('transactions.edit', compact('transaction', 'accounts', 'categories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        $validated = $this->validateTransaction($request);

        $transaction->update($validated);

        return redirect()->route('transactions.show', $transaction)
            ->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);

        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }

    protected function validateTransaction(Request $request)
    {
        return $request->validate([
            'title' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:income,expense,transfer',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'nullable|exists:categories,id',
            'to_account_id' => 'nullable|required_if:type,transfer|different:account_id|exists:accounts,id',
            'converted_amount' => 'nullable|numeric|min:0.01'
        ]);
    }
}
