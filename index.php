<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'config.php';

try {
    $stmt = $pdo->query("SELECT * FROM content ORDER BY id ASC");
    $sections = $stmt->fetchAll();
} catch (PDOException $e) {
    $sections = [];
}

// Ensure settings are defined
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'selling_points_title'");
$stmt->execute();
$selling_points_title = $stmt->fetchColumn() ?: 'Actions';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'cta_text'");
$stmt->execute();
$cta_text = $stmt->fetchColumn() ?: 'Get Started';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'cta_link'");
$stmt->execute();
$cta_link = $stmt->fetchColumn() ?: '#';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'book_us_link'");
$stmt->execute();
$book_us_link = $stmt->fetchColumn() ?: '#';

$contact_msg = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_message'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $contact_msg = "Security mismatch. Please try again.";
    } else {
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $subject = sanitize($_POST['subject']);
        $message = sanitize($_POST['message']);

        if (!empty($name) && !empty($email) && !empty($message)) {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $subject, $message])) {
                $contact_msg = "Your message has been manifested. We will synchronize shortly.";
            } else {
                $contact_msg = "A frequency mismatch occurred. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link rel="icon" href="images/favicon.jpg">
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Cinzel:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body class="landing-page">
    <div class="stroll-bg-container">
        <div class="mesh-bg"></div>
        <div class="stroll-bg-overlay"></div>
    </div>
    <div class="layout-wrapper">
        <header class="layout-header" id="hero">
            <div class="vintage-frame">
                <h1><?php echo SITE_NAME; ?></h1>
            </div>
        </header>

    <div class="aura-brand-header">
        <a href="#" class="brand-title"><?php echo SITE_NAME; ?></a>
    </div>

    <nav class="aura-nav-luxury">
        <div class="nav-links-pill">
            <a href="#hero" class="nav-item">Home</a>
            <a href="#features" class="nav-item">Explore</a>
            <a href="#features" class="nav-item">Features</a>
            <a href="#" class="nav-item" id="contact-trigger">Contact</a>
            <a href="<?php echo htmlspecialchars($book_us_link); ?>" class="nav-item highlight" target="_blank">BOOK<br>US NOW</a>
        </div>
    </nav>

    <div class="section-nav">
        <div class="section-nav-inner">
            <a href="#hero" class="dot-nav active" data-tooltip="Home"></a>
            <a href="#features" class="dot-nav" data-tooltip="Explore"></a>
            <a href="#features" class="dot-nav" data-tooltip="Features"></a>
            <a href="#" class="dot-nav" id="side-contact" data-tooltip="Contact"></a>
        </div>
    </div>

        <main class="layout-main" id="features">
            <div class="section-header reveal-item">
                <h2 class="section-title"><?php echo htmlspecialchars($selling_points_title); ?></h2>
            </div>
            <div class="prism-grid">
                <?php foreach ($sections as $index => $s):
                    $grid_class = ($index % 3 == 0) ? 'grid-large' : 'grid-small';
                ?>
                    <div class="bento-card <?php echo $grid_class; ?> reveal-item">
                        <?php if ($s['image_path']): ?>
                            <div class="card-bg-image" style="background-image: url('<?php echo htmlspecialchars($s['image_path']); ?>');"></div>
                        <?php endif; ?>
                        <div class="card-content">
                            <h3><?php echo htmlspecialchars($s['section_title']); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars($s['description'])); ?></p>
                        </div>
                        <div class="aura-pulse-element"></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <!-- Contact Modal -->
    <div id="contact-modal" class="aura-modal">
        <div class="modal-overlay"></div>
        <div class="modal-content glass-morphism">
            <button class="modal-close">&times;</button>
            <div class="modal-body">
                <div class="modal-header">
                    <h2>CONNECT WITH US</h2>
                    <p>Orchestrate your vision with our creative team.</p>
                </div>
                <form id="contact-form" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <div class="form-group">
                        <input type="text" name="name" placeholder="Full Name" required class="aura-input">
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email Address" required class="aura-input">
                    </div>
                    <div class="form-group">
                        <select name="subject" class="aura-input aura-select">
                            <option value="General Inquiry">General Inquiry</option>
                            <option value="Masterpiece Request">Masterpiece Request</option>
                            <option value="Aura Consultation">Aura Consultation</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <textarea name="message" placeholder="Your Message" required class="aura-input" rows="5"></textarea>
                    </div>
                    <button type="submit" name="send_message" class="cta-shimmer full-width">Manifest Message</button>
                </form>
            </div>
        </div>
    </div>

    <?php if ($contact_msg): ?>
        <div class="notification-toast" style="position: fixed; bottom: 30px; right: 30px; background: var(--primary-color); padding: 20px 40px; border-radius: 15px; color: #fff; z-index: 3000; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <?php echo $contact_msg; ?>
        </div>
    <?php endif; ?>

    <script src="js/script.js"></script>
</body>
</html>
