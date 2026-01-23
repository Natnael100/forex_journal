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

        <!-- Analyst Application Message (Hidden by default) -->
        <div id="analystMessage" class="hidden p-4 bg-purple-500/10 border border-purple-500/30 rounded-lg">
            <h3 class="font-semibold text-purple-300 mb-2">Analyst Application Required</h3>
            <p class="text-sm text-slate-300 mb-4">
                To maintain high quality standards, all Performance Analysts must complete a pre-approval application. You will be redirected to the application form.
            </p>
            <a id="proceedButton" href="#" class="inline-flex items-center justify-center w-full px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors">
                Proceed to Application →
            </a>
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
            const roleSelect = document.getElementById('role');
            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');
            
            const analystMessage = document.getElementById('analystMessage');
            const standardFields = document.getElementById('standardFields');
            const proceedButton = document.getElementById('proceedButton');
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirmation');
            
            const appUrl = "{{ route('analyst-application.create') }}";

            function updateUI() {
                const isAnalyst = roleSelect.value === 'analyst';
                
                if (isAnalyst) {
                    analystMessage.classList.remove('hidden');
                    standardFields.classList.add('hidden');
                    
                    // Update Link
                    const name = encodeURIComponent(nameInput.value || '');
                    const email = encodeURIComponent(emailInput.value || '');
                    proceedButton.href = `${appUrl}?name=${name}&email=${email}`;
                    
                    // Remove required from password to avoid validation error if form submitted (though button is hidden)
                    passwordInput.removeAttribute('required');
                    confirmInput.removeAttribute('required');
                } else {
                    analystMessage.classList.add('hidden');
                    standardFields.classList.remove('hidden');
                    
                    passwordInput.setAttribute('required', 'required');
                    confirmInput.setAttribute('required', 'required');
                }
            }

            // Events
            roleSelect.addEventListener('change', updateUI);
            nameInput.addEventListener('input', updateUI);
            emailInput.addEventListener('input', updateUI);

            // Initial check (for old input or back button)
            updateUI();
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
