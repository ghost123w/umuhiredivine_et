<?php
$hero_title = $hero_title ?? '';
$hero_subtitle = $hero_subtitle ?? '';
$hero_bg = $hero_bg ?? '';

$style_attr = '';
if (!empty($hero_bg)) {
    $style_attr = ' style="background-image: url(\'' . htmlspecialchars($hero_bg) . '\');"';
}
?>
<section class="hero-section"<?php echo $style_attr; ?>>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <?php if (!empty($hero_title)): ?>
            <div class="hero-brand-stack">
                <div class="hero-title-bg"><?php echo htmlspecialchars($hero_title); ?></div>
                <div class="hero-title-fg"><?php echo htmlspecialchars($hero_title); ?></div>
            </div>
        <?php endif; ?>
        <?php if (!empty($hero_subtitle)): ?>
            <div class="hero-slogan"><?php echo htmlspecialchars($hero_subtitle); ?></div>
        <?php endif; ?>
    </div>
</section>
