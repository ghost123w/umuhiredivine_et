<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/contact-logic.php';

// Fetch About Us content
$stmt = $pdo->prepare("SELECT * FROM content WHERE section_title = 'ABOUT US' OR nav_item_id = (SELECT id FROM navigation_items WHERE label = 'ABOUT US' LIMIT 1) LIMIT 1");
$stmt->execute();
$aboutContent = $stmt->fetch();

$display_title = ($aboutContent && isset($aboutContent['section_title'])) ? $aboutContent['section_title'] : 'ABOUT US';
$display_desc = ($aboutContent && isset($aboutContent['description'])) ? $aboutContent['description'] : 'Our journey from heritage to modern luxury.';
$display_img = ($aboutContent && isset($aboutContent['image_path'])) ? $aboutContent['image_path'] : 'images/leadership.jpg';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | <?php echo SITE_NAME; ?></title>
    <link rel="icon" href="images/favicon.jpg">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@900&family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/hero.php'; ?>

    <main class="layout-main">
        <div class="about-container">
            <div class="about-grid">
                <div class="about-image-wrapper">
                    <img src="<?php echo htmlspecialchars($display_img); ?>" alt="Our Legacy" class="about-featured-image">
                    <div class="image-glow"></div>
                </div>
                <div class="about-text-content">
                    <h2 class="luxury-heading"><span class="white-text">ABOUT</span><span class="highlight"> US</span></h2>
                    <div class="about-description">
                        <?php echo nl2br(htmlspecialchars($display_desc)); ?>
                    </div>
                    <div class="about-mission">
                        <h3>OUR MISSION</h3>
                        <p>To redefine luxury through impeccable service, artistic presentation, and a commitment to unforgettable experiences.</p>
                    </div>
                    <div class="about-values">
                        <div class="value-item">
                            <span class="value-number">01</span>
                            <h4>EXCELLENCE</h4>
                        </div>
                        <div class="value-item">
                            <span class="value-number">02</span>
                            <h4>HERITAGE</h4>
                        </div>
                        <div class="value-item">
                            <span class="value-number">03</span>
                            <h4>INNOVATION</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/contact-modal.php'; ?>

    <footer class="aura-footer">
        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?> &mdash; LUXURY EXPERIENCE
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
