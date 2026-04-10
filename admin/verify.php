<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

$error = '';
$success = '';
$email = $_SESSION['verify_email'] ?? '';

if (!$email) {
    header("Location: signup.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }

    $code = sanitize($_POST['code']);

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ? AND confirmation_code = ?");
    $stmt->execute([$email, $code]);
    $admin = $stmt->fetch();

    if ($admin) {
        $update = $pdo->prepare("UPDATE admins SET is_verified = 1, confirmation_code = NULL WHERE id = ?");
        $update->execute([$admin['id']]);
        $success = "Email verified successfully! You can now <a href='login.php'>login</a>.";
        unset($_SESSION['verify_email']);
    } else {
        $error = "Invalid verification code.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email | Modern Selling Point</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="login-wrapper">
    <div class="login-card">
        <h2>Verify Your Email</h2>
        <p style="text-align: center; font-size: 0.9rem; margin-bottom: 20px;">A verification code has been sent to <strong><?php echo htmlspecialchars($email); ?></strong></p>

        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-box" style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; font-size: 0.9rem;">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="form-group">
                <label for="code">Verification Code</label>
                <input type="text" name="code" id="code" class="form-control" placeholder="Enter 6-digit code" required autofocus maxlength="6">
            </div>

            <button type="submit" class="btn-login">Verify Code</button>
        </form>

        <div style="text-align: center; margin-top: 20px; font-size: 0.9rem;">
            Didn't receive a code? <a href="signup.php" style="color: #007bff; text-decoration: none;">Try again</a>
        </div>
    </div>
</body>
</html>
