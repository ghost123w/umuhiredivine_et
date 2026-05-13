document.addEventListener('DOMContentLoaded', () => {
    const navbar = document.querySelector('.aura-nav-luxury');
    const contactTrigger = document.getElementById('contact-trigger');
    const sideContact = document.getElementById('side-contact');
    const contactModal = document.getElementById('contact-modal');
    const modalClose = document.querySelector('.modal-close');
    const modalOverlay = document.querySelector('.modal-overlay');
    const revealItems = document.querySelectorAll('.reveal-item');
    const dotNavs = document.querySelectorAll('.dot-nav');
    const featuresSection = document.getElementById('features');

    // 1. Scroll-driven Navigation effect
    window.addEventListener('scroll', () => {
        const scrolled = window.scrollY;
        if (navbar) {
            if (scrolled > 120) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }

        // Toggle dot nav visibility and active state
        if (featuresSection) {
            const rect = featuresSection.getBoundingClientRect();
            if (rect.top < window.innerHeight * 0.8) {
                document.body.classList.add('features-active');
            } else {
                document.body.classList.remove('features-active');
            }
        }

        // Dot Nav Active State
        let currentSection = '';
        const sections = document.querySelectorAll('#hero, #features');
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            if (window.scrollY >= sectionTop - 150) {
                currentSection = section.getAttribute('id');
            }
        });

        dotNavs.forEach(dot => {
            dot.classList.remove('active');
            if (currentSection && dot.getAttribute('href') === `#${currentSection}`) {
                dot.classList.add('active');
            }
        });
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
            const rotation = normalizedDist * 12; // Max 12deg tilt
            const translation = Math.abs(normalizedDist) * -150; // Recede into distance
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
    window.openContactModal = (subject = null) => {
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

    window.closeModal = () => {
        contactModal.classList.remove('active');
        document.body.style.overflow = 'auto';
    };

    if (contactTrigger) {
        contactTrigger.addEventListener('click', (e) => {
            e.preventDefault();
            window.openContactModal();
        });
    }

    if (sideContact) {
        sideContact.addEventListener('click', (e) => {
            e.preventDefault();
            window.openContactModal();
        });
    }


    if (modalClose) modalClose.addEventListener('click', window.closeModal);
    if (modalOverlay) modalOverlay.addEventListener('click', window.closeModal);

    // Close on ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && contactModal && contactModal.classList.contains('active')) {
            closeModal();
        }
    });
});
