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
    $nav_id = !empty($_POST['nav_item_id']) ? (int)$_POST['nav_item_id'] : null;
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

    $stmt = $pdo->prepare("UPDATE content SET section_title = ?, description = ?, image_path = ?, nav_item_id = ? WHERE id = ?");
    $stmt->execute([$title, $desc, $image_path, $nav_id, $id]);
    header("Location: dashboard.php?view=manage&msg=updated");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refine Masterpiece | <?php echo SITE_NAME; ?></title>
    <link rel="icon" href="../images/favicon.jpg">
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@800&family=Inter:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="aura-body">
    <div class="aura-portal-bg"></div>

    <nav class="aura-nav">
        <div class="glass-pill">
            <a href="dashboard.php?view=overview">Portal</a>
            <a href="dashboard.php?view=settings">Aura</a>
            <a href="dashboard.php?view=add">Create</a>
            <a href="dashboard.php?view=manage" class="active">Manage</a>
            <a href="logout.php" style="color: var(--danger-color); border-left: 1px solid rgba(255,255,255,0.1); padding-left: 20px; margin-left: -20px;">Exit</a>
        </div>
    </nav>

    <main class="portal-container" style="max-width: 900px;">
        <header style="margin-bottom: 60px; text-align: center;">
            <h1 style="font-family: 'Cinzel', serif; font-size: 3rem; letter-spacing: 0.1em; text-transform: uppercase; margin: 0;">
                Refine <span style="color: var(--primary-color);">Masterpiece</span>
            </h1>
            <p style="color: #666; font-size: 0.9rem; margin-top: 10px; text-transform: uppercase; letter-spacing: 2px;">Elevate your vision to perfection.</p>
        </header>

        <section class="aura-card">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                <?php
                    $navOptions = $pdo->query("SELECT id, label FROM navigation_items WHERE nav_type = 'main' AND is_active = 1 ORDER BY sort_order ASC")->fetchAll();
                ?>
                <div style="margin-bottom: 30px;">
                    <label style="color: #666; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 2px; display: block; margin-bottom: 10px;">Section / Destination</label>
                    <select name="nav_item_id" class="form-control">
                        <option value="">Primary Landing Page</option>
                        <?php foreach ($navOptions as $opt): ?>
                            <option value="<?php echo $opt['id']; ?>" <?php echo $section['nav_item_id'] == $opt['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($opt['label']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-bottom: 30px;">
                    <label style="color: #666; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 2px; display: block; margin-bottom: 10px;">Title</label>
                    <input type="text" name="section_title" class="form-control" value="<?php echo htmlspecialchars($section['section_title']); ?>" required>
                </div>

                <div style="margin-bottom: 30px;">
                    <label style="color: #666; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 2px; display: block; margin-bottom: 10px;">Description</label>
                    <textarea name="description" class="form-control" rows="6" required><?php echo htmlspecialchars($section['description']); ?></textarea>
                </div>

                <div style="margin-bottom: 30px;">
                    <label style="color: #666; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 2px; display: block; margin-bottom: 10px;">Visual Asset</label>

                    <?php if ($section['image_path']): ?>
                        <div style="margin-bottom: 25px; position: relative; border-radius: 20px; overflow: hidden; border: 1px solid rgba(255,255,255,0.1);">
                            <img src="../<?php echo htmlspecialchars($section['image_path']); ?>" alt="Current" style="width: 100%; display: block; opacity: 0.6;">
                            <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.3);">
                                <span style="color: #fff; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; background: rgba(0,0,0,0.6); padding: 8px 15px; border-radius: 50px;">Current Inspiration</span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <input type="file" name="section_image" class="form-control" id="imageInput" accept="image/*">

                    <div id="imagePreview" style="margin-top: 25px; display: none;">
                        <div style="border-radius: 20px; overflow: hidden; border: 2px solid var(--primary-color);">
                            <img src="" alt="Preview" style="width: 100%; display: block;">
                        </div>
                        <p style="font-size: 0.7rem; color: var(--primary-color); margin-top: 10px; text-transform: uppercase; letter-spacing: 2px; text-align: center;">New Vision Captured</p>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 30px; margin-top: 50px;">
                    <button type="submit" class="btn-primary" style="flex: 1; padding: 20px; font-size: 1rem; letter-spacing: 4px;">SYNCHRONIZE</button>
                    <a href="dashboard.php?view=manage" style="color: #666; text-decoration: none; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; transition: 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#666'">Discard changes</a>
                </div>
            </form>
        </section>
    </main>

    <footer style="text-align: center; padding: 60px; color: #444; font-size: 0.8rem; letter-spacing: 1px;">
        &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?> &mdash; AURA PORTAL v2.0
    </footer>

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
