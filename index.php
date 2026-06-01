<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/contact-logic.php';

// Fetch all products for the menu section
$stmt = $pdo->query("SELECT * FROM products ORDER BY category, id ASC");
$menu_items = $stmt->fetchAll();

// Fetch contact phone
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'contact_phone'");
$stmt->execute();
$contact_phone = $stmt->fetchColumn() ?: '+234 000 000 0000';

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
        <section class="menu-section-landing" id="menu">
            <header class="menu-header">
                <h2 class="luxury-heading"><span class="white-text">ME</span><span class="highlight">NU</span></h2>
                <p class="menu-subtitle">A SYMPHONY OF AUTHENTIC FLAVORS</p>
                <div class="contact-strip">
                    <span class="phone-label">ORDER VIA WHATSAPP / CALL:</span>
                    <a href="tel:<?php echo htmlspecialchars($contact_phone); ?>" class="phone-link"><?php echo htmlspecialchars($contact_phone); ?></a>
                </div>
            </header>

            <div class="menu-grid">
                <?php if (empty($menu_items)): ?>
                    <div class="empty-menu">
                        <p>Our culinary masters are currently preparing new masterpieces. Please check back soon.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($menu_items as $item):
                        $tags = !empty($item['tags']) ? explode(',', $item['tags']) : [];
                    ?>
                        <div class="menu-item-card">
                            <div class="menu-item-image">
                                <img src="<?php echo htmlspecialchars($item['image_path'] ?: 'images/category-bg.png'); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            </div>
                            <div class="menu-item-details">
                                <div class="tags-row">
                                    <?php foreach ($tags as $tag): ?>
                                        <span class="menu-tag"><?php echo htmlspecialchars(trim(strtoupper($tag))); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <div class="name-price-row">
                                    <h3 class="item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <div class="price-leader"></div>
                                    <span class="item-price"><?php echo htmlspecialchars($item['price']); ?></span>
                                </div>
                                <p class="item-description"><?php echo htmlspecialchars($item['description']); ?></p>
                                <div class="item-footer">
                                    <button class="order-icon-btn" onclick="openContactModal('Order: <?php echo addslashes($item['name']); ?>')">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Best Sellers Section -->
        <?php if (!empty($bestSellers)): ?>
        <section class="best-sellers-section">
            <h2 class="section-title-luxury" style="text-align: center; margin-bottom: 40px; font-family: 'Cinzel', serif; letter-spacing: 4px;"><?php echo htmlspecialchars($best_sellers_title); ?></h2>
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
                                <p class="product-price"><?php echo htmlspecialchars($product['price']); ?></p>
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
