@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header with Completion -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2 flex items-center gap-3">
                <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Profile Settings
            </h1>
            <p class="text-slate-400">Customize your presence and preferences</p>
        </div>
        
        <!-- Profile Completeness Badge -->
        <div class="bg-gradient-to-r from-blue-500/20 to-purple-500/20 border border-blue-500/30 rounded-2xl px-6 py-3">
            <div class="flex items-center gap-3">
                <div class="relative w-12 h-12">
                    <svg class="transform -rotate-90 w-12 h-12">
                        <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="4" fill="none" class="text-slate-700" />
                        <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="4" fill="none" 
                                class="text-blue-400 transition-all duration-1000"
                                stroke-dasharray="125.6"
                                stroke-dashoffset="calc(125.6 - (125.6 * {{ $completion }} / 100))"
                                stroke-linecap="round" />
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-xs font-bold text-white">{{ $completion }}%</span>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold text-white">Profile Complete</p>
                    <p class="text-xs text-slate-400">
                        @if($completion >= 100)
                            Awesome! 🎉
                        @elseif($completion >= 75)
                            Almost there!
                        @elseif($completion >= 50)
                            Keep going!
                        @else
                            Let's complete it!
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 bg-gradient-to-r from-emerald-500/20 to-teal-500/20 border border-emerald-500/30 rounded-xl p-4 flex items-center gap-3 animate-slide-in">
            <svg class="w-6 h-6 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-emerald-400 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Cover Photo Section -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-sm border border-slate-700/50 rounded-2xl overflow-hidden shadow-xl">
            <div class="relative group h-48">
                @if($user->cover_photo)
                    <img src="{{ $user->getCoverPhotoUrl() }}" alt="Cover" class="w-full h-full object-cover" />
                @else
                    <div class="w-full h-full bg-gradient-to-r from-purple-600/20 via-pink-600/20 to-blue-600/20"></div>
                @endif
                
                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-4">
                    <form action="{{ route('profile.upload-cover') }}" method="POST" enctype="multipart/form-data" class="inline">
                        @csrf
                        <label class="cursor-pointer px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium shadow-lg hover:shadow-xl transition-all duration-200 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Upload Cover
                            <input type="file" name="cover" accept="image/*" class="hidden" onchange="this.form.submit()" />
                        </label>
                    </form>
                    
                    @if($user->cover_photo)
                        <form action="{{ route('profile.delete-cover') }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium shadow-lg hover:shadow-xl transition-all duration-200 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Remove
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            
            <div class="p-6">
                <h3 class="text-sm font-semibold text-slate-300 mb-1 flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Cover Photo
                </h3>
                <p class="text-xs text-slate-400">Recommended: 1200x400px, 16:9 ratio, max 4MB</p>
            </div>
        </div>

        <!-- Profile Photo Section -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-sm border border-slate-700/50 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Profile Photo
            </h3>
            
            <div class="flex flex-col md:flex-row items-center gap-6">
                <div class="relative group">
                    <img src="{{ $user->getProfilePhotoUrl('large') }}" 
                         alt="Profile" 
                         class="w-32 h-32 rounded-full border-4 border-purple-500/30 shadow-xl group-hover:border-purple-500/50 transition-all duration-300" />
                    @if($user->is_profile_verified)
                        <div class="absolute bottom-0 right-0 w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center border-4 border-slate-900 shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                
                <div class="flex-1">
                    <div class="flex flex-wrap gap-3">
                        <form action="{{ route('profile.upload-photo') }}" method="POST" enctype="multipart/form-data" class="inline">
                            @csrf
                            <label class="cursor-pointer px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-medium shadow-lg hover:shadow-xl transition-all duration-200 inline-flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                Upload Photo
                                <input type="file" name="photo" accept="image/*" class="hidden" onchange="this.form.submit()" />
                            </label>
                        </form>
                        
                        @if($user->profile_photo)
                            <form action="{{ route('profile.delete-photo') }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-6 py-3 bg-red-600/20 hover:bg-red-600/30 text-red-400 border border-red-500/30 rounded-xl font-medium transition-all duration-200 inline-flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Remove
                                </button>
                            </form>
                        @endif
                    </div>
                    <p class="text-xs text-slate-400 mt-3">Square image recommended, max 2MB (JPG, PNG)</p>
                </div>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-sm border border-slate-700/50 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Basic Information
            </h3>
            
            <div class="space-y-5">
                <!-- Username -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                        Username 
                        <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-slate-400 font-medium">@</span>
                        </div>
                        <input type="text" 
                               name="username" 
                               value="{{ old('username', $user->username) }}" 
                               class="w-full pl-10 pr-4 py-3.5 bg-slate-900/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-200" 
                               placeholder="your_username"
                               required />
                    </div>
                    @error('username')
                        <p class="text-red-400 text-sm mt-2 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Two Column Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Country -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Country
                        </label>
                        <input type="text" 
                               name="country" 
                               value="{{ old('country', $user->country) }}" 
                               class="w-full px-4 py-3.5 bg-slate-900/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-200" 
                               placeholder="e.g., United States" />
                    </div>

                    <!-- Timezone -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Timezone
                        </label>
                        <select name="timezone" class="w-full px-4 py-3.5 bg-slate-900/50 border border-slate-700 rounded-xl text-white focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-200">
                            <option value="UTC" {{ $user->timezone == 'UTC' ? 'selected' : '' }}>UTC</option>
                            <option value="America/New_York" {{ $user->timezone == 'America/New_York' ? 'selected' : '' }}>Eastern Time (ET)</option>
                            <option value="Europe/London" {{ $user->timezone == 'Europe/London' ? 'selected' : '' }}>London (GMT)</option>
                            <option value="Asia/Tokyo" {{ $user->timezone == 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo (JST)</option>
                            <option value="Asia/Shanghai" {{ $user->timezone == 'Asia/Shanghai' ? 'selected' : '' }}>Shanghai (CST)</option>
                            <option value="Australia/Sydney" {{ $user->timezone == 'Australia/Sydney' ? 'selected' : '' }}>Sydney (AEDT)</option>
                        </select>
                    </div>

                    <!-- Experience Level -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                            Experience Level
                        </label>
                        <select name="experience_level" class="w-full px-4 py-3.5 bg-slate-900/50 border border-slate-700 rounded-xl text-white focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-200">
                            <option value="">Select Level</option>
                            <option value="beginner" {{ $user->experience_level == 'beginner' ? 'selected' : '' }}>🌱 Beginner</option>
                            <option value="intermediate" {{ $user->experience_level == 'intermediate' ? 'selected' : '' }}>📈 Intermediate</option>
                            <option value="advanced" {{ $user->experience_level == 'advanced' ? 'selected' : '' }}>🏆 Advanced</option>
                        </select>
                    </div>

                    <!-- Trading Style / Specialization -->
                    @if(Auth::user()->hasRole('trader'))
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                Trading Style
                            </label>
                            <input type="text" 
                                   name="trading_style" 
                                   value="{{ old('trading_style', $user->trading_style) }}" 
                                   class="w-full px-4 py-3.5 bg-slate-900/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-200" 
                                   placeholder="e.g., Scalper, Day Trader, Swing" />
                        </div>
                    @elseif(Auth::user()->hasRole('analyst'))
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                                Specialization
                            </label>
                            <input type="text" 
                                   name="specialization" 
                                   value="{{ old('specialization', $user->specialization) }}" 
                                   class="w-full px-4 py-3.5 bg-slate-900/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-200" 
                                   placeholder="e.g., Technical Analysis, Risk Management" />
                        </div>
                    @endif
                </div>

                <!-- Bio -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2 flex items-center gap-2 justify-between">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                            </svg>
                            Bio
                        </span>
                        <span class="text-xs text-slate-500" id="bioCounter">0 / 500</span>
                    </label>
                    <textarea name="bio" 
                              id="bioTextarea"
                              rows="4" 
                              maxlength="500" 
                              class="w-full px-4 py-3.5 bg-slate-900/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-200 resize-none"
                              placeholder="Tell us about yourself...">{{ old('bio', $user->bio) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Privacy Settings -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-sm border border-slate-700/50 rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                Privacy Settings
            </h3>
            
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-3">Profile Visibility</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="relative flex items-center p-4 bg-slate-900/50 border-2 border-slate-700 rounded-xl cursor-pointer transition-all duration-200 hover:border-purple-500/50 has-[:checked]:border-purple-500 has-[:checked]:bg-purple-500/10">
                        <input type="radio" name="profile_visibility" value="public" {{ $user->profile_visibility == 'public' ? 'checked' : '' }} class="sr-only" />
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-semibold text-white">Public</span>
                            </div>
                            <p class="text-xs text-slate-400">Everyone can view</p>
                        </div>
                    </label>
                    
                    <label class="relative flex items-center p-4 bg-slate-900/50 border-2 border-slate-700 rounded-xl cursor-pointer transition-all duration-200 hover:border-purple-500/50 has-[:checked]:border-purple-500 has-[:checked]:bg-purple-500/10">
                        <input type="radio" name="profile_visibility" value="analyst_only" {{ $user->profile_visibility == 'analyst_only' ? 'checked' : '' }} class="sr-only" />
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span class="font-semibold text-white">Analyst Only</span>
                            </div>
                            <p class="text-xs text-slate-400">Analyst & Admin only</p>
                        </div>
                    </label>
                    
                    <label class="relative flex items-center p-4 bg-slate-900/50 border-2 border-slate-700 rounded-xl cursor-pointer transition-all duration-200 hover:border-purple-500/50 has-[:checked]:border-purple-500 has-[:checked]:bg-purple-500/10">
                        <input type="radio" name="profile_visibility" value="private" {{ $user->profile_visibility == 'private' ? 'checked' : '' }} class="sr-only" />
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <span class="font-semibold text-white">Private</span>
                            </div>
                            <p class="text-xs text-slate-400">Only you & Admin</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col md:flex-row items-center justify-end gap-4 pt-6" style="display: flex; gap: 1rem; padding-top: 1.5rem; justify-content: flex-end;">
            <a href="{{ route(Auth::user()->hasRole('trader') ? 'trader.dashboard' : (Auth::user()->hasRole('analyst') ? 'analyst.dashboard' : 'admin.dashboard')) }}" 
               class="w-full md:w-auto px-8 py-3.5 bg-slate-700 hover:bg-slate-600 text-white rounded-xl font-semibold transition-all duration-200 text-center"
               style="padding: 0.875rem 2rem; background-color: #334155; color: white; border-radius: 0.75rem; font-weight: 600; text-decoration: none; text-align: center; display: inline-block;">
                Cancel
            </a>
            <button type="submit" 
                    onclick="console.log('Button clicked!'); return true;"
                    class="w-full md:w-auto px-8 py-3.5 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer"
                    style="padding: 0.875rem 2rem; background: linear-gradient(to right, #2563eb, #9333ea); color: white; border: none; border-radius: 0.75rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); position: relative; z-index: 9999;">
                <svg class="w-5 h-5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.25rem; height: 1.25rem; pointer-events: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="pointer-events-none" style="pointer-events: none;">Save Changes</span>
            </button>
        </div>
    </form>
</div>

<script>
    // Bio character counter
    const bioTextarea = document.getElementById('bioTextarea');
    const bioCounter = document.getElementById('bioCounter');
    
    if (bioTextarea && bioCounter) {
        const updateCounter = () => {
            const length = bioTextarea.value.length;
            bioCounter.textContent = `${length} / 500`;
            
            if (length > 450) {
                bioCounter.classList.add('text-amber-400');
            } else {
                bioCounter.classList.remove('text-amber-400');
            }
        };
        
        bioTextarea.addEventListener('input', updateCounter);
        updateCounter();
    }
    
    // Privacy radio button interactivity
    const radioLabels = document.querySelectorAll('input[name="profile_visibility"]');
    radioLabels.forEach(radio => {
        const label = radio.closest('label');
        
        // Set initial state
        if (radio.checked) {
            label.classList.add('!border-purple-500', '!bg-purple-500/10');
            label.classList.remove('border-slate-700');
        }
        
        // Handle change
        radio.addEventListener('change', () => {
            // Remove active state from all
            radioLabels.forEach(r => {
                const lbl = r.closest('label');
                lbl.classList.remove('!border-purple-500', '!bg-purple-500/10');
                lbl.classList.add('border-slate-700');
            });
            
            // Add active state to selected
            if (radio.checked) {
                label.classList.add('!border-purple-500', '!bg-purple-500/10');
                label.classList.remove('border-slate-700');
            }
        });
        
        // Visual click feedback
        label.addEventListener('click', () => {
            label.style.transform = 'scale(0.98)';
            setTimeout(() => {
                label.style.transform = 'scale(1)';
            }, 100);
        });
    });
    
    // Form submission handler
    const profileForm = document.querySelector('form[action*="profile"]');
    if (profileForm) {
        profileForm.addEventListener('submit', (e) => {
            console.log('Form is submitting...', new FormData(profileForm));
            // Don't prevent default - let it submit normally
        });
    }
</script>

<style>
    @keyframes slide-in {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .animate-slide-in {
        animation: slide-in 0.3s ease-out;
    }
</style>
@endsection
