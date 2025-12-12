@extends('layouts.app')

@section('title', '@' . $user->username)

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Hero Section with Cover Photo -->
    <div class="relative mb-8 rounded-2xl overflow-hidden shadow-2xl">
        <!-- Cover Photo with Gradient Overlay -->
        <div class="relative h-64 md:h-80">
            @if($user->cover_photo)
                <img src="{{ $user->getCoverPhotoUrl() }}" alt="Cover" class="w-full h-full object-cover" />
            @else
                <div class="w-full h-full bg-gradient-to-br from-purple-600/30 via-pink-600/30 to-blue-600/30"></div>
            @endif
            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/50 to-transparent"></div>
        </div>
        
        <!-- Profile Info Overlay -->
        <div class="relative px-6 md:px-8 pb-6 -mt-20">
            <div class="flex flex-col md:flex-row items-center md:items-end gap-6">
                <!-- Profile Photo -->
                <div class="relative group">
                    <div class="relative">
                        <img src="{{ $user->getProfilePhotoUrl('large') }}" 
                             alt="{{ $user->name }}" 
                             class="w-32 h-32 md:w-40 md:h-40 rounded-full border-4 border-slate-900 shadow-2xl ring-4 ring-purple-500/30 transition-transform group-hover:scale-105" />
                        @if($user->is_profile_verified)
                            <div class="absolute bottom-2 right-2 w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center border-4 border-slate-900 shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- User Info -->
                <div class="flex-1 text-center md:text-left">
                    <h1 class="text-3xl md:text-4xl font-bold text-white mb-2 flex items-center justify-center md:justify-start gap-3">
                        {{ $user->name }}
                    </h1>
                    <p class="text-xl text-slate-400 mb-3">@{{ $user->username }}</p>
                    
                    <!-- Meta Info -->
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 text-sm text-slate-300">
                        @if($user->country)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ $user->country }}</span>
                            </div>
                        @endif
                        
                        @if($user->timezone)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ $user->timezone }}</span>
                            </div>
                        @endif
                        
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>Joined {{ $user->created_at->format('M Y') }}</span>
                        </div>
                        
                        @if($user->show_last_active && $user->updated_at)
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                                <span>Active {{ $user->updated_at->diffForHumans() }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Action Button -->
                @if(Auth::check() && Auth::id() === $user->id)
                    <a href="{{ route('profile.edit') }}" 
                       class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-medium shadow-lg hover:shadow-xl transition-all duration-200 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Profile
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Role & Experience Badge Bar -->
    <div class="flex flex-wrap items-center gap-3 mb-8">
        @if($user->hasRole('trader'))
            <div class="px-4 py-2 bg-gradient-to-r from-emerald-500/20 to-teal-500/20 border border-emerald-500/30 rounded-xl">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    <span class="text-emerald-400 font-semibold">Trader</span>
                </div>
            </div>
        @elseif($user->hasRole('analyst'))
            <div class="px-4 py-2 bg-gradient-to-r from-purple-500/20 to-pink-500/20 border border-purple-500/30 rounded-xl">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span class="text-purple-400 font-semibold">Performance Analyst</span>
                </div>
            </div>
        @endif
        
        @if($user->experience_level)
            <div class="px-4 py-2 bg-blue-500/20 border border-blue-500/30 rounded-xl">
                <span class="text-blue-400 font-medium capitalize">{{ $user->experience_level }}</span>
            </div>
        @endif
        
        @if(Auth::check() && Auth::id() === $user->id && $completion < 100)
            <div class="px-4 py-2 bg-amber-500/20 border border-amber-500/30 rounded-xl">
                <span class="text-amber-400 font-medium">{{ $completion }}% Complete</span>
            </div>
        @endif
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - About & Quick Info -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Profile Completeness (if viewing own profile) -->
            @if(Auth::check() && Auth::id() === $user->id && $completion < 100)
                <x-profile-completeness :completion="$completion" :user="$user" />
            @endif
            
            <!-- Bio Card -->
            @if($user->bio)
                <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-sm border border-slate-700/50 rounded-2xl p-6 shadow-xl">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        About
                    </h3>
                    <p class="text-slate-300 leading-relaxed">{{ $user->bio }}</p>
                </div>
            @endif

            <!-- Trading Profile (for Traders) -->
            @if($user->hasRole('trader') && ($user->trading_style || $user->preferred_sessions || $user->favorite_pairs))
                <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-sm border border-slate-700/50 rounded-2xl p-6 shadow-xl">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        Trading Profile
                    </h3>
                    
                    <div class="space-y-4">
                        @if($user->trading_style)
                            <div>
                                <span class="text-xs text-slate-400 uppercase tracking-wide font-medium">Trading Style</span>
                                <p class="text-white font-semibold mt-1">{{ $user->trading_style }}</p>
                            </div>
                        @endif
                        
                        @if($user->preferred_sessions && count($user->preferred_sessions) > 0)
                            <div>
                                <span class="text-xs text-slate-400 uppercase tracking-wide font-medium">Preferred Sessions</span>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    @foreach($user->preferred_sessions as $session)
                                        <span class="px-3 py-1 bg-blue-500/20 text-blue-400 rounded-lg text-sm font-medium capitalize border border-blue-500/30">
                                            {{ $session }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        @if($user->favorite_pairs && count($user->favorite_pairs) > 0)
                            <div>
                                <span class="text-xs text-slate-400 uppercase tracking-wide font-medium">Favorite Pairs</span>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    @foreach($user->favorite_pairs as $pair)
                                        <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 rounded-lg text-sm font-medium border border-emerald-500/30">
                                            {{ $pair }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Analyst Specialization -->
            @if($user->hasRole('analyst') && $user->specialization)
                <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-sm border border-slate-700/50 rounded-2xl p-6 shadow-xl">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                        Specialization
                    </h3>
                    <p class="text-white font-semibold">{{ $user->specialization }}</p>
                </div>
            @endif

            <!-- Profile Tags -->
            @if($user->profile_tags && count($user->profile_tags) > 0)
                <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-sm border border-slate-700/50 rounded-2xl p-6 shadow-xl">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Skills & Interests
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($user->profile_tags as $tag)
                            <span class="px-3 py-1.5 bg-gradient-to-r from-purple-500/20 to-pink-500/20 text-purple-400 rounded-full text-sm font-medium border border-purple-500/30">
                                #{{ $tag }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Social Links -->
            @if($user->social_links && count($user->social_links) > 0)
                <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-sm border border-slate-700/50 rounded-2xl p-6 shadow-xl">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                        Connect
                    </h3>
                    <div class="space-y-3">
                        @foreach($user->social_links as $platform => $handle)
                            @if($handle)
                                <div class="flex items-center gap-3 text-slate-300 hover:text-white transition-colors group">
                                    <div class="w-10 h-10 bg-slate-700/50 rounded-lg flex items-center justify-center group-hover:bg-slate-700 transition-colors">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-400 capitalize">{{ $platform }}</p>
                                        <p class="font-medium">{{ $handle }}</p>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column - Activity & Stats -->
        <div class="lg:col-span-2">
            @if($user->hasRole('trader') && isset($recentTrades) && $recentTrades->count() > 0)
                <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-sm border border-slate-700/50 rounded-2xl p-6 shadow-xl">
                    <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Recent Trades
                    </h3>
                    
                    <div class="space-y-3">
                        @foreach($recentTrades as $trade)
                            <div class="bg-slate-900/50 border border-slate-700/50 rounded-xl p-4 hover:border-purple-500/30 hover:bg-slate-900/70 transition-all duration-200 group">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-white font-semibold text-lg group-hover:text-purple-400 transition-colors">{{ $trade->pair }}</h4>
                                        <div class="flex items-center gap-2 mt-1">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="text-sm text-slate-400">{{ $trade->entry_date->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-2xl font-bold {{ $trade->profit_loss >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                                            {{ $trade->profit_loss >= 0 ? '+' : '' }}${{ number_format($trade->profit_loss, 2) }}
                                        </p>
                                        <span class="inline-block mt-1 text-xs px-2.5 py-1 rounded-lg font-semibold {{ $trade->outcome->value === 'win' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }}">
                                            {{ $trade->outcome->label() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-sm border border-slate-700/50 rounded-2xl p-12 text-center shadow-xl">
                    <div class="w-24 h-24 bg-gradient-to-br from-purple-500/20 to-pink-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">
                        @if(Auth::check() && Auth::id() === $user->id)
                            Welcome to Your Profile!
                        @else
                            {{ $user->name }}'s Profile
                        @endif
                    </h3>
                    <p class="text-slate-400 text-lg">
                        @if($user->isProfileComplete())
                            Profile is complete and ready! 🎉
                        @else
                            Building something great...
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .max-w-6xl > * {
        animation: fadeIn 0.6s ease-out forwards;
    }
    
    .max-w-6xl > *:nth-child(2) {
        animation-delay: 0.1s;
    }
    
    .max-w-6xl > *:nth-child(3) {
        animation-delay: 0.2s;
    }
</style>
@endsection
