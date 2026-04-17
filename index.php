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
        <div class="stroll-bg-overlay"></div>
    </div>
    <div class="layout-wrapper">
        <header class="layout-header">
            <div class="vintage-frame">
                <h1><?php echo SITE_NAME; ?></h1>
            </div>
        </header>

        <nav class="layout-sidebar glass-pill">
            <?php foreach ($sections as $s): ?>
                <a href="#section-<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['section_title']); ?></a>
            <?php endforeach; ?>
            <a href="admin/login.php" style="opacity: 0.2; font-size: 0.6rem; vertical-align: middle;">ADMIN</a>
        </nav>

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
