<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\User;
use App\Models\UserAchievement;

class AchievementService
{
    /**
     * Check and unlock achievements for a user
     */
    public function checkAndUnlockAchievements(User $user): array
    {
        $unlockedAchievements = [];
        $achievements = Achievement::all();

        foreach ($achievements as $achievement) {
            // Skip if already unlocked
            if ($user->hasAchievement($achievement->slug)) {
                continue;
            }

            // Check if criteria met
            if ($this->checkCriteria($user, $achievement)) {
                $this->unlockAchievement($user, $achievement);
                $unlockedAchievements[] = $achievement;
            }
        }

        return $unlockedAchievements;
    }

    /**
     * Check if achievement criteria is met
     */
    protected function checkCriteria(User $user, Achievement $achievement): bool
    {
        return match($achievement->criteria_type) {
            'count' => $this->checkCountCriteria($user, $achievement),
            'threshold' => $this->checkThresholdCriteria($user, $achievement),
            'streak' => $this->checkStreakCriteria($user, $achievement),
            'special' => $this->checkSpecialCriteria($user, $achievement),
            default => false,
        };
    }

    /**
     * Check count-based criteria (e.g., trades_count >= 10)
     */
    protected function checkCountCriteria(User $user, Achievement $achievement): bool
    {
        $value = match($achievement->criteria_field) {
            'trades_count' => $user->trades()->count(),
            'strategies_count' => $user->strategies()->count(),
            default => 0,
        };

        return $value >= $achievement->criteria_value;
    }

    /**
     * Check threshold-based criteria (e.g., win_rate >= 50)
     */
    protected function checkThresholdCriteria(User $user, Achievement $achievement): bool
    {
        // Need minimum trades for performance achievements
        $minTrades = match($achievement->criteria_field) {
            'win_rate' => $achievement->criteria_value >= 70 ? 50 : ($achievement->criteria_value >= 60 ? 20 : 10),
            'profit_factor' => 20,
            default => 0,
        };

        if ($user->trades()->count() < $minTrades) {
            return false;
        }

        $value = match($achievement->criteria_field) {
            'win_rate' => $this->calculateWinRate($user),
            'profit_factor' => $this->calculateProfitFactor($user),
            default => 0,
        };

        return $value >= $achievement->criteria_value;
    }

    /**
     * Check streak-based criteria
     */
    protected function checkStreakCriteria(User $user, Achievement $achievement): bool
    {
        $value = match($achievement->criteria_field) {
            'win_streak' => $this->getMaxWinStreak($user),
            'daily_streak' => $this->getDailyLoginStreak($user),
            default => 0,
        };

        return $value >= $achievement->criteria_value;
    }

    /**
     * Check special criteria
     */
    protected function checkSpecialCriteria(User $user, Achievement $achievement): bool
    {
        return match($achievement->criteria_field) {
            'profile_complete' => $this->isProfileComplete($user),
            'early_adopter' => $user->created_at->lt(now()->subMonths(3)),
            default => false,
        };
    }

    /**
     * Unlock an achievement for a user
     */
    protected function unlockAchievement(User $user, Achievement $achievement): void
    {
        UserAchievement::create([
            'user_id' => $user->id,
            'achievement_id' => $achievement->id,
            'unlocked_at' => now(),
        ]);

        // Award XP
        $user->addXp($achievement->xp_reward);
    }

    /**
     * Calculate win rate for user
     */
    protected function calculateWinRate(User $user): float
    {
        $total = $user->trades()->count();
        if ($total === 0) return 0;

        $wins = $user->trades()->where('outcome', 'win')->count();
        return ($wins / $total) * 100;
    }

    /**
     * Calculate profit factor for user
     */
    protected function calculateProfitFactor(User $user): float
    {
        $grossProfit = $user->trades()->where('profit_loss', '>', 0)->sum('profit_loss');
        $grossLoss = abs($user->trades()->where('profit_loss', '<', 0)->sum('profit_loss'));

        if ($grossLoss == 0) return $grossProfit > 0 ? 999 : 0;

        return $grossProfit / $grossLoss;
    }

    /**
     * Get maximum win streak
     */
    protected function getMaxWinStreak(User $user): int
    {
        $trades = $user->trades()->orderBy('entry_date')->get();
        $maxStreak = 0;
        $currentStreak = 0;

        foreach ($trades as $trade) {
            if ($trade->outcome?->value === 'win') {
                $currentStreak++;
                $maxStreak = max($maxStreak, $currentStreak);
            } else {
                $currentStreak = 0;
            }
        }

        return $maxStreak;
    }

    /**
     * Get daily trading streak (simplified - based on unique entry dates)
     */
    protected function getDailyLoginStreak(User $user): int
    {
        $dates = $user->trades()
            ->orderBy('entry_date', 'desc')
            ->pluck('entry_date')
            ->map(fn($d) => $d->format('Y-m-d'))
            ->unique()
            ->values();

        if ($dates->isEmpty()) return 0;

        $streak = 1;
        $today = now()->format('Y-m-d');
        
        // Check if traded today or yesterday
        if ($dates[0] !== $today && $dates[0] !== now()->subDay()->format('Y-m-d')) {
            return 0;
        }

        for ($i = 0; $i < count($dates) - 1; $i++) {
            $current = \Carbon\Carbon::parse($dates[$i]);
            $next = \Carbon\Carbon::parse($dates[$i + 1]);
            
            if ($current->diffInDays($next) === 1) {
                $streak++;
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Check if user profile is complete
     */
    protected function isProfileComplete(User $user): bool
    {
        return !empty($user->username) 
            && !empty($user->bio) 
            && !empty($user->profile_photo);
    }

    /**
     * Get leaderboard data
     */
    public function getLeaderboard(int $limit = 50): array
    {
        return User::role('trader')
            ->where('xp', '>', 0)
            ->orderBy('xp', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($user, $index) {
                return [
                    'rank' => $index + 1,
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'xp' => $user->xp,
                    'level' => $user->level,
                    'level_title' => $user->getLevelTitle(),
                    'achievements_count' => $user->achievements()->count(),
                    'trades_count' => $user->trades()->count(),
                    'win_rate' => round($this->calculateWinRate($user), 1),
                ];
            })
            ->toArray();
    }

    /**
     * Award XP for common actions
     */
    public function awardTradeXp(User $user): void
    {
        // Base XP for logging a trade
        $user->addXp(10);
        
        // Check for new achievements
        $this->checkAndUnlockAchievements($user);
    }
}
