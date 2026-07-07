<?php
session_start();
include 'includes/db.php';
include 'includes/functions.php';
include 'includes/contact-logic.php';

$is_explore_page = true;
$site_name = SITE_NAME;
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

    <section class="explore-section explore-hero-bg"></section>

    <section class="explore-section video-section">
        <div id="youtube-player"></div>
    </section>

    <?php include 'includes/contact-modal.php'; ?>

    <footer class="aura-footer">
        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?> &mdash; LUXURY EXPERIENCE
    </footer>

    <script src="https://www.youtube.com/iframe_api"></script>
    <script src="js/script.js"></script>
</body>
</html>
