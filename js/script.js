const observerOptions = {
    threshold: 0.2,
    rootMargin: "0px 0px -10% 0px"
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add('active');
            const sectionId = entry.target.id;
            document.querySelectorAll('.section-dot').forEach(dot => {
                if (dot.dataset.section === sectionId) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        } else {
            entry.target.classList.remove('active');
        }
    });
}, observerOptions);

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

// Navigation
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href === '#contact') {
            e.preventDefault();
            openModal();
            return;
        }

        e.preventDefault();
        const target = document.querySelector(href);
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Interactive Background Tracking
document.addEventListener('mousemove', (e) => {
    const x = (e.clientX / window.innerWidth) * 100;
    const y = (e.clientY / window.innerHeight) * 100;
    document.documentElement.style.setProperty('--mouse-x', `${x}%`);
    document.documentElement.style.setProperty('--mouse-y', `${y}%`);
});

// Parallax Effect
window.addEventListener('scroll', () => {
    const viewportHeight = window.innerHeight;
    const cards = document.querySelectorAll('.portrait-card');

    cards.forEach((card) => {
        const rect = card.getBoundingClientRect();
        const cardCenter = rect.top + rect.height / 2;
        const viewCenter = viewportHeight / 2;
        const distanceFromCenter = (cardCenter - viewCenter) / (viewportHeight / 2);

        const yOffset = distanceFromCenter * 30;
        const rotation = distanceFromCenter * -5;

        card.style.setProperty('--parallax-y', `${yOffset}px`);
        card.style.setProperty('--parallax-rot', `${rotation}deg`);
    });
});
