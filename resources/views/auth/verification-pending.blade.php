@extends('layouts.app')

@section('title', 'Account Pending Verification')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full">
        <!-- Icon -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-yellow-500/20 rounded-full mb-4">
                <svg class="w-10 h-10 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Account Pending</h1>
            <p class="text-slate-400">Your account is awaiting verification</p>
        </div>

        <!-- Message Card -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50 mb-6">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white mb-2">What's happening?</h3>
                    <p class="text-slate-300 mb-4">
                        Your account has been created successfully, but it needs to be verified by an administrator before you can access the system.
                    </p>
                    <p class="text-sm text-slate-400">
                        This is a security measure to ensure all users are properly authenticated.
                    </p>
                </div>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl p-6 border border-slate-700/50 mb-6">
            <h3 class="text-lg font-semibold text-white mb-4">Next Steps:</h3>
            <ol class="space-y-3 text-slate-300">
                <li class="flex items-start gap-3">
                    <span class="flex-shrink-0 w-6 h-6 bg-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center text-sm font-semibold">1</span>
                    <span>Wait for the administrator to review your account</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="flex-shrink-0 w-6 h-6 bg-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center text-sm font-semibold">2</span>
                    <span>You'll be able to login once approved</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="flex-shrink-0 w-6 h-6 bg-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center text-sm font-semibold">3</span>
                    <span>Check back later or contact your administrator</span>
                </li>
            </ol>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-3">
            <form action="{{ route('logout') }}" method="POST" class="flex-1">
                @csrf
                <button type="submit" class="w-full px-6 py-3 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition-colors">
                    Logout
                </button>
            </form>
        </div>

        <!-- Help Text -->
        <p class="text-center text-sm text-slate-500 mt-6">
            Need help? Contact your system administrator
        </p>
    </div>
</div>
@endsection
