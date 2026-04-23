<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!defined('ALLOW_REGISTRATION') || ALLOW_REGISTRATION !== true) {
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
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@800&family=Inter:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="aura-body">
    <div class="aura-portal-bg"></div>
    <main class="login-wrapper">
        <div class="login-card" style="backdrop-filter: blur(20px); background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
            <h2 style="font-family: 'Cinzel', serif; color: #fff; text-align: center; margin-bottom: 10px; font-size: 1.5rem;">Join <span style="color: var(--primary-color);">Atelier</span></h2>
            <p style="text-align: center; font-size: 0.7rem; color: #666; margin-bottom: 40px; text-transform: uppercase; letter-spacing: 2px;">Create administrator account</p>

            <?php if ($error): ?>
                <div style="background: rgba(231, 76, 60, 0.1); border: 1px solid var(--danger-color); color: var(--danger-color); padding: 15px; border-radius: 12px; margin-bottom: 30px; text-align: center; font-size: 0.9rem;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                <div style="margin-bottom: 20px;">
                    <label style="color: #666; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 10px;">Artisan Identity</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="color: #666; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 10px;">Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="color: #666; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 10px;">Security Key</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div style="margin-bottom: 30px;">
                    <label style="color: #666; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 10px;">Confirm Key</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; padding: 20px; font-size: 1rem; letter-spacing: 4px;">REGISTER</button>
            </form>

            <div style="text-align: center; margin-top: 40px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px;">
                <span style="color: #666;">Already an artisan?</span> <a href="login.php" style="color: var(--primary-color); text-decoration: none; font-weight: 700;">Login</a>
            </div>

            <div style="text-align: center; margin-top: 25px;">
                <a href="../index.php" style="color: #444; text-decoration: none; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; transition: 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#444'">&larr; Return to Gallery</a>
            </div>
        </div>
    </main>
</body>
</html>
