<?php
session_start();
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

require_once 'includes/contact-logic.php';

$is_explore_page = true; // Activate inline navigation
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> | Home</title>
    <link rel="icon" href="images/favicon.jpg">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@900&family=Inter:wght@400;700;900&family=Patrick+Hand&display=swap" rel="stylesheet">
    <style>
        /* HTML/Body full viewport setups for scroll snap */
        html, body {
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden !important;
            background-color: #000000;
        }

        /* Scroll Snap Container */
        .scroll-container {
            height: 100vh;
            overflow-y: scroll;
            scroll-snap-type: y mandatory;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
        }

        /* Individual Scroll Section */
        .scroll-section {
            height: 100vh;
            scroll-snap-align: start;
            scroll-snap-stop: always;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            box-sizing: border-box;
            padding: 100px 10% 40px; /* Spacing for fixed site header */
            text-align: center;
        }

        /* Distinct Luxury Backgrounds for Visual Cue */
        .sec-1 { background-color: #000000; }
        .sec-2 { background: linear-gradient(180deg, #000000 0%, #0d0d0d 100%); }
        .sec-3 { background-color: #050505; }
        .sec-4 { background: linear-gradient(180deg, #050505 0%, #0c0a05 100%); }
        .sec-5 { background-color: #080808; }
        .sec-6 { background: linear-gradient(180deg, #080808 0%, #05070a 100%); }
        .sec-7 { background-color: #020202; }
        .sec-8 { background: linear-gradient(180deg, #020202 0%, #000000 100%); }

        /* Subtle design accents */
        .sec-accent {
            width: 60px;
            height: 1.5px;
            background-color: #c8a55c;
            margin: 25px 0;
        }

        .sec-circle-accent {
            width: 12px;
            height: 12px;
            border: 1.5px solid #c8a55c;
            border-radius: 50%;
            margin: 25px 0;
        }

        /* Typography */
        .sec-title {
            font-family: 'Cinzel', serif;
            font-size: 2.8rem;
            font-weight: 900;
            color: #ffffff;
            margin: 0;
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        .sec-subtitle {
            font-size: 0.9rem;
            font-weight: 700;
            color: #c8a55c;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-top: 10px;
        }

        .sec-desc {
            font-family: 'Inter', sans-serif;
            font-size: 1.1rem;
            line-height: 1.8;
            color: #cccccc;
            max-width: 650px;
            margin: 15px 0 0;
            font-weight: 400;
        }

        /* Scroll indicator */
        .scroll-indicator {
            position: absolute;
            bottom: 40px;
            font-size: 0.75rem;
            color: #c8a55c;
            letter-spacing: 2px;
            text-transform: uppercase;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-8px); }
            60% { transform: translateY(-4px); }
        }

        /* CTA Button */
        .cta-btn {
            margin-top: 30px;
            display: inline-block;
            padding: 12px 30px;
            border: 1.5px solid #c8a55c;
            color: #c8a55c;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 2px;
            transition: all 0.3s ease;
            background: transparent;
            cursor: pointer;
            text-transform: uppercase;
        }

        .cta-btn:hover {
            background-color: #c8a55c;
            color: #000000;
        }

        /* Make brand link uppercase per memory / screenshot layout */
        .brand-link-explore {
            text-transform: uppercase !important;
        }

        /* 80px horizontal padding for specialized header alignment */
        .explore-page .site-header-aura {
            padding: 40px 80px 0 !important;
        }

        /* Nav book stack vertical gap 10px per memory */
        .nav-book-stack {
            gap: 10px !important;
        }

        /* Align active nav item specifically */
        .header-explore .nav-item.active {
            color: #c8a55c !important; /* Gold accent color */
            font-weight: 700;
        }

        .header-explore .nav-item:hover {
            color: #c8a55c !important;
        }
    </style>
</head>
<body class="explore-page">
    <?php include 'includes/header.php'; ?>

    <div class="scroll-container">
        <!-- Section 1: Introduction -->
        <section class="scroll-section sec-1">
            <h2 class="sec-title">UMUHIREDIVINE</h2>
            <div class="sec-subtitle">THE ULTIMATE LUXURY ESSENCE</div>
            <div class="sec-accent"></div>
            <p class="sec-desc">Where timeless heritage meets modern refinement. Experience a curated realm of bespoke elegance crafted exclusively for the extraordinary.</p>
            <div class="scroll-indicator">SCROLL DOWN</div>
        </section>

        <!-- Section 2: Our Philosophy -->
        <section class="scroll-section sec-2">
            <h2 class="sec-title">OUR PHILOSOPHY</h2>
            <div class="sec-subtitle">PURITY IN CRAFTSMANSHIP</div>
            <div class="sec-circle-accent"></div>
            <p class="sec-desc">True luxury is not merely seen—it is felt. We believe in meticulous detail, selecting only the rarest botanical ingredients and natural elixirs for our formulations.</p>
        </section>

        <!-- Section 3: Bespoke Collection -->
        <section class="scroll-section sec-3">
            <h2 class="sec-title">PRIVATE COLLECTION</h2>
            <div class="sec-subtitle">CURATED FOR THE CONNOISSEUR</div>
            <div class="sec-accent"></div>
            <p class="sec-desc">A symphony of exquisite skincare, luxury oils, and rare fragrances designed to elevate your daily ritual. Discover custom-tailored creations.</p>
        </section>

        <!-- Section 4: Meticulous Ingredients -->
        <section class="scroll-section sec-4">
            <h2 class="sec-title">NATURAL SPLENDOR</h2>
            <div class="sec-subtitle">NATURE'S NOBLEST GOLD</div>
            <div class="sec-circle-accent"></div>
            <p class="sec-desc">Every drop of our luxury oils is cold-pressed, hand-selected, and ethically sourced. We fuse ancient alchemy with modern cosmetic mastery.</p>
        </section>

        <!-- Section 5: The Private Experience -->
        <section class="scroll-section sec-5">
            <h2 class="sec-title">THE EXPERIENCE</h2>
            <div class="sec-subtitle">PERSONALIZED LUXURY REIMAGINED</div>
            <div class="sec-accent"></div>
            <p class="sec-desc">Step into an intimate curation session. Our elite specialists craft personalized beauty and aromatic profiles to reflect your unique aura.</p>
        </section>

        <!-- Section 6: Our Vision -->
        <section class="scroll-section sec-6">
            <h2 class="sec-title">OUR VISION</h2>
            <div class="sec-subtitle">A LEGACY OF PERFECTION</div>
            <div class="sec-circle-accent"></div>
            <p class="sec-desc">We do not follow trends; we define them. Our vision is to elevate self-care into an artistic expression, raising standard wellness into a state of grace.</p>
        </section>

        <!-- Section 7: The Heritage -->
        <section class="scroll-section sec-7">
            <h2 class="sec-title">THE HERITAGE</h2>
            <div class="sec-subtitle">ROOTED IN EXCELLENCE</div>
            <div class="sec-accent"></div>
            <p class="sec-desc">Born from a passion for uncompromising quality and rare aesthetics. For years, we have served as the silent luxury sanctuary for global tastemakers.</p>
        </section>

        <!-- Section 8: The Invitation -->
        <section class="scroll-section sec-8">
            <h2 class="sec-title">THE INVITATION</h2>
            <div class="sec-subtitle">BEGIN YOUR JOURNEY</div>
            <div class="sec-circle-accent"></div>
            <p class="sec-desc">An extraordinary life deserves extraordinary care. Secure your private consultation and unlock a realm of tailored indulgence.</p>
            <a href="#" onclick="alert('Thank you for your interest! Booking details will be sent shortly.')" class="cta-btn">BOOK PRIVATE SESSION</a>

            <footer class="aura-footer" style="position: absolute; bottom: 0; left: 0; width: 100%; background: transparent; padding: 20px 0; z-index: 10;">
                &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?> &mdash; LUXURY EXPERIENCE
            </footer>
        </section>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
