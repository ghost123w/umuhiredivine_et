<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

require_once 'includes/contact-logic.php';

// Optimized query to fetch categories and their preview images in one go
$query = "
    SELECT n.*,
    COALESCE(c.image_path,
        CASE
            WHEN n.label = 'MENU' THEN 'images/menu-bg.jpg'
            WHEN n.label = 'BLOG' THEN 'images/blog-bg.jpg'
            WHEN n.label = 'GALLERY' THEN 'images/gallery-bg.jpg'
            WHEN n.label = 'EXPLORE' THEN 'images/explore-bg.jpg'
            ELSE 'images/category-bg.png'
        END
    ) as display_image
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
    <?php include 'includes/hero.php'; ?>

    <main class="layout-main">
        <div class="categories-header">
            <h2 class="section-title">OUR COLLECTIONS</h2>
        </div>

        <div class="prism-grid">
            <?php foreach ($categories as $index => $cat):
                $img = $cat['display_image'];
                $card_class = 'bento-card';
                // Pattern: Large, Medium, Medium, Small, Small
                $pos = $index % 5;
                if ($pos == 0) $card_class .= ' grid-large';
                elseif ($pos == 1 || $pos == 2) $card_class .= ' grid-medium';
                else $card_class .= ' grid-small';
            ?>
                <a href="<?php echo htmlspecialchars($cat['link_url']); ?>" class="bento-card <?php echo $card_class; ?>">
                    <div class="card-bg-image" style="background-image: url('<?php echo htmlspecialchars($img); ?>');"></div>
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($cat['label']); ?></h3>
                        <p>EXPLORE <?php echo htmlspecialchars($cat['label']); ?></p>
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
