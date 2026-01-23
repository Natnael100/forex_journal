@extends('layouts.app')

@section('title', 'Review Dispute')

@section('content')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Review Dispute #{{ $dispute->id }}</h1>
            <p class="text-slate-400">Created on {{ $dispute->created_at->format('M d, Y H:i') }}</p>
        </div>
        <a href="{{ route('admin.disputes.index') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors">
            Back to Disputes
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                <h2 class="text-xl font-bold text-white mb-4">Dispute Details</h2>

                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-slate-400 mb-1">Reason</p>
                        <p class="text-white font-medium">{{ ucfirst(str_replace('_', ' ', $dispute->reason)) }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-slate-400 mb-1">Description</p>
                        <div class="p-4 bg-white/5 rounded-lg text-slate-300">
                            {{ $dispute->description }}
                        </div>
                    </div>

                    @if($dispute->status === 'resolved')
                        <div class="pt-4 border-t border-slate-700">
                            <p class="text-sm text-slate-400 mb-1">Resolution</p>
                            <p class="text-green-400 font-medium">{{ ucfirst($dispute->resolution) }}</p>
                            
                            @if($dispute->admin_notes)
                                <div class="mt-2 p-3 bg-green-500/10 border border-green-500/20 rounded text-sm text-green-200">
                                    {{ $dispute->admin_notes }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @if($dispute->status === 'pending')
                <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                    <h2 class="text-xl font-bold text-white mb-4">Take Action</h2>
                    
                    <form action="{{ route('admin.disputes.resolve', $dispute->id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-400 mb-2">Resolution Type</label>
                            <select name="resolution" class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-blue-500 focus:outline-none" required>
                                <option value="">Select resolution...</option>
                                <option value="refund">Refund and Cancel Subscription</option>
                                <option value="warning">Issue Warning to Analyst</option>
                                <option value="dismiss">Dismiss Dispute</option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-slate-400 mb-2">Admin Notes</label>
                            <textarea name="admin_notes" rows="4" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-3 text-white focus:border-blue-500 focus:outline-none" required></textarea>
                        </div>

                        <button type="submit" class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition-colors">
                            Resolve Dispute
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                <h3 class="text-lg font-bold text-white mb-4">Involved Parties</h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase mb-2">Trader</p>
                        <p class="text-white font-medium">{{ $dispute->trader->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-slate-400">{{ $dispute->trader->email ?? '' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase mb-2">Analyst</p>
                        <p class="text-white font-medium">{{ $dispute->analyst->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-slate-400">{{ $dispute->analyst->email ?? '' }}</p>
                    </div>
                </div>
            </div>

            @if($dispute->subscription)
                <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50">
                    <h3 class="text-lg font-bold text-white mb-4">Subscription</h3>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Plan:</span>
                            <span class="text-white capitalize">{{ $dispute->subscription->plan }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Status:</span>
                            <span class="text-white">{{ ucfirst($dispute->subscription->status) }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
