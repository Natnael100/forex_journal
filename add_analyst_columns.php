<?php

// Add analyst fields to users table
// Run this with: php add_analyst_columns.php

$db = new PDO('sqlite:database/database.sqlite');

try {
    echo "Adding analyst fields to users table...\n";
    
    $db->exec("ALTER TABLE users ADD COLUMN years_of_experience INTEGER");
    echo "✓ Added years_of_experience\n";
    
    $db->exec("ALTER TABLE users ADD COLUMN analysis_specialization VARCHAR(255)");
    echo "✓ Added analysis_specialization\n";
    
    $db->exec("ALTER TABLE users ADD COLUMN psychology_focus_areas TEXT");
    echo "✓ Added psychology_focus_areas\n";
    
    $db->exec("ALTER TABLE users ADD COLUMN feedback_style VARCHAR(255)");
    echo "✓ Added feedback_style\n";
    
    $db->exec("ALTER TABLE users ADD COLUMN max_traders_assigned INTEGER DEFAULT 5");
    echo "✓ Added max_traders_assigned\n";
    
    echo "\n✅ All analyst fields added successfully!\n";
    echo "You can now register as an analyst.\n";
    
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'duplicate column name') !== false) {
        echo "⚠️  Columns already exist. No changes needed.\n";
    } else {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}
