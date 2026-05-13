<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$stmt = $pdo->query("SELECT * FROM navigation_items WHERE nav_type = 'main' AND is_active = 1 AND label NOT IN ('HOME', 'GET IN TOUCH') ORDER BY sort_order ASC");
$categories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories | <?php echo SITE_NAME; ?></title>
    <link rel="icon" href="images/favicon.jpg">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@900&family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body class="aura-body">
    <div class="stroll-bg-container">
        <div class="stroll-bg-image" style="background-image: url('images/aura-bg.jpg');"></div>
        <div class="mesh-bg"></div>
    </div>

    <?php include 'includes/header.php'; ?>

    <main class="layout-main" style="padding-top: 200px;">
        <div class="section-header reveal-item" style="text-align: center; margin-bottom: 80px;">
            <h2 class="section-title">OUR UNIVERSE</h2>
            <p style="color: var(--primary-color); text-transform: uppercase; letter-spacing: 10px; font-size: 0.7rem;">Curated Collections</p>
        </div>

        <div class="prism-grid">
            <?php foreach ($categories as $index => $cat):
                $grid_class = ($index % 3 == 0) ? 'grid-large' : (($index % 3 == 1) ? 'grid-tall' : 'grid-small');
                // Try to find an image from content for this category
                $imgStmt = $pdo->prepare("SELECT image_path FROM content WHERE nav_item_id = ? AND image_path IS NOT NULL LIMIT 1");
                $imgStmt->execute([$cat['id']]);
                $bg_image = $imgStmt->fetchColumn() ?: 'images/aura-bg.jpg';
            ?>
                <a href="<?php echo htmlspecialchars($cat['link_url']); ?>" class="bento-card <?php echo $grid_class; ?> reveal-item">
                    <div class="card-bg-image" style="background-image: url('<?php echo htmlspecialchars($bg_image); ?>');"></div>
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($cat['label']); ?></h3>
                        <p>Explore Category</p>
                    </div>
                    <div class="aura-pulse-element"></div>
                </a>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="aura-footer" style="margin-top: 150px; background: rgba(0,0,0,0.8); backdrop-filter: blur(20px); border-top: 1px solid rgba(255,255,255,0.05);">
        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?> &mdash; LUXURY EXPERIENCE
    </footer>

    <script src="js/script.js"></script>
    <script>
        // Scroll Reveal Initialization
        const observerOptions = {
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.reveal-item').forEach(item => {
            observer.observe(item);
        });
    </script>
</body>
</html>
