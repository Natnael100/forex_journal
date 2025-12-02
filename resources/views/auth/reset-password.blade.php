@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-white mb-2">Set New Password</h2>
        <p class="text-slate-400">Enter your new password below</p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

        <!-- Email (readonly) -->
        <div>
            <label for="email_display" class="block text-sm font-medium text-slate-300 mb-2">
                Email Address
            </label>
            <input 
                id="email_display" 
                type="email" 
                readonly
                value="{{ $email ?? old('email') }}"
                class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-slate-400 cursor-not-allowed"
            >
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-slate-300 mb-2">
                New Password
            </label>
            <input 
                id="password" 
                name="password" 
                type="password" 
                required
                autofocus
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
                Confirm New Password
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
            Reset Password
        </button>
    </form>
@endsection

@section('footer')
    <p class="text-slate-400">
        Remember your password? 
        <a href="{{ route('login') }}" class="font-semibold text-emerald-400 hover:text-emerald-300 transition-colors">
            Back to login
        </a>
    </p>
@endsection
