<?php
$dbPath = 'database/database.sqlite';
$outputFile = 'db_tables.txt';
$out = "";

try {
    if (!file_exists($dbPath)) {
        $out .= "Database file not found: $dbPath\n";
    } else {
        $db = new PDO('sqlite:' . $dbPath);
        $result = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
        $tables = [];
        while ($row = $result->fetch()) {
            $tables[] = $row['name'];
        }
        $out .= "Tables: " . implode(', ', $tables) . "\n";
    }
} catch (Exception $e) {
    $out .= "Error: " . $e->getMessage() . "\n";
}

file_put_contents($outputFile, $out);
echo "Dump completed.\n";
