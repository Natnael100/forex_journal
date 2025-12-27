<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Enums\MarketSession;

// Simulate the Blade environment logic
$session = new stdClass();
$session->session = MarketSession::LONDON;
$session->count = 10;

echo "Testing session value extraction...\n";

// Current logic in the blade file (simulated)
try {
    // @php $sessionValue = $session->session instanceof \App\Enums\MarketSession ? $session->session->value : $session->session; @endphp
    $sessionValue = $session->session instanceof MarketSession ? $session->session->value : $session->session;
    
    echo "Session Value: " . $sessionValue . "\n";
    
    // The problematic line: passing object to str_replace if $sessionValue was NOT correctly extracted as string?
    // Wait, if $sessionValue is extracted via ->value, it IS a string. 
    // The error report said "caused by an App\Enums\MarketSession object being passed to str_replace()".
    // This implies $sessionValue IS an object.
    
    // Let's simulate the CASE where it fails.
    // Maybe $session->session is NOT an instance of MarketSession locally but behaves like one? 
    // Or maybe the check `instanceof \App\Enums\MarketSession` fails for some reason (namespace issue)?
    
    // Let's try to reproduce the failure by forcing an object into str_replace
    $badSessionValue = MarketSession::NEWYORK; 
    
    // This is what we suspect is happening effectively:
    // echo ucfirst(str_replace('_', ' ', $badSessionValue)); 
    
} catch (\Throwable $e) {
    echo "Caught expected error: " . $e->getMessage() . "\n";
}

// Proposed Fix Logic Verification
echo "\nTesting Fix Logic...\n";
$sessionValueFixed = $session->session instanceof MarketSession ? $session->session->value : $session->session;

// Ensure it's a string
if ($sessionValueFixed instanceof MarketSession) {
    $sessionValueFixed = $sessionValueFixed->value;
}

echo "Fixed Session Value: " . $sessionValueFixed . "\n";
echo "Display: " . ucfirst(str_replace('_', ' ', $sessionValueFixed)) . "\n";
