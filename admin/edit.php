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
    header("Location: dashboard.php?view=manage&msg=updated");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Point | <?php echo SITE_NAME; ?></title>
    <link rel="icon" href="../images/favicon.jpg">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .form-control {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        .form-control:focus {
            border-color: var(--primary-color);
            background: rgba(255, 255, 255, 0.05);
        }
        label { color: #888; font-weight: 600; margin-bottom: 10px; display: block; }
    </style>
</head>
<body class="admin-body">
    <nav class="top-nav">
        <a href="dashboard.php" class="logo"><?php echo SITE_NAME; ?></a>
        <div class="nav-links">
            <a href="dashboard.php?view=overview">Overview</a>
            <a href="dashboard.php?view=settings">Settings</a>
            <a href="dashboard.php?view=add">Create</a>
            <a href="dashboard.php?view=manage" class="active">Manage</a>
        </div>
        <div class="user-meta">
            <span>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="btn-signout">SIGN OUT</a>
        </div>
    </nav>

    <main style="max-width: 800px; margin: 40px auto; padding: 0 40px;">
        <h2 style="font-family: 'Cinzel', serif; color: var(--primary-color); margin-bottom: 30px;">Edit Selling Point</h2>

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
                        <div style="margin-bottom: 20px;">
                            <img src="../<?php echo htmlspecialchars($section['image_path']); ?>" alt="Current" style="max-width: 100%; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">
                            <p style="font-size: 0.8rem; color: #666; margin-top: 10px;">Current Masterpiece</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="section_image" class="form-control" id="imageInput" accept="image/*">
                    <div id="imagePreview" style="margin-top: 20px; display: none;">
                        <img src="" alt="Preview" style="max-width: 100%; border-radius: 12px; border: 1px solid var(--primary-color);">
                        <p style="font-size: 0.8rem; color: #666; margin-top: 10px;">New Vision Preview</p>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 20px; margin-top: 40px;">
                    <button type="submit" class="btn-primary">Update Point</button>
                    <a href="dashboard.php?view=manage" style="color: #666; text-decoration: none; font-size: 0.9rem;">Cancel and return</a>
                </div>
            </form>
        </section>
    </main>

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
