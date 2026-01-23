<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Subscription;
use App\Models\Notification;
use App\Services\ChapaPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Carbon\Carbon;

class SubscriptionRenewalTest extends TestCase
{
    // usage: php artisan test --filter SubscriptionRenewalTest

    public function test_it_sends_reminders_3_days_before_expiration()
    {
        // 1. Setup
        $analyst = User::factory()->create(['role' => 'analyst']);
        $trader = User::factory()->create(['role' => 'trader']);

        // Create subscription expiring exactly 3 days from now
        // We add 3 days and set time to mid-day to match "startOfDay" to "endOfDay" window
        $expiryDate = now()->addDays(3)->hour(12);

        $subscription = Subscription::create([
            'analyst_id' => $analyst->id,
            'trader_id' => $trader->id,
            'plan' => 'premium',
            'price' => 500,
            'status' => 'active',
            'current_period_start' => now()->subMonth(),
            'current_period_end' => $expiryDate,
        ]);

        // 2. Run Command
        Artisan::call('subscriptions:process-renewals');

        // 3. Assert
        $this->assertDatabaseHas('notifications', [
            'user_id' => $trader->id,
            'type' => 'subscription_renewal_due',
        ]);
        
        $subscription->refresh();
        $this->assertNotNull($subscription->renewal_notified_at);
    }

    public function test_it_pauses_subscriptions_3_days_overdue()
    {
        // 1. Setup
        $analyst = User::factory()->create(['role' => 'analyst']);
        $trader = User::factory()->create(['role' => 'trader']);

        // Create subscription expired 4 days ago
        $expiryDate = now()->subDays(4);

        $subscription = Subscription::create([
            'analyst_id' => $analyst->id,
            'trader_id' => $trader->id,
            'plan' => 'premium',
            'price' => 500,
            'status' => 'active', // Still active but overdue
            'current_period_start' => now()->subMonth()->subDays(4),
            'current_period_end' => $expiryDate,
        ]);

        // 2. Run Command
        Artisan::call('subscriptions:process-renewals');

        // 3. Assert
        $subscription->refresh();
        $this->assertEquals('paused', $subscription->status);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $trader->id,
            'type' => 'subscription_paused',
        ]);
    }
}
