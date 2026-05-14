<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

require_once 'includes/contact-logic.php';

$stmt = $pdo->query("SELECT * FROM content WHERE nav_item_id IS NULL ORDER BY id ASC");
$sections = $stmt->fetchAll();

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
            <h2 class="hero-title"><?php echo strtoupper(SITE_NAME); ?></h2>
        </div>
    </section>

    <main class="layout-main">
        <?php if (!empty($selling_points_title)): ?>
            <h2 class="section-title"><?php echo htmlspecialchars($selling_points_title); ?></h2>
        <?php endif; ?>

        <div class="prism-grid">
            <?php foreach ($sections as $index => $s): ?>
                <div class="bento-card <?php
                    if ($index == 0) echo 'grid-large';
                    elseif ($index == 1) echo 'grid-tall';
                    else echo 'grid-small';
                ?>">
                    <?php if ($s['image_path']): ?>
                        <div class="card-bg-image" style="background-image: url('<?php echo htmlspecialchars($s['image_path']); ?>');"></div>
                    <?php endif; ?>
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($s['section_title']); ?></h3>
                        <p><?php echo htmlspecialchars($s['description']); ?></p>
                    </div>
                </div>
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
