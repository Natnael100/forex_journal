<?php

namespace App\Http\Controllers\Analyst;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TradeAnalyticsService;
use App\Services\PerformanceAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalystDashboardController extends Controller
{
    protected $analyticsService;
    protected $performanceAnalysis;

    public function __construct(TradeAnalyticsService $analyticsService, PerformanceAnalysisService $performanceAnalysis)
    {
        $this->analyticsService = $analyticsService;
        $this->performanceAnalysis = $performanceAnalysis;
    }

    /**
     * Show analyst dashboard with all assigned traders
     */
    public function index(Request $request)
    {
        $analyst = Auth::user();
        
        // Get all traders assigned to this analyst
        $traderIds = $analyst->tradersAssigned()->pluck('trader_id');
        
        $traders = User::role('trader')
            ->whereIn('id', $traderIds)
            ->withCount('trades')
            ->get()
            ->map(function ($trader) {
                return [
                    'id' => $trader->id,
                    'name' => $trader->name,
                    'email' => $trader->email,
                    'total_trades' => $trader->trades_count,
                    'win_rate' => $this->analyticsService->getWinRate($trader),
                    'profit_factor' => $this->analyticsService->getProfitFactor($trader),
                    'total_pl' => $trader->trades->sum('profit_loss'),
                    'last_trade' => $trader->trades()->latest('entry_date')->first()?->entry_date,
                ];
            });

        // Recent feedback given
        $recentFeedback = $analyst->feedbackGiven()
            ->with(['trader', 'trade'])
            ->latest()
            ->take(10)
            ->get();

        // Stats
        $stats = [
            'total_traders' => $traders->count(),
            'total_feedback' => $analyst->feedbackGiven()->count(),
            'recent_feedback_count' => $analyst->feedbackGiven()->where('created_at', '>=', now()->subWeek())->count(),
        ];

        return view('analyst.dashboard', compact('traders', 'recentFeedback', 'stats'));
    }

    /**
     * Show individual trader profile with full analytics
     */
    public function traderProfile($traderId)
    {
        $analyst = Auth::user();
        $trader = User::findOrFail($traderId);

        // Verify analyst is assigned to this trader
        if (!$analyst->tradersAssigned()->where('trader_id', $traderId)->exists() && !$analyst->hasRole('admin')) {
            abort(403, 'You are not assigned to this trader.');
        }

        // Get analytics
        $metrics = [
            'total_trades' => $this->analyticsService->getTotalTrades($trader),
            'win_rate' => $this->analyticsService->getWinRate($trader),
            'avg_rr' => $this->analyticsService->getAverageRiskReward($trader),
            'profit_factor' => $this->analyticsService->getProfitFactor($trader),
            'expectancy' => $this->analyticsService->getExpectancy($trader),
            'max_drawdown' => $this->analyticsService->getMaxDrawdown($trader),
            'recovery_factor' => $this->analyticsService->getRecoveryFactor($trader),
            'avg_hold_time' => $this->analyticsService->getAverageHoldTime($trader),
        ];

        // Charts
        $equityCurve = $this->analyticsService->getEquityCurveData($trader);
        $monthlyPL = $this->analyticsService->getMonthlyPLData($trader, now()->year);
        $sessionPerformance = $this->analyticsService->getSessionPerformance($trader);
        $winLossDistribution = $this->analyticsService->getWinLossDistribution($trader);
        $bestWorstPairs = $this->analyticsService->getBestWorstPairs($trader);
        $streaks = $this->analyticsService->getStreaks($trader);

        // Recent trades with filters
        $query = $trader->trades()->with('tags');
        
        if ($request->filled('outcome')) {
            $query->where('outcome', $request->outcome);
        }
        if ($request->filled('session')) {
            $query->where('session', $request->session);
        }
        if ($request->filled('date_from')) {
            $query->where('entry_date', '>=', $request->date_from);
        }

        $trades = $query->latest('entry_date')->paginate(20);

        // Feedback history
        $feedbackHistory = $trader->feedbackReceived()
            ->with('analyst')
            ->latest()
            ->get();

        return view('analyst.trader-profile', compact(
            'trader',
            'metrics',
            'equityCurve',
            'monthlyPL',
            'sessionPerformance',
            'winLossDistribution',
            'bestWorstPairs',
            'streaks',
            'trades',
            'feedbackHistory'
        ));
    }
}
