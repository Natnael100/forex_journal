@props(['user', 'showBio' => true, 'compact' => false])

<div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-sm border border-slate-700/50 rounded-2xl p-5 shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group">
    <div class="flex items-start gap-4">
        <!-- Profile Photo -->
        <div class="relative flex-shrink-0">
            <a href="{{ route('profile.show', $user->username) }}" class="block relative">
                <img src="{{ $user->getProfilePhotoUrl('medium') }}" 
                     alt="{{ $user->name }}" 
                     class="w-16 h-16 {{ $compact ? 'md:w-14 md:h-14' : 'md:w-16 md:h-16' }} rounded-full border-2 border-purple-500/30 group-hover:border-purple-500/60 transition-all duration-300 ring-2 ring-transparent group-hover:ring-purple-500/20" />
                
                @if($user->is_profile_verified)
                    <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center border-2 border-slate-900 shadow-lg">
                        <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                @endif
                
                @if($user->show_last_active && $user->updated_at->gt(now()->subHours(24)))
                    <div class="absolute top-0 right-0 w-3.5 h-3.5 bg-emerald-400 border-2 border-slate-900 rounded-full animate-pulse shadow-lg"></div>
                @endif
            </a>
        </div>

        <!-- User Info -->
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <a href="{{ route('profile.show', $user->username) }}" class="group/name">
                        <h4 class="text-white font-bold text-base mb-0.5 truncate group-hover/name:text-purple-400 transition-colors">
                            {{ $user->name }}
                        </h4>
                    </a>
                    <p class="text-slate-400 text-sm truncate">@{{ $user->username }}</p>
                </div>
            </div>

            <!-- Role Badge -->
            <div class="flex items-center gap-2 mt-2">
                @if($user->hasRole('trader'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-500/20 text-emerald-400 rounded-lg text-xs font-semibold border border-emerald-500/30">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        Trader
                    </span>
                @elseif($user->hasRole('analyst'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-purple-500/20 text-purple-400 rounded-lg text-xs font-semibold border border-purple-500/30">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Analyst
                    </span>
                @elseif($user->hasRole('admin'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-500/20 text-amber-400 rounded-lg text-xs font-semibold border border-amber-500/30">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Admin
                    </span>
                @endif
                
                @if($user->experience_level)
                    <span class="inline-flex items-center px-2.5 py-1 bg-blue-500/20 text-blue-400 rounded-lg text-xs font-medium border border-blue-500/30 capitalize">
                        {{ $user->experience_level }}
                    </span>
                @endif
            </div>

            <!-- Bio (if enabled) -->
            @if($showBio && $user->bio && !$compact)
                <p class="text-slate-300 text-sm mt-3 line-clamp-2">
                    {{ Str::limit($user->bio, 100) }}
                </p>
            @endif

            <!-- Additional Info -->
            @if(!$compact)
                <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-slate-400">
                    @if($user->hasRole('trader') && $user->trading_style)
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            <span class="font-medium text-slate-300">{{ $user->trading_style }}</span>
                        </div>
                    @endif
                    
                    @if($user->hasRole('analyst') && $user->specialization)
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                            <span class="font-medium text-slate-300">{{ Str::limit($user->specialization, 30) }}</span>
                        </div>
                    @endif
                    
                    @if($user->country)
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ $user->country }}</span>
                        </div>
                    @endif
                </div>
            @endif

            <!-- View Profile Link -->
            <a href="{{ route('profile.show', $user->username) }}" 
               class="inline-flex items-center gap-1.5 mt-3 text-sm text-purple-400 hover:text-purple-300 font-medium transition-colors group/link">
                <span>View Profile</span>
                <svg class="w-4 h-4 group-hover/link:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </a>
        </div>
    </div>
</div>
