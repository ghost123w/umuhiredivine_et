document.addEventListener('DOMContentLoaded', () => {
    const sections = document.querySelectorAll('.page-section, .hero-section');
    const revealItems = document.querySelectorAll('.reveal-item');
    const navLinks = document.querySelectorAll('.nav-item');
    const navbar = document.querySelector('.aura-nav-compact');
    const bgImage = document.querySelector('.stroll-bg-image');

    // 1. Intersection Observer for Reveal Effect (Bento Grid & Cards)
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, {
        threshold: 0.15,
        rootMargin: '0px 0px -100px 0px'
    });

    revealItems.forEach(item => {
        revealObserver.observe(item);
    });

    // 2. ScrollSpy Implementation
    const scrollSpyOptions = {
        threshold: 0.2,
        rootMargin: '0px 0px -30% 0px'
    };

    const scrollSpyObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id') || 'hero';
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
            if (scrolled > 80) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }

        // Refined Parallax Effect
        if (bgImage) {
            // Subtle upward shift to maintain focus
            const val = scrolled * 0.08;
            bgImage.style.transform = `scale(1.15) translate3d(0, ${-val}px, 0)`;
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
                    const offset = 80;
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - offset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });

    // 5. Contact Modal Logic
    const contactTrigger = document.getElementById('contact-trigger');
    const bookUsButtons = document.querySelectorAll('.book-us-btn');
    const contactModal = document.getElementById('contact-modal');
    const modalClose = document.querySelector('.modal-close');
    const modalOverlay = document.querySelector('.modal-overlay');

    if (contactModal) {
        const openModal = (subject = null) => {
            if (subject) {
                const subjectSelect = contactModal.querySelector('select[name="subject"]');
                if (subjectSelect) {
                    // Try to match subject or default to "Masterpiece Request"
                    let found = false;
                    for (let option of subjectSelect.options) {
                        if (option.value === subject) {
                            subjectSelect.value = subject;
                            found = true;
                            break;
                        }
                    }
                    if (!found) {
                        // Dynamically add option if it's a specific feature
                        const newOpt = new Option(subject, subject);
                        subjectSelect.add(newOpt);
                        subjectSelect.value = subject;
                    }
                }
            }
            contactModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        };

        if (contactTrigger) {
            contactTrigger.addEventListener('click', (e) => {
                e.preventDefault();
                openModal();
            });
        }

        bookUsButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const subject = btn.getAttribute('data-subject');
                openModal(subject);
            });
        });

        const closeModal = () => {
            contactModal.classList.remove('active');
            document.body.style.overflow = 'auto';
        };

        if (modalClose) modalClose.addEventListener('click', closeModal);
        if (modalOverlay) modalOverlay.addEventListener('click', closeModal);

        // Close on ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && contactModal.classList.contains('active')) {
                closeModal();
            }
        });
    }

    // Auto-hide notification toast
    const toast = document.querySelector('.notification-toast');
    if (toast) {
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
            toast.style.transition = '0.5s cubic-bezier(0.4, 0, 0.2, 1)';
            setTimeout(() => toast.remove(), 600);
        }, 5000);
    }
});
