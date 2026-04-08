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
</head>
<body>
    <header>
        <h1>Welcome to <?php echo SITE_NAME; ?></h1>
        <p>Your high-converting solution is here.</p>
    </header>

    <main>
        <?php if (empty($sections)): ?>
            <section class="reveal-section">
                <h2>Our Mission</h2>
                <p>We provide the best services to help you grow your business.</p>
            </section>
        <?php else: ?>
            <?php foreach ($sections as $section): ?>
                <section class="reveal-section">
                    <h2><?php echo htmlspecialchars($section['section_title']); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars($section['description'])); ?></p>
                </section>
            <?php endforeach; ?>
        <?php endif; ?>

        <section class="reveal-section" style="text-align: center;">
            <h2>Ready to start?</h2>
            <a href="#" class="cta">Get Started Now</a>
        </section>
    </main>

    <script src="js/script.js"></script>
</body>
</html>
