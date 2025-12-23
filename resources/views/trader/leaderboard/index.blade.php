@extends('layouts.app')

@section('title', 'Leaderboard')

@section('content')
    <!-- Header -->
    <div class="mb-8 pt-6">
        <h1 class="text-3xl font-bold text-white mb-2">Leaderboard 🏅</h1>
        <p class="text-slate-400">See how you rank against other traders.</p>
    </div>

    <!-- Your Rank Card -->
    @if($userRank)
    <div class="mb-8 bg-gradient-to-br from-indigo-600/20 to-purple-600/20 backdrop-blur-xl rounded-xl p-6 border border-indigo-500/30">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-2xl font-bold text-white shadow-lg">
                    #{{ $userRank['rank'] }}
                </div>
                <div>
                    <p class="text-sm text-slate-400">Your Current Rank</p>
                    <p class="text-xl font-bold text-white">{{ $userRank['name'] }}</p>
                    <p class="text-sm text-indigo-400">Level {{ $userRank['level'] }} · {{ $userRank['level_title'] }}</p>
                </div>
            </div>
            <div class="flex gap-6">
                <div class="text-center">
                    <p class="text-2xl font-bold text-white">{{ number_format($userRank['xp']) }}</p>
                    <p class="text-xs text-slate-400">Total XP</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-white">{{ $userRank['achievements_count'] }}</p>
                    <p class="text-xs text-slate-400">Achievements</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-emerald-400">{{ $userRank['win_rate'] }}%</p>
                    <p class="text-xs text-slate-400">Win Rate</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Leaderboard Table -->
    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-xl border border-slate-700/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-700 bg-slate-800/50">
                        <th class="text-left py-4 px-6 text-slate-300 font-semibold">Rank</th>
                        <th class="text-left py-4 px-6 text-slate-300 font-semibold">Trader</th>
                        <th class="text-center py-4 px-6 text-slate-300 font-semibold">Level</th>
                        <th class="text-center py-4 px-6 text-slate-300 font-semibold">XP</th>
                        <th class="text-center py-4 px-6 text-slate-300 font-semibold hidden md:table-cell">Achievements</th>
                        <th class="text-center py-4 px-6 text-slate-300 font-semibold hidden lg:table-cell">Trades</th>
                        <th class="text-center py-4 px-6 text-slate-300 font-semibold hidden lg:table-cell">Win Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaderboard as $entry)
                        @php
                            $isCurrentUser = $entry['id'] === auth()->id();
                        @endphp
                        <tr class="{{ $isCurrentUser ? 'bg-indigo-500/10 border-l-4 border-l-indigo-500' : '' }} border-b border-slate-800 hover:bg-white/5 transition-colors">
                            <!-- Rank -->
                            <td class="py-4 px-6">
                                @if($entry['rank'] === 1)
                                    <span class="text-2xl">🥇</span>
                                @elseif($entry['rank'] === 2)
                                    <span class="text-2xl">🥈</span>
                                @elseif($entry['rank'] === 3)
                                    <span class="text-2xl">🥉</span>
                                @else
                                    <span class="text-lg font-bold text-slate-400">#{{ $entry['rank'] }}</span>
                                @endif
                            </td>
                            
                            <!-- Trader -->
                            <td class="py-4 px-6">
                                <a href="{{ route('profile.show', $entry['username'] ?? $entry['id']) }}" class="flex items-center gap-3 group">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-slate-600 to-slate-700 flex items-center justify-center text-white font-bold group-hover:ring-2 group-hover:ring-indigo-500 transition-all">
                                        {{ substr($entry['name'], 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium {{ $isCurrentUser ? 'text-indigo-400' : 'text-white' }} group-hover:text-indigo-400 transition-colors">
                                            {{ $entry['name'] }}
                                            @if($isCurrentUser)
                                                <span class="text-xs text-slate-400">(You)</span>
                                            @endif
                                        </p>
                                        @if($entry['username'])
                                            <p class="text-xs text-slate-500 group-hover:text-slate-400">@{{ $entry['username'] }}</p>
                                        @endif
                                    </div>
                                </a>
                            </td>
                            
                            <!-- Level -->
                            <td class="py-4 px-6 text-center">
                                <div class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-500/20 rounded-full">
                                    <span class="text-indigo-400 font-bold">{{ $entry['level'] }}</span>
                                </div>
                            </td>
                            
                            <!-- XP -->
                            <td class="py-4 px-6 text-center">
                                <span class="font-semibold text-white">{{ number_format($entry['xp']) }}</span>
                            </td>
                            
                            <!-- Achievements -->
                            <td class="py-4 px-6 text-center hidden md:table-cell">
                                <span class="text-amber-400">🏆 {{ $entry['achievements_count'] }}</span>
                            </td>
                            
                            <!-- Trades -->
                            <td class="py-4 px-6 text-center hidden lg:table-cell">
                                <span class="text-slate-300">{{ number_format($entry['trades_count']) }}</span>
                            </td>
                            
                            <!-- Win Rate -->
                            <td class="py-4 px-6 text-center hidden lg:table-cell">
                                <span class="px-2 py-1 rounded text-sm {{ $entry['win_rate'] >= 50 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">
                                    {{ $entry['win_rate'] }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <p class="text-lg mb-2">🏆 No rankings yet</p>
                                <p class="text-sm">Start logging trades to earn XP and climb the leaderboard!</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
