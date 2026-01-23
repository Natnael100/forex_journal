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
        Schema::create('analyst_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analyst_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->string('stripe_transfer_id')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->text('notes')->nullable(); // For admin notes or error messages
            $table->timestamps();
            
            $table->index(['analyst_id', 'status']);
            $table->index('period_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analyst_payouts');
    }
};
