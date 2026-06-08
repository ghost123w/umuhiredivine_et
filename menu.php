<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Featured image for the reveal section
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'menu_featured_image'");
$stmt->execute();
$featured_img = $stmt->fetchColumn() ?: 'images/menu_featured.jpg';

$hero_bg = $featured_img;
$hero_title = ""; // Use custom title scrolls

// Fetch products for the menu items
$stmt = $pdo->query("SELECT * FROM products ORDER BY category");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu | <?php echo htmlspecialchars(SITE_NAME); ?></title>
    <link rel="icon" href="images/favicon.jpg">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="menu-page">
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/hero.php'; ?>

    <main class="layout-main">
        <!-- Scroll 1: Luxury Branding "MENU" -->
        <section class="solid-scroll-section">
            <div class="hero-brand-stack">
                <div class="hero-title-bg">MENU</div>
                <div class="hero-title-fg">MENU</div>
            </div>
        </section>

        <!-- Scroll 2: Secondary Header / Interaction Hint -->
        <section class="solid-scroll-section">
            <h2 class="luxury-heading" style="text-align: center;"><span class="white-text">OUR</span> <span class="highlight">COLLECTION</span></h2>
            <div class="discover-hint">SCROLL TO DISCOVER</div>
        </section>

        <!-- Scroll 3: Reveal fixed featured image in full display -->
        <section class="transparent-reveal-section"></section>

        <!-- Scroll 4: Detailed Menu Items -->
        <section class="menu-items-section">
            <?php
            $current_cat = '';
            foreach ($products as $product):
                if ($current_cat !== $product['category']):
                    if ($current_cat !== '') echo '</div></div>';
                    $current_cat = $product['category'];
            ?>
                <div class="category-group">
                    <h3 class="category-title-luxury"><?php echo htmlspecialchars($current_cat); ?></h3>
                    <div class="items-grid">
            <?php endif; ?>

                <div class="menu-item-card-v2">
                    <img src="<?php echo htmlspecialchars($product['image_path'] ?: 'images/category-bg.png'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="menu-item-img-v2">
                    <div class="menu-item-content-v2">
                        <?php
                        $tags = array_filter(array_map('trim', explode(',', $product['tags'] ?? '')));
                        if (!empty($tags)): ?>
                            <div class="menu-item-tags-v2">
                                <?php foreach ($tags as $tag): ?>
                                    <span class="menu-tag-v2"><?php echo htmlspecialchars($tag); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="menu-item-header-v2">
                            <h3 class="menu-item-name-v2"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <span class="menu-item-price-v2"><?php echo htmlspecialchars($product['price']); ?></span>
                        </div>

                        <p class="menu-item-desc-v2"><?php echo htmlspecialchars($product['description']); ?></p>

                        <div class="menu-item-actions-v2">
                            <button class="cart-btn-v2" onclick="openContactModal('Order: <?php echo addslashes($product['name']); ?>')">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if ($current_cat !== '') echo '</div></div>'; ?>
        </section>
    </main>

    <?php include 'includes/contact-modal.php'; ?>
    <footer class="aura-footer">
        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?> &mdash; CULINARY EXCELLENCE
    </footer>
    <script src="js/script.js"></script>
</body>
</html>
