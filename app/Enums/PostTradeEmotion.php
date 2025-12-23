<?php

namespace App\Enums;

enum PostTradeEmotion: string
{
    case SATISFIED = 'Satisfied';
    case FRUSTRATED = 'Frustrated';
    case GRATEFUL = 'Grateful';
    case ANGRY = 'Angry';
    case NEUTRAL = 'Neutral';
    case OVER_CONFIDENT = 'Over-confident';
    case OTHER = 'Other';

    public function label(): string
    {
        return $this->value;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
