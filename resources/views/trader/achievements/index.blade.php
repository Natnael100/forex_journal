@extends('layouts.app')

@section('title', 'Achievements')

@section('content')
    <!-- Header with Level Badge -->
    <div class="mb-8 pt-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Achievements 🏆</h1>
                <p class="text-slate-400">Track your trading journey milestones and earn rewards.</p>
            </div>
            
            <!-- Level Badge -->
            <div class="bg-gradient-to-br from-indigo-600/20 to-purple-600/20 backdrop-blur-xl rounded-xl p-4 border border-indigo-500/30 flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-2xl font-bold text-white shadow-lg shadow-indigo-500/30">
                    {{ $user->level }}
                </div>
                <div>
                    <p class="text-sm text-slate-400">{{ $user->getLevelTitle() }}</p>
                    <p class="text-xl font-bold text-white">Level {{ $user->level }}</p>
                    <div class="mt-1 w-32 h-2 bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500" style="width: {{ $user->getXpProgress() }}%"></div>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">{{ number_format($user->xp) }} / {{ number_format($user->xp + ($user->getXpForNextLevel() - ($user->xp % $user->getXpForNextLevel()))) }} XP</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-slate-800/50 p-4 rounded-xl border border-slate-700/50">
            <p class="text-2xl font-bold text-white">{{ $unlockedCount }}</p>
            <p class="text-sm text-slate-400">Unlocked</p>
        </div>
        <div class="bg-slate-800/50 p-4 rounded-xl border border-slate-700/50">
            <p class="text-2xl font-bold text-white">{{ $totalCount }}</p>
            <p class="text-sm text-slate-400">Total Achievements</p>
        </div>
        <div class="bg-slate-800/50 p-4 rounded-xl border border-slate-700/50">
            <p class="text-2xl font-bold text-indigo-400">{{ number_format($user->xp) }}</p>
            <p class="text-sm text-slate-400">Total XP</p>
        </div>
        <div class="bg-slate-800/50 p-4 rounded-xl border border-slate-700/50">
            <p class="text-2xl font-bold text-amber-400">{{ $recentUnlocks->count() }}</p>
            <p class="text-sm text-slate-400">This Week</p>
        </div>
    </div>

    <!-- Recent Unlocks -->
    @if($recentUnlocks->count() > 0)
    <div class="mb-8">
        <h2 class="text-xl font-bold text-white mb-4">🎉 Recently Unlocked</h2>
        <div class="flex flex-wrap gap-4">
            @foreach($recentUnlocks as $achievement)
                <div class="bg-gradient-to-br from-emerald-600/20 to-teal-600/20 p-4 rounded-xl border border-emerald-500/30 flex items-center gap-3 animate-pulse">
                    <span class="text-3xl">{{ $achievement->icon }}</span>
                    <div>
                        <p class="font-semibold text-white">{{ $achievement->name }}</p>
                        <p class="text-xs text-emerald-400">+{{ $achievement->xp_reward }} XP</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Achievement Categories -->
    @foreach($categories as $category => $categoryAchievements)
    <div class="mb-8">
        <h2 class="text-xl font-bold text-white mb-4">
            {{ match($category) {
                'trades' => '📊 Trading Volume',
                'performance' => '📈 Performance',
                'consistency' => '🔥 Consistency',
                'special' => '⭐ Special',
                default => $category,
            } }}
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($categoryAchievements as $achievement)
                @php
                    $isUnlocked = $unlockedIds->contains($achievement->id);
                    $unlockDate = $isUnlocked ? $user->achievements->find($achievement->id)?->pivot->unlocked_at : null;
                @endphp
                
                <div class="relative group {{ $isUnlocked ? 'bg-gradient-to-br from-slate-800/80 to-slate-900/80' : 'bg-slate-800/30' }} rounded-xl p-5 border {{ $isUnlocked ? 'border-emerald-500/30' : 'border-slate-700/30' }} transition-all hover:border-slate-600">
                    
                    <!-- Tier Badge -->
                    <div class="absolute top-3 right-3">
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ match($achievement->tier) {
                            1 => 'bg-amber-900/30 text-amber-600',
                            2 => 'bg-slate-600/30 text-slate-400',
                            3 => 'bg-yellow-900/30 text-yellow-400',
                            4 => 'bg-cyan-900/30 text-cyan-400',
                            default => 'bg-slate-700 text-slate-400',
                        } }}">
                            {{ $achievement->tierLabel() }}
                        </span>
                    </div>
                    
                    <div class="flex items-start gap-4">
                        <!-- Icon -->
                        <div class="w-14 h-14 rounded-xl {{ $isUnlocked ? 'bg-emerald-500/20' : 'bg-slate-700/50' }} flex items-center justify-center text-3xl {{ !$isUnlocked ? 'grayscale opacity-50' : '' }}">
                            {{ $achievement->icon }}
                        </div>
                        
                        <div class="flex-1">
                            <h3 class="font-semibold {{ $isUnlocked ? 'text-white' : 'text-slate-400' }}">
                                {{ $achievement->name }}
                            </h3>
                            <p class="text-sm {{ $isUnlocked ? 'text-slate-400' : 'text-slate-500' }} mt-1">
                                {{ $achievement->description }}
                            </p>
                            
                            <div class="flex items-center gap-3 mt-3">
                                <span class="text-xs {{ $isUnlocked ? 'text-indigo-400' : 'text-slate-500' }}">
                                    +{{ $achievement->xp_reward }} XP
                                </span>
                                
                                @if($isUnlocked && $unlockDate)
                                    <span class="text-xs text-emerald-400">
                                        ✓ {{ \Carbon\Carbon::parse($unlockDate)->diffForHumans() }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @if($isUnlocked)
                        <div class="absolute inset-0 pointer-events-none">
                            <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-b-xl"></div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endforeach
@endsection
