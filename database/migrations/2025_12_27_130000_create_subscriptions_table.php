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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analyst_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('trader_id')->constrained('users')->onDelete('cascade');
            $table->enum('plan', ['basic', 'premium', 'elite'])->default('basic');
            $table->decimal('price', 10, 2);
            $table->enum('status', ['active', 'paused', 'cancelled'])->default('active');
            $table->string('stripe_subscription_id')->nullable();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['analyst_id', 'status']);
            $table->index(['trader_id', 'status']);
            $table->unique(['analyst_id', 'trader_id', 'status'], 'unique_active_subscription');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
