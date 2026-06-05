<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/contact-logic.php';

// Fetch all products for the menu
$stmt = $pdo->query("SELECT * FROM products ORDER BY category, id ASC");
$menu_items = $stmt->fetchAll();

// Fetch contact phone
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'contact_phone'");
$stmt->execute();
$contact_phone = $stmt->fetchColumn() ?: '+234 000 000 0000';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'menu_featured_image'");
$stmt->execute();
$menu_featured_image = $stmt->fetchColumn() ?: 'images/menu-featured.png';
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
    <?php
    $hero_title = "MENU";
    $hero_subtitle = "CULINARY EXCELLENCE";
    // First scroll uses a generic background
    $hero_bg = 'images/menu_page.png';
    include 'includes/hero.php';
    ?>

    <main class="layout-main">
        <!-- Second Scroll: Featured Image -->
        <section class="menu-featured-full" style="background-image: url('<?php echo htmlspecialchars($menu_featured_image); ?>');">
            <div class="featured-overlay"></div>
        </section>

        <!-- Third Scroll onwards: Menu Content -->
        <div class="menu-content-wrapper">
            <div class="menu-header">
                <h2 class="luxury-heading">
                    <span class="white-text">OUR</span>
                    <span class="highlight">MENU</span>
                </h2>
                <p class="menu-subtitle">CULINARY EXCELLENCE</p>

                <div class="contact-strip">
                    <span class="phone-label">ORDER VIA PHONE:</span>
                    <a href="tel:<?php echo htmlspecialchars($contact_phone); ?>" class="phone-link"><?php echo htmlspecialchars($contact_phone); ?></a>
                </div>
            </div>

            <div class="menu-grid">
                <?php
                $current_category = '';
                foreach ($menu_items as $item):
                    if ($current_category !== $item['category']):
                        $current_category = $item['category'];
                ?>
                    <div class="menu-category-divider" style="grid-column: 1 / -1; margin-top: 40px; margin-bottom: 20px;">
                        <h3 style="font-family: 'Cinzel', serif; color: var(--primary-color); letter-spacing: 4px; text-transform: uppercase; font-size: 1.2rem; border-bottom: 1px solid rgba(255,53,3,0.2); padding-bottom: 10px;">
                            <?php echo htmlspecialchars($current_category); ?>
                        </h3>
                    </div>
                <?php endif; ?>

                <div class="menu-item-card">
                    <div class="menu-item-image">
                        <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" onerror="this.src='images/category-bg.png'">
                    </div>
                    <div class="menu-item-details">
                        <div class="tags-row">
                            <?php
                            $tags = explode(',', $item['tags']);
                            foreach ($tags as $tag):
                                if (trim($tag)):
                            ?>
                                <span class="menu-tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                            <?php
                                endif;
                            endforeach;
                            ?>
                        </div>
                        <div class="name-price-row">
                            <h4 class="item-name"><?php echo htmlspecialchars($item['name']); ?></h4>
                            <div class="price-leader"></div>
                            <span class="item-price"><?php echo htmlspecialchars($item['price']); ?></span>
                        </div>
                        <p class="item-description"><?php echo htmlspecialchars($item['description']); ?></p>
                        <div class="item-footer">
                            <button class="order-icon-btn" onclick="openContactModal('Order: <?php echo htmlspecialchars($item['name']); ?>')">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <footer class="aura-footer">
        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?> &mdash; CULINARY EXCELLENCE
    </footer>

    <?php include 'includes/contact-modal.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
