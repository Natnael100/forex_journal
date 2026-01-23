@extends('layouts.app')

@section('title', $analyst->name . ' - Performance Analyst')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-2xl p-8 border border-slate-700/50 mb-8">
        <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
            <img src="{{ $analyst->getProfilePhotoUrl('large') }}" alt="{{ $analyst->name }}" class="w-32 h-32 rounded-full ring-4 ring-blue-500/50">
            
            <div class="flex-1 text-center md:text-left">
                <div class="flex items-center justify-center md:justify-start gap-2 mb-2">
                    <h1 class="text-3xl font-bold text-white">{{ $analyst->name }}</h1>
                    @if($analyst->is_profile_verified)
                        <svg class="w-6 h-6 text-blue-500 fill-current" viewBox="0 0 20 20" title="Verified Analyst">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                </div>

                <!-- Rating -->
                <div class="flex items-center gap-2 justify-center md:justify-start mb-4">
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            <!-- Explicit fill style for reliability -->
                            <svg class="w-5 h-5" style="fill: {{ $i <= $stats['average_rating'] ? '#fbbf24' : '#475569' }}" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="text-white font-bold">{{ number_format($stats['average_rating'], 1) }}</span>
                    <span class="text-slate-400">({{ $stats['total_reviews'] }} reviews)</span>
                </div>

                <!-- Bio -->
                @if($analyst->bio)
                <p class="text-slate-300 mb-4">{{ $analyst->bio }}</p>
                @endif

                <!-- Specs & Certs -->
                <div class="flex flex-wrap gap-2 mb-4">
                    @if(!empty($analyst->specializations))
                        @foreach($analyst->specializations as $spec)
                        <span class="px-3 py-1 bg-blue-500/10 text-blue-400 rounded-full text-sm font-medium border border-blue-500/20">
                            {{ $spec }}
                        </span>
                        @endforeach
                    @endif
                    @if(!empty($analyst->certifications))
                        @foreach($analyst->certifications as $cert)
                        <span class="px-3 py-1 bg-purple-500/10 text-purple-400 rounded-full text-sm font-medium border border-purple-500/20">
                            🏆 {{ $cert }}
                        </span>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="flex-shrink-0 flex flex-col gap-3">
                @if(auth()->id() !== $analyst->id)
                    <!-- Pricing Grid (Phase 7) -->
                    @php
                        $plans = $analyst->plans()->where('is_active', true)->orderBy('price')->get();
                        
                        // Check for active subscription
                        $currentPlan = null;
                        if(auth()->check() && auth()->user()->hasRole('trader')) {
                            $activeSub = auth()->user()->subscriptionsAsTrader()
                                ->where('analyst_id', $analyst->id)
                                ->where('status', 'active')
                                ->first();
                            if($activeSub) {
                                $currentPlan = $activeSub->plan;
                            }
                        }
                    @endphp

                    @if($plans->count() > 0)
                        <div class="grid grid-cols-1 gap-4">
                            @foreach($plans as $plan)
                            <div class="bg-slate-800/50 rounded-xl p-5 border {{ $plan->tier === 'premium' ? 'border-blue-500/50 ring-1 ring-blue-500/20' : 'border-slate-700/50' }} transition-all hover:border-blue-500/30">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-white capitalize">{{ $plan->tier }}</h3>
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-2xl font-bold text-white">${{ number_format($plan->price, 0) }}</span>
                                            <span class="text-sm text-slate-400">/mo</span>
                                        </div>
                                    </div>
                                    @if($plan->tier === 'premium')
                                        <span class="px-2 py-1 bg-blue-500/20 text-blue-400 text-xs font-bold rounded uppercase">Popular</span>
                                    @endif
                                </div>
                                
                                @if(!empty($plan->features))
                                <ul class="space-y-2 mb-6">
                                    @foreach($plan->features as $key)
                                    <li class="flex items-start gap-2 text-sm text-slate-300">
                                        <svg class="w-4 h-4 text-emerald-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span class="capitalize">{{ str_replace('_', ' ', $key) }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                                @endif

                                @if(auth()->id() !== $analyst->id)
                                    @if($currentPlan === $plan->tier)
                                        <button disabled class="block w-full py-2.5 bg-emerald-500/20 text-emerald-400 font-bold rounded-lg text-center border border-emerald-500/30 cursor-default">
                                            Current Plan
                                        </button>
                                    @else
                                        <a href="{{ route('subscription.create', ['analyst' => $analyst, 'plan' => $plan->tier]) }}" class="block w-full py-2.5 {{ $plan->tier === 'premium' ? 'bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white' : 'bg-slate-700 hover:bg-slate-600 text-slate-200' }} font-bold rounded-lg text-center transition-all shadow-lg">
                                            Subscribe
                                        </a>
                                    @endif
                                @else
                                    <div class="text-center py-2 bg-slate-900/50 rounded border border-slate-700 border-dashed text-slate-500 text-sm">
                                        Owner View
                                    </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Fallback if no plans configured yet -->
                        <div class="p-6 bg-slate-800/50 rounded-xl border border-slate-700 text-center">
                            <p class="text-slate-400">Subscription plans are being updated.</p>
                        </div>
                    @endif
                @else
                    <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-4 text-center mb-4">
                        <p class="text-blue-400 font-medium">This is how traders see your profile.</p>
                        <p class="text-xs text-slate-400 mt-1">Use the "Edit Profile" button to update details.</p>
                    </div>
                @endif
                
                @auth
                    @if(auth()->id() === $analyst->id)
                        <a href="{{ route('analyst.profile.edit') }}" class="block px-8 py-3 bg-slate-700 hover:bg-slate-600 text-white font-bold rounded-lg transition-all border border-slate-600 text-center mb-3">
                            Edit Profile
                        </a>
                    @endif

                    @if(auth()->user()->hasRole('trader') && auth()->id() !== $analyst->id)
                    <form action="{{ route('conversations.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="recipient_id" value="{{ $analyst->id }}">
                        <button type="submit" class="w-full px-8 py-3 bg-slate-700 hover:bg-slate-600 text-white font-bold rounded-lg transition-all border border-slate-600">
                            Message
                        </button>
                    </form>
                    @endif
                @endauth

                @if($analyst->hourly_rate)
                <p class="text-center text-sm text-slate-400">Starting at ${{ number_format($analyst->hourly_rate, 0) }}/hour</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700/50 text-center">
            <p class="text-3xl font-bold text-white">{{ $stats['years_experience'] }}</p>
            <p class="text-sm text-slate-400 mt-1">Years Experience</p>
        </div>
        <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700/50 text-center">
            <p class="text-3xl font-bold text-white">{{ $stats['active_clients'] }}</p>
            <p class="text-sm text-slate-400 mt-1">Active Clients</p>
        </div>
        <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700/50 text-center">
            <p class="text-3xl font-bold text-white">{{ number_format($stats['average_rating'], 1) }}</p>
            <p class="text-sm text-slate-400 mt-1">Avg Rating</p>
        </div>
        <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700/50 text-center">
            <p class="text-3xl font-bold text-white">{{ $stats['total_reviews'] }}</p>
            <p class="text-sm text-slate-400 mt-1">Total Reviews</p>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700/50">
        <h2 class="text-2xl font-bold text-white mb-6">Client Reviews</h2>
        
        <!-- Review Submission Form -->
        @auth
            @if(auth()->user()->hasRole('trader'))
                @php
                    $hasActiveSubscription = auth()->user()->subscriptionsAsTrader()
                        ->where('analyst_id', $analyst->id)
                        ->active()
                        ->exists();
                    $hasReviewed = auth()->user()->reviewsGiven()
                        ->where('analyst_id', $analyst->id)
                        ->exists();
                @endphp

                @if($hasActiveSubscription && !$hasReviewed)
                    <div class="mb-8 p-6 bg-gradient-to-br from-blue-600/10 to-purple-600/10 rounded-xl border border-blue-500/20">
                        <h3 class="text-lg font-bold text-white mb-4">Share Your Experience</h3>
                        
                        <form action="{{ route('analysts.review.store', $analyst) }}" method="POST" id="reviewForm">
                            @csrf
                            
                            <!-- Star Rating -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-slate-300 mb-2">Rating *</label>
                                <!-- Hidden Input for Form Submission -->
                                <input type="hidden" name="rating" id="rating-input" value="{{ old('rating') }}" required>
                                
                                <div class="flex gap-2 p-2" id="star-container" onmouseleave="resetStars()">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button" 
                                                class="focus:outline-none transform transition-transform hover:scale-110 p-1"
                                                onclick="setRatingValue({{ $i }})"
                                                onmouseenter="hoverStar({{ $i }})">
                                            <!-- Removed fill-current, using direct style.fill -->
                                            <svg id="star-icon-{{ $i }}" 
                                                 class="w-8 h-8 pointer-events-none transition-colors duration-200" 
                                                 style="fill: {{ old('rating') >= $i ? '#fbbf24' : '#475569' }}"
                                                 viewBox="0 0 20 20">
                                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                            </svg>
                                        </button>
                                    @endfor
                                </div>
                                <p class="text-xs text-slate-500 mt-1" id="rating-text">Select a rating</p>
                                @error('rating')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Comment -->
                            <div class="mb-4">
                                <label for="comment" class="block text-sm font-medium text-slate-300 mb-2">Your Review *</label>
                                <textarea 
                                    name="comment" 
                                    id="comment" 
                                    rows="4" 
                                    maxlength="500"
                                    required
                                    placeholder="Share your experience working with this analyst..."
                                    class="w-full bg-slate-900/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ old('comment') }}</textarea>
                                <p class="text-xs text-slate-500 mt-1 text-right">
                                    <span id="charCount">0</span>/500 characters
                                </p>
                                @error('comment')
                                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end gap-3">
                                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-lg transition-all shadow-lg">
                                    Submit Review
                                </button>
                            </div>
                        </form>
                    </div>
                @elseif($hasReviewed)
                    <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-lg">
                        <p class="text-sm text-emerald-400">✓ Thank you for your review!</p>
                    </div>
                @endif
            @endif
        @endauth
        
        @forelse($analyst->reviewsReceived as $review)
        <div class="mb-6 pb-6 border-b border-slate-700/50 last:border-0">
            <div class="flex items-start gap-4">
                <img src="{{ $review->trader->getProfilePhotoUrl('large') }}" alt="{{ $review->trader->name }}" class="w-12 h-12 rounded-full">
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <p class="font-bold text-white">{{ $review->trader->name }}</p>
                            <div class="flex items-center gap-2">
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        <!-- Explicit fill style for reliability -->
                                        <svg class="w-4 h-4" style="fill: {{ $i <= $review->rating ? '#fbbf24' : '#475569' }}" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-xs text-slate-500">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    @if($review->comment)
                    <p class="text-slate-300 text-sm">{{ $review->comment }}</p>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <p class="text-center text-slate-400 py-8">No reviews yet. Be the first to work with this analyst!</p>
        @endforelse
    </div>
