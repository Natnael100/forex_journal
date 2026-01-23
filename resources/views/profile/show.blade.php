@extends('layouts.app')

@section('title', $user->username . '\'s Profile')

@section('content')
<div class="min-h-screen bg-gray-900">
    
    <!-- Success Message -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 pt-4">
            <div class="rounded-lg border border-green-500/30 bg-green-900/20 p-4 flex items-center gap-3">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm text-green-200 font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Cover Photo + Profile Header -->
    <div class="relative">
        <!-- Cover Photo -->
        <div class="h-80 bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600 relative overflow-hidden">
            @if($user->cover_photo)
                <img src="{{ asset('storage/profiles/' . $user->cover_photo) }}" alt="Cover" class="absolute inset-0 w-full h-full object-cover object-center">
            @else
                <!-- Animated gradient background -->
                <div class="absolute inset-0 bg-gradient-to-br from-purple-600 via-indigo-600 to-blue-600 opacity-90"></div>
                <div class="absolute inset-0" style="background: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,.05) 10px, rgba(255,255,255,.05) 20px);"></div>
            @endif
        </div>

        <!-- Profile Info Overlay -->
        <div class="max-w-7xl mx-auto px-4">
            <div class="relative -mt-32">
                <div class="flex flex-col sm:flex-row items-start sm:items-end gap-6">
                    <!-- Profile Photo -->
                    <div class="relative">
                        <div class="w-40 h-40 rounded-2xl border-4 border-gray-900 bg-gray-800 shadow-2xl overflow-hidden">
                            @if($user->profile_photo)
                                <img src="{{ asset('storage/profiles/large_' . $user->profile_photo) }}" alt="{{ $user->username }}" class="w-full h-full object-cover object-center">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=160&background=6366f1&color=fff&bold=true" alt="{{ $user->username }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        @if($user->is_profile_verified)
                            <div class="absolute -bottom-2 -right-2 bg-blue-500 rounded-full p-2 border-4 border-gray-900">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Name and Actions -->
                    <div class="flex-1 pb-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <h1 class="text-3xl font-bold text-white mb-1 flex items-center gap-2">
                                    {{ $user->name }}
                                    @if($user->isVerifiedAnalyst())
                                        <div class="group relative">
                                            <svg class="w-6 h-6 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-xs text-white rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                                                Verified Analyst
                                            </div>
                                        </div>
                                    @endif
                                </h1>
                                <p class="text-gray-400 text-lg">{{ '@' . $user->username }}</p>
                                @if($user->country)
                                    <p class="text-gray-500 text-sm mt-1 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $user->country }}
                                    </p>
                                @endif
                            </div>

                            @auth
                                @if(Auth::id() === $user->id)
                                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-all shadow-lg hover:shadow-indigo-500/50">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit Profile
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left Column (Sticky Sidebar) -->
            <div class="lg:col-span-1 space-y-6">
                
                <!-- XP & Level Card -->
                @if($user->hasRole('trader'))
                <div class="rounded-xl border border-gray-700 bg-gray-800 p-6 sticky top-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-white">Trader Stats</h3>
                        @if(isset($leaderboardRank) && $leaderboardRank)
                            <a href="{{ route('trader.leaderboard.index') }}" class="flex items-center gap-1 text-sm font-bold text-amber-400 hover:text-amber-300">
                                🏅 #{{ $leaderboardRank }}
                            </a>
                        @endif
                    </div>

                    <!-- Level Badge -->
                    <div class="relative mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-3xl font-bold text-white shadow-lg">
                                {{ $user->level }}
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-400">{{ $user->getLevelTitle() }}</p>
                                <p class="text-xl font-bold text-white">Level {{ $user->level }}</p>
                            </div>
                        </div>
                        
                        <!-- XP Progress -->
                        <div class="mt-4">
                            <div class="flex justify-between text-xs mb-1.5">
                                <span class="text-gray-400">XP Progress</span>
                                <span class="font-medium text-white">{{ number_format($user->xp) }} XP</span>
                            </div>
                            <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500" style="width: {{ $user->getXpProgress() }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Completion (Owner Only) -->
                    @if(Auth::check() && Auth::id() === $user->id && $completion < 100)
                        <div class="pt-4 border-t border-gray-700">
                            <div class="flex justify-between text-xs mb-2">
                                <span class="text-gray-400">Profile Completion</span>
                                <span class="font-medium text-white">{{ $completion }}%</span>
                            </div>
                            <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500 rounded-full" style="width: {{ $completion }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
                @endif

                <!-- Social Links -->
                @if($user->social_links && count($user->social_links) > 0)
                <div class="rounded-xl border border-gray-700 bg-gray-800 p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Connect</h3>
                    <div class="space-y-3">
                        @foreach($user->social_links as $platform => $url)
                            <a href="{{ $url }}" target="_blank" class="flex items-center justify-between p-3 rounded-lg bg-gray-700/50 hover:bg-gray-700 transition-colors group">
                                <span class="capitalize text-gray-300 group-hover:text-white">{{ $platform }}</span>
                                <svg class="w-4 h-4 text-gray-500 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column (Main Content) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Trader DNA Section -->
                @if($user->primary_goal || $user->biggest_challenge)
                <div class="rounded-xl border border-purple-500/30 bg-gradient-to-br from-purple-900/20 to-indigo-900/20 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                            <span class="text-2xl">🧬</span>
                            Trader DNA
                        </h3>
                        
                        <div class="grid gap-4 sm:grid-cols-2">
                            @if($user->primary_goal)
                            <div class="bg-white/5 backdrop-blur-sm rounded-lg p-5 border border-white/10 hover:border-purple-500/50 transition-all">
                                <div class="flex items-start gap-3">
                                    <div class="p-2 bg-purple-500/20 rounded-lg">
                                        <span class="text-2xl">🎯</span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-purple-300 font-medium uppercase tracking-wider mb-1">Primary Goal</p>
                                        <p class="text-sm text-white font-semibold">
                                            @switch($user->primary_goal)
                                                @case('get_funded') Get Funded (Prop Firm) @break
                                                @case('side_income') Generate Side Income @break
                                                @case('full_time_career') Full-Time Trading Career @break
                                                @case('wealth_compounding') Wealth Compounding @break
                                                @default {{ ucwords(str_replace('_', ' ', $user->primary_goal)) }}
                                            @endswitch
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if($user->biggest_challenge)
                            <div class="bg-white/5 backdrop-blur-sm rounded-lg p-5 border border-white/10 hover:border-indigo-500/50 transition-all">
                                <div class="flex items-start gap-3">
                                    <div class="p-2 bg-indigo-500/20 rounded-lg">
                                        <span class="text-2xl">🎭</span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-indigo-300 font-medium uppercase tracking-wider mb-1">Biggest Challenge</p>
                                        <p class="text-sm text-white font-semibold">
                                            @switch($user->biggest_challenge)
                                                @case('psychology_discipline') Psychology/Discipline (FOMO) @break
                                                @case('risk_management') Risk Management @break
                                                @case('technical_strategy') Technical Strategy @break
                                                @case('consistency') Consistency @break
                                                @default {{ ucwords(str_replace('_', ' ', $user->biggest_challenge)) }}
                                            @endswitch
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- About & Preferences -->
                <div class="rounded-xl border border-gray-700 bg-gray-800 p-6">
                    <h3 class="text-xl font-semibold text-white mb-4">About</h3>
                    <p class="text-gray-400 {{ !$user->bio ? 'italic' : '' }}">
                        {{ $user->bio ?: 'No bio provided yet.' }}
                    </p>

                    <!-- Trading Info Grid -->
                    @if($user->experience_level || $user->trading_style)
                    <div class="grid gap-6 sm:grid-cols-2 mt-6 pt-6 border-t border-gray-700">
                        @if($user->experience_level)
                        <div>
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Experience</h4>
                            <div class="text-sm font-medium text-white capitalize">{{ $user->experience_level }}</div>
                        </div>
                        @endif
                        
                        @if($user->trading_style)
                        <div>
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Trading Style</h4>
                            <div class="text-sm font-medium text-white">{{ $user->trading_style }}</div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Tags -->
                    @if($user->preferred_sessions && count($user->preferred_sessions) > 0 || $user->favorite_pairs && count($user->favorite_pairs) > 0 || $user->profile_tags && count($user->profile_tags) > 0)
                    <div class="mt-6 pt-6 border-t border-gray-700 space-y-4">
                        @if($user->preferred_sessions && count($user->preferred_sessions) > 0)
                        <div>
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Preferred Sessions</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($user->preferred_sessions as $session)
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-700 text-gray-300 border border-gray-600">
                                        {{ $session }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($user->favorite_pairs && count($user->favorite_pairs) > 0)
                        <div>
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Favorite Pairs</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($user->favorite_pairs as $pair)
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-600 text-white">
                                        {{ $pair }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($user->profile_tags && count($user->profile_tags) > 0)
                        <div>
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Skills</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($user->profile_tags as $tag)
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-900 text-gray-400 border border-gray-700">
                                        {{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>

                <!-- Recent Activity -->
                @if($recentTrades && count($recentTrades) > 0)
                <div class="rounded-xl border border-gray-700 bg-gray-800 overflow-hidden">
                    <div class="p-6 border-b border-gray-700">
                        <h3 class="text-xl font-semibold text-white">Recent Activity</h3>
                    </div>
                    <div class="divide-y divide-gray-700">
                        @foreach($recentTrades as $trade)
                            <div class="p-6 hover:bg-gray-700/30 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-lg {{ $trade->outcome === 'win' ? 'bg-green-500/20' : ($trade->outcome === 'loss' ? 'bg-red-500/20' : 'bg-gray-700') }} flex items-center justify-center">
                                            <span class="text-2xl">{{ $trade->outcome === 'win' ? '📈' : ($trade->outcome === 'loss' ? '📉' : '⏸️') }}</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-white">{{ $trade->pair }}</p>
                                            <p class="text-sm text-gray-400">{{ $trade->entry_date->format('M d, Y') }} • {{ ucfirst($trade->type) }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-lg {{ $trade->profit_loss >= 0 ? 'text-green-500' : 'text-red-500' }}">
                                            {{ $trade->profit_loss >= 0 ? '+' : '' }}{{ number_format($trade->profit_loss, 2) }}
                                        </p>
                                        <p class="text-xs uppercase font-semibold {{ $trade->outcome === 'win' ? 'text-green-500' : ($trade->outcome === 'loss' ? 'text-red-500' : 'text-gray-500') }}">
                                            {{ $trade->outcome }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
