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

    // Migration for Boutique link
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM navigation_items WHERE label = 'Boutique' AND nav_type = 'admin'");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            $pdo->prepare("INSERT INTO navigation_items (label, link_url, sort_order, nav_type) VALUES (?, ?, ?, ?)")
                ->execute(['Boutique', 'dashboard.php?view=products', 5, 'admin']);
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

    // Create contact_messages table
    $pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        subject TEXT,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create products table
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        price TEXT NOT NULL,
        category TEXT,
        image_path TEXT,
        is_best_seller INTEGER DEFAULT 0,
        description TEXT,
        tags TEXT
    )");

    // Seed default products if empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    if ($stmt->fetchColumn() == 0) {
        $products = [
            ['Arowolo', '₦19,000', 'MAIN COURSES', 'images/product-1.jpg', 1, 'Authentic Nigerian delicacy prepared with choice meats.', 'RICE, TURKEY, FOOD'],
            ['Fish Peppersoup', '₦16,000', 'MAIN COURSES', 'images/product-2.jpg', 1, 'Spicy and aromatic broth with fresh catch of the day.', 'FISH, SOUP, SPICY'],
            ['Sokoyokoto', '₦23,000', 'MAIN COURSES', 'images/product-3.jpg', 1, 'Rich and soulful traditional preparation.', 'BEEF, STEW, LITE'],
            ['Egusi Special', '₦18,500', 'MAIN COURSES', 'images/product-4.jpg', 1, 'Melon seed soup with assorted meats and vegetables.', 'SOUP, FOOD, COMBO']
        ];
        $prodStmt = $pdo->prepare("INSERT INTO products (name, price, category, image_path, is_best_seller, description, tags) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($products as $p) {
            $prodStmt->execute($p);
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
            ['COLLECTIONS', 'categories.php', 1, 'main'],
            ['ABOUT US', 'about.php', 2, 'main'],
            ['MENU', 'menu.php', 3, 'main'],
            ['EXPLORE', 'explore.php', 4, 'main'],
            ['BLOG', 'blog.php', 5, 'main'],
            ['GALLERY', 'gallery.php', 6, 'main'],
            ['GET IN TOUCH', '#contact-modal', 7, 'main'],
            ['Portal', 'dashboard.php?view=overview', 0, 'admin'],
            ['Aura', 'dashboard.php?view=settings', 1, 'admin'],
            ['Create', 'dashboard.php?view=add', 2, 'admin'],
            ['Manage', 'dashboard.php?view=manage', 3, 'admin'],
            ['Inquiries', 'dashboard.php?view=messages', 4, 'admin'],
            ['Boutique', 'dashboard.php?view=products', 5, 'admin'],
            ['Navigation', 'dashboard.php?view=nav', 6, 'admin']
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
