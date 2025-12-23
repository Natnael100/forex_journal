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
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('icon')->default('🏆'); // Emoji icon
            $table->string('category'); // trades, performance, consistency, special
            $table->unsignedInteger('xp_reward')->default(100);
            $table->string('criteria_type'); // count, threshold, streak
            $table->string('criteria_field')->nullable(); // trades_count, win_rate, etc.
            $table->decimal('criteria_value', 10, 2)->default(0);
            $table->unsignedInteger('tier')->default(1); // Bronze=1, Silver=2, Gold=3
            $table->boolean('is_secret')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
