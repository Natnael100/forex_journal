@extends('layouts.app')

@section('title', 'Subscription Successful')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-gradient-to-br from-green-900/20 to-green-800/20 backdrop-blur-xl rounded-2xl p-8 border border-green-700/50 text-center">
        <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-white mb-2">Subscription Successful!</h1>
        <p class="text-slate-300 mb-6">Welcome to your coaching program. Your analyst will be notified and will reach out soon.</p>
        <a href="{{ route('trader.dashboard') }}" class="inline-block px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold rounded-lg transition-all">
            Go to Dashboard
        </a>
    </div>
</div>
@endsection
