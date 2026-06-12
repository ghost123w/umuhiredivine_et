<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/icons.php';

// Featured image 1
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'menu_featured_image'");
$stmt->execute();
$featured_img = $stmt->fetchColumn() ?: 'images/menu_featured.jpg';

// Branding Title
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'menu_branding_title'");
$stmt->execute();
$branding_title = $stmt->fetchColumn() ?: 'MENU';

// Featured image 2
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'menu_featured_image_2'");
$stmt->execute();
$featured_img_2 = $stmt->fetchColumn() ?: 'images/menu-featured-default.jpg';

// Reveal Title 2
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'menu_reveal_title_2'");
$stmt->execute();
$reveal_title_2 = $stmt->fetchColumn() ?: '';

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

    <main class="layout-main">
        <!-- Viewport 1: Reveal first featured image -->
        <section class="reveal-scroll-section" style="background-image: url('<?php echo htmlspecialchars($featured_img); ?>');">
            <div class="hero-overlay" style="background: rgba(0,0,0,0.2);"></div>
            <div class="hero-content">
                <div class="hero-brand-stack">
                    <h1 class="hero-title-bg"><?php echo htmlspecialchars($branding_title); ?></h1>
                    <h1 class="hero-title-fg"><?php echo htmlspecialchars($branding_title); ?></h1>
                </div>
            </div>
        </section>

        <!-- Viewport 2: Full Width Luxury Menu -->
        <section class="reveal-scroll-section menu-full-width-section" style="background-image: url('<?php echo htmlspecialchars($featured_img_2); ?>'); height: auto; min-height: 100vh;">
            <div class="hero-overlay" style="background: rgba(0,0,0,0.7);"></div>

            <div class="menu-items-full-wrapper">
                <?php if ($reveal_title_2): ?>
                    <div class="reveal-title-v2" style="text-align: center; margin-bottom: 80px; position: relative; z-index: 20;">
                        <?php echo htmlspecialchars($reveal_title_2); ?>
                    </div>
                <?php endif; ?>

                <div class="menu-scroll-content">
                    <?php
                    $current_cat = '';
                    foreach ($products as $product):
                        if ($current_cat !== $product['category']):
                            if ($current_cat !== '') echo '</div></div>';
                            $current_cat = $product['category'];
                    ?>
                        <div class="category-group-v3">
                            <h3 class="category-title-luxury-v3"><?php echo htmlspecialchars($current_cat); ?></h3>
                            <div class="items-grid-v3">
                    <?php endif; ?>

                        <div class="menu-item-v3">
                            <div class="item-v3-image">
                                <?php if ($product['image_path']): ?>
                                    <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php endif; ?>
                            </div>
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
                                <div class="item-v3-footer">
                                    <button class="cart-btn-v3">
                                        <?php echo get_cart_icon(); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if ($current_cat !== '') echo '</div></div>'; ?>
                </div>
            </div>

            <!-- Floating Cart Indicator -->
            <div class="cart-floating-btn">
                <span class="cart-text">CART EMPTY</span>
                <span class="cart-icon-wrapper">
                    <?php echo get_cart_icon(); ?>
                </span>
            </div>
        </section>
    </main>

    <?php include 'includes/contact-modal.php'; ?>
    <footer class="aura-footer">
        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?> &mdash; CULINARY EXCELLENCE
    </footer>
    <script src="js/script.js"></script>
</body>
</html>
