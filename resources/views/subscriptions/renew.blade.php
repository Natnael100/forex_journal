@extends('layouts.app')

@section('title', 'Renew Subscription')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-slate-800/50 p-8 rounded-2xl border border-slate-700 backdrop-blur-xl">
        <div class="text-center">
            <h2 class="mt-6 text-3xl font-extrabold text-white">Renew Subscription</h2>
            <p class="mt-2 text-sm text-slate-400">
                Continue your access to {{ $subscription->analyst->name }}'s coaching
            </p>
        </div>

        <div class="mt-8 space-y-6">
            <!-- Plan Details -->
            <div class="bg-slate-900/50 rounded-xl p-6 border border-slate-700">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-slate-400">Plan</span>
                    <span class="text-white font-bold capitalize">{{ $subscription->plan }} Plan</span>
                </div>
                <div class="flex justify-between items-center mb-4">
                    <span class="text-slate-400">Price</span>
                    <span class="text-white font-bold">{{ number_format($subscription->price, 2) }} ETB</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400">Expires On</span>
                    <span class="text-red-400 font-bold">{{ $subscription->current_period_end->format('M d, Y') }}</span>
                </div>
            </div>

            <!-- Payment Methods Info -->
            <div class="text-center">
                <p class="text-xs text-slate-500 mb-2">Secure payment via Chapa</p>
                <div class="flex justify-center items-center gap-3 text-slate-400 text-xs uppercase tracking-wider">
                    <span>Telebirr</span>
                    <span>•</span>
                    <span>CBE Birr</span>
                    <span>•</span>
                    <span>M-PESA</span>
                    <span>•</span>
                    <span>Banks</span>
                </div>
            </div>

            <form action="{{ route('subscription.renew', $subscription) }}" method="POST">
                @csrf
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all shadow-lg shadow-green-900/50">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-green-300 group-hover:text-green-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </span>
                    Renew Now - {{ number_format($subscription->price, 0) }} ETB
                </button>
            </form>
            
            <div class="text-center mt-4">
                <a href="{{ route('trader.dashboard') }}" class="text-sm text-slate-400 hover:text-white transition-colors">
                    Cancel and return to dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
