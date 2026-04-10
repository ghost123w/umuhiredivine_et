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
    <title>Admin Dashboard | Modern Selling Point</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- 2. Use inline CSS in the head of the dashboard file to set that variable as the background image for a hero section. -->
    <style>
        .dashboard-hero {
            background-image: url('<?php echo $hero_bg_path; ?>');
            /* 3. Ensure the background is centered, covered, and fixed so it looks professional. */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            padding: 100px 20px;
            color: #fff;
            text-align: center;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: inset 0 0 0 1000px rgba(0,0,0,0.5); /* Overlay to make text readable */
        }
        .dashboard-hero h2 {
            font-size: 2.5rem;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
        }
    </style>
</head>
<body class="admin-body">
    <div class="admin-container">
        <header class="admin-header">
            <h1>Admin Dashboard</h1>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </header>

        <main class="admin-main">
            <!-- Website dashboard landing page hero section -->
            <section class="dashboard-hero">
                <h2>Control Center</h2>
                <p>Manage your professional high-converting solution from here.</p>
            </section>

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

        <footer class="admin-footer">
            <a href="../index.php">&larr; Back to Website</a>
        </footer>
    </div>
</body>
</html>
