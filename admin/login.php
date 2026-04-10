<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) die("CSRF failed");
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
    } else { $error = "Invalid credentials"; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Admin Login</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
    <div style="max-width: 400px; margin: 100px auto; padding: 20px; background: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2>Admin Login</h2>
        <?php if ($error): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="text" name="username" placeholder="Username" required style="width:100%; padding:10px; margin:10px 0;">
            <input type="password" name="password" placeholder="Password" required style="width:100%; padding:10px; margin:10px 0;">
            <button type="submit" style="width:100%; padding:10px; background:#333; color:#fff; border:none; cursor:pointer;">Login</button>
        </form>
    </div>
</body>
</html>
