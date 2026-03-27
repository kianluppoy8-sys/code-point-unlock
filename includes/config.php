<?php
/**
 * Code Point Unlock - Configuration
 * Optimized for Hostinger (MySQL/PHP/CSS/HTML/JS)
 */

session_start();

// 1. Site Identity
define('SITE_NAME', 'The Code Point Unlock');

// 2. Load Environment Credentials (.env.php)
$envPath = __DIR__ . '/.env.php';
if (file_exists($envPath)) {
    require_once $envPath;
}

// 3. Database Defaults
if (!defined('DB_TYPE')) define('DB_TYPE', 'mysql');
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
// These were verified by test_db.php
if (!defined('DB_NAME')) define('DB_NAME', 'code_point_unlock');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', ''); 
if (!defined('DB_PORT')) define('DB_PORT', '3306');

// 4. Smart Project URL Detection
if (!defined('SITE_URL')) {
    // Enhanced protocol detection
    $isHttps = (
        (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1)) ||
        (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
        (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') ||
        (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
    );
    
    // On Hostinger and other remote hosts, we almost always want https
    if (!$isHttps && isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== 'localhost' && $_SERVER['HTTP_HOST'] !== '127.0.0.1') {
        // Optional: you could force it here, but let's just detect it for now
        // To be safe, if it's not localhost, default to https if the server seems to support it
    }

    $protocol = $isHttps ? 'https' : 'http';
    
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Bulletproof Project Root Detection
    // 1. Get current script paths
    $script_filename = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']);
    $script_name = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
    
    // 2. Identify the web root's physical path on the server
    // By removing the web path from the physical path of the current script
    $physical_root = str_replace($script_name, '', $script_filename);
    
    // 3. Get the physical path of this config directory (includes/)
    $current_physical_dir = str_replace('\\', '/', __DIR__);
    
    // 4. Calculate the web path to this directory
    $web_path_to_includes = str_replace($physical_root, '', $current_physical_dir);
    
    // 5. Project root is one level up from includes/
    $projectRoot = str_replace('/includes', '', $web_path_to_includes);
    
    // Final cleanup
    $projectRoot = rtrim($projectRoot, '/');
    if (!empty($projectRoot) && $projectRoot[0] !== '/') {
        $projectRoot = '/' . $projectRoot;
    }
    
    define('SITE_URL', $protocol . '://' . $host . $projectRoot);
}

// 5. Error Reporting
if (DB_HOST === 'localhost') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// 6. Project Helper Functions
function redirect($page) {
    if (strpos($page, 'http') !== 0) {
        // If relative to project root, prepend SITE_URL
        $baseUrl = rtrim(SITE_URL, '/');
        $page = $baseUrl . '/' . ltrim($page, '/');
    }
    header("Location: $page");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

function sanitize($data) {
    if ($data === null) return '';
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8', false);
}

function flash($name, $message = '', $class = 'success') {
    if (!empty($message)) {
        $_SESSION['flash'][$name] = ['message' => $message, 'class' => $class];
    } elseif (isset($_SESSION['flash'][$name])) {
        $flash = $_SESSION['flash'][$name];
        unset($_SESSION['flash'][$name]);
        return "<div class='alert alert-{$flash['class']}'>{$flash['message']}</div>";
    }
    return '';
}
?>
