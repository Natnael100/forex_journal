@extends('layouts.app')

@section('title', 'Subscription Management')

@section('content')
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Subscription Management</h1>
            <p class="text-slate-400">Monitor all trader-analyst subscriptions</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors">
            ← Back to Dashboard
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50 mb-6">
        <form method="GET" action="{{ route('admin.subscriptions.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search by trader or analyst name..." 
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <select name="status" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" class="text-slate-900">All Statuses</option>
                        <option value="active" class="text-slate-900" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="cancelled" class="text-slate-900" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="expired" class="text-slate-900" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center gap-3 mt-4">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    🔍 Search
                </button>
                <a href="{{ route('admin.subscriptions.index') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors">
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Subscriptions Table -->
    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl border border-slate-700/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm text-slate-400 border-b border-slate-700/50 bg-slate-800/50">
                        <th class="px-6 py-4 font-medium">ID</th>
                        <th class="px-6 py-4 font-medium">Trader</th>
                        <th class="px-6 py-4 font-medium">Analyst</th>
                        <th class="px-6 py-4 font-medium">Plan</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Started</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($subscriptions as $subscription)
                        <tr class="text-slate-300 hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 text-sm">#{{ $subscription->id }}</td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-white">{{ $subscription->trader->name ?? 'Unknown' }}</div>
                                <div class="text-xs text-slate-400">{{ $subscription->trader->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-white">{{ $subscription->analyst->name ?? 'Unknown' }}</div>
                                <div class="text-xs text-slate-400">{{ $subscription->analyst->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 capitalize">{{ $subscription->plan ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-medium rounded-full
                                    {{ $subscription->status === 'active' ? 'bg-green-500/20 text-green-400' : '' }}
                                    {{ $subscription->status === 'cancelled' ? 'bg-red-500/20 text-red-400' : '' }}
                                    {{ $subscription->status === 'expired' ? 'bg-slate-500/20 text-slate-400' : '' }}">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $subscription->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.subscriptions.show', $subscription->id) }}" 
                                   class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                <p class="text-lg mb-2">No subscriptions found</p>
                                <p class="text-sm">No active subscriptions in the system</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($subscriptions->hasPages())
            <div class="px-6 py-4 border-t border-slate-700/50">
                {{ $subscriptions->links() }}
            </div>
        @endif
    </div>

    <!-- Stats Summary -->
    <div class="mt-6 text-sm text-slate-400">
        Showing {{ $subscriptions->firstItem() ?? 0 }} to {{ $subscriptions->lastItem() ?? 0 }} of {{ $subscriptions->total() }} subscriptions
    </div>
@endsection
