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
        Schema::table('subscriptions', function (Blueprint $table) {
            // Only rename if stripe_subscription_id exists AND chapa_tx_ref doesn't exist
            if (Schema::hasColumn('subscriptions', 'stripe_subscription_id') && 
                !Schema::hasColumn('subscriptions', 'chapa_tx_ref')) {
                $table->renameColumn('stripe_subscription_id', 'chapa_tx_ref');
            } elseif (!Schema::hasColumn('subscriptions', 'stripe_subscription_id') && 
                      !Schema::hasColumn('subscriptions', 'chapa_tx_ref')) {
                // If neither exists, create chapa_tx_ref
                $table->string('chapa_tx_ref')->nullable()->after('status');
            }
            
            // Add Chapa-specific columns if they don't exist
            if (!Schema::hasColumn('subscriptions', 'chapa_reference')) {
                $table->string('chapa_reference')->nullable()->after('chapa_tx_ref');
            }
            if (!Schema::hasColumn('subscriptions', 'renewal_notified_at')) {
                $table->timestamp('renewal_notified_at')->nullable()->after('current_period_end');
            }
            if (!Schema::hasColumn('subscriptions', 'last_renewal_attempt')) {
                $table->timestamp('last_renewal_attempt')->nullable()->after('renewal_notified_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Revert column name
            $table->renameColumn('chapa_tx_ref', 'stripe_subscription_id');
            
            // Drop Chapa columns
            $table->dropColumn(['chapa_reference', 'renewal_notified_at', 'last_renewal_attempt']);
        });
    }
};
