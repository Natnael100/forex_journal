<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Controller;
use App\Services\TradeAnalyticsService;
use Illuminate\Support\Facades\Auth;

class TraderDashboardController extends Controller
{
    protected $analytics;

    public function __construct(TradeAnalyticsService $analytics)
    {
        $this->analytics = $analytics;
    }

    /**
     * Display the trader dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total_trades' => $this->analytics->getTotalTrades($user),
            'win_rate' => $this->analytics->getWinRate($user),
            'avg_rr' => $this->analytics->getAverageRiskReward($user),
            'profit_factor' => $this->analytics->getProfitFactor($user),
            'expectancy' => $this->analytics->getExpectancy($user),
            'max_drawdown' => $this->analytics->getMaxDrawdown($user),
            'total_profit' => $user->trades()->sum('profit_loss'),
            'this_month_trades' => $user->trades()->whereMonth('entry_date', now()->month)->count(),
        ];

        // Get mini equity curve data (last 30 trades)
        $equityCurve = collect($this->analytics->getEquityCurveData($user))->take(-30)->values();

        // Get streaks
        $streaks = $this->analytics->getStreaks($user);

        // Recent trades
        $recentTrades = $user->trades()->latest()->take(5)->get();

        return view('trader.dashboard', compact('stats', 'equityCurve', 'streaks', 'recentTrades'));
    }
}
