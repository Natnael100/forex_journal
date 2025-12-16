<?php
// Comprehensive notification badge debug and test script
// Run with: php debug_badge.php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== NOTIFICATION BADGE DEBUG ===\n\n";

// Step 1: Find user
$username = 'yoseph_wegayehu_81b7';
$user = \App\Models\User::where('username', $username)->first();

if (!$user) {
    echo "❌ User '{$username}' not found!\n";
    echo "Trying to find any user...\n";
    $user = \App\Models\User::first();
    if ($user) {
        echo "✅ Using user: {$user->name} (username: {$user->username}, ID: {$user->id})\n\n";
    } else {
        echo "❌ No users found in database!\n";
        exit;
    }
} else {
    echo "✅ Found user: {$user->name} (ID: {$user->id})\n\n";
}

// Step 2: Check current notifications
echo "--- Current Notifications ---\n";
$allNotifications = \App\Models\Notification::where('user_id', $user->id)->get();
echo "Total notifications: " . $allNotifications->count() . "\n";

$unreadNotifications = \App\Models\Notification::where('user_id', $user->id)->whereNull('read_at')->get();
echo "Unread notifications: " . $unreadNotifications->count() . "\n\n";

if ($allNotifications->count() > 0) {
    echo "All notifications:\n";
    foreach ($allNotifications as $notif) {
        $status = $notif->read_at ? "READ ({$notif->read_at})" : "UNREAD";
        echo "  - ID: {$notif->id}, Type: {$notif->type}, Status: {$status}\n";
    }
    echo "\n";
}

// Step 3: Mark all as unread for testing
echo "--- Marking All as UNREAD for Testing ---\n";
\App\Models\Notification::where('user_id', $user->id)->update(['read_at' => null]);
echo "✅ All notifications marked as unread\n\n";

// Step 4: Create new test notification
echo "--- Creating New Test Notification ---\n";
$newNotification = \App\Models\Notification::create([
    'user_id' => $user->id,
    'type' => 'feedback',
    'data' => [
        'title' => '🔴 BADGE TEST NOTIFICATION',
        'message' => 'This notification should make the red badge appear!',
        'url' => '/notifications',
    ],
]);
echo "✅ Created notification ID: {$newNotification->id}\n\n";

// Step 5: Verify unread count
$finalUnreadCount = \App\Models\Notification::where('user_id', $user->id)->whereNull('read_at')->count();
echo "--- Final Verification ---\n";
echo "Unread count for user {$user->id}: {$finalUnreadCount}\n\n";

// Step 6: Clear caches
echo "--- Clearing Caches ---\n";
Artisan::call('view:clear');
echo "✅ View cache cleared\n";
Artisan::call('cache:clear');
echo "✅ Application cache cleared\n\n";

echo "=== INSTRUCTIONS ===\n";
echo "1. Hard refresh your browser: Ctrl+Shift+R (Windows) or Cmd+Shift+R (Mac)\n";
echo "2. You should see a red badge with number: {$finalUnreadCount}\n";
echo "3. Login as: {$user->username}\n";
echo "4. Check the Notifications icon in the sidebar\n\n";
echo "✅ Done! The badge should now appear.\n";
