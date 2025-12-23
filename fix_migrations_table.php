<?php

/**
 * Fix Migration Table - Mark Existing Migrations as Run
 * 
 * This script manually inserts records into the migrations table for migrations
 * that have already been executed but aren't tracked in the migrations table.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking migrations table...\n";

// Get all migration files
$migrationPath = __DIR__ . '/database/migrations';
$migrationFiles = glob($migrationPath . '/*.php');

// Get already run migrations
$ranMigrations = DB::table('migrations')->pluck('migration')->toArray();

echo "Found " . count($migrationFiles) . " migration files.\n";
echo "Already ran " . count($ranMigrations) . " migrations.\n\n";

// Mark old migrations as run (those before 2025_12_23)
$toMark = [];
foreach ($migrationFiles as $file) {
    $filename = basename($file, '.php');
    
    // Skip if already marked as run
    if (in_array($filename, $ranMigrations)) {
        continue;
    }
    
    // Only mark migrations BEFORE 2025_12_23 (before Phase 1)
    if (str_starts_with($filename, '2025_12_07_') || 
        str_starts_with($filename, '2025_12_11_') || 
        str_starts_with($filename, '2025_12_16_') || 
        str_starts_with($filename, '2025_12_02_') || 
        str_starts_with($filename, '0001_01_01_')) {
        $toMark[] = $filename;
    }
}

if (empty($toMark)) {
    echo "✅ All old migrations are already marked as run!\n";
    echo "You can now run: php artisan migrate\n";
    exit(0);
}

echo "Marking " . count($toMark) . " existing migrations as run:\n";
foreach ($toMark as $migration) {
    echo "  - $migration\n";
}

echo "\nInserting into migrations table...\n";

foreach ($toMark as $migration) {
    DB::table('migrations')->insert([
        'migration' => $migration,
        'batch' => 1
    ]);
}

echo "\n✅ Done! Old migrations marked as run.\n";
echo "Now run: php artisan migrate\n";
