<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration creates a default "Default Account" for all existing users who have trades.
     * This ensures backward compatibility - all existing trades will be assigned to this account.
     */
    public function up(): void
    {
        // Get all users who have at least one trade
        $usersWithTrades = DB::table('trades')
            ->select('user_id')
            ->distinct()
            ->pluck('user_id');

        // Create a default account for each user
        foreach ($usersWithTrades as $userId) {
            DB::table('trade_accounts')->insert([
                'user_id' => $userId,
                'account_name' => 'Default Account',
                'account_type' => 'demo',
                'broker' => null,
                'initial_balance' => 10000.00,
                'currency' => 'USD',
                'is_system_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Log the migration result
        $count = count($usersWithTrades);
        \Log::info("Created {$count} default trade accounts for existing users.");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove only the system-generated default accounts
        DB::table('trade_accounts')
            ->where('is_system_default', true)
            ->where('account_name', 'Default Account')
            ->delete();
    }
};
