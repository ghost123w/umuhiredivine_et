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
</head>
<body class="admin-body modern-layout">
    <div class="aurora-bg"></div>

    <nav class="glass-pill-nav">
        <div class="nav-brand shimmer-text"><?php echo SITE_NAME; ?></div>
        <div class="nav-links">
            <a href="dashboard.php?view=overview">Overview</a>
            <a href="dashboard.php?view=settings">Settings</a>
            <a href="dashboard.php?view=add">Create</a>
            <a href="dashboard.php?view=manage" class="active">Manage</a>
        </div>
        <div class="nav-actions">
            <span class="user-badge">Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="btn-logout-minimal">Sign Out</a>
        </div>
    </nav>

    <main class="dashboard-main-modern">
        <div class="bento-header">
            <div class="bento-item welcome-tile">
                <h2 class="shimmer-text">Refine Masterpiece</h2>
                <p style="color: #aaa; margin: 0;">Updating: <?php echo htmlspecialchars($section['section_title']); ?></p>
            </div>
            <div class="bento-item live-tile" onclick="window.open('../index.php', '_blank')">
                <h3>View Live</h3>
                <span class="external-icon">↗</span>
            </div>
        </div>

        <section class="bento-item modern-form-container">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                <div class="modern-form-group">
                    <label>Section Title</label>
                    <input type="text" name="section_title" value="<?php echo htmlspecialchars($section['section_title']); ?>" required>
                </div>

                <div class="modern-form-group">
                    <label>Description</label>
                    <textarea name="description" rows="6" required><?php echo htmlspecialchars($section['description']); ?></textarea>
                </div>

                <div class="modern-form-group">
                    <label>Visual Asset</label>
                    <div style="display: flex; gap: 30px; align-items: flex-start; margin-bottom: 20px;">
                        <?php if ($section['image_path']): ?>
                            <div>
                                <p style="font-size: 0.7rem; color: #888; text-transform: uppercase; margin-bottom: 5px;">Current</p>
                                <img src="../<?php echo htmlspecialchars($section['image_path']); ?>" alt="Current" style="max-width: 150px; border-radius: 12px; border: 1px solid var(--glass-border);">
                            </div>
                        <?php endif; ?>
                        <div id="imagePreview" style="display: none;">
                            <p style="font-size: 0.7rem; color: var(--primary-color); text-transform: uppercase; margin-bottom: 5px;">New Preview</p>
                            <img src="" alt="Preview" style="max-width: 150px; border-radius: 12px; border: 2px solid var(--primary-color);">
                        </div>
                    </div>
                    <div class="modern-file-upload">
                        <span>Drop image here or click to browse</span>
                        <input type="file" name="section_image" id="imageInput" accept="image/*">
                    </div>
                </div>

                <div style="margin-top: 40px; display: flex; gap: 20px;">
                    <button type="submit" class="btn-modern">Save Masterpiece</button>
                    <a href="dashboard.php?view=manage" style="color: #888; text-decoration: none; align-self: center; font-weight: 600;">Cancel Changes</a>
                </div>
            </form>
        </section>
    </main>

    <script src="../js/script.js"></script>
    <script>
        const imageInput = document.getElementById('imageInput');
        if (imageInput) {
            imageInput.addEventListener('change', function(event) {
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
        }
    </script>
</body>
</html>
