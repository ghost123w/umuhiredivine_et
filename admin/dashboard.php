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
    <title>Aura Portal | <?php echo SITE_NAME; ?></title>
    <link rel="icon" href="../images/favicon.jpg">
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@800&family=Inter:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="aura-body">
    <div class="aura-portal-bg"></div>

    <nav class="aura-nav">
        <div class="glass-pill">
            <a href="dashboard.php?view=overview" class="<?php echo $view == 'overview' ? 'active' : ''; ?>">Portal</a>
            <a href="dashboard.php?view=settings" class="<?php echo $view == 'settings' ? 'active' : ''; ?>">Aura</a>
            <a href="dashboard.php?view=add" class="<?php echo $view == 'add' ? 'active' : ''; ?>">Create</a>
            <a href="dashboard.php?view=manage" class="<?php echo $view == 'manage' ? 'active' : ''; ?>">Manage</a>
            <a href="logout.php" style="color: var(--danger-color); border-left: 1px solid rgba(255,255,255,0.1); padding-left: 20px; margin-left: -20px;">Exit</a>
        </div>
    </nav>

    <main class="portal-container">
        <?php if ($view == 'overview'): ?>
            <header class="portal-hero">
                <h1><?php echo SITE_NAME; ?></h1>
                <p>Welcome back, Artisan <?php echo htmlspecialchars($_SESSION['username']); ?></p>
            </header>

            <div class="portal-grid">
                <section class="aura-card welcome-section">
                    <h2>Perspective</h2>
                    <p>Your digital workspace is currently vibrating at peak performance. All systems are synchronized with your creative vision.</p>

                    <div class="quick-stats">
                        <div class="stat-item">
                            <h3><?php echo count($sections); ?></h3>
                            <span>Masterpieces</span>
                        </div>
                        <div class="stat-item">
                            <h3>Active</h3>
                            <span>System Status</span>
                        </div>
                    </div>
                </section>

                <section class="aura-card" style="padding: 0; overflow: hidden;">
                    <div class="portrait-frame">
                        <img src="../images/leadership.jpg" alt="Artisan">
                        <div class="portrait-overlay">
                            <h3>Verified Administrator</h3>
                            <span style="color: var(--primary-color); font-size: 0.7rem; letter-spacing: 2px;">SECURE ACCESS SESSION</span>
                        </div>
                    </div>
                </section>

                <div class="actions-grid">
                    <a href="dashboard.php?view=add" class="action-card">
                        <div class="icon">✧</div>
                        <h4>Add Masterpiece</h4>
                        <p>Expand your digital collection with new points of light.</p>
                    </a>
                    <a href="dashboard.php?view=manage" class="action-card">
                        <div class="icon">❖</div>
                        <h4>Manage Aura</h4>
                        <p>Refine and orchestrate your existing masterpieces.</p>
                    </a>
                    <a href="dashboard.php?view=settings" class="action-card">
                        <div class="icon">⚙</div>
                        <h4>Core Config</h4>
                        <p>Adjust the foundational frequencies of your landing page.</p>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <div style="max-width: 900px; margin: 0 auto; width: 100%;">
            <?php if (isset($_GET['msg'])): ?>
                <div style="background: rgba(255, 53, 3, 0.1); border: 1px solid var(--primary-color); color: var(--primary-color); padding: 20px; border-radius: 20px; margin-bottom: 40px; text-align: center; backdrop-filter: blur(10px);">
                    <?php
                        if ($_GET['msg'] == 'added') echo "<strong>Masterpiece Added:</strong> The collection has been expanded.";
                        if ($_GET['msg'] == 'deleted') echo "<strong>Removed:</strong> The piece has been returned to the void.";
                        if ($_GET['msg'] == 'updated') echo "<strong>Refined:</strong> Your vision has been updated.";
                        if ($_GET['msg'] == 'settings_updated') echo "<strong>Synchronized:</strong> Core settings are now in harmony.";
                    ?>
                </div>
            <?php endif; ?>

            <?php if ($view == 'settings'): ?>
                <section class="aura-card">
                    <h2 style="font-family: 'Cinzel', serif; margin-bottom: 40px;">Core <span style="color: var(--primary-color);">Frequencies</span></h2>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <div class="form-group">
                            <label style="color: #666; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 2px;">Section Title</label>
                            <input type="text" name="selling_points_title" class="form-control" style="background: rgba(255,255,255,0.03); color: #fff; border-color: rgba(255,255,255,0.05); padding: 20px;" value="<?php echo htmlspecialchars($selling_points_title); ?>" required>
                        </div>
                        <div class="form-group">
                            <label style="color: #666; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 2px;">Call to Action Text</label>
                            <input type="text" name="cta_text" class="form-control" style="background: rgba(255,255,255,0.03); color: #fff; border-color: rgba(255,255,255,0.05); padding: 20px;" value="<?php echo htmlspecialchars($cta_text); ?>" required>
                        </div>
                        <div class="form-group">
                            <label style="color: #666; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 2px;">Call to Action Link</label>
                            <input type="text" name="cta_link" class="form-control" style="background: rgba(255,255,255,0.03); color: #fff; border-color: rgba(255,255,255,0.05); padding: 20px;" value="<?php echo htmlspecialchars($cta_link); ?>" required>
                        </div>
                        <button type="submit" name="update_settings" class="btn-primary" style="width: 100%; margin-top: 20px; padding: 20px; font-size: 1rem; letter-spacing: 4px;">SYNCHRONIZE</button>
                    </form>
                </section>
            <?php endif; ?>

            <?php if ($view == 'add'): ?>
                <section class="aura-card">
                    <h2 style="font-family: 'Cinzel', serif; margin-bottom: 40px;">Create <span style="color: var(--primary-color);">Masterpiece</span></h2>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <div class="form-group">
                            <label style="color: #666; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 2px;">Title</label>
                            <input type="text" name="section_title" class="form-control" style="background: rgba(255,255,255,0.03); color: #fff; border-color: rgba(255,255,255,0.05); padding: 20px;" required>
                        </div>
                        <div class="form-group">
                            <label style="color: #666; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 2px;">Description</label>
                            <textarea name="description" class="form-control" style="background: rgba(255,255,255,0.03); color: #fff; border-color: rgba(255,255,255,0.05); padding: 20px;" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label style="color: #666; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 2px;">Visual Asset</label>
                            <input type="file" name="section_image" class="form-control" style="background: rgba(255,255,255,0.03); color: #fff; border-color: rgba(255,255,255,0.05); padding: 20px;" accept="image/*">
                        </div>
                        <button type="submit" name="add_section" class="btn-primary" style="width: 100%; margin-top: 20px; padding: 20px; font-size: 1rem; letter-spacing: 4px;">MANIFEST</button>
                    </form>
                </section>
            <?php endif; ?>

            <?php if ($view == 'manage'): ?>
                <section class="aura-card">
                    <h2 style="font-family: 'Cinzel', serif; margin-bottom: 40px;">Masterpiece <span style="color: var(--primary-color);">Archive</span></h2>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="border: none; padding-bottom: 20px;">Asset</th>
                                <th style="border: none; padding-bottom: 20px;">Title</th>
                                <th style="text-align: right; border: none; padding-bottom: 20px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sections as $s): ?>
                                <tr>
                                    <td style="background: transparent;">
                                        <?php if ($s['image_path']): ?>
                                            <img src="../<?php echo htmlspecialchars($s['image_path']); ?>" style="width: 70px; height: 70px; object-fit: cover; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1);">
                                        <?php endif; ?>
                                    </td>
                                    <td style="background: transparent; vertical-align: middle;">
                                        <strong style="color: #fff; font-size: 1.1rem;"><?php echo htmlspecialchars($s['section_title']); ?></strong>
                                    </td>
                                    <td style="text-align: right; background: transparent; vertical-align: middle;">
                                        <a href="edit.php?id=<?php echo $s['id']; ?>" class="btn-primary" style="padding: 10px 25px; text-decoration: none; font-size: 0.8rem; border-radius: 100px;">REFINE</a>
                                        <form method="POST" style="display: inline-block; margin-left: 10px;">
                                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                            <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                                            <button type="submit" name="delete_section" class="btn-delete" style="padding: 10px 25px; font-size: 0.8rem; border-radius: 100px;" onclick="return confirm('Remove this piece from the archive?');">VOID</button>
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

    <footer class="aura-footer">
        &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?> &mdash; AURA PORTAL v2.0
    </footer>
</body>
</html>
