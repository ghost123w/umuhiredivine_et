<?php
require_once __DIR__ . '/../config.php';
try {
    $db_dir = __DIR__ . '/../data';
    if (!is_dir($db_dir)) {
        mkdir($db_dir, 0755, true);
    }

    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Function to check if a table exists
    function tableExists($pdo, $table) {
        try {
            $result = $pdo->query("SELECT 1 FROM $table LIMIT 1");
        } catch (Exception $e) {
            return false;
        }
        return $result !== false;
    }

    // Initialize tables if they don't exist
    if (!tableExists($pdo, 'products')) {
        $pdo->exec("CREATE TABLE products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            price TEXT NOT NULL,
            category TEXT,
            image_path TEXT,
            is_best_seller INTEGER DEFAULT 0,
            description TEXT,
            tags TEXT
        )");

        // Seed products from Ile Iyan Menu
        $products = [
            ['Ìrésì Àgbàlá Lite Combo', '₦4,651.16', 'LITE', 'images/category-bg.png', 0, 'Village Rice cooked in palm oil sauce served with Turkey and a Pet Coke Drink', 'RICE, TURKEY, FOOD, LITE, COMBO, DRINK'],
            ['Ìrésì Àgbàlá Lite', '₦4,186.04', 'LITE', 'images/category-bg.png', 0, 'Village Rice cooked in palm oil sauce served with Turkey', 'RICE, TURKEY, FOOD, LITE, VILLAGE RICE'],
            ['Ìgbàlódé Lite', '₦4,186.04', 'LITE', 'images/category-bg.png', 0, '1 wrap of Freshly Pounded Yam with 1 Goat Meat', 'POUNDED YAM, GOAT MEAT, EFO RIRO, EGUSI, FOOD, LITE'],
            ['Ìgbàlódé Lite Combo', '₦4,651.16', 'LITE', 'images/category-bg.png', 0, '1 wrap of Freshly Pounded Yam with 1 Goat Meat and a Pet Coke Drink', 'POUNDED YAM, GOAT MEAT, EFO RIRO, EGUSI, FOOD, LITE, COMBO, DRINK'],
            ['Gbajumo Platter', '₦60,000.00', 'PLATTERS', 'images/category-bg.png', 1, 'Boli and Dundun (Fried Yam) served with Green Chili Sauce with 4 pieces of Turkey, Snail and Chicken.', 'BOLI, CHICKEN, DUNDUN, FOOD, GOAT MEAT, GUINEA FOWL, SNAIL, TURKEY'],
            ['Ade Ori Okin Platter', '₦32,000.00', 'PLATTERS', 'images/category-bg.png', 1, 'Dundun (Fried Yam) and Dodo (Plantain) served with Full Fried Fish, 2 pieces of Turkey and Snail.', 'CHILLI, FOOD, PLANTAIN, PLATTER, SPICY, YAM'],
            ['Ila Alasepo Soup Bowl(4 Litres)', '₦44,000.00', 'SOUP BOWLS', 'images/category-bg.png', 1, 'A smooth, seasoned one-pot okro delight (4 Litres).', 'ILA ALASEPO, OKRO SOUP, SOUP BOWL'],
            ['Ila Alasepo Soup Bowl(2 Litres)', '₦22,000.00', 'SOUP BOWLS', 'images/category-bg.png', 0, 'A smooth, seasoned one-pot okro delight (2 Litres).', 'ILA ALASEPO, OKRO SOUP, SOUP BOWL'],
            ['Sea Food Okro Soup Bowl(4 Litres)', '₦94,000.00', 'SOUP BOWLS', 'images/category-bg.png', 1, 'Authentic draw soup, packed with the richest assortment of seafood goodness (4 Litres).', 'SEAFOOD OKRO, OKRA SOUP, SOUP BOWL'],
            ['Sea Food Okro Soup Bowl(2 Litres)', '₦50,000.00', 'SOUP BOWLS', 'images/category-bg.png', 0, 'Authentic draw soup, packed with the richest assortment of seafood goodness (2 Litres).', 'SEAFOOD OKRO, OKRA SOUP, SOUP BOWL'],
            ['Ishapa Soup Bowl(4 Litres)', '₦33,000.00', 'SOUP BOWLS', 'images/category-bg.png', 1, 'Bold and unforgettable Ishapa soup (4 Litres).', 'ISHAPA, TRADITIONAL YORUBA SOUP, SOUP BOWL'],
            ['Ishapa Soup Bowl(2 Litres)', '₦20,000.00', 'SOUP BOWLS', 'images/category-bg.png', 0, 'Bold and unforgettable Ishapa soup (2 Litres).', 'ISHAPA, TRADITIONAL YORUBA SOUP, SOUP BOWL'],
            ['Efo Riro Soup Bowl(4 Litres)', '₦28,000.00', 'SOUP BOWLS', 'images/category-bg.png', 1, 'A rich and delicious assortment of leafy green vegetables (4 Litres).', 'EFO RIRO, VEGETABLE SOUP, SOUP BOWL'],
            ['Efo Riro Soup Bowl(2 Litres)', '₦17,000.00', 'SOUP BOWLS', 'images/category-bg.png', 0, 'A rich and delicious assortment of leafy green vegetables (2 Litres).', 'EFO RIRO, VEGETABLE SOUP, SOUP BOWL'],
            ['Egusi Soup Bowl(4 Litres)', '₦28,000.00', 'SOUP BOWLS', 'images/category-bg.png', 1, 'Rich, thick, and deeply satisfying Egusi soup (4 Litres).', 'EGUSI, MELON SEED SOUP, SOUP BOWL'],
            ['Egusi Soup Bowl(2 Litres)', '₦17,000.00', 'SOUP BOWLS', 'images/category-bg.png', 0, 'Rich, thick, and deeply satisfying Egusi soup (2 Litres).', 'EGUSI, MELON SEED SOUP, SOUP BOWL'],
            ['Fresh Juice "PineSop Fusion"', '₦3,500.00', 'DRINKS', 'images/category-bg.png', 0, 'Luxurious blend of fresh soursop and pineapple.', 'DRINKS, FRESH JUICE, SOURSOP, PINEAPPLE'],
            ['Fresh Juice "Tropical Citrus Bliss"', '₦3,500.00', 'DRINKS', 'images/category-bg.png', 0, 'Zesty blend of oranges and sweet pineapple.', 'DRINKS, FRESH JUICE, ORANGE, PINEAPPLE'],
            ['Fresh Juice "Tropical Carrot Elixir"', '₦3,500.00', 'DRINKS', 'images/category-bg.png', 0, 'Fusion of sweet pineapple and earthy carrots.', 'DRINKS, FRESH JUICE, CARROT, PINEAPPLE'],
            ['Fresh Juice "BeetBliss"', '₦3,500.00', 'DRINKS', 'images/category-bg.png', 0, 'Refreshing juice made with 100% natural beetroot and pineapple.', 'BEETROOT, DRINKS, FRESH JUICE, JUICE, PINEAPPLE'],
            ['Fresh Juice "Tropical Bliss"', '₦3,500.00', 'DRINKS', 'images/category-bg.png', 0, 'Escape to paradise with watermelon and pineapple.', 'BANANA, DRINKS, FRESH, JUICE, PINEAPPLE, WATERMELON'],
            ['Fresh Juice "MangoPine"', '₦3,500.00', 'DRINKS', 'images/category-bg.png', 0, 'Tropical twist with natural mango and pineapple.', 'FRESH JUICE, MANGO, MANGOPINE, NATURAL, PINEAPPLE'],
            ['Fresh Juice "GuavaPine"', '₦3,500.00', 'DRINKS', 'images/category-bg.png', 0, 'Tropical fusion with natural guava and pineapple.', 'DRINKS, FRESH, FRESH JUICE, GUAVA, PINEAPPLE'],
            ['Fresh Juice "Tropical Apple Bliss"', '₦3,500.00', 'DRINKS', 'images/category-bg.png', 0, 'Refreshing juice with apple and pineapple.', 'APPLE, COCONUT, DRINKS, FRESH JUICE, PINEAPPLE']
        ];

        $prodStmt = $pdo->prepare("INSERT INTO products (name, price, category, image_path, is_best_seller, description, tags) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($products as $p) {
            $prodStmt->execute($p);
        }
    }

    if (!tableExists($pdo, 'admins')) {
        $pdo->exec("CREATE TABLE admins (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            email TEXT,
            is_verified INTEGER DEFAULT 1
        )");

        // Seed default admin: admin / admin123
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO admins (username, password, email) VALUES (?, ?, ?)")
            ->execute(['admin', $hashed_password, 'admin@example.com']);
    }

    if (!tableExists($pdo, 'settings')) {
        $pdo->exec("CREATE TABLE settings (
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
        $stmt->execute(['contact_phone', '0812 345 6789']);
    }

    if (!tableExists($pdo, 'contact_messages')) {
        $pdo->exec("CREATE TABLE contact_messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            subject TEXT,
            message TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }

    if (!tableExists($pdo, 'content')) {
        $pdo->exec("CREATE TABLE content (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            section_title TEXT,
            description TEXT,
            image_path TEXT,
            nav_item_id INTEGER,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // Seed some initial content
        $content = [
            ['LUXURY DINING', 'Experience the finest traditional cuisine in a modern setting.', 'images/home_exclusive.jpg', 1],
            ['OUR STORY', 'Passionate about bringing authentic flavors to your table.', 'images/brand-portrait.jpg', 2],
            ['SIGNATURES', 'Our most celebrated dishes crafted with excellence.', 'images/menu_page.png', 3]
        ];
        $contentStmt = $pdo->prepare("INSERT INTO content (section_title, description, image_path, nav_item_id) VALUES (?, ?, ?, ?)");
        foreach ($content as $c) {
            $contentStmt->execute($c);
        }
    }

    if (!tableExists($pdo, 'navigation_items')) {
        $pdo->exec("CREATE TABLE navigation_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            label TEXT NOT NULL,
            link_url TEXT NOT NULL,
            sort_order INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            nav_type TEXT DEFAULT 'main'
        )");

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
            ['Boutique', 'dashboard.php?view=products', 5, 'admin'],
            ['Navigation', 'dashboard.php?view=nav', 6, 'admin']
        ];
        $insertStmt = $pdo->prepare("INSERT INTO navigation_items (label, link_url, sort_order, nav_type) VALUES (?, ?, ?, ?)");
        foreach ($items as $item) {
            $insertStmt->execute($item);
        }
    }

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
