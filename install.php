<?php
require_once 'config.php';
require_once 'includes/functions.php';

$installed = false;
$error = '';

if (file_exists('data/database.db')) {
    $installed = true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !$installed) {
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

        $installed = true;

        // Add some default content
        $pdo->exec("INSERT INTO content (section_title, description) VALUES ('Lightning Fast', 'Our optimized code ensures your site loads in milliseconds.')");
        $pdo->exec("INSERT INTO content (section_title, description) VALUES ('SEO Ready', 'Built-in SEO best practices to help you rank higher on Google.')");

    } catch (PDOException $e) {
        $error = "Installation failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation | Modern Selling Point</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .install-card { max-width: 500px; margin: 100px auto; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .success-box { background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; text-align: center; }
    </style>
</head>
<body class="login-wrapper">
    <div class="install-card">
        <?php if ($installed): ?>
            <div class="success-box">
                <h2>Installation Complete!</h2>
                <p>The website has been successfully set up.</p>
                <div style="margin-top: 20px;">
                    <a href="admin/login.php" class="cta">Go to Admin Login</a>
                    <a href="index.php" class="back-link">View Website</a>
                </div>
            </div>
        <?php else: ?>
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
        <?php endif; ?>
    </div>
</body>
</html>
