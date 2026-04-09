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
            <p>Elevate your business with our proven high-converting strategies and modern digital solutions.</p>
            <a href="#start" class="cta">Get Started Today</a>
        </div>
    </header>

    <main id="start">
        <?php if (empty($sections)): ?>
            <section class="reveal-section">
                <h2>Excellence Guaranteed</h2>
                <p>Our mission is to empower businesses through innovative technology and creative strategy. We focus on results that drive growth.</p>
            </section>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($sections as $section): ?>
                    <section class="reveal-section">
                        <h2><?php echo htmlspecialchars($section['section_title']); ?></h2>
                        <p><?php echo nl2br(htmlspecialchars($section['description'])); ?></p>
                    </section>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <section class="reveal-section" style="text-align: center; background: none; box-shadow: none;">
            <h2>Ready to transform your business?</h2>
            <p style="margin-bottom: 2rem;">Join hundreds of satisfied clients who have scaled their operations with our help.</p>
            <a href="#" class="cta">Contact Our Experts</a>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
