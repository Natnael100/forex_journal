<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds trade_account_id to trades table to link trades to specific accounts.
     * This column is nullable to maintain backward compatibility with existing trades.
     */
    public function up(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->foreignId('trade_account_id')
                ->nullable()
                ->after('user_id')
                ->constrained('trade_accounts')
                ->nullOnDelete();
            
            $table->index('trade_account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->dropForeign(['trade_account_id']);
            $table->dropIndex(['trade_account_id']);
            $table->dropColumn('trade_account_id');
        });
    }
};
