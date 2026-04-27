<?php
require_once 'includes/db.php';
require_once 'config.php';

try {
    // Fetch generic content sections
    $stmt = $pdo->query("SELECT * FROM content ORDER BY id ASC");
    $sections = $stmt->fetchAll();

    // Fetch products
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id ASC");
    $products = $stmt->fetchAll();

    // Fetch creative charge (gallery)
    $stmt = $pdo->query("SELECT * FROM creative_charge ORDER BY id ASC");
    $charge_cards = $stmt->fetchAll();

} catch (PDOException $e) {
    $sections = [];
    $products = [];
    $charge_cards = [];
}

// Fetch settings
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'selling_points_title'");
$stmt->execute();
$selling_points_title = $stmt->fetchColumn() ?: 'Features';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'creative_charge_title'");
$stmt->execute();
$creative_charge_title = $stmt->fetchColumn() ?: 'Creative Gallery';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'cta_text'");
$stmt->execute();
$cta_text = $stmt->fetchColumn() ?: 'Explore Now';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'cta_link'");
$stmt->execute();
$cta_link = $stmt->fetchColumn() ?: '#features';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> | Aura Redesign</title>
    <link rel="icon" href="images/favicon.jpg">
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Cinzel:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body class="landing-page">
    <!-- Navigation Bar -->
    <nav class="aura-nav-bar">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="#"><?php echo SITE_NAME; ?></a>
            </div>
            <div class="nav-links">
                <a href="#features" class="nav-item">Features</a>
                <a href="#products" class="nav-item">Products</a>
                <a href="#gallery" class="nav-item">Gallery</a>
            </div>
            <div class="nav-actions">
                <a href="admin/login.php" class="admin-link">Portal</a>
            </div>
        </div>
    </nav>

    <!-- Background Elements -->
    <div class="stroll-bg-container">
        <div class="stroll-bg-image" style="background-image: url('images/landing-bg.png');"></div>
        <div class="mesh-bg"></div>
        <div class="stroll-bg-overlay"></div>
    </div>

    <div class="layout-wrapper">
        <!-- Hero Section -->
        <header class="hero-section">
            <div class="hero-content">
                <div class="vintage-frame">
                    <h1><?php echo SITE_NAME; ?></h1>
                </div>
                <p class="hero-subtitle">Redefining Excellence through Design and Innovation.</p>
                <div class="hero-cta">
                    <a href="<?php echo htmlspecialchars($cta_link); ?>" class="cta"><?php echo htmlspecialchars($cta_text); ?></a>
                </div>
            </div>
        </header>

        <main class="layout-main">
            <!-- Features Section -->
            <section id="features" class="page-section">
                <h2 class="section-title"><?php echo htmlspecialchars($selling_points_title); ?></h2>
                <div class="features-grid">
                    <?php foreach ($sections as $index => $s): ?>
                        <div class="reveal-section <?php echo ($s['image_path'] && $index % 2 != 0) ? 'has-image reverse' : ($s['image_path'] ? 'has-image' : ''); ?>">
                            <?php if ($s['image_path']): ?>
                                <div class="section-image">
                                    <img src="<?php echo htmlspecialchars($s['image_path']); ?>" alt="<?php echo htmlspecialchars($s['section_title']); ?>" loading="lazy">
                                </div>
                            <?php endif; ?>
                            <div class="section-content">
                                <h3><?php echo htmlspecialchars($s['section_title']); ?></h3>
                                <p><?php echo nl2br(htmlspecialchars($s['description'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Products Section -->
            <section id="products" class="page-section">
                <h2 class="section-title">Power Kits</h2>
                <div class="products-grid">
                    <?php foreach ($products as $p): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="<?php echo htmlspecialchars($p['image_path']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" loading="lazy">
                            </div>
                            <div class="product-info">
                                <h4><?php echo htmlspecialchars($p['name']); ?></h4>
                                <span class="price">$<?php echo htmlspecialchars($p['price']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Gallery Section -->
            <section id="gallery" class="page-section">
                <h2 class="section-title"><?php echo htmlspecialchars($creative_charge_title); ?></h2>
                <div class="gallery-grid">
                    <?php foreach ($charge_cards as $card): ?>
                        <div class="gallery-item">
                            <div class="gallery-image dual">
                                <img src="<?php echo htmlspecialchars($card['image_path_1']); ?>" alt="Gallery Image 1" loading="lazy">
                                <img src="<?php echo htmlspecialchars($card['image_path_2']); ?>" alt="Gallery Image 2" loading="lazy">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>

        <footer class="layout-footer">
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All Rights Reserved.</p>
        </footer>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
