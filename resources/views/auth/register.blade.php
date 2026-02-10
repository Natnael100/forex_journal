@extends('layouts.auth')

@section('title', 'Register')

@section('content')
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-white mb-2">Create Account</h2>
        <p class="text-slate-400">Start your trading journal journey today</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6" enctype="multipart/form-data" id="registerForm">
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
                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 @error('role') border-red-500 @enderror"
            >
                <option value="" class="bg-slate-800">Select your role...</option>
                <option value="trader" class="bg-slate-800" {{ old('role') == 'trader' ? 'selected' : '' }}>📈 Trader - I want to journal my trades</option>
                <option value="analyst" class="bg-slate-800" {{ old('role') == 'analyst' ? 'selected' : '' }}>📊 Performance Analyst - Apply to review traders</option>
            </select>
            @error('role')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
            <p class="mt-2 text-xs text-slate-500">Choose the role that best describes your purpose</p>
        </div>



        <!-- Standard Fields Container -->
        <div id="standardFields">
            <!-- Common Profile Fields (Analysts Only) -->
            <!-- Note: We removed the extra analyst fields from here as they are now in the application form -->

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-slate-300 mb-2">
                    Password
                </label>
                <input 
                    id="password" 
                    name="password" 
                    type="password" 
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 @error('password') border-red-500 @enderror"
                    placeholder="••••••••"
                >
                @error('password')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-slate-500">Must be at least 8 characters</p>
            </div>

            <!-- Confirm Password -->
            <div class="mt-6">
                <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">
                    Confirm Password
                </label>
                <input 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    type="password" 
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                    placeholder="••••••••"
                >
            </div>

            <!-- Submit Button -->
            <button 
                id="submitButton"
                type="submit" 
                class="w-full mt-6 px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-lg shadow-lg shadow-emerald-500/50 hover:shadow-xl hover:shadow-emerald-500/60 transition-all duration-200 transform hover:-translate-y-0.5"
            >
                Create Account
            </button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Logic to auto-fill email from query param if present
            const params = new URLSearchParams(window.location.search);
            if(params.has('email')) {
                document.getElementById('email').value = params.get('email');
            }
            if(params.has('name')) {
                document.getElementById('name').value = params.get('name');
            }
        });
    </script>
@endsection

@section('footer')
    <p class="text-slate-400">
        Already have an account? 
        <a href="{{ route('login') }}" class="font-semibold text-emerald-400 hover:text-emerald-300 transition-colors">
            Sign in
        </a>
    </p>
@endsection
