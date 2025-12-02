<?php

namespace App\Models;

use App\Enums\MarketSession;
use App\Enums\TradeDirection;
use App\Enums\TradeOutcome;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Tags\HasTags;

class Trade extends Model
{
    use HasFactory, SoftDeletes, HasTags, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'pair',
        'direction',
        'entry_date',
        'exit_date',
        'strategy',
        'session',
        'emotion',
        'risk_reward_ratio',
        'outcome',
        'pips',
        'profit_loss',
        'tradingview_link',
        'notes',
        'has_feedback',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'entry_date' => 'datetime',
            'exit_date' => 'datetime',
            'direction' => TradeDirection::class,
            'session' => MarketSession::class,
            'outcome' => TradeOutcome::class,
            'risk_reward_ratio' => 'decimal:2',
            'pips' => 'decimal:2',
            'profit_loss' => 'decimal:2',
            'has_feedback' => 'boolean',
        ];
    }

    /**
     * Get the activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the trader who owns the trade
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $start, $end)
    {
        if ($start) {
            $query->where('entry_date', '>=', $start);
        }
        if ($end) {
            $query->where('entry_date', '<=', $end);
        }
        return $query;
    }

    /**
     * Scope to filter by session
     */
    public function scopeSession($query, $session)
    {
        if ($session) {
            $query->where('session', $session);
        }
        return $query;
    }

    /**
     * Scope to filter by pair
     */
    public function scopePair($query, $pair)
    {
        if ($pair) {
            $query->where('pair', $pair);
        }
        return $query;
    }

    /**
     * Scope to filter by outcome
     */
    public function scopeOutcome($query, $outcome)
    {
        if ($outcome) {
            $query->where('outcome', $outcome);
        }
        return $query;
    }

    /**
     * Scope to filter by feedback status
     */
    public function scopeHasFeedback($query, $hasFeedback)
    {
        if ($hasFeedback !== null) {
            $query->where('has_feedback', $hasFeedback);
        }
        return $query;
    }

    /**
     * Check if trade is profitable
     */
    public function isProfitable(): bool
    {
        return $this->profit_loss > 0;
    }

    /**
     * Get duration in hours
     */
    public function getDurationAttribute(): ?float
    {
        if (!$this->exit_date) {
            return null;
        }
        return $this->entry_date->diffInHours($this->exit_date);
    }
}
