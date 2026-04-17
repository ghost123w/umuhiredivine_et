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
    <title>Immersive Dashboard | <?php echo SITE_NAME; ?></title>
    <link rel="icon" href="../images/favicon.jpg">
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@800&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
    <div class="artisan-layout">
        <aside class="artisan-sidebar">
            <div class="brand">
                <?php echo SITE_NAME; ?>
            </div>
            <nav class="artisan-nav">
                <a href="dashboard.php?view=overview" class="<?php echo $view == 'overview' ? 'active' : ''; ?>">Overview</a>
                <a href="dashboard.php?view=settings" class="<?php echo $view == 'settings' ? 'active' : ''; ?>">Settings</a>
                <a href="dashboard.php?view=add" class="<?php echo $view == 'add' ? 'active' : ''; ?>">Create</a>
                <a href="dashboard.php?view=manage" class="<?php echo $view == 'manage' ? 'active' : ''; ?>">Manage</a>
            </nav>
            <div style="margin-top: auto; padding: 0 20px;">
                <a href="logout.php" class="btn-signout" style="display: block; text-align: center;">SIGN OUT</a>
            </div>
        </aside>

        <main class="artisan-main">
            <?php if ($view == 'overview'): ?>
                <section class="immersive-hero">
                    <div class="hero-text">
                        <h1>Atelier</h1>
                        <span>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <p style="color: #666; margin-top: 40px; font-size: 1.1rem; max-width: 400px;">
                            Your professional workspace is optimized for elegance and performance.
                            Ready to refine your digital legacy?
                        </p>
                    </div>
                    <div class="hero-image-frame">
                        <img src="../images/leadership.jpg" alt="Artisan Workspace">
                    </div>
                </section>

                <div class="module-grid">
                    <div class="module-card">
                        <h3 style="color: var(--primary-color); font-family: 'Cinzel'; margin-top:0;">Statistics</h3>
                        <div style="font-size: 3.5rem; font-weight: 800;"><?php echo count($sections); ?></div>
                        <p style="color: #888;">Active Selling Points</p>
                    </div>
                    <div class="module-card">
                        <h3 style="color: var(--primary-color); font-family: 'Cinzel'; margin-top:0;">System</h3>
                        <p style="color: #fff; font-weight: 700;">Status: Optimal</p>
                        <p style="color: #888; font-size: 0.9rem;">Verified session active for administrator.</p>
                    </div>
                    <div class="module-card" style="display: flex; align-items: center; justify-content: center; background: rgba(255, 53, 3, 0.05);">
                        <a href="dashboard.php?view=add" style="text-decoration: none; text-align: center;">
                            <div style="font-size: 3rem; color: var(--primary-color);">+</div>
                            <span style="color: #fff; text-transform: uppercase; font-weight: 700; letter-spacing: 2px;">New Point</span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <div style="max-width: 1000px; margin-top: 40px;">
                <?php if (isset($_GET['msg'])): ?>
                    <div style="background: rgba(40, 167, 69, 0.1); color: #28a745; padding: 20px; border-radius: 20px; margin-bottom: 40px; border: 1px solid rgba(40, 167, 69, 0.2);">
                        <?php
                            if ($_GET['msg'] == 'added') echo "<strong>Success:</strong> Section published to the artisan collection.";
                            if ($_GET['msg'] == 'deleted') echo "<strong>Removed:</strong> Section successfully archived.";
                            if ($_GET['msg'] == 'updated') echo "<strong>Refined:</strong> Your masterpiece has been updated.";
                            if ($_GET['msg'] == 'settings_updated') echo "<strong>Updated:</strong> Core settings reconfigured.";
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ($view == 'settings'): ?>
                    <section class="module-card">
                        <h3 style="margin-top:0; font-family: 'Cinzel'; color: var(--primary-color);">General Settings</h3>
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <div class="form-group">
                                <label style="color: #888;">Section Title</label>
                                <input type="text" name="selling_points_title" class="form-control" style="background: rgba(0,0,0,0.3); color: #fff; border-color: rgba(255,255,255,0.1);" value="<?php echo htmlspecialchars($selling_points_title); ?>" required>
                            </div>
                            <div class="form-group">
                                <label style="color: #888;">CTA Button Text</label>
                                <input type="text" name="cta_text" class="form-control" style="background: rgba(0,0,0,0.3); color: #fff; border-color: rgba(255,255,255,0.1);" value="<?php echo htmlspecialchars($cta_text); ?>" required>
                            </div>
                            <div class="form-group">
                                <label style="color: #888;">CTA Button Link</label>
                                <input type="text" name="cta_link" class="form-control" style="background: rgba(0,0,0,0.3); color: #fff; border-color: rgba(255,255,255,0.1);" value="<?php echo htmlspecialchars($cta_link); ?>" required>
                            </div>
                            <button type="submit" name="update_settings" class="btn-primary" style="margin-top: 20px;">Commit Changes</button>
                        </form>
                    </section>
                <?php endif; ?>

                <?php if ($view == 'add'): ?>
                    <section class="module-card">
                        <h3 style="margin-top:0; font-family: 'Cinzel'; color: var(--primary-color);">Create New Point</h3>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <div class="form-group">
                                <label style="color: #888;">Title</label>
                                <input type="text" name="section_title" class="form-control" style="background: rgba(0,0,0,0.3); color: #fff; border-color: rgba(255,255,255,0.1);" placeholder="..." required>
                            </div>
                            <div class="form-group">
                                <label style="color: #888;">Description</label>
                                <textarea name="description" class="form-control" style="background: rgba(0,0,0,0.3); color: #fff; border-color: rgba(255,255,255,0.1);" rows="4" required></textarea>
                            </div>
                            <div class="form-group">
                                <label style="color: #888;">Image</label>
                                <input type="file" name="section_image" class="form-control" style="background: rgba(0,0,0,0.3); color: #fff; border-color: rgba(255,255,255,0.1);" accept="image/*">
                            </div>
                            <button type="submit" name="add_section" class="btn-primary" style="margin-top: 20px;">Publish to Collection</button>
                        </form>
                    </section>
                <?php endif; ?>

                <?php if ($view == 'manage'): ?>
                    <section class="module-card">
                        <h3 style="margin-top:0; font-family: 'Cinzel'; color: var(--primary-color);">Collection Management</h3>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th style="color: #666; font-size: 0.7rem;">Image</th>
                                    <th style="color: #666; font-size: 0.7rem;">Title</th>
                                    <th style="text-align: right; color: #666; font-size: 0.7rem;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sections as $s): ?>
                                    <tr>
                                        <td>
                                            <?php if ($s['image_path']): ?>
                                                <img src="../<?php echo htmlspecialchars($s['image_path']); ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 15px; border: 1px solid rgba(255,53,3,0.2);">
                                            <?php endif; ?>
                                        </td>
                                        <td><strong style="color: #fff;"><?php echo htmlspecialchars($s['section_title']); ?></strong></td>
                                        <td style="text-align: right;">
                                            <a href="edit.php?id=<?php echo $s['id']; ?>" class="btn-primary" style="padding: 8px 15px; font-size: 0.7rem; text-decoration: none;">Refine</a>
                                            <form method="POST" style="display: inline-block;">
                                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                                <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                                                <button type="submit" name="delete_section" class="btn-delete" style="padding: 8px 15px; font-size: 0.7rem;" onclick="return confirm('Are you sure you want to archive this masterpiece?');">Archive</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </section>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
