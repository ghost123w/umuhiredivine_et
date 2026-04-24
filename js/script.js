document.addEventListener('DOMContentLoaded', () => {
    const sections = document.querySelectorAll('.reveal-section, .aura-hero');
    const navLinks = document.querySelectorAll('.nav-item');
    const navbar = document.querySelector('.aura-nav-bar');
    const bgImage = document.querySelector('.stroll-bg-image');

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

    document.querySelectorAll('.reveal-section').forEach((section) => {
        revealObserver.observe(section);
    });

    // 2. ScrollSpy: Highlight active navigation link
    const scrollSpyObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');
                navLinks.forEach((link) => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${id}`) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }, {
        threshold: 0.3,
        rootMargin: '-10% 0px -60% 0px'
    });

    sections.forEach((section) => {
        scrollSpyObserver.observe(section);
    });

    // 3. Smooth Scrolling for Navigation Links
    navLinks.forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId.startsWith('#')) {
                e.preventDefault();
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }
        });
    });

    // 4. Parallax Background Effect & Navbar Scroll Class
    window.addEventListener('scroll', () => {
        const scrolled = window.scrollY;

        // Parallax
        if (bgImage) {
            const val = scrolled * 0.1;
            bgImage.style.transform = `translate3d(0, ${val}px, 0)`;
        }

        // Navbar scrolled state
        if (navbar) {
            if (scrolled > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }
    });

    // 5. FAQ Accordion Toggle
    const accordionHeaders = document.querySelectorAll('.accordion-header');
    accordionHeaders.forEach(header => {
        header.addEventListener('click', () => {
            const item = header.parentElement;
            item.classList.toggle('active');

            // Optional: Close other items when one is opened
            const siblings = item.parentElement.querySelectorAll('.accordion-item');
            siblings.forEach(sibling => {
                if (sibling !== item) {
                    sibling.classList.remove('active');
                }
            });
        });
    });
});
