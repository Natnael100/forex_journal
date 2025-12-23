<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Services\AchievementService;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    protected AchievementService $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    /**
     * Display achievements page
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Check for new achievements
        $this->achievementService->checkAndUnlockAchievements($user);
        
        // Get all achievements grouped by category
        $achievements = Achievement::where('is_secret', false)
            ->orWhereIn('id', $user->achievements->pluck('id'))
            ->orderBy('tier')
            ->orderBy('criteria_value')
            ->get();
        
        $categories = $achievements->groupBy('category');
        
        // User's unlocked achievement IDs
        $unlockedIds = $user->achievements->pluck('id');
        
        // Recent unlocks (last 7 days)
        $recentUnlocks = $user->achievements()
            ->wherePivot('unlocked_at', '>=', now()->subDays(7))
            ->orderByPivot('unlocked_at', 'desc')
            ->get();
        
        // Stats
        $unlockedCount = $unlockedIds->count();
        $totalCount = Achievement::count();

        return view('trader.achievements.index', compact(
            'user',
            'categories',
            'unlockedIds',
            'recentUnlocks',
            'unlockedCount',
            'totalCount'
        ));
    }
}
