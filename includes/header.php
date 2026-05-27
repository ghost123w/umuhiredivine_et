<?php
require_once __DIR__ . '/icons.php';
$socials = get_social_icons();

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'book_us_link'");
$stmt->execute();
$book_us_link = $stmt->fetchColumn() ?: '#';

// Fetch Social Media Links
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'facebook_link'");
$stmt->execute();
$fb_link = $stmt->fetchColumn() ?: '#';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'x_link'");
$stmt->execute();
$x_link = $stmt->fetchColumn() ?: '#';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'instagram_link'");
$stmt->execute();
$ig_link = $stmt->fetchColumn() ?: '#';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'whatsapp_link'");
$stmt->execute();
$wa_link = $stmt->fetchColumn() ?: '#';

$social_links = [
    'facebook' => $fb_link,
    'x' => $x_link,
    'instagram' => $ig_link,
    'whatsapp' => $wa_link
];

// Fetch main navigation items
$navItems = $pdo->query("SELECT * FROM navigation_items WHERE nav_type = 'main' AND is_active = 1 ORDER BY sort_order ASC LIMIT 8")->fetchAll();

$current_page = basename($_SERVER['PHP_SELF']);
$is_admin = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
$base_path = $is_admin ? '../' : '';
?>

<header class="site-header">
    <div class="mobile-nav-toggle" id="mobile-nav-toggle">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <div class="header-top">
        <div class="header-socials">
            <?php foreach ($socials as $name => $svg):
                $link = $social_links[$name] ?? '#';
            ?>
                <a href="<?php echo htmlspecialchars($link); ?>" class="social-link" title="<?php echo ucfirst($name); ?>" target="_blank">
                    <?php echo $svg; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="header-branding">
            <h1 class="brand-title"><?php echo strtoupper(SITE_NAME); ?></h1>
        </div>

        <div class="header-actions">
            <a href="<?php echo htmlspecialchars($book_us_link); ?>" class="book-table-btn">BOOK TABLE</a>
            <?php if ($is_admin): ?>
                <a href="../logout.php" class="admin-portal-link" style="margin-left: 20px;">EXIT</a>
            <?php endif; ?>
        </div>
    </div>

    <nav class="header-menu" id="header-menu">
        <div class="mobile-socials">
            <?php foreach ($socials as $name => $svg):
                $link = $social_links[$name] ?? '#';
            ?>
                <a href="<?php echo htmlspecialchars($link); ?>" class="social-link" target="_blank">
                    <?php echo $svg; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php foreach ($navItems as $item):
            $isActive = ($current_page == $item['link_url']) ? 'active' : '';
            $link_url = (strpos($item['link_url'], 'http') === 0) ? $item['link_url'] : $base_path . $item['link_url'];
        ?>
            <a href="<?php echo htmlspecialchars($link_url); ?>" class="menu-item <?php echo $isActive; ?>">
                <?php echo htmlspecialchars($item['label']); ?>
            </a>
        <?php endforeach; ?>

        <div class="mobile-actions">
            <a href="<?php echo htmlspecialchars($book_us_link); ?>" class="book-table-btn">BOOK TABLE</a>
        </div>
    </nav>
</header>
