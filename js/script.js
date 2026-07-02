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
                'mute': 1,
                'rel': 0,
                'showinfo': 0,
                'vq': 'hd1080'
            },
            events: {
                'onReady': (event) => onPlayerReady(event, player)
            }
        });
        players.push(player);
    });
};

function onPlayerReady(event, player) {
    if (typeof player.setPlaybackQuality === 'function') {
        player.setPlaybackQuality('hd1080');
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                player.playVideo();
            } else {
                player.pauseVideo();
            }
        });
    }, { threshold: 0.5 });

    observer.observe(player.getIframe());
}

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

    // Parallax Effect and Overlay Fade for Explore Page
    const exploreBg = document.querySelector('.explore-background-container');
    const exploreOverlay = document.querySelector('.explore-overlay-container');
    if (document.body.classList.contains('explore-page')) {
        window.addEventListener('scroll', () => {
            const scrollValue = window.scrollY;
            const viewportHeight = window.innerHeight;
            const totalScroll = document.documentElement.scrollHeight - viewportHeight;

            // Background Parallax
            if (exploreBg) {
                // Translate upwards as we scroll down to avoid gaps at the top
                // Start at 0, move to -20vh
                const parallaxAmount = (scrollValue / totalScroll) * -20;
                exploreBg.style.transform = `translateY(${parallaxAmount}vh)`;
            }

            // Overlay Fade Logic
            if (exploreOverlay) {
                // Fade out over the first 80% of the viewport height
                const fadeEnd = viewportHeight * 0.8;
                let opacity = 1 - (scrollValue / fadeEnd);

                if (opacity < 0) opacity = 0;
                if (opacity > 1) opacity = 1;

                exploreOverlay.style.opacity = opacity;

                // Toggle visibility to prevent it from blocking clicks when invisible
                if (opacity <= 0) {
                    exploreOverlay.style.visibility = 'hidden';
                } else {
                    exploreOverlay.style.visibility = 'visible';
                }
            }
        });
    }
});
