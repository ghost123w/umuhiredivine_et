<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if any admin exists. If not, redirect to installation/signup.
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
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Modern Selling Point</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="login-wrapper">
    <div class="login-card">
        <h2>Admin Login</h2>

        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Enter username" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>

        <?php if (defined('ALLOW_REGISTRATION') && ALLOW_REGISTRATION === true): ?>
        <div style="text-align: center; margin-top: 20px; font-size: 0.9rem;">
            Don't have an account? <a href="signup.php" style="color: #007bff; text-decoration: none; font-weight: 600;">Sign Up</a>
        </div>
        <?php endif; ?>

        <a href="../index.php" class="back-link">&larr; Back to Website</a>
    </div>
</body>
</html>
