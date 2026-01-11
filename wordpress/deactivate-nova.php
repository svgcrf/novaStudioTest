<?php
/**
 * Desactivar Nova Leads para diagnóstico
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/wp-config.php';

global $wpdb;

// Obtener plugins activos
$active_plugins = $wpdb->get_var("SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = 'active_plugins'");
$plugins = unserialize($active_plugins);

echo "<h2>Plugins activos:</h2>";
echo "<pre>" . print_r($plugins, true) . "</pre>";

// Remover nova-leads
$key = array_search('nova-leads/nova-leads.php', $plugins);
if ($key !== false) {
    unset($plugins[$key]);
    $plugins = array_values($plugins);
    
    $new_value = serialize($plugins);
    $wpdb->update(
        $wpdb->prefix . 'options',
        array('option_value' => $new_value),
        array('option_name' => 'active_plugins')
    );
    
    echo "<p style='color: green;'>✅ Plugin Nova Leads desactivado</p>";
} else {
    echo "<p>Plugin no estaba activo</p>";
}

echo "<p><a href='/'>Ir al sitio</a></p>";
