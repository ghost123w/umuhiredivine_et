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
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES ('selling_points_title', ?)");
        $stmt->execute([$title]);

        $cta_text = sanitize($_POST['cta_text']);
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES ('cta_text', ?)");
        $stmt->execute([$cta_text]);

        $cta_link = sanitize($_POST['cta_link']);
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES ('cta_link', ?)");
        $stmt->execute([$cta_link]);

        $book_us_link = sanitize($_POST['book_us_link']);
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES ('book_us_link', ?)");
        $stmt->execute([$book_us_link]);

        header("Location: dashboard.php?view=settings&msg=settings_updated");
        exit();
    }

    if (isset($_POST['add_section'])) {
        $title = sanitize($_POST['section_title']);
        $desc = sanitize($_POST['description']);
        $nav_id = !empty($_POST['nav_item_id']) ? (int)$_POST['nav_item_id'] : null;
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

        $stmt = $pdo->prepare("INSERT INTO content (section_title, description, image_path, nav_item_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $desc, $image_path, $nav_id]);
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

    if (isset($_POST['update_nav_item'])) {
        $id = (int)$_POST['id'];
        $label = sanitize($_POST['label']);
        $url = sanitize($_POST['link_url']);
        $order = (int)$_POST['sort_order'];
        $active = isset($_POST['is_active']) ? 1 : 0;
        $type = sanitize($_POST['nav_type']);

        // Handle auto-generation of section links
        if (isset($_POST['make_standalone']) && $type == 'main') {
            $url = "section.php?id=" . $id;
        }

        $stmt = $pdo->prepare("UPDATE navigation_items SET label = ?, link_url = ?, sort_order = ?, is_active = ?, nav_type = ? WHERE id = ?");
        $stmt->execute([$label, $url, $order, $active, $type, $id]);
        header("Location: dashboard.php?view=nav&msg=updated");
        exit();
    }

    if (isset($_POST['add_nav_item'])) {
        $label = sanitize($_POST['label']);
        $url = sanitize($_POST['link_url']);
        $order = (int)$_POST['sort_order'];
        $type = sanitize($_POST['nav_type']);

        $stmt = $pdo->prepare("INSERT INTO navigation_items (label, link_url, sort_order, nav_type) VALUES (?, ?, ?, ?)");
        $stmt->execute([$label, $url, $order, $type]);
        header("Location: dashboard.php?view=nav&msg=added");
        exit();
    }

    if (isset($_POST['delete_nav_item'])) {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM navigation_items WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: dashboard.php?view=nav&msg=deleted");
        exit();
    }
}

$filter_nav_id = $_GET['filter_nav'] ?? null;
if ($filter_nav_id === 'landing') {
    $sections = $pdo->query("SELECT * FROM content WHERE nav_item_id IS NULL ORDER BY id ASC")->fetchAll();
} elseif ($filter_nav_id) {
    $stmt = $pdo->prepare("SELECT * FROM content WHERE nav_item_id = ? ORDER BY id ASC");
    $stmt->execute([(int)$filter_nav_id]);
    $sections = $stmt->fetchAll();
} else {
    $sections = $pdo->query("SELECT * FROM content ORDER BY id ASC")->fetchAll();
}

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'selling_points_title'");
$stmt->execute();
$selling_points_title = $stmt->fetchColumn() ?: '';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'cta_text'");
$stmt->execute();
$cta_text = $stmt->fetchColumn() ?: 'Get Started';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'cta_link'");
$stmt->execute();
$cta_link = $stmt->fetchColumn() ?: '#';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'book_us_link'");
$stmt->execute();
$book_us_link = $stmt->fetchColumn() ?: '#';

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
    <?php include '../includes/header.php'; ?>

    <main class="portal-container">
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

            <?php if ($view == 'overview'): ?>
                <header class="portal-hero">
                    <h1><?php echo SITE_NAME; ?></h1>
                    <p>Welcome back, Artisan <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                </header>

                <div class="portal-grid">
                    <section class="aura-card welcome-section" style="position: relative;">
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
                        <div class="form-group">
                            <label style="color: #666; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 2px;">Book Us Link (Admin Nav)</label>
                            <input type="text" name="book_us_link" class="form-control" style="background: rgba(255,255,255,0.03); color: #fff; border-color: rgba(255,255,255,0.05); padding: 20px;" value="<?php echo htmlspecialchars($book_us_link); ?>" placeholder="Enter URL for the admin Book Us button" required>
                        </div>
                        <button type="submit" name="update_settings" class="btn-primary" style="width: 100%; margin-top: 20px; padding: 20px; font-size: 1rem; letter-spacing: 4px;">SYNCHRONIZE</button>
                    </form>
                </section>
            <?php endif; ?>

            <?php if ($view == 'add'):
                $navOptions = $pdo->query("SELECT id, label FROM navigation_items WHERE nav_type = 'main' AND is_active = 1 ORDER BY sort_order ASC")->fetchAll();
            ?>
                <section class="aura-card">
                    <h2 style="font-family: 'Cinzel', serif; margin-bottom: 40px;">Create <span style="color: var(--primary-color);">Masterpiece</span></h2>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <div class="form-group">
                            <label style="color: #666; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 2px;">Section / Destination</label>
                            <select name="nav_item_id" class="form-control" style="background: rgba(255,255,255,0.03); color: #fff; border-color: rgba(255,255,255,0.05); padding: 20px;">
                                <option value="">Primary Landing Page</option>
                                <?php foreach ($navOptions as $opt): ?>
                                    <option value="<?php echo $opt['id']; ?>"><?php echo htmlspecialchars($opt['label']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
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
                            <input type="file" name="section_image" class="form-control" id="imageInput" style="background: rgba(255,255,255,0.03); color: #fff; border-color: rgba(255,255,255,0.05); padding: 20px;" accept="image/*">
                        </div>
                        <div id="imagePreview" style="margin-top: 25px; display: none;">
                            <div style="border-radius: 20px; overflow: hidden; border: 2px solid var(--primary-color);">
                                <img src="" alt="Preview" style="width: 100%; display: block;">
                            </div>
                            <p style="font-size: 0.7rem; color: var(--primary-color); margin-top: 10px; text-transform: uppercase; letter-spacing: 2px; text-align: center;">Vision Captured</p>
                        </div>
                        <button type="submit" name="add_section" class="btn-primary" style="width: 100%; margin-top: 40px; padding: 20px; font-size: 1rem; letter-spacing: 4px;">MANIFEST</button>
                    </form>
                </section>
            <?php endif; ?>

            <?php if ($view == 'manage'):
                $navOptions = $pdo->query("SELECT id, label FROM navigation_items WHERE nav_type = 'main' AND is_active = 1 ORDER BY sort_order ASC")->fetchAll();
            ?>
                <section class="aura-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
                        <h2 style="font-family: 'Cinzel', serif; margin: 0;">Masterpiece <span style="color: var(--primary-color);">Archive</span></h2>
                        <form method="GET" style="display: flex; gap: 10px; align-items: center;">
                            <input type="hidden" name="view" value="manage">
                            <select name="filter_nav" class="form-control" style="padding: 10px; font-size: 0.7rem; min-width: 200px;" onchange="this.form.submit()">
                                <option value="">ALL FREQUENCIES</option>
                                <option value="landing" <?php echo $filter_nav_id === 'landing' ? 'selected' : ''; ?>>PRIMARY LANDING PAGE</option>
                                <?php foreach ($navOptions as $opt): ?>
                                    <option value="<?php echo $opt['id']; ?>" <?php echo $filter_nav_id == $opt['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars(strtoupper($opt['label'])); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>

                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="border: none; padding-bottom: 20px;">Asset</th>
                                <th style="border: none; padding-bottom: 20px;">Title & Destination</th>
                                <th style="text-align: right; border: none; padding-bottom: 20px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($sections)): ?>
                                <tr>
                                    <td colspan="3" style="text-align: center; padding: 40px; color: #666; font-style: italic;">
                                        The archive is currently empty. Manifest your first masterpiece in the 'Create' tab.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($sections as $s): ?>
                                    <tr>
                                        <td style="background: transparent;">
                                            <?php if ($s['image_path']): ?>
                                                <img src="../<?php echo htmlspecialchars($s['image_path']); ?>" style="width: 70px; height: 70px; object-fit: cover; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1);">
                                            <?php endif; ?>
                                        </td>
                                        <td style="background: transparent; vertical-align: middle;">
                                            <strong style="color: #fff; font-size: 1.1rem; display: block;"><?php echo htmlspecialchars($s['section_title']); ?></strong>
                                            <?php
                                                $dest = "Primary Landing Page";
                                                if ($s['nav_item_id']) {
                                                    $stmt = $pdo->prepare("SELECT label FROM navigation_items WHERE id = ?");
                                                    $stmt->execute([$s['nav_item_id']]);
                                                    $dest = $stmt->fetchColumn() ?: "Unknown Section";
                                                }
                                            ?>
                                            <span style="font-size: 0.6rem; color: var(--primary-color); text-transform: uppercase; letter-spacing: 1px;"><?php echo htmlspecialchars($dest); ?></span>
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
                            <?php endif; ?>
                        </tbody>
                    </table>
                </section>
            <?php endif; ?>

            <?php if ($view == 'messages'):
                $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
                $messages = $stmt->fetchAll();
            ?>
                <section class="aura-card">
                    <h2 style="font-family: 'Cinzel', serif; margin-bottom: 40px;">Manifested <span style="color: var(--primary-color);">Inquiries</span></h2>
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <?php if (empty($messages)): ?>
                            <p style="text-align: center; color: #666; font-style: italic;">No inquiries have been manifested yet.</p>
                        <?php else: ?>
                            <?php foreach ($messages as $m): ?>
                                <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); padding: 30px; border-radius: 20px;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                                        <div>
                                            <strong style="color: var(--primary-color); display: block; font-size: 1.1rem;"><?php echo htmlspecialchars($m['name']); ?></strong>
                                            <span style="color: #666; font-size: 0.8rem;"><?php echo htmlspecialchars($m['email']); ?></span>
                                        </div>
                                        <span style="color: #444; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px;"><?php echo $m['created_at']; ?></span>
                                    </div>
                                    <div style="margin-bottom: 10px;">
                                        <span style="background: rgba(255, 53, 3, 0.1); color: var(--primary-color); padding: 4px 12px; border-radius: 50px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase;"><?php echo htmlspecialchars($m['subject']); ?></span>
                                    </div>
                                    <p style="color: #ccc; margin: 0; white-space: pre-wrap;"><?php echo htmlspecialchars($m['message']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($view == 'nav'):
                $navItems = $pdo->query("SELECT * FROM navigation_items ORDER BY nav_type, sort_order ASC")->fetchAll();
            ?>
                <section class="aura-card">
                    <h2 style="font-family: 'Cinzel', serif; margin-bottom: 40px;">Orchestrate <span style="color: var(--primary-color);">Navigation</span></h2>

                    <div style="margin-bottom: 50px; padding-bottom: 30px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <h3 style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; color: #666; margin-bottom: 20px;">Add New Frequency</h3>
                        <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr 80px 100px 120px; gap: 15px; align-items: end;">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <div>
                                <label style="display:block; font-size: 0.6rem; color: #444; margin-bottom: 5px;">LABEL</label>
                                <input type="text" name="label" class="form-control" placeholder="Label" required>
                            </div>
                            <div>
                                <label style="display:block; font-size: 0.6rem; color: #444; margin-bottom: 5px;">URL / TARGET</label>
                                <input type="text" name="link_url" class="form-control" placeholder="URL" required>
                            </div>
                            <div>
                                <label style="display:block; font-size: 0.6rem; color: #444; margin-bottom: 5px;">ORDER</label>
                                <input type="number" name="sort_order" class="form-control" value="0" required>
                            </div>
                            <div>
                                <label style="display:block; font-size: 0.6rem; color: #444; margin-bottom: 5px;">TYPE</label>
                                <select name="nav_type" class="form-control">
                                    <option value="main">Main</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <button type="submit" name="add_nav_item" class="btn-primary" style="padding: 15px;">ADD</button>
                        </form>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <?php foreach ($navItems as $ni): ?>
                            <form method="POST" style="display: grid; grid-template-columns: 1fr 1.5fr 80px 100px 80px 80px 100px 80px; gap: 15px; align-items: center; background: rgba(255,255,255,0.02); padding: 15px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.05);">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <input type="hidden" name="id" value="<?php echo $ni['id']; ?>">
                                <input type="text" name="label" class="form-control" value="<?php echo htmlspecialchars($ni['label']); ?>" required>
                                <div style="position: relative;">
                                    <input type="text" name="link_url" class="form-control" value="<?php echo htmlspecialchars($ni['link_url']); ?>" required>
                                    <?php if ($ni['nav_type'] == 'main'): ?>
                                        <label style="font-size: 0.5rem; color: var(--primary-color); display: block; margin-top: 5px;">
                                            <input type="checkbox" name="make_standalone"> Link to Section Page
                                        </label>
                                    <?php endif; ?>
                                </div>
                                <input type="number" name="sort_order" class="form-control" value="<?php echo $ni['sort_order']; ?>" required>
                                <select name="nav_type" class="form-control">
                                    <option value="main" <?php echo $ni['nav_type'] == 'main' ? 'selected' : ''; ?>>Main</option>
                                    <option value="admin" <?php echo $ni['nav_type'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                </select>
                                <label style="font-size: 0.6rem; color: #666; text-align: center;">
                                    ACTIVE<br>
                                    <input type="checkbox" name="is_active" <?php echo $ni['is_active'] ? 'checked' : ''; ?>>
                                </label>
                                <button type="submit" name="update_nav_item" class="btn-primary" style="padding: 10px; font-size: 0.6rem;">SAVE</button>
                                <button type="submit" name="delete_nav_item" class="btn-delete" style="padding: 10px; font-size: 0.6rem;" onclick="return confirm('Remove this navigation item?');">VOID</button>
                            </form>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </main>

    <footer class="aura-footer">
        &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?> &mdash; AURA PORTAL v2.0
    </footer>

    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const adminSidebar = document.getElementById('adminSidebar');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                adminSidebar.classList.toggle('active');
            });
        }

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
