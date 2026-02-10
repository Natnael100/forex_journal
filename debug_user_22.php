<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::find(22);
if ($user) {
    echo "User 22: " . $user->name . "\n";
    echo "Role: " . implode(',', $user->getRoleNames()->toArray()) . "\n";
    echo "Verification: " . $user->analyst_verification_status . "\n";
    echo "Visibility: " . $user->profile_visibility . "\n";
    
    // Check if it shows up in the query used by the controller
    $visible = \App\Models\User::role('analyst')
        ->where('profile_visibility', 'public')
        ->where('analyst_verification_status', 'verified')
        ->where('id', 22)
        ->exists();
        
    echo "Visible in query? " . ($visible ? "YES" : "NO") . "\n";
} else {
    echo "User 22 not found.\n";
}
