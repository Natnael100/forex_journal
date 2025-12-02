@extends('layouts.auth')

@section('title', 'Verify Email')

@section('content')
    <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-500/20 rounded-full mb-4">
            <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h2 class="text-3xl font-bold text-white mb-2">Verify Your Email</h2>
        <p class="text-slate-400">We've sent a verification link to your email address</p>
    </div>

    <div class="space-y-6">
        <!-- Info Message -->
        <div class="px-4 py-3 bg-blue-500/20 border border-blue-500/50 rounded-lg text-blue-300 text-sm">
            <p>Before proceeding, please check your email for a verification link.</p>
        </div>

        <!-- Success Message -->
        @if (session('status') == 'verification-link-sent')
            <div class="px-4 py-3 bg-emerald-500/20 border border-emerald-500/50 rounded-lg text-emerald-300 text-sm">
                A new verification link has been sent to your email address.
            </div>
        @endif

        <!-- Resend Form -->
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button 
                type="submit" 
                class="w-full px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-lg shadow-lg shadow-emerald-500/50 hover:shadow-xl hover:shadow-emerald-500/60 transition-all duration-200 transform hover:-translate-y-0.5"
            >
                Resend Verification Email
            </button>
        </form>

        <!-- Logout Form -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button 
                type="submit" 
                class="w-full px-6 py-3 bg-white/5 hover:bg-white/10 border border-white/10 text-slate-300 font-medium rounded-lg transition-all duration-200"
            >
                Logout
            </button>
        </form>
    </div>
@endsection
