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
        <div class="stroll-bg-image" style="background-image: url('images/aura-bg.jpg');"></div>
        <div class="stroll-bg-overlay"></div>
    </div>
    <!-- Section Indicator (ScrollSpy) -->
    <nav class="section-nav">
        <ul>
            <?php foreach ($sections as $index => $s): ?>
                <li><a href="#section-<?php echo $s['id']; ?>" class="section-dot" data-section="section-<?php echo $s['id']; ?>"><span class="dot-label"><?php echo htmlspecialchars($s['section_title']); ?></span></a></li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <header class="layout-header">
        <div class="layout-header-content">
            <h1 class="brand-title"><?php echo htmlspecialchars(SITE_NAME); ?></h1>
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="admin/login.php" class="nav-admin">Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>


    <div class="portrait-viewport">
        <main class="portrait-container" id="features">
            <?php foreach ($sections as $s): ?>
                <section id="section-<?php echo $s['id']; ?>" class="portrait-card reveal-section <?php echo $s['image_path'] ? 'with-image' : ''; ?>">
                    <?php if ($s['image_path']): ?>
                        <div class="portrait-image-header">
                            <img src="<?php echo htmlspecialchars($s['image_path']); ?>" alt="<?php echo htmlspecialchars($s['section_title']); ?>">
                        </div>
                    <?php endif; ?>
                    <div class="portrait-content">
                        <span class="section-tag">Feature</span>
                        <h2><?php echo htmlspecialchars($s['section_title']); ?></h2>
                        <p><?php echo nl2br(htmlspecialchars($s['description'])); ?></p>
                        <div class="portrait-footer">
                            <a href="<?php echo htmlspecialchars($cta_link); ?>" class="cta"><?php echo htmlspecialchars($cta_text); ?></a>
                        </div>
                    </div>
                </section>
            <?php endforeach; ?>
        </main>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
