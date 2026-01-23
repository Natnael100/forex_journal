@extends('layouts.app')

@section('title', 'Subscribe to ' . $analyst->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-12">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-white mb-4">Choose Your Plan</h1>
        <p class="text-xl text-slate-400">Subscribe to {{ $analyst->name }}'s coaching program</p>
    </div>

    <!-- Pricing Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
        @foreach($plans as $tier => $plan)
        <div class="relative bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-2xl p-8 border {{ $selectedPlan === $tier ? 'border-green-500 ring-2 ring-green-500/50' : 'border-slate-700/50' }} hover:border-green-500/50 transition-all hover:scale-105">
            @if($tier === 'premium')
            <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-green-500 to-emerald-600 text-white px-4 py-1 rounded-full text-sm font-bold shadow-lg">
                MOST POPULAR
            </div>
            @endif

            @if($selectedPlan === $tier)
            <div class="absolute top-4 right-4 text-green-400">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            </div>
            @endif

            <div class="text-center mb-6">
                <h3 class="text-2xl font-bold text-white mb-2 capitalize">{{ $tier }}</h3>
                <div class="flex items-baseline justify-center gap-1">
                    <span class="text-sm text-slate-400 font-bold mb-auto">ETB</span>
                    <span class="text-5xl font-extrabold text-white">{{ number_format($plan->price, 0) }}</span>
                    <span class="text-slate-400">/mo</span>
                </div>
            </div>

            <ul class="space-y-4 mb-8">
                @if(!empty($plan->features))
                    @foreach($plan->features as $featureKey)
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-slate-300 text-sm capitalize">{{ str_replace('_', ' ', $featureKey) }}</span>
                    </li>
                    @endforeach
                @endif
            </ul>

            <form action="{{ route('subscription.checkout', $analyst) }}" method="POST">
                @csrf
                <input type="hidden" name="plan" value="{{ $tier }}">
                <div class="space-y-3">
                    <button type="submit" class="w-full py-3 rounded-lg font-bold transition-all {{ $selectedPlan === $tier ? 'bg-green-600 hover:bg-green-700 text-white shadow-lg shadow-green-900/50' : ($tier === 'premium' ? 'bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white' : 'bg-slate-700 hover:bg-slate-600 text-white') }}">
                        Subscribe with Chapa
                    </button>
                    <!-- Payment Methods Icons -->
                    <div class="flex justify-center items-center gap-2 grayscale opacity-50 text-xs text-slate-500">
                        <span>Telebirr</span>
                        <span>•</span>
                        <span>CBE Birr</span>
                        <span>•</span>
                        <span>Banks</span>
                    </div>
                </div>
            </form>
        </div>
        @endforeach
    </div>

    <!-- Analyst Info -->
    <div class="bg-slate-800/30 rounded-xl p-6 border border-slate-700/50">
        <div class="flex items-center gap-4 mb-4">
            <img src="{{ $analyst->getProfilePhotoUrl('large') }}" alt="{{ $analyst->name }}" class="w-16 h-16 rounded-full">
            <div>
                <h3 class="text-xl font-bold text-white">{{ $analyst->name }}</h3>
                <div class="flex items-center gap-2">
                    <div class="flex items-center text-yellow-400">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 @if($i <= $analyst->getAverageRating()) fill-current @else text-slate-600 @endif" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        @endfor
                        <span class="ml-2 text-sm text-slate-300">{{ number_format($analyst->getAverageRating(), 1) }}</span>
                    </div>
                    <span class="text-slate-500">•</span>
                    <span class="text-sm text-slate-400">{{ $analyst->reviewsReceived()->approved()->count() }} reviews</span>
                </div>
            </div>
        </div>
        @if($analyst->bio)
        <p class="text-slate-300 text-sm">{{ $analyst->bio }}</p>
        @endif
    </div>
</div>
@endsection
