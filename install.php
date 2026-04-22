<?php
session_start();
require_once 'config.php';
require_once 'includes/functions.php';

$installed = false;
$error = '';
$success = '';

if (file_exists('data/database.db')) {
    $installed = true;
    try {
        $pdo = new PDO('sqlite:' . DB_PATH);
        $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
        if ($stmt->fetchColumn() > 0) {
            $installed = true;
        } else {
            $installed = false;
        }
    } catch (Exception $e) {
        $installed = false;
    }
}

$mode = isset($_GET['mode']) ? $_GET['mode'] : ($installed ? 'login' : 'install');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'install') {
        try {
            require_once 'includes/db.php';
            // The tables are already created by db.php

            // Enforce single admin constraint
            $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
            if ($stmt->fetchColumn() > 0) {
                header("Location: admin/login.php");
                exit();
            }

            $username = sanitize($_POST['username']);
            $email = sanitize($_POST['email'] ?? '');
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO admins (username, email, password, is_verified) VALUES (?, ?, ?, 1)");
            $stmt->execute([$username, $email, $password]);

            // Seed default content if empty
            $stmt = $pdo->query("SELECT COUNT(*) FROM content");
            if ($stmt->fetchColumn() == 0) {
                $pdo->exec("INSERT INTO content (section_title, description) VALUES ('Lightning Fast', 'Our optimized code ensures your site loads in milliseconds.')");
                $pdo->exec("INSERT INTO content (section_title, description) VALUES ('SEO Ready', 'Built-in SEO best practices to help you rank higher on Google.')");
                $pdo->exec("INSERT INTO content (section_title, description) VALUES ('Mobile First', 'Optimized for a seamless experience across all devices and screen sizes.')");
                $pdo->exec("INSERT INTO content (section_title, description) VALUES ('Secure by Design', 'Advanced security measures to protect your data and user privacy.')");
            }

            $_SESSION['admin_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $username;
            header("Location: admin/dashboard.php");
            exit();

        } catch (PDOException $e) {
            $error = "Installation failed: " . $e->getMessage();
        }
    } elseif ($action === 'login') {
        try {
            $pdo = new PDO('sqlite:' . DB_PATH);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $user = sanitize($_POST['username']);
            $pass = $_POST['password'];

            $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
            $stmt->execute([$user]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($pass, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['username'] = $admin['username'];
                header("Location: admin/dashboard.php");
                exit();
            } else {
                $error = "Invalid credentials.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $installed ? 'Admin Login' : 'Administrator Installation'; ?> | <?php echo SITE_NAME; ?></title>
    <link rel="icon" href="images/favicon.jpg">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-wrapper">
    <div class="stroll-bg-container admin-bg">
        <div class="stroll-bg-overlay"></div>
    </div>
    <div class="install-card">
        <?php if (!$installed): ?>
            <h2>Administration Installation</h2>
            <p>Welcome! Set up the primary administrator account to begin managing your website.</p>

            <?php if ($error): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="hidden" name="action" value="install">
                <div class="form-group">
                    <label>Admin Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Choose a username" required autofocus>
                </div>
                <div class="form-group">
                    <label>Admin Email</label>
                    <input type="email" name="email" class="form-control" placeholder="your@email.com" required>
                </div>
                <div class="form-group">
                    <label>Admin Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Choose a strong password" required>
                </div>
                <button type="submit" class="btn-login" style="background: #28a745;">Complete Installation</button>
            </form>
        <?php else: ?>
            <h2>Admin Login</h2>
            <p>Access your dashboard to manage selling points.</p>

            <?php if ($error): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Enter username" required autofocus>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                </div>
                <button type="submit" class="btn-login">Login</button>
            </form>
            <div style="text-align: center; margin-top: 20px;">
                <a href="index.php" class="back-link">View Website</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
