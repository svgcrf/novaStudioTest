/**
 * Nova Leads - JavaScript del formulario
 */

(function($) {
    'use strict';
    
    // Variables globales
    var startTime = Date.now();
    
    /**
     * Inicializar formularios
     */
    function initForms() {
        $('.nova-lead-form__form').each(function() {
            var $form = $(this);
            
            // Multi-step navigation
            initStepNavigation($form);
            
            // Form submission
            $form.on('submit', handleFormSubmit);
            
            // Real-time validation
            $form.find('input, select, textarea').on('blur', validateField);
            
            // Update time on page
            updateTimeOnPage($form);
        });
    }
    
    /**
     * Navegación por pasos
     */
    function initStepNavigation($form) {
        var $steps = $form.find('.nova-form-step');
        var $indicators = $form.find('.nova-step');
        
        // Next button
        $form.find('.nova-next-step').on('click', function() {
            var $currentStep = $form.find('.nova-form-step.active');
            var currentIndex = $currentStep.data('step');
            var $nextStep = $form.find('.nova-form-step[data-step="' + (currentIndex + 1) + '"]');
            
            // Validate current step
            if (!validateStep($currentStep)) {
                return;
            }
            
            if ($nextStep.length) {
                // Update steps
                $currentStep.removeClass('active');
                $nextStep.addClass('active').addClass('slide-right');
                
                // Update indicators
                $indicators.filter('[data-step="' + currentIndex + '"]')
                    .removeClass('active').addClass('completed');
                $indicators.filter('[data-step="' + (currentIndex + 1) + '"]')
                    .addClass('active');
                
                // Scroll to form
                scrollToForm($form);
            }
        });
        
        // Previous button
        $form.find('.nova-prev-step').on('click', function() {
            var $currentStep = $form.find('.nova-form-step.active');
            var currentIndex = $currentStep.data('step');
            var $prevStep = $form.find('.nova-form-step[data-step="' + (currentIndex - 1) + '"]');
            
            if ($prevStep.length) {
                // Update steps
                $currentStep.removeClass('active');
                $prevStep.addClass('active').addClass('slide-left');
                
                // Update indicators
                $indicators.filter('[data-step="' + currentIndex + '"]')
                    .removeClass('active');
                $indicators.filter('[data-step="' + (currentIndex - 1) + '"]')
                    .removeClass('completed').addClass('active');
            }
        });
    }
    
    /**
     * Validar paso actual
     */
    function validateStep($step) {
        var isValid = true;
        
        $step.find('input[required], select[required], textarea[required]').each(function() {
            if (!validateField.call(this)) {
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    /**
     * Validar campo individual
     */
    function validateField() {
        var $field = $(this);
        var value = $field.val().trim();
        var isRequired = $field.prop('required');
        var type = $field.attr('type');
        var isValid = true;
        var errorMessage = '';
        
        // Remove previous error
        $field.removeClass('error');
        $field.siblings('.nova-field-error').remove();
        
        // Required check
        if (isRequired && !value) {
            isValid = false;
            errorMessage = novaLeads.messages.required;
        }
        
        // Email validation
        if (isValid && type === 'email' && value) {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                errorMessage = novaLeads.messages.invalid_email;
            }
        }
        
        // Phone validation
        if (isValid && type === 'tel' && value) {
            var phoneRegex = /^[\d\s+()-]{7,20}$/;
            if (!phoneRegex.test(value)) {
                isValid = false;
                errorMessage = novaLeads.messages.invalid_phone;
            }
        }
        
        // Show error
        if (!isValid) {
            $field.addClass('error');
            $field.after('<span class="nova-field-error">' + errorMessage + '</span>');
        }
        
        return isValid;
    }
    
    /**
     * Manejar envío del formulario
     */
    function handleFormSubmit(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $container = $form.closest('.nova-lead-form');
        var $button = $form.find('button[type="submit"]');
        var $message = $form.find('.nova-lead-form__message');
        
        // Validate all required fields
        var isValid = true;
        $form.find('input[required], select[required], textarea[required]').each(function() {
            if (!validateField.call(this)) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            showMessage($message, novaLeads.messages.error, 'error');
            return;
        }
        
        // Prepare data
        var formData = new FormData($form[0]);
        formData.append('action', 'nova_submit_lead');
        formData.append('nonce', novaLeads.nonce);
        formData.append('form_source', $form.data('source') || 'unknown');
        
        // Update time on page
        var timeOnPage = Math.floor((Date.now() - startTime) / 1000);
        formData.set('time_on_page', timeOnPage);
        
        // Loading state
        $button.addClass('loading').prop('disabled', true);
        $message.removeClass('error success').hide();
        
        // AJAX request
        $.ajax({
            url: novaLeads.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    $form.hide();
                    $container.find('.nova-lead-form__success').show();
                    
                    // Track conversion (if analytics available)
                    trackConversion(response.data.lead_id);
                    
                    // Trigger custom event
                    $(document).trigger('nova_lead_submitted', [response.data]);
                } else {
                    showMessage($message, response.data.message || novaLeads.messages.error, 'error');
                }
            },
            error: function() {
                showMessage($message, novaLeads.messages.error, 'error');
            },
            complete: function() {
                $button.removeClass('loading').prop('disabled', false);
            }
        });
    }
    
    /**
     * Mostrar mensaje
     */
    function showMessage($message, text, type) {
        $message
            .text(text)
            .removeClass('error success')
            .addClass(type)
            .show();
    }
    
    /**
     * Scroll al formulario
     */
    function scrollToForm($form) {
        var offset = $form.offset().top - 100;
        $('html, body').animate({ scrollTop: offset }, 300);
    }
    
    /**
     * Actualizar tiempo en página
     */
    function updateTimeOnPage($form) {
        setInterval(function() {
            var timeOnPage = Math.floor((Date.now() - startTime) / 1000);
            $form.find('.nova-time-on-page').val(timeOnPage);
        }, 5000);
    }
    
    /**
     * Tracking de conversión
     */
    function trackConversion(leadId) {
        // Google Analytics 4
        if (typeof gtag !== 'undefined') {
            gtag('event', 'generate_lead', {
                'event_category': 'Nova Leads',
                'event_label': leadId
            });
        }
        
        // Facebook Pixel
        if (typeof fbq !== 'undefined') {
            fbq('track', 'Lead', { lead_id: leadId });
        }
        
        // Custom tracking
        if (typeof novaTrackConversion === 'function') {
            novaTrackConversion(leadId);
        }
    }
    
    /**
     * Exit Intent Detection
     */
    function initExitIntent() {
        var shown = false;
        var exitPopup = document.getElementById('nova-exit-popup');
        
        if (!exitPopup) return;
        
        // Check if already shown this session
        if (sessionStorage.getItem('nova_exit_shown')) return;
        
        document.addEventListener('mouseout', function(e) {
            if (shown) return;
            
            // Detect exit intent (mouse leaving viewport from top)
            if (e.clientY < 10) {
                showExitPopup();
                shown = true;
                sessionStorage.setItem('nova_exit_shown', 'true');
            }
        });
        
        // Mobile: show after scroll up
        var lastScrollTop = 0;
        var scrollUpCount = 0;
        
        window.addEventListener('scroll', function() {
            if (shown) return;
            
            var st = window.pageYOffset || document.documentElement.scrollTop;
            
            if (st < lastScrollTop) {
                scrollUpCount++;
                if (scrollUpCount > 3) {
                    showExitPopup();
                    shown = true;
                    sessionStorage.setItem('nova_exit_shown', 'true');
                }
            } else {
                scrollUpCount = 0;
            }
            
            lastScrollTop = st <= 0 ? 0 : st;
        });
    }
    
    /**
     * Mostrar Exit Popup
     */
    function showExitPopup() {
        var popup = document.getElementById('nova-exit-popup');
        if (popup) {
            popup.classList.add('active');
            document.body.classList.add('nova-popup-open');
        }
    }
    
    /**
     * Cerrar Exit Popup
     */
    function closeExitPopup() {
        var popup = document.getElementById('nova-exit-popup');
        if (popup) {
            popup.classList.remove('active');
            document.body.classList.remove('nova-popup-open');
        }
    }
    
    // Close popup handlers
    $(document).on('click', '.nova-exit-popup__close, .nova-exit-popup__overlay', closeExitPopup);
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') closeExitPopup();
    });
    
    // Initialize on ready
    $(document).ready(function() {
        initForms();
        initExitIntent();
    });
    
    // Expose functions globally
    window.NovaLeads = {
        initForms: initForms,
        showExitPopup: showExitPopup,
        closeExitPopup: closeExitPopup
    };
    
})(jQuery);
