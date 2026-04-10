<?php
session_start();
require_once 'config.php';
require_once 'includes/functions.php';

$installed = false;
$error = '';

if (file_exists('data/database.db')) {
    $installed = true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!$installed) {
        // Handle Installation
        try {
            if (!is_dir('data')) {
                mkdir('data', 0777, true);
            }
            $pdo = new PDO('sqlite:' . DB_PATH);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $pdo->exec("CREATE TABLE IF NOT EXISTS admins (id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT NOT NULL UNIQUE, password TEXT NOT NULL)");
            $pdo->exec("CREATE TABLE IF NOT EXISTS content (id INTEGER PRIMARY KEY AUTOINCREMENT, section_title TEXT, description TEXT, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

            $username = sanitize($_POST['username']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $password]);

            // Log them in immediately after install
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];

            // Add some default content
            $pdo->exec("INSERT INTO content (section_title, description) VALUES ('Lightning Fast', 'Our optimized code ensures your site loads in milliseconds.')");
            $pdo->exec("INSERT INTO content (section_title, description) VALUES ('SEO Ready', 'Built-in SEO best practices to help you rank higher on Google.')");

            header("Location: admin/dashboard.php");
            exit();

        } catch (PDOException $e) {
            $error = "Installation failed: " . $e->getMessage();
        }
    } else {
        // Handle Login at setup page (admin landing page)
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
    <title><?php echo $installed ? 'Admin Login' : 'Installation'; ?> | Modern Selling Point</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .install-card { max-width: 500px; margin: 100px auto; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="login-wrapper">
    <div class="install-card">
        <?php if (!$installed): ?>
            <h2>Installation Wizard</h2>
            <p>Set up your admin account to get started.</p>

            <?php if ($error): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Admin Username</label>
                    <input type="text" name="username" class="form-control" placeholder="e.g. admin" required autofocus>
                </div>
                <div class="form-group">
                    <label>Admin Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Choose a strong password" required>
                </div>
                <button type="submit" class="btn-login" style="background: #28a745;">Install Now</button>
            </form>
        <?php else: ?>
            <h2>Admin Login</h2>
            <p>Access your dashboard to manage selling points.</p>

            <?php if ($error): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
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
