<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analyst_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('trader_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Ensure unique pair of analyst-trader
            $table->unique(['analyst_id', 'trader_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
