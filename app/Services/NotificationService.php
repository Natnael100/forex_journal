<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Feedback;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationApproved;
use App\Mail\VerificationRejected;
use App\Mail\FeedbackReceived;
use App\Mail\AnalystAssigned;
use App\Mail\TraderAssignedAnalyst;
use App\Mail\RiskRuleAdded;
use App\Mail\FocusAreaUpdated;
use App\Models\RiskRule;
use App\Models\AnalystAssignment;

class NotificationService
{
    /**
     * Send feedback received notification to trader
     */
    public function notifyFeedbackReceived(User $trader, Feedback $feedback): void
    {
        Notification::create([
            'user_id' => $trader->id,
            'type' => 'feedback_received',
            'data' => [
                'analyst_name' => $feedback->analyst->name,
                'analyst_id' => $feedback->analyst_id,
                'feedback_id' => $feedback->id,
                'trade_id' => $feedback->trade_id,
                'message' => "New feedback from {$feedback->analyst->name}",
                'timestamp' => now()->toDateTimeString(),
            ],
        ]);

        // Send email
        if ($trader->email) {
            Mail::to($trader->email)->send(new FeedbackReceived($trader, $feedback));
        }
    }

    /**
     * Notify analyst when assigned to a trader
     */
    public function notifyAnalystAssigned(User $analyst, User $trader): void
    {
        Notification::create([
            'user_id' => $analyst->id,
            'type' => 'analyst_assigned',
            'data' => [
                'trader_name' => $trader->name,
                'trader_id' => $trader->id,
                'message' => "You have been assigned to {$trader->name}",
                'timestamp' => now()->toDateTimeString(),
            ],
        ]);

        // Send email
        if ($analyst->email) {
            Mail::to($analyst->email)->send(new AnalystAssigned($analyst, $trader));
        }
    }

    /**
     * Notify trader when assigned to an analyst
     */
    public function notifyTraderAssigned(User $trader, User $analyst): void
    {
        Notification::create([
            'user_id' => $trader->id,
            'type' => 'analyst_assigned',
            'data' => [
                'analyst_name' => $analyst->name,
                'analyst_id' => $analyst->id,
                'message' => "{$analyst->name} has been assigned as your analyst",
                'timestamp' => now()->toDateTimeString(),
            ],
        ]);

        // Send email
        if ($trader->email) {
            Mail::to($trader->email)->send(new TraderAssignedAnalyst($trader, $analyst));
        }
    }

    /**
     * Notify trader when feedback is edited
     */
    public function notifyFeedbackEdited(User $trader, Feedback $feedback): void
    {
        Notification::create([
            'user_id' => $trader->id,
            'type' => 'feedback_edited',
            'data' => [
                'analyst_name' => $feedback->analyst->name,
                'feedback_id' => $feedback->id,
                'trade_id' => $feedback->trade_id,
                'message' => "{$feedback->analyst->name} updated their feedback",
                'timestamp' => now()->toDateTimeString(),
            ],
        ]);
    }

    /**
     * Notify user when verification is approved
     */
    public function notifyVerificationApproved(User $user): void
    {
        Notification::create([
            'user_id' => $user->id,
            'type' => 'verification',
            'data' => [
                'title' => '✅ Account Verified!',
                'message' => 'Your account has been approved by an administrator. You now have full access to the platform.',
                'url' => $user->hasRole('analyst') ? route('analyst.dashboard') : route('trader.dashboard'),
            ],
        ]);

        // Send email
        if ($user->email) {
            Mail::to($user->email)->send(new VerificationApproved($user));
        }
    }

    /**
     * Notify user when verification is rejected
     */
    public function notifyVerificationRejected(User $user, string $reason): void
    {
        Notification::create([
            'user_id' => $user->id,
            'type' => 'verification',
            'data' => [
                'title' => '❌ Account Verification Rejected',
                'message' => "Your account verification was rejected. Reason: {$reason}",
                'url' => route('profile.edit'),
            ],
        ]);

        // Send email
        if ($user->email) {
            Mail::to($user->email)->send(new VerificationRejected($user, $reason));
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification): void
    {
        $notification->markAsRead();
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(User $user): void
    {
        $user->notifications()->whereNull('read_at')->update(['read_at' => now()]);
    }

    /**
     * Get unread count for a user
     */
    public function getUnreadCount(User $user): int
    {
        return $user->notifications()->unread()->count();
    }

    /**
     * Get recent notifications for a user
     */
    public function getRecentNotifications(User $user, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return $user->notifications()->latest()->limit($limit)->get();
    }

    /**
     * Notify trader when a risk rule is added
     */
    public function notifyRiskRuleAdded(User $trader, RiskRule $rule): void
    {
        Notification::create([
            'user_id' => $trader->id,
            'type' => 'risk_rule',
            'data' => [
                'title' => 'Risk Rule Added',
                'message' => "New " . str_replace('_', ' ', $rule->rule_type) . " rule assigned.",
                'url' => route('trader.dashboard'),
                'timestamp' => now()->toDateTimeString(),
            ],
        ]);

        if ($trader->email) {
            Mail::to($trader->email)->send(new RiskRuleAdded($trader, $rule));
        }
    }

    /**
     * Notify trader when focus area changes
     */
    public function notifyFocusUpdated(User $trader, AnalystAssignment $assignment): void
    {
        Notification::create([
            'user_id' => $trader->id,
            'type' => 'focus_area',
            'data' => [
                'title' => 'Focus Area Updated',
                'message' => "Your coaching focus is now: " . ucfirst($assignment->current_focus_area),
                'url' => route('trader.trades.create'),
                'timestamp' => now()->toDateTimeString(),
            ],
        ]);

        if ($trader->email) {
            Mail::to($trader->email)->send(new FocusAreaUpdated($trader, $assignment));
        }
    }
}
