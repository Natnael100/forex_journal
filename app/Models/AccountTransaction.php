<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'trade_account_id',
        'type',
        'amount',
        'description',
        'transaction_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => TransactionType::class,
            'amount' => 'decimal:2',
            'transaction_date' => 'datetime',
        ];
    }

    /**
     * Get the trade account this transaction belongs to
     */
    public function tradeAccount(): BelongsTo
    {
        return $this->belongsTo(TradeAccount::class);
    }

    /**
     * Check if this transaction increases the balance
     */
    public function isPositive(): bool
    {
        return $this->type->isPositive();
    }

    /**
     * Get the signed amount (positive or negative based on type)
     */
    public function getSignedAmountAttribute(): float
    {
        return $this->isPositive() ? $this->amount : -$this->amount;
    }

    /**
     * Scope to filter by transaction type
     */
    public function scopeOfType($query, TransactionType $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $start, $end)
    {
        if ($start) {
            $query->where('transaction_date', '>=', $start);
        }
        if ($end) {
            $query->where('transaction_date', '<=', $end);
        }
        return $query;
    }
}
