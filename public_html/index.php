<?php
require_once 'includes/db.php';
require_once 'config.php';
try {
    $stmt = $pdo->query("SELECT * FROM content ORDER BY id ASC");
    $sections = $stmt->fetchAll();
} catch (PDOException $e) {
    $sections = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <style>
        :root { --primary: #2563eb; --bg: #f8fafc; --text: #1e293b; }
        body { font-family: sans-serif; background: var(--bg); color: var(--text); margin: 0; }
        header { background: #0f172a; color: white; padding: 100px 20px; text-align: center; }
        .hero h1 { font-size: 3rem; margin: 0; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; padding: 50px 20px; max-width: 1200px; margin: 0 auto; }
        .reveal-section { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); opacity: 0; transform: translateY(30px); transition: 0.8s; }
        .reveal-section.active { opacity: 1; transform: translateY(0); }
        .cta { display: inline-block; background: var(--primary); color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <header>
        <h1><?php echo SITE_NAME; ?></h1>
        <p>Your professional high-converting solution.</p>
        <a href="#features" class="cta">Explore Features</a>
    </header>
    <main class="grid" id="features">
        <?php if (empty($sections)): ?>
            <section class="reveal-section">
                <h2>Innovation</h2>
                <p>We deliver cutting-edge technology to your business.</p>
            </section>
        <?php else: ?>
            <?php foreach ($sections as $s): ?>
                <section class="reveal-section">
                    <h2><?php echo htmlspecialchars($s['section_title']); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars($s['description'])); ?></p>
                </section>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('active'); });
            }, { threshold: 0.1 });
            document.querySelectorAll('.reveal-section').forEach(s => observer.observe(s));
        });
    </script>
</body>
</html>
