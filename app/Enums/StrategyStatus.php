<?php

namespace App\Enums;

enum StrategyStatus: string
{
    case ACTIVE = 'active';
    case TESTING = 'testing';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::TESTING => 'Testing',
            self::ARCHIVED => 'Archived',
        };
    }
}
