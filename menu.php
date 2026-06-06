<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Fetch the featured image from settings
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'menu_featured_image'");
$stmt->execute();
$menu_featured_image = $stmt->fetchColumn() ?: 'images/menu_featured.jpg';
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
        <!-- Scroll 1: Hero (Cleaned up, no text words as requested) -->
        <section class="menu-hero-scroll">
            <div class="menu-hero-content">
                <p class="scrolly-hint">SCROLL TO DISCOVER</p>
            </div>
        </section>

        <!-- Scroll 2: Featured Image (Full Display) -->
        <section class="menu-featured-scroll" style="background-image: url('<?php echo htmlspecialchars($menu_featured_image); ?>');">
            <div class="scroll-overlay">
                <div class="frame-border"></div>
            </div>
        </section>
    </main>

    <?php include 'includes/contact-modal.php'; ?>
    <footer class="aura-footer">
        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?> &mdash; CULINARY EXCELLENCE
    </footer>
    <script src="js/script.js"></script>
</body>
</html>
