<?php
require_once __DIR__ . '/../config.php';
try {
    $db_dir = __DIR__ . '/../data';
    if (!is_dir($db_dir)) {
        mkdir($db_dir, 0755, true);
    }

    $db_exists = file_exists(DB_PATH);
    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Only run initialization if database is new or essential tables are missing
    $table_check = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='admins'");
    if (!$table_check->fetch()) {
        $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            email TEXT,
            is_verified INTEGER DEFAULT 1
        )");

        // Seed default admin: admin / admin123
        $admin_exists = $pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
        if ($admin_exists == 0) {
            $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO admins (username, password, email) VALUES (?, ?, ?)")
                ->execute(['admin', $hashed_password, 'admin@example.com']);
        }


        $pdo->exec("CREATE TABLE IF NOT EXISTS content (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            section_title TEXT,
            description TEXT,
            image_path TEXT,
            nav_item_id INTEGER DEFAULT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            setting_key TEXT UNIQUE,
            setting_value TEXT
        )");

        // Seed default settings
        $stmt = $pdo->prepare("INSERT OR IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)");
        $stmt->execute(['selling_points_title', '']);
        $stmt->execute(['book_us_link', '#']);
        $stmt->execute(['menu_hero_image', 'images/menu_featured.jpg']);
        $stmt->execute(['menu_featured_image', 'images/menu_featured.jpg']);
        $stmt->execute(['menu_featured_image_2', 'images/woman-portrait.png']);
        $stmt->execute(['menu_featured_image_3', 'images/menu_featured.jpg']);
        $stmt->execute(['menu_featured_image_4', 'images/home-bg.jpg']);
        $stmt->execute(['menu_featured_image_5', 'images/brand-hero.jpg']);
        $stmt->execute(['menu_branding_title', 'MENU']);
        $stmt->execute(['menu_reveal_title_2', '']);
        $stmt->execute(['menu_collection_title', 'OUR COLLECTION']);

        $pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            subject TEXT,
            message TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

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

        // Seed default products
        $products = [
            ['Peppered Snail', '₦12,000', 'APPETIZERS', 'images/category-bg.png', 1, 'Jumbo snails sautéed in spicy pepper sauce.', 'SPICY, SEAFOOD'],
            ['Suya Platter', '₦9,000', 'APPETIZERS', 'images/category-bg.png', 1, 'Spiced grilled beef skewers with onions and yaji.', 'BEEF, SPICY'],
            ['Fish Peppersoup', '₦16,000', 'MAIN COURSES', 'images/category-bg.png', 1, 'Spicy and aromatic broth with fresh catch of the day.', 'FISH, SOUP, SPICY'],
            ['Sokoyokoto', '₦23,000', 'MAIN COURSES', 'images/category-bg.png', 1, 'Experience the delightful blend of flavors and textures with our Sokoyokoto.', 'SWALLOW, SEAFOOD'],
            ['Egusi Special', '₦18,500', 'MAIN COURSES', 'images/category-bg.png', 1, 'Melon seed soup with assorted meats and vegetables.', 'SOUP, FOOD, COMBO'],
            ['Pounded Yam & Egusi', '₦15,000', 'MAIN COURSES', 'images/category-bg.png', 1, 'Freshly pounded yam with rich Egusi soup.', 'SWALLOW, TRADITIONAL'],
            ['Chocolate Fondant', '₦6,500', 'DESSERTS', 'images/category-bg.png', 0, 'Warm chocolate cake with a molten center.', 'SWEET, CHOCOLATE'],
            ['Fruit Platter', '₦5,000', 'DESSERTS', 'images/category-bg.png', 0, 'Seasonal fresh tropical fruits.', 'FRESH, LIGHT'],
            ['Alariya', '₦18,000.00', 'SIGNATURES', 'images/category-bg.png', 1, 'Freshly pounded yam served with Egusi/Efo riro and assorted meat.', 'EGUSI, EFO-RIRO, ASSORTED'],
            ['Ayedun', '₦18,500.00', 'SIGNATURES', 'images/category-bg.png', 1, 'Freshly pounded yam served with Egusi/Efo riro, 1 goat meat, and 1 chicken.', 'EGUSI, EFO-RIRO, GOAT, CHICKEN'],
            ['Oyin Momo Combo', '₦16,000.00', 'SIGNATURES', 'images/category-bg.png', 1, 'Dundun (Fried Yam) or Dodo (Plantain).', 'ASUN, DODO, DUNDUN, FOOD, GOAT MEAT'],
            ['Arowolo', '₦19,000.00', 'SIGNATURES', 'images/category-bg.png', 1, '2 wraps of Freshly Pounded Yam with choice of assorted Egusi/Efo Riro.', 'EFO RIRO, EGUSI, FOOD, POUNDED YAM'],
            ['Ila Alasepo Soup Bowl(4 Litres)', '₦44,000.00', 'SOUP BOWLS', 'images/category-bg.png', 1, 'A smooth, seasoned one-pot okro delight that brings comfort and culture together.', 'ILA ALASEPO, OKRO SOUP, SOUP BOWL'],
            ['Ila Alasepo Soup Bowl(2 Litres)', '₦22,000.00', 'SOUP BOWLS', 'images/category-bg.png', 1, 'A smooth, seasoned one-pot okro delight that brings comfort and culture together.', 'ILA ALASEPO, OKRO SOUP, SOUP BOWL'],
            ['Sea Food Okro Soup Bowl(4 Litres)', '₦94,000.00', 'SOUP BOWLS', 'images/category-bg.png', 1, 'A smooth, seasoned one-pot okro delight that brings comfort and culture together.', 'SEAFOOD OKRO, OKRA SOUP, SOUP BOWL'],
            ['Sea Food Okro Soup Bowl(2 Litres)', '₦50,000.00', 'SOUP BOWLS', 'images/category-bg.png', 1, 'A smooth, seasoned one-pot okro delight that brings comfort and culture together.', 'SEAFOOD OKRO, OKRA SOUP, SOUP BOWL']
        ];
        $prodStmt = $pdo->prepare("INSERT INTO products (name, price, category, image_path, is_best_seller, description, tags) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($products as $p) {
            $prodStmt->execute($p);
        }

        $pdo->exec("CREATE TABLE IF NOT EXISTS navigation_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            label TEXT NOT NULL,
            link_url TEXT NOT NULL,
            sort_order INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            nav_type TEXT DEFAULT 'main'
        )");

        $items = [
            ['HOME', 'index.php', 0, 'main'],
            ['MENU', 'menu.php', 3, 'main'],
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

    // Dynamic Migrations (Run once if columns missing)
    $content_columns = $pdo->query("PRAGMA table_info(content)")->fetchAll(PDO::FETCH_COLUMN, 1);
    if (!in_array('image_path', $content_columns)) {
        $pdo->exec("ALTER TABLE content ADD COLUMN image_path TEXT");
    }
    if (!in_array('nav_item_id', $content_columns)) {
        $pdo->exec("ALTER TABLE content ADD COLUMN nav_item_id INTEGER DEFAULT NULL");
    }

} catch (PDOException $e) {
    die("Database connection failed");
}
?>
