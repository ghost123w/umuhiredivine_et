<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
check_login();

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: dashboard.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM content WHERE id = ?");
$stmt->execute([$id]);
$section = $stmt->fetch();

if (!$section) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }

    $title = sanitize($_POST['section_title']);
    $desc = sanitize($_POST['description']);
    $image_path = $section['image_path'];

    if (isset($_FILES['section_image']) && $_FILES['section_image']['error'] == 0) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);

        $file_ext = strtolower(pathinfo($_FILES["section_image"]["name"], PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($file_ext, $allowed_exts)) {
            $new_filename = uniqid() . '.' . $file_ext;
            $target_file = $target_dir . $new_filename;
            if (move_uploaded_file($_FILES["section_image"]["tmp_name"], $target_file)) {
                // Delete old image if exists
                if ($image_path && file_exists('../' . $image_path)) {
                    unlink('../' . $image_path);
                }
                $image_path = 'uploads/' . $new_filename;
            }
        }
    }

    $stmt = $pdo->prepare("UPDATE content SET section_title = ?, description = ?, image_path = ? WHERE id = ?");
    $stmt->execute([$title, $desc, $image_path, $id]);
    header("Location: dashboard.php?msg=updated");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Selling Point | <?php echo SITE_NAME; ?></title>
    <link rel="icon" href="../images/favicon.jpg">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">
    <div class="layout-wrapper">
        <header class="layout-header">
            <div class="vintage-frame">
                <h1><?php echo SITE_NAME; ?></h1>
            </div>
            <div class="user-info" style="color: #fff; z-index: 10;">
                <a href="logout.php" class="btn-primary" style="padding: 8px 15px; font-size: 0.8rem; background: #fff; color: var(--primary-color); animation: none;">Logout</a>
            </div>
        </header>

        <aside class="layout-sidebar">
            <div class="sidebar-content">
                <h2>Admin Panel</h2>
                <div class="sidebar-line"></div>
                <nav style="margin-top: 40px;">
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 15px;"><a href="dashboard.php" style="color: #fff; text-decoration: none; opacity: 0.8;">Back to Dashboard</a></li>
                    </ul>
                </nav>
            </div>
        </aside>

        <main class="layout-main">
            <h2 class="actions-title">Edit Selling Point</h2>

            <section class="admin-card">
                <form method="POST" class="admin-form" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <div class="form-group">
                        <label>Section Title</label>
                        <input type="text" name="section_title" class="form-control" value="<?php echo htmlspecialchars($section['section_title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="6" required><?php echo htmlspecialchars($section['description']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Image (Optional)</label>
                        <?php if ($section['image_path']): ?>
                            <div style="margin-bottom: 10px;">
                                <img src="../<?php echo htmlspecialchars($section['image_path']); ?>" alt="Current" style="max-width: 200px; border-radius: 8px;">
                                <p style="font-size: 0.8rem; color: #aaa;">Current Image</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="section_image" class="form-control" id="imageInput" accept="image/*">
                        <div id="imagePreview" style="margin-top: 15px; display: none;">
                            <img src="" alt="Preview" style="max-width: 200px; border-radius: 8px; box-shadow: 0 4px 12px rgba(255,53,3,0.2);">
                            <p style="font-size: 0.8rem; color: #aaa;">New Image Preview</p>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary">Update Selling Point</button>
                    <a href="dashboard.php" style="margin-left: 20px; color: #aaa; text-decoration: none;">Cancel</a>
                </form>
            </section>
        </main>
    </div>

    <script>
        document.getElementById('imageInput').addEventListener('change', function(event) {
            const previewContainer = document.getElementById('imagePreview');
            const previewImage = previewContainer.querySelector('img');
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
            }
        });
    </script>
</body>
</html>
