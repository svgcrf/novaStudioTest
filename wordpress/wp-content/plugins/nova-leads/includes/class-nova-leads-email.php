<?php
/**
 * Notificaciones por email
 */

if (!defined('ABSPATH')) {
    exit;
}

class Nova_Leads_Email {
    
    private static $instance = null;
    
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Enviar notificación de nuevo lead
     */
    public static function send_notification($lead_id, $data) {
        $options = get_option('nova_leads_settings', array());
        
        // Verificar si las notificaciones están habilitadas
        if (empty($options['email_notifications']) || $options['email_notifications'] !== 'yes') {
            return false;
        }
        
        // Email destinatario
        $to = !empty($options['notification_email']) 
            ? $options['notification_email'] 
            : get_option('admin_email');
        
        // Asunto
        $subject = sprintf(
            '[Nuevo Lead] %s - %s',
            $data['name'],
            !empty($data['service']) ? $data['service'] : 'Sin servicio especificado'
        );
        
        // Contenido HTML
        $message = self::build_email_template($lead_id, $data);
        
        // Headers
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: Nova Leads <' . get_option('admin_email') . '>',
        );
        
        // Enviar
        $sent = wp_mail($to, $subject, $message, $headers);
        
        // Enviar webhook si está configurado
        self::send_webhook($lead_id, $data);
        
