<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dbPath = config('database.connections.sqlite.database');
echo "Database Config Path: " . $dbPath . "\n";
echo "Real Path: " . (file_exists($dbPath) ? realpath($dbPath) : 'NOT FOUND') . "\n";

try {
    $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "Connected successfully.\n";
    
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('feedback');
    echo "Columns in 'feedback' table:\n";
    print_r($columns);

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
