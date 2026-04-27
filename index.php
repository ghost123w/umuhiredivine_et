<?php
require_once 'includes/db.php';
require_once 'config.php';
try {
    $stmt = $pdo->query("SELECT * FROM content ORDER BY id ASC");
    $sections = $stmt->fetchAll();
} catch (PDOException $e) {
    $sections = [];
}

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'selling_points_title'");
$stmt->execute();
$selling_points_title = $stmt->fetchColumn() ?: 'Actions';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'cta_text'");
$stmt->execute();
$cta_text = $stmt->fetchColumn() ?: 'Get Started';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'cta_link'");
$stmt->execute();
$cta_link = $stmt->fetchColumn() ?: '#';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'creative_charge_title'");
$stmt->execute();
$creative_charge_title = $stmt->fetchColumn() ?: 'FOLLOW OUR CREATIVE CHARGE';

$creative_charge_items = $pdo->query("SELECT * FROM creative_charge ORDER BY id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link rel="icon" href="images/favicon.jpg">
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="landing-page">
    <div class="stroll-bg-container">
        <div class="stroll-bg-image"></div>
        <div class="mesh-bg"></div>
        <div class="stroll-bg-overlay"></div>
        <div class="grain-overlay"></div>
    </div>

    <nav class="aura-nav-bar">
        <div class="nav-brand-top">
            <a href="#hero" class="aura-brand-3d"><?php echo SITE_NAME; ?></a>
        </div>
        <div class="nav-pill">
            <div class="nav-links">
                <?php foreach ($sections as $s): ?>
                    <a href="#section-<?php echo $s['id']; ?>" class="nav-item"><?php echo strtoupper(htmlspecialchars($s['section_title'])); ?></a>
                <?php endforeach; ?>
                <a href="#power-kits" class="nav-item">POWER</a>
                <a href="#creative-charge" class="nav-item">CHARGE</a>
            </div>
        </div>
    </nav>

    <div class="layout-wrapper">
        <section id="hero" class="aura-hero">
            <div class="hero-content">
                <p class="hero-subtitle">We forge visuals that crush doubts and spark action.</p>
                <div class="hero-title-wrapper">
                    <h1><?php echo SITE_NAME; ?></h1>
                </div>
                <div class="hero-cta">
                    <a href="<?php echo htmlspecialchars($cta_link); ?>" class="cta"><?php echo htmlspecialchars($cta_text); ?></a>
                </div>
            </div>
            <div class="scroll-indicator">
                <div class="mouse"></div>
            </div>
        </section>

        <main class="layout-main" id="features">
            <!-- POWER KITS SECTION -->
            <section id="power-kits" class="reveal-section power-kits-section">
                <div class="power-kits-container">
                    <h2 class="section-title-bold">POWER KITS</h2>
                    <div class="product-grid">
                        <?php
                        $products = $pdo->query("SELECT * FROM products ORDER BY id ASC")->fetchAll();
                        foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" onerror="this.src='images/brand-portrait.jpg'">
                            </div>
                            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-price">$<?php echo htmlspecialchars($product['price']); ?></p>
                            <div class="product-actions">
                                <div class="quantity-selector">
                                    <button class="qty-btn minus">−</button>
                                    <span class="qty-value">1</span>
                                    <button class="qty-btn plus">+</button>
                                </div>
                                <button class="add-to-cart-btn">ADD TO CART</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <!-- DYNAMIC CONTENT SECTIONS -->
            <?php foreach ($sections as $index => $s): ?>
                <section id="section-<?php echo $s['id']; ?>" class="reveal-section <?php echo ($s['image_path'] && $index % 2 != 0) ? 'has-image reverse' : ($s['image_path'] ? 'has-image' : ''); ?>">
                    <?php if ($s['image_path']): ?>
                        <div class="section-image">
                            <img src="<?php echo htmlspecialchars($s['image_path']); ?>" alt="<?php echo htmlspecialchars($s['section_title']); ?>">
                        </div>
                    <?php endif; ?>
                    <div class="section-content">
                        <h2><?php echo htmlspecialchars($s['section_title']); ?></h2>
                        <p><?php echo nl2br(htmlspecialchars($s['description'])); ?></p>
                        <div style="margin-top: 40px;">
                            <a href="<?php echo htmlspecialchars($cta_link); ?>" class="cta secondary"><?php echo htmlspecialchars($cta_text); ?></a>
                        </div>
                    </div>
                </section>
            <?php endforeach; ?>

            <!-- CREATIVE CHARGE SECTION -->
            <section id="creative-charge" class="reveal-section creative-charge-section">
                <div class="creative-charge-container">
                    <h2 class="creative-charge-title"><?php echo htmlspecialchars($creative_charge_title); ?></h2>
                    <div class="creative-grid">
                        <?php foreach ($creative_charge_items as $item): ?>
                            <div class="creative-card">
                                <h3 class="creative-card-header"><?php echo htmlspecialchars($creative_charge_title); ?></h3>
                                <div class="creative-dual-images">
                                    <div class="creative-img-box">
                                        <img src="<?php echo htmlspecialchars($item['image_path_1']); ?>" alt="Creative 1" onerror="this.src='images/brand-portrait.jpg'">
                                    </div>
                                    <div class="creative-img-box">
                                        <img src="<?php echo htmlspecialchars($item['image_path_2']); ?>" alt="Creative 2" onerror="this.src='images/brand-portrait.jpg'">
                                    </div>
                                </div>
                                <div class="creative-card-footer">
                                    <a href="<?php echo htmlspecialchars($cta_link); ?>" class="connect-pill-small">CONNECT NOW</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <!-- FAQ SECTION -->
            <section id="faq" class="reveal-section faq-section">
                <div class="faq-container">
                    <div class="accordion">
                        <div class="accordion-item">
                            <div class="accordion-header">
                                <h3>ACCORDION ITEM 1</h3>
                                <span class="icon">+</span>
                            </div>
                            <div class="accordion-content">
                                <p>Content for accordion item 1 goes here. This section expands when the header is clicked.</p>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <div class="accordion-header">
                                <h3>ACCORDION ITEM 2</h3>
                                <span class="icon">+</span>
                            </div>
                            <div class="accordion-content">
                                <p>Content for accordion item 2 goes here. Detailed information can be displayed here.</p>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <div class="accordion-header">
                                <h3>ACCORDION ITEM 3</h3>
                                <span class="icon">+</span>
                            </div>
                            <div class="accordion-content">
                                <p>Content for accordion item 3 goes here. This provides a clean way to manage large amounts of content.</p>
                            </div>
                        </div>
                    </div>

                    <div class="faq-cta">
                        <a href="#" class="connect-now-pill">CONNECT NOW</a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="stroll-footer">
            <div class="footer-container">
                <div class="footer-brand">
                    <h2 class="footer-logo"><?php echo SITE_NAME; ?></h2>
                    <p class="footer-credit">Made with <a href="#" style="color: #ff602e; text-decoration: underline;">Squarespace</a></p>
                </div>
                <div class="footer-info">
                    <div class="footer-column">
                        <h4>LOCATION</h4>
                        <p>123 Demo Street<br>New York, NY 12345</p>
                    </div>
                    <div class="footer-column">
                        <h4>CONTACT</h4>
                        <p>email@example.com<br>(555) 555-5555</p>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. ALL RIGHTS RESERVED.</p>
            </div>
        </footer>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
