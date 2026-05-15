<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

require_once 'includes/contact-logic.php';

$stmt = $pdo->query("SELECT n.*, c.image_path
                    FROM navigation_items n
                    LEFT JOIN content c ON n.label = c.title
                    WHERE n.nav_type = 'main' AND n.is_active = 1 AND n.label NOT IN ('HOME', 'GET IN TOUCH')
                    ORDER BY n.sort_order ASC");
$categories = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'selling_points_title'");
$stmt->execute();
$selling_points_title = $stmt->fetchColumn() ?: '';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'cta_text'");
$stmt->execute();
$cta_text = $stmt->fetchColumn() ?: 'Get Started';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'cta_link'");
$stmt->execute();
$cta_link = $stmt->fetchColumn() ?: '#';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> | Home</title>
    <link rel="icon" href="images/favicon.jpg">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@900&family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="hero-section">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="hero-brand-stack">
                <h2 class="hero-title-bg"><?php echo strtoupper(SITE_NAME); ?></h2>
                <h2 class="hero-title-fg"><?php echo strtoupper(SITE_NAME); ?></h2>
            </div>
            <div class="hero-slogan-box">
                <p class="hero-slogan">BY PODs</p>
            </div>
        </div>
    </section>

    <main class="layout-main">
        <div class="prism-grid">
            <?php foreach ($categories as $index => $cat):
                $bg_image = !empty($cat['image_path']) ? $cat['image_path'] : 'images/category-bg.png';
            ?>
                <a href="<?php echo htmlspecialchars($cat['link_url']); ?>" class="bento-card">
                    <div class="card-bg-image" style="background-image: url('<?php echo htmlspecialchars($bg_image); ?>');"></div>
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($cat['label']); ?></h3>
                        <p>EXPLORE CATEGORY</p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </main>

    <?php include 'includes/contact-modal.php'; ?>

    <footer class="aura-footer">
        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?> &mdash; LUXURY EXPERIENCE
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
