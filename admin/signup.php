<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

$stmt = $pdo->query("SELECT COUNT(*) FROM admins");
$admin_count = $stmt->fetchColumn();

if ($admin_count > 0 && (!defined('ALLOW_REGISTRATION') || ALLOW_REGISTRATION !== true)) {
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
<body class="login-wrapper">
    <div class="login-card">
        <h2>Admin Sign Up</h2>
        <p style="text-align: center; font-size: 0.9rem; color: #666; margin-bottom: 20px;">Create the primary administrator account.</p>

        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Choose a username" required autofocus>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Choose a password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm your password" required>
            </div>

            <button type="submit" class="btn-login">Sign Up</button>
        </form>

        <div style="text-align: center; margin-top: 20px; font-size: 0.9rem;">
            Already have an account? <a href="login.php" style="color: #007bff; text-decoration: none; font-weight: 600;">Login</a>
        </div>

        <a href="../index.php" class="back-link">&larr; Back to Website</a>
    </div>
</body>
</html>
