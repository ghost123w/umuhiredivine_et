document.addEventListener('DOMContentLoaded', () => {
    const navbar = document.querySelector('.aura-nav-compact');
    const contactTrigger = document.getElementById('contact-trigger');
    const contactModal = document.getElementById('contact-modal');
    const modalClose = document.querySelector('.modal-close');
    const modalOverlay = document.querySelector('.modal-overlay');
    const bookUsWrappers = document.querySelectorAll('.book-us-wrapper');
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

    // 2. Intersection Observer for Reveal animations
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, { threshold: 0.15 });

    revealItems.forEach(item => revealObserver.observe(item));

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

    bookUsWrappers.forEach(wrapper => {
        wrapper.addEventListener('click', () => {
            const subject = wrapper.getAttribute('data-subject');
            openModal(subject);
        });
    });

    if (modalClose) modalClose.addEventListener('click', closeModal);
    if (modalOverlay) modalOverlay.addEventListener('click', closeModal);

    // Close on ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && contactModal && contactModal.classList.contains('active')) {
            closeModal();
        }
    });
});
