<?php

$logFile = __DIR__ . '/db-fix-log.txt';
file_put_contents($logFile, "=== Starting Database Fix ===\n" . date('Y-m-d H:i:s') . "\n\n");

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

function logMessage($msg) {
    global $logFile;
    file_put_contents($logFile, $msg . "\n", FILE_APPEND);
    echo $msg . "\n";
}

logMessage("Checking if username column exists...");
$hasUsername = Schema::hasColumn('users', 'username');
logMessage("Username exists: " . ($hasUsername ? "YES" : "NO"));

if ($hasUsername) {
    logMessage("\n✓ All columns already exist! No action needed.");
    exit(0);
}

logMessage("\n=== Adding Columns ===\n");

$columns = [
    'username' => "ALTER TABLE users ADD COLUMN username VARCHAR(20)",
    'profile_visibility' => "ALTER TABLE users ADD COLUMN profile_visibility VARCHAR(20) DEFAULT 'analyst_only'",
    'bio' => "ALTER TABLE users ADD COLUMN bio TEXT",
    'profile_photo' => "ALTER TABLE users ADD COLUMN profile_photo VARCHAR(255)",
    'cover_photo' => "ALTER TABLE users ADD COLUMN cover_photo VARCHAR(255)",
    'country' => "ALTER TABLE users ADD COLUMN country VARCHAR(100)",
    'timezone' => "ALTER TABLE users ADD COLUMN timezone VARCHAR(100) DEFAULT 'UTC'",
    'experience_level' => "ALTER TABLE users ADD COLUMN experience_level VARCHAR(20)",
    'specialization' => "ALTER TABLE users ADD COLUMN specialization VARCHAR(100)",
    'trading_style' => "ALTER TABLE users ADD COLUMN trading_style VARCHAR(100)",
    'preferred_sessions' => "ALTER TABLE users ADD COLUMN preferred_sessions TEXT",
    'favorite_pairs' => "ALTER TABLE users ADD COLUMN favorite_pairs TEXT",
    'profile_tags' => "ALTER TABLE users ADD COLUMN profile_tags TEXT",
    'social_links' => "ALTER TABLE users ADD COLUMN social_links TEXT",
    'show_last_active' => "ALTER TABLE users ADD COLUMN show_last_active INTEGER DEFAULT 1",
    'profile_completed_at' => "ALTER TABLE users ADD COLUMN profile_completed_at TEXT",
    'is_profile_verified' => "ALTER TABLE users ADD COLUMN is_profile_verified INTEGER DEFAULT 0",
];

$success = 0;
$failed = 0;

foreach ($columns as $name => $sql) {
    try {
        DB::statement($sql);
        logMessage("✓ Added: $name");
        $success++;
    } catch (\Exception $e) {
        logMessage("✗ Failed: $name - " . $e->getMessage());
        $failed++;
    }
}

logMessage("\n=== Summary ===");
logMessage("Success: $success");
logMessage("Failed: $failed");

// Verify
$hasUsername = Schema::hasColumn('users', 'username');
logMessage("\nFinal verification - Username column exists: " . ($hasUsername ? "YES ✓" : "NO ✗"));

if ($hasUsername) {
    logMessage("\n✓✓✓ DATABASE FIX SUCCESSFUL! ✓✓✓");
    logMessage("You can now access /settings/profile");
} else {
    logMessage("\n✗✗✗ DATABASE FIX FAILED ✗✗✗");
    logMessage("Please check the error messages above");
}

logMessage("\n===== END =====");
