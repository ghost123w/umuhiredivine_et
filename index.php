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

        /* Prevent vertical overflow on the layout-main wrapper */
        .explore-page .layout-main {
            padding: 0 !important;
        }

        /* Custom styling for the landing page body to ensure immersive luxury aesthetic */
        body.explore-page {
            background-color: #000000;
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

    <main class="layout-main" style="min-height: 50vh;">
        <!-- Body content deleted per request -->
    </main>

    <footer class="aura-footer" style="position: relative; z-index: 100; background: #000;">
        &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?> &mdash; LUXURY EXPERIENCE
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
