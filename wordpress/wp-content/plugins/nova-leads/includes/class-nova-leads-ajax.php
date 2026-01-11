<?php
/**
 * Manejo de AJAX
 */

if (!defined('ABSPATH')) {
    exit;
}

class Nova_Leads_Ajax {
    
    private static $instance = null;
    
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        // Frontend - Enviar formulario (registrar siempre)
        add_action('wp_ajax_nova_submit_lead', array($this, 'submit_lead'));
        add_action('wp_ajax_nopriv_nova_submit_lead', array($this, 'submit_lead'));
        
        // También registrar con el nombre alternativo por compatibilidad
        add_action('wp_ajax_nova_leads_submit', array($this, 'submit_lead'));
        add_action('wp_ajax_nopriv_nova_leads_submit', array($this, 'submit_lead'));
        
        // Admin - Notas
        add_action('wp_ajax_nova_add_note', array($this, 'add_note'));
        add_action('wp_ajax_nova_delete_note', array($this, 'delete_note'));
        
        // Admin - Actualizar estado
        add_action('wp_ajax_nova_update_status', array($this, 'update_status'));
        
        // Admin - Obtener estadísticas
        add_action('wp_ajax_nova_get_stats', array($this, 'get_stats'));
    }
    
    /**
     * Enviar formulario de lead
     */
    public function submit_lead() {
        // Verificar nonce
        if (!wp_verify_nonce($_POST['nonce'], 'nova_leads_submit')) {
            wp_send_json_error(array('message' => 'Error de seguridad'));
        }
        
        // Verificar honeypot (anti-spam)
        if (!empty($_POST['website_url'])) {
            wp_send_json_error(array('message' => 'Spam detectado'));
        }
        
        // Rate limiting
        $ip = $this->get_client_ip();
        $rate_key = 'nova_lead_rate_' . md5($ip);
        $submissions = get_transient($rate_key);
        
        if ($submissions && $submissions >= 3) {
            wp_send_json_error(array('message' => 'Demasiados envíos. Intenta más tarde.'));
        }
        
        // Validar campos requeridos
        $required = array('name', 'email');
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                wp_send_json_error(array('message' => 'Por favor completa todos los campos requeridos'));
            }
        }
        
        // Validar email
        if (!is_email($_POST['email'])) {
            wp_send_json_error(array('message' => 'Email no válido'));
        }
        
        // Obtener datos de tracking (método estático)
        $tracking = class_exists('Nova_Leads_Tracking') ? Nova_Leads_Tracking::get_data() : array();
        
        // Preparar datos del lead
        $lead_data = array(
            'name' => sanitize_text_field($_POST['name']),
            'email' => sanitize_email($_POST['email']),
            'phone' => isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '',
            'company' => isset($_POST['company']) ? sanitize_text_field($_POST['company']) : '',
            'sector' => isset($_POST['sector']) ? sanitize_text_field($_POST['sector']) : '',
            'company_size' => isset($_POST['company_size']) ? sanitize_text_field($_POST['company_size']) : '',
            'service' => isset($_POST['service']) ? sanitize_text_field($_POST['service']) : '',
            'budget' => isset($_POST['budget']) ? sanitize_text_field($_POST['budget']) : '',
            'urgency' => isset($_POST['urgency']) ? sanitize_text_field($_POST['urgency']) : '',
            'source' => isset($_POST['source']) ? sanitize_text_field($_POST['source']) : '',
            'message' => isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '',
            'challenges' => isset($_POST['challenges']) ? sanitize_textarea_field($_POST['challenges']) : '',
            'form_source' => isset($_POST['form_source']) ? sanitize_text_field($_POST['form_source']) : 'main_form',
            'time_on_page' => isset($_POST['time_on_page']) ? intval($_POST['time_on_page']) : 0,
        );
        
        // Merge con tracking data
        $lead_data = array_merge($lead_data, $tracking);
        
        // Crear el lead
        $lead_id = Nova_Leads_CPT::create_lead($lead_data);
        
        if (is_wp_error($lead_id)) {
            wp_send_json_error(array('message' => 'Error al guardar el lead'));
        }
        
        // Actualizar rate limiting
        set_transient($rate_key, ($submissions ? $submissions + 1 : 1), HOUR_IN_SECONDS);
        
        // Enviar email de notificación (si la clase existe)
        if (class_exists('Nova_Leads_Email')) {
            Nova_Leads_Email::send_notification($lead_id, $lead_data);
        }
        
        wp_send_json_success(array(
            'message' => '¡Gracias! Nos pondremos en contacto contigo pronto.',
            'lead_id' => $lead_id,
        ));
    }
    
    /**
     * Añadir nota a un lead
     */
    public function add_note() {
        if (!wp_verify_nonce($_POST['nonce'], 'nova_leads_admin')) {
            wp_send_json_error('Error de seguridad');
        }
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Sin permisos');
        }
        
        global $wpdb;
        
        $lead_id = intval($_POST['lead_id']);
        $note = sanitize_textarea_field($_POST['note']);
        
        if (empty($note)) {
            wp_send_json_error('La nota no puede estar vacía');
        }
        
        $table = $wpdb->prefix . 'nova_lead_notes';
        
        $result = $wpdb->insert($table, array(
            'lead_id' => $lead_id,
            'user_id' => get_current_user_id(),
            'note' => $note,
        ));
        
        if ($result === false) {
            wp_send_json_error('Error al guardar la nota');
        }
        
        $user = wp_get_current_user();
        
        wp_send_json_success(array(
            'id' => $wpdb->insert_id,
            'note' => $note,
            'author' => $user->display_name,
            'date' => date_i18n(get_option('date_format') . ' ' . get_option('time_format')),
        ));
    }
    
    /**
     * Eliminar nota
     */
    public function delete_note() {
        if (!wp_verify_nonce($_POST['nonce'], 'nova_leads_admin')) {
            wp_send_json_error('Error de seguridad');
        }
        
        if (!current_user_can('delete_posts')) {
            wp_send_json_error('Sin permisos');
        }
        
        global $wpdb;
        
        $note_id = intval($_POST['note_id']);
        $table = $wpdb->prefix . 'nova_lead_notes';
        
        $result = $wpdb->delete($table, array('id' => $note_id));
        
        if ($result === false) {
            wp_send_json_error('Error al eliminar la nota');
        }
        
        wp_send_json_success();
    }
    
    /**
     * Actualizar estado del lead
     */
    public function update_status() {
        if (!wp_verify_nonce($_POST['nonce'], 'nova_leads_admin')) {
            wp_send_json_error('Error de seguridad');
        }
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Sin permisos');
        }
        
        $lead_id = intval($_POST['lead_id']);
        $status = sanitize_text_field($_POST['status']);
        
        wp_set_object_terms($lead_id, $status, 'lead_status');
        
        wp_send_json_success();
    }
    
    /**
     * Obtener estadísticas (para AJAX refresh)
     */
    public function get_stats() {
        if (!wp_verify_nonce($_POST['nonce'], 'nova_leads_admin')) {
            wp_send_json_error('Error de seguridad');
        }
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Sin permisos');
        }
        
        $stats = Nova_Leads_Admin::get_stats();
        
        wp_send_json_success($stats);
    }
    
    /**
     * Obtener IP del cliente
     */
    private function get_client_ip() {
        $ip_keys = array(
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR',
        );
        
        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                // Tomar la primera IP si hay varias
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                return trim($ip);
            }
        }
        
        return '0.0.0.0';
    }
}