        return $sent;
    }
    
    /**
     * Construir template del email
     */
    private static function build_email_template($lead_id, $data) {
        $score = get_post_meta($lead_id, '_lead_score', true);
        $score_color = self::get_score_color($score);
        
        $budget_labels = array(
            'menos_5k' => 'Menos de 5.000€',
            '5k_15k' => '5.000€ - 15.000€',
            '15k_30k' => '15.000€ - 30.000€',
            '30k_50k' => '30.000€ - 50.000€',
            'mas_50k' => 'Más de 50.000€',
        );
        
        $urgency_labels = array(
            'inmediato' => 'Lo antes posible',
            '1_mes' => 'En el próximo mes',
            '3_meses' => 'En 1-3 meses',
            '6_meses' => 'En 3-6 meses',
            'explorando' => 'Solo explorando opciones',
        );
        
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif; background-color: #f4f4f5;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f5; padding: 40px 20px;">
                <tr>
                    <td align="center">
                        <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                            
                            <!-- Header -->
                            <tr>
                                <td style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); padding: 30px; text-align: center;">
                                    <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 600;">
                                        ✨ Nuevo Lead Recibido
                                    </h1>
                                </td>
                            </tr>
                            
                            <!-- Lead Score -->
                            <tr>
                                <td style="padding: 25px 30px; text-align: center; border-bottom: 1px solid #e5e7eb;">
                                    <div style="display: inline-block; padding: 10px 25px; background-color: <?php echo $score_color['bg']; ?>; border-radius: 50px;">
                                        <span style="font-size: 14px; color: <?php echo $score_color['text']; ?>;">
                                            Lead Score: <strong><?php echo $score; ?>/100</strong>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Datos del contacto -->
                            <tr>
                                <td style="padding: 25px 30px;">
                                    <h2 style="margin: 0 0 15px 0; font-size: 16px; color: #374151; border-bottom: 2px solid #2563eb; padding-bottom: 8px;">
                                        📋 Datos de Contacto
                                    </h2>
                                    <table width="100%" cellpadding="5" cellspacing="0">
                                        <tr>
                                            <td style="color: #6b7280; width: 120px;">Nombre:</td>
                                            <td style="color: #111827; font-weight: 500;"><?php echo esc_html($data['name']); ?></td>
                                        </tr>
                                        <tr>
                                            <td style="color: #6b7280;">Email:</td>
                                            <td>
                                                <a href="mailto:<?php echo esc_attr($data['email']); ?>" style="color: #2563eb;">
                                                    <?php echo esc_html($data['email']); ?>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php if (!empty($data['phone'])) : ?>
                                        <tr>
                                            <td style="color: #6b7280;">Teléfono:</td>
                                            <td>
                                                <a href="tel:<?php echo esc_attr($data['phone']); ?>" style="color: #2563eb;">
                                                    <?php echo esc_html($data['phone']); ?>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if (!empty($data['company'])) : ?>
                                        <tr>
                                            <td style="color: #6b7280;">Empresa:</td>
                                            <td style="color: #111827;"><?php echo esc_html($data['company']); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                </td>
                            </tr>
                            
                            <!-- Perfil del proyecto -->
                            <?php if (!empty($data['service']) || !empty($data['budget']) || !empty($data['urgency'])) : ?>
                            <tr>
                                <td style="padding: 0 30px 25px 30px;">
                                    <h2 style="margin: 0 0 15px 0; font-size: 16px; color: #374151; border-bottom: 2px solid #f59e0b; padding-bottom: 8px;">
                                        🎯 Perfil del Proyecto
                                    </h2>
                                    <table width="100%" cellpadding="5" cellspacing="0">
                                        <?php if (!empty($data['service'])) : ?>
                                        <tr>
                                            <td style="color: #6b7280; width: 120px;">Servicio:</td>
                                            <td style="color: #111827;"><?php echo esc_html($data['service']); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if (!empty($data['budget'])) : ?>
                                        <tr>
                                            <td style="color: #6b7280;">Presupuesto:</td>
                                            <td style="color: #111827; font-weight: 500;">
                                                <?php echo esc_html($budget_labels[$data['budget']] ?? $data['budget']); ?>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if (!empty($data['urgency'])) : ?>
                                        <tr>
                                            <td style="color: #6b7280;">Urgencia:</td>
                                            <td style="color: #111827;">
                                                <?php echo esc_html($urgency_labels[$data['urgency']] ?? $data['urgency']); ?>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                </td>
                            </tr>
                            <?php endif; ?>
                            
                            <!-- Mensaje -->
                            <?php if (!empty($data['message'])) : ?>
                            <tr>
                                <td style="padding: 0 30px 25px 30px;">
                                    <h2 style="margin: 0 0 15px 0; font-size: 16px; color: #374151; border-bottom: 2px solid #10b981; padding-bottom: 8px;">
                                        💬 Mensaje
                                    </h2>
                                    <div style="background-color: #f9fafb; padding: 15px; border-radius: 6px; color: #374151; line-height: 1.6;">
                                        <?php echo nl2br(esc_html($data['message'])); ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                            
                            <!-- Tracking -->
                            <tr>
                                <td style="padding: 0 30px 25px 30px;">
                                    <h2 style="margin: 0 0 15px 0; font-size: 16px; color: #374151; border-bottom: 2px solid #8b5cf6; padding-bottom: 8px;">
                                        📊 Datos de Tracking
                                    </h2>
                                    <table width="100%" cellpadding="5" cellspacing="0" style="font-size: 13px;">
                                        <tr>
                                            <td style="color: #6b7280; width: 120px;">Ubicación:</td>
                                            <td style="color: #111827;">
                                                <?php 
                                                $location = array_filter(array($data['city'] ?? '', $data['country'] ?? ''));
                                                echo esc_html(implode(', ', $location) ?: 'No disponible'); 
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="color: #6b7280;">Dispositivo:</td>
                                            <td style="color: #111827;"><?php echo esc_html($data['device_type'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <td style="color: #6b7280;">Navegador:</td>
                                            <td style="color: #111827;"><?php echo esc_html($data['browser'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <?php if (!empty($data['utm_source'])) : ?>
                                        <tr>
                                            <td style="color: #6b7280;">Fuente:</td>
                                            <td style="color: #111827;">
                                                <?php 
                                                echo esc_html($data['utm_source']);
                                                if (!empty($data['utm_medium'])) echo ' / ' . esc_html($data['utm_medium']);
                                                if (!empty($data['utm_campaign'])) echo ' / ' . esc_html($data['utm_campaign']);
                                                ?>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <td style="color: #6b7280;">Tiempo página:</td>
                                            <td style="color: #111827;">
                                                <?php 
                                                $time = intval($data['time_on_page'] ?? 0);
                                                echo $time > 0 ? gmdate('i:s', $time) . ' minutos' : 'N/A';
                                                ?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            
                            <!-- CTA -->
                            <tr>
                                <td style="padding: 20px 30px 30px 30px; text-align: center;">
                                    <a href="<?php echo admin_url('post.php?post=' . $lead_id . '&action=edit'); ?>" 
                                       style="display: inline-block; background-color: #2563eb; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: 500;">
                                        Ver Lead en WordPress
                                    </a>
                                </td>
                            </tr>
                            
                            <!-- Footer -->
                            <tr>
                                <td style="background-color: #f9fafb; padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                                    <p style="margin: 0; font-size: 13px; color: #6b7280;">
                                        Este email fue enviado por Nova Leads desde 
                                        <a href="<?php echo home_url(); ?>" style="color: #2563eb;"><?php echo get_bloginfo('name'); ?></a>
                                    </p>
                                </td>
                            </tr>
                            
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Obtener color según score
     */
    private static function get_score_color($score) {
        if ($score >= 70) {
            return array('bg' => '#dcfce7', 'text' => '#166534'); // Verde
        } elseif ($score >= 40) {
            return array('bg' => '#fef3c7', 'text' => '#92400e'); // Amarillo
        } else {
            return array('bg' => '#fee2e2', 'text' => '#991b1b'); // Rojo
        }
    }
    
    /**
     * Enviar datos a webhook
     */
    private static function send_webhook($lead_id, $data) {
        $webhook_url = get_option('nova_leads_webhook_url', '');
        
        if (empty($webhook_url)) {
            return false;
        }
        
        $payload = array(
            'event' => 'new_lead',
            'lead_id' => $lead_id,
            'timestamp' => current_time('c'),
            'data' => $data,
        );
        
        $response = wp_remote_post($webhook_url, array(
            'body' => json_encode($payload),
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'timeout' => 10,
        ));
        
        return !is_wp_error($response);
    }
}
