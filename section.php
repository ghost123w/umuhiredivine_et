<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$contact_msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $contact_msg = "Security mismatch. Please try again.";
    } else {
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $subject = sanitize($_POST['subject']);
        $message = sanitize($_POST['message']);

        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $subject, $message])) {
            $contact_msg = "Your message has been manifested. We will synchronize shortly.";
        } else {
            $contact_msg = "A frequency mismatch occurred. Please try again.";
        }
    }
}

$nav_id = $_GET['id'] ?? null;
if (!$nav_id) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM navigation_items WHERE id = ? AND is_active = 1");
$stmt->execute([$nav_id]);
$navItem = $stmt->fetch();

if (!$navItem) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM content WHERE nav_item_id = ? ORDER BY id DESC");
$stmt->execute([$nav_id]);
$fixtures = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($navItem['label']); ?> | <?php echo SITE_NAME; ?></title>
    <link rel="icon" href="images/favicon.jpg">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@900&family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body class="landing-page">
    <?php include 'includes/header.php'; ?>

    <main class="layout-main">
        <header class="layout-header" style="padding: 100px 0 60px;">
            <h1 class="shimmer-text" style="font-size: 5rem; letter-spacing: -2px;"><?php echo htmlspecialchars(strtoupper($navItem['label'])); ?></h1>
            <p style="text-transform: uppercase; letter-spacing: 10px; color: #999; font-size: 0.8rem; margin-top: 20px;">Artisan Experience</p>
        </header>

        <div class="prism-grid">
            <?php if (empty($fixtures)): ?>
                <div class="bento-card grid-large" style="text-align: center; display: flex; align-items: center; justify-content: center; min-height: 400px;">
                    <div>
                        <h2 style="font-family: 'Cinzel', serif; color: #444;">No fixtures found in this frequency.</h2>
                        <a href="index.php" style="color: var(--primary-color); text-transform: uppercase; letter-spacing: 2px; font-size: 0.8rem; text-decoration: none; margin-top: 20px; display: inline-block;">Return to Source</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($fixtures as $index => $f):
                    $cardClass = ($index % 3 == 0) ? 'grid-large' : (($index % 3 == 1) ? 'grid-medium' : 'grid-tall');
                ?>
                    <section class="bento-card <?php echo $cardClass; ?> reveal-item">
                        <?php if ($f['image_path']): ?>
                            <div class="card-image-wrapper">
                                <img src="<?php echo htmlspecialchars($f['image_path']); ?>" alt="<?php echo htmlspecialchars($f['section_title']); ?>" loading="lazy">
                                <div class="image-overlay"></div>
                            </div>
                        <?php endif; ?>
                        <div class="card-content">
                            <span class="card-tag">Artisan Fixture</span>
                            <h3><?php echo htmlspecialchars($f['section_title']); ?></h3>
                            <p><?php echo htmlspecialchars($f['description']); ?></p>
                            <button class="card-btn" onclick="openContactModal('<?php echo addslashes($f['section_title']); ?>')">Enquire</button>
                        </div>
                    </section>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <footer class="aura-footer" style="margin-top: 100px; background: rgba(10,10,10,0.4); backdrop-filter: blur(20px);">
        &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?> &mdash; AURA LUXURY UX
    </footer>

    <!-- Contact Modal -->
    <div class="aura-modal" id="contact-modal">
        <div class="modal-overlay" onclick="closeModal()"></div>
        <div class="modal-content">
            <button class="modal-close" onclick="closeModal()">&times;</button>
            <div class="modal-header">
                <div class="brand-portrait-mini">
                    <img src="images/brand-portrait.jpg" alt="Portrait">
                </div>
                <h2 class="modal-title">Connect With Us</h2>
                <p class="modal-subtitle">Manifest your vision into reality.</p>
            </div>
            <form id="contact-form" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <div class="form-group">
                    <input type="text" name="name" class="form-control" placeholder="YOUR IDENTITY" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="CONTACT FREQUENCY (EMAIL)" required>
                </div>
                <div class="form-group">
                    <select name="subject" id="modalSubject" class="form-control" required>
                        <option value="" disabled selected>NATURE OF INQUIRY</option>
                        <option value="General Vision">General Vision</option>
                        <option value="Bespoke Design">Bespoke Design</option>
                        <option value="Fixture Procurement">Fixture Procurement</option>
                        <option value="Creative Direction">Creative Direction</option>
                    </select>
                </div>
                <div class="form-group">
                    <textarea name="message" class="form-control" placeholder="MANIFEST YOUR MESSAGE" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; padding: 20px; font-size: 1rem; letter-spacing: 4px;">SEND MESSAGE</button>
            </form>
            <div id="formStatus" style="margin-top: 20px; text-align: center; display: none;"></div>
        </div>
    </div>

    <?php if ($contact_msg): ?>
        <div id="contact-success-toast" style="position: fixed; bottom: 40px; right: 40px; background: var(--primary-color); color: #fff; padding: 20px 40px; border-radius: 100px; z-index: 10000; box-shadow: 0 20px 40px rgba(255, 53, 3, 0.4); font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; animation: slideInUp 0.5s cubic-bezier(0.2, 1, 0.3, 1);">
            <?php echo $contact_msg; ?>
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('contact-success-toast').style.display = 'none';
            }, 5000);
        </script>
    <?php endif; ?>

    <script src="js/script.js"></script>
    <script>
        // Background parallax effect
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const bg = document.querySelector('.stroll-bg-image');
            if (bg) {
                bg.style.transform = `translate3d(0, ${scrolled * 0.4}px, 0) scale(1.2)`;
            }
        });
    </script>
</body>
</html>
