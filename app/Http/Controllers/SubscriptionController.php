<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\User;
use App\Services\ChapaPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    protected $chapaService;

    public function __construct(ChapaPaymentService $chapaService)
    {
        $this->chapaService = $chapaService;
    }

    /**
     * Show pricing page for subscribing to an analyst
     */
    /**
     * Show pricing page for subscribing to an analyst
     */
    public function create(Request $request, User $analyst)
    {
        // Ensure analyst has public profile
        if (!$analyst->hasRole('analyst') || !$analyst->profile_visibility) {
            abort(404, 'Analyst not found');
        }

        // Fetch active plans from DB
        $plans = $analyst->plans()->where('is_active', true)->get()->keyBy('tier');
        
        // If no plans configured, fallback to default/empty or handle gracefully
        if ($plans->isEmpty()) {
             // Optional: could redirect back with error or show 'not available'
             // For now, we pass empty text or handle in view
        }

        $selectedPlan = $request->query('plan');

        return view('subscriptions.create', compact('analyst', 'plans', 'selectedPlan'));
    }

    /**
     * Create Chapa payment session
     */
    public function checkout(Request $request, User $analyst)
    {
        $validated = $request->validate([
            'plan' => 'required|in:basic,premium,elite',
        ]);

        $trader = Auth::user();

        // Check if already subscribed
        if ($trader->subscriptionsAsTrader()->where('analyst_id', $analyst->id)->active()->exists()) {
            return redirect()->back()->with('error', 'You already have an active subscription with this analyst.');
        }

        // Get plan details from DB
        $planModel = $analyst->getPlan($validated['plan']);

        if (!$planModel || !$planModel->is_active) {
            return redirect()->back()->with('error', 'The selected plan is not available.');
        }

        $price = $planModel->price;

        // Generate unique transaction reference
        $txRef = 'SUB-' . $analyst->id . '-' . $trader->id . '-' . time();

        try {
            $result = $this->chapaService->initializePayment([
                'amount' => $price,
                'currency' => 'ETB',
                'email' => $trader->email,
                'first_name' => $trader->name,
                'last_name' => '',
                'phone_number' => $trader->phone ?? null,
                'tx_ref' => $txRef,
                'callback_url' => route('chapa.callback'),
                'return_url' => route('subscription.success', ['tx_ref' => $txRef]),
                'customization' => [
                    'title' => ucfirst($validated['plan']) . ' Subscription',
                    'description' => 'Monthly coaching with ' . $analyst->name,
                ],
                'meta' => [
                    'trader_id' => $trader->id,
                    'analyst_id' => $analyst->id,
                    'plan' => $validated['plan'],
                ],
            ]);

            if ($result['status'] === 'success') {
                return redirect($result['checkout_url']);
            }

            return redirect()->back()->with('error', $result['message'] ?? 'Payment initialization failed');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Payment processing error: ' . $e->getMessage());
        }
    }

    /**
     * Handle successful subscription
     */
    public function success(Request $request)
    {
        $txRef = $request->get('tx_ref');
        
        if (!$txRef) {
            return view('subscriptions.success')->with('message', 'Payment is being processed. You will be notified once confirmed.');
        }

        // Verify payment with Chapa
        $result = $this->chapaService->verifyPayment($txRef);
        
        if ($result['status'] === 'success' && isset($result['data'])) {
            $paymentData = $result['data'];
            
            // Webhook will handle subscription creation, but we can show success
            return view('subscriptions.success')->with([
                'verified' => true,
                'amount' => $paymentData['amount'] ?? null,
            ]);
        }

        return view('subscriptions.success')->with('message', 'Payment verification in progress.');
    }

    /**
     * Handle cancelled checkout
     */
    public function cancel()
    {
        return view('subscriptions.cancel');
    }

    /**
     * Chapa callback (not used currently, webhook handles everything)
     */
    public function chapaCallback(Request $request)
    {
        // Chapa redirects here after payment
        // We redirect to success page with tx_ref
        return redirect()->route('subscription.success', ['tx_ref' => $request->tx_ref]);
    }

    /**
     * Cancel subscription
     */
    public function destroy(Subscription $subscription)
    {
        $user = Auth::user();

        // Only trader can cancel their own subscription
        if ($subscription->trader_id !== $user->id) {
            abort(403);
        }

        // With Chapa, we just mark as cancelled (no recurring charges to cancel)
        $subscription->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Subscription cancelled successfully.');
    }
}
