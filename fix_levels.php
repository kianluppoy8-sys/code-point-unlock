<?php
require_once 'includes/db.php';

echo "<h2>Fixing Level Names...</h2>";

try {
    $db->execute("UPDATE levels SET name = 'Script Kiddie' WHERE name = 'medium'");
    $db->execute("UPDATE levels SET name = 'Hacker' WHERE name = 'master'");
    
    echo "✅ Level names updated successfully!<br>";
    echo "<p><a href='import_questions.php'>Now click here to import the 10 questions again</a></p>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
