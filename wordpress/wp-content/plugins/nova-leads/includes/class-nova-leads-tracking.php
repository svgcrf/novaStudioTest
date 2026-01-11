<?php
/**
 * Tracking de datos del visitante
 */

if (!defined('ABSPATH')) {
    exit;
}

class Nova_Leads_Tracking {
    
    private static $instance = null;
    
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_tracking_script'));
    }
    
    /**
     * Script de tracking frontend
     */
    public function enqueue_tracking_script() {
        wp_enqueue_script(
            'nova-leads-tracking',
            NOVA_LEADS_URL . 'assets/js/tracking.js',
            array('jquery'),
            NOVA_LEADS_VERSION,
            true
        );
        
        wp_localize_script('nova-leads-tracking', 'novaTracking', array(
            'startTime' => time(),
        ));
    }
    
    /**
     * Obtener todos los datos de tracking
     */
    public static function get_data() {
        return array(
            'ip_address' => self::get_ip(),
            'country' => self::get_country(),
            'city' => self::get_city(),
            'device_type' => self::get_device_type(),
            'browser' => self::get_browser(),
            'os' => self::get_os(),
            'referrer' => self::get_referrer(),
            'landing_page' => self::get_landing_page(),
            'utm_source' => self::get_utm('utm_source'),
            'utm_medium' => self::get_utm('utm_medium'),
            'utm_campaign' => self::get_utm('utm_campaign'),
            'utm_term' => self::get_utm('utm_term'),
            'utm_content' => self::get_utm('utm_content'),
        );
    }
    
    /**
     * Obtener IP del visitante
     */
    public static function get_ip() {
        $ip_keys = array(
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR',
        );
        
        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                return sanitize_text_field(trim($ip));
            }
        }
        
        return '';
    }
    
    /**
     * Obtener país por IP (usando API gratuita)
     */
    public static function get_country() {
        $ip = self::get_ip();
        
        if (empty($ip) || $ip === '127.0.0.1' || $ip === '::1') {
            return 'Local';
        }
        
        // Cache por IP
        $cache_key = 'nova_geo_' . md5($ip);
        $cached = get_transient($cache_key);
        
        if ($cached !== false) {
            return $cached['country'] ?? '';
        }
        
        // Usar ip-api.com (gratuito, sin API key)
        $response = wp_remote_get("http://ip-api.com/json/{$ip}?fields=status,country,city", array(
            'timeout' => 3,
        ));
        
        if (is_wp_error($response)) {
            return '';
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if ($data && $data['status'] === 'success') {
            set_transient($cache_key, $data, DAY_IN_SECONDS);
            return $data['country'] ?? '';
        }
        
        return '';
    }
    
    /**
     * Obtener ciudad
     */
    public static function get_city() {
        $ip = self::get_ip();
        
        if (empty($ip) || $ip === '127.0.0.1' || $ip === '::1') {
            return 'Local';
        }
        
        $cache_key = 'nova_geo_' . md5($ip);
        $cached = get_transient($cache_key);
        
        if ($cached !== false) {
            return $cached['city'] ?? '';
        }
        
        return '';
    }
    
    /**
     * Detectar tipo de dispositivo
     */
    public static function get_device_type() {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $mobile_keywords = array(
            'Mobile', 'Android', 'iPhone', 'iPod', 'BlackBerry',
            'Windows Phone', 'Opera Mini', 'IEMobile',
        );
        
        $tablet_keywords = array(
            'iPad', 'Tablet', 'PlayBook', 'Silk',
        );
        
        foreach ($tablet_keywords as $keyword) {
            if (stripos($ua, $keyword) !== false) {
                return 'tablet';
            }
        }
        
        foreach ($mobile_keywords as $keyword) {
            if (stripos($ua, $keyword) !== false) {
                return 'mobile';
            }
        }
        
        return 'desktop';
    }
    
    /**
     * Detectar navegador
     */
    public static function get_browser() {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $browsers = array(
            'Edge' => '/Edg\//',
            'Opera' => '/OPR\//',
            'Chrome' => '/Chrome\//',
            'Safari' => '/Safari\//',
            'Firefox' => '/Firefox\//',
            'IE' => '/MSIE|Trident/',
        );
        
        foreach ($browsers as $name => $pattern) {
            if (preg_match($pattern, $ua)) {
                return $name;
            }
        }
        
        return 'Otro';
    }
    
    /**
     * Detectar sistema operativo
     */
    public static function get_os() {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $os_list = array(
            'Windows 11' => '/Windows NT 10.*Win64/',
            'Windows 10' => '/Windows NT 10/',
            'Windows 8' => '/Windows NT 6\.[23]/',
            'Windows 7' => '/Windows NT 6\.1/',
            'macOS' => '/Macintosh|Mac OS X/',
            'iOS' => '/iPhone|iPad|iPod/',
            'Android' => '/Android/',
            'Linux' => '/Linux/',
        );
        
        foreach ($os_list as $name => $pattern) {
            if (preg_match($pattern, $ua)) {
                return $name;
            }
        }
        
        return 'Otro';
    }
    
    /**
     * Obtener referrer
     */
    public static function get_referrer() {
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        
        if (empty($referrer)) {
            return 'Directo';
        }
        
        $parsed = parse_url($referrer);
        $host = $parsed['host'] ?? '';
        
        // Si es del mismo dominio, ignorar
        $site_host = parse_url(home_url(), PHP_URL_HOST);
        if ($host === $site_host) {
            return 'Interno';
        }
        
        // Detectar fuente conocida
        $sources = array(
            'google' => 'Google',
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'linkedin' => 'LinkedIn',
            'twitter' => 'Twitter',
            'youtube' => 'YouTube',
            'bing' => 'Bing',
            'yahoo' => 'Yahoo',
        );
        
        foreach ($sources as $key => $name) {
            if (stripos($host, $key) !== false) {
                return $name;
            }
        }
        
        return $host;
    }
    
    /**
     * Obtener landing page
     */
    public static function get_landing_page() {
        // Primero intentar obtener de cookie/session
        if (isset($_COOKIE['nova_landing_page'])) {
            return sanitize_text_field($_COOKIE['nova_landing_page']);
        }
        
        // Si no, usar la URL actual
        return $_SERVER['REQUEST_URI'] ?? '/';
    }
    
    /**
     * Obtener parámetro UTM
     */
    public static function get_utm($param) {
        // Primero de la URL actual
        if (isset($_GET[$param])) {
            return sanitize_text_field($_GET[$param]);
        }
        
        // Luego de cookie
        if (isset($_COOKIE['nova_' . $param])) {
            return sanitize_text_field($_COOKIE['nova_' . $param]);
        }
        
        return '';
    }
    
    /**
     * Calcular lead score basado en datos
     */
    public static function calculate_score($data) {
        $score = 0;
        
        // Datos de contacto (+10 cada uno)
        if (!empty($data['email'])) $score += 10;
        if (!empty($data['phone'])) $score += 10;
        if (!empty($data['company'])) $score += 10;
        
        // Perfil completo (+5 cada uno)
        if (!empty($data['sector'])) $score += 5;
        if (!empty($data['company_size'])) $score += 5;
        
        // Interés comercial
        $budget_scores = array(
            'menos_5k' => 5,
            '5k_15k' => 15,
            '15k_30k' => 25,
            '30k_50k' => 35,
            'mas_50k' => 50,
        );
        if (!empty($data['budget']) && isset($budget_scores[$data['budget']])) {
            $score += $budget_scores[$data['budget']];
        }
        
        // Urgencia
        $urgency_scores = array(
            'inmediato' => 30,
            '1_mes' => 20,
            '3_meses' => 10,
            '6_meses' => 5,
            'explorando' => 0,
        );
        if (!empty($data['urgency']) && isset($urgency_scores[$data['urgency']])) {
            $score += $urgency_scores[$data['urgency']];
        }
        
        // Engagement
        if (!empty($data['time_on_page']) && $data['time_on_page'] > 60) {
            $score += min(20, floor($data['time_on_page'] / 30));
        }
        
        // Mensaje detallado
        if (!empty($data['message']) && strlen($data['message']) > 50) {
            $score += 10;
        }
        
        return min(100, $score);
    }
}
