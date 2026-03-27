<?php
require_once 'includes/db.php';

$questions = [
    // --- LEVEL 1: BASIC (4 Questions) ---
    [
        'level_id' => 1,
        'title' => 'The ICT Greeting',
        'description' => 'Write a Java program that prints "Welcome to ICT Club".',
        'code_snippet' => "public class Main {\n    public static void main(String[] args) {\n        // Write your code here\n    }\n}",
        'expected_answer' => "public class Main {\n    public static void main(String[] args) {\n        System.out.println(\"Welcome to ICT Club\");\n    }\n}",
        'expected_output' => "Welcome to ICT Club",
        'type' => 'write-code',
        'points' => 50,
        'language' => 'java'
    ],
    [
        'level_id' => 1,
        'title' => 'List the Levels',
        'description' => 'Select the "name" and "required_points" from the "levels" table.',
        'code_snippet' => "-- Write your SQL query here",
        'expected_answer' => "SELECT name, required_points FROM levels;",
        'expected_output' => '[{"name":"Novice Coder","required_points":"0"},{"name":"Script Kiddie","required_points":"300"},{"name":"Hacker","required_points":"1000"}]',
        'type' => 'write-code',
        'points' => 50,
        'language' => 'sql'
    ],
    [
        'level_id' => 1,
        'title' => 'Basic Math',
        'description' => 'Print the result of (100 * 2) + 50 in Java.',
        'code_snippet' => "public class Main {\n    public static void main(String[] args) {\n        // Calculate and print here\n    }\n}",
        'expected_answer' => "public class Main {\n    public static void main(String[] args) {\n        System.out.println((100 * 2) + 50);\n    }\n}",
        'expected_output' => "250",
        'type' => 'write-code',
        'points' => 75,
        'language' => 'java'
    ],
    [
        'level_id' => 1,
        'title' => 'Finding Admins',
        'description' => 'Select only the "username" from the "users" table where "is_admin" is 1.',
        'code_snippet' => "-- Filter the users",
        'expected_answer' => "SELECT username FROM users WHERE is_admin = 1;",
        'expected_output' => '[{"username":"admin"}]',
        'type' => 'write-code',
        'points' => 100,
        'language' => 'sql'
    ],

    // --- LEVEL 2: INTERMEDIATE (3 Questions) ---
    [
        'level_id' => 2,
        'title' => 'The For Loop',
        'description' => 'Print numbers 1, 2, and 3 using a for loop in Java. (Each on a new line)',
        'code_snippet' => "public class Main {\n    public static void main(String[] args) {\n        // Use a loop\n    }\n}",
        'expected_answer' => "public class Main {\n    public static void main(String[] args) {\n        for(int i=1; i<=3; i++) {\n            System.out.println(i);\n        }\n    }\n}",
        'expected_output' => "1\n2\n3",
        'type' => 'write-code',
        'points' => 150,
        'language' => 'java'
    ],
    [
        'level_id' => 2,
        'title' => 'High Scores',
        'description' => 'Select "username" and "total_points" from "users" and sort them by "total_points" from highest to lowest.',
        'code_snippet' => "-- Sort the leaderboard",
        'expected_answer' => "SELECT username, total_points FROM users ORDER BY total_points DESC;",
        'expected_output' => '[{"total_points":"0","username":"admin"}]', // Note: assuming admin has 0 points initially
        'type' => 'write-code',
        'points' => 150,
        'language' => 'sql'
    ],
    [
        'level_id' => 2,
        'title' => 'String Merger',
        'description' => 'Concatenate two strings "Code" and "Point" and print the result with a space in between ("Code Point").',
        'code_snippet' => "public class Main {\n    public static void main(String[] args) {\n        String a = \"Code\";\n        String b = \"Point\";\n        // Merge and print\n    }\n}",
        'expected_answer' => "public class Main {\n    public static void main(String[] args) {\n        String a = \"Code\";\n        String b = \"Point\";\n        System.out.println(a + \" \" + b);\n    }\n}",
        'expected_output' => "Code Point",
        'type' => 'write-code',
        'points' => 200,
        'language' => 'java'
    ],

    // --- LEVEL 3: MEDIUM (3 Questions) ---
    [
        'level_id' => 3,
        'title' => 'Count the Levels',
        'description' => 'Write a SQL query to count total number of levels available in the game. Name the result column "total".',
        'code_snippet' => "-- COUNT function",
        'expected_answer' => "SELECT COUNT(*) as total FROM levels;",
        'expected_output' => '[{"total":"3"}]',
        'type' => 'write-code',
        'points' => 250,
        'language' => 'sql'
    ],
    [
        'level_id' => 3,
        'title' => 'IF Logic',
        'description' => 'Create a variable int points = 1000. If points is greater than or equal to 1000, print "Hacker Level", otherwise print "Regular".',
        'code_snippet' => "public class Main {\n    public static void main(String[] args) {\n        int points = 1000;\n        // Write IF statement\n    }\n}",
        'expected_answer' => "public class Main {\n    public static void main(String[] args) {\n        int points = 1000;\n        if(points >= 1000) {\n            System.out.println(\"Hacker Level\");\n        } else {\n            System.out.println(\"Regular\");\n        }\n    }\n}",
        'expected_output' => "Hacker Level",
        'type' => 'write-code',
        'points' => 300,
        'language' => 'java'
    ],
    [
        'level_id' => 3,
        'title' => 'Advanced Filter',
        'description' => 'Select the "title" of all questions that are in Level 1 and use "java" as the language.',
        'code_snippet' => "-- Double filter",
        'expected_answer' => "SELECT title FROM questions WHERE level_id = 1 AND language = 'java';",
        'expected_output' => '[{"title":"The ICT Greeting"},{"title":"Basic Math"},{"title":"Java Hello World"}]',
        'type' => 'write-code',
        'points' => 350,
        'language' => 'sql'
    ],
];

echo "<h2>Importing 10 New Challenges...</h2>";
$count = 0;

foreach ($questions as $q) {
    // Check if question already exists
    $exists = $db->fetch("SELECT id FROM questions WHERE title = ?", [$q['title']]);
    if (!$exists) {
        $db->insert(
            "INSERT INTO questions (level_id, title, description, code_snippet, expected_answer, expected_output, type, points, language) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$q['level_id'], $q['title'], $q['description'], $q['code_snippet'], $q['expected_answer'], $q['expected_output'], $q['type'], $q['points'], $q['language']]
        );
        echo "✅ Added: " . $q['title'] . "<br>";
        $count++;
    } else {
        echo "⏭️ Skipped (already exists): " . $q['title'] . "<br>";
    }
}

echo "<h3>Successfully added $count new questions!</h3>";
echo "<p><a href='pages/dashboard.php'>Go to Dashboard</a></p>";
