<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Achievement extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'category',
        'xp_reward',
        'criteria_type',
        'criteria_field',
        'criteria_value',
        'tier',
        'is_secret',
    ];

    protected function casts(): array
    {
        return [
            'xp_reward' => 'integer',
            'criteria_value' => 'decimal:2',
            'tier' => 'integer',
            'is_secret' => 'boolean',
        ];
    }

    /**
     * Users who have unlocked this achievement
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_achievements')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }

    /**
     * Get tier label
     */
    public function tierLabel(): string
    {
        return match($this->tier) {
            1 => 'Bronze',
            2 => 'Silver',
            3 => 'Gold',
            4 => 'Platinum',
            default => 'Standard',
        };
    }

    /**
     * Get tier color class
     */
    public function tierColor(): string
    {
        return match($this->tier) {
            1 => 'text-amber-600',
            2 => 'text-slate-400',
            3 => 'text-yellow-400',
            4 => 'text-cyan-400',
            default => 'text-slate-500',
        };
    }

    /**
     * Get category label
     */
    public function categoryLabel(): string
    {
        return match($this->category) {
            'trades' => '📊 Trades',
            'performance' => '📈 Performance',
            'consistency' => '🔥 Consistency',
            'special' => '⭐ Special',
            default => $this->category,
        };
    }
}
