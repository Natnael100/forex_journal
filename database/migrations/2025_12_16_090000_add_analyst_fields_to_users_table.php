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
        Schema::table('users', function (Blueprint $table) {
            // Analyst-specific profile fields
            $table->integer('years_of_experience')->nullable()->after('specialization');
            $table->string('analysis_specialization')->nullable()->after('years_of_experience');
            $table->json('psychology_focus_areas')->nullable()->after('analysis_specialization');
            $table->string('feedback_style')->nullable()->after('psychology_focus_areas');
            $table->integer('max_traders_assigned')->default(5)->after('feedback_style');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'years_of_experience',
                'analysis_specialization',
                'psychology_focus_areas',
                'feedback_style',
                'max_traders_assigned',
            ]);
        });
    }
};
