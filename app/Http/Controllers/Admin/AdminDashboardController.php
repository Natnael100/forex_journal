<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_traders' => User::role('trader')->count(),
            'total_analysts' => User::role('analyst')->count(),
            'total_admins' => User::role('admin')->count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
        ];

        $recentUsers = User::with('roles')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentUsers'));
    }
}
