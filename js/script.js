const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -100px 0px"
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add('active');
        } else {
            // Keep the exit animation if desired, or remove for one-way reveal
            // entry.target.classList.remove('active');
        }
    });
}, observerOptions);

document.querySelectorAll('.reveal-section').forEach((section) => {
    observer.observe(section);
});

// Smooth scroll for nav links
document.querySelectorAll('.admin-nav-minimal a, .sidebar-nav a').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        if (this.getAttribute('href').startsWith('#')) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    });
});

// Advanced dynamic interactions
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;

    // Smooth Parallax for cards and background
    const cards = document.querySelectorAll('.portrait-card');
    cards.forEach((card, index) => {
        const speed = 0.05 * (index + 1);
        const yOffset = -(scrolled * speed);
        // Using CSS variables to handle parallax so it doesn't fight with the .active reveal transform
        card.style.setProperty('--parallax-y', `${yOffset}px`);
        card.style.setProperty('--parallax-rot', `${scrolled * 0.01}deg`);
    });

    // Header shimmer intensity based on scroll
    const header = document.querySelector('.layout-header h1');
    if (header) {
        const opacity = Math.max(0.2, 1 - scrolled / 400);
        header.style.opacity = opacity;
        header.style.transform = `translateX(-50%) translateY(${scrolled * 0.2}px)`;
    }
});
