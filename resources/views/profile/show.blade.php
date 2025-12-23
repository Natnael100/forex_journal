@extends('layouts.app')

@section('title', $user->username . '\'s Profile')

@section('content')
<div class="container mx-auto py-8 px-4 max-w-6xl">
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 w-full max-w-6xl mx-auto">
        
        <!-- Sidebar Section (Left) -->
        <div class="lg:col-span-1 space-y-6 w-full">
            <div class="rounded-xl border border-gray-700 bg-gray-800 text-gray-100 shadow-sm overflow-hidden flex flex-col">
                <!-- Cover Photo (Small strip) -->
                @if($user->cover_photo)
                    <div class="h-24 w-full bg-gray-700 overflow-hidden">
                        <img src="{{ $user->getCoverPhotoUrl() }}" alt="Cover" class="w-full h-full object-cover">
                    </div>
                @else
                   <div class="h-24 w-full bg-gray-700 border-b border-gray-600"></div>
                @endif

                <div class="p-6 relative">
                    <!-- Profile Photo -->
                    <div class="flex justify-center -mt-16 mb-4">
                        <div class="relative h-20 w-20 rounded-full border-4 border-gray-800 bg-gray-800 shadow-sm overflow-hidden">
                            <img src="{{ $user->getProfilePhotoUrl() }}" alt="{{ $user->username }}" class="h-full w-full object-cover" />
                        </div>
                    </div>

                    <!-- Profile Overview -->
                    <div class="text-center">
                        <h2 class="text-lg font-semibold text-gray-100">{{ $user->username }}</h2>
                        <div class="flex items-center justify-center gap-1 text-sm text-gray-400">
                            <span>@ {{ $user->username }}</span>
                            @if($user->is_profile_verified)
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="text-blue-500"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            @endif
                        </div>
                        
                        @if($user->country)
                            <div class="mt-2 flex items-center justify-center gap-1 text-xs text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/><path d="M2 12h20"/></svg>
                                {{ $user->country }}
                            </div>
                        @endif

                        @auth
                            @if(Auth::id() === $user->id)
                                <a href="{{ route('profile.edit') }}" class="mt-4 w-full inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300 disabled:pointer-events-none disabled:opacity-50 border border-gray-600 bg-gray-700 text-gray-200 hover:bg-gray-600 hover:text-white h-9 px-3">
                                    Edit Profile
                                </a>
                            @endif
                        @endauth
                    </div>
                    
                    <!-- Profile Stats/Completion (Only for owner) -->
                    @if(Auth::check() && Auth::id() === $user->id && $completion < 100)
                        <div class="mt-6">
                            <div class="flex justify-between text-xs mb-1.5">
                                <span class="text-gray-400">Profile Completion</span>
                                <span class="font-medium text-gray-200">{{ $completion }}%</span>
                            </div>
                            <div class="h-1.5 bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500 rounded-full" style="width: {{ $completion }}%"></div>
                            </div>
                        </div>
                    @endif

                    <!-- XP & Level Badge -->
                    @if($user->hasRole('trader'))
                        <div class="mt-6 p-4 bg-gradient-to-br from-indigo-600/20 to-purple-600/20 rounded-lg border border-indigo-500/30">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-lg font-bold text-white shadow-lg">
                                    {{ $user->level }}
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-400">{{ $user->getLevelTitle() }}</p>
                                    <p class="text-sm font-bold text-white">Level {{ $user->level }}</p>
                                    <div class="mt-1 w-full h-1.5 bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500" style="width: {{ $user->getXpProgress() }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ number_format($user->xp) }} XP</p>
                                </div>
                            </div>
                            
                            @if(isset($leaderboardRank) && $leaderboardRank)
                                <div class="mt-3 pt-3 border-t border-indigo-500/20 flex items-center justify-between">
                                    <span class="text-xs text-gray-400">Leaderboard Rank</span>
                                    <a href="{{ route('trader.leaderboard.index') }}" class="flex items-center gap-1 text-sm font-bold text-amber-400 hover:text-amber-300 transition-colors">
                                        🏅 #{{ $leaderboardRank }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Social Links -->
            @if($user->social_links && count($user->social_links) > 0)
                <div class="rounded-xl border border-gray-700 bg-gray-800 text-gray-100 shadow-sm p-6">
                    <h3 class="text-sm font-medium text-gray-200 mb-4">Connect</h3>
                    <div class="space-y-3">
                        @foreach($user->social_links as $platform => $url)
                            <a href="{{ $url }}" target="_blank" class="flex items-center gap-3 text-sm text-gray-400 hover:text-white transition-colors">
                                <span class="capitalize">{{ $platform }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-auto"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Main Content (Right) -->
        <div class="lg:col-span-2 space-y-6 w-full">
            
            <!-- Bio & Preferences -->
            <div class="rounded-xl border border-gray-700 bg-gray-800 text-gray-100 shadow-sm p-6">
                <!-- Bio -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold leading-none tracking-tight mb-4 text-white">About</h3>
                    <p class="text-sm text-gray-400 {{ !$user->bio ? 'italic' : '' }}">
                        {{ $user->bio ?: 'No bio provided yet.' }}
                    </p>
                </div>

                <!-- Trading Info Grid -->
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @if($user->experience_level)
                    <div>
                        <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Experience</h4>
                        <div class="text-sm font-medium text-gray-200 capitalize">{{ $user->experience_level }}</div>
                    </div>
                    @endif
                    
                    @if($user->trading_style)
                    <div>
                        <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Style</h4>
                        <div class="text-sm font-medium text-gray-200">{{ $user->trading_style }}</div>
                    </div>
                    @endif

                    @if($user->specialization)
                    <div>
                        <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Specialization</h4>
                        <div class="text-sm font-medium text-gray-200">{{ $user->specialization }}</div>
                    </div>
                    @endif
                </div>

                <!-- Tags / Lists -->
                <div class="mt-8 space-y-6">
                    @if($user->preferred_sessions && count($user->preferred_sessions) > 0)
                    <div>
                        <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Preferred Sessions</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($user->preferred_sessions as $session)
                                <span class="inline-flex items-center rounded-md border border-gray-600 bg-gray-700 px-2.5 py-0.5 text-xs font-semibold transition-colors text-gray-200">
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
                                <span class="inline-flex items-center rounded-md border border-transparent bg-blue-600 text-white px-2.5 py-0.5 text-xs font-semibold transition-colors hover:bg-blue-500">
                                    {{ $pair }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($user->profile_tags && count($user->profile_tags) > 0)
                    <div>
                        <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Tags</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($user->profile_tags as $tag)
                                <span class="inline-flex items-center rounded-md border border-gray-600 bg-gray-900 px-2.5 py-0.5 text-xs font-semibold transition-colors text-gray-400">
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Recent Trades -->
            @if($recentTrades && count($recentTrades) > 0)
                <div class="rounded-xl border border-gray-700 bg-gray-800 text-gray-100 shadow-sm">
                    <div class="flex flex-col space-y-1.5 p-6">
                        <h3 class="text-lg font-semibold leading-none tracking-tight text-white">Recent Activity</h3>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="space-y-4">
                            @foreach($recentTrades as $trade)
                                <div class="flex items-center justify-between border-b border-gray-700 pb-4 last:border-0 last:pb-0">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-sm text-gray-200">{{ $trade->pair }}</span>
                                            <span class="text-xs text-gray-500">{{ $trade->entry_date->format('M d') }}</span>
                                        </div>
                                        <div class="text-xs text-gray-400 capitalize">{{ $trade->type }} • {{ $trade->status }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-medium text-sm {{ $trade->profit_loss >= 0 ? 'text-green-500' : 'text-red-500' }}">
                                            {{ $trade->profit_loss >= 0 ? '+' : '' }}{{ number_format($trade->profit_loss, 2) }}
                                        </div>
                                        <div class="text-xs {{ $trade->outcome === 'win' ? 'text-green-500' : ($trade->outcome === 'loss' ? 'text-red-500' : 'text-gray-500') }} uppercase">
                                            {{ $trade->outcome }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
