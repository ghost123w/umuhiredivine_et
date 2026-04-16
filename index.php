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
$selling_points_title = $stmt->fetchColumn() ?: 'Curation';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'cta_text'");
$stmt->execute();
$cta_text = $stmt->fetchColumn() ?: 'Explore Now';

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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Cinzel:wght@400;700;800&display=swap" rel="stylesheet">
</head>
<body class="landing-page">
    <div class="aurora-bg"></div>

    <nav class="glass-pill-nav">
        <div class="nav-brand shimmer-text"><?php echo SITE_NAME; ?></div>
        <div class="nav-links">
            <a href="#home" class="active">Home</a>
            <a href="#curation"><?php echo htmlspecialchars($selling_points_title); ?></a>
            <a href="admin/login.php">Portal</a>
        </div>
        <div class="nav-actions">
            <a href="<?php echo htmlspecialchars($cta_link); ?>" class="btn-modern" style="padding: 8px 25px; font-size: 0.75rem;"><?php echo htmlspecialchars($cta_text); ?></a>
        </div>
    </nav>

    <header class="landing-hero" id="home">
        <h1 class="shimmer-text"><?php echo SITE_NAME; ?></h1>
        <p>A digital atelier for the modern visionary. Crafted with precision, powered by passion.</p>
        <div class="status-indicator" style="justify-content: center;">
            <div class="pulse-dot"></div>
            <span>System Operational • Artisan Version 2.0</span>
        </div>
    </header>

    <main id="curation">
        <div class="content-grid">
            <?php foreach ($sections as $s): ?>
                <article class="bento-card reveal-section">
                    <?php if ($s['image_path']): ?>
                        <img src="<?php echo htmlspecialchars($s['image_path']); ?>" alt="<?php echo htmlspecialchars($s['section_title']); ?>">
                    <?php endif; ?>
                    <div class="bento-card-content">
                        <h2><?php echo htmlspecialchars($s['section_title']); ?></h2>
                        <p><?php echo nl2br(htmlspecialchars($s['description'])); ?></p>
                        <div style="margin-top: auto;">
                             <a href="<?php echo htmlspecialchars($cta_link); ?>" class="btn-modern" style="display: inline-block; text-decoration: none;"><?php echo htmlspecialchars($cta_text); ?></a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="landing-footer">
        &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?> • Built for Excellence
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
