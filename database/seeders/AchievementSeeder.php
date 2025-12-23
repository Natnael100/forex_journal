<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $achievements = [
            // TRADES CATEGORY - Volume based
            [
                'name' => 'First Trade',
                'slug' => 'first-trade',
                'description' => 'Log your first trade in the journal.',
                'icon' => '🎯',
                'category' => 'trades',
                'xp_reward' => 50,
                'criteria_type' => 'count',
                'criteria_field' => 'trades_count',
                'criteria_value' => 1,
                'tier' => 1,
            ],
            [
                'name' => 'Getting Started',
                'slug' => 'getting-started',
                'description' => 'Log 10 trades in your journal.',
                'icon' => '📊',
                'category' => 'trades',
                'xp_reward' => 100,
                'criteria_type' => 'count',
                'criteria_field' => 'trades_count',
                'criteria_value' => 10,
                'tier' => 1,
            ],
            [
                'name' => 'Committed Trader',
                'slug' => 'committed-trader',
                'description' => 'Log 50 trades in your journal.',
                'icon' => '📈',
                'category' => 'trades',
                'xp_reward' => 250,
                'criteria_type' => 'count',
                'criteria_field' => 'trades_count',
                'criteria_value' => 50,
                'tier' => 2,
            ],
            [
                'name' => 'Century Club',
                'slug' => 'century-club',
                'description' => 'Log 100 trades in your journal.',
                'icon' => '💯',
                'category' => 'trades',
                'xp_reward' => 500,
                'criteria_type' => 'count',
                'criteria_field' => 'trades_count',
                'criteria_value' => 100,
                'tier' => 3,
            ],
            [
                'name' => 'Trading Machine',
                'slug' => 'trading-machine',
                'description' => 'Log 500 trades in your journal.',
                'icon' => '🤖',
                'category' => 'trades',
                'xp_reward' => 1000,
                'criteria_type' => 'count',
                'criteria_field' => 'trades_count',
                'criteria_value' => 500,
                'tier' => 4,
            ],

            // PERFORMANCE CATEGORY
            [
                'name' => 'Positive Mindset',
                'slug' => 'positive-mindset',
                'description' => 'Achieve a 50% win rate.',
                'icon' => '🎯',
                'category' => 'performance',
                'xp_reward' => 150,
                'criteria_type' => 'threshold',
                'criteria_field' => 'win_rate',
                'criteria_value' => 50,
                'tier' => 1,
            ],
            [
                'name' => 'Sharp Shooter',
                'slug' => 'sharp-shooter',
                'description' => 'Achieve a 60% win rate (min 20 trades).',
                'icon' => '🏹',
                'category' => 'performance',
                'xp_reward' => 300,
                'criteria_type' => 'threshold',
                'criteria_field' => 'win_rate',
                'criteria_value' => 60,
                'tier' => 2,
            ],
            [
                'name' => 'Sniper',
                'slug' => 'sniper',
                'description' => 'Achieve a 70% win rate (min 50 trades).',
                'icon' => '🎯',
                'category' => 'performance',
                'xp_reward' => 500,
                'criteria_type' => 'threshold',
                'criteria_field' => 'win_rate',
                'criteria_value' => 70,
                'tier' => 3,
            ],
            [
                'name' => 'Risk Manager',
                'slug' => 'risk-manager',
                'description' => 'Achieve a profit factor above 1.5.',
                'icon' => '⚖️',
                'category' => 'performance',
                'xp_reward' => 200,
                'criteria_type' => 'threshold',
                'criteria_field' => 'profit_factor',
                'criteria_value' => 1.5,
                'tier' => 2,
            ],
            [
                'name' => 'Profitable Trader',
                'slug' => 'profitable-trader',
                'description' => 'Achieve a profit factor above 2.0.',
                'icon' => '💰',
                'category' => 'performance',
                'xp_reward' => 400,
                'criteria_type' => 'threshold',
                'criteria_field' => 'profit_factor',
                'criteria_value' => 2.0,
                'tier' => 3,
            ],

            // CONSISTENCY CATEGORY
            [
                'name' => 'Weekly Warrior',
                'slug' => 'weekly-warrior',
                'description' => 'Log at least 1 trade every day for a week.',
                'icon' => '📅',
                'category' => 'consistency',
                'xp_reward' => 200,
                'criteria_type' => 'streak',
                'criteria_field' => 'daily_streak',
                'criteria_value' => 7,
                'tier' => 2,
            ],
            [
                'name' => 'Monthly Master',
                'slug' => 'monthly-master',
                'description' => 'Log trades for 30 consecutive days.',
                'icon' => '🔥',
                'category' => 'consistency',
                'xp_reward' => 500,
                'criteria_type' => 'streak',
                'criteria_field' => 'daily_streak',
                'criteria_value' => 30,
                'tier' => 3,
            ],
            [
                'name' => 'Hot Streak',
                'slug' => 'hot-streak',
                'description' => 'Win 5 trades in a row.',
                'icon' => '🔥',
                'category' => 'consistency',
                'xp_reward' => 150,
                'criteria_type' => 'streak',
                'criteria_field' => 'win_streak',
                'criteria_value' => 5,
                'tier' => 2,
            ],
            [
                'name' => 'Unstoppable',
                'slug' => 'unstoppable',
                'description' => 'Win 10 trades in a row.',
                'icon' => '⚡',
                'category' => 'consistency',
                'xp_reward' => 400,
                'criteria_type' => 'streak',
                'criteria_field' => 'win_streak',
                'criteria_value' => 10,
                'tier' => 3,
            ],

            // SPECIAL CATEGORY
            [
                'name' => 'Profile Complete',
                'slug' => 'profile-complete',
                'description' => 'Complete your trader profile.',
                'icon' => '👤',
                'category' => 'special',
                'xp_reward' => 100,
                'criteria_type' => 'special',
                'criteria_field' => 'profile_complete',
                'criteria_value' => 1,
                'tier' => 1,
            ],
            [
                'name' => 'Strategy Master',
                'slug' => 'strategy-master',
                'description' => 'Create 5 trading strategies.',
                'icon' => '📘',
                'category' => 'special',
                'xp_reward' => 200,
                'criteria_type' => 'count',
                'criteria_field' => 'strategies_count',
                'criteria_value' => 5,
                'tier' => 2,
            ],
            [
                'name' => 'Early Adopter',
                'slug' => 'early-adopter',
                'description' => 'Join the platform in its early days.',
                'icon' => '🌟',
                'category' => 'special',
                'xp_reward' => 100,
                'criteria_type' => 'special',
                'criteria_field' => 'early_adopter',
                'criteria_value' => 1,
                'tier' => 1,
                'is_secret' => true,
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::updateOrCreate(
                ['slug' => $achievement['slug']],
                $achievement
            );
        }
    }
}
