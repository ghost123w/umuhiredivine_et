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

    if (isset($_POST['update_charge_title'])) {
        $title = sanitize($_POST['creative_charge_title']);
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES ('creative_charge_title', ?)");
        $stmt->execute([$title]);
        header("Location: dashboard.php?view=charge&msg=updated");
        exit();
    }

    if (isset($_POST['add_charge_card'])) {
        $image_1 = null;
        $image_2 = null;
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);

        foreach (['image_1', 'image_2'] as $key) {
            if (isset($_FILES[$key]) && $_FILES[$key]['error'] == 0) {
                $file_ext = strtolower(pathinfo($_FILES[$key]["name"], PATHINFO_EXTENSION));
                $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (in_array($file_ext, $allowed_exts)) {
                    $new_filename = uniqid('charge_') . '_' . $key . '.' . $file_ext;
                    if (move_uploaded_file($_FILES[$key]["tmp_name"], $target_dir . $new_filename)) {
                        if ($key == 'image_1') $image_1 = 'uploads/' . $new_filename;
                        else $image_2 = 'uploads/' . $new_filename;
                    }
                }
            }
        }

        if ($image_1 || $image_2) {
            $stmt = $pdo->prepare("INSERT INTO creative_charge (image_path_1, image_path_2) VALUES (?, ?)");
            $stmt->execute([$image_1, $image_2]);
            header("Location: dashboard.php?view=charge&msg=added");
        } else {
            header("Location: dashboard.php?view=charge&msg=error");
        }
        exit();
    }

    if (isset($_POST['delete_charge'])) {
        $id = (int)$_POST['id'];

        $stmt = $pdo->prepare("SELECT image_path_1, image_path_2 FROM creative_charge WHERE id = ?");
        $stmt->execute([$id]);
        $card = $stmt->fetch();

        if ($card) {
            foreach (['image_path_1', 'image_path_2'] as $img_col) {
                if ($card[$img_col] && file_exists('../' . $card[$img_col])) {
                    unlink('../' . $card[$img_col]);
                }
            }
        }

        $stmt = $pdo->prepare("DELETE FROM creative_charge WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: dashboard.php?view=charge&msg=deleted");
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

        $stmt = $pdo->prepare("SELECT image_path FROM content WHERE id = ?");
        $stmt->execute([$id]);
        $section = $stmt->fetch();

        if ($section && $section['image_path'] && file_exists('../' . $section['image_path'])) {
            unlink('../' . $section['image_path']);
        }

        $stmt = $pdo->prepare("DELETE FROM content WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: dashboard.php?view=manage&msg=deleted");
        exit();
    }
}

$sections = $pdo->query("SELECT * FROM content ORDER BY id ASC")->fetchAll();
$charge_cards = $pdo->query("SELECT * FROM creative_charge ORDER BY id ASC")->fetchAll();

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'selling_points_title'");
$stmt->execute();
$selling_points_title = $stmt->fetchColumn() ?: 'Actions';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'creative_charge_title'");
$stmt->execute();
$creative_charge_title = $stmt->fetchColumn() ?: 'FOLLOW OUR CREATIVE CHARGE';

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
            <a href="dashboard.php?view=charge" class="<?php echo $view == 'charge' ? 'active' : ''; ?>">Charge</a>
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
                    <a href="dashboard.php?view=charge" class="action-card">
                        <div class="icon">⚡</div>
                        <h4>Creative Charge</h4>
                        <p>Curate the dual-vision gallery of your high-energy feed.</p>
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
                        if ($_GET['msg'] == 'error') echo "<strong>Interrupted:</strong> A frequency mismatch occurred.";
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

            <?php if ($view == 'charge'): ?>
                <section class="aura-card">
                    <h2 style="font-family: 'Cinzel', serif; margin-bottom: 40px;">Creative <span style="color: var(--primary-color);">Charge</span></h2>

                    <form method="POST" style="margin-bottom: 60px; padding-bottom: 40px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <div class="form-group">
                            <label style="color: #666; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 2px;">Feed Title</label>
                            <input type="text" name="creative_charge_title" class="form-control" style="background: rgba(255,255,255,0.03); color: #fff; border-color: rgba(255,255,255,0.05); padding: 20px;" value="<?php echo htmlspecialchars($creative_charge_title); ?>" required>
                        </div>
                        <button type="submit" name="update_charge_title" class="btn-primary" style="width: 100%; padding: 15px; font-size: 0.8rem; letter-spacing: 2px;">UPDATE TITLE</button>
                    </form>

                    <h3 style="font-family: 'Cinzel', serif; margin-bottom: 30px; font-size: 1.2rem;">Add <span style="color: var(--primary-color);">Dual-Vision</span> Card</h3>
                    <form method="POST" enctype="multipart/form-data" style="margin-bottom: 60px;">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label style="color: #666; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 2px;">Image 1 (Left)</label>
                                <input type="file" name="image_1" class="form-control" style="background: rgba(255,255,255,0.03); color: #fff; border-color: rgba(255,255,255,0.05); padding: 15px;" accept="image/*" required>
                            </div>
                            <div class="form-group">
                                <label style="color: #666; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 2px;">Image 2 (Right)</label>
                                <input type="file" name="image_2" class="form-control" style="background: rgba(255,255,255,0.03); color: #fff; border-color: rgba(255,255,255,0.05); padding: 15px;" accept="image/*" required>
                            </div>
                        </div>
                        <button type="submit" name="add_charge_card" class="btn-primary" style="width: 100%; padding: 20px; font-size: 1rem; letter-spacing: 4px;">INJECT CHARGE</button>
                    </form>

                    <h3 style="font-family: 'Cinzel', serif; margin-bottom: 30px; font-size: 1.2rem;">Existing <span style="color: var(--primary-color);">Frequencies</span></h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
                        <?php foreach ($charge_cards as $card): ?>
                            <div class="aura-card" style="padding: 15px; background: rgba(255,255,255,0.02);">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 5px; height: 100px; margin-bottom: 15px; border-radius: 10px; overflow: hidden;">
                                    <img src="../<?php echo htmlspecialchars($card['image_path_1']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                    <img src="../<?php echo htmlspecialchars($card['image_path_2']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <form method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                    <input type="hidden" name="id" value="<?php echo $card['id']; ?>">
                                    <button type="submit" name="delete_charge" class="btn-delete" style="width: 100%; padding: 10px; font-size: 0.7rem;" onclick="return confirm('Disconnect this charge?');">VOID</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
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
                            <?php endif; ?>
                        </tbody>
                    </table>
                </section>
            <?php endif; ?>
        </div>
    </main>

    <footer class="aura-footer" style="margin-top: 150px;">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. ALL RIGHTS RESERVED.</p>
    </footer>

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
