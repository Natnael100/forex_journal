<?php

namespace App\Enums;

enum TransactionType: string
{
    case DEPOSIT = 'deposit';
    case WITHDRAWAL = 'withdrawal';
    case INTEREST = 'interest';
    case FEE = 'fee';
    case ADJUSTMENT = 'adjustment';

    /**
     * Get the display label
     */
    public function label(): string
    {
        return match($this) {
            self::DEPOSIT => 'Deposit',
            self::WITHDRAWAL => 'Withdrawal',
            self::INTEREST => 'Interest',
            self::FEE => 'Fee',
            self::ADJUSTMENT => 'Adjustment',
        };
    }

    /**
     * Get the emoji/icon
     */
    public function icon(): string
    {
        return match($this) {
            self::DEPOSIT => '➕',
            self::WITHDRAWAL => '➖',
            self::INTEREST => '💹',
            self::FEE => '💸',
            self::ADJUSTMENT => '⚖️',
        };
    }

    /**
     * Get the color class
     */
    public function colorClass(): string
    {
        return match($this) {
            self::DEPOSIT => 'text-green-400',
            self::WITHDRAWAL => 'text-red-400',
            self::INTEREST => 'text-blue-400',
            self::FEE => 'text-orange-400',
            self::ADJUSTMENT => 'text-yellow-400',
        };
    }

    /**
     * Check if transaction adds funds
     */
    public function isPositive(): bool
    {
        return match($this) {
            self::DEPOSIT, self::INTEREST, self::ADJUSTMENT => true,
            self::WITHDRAWAL, self::FEE => false,
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
