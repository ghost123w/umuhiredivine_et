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
            if (!is_dir('data')) {
                mkdir('data', 0755, true);
            }
            $pdo = new PDO('sqlite:' . DB_PATH);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                email TEXT,
                is_verified INTEGER DEFAULT 1
            )");
            $pdo->exec("CREATE TABLE IF NOT EXISTS content (id INTEGER PRIMARY KEY AUTOINCREMENT, section_title TEXT, description TEXT, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

            // Removed restriction that blocked multiple administrators during installation phase if needed
            // However, installation is usually for the FIRST admin.
            // For subsequent admins, they should use signup.php if enabled.

            $username = sanitize($_POST['username']);
            $email = sanitize($_POST['email'] ?? '');
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO admins (username, email, password, is_verified) VALUES (?, ?, ?, 1)");
            $stmt->execute([$username, $email, $password]);

            $pdo->exec("INSERT INTO content (section_title, description) VALUES ('Lightning Fast', 'Our optimized code ensures your site loads in milliseconds.')");
            $pdo->exec("INSERT INTO content (section_title, description) VALUES ('SEO Ready', 'Built-in SEO best practices to help you rank higher on Google.')");

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
    <style>
        .install-card { max-width: 500px; margin: 100px auto; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="aura-body">
    <div class="aura-portal-bg"></div>
    <div class="login-wrapper">
    <div class="install-card" style="backdrop-filter: blur(20px); background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); box-shadow: none;">
        <?php if (!$installed): ?>
            <h2>Administration Installation</h2>
            <p>Welcome! Set up the primary administrator account to begin managing your website.</p>

            <?php if ($error): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="hidden" name="action" value="install">
                <div style="margin-bottom: 20px;">
                    <label style="color: #666; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 10px;">Artisan Identity</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="color: #666; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 10px;">Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div style="margin-bottom: 30px;">
                    <label style="color: #666; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 10px;">Security Key</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; padding: 20px; font-size: 1rem; letter-spacing: 4px;">MANIFEST</button>
            </form>
        <?php else: ?>
            <h2 style="font-family: 'Cinzel', serif; color: #fff; text-align: center; margin-bottom: 10px; font-size: 1.5rem;">Access <span style="color: var(--primary-color);">Portal</span></h2>
            <p style="text-align: center; font-size: 0.7rem; color: #666; margin-bottom: 40px; text-transform: uppercase; letter-spacing: 2px;">Secured Gateway</p>

            <?php if ($error): ?>
                <div style="background: rgba(231, 76, 60, 0.1); border: 1px solid var(--danger-color); color: var(--danger-color); padding: 15px; border-radius: 12px; margin-bottom: 30px; text-align: center; font-size: 0.9rem;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="hidden" name="action" value="login">
                <div style="margin-bottom: 20px;">
                    <label style="color: #666; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 10px;">Artisan Identity</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
                <div style="margin-bottom: 30px;">
                    <label style="color: #666; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 10px;">Security Key</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; padding: 20px; font-size: 1rem; letter-spacing: 4px;">UNLOCK</button>
            </form>
            <div style="text-align: center; margin-top: 40px;">
                <a href="index.php" style="color: #444; text-decoration: none; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; transition: 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#444'">&larr; Return to Gallery</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
