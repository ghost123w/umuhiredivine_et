<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';


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
        $error = "Invalid credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unlock Atelier | <?php echo SITE_NAME; ?></title>
    <link rel="icon" href="../images/favicon.jpg">
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@800&family=Inter:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="aura-body">
    <div class="aura-portal-bg"></div>

    <main class="login-wrapper">
        <div style="text-align: center; margin-bottom: 40px;">
             <h1 class="shimmer-text" style="font-family: 'Cinzel', serif; font-size: 2.5rem; letter-spacing: 10px; margin: 0;"><?php echo SITE_NAME; ?></h1>
             <p style="color: var(--primary-color); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 4px; margin-top: 10px;">Security Gateway</p>
        </div>

        <div class="login-card" style="backdrop-filter: blur(20px); background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
            <h2 style="font-family: 'Cinzel', serif; color: #fff; text-align: center; margin-bottom: 40px; font-size: 1.5rem;">Access <span style="color: var(--primary-color);">Portal</span></h2>

            <?php if ($error): ?>
                <div style="background: rgba(231, 76, 60, 0.1); border: 1px solid var(--danger-color); color: var(--danger-color); padding: 15px; border-radius: 12px; margin-bottom: 30px; text-align: center; font-size: 0.9rem;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                <div style="margin-bottom: 25px;">
                    <label style="color: #666; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 10px;">Artisan Identity</label>
                    <input type="text" name="username" class="form-control" style="padding: 18px;" required autofocus>
                </div>

                <div style="margin-bottom: 35px;">
                    <label style="color: #666; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 10px;">Security Key</label>
                    <input type="password" name="password" class="form-control" style="padding: 18px;" required>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; padding: 20px; font-size: 1rem; letter-spacing: 4px;">UNLOCK</button>
            </form>

            <div style="text-align: center; margin-top: 40px;">
                <p style="color: #666; font-size: 0.8rem; margin-bottom: 10px;">New administrator? <a href="signup.php" style="color: var(--primary-color); text-decoration: none;">Register here</a></p>
                <a href="../index.php" style="color: #444; text-decoration: none; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; transition: 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#444'">&larr; Return to Gallery</a>
            </div>
        </div>
    </main>
</body>
</html>
