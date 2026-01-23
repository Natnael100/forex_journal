@extends('layouts.app')

@section('title', 'Analyst Payouts')

@section('content')
<div class="container mx-auto py-8 px-4 max-w-7xl">
    
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Earnings & Payouts</h1>
        <p class="text-slate-400">Track your revenue and manage withdrawals</p>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 border border-green-500/20 bg-green-500/10 text-green-400 rounded-lg flex items-center gap-2">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 border border-red-500/20 bg-red-500/10 text-red-400 rounded-lg flex items-center gap-2">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Available Balance -->
        <div class="rounded-xl border border-emerald-500/20 bg-gradient-to-br from-emerald-600/10 to-teal-600/10 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-emerald-400">Available Balance</h3>
                <svg class="h-5 w-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-3xl font-bold text-white">${{ number_format($stats['pending_earnings'], 2) }}</p>
            <p class="text-xs text-slate-400 mt-1">Ready for withdrawal</p>
        </div>

        <!-- Total Earned -->
        <div class="rounded-xl border border-blue-500/20 bg-gradient-to-br from-blue-600/10 to-purple-600/10 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-blue-400">Total Earned</h3>
                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <p class="text-3xl font-bold text-white">${{ number_format($stats['total_earned'], 2) }}</p>
            <p class="text-xs text-slate-400 mt-1">Lifetime earnings</p>
        </div>

        <!-- Active Subscriptions -->
        <div class="rounded-xl border border-purple-500/20 bg-gradient-to-br from-purple-600/10 to-pink-600/10 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-purple-400">Active Clients</h3>
                <svg class="h-5 w-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <p class="text-3xl font-bold text-white">{{ $stats['active_subscriptions'] }}</p>
            <p class="text-xs text-slate-400 mt-1">Paying subscribers</p>
        </div>

        <!-- Last Payout -->
        <div class="rounded-xl border border-slate-600 bg-slate-800/50 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-slate-400">Last Payout</h3>
                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            @if($stats['last_payout'])
                <p class="text-2xl font-bold text-white">${{ number_format($stats['last_payout']->amount, 2) }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ $stats['last_payout']->created_at->format('M d, Y') }}</p>
            @else
                <p class="text-2xl font-bold text-slate-600">--</p>
                <p class="text-xs text-slate-500 mt-1">No payouts yet</p>
            @endif
        </div>
    </div>

    <!-- Request Payout Section -->
    <div class="mb-8">
        <div class="rounded-xl border border-slate-700 bg-slate-800/50 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-white mb-1">Request Withdrawal</h3>
                    <p class="text-sm text-slate-400">Minimum withdrawal amount is $50.00</p>
                </div>
                <form action="{{ route('analyst.payout.request') }}" method="POST">
                    @csrf
                    <button 
                        type="submit"
                        @if($stats['pending_earnings'] < 50) disabled @endif
                        class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 disabled:from-slate-700 disabled:to-slate-700 disabled:cursor-not-allowed text-white font-medium rounded-lg transition-all shadow-lg">
                        @if($stats['pending_earnings'] >= 50)
                            Withdraw ${{ number_format($stats['pending_earnings'], 2) }}
                        @else
                            Insufficient Balance
                        @endif
                    </button>
                </form>
            </div>

            @if($pendingPayouts->count() > 0)
                <div class="mt-4 p-4 border border-yellow-500/20 bg-yellow-500/10 rounded-lg">
                    <p class="text-sm text-yellow-400">
                        <svg class="inline h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        You have {{$pendingPayouts->count()}} pending payout request(s). Processing typically takes 5-7 business days.
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Payout History Table -->
    <div class="rounded-xl border border-slate-700 bg-slate-800/50 overflow-hidden">
        <div class="p-6 border-b border-slate-700">
            <h3 class="text-lg font-bold text-white">Payout History</h3>
        </div>
        
        @if($payouts->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-900/50 border-b border-slate-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Period</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Transaction ID</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700">
                        @foreach($payouts as $payout)
                            <tr class="hover:bg-slate-900/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                    {{ $payout->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-400">
                                    {{ $payout->period_start?->format('M Y') ?? '--' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-emerald-400">
                                    ${{ number_format($payout->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($payout->status === 'completed')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-500/20 text-green-400 border border-green-500/30">
                                            Completed
                                        </span>
                                    @elseif($payout->status === 'pending')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">
                                            Pending
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-500/20 text-red-400 border border-red-500/30">
                                            Failed
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 font-mono">
                                    {{ $payout->stripe_transfer_id ?? '--' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-700">
                {{ $payouts->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <h3class="text-lg font-medium text-slate-400 mb-1">No payout history yet</h3>
                <p class="text-sm text-slate-500">Your payout requests will appear here</p>
            </div>
        @endif
    </div>
</div>
@endsection
