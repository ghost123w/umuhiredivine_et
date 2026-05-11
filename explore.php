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

// Find the Explore Nav Item ID
$stmt = $pdo->prepare("SELECT id FROM navigation_items WHERE label = 'Explore' LIMIT 1");
$stmt->execute();
$nav_id = $stmt->fetchColumn();

// Fetch content assigned to Explore
$fixtures = [];
if ($nav_id) {
    $stmt = $pdo->prepare("SELECT * FROM content WHERE nav_item_id = ? ORDER BY id ASC");
    $stmt->execute([$nav_id]);
    $fixtures = $stmt->fetchAll();
}

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'book_us_link'");
$stmt->execute();
$book_us_link = $stmt->fetchColumn() ?: '#';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore | <?php echo htmlspecialchars(SITE_NAME); ?></title>
    <link rel="icon" href="images/favicon.jpg">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@900&family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body class="aura-body">
    <div class="stroll-bg-container">
        <div class="stroll-bg-image" style="background-image: url('images/aura-bg.jpg');"></div>
        <div class="mesh-gradient"></div>
        <div class="grain-overlay"></div>
    </div>

    <nav class="aura-nav-compact scrolled">
        <div class="nav-pill-wrapper">
            <div class="brand-pill">
                <a href="index.php" class="nav-brand-pill"><?php echo htmlspecialchars(SITE_NAME); ?></a>
            </div>
            <div class="nav-links-pill">
                <?php
                $navItems = $pdo->query("SELECT * FROM navigation_items WHERE nav_type = 'main' AND is_active = 1 ORDER BY sort_order ASC")->fetchAll();
                foreach ($navItems as $item):
                    $activeClass = ($item['label'] == 'Explore') ? 'active' : '';
                    $idAttr = ($item['label'] == 'Contact') ? 'id="contact-trigger"' : '';
                ?>
                    <a href="<?php echo htmlspecialchars($item['link_url']); ?>" class="nav-item <?php echo $activeClass; ?>" <?php echo $idAttr; ?>><?php echo htmlspecialchars($item['label']); ?></a>
                <?php endforeach; ?>
                <a href="<?php echo htmlspecialchars($book_us_link); ?>" class="nav-item highlight">BOOK<br>US NOW</a>
            </div>
        </div>
    </nav>

    <main class="layout-main" style="padding-top: 150px;">
        <header class="layout-header" style="text-align: center; margin-bottom: 100px;">
            <h1 class="shimmer-text" style="font-family: 'Cinzel', serif; font-size: 4rem; letter-spacing: 15px; margin: 0;">Explore</h1>
            <p style="color: var(--primary-color); text-transform: uppercase; letter-spacing: 5px; font-weight: 700; font-size: 0.7rem; margin-top: 20px;">Discovery Awaits</p>
            <div style="width: 100px; height: 2px; background: var(--primary-color); margin: 30px auto; box-shadow: 0 0 20px var(--primary-color);"></div>
        </header>

        <div class="prism-grid">
            <?php if (empty($fixtures)): ?>
                <div class="bento-card grid-large" style="text-align: center; display: flex; align-items: center; justify-content: center; min-height: 400px;">
                    <div>
                        <h2 style="font-family: 'Cinzel', serif; color: #444;">The exploration horizon is currently clear.</h2>
                        <p style="color: #666; font-size: 0.8rem; margin-top: 10px;">Check back as we expand our digital frequency.</p>
                        <a href="index.php" style="color: var(--primary-color); text-transform: uppercase; letter-spacing: 2px; font-size: 0.8rem; text-decoration: none; margin-top: 40px; display: inline-block; border: 1px solid var(--primary-color); padding: 10px 25px; border-radius: 50px;">Return to Source</a>
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
                            <span class="card-tag">Discovery</span>
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
        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?> &mdash; AURA EXPLORATION
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
        </div>
    </div>

    <?php if ($contact_msg): ?>
        <div id="contact-success-toast" style="position: fixed; bottom: 40px; right: 40px; background: var(--primary-color); color: #fff; padding: 20px 40px; border-radius: 100px; z-index: 10000; box-shadow: 0 20px 40px rgba(255, 53, 3, 0.4); font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 2px;">
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
