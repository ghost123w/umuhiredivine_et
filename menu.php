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
    $hero_bg = 'images/menu_page.png';
    include 'includes/hero.php';
    ?>

    <?php if ($menu_featured_image): ?>
        <section class="menu-featured-section" style="background-image: url('<?php echo htmlspecialchars($menu_featured_image); ?>');"></section>
    <?php endif; ?>

    <main class="menu-container">

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
    </main>

    <footer class="aura-footer">
        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?> &mdash; CULINARY EXCELLENCE
    </footer>

    <?php include 'includes/contact-modal.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
