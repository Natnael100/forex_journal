<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Trade;
use App\Models\AnalystApplication;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Display admin dashboard with platform statistics
     */
    public function index()
    {
        // Core User Statistics
        $stats = [
            'total_users' => User::count(),
            'total_traders' => User::role('trader')->count(),
            'total_analysts' => User::role('analyst')->count(),
            'total_admins' => User::role('admin')->count(),
            
            // Analyst Verification Stats
            'verified_analysts' => User::role('analyst')
                ->where('analyst_verification_status', 'verified')
                ->count(),
            'pending_analyst_applications' => 0,
            'approved_applications_this_month' => 0,
            
            // Subscription Stats (will be 0 if subscriptions table doesn't exist)
            'active_subscriptions' => 0,
            'total_revenue_this_month' => 0,
            
            // Activity Stats
            'total_trades' => Trade::count(),
            'active_users_last_7_days' => 0,
            'new_users_this_week' => User::where('created_at', '>=', now()->subDays(7))->count(),
            'banned_users' => 0,
        ];

        // Try to get analyst application stats
        try {
            $stats['pending_analyst_applications'] = AnalystApplication::where('status', 'pending')->count();
            $stats['approved_applications_this_month'] = AnalystApplication::where('status', 'approved')
                ->whereMonth('created_at', now()->month)
                ->count();
        } catch (\Exception $e) {
            // analyst_applications table doesn't exist yet
        }

        // Try to get subscription stats (Phase 1 feature)
        try {
            $subscriptionModel = app('App\Models\Subscription');
            $stats['active_subscriptions'] = $subscriptionModel::where('status', 'active')->count();
            $stats['total_revenue_this_month'] = $subscriptionModel::where('status', 'active')
                ->whereMonth('created_at', now()->month)
                ->sum(DB::raw('CAST(amount as DECIMAL(10,2))'));
        } catch (\Exception $e) {
            // subscriptions table doesn't exist or model not found
        }

        // Try to get last login stats
        try {
            $stats['active_users_last_7_days'] = User::where('last_login_at', '>=', now()->subDays(7))->count();
        } catch (\Exception $e) {
            // last_login_at column doesn't exist
        }

        // Try to get ban stats
        try {
            $stats['banned_users'] = User::whereNotNull('banned_at')->count();
        } catch (\Exception $e) {
            // banned_at column doesn't exist yet
        }

        // Recent analyst applications (last 5 pending)
        $pendingApplications = collect([]);
        try {
            $pendingApplications = AnalystApplication::where('status', 'pending')
                ->latest()
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            // analyst_applications table doesn't exist
        }

        // Recent users (last 10 registrations)
        $recentUsers = User::with('roles')
            ->latest()
            ->take(10)
            ->get();

        // Platform health indicators
        $healthIndicators = [
            'analyst_response_rate' => 0, // TODO: Calculate from feedback system
            'average_subscription_duration' => 0, // TODO: Calculate from subscriptions
            'user_retention_rate' => 0, // TODO: Calculate from login activity
        ];

        return view('admin.dashboard', compact(
            'stats',
            'pendingApplications',
            'recentUsers',
            'healthIndicators'
        ));
    }
}
