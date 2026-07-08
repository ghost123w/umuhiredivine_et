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
    <title>Menu | <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="menu-page">
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/hero.php'; ?>
    <main class="layout-main" style="margin-top: 80vh; min-height: 100vh; padding: 120px 60px;">
        <h1 class="luxury-heading-v3">OUR MENU</h1>
        <div class="gold-line"></div>
        <p class="explore-description">Exquisite flavors crafted for the discerning palate.</p>
    </main>
    <?php include 'includes/contact-modal.php'; ?>
</body>
</html>
