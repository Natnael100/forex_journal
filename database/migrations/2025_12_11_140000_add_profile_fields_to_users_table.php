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
            // Required profile fields
            $table->string('username', 20)->unique()->nullable()->after('email');
            $table->enum('profile_visibility', ['public', 'analyst_only', 'private'])
                  ->default('analyst_only')
                  ->after('password');
            
            // Profile information
            $table->text('bio')->nullable()->after('profile_visibility');
            $table->string('profile_photo')->nullable()->after('bio');
            $table->string('cover_photo')->nullable()->after('profile_photo');
            $table->string('country', 100)->nullable()->after('cover_photo');
            $table->string('timezone', 100)->default('UTC')->after('country');
            
            // Trading/Performance data
            $table->enum('experience_level', ['beginner', 'intermediate', 'advanced'])
                  ->nullable()
                  ->after('timezone');
            $table->string('specialization', 100)->nullable()->after('experience_level');
            $table->string('trading_style', 100)->nullable()->after('specialization');
            $table->json('preferred_sessions')->nullable()->after('trading_style');
            $table->json('favorite_pairs')->nullable()->after('preferred_sessions');
            $table->json('profile_tags')->nullable()->after('favorite_pairs');
            
            // Social & settings
            $table->json('social_links')->nullable()->after('profile_tags');
            $table->boolean('show_last_active')->default(true)->after('social_links');
            $table->timestamp('profile_completed_at')->nullable()->after('show_last_active');
            $table->boolean('is_profile_verified')->default(false)->after('profile_completed_at');
            
            // Indexes for performance
            $table->index('username');
            $table->index('profile_visibility');
            $table->index('experience_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['username']);
            $table->dropIndex(['profile_visibility']);
            $table->dropIndex(['experience_level']);
            
            $table->dropColumn([
                'username',
                'bio',
                'profile_photo',
                'cover_photo',
                'country',
                'timezone',
                'experience_level',
                'specialization',
                'trading_style',
                'preferred_sessions',
                'favorite_pairs',
                'profile_tags',
                'social_links',
                'profile_visibility',
                'show_last_active',
                'profile_completed_at',
                'is_profile_verified',
            ]);
        });
    }
};
