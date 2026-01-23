@extends('layouts.app')

@section('title', 'My Subscriptions')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white mb-2">My Subscriptions</h1>
            <p class="text-slate-400">Manage your active subscriptions and billing history</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-400 p-4 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-900/50 border-b border-slate-700">
                        <th class="px-6 py-4 text-slate-400 font-medium text-sm text-left">Analyst</th>
                        <th class="px-6 py-4 text-slate-400 font-medium text-sm text-left">Plan</th>
                        <th class="px-6 py-4 text-slate-400 font-medium text-sm text-left">Status</th>
                        <th class="px-6 py-4 text-slate-400 font-medium text-sm text-left">Started</th>
                        <th class="px-6 py-4 text-slate-400 font-medium text-sm text-left">Renews</th>
                        <th class="px-6 py-4 text-slate-400 font-medium text-sm text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse($subscriptions as $subscription)
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center text-slate-300 font-bold border border-slate-600">
                                        {{ substr($subscription->analyst->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-white font-medium">{{ $subscription->analyst->name }}</div>
                                        <div class="text-sm text-slate-400">Analyst</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="uppercase text-xs font-bold tracking-wider px-2 py-1 rounded-md bg-white/5 text-slate-300 border border-white/10">
                                    {{ $subscription->plan }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($subscription->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        Active
                                    </span>
                                @elseif($subscription->status === 'cancelled')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-500/10 text-red-400 border border-red-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                        Cancelled
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-300 text-sm">
                                {{ $subscription->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-slate-300 text-sm">
                                @if($subscription->status === 'active')
                                    {{ $subscription->current_period_end->format('M d, Y') }}
                                @else
                                    <span class="text-slate-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('trader.subscriptions.show', $subscription->id) }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium transition-colors">
                                    Manage
                                </a>
                                @if($subscription->status === 'active' || $subscription->status === 'cancelled')
                                    <a href="{{ route('trader.disputes.create', $subscription->id) }}" class="text-red-400 hover:text-red-300 text-sm font-medium transition-colors ml-3">
                                        Report Issue
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="w-16 h-16 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                                <h3 class="text-white font-medium mb-1">No subscriptions found</h3>
                                <p class="text-slate-400 text-sm mb-4">You haven't subscribed to any analysts yet.</p>
                                <a href="{{ url('/analysts') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    Find an Analyst
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($subscriptions->hasPages())
            <div class="px-6 py-4 border-t border-slate-700">
                {{ $subscriptions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
