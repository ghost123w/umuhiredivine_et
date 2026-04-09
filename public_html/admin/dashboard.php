<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
check_login();

$message = '';

// Handle Add/Edit/Delete actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF verification
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }

    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action == 'add' || $action == 'edit') {
            $title = sanitize($_POST['section_title']);
            $desc = sanitize($_POST['description']);

            if ($action == 'add') {
                $stmt = $pdo->prepare("INSERT INTO content (section_title, description) VALUES (?, ?)");
                $stmt->execute([$title, $desc]);
                $message = "Content added successfully!";
            } elseif ($action == 'edit') {
                $id = (int)$_POST['id'];
                $stmt = $pdo->prepare("UPDATE content SET section_title = ?, description = ? WHERE id = ?");
                $stmt->execute([$title, $desc, $id]);
                $message = "Content updated successfully!";
            }
        } elseif ($action == 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM content WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Content deleted successfully!";
        }
    }
}

// Fetch all content
$stmt = $pdo->query("SELECT * FROM content ORDER BY id ASC");
$sections = $stmt->fetchAll();

// Handle edit mode fetch
$edit_section = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM content WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_section = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .container { max-width: 900px; margin: 20px auto; padding: 20px; background: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f4f4f4; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], textarea { width: 100%; padding: 10px; border: 1px solid #ccc; box-sizing: border-box; }
        .btn { padding: 8px 15px; border: none; cursor: pointer; text-decoration: none; color: white; border-radius: 4px; }
        .btn-add { background: #28a745; }
        .btn-edit { background: #ffc107; color: #000; }
        .btn-delete { background: #dc3545; }
        .nav { margin-bottom: 20px; text-align: right; }
        .success { color: green; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav">
            Logged in as <?php echo htmlspecialchars($_SESSION['username']); ?> | <a href="logout.php">Logout</a> | <a href="../index.php" target="_blank">View Site</a>
        </div>

        <h2>Manage Selling Points</h2>

        <?php if ($message): ?>
            <p class="success"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="action" value="<?php echo $edit_section ? 'edit' : 'add'; ?>">
            <?php if ($edit_section): ?>
                <input type="hidden" name="id" value="<?php echo $edit_section['id']; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Section Title</label>
                <input type="text" name="section_title" value="<?php echo $edit_section ? htmlspecialchars($edit_section['section_title']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="5" required><?php echo $edit_section ? htmlspecialchars($edit_section['description']) : ''; ?></textarea>
            </div>
            <button type="submit" class="btn btn-add"><?php echo $edit_section ? 'Update' : 'Add'; ?> Section</button>
            <?php if ($edit_section): ?>
                <a href="dashboard.php" class="btn" style="background: #6c757d;">Cancel</a>
            <?php endif; ?>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sections as $section): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($section['section_title']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($section['description'])); ?></td>
                        <td>
                            <a href="?edit=<?php echo $section['id']; ?>" class="btn btn-edit">Edit</a>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $section['id']; ?>">
                                <button type="submit" class="btn btn-delete" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
