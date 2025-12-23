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
        Schema::create('account_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trade_account_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['deposit', 'withdrawal', 'interest', 'fee', 'adjustment']);
            $table->decimal('amount', 15, 2);
            $table->string('description')->nullable();
            $table->datetime('transaction_date');
            $table->timestamps();
            
            // Indexes
            $table->index(['trade_account_id', 'transaction_date']);
            $table->index('transaction_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_transactions');
    }
};
