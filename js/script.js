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

    // Best Sellers Carousel Scroll Listener and Autoplay
    const carouselContainer = document.querySelector('.best-sellers-carousel');
    const dots = document.querySelectorAll('.dot');

    if (carouselContainer && dots.length > 0) {
        let currentIndex = 0;
        let autoplayInterval = null;
        const intervalTime = 3000; // 3 seconds
        let isProgrammaticScroll = false;
        let scrollTimeout = null;

        const scrollToDot = (index) => {
            const scrollWidth = carouselContainer.scrollWidth - carouselContainer.clientWidth;
            const scrollPos = (index / (dots.length - 1)) * scrollWidth;
            isProgrammaticScroll = true;
            carouselContainer.scrollTo({
                left: scrollPos,
                behavior: 'smooth'
            });
            // Reset programmatic flag after smooth scroll is expected to complete
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                isProgrammaticScroll = false;
            }, 800);
        };

        const startAutoplay = () => {
            if (autoplayInterval) clearInterval(autoplayInterval);
            autoplayInterval = setInterval(() => {
                currentIndex = (currentIndex + 1) % dots.length;
                scrollToDot(currentIndex);
            }, intervalTime);
        };

        const stopAutoplay = () => {
            if (autoplayInterval) {
                clearInterval(autoplayInterval);
                autoplayInterval = null;
            }
        };

        carouselContainer.addEventListener('scroll', () => {
            const scrollWidth = carouselContainer.scrollWidth - carouselContainer.clientWidth;
            const scrollPos = carouselContainer.scrollLeft;
            const activeIndex = Math.round((scrollPos / (scrollWidth || 1)) * (dots.length - 1));

            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === activeIndex);
            });

            // If the active index changed due to manual scroll, align currentIndex
            if (!isProgrammaticScroll) {
                currentIndex = activeIndex;
                stopAutoplay();
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    startAutoplay();
                }, 4000); // Resume autoplay 4 seconds after manual scrolling ceases
            }
        });

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                stopAutoplay();
                currentIndex = index;
                scrollToDot(currentIndex);
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    startAutoplay();
                }, 4000); // Resume autoplay 4 seconds after user click
            });
        });

        // Pause on hover
        carouselContainer.addEventListener('mouseenter', () => {
            stopAutoplay();
            clearTimeout(scrollTimeout);
        });

        carouselContainer.addEventListener('mouseleave', () => {
            if (!isProgrammaticScroll) {
                startAutoplay();
            }
        });

        // Initialize autoplay
        startAutoplay();
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
