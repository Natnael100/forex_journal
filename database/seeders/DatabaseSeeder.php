<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class, // New Admin Seeder
            UserSeeder::class,
            TradeAccountSeeder::class,
            StrategySeeder::class,
            TradeSeeder::class,
            AnalystAssignmentSeeder::class,
            FeedbackSeeder::class,
            AchievementSeeder::class, // Existing one
        ]);
        
        $this->command->info('ALL DATABASE SEEDS COMPLETED SUCCESSFULLY.');
    }
}
