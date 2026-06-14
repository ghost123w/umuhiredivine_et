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

    // --- Cart Functionality ---
    let cart = [];
    const cartCountEl = document.querySelector('.cart-count');
    const cartTotalEl = document.querySelector('.cart-total');
    const floatingCart = document.querySelector('.cart-floating-btn');

    const updateCartUI = () => {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        const totalPrice = cart.reduce((sum, item) => {
            // Remove currency symbol and commas for calculation
            const price = parseFloat(item.price.replace(/[^\d.]/g, '')) || 0;
            return sum + (price * item.quantity);
        }, 0);

        if (totalItems > 0) {
            if (cartCountEl) cartCountEl.textContent = `${totalItems} ITEM${totalItems !== 1 ? 'S' : ''}`;
            if (cartTotalEl) cartTotalEl.textContent = `₦${totalPrice.toLocaleString()}`;
            if (cartTotalEl) cartTotalEl.style.display = 'block';
        } else {
            if (cartCountEl) cartCountEl.textContent = 'CART EMPTY';
            if (cartTotalEl) cartTotalEl.style.display = 'none';
        }

        if (floatingCart) {
            floatingCart.classList.add('active');
        }
    };

    document.querySelectorAll('.add-to-cart-btn-v3').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const itemContainer = e.target.closest('.menu-item-v3');
            const name = itemContainer.querySelector('.item-v3-name').textContent;
            const price = itemContainer.querySelector('.item-v3-price').textContent;

            const existingItem = cart.find(item => item.name === name);
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({ name, price, quantity: 1 });
            }

            updateCartUI();

            // Feedback animation
            if (!btn.classList.contains('adding')) {
                btn.classList.add('adding');
                const btnText = btn.childNodes[0];
                const originalText = btnText.textContent;
                btnText.textContent = 'ADDED! ';

                setTimeout(() => {
                    btnText.textContent = originalText;
                    btn.classList.remove('adding');
                }, 1500);
            }
        });
    });

    // --- Sticky Nav Active State ---
    const menuNavLinks = document.querySelectorAll('.menu-nav-link');
    const sections = document.querySelectorAll('section[id^="cat-"]');

    const handleNavActive = () => {
        let current = "";
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (window.scrollY >= (sectionTop - 150)) {
                current = section.getAttribute('id');
            }
        });

        menuNavLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href').includes(current) && current !== "") {
                link.classList.add('active');
            }
        });
    };

    window.addEventListener('scroll', handleNavActive);

    // Initial UI state
    updateCartUI();
    handleNavActive();
});
