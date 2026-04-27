document.addEventListener('DOMContentLoaded', () => {
    const sections = document.querySelectorAll('.page-section, .hero-section');
    const revealSections = document.querySelectorAll('.reveal-section');
    const navLinks = document.querySelectorAll('.nav-item');
    const navbar = document.querySelector('.aura-nav-bar');
    const bgImage = document.querySelector('.stroll-bg-image');

    // 1. Intersection Observer for Reveal Effect
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    revealSections.forEach(section => {
        revealObserver.observe(section);
    });

    // 2. ScrollSpy Implementation
    const scrollSpyOptions = {
        threshold: 0.3,
        rootMargin: '0px 0px -20% 0px'
    };

    const scrollSpyObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id') || '#';
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${id}`) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }, scrollSpyOptions);

    sections.forEach(section => {
        scrollSpyObserver.observe(section);
    });

    // 3. Navbar Scroll Effect & Parallax
    window.addEventListener('scroll', () => {
        const scrolled = window.scrollY;

        // Navbar appearance
        if (navbar) {
            if (scrolled > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }

        // Parallax Effect
        if (bgImage) {
            // Subtle parallax that moves upward to avoid gaps at the top
            // scale(1.2) provides 10% margin top/bottom.
            // 0.05 factor means 100px move for 2000px scroll.
            const val = scrolled * 0.05;
            bgImage.style.transform = `scale(1.2) translate3d(0, ${-val}px, 0)`;
        }
    });

    // 4. Smooth Scrolling for Navigation
    navLinks.forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId.startsWith('#')) {
                e.preventDefault();
                const targetElement = document.querySelector(targetId === '#' ? 'body' : targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
});
