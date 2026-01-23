@extends('layouts.app')

@section('title', 'Revenue Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Revenue Dashboard 💰</h1>
        <p class="text-slate-400">Track your earnings and manage payouts</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-green-900/20 to-green-800/20 backdrop-blur-xl rounded-xl p-6 border border-green-700/50">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-green-300">Monthly Revenue</h3>
                <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-white">${{ number_format($stats['monthly_revenue'], 2) }}</p>
            <p class="text-sm text-green-400 mt-2">{{ $stats['active_subscriptions'] }} active subscriptions</p>
        </div>

        <div class="bg-gradient-to-br from-blue-900/20 to-blue-800/20 backdrop-blur-xl rounded-xl p-6 border border-blue-700/50">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-blue-300">Pending Payout</h3>
                <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-white">${{ number_format($stats['pending_payout'], 2) }}</p>
            <form action="{{ route('analyst.payouts.request') }}" method="POST" class="mt-2">
                @csrf
                <button type="submit" class="text-sm text-blue-400 hover:text-blue-300 transition-colors" @if($stats['pending_payout'] < 50) disabled title="Minimum $50 required" @endif>
                    Request payout →
                </button>
            </form>
        </div>

        <div class="bg-gradient-to-br from-purple-900/20 to-purple-800/20 backdrop-blur-xl rounded-xl p-6 border border-purple-700/50">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-purple-300">Total Earned</h3>
                <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-white">${{ number_format($stats['total_earned'], 2) }}</p>
            <p class="text-sm text-purple-400 mt-2">All-time payouts</p>
        </div>

        <div class="bg-gradient-to-br from-yellow-900/20 to-yellow-800/20 backdrop-blur-xl rounded-xl p-6 border border-yellow-700/50">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-yellow-300">Rating</h3>
                <svg class="w-8 h-8 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-white">{{ number_format($stats['average_rating'], 1) }}</p>
            <p class="text-sm text-yellow-400 mt-2">{{ $stats['total_reviews'] }} reviews</p>
        </div>
    </div>

    <!-- Revenue by Plan -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-bold text-white mb-4">Revenue by Plan</h3>
            <div class="space-y-3">
                @forelse($revenueByPlan as $plan)
                <div class="flex items-center justify-between p-3 bg-slate-900/50 rounded-lg">
                    <div>
                        <p class="font-medium text-white capitalize">{{ $plan->plan }}</p>
                        <p class="text-sm text-slate-400">{{ $plan->count }} subscribers</p>
                    </div>
                    <p class="text-xl font-bold text-green-400">${{ number_format($plan->total, 2) }}</p>
                </div>
                @empty
                <p class="text-slate-400 text-center py-4">No active subscriptions yet</p>
                @endforelse
            </div>
        </div>

        <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700/50">
            <h3 class="text-lg font-bold text-white mb-4">Recent Subscriptions</h3>
            <div class="space-y-3">
                @forelse($recentSubscriptions as $subscription)
                <div class="flex items-center justify-between p-3 bg-slate-900/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <img src="{{ $subscription->trader->getProfilePhotoUrl('large') }}" alt="{{ $subscription->trader->name }}" class="w-10 h-10 rounded-full">
                        <div>
                            <p class="font-medium text-white">{{ $subscription->trader->name }}</p>
                            <p class="text-xs text-slate-400">{{ $subscription->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-white capitalize">{{ $subscription->plan }}</p>
                        <p class="text-xs text-green-400">${{ number_format($subscription->price, 2) }}/mo</p>
                    </div>
                </div>
                @empty
                <p class="text-slate-400 text-center py-4">No subscriptions yet</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Payout History -->
    <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700/50">
        <h3 class="text-lg font-bold text-white mb-4">Payout History</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-700">
                        <th class="text-left text-sm font-medium text-slate-400 pb-3">Period</th>
                        <th class="text-left text-sm font-medium text-slate-400 pb-3">Amount</th>
                        <th class="text-left text-sm font-medium text-slate-400 pb-3">Status</th>
                        <th class="text-left text-sm font-medium text-slate-400 pb-3">Processed</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payoutHistory as $payout)
                    <tr class="border-b border-slate-800">
                        <td class="py-3 text-white text-sm">{{ $payout->period_start->format('M d') }} - {{ $payout->period_end->format('M d, Y') }}</td>
                        <td class="py-3 text-white font-bold">${{ number_format($payout->amount, 2) }}</td>
                        <td class="py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                @if($payout->status === 'completed') bg-green-500/20 text-green-400
                                @elseif($payout->status === 'pending') bg-yellow-500/20 text-yellow-400
                                @else bg-red-500/20 text-red-400 @endif">
                                {{ ucfirst($payout->status) }}
                            </span>
                        </td>
                        <td class="py-3 text-slate-400 text-sm">{{ $payout->processed_at?->format('M d, Y') ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-slate-400">No payout history yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $payoutHistory->links() }}
        </div>
    </div>
</div>
@endsection
