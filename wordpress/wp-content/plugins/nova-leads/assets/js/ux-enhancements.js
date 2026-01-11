/**
 * Nova Leads UX Enhancements
 * Exit popup, WhatsApp widget, animated counters
 */

(function($) {
    'use strict';
    
    // Configuration
    const config = {
        exitPopupDelay: novaLeads?.exitPopup?.delay || 5000,
        exitPopupEnabled: novaLeads?.exitPopup?.enabled ?? true,
        whatsappNumber: novaLeads?.whatsapp?.number || '',
        whatsappMessage: novaLeads?.whatsapp?.message || '',
    };
    
    // Track if exit popup has been shown
    let exitPopupShown = false;
    let pageLoadTime = Date.now();
    
    /**
     * Initialize Exit Intent Popup
     */
    function initExitPopup() {
        if (!config.exitPopupEnabled) return;
        
        // Only on desktop
        if (window.innerWidth < 768) return;
        
        // Create popup HTML if not exists
        if (!$('#nova-exit-popup').length) {
            createExitPopup();
        }
        
        // Mouse leave detection
        $(document).on('mouseleave', function(e) {
            if (e.clientY < 10 && !exitPopupShown) {
                const timeOnPage = Date.now() - pageLoadTime;
                
                // Only show after minimum time on page
                if (timeOnPage > config.exitPopupDelay) {
                    showExitPopup();
                }
            }
        });
    }
    
    /**
     * Create Exit Popup HTML
     */
    function createExitPopup() {
        const title = novaLeads?.exitPopup?.title || '¿Te vas tan pronto?';
        const text = novaLeads?.exitPopup?.text || 'Déjanos tu email y recibe información exclusiva';
        
        const popupHTML = `
            <div id="nova-exit-popup" class="nova-exit-popup" style="display: none;">
                <div class="nova-exit-popup-overlay"></div>
                <div class="nova-exit-popup-content">
                    <button class="nova-exit-popup-close">&times;</button>
                    <div class="nova-exit-popup-icon">🎁</div>
                    <h3 class="nova-exit-popup-title">${title}</h3>
                    <p class="nova-exit-popup-text">${text}</p>
                    <form class="nova-exit-popup-form" id="nova-exit-form">
                        <input type="email" name="email" placeholder="Tu email" required>
                        <button type="submit">¡Lo quiero!</button>
                    </form>
                    <p class="nova-exit-popup-privacy">🔒 No spam. Cancelar cuando quieras.</p>
                </div>
            </div>
            <style>
                .nova-exit-popup {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    z-index: 99999;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .nova-exit-popup-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.7);
                    backdrop-filter: blur(5px);
                }
                .nova-exit-popup-content {
                    position: relative;
                    background: white;
                    padding: 50px;
                    border-radius: 20px;
                    max-width: 450px;
                    width: 90%;
                    text-align: center;
                    animation: novaPopupIn 0.4s ease;
                    box-shadow: 0 25px 50px rgba(0,0,0,0.3);
                }
                @keyframes novaPopupIn {
                    from {
                        opacity: 0;
                        transform: scale(0.8) translateY(-20px);
                    }
                    to {
                        opacity: 1;
                        transform: scale(1) translateY(0);
                    }
                }
                .nova-exit-popup-close {
                    position: absolute;
                    top: 15px;
                    right: 20px;
                    background: none;
                    border: none;
                    font-size: 30px;
                    cursor: pointer;
                    color: #999;
                    transition: color 0.3s;
                }
                .nova-exit-popup-close:hover {
                    color: #333;
                }
                .nova-exit-popup-icon {
                    font-size: 60px;
                    margin-bottom: 20px;
                }
                .nova-exit-popup-title {
                    font-size: 28px;
                    margin-bottom: 15px;
                    color: #1a1a2e;
                }
                .nova-exit-popup-text {
                    font-size: 16px;
                    color: #666;
                    margin-bottom: 25px;
                }
                .nova-exit-popup-form {
                    display: flex;
                    gap: 10px;
                    margin-bottom: 15px;
                }
                .nova-exit-popup-form input {
                    flex: 1;
                    padding: 15px 20px;
                    border: 2px solid #eee;
                    border-radius: 10px;
                    font-size: 16px;
                    transition: border-color 0.3s;
                }
                .nova-exit-popup-form input:focus {
                    outline: none;
                    border-color: #6c63ff;
                }
                .nova-exit-popup-form button {
                    padding: 15px 25px;
                    background: linear-gradient(135deg, #6c63ff 0%, #4834d4 100%);
                    color: white;
                    border: none;
                    border-radius: 10px;
                    font-size: 16px;
                    font-weight: bold;
                    cursor: pointer;
                    transition: transform 0.3s;
                }
                .nova-exit-popup-form button:hover {
                    transform: scale(1.05);
                }
                .nova-exit-popup-privacy {
                    font-size: 12px;
                    color: #999;
                }
            </style>
        `;
        
        $('body').append(popupHTML);
        
        // Close handlers
        $('#nova-exit-popup .nova-exit-popup-close, #nova-exit-popup .nova-exit-popup-overlay').on('click', function() {
            hideExitPopup();
        });
        
        // Form handler
        $('#nova-exit-form').on('submit', function(e) {
            e.preventDefault();
            const email = $(this).find('input[name="email"]').val();
            
            // Submit via AJAX
            $.ajax({
                url: novaLeads.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'nova_leads_exit_popup',
                    email: email,
                    nonce: novaLeads.nonce
                },
                success: function() {
                    $('#nova-exit-popup .nova-exit-popup-content').html(`
                        <div class="nova-exit-popup-icon">✅</div>
                        <h3 class="nova-exit-popup-title">¡Gracias!</h3>
                        <p class="nova-exit-popup-text">Te hemos enviado la información a tu email.</p>
                    `);
                    
                    setTimeout(hideExitPopup, 3000);
                }
            });
        });
    }
    
    /**
     * Show Exit Popup
     */
    function showExitPopup() {
        exitPopupShown = true;
        $('#nova-exit-popup').fadeIn(300);
        $('body').css('overflow', 'hidden');
        
        // Track event
        if (typeof gtag !== 'undefined') {
            gtag('event', 'exit_intent_shown', {
                'event_category': 'engagement'
            });
        }
    }
    
    /**
     * Hide Exit Popup
     */
    function hideExitPopup() {
        $('#nova-exit-popup').fadeOut(300);
        $('body').css('overflow', '');
    }
    
    /**
     * Initialize WhatsApp Widget
     */
    function initWhatsAppWidget() {
        if (!config.whatsappNumber) return;
        
        const widgetHTML = `
            <div id="nova-whatsapp-widget" class="nova-whatsapp-widget">
                <a href="https://wa.me/${config.whatsappNumber}?text=${encodeURIComponent(config.whatsappMessage)}" 
                   target="_blank" 
                   class="nova-whatsapp-button"
                   title="Escríbenos por WhatsApp">
                    <svg viewBox="0 0 32 32" width="32" height="32">
                        <path fill="currentColor" d="M16 2C8.28 2 2 8.28 2 16c0 2.47.64 4.78 1.76 6.8L2 30l7.4-1.94A13.89 13.89 0 0016 30c7.72 0 14-6.28 14-14S23.72 2 16 2zm7.66 19.66c-.32.9-1.86 1.72-2.56 1.84-.66.1-1.5.14-2.42-.16-.56-.18-1.28-.42-2.2-.82-3.88-1.68-6.4-5.62-6.6-5.88-.18-.26-1.5-2-1.5-3.82s.94-2.72 1.28-3.08c.34-.36.74-.44 1-.44h.72c.24 0 .54-.04.84.64.32.7 1.08 2.64 1.18 2.84.1.2.16.44.04.7-.12.26-.18.42-.36.64-.18.22-.38.5-.54.68-.18.18-.36.38-.16.76.2.36.9 1.48 1.94 2.4 1.34 1.18 2.46 1.54 2.82 1.72.36.18.56.14.78-.08.22-.22.92-.96 1.16-1.3.24-.34.48-.28.8-.16.34.12 2.14 1.02 2.5 1.2.36.18.6.26.7.42.1.14.1.82-.22 1.7z"/>
                    </svg>
                </a>
                <span class="nova-whatsapp-tooltip">¿Necesitas ayuda?</span>
            </div>
            <style>
                .nova-whatsapp-widget {
                    position: fixed;
                    bottom: 30px;
                    right: 30px;
                    z-index: 9999;
                }
                .nova-whatsapp-button {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 60px;
                    height: 60px;
                    background: #25D366;
                    border-radius: 50%;
                    color: white;
                    box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
                    transition: all 0.3s ease;
                    animation: novaPulse 2s infinite;
                }
                .nova-whatsapp-button:hover {
                    transform: scale(1.1);
                    box-shadow: 0 6px 30px rgba(37, 211, 102, 0.5);
                }
                @keyframes novaPulse {
                    0%, 100% {
                        box-shadow: 0 4px 20px rgba(37, 211, 102, 0.4);
                    }
                    50% {
                        box-shadow: 0 4px 30px rgba(37, 211, 102, 0.6);
                    }
                }
                .nova-whatsapp-tooltip {
                    position: absolute;
                    right: 70px;
                    top: 50%;
                    transform: translateY(-50%);
                    background: #333;
                    color: white;
                    padding: 8px 15px;
                    border-radius: 8px;
                    font-size: 14px;
                    white-space: nowrap;
                    opacity: 0;
                    visibility: hidden;
                    transition: all 0.3s ease;
                }
                .nova-whatsapp-widget:hover .nova-whatsapp-tooltip {
                    opacity: 1;
                    visibility: visible;
                }
                .nova-whatsapp-tooltip::after {
                    content: '';
                    position: absolute;
                    right: -6px;
                    top: 50%;
                    transform: translateY(-50%);
                    border-left: 6px solid #333;
                    border-top: 6px solid transparent;
                    border-bottom: 6px solid transparent;
                }
            </style>
        `;
        
        $('body').append(widgetHTML);
    }
    
    /**
     * Initialize Animated Counters
     */
    function initAnimatedCounters() {
        const counters = document.querySelectorAll('[data-counter]');
        
        if (!counters.length) return;
        
        const observerOptions = {
            threshold: 0.5
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.dataset.animated) {
                    animateCounter(entry.target);
                    entry.target.dataset.animated = 'true';
                }
            });
        }, observerOptions);
        
        counters.forEach(counter => observer.observe(counter));
    }
    
    /**
     * Animate a single counter
     */
    function animateCounter(element) {
        const target = parseInt(element.dataset.counter, 10);
        const duration = parseInt(element.dataset.duration, 10) || 2000;
        const suffix = element.dataset.suffix || '';
        const prefix = element.dataset.prefix || '';
        
        const startTime = performance.now();
        
        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function
            const easeOutQuart = 1 - Math.pow(1 - progress, 4);
            const currentValue = Math.floor(easeOutQuart * target);
            
            element.textContent = prefix + currentValue.toLocaleString() + suffix;
            
            if (progress < 1) {
                requestAnimationFrame(update);
            }
        }
        
        requestAnimationFrame(update);
    }
    
    /**
     * Initialize Smooth Scroll
     */
    function initSmoothScroll() {
        $('a[href^="#"]').on('click', function(e) {
            const target = $(this.getAttribute('href'));
            
            if (target.length) {
                e.preventDefault();
                
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 800, 'swing');
            }
        });
    }
    
    /**
     * Initialize on Document Ready
     */
    $(document).ready(function() {
        initExitPopup();
        initWhatsAppWidget();
        initAnimatedCounters();
        initSmoothScroll();
    });
    
})(jQuery);
