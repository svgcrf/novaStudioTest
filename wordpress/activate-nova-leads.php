<?php
/**
 * Script para activar el plugin Nova Leads
 */

require_once dirname(__FILE__) . '/wp-load.php';

// Activar plugin
$plugin = 'nova-leads/nova-leads.php';

if (!is_plugin_active($plugin)) {
    $result = activate_plugin($plugin);
    
    if (is_wp_error($result)) {
        echo "Error al activar: " . $result->get_error_message();
    } else {
        echo "Plugin Nova Leads activado correctamente!";
    }
} else {
    echo "El plugin ya estaba activo.";
}
