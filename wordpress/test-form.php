<?php
/**
 * Test del formulario de leads
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('WP_USE_THEMES', false);
require_once __DIR__ . '/wp-load.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Formulario Nova Leads</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h1 { color: #1a1a2e; }
        .test-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .test-result {
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
        pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: linear-gradient(135deg, #6c63ff, #4834d4);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin: 5px;
        }
        .btn:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <h1>🧪 Test del Sistema Nova Leads</h1>
    
    <div class="test-section">
        <h2>1. Estado del Plugin</h2>
        <?php
        $active_plugins = get_option('active_plugins', array());
        $is_active = in_array('nova-leads/nova-leads.php', $active_plugins);
        
        if ($is_active) {
            echo '<div class="test-result success">✅ Plugin Nova Leads está ACTIVO</div>';
        } else {
            echo '<div class="test-result error">❌ Plugin Nova Leads está INACTIVO</div>';
            echo '<p><a href="/activate-safe.php" class="btn">Activar Plugin</a></p>';
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>2. Verificación de Clases</h2>
        <?php
        $classes = array(
            'Nova_Leads' => 'Clase principal',
            'Nova_Leads_Form' => 'Formulario frontend',
            'Nova_Leads_Ajax' => 'Manejador AJAX',
            'Nova_Leads_CPT' => 'Custom Post Type',
            'Nova_Leads_Email' => 'Notificaciones email',
            'Nova_Leads_Tracking' => 'Tracking de visitantes',
        );
        
        foreach ($classes as $class => $desc) {
            if (class_exists($class)) {
                echo "<div class='test-result success'>✅ $class - $desc</div>";
            } else {
                echo "<div class='test-result error'>❌ $class - $desc (NO CARGADA)</div>";
            }
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>3. Shortcode del Formulario</h2>
        <?php
        if (shortcode_exists('nova_lead_form')) {
            echo '<div class="test-result success">✅ Shortcode [nova_lead_form] registrado</div>';
            
            echo '<h3>Vista previa del formulario:</h3>';
            echo '<div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-top: 15px;">';
            echo do_shortcode('[nova_lead_form]');
            echo '</div>';
        } else {
            echo '<div class="test-result error">❌ Shortcode [nova_lead_form] NO registrado</div>';
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>4. Custom Post Type</h2>
        <?php
        if (post_type_exists('nova_lead')) {
            echo '<div class="test-result success">✅ CPT nova_lead registrado</div>';
            
            // Contar leads
            $count = wp_count_posts('nova_lead');
            $total = ($count->publish ?? 0) + ($count->draft ?? 0);
            echo "<div class='test-result info'>📊 Total de leads: $total</div>";
        } else {
            echo '<div class="test-result error">❌ CPT nova_lead NO registrado</div>';
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>5. AJAX Endpoints</h2>
        <?php
        global $wp_filter;
        
        $ajax_actions = array(
            'wp_ajax_nova_submit_lead' => 'Envío de formulario (admin)',
            'wp_ajax_nopriv_nova_submit_lead' => 'Envío de formulario (público)',
            'wp_ajax_nova_leads_submit' => 'Envío alternativo (admin)',
            'wp_ajax_nopriv_nova_leads_submit' => 'Envío alternativo (público)',
        );
        
        foreach ($ajax_actions as $action => $desc) {
            if (isset($wp_filter[$action])) {
                echo "<div class='test-result success'>✅ $action - $desc</div>";
            } else {
                echo "<div class='test-result error'>❌ $action - $desc (NO REGISTRADO)</div>";
            }
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>6. Test de Envío de Formulario</h2>
        <p>Para probar el envío del formulario, usa esta configuración:</p>
        
        <pre>{
    "ajaxUrl": "<?php echo admin_url('admin-ajax.php'); ?>",
    "action": "nova_leads_submit",
    "nonce": "<?php echo wp_create_nonce('nova_leads_submit'); ?>"
}</pre>
        
        <h3>Prueba rápida con curl:</h3>
        <pre>curl -X POST "<?php echo admin_url('admin-ajax.php'); ?>" \
  -d "action=nova_leads_submit" \
  -d "nonce=<?php echo wp_create_nonce('nova_leads_submit'); ?>" \
  -d "name=Test Usuario" \
  -d "email=test@ejemplo.com" \
  -d "phone=+34600123456" \
  -d "service=diseno-web" \
  -d "message=Este es un mensaje de prueba"</pre>
    </div>
    
    <div class="test-section">
        <h2>7. Formulario de Prueba Manual</h2>
        <form id="test-form" method="post" style="max-width: 500px;">
            <div style="margin-bottom: 15px;">
                <label><strong>Nombre:</strong></label><br>
                <input type="text" name="name" value="Test Usuario" style="width: 100%; padding: 10px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label><strong>Email:</strong></label><br>
                <input type="email" name="email" value="test@ejemplo.com" style="width: 100%; padding: 10px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label><strong>Teléfono:</strong></label><br>
                <input type="text" name="phone" value="+34600123456" style="width: 100%; padding: 10px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label><strong>Servicio:</strong></label><br>
                <select name="service" style="width: 100%; padding: 10px;">
                    <option value="diseno-web">Diseño Web UI/UX</option>
                    <option value="desarrollo-web">Desarrollo Web</option>
                    <option value="estrategia-digital">Estrategia Digital</option>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <label><strong>Mensaje:</strong></label><br>
                <textarea name="message" rows="4" style="width: 100%; padding: 10px;">Este es un mensaje de prueba desde el formulario de test.</textarea>
            </div>
            <button type="submit" class="btn">📤 Enviar Prueba</button>
        </form>
        
        <div id="test-result" style="margin-top: 20px;"></div>
    </div>
    
    <p style="text-align: center; margin-top: 30px;">
        <a href="/" class="btn">🏠 Ver Landing Page</a>
        <a href="/wp-admin/admin.php?page=nova-leads" class="btn">📊 Ver Dashboard</a>
        <a href="/wp-admin/edit.php?post_type=nova_lead" class="btn">📋 Ver Leads</a>
    </p>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#test-form').on('submit', function(e) {
            e.preventDefault();
            
            var formData = $(this).serialize();
            formData += '&action=nova_leads_submit';
            formData += '&nonce=<?php echo wp_create_nonce('nova_leads_submit'); ?>';
            
            $('#test-result').html('<div class="test-result info">⏳ Enviando...</div>');
            
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: formData,
                success: function(response) {
                    console.log('Respuesta:', response);
                    if (response.success) {
                        $('#test-result').html('<div class="test-result success">✅ ' + response.data.message + '</div>');
                    } else {
                        $('#test-result').html('<div class="test-result error">❌ Error: ' + (response.data || 'Error desconocido') + '</div>');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error:', xhr.responseText);
                    $('#test-result').html('<div class="test-result error">❌ Error de conexión: ' + error + '</div>');
                }
            });
        });
    });
    </script>
</body>
</html>
