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

        // Delete image file if exists
        $stmt = $pdo->prepare("SELECT image_path FROM content WHERE id = ?");
        $stmt->execute([$id]);
        $img = $stmt->fetchColumn();
        if ($img && file_exists('../' . $img)) {
            unlink('../' . $img);
        }

        $stmt = $pdo->prepare("DELETE FROM content WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: dashboard.php?view=manage&msg=deleted");
        exit();
    }

    if (isset($_POST['delete_message'])) {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: dashboard.php?view=messages&msg=msg_deleted");
        exit();
    }
}

$sections = $pdo->query("SELECT * FROM content ORDER BY id ASC")->fetchAll();
$messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();

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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="aura-body">
    <div class="stroll-bg-container"></div>

    <header class="layout-header">
        <div class="layout-header-content">
            <h1 class="brand-title"><?php echo htmlspecialchars(SITE_NAME); ?></h1>
            <nav class="main-nav">
                <ul>
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="dashboard.php?view=manage">Features</a></li>
                    <li><a href="dashboard.php?view=messages">Messages</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="aura-container">
        <?php if (isset($_GET['msg'])): ?>
            <div class="aura-card" style="padding: 15px 30px; margin-bottom: 30px; border-left: 4px solid var(--primary-color);">
                <p style="margin: 0; font-size: 0.9rem; color: var(--primary-color);">
                    <?php
                        if ($_GET['msg'] == 'added') echo "Aura synchronized: New content piece initialized.";
                        if ($_GET['msg'] == 'deleted') echo "System purge: Content piece removed.";
                        if ($_GET['msg'] == 'settings_updated') echo "Core update: Aura parameters refined.";
                        if ($_GET['msg'] == 'msg_deleted') echo "Archive update: Message removed.";
                    ?>
                </p>
            </div>
        <?php endif; ?>

        <?php if ($view == 'overview'): ?>
            <div class="aura-card" style="text-align: center; background: linear-gradient(135deg, rgba(255,53,3,0.1) 0%, rgba(100,50,255,0.1) 100%);">
                <h2 class="aura-title" style="margin-bottom: 10px;">Atelier Dashboard</h2>
                <p style="opacity: 0.6; margin-bottom: 30px;">Managing the <?php echo SITE_NAME; ?> Experience</p>
                <div style="display: flex; justify-content: center; gap: 40px;">
                    <div>
                        <span style="display: block; font-size: 2.5rem; font-weight: 800; color: var(--primary-color);"><?php echo count($sections); ?></span>
                        <span style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; opacity: 0.5;">Active Points</span>
                    </div>
                    <div>
                        <span style="display: block; font-size: 2.5rem; font-weight: 800; color: var(--primary-color);"><?php echo count($messages); ?></span>
                        <span style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; opacity: 0.5;">Inquiries</span>
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 30px;">
                <div class="aura-card">
                    <h3 style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 20px;">System Status</h3>
                    <p style="font-size: 0.9rem; color: rgba(255,255,255,0.6);">Aura dynamic background is active. All systems functional.</p>
                </div>
                <div class="aura-card" style="display: flex; align-items: center; gap: 20px;">
                    <img src="../images/brand-portrait.jpg" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 1px solid var(--primary-color);">
                    <div>
                        <h4 style="margin: 0; font-size: 1rem;"><?php echo htmlspecialchars($_SESSION['username']); ?></h4>
                        <p style="margin: 0; font-size: 0.7rem; opacity: 0.5;">ADMINISTRATOR</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($view == 'settings'): ?>
            <div class="aura-card">
                <h2 class="aura-title">System Settings</h2>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <div class="form-group">
                        <label>Landing Page Tagline</label>
                        <input type="text" name="selling_points_title" class="form-control" value="<?php echo htmlspecialchars($selling_points_title); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>CTA Button Text</label>
                        <input type="text" name="cta_text" class="form-control" value="<?php echo htmlspecialchars($cta_text); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>CTA Global Link</label>
                        <input type="text" name="cta_link" class="form-control" value="<?php echo htmlspecialchars($cta_link); ?>" required>
                    </div>
                    <button type="submit" name="update_settings" class="btn-aura">Sync Aura</button>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($view == 'manage'): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
                <h2 class="aura-title" style="margin: 0;">Masterpiece Archive</h2>
                <a href="dashboard.php?view=add" class="btn-aura" style="padding: 10px 20px; font-size: 0.8rem;">+ New Entry</a>
            </div>

            <?php foreach ($sections as $s): ?>
                <div class="aura-card" style="display: flex; align-items: center; gap: 30px; margin-bottom: 20px;">
                    <div style="width: 100px; height: 100px; border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.1);">
                        <?php if ($s['image_path']): ?>
                            <img src="../<?php echo htmlspecialchars($s['image_path']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <div style="width: 100%; height: 100%; background: #111; display: flex; align-items: center; justify-content: center; font-size: 0.6rem; opacity: 0.3;">NO IMG</div>
                        <?php endif; ?>
                    </div>
                    <div style="flex: 1;">
                        <h4 style="margin: 0 0 5px; font-size: 1.2rem;"><?php echo htmlspecialchars($s['section_title']); ?></h4>
                        <p style="margin: 0; font-size: 0.85rem; opacity: 0.5; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;"><?php echo htmlspecialchars($s['description']); ?></p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <a href="edit.php?id=<?php echo $s['id']; ?>" class="btn-aura" style="background: rgba(255,255,255,0.05); color: #fff; font-size: 0.7rem; padding: 10px 15px;">Edit</a>
                        <form method="POST" onsubmit="return confirm('Purge this content?');">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                            <button type="submit" name="delete_section" class="btn-aura" style="background: rgba(231, 76, 60, 0.2); color: #e74c3c; font-size: 0.7rem; padding: 10px 15px;">Purge</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($view == 'messages'): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
                <h2 class="aura-title" style="margin: 0;">Correspondence Archive</h2>
            </div>

            <?php if (empty($messages)): ?>
                <div class="aura-card" style="text-align: center; padding: 60px;">
                    <p style="opacity: 0.5;">No active inquiries in the aura.</p>
                </div>
            <?php endif; ?>

            <?php foreach ($messages as $m): ?>
                <div class="aura-card" style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                        <div>
                            <h4 style="margin: 0; font-size: 1.2rem; color: #fff;"><?php echo htmlspecialchars($m['name']); ?></h4>
                            <p style="margin: 5px 0; font-size: 0.8rem; color: var(--primary-color);"><?php echo htmlspecialchars($m['email']); ?></p>
                        </div>
                        <div style="text-align: right;">
                            <span style="display: block; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.4;"><?php echo date('M d, Y H:i', strtotime($m['created_at'])); ?></span>
                            <span style="display: inline-block; margin-top: 5px; padding: 4px 10px; background: rgba(197, 160, 89, 0.1); color: var(--primary-color); border-radius: 4px; font-size: 0.6rem; font-weight: 800; text-transform: uppercase;"><?php echo htmlspecialchars($m['subject']); ?></span>
                        </div>
                    </div>
                    <div style="background: rgba(255,255,255,0.03); padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                        <p style="margin: 0; font-size: 0.95rem; line-height: 1.6; color: rgba(255,255,255,0.8);"><?php echo nl2br(htmlspecialchars($m['message'])); ?></p>
                    </div>
                    <div style="display: flex; justify-content: flex-end;">
                        <form method="POST" onsubmit="return confirm('Archive permanently?');">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                            <button type="submit" name="delete_message" class="btn-aura" style="background: rgba(231, 76, 60, 0.1); color: #e74c3c; font-size: 0.7rem; padding: 8px 20px; border: 1px solid rgba(231, 76, 60, 0.2);">Archive</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($view == 'add'): ?>
            <div class="aura-card">
                <h2 class="aura-title">Initialize New Masterpiece</h2>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="section_title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Manifesto (Description)</label>
                        <textarea name="description" class="form-control" rows="5" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Visual Asset</label>
                        <input type="file" name="section_image" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" name="add_section" class="btn-aura">Deploy to Aura</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
