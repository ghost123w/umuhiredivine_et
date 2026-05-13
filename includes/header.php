<?php
require_once __DIR__ . '/icons.php';
$socials = get_social_icons();

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'book_us_link'");
$stmt->execute();
$book_us_link = $stmt->fetchColumn() ?: '#';

// Fetch main navigation items
$navItems = $pdo->query("SELECT * FROM navigation_items WHERE nav_type = 'main' AND is_active = 1 ORDER BY sort_order ASC LIMIT 8")->fetchAll();

$current_page = basename($_SERVER['PHP_SELF']);
$is_admin = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
$base_path = $is_admin ? '../' : '';
?>

<header class="site-header">
    <div class="header-top">
        <div class="header-socials">
            <?php foreach ($socials as $name => $svg): ?>
                <a href="#" class="social-link" title="<?php echo ucfirst($name); ?>">
                    <?php echo $svg; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="header-branding">
            <h1 class="brand-title"><?php echo strtoupper(SITE_NAME); ?></h1>
            <span class="brand-sub">by PODs</span>
        </div>

        <div class="header-actions">
            <a href="<?php echo htmlspecialchars($book_us_link); ?>" class="book-table-btn">BOOK TABLE</a>
            <?php if ($is_admin): ?>
                <a href="../logout.php" class="admin-portal-link" style="margin-left: 20px;">EXIT</a>
            <?php else: ?>
                <a href="admin/login.php" class="admin-portal-link" style="margin-left: 20px;">PORTAL</a>
            <?php endif; ?>
        </div>
    </div>

    <nav class="header-menu">
        <?php foreach ($navItems as $item):
            $isActive = ($current_page == $item['link_url']) ? 'active' : '';
            $link_url = (strpos($item['link_url'], 'http') === 0) ? $item['link_url'] : $base_path . $item['link_url'];
        ?>
            <a href="<?php echo htmlspecialchars($link_url); ?>" class="menu-item <?php echo $isActive; ?>">
                <?php echo htmlspecialchars($item['label']); ?>
            </a>
        <?php endforeach; ?>
    </nav>
</header>
