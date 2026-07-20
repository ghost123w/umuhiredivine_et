<?php
require_once __DIR__ . '/../config.php';
try {
    if (!is_dir(__DIR__ . '/../data')) {
        mkdir(__DIR__ . '/../data', 0755, true);
    }
    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        email TEXT,
        is_verified INTEGER DEFAULT 1
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS content (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        section_title TEXT,
        description TEXT,
        image_path TEXT,
        nav_item_id INTEGER DEFAULT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Migration for existing databases
    try {
        $pdo->query("SELECT image_path FROM content LIMIT 1");
    } catch (Exception $e) {
        $pdo->exec("ALTER TABLE content ADD COLUMN image_path TEXT");
    }

    try {
        $pdo->query("SELECT nav_item_id FROM content LIMIT 1");
    } catch (Exception $e) {
        $pdo->exec("ALTER TABLE content ADD COLUMN nav_item_id INTEGER DEFAULT NULL");
    }

    // Migration for Footer link
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM navigation_items WHERE label = 'Footer' AND nav_type = 'admin'");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            $pdo->prepare("INSERT INTO navigation_items (label, link_url, sort_order, nav_type) VALUES (?, ?, ?, ?)")
                ->execute(['Footer', 'dashboard.php?view=footer', 7, 'admin']);
        }
    } catch (Exception $e) {
        // Table might not exist yet, handled by seed below
    }

    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        setting_key TEXT UNIQUE,
        setting_value TEXT
    )");

    // Seed default settings
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)");
    $stmt->execute(['selling_points_title', '']);
    $stmt->execute(['book_us_link', '#']);
    $stmt->execute(['section3_image_1', 'images/making.jpg']);
    $stmt->execute(['section3_image_2', 'images/brand-portrait.jpg']);
    $stmt->execute(['section3_subtitle', 'CURATED FOR THE CONNOISSEUR']);
    $stmt->execute(['section3_title', 'PRIVATE COLLECTION']);
    $stmt->execute(['section3_desc', 'A symphony of exquisite skincare, luxury oils, and rare fragrances designed to elevate your daily ritual. Discover custom-tailored creations.']);

    // Create contact_messages table
    $pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        subject TEXT,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create footer_items table
    $pdo->exec("CREATE TABLE IF NOT EXISTS footer_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        label TEXT NOT NULL,
        link_url TEXT NOT NULL,
        sort_order INTEGER DEFAULT 0,
        is_active INTEGER DEFAULT 1
    )");

    // Seed default footer items if table is empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM footer_items");
    if ($stmt->fetchColumn() == 0) {
        $footerItems = [
            ['PRIVACY POLICY', '#', 0],
            ['TERMS OF SERVICE', '#', 1],
            ['COOKIE POLICY', '#', 2],
            ['INSTAGRAM', '#', 3],
            ['FACEBOOK', '#', 4]
        ];
        $footerStmt = $pdo->prepare("INSERT INTO footer_items (label, link_url, sort_order) VALUES (?, ?, ?)");
        foreach ($footerItems as $f) {
            $footerStmt->execute($f);
        }
    }

    // Create navigation_items table
    $pdo->exec("CREATE TABLE IF NOT EXISTS navigation_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        label TEXT NOT NULL,
        link_url TEXT NOT NULL,
        sort_order INTEGER DEFAULT 0,
        is_active INTEGER DEFAULT 1,
        nav_type TEXT DEFAULT 'main'
    )");

    // Seed default navigation items if table is empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM navigation_items");
    if ($stmt->fetchColumn() == 0) {
        $items = [
            ['HOME', 'index.php', 0, 'main'],
            ['ABOUT US', 'about.php', 1, 'main'],
            ['MENU', 'menu.php', 2, 'main'],
            ['EXPLORE', 'explore.php', 3, 'main'],
            ['BLOG', 'blog.php', 4, 'main'],
            ['GALLERY', 'gallery.php', 5, 'main'],
            ['GET IN TOUCH', '#contact-modal', 6, 'main'],
            ['Portal', 'dashboard.php?view=overview', 0, 'admin'],
            ['Aura', 'dashboard.php?view=settings', 1, 'admin'],
            ['Create', 'dashboard.php?view=add', 2, 'admin'],
            ['Manage', 'dashboard.php?view=manage', 3, 'admin'],
            ['Inquiries', 'dashboard.php?view=messages', 4, 'admin'],
            ['Navigation', 'dashboard.php?view=nav', 5, 'admin'],
            ['Footer', 'dashboard.php?view=footer', 6, 'admin']
        ];
        $insertStmt = $pdo->prepare("INSERT INTO navigation_items (label, link_url, sort_order, nav_type) VALUES (?, ?, ?, ?)");
        foreach ($items as $item) {
            $insertStmt->execute($item);
        }
    }

} catch (PDOException $e) {
    die("Database connection failed");
}
?>
