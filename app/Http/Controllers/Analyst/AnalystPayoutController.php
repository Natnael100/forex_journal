<?php

namespace App\Http\Controllers\Analyst;

use App\Http\Controllers\Controller;
use App\Models\AnalystPayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalystPayoutController extends Controller
{
    /**
     * Display payout history and pending earnings
     */
    public function index()
    {
        $analyst = Auth::user();

        $stats = [
            'pending_earnings' => $analyst->getPendingEarnings(),
            'total_earned' => $analyst->payouts()->completed()->sum('amount'),
            'last_payout' => $analyst->payouts()->completed()->latest()->first(),
            'active_subscriptions' => $analyst->subscriptionsAsAnalyst()->active()->count(),
        ];

        $payouts = $analyst->payouts()->latest()->paginate(20);
        $pendingPayouts = $analyst->payouts()->pending()->get();

        return view('analyst.payouts.index', compact('stats', 'payouts', 'pendingPayouts'));
    }

    /**
     * Request payout (create payout request)
     */
    public function request(Request $request)
    {
        $analyst = Auth::user();
        $pendingEarnings = $analyst->getPendingEarnings();

        // Minimum payout threshold
        if ($pendingEarnings < 50.00) {
            return redirect()->back()->with('error', 'Minimum payout amount is $50.00');
        }

        // Check if there's already a pending payout
        if ($analyst->payouts()->pending()->exists()) {
            return redirect()->back()->with('error', 'You already have a pending payout request.');
        }

        // Create payout request
        AnalystPayout::create([
            'analyst_id' => $analyst->id,
            'amount' => $pendingEarnings,
            'period_start' => now()->subMonth()->startOfMonth(),
            'period_end' => now()->subMonth()->endOfMonth(),
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Payout request submitted. You will receive payment within 5-7 business days.');
    }
}
