<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/contact-logic.php';
$is_explore_page = true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore | <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@900&family=Inter:wght@400;700;900&family=Patrick+Hand&display=swap" rel="stylesheet">
</head>
<body class="explore-page">
<?php include 'includes/header.php'; ?>

<main class="layout-main explore-layout">
    <section id="section-2" class="explore-section">
        <div class="video-container">
            <!-- YouTube Video integration with auto-play scroll logic in script.js -->
            <div class="youtube-player" data-video-id="WTI-TNm6bjI"></div>
        </div>
    </section>
</main>

<!-- YouTube IFrame API and Unified Script -->
<script src="https://www.youtube.com/iframe_api"></script>
<script src="js/script.js"></script>

</body>
</html>
