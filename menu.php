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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu | <?php echo htmlspecialchars(SITE_NAME); ?></title>
    <link rel="icon" href="images/favicon.jpg">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="menu-page">
    <?php include 'includes/header.php'; ?>

    <main class="layout-main" style="margin-top: 0;">
        <div style="padding: 200px 60px; text-align: center; background: #000; min-height: 100vh;">
            <h1 style="font-family: 'Cinzel', serif; font-size: 3rem; color: #fff;">MENU</h1>
            <p style="color: #666; letter-spacing: 2px; margin-top: 20px;">PREPARING THE COLLECTION</p>
        </div>
    </main>

    <?php include 'includes/contact-modal.php'; ?>
    <footer class="aura-footer" style="background: #000;">
        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?> &mdash; CULINARY EXCELLENCE
    </footer>
    <script src="js/script.js"></script>
</body>
</html>
