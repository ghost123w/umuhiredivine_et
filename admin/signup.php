<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!defined('ALLOW_REGISTRATION') || ALLOW_REGISTRATION !== true) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }

    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO admins (username, email, password, is_verified) VALUES (?, ?, ?, 1)");
            $stmt->execute([$username, $email, $hashed_password]);

            $_SESSION['admin_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit();
        } catch (PDOException $e) {
            $error = "Registration failed: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign Up | <?php echo SITE_NAME; ?></title>
    <link rel="icon" href="../images/favicon.jpg">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="login-page">
    <div class="aurora-bg"></div>

    <div class="login-bento">
        <div style="text-align: center; margin-bottom: 30px;">
            <div class="login-brand shimmer-text"><?php echo SITE_NAME; ?></div>
            <p style="color: #666; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Admin Registration</p>
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
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="modern-form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <div class="modern-form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn-modern" style="width: 100%; margin-top: 10px;">Create Account</button>
        </form>

        <div style="margin-top: 30px; text-align: center; border-top: 1px solid var(--glass-border); padding-top: 20px;">
            <a href="login.php" style="color: var(--primary-color); text-decoration: none; font-size: 0.85rem; font-weight: 600;">Already have access?</a>
            <span style="color: #444; margin: 0 10px;">•</span>
            <a href="../index.php" style="color: #666; text-decoration: none; font-size: 0.85rem;">View Website</a>
        </div>
    </div>
</body>
</html>
