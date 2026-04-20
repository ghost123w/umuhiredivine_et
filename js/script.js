const observerOptions = {
    threshold: 0.2,
    rootMargin: "0px 0px -10% 0px"
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
        } else {
            // Re-triggerable: remove active when out of view
            entry.target.classList.remove('active');
        }
    });
}, observerOptions);

// Separate observer for Navigation Visibility
const navObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.target.id === 'features') {
            if (entry.isIntersecting) {
                document.body.classList.add('features-active');
            } else {
                if (entry.boundingClientRect.top > 0) {
                    document.body.classList.remove('features-active');
                }
            }
        }
        if (entry.target.id === 'contact') {
            if (entry.isIntersecting) {
                document.body.classList.add('contact-active');
            } else {
                if (entry.boundingClientRect.top > 0) {
                    document.body.classList.remove('contact-active');
                }
            }
        }
    });
}, { threshold: 0.01 });

document.querySelectorAll('.reveal-section').forEach((section) => {
    observer.observe(section);
});

const featureContainer = document.getElementById('features');
if (featureContainer) {
    navObserver.observe(featureContainer);
}
const contactSection = document.getElementById('contact');
if (contactSection) {
    navObserver.observe(contactSection);
}

// Modal Logic
const contactModal = document.getElementById('contact-modal');
const modalClose = document.querySelector('.modal-close');
const modalOverlay = document.querySelector('.aura-modal-overlay');

function openModal() {
    contactModal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    contactModal.classList.remove('active');
    document.body.style.overflow = '';
}

if (modalClose) modalClose.addEventListener('click', closeModal);
if (modalOverlay) modalOverlay.addEventListener('click', closeModal);

// Smooth scroll and Modal trigger
document.querySelectorAll('.admin-nav-minimal a, .sidebar-nav a, .section-dot, .main-nav a, .cta').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');

        if (href === '#contact') {
            e.preventDefault();
            openModal();
            return;
        }

        if (href && href.startsWith('#')) {
            e.preventDefault();
            const target = document.querySelector(href);
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
