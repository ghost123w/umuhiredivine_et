// YouTube API Multi-video support
const players = [];

window.onYouTubeIframeAPIReady = function() {
    const youtubePlayers = document.querySelectorAll('.youtube-player');
    youtubePlayers.forEach((element) => {
        const videoId = element.getAttribute('data-video-id');
        const player = new YT.Player(element, {
            height: '100%',
            width: '100%',
            videoId: videoId,
            playerVars: {
                'autoplay': 0,
                'controls': 0,
                'modestbranding': 1,
                'loop': 1,
                'playlist': videoId,
                'mute': 0,
                'rel': 0,
                'showinfo': 0
            },
            events: {
                'onReady': (event) => onPlayerReady(event, player)
            }
        });
        players.push(player);
    });
};

function onPlayerReady(event, player) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // User explicitly wants unmuted playback
                player.unMute();
                player.setVolume(100);
                player.playVideo();
            } else {
                player.pauseVideo();
            }
        });
    }, { threshold: 0.5 });

    observer.observe(player.getIframe());
}

document.addEventListener('DOMContentLoaded', () => {
    // Global interaction listener to ensure unmuted playback is allowed by browser policies
    const unmuteAll = () => {
        players.forEach(player => {
            if (player && typeof player.unMute === 'function') {
                player.unMute();
                player.setVolume(100);
            }
        });
        // Remove listener once interaction is established
        document.removeEventListener('click', unmuteAll);
        document.removeEventListener('touchstart', unmuteAll);
    };

    document.addEventListener('click', unmuteAll);
    document.addEventListener('touchstart', unmuteAll);

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
