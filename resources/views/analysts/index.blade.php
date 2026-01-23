@extends('layouts.app')

@section('title', 'Find Your Performance Analyst')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-12">
    <!-- Header -->
    <div class="text-center mb-12 relative">
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Find Your Performance Analyst</h1>
        <p class="text-xl text-slate-400 max-w-2xl mx-auto mb-8">Connect with experienced traders who will help you eliminate mistakes and accelerate your growth.</p>
        
        <!-- Wizard Button -->
        <button onclick="openWizard()" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold rounded-full shadow-lg shadow-emerald-500/20 hover:scale-105 transition-transform animate-pulse-slow">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Can't Decide? Get Matched
        </button>
    </div>

    <!-- Analysts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($analysts as $analyst)
        <div class="group bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-2xl p-6 border border-slate-700/50 hover:border-blue-500/50 transition-all hover:scale-105">
            <!-- Analyst Header -->
            <div class="flex items-center gap-4 mb-4">
                <div class="relative">
                    <img src="{{ $analyst->getProfilePhotoUrl('large') }}" alt="{{ $analyst->name }}" class="w-16 h-16 rounded-full ring-2 ring-slate-700 group-hover:ring-blue-500 transition-all">
                    <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 border-2 border-slate-900 rounded-full"></div>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-white text-lg group-hover:text-blue-400 transition-colors">{{ $analyst->name }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 @if($i <= $analyst->average_rating) text-yellow-400 fill-current @else text-slate-600 @endif" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        @endfor
                        <span class="text-sm text-slate-300 ml-1">({{ $analyst->reviews_count }})</span>
                    </div>
                </div>
            </div>

            <!-- Bio -->
            @if($analyst->bio)
            <p class="text-sm text-slate-300 mb-4 line-clamp-3">{{ $analyst->bio }}</p>
            @endif

            <!-- Specializations -->
            @if(!empty($analyst->specializations))
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach(array_slice($analyst->specializations, 0, 3) as $spec)
                <span class="px-2 py-1 bg-blue-500/10 text-blue-400 rounded-full text-xs font-medium border border-blue-500/20">
                    {{ $spec }}
                </span>
                @endforeach
            </div>
            @endif

            <!-- Stats -->
            <div class="grid grid-cols-2 gap-3 mb-4 pt-4 border-t border-slate-700/50">
                <div class="text-center p-2 bg-slate-900/50 rounded-lg">
                    <p class="text-2xl font-bold text-white">{{ $analyst->years_experience ?? 0 }}</p>
                    <p class="text-xs text-slate-400">Years Exp</p>
                </div>
                <div class="text-center p-2 bg-slate-900/50 rounded-lg">
                    <p class="text-2xl font-bold text-white">{{ $analyst->subscriptionsAsAnalyst()->active()->count() }}</p>
                    <p class="text-xs text-slate-400">Active Clients</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-2">
                <a href="{{ route('analysts.show', $analyst->username ?? $analyst->id) }}" class="flex-1 py-2 bg-slate-700 hover:bg-slate-600 text-white text-center rounded-lg font-medium transition-colors text-sm">
                    View Profile
                </a>
                <a href="{{ route('subscription.create', $analyst) }}" class="flex-1 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-center rounded-lg font-medium transition-colors text-sm shadow-lg shadow-blue-900/30">
                    Subscribe
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <p class="text-xl text-slate-400">No analysts available yet. Check back soon!</p>
        </div>
        @endforelse
    </div>

    <!-- Wizard Modal -->
    <div id="wizardModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-slate-800 rounded-2xl border border-slate-700 w-full max-w-lg overflow-hidden shadow-2xl transform transition-all scale-100">
            <!-- Header -->
            <div class="p-6 border-b border-slate-700 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Analyst Matcher</h3>
                <button onclick="closeWizard()" class="text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Step 1: Style -->
            <div id="step1" class="p-8">
                <h4 class="text-lg font-medium text-white mb-6">What is your preferred trading style?</h4>
                <div class="grid gap-3">
                    <button onclick="selectStyle('Scalping')" class="p-4 rounded-xl border border-slate-600 hover:border-blue-500 hover:bg-blue-500/10 text-left transition-all group">
                        <span class="block font-bold text-white group-hover:text-blue-400">Scalping</span>
                        <span class="text-sm text-slate-400">Fast-paced, short duration trades</span>
                    </button>
                    <button onclick="selectStyle('Day Trading')" class="p-4 rounded-xl border border-slate-600 hover:border-blue-500 hover:bg-blue-500/10 text-left transition-all group">
                        <span class="block font-bold text-white group-hover:text-blue-400">Day Trading</span>
                        <span class="text-sm text-slate-400">Open and close positions within the day</span>
                    </button>
                    <button onclick="selectStyle('Swing Trading')" class="p-4 rounded-xl border border-slate-600 hover:border-blue-500 hover:bg-blue-500/10 text-left transition-all group">
                        <span class="block font-bold text-white group-hover:text-blue-400">Swing Trading</span>
                        <span class="text-sm text-slate-400">Holding positions for days or weeks</span>
                    </button>
                </div>
            </div>

            <!-- Step 2: Experience -->
            <div id="step2" class="p-8 hidden">
                <h4 class="text-lg font-medium text-white mb-6">What is your experience level?</h4>
                <div class="grid gap-3">
                    <button onclick="selectExperience('Beginner')" class="p-4 rounded-xl border border-slate-600 hover:border-blue-500 hover:bg-blue-500/10 text-left transition-all group">
                        <span class="block font-bold text-white group-hover:text-blue-400">Beginner</span>
                        <span class="text-sm text-slate-400">New to trading, need guidance</span>
                    </button>
                    <button onclick="selectExperience('Intermediate')" class="p-4 rounded-xl border border-slate-600 hover:border-blue-500 hover:bg-blue-500/10 text-left transition-all group">
                        <span class="block font-bold text-white group-hover:text-blue-400">Intermediate</span>
                        <span class="text-sm text-slate-400">Have some experience, looking to improve</span>
                    </button>
                    <button onclick="selectExperience('Advanced')" class="p-4 rounded-xl border border-slate-600 hover:border-blue-500 hover:bg-blue-500/10 text-left transition-all group">
                        <span class="block font-bold text-white group-hover:text-blue-400">Advanced</span>
                        <span class="text-sm text-slate-400">Experienced trader seeking professional edge</span>
                    </button>
                </div>
            </div>

            <!-- Loading -->
            <div id="stepLoading" class="p-12 text-center hidden">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
                <p class="text-slate-300">Finding your perfect match...</p>
            </div>

            <!-- Results -->
            <div id="stepResults" class="p-6 hidden">
                <h4 class="text-lg font-medium text-white mb-4">Top Matches for You</h4>
                <div id="resultsList" class="space-y-4">
                    <!-- JS will populate this -->
                </div>
                <div class="mt-6 text-center">
                    <button onclick="resetWizard()" class="text-slate-400 hover:text-white text-sm underline">Start Over</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let wizardData = {
        trading_style: '',
        experience_level: ''
    };

    function openWizard() {
        document.getElementById('wizardModal').classList.remove('hidden');
        resetWizard();
    }

    function closeWizard() {
        document.getElementById('wizardModal').classList.add('hidden');
    }

    function resetWizard() {
        wizardData = { trading_style: '', experience_level: '' };
        showStep('step1');
    }

    function showStep(stepId) {
        ['step1', 'step2', 'stepLoading', 'stepResults'].forEach(id => {
            document.getElementById(id).classList.add('hidden');
        });
        document.getElementById(stepId).classList.remove('hidden');
    }

    function selectStyle(style) {
        wizardData.trading_style = style;
        showStep('step2');
    }

    function selectExperience(level) {
        wizardData.experience_level = level;
        findMatches();
    }

    function findMatches() {
        showStep('stepLoading');

        fetch('{{ route("analysts.recommend") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(wizardData)
        })
        .then(response => response.json())
        .then(data => {
            renderResults(data.matches);
            showStep('stepResults');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Something went wrong. Please try again.');
            closeWizard();
        });
    }

    function renderResults(matches) {
        const container = document.getElementById('resultsList');
        container.innerHTML = '';

        if (matches.length === 0) {
            container.innerHTML = '<p class="text-slate-400 text-center">No exact matches found. Try browsing the full list.</p>';
            return;
        }

        matches.forEach(match => {
            const html = `
                <div class="flex items-center gap-4 p-4 bg-slate-700/50 rounded-xl border border-slate-600">
                    <img src="${match.profile_photo_url || '/images/default-avatar.png'}" class="w-12 h-12 rounded-full">
                    <div class="flex-1">
                        <h5 class="text-white font-bold">${match.name}</h5>
                        <p class="text-xs text-slate-400">${Number(match.average_rating).toFixed(1)} ★ Rating</p>
                    </div>
                     <a href="/analysts/${match.username || match.id}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-lg">View</a>
                </div>
            `;
            container.innerHTML += html;
        });
    }
</script>
@endsection
