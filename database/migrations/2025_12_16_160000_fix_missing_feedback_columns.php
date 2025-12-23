<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            if (!Schema::hasColumn('feedback', 'strengths')) {
                $table->json('strengths')->nullable();
                $table->json('weaknesses')->nullable();
                $table->json('recommendations')->nullable();
                $table->tinyInteger('confidence_rating')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->dropColumn(['strengths', 'weaknesses', 'recommendations', 'confidence_rating']);
        });
    }
};
