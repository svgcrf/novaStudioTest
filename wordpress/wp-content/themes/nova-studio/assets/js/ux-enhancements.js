/**
 * Nova Studio - UX Enhancements
 * WhatsApp Button, Animated Counters, Scroll Effects
 */

(function() {
    'use strict';
    
    // =============================================
    // ANIMATED COUNTERS
    // =============================================
    
    function initCounters() {
        var counters = document.querySelectorAll('.nova-counter');
        if (!counters.length) return;
        
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        counters.forEach(function(counter) {
            observer.observe(counter);
        });
    }
    
    function animateCounter(element) {
        var target = parseInt(element.dataset.target) || 0;
        var duration = parseInt(element.dataset.duration) || 2000;
        var suffix = element.dataset.suffix || '';
        var start = 0;
        var startTime = null;
        
        function easeOutQuart(t) {
            return 1 - Math.pow(1 - t, 4);
        }
        
        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);
            var easedProgress = easeOutQuart(progress);
            var current = Math.floor(easedProgress * target);
            
            element.textContent = current.toLocaleString() + suffix;
            
            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                element.textContent = target.toLocaleString() + suffix;
            }
        }
        
        requestAnimationFrame(step);
    }
    
    // =============================================
    // SCROLL PROGRESS BAR
    // =============================================
    
    function initScrollProgress() {
        var progressBar = document.getElementById('nova-scroll-progress');
        if (!progressBar) return;
        
        window.addEventListener('scroll', function() {
            var winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            var height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            var scrolled = (winScroll / height) * 100;
            progressBar.style.width = scrolled + '%';
        });
    }
    
    // =============================================
    // BACK TO TOP BUTTON
    // =============================================
    
    function initBackToTop() {
        var button = document.getElementById('nova-back-to-top');
        if (!button) return;
        
        window.addEventListener('scroll', function() {
            if (window.scrollY > 500) {
                button.classList.add('visible');
            } else {
                button.classList.remove('visible');
            }
        });
        
        button.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // =============================================
    // WHATSAPP FLOATING BUTTON
    // =============================================
    
    function initWhatsAppButton() {
        var button = document.getElementById('nova-whatsapp-btn');
        if (!button) return;
        
        // Show after 3 seconds
        setTimeout(function() {
            button.classList.add('visible');
        }, 3000);
        
        // Pulse animation every 10 seconds
        setInterval(function() {
            button.classList.add('pulse');
            setTimeout(function() {
                button.classList.remove('pulse');
            }, 1000);
        }, 10000);
    }
    
    // =============================================
    // SCROLL ANIMATIONS (Reveal on scroll)
    // =============================================
    
    function initScrollAnimations() {
        var elements = document.querySelectorAll('.nova-animate-on-scroll');
        if (!elements.length) return;
        
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
        
        elements.forEach(function(el) {
            observer.observe(el);
        });
    }
    
    // =============================================
    // TYPING EFFECT
    // =============================================
    
    function initTypingEffect() {
        var elements = document.querySelectorAll('.nova-typing');
        
        elements.forEach(function(element) {
            var text = element.dataset.text || element.textContent;
            var speed = parseInt(element.dataset.speed) || 50;
            element.textContent = '';
            element.style.visibility = 'visible';
            
            var i = 0;
            function type() {
                if (i < text.length) {
                    element.textContent += text.charAt(i);
                    i++;
                    setTimeout(type, speed);
                }
            }
            
            var observer = new IntersectionObserver(function(entries) {
                if (entries[0].isIntersecting) {
                    type();
                    observer.unobserve(element);
                }
            });
            observer.observe(element);
        });
    }
    
    // =============================================
    // SMOOTH ANCHOR SCROLL
    // =============================================
    
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
            anchor.addEventListener('click', function(e) {
                var href = this.getAttribute('href');
                if (href === '#') return;
                
                var target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    var headerHeight = document.querySelector('.site-header')?.offsetHeight || 80;
                    var targetPosition = target.offsetTop - headerHeight;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                    
                    // Update URL without jump
                    history.pushState(null, null, href);
                }
            });
        });
    }
    
    // =============================================
    // PARALLAX EFFECT
    // =============================================
    
    function initParallax() {
        var parallaxElements = document.querySelectorAll('.nova-parallax');
        if (!parallaxElements.length) return;
        
        window.addEventListener('scroll', function() {
            var scrolled = window.pageYOffset;
            
            parallaxElements.forEach(function(element) {
                var speed = parseFloat(element.dataset.speed) || 0.5;
                var yPos = -(scrolled * speed);
                element.style.transform = 'translateY(' + yPos + 'px)';
            });
        });
    }
    
    // =============================================
    // FORM FIELD FOCUS EFFECTS
    // =============================================
    
    function initFormEffects() {
        var inputs = document.querySelectorAll('.nova-lead-form input, .nova-lead-form textarea, .nova-lead-form select');
        
        inputs.forEach(function(input) {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
                if (this.value) {
                    this.parentElement.classList.add('has-value');
                } else {
                    this.parentElement.classList.remove('has-value');
                }
            });
        });
    }
    
    // =============================================
    // INITIALIZE ALL
    // =============================================
    
    document.addEventListener('DOMContentLoaded', function() {
        initCounters();
        initScrollProgress();
        initBackToTop();
        initWhatsAppButton();
        initScrollAnimations();
        initTypingEffect();
        initSmoothScroll();
        initParallax();
        initFormEffects();
    });
    
    // Expose to global
    window.NovaUX = {
        animateCounter: animateCounter
    };
    
})();
