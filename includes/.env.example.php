<?php
/**
 * Production Configuration (Hostinger)
 * 
 * 1. Create a copy of this file named '.env.php' in the 'includes/' directory
 * 2. Update the credentials below with your Hostinger MySQL details
 * 3. Set your SITE_URL (e.g., https://yourdomain.com)
 * 
 * IMPORTANT: .env.php is ignored by git for security.
 */

define('DB_TYPE', 'mysql');
define('DB_HOST', 'localhost'); // Usually 'localhost' on Hostinger
define('DB_NAME', 'u123456789_database'); // Replace with your DB name
define('DB_USER', 'u123456789_username'); // Replace with your DB user
define('DB_PASS', 'your_secure_password'); // Replace with your DB password
define('DB_PORT', '3306');

// Set your production URL (important for assets and links)
define('SITE_URL', 'https://yourdomain.com');

// External APIs
define('JDOODLE_CLIENT_ID', 'YOUR_JDOODLE_CLIENT_ID'); // Get from jdoodle.com/compiler-api
define('JDOODLE_CLIENT_SECRET', 'YOUR_JDOODLE_CLIENT_SECRET');

// Error reporting: 0 for production to hide sensitive info
error_reporting(0);
ini_set('display_errors', 0);
