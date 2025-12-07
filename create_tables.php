<?php
// Direct PHP script to create Phase 6/7 tables
$dbPath = __DIR__ . '/database/database.sqlite';

if (!file_exists($dbPath)) {
    die("Error: Database file not found at: $dbPath\n");
}

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully.\n";
    
    // Read SQL file
    $sql = file_get_contents(__DIR__ . '/database/create_phase6_7_tables.sql');
    
    // Execute the SQL
    $pdo->exec($sql);
    
    echo "✓ SQL executed successfully!\n\n";
    
    // Verify tables were created
    $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name IN ('analyst_assignments', 'feedback', 'notifications')");
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tables found:\n";
    foreach ($tables as $table) {
        echo "  ✓ $table\n";
    }
    
    $expected = ['analyst_assignments', 'feedback', 'notifications'];
    $missing = array_diff($expected, $tables);
    
    if (empty($missing)) {
        echo "\n✅ All Phase 6/7 tables created successfully!\n";
    } else {
        echo "\n⚠️  Missing tables: " . implode(', ', $missing) . "\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
