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

    public function analystAssignments()
    {
        return $this->hasMany(\App\Models\AnalystAssignment::class, 'trader_id');
    }

    public function tradersAssigned()
    {
        return $this->hasMany(\App\Models\AnalystAssignment::class, 'analyst_id');
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
}
