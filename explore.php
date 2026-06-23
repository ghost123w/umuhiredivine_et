<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

require_once 'includes/contact-logic.php';

// Find the Explore Nav Item ID
$stmt = $pdo->prepare("SELECT id FROM navigation_items WHERE link_url LIKE '%explore.php%' OR label = 'Explore' LIMIT 1");
$stmt->execute();
$nav_id = $stmt->fetchColumn();

// Fetch content assigned to Explore (Optional, if needed for other logic)
$fixtures = [];
if ($nav_id) {
    $stmt = $pdo->prepare("SELECT * FROM content WHERE nav_item_id = ? ORDER BY id ASC");
    $stmt->execute([$nav_id]);
    $fixtures = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore | <?php echo htmlspecialchars(SITE_NAME); ?></title>
    <link rel="icon" href="images/favicon.jpg">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@900&family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body class="explore-page">
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/hero.php'; ?>

    <main class="layout-main">
        <!-- Content removed for minimal full-screen background experience -->
    </main>

    <script src="js/script.js"></script>
</body>
</html>
