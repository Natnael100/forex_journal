<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Trade;
use App\Models\Feedback;
use App\Models\AnalystAssignment;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class AdminDashboardController extends Controller
{
    /**
     * Display the enhanced admin dashboard with system overview
     */
    public function index()
    {
        // Comprehensive system stats (with graceful fallback for missing Phase 6/7 tables)
        $stats = [
            'total_users' => User::count(),
            'total_traders' => User::role('trader')->count(),
            'total_analysts' => User::role('analyst')->count(),
            'total_admins' => User::role('admin')->count(),
            'pending_verifications' => 0,
            'assigned_traders' => 0,
            'unassigned_traders' => 0,
            'total_trades' => Trade::count(),
            'total_feedback' => 0,
            'recent_activity_count' => 0,
            'active_users' => User::count(),
            'inactive_users' => 0,
        ];

        // Try to get verification stats (Phase 7 feature)
        try {
            $stats['pending_verifications'] = User::where('verification_status', 'pending')->count();
            $stats['active_users'] = User::where('is_active', true)->count();
            $stats['inactive_users'] = User::where('is_active', false)->count();
        } catch (\Exception $e) {
            // Verification columns don't exist yet
        }

        // Try to get assignment stats (Phase 6 feature)
        try {
            $stats['assigned_traders'] = AnalystAssignment::distinct('trader_id')->count();
            $stats['unassigned_traders'] = User::role('trader')
                                        ->where('verification_status', 'verified')
                                        ->whereNotIn('id', AnalystAssignment::pluck('trader_id'))
                                        ->count();
        } catch (\Exception $e) {
            // analyst_assignments table doesn't exist yet
        }

        // Try to get feedback stats (Phase 6 feature)
        try {
            $stats['total_feedback'] = Feedback::count();
        } catch (\Exception $e) {
            // feedback table doesn't exist yet
        }

        // Try to get activity stats (Phase 6 feature)
        try {
            $stats['recent_activity_count'] = Activity::where('created_at', '>=', now()->subWeek())->count();
        } catch (\Exception $e) {
            // activity_log table might not exist
        }

        // Pending verifications (show first 5) - Phase 7 feature
        $pendingVerifications = collect([]);
        try {
            $pendingVerifications = User::where('verification_status', 'pending')
                ->with('roles')
                ->latest()
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            // Verification columns don't exist yet
        }

        // Recent activity (last 10) - Phase 6 feature
        $recentActivity = collect([]);
        try {
            $recentActivity = Activity::with('causer')
                ->latest()
                ->take(10)
                ->get();
        } catch (\Exception $e) {
            // activity_log table doesn't exist yet
        }

        // Recent users
        $recentUsers = User::with('roles')
            ->latest()
            ->take(5)
            ->get();

        // Unassigned traders count by role - Phase 6/7 feature
        $unassignedTradersList = collect([]);
        try {
            $unassignedTradersList = User::role('trader')
                ->where('verification_status', 'verified')
                ->whereNotIn('id', AnalystAssignment::pluck('trader_id'))
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            // analyst_assignments or verification_status doesn't exist yet
        }

        return view('admin.dashboard', compact(
            'stats',
            'pendingVerifications',
            'recentActivity',
            'recentUsers',
            'unassignedTradersList'
        ));
    }
}
