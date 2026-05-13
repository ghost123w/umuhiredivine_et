<?php
require_once 'includes/icons.php';
$social_icons = get_social_icons();

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'book_us_link'");
$stmt->execute();
$book_us_link = $stmt->fetchColumn() ?: '#';

$navItems = $pdo->query("SELECT * FROM navigation_items WHERE nav_type = 'main' AND is_active = 1 ORDER BY sort_order ASC")->fetchAll();
?>
<header class="main-site-header">
    <div class="header-left">
        <div class="social-links">
            <a href="#"><?php echo $social_icons['facebook']; ?></a>
            <a href="#"><?php echo $social_icons['x']; ?></a>
            <a href="#"><?php echo $social_icons['instagram']; ?></a>
            <a href="#"><?php echo $social_icons['whatsapp']; ?></a>
        </div>
    </div>

    <div class="header-center">
        <a href="index.php" class="logo-text">
            <?php echo htmlspecialchars(SITE_NAME); ?>
            <span class="logo-sub">by PODs</span>
        </a>
        <nav class="header-nav">
            <?php foreach ($navItems as $item): ?>
                <a href="<?php echo htmlspecialchars($item['link_url']); ?>" class="nav-link"><?php echo htmlspecialchars($item['label']); ?></a>
            <?php endforeach; ?>
        </nav>
    </div>

    <div class="header-right">
        <a href="<?php echo htmlspecialchars($book_us_link); ?>" class="book-table-btn">BOOK TABLE</a>
    </div>
</header>
