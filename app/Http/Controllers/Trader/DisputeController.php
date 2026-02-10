<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisputeController extends Controller
{
    /**
     * Show form to create a dispute
     */
    public function create($subscriptionId)
    {
        $subscription = Subscription::with(['analyst', 'trader'])
            ->where('id', $subscriptionId)
            ->where('trader_id', Auth::id())
            ->firstOrFail();

        // Check if dispute already exists for this subscription
        $existingDispute = Dispute::where('subscription_id', $subscriptionId)
            ->where('status', 'pending')
            ->first();

        if ($existingDispute) {
            return redirect()
                ->route('trader.subscriptions.index')
                ->with('error', 'You already have a pending dispute for this subscription.');
        }

        return view('trader.disputes.create', compact('subscription'));
    }

    /**
     * Store a new dispute
     */
    public function store(Request $request, $subscriptionId)
    {
        $subscription = Subscription::where('id', $subscriptionId)
            ->where('trader_id', Auth::id())
            ->firstOrFail();

        // Check for existing pending dispute
        $existingDispute = Dispute::where('subscription_id', $subscriptionId)
            ->where('status', 'pending')
            ->first();

        if ($existingDispute) {
            return redirect()
                ->route('trader.subscriptions.index')
                ->with('error', 'You already have a pending dispute for this subscription.');
        }

        $validated = $request->validate([
            'reason' => 'required|in:analyst_inactive,poor_quality,scam,other',
            'description' => 'required|string|min:20|max:1000',
        ]);

        $dispute = Dispute::create([
            'trader_id' => Auth::id(),
            'analyst_id' => $subscription->analyst_id,
            'subscription_id' => $subscriptionId,
            'reason' => $validated['reason'],
            'description' => $validated['description'],
            'status' => 'pending',
        ]);

        // Notify Analyst (Email + DB)
        $analyst = $subscription->analyst;
        if ($analyst) {
            \App\Models\Notification::create([
                'user_id' => $analyst->id,
                'type' => 'dispute_filed',
                'title' => 'Dispute Filed',
                'message' => 'A trader has filed a dispute against you regarding their subscription.',
                'data' => json_encode(['dispute_id' => $dispute->id]),
            ]);

            try {
                \Illuminate\Support\Facades\Mail::to($analyst->email)->send(new \App\Mail\DisputeFiledMail($dispute));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send dispute email: ' . $e->getMessage());
            }
        }
        
        // Notify Admin (DB only for now, or email if configured)
        // Find all admins
        $admins = \App\Models\User::role('admin')->get();
        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'type' => 'admin_alert',
                'title' => 'New Dispute Filed',
                'message' => "Dispute #{$dispute->id} needs review.",
                'data' => json_encode(['dispute_id' => $dispute->id, 'url' => route('admin.disputes.show', $dispute->id)]),
            ]);
        }

        return redirect()
            ->route('trader.subscriptions.index')
            ->with('success', 'Dispute filed successfully. An admin will review your case shortly.');
    }

    /**
     * Show trader's disputes
     */
    public function index()
    {
        $disputes = Dispute::with(['analyst', 'subscription', 'resolver'])
            ->where('trader_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('trader.disputes.index', compact('disputes'));
    }

    /**
     * Show specific dispute details
     */
    public function show($id)
    {
        $dispute = Dispute::with(['analyst', 'subscription', 'resolver'])
            ->where('trader_id', Auth::id())
            ->findOrFail($id);

        return view('trader.disputes.show', compact('dispute'));
    }
}
