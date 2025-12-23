@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <nav class="flex text-sm text-slate-400">
            <a href="{{ route('trader.accounts.index') }}" class="hover:text-white transition-colors">Accounts</a>
            <span class="mx-2">/</span>
            <span class="text-white">{{ $account->account_name }}</span>
        </nav>

        <div class="flex gap-3">
            @if(!$account->is_system_default)
                <a href="{{ route('trader.accounts.edit', $account) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-medium rounded-lg border border-slate-700 transition-colors">
                    Edit Account
                </a>
            @endif
            <button onclick="document.getElementById('transactionModal').classList.remove('hidden')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-lg shadow-blue-900/20 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Transaction
            </button>
        </div>
    </div>

    <!-- Account Overview Card -->
    <div class="bg-slate-800/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-xl {{ $account->account_type->badgeColorClass() }} flex items-center justify-center text-3xl bg-opacity-20 backdrop-blur-md">
                    {{ $account->account_type->icon() }}
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-white">{{ $account->account_name }}</h1>
                        @if($account->is_system_default)
                            <span class="px-2 py-0.5 rounded text-xs bg-slate-700 text-slate-300 border border-slate-600">Default</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-4 mt-1 text-sm text-slate-400">
                        <span class="px-2 py-0.5 rounded border {{ $account->account_type->badgeColorClass() }}">
                            {{ $account->account_type->label() }}
                        </span>
                        @if($account->broker)
                            <span>Broker: <span class="text-slate-300">{{ $account->broker }}</span></span>
                        @endif
                        <span>Start: <span class="text-slate-300">{{ $account->currency }} {{ number_format($account->initial_balance, 2) }}</span></span>
                    </div>
                </div>
            </div>

            <div class="text-right">
                <p class="text-slate-400 text-sm mb-1">Current Balance</p>
                <div class="text-4xl font-bold text-white tracking-tight flex items-baseline justify-end gap-1">
                    <span class="text-lg text-slate-500 font-medium">{{ $account->currency }}</span>
                    {{ number_format($metrics['current_balance'], 2) }}
                </div>
                <p class="text-sm mt-1 {{ $metrics['net_profit_loss'] >= 0 ? 'text-green-400' : 'text-red-400' }}">
                    {{ $metrics['net_profit_loss'] >= 0 ? '+' : '' }}{{ number_format($metrics['net_profit_loss'], 2) }} P/L
                </p>
            </div>
        </div>

        <!-- Metrics Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 pt-6 border-t border-slate-700/50">
            <div class="p-4 bg-slate-900/40 rounded-lg">
                <p class="text-xs text-slate-500 mb-1">Total Trades</p>
                <p class="text-xl font-bold text-white">{{ $metrics['total_trades'] }}</p>
            </div>
            <div class="p-4 bg-slate-900/40 rounded-lg">
                <p class="text-xs text-slate-500 mb-1">Win Rate</p>
                <p class="text-xl font-bold {{ $metrics['win_rate'] >= 50 ? 'text-green-400' : 'text-red-400' }}">
                    {{ $metrics['win_rate'] }}%
                </p>
            </div>
            <div class="p-4 bg-slate-900/40 rounded-lg">
                <p class="text-xs text-slate-500 mb-1">Profit Factor</p>
                @php
                    $grossProfit = $account->trades()->where('profit_loss', '>', 0)->sum('profit_loss');
                    $grossLoss = abs($account->trades()->where('profit_loss', '<', 0)->sum('profit_loss'));
                    $pf = $grossLoss > 0 ? $grossProfit / $grossLoss : ($grossProfit > 0 ? 99.99 : 0);
                @endphp
                <p class="text-xl font-bold {{ $pf >= 1.5 ? 'text-green-400' : ($pf >= 1 ? 'text-yellow-400' : 'text-red-400') }}">
                    {{ number_format($pf, 2) }}
                </p>
            </div>
            <div class="p-4 bg-slate-900/40 rounded-lg">
                <p class="text-xs text-slate-500 mb-1">Equity Change</p>
                @php
                    $roi = $account->initial_balance > 0 ? ($metrics['current_balance'] - $account->initial_balance) / $account->initial_balance * 100 : 0;
                @endphp
                <p class="text-xl font-bold {{ $roi >= 0 ? 'text-green-400' : 'text-red-400' }}">
                    {{ $roi >= 0 ? '+' : '' }}{{ number_format($roi, 2) }}%
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Transactions -->
        <div class="bg-slate-800/50 backdrop-blur-xl rounded-xl border border-slate-700/50 overflow-hidden">
            <div class="p-6 border-b border-slate-700/50 flex justify-between items-center">
                <h3 class="font-semibold text-white">Recent Transactions</h3>
            </div>
            <div class="divide-y divide-slate-700/50">
                @forelse($account->transactions as $transaction)
                <div class="p-4 flex items-center justify-between hover:bg-white/5 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-sm">
                            {{ $transaction->type->icon() }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white">{{ $transaction->type->label() }}</p>
                            <p class="text-xs text-slate-500">{{ $transaction->transaction_date->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <span class="font-mono font-medium {{ $transaction->isPositive() ? 'text-green-400' : 'text-red-400' }}">
                        {{ $transaction->isPositive() ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                    </span>
                </div>
                @empty
                <div class="p-8 text-center text-slate-500 text-sm">No transactions yet</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Trades -->
        <div class="lg:col-span-2 bg-slate-800/50 backdrop-blur-xl rounded-xl border border-slate-700/50 overflow-hidden">
            <div class="p-6 border-b border-slate-700/50 flex justify-between items-center">
                <h3 class="font-semibold text-white">Recent Trades</h3>
                <a href="{{ route('trader.trades.index') }}" class="text-sm text-blue-400 hover:text-blue-300">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-slate-400 uppercase bg-slate-900/50">
                        <tr>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Pair</th>
                            <th class="px-6 py-3">Session</th>
                            <th class="px-6 py-3 text-right">P/L</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($account->trades as $trade)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 text-slate-300">{{ $trade->entry_date->format('M d H:i') }}</td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-white">{{ $trade->pair }}</span>
                                <span class="ml-2 text-xs {{ $trade->direction->colorClass() }}">{{ $trade->direction->label() }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-400">{{ $trade->session->label() }}</td>
                            <td class="px-6 py-4 text-right font-medium {{ $trade->profit_loss >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                {{ number_format($trade->profit_loss, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500">No trades yet on this account</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Modal -->
<div id="transactionModal" class="hidden fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="document.getElementById('transactionModal').classList.add('hidden')"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-slate-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-700 relative z-[101]">
            <form action="{{ route('trader.accounts.transaction', $account) }}" method="POST">
                @csrf
                <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-bold text-white mb-4" id="modal-title">Record Transaction</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Type</label>
                            <select name="type" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-3 py-2 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="deposit">Deposit (Add Funds)</option>
                                <option value="withdrawal">Withdrawal (Remove Funds)</option>
                                <option value="adjustment">Adjustment (Correction)</option>
                                <option value="fee">Fee (Deduction)</option>
                                <option value="interest">Interest (Income)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Amount</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 font-bold">$</span>
                                <input type="number" step="0.01" name="amount" required class="w-full bg-slate-900/50 border border-slate-700 rounded-lg pl-8 pr-3 py-2 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0.00">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Date</label>
                            <input type="datetime-local" name="transaction_date" required value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-3 py-2 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 [color-scheme:dark]">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Description (Optional)</label>
                            <input type="text" name="description" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-3 py-2 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., Weekly Deposit">
                        </div>
                    </div>
                </div>
                <div class="bg-slate-900/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-700">
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm shadow-blue-900/20">
                        Save Transaction
                    </button>
                    <button type="button" onclick="document.getElementById('transactionModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-600 shadow-sm px-4 py-2 bg-transparent text-base font-medium text-slate-300 hover:text-white hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
