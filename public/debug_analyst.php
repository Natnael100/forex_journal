<?php

use App\Models\User;

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$username = 'yoseph_wegayehu_81b7';
$user = User::where('username', $username)->first();

echo "<pre>";
if (!$user) {
    echo "User found by ID (fallback if username changed)...\n";
    // Try to find the latest analyst user updated
    $user = User::role('analyst')->latest('updated_at')->first();
}

if ($user) {
    echo "User: " . $user->name . " (ID: " . $user->id . ")\n";
    echo "Username: " . $user->username . "\n";
    echo "Profile Visibility: " . ($user->profile_visibility ? 'TRUE' : 'FALSE') . "\n";
    echo "Roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";
    
    // Check if they show up in the index query
    $inQuery = User::role('analyst')
            ->where('profile_visibility', true)
            ->where('id', $user->id)
            ->exists();
            
    echo "Visible in public query: " . ($inQuery ? 'YES' : 'NO') . "\n";
} else {
    echo "User not found.\n";
}
echo "</pre>";
