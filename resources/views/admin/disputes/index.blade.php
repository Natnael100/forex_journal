@extends('layouts.app')

@section('title', 'Dispute Resolution')

@section('content')
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Dispute Resolution</h1>
            <p class="text-slate-400">Manage conflicting reports between traders and analysts</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors">
            ← Back to Dashboard
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50 mb-6">
        <form method="GET" action="{{ route('admin.disputes.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search by trader or analyst name..." 
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <select name="status" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" class="text-slate-900">All Statuses</option>
                        <option value="pending" class="text-slate-900" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="resolved" class="text-slate-900" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="dismissed" class="text-slate-900" {{ request('status') == 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                        Filter Disputes
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Disputes Table -->
    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl border border-slate-700/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm text-slate-400 border-b border-slate-700/50 bg-slate-800/50">
                        <th class="px-6 py-4 font-medium">ID</th>
                        <th class="px-6 py-4 font-medium">Trader</th>
                        <th class="px-6 py-4 font-medium">Analyst</th>
                        <th class="px-6 py-4 font-medium">Reason</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Date</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($disputes as $dispute)
                        <tr class="text-slate-300 hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 text-sm">#{{ $dispute->id }}</td>
                            <td class="px-6 py-4 font-medium text-white">{{ $dispute->trader->name ?? 'Unknown' }}</td>
                            <td class="px-6 py-4">{{ $dispute->analyst->name ?? 'Unknown' }}</td>
                            <td class="px-6 py-4">{{ ucfirst(str_replace('_', ' ', $dispute->reason)) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-medium rounded-full
                                    {{ $dispute->status === 'resolved' ? 'bg-green-500/20 text-green-400' : '' }}
                                    {{ $dispute->status === 'pending' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                                    {{ $dispute->status === 'dismissed' ? 'bg-slate-500/20 text-slate-400' : '' }}">
                                    {{ ucfirst($dispute->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $dispute->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.disputes.show', $dispute->id) }}" 
                                   class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors">
                                    Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                <p class="text-lg mb-2">No disputes found</p>
                                <p class="text-sm">Everything is running smoothly!</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($disputes->hasPages())
            <div class="px-6 py-4 border-t border-slate-700/50">
                {{ $disputes->links() }}
            </div>
        @endif
    </div>
@endsection
