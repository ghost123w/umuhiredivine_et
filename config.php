<?php
// config.php

// Database type: 'sqlite' or 'mysql'
define('DB_TYPE', 'sqlite');

// SQLite settings
define('DB_PATH', __DIR__ . '/data/database.db');

// MySQL settings (Update for cPanel deployment)
define('DB_HOST', 'localhost');
define('DB_NAME', 'my_landing_page');
define('DB_USER', 'root');
define('DB_PASS', '');

define('SITE_NAME', 'My Selling Point Site');
?>
