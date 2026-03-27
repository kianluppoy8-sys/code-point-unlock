<?php
// Script to safely run database updates on Hostinger
require_once 'includes/db.php';

echo "Checking database schema updates...\n";

try {
    $conn = $db->getConnection();
    
    // 1. Add expected_output to questions if it doesn't exist
    $check = $conn->query("SHOW COLUMNS FROM questions LIKE 'expected_output'");
    if ($check->rowCount() == 0) {
        echo "Adding expected_output column to questions table...\n";
        $conn->exec("ALTER TABLE questions ADD COLUMN expected_output TEXT AFTER expected_answer");
    } else {
        echo "expected_output column already exists.\n";
    }

    // 2. Update existing questions to be Java/SQL only if they are not
    echo "Updating existing non-Java/SQL questions...\n";
    $conn->exec("UPDATE questions SET language = 'java' WHERE language NOT IN ('java', 'sql', 'mysql')");

    echo "Database update completed successfully!\n";
    
} catch (Exception $e) {
    die("Error during update: " . $e->getMessage());
}
