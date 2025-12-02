@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-white mb-2">Reset Password</h2>
        <p class="text-slate-400">Enter your email to receive a password reset link</p>
    </div>

    <!-- Success Message -->
    @if (session('status'))
        <div class="mb-6 px-4 py-3 bg-emerald-500/20 border border-emerald-500/50 rounded-lg text-emerald-300">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
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

        <!-- Submit Button -->
        <button 
            type="submit" 
            class="w-full px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-lg shadow-lg shadow-emerald-500/50 hover:shadow-xl hover:shadow-emerald-500/60 transition-all duration-200 transform hover:-translate-y-0.5"
        >
            Send Reset Link
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
