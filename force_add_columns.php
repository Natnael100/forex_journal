<?php

$dbPath = 'C:\\Users\\natna\\sites\\forex-journal\\database\\database.sqlite';

if (!file_exists($dbPath)) {
    die("Database not found at $dbPath");
}

try {
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verify current structure
    $log = "Checking current columns...\n";
    $stmt = $pdo->query("PRAGMA table_info(feedback)");
    $existingColumns = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $existingColumns[] = $row['name'];
    }
    $log .= "Current columns: " . implode(', ', $existingColumns) . "\n";

    $columnsToAdd = [
        "ALTER TABLE feedback ADD COLUMN strengths TEXT",
        "ALTER TABLE feedback ADD COLUMN weaknesses TEXT",
        "ALTER TABLE feedback ADD COLUMN recommendations TEXT",
        "ALTER TABLE feedback ADD COLUMN confidence_rating INTEGER"
    ];

    foreach ($columnsToAdd as $sql) {
        try {
            $pdo->exec($sql);
            $log .= "Executed: $sql\n";
        } catch (PDOException $e) {
            // Ignore "duplicate column name" error (Code HY000 or similar)
            if (strpos($e->getMessage(), 'duplicate column name') !== false) {
                $log .= "Column already exists: " . substr($sql, 30) . "\n";
            } else {
                $log .= "Error executing $sql: " . $e->getMessage() . "\n";
            }
        }
    }

    $log .= "Schema update complete.\n";

} catch (Exception $e) {
    $log .= "Connection failed: " . $e->getMessage();
}

file_put_contents(__DIR__ . '/db_check_result.log', $log);
