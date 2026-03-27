-- Code-Based Point-Unlock Game Database Schema
-- Run this in phpMyAdmin or MySQL command line

CREATE DATABASE IF NOT EXISTS code_point_unlock;
USE code_point_unlock;

-- Users table
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

-- Levels table
CREATE TABLE IF NOT EXISTS levels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    required_points INT DEFAULT 0,
    display_order INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Questions table
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

-- User progress - tracks completed questions
CREATE TABLE IF NOT EXISTS user_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    question_id INT NOT NULL,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_question (user_id, question_id)
);

-- Submissions table - tracks all attempts
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

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password, is_admin, total_points) VALUES 
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 0);

-- Insert sample levels
INSERT INTO levels (name, required_points, display_order) VALUES 
('Novice Coder', 0, 1),
('medium', 300, 2),
('master', 1000, 3);

-- Insert sample questions
INSERT INTO questions (level_id, title, description, code_snippet, expected_answer, expected_output, type, points, language) VALUES 
(1, 'SQL Select All Users', 'Select all columns from the "users" table.', 
'-- Write your query here', 
'SELECT * FROM users;', 
'[{"id":"1","username":"admin","email":"admin@example.com","is_admin":"1"}]',
'write-code', 200, 'sql'),

(1, 'Java Hello World', 'Complete the main method to print "Hello World".', 
'public class Main {\n  public static void main(String[] args) {\n    // Print here\n  }\n}', 
'public class Main {\n  public static void main(String[] args) {\n    System.out.println("Hello World");\n  }\n}', 
'Hello World',
'write-code', 300, 'java'),

(2, 'Java Basic Addition', 'Print the result of 5 + 10.',
'public class Main {\n  public static void main(String[] args) {\n    // Calculate and print\n  }\n}',
'public class Main {\n  public static void main(String[] args) {\n    System.out.println(5 + 10);\n  }\n}',
'15',
'write-code', 400, 'java');
