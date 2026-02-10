<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Chapa Diagnostic Tool ---\n";

// 1. Check Env
$key = config('services.chapa.secret_key');
echo "1. Checking Config...\n";
if (empty($key)) {
    echo "   [FAIL] CHAPA_SECRET_KEY is empty in Laravel config.\n";
    echo "   Did you edit .env and save it?\n";
    exit(1);
} else {
    echo "   [PASS] Secret Key detected (Length: " . strlen($key) . ")\n";
    echo "   [INFO] Key starts with: " . substr($key, 0, 8) . "...\n";
}

// 2. Check Service Logic
echo "\n2. Initializing Service...\n";
try {
    $service = new \App\Services\ChapaPaymentService();
    // Use reflection to check the 'mode' property
    $reflection = new ReflectionClass($service);
    $property = $reflection->getProperty('mode');
    $property->setAccessible(true);
    $mode = $property->getValue($service);
    
    echo "   [INFO] Service Mode is: " . $mode . "\n";
    
    if ($mode !== 'live') {
        echo "   [WARN] Service is NOT in live mode. This might cause the route error.\n";
    }
} catch (\Exception $e) {
    echo "   [FAIL] Service init failed: " . $e->getMessage() . "\n";
    exit(1);
}

// 3. Attempt API Call
echo "\n3. Testing API Connection...\n";
$txRef = 'TEST-' . time();
$data = [
    'amount' => '100',
    'currency' => 'ETB',
    'email' => 'test@example.com',
    'first_name' => 'Test',
    'last_name' => 'User',
    'tx_ref' => $txRef,
    'callback_url' => 'http://localhost/callback',
    'return_url' => 'http://localhost/return',
    'customization' => [
        'title' => 'Connection Test',
        'description' => 'Testing API connectivity'
    ]
];

try {
    $result = $service->initializePayment($data);
    
    if ($result['status'] === 'success') {
        echo "   [PASS] API Call Successful!\n";
        echo "   Checkout URL: " . $result['checkout_url'] . "\n";
    } else {
        echo "   [FAIL] API Call Returned Error:\n";
        print_r($result);
    }
} catch (\Exception $e) {
    echo "   [FAIL] Exception during API call: " . $e->getMessage() . "\n";
}
