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
<body class="landing-page">
    <?php include 'includes/header.php'; ?>

    <main class="layout-main">
        <div class="section-header reveal-item" style="margin-bottom: 80px;">
            <h2 class="section-title" style="text-align: left; font-size: 4rem;">UNIVERSE</h2>
            <p style="color: #999; text-transform: uppercase; letter-spacing: 10px; font-size: 0.7rem;">Curated Collections</p>
        </div>

        <div class="prism-grid">
            <?php foreach ($categories as $index => $cat):
                // Try to find an image from content for this category
                $imgStmt = $pdo->prepare("SELECT image_path FROM content WHERE nav_item_id = ? AND image_path IS NOT NULL LIMIT 1");
                $imgStmt->execute([$cat['id']]);
                $bg_image = $imgStmt->fetchColumn();
            ?>
                <a href="<?php echo htmlspecialchars($cat['link_url']); ?>" class="bento-card grid-small reveal-item">
                    <?php if ($bg_image): ?>
                        <div class="card-bg-image" style="background-image: url('<?php echo htmlspecialchars($bg_image); ?>'); opacity: 0.1;"></div>
                    <?php endif; ?>
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($cat['label']); ?></h3>
                        <p>Explore Frequency</p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="aura-footer" style="margin-top: 150px; text-align: left; padding-left: 100px;">
        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?> &mdash; LUXURY EXPERIENCE
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
