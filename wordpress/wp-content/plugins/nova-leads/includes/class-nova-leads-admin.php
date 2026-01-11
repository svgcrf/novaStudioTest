<?php
/**
 * Administración del Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class Nova_Leads_Admin {
    
    private static $instance = null;
    
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_menu_pages'));
        add_filter('manage_nova_lead_posts_columns', array($this, 'set_columns'));
        add_action('manage_nova_lead_posts_custom_column', array($this, 'render_columns'), 10, 2);
        add_filter('manage_edit-nova_lead_sortable_columns', array($this, 'sortable_columns'));
        add_action('admin_head', array($this, 'admin_styles'));
    }
    
    public function add_menu_pages() {
        // Menú principal
        add_menu_page(
            __('Nova Leads', 'nova-leads'),
            __('Nova Leads', 'nova-leads'),
            'manage_options',
            'nova-leads',
            array($this, 'render_dashboard_page'),
            'dashicons-chart-area',
            30
        );
        
        // Submenú: Dashboard
        add_submenu_page(
            'nova-leads',
            __('Dashboard', 'nova-leads'),
            __('📊 Dashboard', 'nova-leads'),
            'manage_options',
            'nova-leads',
            array($this, 'render_dashboard_page')
        );
        
        // Submenú: Todos los Leads
        add_submenu_page(
            'nova-leads',
            __('Todos los Leads', 'nova-leads'),
            __('📋 Todos los Leads', 'nova-leads'),
            'manage_options',
            'edit.php?post_type=nova_lead'
        );
        
        // Submenú: Añadir Lead
        add_submenu_page(
            'nova-leads',
            __('Añadir Lead', 'nova-leads'),
            __('➕ Añadir Lead', 'nova-leads'),
            'manage_options',
            'post-new.php?post_type=nova_lead'
        );
        
        // Submenú: Configuración
        add_submenu_page(
            'nova-leads',
            __('Configuración', 'nova-leads'),
            __('⚙️ Configuración', 'nova-leads'),
            'manage_options',
            'nova-leads-settings',
            array($this, 'render_settings_page')
        );
        
        // Submenú: Exportar
        add_submenu_page(
            'nova-leads',
            __('Exportar', 'nova-leads'),
            __('📤 Exportar', 'nova-leads'),
            'manage_options',
            'nova-leads-export',
            array($this, 'render_export_page')
        );
    }
    
    public function render_dashboard_page() {
        include NOVA_LEADS_PATH . 'templates/admin-dashboard.php';
    }
    
    public function render_settings_page() {
        // Guardar configuración
        if (isset($_POST['nova_leads_save_settings']) && wp_verify_nonce($_POST['_wpnonce'], 'nova_leads_settings')) {
            update_option('nova_leads_email', sanitize_email($_POST['nova_leads_email']));
            update_option('nova_leads_email_subject', sanitize_text_field($_POST['nova_leads_email_subject']));
            update_option('nova_leads_whatsapp', sanitize_text_field($_POST['nova_leads_whatsapp']));
            update_option('nova_leads_whatsapp_message', sanitize_textarea_field($_POST['nova_leads_whatsapp_message']));
            update_option('nova_leads_exit_popup', isset($_POST['nova_leads_exit_popup']) ? 1 : 0);
            update_option('nova_leads_exit_popup_title', sanitize_text_field($_POST['nova_leads_exit_popup_title']));
            update_option('nova_leads_exit_popup_text', sanitize_textarea_field($_POST['nova_leads_exit_popup_text']));
            
            echo '<div class="notice notice-success"><p>' . __('Configuración guardada', 'nova-leads') . '</p></div>';
        }
        
        include NOVA_LEADS_PATH . 'templates/admin-settings.php';
    }
    
    public function render_export_page() {
        include NOVA_LEADS_PATH . 'templates/admin-export.php';
    }
    
    public function set_columns($columns) {
        $new_columns = array(
            'cb' => $columns['cb'],
            'title' => __('Nombre', 'nova-leads'),
            'lead_email' => __('Email', 'nova-leads'),
            'lead_phone' => __('Teléfono', 'nova-leads'),
            'taxonomy-lead_service' => __('Servicio', 'nova-leads'),
            'lead_budget' => __('Presupuesto', 'nova-leads'),
            'taxonomy-lead_status' => __('Estado', 'nova-leads'),
            'taxonomy-lead_source' => __('Fuente', 'nova-leads'),
            'date' => __('Fecha', 'nova-leads'),
        );
        
        return $new_columns;
    }
    
    public function render_columns($column, $post_id) {
        switch ($column) {
            case 'lead_email':
                $email = get_post_meta($post_id, '_lead_email', true);
                echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
                break;
                
            case 'lead_phone':
                $phone = get_post_meta($post_id, '_lead_phone', true);
                if ($phone) {
                    echo '<a href="tel:' . esc_attr($phone) . '">' . esc_html($phone) . '</a>';
                } else {
                    echo '—';
                }
                break;
                
            case 'lead_budget':
                $budget = get_post_meta($post_id, '_lead_budget', true);
                $budgets = array(
                    'menos-3k' => '< 3K€',
                    '3k-5k' => '3-5K€',
                    '5k-10k' => '5-10K€',
                    '10k-20k' => '10-20K€',
                    '20k+' => '> 20K€',
                );
                echo isset($budgets[$budget]) ? esc_html($budgets[$budget]) : '—';
                break;
        }
    }
    
    public function sortable_columns($columns) {
        $columns['lead_email'] = 'lead_email';
        $columns['lead_budget'] = 'lead_budget';
        return $columns;
    }
    
    public function admin_styles() {
        $screen = get_current_screen();
        
        if (!$screen || strpos($screen->id, 'nova_lead') === false && strpos($screen->id, 'nova-leads') === false) {
            return;
        }
        ?>
        <style>
            .column-lead_email { width: 180px; }
            .column-lead_phone { width: 120px; }
            .column-lead_budget { width: 100px; }
            .column-taxonomy-lead_status { width: 140px; }
            .column-taxonomy-lead_source { width: 120px; }
            
            .nova-lead-form th { width: 150px; padding: 15px 10px; }
            .nova-lead-form td { padding: 15px 10px; }
            
            .nova-tracking-info p { margin: 8px 0; font-size: 13px; }
            .nova-tracking-info small { word-break: break-all; }
            
            .nova-notes-container { margin: -6px -12px -12px; }
            .nova-add-note { padding: 12px; border-bottom: 1px solid #ddd; background: #f9f9f9; }
            .nova-add-note textarea { margin-bottom: 10px; }
            .nova-notes-list { max-height: 300px; overflow-y: auto; }
            .nova-note { padding: 12px; border-bottom: 1px solid #eee; }
            .nova-note:last-child { border-bottom: none; }
            .note-header { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; font-size: 12px; }
            .note-author { font-weight: 600; color: #2271b1; }
            .note-date { color: #666; }
            .note-delete { margin-left: auto; background: none; border: none; color: #dc3232; cursor: pointer; font-size: 18px; line-height: 1; }
            .note-content { color: #333; line-height: 1.5; }
            .no-notes { padding: 20px; text-align: center; color: #666; }
        </style>
        <?php
    }
    
    /**
     * Obtener estadísticas de leads
     */
    public static function get_stats() {
        global $wpdb;
        
        $stats = array();
        
        // Total de leads
        $stats['total'] = wp_count_posts('nova_lead')->publish;
        
        // Leads por estado
        $statuses = get_terms(array(
            'taxonomy' => 'lead_status',
            'hide_empty' => false,
        ));
        
        $stats['by_status'] = array();
        foreach ($statuses as $status) {
            $count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $wpdb->term_relationships tr
                 JOIN $wpdb->posts p ON tr.object_id = p.ID
                 WHERE tr.term_taxonomy_id = %d AND p.post_type = 'nova_lead' AND p.post_status = 'publish'",
                $status->term_taxonomy_id
            ));
            $stats['by_status'][$status->slug] = array(
                'name' => $status->name,
                'count' => intval($count),
            );
        }
        
        // Leads nuevos este mes
        $stats['this_month'] = $wpdb->get_var(
            "SELECT COUNT(*) FROM $wpdb->posts 
             WHERE post_type = 'nova_lead' 
             AND post_status = 'publish' 
             AND MONTH(post_date) = MONTH(CURRENT_DATE()) 
             AND YEAR(post_date) = YEAR(CURRENT_DATE())"
        );
        
        // Leads por servicio
        $services = get_terms(array(
            'taxonomy' => 'lead_service',
            'hide_empty' => false,
        ));
        
        $stats['by_service'] = array();
        foreach ($services as $service) {
            $count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $wpdb->term_relationships tr
                 JOIN $wpdb->posts p ON tr.object_id = p.ID
                 WHERE tr.term_taxonomy_id = %d AND p.post_type = 'nova_lead' AND p.post_status = 'publish'",
                $service->term_taxonomy_id
            ));
            if ($count > 0) {
                $stats['by_service'][$service->slug] = array(
                    'name' => $service->name,
                    'count' => intval($count),
                );
            }
        }
        
        // Leads por fuente
        $sources = get_terms(array(
            'taxonomy' => 'lead_source',
            'hide_empty' => false,
        ));
        
        $stats['by_source'] = array();
        foreach ($sources as $source) {
            $count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $wpdb->term_relationships tr
                 JOIN $wpdb->posts p ON tr.object_id = p.ID
                 WHERE tr.term_taxonomy_id = %d AND p.post_type = 'nova_lead' AND p.post_status = 'publish'",
                $source->term_taxonomy_id
            ));
            if ($count > 0) {
                $stats['by_source'][$source->slug] = array(
                    'name' => $source->name,
                    'count' => intval($count),
                );
            }
        }
        
        // Leads por presupuesto
        $budgets = array('menos-3k', '3k-5k', '5k-10k', '10k-20k', '20k+');
        $stats['by_budget'] = array();
        foreach ($budgets as $budget) {
            $count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $wpdb->postmeta pm
                 JOIN $wpdb->posts p ON pm.post_id = p.ID
                 WHERE pm.meta_key = '_lead_budget' AND pm.meta_value = %s
                 AND p.post_type = 'nova_lead' AND p.post_status = 'publish'",
                $budget
            ));
            if ($count > 0) {
                $stats['by_budget'][$budget] = intval($count);
            }
        }
        
        // Leads por mes (últimos 6 meses)
        $stats['by_month'] = array();
        for ($i = 5; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $wpdb->posts 
                 WHERE post_type = 'nova_lead' 
                 AND post_status = 'publish' 
                 AND DATE_FORMAT(post_date, '%%Y-%%m') = %s",
                $date
            ));
            $stats['by_month'][$date] = intval($count);
        }
        
        // Leads por dispositivo
        $devices = $wpdb->get_results(
            "SELECT pm.meta_value as device, COUNT(*) as count
             FROM $wpdb->postmeta pm
             JOIN $wpdb->posts p ON pm.post_id = p.ID
             WHERE pm.meta_key = '_lead_device'
             AND p.post_type = 'nova_lead' AND p.post_status = 'publish'
             GROUP BY pm.meta_value"
        );
        
        $stats['by_device'] = array();
        foreach ($devices as $device) {
            if (!empty($device->device)) {
                $stats['by_device'][$device->device] = intval($device->count);
            }
        }
        
        // Tasa de conversión
        $converted = isset($stats['by_status']['convertido']) ? $stats['by_status']['convertido']['count'] : 0;
        $stats['conversion_rate'] = $stats['total'] > 0 ? round(($converted / $stats['total']) * 100, 1) : 0;
        
        // Últimos 5 leads
        $stats['recent'] = get_posts(array(
            'post_type' => 'nova_lead',
            'posts_per_page' => 5,
            'orderby' => 'date',
            'order' => 'DESC',
        ));
        
        return $stats;
    }
}
