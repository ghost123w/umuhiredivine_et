<?php
require_once __DIR__ . '/icons.php';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'book_us_link'");
$stmt->execute();
$book_us_link = $stmt->fetchColumn() ?: '#';

// Fetch main navigation items
$navItems = $pdo->query("SELECT * FROM navigation_items WHERE nav_type = 'main' AND is_active = 1 ORDER BY sort_order ASC LIMIT 8")->fetchAll();

$current_page = basename($_SERVER['PHP_SELF']);
$is_admin = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
$base_path = $is_admin ? '../' : '';
?>
<div class="red-status-dot"></div>

<header class="main-site-header">
    <nav class="header-nav">
        <?php foreach ($navItems as $item):
            $isActive = ($current_page == $item['link_url']) ? 'active' : '';
            $link_url = (strpos($item['link_url'], 'http') === 0) ? $item['link_url'] : $base_path . $item['link_url'];
        ?>
            <a href="<?php echo htmlspecialchars($link_url); ?>"
               class="nav-dot <?php echo $isActive; ?>"
               data-label="<?php echo htmlspecialchars($item['label']); ?>">
            </a>
        <?php endforeach; ?>

        <!-- Add Admin/Exit link if in admin area or as a special dot -->
        <?php if ($is_admin): ?>
            <a href="../logout.php" class="nav-dot" data-label="EXIT" style="border-color: var(--danger-color); margin-top: 20px;"></a>
        <?php else: ?>
            <a href="admin/login.php" class="nav-dot" data-label="PORTAL" style="border-color: #444; margin-top: 20px;"></a>
        <?php endif; ?>
    </nav>
</header>
