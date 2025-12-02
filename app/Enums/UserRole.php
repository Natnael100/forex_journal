<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case TRADER = 'trader';
    case ANALYST = 'analyst';

    /**
     * Get the display name for the role
     */
    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::TRADER => 'Trader',
            self::ANALYST => 'Performance Analyst',
        };
    }

    /**
     * Get the dashboard route for the role
     */
    public function dashboardRoute(): string
    {
        return match($this) {
            self::ADMIN => 'admin.dashboard',
            self::TRADER => 'trader.dashboard',
            self::ANALYST => 'analyst.dashboard',
        };
    }

    /**
     * Get all role values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all role labels
     */
    public static function labels(): array
    {
        return array_map(fn($role) => $role->label(), self::cases());
    }
}
