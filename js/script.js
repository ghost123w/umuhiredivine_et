const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -100px 0px"
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add('active');

            // Update Section Nav
            const sectionId = entry.target.id;
            document.querySelectorAll('.section-dot').forEach(dot => {
                if (dot.dataset.section === sectionId) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        }
    });
}, observerOptions);

document.querySelectorAll('.reveal-section').forEach((section) => {
    observer.observe(section);
});

// Smooth scroll for nav links
document.querySelectorAll('.admin-nav-minimal a, .sidebar-nav a, .section-dot, .main-nav a').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href && href.startsWith('#')) {
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
    const viewportHeight = window.innerHeight;

    // Smooth Parallax for cards based on viewport position
    const cards = document.querySelectorAll('.portrait-card');
    cards.forEach((card) => {
        const rect = card.getBoundingClientRect();
        const cardCenter = rect.top + rect.height / 2;
        const viewCenter = viewportHeight / 2;

        // Calculate distance from center of viewport (-1 to 1)
        const distanceFromCenter = (cardCenter - viewCenter) / (viewportHeight / 2);

        // Apply parallax based on proximity to center
        const yOffset = distanceFromCenter * 30; // 30px max offset
        const rotation = distanceFromCenter * -5; // -5deg to 5deg

        card.style.setProperty('--parallax-y', `${yOffset}px`);
        card.style.setProperty('--parallax-rot', `${rotation}deg`);
    });

});
