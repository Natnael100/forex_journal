<?php

namespace App\Http\Controllers\Trader;

use App\Enums\AccountType;
use App\Http\Controllers\Controller;
use App\Models\TradeAccount;
use App\Models\AccountTransaction;
use App\Enums\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TradeAccountController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of trader's accounts
     */
    public function index()
    {
        $accounts = Auth::user()->tradeAccounts()
            ->withCount('trades')
            ->latest()
            ->get();

        return view('trader.accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new account
     */
    public function create()
    {
        $accountTypes = AccountType::cases();
        
        return view('trader.accounts.create', compact('accountTypes'));
    }

    /**
     * Store a newly created account
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:' . implode(',', AccountType::values()),
            'broker' => 'nullable|string|max:255',
            'initial_balance' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_system_default'] = false;

        $account = TradeAccount::create($validated);

        return redirect()
            ->route('trader.accounts.show', $account)
            ->with('success', 'Trade account created successfully!');
    }

    /**
     * Display the specified account with details
     */
    public function show(TradeAccount $account)
    {
        $this->authorize('view', $account);

        $account->load(['trades' => function ($query) {
            $query->latest('entry_date')->limit(10);
        }, 'transactions' => function ($query) {
            $query->latest('transaction_date')->limit(10);
        }]);

        // Calculate metrics
        $metrics = [
            'current_balance' => $account->current_balance,
            'total_trades' => $account->trades()->count(),
            'winning_trades' => $account->trades()->where('outcome', 'win')->count(),
            'losing_trades' => $account->trades()->where('outcome', 'loss')->count(),
            'net_profit_loss' => $account->net_profit_loss,
            'total_deposits' => $account->total_deposits,
            'total_withdrawals' => $account->total_withdrawals,
        ];

        if ($metrics['total_trades'] > 0) {
            $metrics['win_rate'] = round(($metrics['winning_trades'] / $metrics['total_trades']) * 100, 1);
        } else {
            $metrics['win_rate'] = 0;
        }

        return view('trader.accounts.show', compact('account', 'metrics'));
    }

    /**
     * Show the form for editing the account
     */
    public function edit(TradeAccount $account)
    {
        $this->authorize('update', $account);

        // Prevent editing system default accounts
        if ($account->is_system_default) {
            return redirect()
                ->route('trader.accounts.show', $account)
                ->with('error', 'Cannot edit system default account.');
        }

        $accountTypes = AccountType::cases();

        return view('trader.accounts.edit', compact('account', 'accountTypes'));
    }

    /**
     * Update the specified account
     */
    public function update(Request $request, TradeAccount $account)
    {
        $this->authorize('update', $account);

        if ($account->is_system_default) {
            return redirect()
                ->route('trader.accounts.show', $account)
                ->with('error', 'Cannot edit system default account.');
        }

        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:' . implode(',', AccountType::values()),
            'broker' => 'nullable|string|max:255',
            'currency' => 'required|string|size:3',
        ]);

        $account->update($validated);

        return redirect()
            ->route('trader.accounts.show', $account)
            ->with('success', 'Account updated successfully!');
    }

    /**
     * Remove the specified account
     */
    public function destroy(TradeAccount $account)
    {
        $this->authorize('delete', $account);

        if ($account->is_system_default) {
            return redirect()
                ->route('trader.accounts.index')
                ->with('error', 'Cannot delete system default account.');
        }

        if ($account->trades()->count() > 0) {
            return redirect()
                ->route('trader.accounts.show', $account)
                ->with('error', 'Cannot delete account with existing trades. Please reassign trades first.');
        }

        $account->delete();

        return redirect()
            ->route('trader.accounts.index')
            ->with('success', 'Account deleted successfully!');
    }

    /**
     * Add a transaction (deposit/withdrawal)
     */
    public function addTransaction(Request $request, TradeAccount $account)
    {
        $this->authorize('update', $account);

        $validated = $request->validate([
            'type' => 'required|in:' . implode(',', TransactionType::values()),
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'transaction_date' => 'required|date',
        ]);

        $validated['trade_account_id'] = $account->id;

        AccountTransaction::create($validated);

        return redirect()
            ->route('trader.accounts.show', $account)
            ->with('success', ucfirst($validated['type']) . ' recorded successfully!');
    }
}
