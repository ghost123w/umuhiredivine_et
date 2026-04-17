document.addEventListener('DOMContentLoaded', () => {
    const sections = document.querySelectorAll('.reveal-section');
    const navLinks = document.querySelectorAll('.layout-sidebar a');

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

    // 2. ScrollSpy: Highlight active sidebar link
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

    // 4. Parallax Background Effect
    const bgImage = document.querySelector('.stroll-bg-image');
    if (bgImage) {
        window.addEventListener('scroll', () => {
            const scrolled = window.scrollY;
            const val = scrolled * 0.15;
            bgImage.style.transform = `translate3d(0, ${val}px, 0)`;
        });
    }
});
