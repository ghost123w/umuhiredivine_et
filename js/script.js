document.addEventListener('DOMContentLoaded', () => {
    const sections = document.querySelectorAll('.reveal-section');

    // Function to show a specific section and hide others
    const showSection = (id) => {
        sections.forEach(s => {
            s.classList.remove('active');
            // We use a small timeout to ensure display: flex is set before opacity transition
            if (s.id === id || s.id === id.substring(1)) {
                s.style.display = 'flex';
                setTimeout(() => s.classList.add('active'), 10);
            } else {
                s.classList.remove('active');
                s.style.display = 'none';
            }
        });
    };

    // Show first section by default if any exist
    if (sections.length > 0) {
        showSection(sections[0].id);
    }

    // Parallax stroll effect
    const bgImage = document.querySelector('.stroll-bg-image');
    if (bgImage) {
        window.addEventListener('scroll', () => {
            const scrolled = window.scrollY;
            const val = scrolled * 0.15;
            bgImage.style.transform = `translate3d(0, ${val}px, 0)`;
        });
    }

    // Handle sidebar links to toggle sections
    document.querySelectorAll('.sidebar-nav a').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            showSection(targetId);

            // Optional: Scroll to top of the main area when switching
            const main = document.querySelector('.layout-main');
            if (main) {
                window.scrollTo({
                    top: main.offsetTop - 20,
                    behavior: 'smooth'
                });
            }
        });
    });
});
