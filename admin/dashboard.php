<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
check_login();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }

    if (isset($_POST['update_settings'])) {
        $title = sanitize($_POST['selling_points_title']);
        $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'selling_points_title'");
        $stmt->execute([$title]);

        $cta_text = sanitize($_POST['cta_text']);
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES ('cta_text', ?)");
        $stmt->execute([$cta_text]);

        $cta_link = sanitize($_POST['cta_link']);
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES ('cta_link', ?)");
        $stmt->execute([$cta_link]);

        header("Location: dashboard.php?view=settings&msg=settings_updated");
        exit();
    }

    if (isset($_POST['add_section'])) {
        $title = sanitize($_POST['section_title']);
        $desc = sanitize($_POST['description']);
        $image_path = null;

        if (isset($_FILES['section_image']) && $_FILES['section_image']['error'] == 0) {
            $target_dir = "../uploads/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);

            $file_ext = strtolower(pathinfo($_FILES["section_image"]["name"], PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($file_ext, $allowed_exts)) {
                $new_filename = uniqid() . '.' . $file_ext;
                $target_file = $target_dir . $new_filename;
                if (move_uploaded_file($_FILES["section_image"]["tmp_name"], $target_file)) {
                    $image_path = 'uploads/' . $new_filename;
                }
            }
        }

        $stmt = $pdo->prepare("INSERT INTO content (section_title, description, image_path) VALUES (?, ?, ?)");
        $stmt->execute([$title, $desc, $image_path]);
        header("Location: dashboard.php?view=manage&msg=added");
        exit();
    }

    if (isset($_POST['delete_section'])) {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM content WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: dashboard.php?view=manage&msg=deleted");
        exit();
    }
}

$sections = $pdo->query("SELECT * FROM content ORDER BY id ASC")->fetchAll();

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'selling_points_title'");
$stmt->execute();
$selling_points_title = $stmt->fetchColumn() ?: 'Actions';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'cta_text'");
$stmt->execute();
$cta_text = $stmt->fetchColumn() ?: 'Get Started';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'cta_link'");
$stmt->execute();
$cta_link = $stmt->fetchColumn() ?: '#';

