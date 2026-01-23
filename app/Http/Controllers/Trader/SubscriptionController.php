<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Display trader's subscriptions
     */
    public function index()
    {
        $subscriptions = Subscription::with(['analyst'])
            ->where('trader_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('trader.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Show specific subscription details
     */
    public function show($id)
    {
        $subscription = Subscription::with(['analyst'])
            ->where('trader_id', Auth::id())
            ->findOrFail($id);

        return view('trader.subscriptions.show', compact('subscription'));
    }

    /**
     * Cancel a subscription
     */
    public function cancel($id)
    {
        $subscription = Subscription::where('trader_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        if ($subscription->status === 'cancelled') {
            return redirect()
                ->back()
                ->with('error', 'This subscription is already cancelled.');
        }

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return redirect()
            ->route('trader.subscriptions.index')
            ->with('success', 'Subscription cancelled successfully.');
    }
}
