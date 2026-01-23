<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalystPayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'analyst_id',
        'amount',
        'period_start',
        'period_end',
        'stripe_transfer_id',
        'status',
        'processed_at',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'processed_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the analyst for this payout.
     */
    public function analyst(): BelongsTo
    {
        return $this->belongsTo(User::class, 'analyst_id');
    }

    /**
     * Scope to completed payouts.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to pending payouts.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Mark payout as completed.
     */
    public function markCompleted(string $transferId): void
    {
        $this->update([
            'status' => 'completed',
            'stripe_transfer_id' => $transferId,
            'processed_at' => now(),
        ]);
    }

    /**
     * Mark payout as failed.
     */
    public function markFailed(string $reason): void
    {
        $this->update([
            'status' => 'failed',
            'notes' => $reason,
            'processed_at' => now(),
        ]);
    }
}
