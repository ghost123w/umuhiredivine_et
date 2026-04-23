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

    <div class="aura-dashboard-wrapper">
        <aside class="aura-sidebar">
            <div class="brand"><?php echo SITE_NAME; ?></div>
            <nav class="sidebar-nav">
                <a href="dashboard.php?view=overview" class="<?php echo $view == 'overview' ? 'active' : ''; ?>">Portal</a>
                <a href="dashboard.php?view=settings" class="<?php echo $view == 'settings' ? 'active' : ''; ?>">Aura</a>
                <a href="dashboard.php?view=add" class="<?php echo $view == 'add' ? 'active' : ''; ?>">Create</a>
                <a href="dashboard.php?view=manage" class="<?php echo $view == 'manage' ? 'active' : ''; ?>">Manage</a>
                <a href="logout.php" class="sidebar-logout">Exit</a>
            </nav>
        </aside>

        <div class="aura-main-content">
            <div class="aura-dashboard-frame">
                <main class="portal-container">
        <?php if ($view == 'overview'): ?>
            <header class="portal-hero redesigned">
                <h1><?php echo SITE_NAME; ?></h1>
                <p>ORCHESTRATING THE DIGITAL EXPERIENCE</p>
            </header>

            <div class="stats-perspective-grid">
                <section class="aura-card stats-card">
                    <h2 class="section-label">SYSTEM STATUS</h2>
                    <div class="stats-values-container">
                        <div>
                            <div class="stat-value"><?php echo count($sections); ?></div>
                            <div class="stat-label">Active Assets</div>
                        </div>
                        <div>
                            <div class="stat-value white">99<span class="stat-fraction">.9</span></div>
                            <div class="stat-label">Stability Index</div>
                        </div>
                    </div>
                </section>

                <section class="aura-card portrait-container">
                    <div class="portrait-frame">
                        <img src="../images/leadership.jpg" alt="Artisan">
                        <div class="portrait-info">
                            <h3 class="artisan-name"><?php echo htmlspecialchars($_SESSION['username']); ?></h3>
                            <span class="artisan-badge">AUTHENTICATED ARTISAN</span>
                        </div>
                    </div>
                </section>
            </div>

            <div class="actions-redesigned-grid">
                <a href="dashboard.php?view=add" class="action-card redesigned">
                    <div class="icon">✧</div>
                    <h4>CREATE</h4>
                    <p>Manifest a new masterpiece in your digital collection.</p>
                </a>
                <a href="dashboard.php?view=manage" class="action-card redesigned">
                    <div class="icon">❖</div>
                    <h4>ORCHESTRATE</h4>
                    <p>Refine and manage your existing creative assets.</p>
                </a>
                <a href="dashboard.php?view=settings" class="action-card redesigned">
                    <div class="icon">⚙</div>
                    <h4>CONFIG</h4>
                    <p>Adjust the foundational frequencies of the portal.</p>
                </a>
            </div>
        <?php endif; ?>

        <div class="admin-view-container">
            <?php if (isset($_GET['msg'])): ?>
                <div class="admin-alert">
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
                    <h2 class="admin-view-title">Core <span>Frequencies</span></h2>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <div class="form-group">
                            <label class="form-label">Section Title</label>
                            <input type="text" name="selling_points_title" class="form-control aura" value="<?php echo htmlspecialchars($selling_points_title); ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Call to Action Text</label>
                            <input type="text" name="cta_text" class="form-control aura" value="<?php echo htmlspecialchars($cta_text); ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Call to Action Link</label>
                            <input type="text" name="cta_link" class="form-control aura" value="<?php echo htmlspecialchars($cta_link); ?>" required>
                        </div>
                        <button type="submit" name="update_settings" class="btn-primary btn-block">SYNCHRONIZE</button>
                    </form>
                </section>
            <?php endif; ?>

            <?php if ($view == 'add'): ?>
                <section class="aura-card">
                    <h2 class="admin-view-title">Create <span>Masterpiece</span></h2>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <div class="form-group">
                            <label class="form-label">Title</label>
                            <input type="text" name="section_title" class="form-control aura" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control aura" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Visual Asset</label>
                            <input type="file" name="section_image" class="form-control aura" id="imageInput" accept="image/*">
                        </div>
                        <div id="imagePreview" class="image-preview-wrapper">
                            <div class="image-preview-frame">
                                <img src="" alt="Preview">
                            </div>
                            <p class="preview-caption">Vision Captured</p>
                        </div>
                        <button type="submit" name="add_section" class="btn-primary btn-block">MANIFEST</button>
                    </form>
                </section>
            <?php endif; ?>

            <?php if ($view == 'manage'): ?>
                <section class="aura-card">
                    <h2 class="admin-view-title">Masterpiece <span>Archive</span></h2>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Asset</th>
                                <th>Title</th>
                                <th class="actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($sections)): ?>
                                <tr>
                                    <td colspan="3" class="center">
                                        The archive is currently empty. Manifest your first masterpiece in the 'Create' tab.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($sections as $s): ?>
                                    <tr>
                                        <td>
                                            <?php if ($s['image_path']): ?>
                                                <img src="../<?php echo htmlspecialchars($s['image_path']); ?>" class="admin-table-thumb">
                                            <?php endif; ?>
                                        </td>
                                        <td class="middle">
                                            <strong class="admin-table-title"><?php echo htmlspecialchars($s['section_title']); ?></strong>
                                        </td>
                                        <td class="actions">
                                            <a href="edit.php?id=<?php echo $s['id']; ?>" class="btn-primary btn-refine">REFINE</a>
                                            <form method="POST" class="inline-form">
                                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                                <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                                                <button type="submit" name="delete_section" class="btn-delete btn-void" onclick="return confirm('Remove this piece from the archive?');">VOID</button>
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

                <footer class="aura-footer">
                    &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?> &mdash; AURA PORTAL v3.0
                </footer>
            </div>
        </div>
    </div>

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
