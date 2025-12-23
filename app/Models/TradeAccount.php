<?php

namespace App\Models;

use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradeAccount extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'account_name',
        'account_type',
        'broker',
        'initial_balance',
        'currency',
        'is_system_default',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'account_type' => AccountType::class,
            'initial_balance' => 'decimal:2',
            'is_system_default' => 'boolean',
        ];
    }

    /**
     * Get the user who owns this account
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all trades for this account
     */
    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class);
    }

    /**
     * Get all transactions for this account
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(AccountTransaction::class);
    }

    /**
     * Calculate the current balance
     * 
     * Current Balance = Initial Balance + Trade P/L + Deposits - Withdrawals + Adjustments - Fees
     */
    public function getCurrentBalanceAttribute(): float
    {
        // Sum of all trade profit/loss
        $tradesPL = $this->trades()->sum('profit_loss') ?? 0;

        // Sum deposits
        $deposits = $this->transactions()
            ->where('type', 'deposit')
            ->sum('amount') ?? 0;

        // Sum withdrawals (negative impact)
        $withdrawals = $this->transactions()
            ->where('type', 'withdrawal')
            ->sum('amount') ?? 0;

        // Sum interest (positive impact)
        $interest = $this->transactions()
            ->where('type', 'interest')
            ->sum('amount') ?? 0;

        // Sum fees (negative impact)
        $fees = $this->transactions()
            ->where('type', 'fee')
            ->sum('amount') ?? 0;

        // Sum adjustments
        $adjustments = $this->transactions()
            ->where('type', 'adjustment')
            ->sum('amount') ?? 0;

        return (float) ($this->initial_balance + $tradesPL + $deposits - $withdrawals + $interest - $fees + $adjustments);
    }

    /**
     * Get total deposits
     */
    public function getTotalDepositsAttribute(): float
    {
        return (float) $this->transactions()
            ->where('type', 'deposit')
            ->sum('amount') ?? 0;
    }

    /**
     * Get total withdrawals
     */
    public function getTotalWithdrawalsAttribute(): float
    {
        return (float) $this->transactions()
            ->where('type', 'withdrawal')
            ->sum('amount') ?? 0;
    }

    /**
     * Get net profit/loss from trades
     */
    public function getNetProfitLossAttribute(): float
    {
        return (float) $this->trades()->sum('profit_loss') ?? 0;
    }

    /**
     * Get trade count
     */
    public function getTradeCountAttribute(): int
    {
        return $this->trades()->count();
    }

    /**
     * Check if this is a system default account
     */
    public function isSystemDefault(): bool
    {
        return $this->is_system_default;
    }

    /**
     * Scope to get only user-created accounts
     */
    public function scopeUserCreated($query)
    {
        return $query->where('is_system_default', false);
    }

    /**
     * Scope to get only default accounts
     */
    public function scopeSystemDefault($query)
    {
        return $query->where('is_system_default', true);
    }
}
