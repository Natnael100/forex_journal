<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'verification_status',
        'is_active',
        'verified_at',
        'rejection_reason',
        'verified_by',
        // Profile fields
        'username',
        'bio',
        'profile_photo',
        'cover_photo',
        'country',
        'timezone',
        'experience_level',
        'specialization',
        'trading_style',
        'preferred_sessions',
        'favorite_pairs',
        'profile_tags',
        'social_links',
        'profile_visibility',
        'show_last_active',
        'profile_completed_at',
        'is_profile_verified',
        // Analyst fields
        'years_of_experience',
        'analysis_specialization',
        'psychology_focus_areas',
        'feedback_style',
        'max_traders_assigned',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'verified_at' => 'datetime',
            // Profile casts
            'preferred_sessions' => 'array',
            'favorite_pairs' => 'array',
            'profile_tags' => 'array',
            'social_links' => 'array',
            'show_last_active' => 'boolean',
            'is_profile_verified' => 'boolean',
            'profile_completed_at' => 'datetime',
            // Analyst casts
            'psychology_focus_areas' => 'array',
        ];
    }

    /**
     * Relationships
     */
    public function trades()
    {
        return $this->hasMany(\App\Models\Trade::class);
    }

    public function feedbackGiven()
    {
        return $this->hasMany(\App\Models\Feedback::class, 'analyst_id');
    }

    public function feedbackReceived()
    {
        return $this->hasMany(\App\Models\Feedback::class, 'trader_id');
    }

    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->hasMany(\App\Models\Notification::class)->whereNull('read_at');
    }

    public function analystAssignments()
    {
        return $this->hasMany(\App\Models\AnalystAssignment::class, 'trader_id');
    }

    public function tradersAssigned()
    {
        return $this->hasMany(\App\Models\AnalystAssignment::class, 'analyst_id');
    }

    public function tradeAccounts()
    {
        return $this->hasMany(\App\Models\TradeAccount::class);
    }

    public function strategies()
    {
        return $this->hasMany(\App\Models\Strategy::class);
    }

    /**
     * Profile methods
     */
    public function getProfilePhotoUrl(string $size = 'large'): string
    {
        if ($this->profile_photo) {
            return asset('storage/profiles/' . $size . '_' . $this->profile_photo);
        }
        return asset('images/default-avatar.png');
    }

    public function getCoverPhotoUrl(): string
    {
        if ($this->cover_photo) {
            return asset('storage/profiles/' . $this->cover_photo);
        }
        return asset('images/default-cover.jpg');
    }

    public function getProfileUrl(): string
    {
        return route('profile.show', $this->username ?? $this->id);
    }

    public function isProfileComplete(): bool
    {
        return $this->profile_completed_at !== null;
    }

    public function calculateProfileCompletion(): int
    {
        $fields = config('profile.completeness_fields', [
            'username', 'bio', 'profile_photo', 'country',
            'timezone', 'experience_level', 'trading_style',
            'preferred_sessions', 'favorite_pairs'
        ]);
        
        $completed = 0;
        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $completed++;
            }
        }
        
        return round(($completed / count($fields)) * 100);
    }

    public function ensureUsername(): void
    {
        if (!$this->username) {
            $base = strtolower(str_replace(' ', '_', $this->name));
            $username = $base . '_' . substr(md5($this->id . time()), 0, 4);
            $this->update(['username' => $username]);
        }
    }

    /**
     * Boot method for activity logging
     */
    protected static function boot()
    {
        parent::boot();
        
        static::updated(function ($user) {
            $profileFields = ['username', 'bio', 'profile_photo', 'cover_photo', 'trading_style', 'specialization', 'profile_visibility'];
            
            if ($user->isDirty($profileFields)) {
                $changes = array_keys(array_intersect_key($user->getDirty(), array_flip($profileFields)));
                activity()
                    ->performedOn($user)
                    ->log('Profile updated: ' . implode(', ', $changes));
            }
        });
    }

    /**
     * Achievements relationship
     */
    public function achievements()
    {
        return $this->belongsToMany(\App\Models\Achievement::class, 'user_achievements')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }

    /**
     * Check if user has unlocked an achievement
     */
    public function hasAchievement(string $slug): bool
    {
        return $this->achievements()->where('slug', $slug)->exists();
    }

    /**
     * Add XP to user and level up if needed
     */
    public function addXp(int $amount): void
    {
        $this->xp += $amount;
        $this->level = $this->calculateLevel();
        $this->save();
    }

    /**
     * Calculate level based on XP
     * Level formula: 100 XP per level, exponential scaling
     */
    public function calculateLevel(): int
    {
        $xp = $this->xp;
        $level = 1;
        $xpNeeded = 100;
        
        while ($xp >= $xpNeeded) {
            $xp -= $xpNeeded;
            $level++;
            $xpNeeded = (int) ($xpNeeded * 1.2); // 20% more XP per level
        }
        
        return $level;
    }

    /**
     * Get XP needed for next level
     */
    public function getXpForNextLevel(): int
    {
        $xp = $this->xp;
        $xpNeeded = 100;
        
        for ($i = 1; $i < $this->level; $i++) {
            $xpNeeded = (int) ($xpNeeded * 1.2);
        }
        
        return $xpNeeded;
    }

    /**
     * Get XP progress to next level (0-100%)
     */
    public function getXpProgress(): int
    {
        $currentLevelXp = 0;
        $xpNeeded = 100;
        
        for ($i = 1; $i < $this->level; $i++) {
            $currentLevelXp += $xpNeeded;
            $xpNeeded = (int) ($xpNeeded * 1.2);
        }
        
        $xpInCurrentLevel = $this->xp - $currentLevelXp;
        return min(100, (int) (($xpInCurrentLevel / $xpNeeded) * 100));
    }

    /**
     * Get level title
     */
    public function getLevelTitle(): string
    {
        return match(true) {
            $this->level >= 50 => 'Legendary Trader',
            $this->level >= 40 => 'Master Trader',
            $this->level >= 30 => 'Expert Trader',
            $this->level >= 20 => 'Advanced Trader',
            $this->level >= 10 => 'Intermediate Trader',
            $this->level >= 5 => 'Apprentice Trader',
            default => 'Novice Trader',
        };
    }
}
