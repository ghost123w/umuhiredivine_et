<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
check_login();

// 1. Create a variable in PHP that stores the path to a user's image.
$hero_bg_path = '../images/hero-bg.png';

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

        header("Location: dashboard.php?msg=settings_updated");
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
        header("Location: dashboard.php?msg=added");
        exit();
    }

    if (isset($_POST['delete_section'])) {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM content WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: dashboard.php?msg=deleted");
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
    <style>
        .dashboard-hero {
            background-image: url('<?php echo $hero_bg_path; ?>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            padding: 40px 20px;
            color: #fff;
            text-align: center;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: inset 0 0 0 1000px rgba(0,0,0,0.5);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 200px;
        }
        .dashboard-hero h2 {
            font-size: 2.5rem;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
        }
        .sidebar-nav-admin li {
            margin-bottom: 10px;
        }
        .sidebar-nav-admin a {
            display: block;
            padding: 12px 20px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .sidebar-nav-admin a:hover, .sidebar-nav-admin a.active {
            background: rgba(255, 53, 3, 0.2);
            color: var(--primary-color);
        }
    </style>
</head>
<body class="admin-body">
    <div class="layout-wrapper">
        <header class="layout-header">
            <div class="vintage-frame">
                <h1><?php echo SITE_NAME; ?></h1>
            </div>
            <div class="user-info" style="position: absolute; right: 40px; color: #fff;">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="btn-logout" style="margin-left: 20px;">Logout</a>
            </div>
        </header>

        <aside class="layout-sidebar">
            <div class="sidebar-content">
                <h2>Admin Panel</h2>
                <div class="sidebar-line"></div>
                <nav class="sidebar-nav-admin">
                    <ul style="list-style: none; padding: 0;">
                        <li><a href="dashboard.php?view=overview" class="<?php echo $view == 'overview' ? 'active' : ''; ?>">Overview</a></li>
                        <li><a href="dashboard.php?view=settings" class="<?php echo $view == 'settings' ? 'active' : ''; ?>">General Settings</a></li>
                        <li><a href="dashboard.php?view=add" class="<?php echo $view == 'add' ? 'active' : ''; ?>">Add New Point</a></li>
                        <li><a href="dashboard.php?view=manage" class="<?php echo $view == 'manage' ? 'active' : ''; ?>">Manage Points</a></li>
                        <li style="margin-top: 40px;"><a href="../index.php" target="_blank" style="font-size: 0.8rem; opacity: 0.6;">View Website ↗</a></li>
                    </ul>
                </nav>
            </div>
        </aside>

        <main class="layout-main">
            <?php if ($view == 'overview'): ?>
                <div class="dashboard-hero">
                    <h2>Welcome to the Atelier</h2>
                <div class="vintage-frame" style="transform: scale(0.6);">
                    <span style="color: var(--primary-color); font-weight: bold; letter-spacing: 4px;">Verified Admin Access</span>
                </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                    <div class="admin-card" style="margin-bottom: 0; text-align: center; padding: 30px;">
                        <h4 style="margin: 0; color: var(--secondary-color); text-transform: uppercase; font-size: 0.8rem; letter-spacing: 2px;">Total Points</h4>
                        <p style="font-size: 3rem; font-weight: 700; margin: 10px 0; color: var(--primary-color);"><?php echo count($sections); ?></p>
                        <a href="dashboard.php?view=manage" class="btn-primary" style="padding: 8px 20px; font-size: 0.8rem;">Manage All</a>
                    </div>
                    <div class="admin-card" style="margin-bottom: 0; text-align: center; padding: 30px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                         <img src="../images/brand-portrait.jpg" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary-color); margin-bottom: 10px;" alt="Brand">
                         <h4 style="margin: 0; color: var(--dark-color);"><?php echo SITE_NAME; ?></h4>
                         <p style="font-size: 0.8rem; color: #888;">Live Brand Profile</p>
                    </div>
                </div>

                <div class="admin-card" style="margin-top: 40px;">
                    <h3>Quick Tips</h3>
                    <ul style="color: #666; font-size: 0.95rem; line-height: 1.8;">
                        <li>Use high-quality images for your selling points to maintain the luxury aesthetic.</li>
                        <li>Keep descriptions concise and punchy for better conversion.</li>
                        <li>You can change the title of the sidebar section in General Settings.</li>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['msg'])): ?>
                <div style="background: #e1f5fe; color: #0277bd; padding: 15px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #0277bd;">
                    <?php
                        if ($_GET['msg'] == 'added') echo "<strong>Success:</strong> New selling point has been added to the gallery.";
                        if ($_GET['msg'] == 'deleted') echo "<strong>Removed:</strong> The section has been successfully deleted.";
                        if ($_GET['msg'] == 'updated') echo "<strong>Refined:</strong> Your changes have been saved perfectly.";
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
                            <label>Selling Points Section Title (Sidebar Header)</label>
                            <input type="text" name="selling_points_title" class="form-control" value="<?php echo htmlspecialchars($selling_points_title); ?>" required>
                            <small style="color: #888;">This appears at the top of the sidebar on the landing page.</small>
                        </div>

                        <div class="form-group">
                            <label>CTA Button Text</label>
                            <input type="text" name="cta_text" class="form-control" value="<?php echo htmlspecialchars($cta_text); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>CTA Button Link (URL)</label>
                            <input type="text" name="cta_link" class="form-control" value="<?php echo htmlspecialchars($cta_link); ?>" required>
                            <small style="color: #888;">Enter a URL or an anchor like #features.</small>
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
                                <img src="" alt="Preview" style="max-width: 200px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
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
                        <a href="dashboard.php?view=add" class="btn-primary" style="font-size: 0.8rem; padding: 10px 20px;">+ Add New</a>
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
                                    <td colspan="3" class="text-center" style="padding: 40px; color: #999;">No selling points found. Start by adding one.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($sections as $s): ?>
                                    <tr>
                                        <td>
                                            <?php if ($s['image_path']): ?>
                                                <img src="../<?php echo htmlspecialchars($s['image_path']); ?>" alt="" style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px; border: 1px solid #eee;">
                                            <?php else: ?>
                                                <div style="width: 70px; height: 70px; background: #f9f9f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #ccc; font-size: 0.6rem; text-transform: uppercase;">No Image</div>
                                            <?php endif; ?>
                                        </td>
                                        <td><strong style="color: var(--dark-color); font-size: 1.1rem;"><?php echo htmlspecialchars($s['section_title']); ?></strong></td>
                                        <td style="white-space: nowrap; text-align: right;">
                                            <a href="edit.php?id=<?php echo $s['id']; ?>" class="btn-primary" style="padding: 8px 18px; font-size: 0.85rem; text-decoration: none; margin-right: 5px; background: var(--secondary-color);">Edit / View Text</a>
                                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this masterpiece?');" style="display: inline-block;">
                                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                                <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                                                <button type="submit" name="delete_section" class="btn-delete" style="padding: 7px 16px; font-size: 0.85rem;">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </section>
            <?php endif; ?>

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
        </main>
    </div>
</body>
</html>
