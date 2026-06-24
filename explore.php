<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

require_once 'includes/contact-logic.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore | <?php echo htmlspecialchars(SITE_NAME); ?></title>
    <link rel="icon" href="images/favicon.jpg">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@900&family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body class="explore-page">
    <?php include 'includes/header.php'; ?>

    <div class="explore-background-container"></div>

    <section class="explore-section" id="section-1"></section>

    <section class="explore-section" id="section-2"></section>

    <section class="explore-section" id="section-3">
        <div class="video-container">
            <div class="youtube-player" data-video-id="WTI-TNm6bjI"></div>
        </div>
    </section>

    <script src="https://www.youtube.com/iframe_api"></script>
    <script src="js/script.js"></script>
</body>
</html>
