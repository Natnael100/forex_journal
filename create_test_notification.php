<?php
// Create test unread notification for user
// Run with: php create_test_notification.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get the user by username
$user = \App\Models\User::where('username', 'yoseph_wegayehu_81b7')->first();

if (!$user) {
    echo "❌ User 'yoseph_wegayehu_81b7' not found!\n";
    exit;
}

echo "Creating test notification for: {$user->name} (ID: {$user->id})\n";

// Create an unread notification
\App\Models\Notification::create([
    'user_id' => $user->id,
    'type' => 'feedback',
    'data' => [
        'title' => '💬 New Test Notification',
        'message' => 'This is a test unread notification to verify the badge works!',
        'url' => route('notifications.index'),
    ],
]);

echo "✅ Test notification created successfully!\n";
echo "➡️  Now refresh your browser to see the red badge appear on the Notifications icon!\n";
