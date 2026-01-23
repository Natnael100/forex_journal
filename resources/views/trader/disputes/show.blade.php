@extends('layouts.app')

@section('title', 'Dispute Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('trader.disputes.index') }}" class="text-slate-400 hover:text-white transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Disputes
            </a>
        </div>

        <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-700 flex justify-between items-center bg-slate-900/30">
                <h1 class="text-xl font-bold text-white">Case #{{ $dispute->id }}</h1>
                @if($dispute->status === 'pending')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-500/10 text-yellow-500 border border-yellow-500/20">
                        Pending Review
                    </span>
                @elseif($dispute->status === 'resolved')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-500/10 text-green-400 border border-green-500/20">
                        Resolved
                    </span>
                @elseif($dispute->status === 'dismissed')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-500/10 text-slate-400 border border-slate-500/20">
                        Dismissed
                    </span>
                @endif
            </div>

            <div class="p-6 space-y-8">
                <!-- Case Info -->
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <div class="text-sm text-slate-400 mb-1">Filed Against</div>
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-slate-700 flex items-center justify-center text-slate-300 font-bold border border-slate-600 text-xs">
                                {{ substr($dispute->analyst->name, 0, 1) }}
                            </div>
                            <span class="text-white font-medium">{{ $dispute->analyst->name }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-slate-400 mb-1">Date Filed</div>
                        <div class="text-white font-medium">{{ $dispute->created_at->format('M d, Y') }}</div>
                    </div>
                </div>

                <!-- Complaint Details -->
                <div>
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-3">Complaint Details</h3>
                    <div class="bg-slate-900/50 rounded-lg p-4 border border-slate-700/50">
                        <div class="text-sm text-slate-400 mb-2 font-mono">
                            Reason: {{ ucwords(str_replace('_', ' ', $dispute->reason)) }}
                        </div>
                        <p class="text-slate-300 whitespace-pre-wrap">{{ $dispute->description }}</p>
                    </div>
                </div>

                <!-- Admin Resolution -->
                @if($dispute->status !== 'pending' && $dispute->resolver)
                    <div>
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-3">Resolution Decision</h3>
                        <div class="bg-blue-600/10 rounded-lg p-5 border border-blue-600/20">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full bg-blue-600/20 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-blue-400 font-bold mb-2">
                                        @if($dispute->resolution === 'refund')
                                            Case Resolved: Refund Issued
                                        @elseif($dispute->resolution === 'warning')
                                            Case Resolved: Analyst Warned
                                        @elseif($dispute->resolution === 'dismissed')
                                            Case Dismissed
                                        @endif
                                    </div>
                                    <p class="text-blue-200/80 text-sm mb-3">
                                        {{ $dispute->admin_notes ?? 'No additional notes provided.' }}
                                    </p>
                                    <div class="text-xs text-blue-400/50">
                                        Resolved by Admin {{ $dispute->resolver->name }} on {{ $dispute->resolved_at ? $dispute->resolved_at->format('M d, Y') : 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            
            @if($dispute->status === 'pending')
                <div class="px-6 py-4 bg-slate-900/30 border-t border-slate-700 flex justify-between items-center text-sm text-slate-400">
                    <p>An admin is currently reviewing your case.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
