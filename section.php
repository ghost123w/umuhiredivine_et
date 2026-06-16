<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/contact-logic.php';

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

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'book_us_link'");
$stmt->execute();
$book_us_link = $stmt->fetchColumn() ?: '#';
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
                    $activeClass = ($item['id'] == $nav_id) ? 'active' : '';
                    $idAttr = ($item['label'] == 'Contact') ? 'id="contact-trigger"' : '';
                ?>
                    <a href="<?php echo htmlspecialchars($item['link_url']); ?>" class="nav-item <?php echo $activeClass; ?>" <?php echo $idAttr; ?>><?php echo htmlspecialchars($item['label']); ?></a>
                <?php endforeach; ?>
                <a href="<?php echo htmlspecialchars($book_us_link); ?>" class="nav-item highlight">BOOK<br>US NOW</a>
            </div>
        </div>
    </nav>

    <main class="layout-main" style="padding-top: 150px;">
        <header class="layout-header" style="max-width: 1400px; margin: 0 auto 100px;">
            <?php
                $label = strtoupper($navItem['label']);
                $len = strlen($label);
                $split = ceil($len / 2);
                $white = substr($label, 0, $split);
                $orange = substr($label, $split);
            ?>
            <h2 class="luxury-heading"><span class="white-text"><?php echo htmlspecialchars($white); ?></span><span class="highlight"><?php echo htmlspecialchars($orange); ?></span></h2>
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
                        <div class="card-laurel-container">
                            <div class="laurel-icon mini">
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
                            </div>
                        </div>
                    </section>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <footer class="aura-footer" style="margin-top: 100px; background: rgba(10,10,10,0.4); backdrop-filter: blur(20px);">
        &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?> &mdash; AURA LUXURY UX
    </footer>

    <?php include 'includes/contact-modal.php'; ?>

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
