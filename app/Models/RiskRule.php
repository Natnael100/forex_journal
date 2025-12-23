<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'analyst_id',
        'trader_id',
        'rule_type',
        'value',
        'parameters',
        'is_hard_stop',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'is_hard_stop' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function analyst()
    {
        return $this->belongsTo(User::class, 'analyst_id');
    }

    public function trader()
    {
        return $this->belongsTo(User::class, 'trader_id');
    }

    /**
     * Check if a trade violates this rule
     */
    public function checkViolation(array $tradeData): bool
    {
        // Implementation logic will go here in the Service
        // This is a placeholder for model-based check convenience
        return false;
    }
}