$view = $_GET['view'] ?? 'overview';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | <?php echo SITE_NAME; ?></title>
    <link rel="icon" href="../images/favicon.jpg">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">
    <nav class="top-nav">
        <a href="dashboard.php" class="logo"><?php echo SITE_NAME; ?></a>
        <div class="nav-links">
            <a href="dashboard.php?view=overview" class="<?php echo $view == 'overview' ? 'active' : ''; ?>">Overview</a>
            <a href="dashboard.php?view=settings" class="<?php echo $view == 'settings' ? 'active' : ''; ?>">Settings</a>
            <a href="dashboard.php?view=add" class="<?php echo $view == 'add' ? 'active' : ''; ?>">Create</a>
            <a href="dashboard.php?view=manage" class="<?php echo $view == 'manage' ? 'active' : ''; ?>">Manage</a>
        </div>
        <div class="user-meta">
            <span>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="btn-signout">SIGN OUT</a>
        </div>
    </nav>

    <main class="content-wrapper">
        <?php if ($view == 'overview'): ?>
            <div class="bento-grid">
                <div class="bento-card card-welcome">
                    <h2>Welcome back, Artisan</h2>
                    <p>The atelier is ready for your next creation.</p>
                    <div class="status-indicator">
                        <span class="dot"></span>
                        Verified Session Active
                    </div>
                </div>

                <div class="bento-card card-image">
                    <img src="../images/brand-portrait.jpg" alt="Brand Essence">
                    <div class="image-overlay">
                        <h3><?php echo SITE_NAME; ?></h3>
                        <p>Brand Essence</p>
                    </div>
                </div>

                <div class="bento-card card-stat">
                    <h4>Collections</h4>
                    <div class="stat-value"><?php echo count($sections); ?></div>
                    <div class="stat-label">Active Points</div>
                </div>

                <a href="dashboard.php?view=add" class="bento-card card-create">
                    <div class="icon-plus">+</div>
                    <h3>Create New</h3>
                    <p>Expand your horizon</p>
                </a>

                <div class="bento-card card-tip">
                    <h3>Atelier Tip</h3>
                    <p>Visual harmony is achieved through high-contrast imagery and minimalist descriptions. Let the brand breathe.</p>
                </div>
            </div>
        <?php endif; ?>

        <div style="max-width: 1000px; margin: 0 auto; padding: 0 40px;">
            <?php if (isset($_GET['msg'])): ?>
                <div style="background: rgba(40, 167, 69, 0.1); color: #28a745; padding: 15px; border-radius: 12px; margin-bottom: 30px; border: 1px solid rgba(40, 167, 69, 0.2);">
                    <?php
                        if ($_GET['msg'] == 'added') echo "<strong>Success:</strong> New selling point has been added.";
                        if ($_GET['msg'] == 'deleted') echo "<strong>Removed:</strong> The section has been deleted.";
                        if ($_GET['msg'] == 'updated') echo "<strong>Refined:</strong> Your changes have been saved.";
                        if ($_GET['msg'] == 'settings_updated') echo "<strong>Updated:</strong> General settings are now live.";
                    ?>
                </div>
            <?php endif; ?>

            <?php if ($view == 'settings'): ?>
                <section class="admin-card">
                    <h3>General Settings</h3>
                    <form method="POST" class="admin-form">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <div class="form-group">
                            <label>Selling Points Section Title</label>
                            <input type="text" name="selling_points_title" class="form-control" value="<?php echo htmlspecialchars($selling_points_title); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>CTA Button Text</label>
                            <input type="text" name="cta_text" class="form-control" value="<?php echo htmlspecialchars($cta_text); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>CTA Button Link (URL)</label>
                            <input type="text" name="cta_link" class="form-control" value="<?php echo htmlspecialchars($cta_link); ?>" required>
                        </div>

                        <button type="submit" name="update_settings" class="btn-primary">Save Changes</button>
                    </form>
                </section>
            <?php endif; ?>

            <?php if ($view == 'add'): ?>
                <section class="admin-card">
                    <h3>Add New Selling Point</h3>
                    <form method="POST" class="admin-form" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <div class="form-group">
                            <label>Section Title</label>
                            <input type="text" name="section_title" class="form-control" placeholder="e.g. Bespoke Tailoring" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Describe the value proposition..." required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Image (Optional)</label>
                            <input type="file" name="section_image" class="form-control" id="imageInput" accept="image/*">
                            <div id="imagePreview" style="margin-top: 15px; display: none;">
                                <img src="" alt="Preview" style="max-width: 200px; border-radius: 8px;">
                            </div>
                        </div>
                        <button type="submit" name="add_section" class="btn-primary">Publish Section</button>
                    </form>
                </section>
            <?php endif; ?>

            <?php if ($view == 'manage'): ?>
                <section class="admin-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                        <h3 style="margin: 0;">Manage Selling Points</h3>
                    </div>

                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($sections)): ?>
                                <tr>
                                    <td colspan="3" class="text-center" style="padding: 40px; color: #666;">No selling points found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($sections as $s): ?>
                                    <tr>
                                        <td>
                                            <?php if ($s['image_path']): ?>
                                                <img src="../<?php echo htmlspecialchars($s['image_path']); ?>" alt="" style="width: 70px; height: 70px; object-fit: cover; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
                                            <?php else: ?>
                                                <div style="width: 70px; height: 70px; background: #222; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #444; font-size: 0.6rem;">No Image</div>
                                            <?php endif; ?>
                                        </td>
                                        <td><strong style="color: #fff; font-size: 1rem;"><?php echo htmlspecialchars($s['section_title']); ?></strong></td>
                                        <td style="white-space: nowrap; text-align: right;">
                                            <a href="edit.php?id=<?php echo $s['id']; ?>" class="btn-primary" style="padding: 8px 18px; font-size: 0.8rem; text-decoration: none; margin-right: 5px;">Edit</a>
                                            <form method="POST" onsubmit="return confirm('Are you sure?');" style="display: inline-block;">
                                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                                <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                                                <button type="submit" name="delete_section" class="btn-delete" style="padding: 7px 16px; font-size: 0.8rem;">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </section>
            <?php endif; ?>
        </div>
    </main>

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
