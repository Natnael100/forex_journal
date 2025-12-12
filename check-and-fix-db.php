<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== Checking Database Schema ===\n\n";

// Check if username column exists
$hasUsername = Schema::hasColumn('users', 'username');
echo "Username column exists: " . ($hasUsername ? "YES" : "NO") . "\n";

if (!$hasUsername) {
    echo "\n=== Adding Missing Columns ===\n";
    
    try {
        // Add columns using raw SQL since Schema::table might have issues with SQLite
        DB::statement('ALTER TABLE users ADD COLUMN username VARCHAR(20)');
        echo "✓ Added username\n";
    } catch (\Exception $e) {
        echo "✗ Username: " . $e->getMessage() . "\n";
    }
    
    try {
        DB::statement("ALTER TABLE users ADD COLUMN profile_visibility VARCHAR(20) DEFAULT 'analyst_only'");
        echo "✓ Added profile_visibility\n";
    } catch (\Exception $e) {
        echo "✗ Profile visibility: " . $e->getMessage() . "\n";
    }
    
    try {
        DB::statement('ALTER TABLE users ADD COLUMN bio TEXT');
        echo "✓ Added bio\n";
    } catch (\Exception $e) {
        echo "✗ Bio: " . $e->getMessage() . "\n";
    }
    
    try {
        DB::statement('ALTER TABLE users ADD COLUMN profile_photo VARCHAR(255)');
        echo "✓ Added profile_photo\n";
    } catch (\Exception $e) {
        echo "✗ Profile photo: " . $e->getMessage() . "\n";
    }
    
    try {
        DB::statement('ALTER TABLE users ADD COLUMN cover_photo VARCHAR(255)');
        echo "✓ Added cover_photo\n";
    } catch (\Exception $e) {
        echo "✗ Cover photo: " . $e->getMessage() . "\n";
    }
    
    try {
        DB::statement('ALTER TABLE users ADD COLUMN country VARCHAR(100)');
        echo "✓ Added country\n";
    } catch (\Exception $e) {
        echo "✗ Country: " . $e->getMessage() . "\n";
    }
    
    try {
        DB::statement("ALTER TABLE users ADD COLUMN timezone VARCHAR(100) DEFAULT 'UTC'");
        echo "✓ Added timezone\n";
    } catch (\Exception $e) {
        echo "✗ Timezone: " . $e->getMessage() . "\n";
    }
    
    try {
        DB::statement('ALTER TABLE users ADD COLUMN experience_level VARCHAR(20)');
        echo "✓ Added experience_level\n";
    } catch (\Exception $e) {
        echo "✗ Experience level: " . $e->getMessage() . "\n";
    }
    
    try {
        DB::statement('ALTER TABLE users ADD COLUMN specialization VARCHAR(100)');
        echo "✓ Added specialization\n";
    } catch (\Exception $e) {
        echo "✗ Specialization: " . $e->getMessage() . "\n";
    }
    
    try {
        DB::statement('ALTER TABLE users ADD COLUMN trading_style VARCHAR(100)');
        echo "✓ Added trading_style\n";
    } catch (\Exception $e) {
        echo "✗ Trading style: " . $e->getMessage() . "\n";
    }
    
    try {
        DB::statement('ALTER TABLE users ADD COLUMN preferred_sessions TEXT');
        echo "✓ Added preferred_sessions\n";
    } catch (\Exception $e) {
        echo "✗ Preferred sessions: " . $e->getMessage() . "\n";
    }
    
    try {
        DB::statement('ALTER TABLE users ADD COLUMN favorite_pairs TEXT');
        echo "✓ Added favorite_pairs\n";
    } catch (\Exception $e) {
        echo "✗ Favorite pairs: " . $e->getMessage() . "\n";
    }
    
    try {
        DB::statement('ALTER TABLE users ADD COLUMN profile_tags TEXT');
        echo "✓ Added profile_tags\n";
    } catch (\Exception $e) {
        echo "✗ Profile tags: " . $e->getMessage() . "\n";
    }
    
    try {
        DB::statement('ALTER TABLE users ADD COLUMN social_links TEXT');
        echo "✓ Added social_links\n";
    } catch (\Exception $e) {
        echo "✗ Social links: " . $e->getMessage() . "\n";
    }
    
    try {
        DB::statement('ALTER TABLE users ADD COLUMN show_last_active INTEGER DEFAULT 1');
        echo "✓ Added show_last_active\n";
    } catch (\Exception $e) {
        echo "✗ Show last active: " . $e->getMessage() . "\n";
    }
    
    try {
        DB::statement('ALTER TABLE users ADD COLUMN profile_completed_at TIMESTAMP');
        echo "✓ Added profile_completed_at\n";
    } catch (\Exception $e) {
        echo "✗ Profile completed at: " . $e->getMessage() . "\n";
    }
    
    try {
        DB::statement('ALTER TABLE users ADD COLUMN is_profile_verified INTEGER DEFAULT 0');
        echo "✓ Added is_profile_verified\n";
    } catch (\Exception $e) {
        echo "✗ Is profile verified: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Verification ===\n";
    $hasUsername = Schema::hasColumn('users', 'username');
    echo "Username column NOW exists: " . ($hasUsername ? "YES ✓" : "NO ✗") . "\n";
} else {
    echo "\nAll columns already exist!\n";
}

echo "\n=== Done ===\n";
