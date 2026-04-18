<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
    if ($stmt->fetchColumn() == 0) {
        header("Location: ../install.php");
        exit();
    }
} catch (Exception $e) {
    header("Location: ../install.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }

    $username = sanitize($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['username'] = $admin['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid credentials. Aura access denied.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aura Access | <?php echo SITE_NAME; ?></title>
    <link rel="icon" href="../images/favicon.jpg">
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="aura-body" style="display: flex; align-items: center; justify-content: center;">
    <div class="stroll-bg-container"></div>

    <div class="aura-card" style="width: 100%; max-width: 400px;">
        <h2 class="aura-title" style="text-align: center; margin-bottom: 40px;">System Authentication</h2>

        <?php if ($error): ?>
            <div style="background: rgba(231, 76, 60, 0.1); border: 1px solid #e74c3c; color: #e74c3c; padding: 15px; border-radius: 12px; margin-bottom: 30px; font-size: 0.85rem; text-align: center;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="form-group">
                <label>Identifier</label>
                <input type="text" name="username" class="form-control" placeholder="Admin username" required autofocus>
            </div>

            <div class="form-group">
                <label>Key</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-aura" style="width: 100%;">Initialize Session</button>
        </form>

        <div style="text-align: center; margin-top: 30px;">
            <a href="../index.php" style="color: rgba(255,255,255,0.4); text-decoration: none; font-size: 0.8rem; letter-spacing: 1px; text-transform: uppercase;">&larr; Return to Website</a>
        </div>
    </div>
</body>
</html>
