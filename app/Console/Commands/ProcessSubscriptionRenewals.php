<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\Notification;
use App\Services\ChapaPaymentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessSubscriptionRenewals extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'subscriptions:process-renewals';

    /**
     * The console command description.
     */
    protected $description = 'Process subscription renewals - send reminders and pause overdue subscriptions';

    protected $chapaService;

    public function __construct(ChapaPaymentService $chapaService)
    {
        parent::__construct();
        $this->chapaService = $chapaService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing subscription renewals...');

        // 1. Send reminders for subscriptions expiring in 3 days
        $this->sendRenewalReminders();

        // 2. Pause subscriptions that are 3+ days overdue
        $this->pauseOverdueSubscriptions();

        $this->info('Renewal processing complete!');
        return 0;
    }

    /**
     * Send renewal reminders to traders 3 days before expiration
     */
    private function sendRenewalReminders()
    {
        $threeDaysFromNow = now()->addDays(3)->startOfDay();
        $endOfDay = now()->addDays(3)->endOfDay();

        $subscriptions = Subscription::where('status', 'active')
            ->whereBetween('current_period_end', [$threeDaysFromNow, $endOfDay])
            ->whereNull('renewal_notified_at')
            ->with(['trader', 'analyst'])
            ->get();

        $this->info("Found {$subscriptions->count()} subscriptions expiring in 3 days");

        foreach ($subscriptions as $subscription) {
            try {
                // Generate renewal payment link
                $renewalLink = $this->chapaService->generateRenewalLink($subscription);

                // Send notification to trader
                Notification::create([
                    'user_id' => $subscription->trader_id,
                    'type' => 'subscription_renewal_due',
                    'title' => 'Subscription Renewal Due',
                    'message' => "Your {$subscription->plan} subscription with {$subscription->analyst->name} expires in 3 days. Click to renew: {$renewalLink}",
                    'data' => json_encode([
                        'subscription_id' => $subscription->id,
                        'analyst_id' => $subscription->analyst_id,
                        'plan' => $subscription->plan,
                        'price' => $subscription->price,
                        'renewal_link' => $renewalLink,
                        'expires_at' => $subscription->current_period_end,
                    ]),
                ]);

                // Mark as notified
                $subscription->update(['renewal_notified_at' => now()]);

                $this->info("✓ Sent renewal reminder to {$subscription->trader->name}");

            } catch (\Exception $e) {
                Log::error('Failed to send renewal reminder', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("✗ Failed for subscription #{$subscription->id}");
            }
        }
    }

    /**
     * Pause subscriptions that haven't been renewed 3 days after expiration
     */
    private function pauseOverdueSubscriptions()
    {
        $threeDaysAgo = now()->subDays(3);

        $overdueSubscriptions = Subscription::where('status', 'active')
            ->where('current_period_end', '<', $threeDaysAgo)
            ->with(['trader', 'analyst'])
            ->get();

        $this->info("Found {$overdueSubscriptions->count()} overdue subscriptions");

        foreach ($overdueSubscriptions as $subscription) {
            try {
                // Pause subscription
                $subscription->update(['status' => 'paused']);

                // Notify trader
                Notification::create([
                    'user_id' => $subscription->trader_id,
                    'type' => 'subscription_paused',
                    'title' => 'Subscription Paused',
                    'message' => "Your {$subscription->plan} subscription with {$subscription->analyst->name} has been paused due to non-renewal. You can reactivate it anytime.",
                    'data' => json_encode([
                        'subscription_id' => $subscription->id,
                        'analyst_id' => $subscription->analyst_id,
                        'plan' => $subscription->plan,
                    ]),
                ]);

                // Notify analyst
                Notification::create([
                    'user_id' => $subscription->analyst_id,
                    'type' => 'subscription_paused',
                    'title' => 'Subscriber Paused',
                    'message' => "{$subscription->trader->name}'s {$subscription->plan} subscription has been paused due to non-renewal.",
                    'data' => json_encode([
                        'subscription_id' => $subscription->id,
                        'trader_id' => $subscription->trader_id,
                        'plan' => $subscription->plan,
                    ]),
                ]);

                $this->info("✓ Paused subscription for {$subscription->trader->name}");

            } catch (\Exception $e) {
                Log::error('Failed to pause subscription', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("✗ Failed to pause subscription #{$subscription->id}");
            }
        }
    }
}
