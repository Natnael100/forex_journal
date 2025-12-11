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
}
