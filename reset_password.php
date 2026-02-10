<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Hash;

$email = 'analystt12@gmail.com';
$newPassword = '12345678';

$user = \App\Models\User::where('email', $email)->first();

if (!$user) {
    echo "❌ No user found with email: $email\n";
    echo "\nSearching for similar emails:\n";
    $similar = \App\Models\User::where('email', 'LIKE', '%analyst%')->get();
    foreach ($similar as $u) {
        echo "  Found: {$u->email} (Name: {$u->name})\n";
    }
    exit;
}

echo "✅ Found user: {$user->name}\n";
echo "Resetting password to: $newPassword\n";

$user->password = Hash::make($newPassword);
$user->save();

echo "✅ Password reset successful!\n";
echo "You can now login with:\n";
echo "  Email: $email\n";
echo "  Password: $newPassword\n";
