<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<pre>";
echo "Checking conversations table...\n";
if (Schema::hasTable('conversations')) {
    echo "Conversations table ALREADY EXISTS.\n";
} else {
    echo "Conversations table MISSING. Running migration...\n";
    try {
        Artisan::call('migrate', ['--force' => true]);
        echo "Migration output:\n";
        echo Artisan::output();
    } catch (\Exception $e) {
        echo "Migration FAILED: " . $e->getMessage() . "\n";
    }
}

if (Schema::hasTable('conversations')) {
    echo "SUCCESS: Conversations table now exists.\n";
} else {
    echo "FAILURE: Conversations table still missing.\n";
}
echo "</pre>";
