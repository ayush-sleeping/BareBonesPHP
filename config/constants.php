<?php
// filepath: /Applications/XAMPP/xamppfiles/htdocs/BareBonesPHP/config/constants.php

// Site Configuration
define('SITE_NAME', 'BareBonesPHP');
define('SITE_URL', 'http://localhost/BareBonesPHP/public');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'barebonesphp');
define('DB_USER', 'root');
define('DB_PASS', '');

// Security
define('HASH_ALGO', PASSWORD_DEFAULT);
define('SESSION_LIFETIME', 3600); // 1 hour
define('PASSWORD_MIN_LENGTH', 6);

// Paths
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('SRC_PATH', ROOT_PATH . '/src');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('STORAGE_PATH', ROOT_PATH . '/storage');

// Timezone - Set to India/Kolkata (IST)
date_default_timezone_set('Asia/Kolkata');
