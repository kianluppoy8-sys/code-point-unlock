<?php
require_once 'config.php';

class Database {
    private $conn;
    private static $instance = null;

    private function __construct() {
        try {
            // Exclusively MySQL connection
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";port=" . DB_PORT . ";charset=utf8mb4";
            
            $this->conn = new PDO(
                $dsn,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            
            // Check if database needs initialization
            $this->initTables();
            
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    public function query($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    public function insert($sql, $params = []) {
        $this->query($sql, $params);
        return $this->conn->lastInsertId();
    }

    public function execute($sql, $params = []) {
        return $this->query($sql, $params)->rowCount();
    }
    
    private function initTables() {
        // Check if tables exist (MySQL version)
        $check = $this->fetch("SHOW TABLES LIKE 'users'");
        
        if (empty($check)) {
            // MySQL schema
            $this->conn->exec("
                CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(50) UNIQUE NOT NULL,
                    email VARCHAR(100) UNIQUE NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    is_admin TINYINT(1) DEFAULT 0,
                    total_points INT DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                );
                
                CREATE TABLE IF NOT EXISTS levels (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(100) NOT NULL,
                    required_points INT DEFAULT 0,
                    display_order INT DEFAULT 1,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );
                
                CREATE TABLE IF NOT EXISTS questions (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    level_id INT NOT NULL,
                    title VARCHAR(255) NOT NULL,
                    description TEXT NOT NULL,
                    code_snippet TEXT,
                    expected_answer TEXT NOT NULL,
                    expected_output TEXT,
                    type ENUM('fix-code', 'write-code') DEFAULT 'fix-code',
                    points INT DEFAULT 100,
                    language VARCHAR(50) DEFAULT 'java',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (level_id) REFERENCES levels(id) ON DELETE CASCADE
                );
                
                CREATE TABLE IF NOT EXISTS user_progress (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    question_id INT NOT NULL,
                    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
                    UNIQUE KEY unique_user_question (user_id, question_id)
                );
                
                CREATE TABLE IF NOT EXISTS submissions (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    question_id INT NOT NULL,
                    code TEXT NOT NULL,
                    is_correct TINYINT(1) DEFAULT 0,
                    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
                );
            ");
            
            $this->seedData();
        }
    }
    
    private function seedData() {
        // Only seed if empty
        $admin = $this->fetch("SELECT id FROM users WHERE email = ?", ['admin@example.com']);
        if (!$admin) {
            $this->insert(
                "INSERT INTO users (username, email, password, is_admin, total_points) VALUES (?, ?, ?, ?, ?)",
                ['admin', 'admin@example.com', password_hash('password', PASSWORD_DEFAULT), 1, 0]
            );
        }

        // Check if levels exist
        $levels = $this->fetch("SELECT id FROM levels LIMIT 1");
        if (!$levels) {
            $this->insert("INSERT INTO levels (name, required_points, display_order) VALUES (?, ?, ?)", ['Web Novice', 0, 1]);
            $this->insert("INSERT INTO levels (name, required_points, display_order) VALUES (?, ?, ?)", ['PHP Scripter', 300, 2]);
            $this->insert("INSERT INTO levels (name, required_points, display_order) VALUES (?, ?, ?)", ['Core Architect', 1000, 3]);
            
            // PHP Challenge
            $this->insert(
                "INSERT INTO questions (level_id, title, description, code_snippet, expected_answer, expected_output, type, points, language) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [1, 'PHP Echo Practice', 'Modify the PHP code to print "Hello from ICT Club".',
                "<?php\n// Write code below\n",
                "<?php\necho \"Hello from ICT Club\";",
                "Hello from ICT Club",
                'write-code', 100, 'php']
            );
            
            // SQL Challenge
            $this->insert(
                "INSERT INTO questions (level_id, title, description, code_snippet, expected_answer, expected_output, type, points, language) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [1, 'MySQL Selection', 'Select the username and email of all admins (is_admin = 1).',
                "-- Write your SQL here",
                "SELECT username, email FROM users WHERE is_admin = 1;",
                '[{"email":"admin@example.com","username":"admin"}]', 
                'write-code', 200, 'sql']
            );
        }
    }
}

$db = Database::getInstance();
?>
