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
            // Only add columns that don't already exist
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'specializations')) {
                $table->json('specializations')->nullable()->comment('Array of trading specializations (scalping, swing, etc.)');
            }
            if (!Schema::hasColumn('users', 'certifications')) {
                $table->json('certifications')->nullable()->comment('Professional credentials (CFA, CMT, etc.)');
            }
            if (!Schema::hasColumn('users', 'years_experience')) {
                $table->integer('years_experience')->nullable()->comment('Years of trading experience');
            }
            if (!Schema::hasColumn('users', 'hourly_rate')) {
                $table->decimal('hourly_rate', 8, 2)->nullable()->comment('Consultation rate per hour');
            }
            if (!Schema::hasColumn('users', 'profile_visibility')) {
                $table->boolean('profile_visibility')->default(false)->comment('Show on public analyst marketplace');
            }
            
            // Stripe integration
            if (!Schema::hasColumn('users', 'stripe_account_id')) {
                $table->string('stripe_account_id')->nullable()->comment('Stripe Connected Account ID');
            }
            if (!Schema::hasColumn('users', 'stripe_onboarding_complete')) {
                $table->boolean('stripe_onboarding_complete')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Only drop columns that exist
            $columnsToDrop = [];
            $possibleColumns = [
                'bio',
                'specializations',
                'certifications',
                'years_experience',
                'hourly_rate',
                'profile_visibility',
                'stripe_account_id',
                'stripe_onboarding_complete',
            ];
            
            foreach ($possibleColumns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $columnsToDrop[] = $column;
                }
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
