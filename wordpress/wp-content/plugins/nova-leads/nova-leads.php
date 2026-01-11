<?php
/**
 * Plugin Name: Nova Studio Leads
 * Plugin URI: https://novastudio.com
 * Description: Sistema completo de gestión de leads con métricas, tracking y dashboard para Nova Studio
 * Version: 1.0.0
 * Author: Nova Studio
 * Author URI: https://novastudio.com
 * Text Domain: nova-leads
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Constantes del plugin
define('NOVA_LEADS_VERSION', '1.0.0');
define('NOVA_LEADS_PATH', plugin_dir_path(__FILE__));
define('NOVA_LEADS_URL', plugin_dir_url(__FILE__));
define('NOVA_LEADS_BASENAME', plugin_basename(__FILE__));

/**
 * Clase principal del plugin
 */
class Nova_Leads {
    
    private static $instance = null;
    
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        $this->includes();
        $this->init_hooks();
    }
    
    private function includes() {
        // Core
        require_once NOVA_LEADS_PATH . 'includes/class-nova-leads-cpt.php';
        require_once NOVA_LEADS_PATH . 'includes/class-nova-leads-admin.php';
        require_once NOVA_LEADS_PATH . 'includes/class-nova-leads-dashboard.php';
        require_once NOVA_LEADS_PATH . 'includes/class-nova-leads-ajax.php';
        require_once NOVA_LEADS_PATH . 'includes/class-nova-leads-tracking.php';
        require_once NOVA_LEADS_PATH . 'includes/class-nova-leads-export.php';
        require_once NOVA_LEADS_PATH . 'includes/class-nova-leads-email.php';
        
        // Frontend
        require_once NOVA_LEADS_PATH . 'includes/class-nova-leads-form.php';
        
        // Elementor Widget (si está activo)
        if (did_action('elementor/loaded')) {
            require_once NOVA_LEADS_PATH . 'includes/class-nova-leads-elementor.php';
        }
    }
    
    private function init_hooks() {
        // Activación/Desactivación
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Inicializar AJAX handlers inmediatamente (antes de init)
        Nova_Leads_Ajax::instance();
        
        // Init - para CPT y otras cosas
        add_action('init', array($this, 'init'), 5); // Prioridad alta
        add_action('admin_init', array($this, 'admin_init'));
        
        // Assets
        add_action('admin_enqueue_scripts', array($this, 'admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'frontend_assets'));
        
        // Elementor
        add_action('elementor/widgets/register', array($this, 'register_elementor_widgets'));
    }
    
    public function init() {
        // Inicializar CPT
        Nova_Leads_CPT::instance();
        
        // Inicializar Form (el shortcode se registra dentro del constructor)
        Nova_Leads_Form::instance();
        
        // Inicializar Tracking
        Nova_Leads_Tracking::instance();
    }
    
    public function admin_init() {
        // Inicializar Admin
        Nova_Leads_Admin::instance();
        
        // Inicializar Dashboard
        Nova_Leads_Dashboard::instance();
        
        // Inicializar Export
        Nova_Leads_Export::instance();
    }
    
    public function admin_assets($hook) {
        // Solo en páginas del plugin
        if (strpos($hook, 'nova-leads') === false && $hook !== 'toplevel_page_nova-leads') {
            return;
        }
        
        // Chart.js para gráficos
        wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js', array(), '4.4.0', true);
        
        // Admin CSS
        wp_enqueue_style('nova-leads-admin', NOVA_LEADS_URL . 'assets/css/admin.css', array(), NOVA_LEADS_VERSION);
        
        // Admin JS
        wp_enqueue_script('nova-leads-admin', NOVA_LEADS_URL . 'assets/js/admin.js', array('jquery', 'chartjs'), NOVA_LEADS_VERSION, true);
        
        wp_localize_script('nova-leads-admin', 'novaLeadsAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nova_leads_admin'),
            'strings' => array(
                'confirmDelete' => __('¿Estás seguro de eliminar este lead?', 'nova-leads'),
                'saved' => __('Guardado correctamente', 'nova-leads'),
                'error' => __('Error al procesar', 'nova-leads'),
            )
        ));
    }
    
    public function frontend_assets() {
        // Frontend CSS
        wp_enqueue_style('nova-leads-form', NOVA_LEADS_URL . 'assets/css/form.css', array(), NOVA_LEADS_VERSION);
        
        // Frontend JS
        wp_enqueue_script('nova-leads-form', NOVA_LEADS_URL . 'assets/js/form.js', array('jquery'), NOVA_LEADS_VERSION, true);
        
        // UX Enhancements
        wp_enqueue_script('nova-leads-ux', NOVA_LEADS_URL . 'assets/js/ux-enhancements.js', array('jquery'), NOVA_LEADS_VERSION, true);
        
        wp_localize_script('nova-leads-form', 'novaLeads', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nova_leads_submit'),
            'strings' => array(
                'sending' => __('Enviando...', 'nova-leads'),
                'success' => __('¡Mensaje enviado! Te contactaremos pronto.', 'nova-leads'),
                'error' => __('Error al enviar. Intenta de nuevo.', 'nova-leads'),
                'required' => __('Este campo es obligatorio', 'nova-leads'),
                'invalidEmail' => __('Email no válido', 'nova-leads'),
            ),
            'whatsapp' => array(
                'number' => get_option('nova_leads_whatsapp', '34900000000'),
                'message' => get_option('nova_leads_whatsapp_message', 'Hola, me interesa información sobre sus servicios'),
            ),
            'exitPopup' => array(
                'enabled' => get_option('nova_leads_exit_popup', true),
                'delay' => get_option('nova_leads_exit_popup_delay', 5000),
                'title' => get_option('nova_leads_exit_popup_title', '¿Te vas tan pronto?'),
                'text' => get_option('nova_leads_exit_popup_text', 'Déjanos tu email y recibe nuestra guía gratuita de conversión web'),
            )
        ));
    }
    
    public function register_elementor_widgets($widgets_manager) {
        if (class_exists('Nova_Leads_Elementor')) {
            $widgets_manager->register(new Nova_Leads_Elementor_Form_Widget());
        }
    }
    
    public function activate() {
        // Crear tablas si es necesario
        $this->create_tables();
        
        // Crear CPT
        Nova_Leads_CPT::instance()->register_post_type();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Opciones por defecto
        $this->set_default_options();
    }
    
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    private function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Tabla para notas de leads
        $table_notes = $wpdb->prefix . 'nova_lead_notes';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_notes (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            lead_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            note text NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY lead_id (lead_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    private function set_default_options() {
        $defaults = array(
            'nova_leads_email' => get_option('admin_email'),
            'nova_leads_email_subject' => '🎯 Nuevo lead en Nova Studio',
            'nova_leads_whatsapp' => '34900000000',
            'nova_leads_whatsapp_message' => 'Hola, me interesa información sobre sus servicios',
            'nova_leads_exit_popup' => true,
            'nova_leads_exit_popup_delay' => 5000,
            'nova_leads_exit_popup_title' => '¿Te vas tan pronto?',
            'nova_leads_exit_popup_text' => 'Déjanos tu email y recibe nuestra guía gratuita',
        );
        
        foreach ($defaults as $key => $value) {
            if (get_option($key) === false) {
                update_option($key, $value);
            }
        }
    }
}

// Inicializar plugin
function nova_leads() {
    return Nova_Leads::instance();
}

// Arrancar
add_action('plugins_loaded', 'nova_leads');
