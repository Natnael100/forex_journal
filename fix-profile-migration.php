<?php

// Quick script to check and apply profile migration manually
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Checking users table structure...\n";

// Check if username column exists
$hasUsername = Schema::hasColumn('users', 'username');

if ($hasUsername) {
    echo "✓ Username column EXISTS\n";
} else {
    echo "✗ Username column MISSING\n";
    echo "Attempting to add columns manually...\n";
    
    try {
        Schema::table('users', function ($table) {
            $table->string('username', 20)->unique()->nullable()->after('email');
            $table->enum('profile_visibility', ['public', 'analyst_only', 'private'])
                  ->default('analyst_only')
                  ->after('password');
            $table->text('bio')->nullable()->after('profile_visibility');
            $table->string('profile_photo')->nullable()->after('bio');
            $table->string('cover_photo')->nullable()->after('profile_photo');
            $table->string('country', 100)->nullable()->after('cover_photo');
            $table->string('timezone', 100)->default('UTC')->after('country');
            $table->enum('experience_level', ['beginner', 'intermediate', 'advanced'])
                  ->nullable()->after('timezone');
            $table->string('specialization', 100)->nullable()->after('experience_level');
            $table->string('trading_style', 100)->nullable()->after('specialization');
            $table->json('preferred_sessions')->nullable()->after('trading_style');
            $table->json('favorite_pairs')->nullable()->after('preferred_sessions');
            $table->json('profile_tags')->nullable()->after('favorite_pairs');
            $table->json('social_links')->nullable()->after('profile_tags');
            $table->boolean('show_last_active')->default(true)->after('social_links');
            $table->timestamp('profile_completed_at')->nullable()->after('show_last_active');
            $table->boolean('is_profile_verified')->default(false)->after('profile_completed_at');
            
            $table->index('username');
            $table->index('profile_visibility');
            $table->index('experience_level');
        });
        
        echo "✓ Columns added successfully!\n";
    } catch (\Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
}

// Verify
$hasUsername = Schema::hasColumn('users', 'username');
echo "\nFinal check: Username column " . ($hasUsername ? "EXISTS" : "MISSING") . "\n";
