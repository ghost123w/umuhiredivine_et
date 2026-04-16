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
    <title>Dashboard | <?php echo SITE_NAME; ?></title>
    <link rel="icon" href="../images/favicon.jpg">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body modern-layout">
    <div class="aurora-bg"></div>

    <!-- Floating Glass Navigation -->
    <nav class="glass-pill-nav">
        <div class="nav-brand"><?php echo SITE_NAME; ?></div>
        <div class="nav-links">
            <a href="dashboard.php?view=overview" class="<?php echo $view == 'overview' ? 'active' : ''; ?>">Overview</a>
            <a href="dashboard.php?view=settings" class="<?php echo $view == 'settings' ? 'active' : ''; ?>">Settings</a>
            <a href="dashboard.php?view=add" class="<?php echo $view == 'add' ? 'active' : ''; ?>">Create</a>
            <a href="dashboard.php?view=manage" class="<?php echo $view == 'manage' ? 'active' : ''; ?>">Manage</a>
        </div>
        <div class="nav-actions">
            <span class="user-badge">Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="btn-logout-minimal">Sign Out</a>
        </div>
    </nav>

    <main class="dashboard-main-modern">
        <?php if (isset($_GET['msg'])): ?>
            <div class="modern-toast">
                <?php
                    if ($_GET['msg'] == 'added') echo "✨ Masterpiece published successfully!";
                    if ($_GET['msg'] == 'deleted') echo "🗑️ Section removed from gallery.";
                    if ($_GET['msg'] == 'updated') echo "🪄 Changes applied beautifully.";
                    if ($_GET['msg'] == 'settings_updated') echo "⚙️ Global settings synchronized.";
                ?>
            </div>
        <?php endif; ?>

        <?php if ($view == 'overview'): ?>
            <header class="bento-header">
                <div class="bento-item welcome-tile">
                    <h2 class="shimmer-text">Welcome back, Artisan</h2>
                    <p>The atelier is ready for your next creation.</p>
                    <div class="status-indicator">
                        <span class="pulse-dot"></span>
                        Verified Session Active
                    </div>
                </div>
                <div class="bento-item portrait-tile">
                    <img src="../images/sidebar-portrait.jpg" alt="Brand">
                    <div class="portrait-overlay">
                        <h3><?php echo SITE_NAME; ?></h3>
                        <p>Brand Essence</p>
                    </div>
                </div>
            </header>

            <div class="bento-grid">
                <div class="bento-item stats-tile">
                    <h4>Collections</h4>
                    <span class="stat-number"><?php echo count($sections); ?></span>
                    <p>Active Points</p>
                </div>
                <div class="bento-item quick-action-tile" onclick="window.location='dashboard.php?view=add'">
                    <div class="action-icon">+</div>
                    <h4>Create New</h4>
                    <p>Expand your horizon</p>
                </div>
                <div class="bento-item tip-tile">
                    <h3>Atelier Tip</h3>
                    <p>Visual harmony is achieved through high-contrast imagery and minimalist descriptions. Let the brand breathe.</p>
                </div>
                <div class="bento-item live-tile" onclick="window.open('../index.php', '_blank')">
                    <h3>View Live</h3>
                    <div class="external-icon">↗</div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($view == 'settings'): ?>
            <div class="modern-form-container bento-item">
                <h3>General Settings</h3>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <div class="modern-form-group">
                        <label>Section Title</label>
                        <input type="text" name="selling_points_title" value="<?php echo htmlspecialchars($selling_points_title); ?>" required>
                    </div>
                    <div class="modern-form-row">
                        <div class="modern-form-group">
                            <label>CTA Button Text</label>
                            <input type="text" name="cta_text" value="<?php echo htmlspecialchars($cta_text); ?>" required>
                        </div>
                        <div class="modern-form-group">
                            <label>CTA Button Link</label>
                            <input type="text" name="cta_link" value="<?php echo htmlspecialchars($cta_link); ?>" required>
                        </div>
                    </div>
                    <button type="submit" name="update_settings" class="btn-modern">Sync Settings</button>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($view == 'add'): ?>
            <div class="modern-form-container bento-item">
                <h3>Create Selling Point</h3>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <div class="modern-form-group">
                        <label>Title</label>
                        <input type="text" name="section_title" placeholder="A name for your masterpiece..." required>
                    </div>
                    <div class="modern-form-group">
                        <label>Narrative</label>
                        <textarea name="description" rows="5" placeholder="Tell the story..." required></textarea>
                    </div>
                    <div class="modern-form-group">
                        <label>Visual Asset</label>
                        <div class="modern-file-upload">
                            <input type="file" name="section_image" id="modernImageInput" accept="image/*">
                            <div class="upload-placeholder">
                                <span>Drop image here or click to browse</span>
                            </div>
                            <div id="modernImagePreview" class="modern-preview-box" style="display: none;">
                                <img src="" alt="Preview">
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="add_section" class="btn-modern">Publish to Atelier</button>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($view == 'manage'): ?>
            <div class="modern-table-container bento-item">
                <div class="table-header">
                    <h3>Manage Assets</h3>
                    <a href="dashboard.php?view=add" class="btn-modern-pill">+ New</a>
                </div>
                <div class="modern-grid-table">
                    <?php if (empty($sections)): ?>
                        <div class="empty-state">No masterpieces found in the atelier.</div>
                    <?php else: ?>
                        <?php foreach ($sections as $s): ?>
                            <div class="modern-table-row">
                                <div class="row-image">
                                    <?php if ($s['image_path']): ?>
                                        <img src="../<?php echo htmlspecialchars($s['image_path']); ?>" alt="">
                                    <?php else: ?>
                                        <div class="placeholder-img">No Image</div>
                                    <?php endif; ?>
                                </div>
                                <div class="row-info">
                                    <strong><?php echo htmlspecialchars($s['section_title']); ?></strong>
                                </div>
                                <div class="row-actions">
                                    <a href="edit.php?id=<?php echo $s['id']; ?>" class="icon-btn-modern edit" title="Edit">✎</a>
                                    <form method="POST" onsubmit="return confirm('Remove this masterpiece?');" style="display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                        <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                                        <button type="submit" name="delete_section" class="icon-btn-modern delete" title="Delete">×</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <script src="../js/script.js"></script>
    <script>
        const mInput = document.getElementById('modernImageInput');
        if (mInput) {
            mInput.addEventListener('change', function(e) {
                const preview = document.getElementById('modernImagePreview');
                const img = preview.querySelector('img');
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(re) {
                        img.src = re.target.result;
                        preview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                } else {
                    preview.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
