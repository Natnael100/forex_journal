<?php
// Test unread notification count for current user
// Run with: php test_unread_count.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get the analyst user (ID 19 from your screenshot)
$user = \App\Models\User::find(19);

if (!$user) {
    echo "User not found!\n";
    exit;
}

echo "Testing notification count for user: {$user->name} (ID: {$user->id})\n\n";

// Test all notifications
$allNotifications = $user->notifications()->get();
echo "Total notifications: " . $allNotifications->count() . "\n";

// Test unread notifications
$unreadNotifications = $user->unreadNotifications()->get();
echo "Unread notifications: " . $unreadNotifications->count() . "\n\n";

// Show details
if ($unreadNotifications->count() > 0) {
    echo "Unread notification details:\n";
    foreach ($unreadNotifications as $notification) {
        echo "- ID: {$notification->id}, Type: {$notification->type}, Created: {$notification->created_at}, Read At: " . ($notification->read_at ?? 'NULL') . "\n";
    }
} else {
    echo "No unread notifications found!\n";
    echo "\nAll notifications:\n";
    foreach ($allNotifications as $notification) {
        echo "- ID: {$notification->id}, Type: {$notification->type}, Read At: " . ($notification->read_at ?? 'NULL (UNREAD)') . "\n";
    }
}
