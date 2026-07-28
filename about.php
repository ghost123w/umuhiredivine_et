<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us | <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main class="layout-main" style="margin-top: 0; min-height: 100vh; padding: 120px 60px;">
        <h1 class="luxury-heading-v3">ABOUT US</h1>
        <div class="gold-line"></div>
        <p class="explore-description">Dedicated to providing the finest luxury experiences.</p>
    </main>
    <?php include 'includes/contact-modal.php'; ?>
</body>
</html>
