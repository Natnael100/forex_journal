@extends('layouts.auth')

@section('title', 'Register')

@section('content')
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-white mb-2">Create Account</h2>
        <p class="text-slate-400">Start your trading journal journey today</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6" x-data="{ selectedRole: '{{ old('role') }}' }">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-slate-300 mb-2">
                Full Name
            </label>
            <input 
                id="name" 
                name="name" 
                type="text" 
                required 
                autofocus
                value="{{ old('name') }}"
                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 @error('name') border-red-500 @enderror"
                placeholder="John Doe"
            >
            @error('name')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-slate-300 mb-2">
                Email Address
            </label>
            <input 
                id="email" 
                name="email" 
                type="email" 
                required
                value="{{ old('email') }}"
                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 @error('email') border-red-500 @enderror"
                placeholder="john@example.com"
            >
            @error('email')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Role Selection -->
        <div>
            <label for="role" class="block text-sm font-medium text-slate-300 mb-2">
                I am a
            </label>
            <select 
                id="role" 
                name="role" 
                required
                x-model="selectedRole"
                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 @error('role') border-red-500 @enderror"
            >
                <option value="" class="bg-slate-800">Select your role...</option>
                <option value="trader" class="bg-slate-800">📈 Trader - I want to journal my trades</option>
                <option value="analyst" class="bg-slate-800">📊 Performance Analyst - I want to review traders</option>
            </select>
            @error('role')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
            <p class="mt-2 text-xs text-slate-500">Choose the role that best describes your purpose</p>
        </div>

        <!-- Common Profile Fields -->
        <div class="space-y-6 p-4 bg-white/5 rounded-lg border border-white/10">
            <h3 class="text-sm font-semibold text-emerald-400">Profile Information</h3>
            
            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-medium text-slate-300 mb-2">
                    Username
                </label>
                <input 
                    id="username" 
                    name="username" 
                    type="text" 
                    required
                    value="{{ old('username') }}"
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 @error('username') border-red-500 @enderror"
                    placeholder="john_trader_123"
                >
                @error('username')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-slate-500">Lowercase letters, numbers, and underscores only</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Country -->
                <div>
                    <label for="country" class="block text-sm font-medium text-slate-300 mb-2">
                        Country
                    </label>
                    <input 
                        id="country" 
                        name="country" 
                        type="text"
                        value="{{ old('country') }}"
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                        placeholder="United States"
                    >
                </div>

                <!-- Timezone -->
                <div>
                    <label for="timezone" class="block text-sm font-medium text-slate-300 mb-2">
                        Timezone
                    </label>
                    <select 
                        id="timezone" 
                        name="timezone"
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                    >
                        <option value="" class="bg-slate-800">Select timezone...</option>
                        <option value="America/New_York" class="bg-slate-800">Eastern Time (ET)</option>
                        <option value="America/Chicago" class="bg-slate-800">Central Time (CT)</option>
                        <option value="America/Denver" class="bg-slate-800">Mountain Time (MT)</option>
                        <option value="America/Los_Angeles" class="bg-slate-800">Pacific Time (PT)</option>
                        <option value="Europe/London" class="bg-slate-800">London (GMT)</option>
                        <option value="Europe/Paris" class="bg-slate-800">Paris (CET)</option>
                        <option value="Asia/Tokyo" class="bg-slate-800">Tokyo (JST)</option>
                        <option value="Asia/Singapore" class="bg-slate-800">Singapore (SGT)</option>
                        <option value="Australia/Sydney" class="bg-slate-800">Sydney (AEDT)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Trader-Specific Fields -->
        <div x-show="selectedRole === 'trader'" x-cloak class="space-y-6 p-4 bg-blue-500/5 rounded-lg border border-blue-500/20">
            <h3 class="text-sm font-semibold text-blue-400">Trader Profile</h3>
            
            <!-- Experience Level -->
            <div>
                <label for="experience_level" class="block text-sm font-medium text-slate-300 mb-2">
                    Experience Level
                </label>
                <select 
                    id="experience_level" 
                    name="experience_level"
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                >
                    <option value="" class="bg-slate-800">Select experience level...</option>
                    <option value="beginner" class="bg-slate-800" {{ old('experience_level') == 'beginner' ? 'selected' : '' }}>Beginner (0-1 years)</option>
                    <option value="intermediate" class="bg-slate-800" {{ old('experience_level') == 'intermediate' ? 'selected' : '' }}>Intermediate (1-3 years)</option>
                    <option value="advanced" class="bg-slate-800" {{ old('experience_level') == 'advanced' ? 'selected' : '' }}>Advanced (3+ years)</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Trading Style -->
                <div>
                    <label for="trading_style" class="block text-sm font-medium text-slate-300 mb-2">
                        Trading Style
                    </label>
                    <input 
                        id="trading_style" 
                        name="trading_style" 
                        type="text"
                        value="{{ old('trading_style') }}"
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                        placeholder="e.g., Day Trading, Swing Trading"
                    >
                </div>

                <!-- Specialization -->
                <div>
                    <label for="specialization" class="block text-sm font-medium text-slate-300 mb-2">
                        Market Focus
                    </label>
                    <input 
                        id="specialization" 
                        name="specialization" 
                        type="text"
                        value="{{ old('specialization') }}"
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                        placeholder="e.g., Forex, Stocks, Crypto"
                    >
                </div>
            </div>

            <!-- Preferred Sessions -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Preferred Trading Sessions
                </label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="preferred_sessions[]" value="asian" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                        <span class="text-sm text-slate-300">Asian</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="preferred_sessions[]" value="london" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                        <span class="text-sm text-slate-300">London</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="preferred_sessions[]" value="new_york" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                        <span class="text-sm text-slate-300">New York</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="preferred_sessions[]" value="sydney" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                        <span class="text-sm text-slate-300">Sydney</span>
                    </label>
                </div>
            </div>

            <!-- Favorite Pairs -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Favorite Currency Pairs
                </label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="favorite_pairs[]" value="EUR/USD" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                        <span class="text-sm text-slate-300">EUR/USD</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="favorite_pairs[]" value="GBP/USD" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                        <span class="text-sm text-slate-300">GBP/USD</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="favorite_pairs[]" value="USD/JPY" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                        <span class="text-sm text-slate-300">USD/JPY</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="favorite_pairs[]" value="USD/CHF" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                        <span class="text-sm text-slate-300">USD/CHF</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="favorite_pairs[]" value="AUD/USD" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                        <span class="text-sm text-slate-300">AUD/USD</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="favorite_pairs[]" value="USD/CAD" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                        <span class="text-sm text-slate-300">USD/CAD</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Analyst-Specific Fields -->
        <div x-show="selectedRole === 'analyst'" x-cloak class="space-y-6 p-4 bg-purple-500/5 rounded-lg border border-purple-500/20">
            <h3 class="text-sm font-semibold text-purple-400">Analyst Profile</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Years of Experience -->
                <div>
                    <label for="years_of_experience" class="block text-sm font-medium text-slate-300 mb-2">
                        Years of Experience
                    </label>
                    <input 
                        id="years_of_experience" 
                        name="years_of_experience" 
                        type="number"
                        min="0"
                        max="50"
                        value="{{ old('years_of_experience') }}"
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                        placeholder="5"
                    >
                </div>

                <!-- Analysis Specialization -->
                <div>
                    <label for="analysis_specialization" class="block text-sm font-medium text-slate-300 mb-2">
                        Analysis Specialization
                    </label>
                    <select 
                        id="analysis_specialization" 
                        name="analysis_specialization"
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                    >
                        <option value="" class="bg-slate-800">Select specialization...</option>
                        <option value="risk_management" class="bg-slate-800">Risk Management</option>
                        <option value="psychology" class="bg-slate-800">Trading Psychology</option>
                        <option value="statistics" class="bg-slate-800">Statistical Analysis</option>
                        <option value="technical" class="bg-slate-800">Technical Analysis</option>
                        <option value="fundamental" class="bg-slate-800">Fundamental Analysis</option>
                    </select>
                </div>
            </div>

            <!-- Psychology Focus Areas -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    Psychology Focus Areas
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="psychology_focus_areas[]" value="emotional_control" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                        <span class="text-sm text-slate-300">Emotional Control</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="psychology_focus_areas[]" value="discipline" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                        <span class="text-sm text-slate-300">Discipline</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="psychology_focus_areas[]" value="patience" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                        <span class="text-sm text-slate-300">Patience</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="psychology_focus_areas[]" value="risk_aversion" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                        <span class="text-sm text-slate-300">Risk Aversion</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Feedback Style -->
                <div>
                    <label for="feedback_style" class="block text-sm font-medium text-slate-300 mb-2">
                        Feedback Style
                    </label>
                    <select 
                        id="feedback_style" 
                        name="feedback_style"
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                    >
                        <option value="" class="bg-slate-800">Select style...</option>
                        <option value="direct" class="bg-slate-800">Direct & Concise</option>
                        <option value="detailed" class="bg-slate-800">Detailed & Comprehensive</option>
                        <option value="supportive" class="bg-slate-800">Supportive & Encouraging</option>
                        <option value="analytical" class="bg-slate-800">Analytical & Data-Driven</option>
                    </select>
                </div>

                <!-- Max Traders -->
                <div>
                    <label for="max_traders_assigned" class="block text-sm font-medium text-slate-300 mb-2">
                        Max Traders to Manage
                    </label>
                    <input 
                        id="max_traders_assigned" 
                        name="max_traders_assigned" 
                        type="number"
                        min="1"
                        max="20"
                        value="{{ old('max_traders_assigned', 5) }}"
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                    >
                    <p class="mt-1 text-xs text-slate-500">Recommended: 5-10 traders</p>
                </div>
            </div>
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-slate-300 mb-2">
                Password
            </label>
            <input 
                id="password" 
                name="password" 
                type="password" 
                required
                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 @error('password') border-red-500 @enderror"
                placeholder="••••••••"
            >
            @error('password')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
            <p class="mt-2 text-xs text-slate-500">Must be at least 8 characters</p>
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">
                Confirm Password
            </label>
            <input 
                id="password_confirmation" 
                name="password_confirmation" 
                type="password" 
                required
                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                placeholder="••••••••"
            >
        </div>

        <!-- Submit Button -->
        <button 
            type="submit" 
            class="w-full px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-lg shadow-lg shadow-emerald-500/50 hover:shadow-xl hover:shadow-emerald-500/60 transition-all duration-200 transform hover:-translate-y-0.5"
        >
            Create Account
        </button>
    </form>

    <style>
        [x-cloak] { display: none !important; }
    </style>
@endsection

@section('footer')
    <p class="text-slate-400">
        Already have an account? 
        <a href="{{ route('login') }}" class="font-semibold text-emerald-400 hover:text-emerald-300 transition-colors">
            Sign in
        </a>
    </p>
@endsection
