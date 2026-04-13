<?php
require_once __DIR__ . '/../config.php';
try {
    if (!is_dir(__DIR__ . '/../data')) {
        mkdir(__DIR__ . '/../data', 0777, true);
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
    $stmt->execute(['selling_points_title', 'Actions']);

} catch (PDOException $e) {
    die("Database connection failed");
}
?>
