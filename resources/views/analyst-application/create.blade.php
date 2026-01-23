@extends('layouts.auth')

@section('title', 'Apply as Performance Analyst')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-white mb-2">Apply to Become a Performance Analyst</h2>
    <p class="text-slate-400">Join our team of verified trading coaches and help traders succeed</p>
</div>

<form method="POST" action="{{ route('analyst-application.store') }}" class="space-y-8" enctype="multipart/form-data">
    @csrf

    <!-- Section 1: Personal Information -->
    <div class="p-6 bg-white/5 rounded-xl border border-white/10 space-y-6">
        <h3 class="text-xl font-semibold text-white flex items-center gap-2">
            <span>👤</span> Personal Information
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-slate-300 mb-2">
                    Full Name <span class="text-red-400">*</span>
                </label>
                <input 
                    id="name" 
                    name="name" 
                    type="text" 
                    required
                    value="{{ old('name', request('name')) }}"
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('name') border-red-500 @enderror"
                    placeholder="John Doe"
                >
                @error('name')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-slate-300 mb-2">
                    Email Address <span class="text-red-400">*</span>
                </label>
                <input 
                    id="email" 
                    name="email" 
                    type="email" 
                    required
                    value="{{ old('email', request('email')) }}"
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('email') border-red-500 @enderror"
                    placeholder="john@example.com"
                >
                @error('email')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500"
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
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
                    <option value="" class="bg-slate-800">Select timezone...</option>
                    <option value="America/New_York" class="bg-slate-800">Eastern Time (ET)</option>
                    <option value="America/Chicago" class="bg-slate-800">Central Time (CT)</option>
                    <option value="Europe/London" class="bg-slate-800">London (GMT)</option>
                    <option value="Asia/Tokyo" class="bg-slate-800">Tokyo (JST)</option>
                    <option value="Australia/Sydney" class="bg-slate-800">Sydney (AEDT)</option>
                </select>
            </div>

            <!-- Phone -->
            <div>
                <label for="phone" class="block text-sm font-medium text-slate-300 mb-2">
                    Phone (Optional)
                </label>
                <input 
                    id="phone" 
                    name="phone" 
                    type="text"
                    value="{{ old('phone') }}"
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="+1 234 567 8900"
                >
            </div>
        </div>
    </div>

    <!-- Section 2: Professional Credentials -->
    <div class="p-6 bg-white/5 rounded-xl border border-white/10 space-y-6">
        <h3 class="text-xl font-semibold text-white flex items-center gap-2">
            <span>📜</span> Professional Credentials
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Years of Experience -->
            <div>
                <label for="years_experience" class="block text-sm font-medium text-slate-300 mb-2">
                    Years of Trading Experience <span class="text-red-400">*</span>
                </label>
                <select 
                    id="years_experience" 
                    name="years_experience"
                    required
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('years_experience') border-red-500 @enderror"
                >
                    <option value="" class="bg-slate-800">Select experience...</option>
                    <option value="1-3" class="bg-slate-800">1-3 years</option>
                    <option value="3-5" class="bg-slate-800">3-5 years</option>
                    <option value="5-10" class="bg-slate-800">5-10 years</option>
                    <option value="10+" class="bg-slate-800">10+ years</option>
                </select>
                @error('years_experience')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Coaching Experience -->
            <div>
                <label for="coaching_experience" class="block text-sm font-medium text-slate-300 mb-2">
                    Years of Coaching Experience <span class="text-red-400">*</span>
                </label>
                <select 
                    id="coaching_experience" 
                    name="coaching_experience"
                    required
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('coaching_experience') border-red-500 @enderror"
                >
                    <option value="" class="bg-slate-800">Select experience...</option>
                    <option value="<1" class="bg-slate-800">Less than 1 year</option>
                    <option value="1-2" class="bg-slate-800">1-2 years</option>
                    <option value="2-5" class="bg-slate-800">2-5 years</option>
                    <option value="5+" class="bg-slate-800">5+ years</option>
                </select>
                @error('coaching_experience')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Certifications -->
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">
                Certifications (Select all that apply)
            </label>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="certifications[]" value="CFA" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                    <span class="text-sm text-slate-300">CFA</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="certifications[]" value="CMT" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                    <span class="text-sm text-slate-300">CMT</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="certifications[]" value="CFTe" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                    <span class="text-sm text-slate-300">CFTe</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="certifications[]" value="Licensed Broker" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                    <span class="text-sm text-slate-300">Licensed Broker</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="certifications[]" value="None" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                    <span class="text-sm text-slate-300">None</span>
                </label>
            </div>
        </div>

        <!-- Certificate Upload -->
        <div>
            <label for="certificate_files" class="block text-sm font-medium text-slate-300 mb-2">
                Upload Certificates (Optional)
            </label>
            <input 
                type="file" 
                id="certificate_files" 
                name="certificate_files[]" 
                multiple
                accept=".pdf,.jpg,.jpeg,.png"
                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-500 file:text-white hover:file:bg-emerald-600"
            >
            <p class="mt-1 text-xs text-slate-500">PDF, JPG, PNG. Max 10MB per file.</p>
            @error('certificate_files.*')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Trading Methodology -->
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">
                Trading Methodology (Select all that apply)
            </label>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="methodology[]" value="SMC" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                    <span class="text-sm text-slate-300">Smart Money Concepts</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="methodology[]" value="ICT" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                    <span class="text-sm text-slate-300">ICT</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="methodology[]" value="Price Action" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                    <span class="text-sm text-slate-300">Price Action</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="methodology[]" value="Elliott Wave" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                    <span class="text-sm text-slate-300">Elliott Wave</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="methodology[]" value="Supply & Demand" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                    <span class="text-sm text-slate-300">Supply & Demand</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="methodology[]" value="Fundamental" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                    <span class="text-sm text-slate-300">Fundamental Analysis</span>
                </label>
            </div>
        </div>

        <!-- Asset Specialization -->
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">
                Asset Specialization (Select all that apply)
            </label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="specializations[]" value="Forex Majors" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                    <span class="text-sm text-slate-300">Forex Majors</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="specializations[]" value="Gold" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                    <span class="text-sm text-slate-300">Gold/Silver</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="specializations[]" value="Indices" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                    <span class="text-sm text-slate-300">Indices</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="specializations[]" value="Crypto" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                    <span class="text-sm text-slate-300">Cryptocurrency</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Section 3: Coaching Details -->
    <div class="p-6 bg-white/5 rounded-xl border border-white/10 space-y-6">
        <h3 class="text-xl font-semibold text-white flex items-center gap-2">
            <span>🎓</span> Coaching Details
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Clients Coached -->
            <div>
                <label for="clients_coached" class="block text-sm font-medium text-slate-300 mb-2">
                    Number of Clients Coached <span class="text-red-400">*</span>
                </label>
                <select 
                    id="clients_coached" 
                    name="clients_coached"
                    required
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('clients_coached') border-red-500 @enderror"
                >
                    <option value="" class="bg-slate-800">Select range...</option>
                    <option value="0-5" class="bg-slate-800">0-5 clients</option>
                    <option value="5-20" class="bg-slate-800">5-20 clients</option>
                    <option value="20-50" class="bg-slate-800">20-50 clients</option>
                    <option value="50+" class="bg-slate-800">50+ clients</option>
                </select>
                @error('clients_coached')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Coaching Style -->
            <div>
                <label for="coaching_style" class="block text-sm font-medium text-slate-300 mb-2">
                    Coaching Style
                </label>
                <select 
                    id="coaching_style" 
                    name="coaching_style"
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                >
                    <option value="" class="bg-slate-800">Select style...</option>
                    <option value="Hands-on & Active" class="bg-slate-800">Hands-on & Active</option>
                    <option value="Technical-Focused" class="bg-slate-800">Technical-Focused</option>
                    <option value="Psychology-First" class="bg-slate-800">Psychology-First</option>
                    <option value="Fundamental-Focused" class="bg-slate-800">Fundamental-Focused</option>
                </select>
            </div>
        </div>

        <!-- Max Clients -->
        <div>
            <label for="max_clients" class="block text-sm font-medium text-slate-300 mb-2">
                How many clients can you handle? <span class="text-red-400">*</span>
            </label>
            <select 
                id="max_clients" 
                name="max_clients"
                required
                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('max_clients') border-red-500 @enderror"
            >
                <option value="" class="bg-slate-800">Select capacity...</option>
                <option value="1-5" class="bg-slate-800">1-5 clients</option>
                <option value="5-10" class="bg-slate-800">5-10 clients</option>
                <option value="10-20" class="bg-slate-800">10-20 clients</option>
                <option value="20+" class="bg-slate-800">20+ clients</option>
            </select>
            @error('max_clients')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Communication Methods -->
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">
                Preferred Communication Methods
            </label>
            <div class="grid grid-cols-2 gap-3">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="communication_methods[]" value="1-on-1 Video Calls" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                    <span class="text-sm text-slate-300">1-on-1 Video Calls</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="communication_methods[]" value="Trade Journal Reviews" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                    <span class="text-sm text-slate-300">Trade Journal Reviews</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="communication_methods[]" value="Written Feedback" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                    <span class="text-sm text-slate-300">Written Feedback</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="communication_methods[]" value="Group Sessions" class="rounded bg-white/10 border-white/20 text-emerald-500 focus:ring-emerald-500">
                    <span class="text-sm text-slate-300">Group Sessions</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Section 4: Social Proof -->
    <div class="p-6 bg-white/5 rounded-xl border border-white/10 space-y-6">
        <h3 class="text-xl font-semibold text-white flex items-center gap-2">
            <span>⭐</span> Social Proof & Track Record
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Track Record -->
            <div class="md:col-span-2">
                <label for="track_record_url" class="block text-sm font-medium text-slate-300 mb-2">
                    Track Record URL (Myfxbook, TradingView, etc.)
                </label>
                <input 
                    id="track_record_url" 
                    name="track_record_url" 
                    type="url"
                    value="{{ old('track_record_url') }}"
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="https://myfxbook.com/..."
                >
                <p class="mt-1 text-xs text-slate-500">Recommended: Verified track record builds trust</p>
            </div>

            <!-- LinkedIn -->
            <div>
                <label for="linkedin_url" class="block text-sm font-medium text-slate-300 mb-2">
                    LinkedIn Profile
                </label>
                <input 
                    id="linkedin_url" 
                    name="linkedin_url" 
                    type="url"
                    value="{{ old('linkedin_url') }}"
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="https://linkedin.com/in/..."
                >
            </div>

            <!-- Twitter -->
            <div>
                <label for="twitter_handle" class="block text-sm font-medium text-slate-300 mb-2">
                    Twitter/X Handle
                </label>
                <input 
                    id="twitter_handle" 
                    name="twitter_handle" 
                    type="text"
                    value="{{ old('twitter_handle') }}"
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="@yourhandle"
                >
            </div>

            <!-- YouTube -->
            <div>
                <label for="youtube_url" class="block text-sm font-medium text-slate-300 mb-2">
                    YouTube Channel
                </label>
                <input 
                    id="youtube_url" 
                    name="youtube_url" 
                    type="url"
                    value="{{ old('youtube_url') }}"
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="https://youtube.com/@..."
                >
            </div>

            <!-- Website -->
            <div>
                <label for="website_url" class="block text-sm font-medium text-slate-300 mb-2">
                    Personal Website
                </label>
                <input 
                    id="website_url" 
                    name="website_url" 
                    type="url"
                    value="{{ old('website_url') }}"
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="https://yourwebsite.com"
                >
            </div>
        </div>
    </div>

    <!-- Section 5: Application Statement -->
    <div class="p-6 bg-white/5 rounded-xl border border-white/10 space-y-6">
        <h3 class="text-xl font-semibold text-white flex items-center gap-2">
            <span>✍️</span> Application Statement
        </h3>

        <!-- Why Join -->
        <div>
            <label for="why_join" class="block text-sm font-medium text-slate-300 mb-2">
                Why do you want to be an analyst on pipJournal? <span class="text-red-400">*</span>
            </label>
            <textarea 
                id="why_join" 
                name="why_join" 
                required
                rows="5"
                minlength="200"
                maxlength="500"
                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('why_join') border-red-500 @enderror"
                placeholder="Tell us about your coaching philosophy and what you can offer traders..."
            >{{ old('why_join') }}</textarea>
            <p class="mt-1 text-xs text-slate-500">200-500 characters required</p>
            @error('why_join')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Unique Value -->
        <div>
            <label for="unique_value" class="block text-sm font-medium text-slate-300 mb-2">
                What makes you unique as a trading coach? <span class="text-red-400">*</span>
            </label>
            <textarea 
                id="unique_value" 
                name="unique_value" 
                required
                rows="5"
                minlength="200"
                maxlength="500"
                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('unique_value') border-red-500 @enderror"
                placeholder="Share your unique approach, experience, or methodology..."
            >{{ old('unique_value') }}</textarea>
            <p class="mt-1 text-xs text-slate-500">200-500 characters required</p>
            @error('unique_value')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Submit Button -->
    <div class="flex items-center justify-between pt-4">
        <a href="{{ route('login') }}" class="text-slate-400 hover:text-white transition-colors">
            ← Back to Login
        </a>
        <button 
            type="submit" 
            class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-all shadow-lg hover:shadow-emerald-500/50"
        >
            Submit Application
        </button>
    </div>

    <p class="text-xs text-slate-500 text-center">
        By submitting this application, you agree that all information provided is accurate and you consent to background verification.
    </p>
</form>
@endsection
