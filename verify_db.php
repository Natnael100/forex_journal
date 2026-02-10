<?php
$dbPath = 'database/database.sqlite';
$outputFile = 'check_result.txt';
$out = "";

try {
    if (!file_exists($dbPath)) {
        $out .= "Database file not found: $dbPath\n";
    } else {
        $db = new PDO('sqlite:' . $dbPath);
        $result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='dispute_messages'");
        if ($result->fetch()) {
            $out .= "Table dispute_messages exists.\n";
        } else {
            $out .= "Table dispute_messages DOES NOT exist.\n";
            // Try to create it here too as a backup
            $db->exec("CREATE TABLE dispute_messages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                dispute_id INTEGER NOT NULL,
                user_id INTEGER NOT NULL,
                content TEXT,
                attachment TEXT,
                created_at DATETIME,
                updated_at DATETIME,
                FOREIGN KEY (dispute_id) REFERENCES disputes(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )");
            $out .= "Table dispute_messages created manually.\n";
        }
    }
} catch (Exception $e) {
    $out .= "Error: " . $e->getMessage() . "\n";
}

file_put_contents($outputFile, $out);
echo "Check completed. Result written to $outputFile\n";
