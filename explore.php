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
    <title>Explore | <?php echo htmlspecialchars(SITE_NAME); ?></title>
    <link rel="icon" href="images/favicon.jpg">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@900&family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body class="explore-page">
    <?php include 'includes/header.php'; ?>

    <div class="explore-bg-wrapper">
        <div class="explore-background-container"></div>
    </div>

    <section class="explore-section" id="section-1"></section>

    <section class="explore-section" id="section-2">
        <div class="video-container">
            <div class="youtube-player" data-video-id="WTI-TNm6bjI"></div>
        </div>
    </section>

    <section class="explore-section" id="section-3">
        <footer class="explore-footer">
            <div class="footer-list">
                <?php
                $stmt = $pdo->query("SELECT * FROM footer_items WHERE is_active = 1 ORDER BY sort_order ASC");
                $footers = $stmt->fetchAll();
                foreach ($footers as $f):
                ?>
                    <a href="<?php echo htmlspecialchars($f['link_url']); ?>" class="footer-link"><?php echo htmlspecialchars($f['label']); ?></a>
                <?php endforeach; ?>
            </div>
            <div class="footer-copyright">
                &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?>. ALL RIGHTS RESERVED.
            </div>
        </footer>
    </section>

    <?php include 'includes/contact-modal.php'; ?>

    <script src="https://www.youtube.com/iframe_api"></script>
    <script src="js/script.js"></script>
</body>
</html>
