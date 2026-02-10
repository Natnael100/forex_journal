<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChapaPaymentService
{
    private string $secretKey;
    private string $baseUrl;
    private string $mode;

    public function __construct()
    {
        $this->secretKey = (string) config('services.chapa.secret_key', '');
        $configMode = config('services.chapa.mode');
        
        // If a secret key is present, we assume the user wants to use the API (Live or Test),
        // effectively disabling the local simulation fallback.
        if (!empty($this->secretKey)) {
            $this->mode = 'live';
        } else {
            $this->mode = $configMode === 'simulation' ? 'simulation' : 'live';
        }

        $this->baseUrl = 'https://api.chapa.co/v1';
        
        Log::info('Chapa Service Initialized', ['mode' => $this->mode]);
    }

    /**
     * Initialize a payment
     */
    public function initializePayment(array $data): array
    {
        // REAL API INTEGRATION
        // Only use simulation if explicitly configured AND allowed
        if ($this->mode === 'simulation') {
             // Keep simulation logic reachable ONLY if config is explicitly set to 'simulation'
             // This corresponds to 'Removing' the simulation page from default flow
            return [
                'status' => 'success',
                'checkout_url' => route('test.chapa.checkout', [
                    'tx_ref' => $data['tx_ref'],
                    'amount' => $data['amount'],
                    'meta' => urlencode(json_encode($data['meta'] ?? []))
                ]),
                'tx_ref' => $data['tx_ref'],
            ];
        }

        // DEFAULT TO REAL API CALL
        // If mode is 'live' or anything else (e.g. not set), we try the real API.
        try {
            if (empty($this->secretKey)) {
                return [
                    'status' => 'error',
                    'message' => 'Chapa secret key is missing. Please configure CHAPA_SECRET_KEY in .env',
                ];
            }
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transaction/initialize', [
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'ETB',
                'email' => $data['email'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone_number' => $data['phone_number'] ?? null,
                'tx_ref' => $data['tx_ref'],
                'callback_url' => $data['callback_url'],
                'return_url' => $data['return_url'],
                'customization' => $data['customization'] ?? [],
                'meta' => $data['meta'] ?? null,
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['data']['checkout_url'])) {
                return [
                    'status' => 'success',
                    'checkout_url' => $result['data']['checkout_url'],
                    'tx_ref' => $data['tx_ref'],
                ];
            }

            Log::error('Chapa payment initialization failed', ['response' => $result]);
            
            return [
                'status' => 'error',
                'message' => $result['message'] ?? 'Payment initialization failed',
            ];

        } catch (\Exception $e) {
            Log::error('Chapa API error', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return [
                'status' => 'error',
                'message' => 'Payment system error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verify a payment
     */
    public function verifyPayment(string $txRef): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/transaction/verify/' . $txRef);

            $result = $response->json();

            if ($response->successful()) {
                return [
                    'status' => 'success',
                    'data' => $result['data'] ?? $result,
                ];
            }

            return [
                'status' => 'error',
                'message' => $result['message'] ?? 'Verification failed',
            ];

        } catch (\Exception $e) {
            Log::error('Chapa verification error', [
                'error' => $e->getMessage(),
                'tx_ref' => $txRef,
            ]);

            return [
                'status' => 'error',
                'message' => 'Verification error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $secretHash = config('services.chapa.secret_hash');
        $computedHash = hash_hmac('sha256', $payload, $secretHash);
        
        return hash_equals($computedHash, $signature);
    }

    /**
     * Generate renewal payment link
     */
    public function generateRenewalLink($subscription): string
    {
        $txRef = 'RENEW-' . $subscription->id . '-' . uniqid();
        
        $result = $this->initializePayment([
            'amount' => $subscription->price,
            'currency' => 'ETB',
            'email' => $subscription->trader->email,
            'first_name' => $subscription->trader->name,
            'last_name' => '',
            'tx_ref' => $txRef,
            'callback_url' => route('chapa.callback'),
            'return_url' => route('subscription.success'),
            'customization' => [
                'title' => 'Sub Renewal',
                'description' => 'Renew your ' . ucfirst($subscription->plan) . ' plan with ' . $subscription->analyst->name,
            ],
            'meta' => [
                'subscription_id' => $subscription->id,
                'renewal' => true,
            ],
        ]);

        return $result['checkout_url'] ?? '#';
        }

    /**
     * Process successful payment
     */
    public function processPaymentSuccess(string $txRef, array $data, array $meta = [])
    {
        Log::info('Processing successful payment', ['tx_ref' => $txRef, 'meta' => $meta]);

        // Handle Renewal
        if (isset($meta['subscription_id']) && isset($meta['renewal'])) {
            $subscription = \App\Models\Subscription::find($meta['subscription_id']);
            if ($subscription) {
                // Extend subscription by 30 days
                $subscription->update([
                    'status' => 'active',
                    'current_period_end' => $subscription->current_period_end > now() 
                        ? $subscription->current_period_end->addMonth() 
                        : now()->addMonth(),
                    'chapa_tx_ref' => $txRef, // Update latest ref
                ]);

                // Create transaction record if you have one, or just log
                Log::info('Subscription renewed', ['id' => $subscription->id]);
                
                // Notify
                \App\Models\Notification::create([
                    'user_id' => $subscription->trader_id,
                    'type' => 'subscription_renewed',
                    'title' => 'Subscription Renewed',
                    'message' => "Your subscription to {$subscription->analyst->name} has been renewed.",
                ]);
            }
            return;
        }

        // Handle New Subscription
        $traderId = $meta['trader_id'] ?? null;
        $analystId = $meta['analyst_id'] ?? null;
        $plan = $meta['plan'] ?? 'basic';

        if (!$traderId || !$analystId) {
            Log::error('Missing meta data for subscription', $meta);
            return;
        }

        // Create or Update Subscription (Handle duplicates gracefully)
        $subscription = \App\Models\Subscription::updateOrCreate(
            [
                'analyst_id' => $analystId,
                'trader_id' => $traderId,
                'status' => 'active' // Only match active subscriptions
            ],
            [
                'plan' => $plan,
                'price' => $data['amount'] ?? 0,
                'chapa_tx_ref' => $txRef,
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
            ]
        );

        // Create Analyst Assignment
        \App\Models\AnalystAssignment::firstOrCreate(
            ['analyst_id' => $analystId, 'trader_id' => $traderId],
            [
                'status' => 'active',
                'assigned_by' => $traderId, // Self-assigned via payment
            ]
        );

        // Send Notification to Analyst
        $trader = \App\Models\User::find($traderId);
        if ($trader) {
            \App\Models\Notification::create([
                'user_id' => $analystId,
                'type' => 'new_subscription',
                'title' => 'New Subscriber!',
                'message' => "{$trader->name} subscribed to your " . ucfirst($plan) . " plan.",
                'data' => json_encode(['trader_id' => $traderId, 'plan' => $plan]),
            ]);
        }
    }
}
