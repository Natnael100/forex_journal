<?php

namespace App\Policies;

use App\Models\User;

class ProfilePolicy
{
    /**
     * Determine if the user can view the profile
     */
    public function view(User $user, User $profile): bool
    {
        // Admin can always view
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // User can view own profile
        if ($user->id === $profile->id) {
            return true;
        }
        
        // Check visibility setting
        if ($profile->profile_visibility === 'public') {
            return true;
        }
        
        if ($profile->profile_visibility === 'private') {
            return false;
        }
        
        // analyst_only: check if user is assigned analyst
        if ($profile->profile_visibility === 'analyst_only') {
            // If viewer is analyst and profile is trader
            if ($user->hasRole('analyst') && $profile->hasRole('trader')) {
                return $user->tradersAssigned()->where('trader_id', $profile->id)->exists();
            }
            
            // If viewer is trader and profile is analyst (assigned to them)
            if ($user->hasRole('trader') && $profile->hasRole('analyst')) {
                return $profile->tradersAssigned()->where('trader_id', $user->id)->exists();
            }
        }
        
        return false;
    }

    /**
     * Determine if the user can update the profile
     */
    public function update(User $user, User $profile): bool
    {
        return $user->id === $profile->id || $user->hasRole('admin');
    }

    /**
     * Determine if the user can delete profile photos
     */
    public function deletePhoto(User $user, User $profile): bool
    {
        return $user->id === $profile->id || $user->hasRole('admin');
    }
}
