<?php

namespace App\Enums;

enum AccountType: string
{
    case DEMO = 'demo';
    case REAL = 'real';
    case PROP = 'prop';
    case FUNDED = 'funded';

    /**
     * Get the display label
     */
    public function label(): string
    {
        return match($this) {
            self::DEMO => 'Demo Account',
            self::REAL => 'Real Account',
            self::PROP => 'Prop Firm',
            self::FUNDED => 'Funded Account',
        };
    }

    /**
     * Get the emoji/icon
     */
    public function icon(): string
    {
        return match($this) {
            self::DEMO => '🎮',
            self::REAL => '💰',
            self::PROP => '🏢',
            self::FUNDED => '💎',
        };
    }

    /**
     * Get the color class
     */
    public function colorClass(): string
    {
        return match($this) {
            self::DEMO => 'text-blue-400',
            self::REAL => 'text-green-400',
            self::PROP => 'text-purple-400',
            self::FUNDED => 'text-cyan-400',
        };
    }

    /**
     * Get the badge color class
     */
    public function badgeColorClass(): string
    {
        return match($this) {
            self::DEMO => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
            self::REAL => 'bg-green-500/20 text-green-400 border-green-500/30',
            self::PROP => 'bg-purple-500/20 text-purple-400 border-purple-500/30',
            self::FUNDED => 'bg-cyan-500/20 text-cyan-400 border-cyan-500/30',
        };
    }

    /**
     * Get all values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
