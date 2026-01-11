/**
 * Nova Studio - Custom JavaScript
 * 
 * @package Nova_Studio
 */

(function() {
    'use strict';

    // DOM Ready
    document.addEventListener('DOMContentLoaded', function() {
        initSmoothScroll();
        initScrollAnimations();
        initNavbarScroll();
    });

    /**
     * Smooth Scroll for anchor links
     */
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    const headerOffset = 80;
                    const elementPosition = target.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    /**
     * Scroll-triggered animations using Intersection Observer
     */
    function initScrollAnimations() {
        const animatedElements = document.querySelectorAll(
            '.nova-service-card, .nova-feature-item, .nova-process-step, .elementor-widget'
        );

        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('nova-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            animatedElements.forEach(el => {
                el.classList.add('nova-animate-on-scroll');
                observer.observe(el);
            });
        } else {
            // Fallback for older browsers
            animatedElements.forEach(el => el.classList.add('nova-visible'));
        }
    }

    /**
     * Navbar background change on scroll
     */
    function initNavbarScroll() {
        const header = document.querySelector('.elementor-location-header, header');
        if (!header) return;

        let lastScroll = 0;
        const scrollThreshold = 100;

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;

            if (currentScroll > scrollThreshold) {
                header.classList.add('nova-header-scrolled');
            } else {
                header.classList.remove('nova-header-scrolled');
            }

            lastScroll = currentScroll;
        }, { passive: true });
    }

    /**
     * Counter Animation (for stats sections)
     */
    window.novaCounterAnimation = function(element, target, duration = 2000) {
        let start = 0;
        const increment = target / (duration / 16);
        
        const updateCounter = () => {
            start += increment;
            if (start < target) {
                element.textContent = Math.ceil(start);
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = target;
            }
        };
        
        updateCounter();
    };

})();
