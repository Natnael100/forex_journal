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
        // 1. Create Risk Rules table
        if (!Schema::hasTable('risk_rules')) {
            Schema::create('risk_rules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('analyst_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('trader_id')->constrained('users')->onDelete('cascade');
                $table->string('rule_type'); // max_risk_percent, max_daily_loss, no_trading_session, max_leverage
                $table->decimal('value', 10, 2)->nullable(); // e.g., 2.0 (for 2%)
                $table->string('parameters')->nullable(); // e.g., "asia" for session rule
                $table->boolean('is_hard_stop')->default(false); // true = blocks trade, false = warning
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['trader_id', 'is_active']);
            });
        }

        // 2. Create Feedback Templates table
        if (!Schema::hasTable('feedback_templates')) {
            Schema::create('feedback_templates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('analyst_id')->constrained('users')->onDelete('cascade');
                $table->string('category'); // risk, psychology, strategy, general
                $table->string('title');
                $table->text('content');
                $table->timestamps();
            });
        }

        // 3. Update Trades table for Compliance & Guided Journaling
        if (Schema::hasTable('trades')) {
            Schema::table('trades', function (Blueprint $table) {
                if (!Schema::hasColumn('trades', 'is_compliant')) {
                    $table->boolean('is_compliant')->default(true)->after('outcome');
                }
                if (!Schema::hasColumn('trades', 'violation_reason')) {
                    $table->string('violation_reason')->nullable()->after('is_compliant');
                }
                if (!Schema::hasColumn('trades', 'focus_data')) {
                    $table->json('focus_data')->nullable()->after('notes');
                }
            });
        }

        // 4. Update Analyst Assignments for Focus Area
        if (Schema::hasTable('analyst_assignments')) {
            Schema::table('analyst_assignments', function (Blueprint $table) {
                if (!Schema::hasColumn('analyst_assignments', 'current_focus_area')) {
                    $table->string('current_focus_area')->default('standard')->after('assigned_by');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_rules');
        Schema::dropIfExists('feedback_templates');

        Schema::table('trades', function (Blueprint $table) {
            $table->dropColumn(['is_compliant', 'violation_reason', 'focus_data']);
        });

        Schema::table('analyst_assignments', function (Blueprint $table) {
            $table->dropColumn('current_focus_area');
        });
    }
};
