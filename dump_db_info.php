<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dbPath = config('database.connections.sqlite.database');
$realPath = file_exists($dbPath) ? realpath($dbPath) : 'NOT FOUND';

$output = "Database Config Path: " . $dbPath . "\n";
$output .= "Real Path: " . $realPath . "\n";

try {
    $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
    $output .= "Connected successfully.\n";
    
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('feedback');
    $output .= "Columns in 'feedback' table:\n" . print_r($columns, true);

} catch (\Exception $e) {
    $output .= "Error: " . $e->getMessage();
}

file_put_contents(__DIR__ . '/db_status_report.txt', $output);
