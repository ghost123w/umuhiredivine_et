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

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'selling_points_title'");
$stmt->execute();
$selling_points_title = $stmt->fetchColumn() ?: 'Actions';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'cta_text'");
$stmt->execute();
$cta_text = $stmt->fetchColumn() ?: 'Get Started';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'cta_link'");
$stmt->execute();
$cta_link = $stmt->fetchColumn() ?: '#';
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="landing-page">
    <?php if (isset($_GET['msg'])): ?>
        <div style="position: fixed; top: 120px; left: 50%; transform: translateX(-50%); z-index: 5000; width: 90%; max-width: 400px;">
            <div class="aura-card" style="padding: 15px 30px; border-left: 4px solid var(--primary-color); background: rgba(0,0,0,0.9); backdrop-filter: blur(20px);">
                <p style="margin: 0; font-size: 0.9rem; color: var(--primary-color);">
                    <?php
                        if ($_GET['msg'] == 'sent') echo "Message received. Your aura has been noted.";
                        if ($_GET['msg'] == 'error') echo "System failure: Unable to transmit message.";
                        if ($_GET['msg'] == 'missing') echo "Incomplete data: All fields are required.";
                    ?>
                </p>
                <a href="index.php" style="position: absolute; top: 10px; right: 10px; color: #fff; text-decoration: none; font-size: 0.8rem; opacity: 0.5;">&times;</a>
            </div>
        </div>
    <?php endif; ?>

    <div class="stroll-bg-container">
        <div class="stroll-bg-image" style="background-image: url('images/aura-bg.jpg');"></div>
        <div class="stroll-bg-overlay"></div>
    </div>
    <!-- Section Indicator (ScrollSpy) -->
    <nav class="section-nav">
        <ul>
            <?php foreach ($sections as $index => $s): ?>
                <li><a href="#section-<?php echo $s['id']; ?>" class="section-dot" data-section="section-<?php echo $s['id']; ?>"><span class="dot-label"><?php echo htmlspecialchars($s['section_title']); ?></span></a></li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <header class="layout-header">
        <div class="layout-header-content">
            <h1 class="brand-title"><?php echo htmlspecialchars(SITE_NAME); ?></h1>
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#contact">Contact Us</a></li>
                </ul>
            </nav>
        </div>
    </header>


    <div class="portrait-viewport">
        <main class="portrait-container" id="features">
            <?php foreach ($sections as $s): ?>
                <section id="section-<?php echo $s['id']; ?>" class="portrait-card reveal-section <?php echo $s['image_path'] ? 'with-image' : ''; ?>">
                    <?php if ($s['image_path']): ?>
                        <div class="portrait-image-header">
                            <img src="<?php echo htmlspecialchars($s['image_path']); ?>" alt="<?php echo htmlspecialchars($s['section_title']); ?>">
                        </div>
                    <?php endif; ?>
                    <div class="portrait-content">
                        <span class="section-tag">Feature</span>
                        <h2><?php echo htmlspecialchars($s['section_title']); ?></h2>
                        <p><?php echo nl2br(htmlspecialchars($s['description'])); ?></p>
                        <div class="portrait-footer">
                            <a href="<?php echo htmlspecialchars($cta_link); ?>" class="cta"><?php echo htmlspecialchars($cta_text); ?></a>
                        </div>
                    </div>
                </section>
            <?php endforeach; ?>

            <!-- Contact Us Section -->
            <section id="contact" class="portrait-card reveal-section">
                <div class="portrait-content">
                    <span class="section-tag">Inquiry</span>
                    <h2>Connect With Us</h2>
                    <p>Experience the aura of personalized luxury. Send us a message and we will get back to you shortly.</p>

                    <form action="process_contact.php" method="POST" class="contact-form">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <div class="form-group">
                            <input type="text" name="name" placeholder="Your Name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Your Email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <select name="subject" class="form-control" required>
                                <option value="" disabled selected>Select Inquiry Type</option>
                                <option value="Consultation">Private Consultation</option>
                                <option value="Booking">Direct Booking</option>
                                <option value="Collaboration">Collaboration</option>
                                <option value="Other">General Inquiry</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <textarea name="message" placeholder="Your Message" class="form-control" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="cta" style="width: 100%; border: none; cursor: pointer; background: #fff; color: #000;">Send Message</button>
                    </form>
                </div>
            </section>
        </main>
    </div>
    <script src="js/script.js"></script>
</body>
</html>
