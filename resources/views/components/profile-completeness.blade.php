@props(['completion', 'user'])

<div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-sm border border-slate-700/50 rounded-2xl p-6 shadow-xl">
    <div class="text-center">
        <!-- Circular Progress Ring -->
        <div class="relative inline-flex items-center justify-center mb-4">
            <svg class="transform -rotate-90" width="120" height="120">
                <!-- Background Circle -->
                <circle cx="60" cy="60" r="52" 
                        stroke="currentColor" 
                        stroke-width="8" 
                        fill="none" 
                        class="text-slate-700/50" />
                
                <!-- Progress Circle -->
                <circle cx="60" cy="60" r="52" 
                        stroke="url(#gradient)" 
                        stroke-width="8" 
                        fill="none"
                        class="transition-all duration-1000 ease-out"
                        stroke-dasharray="326.56"
                        stroke-dashoffset="calc(326.56 - (326.56 * {{ $completion }} / 100))"
                        stroke-linecap="round" />
                
                <!-- Gradient Definition -->
                <defs>
                    <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                        @if($completion >= 100)
                            <stop offset="0%" style="stop-color:#10B981;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#059669;stop-opacity:1" />
                        @elseif($completion >= 75)
                            <stop offset="0%" style="stop-color:#3B82F6;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#8B5CF6;stop-opacity:1" />
                        @elseif($completion >= 50)
                            <stop offset="0%" style="stop-color:#8B5CF6;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#EC4899;stop-opacity:1" />
                        @else
                            <stop offset="0%" style="stop-color:#F59E0B;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#EF4444;stop-opacity:1" />
                        @endif
                    </linearGradient>
                </defs>
            </svg>
            
            <!-- Percentage in Center -->
            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-4xl font-bold bg-gradient-to-br 
                    @if($completion >= 100) from-emerald-400 to-teal-400
                    @elseif($completion >= 75) from-blue-400 to-purple-400
                    @elseif($completion >= 50) from-purple-400 to-pink-400
                    @else from-amber-400 to-red-400
                    @endif
                    bg-clip-text text-transparent">
                    {{ $completion }}%
                </span>
                <span class="text-xs text-slate-400 font-medium mt-1">Complete</span>
            </div>
        </div>

        <!-- Status Message -->
        <div class="mb-4">
            @if($completion >= 100)
                <div class="flex items-center justify-center gap-2 mb-2">
                    <svg class="w-6 h-6 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <h3 class="text-lg font-bold text-emerald-400">Profile Complete!</h3>
                </div>
                <p class="text-sm text-slate-300">Your profile looks amazing! 🎉</p>
            @elseif($completion >= 75)
                <h3 class="text-lg font-bold text-blue-400 mb-2">Almost There!</h3>
                <p class="text-sm text-slate-300">Just a few more fields to go</p>
            @elseif($completion >= 50)
                <h3 class="text-lg font-bold text-purple-400 mb-2">Good Progress!</h3>
                <p class="text-sm text-slate-300">You're halfway there, keep going!</p>
            @else
                <h3 class="text-lg font-bold text-amber-400 mb-2">Let's Get Started!</h3>
                <p class="text-sm text-slate-300">Complete your profile for better visibility</p>
            @endif
        </div>

        <!-- Missing Fields -->
        @if($completion < 100)
            <div class="bg-slate-900/50 border border-slate-700/50 rounded-xl p-4 text-left">
                <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Complete These
                </h4>
                <div class="space-y-2">
                    @if(!$user->username)
                        <div class="flex items-center gap-2 text-sm text-slate-300">
                            <svg class="w-4 h-4 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>Set your username</span>
                        </div>
                    @endif
                    
                    @if(!$user->bio)
                        <div class="flex items-center gap-2 text-sm text-slate-300">
                            <svg class="w-4 h-4 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>Add a bio</span>
                        </div>
                    @endif
                    
                    @if(!$user->profile_photo)
                        <div class="flex items-center gap-2 text-sm text-slate-300">
                            <svg class="w-4 h-4 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>Upload profile photo</span>
                        </div>
                    @endif
                    
                    @if(!$user->country)
                        <div class="flex items-center gap-2 text-sm text-slate-300">
                            <svg class="w-4 h-4 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>Add your country</span>
                        </div>
                    @endif
                    
                    @if(!$user->experience_level)
                        <div class="flex items-center gap-2 text-sm text-slate-300">
                            <svg class="w-4 h-4 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>Select experience level</span>
                        </div>
                    @endif
                    
                    @if($user->hasRole('trader') && !$user->trading_style)
                        <div class="flex items-center gap-2 text-sm text-slate-300">
                            <svg class="w-4 h-4 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>Add trading style</span>
                        </div>
                    @endif
                    
                    @if($user->hasRole('analyst') && !$user->specialization)
                        <div class="flex items-center gap-2 text-sm text-slate-300">
                            <svg class="w-4 h-4 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>Add your specialization</span>
                        </div>
                    @endif
                    
                    @if($user->hasRole('trader') && (!$user->preferred_sessions || count($user->preferred_sessions) === 0))
                        <div class="flex items-center gap-2 text-sm text-slate-300">
                            <svg class="w-4 h-4 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>Set preferred sessions</span>
                        </div>
                    @endif
                    
                    @if($user->hasRole('trader') && (!$user->favorite_pairs || count($user->favorite_pairs) === 0))
                        <div class="flex items-center gap-2 text-sm text-slate-300">
                            <svg class="w-4 h-4 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>Add favorite pairs</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- CTA Button -->
            <a href="{{ route('profile.edit') }}" 
               class="mt-4 w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Complete Profile
            </a>
        @else
            <!-- Share Profile Button -->
            <a href="{{ route('profile.show', $user->username) }}" 
               class="mt-4 w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                View Public Profile
            </a>
        @endif
    </div>
</div>

<style>
    @keyframes progressPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }
    
    svg circle:last-of-type {
        animation: progressPulse 2s ease-in-out infinite;
    }
</style>
