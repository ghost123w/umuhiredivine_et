<?php
// includes/functions.php

/**
 * Checks if an admin is logged in, redirects if not.
 */
function check_login() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['admin_id'])) {
        header("Location: login.php");
        exit();
    }
}

/**
 * Sanitizes input data before storing in DB.
 * Removed htmlspecialchars to avoid double encoding.
 */
function sanitize($data) {
    return trim($data);
}

/**
 * Generates a CSRF token.
 */
function generate_csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifies a CSRF token.
 */
function verify_csrf_token($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
