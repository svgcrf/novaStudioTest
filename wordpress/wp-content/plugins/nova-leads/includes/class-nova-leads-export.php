<?php
/**
 * Exportación de leads
 */

if (!defined('ABSPATH')) {
    exit;
}

class Nova_Leads_Export {
    
    private static $instance = null;
    
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        add_action('admin_post_nova_export_leads', array($this, 'export_csv'));
    }
    
    /**
     * Exportar a CSV
     */
    public function export_csv() {
        if (!current_user_can('manage_options')) {
            wp_die('Sin permisos');
        }
        
        if (!wp_verify_nonce($_GET['nonce'] ?? '', 'nova_export_leads')) {
            wp_die('Error de seguridad');
        }
        
        // Parámetros de filtro
        $args = array(
            'post_type' => 'nova_lead',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );
        
        // Filtro por estado
        if (!empty($_GET['status'])) {
            $args['tax_query'][] = array(
                'taxonomy' => 'lead_status',
                'field' => 'slug',
                'terms' => sanitize_text_field($_GET['status']),
            );
        }
        
        // Filtro por fecha
        if (!empty($_GET['date_from'])) {
            $args['date_query']['after'] = sanitize_text_field($_GET['date_from']);
        }
        if (!empty($_GET['date_to'])) {
            $args['date_query']['before'] = sanitize_text_field($_GET['date_to']);
        }
        
        // Filtro por servicio
        if (!empty($_GET['service'])) {
            $args['tax_query'][] = array(
                'taxonomy' => 'lead_service',
                'field' => 'slug',
                'terms' => sanitize_text_field($_GET['service']),
            );
        }
        
        $leads = get_posts($args);
        
        // Headers para descarga
        $filename = 'nova-leads-' . date('Y-m-d-His') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $output = fopen('php://output', 'w');
        
        // BOM para Excel UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Cabeceras
        $headers = array(
            'ID',
            'Fecha',
            'Nombre',
            'Email',
            'Teléfono',
            'Empresa',
            'Sector',
            'Tamaño',
            'Servicio',
            'Presupuesto',
            'Urgencia',
            'Mensaje',
            'Estado',
            'Fuente',
            'País',
            'Ciudad',
            'Dispositivo',
            'Navegador',
            'UTM Source',
            'UTM Medium',
            'UTM Campaign',
            'Lead Score',
            'Tiempo en página',
        );
        
        fputcsv($output, $headers, ';');
        
        // Datos
        foreach ($leads as $lead) {
            $status = wp_get_object_terms($lead->ID, 'lead_status', array('fields' => 'names'));
            $service = wp_get_object_terms($lead->ID, 'lead_service', array('fields' => 'names'));
            $source = wp_get_object_terms($lead->ID, 'lead_source', array('fields' => 'names'));
            
            $row = array(
                $lead->ID,
                get_the_date('Y-m-d H:i:s', $lead),
                get_post_meta($lead->ID, '_lead_name', true),
                get_post_meta($lead->ID, '_lead_email', true),
                get_post_meta($lead->ID, '_lead_phone', true),
                get_post_meta($lead->ID, '_lead_company', true),
                get_post_meta($lead->ID, '_lead_sector', true),
                get_post_meta($lead->ID, '_lead_company_size', true),
                !empty($service) ? $service[0] : '',
                get_post_meta($lead->ID, '_lead_budget', true),
                get_post_meta($lead->ID, '_lead_urgency', true),
                get_post_meta($lead->ID, '_lead_message', true),
                !empty($status) ? $status[0] : '',
                !empty($source) ? $source[0] : '',
                get_post_meta($lead->ID, '_lead_country', true),
                get_post_meta($lead->ID, '_lead_city', true),
                get_post_meta($lead->ID, '_lead_device_type', true),
                get_post_meta($lead->ID, '_lead_browser', true),
                get_post_meta($lead->ID, '_lead_utm_source', true),
                get_post_meta($lead->ID, '_lead_utm_medium', true),
                get_post_meta($lead->ID, '_lead_utm_campaign', true),
                get_post_meta($lead->ID, '_lead_score', true),
                get_post_meta($lead->ID, '_lead_time_on_page', true),
            );
            
            fputcsv($output, $row, ';');
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Render página de exportación
     */
    public static function render_export_page() {
        $statuses = get_terms(array(
            'taxonomy' => 'lead_status',
            'hide_empty' => false,
        ));
        
        $services = get_terms(array(
            'taxonomy' => 'lead_service',
            'hide_empty' => false,
        ));
        ?>
        <div class="wrap nova-export-page">
            <h1>Exportar Leads</h1>
            
            <div class="nova-export-card">
                <h2>Exportar a CSV</h2>
                <p>Descarga todos tus leads en formato CSV compatible con Excel.</p>
                
                <form method="get" action="<?php echo admin_url('admin-post.php'); ?>">
                    <input type="hidden" name="action" value="nova_export_leads">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('nova_export_leads'); ?>">
                    
                    <div class="nova-export-filters">
                        <div class="nova-export-filter">
                            <label>Estado</label>
                            <select name="status">
                                <option value="">Todos</option>
                                <?php foreach ($statuses as $status) : ?>
                                    <option value="<?php echo esc_attr($status->slug); ?>">
                                        <?php echo esc_html($status->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="nova-export-filter">
                            <label>Servicio</label>
                            <select name="service">
                                <option value="">Todos</option>
                                <?php foreach ($services as $service) : ?>
                                    <option value="<?php echo esc_attr($service->slug); ?>">
                                        <?php echo esc_html($service->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="nova-export-filter">
                            <label>Desde</label>
                            <input type="date" name="date_from">
                        </div>
                        
                        <div class="nova-export-filter">
                            <label>Hasta</label>
                            <input type="date" name="date_to">
                        </div>
                    </div>
                    
                    <button type="submit" class="button button-primary button-large">
                        <span class="dashicons dashicons-download" style="margin-top: 4px;"></span>
                        Descargar CSV
                    </button>
                </form>
            </div>
            
            <div class="nova-export-card">
                <h2>Integración con CRM</h2>
                <p>Conecta Nova Leads con tu CRM favorito mediante webhooks.</p>
                
                <div class="nova-webhook-settings">
                    <label>URL del Webhook</label>
                    <input type="url" 
                           id="nova-webhook-url" 
                           placeholder="https://tu-crm.com/api/webhook"
                           value="<?php echo esc_attr(get_option('nova_leads_webhook_url', '')); ?>">
                    <button type="button" class="button" id="nova-save-webhook">Guardar</button>
                </div>
                
                <p class="description">
                    Cada nuevo lead enviará un POST con los datos en formato JSON al webhook configurado.
                </p>
            </div>
        </div>
        
        <style>
        .nova-export-card {
            background: #fff;
            padding: 20px 25px;
            margin: 20px 0;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
        }
        .nova-export-card h2 {
            margin-top: 0;
        }
        .nova-export-filters {
            display: flex;
            gap: 20px;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        .nova-export-filter {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .nova-export-filter select,
        .nova-export-filter input {
            min-width: 150px;
        }
        .nova-webhook-settings {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            margin: 15px 0;
        }
        .nova-webhook-settings input {
            flex: 1;
            max-width: 400px;
        }
        </style>
        <?php
    }
}
