<?php
/**
 * Template: Configuración de Nova Leads
 */

if (!defined('ABSPATH')) {
    exit;
}

// Guardar opciones
if (isset($_POST['nova_leads_save_settings']) && wp_verify_nonce($_POST['nova_leads_nonce'], 'nova_leads_settings')) {
    $options = array(
        'email_notifications' => isset($_POST['email_notifications']) ? 'yes' : 'no',
        'notification_email' => sanitize_email($_POST['notification_email']),
        'auto_response' => isset($_POST['auto_response']) ? 'yes' : 'no',
        'auto_response_subject' => sanitize_text_field($_POST['auto_response_subject']),
        'auto_response_message' => wp_kses_post($_POST['auto_response_message']),
    );
    
    update_option('nova_leads_settings', $options);
    echo '<div class="notice notice-success is-dismissible"><p>Configuración guardada correctamente.</p></div>';
}

$options = get_option('nova_leads_settings', array(
    'email_notifications' => 'yes',
    'notification_email' => get_option('admin_email'),
    'auto_response' => 'no',
    'auto_response_subject' => 'Gracias por contactarnos',
    'auto_response_message' => "Hola {name},\n\nGracias por tu interés en nuestros servicios. Hemos recibido tu mensaje y nos pondremos en contacto contigo pronto.\n\nSaludos,\nEl equipo de Nova Studio",
));
?>

<div class="wrap">
    <h1>Configuración de Nova Leads</h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('nova_leads_settings', 'nova_leads_nonce'); ?>
        
        <div class="nova-settings-section">
            <h2>Notificaciones por Email</h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Activar notificaciones</th>
                    <td>
                        <label>
                            <input type="checkbox" name="email_notifications" value="yes" <?php checked($options['email_notifications'], 'yes'); ?>>
                            Recibir email cuando llegue un nuevo lead
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Email de notificación</th>
                    <td>
                        <input type="email" name="notification_email" value="<?php echo esc_attr($options['notification_email']); ?>" class="regular-text">
                        <p class="description">Email donde recibirás las notificaciones de nuevos leads.</p>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="nova-settings-section">
            <h2>Respuesta Automática</h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Activar autorespuesta</th>
                    <td>
                        <label>
                            <input type="checkbox" name="auto_response" value="yes" <?php checked($options['auto_response'], 'yes'); ?>>
                            Enviar email automático al lead cuando envíe el formulario
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Asunto del email</th>
                    <td>
                        <input type="text" name="auto_response_subject" value="<?php echo esc_attr($options['auto_response_subject']); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Mensaje</th>
                    <td>
                        <textarea name="auto_response_message" rows="8" class="large-text"><?php echo esc_textarea($options['auto_response_message']); ?></textarea>
                        <p class="description">Variables disponibles: {name}, {email}, {company}, {service}</p>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="nova-settings-section">
            <h2>Shortcodes Disponibles</h2>
            
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Shortcode</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>[nova_lead_form]</code></td>
                        <td>Formulario simple de contacto</td>
                    </tr>
                    <tr>
                        <td><code>[nova_lead_form type="full"]</code></td>
                        <td>Formulario completo con pasos (contacto, empresa, proyecto)</td>
                    </tr>
                    <tr>
                        <td><code>[nova_lead_form type="cta"]</code></td>
                        <td>Formulario compacto para CTA inline</td>
                    </tr>
                    <tr>
                        <td><code>[nova_lead_form type="exit"]</code></td>
                        <td>Formulario para popup de salida</td>
                    </tr>
                </tbody>
            </table>
            
            <h3 style="margin-top: 20px;">Atributos disponibles:</h3>
            <ul style="list-style: disc; margin-left: 20px;">
                <li><code>title="Tu título"</code> - Título del formulario</li>
                <li><code>description="Tu descripción"</code> - Descripción del formulario</li>
                <li><code>button_text="Enviar"</code> - Texto del botón</li>
                <li><code>source="landing"</code> - Identificador para tracking</li>
                <li><code>show_company="yes|no"</code> - Mostrar campos de empresa</li>
                <li><code>show_budget="yes|no"</code> - Mostrar campo de presupuesto</li>
                <li><code>show_urgency="yes|no"</code> - Mostrar campo de urgencia</li>
                <li><code>show_message="yes|no"</code> - Mostrar campo de mensaje</li>
            </ul>
        </div>
        
        <p class="submit">
            <input type="submit" name="nova_leads_save_settings" class="button button-primary" value="Guardar Cambios">
        </p>
    </form>
</div>

<style>
.nova-settings-section {
    background: #fff;
    padding: 20px;
    margin: 20px 0;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
}
.nova-settings-section h2 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}
.nova-settings-section code {
    background: #f0f0f1;
    padding: 2px 6px;
    border-radius: 3px;
}
</style>
