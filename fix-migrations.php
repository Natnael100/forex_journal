<?php

// Mark existing migrations as completed and run profile migration
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Fixing migration status...\n\n";

// Get the current max batch number
$maxBatch = DB::table('migrations')->max('batch') ?? 0;
$nextBatch = $maxBatch + 1;

// Migrations that exist but are marked as pending
$existingMigrations = [
    '2025_12_07_113800_create_feedback_table',
    '2025_12_07_113900_create_notifications_table',
    '2025_12_07_114000_create_analyst_assignments_table',
];

foreach ($existingMigrations as $migration) {
    // Check if already in migrations table
    $exists = DB::table('migrations')->where('migration', $migration)->exists();
    
    if (!$exists) {
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => $nextBatch,
        ]);
        echo "✓ Marked as completed: $migration\n";
    } else {
        echo "- Already completed: $migration\n";
    }
}

echo "\nNow running profile migration...\n";

// Run the profile migration
try {
    \Illuminate\Support\Facades\Artisan::call('migrate', [
        '--path' => 'database/migrations/2025_12_11_140000_add_profile_fields_to_users_table.php',
        '--force' => true
    ]);
    
    $output = \Illuminate\Support\Facades\Artisan::output();
    echo $output;
    echo "\n✓ Profile migration completed!\n";
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
