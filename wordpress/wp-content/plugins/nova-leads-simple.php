<?php
/**
 * Plugin Name: Nova Leads Simple
 * Description: Sistema simple de gestión de leads para Nova Studio
 * Version: 1.0.0
 * Author: Nova Studio
 */

if (!defined('ABSPATH')) exit;

// =====================================
// ACTIVACIÓN
// =====================================
register_activation_hook(__FILE__, function() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table = $wpdb->prefix . 'nova_lead_notes';
    
    $sql = "CREATE TABLE IF NOT EXISTS $table (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        lead_id bigint(20) NOT NULL,
        note text NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    if (get_option('nova_leads_email') === false) {
        update_option('nova_leads_email', get_option('admin_email'));
    }
    
    flush_rewrite_rules();
});

// =====================================
// CUSTOM POST TYPE
// =====================================
add_action('init', function() {
    register_post_type('nova_lead', array(
        'labels' => array(
            'name' => 'Leads',
            'singular_name' => 'Lead',
            'add_new' => 'Añadir Lead',
            'edit_item' => 'Editar Lead',
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => false,
        'supports' => array('title'),
        'menu_icon' => 'dashicons-groups',
    ));
    
    register_taxonomy('lead_status', 'nova_lead', array(
        'labels' => array('name' => 'Estados'),
        'public' => false,
        'show_ui' => true,
        'show_admin_column' => true,
    ));
    
    register_taxonomy('lead_service', 'nova_lead', array(
        'labels' => array('name' => 'Servicios'),
        'public' => false,
        'show_ui' => true,
        'show_admin_column' => true,
    ));
    
    // Estados por defecto
    $statuses = array('nuevo' => '🆕 Nuevo', 'contactado' => '📞 Contactado', 'convertido' => '✅ Convertido', 'perdido' => '❌ Perdido');
    foreach ($statuses as $slug => $name) {
        if (!term_exists($slug, 'lead_status')) {
            wp_insert_term($name, 'lead_status', array('slug' => $slug));
        }
    }
    
    // Servicios por defecto
    $services = array('diseno-web' => 'Diseño Web', 'desarrollo-web' => 'Desarrollo Web', 'marketing' => 'Marketing Digital');
    foreach ($services as $slug => $name) {
        if (!term_exists($slug, 'lead_service')) {
            wp_insert_term($name, 'lead_service', array('slug' => $slug));
        }
    }
});

// =====================================
// MENÚ DE ADMIN
// =====================================
add_action('admin_menu', function() {
    add_menu_page('Nova Leads', 'Nova Leads', 'manage_options', 'nova-leads', 'nova_leads_dashboard', 'dashicons-groups', 25);
    add_submenu_page('nova-leads', 'Dashboard', '📊 Dashboard', 'manage_options', 'nova-leads', 'nova_leads_dashboard');
    add_submenu_page('nova-leads', 'Todos los Leads', '📋 Todos los Leads', 'manage_options', 'edit.php?post_type=nova_lead');
    add_submenu_page('nova-leads', 'Añadir Lead', '➕ Añadir Lead', 'manage_options', 'post-new.php?post_type=nova_lead');
    add_submenu_page('nova-leads', 'Configuración', '⚙️ Configuración', 'manage_options', 'nova-leads-settings', 'nova_leads_settings');
    add_submenu_page('nova-leads', 'Exportar', '📥 Exportar', 'manage_options', 'nova-leads-export', 'nova_leads_export');
});

// Highlight menú correcto
add_action('admin_head', function() {
    global $parent_file, $submenu_file, $post_type;
    if ($post_type === 'nova_lead') {
        $parent_file = 'nova-leads';
        $submenu_file = 'edit.php?post_type=nova_lead';
    }
});

// =====================================
// DASHBOARD
// =====================================
function nova_leads_dashboard() {
    $total = wp_count_posts('nova_lead');
    $total_count = isset($total->publish) ? $total->publish : 0;
    
    $nuevo = nova_leads_count_status('nuevo');
    $contactado = nova_leads_count_status('contactado');
    $convertido = nova_leads_count_status('convertido');
    
    $recent = get_posts(array('post_type' => 'nova_lead', 'posts_per_page' => 10));
    ?>
    <div class="wrap">
        <h1>📊 Dashboard de Nova Leads</h1>
        
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin: 30px 0;">
            <div style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 30px; border-radius: 15px; text-align: center;">
                <div style="font-size: 48px; font-weight: bold;"><?php echo $total_count; ?></div>
                <div>Total Leads</div>
            </div>
            <div style="background: linear-gradient(135deg, #11998e, #38ef7d); color: white; padding: 30px; border-radius: 15px; text-align: center;">
                <div style="font-size: 48px; font-weight: bold;"><?php echo $nuevo; ?></div>
                <div>🆕 Nuevos</div>
            </div>
            <div style="background: linear-gradient(135deg, #f093fb, #f5576c); color: white; padding: 30px; border-radius: 15px; text-align: center;">
                <div style="font-size: 48px; font-weight: bold;"><?php echo $contactado; ?></div>
                <div>📞 Contactados</div>
            </div>
            <div style="background: linear-gradient(135deg, #4facfe, #00f2fe); color: white; padding: 30px; border-radius: 15px; text-align: center;">
                <div style="font-size: 48px; font-weight: bold;"><?php echo $convertido; ?></div>
                <div>✅ Convertidos</div>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                <h2>📋 Leads Recientes</h2>
                <?php if ($recent): ?>
                <table class="wp-list-table widefat fixed striped" style="margin-top: 15px;">
                    <thead><tr><th>Nombre</th><th>Email</th><th>Estado</th><th>Fecha</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($recent as $lead): 
                        $email = get_post_meta($lead->ID, '_lead_email', true);
                        $status = wp_get_post_terms($lead->ID, 'lead_status', array('fields' => 'names'));
                    ?>
                    <tr>
                        <td><strong><?php echo esc_html($lead->post_title); ?></strong></td>
                        <td><?php echo esc_html($email); ?></td>
                        <td><?php echo !empty($status) ? $status[0] : '🆕 Nuevo'; ?></td>
                        <td><?php echo get_the_date('d/m/Y', $lead->ID); ?></td>
                        <td><a href="<?php echo get_edit_post_link($lead->ID); ?>" class="button button-small">Ver</a></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p style="text-align: center; padding: 40px; color: #666;">Aún no hay leads.</p>
                <?php endif; ?>
            </div>
            
            <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                <h2>⚡ Acciones Rápidas</h2>
                <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 15px;">
                    <a href="<?php echo admin_url('post-new.php?post_type=nova_lead'); ?>" class="button button-primary button-hero">➕ Añadir Lead</a>
                    <a href="<?php echo admin_url('edit.php?post_type=nova_lead'); ?>" class="button button-hero">📋 Ver Todos</a>
                    <a href="<?php echo admin_url('admin.php?page=nova-leads-export'); ?>" class="button button-hero">📥 Exportar CSV</a>
                </div>
                
                <h3 style="margin-top: 30px;">📝 Shortcode</h3>
                <code style="display: block; padding: 15px; background: #f0f0f0; border-radius: 8px;">[nova_lead_form]</code>
            </div>
        </div>
    </div>
    <?php
}

function nova_leads_count_status($slug) {
    $q = new WP_Query(array(
        'post_type' => 'nova_lead',
        'posts_per_page' => -1,
        'tax_query' => array(array('taxonomy' => 'lead_status', 'field' => 'slug', 'terms' => $slug)),
    ));
    return $q->found_posts;
}

// =====================================
// CONFIGURACIÓN
// =====================================
function nova_leads_settings() {
    if (isset($_POST['save']) && wp_verify_nonce($_POST['_wpnonce'], 'nova_settings')) {
        update_option('nova_leads_email', sanitize_email($_POST['email']));
        echo '<div class="notice notice-success"><p>Guardado.</p></div>';
    }
    $email = get_option('nova_leads_email', get_option('admin_email'));
    ?>
    <div class="wrap">
        <h1>⚙️ Configuración</h1>
        <form method="post" style="max-width: 500px; margin-top: 20px; background: white; padding: 30px; border-radius: 15px;">
            <?php wp_nonce_field('nova_settings'); ?>
            <table class="form-table">
                <tr><th>Email notificaciones</th><td><input type="email" name="email" value="<?php echo esc_attr($email); ?>" class="regular-text"></td></tr>
            </table>
            <p><input type="submit" name="save" class="button button-primary" value="Guardar"></p>
        </form>
    </div>
    <?php
}

// =====================================
// EXPORTAR
// =====================================
function nova_leads_export() {
    if (isset($_POST['export']) && wp_verify_nonce($_POST['_wpnonce'], 'nova_export')) {
        $leads = get_posts(array('post_type' => 'nova_lead', 'posts_per_page' => -1));
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=leads-' . date('Y-m-d') . '.csv');
        
        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($out, array('Nombre', 'Email', 'Teléfono', 'Empresa', 'Mensaje', 'Fecha'));
        
        foreach ($leads as $lead) {
            fputcsv($out, array(
                $lead->post_title,
                get_post_meta($lead->ID, '_lead_email', true),
                get_post_meta($lead->ID, '_lead_phone', true),
                get_post_meta($lead->ID, '_lead_company', true),
                get_post_meta($lead->ID, '_lead_message', true),
                get_the_date('Y-m-d', $lead->ID),
            ));
        }
        fclose($out);
        exit;
    }
    ?>
    <div class="wrap">
        <h1>📥 Exportar Leads</h1>
        <form method="post" style="margin-top: 20px; background: white; padding: 30px; border-radius: 15px; max-width: 400px;">
            <?php wp_nonce_field('nova_export'); ?>
            <p>Descarga todos los leads en formato CSV.</p>
            <button type="submit" name="export" class="button button-primary button-hero">📥 Descargar CSV</button>
        </form>
    </div>
    <?php
}

// =====================================
// META BOXES
// =====================================
add_action('add_meta_boxes', function() {
    add_meta_box('nova_lead_info', '📧 Información del Lead', 'nova_leads_meta_box', 'nova_lead', 'normal', 'high');
});

function nova_leads_meta_box($post) {
    wp_nonce_field('nova_lead', 'nova_lead_nonce');
    $fields = array('_lead_email' => 'Email', '_lead_phone' => 'Teléfono', '_lead_company' => 'Empresa', '_lead_budget' => 'Presupuesto');
    
    echo '<table class="form-table">';
    foreach ($fields as $key => $label) {
        $value = get_post_meta($post->ID, $key, true);
        echo "<tr><th>$label</th><td><input type='text' name='$key' value='" . esc_attr($value) . "' class='regular-text'></td></tr>";
    }
    $msg = get_post_meta($post->ID, '_lead_message', true);
    echo "<tr><th>Mensaje</th><td><textarea name='_lead_message' rows='4' class='large-text'>" . esc_textarea($msg) . "</textarea></td></tr>";
    echo '</table>';
}

add_action('save_post_nova_lead', function($post_id) {
    if (!isset($_POST['nova_lead_nonce']) || !wp_verify_nonce($_POST['nova_lead_nonce'], 'nova_lead')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    
    foreach (array('_lead_email', '_lead_phone', '_lead_company', '_lead_budget', '_lead_message') as $key) {
        if (isset($_POST[$key])) {
            update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
        }
    }
});

// =====================================
// COLUMNAS PERSONALIZADAS
// =====================================
add_filter('manage_nova_lead_posts_columns', function($cols) {
    return array('cb' => $cols['cb'], 'title' => 'Nombre', 'email' => 'Email', 'phone' => 'Teléfono', 'taxonomy-lead_status' => 'Estado', 'date' => 'Fecha');
});

add_action('manage_nova_lead_posts_custom_column', function($col, $id) {
    if ($col === 'email') echo esc_html(get_post_meta($id, '_lead_email', true));
    if ($col === 'phone') echo esc_html(get_post_meta($id, '_lead_phone', true));
}, 10, 2);

// =====================================
// AJAX - ENVIAR FORMULARIO
// =====================================
add_action('wp_ajax_nova_submit_lead', 'nova_leads_ajax_submit');
add_action('wp_ajax_nopriv_nova_submit_lead', 'nova_leads_ajax_submit');
add_action('wp_ajax_nova_leads_submit', 'nova_leads_ajax_submit');
add_action('wp_ajax_nopriv_nova_leads_submit', 'nova_leads_ajax_submit');

function nova_leads_ajax_submit() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'nova_leads_submit')) {
        wp_send_json_error('Error de seguridad');
    }
    
    if (empty($_POST['name']) || empty($_POST['email'])) {
        wp_send_json_error('Campos requeridos vacíos');
    }
    
    if (!is_email($_POST['email'])) {
        wp_send_json_error('Email no válido');
    }
    
    $lead_id = wp_insert_post(array(
        'post_type' => 'nova_lead',
        'post_title' => sanitize_text_field($_POST['name']),
        'post_status' => 'publish',
    ));
    
    if (is_wp_error($lead_id)) {
        wp_send_json_error('Error al guardar');
    }
    
    update_post_meta($lead_id, '_lead_email', sanitize_email($_POST['email']));
    if (!empty($_POST['phone'])) update_post_meta($lead_id, '_lead_phone', sanitize_text_field($_POST['phone']));
    if (!empty($_POST['company'])) update_post_meta($lead_id, '_lead_company', sanitize_text_field($_POST['company']));
    if (!empty($_POST['message'])) update_post_meta($lead_id, '_lead_message', sanitize_textarea_field($_POST['message']));
    if (!empty($_POST['service'])) wp_set_object_terms($lead_id, sanitize_text_field($_POST['service']), 'lead_service');
    
    wp_set_object_terms($lead_id, 'nuevo', 'lead_status');
    
    // Email de notificación
    $admin_email = get_option('nova_leads_email', get_option('admin_email'));
    $subject = '🎯 Nuevo Lead: ' . sanitize_text_field($_POST['name']);
    $body = "Nombre: " . $_POST['name'] . "\nEmail: " . $_POST['email'] . "\n";
    if (!empty($_POST['phone'])) $body .= "Teléfono: " . $_POST['phone'] . "\n";
    if (!empty($_POST['message'])) $body .= "Mensaje: " . $_POST['message'] . "\n";
    $body .= "\nVer: " . admin_url('post.php?post=' . $lead_id . '&action=edit');
    
    wp_mail($admin_email, $subject, $body);
    
    wp_send_json_success(array('message' => '¡Gracias! Te contactaremos pronto.', 'lead_id' => $lead_id));
}

