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
    <div class="layout-wrapper">
        <header class="layout-header">
            <div class="vintage-frame">
                <h1><?php echo SITE_NAME; ?></h1>
            </div>
        </header>

        <aside class="layout-sidebar">
            <div class="sidebar-content">
                <h2><?php echo htmlspecialchars($selling_points_title); ?></h2>
                <div class="sidebar-line"></div>

                <nav class="sidebar-nav">
                    <ul>
                        <?php foreach ($sections as $s): ?>
                            <li><a href="#section-<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['section_title']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            </div>
        </aside>

        <main class="layout-main" id="features">
            <?php foreach ($sections as $s): ?>
                <section id="section-<?php echo $s['id']; ?>" class="reveal-section <?php echo $s['image_path'] ? 'has-image' : ''; ?>">
                    <?php if ($s['image_path']): ?>
                        <div class="section-image">
                            <img src="<?php echo htmlspecialchars($s['image_path']); ?>" alt="<?php echo htmlspecialchars($s['section_title']); ?>">
                        </div>
                    <?php endif; ?>
                    <div class="section-content">
                        <h2><?php echo htmlspecialchars($s['section_title']); ?></h2>
                        <p><?php echo nl2br(htmlspecialchars($s['description'])); ?></p>
                    </div>
                </section>
            <?php endforeach; ?>
        </main>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
