@extends('layouts.auth')

@section('title', 'Application Submitted')

@section('content')
<div class="max-w-2xl mx-auto text-center">
    <!-- Success Icon -->
    <div class="mb-8 inline-flex items-center justify-center w-20 h-20 bg-emerald-500/20 rounded-full">
        <svg class="w-12 h-12 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </div>

    <!-- Heading -->
    <h2 class="text-3xl font-bold text-white mb-4">Application Submitted Successfully!</h2>
    <p class="text-lg text-slate-400 mb-8">
        Thank you for applying to become a Performance Analyst at pipJournal.
    </p>

    <!-- Confirmation Email -->
    @if(session('application_email'))
    <div class="mb-8 p-4 bg-blue-500/10 border border-blue-500/30 rounded-lg">
        <p class="text-sm text-blue-300">
            📧 A confirmation email has been sent to <span class="font-semibold">{{ session('application_email') }}</span>
        </p>
    </div>
    @endif

    <!-- Timeline -->
    <div class="mb-12 p-6 bg-white/5 rounded-xl border border-white/10 text-left">
        <h3 class="text-xl font-semibold text-white mb-6 text-center">What Happens Next?</h3>
        
        <div class="space-y-6">
            <!-- Step 1 -->
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center text-white font-bold">
                    1
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-white mb-1">Application Review</h4>
                    <p class="text-sm text-slate-400">Our team will carefully review your credentials, experience, and application statement.</p>
                    <p class="text-xs text-slate-500 mt-1">⏱️ Typically within 2-3 business days</p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-10 h-10 bg-white/10 border border-white/20 rounded-full flex items-center justify-center text-white font-bold">
                    2
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-white mb-1">Credential Verification</h4>
                    <p class="text-sm text-slate-400">We'll verify your certifications and review any submitted track records.</p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-10 h-10 bg-white/10 border border-white/20 rounded-full flex items-center justify-center text-white font-bold">
                    3
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-white mb-1">Decision Email</h4>
                    <p class="text-sm text-slate-400">You'll receive an email with our decision and next steps.</p>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-10 h-10 bg-white/10 border border-white/20 rounded-full flex items-center justify-center text-white font-bold">
                    4
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-white mb-1">Account Setup (If Approved)</h4>
                    <p class="text-sm text-slate-400">Set your password, complete your profile, and start connecting with traders!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a 
            href="{{ route('login') }}" 
            class="px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-medium rounded-lg transition-colors border border-white/20"
        >
            Return to Login
        </a>
        <a 
            href="/" 
            class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-all shadow-lg hover:shadow-emerald-500/50"
        >
            Go to Homepage
        </a>
    </div>

    <!-- Help Text -->
    <div class="mt-12 p-4 bg-white/5 rounded-lg border border-white/10">
        <p class="text-sm text-slate-400">
            <span class="font-semibold text-white">Have questions?</span><br>
            Email us at <a href="mailto:support@pipjournal.com" class="text-emerald-400 hover:text-emerald-300">support@pipjournal.com</a>
        </p>
    </div>
</div>
@endsection