// =====================================
// SHORTCODE DEL FORMULARIO
// =====================================
add_shortcode('nova_lead_form', function($atts) {
    $atts = shortcode_atts(array('title' => 'Solicita tu Presupuesto'), $atts);
    
    ob_start();
    ?>
    <div class="nova-lead-form" id="nova-form">
        <form class="nova-form" method="post">
            <h3 style="text-align: center; margin-bottom: 25px;"><?php echo esc_html($atts['title']); ?></h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Nombre *</label>
                    <input type="text" name="name" required placeholder="Tu nombre">
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" required placeholder="tu@email.com">
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px;">
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="tel" name="phone" placeholder="+34 600 000 000">
                </div>
                <div class="form-group">
                    <label>Empresa</label>
                    <input type="text" name="company" placeholder="Tu empresa">
                </div>
            </div>
            
            <div class="form-group" style="margin-top: 15px;">
                <label>Servicio</label>
                <select name="service">
                    <option value="">Selecciona...</option>
                    <option value="diseno-web">Diseño Web</option>
                    <option value="desarrollo-web">Desarrollo Web</option>
                    <option value="marketing">Marketing Digital</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-top: 15px;">
                <label>Mensaje</label>
                <textarea name="message" rows="4" placeholder="Cuéntanos sobre tu proyecto..."></textarea>
            </div>
            
            <button type="submit" class="nova-submit">Enviar Solicitud →</button>
            <div class="nova-message" style="display: none;"></div>
        </form>
        
        <div class="nova-success" style="display: none; text-align: center; padding: 40px;">
            <div style="font-size: 60px;">✅</div>
            <h3>¡Mensaje Enviado!</h3>
            <p>Te contactaremos pronto.</p>
        </div>
    </div>
    
    <style>
    .nova-lead-form { max-width: 600px; margin: 0 auto; padding: 30px; background: white; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); }
    .nova-lead-form .form-group { margin-bottom: 0; }
    .nova-lead-form label { display: block; margin-bottom: 5px; font-weight: 600; }
    .nova-lead-form input, .nova-lead-form select, .nova-lead-form textarea { width: 100%; padding: 12px 15px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px; box-sizing: border-box; }
    .nova-lead-form input:focus, .nova-lead-form select:focus, .nova-lead-form textarea:focus { outline: none; border-color: #6c63ff; }
    .nova-submit { width: 100%; margin-top: 20px; padding: 15px; background: linear-gradient(135deg, #6c63ff, #4834d4); color: white; border: none; border-radius: 10px; font-size: 18px; font-weight: 600; cursor: pointer; }
    .nova-submit:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(108,99,255,0.4); }
    .nova-message { margin-top: 15px; padding: 15px; border-radius: 8px; text-align: center; }
    .nova-message.error { background: #fee; color: #c00; }
    .nova-message.success { background: #efe; color: #080; }
    @media (max-width: 600px) { .nova-lead-form div[style*="grid-template-columns: 1fr 1fr"] { grid-template-columns: 1fr !important; } }
    </style>
    
    <script>
    document.querySelector('.nova-form').addEventListener('submit', function(e) {
        e.preventDefault();
        var form = this;
        var btn = form.querySelector('.nova-submit');
        var msg = form.querySelector('.nova-message');
        var data = new FormData(form);
        
        data.append('action', 'nova_submit_lead');
        data.append('nonce', '<?php echo wp_create_nonce('nova_leads_submit'); ?>');
        
        btn.textContent = 'Enviando...';
        btn.disabled = true;
        msg.style.display = 'none';
        
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                form.style.display = 'none';
                document.querySelector('.nova-success').style.display = 'block';
            } else {
                msg.className = 'nova-message error';
                msg.textContent = res.data;
                msg.style.display = 'block';
                btn.textContent = 'Enviar Solicitud →';
                btn.disabled = false;
            }
        })
        .catch(() => {
            msg.className = 'nova-message error';
            msg.textContent = 'Error de conexión';
            msg.style.display = 'block';
            btn.textContent = 'Enviar Solicitud →';
            btn.disabled = false;
        });
    });
    </script>
    <?php
    return ob_get_clean();
});
