<?php
session_start();
include 'includes/db.php';
include 'includes/functions.php';
include 'includes/contact-logic.php';

$is_explore_page = true;
$site_name = SITE_NAME;

// Fetch navigation items specifically for explore (if different)
$navItems = $pdo->query("SELECT * FROM navigation_items WHERE nav_type = 'main' AND is_active = 1 ORDER BY sort_order ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore | <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Inter:wght@300;400;700&family=Patrick+Hand&display=swap" rel="stylesheet">
</head>
<body class="explore-page">

    <?php include 'includes/header.php'; ?>

    <section class="explore-section explore-hero-bg">
        <div class="explore-quote-container">
            <h1 class="explore-quote">"True luxury is found in the moments we explore the extraordinary."</h1>
        </div>
    </section>

    <?php include 'includes/contact-modal.php'; ?>

    <footer class="aura-footer">
        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?> &mdash; LUXURY EXPERIENCE
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
