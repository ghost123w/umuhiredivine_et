<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM content WHERE id = ?");
$stmt->execute([$id]);
$content = $stmt->fetch();

if (!$content) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($content['section_title']); ?> | <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main class="layout-main" style="margin-top: 0; min-height: 100vh; padding: 120px 60px;">
        <h1 class="luxury-heading-v3"><?php echo htmlspecialchars($content['section_title']); ?></h1>
        <div class="gold-line"></div>
        <div class="explore-description" style="max-width: 800px;">
            <?php echo nl2br(htmlspecialchars($content['description'])); ?>
        </div>
        <?php if ($content['image_path']): ?>
            <img src="<?php echo htmlspecialchars($content['image_path']); ?>" style="max-width: 100%; margin-top: 40px; border-radius: 20px;">
        <?php endif; ?>
    </main>
    <?php include 'includes/contact-modal.php'; ?>
</body>
</html>
