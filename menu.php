<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/contact-logic.php';

// Fetch all products for the menu
$stmt = $pdo->query("SELECT * FROM products ORDER BY category, id ASC");
$menu_items = $stmt->fetchAll();

// Group products by category
$categories = [];
foreach ($menu_items as $item) {
    $categories[$item['category']][] = $item;
}

// Fetch contact phone
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'contact_phone'");
$stmt->execute();
$contact_phone = $stmt->fetchColumn() ?: '+234 000 000 0000';

// Fetch Menu Hero Image
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'menu_hero_image'");
$stmt->execute();
$menu_hero_image = $stmt->fetchColumn() ?: 'images/menu_page.png';

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

    <!-- First Scroll: Standard Hero -->
    <?php
    $hero_title = "MENU";
    $hero_subtitle = "CULINARY EXCELLENCE";
    $hero_bg = 'images/brand-hero.jpg';
    include 'includes/hero.php';
    ?>

    <main class="layout-main" style="padding-top: 0; margin-top: 100vh;">
        <!-- Second Scroll: Full-display Custom Menu Image -->
        <section class="menu-featured-full" style="background-image: url('<?php echo htmlspecialchars($menu_hero_image); ?>'); height: 100vh; background-attachment: fixed; background-size: cover; background-position: center; position: relative;">
            <div class="featured-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.2);"></div>
        </section>

        <!-- Third Section: Menu Grid -->
        <section class="menu-content-wrapper" style="background: #000; position: relative; z-index: 10;">
            <div class="menu-header">
                <p class="menu-subtitle">EXPERIENCE THE ART OF TASTE</p>
                <div class="contact-strip">
                    <span class="phone-label">RESERVATIONS & ORDERS</span>
                    <a href="tel:<?php echo htmlspecialchars($contact_phone); ?>" class="phone-link"><?php echo htmlspecialchars($contact_phone); ?></a>
                </div>
            </div>

            <?php foreach ($categories as $category_name => $items): ?>
                <div class="menu-category-section" style="margin-bottom: 80px;">
                    <h3 class="luxury-heading" style="margin-bottom: 50px;">
                        <span class="white-text"><?php echo htmlspecialchars(strtoupper($category_name)); ?></span>
                    </h3>

                    <div class="menu-grid">
                        <?php foreach ($items as $item): ?>
                            <div class="menu-item-card">
                                <?php if ($item['image_path']): ?>
                                    <div class="menu-item-image">
                                        <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    </div>
                                <?php endif; ?>
                                <div class="menu-item-details">
                                    <?php if ($item['tags']): ?>
                                        <div class="tags-row">
                                            <?php foreach (explode(',', $item['tags']) as $tag): ?>
                                                <span class="menu-tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="name-price-row">
                                        <h4 class="item-name"><?php echo htmlspecialchars($item['name']); ?></h4>
                                        <div class="price-leader"></div>
                                        <span class="item-price"><?php echo htmlspecialchars($item['price']); ?></span>
                                    </div>

                                    <p class="item-description"><?php echo htmlspecialchars($item['description']); ?></p>

                                    <div class="item-footer">
                                        <button class="order-icon-btn" title="Add to Experience">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="9" cy="21" r="1"></circle>
                                                <circle cx="20" cy="21" r="1"></circle>
                                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
    </main>

    <footer class="aura-footer" style="position: relative; z-index: 20; background: #000;">
        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?> &mdash; CULINARY EXCELLENCE
    </footer>

    <?php include 'includes/contact-modal.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
