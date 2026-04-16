<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

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
        $error = "Invalid credentials. The atelier remains closed.";
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
            <p style="color: #666; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Administration Portal</p>
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
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn-modern" style="width: 100%; margin-top: 10px;">Enter Atelier</button>
        </form>

        <div style="margin-top: 30px; text-align: center; border-top: 1px solid var(--glass-border); padding-top: 20px;">
            <?php if (defined('ALLOW_REGISTRATION') && ALLOW_REGISTRATION === true): ?>
                <a href="signup.php" style="color: var(--primary-color); text-decoration: none; font-size: 0.85rem; font-weight: 600;">Request Access</a>
                <span style="color: #444; margin: 0 10px;">•</span>
            <?php endif; ?>
            <a href="../index.php" style="color: #666; text-decoration: none; font-size: 0.85rem;">View Website</a>
        </div>
    </div>
</body>
</html>
