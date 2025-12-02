@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-white mb-2">Welcome Back</h2>
        <p class="text-slate-400">Sign in to your trading journal</p>
    </div>

    <!-- Success Message -->
    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-emerald-500/20 border border-emerald-500/50 rounded-lg text-emerald-300">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

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
                autofocus
                value="{{ old('email') }}"
                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 @error('email') border-red-500 @enderror"
                placeholder="john@example.com"
            >
            @error('email')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <label for="password" class="block text-sm font-medium text-slate-300">
                    Password
                </label>
                <a href="{{ route('password.request') }}" class="text-sm text-emerald-400 hover:text-emerald-300 transition-colors">
                    Forgot password?
                </a>
            </div>
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
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input 
                id="remember" 
                name="remember" 
                type="checkbox"
                class="w-4 h-4 bg-white/10 border-white/20 rounded text-emerald-600 focus:ring-emerald-500 focus:ring-offset-slate-900 focus:ring-2"
            >
            <label for="remember" class="ml-2 block text-sm text-slate-300">
                Remember me
            </label>
        </div>

        <!-- Submit Button -->
        <button 
            type="submit" 
            class="w-full px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-lg shadow-lg shadow-emerald-500/50 hover:shadow-xl hover:shadow-emerald-500/60 transition-all duration-200 transform hover:-translate-y-0.5"
        >
            Sign In
        </button>
    </form>
@endsection

@section('footer')
    <p class="text-slate-400">
        Don't have an account? 
        <a href="{{ route('register') }}" class="font-semibold text-emerald-400 hover:text-emerald-300 transition-colors">
            Create one now
        </a>
    </p>
@endsection
