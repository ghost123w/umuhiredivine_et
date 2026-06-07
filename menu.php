<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Set the hero background for the menu page
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'menu_featured_image'");
$stmt->execute();
$hero_bg = $stmt->fetchColumn() ?: 'images/menu_featured.jpg';

// Fetch products for the second scroll
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
    <style>
        /* Scrollytelling overrides for Menu page */
        .hero-content { display: none; } /* Hide text on first scroll */
        .layout-main { margin-top: 0; }
        .transparent-scroll {
            height: 100vh;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
        }
        .solid-bg {
            background: #000 !important;
            min-height: 100vh;
        }
        .menu-grid-luxury {
            max-width: 1000px;
            margin: 0 auto;
        }
        .category-header {
            font-family: 'Cinzel', serif;
            color: var(--primary-color);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 10px;
            margin: 60px 0 30px;
            letter-spacing: 5px;
            text-transform: uppercase;
            font-size: 0.9rem;
        }
        .menu-item-aura {
            display: flex;
            justify-content: space-between;
            padding: 25px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .item-detail h3 {
            font-family: 'Cinzel', serif;
            font-size: 1.3rem;
            margin-bottom: 8px;
        }
        .item-detail p {
            color: #666;
            font-size: 0.85rem;
            max-width: 500px;
        }
        .item-price-aura {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 1.1rem;
        }
    </style>
</head>
<body class="menu-page">
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/hero.php'; ?>

    <main class="layout-main">
        <!-- First Scroll: Background + Frame -->
        <section class="transparent-scroll">
            <div class="frame-border"></div>
        </section>

        <!-- Second Scroll: Menu Items -->
        <section class="solid-bg">
            <div class="container menu-grid-luxury">
                <div class="menu-header-minimal" style="margin-bottom: 60px;">
                    <h1 class="luxury-heading"><span class="white-text">OUR</span> <span class="highlight">MENU</span></h1>
                </div>

                <?php
                $current_cat = '';
                foreach ($products as $product):
                    if ($current_cat !== $product['category']):
                        $current_cat = $product['category'];
                        echo "<h2 class='category-header'>$current_cat</h2>";
                    endif;
                ?>
                <div class="menu-item-aura">
                    <div class="item-detail">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                    </div>
                    <div class="item-price-aura">
                        <?php echo htmlspecialchars($product['price']); ?>
                    </div>
                </div>
                <?php endforeach; ?>
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
