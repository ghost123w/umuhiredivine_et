<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
check_login();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) die("CSRF failed");
    if ($_POST['action'] == 'add') {
        $stmt = $pdo->prepare("INSERT INTO content (section_title, description) VALUES (?, ?)");
        $stmt->execute([sanitize($_POST['section_title']), sanitize($_POST['description'])]);
    }
}
$sections = $pdo->query("SELECT * FROM content ORDER BY id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Dashboard</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
    <div style="max-width: 800px; margin: 20px auto; padding: 20px; background: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <header style="padding: 20px 0; background: none; color: inherit; text-align: left;">
            <h2>Manage Content</h2>
            <a href="logout.php">Logout</a>
        </header>
        <form method="POST" style="margin-bottom: 30px;">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="action" value="add">
            <input type="text" name="section_title" placeholder="Title" required style="width:100%; padding:10px; margin:10px 0;">
            <textarea name="description" placeholder="Description" required style="width:100%; padding:10px; margin:10px 0;"></textarea>
            <button type="submit" style="padding:10px 20px; background:#28a745; color:#fff; border:none; cursor:pointer;">Add Section</button>
        </form>
        <table style="width:100%; border-collapse: collapse;">
            <thead><tr><th style="border:1px solid #ddd; padding:8px;">Title</th></tr></thead>
            <tbody>
                <?php foreach($sections as $s): ?>
                    <tr><td style="border:1px solid #ddd; padding:8px;"><?php echo htmlspecialchars($s['section_title']); ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
