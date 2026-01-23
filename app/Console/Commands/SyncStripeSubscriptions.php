<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class SyncStripeSubscriptions extends Command
{
    protected $signature = 'stripe:sync-subscriptions';
    protected $description = 'Manually sync Stripe subscriptions to local database';

    public function handle()
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $this->info('Fetching checkout sessions from Stripe...');

        // Get recent checkout sessions
        $sessions = StripeSession::all(['limit' => 100]);

        $synced = 0;

        foreach ($sessions->data as $session) {
            if ($session->status === 'complete' && $session->metadata) {
                $metadata = $session->metadata;

                // Check if subscription already exists
                if ($session->subscription) {
                    $exists = Subscription::where('stripe_subscription_id', $session->subscription)->exists();

                    if (!$exists && isset($metadata['trader_id']) && isset($metadata['analyst_id'])) {
                        Subscription::create([
                            'analyst_id' => $metadata['analyst_id'],
                            'trader_id' => $metadata['trader_id'],
                            'plan' => $metadata['plan'] ?? 'basic',
                            'price' => $session->amount_total / 100,
                            'status' => 'active',
                            'stripe_subscription_id' => $session->subscription,
                            'current_period_start' => now(),
                            'current_period_end' => now()->addMonth(),
                        ]);

                        $this->info("Created subscription: {$session->subscription}");
                        $synced++;
                    }
                }
            }
        }

        $this->info("Synced {$synced} subscriptions.");
        return 0;
    }
}
