@extends('layouts.app')

@section('title', 'My Disputes')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white mb-2">Dispute History</h1>
            <p class="text-slate-400">Track the status of your reported issues</p>
        </div>
        <a href="{{ route('trader.subscriptions.index') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg text-sm font-medium transition-colors">
            Back to Subscriptions
        </a>
    </div>

    <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-900/50 border-b border-slate-700">
                        <th class="px-6 py-4 text-slate-400 font-medium text-sm">Case ID</th>
                        <th class="px-6 py-4 text-slate-400 font-medium text-sm">Against Analyst</th>
                        <th class="px-6 py-4 text-slate-400 font-medium text-sm">Reason</th>
                        <th class="px-6 py-4 text-slate-400 font-medium text-sm">Status</th>
                        <th class="px-6 py-4 text-slate-400 font-medium text-sm">Date Filed</th>
                        <th class="px-6 py-4 text-slate-400 font-medium text-sm text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse($disputes as $dispute)
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4 font-mono text-slate-300 text-sm">#{{ $dispute->id }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-slate-300 font-bold border border-slate-600 text-xs">
                                        {{ substr($dispute->analyst->name, 0, 1) }}
                                    </div>
                                    <span class="text-white font-medium">{{ $dispute->analyst->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-300">
                                {{ ucwords(str_replace('_', ' ', $dispute->reason)) }}
                            </td>
                            <td class="px-6 py-4">
                                @if($dispute->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-500/10 text-yellow-500 border border-yellow-500/20">
                                        Pending Review
                                    </span>
                                @elseif($dispute->status === 'resolved')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-500/10 text-green-400 border border-green-500/20">
                                        Resolved
                                    </span>
                                @elseif($dispute->status === 'dismissed')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                        Dismissed
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-400 text-sm">
                                {{ $dispute->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('trader.disputes.show', $dispute->id) }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium transition-colors">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <p class="mb-2">No disputes found</p>
                                <a href="{{ route('trader.subscriptions.index') }}" class="text-sm text-blue-400 hover:underline">
                                    Back to Subscriptions
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($disputes->hasPages())
            <div class="px-6 py-4 border-t border-slate-700">
                {{ $disputes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
