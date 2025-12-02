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
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Basic trade info
            $table->string('pair'); // e.g., EUR/USD, GBP/JPY
            $table->enum('direction', ['buy', 'sell']); // TradeDirection enum
            
            // Entry/Exit info
            $table->dateTime('entry_date');
            $table->dateTime('exit_date')->nullable();
            
            // Trade classification
            $table->string('strategy')->nullable(); // Will use tagging system
            $table->enum('session', ['london', 'newyork', 'asia', 'sydney']); // MarketSession enum
            $table->string('emotion')->nullable(); // Psychological state
            
            // Risk/Reward
            $table->decimal('risk_reward_ratio', 8, 2)->nullable(); // e.g., 1:3 = 3.00
            
            // Results
            $table->enum('outcome', ['win', 'loss', 'breakeven']); // TradeOutcome enum
            $table->decimal('pips', 10, 2)->nullable(); // Pips gained/lost
            $table->decimal('profit_loss', 15, 2)->default(0); // Profit/Loss amount
            
            // Additional info
            $table->text('tradingview_link')->nullable();
            $table->text('notes')->nullable();
            
            // Activity tracking (for future phase)
            $table->boolean('has_feedback')->default(false); // Quick check for analyst feedback
            
            $table->timestamps();
            $table->softDeletes(); // Soft delete for data preservation
            
            // Indexes for filtering performance
            $table->index('user_id');
            $table->index('pair');
            $table->index('direction');
            $table->index('session');
            $table->index('outcome');
            $table->index('entry_date');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
