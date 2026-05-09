document.addEventListener('DOMContentLoaded', () => {
    const navbar = document.querySelector('.aura-nav-compact');
    const contactTrigger = document.getElementById('contact-trigger');
    const contactModal = document.getElementById('contact-modal');
    const modalClose = document.querySelector('.modal-close');
    const modalOverlay = document.querySelector('.modal-overlay');
    const revealItems = document.querySelectorAll('.reveal-item');
    const bgImage = document.querySelector('.stroll-bg-image');

    // 1. Scroll-driven Navigation effect
    window.addEventListener('scroll', () => {
        const scrolled = window.scrollY;
        if (scrolled > 80) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }

        // Parallax Effect
        if (bgImage) {
            const val = scrolled * 0.1;
            bgImage.style.transform = `scale(1.2) translate3d(0, ${-val}px, 0)`;
        }
    });

    // 2. 3D Scroll Stroll Logic
    const handle3DScroll = () => {
        const viewportCenter = window.innerHeight / 2;

        revealItems.forEach(item => {
            const rect = item.getBoundingClientRect();
            const cardCenter = rect.top + rect.height / 2;
            const distance = cardCenter - viewportCenter;

            // Normalize distance (percentage of viewport height)
            const normalizedDist = distance / (window.innerHeight / 2);

            // 3D Transforms
            const rotation = normalizedDist * 15; // Max 15deg tilt
            const translation = Math.abs(normalizedDist) * -100; // Recede into distance
            const opacity = 1 - Math.min(Math.abs(normalizedDist) * 0.5, 0.7);

            item.style.transform = `
                rotateX(${rotation}deg)
                translateZ(${translation}px)
            `;
            item.style.opacity = opacity;
        });
    };

    window.addEventListener('scroll', handle3DScroll);
    handle3DScroll(); // Initial call

    // 3. Modal Logic
    const openModal = (subject = null) => {
        if (subject) {
            const subjectSelect = contactModal.querySelector('select[name="subject"]');
            if (subjectSelect) {
                let found = false;
                for (let option of subjectSelect.options) {
                    if (option.value === subject) {
                        subjectSelect.value = subject;
                        found = true;
                        break;
                    }
                }
                if (!found) {
                    const newOpt = new Option(subject, subject);
                    subjectSelect.add(newOpt);
                    subjectSelect.value = subject;
                }
            }
        }
        contactModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    const closeModal = () => {
        contactModal.classList.remove('active');
        document.body.style.overflow = 'auto';
    };

    if (contactTrigger) {
        contactTrigger.addEventListener('click', (e) => {
            e.preventDefault();
            openModal();
        });
    }


    if (modalClose) modalClose.addEventListener('click', closeModal);
    if (modalOverlay) modalOverlay.addEventListener('click', closeModal);

    // Close on ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && contactModal && contactModal.classList.contains('active')) {
            closeModal();
        }
    });
});
