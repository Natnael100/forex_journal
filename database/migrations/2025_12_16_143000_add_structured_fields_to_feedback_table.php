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
        Schema::table('feedback', function (Blueprint $table) {
            $table->json('strengths')->nullable()->after('content');
            $table->json('weaknesses')->nullable()->after('strengths');
            $table->json('recommendations')->nullable()->after('weaknesses');
            $table->tinyInteger('confidence_rating')->nullable()->after('recommendations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->dropColumn(['strengths', 'weaknesses', 'recommendations', 'confidence_rating']);
        });
    }
};
