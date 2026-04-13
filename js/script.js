document.addEventListener('DOMContentLoaded', () => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('active'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal-section').forEach(s => observer.observe(s));

    // Parallax stroll effect
    const bgImage = document.querySelector('.stroll-bg-image');
    if (bgImage) {
        window.addEventListener('scroll', () => {
            const scrolled = window.scrollY;
            const val = scrolled * 0.15;
            bgImage.style.transform = `translate3d(0, ${val}px, 0)`;
        });
    }

    // Smooth scroll for sidebar links
    document.querySelectorAll('.sidebar-nav a').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 100, // Offset for header/padding
                    behavior: 'smooth'
                });
            }
        });
    });
});
