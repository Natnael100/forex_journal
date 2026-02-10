<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Hash;

$email = 'analystt12@gmail.com';
$password = '12345678';

$user = \App\Models\User::where('email', $email)->first();

if (!$user) {
    echo "❌ User NOT found with email: $email\n";
    echo "Recent users:\n";
    foreach (\App\Models\User::latest()->take(3)->get() as $u) {
        echo "  - {$u->email} (created: {$u->created_at})\n";
    }
} else {
    echo "✅ User FOUND:\n";
    echo "  Name: {$user->name}\n";
    echo "  Email: {$user->email}\n";
    echo "  Active: " . ($user->is_active ? 'YES' : 'NO') . "\n";
    echo "  Banned: " . ($user->banned_at ? 'YES' : 'NO') . "\n";
    echo "  Has Password Hash: " . (!empty($user->password) ? 'YES' : 'NO') . "\n";
    
    // Test password
    if (Hash::check($password, $user->password)) {
        echo "  ✅ Password MATCHES!\n";
    } else {
        echo "  ❌ Password DOES NOT MATCH!\n";
        echo "  Stored hash: " . substr($user->password, 0, 30) . "...\n";
    }
}
