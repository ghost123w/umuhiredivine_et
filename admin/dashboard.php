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

    if (isset($_POST['add_section'])) {
        $title = sanitize($_POST['section_title']);
        $desc = sanitize($_POST['description']);
        $stmt = $pdo->prepare("INSERT INTO content (section_title, description) VALUES (?, ?)");
        $stmt->execute([$title, $desc]);
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- 2. Use inline CSS in the head of the dashboard file to set that variable as the background image for a hero section. -->
    <style>
        .dashboard-hero {
            background-image: url('<?php echo $hero_bg_path; ?>');
            /* 3. Ensure the background is centered, covered, and fixed so it looks professional. */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            padding: 20px 20px 60px;
            color: #fff;
            text-align: center;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: inset 0 0 0 1000px rgba(0,0,0,0.5); /* Overlay to make text readable */
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            min-height: 200px;
        }
        .dashboard-hero h2 {
            font-size: 2.5rem;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
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
                <nav style="margin-top: 40px;">
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 15px;"><a href="dashboard.php" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">Dashboard Home</a></li>
                        <li style="margin-bottom: 15px;"><a href="../index.php" style="color: #fff; text-decoration: none; opacity: 0.8;">View Website</a></li>
                    </ul>
                </nav>
            </div>
        </aside>

        <main class="layout-main">
            <div class="dashboard-hero">
                <h2>Admin Dashboard</h2>
                <div class="vintage-frame">
                    <span style="color: #c5a059; font-weight: bold; letter-spacing: 4px;">Verified Admin</span>
                </div>
            </div>

            <h2 class="actions-title">Management Actions</h2>

            <?php if (isset($_GET['msg'])): ?>
                <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
                    <?php
                        if ($_GET['msg'] == 'added') echo "Selling point added successfully.";
                        if ($_GET['msg'] == 'deleted') echo "Selling point deleted successfully.";
                    ?>
                </div>
            <?php endif; ?>

            <section class="admin-card">
                <h3>Add New Selling Point</h3>
                <form method="POST" class="admin-form">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <div class="form-group">
                        <label>Section Title</label>
                        <input type="text" name="section_title" class="form-control" placeholder="e.g. Modern UI" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Describe this selling point..." required></textarea>
                    </div>
                    <button type="submit" name="add_section" class="btn-primary">Add Section</button>
                </form>
            </section>

            <section class="admin-card">
                <h3>Manage Selling Points</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sections)): ?>
                            <tr>
                                <td colspan="3" class="text-center">No selling points found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sections as $s): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($s['section_title']); ?></strong></td>
                                    <td><?php echo nl2br(htmlspecialchars($s['description'])); ?></td>
                                    <td>
                                        <form method="POST" onsubmit="return confirm('Are you sure?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                            <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                                            <button type="submit" name="delete_section" class="btn-delete">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
