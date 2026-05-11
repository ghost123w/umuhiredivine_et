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
    <div class="aura-portal-bg"></div>

    <nav class="aura-nav">
        <div class="glass-pill">
            <a href="dashboard.php?view=overview" class="<?php echo $view == 'overview' ? 'active' : ''; ?>">Portal</a>
            <a href="dashboard.php?view=settings" class="<?php echo $view == 'settings' ? 'active' : ''; ?>">Aura</a>
            <a href="dashboard.php?view=add" class="<?php echo $view == 'add' ? 'active' : ''; ?>">Create</a>
            <a href="dashboard.php?view=manage" class="<?php echo $view == 'manage' ? 'active' : ''; ?>">Manage</a>
            <a href="dashboard.php?view=messages" class="<?php echo $view == 'messages' ? 'active' : ''; ?>">Inquiries</a>
            <a href="<?php echo htmlspecialchars($book_us_link); ?>" class="nav-book-us" target="_blank" style="color: var(--primary-color); font-weight: 800; border-left: 1px solid rgba(255,255,255,0.1); padding-left: 20px;">BOOK US</a>
            <a href="logout.php" style="color: var(--danger-color); border-left: 1px solid rgba(255,255,255,0.1); padding-left: 20px;">Exit</a>
        </div>
    </nav>

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
                        <div class="dashboard-laurel-container">
                            <div class="laurel-icon small">
                                <svg viewBox="0 0 100 80" class="laurel-svg">
                                    <path d="M10,40 Q10,10 50,10" fill="none" stroke="currentColor" stroke-width="2"/>
                                    <path d="M90,40 Q90,10 50,10" fill="none" stroke="currentColor" stroke-width="2"/>
                                    <circle cx="15" cy="30" r="3" fill="currentColor"/>
                                    <circle cx="20" cy="20" r="3" fill="currentColor"/>
                                    <circle cx="30" cy="15" r="3" fill="currentColor"/>
                                    <circle cx="45" cy="12" r="3" fill="currentColor"/>
                                    <circle cx="85" cy="30" r="3" fill="currentColor"/>
                                    <circle cx="80" cy="20" r="3" fill="currentColor"/>
                                    <circle cx="70" cy="15" r="3" fill="currentColor"/>
                                    <circle cx="55" cy="12" r="3" fill="currentColor"/>
                                </svg>
                                <div class="laurel-text">
                                    <span class="book">BOOK</span>
                                    <span class="now">US</span>
                                </div>
                                <div class="laurel-stars">
                                    <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                                </div>
                            </div>
                        </div>
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
        </div>
    </main>

    <footer class="aura-footer">
        &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?> &mdash; AURA PORTAL v2.0
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
