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

    $pdo->exec("CREATE TABLE IF NOT EXISTS content (id INTEGER PRIMARY KEY AUTOINCREMENT, section_title TEXT, description TEXT, image_path TEXT, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

    // Check if image_path exists (for existing databases)
    try {
        $pdo->query("SELECT image_path FROM content LIMIT 1");
    } catch (Exception $e) {
        $pdo->exec("ALTER TABLE content ADD COLUMN image_path TEXT");
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
            ['Home', '#hero', 0, 'main'],
            ['Explore', '#features', 1, 'main'],
            ['Features', '#features', 2, 'main'],
            ['Contact', '#', 3, 'main'],
            ['Portal', 'dashboard.php?view=overview', 0, 'admin'],
            ['Aura', 'dashboard.php?view=settings', 1, 'admin'],
            ['Create', 'dashboard.php?view=add', 2, 'admin'],
            ['Manage', 'dashboard.php?view=manage', 3, 'admin'],
            ['Inquiries', 'dashboard.php?view=messages', 4, 'admin'],
            ['Navigation', 'dashboard.php?view=nav', 5, 'admin']
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
