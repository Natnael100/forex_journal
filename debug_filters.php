<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$id = 22; 
$user = \App\Models\User::find($id);

if (!$user) {
    echo "User $id not found.\n";
    exit;
}

echo "--- DIAGNOSTIC FOR USER $id ---\n";
echo "Name: " . $user->name . "\n";

// 1. Check Role
$hasRole = $user->hasRole('analyst');
echo "1. Has 'analyst' Role? " . ($hasRole ? "YES" : "NO") . "\n";
if (!$hasRole) {
    echo "   Current Roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";
}

// 2. Check Visibility
echo "2. profile_visibility: '" . $user->profile_visibility . "'\n";
echo "   Matches 'public'? " . ($user->profile_visibility === 'public' ? "YES" : "NO") . "\n";

// 3. Check Verification
echo "3. analyst_verification_status: '" . $user->analyst_verification_status . "'\n";
echo "   Matches 'verified'? " . ($user->analyst_verification_status === 'verified' ? "YES" : "NO") . "\n";

// 4. Test Query
$queryCount = \App\Models\User::role('analyst')
    ->where('profile_visibility', 'public')
    ->where('analyst_verification_status', 'verified')
    ->where('id', $id)
    ->count();

echo "4. Database Query Result: " . ($queryCount > 0 ? "FOUND" : "NOT FOUND") . "\n";

// If Role is missing, add it
if (!$hasRole) {
    echo "\nATTEPTING FIX: Assigning 'analyst' role...\n";
    $user->assignRole('analyst');
    echo "Role assigned. Retesting query...\n";
    $retryCount = \App\Models\User::role('analyst')->where('id', $id)->count();
    echo "Query Result after fix: " . ($retryCount > 0 ? "FOUND" : "NOT FOUND") . "\n";
}
