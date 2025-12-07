<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Feedback;

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
}
