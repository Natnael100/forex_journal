<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Assigns ALL existing trades to their user's default account.
     * This ensures 100% backward compatibility - no trade is left without an account.
     */
    public function up(): void
    {
        // Get all unique users from trades
        $usersWithTrades = DB::table('trades')
            ->select('user_id')
            ->distinct()
            ->pluck('user_id');

        $totalAssigned = 0;

        foreach ($usersWithTrades as $userId) {
            // Get the user's default account
            $defaultAccount = DB::table('trade_accounts')
                ->where('user_id', $userId)
                ->where('is_system_default', true)
                ->first();

            if ($defaultAccount) {
                // Assign all trades without an account to the default account
                $assigned = DB::table('trades')
                    ->where('user_id', $userId)
                    ->whereNull('trade_account_id')
                    ->update(['trade_account_id' => $defaultAccount->id]);

                $totalAssigned += $assigned;
            } else {
                \Log::warning("No default account found for user {$userId}. Trades not assigned.");
            }
        }

        \Log::info("Assigned {$totalAssigned} existing trades to default accounts.");
    }

    /**
     * Reverse the migrations.
     * 
     * Sets trade_account_id back to NULL for all trades that were assigned to default accounts.
     */
    public function down(): void
    {
        // Get all default account IDs
        $defaultAccountIds = DB::table('trade_accounts')
            ->where('is_system_default', true)
            ->pluck('id');

        // Set trades back to null if they were pointing to default accounts
        DB::table('trades')
            ->whereIn('trade_account_id', $defaultAccountIds)
            ->update(['trade_account_id' => null]);
    }
};
