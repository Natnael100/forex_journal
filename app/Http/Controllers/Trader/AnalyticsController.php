<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Controller;
use App\Services\TradeAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    protected $analytics;

    public function __construct(TradeAnalyticsService $analytics)
    {
        $this->analytics = $analytics;
    }

    /**
     * Display main analytics page
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Build filters from request
        $filters = array_filter([
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'pair' => $request->pair,
            'session' => $request->session,
            'outcome' => $request->outcome,
        ]);

        // Get all metrics
        $metrics = [
            'total_trades' => $this->analytics->getTotalTrades($user, $filters),
            'win_rate' => $this->analytics->getWinRate($user, $filters),
            'avg_rr' => $this->analytics->getAverageRiskReward($user, $filters),
            'profit_factor' => $this->analytics->getProfitFactor($user, $filters),
            'expectancy' => $this->analytics->getExpectancy($user, $filters),
            'max_drawdown' => $this->analytics->getMaxDrawdown($user, $filters),
            'recovery_factor' => $this->analytics->getRecoveryFactor($user, $filters),
            'avg_hold_time' => $this->analytics->getAverageHoldTime($user, $filters),
        ];

        // Get chart data
        $equityCurve = $this->analytics->getEquityCurveData($user, $filters);
        $monthlyPL = $this->analytics->getMonthlyPLData($user, $request->year ?? now()->year);
        $sessionPerformance = $this->analytics->getSessionPerformance($user, $filters);
        $bestWorstPairs = $this->analytics->getBestWorstPairs($user);
        $winLossDistribution = $this->analytics->getWinLossDistribution($user, $filters);

        // Get available pairs for filter
        $pairs = $user->trades()->distinct()->pluck('pair')->sort()->values();

        return view('trader.analytics.index', compact(
            'metrics',
            'equityCurve',
            'monthlyPL',
            'sessionPerformance',
            'bestWorstPairs',
            'winLossDistribution',
            'pairs',
            'filters'
        ));
    }

    /**
     * Display trade review/pattern recognition page
     */
    public function review()
    {
        $user = Auth::user();

        $patterns = [
            'streaks' => $this->analytics->getStreaks($user),
            'time_of_day' => $this->analytics->getTimeOfDayPerformance($user),
            'day_of_week' => $this->analytics->getDayOfWeekPerformance($user),
        ];

        return view('trader.analytics.review', compact('patterns'));
    }
}
