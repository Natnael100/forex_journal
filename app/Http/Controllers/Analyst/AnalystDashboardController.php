<?php

namespace App\Http\Controllers\Analyst;

use App\Http\Controllers\Controller;
use App\Models\User;

class AnalystDashboardController extends Controller
{
    /**
     * Display the analyst dashboard.
     */
    public function index()
    {
        // Get all traders  (in a real system, this would be assigned traders only)
        $traders = User::role('trader')
            ->withCount('roles')
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'total_traders' => User::role('trader')->count(),
            'pending_feedback' => 0, // Will be populated in Phase 3
            'total_feedback' => 0,
        ];

        return view('analyst.dashboard', compact('traders', 'stats'));
    }
}
