<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/icons.php';
require_once 'includes/contact-logic.php';

// Branding Title
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'menu_branding_title'");
$stmt->execute();
$branding_title = $stmt->fetchColumn() ?: 'MENU';

// Background images for reveal sections
$bg_keys = ['menu_featured_image', 'menu_featured_image_2', 'menu_featured_image_3', 'menu_featured_image_4', 'menu_featured_image_5'];
$backgrounds = [];
foreach ($bg_keys as $key) {
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $backgrounds[] = $stmt->fetchColumn() ?: 'images/menu_featured.jpg';
}

// Fetch all products
$stmt = $pdo->query("SELECT * FROM products ORDER BY category ASC, id ASC");
$products = $stmt->fetchAll();

// Group products by category
$categories = [];
foreach ($products as $product) {
    $categories[$product['category']][] = $product;
}
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

    <!-- Sticky Category Nav -->
    <nav class="menu-sticky-nav">
        <div class="nav-scroll-container">
            <?php
            // Extract unique categories from the database items
            $raw_categories = array_keys($categories);

            // Define desired order based on reference site
            $desired_order = ['LITE', 'MAIN COURSES', 'PLATTERS', 'SOUP BOWLS', 'DRINKS'];

            // Sort categories based on desired order, putting others at the end
            usort($raw_categories, function($a, $b) use ($desired_order) {
                $pos_a = array_search($a, $desired_order);
                $pos_b = array_search($b, $desired_order);

                if ($pos_a === false && $pos_b === false) return strcmp($a, $b);
                if ($pos_a === false) return 1;
                if ($pos_b === false) return -1;
                return $pos_a - $pos_b;
            });

            $nav_categories = $raw_categories;

            foreach ($nav_categories as $nav_cat):
            ?>
                <a href="#cat-<?php echo str_replace(' ', '-', strtolower($nav_cat)); ?>" class="menu-nav-link">
                    <?php echo htmlspecialchars($nav_cat); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>

    <main class="layout-main">
        <!-- Viewport 1: Hero Branding -->
        <section class="reveal-scroll-section menu-full-width-section" style="background-image: url('<?php echo htmlspecialchars($backgrounds[0]); ?>');">
            <div class="hero-overlay" style="background: rgba(0,0,0,0.2);"></div>
            <div class="hero-content">
                <div class="hero-brand-stack">
                    <span class="hero-brand-main"><?php echo htmlspecialchars($branding_title); ?></span>
                </div>
            </div>
        </section>

        <?php
        $bg_index = 1;
        // Iterate through all categories found in the database
        foreach ($nav_categories as $cat_name):
            $cat_products = $categories[$cat_name];
            $current_bg = $backgrounds[$bg_index] ?? $backgrounds[0];
            $cat_id = 'cat-' . str_replace(' ', '-', strtolower($cat_name));
        ?>
            <!-- Scrollytelling Section for Category: <?php echo htmlspecialchars($cat_name); ?> -->
            <section id="<?php echo $cat_id; ?>" class="reveal-scroll-section menu-full-width-section" style="background-image: url('<?php echo htmlspecialchars($current_bg); ?>'); height: auto; min-height: 100vh;">
                <div class="hero-overlay" style="background: rgba(0,0,0,0.7);"></div>

                <div class="menu-items-full-wrapper">
                    <div class="category-group-v3">
                        <h3 class="category-title-luxury-v3"><?php echo htmlspecialchars($cat_name); ?></h3>
                        <div class="items-grid-v3">
                            <?php foreach ($cat_products as $product): ?>
                                <div class="menu-item-v3">
                                    <?php if ($product['image_path'] && $product['image_path'] !== 'images/category-bg.png'): ?>
                                    <div class="item-v3-image">
                                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    </div>
                                    <?php endif; ?>
                                    <div class="item-v3-content">
                                        <div class="item-v3-tags">
                                            <?php
                                            $tags = array_filter(array_map('trim', explode(',', $product['tags'] ?? '')));
                                            foreach ($tags as $tag): ?>
                                                <span class="tag-v3"><?php echo htmlspecialchars($tag); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="item-v3-title-price">
                                            <h3 class="item-v3-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                                            <div class="item-v3-dots"></div>
                                            <span class="item-v3-price"><?php echo htmlspecialchars($product['price']); ?></span>
                                        </div>
                                        <p class="item-v3-desc"><?php echo htmlspecialchars($product['description'] ?? ''); ?></p>
                                        <div class="item-v3-actions">
                                            <button class="add-to-cart-btn-v3">
                                                ADD TO CART
                                                <span class="btn-icon"><?php echo get_cart_icon(); ?></span>
                                            </button>
                                        </div>
                                        <div class="item-v3-cart-icon">
                                            <?php echo get_cart_icon(); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php
            $bg_index++;
        endforeach;
        ?>

        <!-- Floating Cart Indicator -->
        <div class="cart-floating-btn">
            <div class="cart-info">
                <span class="cart-count">0 ITEMS</span>
                <span class="cart-total">₦0.00</span>
            </div>
            <span class="cart-icon-wrapper">
                <?php echo get_cart_icon(); ?>
            </span>
        </div>
    </main>

    <?php include 'includes/contact-modal.php'; ?>
    <footer class="aura-footer">
        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?> &mdash; CULINARY EXCELLENCE
    </footer>
    <script src="js/script.js"></script>
</body>
</html>
