<section class="hero-section">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <?php if (basename($_SERVER['PHP_SELF']) !== 'index.php'): ?>
            <div class="hero-brand-stack">
                <h1 class="hero-title-bg"><?php echo strtoupper(SITE_NAME); ?></h1>
                <h1 class="hero-title-fg"><?php echo strtoupper(SITE_NAME); ?></h1>
            </div>
        <?php endif; ?>
        <p class="hero-slogan">LUXURY DINING & LIFESTYLE</p>
    </div>
</section>
