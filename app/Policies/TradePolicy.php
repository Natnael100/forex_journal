<?php

namespace App\Policies;

use App\Models\Trade;
use App\Models\User;

class TradePolicy
{
    /**
     * Determine if the user can view any trades.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['trader', 'analyst', 'admin']);
    }

    /**
     * Determine if the user can view the trade.
     */
    public function view(User $user, Trade $trade): bool
    {
        // Traders can only view their own trades
        // Analysts and admins can view all trades
        return $user->id === $trade->user_id || $user->hasRole(['analyst', 'admin']);
    }

    /**
     * Determine if the user can create trades.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('trader');
    }

    /**
     * Determine if the user can update the trade.
     */
    public function update(User $user, Trade $trade): bool
    {
        // Only the trade owner can update
        return $user->id === $trade->user_id && $user->hasRole('trader');
    }

    /**
     * Determine if the user can delete the trade.
     */
    public function delete(User $user, Trade $trade): bool
    {
        // Only the trade owner or admin can delete
        return $user->id === $trade->user_id || $user->hasRole('admin');
    }

    /**
     * Determine if the user can restore the trade.
     */
    public function restore(User $user, Trade $trade): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can permanently delete the trade.
     */
    public function forceDelete(User $user, Trade $trade): bool
    {
        return $user->hasRole('admin');
    }
}
