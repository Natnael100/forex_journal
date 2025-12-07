<?php

namespace App\Policies;

use App\Models\Feedback;
use App\Models\User;

class FeedbackPolicy
{
    /**
     * Determine if the user can view feedback
     */
    public function view(User $user, Feedback $feedback): bool
    {
        // Admins can view all
        if ($user->hasRole('admin')) {
            return true;
        }

        // Analysts can view their own feedback
        if ($user->hasRole('analyst') && $feedback->analyst_id === $user->id) {
            return true;
        }

        // Traders can view feedback on their trades/profile
        if ($user->hasRole('trader') && $feedback->trader_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can create feedback
     */
    public function create(User $user): bool
    {
        return $user->hasRole('analyst') || $user->hasRole('admin');
    }

    /**
     * Determine if the user can update feedback
     */
    public function update(User $user, Feedback $feedback): bool
    {
        // Must be editable and belong to the analyst
        return $feedback->canBeEditedBy($user);
    }

    /**
     * Determine if the user can delete feedback
     */
    public function delete(User $user, Feedback $feedback): bool
    {
        // Same as update - must be editable and belong to analyst
        return $feedback->canBeEditedBy($user);
    }

    /**
     * Determine if analysts can view any feedback
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('analyst') || $user->hasRole('admin');
    }
}
