<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "Checking Schema status...\n";

// 1. Risk Rules
if (!Schema::hasTable('risk_rules')) {
    echo "Creating risk_rules table...\n";
    Schema::create('risk_rules', function (Blueprint $table) {
        $table->id();
        $table->foreignId('analyst_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('trader_id')->constrained('users')->onDelete('cascade');
        $table->string('rule_type');
        $table->decimal('value', 10, 2)->nullable();
        $table->string('parameters')->nullable();
        $table->boolean('is_hard_stop')->default(false);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
        $table->index(['trader_id', 'is_active']);
    });
    echo "risk_rules created.\n";
} else {
    echo "risk_rules table already exists.\n";
}

// 2. Feedback Templates
if (!Schema::hasTable('feedback_templates')) {
    echo "Creating feedback_templates...\n";
    Schema::create('feedback_templates', function (Blueprint $table) {
        $table->id();
        $table->foreignId('analyst_id')->constrained('users')->onDelete('cascade');
        $table->string('category');
        $table->string('title');
        $table->text('content');
        $table->timestamps();
    });
     echo "feedback_templates created.\n";
} else {
    echo "feedback_templates table already exists.\n";
}

// 3. Trades Columns
if (Schema::hasTable('trades')) {
    Schema::table('trades', function (Blueprint $table) {
        if (!Schema::hasColumn('trades', 'is_compliant')) {
             $table->boolean('is_compliant')->default(true)->nullable(); // Nullable for safety on existing rows
             echo "Added is_compliant to trades.\n";
        }
        if (!Schema::hasColumn('trades', 'violation_reason')) {
             $table->string('violation_reason')->nullable();
             echo "Added violation_reason to trades.\n";
        }
        if (!Schema::hasColumn('trades', 'focus_data')) {
             $table->json('focus_data')->nullable();
             echo "Added focus_data to trades.\n";
        }
    });
}

// 4. Assignments Columns
if (Schema::hasTable('analyst_assignments')) {
    Schema::table('analyst_assignments', function (Blueprint $table) {
        if (!Schema::hasColumn('analyst_assignments', 'current_focus_area')) {
             $table->string('current_focus_area')->default('standard');
             echo "Added current_focus_area to assignments.\n";
        }
    });
}

echo "Database Fix Complete.\n";
