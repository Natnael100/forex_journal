@extends('layouts.app')

@section('title', 'Subscription Cancelled')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-2xl p-8 border border-slate-700/50 text-center">
        <div class="w-16 h-16 bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-white mb-2">Subscription Cancelled</h1>
        <p class="text-slate-300 mb-6">No worries! You can subscribe anytime.</p>
        <div class="flex gap-4 justify-center">
            <a href="{{ route('analysts.index') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition-colors">
                Browse Analysts
            </a>
            <a href="{{ route('trader.dashboard') }}" class="px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white font-bold rounded-lg transition-colors">
                Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
