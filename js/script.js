document.addEventListener('DOMContentLoaded', () => {
    const bentoItems = document.querySelectorAll('.bento-item, .bento-card');
    const revealSections = document.querySelectorAll('.reveal-section');

    // 1. Intersection Observer for Scroll-Triggered Reveal Effect
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    revealSections.forEach((section) => {
        revealObserver.observe(section);
    });

    // 2. 3D Tilt Effect on Scroll
    window.addEventListener('scroll', () => {
        const scrolled = window.scrollY;
        const viewportHeight = window.innerHeight;

        bentoItems.forEach(item => {
            const rect = item.getBoundingClientRect();
            // Only apply if the item is somewhat visible
            if (rect.top < viewportHeight && rect.bottom > 0) {
                const itemCenter = rect.top + rect.height / 2;
                const distanceFromCenter = (itemCenter - viewportHeight / 2) / (viewportHeight / 2);

                // Subtle tilt based on scroll position
                const tilt = distanceFromCenter * 5;
                item.style.transform = `perspective(1000px) rotateX(${tilt}deg) translateY(${distanceFromCenter * -10}px)`;

                // For bento-items in dashboard, maybe some extra glow
                if (item.classList.contains('bento-item')) {
                     const intensity = Math.max(0.1, 1 - Math.abs(distanceFromCenter));
                     item.style.borderColor = `rgba(255, 53, 3, ${0.2 * intensity})`;
                }
            }
        });

        // Dynamic Aurora Shift on Scroll (additional to CSS animation)
        const aurora = document.querySelector('.aurora-bg');
        if (aurora) {
            const shift = scrolled * 0.05;
            aurora.style.filter = `blur(80px) hue-rotate(${shift}deg)`;
        }
    });

    // 3. Smooth Scrolling for Navigation
    const navLinks = document.querySelectorAll('.nav-links a, .sidebar-nav a');
    navLinks.forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId && targetId.startsWith('#')) {
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
});
