document.addEventListener('DOMContentLoaded', () => {
    const sections = document.querySelectorAll('.reveal-section');
    const navLinks = document.querySelectorAll('.sidebar-nav a');

    // 1. Intersection Observer for Scroll-Triggered Reveal Effect
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, {
        threshold: 0.15,
        rootMargin: '0px 0px -50px 0px'
    });

    sections.forEach((section) => {
        revealObserver.observe(section);
    });

    // Observe Admin Cards if present
    const adminCards = document.querySelectorAll('.admin-card');
    adminCards.forEach((card) => {
        revealObserver.observe(card);
    });

    // 2. ScrollSpy: Highlight active sidebar link
    const scrollSpyObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');
                navLinks.forEach((link) => {
                    link.classList.remove('active-link');
                    if (link.getAttribute('href') === `#${id}`) {
                        link.classList.add('active-link');
                    }
                });
            }
        });
    }, {
        threshold: 0.5
    });

    sections.forEach((section) => {
        scrollSpyObserver.observe(section);
    });

    // 3. Smooth Scrolling for Sidebar Links
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

    // 4. Parallax Background & 3D Stroll Tilt Effect
    const bgImage = document.querySelector('.stroll-bg-image');
    const sidebar = document.querySelector('.layout-sidebar');

    window.addEventListener('scroll', () => {
        const scrolled = window.scrollY;
        const viewportHeight = window.innerHeight;

        // Background parallax
        if (bgImage) {
            const val = scrolled * 0.15;
            bgImage.style.transform = `translate3d(0, ${val}px, 0)`;
        }

        // Sidebar 3D Tilt
        if (sidebar) {
            const sidebarScrollProgress = Math.min(1, scrolled / 500);
            const sidebarTilt = sidebarScrollProgress * 3; // Tilt up to 3 degrees
            const sidebarRotateY = sidebarScrollProgress * 2; // Subtle side rotate
            sidebar.style.transform = `perspective(1000px) rotateX(${sidebarTilt}deg) rotateY(${sidebarRotateY}deg) translateZ(10px)`;
        }

        // 3D Stroll Tilt for Sections
        sections.forEach(section => {
            if (section.classList.contains('active')) {
                const rect = section.getBoundingClientRect();
                const sectionCenter = rect.top + rect.height / 2;
                const distanceFromCenter = (sectionCenter - viewportHeight / 2) / (viewportHeight / 2);

                // Subtle tilt based on scroll position
                const tilt = distanceFromCenter * 5;
                section.style.transform = `perspective(1200px) rotateX(${tilt}deg) translateZ(0)`;
            }
        });

        // 3D Stroll Tilt for Admin Cards & Dynamic Glow
        adminCards.forEach(card => {
            if (card.classList.contains('active')) {
                const rect = card.getBoundingClientRect();
                const cardCenter = rect.top + rect.height / 2;
                const distanceFromCenter = (cardCenter - viewportHeight / 2) / (viewportHeight / 2);

                const tilt = distanceFromCenter * 4;
                card.style.transform = `perspective(1200px) rotateX(${tilt}deg) translateZ(0)`;

                // Dynamic Glow based on position
                const intensity = Math.max(0.2, 1 - Math.abs(distanceFromCenter));
                card.style.boxShadow = `0 ${10 * intensity}px ${30 * intensity}px rgba(255, 53, 3, ${0.3 * intensity})`;
            }
        });

        // Dynamic Background Shift on Scroll
        const header = document.querySelector('.layout-header');
        if (header) {
            const opacity = Math.min(0.95, 0.8 + (scrolled / 500));
            header.style.background = `linear-gradient(135deg, rgba(255, 53, 3, ${opacity}) 0%, rgba(255, 122, 92, ${opacity}) 100%)`;
        }
    });
});
