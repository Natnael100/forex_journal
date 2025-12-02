<?php

namespace App\Enums;

enum MarketSession: string
{
    case LONDON = 'london';
    case NEWYORK = 'newyork';
    case ASIA = 'asia';
    case SYDNEY = 'sydney';

    /**
     * Get the display label
     */
    public function label(): string
    {
        return match($this) {
            self::LONDON => 'London',
            self::NEWYORK => 'New York',
            self::ASIA => 'Asia',
            self::SYDNEY => 'Sydney',
        };
    }

    /**
     * Get trading hours (UTC)
     */
    public function tradingHours(): string
    {
        return match($this) {
            self::SYDNEY => '22:00 - 07:00 UTC',
            self::ASIA => '00:00 - 09:00 UTC',
            self::LONDON => '08:00 - 17:00 UTC',
            self::NEWYORK => '13:00 - 22:00 UTC',
        };
    }

    /**
     * Get the color class
     */
    public function colorClass(): string
    {
        return match($this) {
            self::LONDON => 'bg-blue-500/20 text-blue-400',
            self::NEWYORK => 'bg-purple-500/20 text-purple-400',
            self::ASIA => 'bg-red-500/20 text-red-400',
            self::SYDNEY => 'bg-green-500/20 text-green-400',
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
