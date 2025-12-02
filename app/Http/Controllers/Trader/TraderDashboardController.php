<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Controller;

class TraderDashboardController extends Controller
{
    /**
     * Display the trader dashboard.
     */
    public function index()
    {
        $stats = [
            'total_trades' => 0, // Will be populated in Phase 3
            'win_rate' => 0,
            'total_profit' => 0,
            'this_month_trades' => 0,
        ];

        return view('trader.dashboard', compact('stats'));
    }
}
