@extends('layouts.app')

@section('title', 'Request Performance Analyst')

@section('content')
<div class="max-w-2xl mx-auto py-12">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-3xl font-bold text-white mb-4">Performance Analyst Program</h1>
        <p class="text-slate-400">Get professional oversight, disciplined feedback, and AI-powered coaching to take your trading to the next level.</p>
    </div>

    @if($activeAssignment)
        <!-- Active State -->
        <div class="bg-gradient-to-br from-emerald-900/40 to-emerald-800/20 border border-emerald-500/30 rounded-xl p-8 text-center">
            <div class="w-16 h-16 bg-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                ✓
            </div>
            <h2 class="text-xl font-bold text-white mb-2">You are assigned to an Analyst</h2>
            <p class="text-slate-300 mb-6">Your performance is currently being monitored by <span class="text-white font-semibold">{{ $activeAssignment->analyst->name }}</span>.</p>
            <a href="{{ route('trader.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors">
                Go to Dashboard
            </a>
        </div>

    @elseif($existingRequest)
        <!-- Pending State -->
        <div class="bg-secondary-800 rounded-xl border border-slate-700 p-8">
            <div class="flex items-center gap-4 mb-6">
                @if($existingRequest->status === 'pending')
                    <div class="w-12 h-12 bg-yellow-500/20 text-yellow-400 rounded-full flex items-center justify-center text-xl">⏳</div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Application Pending</h2>
                        <p class="text-slate-400 text-sm">Submitted on {{ $existingRequest->created_at->format('M d, Y') }}</p>
                    </div>
                @elseif($existingRequest->status === 'approved' || $existingRequest->status === 'reviewed')
                     <div class="w-12 h-12 bg-blue-500/20 text-blue-400 rounded-full flex items-center justify-center text-xl">📝</div>
                    <div>
                         <h2 class="text-xl font-bold text-white">Action Required: Sign Agreement</h2>
                        <p class="text-slate-400 text-sm">Your request was approved. Please review the charter.</p>
                    </div>
                @endif
            </div>

            <div class="bg-slate-900/50 rounded-lg p-4 mb-6">
                <h3 class="text-sm font-semibold text-slate-300 mb-2">Your Motivation:</h3>
                <p class="text-slate-400 italic">"{{ $existingRequest->motivation }}"</p>
            </div>

            @if($existingRequest->status === 'pending')
                <div class="flex justify-between items-center">
                    <p class="text-xs text-slate-500">Admins usually review requests within 24-48 hours.</p>
                    <form action="{{ route('trader.analyst-request.cancel', $existingRequest->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel?');">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-400 hover:text-red-300 text-sm">Cancel Request</button>
                    </form>
                </div>
            @else
                <div class="mt-4">
                     <a href="{{ route('trader.analyst-request.consent', $existingRequest->id) }}" class="block w-full text-center py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg animate-pulse">
                        Review & Sign Charter ->
                    </a>
                </div>
            @endif
        </div>

    @else
        <!-- Application Form -->
        <div class="bg-secondary-800 rounded-xl border border-slate-700 p-8 shadow-2xl">
            <form action="{{ route('trader.analyst-request.store') }}" method="POST">
                @csrf
                
                <h2 class="text-xl font-bold text-white mb-6">Application Details</h2>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Why do you want a Performance Analyst?</label>
                    <textarea 
                        name="motivation" 
                        rows="4" 
                        class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="e.g. My technical analysis is good, but I struggle with emotions after a loss..."
                        required></textarea>
                     <p class="text-xs text-slate-500 mt-2">This helps us pair you with the right specialist.</p>
                </div>

                <div class="mb-6">
                    <h3 class="text-sm font-medium text-slate-300 mb-3">Program Requirements</h3>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2 text-sm text-slate-400">
                             <span class="text-green-400">✓</span> You must journal at least 5 trades/week
                        </li>
                        <li class="flex items-center gap-2 text-sm text-slate-400">
                             <span class="text-green-400">✓</span> You agree to receive critical feedback
                        </li>
                         <li class="flex items-center gap-2 text-sm text-slate-400">
                             <span class="text-green-400">✓</span> You grant view-access to your trade history
                        </li>
                    </ul>
                </div>

                <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/20 transition-all transform hover:scale-[1.01]">
                    Submit Application
                </button>
            </form>
        </div>
    @endif
</div>
@endsection
