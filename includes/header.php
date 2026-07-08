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
$navItems = $pdo->query("SELECT * FROM navigation_items WHERE nav_type = 'main' AND is_active = 1 ORDER BY sort_order ASC")->fetchAll();

$current_page = basename($_SERVER['PHP_SELF']);
$is_admin = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
$base_path = $is_admin ? '../' : '';
$is_explore = (isset($is_explore_page) && $is_explore_page === true);
?>

<header class="site-header-aura <?php echo $is_explore ? 'header-explore' : ''; ?> <?php echo $is_explore ? 'explore-page' : ''; ?>">
    <?php if ($is_explore): ?>
        <div class="header-explore-row">
            <div class="header-brand-explore">
                <a href="<?php echo $base_path; ?>index.php" class="brand-link-explore">
                    <?php echo SITE_NAME; ?>
                </a>
            </div>

            <nav class="header-nav-inline" id="header-menu">
                <?php foreach ($navItems as $item):
                    if (strtoupper($item['label']) === 'GET IN TOUCH') continue;
                    $isActive = ($current_page == $item['link_url']) ? 'active' : '';
                    $link_url = (strpos($item['link_url'], 'http') === 0) ? $item['link_url'] : $base_path . $item['link_url'];
                ?>
                    <a href="<?php echo htmlspecialchars($link_url); ?>" class="nav-item <?php echo $isActive; ?>">
                        <?php echo htmlspecialchars(strtoupper($item['label'])); ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="header-actions-explore">
                <div class="nav-book-stack">
                    <div class="header-socials-horizontal">
                        <?php foreach ($socials as $name => $svg):
                            $link = $social_links[$name] ?? '#';
                        ?>
                            <a href="<?php echo htmlspecialchars($link); ?>" class="social-link" title="<?php echo ucfirst($name); ?>" target="_blank">
                                <?php echo $svg; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <a href="<?php echo htmlspecialchars($book_us_link); ?>" class="book-table-btn-inline">BOOK TABLE</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="header-top-row">
            <div class="header-socials-left">
                <?php foreach ($socials as $name => $svg):
                    $link = $social_links[$name] ?? '#';
                ?>
                    <a href="<?php echo htmlspecialchars($link); ?>" class="social-link" title="<?php echo ucfirst($name); ?>" target="_blank">
                        <?php echo $svg; ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="header-brand-center">
                <a href="<?php echo $base_path; ?>index.php" class="brand-link">
                    <?php echo SITE_NAME; ?>
                </a>
            </div>

            <div class="header-actions-right">
                <a href="<?php echo htmlspecialchars($book_us_link); ?>" class="book-table-btn">BOOK TABLE</a>
                <?php if ($is_admin): ?>
                    <a href="../logout.php" class="admin-portal-link" style="margin-left: 20px;">EXIT</a>
                <?php endif; ?>
            </div>
        </div>

        <nav class="header-nav-bottom" id="header-menu">
            <?php foreach ($navItems as $item):
                $isActive = ($current_page == $item['link_url']) ? 'active' : '';
                $link_url = (strpos($item['link_url'], 'http') === 0) ? $item['link_url'] : $base_path . $item['link_url'];
            ?>
                <a href="<?php echo htmlspecialchars($link_url); ?>" class="nav-item <?php echo $isActive; ?>">
                    <?php echo htmlspecialchars(strtoupper($item['label'])); ?>
                </a>
            <?php endforeach; ?>
        </nav>
    <?php endif; ?>

    <div class="mobile-nav-toggle" id="mobile-nav-toggle">
        <span></span>
        <span></span>
        <span></span>
    </div>
</header>
