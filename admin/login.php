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
    <title>Admin Login | <?php echo SITE_NAME; ?></title>
    <link rel="icon" href="../images/favicon.jpg">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .login-wrapper {
            background: var(--admin-bg);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-card {
            background: var(--card-bg);
            padding: 40px;
            border-radius: 24px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 400px;
            border: 1px solid rgba(255, 255, 255, 0.03);
        }
        .login-card h2 {
            font-family: 'Cinzel', serif;
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 30px;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        .form-control:focus {
            border-color: var(--primary-color);
            background: rgba(255, 255, 255, 0.05);
        }
        label { color: #888; }
        .btn-login {
            background: var(--primary-color);
            color: #fff;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
        }
        .btn-login:hover {
            background: #ff5020;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 53, 3, 0.3);
        }
    </style>
</head>
<body class="login-wrapper">
    <div style="margin-bottom: 40px;">
        <h1 style="font-family: 'Cinzel', serif; color: #fff; font-size: 1.5rem; letter-spacing: 4px;"><?php echo SITE_NAME; ?></h1>
    </div>

    <div class="login-card">
        <h2>Atelier Login</h2>

        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="form-group">
                <label for="username">Artisan Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Enter username" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Security Code</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
            </div>

            <button type="submit" class="btn-login">Unlock Workspace</button>
        </form>

        <?php if (defined('ALLOW_REGISTRATION') && ALLOW_REGISTRATION === true): ?>
        <div style="text-align: center; margin-top: 20px; font-size: 0.9rem; color: #666;">
            New Artisan? <a href="signup.php" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">Apply for Access</a>
        </div>
        <?php endif; ?>

        <a href="../index.php" class="back-link" style="color: #444;">&larr; Return to Gallery</a>
    </div>
</body>
</html>
