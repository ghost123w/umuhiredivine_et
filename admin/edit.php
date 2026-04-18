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
    <title>Edit Aura | <?php echo SITE_NAME; ?></title>
    <link rel="icon" href="../images/favicon.jpg">
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="aura-body">
    <div class="stroll-bg-container"></div>

    <header class="layout-header">
        <h1>REFINEMENT PORTAL</h1>
    </header>

    <nav class="glass-pill">
        <ul>
            <li><a href="dashboard.php?view=manage">Return</a></li>
            <li><a href="logout.php" class="pill-cta">Exit</a></li>
        </ul>
    </nav>

    <div class="aura-container" style="max-width: 800px;">
        <div class="aura-card">
            <h2 class="aura-title">Refine Masterpiece</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="section_title" class="form-control" value="<?php echo htmlspecialchars($section['section_title']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Manifesto (Description)</label>
                    <textarea name="description" class="form-control" rows="8" required><?php echo htmlspecialchars($section['description']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Update Visual Asset</label>
                    <?php if ($section['image_path']): ?>
                        <div style="margin-bottom: 20px; position: relative; width: fit-content;">
                            <img src="../<?php echo htmlspecialchars($section['image_path']); ?>" alt="Current" style="max-width: 300px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">
                            <span style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.6); padding: 5px 10px; border-radius: 5px; font-size: 0.7rem;">CURRENT</span>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="section_image" class="form-control" id="imageInput" accept="image/*">
                    <div id="imagePreview" style="margin-top: 20px; display: none;">
                        <img src="" alt="Preview" style="max-width: 300px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                        <p style="font-size: 0.7rem; color: var(--primary-color); text-transform: uppercase; margin-top: 10px;">New Aura Fragment Preview</p>
                    </div>
                </div>
                <div style="margin-top: 40px; display: flex; gap: 20px; align-items: center;">
                    <button type="submit" class="btn-aura">Sync Refinement</button>
                    <a href="dashboard.php?view=manage" style="color: rgba(255,255,255,0.5); text-decoration: none; font-size: 0.9rem;">Discard Changes</a>
                </div>
            </form>
        </div>
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
