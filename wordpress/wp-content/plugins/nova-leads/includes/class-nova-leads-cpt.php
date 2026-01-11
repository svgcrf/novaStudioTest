<?php
/**
 * Custom Post Type para Leads
 */

if (!defined('ABSPATH')) {
    exit;
}

class Nova_Leads_CPT {
    
    private static $instance = null;
    
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        // Registrar CPT y taxonomías directamente si init ya corrió
        if (did_action('init')) {
            $this->register_post_type();
            $this->register_taxonomies();
        } else {
            add_action('init', array($this, 'register_post_type'));
            add_action('init', array($this, 'register_taxonomies'));
        }
        
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_nova_lead', array($this, 'save_meta_boxes'), 10, 2);
    }
    
    public function register_post_type() {
        $labels = array(
            'name'                  => __('Leads', 'nova-leads'),
            'singular_name'         => __('Lead', 'nova-leads'),
            'menu_name'             => __('Nova Leads', 'nova-leads'),
            'add_new'               => __('Añadir Lead', 'nova-leads'),
            'add_new_item'          => __('Añadir Nuevo Lead', 'nova-leads'),
            'edit_item'             => __('Editar Lead', 'nova-leads'),
            'new_item'              => __('Nuevo Lead', 'nova-leads'),
            'view_item'             => __('Ver Lead', 'nova-leads'),
            'search_items'          => __('Buscar Leads', 'nova-leads'),
            'not_found'             => __('No se encontraron leads', 'nova-leads'),
            'not_found_in_trash'    => __('No hay leads en la papelera', 'nova-leads'),
            'all_items'             => __('Todos los Leads', 'nova-leads'),
        );
        
        $args = array(
            'labels'              => $labels,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => false, // Lo añadimos manualmente
            'query_var'           => false,
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'supports'            => array('title'),
            'menu_icon'           => 'dashicons-groups',
        );
        
        register_post_type('nova_lead', $args);
    }
    
    public function register_taxonomies() {
        // Estado del Lead
        register_taxonomy('lead_status', 'nova_lead', array(
            'labels' => array(
                'name' => __('Estados', 'nova-leads'),
                'singular_name' => __('Estado', 'nova-leads'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_admin_column' => true,
            'hierarchical' => false,
        ));
        
        // Insertar estados por defecto
        $this->insert_default_terms();
        
        // Servicio de interés
        register_taxonomy('lead_service', 'nova_lead', array(
            'labels' => array(
                'name' => __('Servicios', 'nova-leads'),
                'singular_name' => __('Servicio', 'nova-leads'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_admin_column' => true,
            'hierarchical' => false,
        ));
        
        // Fuente
        register_taxonomy('lead_source', 'nova_lead', array(
            'labels' => array(
                'name' => __('Fuentes', 'nova-leads'),
                'singular_name' => __('Fuente', 'nova-leads'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_admin_column' => true,
            'hierarchical' => false,
        ));
    }
    
    private function insert_default_terms() {
        $statuses = array(
            'nuevo' => array('name' => '🆕 Nuevo', 'slug' => 'nuevo'),
            'contactado' => array('name' => '📞 Contactado', 'slug' => 'contactado'),
            'negociacion' => array('name' => '💬 En Negociación', 'slug' => 'negociacion'),
            'propuesta' => array('name' => '📋 Propuesta Enviada', 'slug' => 'propuesta'),
            'convertido' => array('name' => '✅ Convertido', 'slug' => 'convertido'),
            'perdido' => array('name' => '❌ Perdido', 'slug' => 'perdido'),
        );
        
        foreach ($statuses as $status) {
            if (!term_exists($status['slug'], 'lead_status')) {
                wp_insert_term($status['name'], 'lead_status', array('slug' => $status['slug']));
            }
        }
        
        $services = array(
            'diseno-web' => 'Diseño Web UI/UX',
            'desarrollo-web' => 'Desarrollo Web',
            'estrategia-digital' => 'Estrategia Digital',
            'ecommerce' => 'E-commerce',
            'seo-marketing' => 'SEO & Marketing',
            'proyecto-completo' => 'Proyecto Completo',
        );
        
        foreach ($services as $slug => $name) {
            if (!term_exists($slug, 'lead_service')) {
                wp_insert_term($name, 'lead_service', array('slug' => $slug));
            }
        }
        
        $sources = array(
            'google' => 'Google (Orgánico)',
            'google-ads' => 'Google Ads',
            'redes-sociales' => 'Redes Sociales',
            'referido' => 'Referido',
            'directo' => 'Directo',
            'otro' => 'Otro',
        );
        
        foreach ($sources as $slug => $name) {
            if (!term_exists($slug, 'lead_source')) {
                wp_insert_term($name, 'lead_source', array('slug' => $slug));
            }
        }
    }
    
    public function add_meta_boxes() {
        // Información de Contacto
        add_meta_box(
            'nova_lead_contact',
            __('📧 Información de Contacto', 'nova-leads'),
            array($this, 'render_contact_meta_box'),
            'nova_lead',
            'normal',
            'high'
        );
        
        // Perfil del Proyecto
        add_meta_box(
            'nova_lead_project',
            __('🎯 Perfil del Proyecto', 'nova-leads'),
            array($this, 'render_project_meta_box'),
            'nova_lead',
            'normal',
            'high'
        );
        
        // Datos de Tracking
        add_meta_box(
            'nova_lead_tracking',
            __('📊 Datos de Tracking', 'nova-leads'),
            array($this, 'render_tracking_meta_box'),
            'nova_lead',
            'side',
            'default'
        );
        
        // Notas Internas
        add_meta_box(
            'nova_lead_notes',
            __('📝 Notas Internas', 'nova-leads'),
            array($this, 'render_notes_meta_box'),
            'nova_lead',
            'normal',
            'default'
        );
    }
    
    public function render_contact_meta_box($post) {
        wp_nonce_field('nova_lead_save', 'nova_lead_nonce');
        
        $email = get_post_meta($post->ID, '_lead_email', true);
        $phone = get_post_meta($post->ID, '_lead_phone', true);
        $company = get_post_meta($post->ID, '_lead_company', true);
        $sector = get_post_meta($post->ID, '_lead_sector', true);
        $company_size = get_post_meta($post->ID, '_lead_company_size', true);
        $location = get_post_meta($post->ID, '_lead_location', true);
        ?>
        <table class="form-table nova-lead-form">
            <tr>
                <th><label for="lead_email"><?php _e('Email', 'nova-leads'); ?> *</label></th>
                <td>
                    <input type="email" id="lead_email" name="lead_email" value="<?php echo esc_attr($email); ?>" class="regular-text" required>
                </td>
            </tr>
            <tr>
                <th><label for="lead_phone"><?php _e('Teléfono', 'nova-leads'); ?></label></th>
                <td>
                    <input type="tel" id="lead_phone" name="lead_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th><label for="lead_company"><?php _e('Empresa', 'nova-leads'); ?></label></th>
                <td>
                    <input type="text" id="lead_company" name="lead_company" value="<?php echo esc_attr($company); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th><label for="lead_sector"><?php _e('Sector/Industria', 'nova-leads'); ?></label></th>
                <td>
                    <select id="lead_sector" name="lead_sector" class="regular-text">
                        <option value=""><?php _e('Seleccionar...', 'nova-leads'); ?></option>
                        <?php
                        $sectors = array(
                            'tecnologia' => 'Tecnología',
                            'salud' => 'Salud',
                            'educacion' => 'Educación',
                            'finanzas' => 'Finanzas',
                            'retail' => 'Retail/Comercio',
                            'servicios' => 'Servicios Profesionales',
                            'industria' => 'Industria/Manufactura',
                            'hosteleria' => 'Hostelería/Turismo',
                            'inmobiliaria' => 'Inmobiliaria',
                            'otro' => 'Otro',
                        );
                        foreach ($sectors as $value => $label) {
                            printf(
                                '<option value="%s" %s>%s</option>',
                                esc_attr($value),
                                selected($sector, $value, false),
                                esc_html($label)
                            );
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="lead_company_size"><?php _e('Tamaño Empresa', 'nova-leads'); ?></label></th>
                <td>
                    <select id="lead_company_size" name="lead_company_size" class="regular-text">
                        <option value=""><?php _e('Seleccionar...', 'nova-leads'); ?></option>
                        <?php
                        $sizes = array(
                            'autonomo' => 'Autónomo/Freelance',
                            '1-10' => '1-10 empleados',
                            '11-50' => '11-50 empleados',
                            '51-200' => '51-200 empleados',
                            '200+' => 'Más de 200 empleados',
                        );
                        foreach ($sizes as $value => $label) {
                            printf(
                                '<option value="%s" %s>%s</option>',
                                esc_attr($value),
                                selected($company_size, $value, false),
                                esc_html($label)
                            );
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="lead_location"><?php _e('Ubicación', 'nova-leads'); ?></label></th>
                <td>
                    <input type="text" id="lead_location" name="lead_location" value="<?php echo esc_attr($location); ?>" class="regular-text" placeholder="Ciudad, País">
                </td>
            </tr>
        </table>
        <?php
    }
    
    public function render_project_meta_box($post) {
        $budget = get_post_meta($post->ID, '_lead_budget', true);
        $urgency = get_post_meta($post->ID, '_lead_urgency', true);
        $message = get_post_meta($post->ID, '_lead_message', true);
        $challenges = get_post_meta($post->ID, '_lead_challenges', true);
        ?>
        <table class="form-table nova-lead-form">
            <tr>
                <th><label for="lead_budget"><?php _e('Presupuesto Estimado', 'nova-leads'); ?></label></th>
                <td>
                    <select id="lead_budget" name="lead_budget" class="regular-text">
                        <option value=""><?php _e('Seleccionar...', 'nova-leads'); ?></option>
                        <?php
                        $budgets = array(
                            'menos-3k' => 'Menos de 3.000€',
                            '3k-5k' => '3.000€ - 5.000€',
                            '5k-10k' => '5.000€ - 10.000€',
                            '10k-20k' => '10.000€ - 20.000€',
                            '20k+' => 'Más de 20.000€',
                        );
                        foreach ($budgets as $value => $label) {
                            printf(
                                '<option value="%s" %s>%s</option>',
                                esc_attr($value),
                                selected($budget, $value, false),
                                esc_html($label)
                            );
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="lead_urgency"><?php _e('Urgencia', 'nova-leads'); ?></label></th>
                <td>
                    <select id="lead_urgency" name="lead_urgency" class="regular-text">
                        <option value=""><?php _e('Seleccionar...', 'nova-leads'); ?></option>
                        <?php
                        $urgencies = array(
                            'inmediato' => '🔥 Inmediato (< 2 semanas)',
                            '1-mes' => '⚡ En 1 mes',
                            '1-3-meses' => '📅 En 1-3 meses',
                            '3-6-meses' => '🗓️ En 3-6 meses',
                            'explorando' => '🔍 Solo explorando',
                        );
                        foreach ($urgencies as $value => $label) {
                            printf(
                                '<option value="%s" %s>%s</option>',
                                esc_attr($value),
                                selected($urgency, $value, false),
                                esc_html($label)
                            );
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="lead_challenges"><?php _e('Principales Desafíos', 'nova-leads'); ?></label></th>
                <td>
                    <textarea id="lead_challenges" name="lead_challenges" rows="3" class="large-text"><?php echo esc_textarea($challenges); ?></textarea>
                </td>
            </tr>
            <tr>
                <th><label for="lead_message"><?php _e('Mensaje/Descripción', 'nova-leads'); ?></label></th>
                <td>
                    <textarea id="lead_message" name="lead_message" rows="5" class="large-text"><?php echo esc_textarea($message); ?></textarea>
                </td>
            </tr>
        </table>
        <?php
    }
    
    public function render_tracking_meta_box($post) {
        $ip = get_post_meta($post->ID, '_lead_ip', true);
        $country = get_post_meta($post->ID, '_lead_country', true);
        $city = get_post_meta($post->ID, '_lead_city', true);
        $device = get_post_meta($post->ID, '_lead_device', true);
        $browser = get_post_meta($post->ID, '_lead_browser', true);
        $referer = get_post_meta($post->ID, '_lead_referer', true);
        $landing_page = get_post_meta($post->ID, '_lead_landing_page', true);
        $utm_source = get_post_meta($post->ID, '_lead_utm_source', true);
        $utm_medium = get_post_meta($post->ID, '_lead_utm_medium', true);
        $utm_campaign = get_post_meta($post->ID, '_lead_utm_campaign', true);
        $time_on_page = get_post_meta($post->ID, '_lead_time_on_page', true);
        $form_source = get_post_meta($post->ID, '_lead_form_source', true);
        ?>
        <div class="nova-tracking-info">
            <?php if ($ip) : ?>
                <p><strong>🌐 IP:</strong> <?php echo esc_html($ip); ?></p>
            <?php endif; ?>
            
            <?php if ($country || $city) : ?>
                <p><strong>📍 Ubicación:</strong> <?php echo esc_html(trim("$city, $country", ', ')); ?></p>
            <?php endif; ?>
            
            <?php if ($device) : ?>
                <p><strong>📱 Dispositivo:</strong> <?php echo esc_html($device); ?></p>
            <?php endif; ?>
            
            <?php if ($browser) : ?>
                <p><strong>🌍 Navegador:</strong> <?php echo esc_html($browser); ?></p>
            <?php endif; ?>
            
            <?php if ($referer) : ?>
                <p><strong>🔗 Referer:</strong> <small><?php echo esc_url($referer); ?></small></p>
            <?php endif; ?>
            
            <?php if ($landing_page) : ?>
                <p><strong>📄 Página:</strong> <small><?php echo esc_html($landing_page); ?></small></p>
            <?php endif; ?>
            
            <?php if ($utm_source) : ?>
                <p><strong>📊 UTM Source:</strong> <?php echo esc_html($utm_source); ?></p>
            <?php endif; ?>
            
            <?php if ($utm_medium) : ?>
                <p><strong>📊 UTM Medium:</strong> <?php echo esc_html($utm_medium); ?></p>
            <?php endif; ?>
            
            <?php if ($utm_campaign) : ?>
                <p><strong>📊 UTM Campaign:</strong> <?php echo esc_html($utm_campaign); ?></p>
            <?php endif; ?>
            
            <?php if ($time_on_page) : ?>
                <p><strong>⏱️ Tiempo en página:</strong> <?php echo esc_html($time_on_page); ?>s</p>
            <?php endif; ?>
            
            <?php if ($form_source) : ?>
                <p><strong>📝 Fuente formulario:</strong> <?php echo esc_html($form_source); ?></p>
            <?php endif; ?>
            
            <?php if (!$ip && !$device) : ?>
                <p class="description"><?php _e('No hay datos de tracking disponibles', 'nova-leads'); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }
    
    public function render_notes_meta_box($post) {
        global $wpdb;
        
        $table_notes = $wpdb->prefix . 'nova_lead_notes';
        $notes = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_notes WHERE lead_id = %d ORDER BY created_at DESC",
            $post->ID
        ));
        ?>
        <div class="nova-notes-container">
            <div class="nova-add-note">
                <textarea id="nova_new_note" placeholder="<?php _e('Añadir nueva nota...', 'nova-leads'); ?>" rows="3" class="large-text"></textarea>
                <button type="button" class="button button-primary" id="nova_add_note_btn" data-lead-id="<?php echo $post->ID; ?>">
                    <?php _e('Añadir Nota', 'nova-leads'); ?>
                </button>
            </div>
            
            <div class="nova-notes-list" id="nova_notes_list">
                <?php if (empty($notes)) : ?>
                    <p class="no-notes"><?php _e('No hay notas todavía', 'nova-leads'); ?></p>
                <?php else : ?>
                    <?php foreach ($notes as $note) : 
                        $user = get_userdata($note->user_id);
                    ?>
                        <div class="nova-note" data-note-id="<?php echo $note->id; ?>">
                            <div class="note-header">
                                <span class="note-author"><?php echo esc_html($user->display_name); ?></span>
                                <span class="note-date"><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($note->created_at)); ?></span>
                                <button type="button" class="note-delete" data-note-id="<?php echo $note->id; ?>" title="<?php _e('Eliminar', 'nova-leads'); ?>">×</button>
                            </div>
                            <div class="note-content"><?php echo nl2br(esc_html($note->note)); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
    
    public function save_meta_boxes($post_id, $post) {
        // Verificar nonce
        if (!isset($_POST['nova_lead_nonce']) || !wp_verify_nonce($_POST['nova_lead_nonce'], 'nova_lead_save')) {
            return;
        }
        
        // Verificar autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Verificar permisos
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Campos a guardar
        $fields = array(
            'lead_email' => 'sanitize_email',
            'lead_phone' => 'sanitize_text_field',
            'lead_company' => 'sanitize_text_field',
            'lead_sector' => 'sanitize_text_field',
            'lead_company_size' => 'sanitize_text_field',
            'lead_location' => 'sanitize_text_field',
            'lead_budget' => 'sanitize_text_field',
            'lead_urgency' => 'sanitize_text_field',
            'lead_message' => 'sanitize_textarea_field',
            'lead_challenges' => 'sanitize_textarea_field',
        );
        
        foreach ($fields as $field => $sanitize_callback) {
            if (isset($_POST[$field])) {
                $value = call_user_func($sanitize_callback, $_POST[$field]);
                update_post_meta($post_id, '_' . $field, $value);
            }
        }
    }
    
    /**
     * Crear un nuevo lead
     */
    public static function create_lead($data) {
        $lead_data = array(
            'post_title' => sanitize_text_field($data['name']),
            'post_type' => 'nova_lead',
            'post_status' => 'publish',
        );
        
        $lead_id = wp_insert_post($lead_data);
        
        if (is_wp_error($lead_id)) {
            return $lead_id;
        }
        
        // Meta datos de contacto
        $meta_fields = array(
            'email', 'phone', 'company', 'sector', 'company_size', 'location',
            'budget', 'urgency', 'message', 'challenges',
            'ip', 'country', 'city', 'device', 'browser', 'referer',
            'landing_page', 'utm_source', 'utm_medium', 'utm_campaign',
            'time_on_page', 'form_source'
        );
        
        foreach ($meta_fields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                update_post_meta($lead_id, '_lead_' . $field, sanitize_text_field($data[$field]));
            }
        }
        
        // Asignar estado "Nuevo"
        wp_set_object_terms($lead_id, 'nuevo', 'lead_status');
        
        // Asignar servicio si existe
        if (!empty($data['service'])) {
            wp_set_object_terms($lead_id, sanitize_text_field($data['service']), 'lead_service');
        }
        
        // Asignar fuente si existe
        if (!empty($data['source'])) {
            wp_set_object_terms($lead_id, sanitize_text_field($data['source']), 'lead_source');
        }
        
        return $lead_id;
    }
}
