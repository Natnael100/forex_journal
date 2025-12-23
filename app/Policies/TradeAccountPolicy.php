<?php

namespace App\Policies;

use App\Models\TradeAccount;
use App\Models\User;

class TradeAccountPolicy
{
    /**
     * Determine if the user can view the account
     */
    public function view(User $user, TradeAccount $account): bool
    {
        return $user->id === $account->user_id;
    }

    /**
     * Determine if the user can update the account
     */
    public function update(User $user, TradeAccount $account): bool
    {
        return $user->id === $account->user_id && !$account->is_system_default;
    }

    /**
     * Determine if the user can delete the account
     */
    public function delete(User $user, TradeAccount $account): bool
    {
        return $user->id === $account->user_id 
            && !$account->is_system_default 
            && $account->trades()->count() === 0;
    }
}
