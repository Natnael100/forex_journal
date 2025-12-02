<?php

namespace App\Enums;

enum TradeDirection: string
{
    case BUY = 'buy';
    case SELL = 'sell';

    /**
     * Get the display label
     */
    public function label(): string
    {
        return match($this) {
            self::BUY => 'Buy (Long)',
            self::SELL => 'Sell (Short)',
        };
    }

    /**
     * Get the emoji/icon
     */
    public function icon(): string
    {
        return match($this) {
            self::BUY => '📈',
            self::SELL => '📉',
        };
    }

    /**
     * Get the color class
     */
    public function colorClass(): string
    {
        return match($this) {
            self::BUY => 'text-green-400',
            self::SELL => 'text-red-400',
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
