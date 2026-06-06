<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/contact-logic.php';

// Fetch Menu Hero Image from settings
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'menu_hero_image'");
$stmt->execute();
$menu_hero_image = $stmt->fetchColumn() ?: 'images/menu_full.png';

// Fetch Contact Phone from settings
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'contact_phone'");
$stmt->execute();
$contact_phone = $stmt->fetchColumn() ?: '+234 000 000 0000';

// Fetch all products grouped by category (category must be the first column for FETCH_GROUP)
$stmt = $pdo->query("SELECT category, id, name, price, image_path, description, tags, is_best_seller FROM products ORDER BY category, id ASC");
$all_products = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);

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
    <style>
        .menu-featured-full {
            height: 100vh;
            width: 100%;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
            z-index: 1;
        }
        .featured-clear {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: transparent;
        }
        /* Ensure the layout main doesn't overlap the fixed hero */
        .layout-main {
            position: relative;
            z-index: 10;
            margin-top: 100vh;
        }
    </style>
</head>
<body class="menu-page">
    <?php include 'includes/header.php'; ?>

    <!-- First Scroll: Standard Hero (Purely Visual) -->
    <?php
    $hero_title = "";
    $hero_subtitle = "";
    $hero_bg = 'images/brand-hero.jpg';
    include 'includes/hero.php';
    ?>

    <main class="layout-main">
        <!-- Second Scroll: Full-display Custom Menu Image -->
        <section class="menu-featured-full" style="background-image: url('<?php echo htmlspecialchars($menu_hero_image); ?>');">
            <div class="featured-clear"></div>
        </section>

        <!-- Third Scroll: Digital Menu List -->
        <div class="menu-content-wrapper">
            <header class="menu-header">
                <div class="menu-subtitle">EXPERIENCE OUR SIGNATURE COLLECTION</div>
                <?php
                    $label = "OUR MENU";
                    $white = "OUR ";
                    $orange = "MENU";
                ?>
                <h2 class="luxury-heading"><span class="white-text"><?php echo $white; ?></span><span class="highlight"><?php echo $orange; ?></span></h2>

                <div class="contact-strip" style="margin-top: 40px;">
                    <span class="phone-label">RESERVATIONS & INQUIRIES</span>
                    <a href="tel:<?php echo htmlspecialchars($contact_phone); ?>" class="phone-link"><?php echo htmlspecialchars($contact_phone); ?></a>
                </div>
            </header>

            <?php foreach ($all_products as $category => $products): ?>
                <div class="category-section" style="margin-bottom: 100px;">
                    <h3 style="font-family: 'Cinzel', serif; color: var(--primary-color); font-size: 1.5rem; letter-spacing: 4px; margin-bottom: 60px; border-bottom: 1px solid rgba(255,53,3,0.2); padding-bottom: 20px; text-transform: uppercase;">
                        <?php echo htmlspecialchars($category); ?>
                    </h3>

                    <div class="menu-grid">
                        <?php foreach ($products as $product): ?>
                            <div class="menu-item-card reveal-item">
                                <div class="menu-item-image">
                                    <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" onerror="this.src='images/category-bg.png'">
                                </div>
                                <div class="menu-item-details">
                                    <div class="tags-row">
                                        <?php
                                        $tags = explode(',', $product['tags'] ?? '');
                                        foreach ($tags as $tag):
                                            if (trim($tag)):
                                        ?>
                                            <span class="menu-tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                        <?php
                                            endif;
                                        endforeach; ?>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 10px;">
                                        <h4 style="font-family: 'Cinzel', serif; color: #fff; font-size: 1.2rem; margin: 0;"><?php echo htmlspecialchars($product['name']); ?></h4>
                                        <span style="color: var(--primary-color); font-weight: 700; font-family: 'Inter', sans-serif;"><?php echo htmlspecialchars($product['price']); ?></span>
                                    </div>
                                    <p style="color: #888; font-size: 0.9rem; line-height: 1.6; margin-bottom: 20px;"><?php echo htmlspecialchars($product['description']); ?></p>
                                    <button class="order-btn" style="background: none; border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 10px 20px; border-radius: 50px; font-size: 0.7rem; letter-spacing: 2px; cursor: pointer; transition: all 0.3s ease;" onclick="openContactModal('Order: <?php echo addslashes($product['name']); ?>')">
                                        RESERVE NOW
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="aura-footer" style="position: relative; z-index: 20; background: #000; padding: 60px 0;">
        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?> &mdash; CULINARY EXCELLENCE
    </footer>

    <?php include 'includes/contact-modal.php'; ?>
    <script src="js/script.js"></script>
    <script>
        // Reveal animation on scroll
        const revealItems = document.querySelectorAll('.reveal-item');
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });

        revealItems.forEach(item => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(30px)';
            item.style.transition = 'all 0.8s cubic-bezier(0.2, 1, 0.3, 1)';
            revealObserver.observe(item);
        });
    </script>
</body>
</html>
