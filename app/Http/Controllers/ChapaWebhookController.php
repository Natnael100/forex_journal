<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\AnalystAssignment;
use App\Models\Notification;
use App\Models\User;
use App\Services\ChapaPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChapaWebhookController extends Controller
{
    protected $chapaService;

    public function __construct(ChapaPaymentService $chapaService)
    {
        $this->chapaService = $chapaService;
    }

    /**
     * Handle Chapa webhook events
     */
    public function handle(Request $request)
    {
        // Get raw payload
        $payload = $request->getContent();
        $signature = $request->header('chapa-signature') ?? $request->header('x-chapa-signature');

        // Verify webhook signature
        if (!$signature || !$this->chapaService->verifyWebhookSignature($payload, $signature)) {
            Log::warning('Invalid Chapa webhook signature');
            return response('Invalid signature', 400);
        }

        $event = json_decode($payload, true);

        Log::info('Chapa webhook received', ['event_type' => $event['event'] ?? 'unknown']);

        // Handle different event types
        switch ($event['event'] ?? null) {
            case 'charge.success':
                $this->handleChargeSuccess($event);
                break;

            case 'charge.failed/cancelled':
            case 'charge.failed':
            case 'charge.cancelled':
                $this->handleChargeFailed($event);
                break;

            case 'charge.refunded':
                $this->handleChargeRefunded($event);
                break;

            default:
                Log::info('Unhandled Chapa webhook event', ['type' => $event['event'] ?? 'unknown']);
        }

        return response('Webhook handled', 200);
    }

    /**
     * Handle successful charge
     */

    private function handleChargeSuccess(array $event)
    {
        try {
            $txRef = $event['tx_ref'] ?? null;
            $meta = $event['meta'] ?? [];
            
            // Extract core data from event if not in meta
            // Note: Chapa webhook payload structure varies, sometimes data is at top level
            if (empty($meta)) {
                $meta = [
                    'trader_id' => $event['trader_id'] ?? null,
                    'analyst_id' => $event['analyst_id'] ?? null,
                    'plan' => $event['plan'] ?? null,
                ];
            }

            $this->chapaService->processPaymentSuccess($txRef, $event, $meta);

        } catch (\Exception $e) {
            Log::error('Failed to process charge.success webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Handle failed charge
     */
    private function handleChargeFailed(array $event)
    {
        Log::warning('Payment failed', [
            'tx_ref' => $event['tx_ref'] ?? null,
            'status' => $event['status'] ?? null,
        ]);

        // Optionally notify the user about failed payment
    }

    /**
     * Handle refunded charge
     */
    private function handleChargeRefunded(array $event)
    {
        $txRef = $event['tx_ref'] ?? null;
        
        if (!$txRef) {
            return;
        }

        $subscription = Subscription::where('chapa_tx_ref', $txRef)->first();

        if ($subscription) {
            $subscription->update(['status' => 'cancelled']);
            
            Log::info('Subscription cancelled due to refund', [
                'subscription_id' => $subscription->id,
                'tx_ref' => $txRef,
            ]);
        }
    }
}
