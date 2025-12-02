<?php

namespace App\Enums;

enum TradeOutcome: string
{
    case WIN = 'win';
    case LOSS = 'loss';
    case BREAKEVEN = 'breakeven';

    /**
     * Get the display label
     */
    public function label(): string
    {
        return match($this) {
            self::WIN => 'Win',
            self::LOSS => 'Loss',
            self::BREAKEVEN => 'Breakeven',
        };
    }

    /**
     * Get the emoji/icon
     */
    public function icon(): string
    {
        return match($this) {
            self::WIN => '✅',
            self::LOSS => '❌',
            self::BREAKEVEN => '⚖️',
        };
    }

    /**
     * Get the color class
     */
    public function colorClass(): string
    {
        return match($this) {
            self::WIN => 'text-green-400 bg-green-500/20',
            self::LOSS => 'text-red-400 bg-red-500/20',
            self::BREAKEVEN => 'text-yellow-400 bg-yellow-500/20',
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
