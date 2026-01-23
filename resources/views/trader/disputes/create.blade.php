@extends('layouts.app')

@section('title', 'File a Dispute')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('trader.subscriptions.show', $subscription->id) }}" class="text-slate-400 hover:text-white transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Subscription
            </a>
        </div>

        <div class="bg-slate-800 rounded-xl border border-slate-700 p-8">
            <h1 class="text-2xl font-bold text-white mb-2">File a Dispute</h1>
            <p class="text-slate-400 mb-6">Report an issue with your subscription to {{ $subscription->analyst->name }}</p>

            <form action="{{ route('trader.disputes.store', $subscription->id) }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Reason for Dispute</label>
                        <select name="reason" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                            <option value="" disabled selected>Select a reason...</option>
                            <option value="analyst_inactive">Analyst Inactivity (Ghosting)</option>
                            <option value="poor_quality">Poor Service Quality</option>
                            <option value="scam">Scam / Fraud</option>
                            <option value="other">Other Issue</option>
                        </select>
                        @error('reason')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Description</label>
                        <textarea name="description" rows="6" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-white placeholder-slate-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none" placeholder="Please describe the issue in detail. bold specifics like dates and missing deliverables..."></textarea>
                        <p class="text-xs text-slate-500 mt-2">Minimum 20 characters. Be specific to help us resolve the issue faster.</p>
                        @error('description')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-blue-600/10 border border-blue-600/20 rounded-lg p-4">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-sm text-blue-300/90">
                                <p class="font-medium mb-1">What happens next?</p>
                                <ul class="list-disc list-inside space-y-1 opacity-80">
                                    <li>An admin will review your case within 24-48 hours</li>
                                    <li>We may contact you for more evidence</li>
                                    <li>If resolved in your favor, you may receive a refund</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-4 border-t border-slate-700">
                        <button type="submit" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                            Submit Dispute
                        </button>
                        <a href="{{ route('trader.subscriptions.show', $subscription->id) }}" class="px-6 py-2.5 text-slate-400 hover:text-white font-medium transition-colors">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
