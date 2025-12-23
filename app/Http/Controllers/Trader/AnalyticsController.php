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
        
        // Handle Period Presets
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

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

        // Build filters from request
        $filters = array_filter([
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'pair' => $request->pair,
            'session' => $request->session,
            'outcome' => $request->outcome,
            'trade_account_id' => $request->trade_account_id,
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
        $monthlyPL = $this->analytics->getMonthlyPLData($user, $request->year ?? now()->year, $request->trade_account_id);
        $sessionPerformance = $this->analytics->getSessionPerformance($user, $filters);
        $bestWorstPairs = $this->analytics->getBestWorstPairs($user, 5, $request->trade_account_id);
        $winLossDistribution = $this->analytics->getWinLossDistribution($user, $filters);

        // Get available pairs for filter
        $pairs = $user->trades()->distinct()->pluck('pair')->sort()->values();
        
        // Get available accounts
        $accounts = $user->tradeAccounts()->get();

        return view('trader.analytics.index', compact(
            'metrics',
            'equityCurve',
            'monthlyPL',
            'sessionPerformance',
            'bestWorstPairs',
            'winLossDistribution',
            'pairs',
            'accounts',
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
