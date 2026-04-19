<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);

    if ($name && $email && $subject && $message) {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message]);
            header("Location: index.php?msg=sent#contact");
            exit();
        } catch (PDOException $e) {
            header("Location: index.php?msg=error#contact");
            exit();
        }
    } else {
        header("Location: index.php?msg=missing#contact");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
