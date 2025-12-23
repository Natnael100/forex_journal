<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Controller;
use App\Services\AchievementService;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    protected AchievementService $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    /**
     * Display leaderboard page
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Get leaderboard data
        $leaderboard = $this->achievementService->getLeaderboard(50);
        
        // Find current user's rank
        $userRank = collect($leaderboard)->firstWhere('id', $user->id);
        
        // If user not in top 50, calculate their rank
        if (!$userRank && $user->xp > 0) {
            $rank = \App\Models\User::role('trader')
                ->where('xp', '>', $user->xp)
                ->count() + 1;
            
            $userRank = [
                'rank' => $rank,
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
        }

        return view('trader.leaderboard.index', compact('leaderboard', 'userRank'));
    }

    /**
     * Calculate win rate for a user
     */
    protected function calculateWinRate($user): float
    {
        $total = $user->trades()->count();
        if ($total === 0) return 0;

        $wins = $user->trades()->where('outcome', 'win')->count();
        return ($wins / $total) * 100;
    }
}
