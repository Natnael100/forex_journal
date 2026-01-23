<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Only add columns if they don't already exist
            if (!Schema::hasColumn('users', 'primary_goal')) {
                $table->enum('primary_goal', [
                    'get_funded',           // Pass Prop Firm Challenge
                    'side_income',          // Generate Side Income
                    'full_time_career',     // Full-Time Trading Career
                    'wealth_compounding',   // Long-term Wealth Building
                ])->nullable()->after('favorite_pairs');
            }
            
            if (!Schema::hasColumn('users', 'biggest_challenge')) {
                $table->enum('biggest_challenge', [
                    'psychology_discipline', // FOMO, Revenge Trading, Emotional Control
                    'risk_management',       // Position Sizing, Stop Losses
                    'technical_strategy',    // Entry/Exit Execution
                    'consistency',           // Sticking to Plan
                ])->nullable()->after('primary_goal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['primary_goal', 'biggest_challenge']);
        });
    }
};
