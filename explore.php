<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

require_once 'includes/contact-logic.php';

// Find the Explore Nav Item ID
$stmt = $pdo->prepare("SELECT id FROM navigation_items WHERE link_url LIKE '%explore.php%' OR label = 'Explore' LIMIT 1");
$stmt->execute();
$nav_id = $stmt->fetchColumn();

// Fetch content assigned to Explore
$fixtures = [];
if ($nav_id) {
    $stmt = $pdo->prepare("SELECT * FROM content WHERE nav_item_id = ? ORDER BY id ASC");
    $stmt->execute([$nav_id]);
    $fixtures = $stmt->fetchAll();
}
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
    <?php include 'includes/hero.php'; ?>

    <main class="layout-main">
        <div class="prism-grid">
            <?php if (!empty($fixtures)): ?>
                <?php foreach ($fixtures as $index => $f):
                    $cardClass = ($index % 3 == 0) ? 'grid-large' : (($index % 3 == 1) ? 'grid-medium' : 'grid-tall');
                    $bg_image = !empty($f['image_path']) ? $f['image_path'] : 'images/category-bg.png';
                ?>
                    <div class="bento-card <?php echo $cardClass; ?>">
                        <div class="card-bg-image" style="background-image: url('<?php echo htmlspecialchars($bg_image); ?>');"></div>
                        <div class="card-content">
                            <h3><?php echo htmlspecialchars($f['section_title']); ?></h3>
                            <p><?php echo htmlspecialchars($f['description']); ?></p>
                            <button class="card-btn" style="background: none; border: 1px solid var(--primary-color); color: #fff; padding: 10px 20px; border-radius: 4px; cursor: pointer; margin-top: 20px;" onclick="openContactModal('<?php echo addslashes($f['section_title']); ?>')">ENQUIRE</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <footer class="aura-footer">
        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?> &mdash; LUXURY EXPLORATION
    </footer>

    <?php include 'includes/contact-modal.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
