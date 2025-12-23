<?php

namespace App\Enums;

enum TradeEmotion: string
{
    case CALM = 'Calm';
    case FOCUSED = 'Focused';
    case EXCITED = 'Excited';
    case FEARFUL = 'Fearful';
    case FOMO = 'FOMO';
    case REVENGE = 'Revenge';
    case TIRED = 'Tired';
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
