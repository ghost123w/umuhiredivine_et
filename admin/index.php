<?php
session_start();
require_once '../includes/functions.php';

// Redirect to dashboard, which will handle auth check and redirect to login if needed
header("Location: dashboard.php");
exit();
?>
