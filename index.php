<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$stmt = $pdo->query("SELECT * FROM content WHERE nav_item_id IS NULL ORDER BY id ASC");
$sections = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'selling_points_title'");
$stmt->execute();
$selling_points_title = $stmt->fetchColumn() ?: '';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'cta_text'");
$stmt->execute();
$cta_text = $stmt->fetchColumn() ?: 'Get Started';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'cta_link'");
$stmt->execute();
$cta_link = $stmt->fetchColumn() ?: '#';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF token validation failed.");
    }
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);

    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $subject, $message]);
    $success = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> | Home</title>
    <link rel="icon" href="images/favicon.jpg">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@900&family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="hero-section">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h2 class="hero-title"><?php echo strtoupper(SITE_NAME); ?></h2>
            <div class="hero-slogan-box">
                <p class="hero-slogan">EXPRESSING CULTURE THROUGH TASTE</p>
            </div>
        </div>
    </section>

    <main class="layout-main">
        <?php if (!empty($selling_points_title)): ?>
            <h2 class="section-title"><?php echo htmlspecialchars($selling_points_title); ?></h2>
        <?php endif; ?>

        <div class="prism-grid">
            <?php foreach ($sections as $index => $s): ?>
                <div class="bento-card <?php
                    if ($index == 0) echo 'grid-large';
                    elseif ($index == 1) echo 'grid-tall';
                    else echo 'grid-small';
                ?>">
                    <?php if ($s['image_path']): ?>
                        <div class="card-bg-image" style="background-image: url('<?php echo htmlspecialchars($s['image_path']); ?>');"></div>
                    <?php endif; ?>
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($s['section_title']); ?></h3>
                        <p><?php echo htmlspecialchars($s['description']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Contact Modal -->
    <div id="contact-modal" class="aura-modal">
        <div class="modal-overlay" onclick="toggleModal('contact-modal')"></div>
        <div class="modal-content">
            <h2 style="font-family: 'Cinzel', serif; margin-bottom: 30px;">GET IN <span style="color: var(--primary-color);">TOUCH</span></h2>
            <form id="contactForm" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="text" name="name" class="aura-input" placeholder="NAME" required>
                <input type="email" name="email" class="aura-input" placeholder="EMAIL" required>
                <input type="text" name="subject" class="aura-input" placeholder="SUBJECT" required id="modalSubject">
                <textarea name="message" class="aura-input" placeholder="MESSAGE" rows="5" required></textarea>
                <button type="submit" name="send_message" class="cta-shimmer">SEND MESSAGE</button>
            </form>
        </div>
    </div>

    <footer class="aura-footer">
        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?> &mdash; LUXURY EXPERIENCE
    </footer>

    <?php if (isset($success)): ?>
    <div id="success-toast" class="success-toast active">
        Message sent successfully!
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('success-toast').classList.remove('active');
        }, 3000);
    </script>
    <?php endif; ?>

    <script src="js/script.js"></script>
    <script>
        function toggleModal(id) {
            const modal = document.getElementById(id);
            modal.classList.toggle('active');
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('aura-modal')) {
                event.target.classList.remove('active');
            }
        }

        // Handle navigation to modal
        document.querySelectorAll('a[href="#contact-modal"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                toggleModal('contact-modal');
            });
        });
    </script>
</body>
</html>
