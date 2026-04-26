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

    $pdo->exec("CREATE TABLE IF NOT EXISTS creative_charge (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        image_path_1 TEXT,
        image_path_2 TEXT
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        price TEXT,
        image_path TEXT
    )");

    // Seed default settings
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)");
    $stmt->execute(['selling_points_title', 'Actions']);
    $stmt->execute(['creative_charge_title', 'FOLLOW OUR CREATIVE CHARGE']);

    // Seed creative_charge if empty
    $count = $pdo->query("SELECT COUNT(*) FROM creative_charge")->fetchColumn();
    if ($count == 0) {
        for ($i=1; $i<=4; $i++) {
            $pdo->exec("INSERT INTO creative_charge (image_path_1, image_path_2) VALUES ('images/brand-portrait.jpg', 'images/brand-portrait.jpg')");
        }
    }

    // Seed products if empty
    $pCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    if ($pCount == 0) {
        for ($i=1; $i<=3; $i++) {
            $stmt = $pdo->prepare("INSERT INTO products (name, price, image_path) VALUES (?, ?, ?)");
            $stmt->execute(["Power Kit $i", "25.00", "images/power-kit-$i.png"]);
        }
    }

} catch (PDOException $e) {
    die("Database connection failed");
}
?>
