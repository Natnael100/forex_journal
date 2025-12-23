<?php

use Illuminate\Support\Facades\Schema;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$hasStrategies = Schema::hasTable('strategies');
$hasColumns = Schema::hasColumns('trades', ['strategy_id', 'entry_price']);

$output = "Strategies Table: " . ($hasStrategies ? 'YES' : 'NO') . PHP_EOL;
$output .= "Trades Columns: " . ($hasColumns ? 'YES' : 'NO') . PHP_EOL;

file_put_contents('schema_status.txt', $output);
