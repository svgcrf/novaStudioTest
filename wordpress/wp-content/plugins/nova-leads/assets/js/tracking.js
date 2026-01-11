/**
 * Nova Leads - Tracking Frontend
 */

(function() {
    'use strict';
    
    // Guardar landing page
    if (!getCookie('nova_landing_page')) {
        setCookie('nova_landing_page', window.location.pathname, 30);
    }
    
    // Guardar UTM parameters
    var utmParams = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
    var urlParams = new URLSearchParams(window.location.search);
    
    utmParams.forEach(function(param) {
        var value = urlParams.get(param);
        if (value) {
            setCookie('nova_' + param, value, 30);
        }
    });
    
    // Helper: Set cookie
    function setCookie(name, value, days) {
        var expires = '';
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = name + '=' + encodeURIComponent(value) + expires + '; path=/; SameSite=Lax';
    }
    
    // Helper: Get cookie
    function getCookie(name) {
        var nameEQ = name + '=';
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
        }
        return null;
    }
    
    // Expose tracking info
    window.novaTrackingInfo = {
        landing_page: getCookie('nova_landing_page') || window.location.pathname,
        utm_source: getCookie('nova_utm_source') || '',
        utm_medium: getCookie('nova_utm_medium') || '',
        utm_campaign: getCookie('nova_utm_campaign') || '',
        utm_term: getCookie('nova_utm_term') || '',
        utm_content: getCookie('nova_utm_content') || '',
        referrer: document.referrer || 'direct',
        page_url: window.location.href
    };
    
})();
