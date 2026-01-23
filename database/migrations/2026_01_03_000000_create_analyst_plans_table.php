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
        Schema::create('analyst_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analyst_id')->constrained('users')->onDelete('cascade');
            $table->enum('tier', ['basic', 'premium', 'elite']);
            $table->decimal('price', 10, 2);
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['analyst_id', 'tier']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analyst_plans');
    }
};
