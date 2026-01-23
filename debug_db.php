<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Database Configured Path: " . Config::get('database.connections.sqlite.database') . "\n";
echo "Database Relative Path: " . database_path('database.sqlite') . "\n";

try {
    $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
    echo "Tables found:\n";
    $found = false;
    foreach ($tables as $table) {
        echo "- " . $table->name . "\n";
        if ($table->name === 'analyst_applications') {
            $found = true;
        }
    }
    
    if ($found) {
        echo "\nSUCCESS: analyst_applications table EXISTS.\n";
    } else {
        echo "\nFAILURE: analyst_applications table MISSING.\n";
        
        // Attempt to create it right here
        echo "Attempting to create table via PHP...\n";
        DB::statement("
            CREATE TABLE IF NOT EXISTS analyst_applications (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                name VARCHAR NOT NULL,
                email VARCHAR NOT NULL UNIQUE,
                country VARCHAR NULL,
                timezone VARCHAR NULL,
                phone VARCHAR NULL,
                years_experience VARCHAR NOT NULL,
                certifications TEXT NULL,
                certificate_files TEXT NULL,
                methodology TEXT NULL,
                specializations TEXT NULL,
                coaching_experience VARCHAR NOT NULL,
                clients_coached VARCHAR NOT NULL,
                coaching_style VARCHAR NULL,
                track_record_url VARCHAR NULL,
                linkedin_url VARCHAR NULL,
                twitter_handle VARCHAR NULL,
                youtube_url VARCHAR NULL,
                website_url VARCHAR NULL,
                why_join TEXT NOT NULL,
                unique_value TEXT NOT NULL,
                max_clients VARCHAR NOT NULL,
                communication_methods TEXT NULL,
                status VARCHAR DEFAULT 'pending' CHECK(status IN ('pending', 'approved', 'rejected')) NOT NULL,
                rejection_reason TEXT NULL,
                reviewed_by INTEGER NULL,
                reviewed_at DATETIME NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                FOREIGN KEY(reviewed_by) REFERENCES users(id) ON DELETE SET NULL
            );
        ");
        echo "Table creation command sent.\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
