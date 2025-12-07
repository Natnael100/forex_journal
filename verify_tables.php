<?php
$pdo = new PDO('sqlite:database/database.sqlite');
$result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name IN ('analyst_assignments', 'feedback', 'notifications')");
$tables = $result->fetchAll(PDO::FETCH_COLUMN);

echo "Phase 6/7 Tables Status:\n";
echo "analyst_assignments: " . (in_array('analyst_assignments', $tables) ? '✓ EXISTS' : '✗ MISSING') . "\n";
echo "feedback: " . (in_array('feedback', $tables) ? '✓ EXISTS' : '✗ MISSING') . "\n";  
echo "notifications: " . (in_array('notifications', $tables) ? '✓ EXISTS' : '✗ MISSING') . "\n";

if (count($tables) == 3) {
    echo "\n✅ ALL TABLES EXIST! Phase 6/7 ready to go!\n";
} else {
    echo "\n⚠️ Tables missing. Run: php create_tables.php\n";
}
