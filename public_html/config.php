<?php
// public_html/config.php

// Database type: 'sqlite' or 'mysql'
define('DB_TYPE', 'sqlite');

// SQLite settings (Absolute path to data outside public_html)
define('DB_PATH', __DIR__ . '/../data/database.db');

// MySQL settings (Update for cPanel deployment)
define('DB_HOST', 'localhost');
define('DB_NAME', 'my_landing_page');
define('DB_USER', 'root');
define('DB_PASS', '');

define('SITE_NAME', 'My Selling Point Site');
?>