</div>

<script>
    // Explicit Global State
    window.currentRating = parseInt("{{ old('rating', 0) }}");

    window.hoverStar = function(rating) {
        updateVisuals(rating);
    }

    window.resetStars = function() {
        updateVisuals(window.currentRating);
    }

    window.setRatingValue = function(rating) {
        window.currentRating = rating;
        
        const hiddenInput = document.getElementById('rating-input');
        const ratingText = document.getElementById('rating-text');
        
        if (hiddenInput) {
            hiddenInput.value = rating;
        }
        
        if (ratingText) {
            ratingText.textContent = rating + ' Star' + (rating > 1 ? 's' : '');
            ratingText.className = 'text-sm text-yellow-400 mt-1 font-bold';
        }
        
        updateVisuals(rating);
    }

    window.updateVisuals = function(rating) {
        console.log('Updating Visuals:', rating);
        for (let i = 1; i <= 5; i++) {
            const star = document.getElementById('star-icon-' + i);
            if (!star) {
                console.error('Star icon not found:', i);
                continue;
            }

            if (i <= rating) {
                // Active: Yellow (Direct Fill)
                star.style.fill = '#fbbf24'; 
            } else {
                // Inactive: Slate (Direct Fill)
                star.style.fill = '#475569';
            }
        }
    }

    // Initialize on load (to handle old input or reset)
    document.addEventListener('DOMContentLoaded', () => {
        updateVisuals(window.currentRating);
        
        const commentTextarea = document.getElementById('comment');
        const charCount = document.getElementById('charCount');
        if (commentTextarea && charCount) {
            charCount.textContent = commentTextarea.value.length;
            commentTextarea.addEventListener('input', (e) => {
                charCount.textContent = e.target.value.length;
            });
        }
    });
</script>
@endsection
