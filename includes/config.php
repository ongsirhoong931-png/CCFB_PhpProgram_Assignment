<?php
// ============================================
// CONFIGURATION FILE
// ============================================

// Database configuration for movie_db
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'movie_db');

// Application configuration
define('SITE_NAME', 'Cinema Booking Online');
define('SITE_URL', 'http://localhost/cinema-booking/');
define('ADMIN_EMAIL', 'admin@cinema.com');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

// Error reporting (turn off in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Kuala_Lumpur');

// Upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('UPLOAD_DIR', __DIR__ . '/../assets/images/');
?>