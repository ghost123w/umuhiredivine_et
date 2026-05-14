<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

require_once 'includes/contact-logic.php';

// Optimized query to fetch categories and their preview images in one go
$query = "
    SELECT n.*, c.image_path
    FROM navigation_items n
    LEFT JOIN (
        SELECT nav_item_id, MIN(id) as min_id
        FROM content
        WHERE image_path IS NOT NULL
        GROUP BY nav_item_id
    ) c_min ON n.id = c_min.nav_item_id
    LEFT JOIN content c ON c.id = c_min.min_id
    WHERE n.nav_type = 'main'
      AND n.is_active = 1
      AND n.label NOT IN ('HOME', 'GET IN TOUCH')
    ORDER BY n.sort_order ASC
";
$stmt = $pdo->query($query);
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
<body>
    <?php include 'includes/header.php'; ?>

    <main class="layout-main">
        <div class="categories-header">
            <h2 class="section-title">OUR CATEGORIES</h2>
        </div>

        <div class="prism-grid">
            <?php foreach ($categories as $index => $cat): ?>
                <a href="<?php echo htmlspecialchars($cat['link_url']); ?>" class="bento-card">
                    <div class="card-bg-image" style="background-image: url('images/category-bg.png');"></div>
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($cat['label']); ?></h3>
                        <p>EXPLORE</p>
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
