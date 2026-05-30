<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

require_once 'includes/contact-logic.php';

$stmt = $pdo->query("SELECT n.*, c.image_path
                    FROM navigation_items n
                    LEFT JOIN (
                        SELECT nav_item_id, MIN(id) as min_id
                        FROM content
                        WHERE image_path IS NOT NULL
                        GROUP BY nav_item_id
                    ) c_min ON n.id = c_min.nav_item_id
                    LEFT JOIN content c ON c.id = c_min.min_id
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

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'best_sellers_title'");
$stmt->execute();
$best_sellers_title = $stmt->fetchColumn() ?: 'BEST SELLERS';

// Fetch Best Sellers
$bestSellers = $pdo->query("SELECT * FROM products WHERE is_best_seller = 1 ORDER BY id ASC")->fetchAll();
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
    <?php include 'includes/hero.php'; ?>

    <main class="layout-main">
        <!-- Categories Section -->
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

        <!-- Best Sellers Section -->
        <?php if (!empty($bestSellers)): ?>
        <section class="best-sellers-section">
            <h2 class="section-title"><?php echo htmlspecialchars($best_sellers_title); ?></h2>
            <div class="best-sellers-carousel-wrapper">
                <div class="best-sellers-carousel">
                    <?php foreach ($bestSellers as $product): ?>
                        <div class="product-card">
                            <div class="product-image-container">
                                <img src="<?php echo htmlspecialchars($product['image_path']); ?>"
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     onerror="this.src='images/category-bg.png'">
                            </div>
                            <div class="product-info">
                                <span class="product-category"><?php echo htmlspecialchars($product['category']); ?></span>
                                <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="product-price">₦<?php echo htmlspecialchars($product['price']); ?></p>
                                <div class="product-actions">
                                    <a href="#contact-modal" class="order-btn" onclick="openContactModal('Order: <?php echo $product['name']; ?>')">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                                        Order
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="carousel-dots">
                <?php foreach ($bestSellers as $index => $product): ?>
                    <span class="dot <?php echo $index === 0 ? 'active' : ''; ?>"></span>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </main>

    <?php include 'includes/contact-modal.php'; ?>

    <footer class="aura-footer">
        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?> &mdash; LUXURY EXPERIENCE
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
