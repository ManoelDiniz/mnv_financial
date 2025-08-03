<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AccountController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $accounts = Account::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();

        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'agency_number' => 'nullable|string|max:20',
            'type' => 'required|in:checking,savings,investment,credit_card,other',
            'balance' => 'required|numeric',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $account = Account::create($validated + ['user_id' => Auth::id()]);

        return redirect()->route('accounts.show', $account)
            ->with('success', 'Account created successfully.');
    }

    public function show(Account $account)
    {
        $this->authorize('view', $account);

        $transactions = $account->transactions()
            ->with('category')
            ->latest('date')
            ->paginate(15);

        return view('accounts.show', compact('account', 'transactions'));
    }

    public function edit(Account $account)
    {
        $this->authorize('update', $account);
        return view('accounts.edit', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        $this->authorize('update', $account);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'agency_number' => 'nullable|string|max:20',
            'type' => 'required|in:checking,savings,investment,credit_card,other',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $account->update($validated);

        return redirect()->route('accounts.show', $account)
            ->with('success', 'Account updated successfully.');
    }

    public function destroy(Account $account)
    {
        $this->authorize('delete', $account);

        if ($account->transactions()->exists()) {
            return back()->with('error', 'Cannot delete account with transactions.');
        }

        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'Account deleted successfully.');
    }
}
