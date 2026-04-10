<?php
require_once 'includes/db.php';
require_once 'config.php';
try {
    $stmt = $pdo->query("SELECT * FROM content ORDER BY id ASC");
    $sections = $stmt->fetchAll();
} catch (PDOException $e) {
    $sections = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="hero">
            <h1><?php echo SITE_NAME; ?></h1>
            <p>Your professional high-converting solution. Elevate your business with our proven strategies.</p>
            <a href="#features" class="cta">Explore Features</a>
        </div>
    </header>
    <main class="grid" id="features">
        <?php if (empty($sections)): ?>
            <section class="reveal-section">
                <h2>Innovation</h2>
                <p>We deliver cutting-edge technology to your business.</p>
            </section>
        <?php else: ?>
            <?php foreach ($sections as $s): ?>
                <section class="reveal-section">
                    <h2><?php echo htmlspecialchars($s['section_title']); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars($s['description'])); ?></p>
                </section>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
    <script src="js/script.js"></script>
</body>
</html>
