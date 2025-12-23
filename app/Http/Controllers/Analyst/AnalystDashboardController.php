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
    public function traderProfile(Request $request, $traderId)
    {
        $analyst = Auth::user();
        $trader = User::findOrFail($traderId);

        // Verify analyst is assigned to this trader
        if (!$analyst->tradersAssigned()->where('trader_id', $traderId)->exists() && !$analyst->hasRole('admin')) {
            abort(403, 'You are not assigned to this trader.');
        }

        // Get available accounts
        $accounts = $trader->tradeAccounts()->get();

        // Handle Period Presets
        $dateFrom = $request->date_from;
        $dateTo = null;
        
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'this_week':
                    $dateFrom = now()->startOfWeek()->format('Y-m-d');
                    $dateTo = now()->endOfWeek()->format('Y-m-d');
                    break;
                case 'last_week':
                    $dateFrom = now()->subWeek()->startOfWeek()->format('Y-m-d');
                    $dateTo = now()->subWeek()->endOfWeek()->format('Y-m-d');
                    break;
                case 'this_month':
                    $dateFrom = now()->startOfMonth()->format('Y-m-d');
                    $dateTo = now()->endOfMonth()->format('Y-m-d');
                    break;
                case 'last_month':
                    $dateFrom = now()->subMonth()->startOfMonth()->format('Y-m-d');
                    $dateTo = now()->subMonth()->endOfMonth()->format('Y-m-d');
                    break;
                case 'this_year':
                    $dateFrom = now()->startOfYear()->format('Y-m-d');
                    $dateTo = now()->endOfYear()->format('Y-m-d');
                    break;
            }
        }

        // Build filters
        $filters = array_filter([
            'outcome' => $request->outcome,
            'session' => $request->session,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'trade_account_id' => $request->trade_account_id,
            'strategy_id' => $request->strategy_id,
        ]);

        // Get analytics
        $metrics = [
            'total_trades' => $this->analyticsService->getTotalTrades($trader, $filters),
            'win_rate' => $this->analyticsService->getWinRate($trader, $filters),
            'avg_rr' => $this->analyticsService->getAverageRiskReward($trader, $filters),
            'profit_factor' => $this->analyticsService->getProfitFactor($trader, $filters),
            'expectancy' => $this->analyticsService->getExpectancy($trader, $filters),
            'max_drawdown' => $this->analyticsService->getMaxDrawdown($trader, $filters),
            'recovery_factor' => $this->analyticsService->getRecoveryFactor($trader, $filters),
            'avg_hold_time' => $this->analyticsService->getAverageHoldTime($trader, $filters),
        ];

        // Charts
        $equityCurve = $this->analyticsService->getEquityCurveData($trader, $filters);
        $monthlyPL = $this->analyticsService->getMonthlyPLData($trader, now()->year, $request->trade_account_id);
        $sessionPerformance = $this->analyticsService->getSessionPerformance($trader, $filters);
        $winLossDistribution = $this->analyticsService->getWinLossDistribution($trader, $filters);
        $bestWorstPairs = $this->analyticsService->getBestWorstPairs($trader, 5, $request->trade_account_id);
        $streaks = $this->analyticsService->getStreaks($trader);
        $strategyPerformance = $this->analyticsService->getStrategyPerformance($trader, $filters);

        // Get strategies for filter dropdown
        $strategies = $trader->strategies()->get();

        // Recent trades with filters
        $query = $trader->trades()->with('tags', 'tradeAccount');
        
        if ($request->filled('outcome')) {
            $query->where('outcome', $request->outcome);
        }
        if ($request->filled('session')) {
            $query->where('session', $request->session);
        }
        if ($request->filled('date_from') || $dateFrom) {
            $query->where('entry_date', '>=', $dateFrom ?? $request->date_from);
        }
        if ($dateTo) {
            $query->where('entry_date', '<=', $dateTo);
        }
        if ($request->filled('trade_account_id')) {
            $query->where('trade_account_id', $request->trade_account_id);
        }
        if ($request->filled('strategy_id')) {
            $query->where('strategy_id', $request->strategy_id);
        }

        $trades = $query->latest('entry_date')->paginate(20);

        // Feedback history
        $feedbackHistory = $trader->feedbackReceived()
            ->with('analyst')
            ->latest()
            ->get();

        // Progress Tracking: Compare performance before/after last feedback
        $comparisonMetrics = null;
        $lastFeedback = $feedbackHistory->first();

        if ($lastFeedback) {
            $feedbackDate = $lastFeedback->submitted_at ?? $lastFeedback->created_at;
            
            // Before Metrics (Last 30 days before feedback or all time before)
            // We'll compare generally against the period before
            $beforeFilters = ['date_to' => $feedbackDate];
            $afterFilters = ['date_from' => $feedbackDate];

            $comparisonMetrics = [
                'feedback_date' => $feedbackDate,
                'before' => [
                    'win_rate' => $this->analyticsService->getWinRate($trader, $beforeFilters),
                    'profit_factor' => $this->analyticsService->getProfitFactor($trader, $beforeFilters),
                    'total_trades' => $this->analyticsService->getTotalTrades($trader, $beforeFilters),
                    'avg_rr' => $this->analyticsService->getAverageRiskReward($trader, $beforeFilters),
                ],
                'after' => [
                    'win_rate' => $this->analyticsService->getWinRate($trader, $afterFilters),
                    'profit_factor' => $this->analyticsService->getProfitFactor($trader, $afterFilters),
                    'total_trades' => $this->analyticsService->getTotalTrades($trader, $afterFilters),
                    'avg_rr' => $this->analyticsService->getAverageRiskReward($trader, $afterFilters),
                ]
            ];
        }

        return view('analyst.trader-profile', compact(
            'trader',
            'accounts',
            'strategies',
            'metrics',
            'equityCurve',
            'monthlyPL',
            'sessionPerformance',
            'winLossDistribution',
            'bestWorstPairs',
            'streaks',
            'strategyPerformance',
            'trades',
            'feedbackHistory',
            'comparisonMetrics'
        ));
    }
}
