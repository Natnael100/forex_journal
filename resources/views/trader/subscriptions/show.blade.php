@extends('layouts.app')

@section('title', 'Subscription Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('trader.subscriptions.index') }}" class="text-slate-400 hover:text-white transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Subscriptions
            </a>
        </div>

        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold text-white">Subscription #{{ $subscription->id }}</h1>
            @if($subscription->status === 'active')
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    Active
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-slate-500/10 text-slate-400 border border-slate-500/20">
                    {{ ucfirst($subscription->status) }}
                </span>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Main Details -->
            <div class="md:col-span-2 space-y-6">
                <!-- Plan Info -->
                <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                    <h2 class="text-lg font-bold text-white mb-4">Plan Details</h2>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <div class="text-sm text-slate-400 mb-1">Plan Type</div>
                            <div class="text-white font-medium uppercase">{{ $subscription->plan }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-slate-400 mb-1">Price</div>
                            <div class="text-white font-medium">${{ number_format($subscription->price, 2) }}/month</div>
                        </div>
                        <div>
                            <div class="text-sm text-slate-400 mb-1">Start Date</div>
                            <div class="text-white">{{ $subscription->current_period_start->format('M d, Y') }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-slate-400 mb-1">Next Renewal</div>
                            <div class="text-white">
                                @if($subscription->status === 'active')
                                    {{ $subscription->current_period_end->format('M d, Y') }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analyst Info -->
                <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                    <h2 class="text-lg font-bold text-white mb-4">Analyst Information</h2>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full bg-slate-700 flex items-center justify-center text-slate-300 font-bold border border-slate-600 text-xl">
                            {{ substr($subscription->analyst->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="text-xl font-bold text-white">{{ $subscription->analyst->name }}</div>
                            <div class="text-slate-400">{{ $subscription->analyst->email }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Actions -->
            <div class="space-y-6">
                @if($subscription->status === 'active')
                    <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                        <h2 class="text-lg font-bold text-white mb-4">Actions</h2>
                        
                        <form action="{{ route('trader.subscriptions.cancel', $subscription->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel? You will lose access at the end of the billing period.')">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2.5 bg-red-600/10 hover:bg-red-600/20 text-red-400 border border-red-900/50 rounded-lg text-sm font-medium transition-colors text-center mb-3">
                                Cancel Subscription
                            </button>
                        </form>
                        
                        <a href="{{ route('trader.disputes.create', $subscription->id) }}" class="block w-full px-4 py-2.5 bg-slate-700/50 hover:bg-slate-700 text-slate-300 rounded-lg text-sm font-medium transition-colors text-center">
                            Report an Issue
                        </a>
                    </div>
                @endif

                <div class="bg-blue-600/10 rounded-xl border border-blue-600/20 p-6">
                    <h3 class="text-blue-400 font-bold mb-2">Need Help?</h3>
                    <p class="text-sm text-blue-300/80 mb-4">
                        If you're experiencing issues with this analyst or subscription, please try to contact them directly first. If the issue remains unresolved, you can file a dispute.
                    </p>
                    <a href="{{ route('trader.disputes.index') }}" class="text-sm text-blue-400 hover:text-blue-300 font-medium underline">
                        View my disputes
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
