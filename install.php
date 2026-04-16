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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Cinzel:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        .login-page {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--dark-color);
        }
        .login-bento {
            width: 100%;
            max-width: 450px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.5);
            animation: bento-pop 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes bento-pop {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .error-toast {
            background: rgba(255, 77, 77, 0.1);
            border: 1px solid rgba(255, 77, 77, 0.3);
            color: #ff4d4d;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 0.85rem;
            text-align: center;
        }
        .login-brand {
            font-family: 'Cinzel', serif;
            font-weight: 800;
            color: var(--primary-color);
            font-size: 1.5rem;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }
    </style>
</head>
<body class="login-page">
    <div class="aurora-bg"></div>

    <div class="login-bento">
        <div style="text-align: center; margin-bottom: 30px;">
            <div class="login-brand shimmer-text"><?php echo SITE_NAME; ?></div>
            <p style="color: #666; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">
                <?php echo $installed ? 'Administration Portal' : 'Installation Wizard'; ?>
            </p>
        </div>

        <?php if ($error): ?>
            <div class="error-toast"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!$installed): ?>
            <p style="color: #888; text-align: center; margin-bottom: 30px; font-size: 0.9rem;">Set up the primary administrator account.</p>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="hidden" name="action" value="install">

                <div class="modern-form-group">
                    <label>Admin Username</label>
                    <input type="text" name="username" required autofocus>
                </div>

                <div class="modern-form-group">
                    <label>Admin Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="modern-form-group">
                    <label>Admin Password</label>
                    <input type="password" name="password" required>
                </div>

                <button type="submit" class="btn-modern" style="width: 100%; margin-top: 10px;">Complete Installation</button>
            </form>
        <?php else: ?>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="hidden" name="action" value="login">

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
                <a href="index.php" style="color: #666; text-decoration: none; font-size: 0.85rem;">Return to Website</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
