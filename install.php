<?php
session_start();
require_once 'config.php';
require_once 'includes/functions.php';

$installed = false;
$error = '';
$success = '';

if (file_exists('data/database.db')) {
    $installed = true;
}

$mode = isset($_GET['mode']) ? $_GET['mode'] : ($installed ? 'login' : 'install');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'install' || $action === 'register') {
        try {
            if (!is_dir('data')) {
                mkdir('data', 0777, true);
            }
            $pdo = new PDO('sqlite:' . DB_PATH);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                email TEXT,
                confirmation_code TEXT,
                is_verified INTEGER DEFAULT 0
            )");
            $pdo->exec("CREATE TABLE IF NOT EXISTS content (id INTEGER PRIMARY KEY AUTOINCREMENT, section_title TEXT, description TEXT, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

            $username = sanitize($_POST['username']);
            $email = sanitize($_POST['email'] ?? '');
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $verified = ($action === 'install') ? 1 : 0;
            $code = ($verified) ? null : str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            $stmt = $pdo->prepare("INSERT INTO admins (username, email, password, confirmation_code, is_verified) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $email, $password, $code, $verified]);

            if ($action === 'install') {
                $pdo->exec("INSERT INTO content (section_title, description) VALUES ('Lightning Fast', 'Our optimized code ensures your site loads in milliseconds.')");
                $pdo->exec("INSERT INTO content (section_title, description) VALUES ('SEO Ready', 'Built-in SEO best practices to help you rank higher on Google.')");

                $_SESSION['admin_id'] = $pdo->lastInsertId();
                $_SESSION['username'] = $username;
                header("Location: admin/dashboard.php");
                exit();
            } else {
                if ($code) {
                     // Log verification code for developers
                     file_put_contents(__DIR__ . '/data/last_email.txt', "To: $email\nCode: $code");
                     $_SESSION['verify_email'] = $email;
                     header("Location: admin/verify.php");
                     exit();
                }
                $success = "Account created successfully! You can now log in.";
                $mode = 'login';
            }

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Username already exists.";
            } else {
                $error = "Action failed: " . $e->getMessage();
            }
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
                if (!$admin['is_verified']) {
                    $_SESSION['verify_email'] = $admin['email'];
                    header("Location: admin/verify.php");
                    exit();
                }
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
    <title><?php
        if ($mode === 'install') echo 'Installation';
        elseif ($mode === 'register') echo 'Create Account';
        else echo 'Admin Login';
    ?> | Modern Selling Point</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .install-card { max-width: 500px; margin: 100px auto; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .mode-switch { text-align: center; margin-top: 20px; font-size: 0.9rem; color: #666; }
        .mode-switch a { color: #007bff; text-decoration: none; font-weight: 600; }
        .success-msg { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; font-size: 0.9rem; text-align: center; }
    </style>
</head>
<body class="login-wrapper">
    <div class="install-card">
        <?php if ($success): ?>
            <div class="success-msg"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($mode === 'install'): ?>
            <h2>Installation Wizard</h2>
            <p>Set up your first admin account to get started.</p>

            <?php if ($error): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="hidden" name="action" value="install">
                <div class="form-group">
                    <label>Admin Username</label>
                    <input type="text" name="username" class="form-control" placeholder="e.g. admin" required autofocus>
                </div>
                <div class="form-group">
                    <label>Admin Email</label>
                    <input type="email" name="email" class="form-control" placeholder="admin@example.com" required>
                </div>
                <div class="form-group">
                    <label>Admin Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Choose a strong password" required>
                </div>
                <button type="submit" class="btn-login" style="background: #28a745;">Install Now</button>
            </form>

        <?php elseif ($mode === 'register'): ?>
            <h2>Create Admin Account</h2>
            <p>Register a new administrator account.</p>

            <?php if ($error): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="hidden" name="action" value="register">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Choose a username" required autofocus>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Choose a password" required>
                </div>
                <button type="submit" class="btn-login" style="background: #007bff;">Register Account</button>
            </form>
            <div class="mode-switch">
                Already have an account? <a href="?mode=login">Login here</a>
            </div>

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
            <div class="mode-switch">
                Need another account? <a href="?mode=register">Create one here</a>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="index.php" class="back-link">View Website</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
