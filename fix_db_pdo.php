<?php
try {
    $dbPath = __DIR__ . '/database/database.sqlite';
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Create risk_rules
    $pdo->exec("CREATE TABLE IF NOT EXISTS risk_rules (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        analyst_id INTEGER NOT NULL,
        trader_id INTEGER NOT NULL,
        rule_type VARCHAR NOT NULL,
        value DECIMAL(10, 2),
        parameters VARCHAR,
        is_hard_stop TINYINT(1) DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at DATETIME,
        updated_at DATETIME,
        FOREIGN KEY (analyst_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (trader_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    
    // Index for risk_rules
    $pdo->exec("CREATE INDEX IF NOT EXISTS risk_rules_trader_id_is_active_index ON risk_rules(trader_id, is_active)");

    // 2. Create feedback_templates
    $pdo->exec("CREATE TABLE IF NOT EXISTS feedback_templates (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        analyst_id INTEGER NOT NULL,
        category VARCHAR NOT NULL,
        title VARCHAR NOT NULL,
        content TEXT NOT NULL,
        created_at DATETIME,
        updated_at DATETIME,
        FOREIGN KEY (analyst_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // 3. Add columns to trades (SQLite is tricky with Add Column if exists, so we try/catch)
    $columnsToAdd = [
        "ALTER TABLE trades ADD COLUMN is_compliant TINYINT(1) DEFAULT 1",
        "ALTER TABLE trades ADD COLUMN violation_reason VARCHAR",
        "ALTER TABLE trades ADD COLUMN focus_data TEXT" // JSON stored as TEXT in SQLite
    ];

    foreach ($columnsToAdd as $sql) {
        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            // Column likely exists, ignore
        }
    }

    // 4. Add columns to analyst_assignments
    try {
        $pdo->exec("ALTER TABLE analyst_assignments ADD COLUMN current_focus_area VARCHAR DEFAULT 'standard'");
    } catch (PDOException $e) {
        // Column likely exists
    }

    echo "Manual PDO Fix Applied Successfully.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
