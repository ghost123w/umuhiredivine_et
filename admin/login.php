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
        $error = "Invalid credentials. The atelier remains closed.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | <?php echo SITE_NAME; ?></title>
    <link rel="icon" href="../images/favicon.jpg">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="login-page">
    <div class="aurora-bg"></div>

    <div class="login-bento">
        <div style="text-align: center; margin-bottom: 30px;">
            <div class="login-brand shimmer-text"><?php echo SITE_NAME; ?></div>
            <p style="color: #666; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Administration Portal</p>
        </div>

        <?php if ($error): ?>
            <div class="error-toast"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="modern-form-group">
                <label>Username</label>
                <input type="text" name="username" required autofocus>
            </div>

            <div class="modern-form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn-modern" style="width: 100%; margin-top: 10px;">Enter Atelier</button>
        </form>

        <div style="margin-top: 30px; text-align: center; border-top: 1px solid var(--glass-border); padding-top: 20px;">
            <?php if (defined('ALLOW_REGISTRATION') && ALLOW_REGISTRATION === true): ?>
                <a href="signup.php" style="color: var(--primary-color); text-decoration: none; font-size: 0.85rem; font-weight: 600;">Request Access</a>
                <span style="color: #444; margin: 0 10px;">•</span>
            <?php endif; ?>
            <a href="../index.php" style="color: #666; text-decoration: none; font-size: 0.85rem;">View Website</a>
        </div>
    </div>
</body>
</html>
