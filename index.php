<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'config.php';

try {
    // Fetch generic content sections
    $stmt = $pdo->query("SELECT * FROM content ORDER BY id ASC");
    $sections = $stmt->fetchAll();

    // Fetch products
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id ASC");
    $products = $stmt->fetchAll();

    // Fetch creative charge (gallery)
    $stmt = $pdo->query("SELECT * FROM creative_charge ORDER BY id ASC");
    $charge_cards = $stmt->fetchAll();

} catch (PDOException $e) {
    $sections = [];
    $products = [];
    $charge_cards = [];
}

// Fetch settings
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'selling_points_title'");
$stmt->execute();
$selling_points_title = $stmt->fetchColumn() ?: 'Features';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'creative_charge_title'");
$stmt->execute();
$creative_charge_title = $stmt->fetchColumn() ?: 'Creative Gallery';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'cta_text'");
$stmt->execute();
$cta_text = $stmt->fetchColumn() ?: 'Explore Now';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'cta_link'");
$stmt->execute();
$cta_link = $stmt->fetchColumn() ?: '#features';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'contact_title'");
$stmt->execute();
$contact_title = $stmt->fetchColumn() ?: 'Connect With Us';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'contact_subtitle'");
$stmt->execute();
$contact_subtitle = $stmt->fetchColumn() ?: 'Orchestrate your vision with our creative team.';

$contact_msg = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_message'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $contact_msg = "Security mismatch. Please refresh and try again.";
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
    <title><?php echo SITE_NAME; ?> | Aura Redesign</title>
    <link rel="icon" href="images/favicon.jpg">
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Cinzel:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body class="landing-page">
    <!-- Navigation Bar -->
    <nav class="aura-nav-bar">
        <div class="nav-container">
            <div class="nav-brand-centered">
                <a href="#" class="nav-brand-title"><?php echo SITE_NAME; ?></a>
            </div>
            <div class="nav-links-pill">
                <a href="#features" class="nav-item">Features</a>
                <a href="#products" class="nav-item">Products</a>
                <a href="#gallery" class="nav-item">Gallery</a>
                <a href="#" class="nav-item" id="contact-trigger">Contact</a>
            </div>
        </div>
    </nav>

    <!-- Background Elements -->
    <div class="stroll-bg-container">
        <div class="stroll-bg-image" style="background-image: url('images/landing-bg.png');"></div>
        <div class="mesh-bg"></div>
        <div class="stroll-bg-overlay"></div>
    </div>

    <div class="layout-wrapper">
        <!-- Hero Section -->
        <header class="hero-section" id="hero">
            <div class="hero-content">
                <div class="brand-display">
                    <h1 class="floating-brand-title"><?php echo SITE_NAME; ?></h1>
                </div>
                <p class="hero-subtitle">Redefining Excellence through Design and Innovation.</p>
                <div class="hero-cta">
                    <a href="<?php echo htmlspecialchars($cta_link); ?>" class="cta-shimmer"><?php echo htmlspecialchars($cta_text); ?></a>
                </div>
            </div>
        </header>

        <main class="layout-main">
            <!-- Features Section (Bento Grid) -->
            <section id="features" class="page-section">
                <h2 class="section-title"><?php echo htmlspecialchars($selling_points_title); ?></h2>
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
            </section>

            <!-- Products Section -->
            <section id="products" class="page-section">
                <h2 class="section-title">Power Kits</h2>
                <div class="products-grid">
                    <?php foreach ($products as $p): ?>
                        <div class="product-card aura-glow reveal-item">
                            <div class="product-image">
                                <img src="<?php echo htmlspecialchars($p['image_path']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" loading="lazy">
                            </div>
                            <div class="product-info">
                                <h4><?php echo htmlspecialchars($p['name']); ?></h4>
                                <span class="price">$<?php echo htmlspecialchars($p['price']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Gallery Section (Bento Grid) -->
            <section id="gallery" class="page-section">
                <h2 class="section-title"><?php echo htmlspecialchars($creative_charge_title); ?></h2>
                <div class="prism-grid gallery-prism">
                    <?php foreach ($charge_cards as $index => $card): ?>
                        <div class="gallery-bento-item reveal-item">
                            <div class="gallery-img-container">
                                <img src="<?php echo htmlspecialchars($card['image_path_1']); ?>" alt="Gallery Image 1" loading="lazy" class="img-primary">
                                <img src="<?php echo htmlspecialchars($card['image_path_2']); ?>" alt="Gallery Image 2" loading="lazy" class="img-secondary">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>

        <footer class="layout-footer">
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All Rights Reserved.</p>
        </footer>
    </div>

    <!-- Contact Modal -->
    <div id="contact-modal" class="aura-modal">
        <div class="modal-overlay"></div>
        <div class="modal-content glass-morphism">
            <button class="modal-close">&times;</button>
            <div class="modal-body">
                <div class="modal-header">
                    <img src="images/brand-portrait.jpg" alt="Brand Portrait" class="modal-brand-img">
                    <h2><?php echo htmlspecialchars($contact_title); ?></h2>
                    <p><?php echo htmlspecialchars($contact_subtitle); ?></p>
                </div>
                <form id="contact-form" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <div class="form-grid">
                        <div class="form-group">
                            <input type="text" name="name" placeholder="Full Name" required class="aura-input">
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Email Address" required class="aura-input">
                        </div>
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
        <div class="notification-toast">
            <?php echo $contact_msg; ?>
        </div>
    <?php endif; ?>

    <script src="js/script.js"></script>
</body>
</html>
