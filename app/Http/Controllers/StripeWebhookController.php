<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    /**
     * Handle Stripe webhook events
     */
    public function handle(Request $request)
    {
        // Debug: Log that webhook was received
        Log::info('Stripe webhook received', [
            'headers' => $request->headers->all(),
            'has_signature' => $request->hasHeader('Stripe-Signature'),
        ]);

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        // Debug: Log configuration
        Log::info('Webhook config', [
            'has_secret' => !empty($webhookSecret),
            'payload_length' => strlen($payload),
        ]);

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
            
            Log::info('Webhook event constructed successfully', ['type' => $event->type]);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutCompleted($event->data->object);
                break;

            case 'invoice.paid':
                // For subscriptions, invoice.paid contains the checkout session metadata
                $this->handleInvoicePaid($event->data->object);
                break;

            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($event->data->object);
                break;

            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;

            case 'invoice.payment_succeeded':
                $this->handlePaymentSucceeded($event->data->object);
                break;

            case 'invoice.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;

            default:
                Log::info('Unhandled Stripe webhook event', ['type' => $event->type]);
        }

        return response('Webhook handled', 200);
    }

    /**
     * Handle checkout session completion
     */
    private function handleCheckoutCompleted($session)
    {
        try {
            $metadata = (array) $session->metadata;

            // Validate required metadata
            if (!isset($metadata['analyst_id']) || !isset($metadata['trader_id']) || !isset($metadata['plan'])) {
                Log::error('Missing required metadata in checkout session', ['session_id' => $session->id]);
                return;
            }

            Subscription::create([
                'analyst_id' => $metadata['analyst_id'],
                'trader_id' => $metadata['trader_id'],
                'plan' => $metadata['plan'],
                'price' => $session->amount_total / 100, // Convert from cents
                'status' => 'active',
                'stripe_subscription_id' => $session->subscription,
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
            ]);

            Log::info('Subscription created via Stripe webhook', [
                'trader_id' => $metadata['trader_id'],
                'analyst_id' => $metadata['analyst_id'],
                'plan' => $metadata['plan'],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create subscription from webhook', [
                'error' => $e->getMessage(),
                'session_id' => $session->id,
            ]);
        }
    }

    /**
     * Handle invoice paid event (primary event for subscription creation)
     */
    private function handleInvoicePaid($invoice)
    {
        try {
            // Log full invoice details for debugging
            Log::info('Invoice paid event details', [
                'invoice_id' => $invoice->id,
                'subscription' => $invoice->subscription,
                'customer' => $invoice->customer,
                'subscription_details' => $invoice->subscription_details ?? null,
                'metadata' => $invoice->metadata ?? null,
            ]);

            // Skip if this is not a subscription invoice
            if (!$invoice->subscription) {
                Log::warning('Invoice paid but no subscription ID found', [
                    'invoice_id' => $invoice->id,
                    'has_lines' => isset($invoice->lines),
                    'customer' => $invoice->customer,
                ]);
                return;
            }

            // Check if we already created this subscription
            $existingSubscription = Subscription::where('stripe_subscription_id', $invoice->subscription)->first();
            if ($existingSubscription) {
                Log::info('Subscription already exists, skipping', ['subscription_id' => $invoice->subscription]);
                return;
            }

            // Fetch the checkout session to get metadata
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $checkoutSessions = \Stripe\Checkout\Session::all([
                'subscription' => $invoice->subscription,
                'limit' => 1,
            ]);

            if (count($checkoutSessions->data) === 0) {
                Log::error('Could not find checkout session for subscription', ['subscription_id' => $invoice->subscription]);
                return;
            }

            $session = $checkoutSessions->data[0];
            $metadata = (array) $session->metadata;

            // Validate required metadata
            if (!isset($metadata['analyst_id']) || !isset($metadata['trader_id']) || !isset($metadata['plan'])) {
                Log::error('Missing required metadata in checkout session', [
                    'session_id' => $session->id,
                    'metadata' => $metadata
                ]);
                return;
            }

            Subscription::create([
                'analyst_id' => $metadata['analyst_id'],
                'trader_id' => $metadata['trader_id'],
                'plan' => $metadata['plan'],
                'price' => $invoice->amount_paid / 100, // Convert from cents
                'status' => 'active',
                'stripe_subscription_id' => $invoice->subscription,
                'current_period_start' => \Carbon\Carbon::createFromTimestamp($invoice->period_start),
                'current_period_end' => \Carbon\Carbon::createFromTimestamp($invoice->period_end),
            ]);

            // Create analyst assignment if it doesn't exist
            $assignment = \App\Models\AnalystAssignment::firstOrCreate(
                [
                    'analyst_id' => $metadata['analyst_id'],
                    'trader_id' => $metadata['trader_id'],
                ],
                [
                    'status' => 'active',
                ]
            );

            // Send notification to analyst
            $analyst = \App\Models\User::find($metadata['analyst_id']);
            $trader = \App\Models\User::find($metadata['trader_id']);
            
            if ($analyst && $trader) {
                \App\Models\Notification::create([
                    'user_id' => $analyst->id,
                    'type' => 'new_subscription',
                    'title' => 'New Subscriber!',
                    'message' => "{$trader->name} subscribed to your " . ucfirst($metadata['plan']) . " plan ($" . number_format($invoice->amount_paid / 100, 2) . "/mo)",
                    'data' => json_encode([
                        'trader_id' => $trader->id,
                        'trader_name' => $trader->name,
                        'plan' => $metadata['plan'],
                        'price' => $invoice->amount_paid / 100,
                    ]),
                ]);

                Log::info('Notification sent to analyst for new subscription', [
                    'analyst_id' => $analyst->id,
                    'trader_id' => $trader->id,
                    'plan' => $metadata['plan'],
                ]);
            }

            Log::info('Subscription created from invoice.paid webhook', [
                'trader_id' => $metadata['trader_id'],
                'analyst_id' => $metadata['analyst_id'],
                'plan' => $metadata['plan'],
                'amount' => $invoice->amount_paid / 100,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create subscription from invoice.paid', [
                'error' => $e->getMessage(),
                'invoice_id' => $invoice->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }


    /**
     * Handle subscription updates
     */
    private function handleSubscriptionUpdated($stripeSubscription)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if ($subscription) {
            $subscription->update([
                'status' => $stripeSubscription->status === 'active' ? 'active' : 'paused',
                'current_period_end' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end),
            ]);
        }
    }

    /**
     * Handle subscription deletion
     */
    private function handleSubscriptionDeleted($stripeSubscription)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if ($subscription) {
            $subscription->update(['status' => 'cancelled']);
        }
    }

    /**
     * Handle successful payment
     */
    private function handlePaymentSucceeded($invoice)
    {
        Log::info('Payment succeeded', ['subscription_id' => $invoice->subscription]);
        // Could send notification to analyst here
    }

    /**
     * Handle failed payment
     */
    private function handlePaymentFailed($invoice)
    {
        Log::warning('Payment failed', ['subscription_id' => $invoice->subscription]);
        
        $subscription = Subscription::where('stripe_subscription_id', $invoice->subscription)->first();
        
        if ($subscription) {
            $subscription->update(['status' => 'paused']);
            // Send notification to trader about failed payment
        }
    }
}
