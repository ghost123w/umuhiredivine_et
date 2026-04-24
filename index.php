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
        <div class="stroll-bg-image" style="background-image: url('images/landing-bg.png');"></div>
        <div class="mesh-bg"></div>
        <div class="stroll-bg-overlay"></div>
    </div>

    <nav class="aura-nav-bar">
        <div class="nav-pill">
            <div class="nav-brand-wrapper">
                <a href="#hero" class="aura-brand-3d"><?php echo SITE_NAME; ?></a>
            </div>
            <div class="nav-links">
                <?php foreach ($sections as $s): ?>
                    <a href="#section-<?php echo $s['id']; ?>" class="nav-item"><?php echo strtoupper(htmlspecialchars($s['section_title'])); ?></a>
                <?php endforeach; ?>
                <a href="#power-kits" class="nav-item">POWER</a>
                <a href="#creative-charge" class="nav-item">CHARGE</a>
                <a href="admin/login.php" class="nav-item portal">PORTAL</a>
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

            <!-- POWER KITS SECTION -->
            <section id="power-kits" class="reveal-section power-kits-section">
                <div class="power-kits-container">
                    <h2 class="section-title-bold">POWER KITS</h2>
                    <div class="product-grid">
                        <?php for($i=1; $i<=3; $i++): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="images/power-kit-<?php echo $i; ?>.png" alt="Power Kit <?php echo $i; ?>" onerror="this.src='images/power-kits-section.png'">
                            </div>
                            <h3 class="product-name">Product Name</h3>
                            <p class="product-price">$25.00</p>
                            <div class="product-actions">
                                <div class="quantity-selector">
                                    <button class="qty-btn minus">−</button>
                                    <span class="qty-value">1</span>
                                    <button class="qty-btn plus">+</button>
                                </div>
                                <button class="add-to-cart-btn">ADD TO CART</button>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </section>

            <!-- CREATIVE CHARGE SECTION -->
            <section id="creative-charge" class="reveal-section creative-charge-section">
                <div class="creative-charge-container">
                    <h2 class="creative-charge-title">FOLLOW OUR CREATIVE CHARGE</h2>
                    <div class="creative-grid">
                        <div class="creative-item"><img src="images/creative-1.jpg" alt="Creative 1" onerror="this.src='images/creative-charge.png'"></div>
                        <div class="creative-item"><img src="images/creative-2.jpg" alt="Creative 2" onerror="this.src='images/creative-charge.png'"></div>
                        <div class="creative-item"><img src="images/creative-3.jpg" alt="Creative 3" onerror="this.src='images/creative-charge.png'"></div>
                        <div class="creative-item"><img src="images/creative-4.jpg" alt="Creative 4" onerror="this.src='images/creative-charge.png'"></div>
                    </div>
                    <div class="creative-footer">
                        <a href="#" class="cta connect-btn">CONNECT NOW</a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="aura-footer">
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. ALL RIGHTS RESERVED.</p>
        </footer>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
