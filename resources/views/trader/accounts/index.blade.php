@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">My Trading Accounts</h1>
            <p class="text-slate-400">Manage your capital across multiple accounts</p>
        </div>
        <a href="{{ route('trader.accounts.create') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors flex items-center gap-2 shadow-lg shadow-blue-900/20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New Account
        </a>
    </div>

    <!-- Accounts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($accounts as $account)
        <a href="{{ route('trader.accounts.show', $account) }}" class="group relative bg-slate-800/50 backdrop-blur-xl rounded-xl border border-slate-700/50 hover:border-blue-500/50 transition-all duration-300 overflow-hidden">
            <!-- Card Content -->
            <div class="p-6">
                <!-- Top Row: Icon/Name/Balance -->
                <div class="flex justify-between items-start mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-lg {{ $account->account_type->badgeColorClass() }} flex items-center justify-center text-xl bg-opacity-20 backdrop-blur-md">
                            {{ $account->account_type->icon() }}
                        </div>
                        <div>
                            <h3 class="font-bold text-white group-hover:text-blue-400 transition-colors">{{ $account->account_name }}</h3>
                            <span class="text-xs px-2 py-0.5 rounded border {{ $account->account_type->badgeColorClass() }}">
                                {{ $account->account_type->label() }}
                            </span>
                        </div>
                    </div>
                    
                    @if($account->is_system_default)
                        <span title="Default Account" class="text-slate-500 hover:text-blue-400 cursor-help">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </span>
                    @endif
                </div>

                <!-- Balance Display -->
                <div class="mb-6">
                    <p class="text-slate-400 text-sm mb-1">Current Balance</p>
                    <p class="text-3xl font-bold text-white tracking-tight">
                        {{ $account->currency }} {{ number_format($account->current_balance, 2) }}
                    </p>
                </div>

                <!-- Mini Stats -->
                <div class="grid grid-cols-2 gap-4 border-t border-slate-700/50 pt-4">
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Total Trades</p>
                        <p class="font-mono text-white">{{ $account->trades_count }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Net P/L</p>
                        <p class="font-mono {{ $account->net_profit_loss >= 0 ? 'text-green-400' : 'text-red-400' }}">
                            {{ $account->net_profit_loss >= 0 ? '+' : '' }}{{ number_format($account->net_profit_loss, 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Absolute Bottom Decoration -->
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-blue-500/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
        </a>
        @endforeach

        <!-- Add New Placeholder (always visible if fewer than 3 accounts) -->
        @if($accounts->count() < 3)
        <a href="{{ route('trader.accounts.create') }}" class="group flex flex-col items-center justify-center p-6 rounded-xl border-2 border-dashed border-slate-700 hover:border-blue-500/50 hover:bg-slate-800/30 transition-all h-full min-h-[240px]">
            <div class="w-16 h-16 rounded-full bg-slate-800 flex items-center justify-center text-slate-500 group-hover:text-blue-400 group-hover:scale-110 transition-all mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
            </div>
            <p class="font-semibold text-slate-400 group-hover:text-white transition-colors">Create Another Account</p>
            <p class="text-sm text-slate-500 mt-2 text-center px-4">Separate your demo, funded, and personal trading</p>
        </a>
        @endif
    </div>
</div>
@endsection
