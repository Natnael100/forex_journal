<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalystReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'analyst_id',
        'trader_id',
        'rating',
        'comment',
        'is_approved',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'is_approved' => 'boolean',
        'rating' => 'integer',
    ];

    /**
     * Get the analyst being reviewed.
     */
    public function analyst(): BelongsTo
    {
        return $this->belongsTo(User::class, 'analyst_id');
    }

    /**
     * Get the trader who wrote the review.
     */
    public function trader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trader_id');
    }

    /**
     * Scope to approved reviews only.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Approve this review.
     */
    public function approve(): void
    {
        $this->update([
            'is_approved' => true,
            'approved_at' => now(),
        ]);
    }

    /**
     * Check if review is pending approval.
     */
    public function isPending(): bool
    {
        return !$this->is_approved;
    }
}
