<?php
require_once 'includes/db.php';
require_once 'config.php';
try {
    $stmt = $pdo->query("SELECT * FROM content ORDER BY id ASC");
    $sections = $stmt->fetchAll();
} catch (PDOException $e) {
    $sections = [];
}

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'selling_points_title'");
$stmt->execute();
$selling_points_title = $stmt->fetchColumn() ?: 'Curation';

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'cta_text'");
$stmt->execute();
$cta_text = $stmt->fetchColumn() ?: 'Explore Now';

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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Cinzel:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        .landing-hero {
            height: 80vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0 5%;
        }
        .landing-hero h1 {
            font-size: clamp(3rem, 10vw, 6rem);
            font-family: 'Cinzel', serif;
            margin: 0;
            line-height: 1;
        }
        .landing-hero p {
            color: #888;
            font-size: 1.2rem;
            max-width: 600px;
            margin: 20px 0 40px;
        }
        .content-grid {
            padding: 0 5% 100px;
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
        }
        @media (max-width: 600px) {
            .content-grid { grid-template-columns: 1fr; }
        }
        .bento-card {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 40px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .bento-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 53, 3, 0.3);
            box-shadow: 0 40px 80px rgba(0,0,0,0.6);
        }
        .bento-card img {
            width: 100%;
            height: 350px;
            object-fit: cover;
            border-bottom: 1px solid var(--glass-border);
        }
        .bento-card-content {
            padding: 40px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .bento-card-content h2 {
            font-family: 'Cinzel', serif;
            font-size: 2rem;
            margin: 0 0 15px;
            color: var(--primary-color);
        }
        .bento-card-content p {
            color: #aaa;
            line-height: 1.8;
            margin: 0 0 30px;
            font-size: 1.05rem;
        }
        .floating-cta {
            position: fixed;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
        }
        .landing-footer {
            text-align: center;
            padding: 100px 5%;
            border-top: 1px solid var(--glass-border);
            color: #444;
            font-size: 0.9rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
    </style>
</head>
<body class="landing-page">
    <div class="aurora-bg"></div>

    <nav class="glass-pill-nav">
        <div class="nav-brand shimmer-text"><?php echo SITE_NAME; ?></div>
        <div class="nav-links">
            <a href="#home" class="active">Home</a>
            <a href="#curation"><?php echo htmlspecialchars($selling_points_title); ?></a>
            <a href="admin/login.php">Portal</a>
        </div>
        <div class="nav-actions">
            <a href="<?php echo htmlspecialchars($cta_link); ?>" class="btn-modern" style="padding: 8px 25px; font-size: 0.75rem;"><?php echo htmlspecialchars($cta_text); ?></a>
        </div>
    </nav>

    <header class="landing-hero" id="home">
        <h1 class="shimmer-text"><?php echo SITE_NAME; ?></h1>
        <p>A digital atelier for the modern visionary. Crafted with precision, powered by passion.</p>
        <div class="status-indicator" style="justify-content: center;">
            <div class="pulse-dot"></div>
            <span>System Operational • Artisan Version 2.0</span>
        </div>
    </header>

    <main id="curation">
        <div class="content-grid">
            <?php foreach ($sections as $s): ?>
                <article class="bento-card reveal-section">
                    <?php if ($s['image_path']): ?>
                        <img src="<?php echo htmlspecialchars($s['image_path']); ?>" alt="<?php echo htmlspecialchars($s['section_title']); ?>">
                    <?php endif; ?>
                    <div class="bento-card-content">
                        <h2><?php echo htmlspecialchars($s['section_title']); ?></h2>
                        <p><?php echo nl2br(htmlspecialchars($s['description'])); ?></p>
                        <div style="margin-top: auto;">
                             <a href="<?php echo htmlspecialchars($cta_link); ?>" class="btn-modern" style="display: inline-block; text-decoration: none;"><?php echo htmlspecialchars($cta_text); ?></a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="landing-footer">
        &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?> • Built for Excellence
    </footer>

    <script src="js/script.js"></script>
    <script>
        // Simple scroll spy for active link
        window.addEventListener('scroll', () => {
            const sections = document.querySelectorAll('header, main');
            const navLinks = document.querySelectorAll('.nav-links a');

            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (pageYOffset >= sectionTop - 150) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').includes(current)) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
