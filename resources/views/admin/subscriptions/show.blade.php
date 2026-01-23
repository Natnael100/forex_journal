@extends('layouts.app')

@section('title', 'Subscription Details')

@section('content')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Subscription #{{ $subscription->id }}</h1>
            <p class="text-slate-400">View subscription details and manage</p>
        </div>
        <a href="{{ route('admin.subscriptions.index') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors">
            ← Back to Subscriptions
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                <h2 class="text-xl font-bold text-white mb-4">Subscription Information</h2>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-slate-400 mb-1">Plan</p>
                            <p class="text-white font-medium capitalize">{{ $subscription->plan }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400 mb-1">Status</p>
                            <span class="px-3 py-1 text-xs font-medium rounded-full
                                {{ $subscription->status === 'active' ? 'bg-green-500/20 text-green-400' : '' }}
                                {{ $subscription->status === 'cancelled' ? 'bg-red-500/20 text-red-400' : '' }}">
                                {{ ucfirst($subscription->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-slate-400 mb-1">Start Date</p>
                            <p class="text-white">{{ $subscription->current_period_start->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400 mb-1">End Date</p>
                            <p class="text-white">{{ $subscription->current_period_end->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm text-slate-400 mb-1">Price</p>
                        <p class="text-white font-bold text-lg">${{ number_format($subscription->price, 2) }}</p>
                    </div>
                </div>
            </div>

            @if($subscription->status === 'active')
                <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                    <h2 class="text-xl font-bold text-white mb-4">Admin Actions</h2>
                    
                    <form action="{{ route('admin.subscriptions.cancel', $subscription->id) }}" method="POST" onsubmit="return confirm('Cancel this subscription? This cannot be undone.')">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                            Cancel Subscription
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                <h3 class="text-lg font-bold text-white mb-4">Trader</h3>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-emerald-600 rounded-full flex items-center justify-center text-white font-bold">
                        {{ substr($subscription->trader->name ?? '?', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-white font-medium">{{ $subscription->trader->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-slate-400">{{ $subscription->trader->email ?? '' }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.users.show', $subscription->trader_id) }}" class="text-sm text-blue-400 hover:underline">
                    View Profile →
                </a>
            </div>

            <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                <h3 class="text-lg font-bold text-white mb-4">Analyst</h3>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                        {{ substr($subscription->analyst->name ?? '?', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-white font-medium">{{ $subscription->analyst->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-slate-400">{{ $subscription->analyst->email ?? '' }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.users.show', $subscription->analyst_id) }}" class="text-sm text-blue-400 hover:underline">
                    View Profile →
                </a>
            </div>
        </div>
    </div>
@endsection
