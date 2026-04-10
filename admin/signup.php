<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

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
            $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            $stmt = $pdo->prepare("INSERT INTO admins (username, email, password, confirmation_code, is_verified) VALUES (?, ?, ?, ?, 0)");
            $stmt->execute([$username, $email, $hashed_password, $code]);

            // Mock email sending
            error_log("Verification code for $email: $code");
            file_put_contents(__DIR__ . '/../data/last_email.txt', "To: $email\nCode: $code");

            $_SESSION['verify_email'] = $email;
            header("Location: verify.php");
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Username or email already exists.";
            } else {
                $error = "Registration failed: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign Up | Modern Selling Point</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="login-wrapper">
    <div class="login-card">
        <h2>Admin Sign Up</h2>

        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Choose a username" required autofocus>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Choose a password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm your password" required>
            </div>

            <button type="submit" class="btn-login">Sign Up</button>
        </form>

        <div style="text-align: center; margin-top: 20px; font-size: 0.9rem;">
            Already have an account? <a href="login.php" style="color: #007bff; text-decoration: none; font-weight: 600;">Login</a>
        </div>

        <a href="../index.php" class="back-link">&larr; Back to Website</a>
    </div>
</body>
</html>
