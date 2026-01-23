<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'analyst_id',
        'trader_id',
        'plan',
        'price',
        'status',
        'chapa_tx_ref',
        'chapa_reference',
        'current_period_start',
        'current_period_end',
        'renewal_notified_at',
        'last_renewal_attempt',
    ];

    protected $casts = [
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'renewal_notified_at' => 'datetime',
        'last_renewal_attempt' => 'datetime',
        'price' => 'decimal:2',
    ];

    /**
     * Get the analyst for this subscription.
     */
    public function analyst(): BelongsTo
    {
        return $this->belongsTo(User::class, 'analyst_id');
    }

    /**
     * Get the trader for this subscription.
     */
    public function trader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trader_id');
    }

    /**
     * Scope query to active subscriptions only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if subscription is currently active (including 3-day grace period).
     */
    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if (!$this->current_period_end) {
            return false;
        }

        // Allow access if period is future OR within 3 days grace period
        // We use 4 days here to be safe and ensure full 3 day grace coverage
        return $this->current_period_end->isFuture() || 
               now()->diffInDays($this->current_period_end, false) >= -3;
    }

    /**
     * Calculate platform commission (30%).
     */
    public function getPlatformFee(): float
    {
        return $this->price * 0.30;
    }

    /**
     * Calculate analyst earnings (70%).
     */
    public function getAnalystEarnings(): float
    {
        return $this->price * 0.70;
    }

    /**
     * Check if subscription has access to a specific feature.
     */
    public function hasFeature(string $featureKey): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        $planDetails = $this->analyst->getPlan($this->plan);
        
        if (!$planDetails) {
            return false;
        }

        return in_array($featureKey, $planDetails->features ?? []);
    }
}
