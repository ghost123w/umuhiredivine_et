document.addEventListener('DOMContentLoaded', () => {
    const contactModal = document.getElementById('contact-modal');

    // Unified Modal Toggle
    window.toggleModal = (id) => {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.toggle('active');
            if (modal.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = 'auto';
            }
        }
    };

    // Open Contact Modal with Subject
    window.openContactModal = (subject = '') => {
        const subjectInput = document.querySelector('#contact-modal input[name="subject"]');
        if (subjectInput && subject) {
            subjectInput.value = subject;
        }
        window.toggleModal('contact-modal');
    };

    // Close on Modal Click
    window.onclick = function(event) {
        if (event.target.classList.contains('aura-modal')) {
            const modalId = event.target.id;
            window.toggleModal(modalId);
        }
    };

    // Handle navigation to modal (for links like href="#contact-modal")
    document.querySelectorAll('a[href="#contact-modal"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            window.toggleModal('contact-modal');
        });
    });

    // Close on ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && contactModal && contactModal.classList.contains('active')) {
            window.toggleModal('contact-modal');
        }
    });


    // Success Toast Auto-hide
    const successToast = document.getElementById('success-toast');
    if (successToast) {
        setTimeout(() => {
            successToast.classList.remove('active');
        }, 3000);
    }

    // Mobile Navigation Toggle
    const mobileToggle = document.getElementById('mobile-nav-toggle');
    const headerMenu = document.getElementById('header-menu');

    // Best Sellers Carousel Scroll Listener
    const carouselContainer = document.querySelector('.best-sellers-carousel');
    const dots = document.querySelectorAll('.dot');

    if (carouselContainer && dots.length > 0) {
        carouselContainer.addEventListener('scroll', () => {
            const scrollWidth = carouselContainer.scrollWidth - carouselContainer.clientWidth;
            const scrollPos = carouselContainer.scrollLeft;
            const activeIndex = Math.round((scrollPos / scrollWidth) * (dots.length - 1));

            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === activeIndex);
            });
        });

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                const scrollWidth = carouselContainer.scrollWidth - carouselContainer.clientWidth;
                const scrollPos = (index / (dots.length - 1)) * scrollWidth;
                carouselContainer.scrollTo({
                    left: scrollPos,
                    behavior: 'smooth'
                });
            });
        });
    }

    if (mobileToggle && headerMenu) {
        mobileToggle.addEventListener('click', () => {
            mobileToggle.classList.toggle('active');
            headerMenu.classList.toggle('active');

            if (headerMenu.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = 'auto';
            }
        });

        // Close menu when clicking a link
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', () => {
                mobileToggle.classList.remove('active');
                headerMenu.classList.remove('active');
                document.body.style.overflow = 'auto';
            });
        });
    }

});

// YouTube IFrame API Initialization
let player;
function onYouTubeIframeAPIReady() {
    if (document.getElementById('youtube-player')) {
        player = new YT.Player('youtube-player', {
            videoId: 'tHEa6HHAdaI',
            playerVars: {
                'autoplay': 0,
                'controls': 0,
                'rel': 0,
                'showinfo': 0,
                'mute': 1,
                'loop': 1,
                'playlist': 'tHEa6HHAdaI',
                'playsinline': 1
            },
            events: {
                'onReady': onPlayerReady
            }
        });
    }
}

function onPlayerReady(event) {
    const observerOptions = {
        root: null,
        threshold: 0.5
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                player.playVideo();
            } else {
                player.pauseVideo();
            }
        });
    }, observerOptions);

    const videoSection = document.querySelector('.video-section');
    if (videoSection) {
        observer.observe(videoSection);
    }
}
