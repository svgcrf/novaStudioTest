<?php
/**
 * Activar Nova Leads de forma segura
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('WP_USE_THEMES', false);
require_once __DIR__ . '/wp-load.php';

echo "<h2>Activando Nova Leads</h2>";

// Verificar si el plugin existe
$plugin_file = WP_PLUGIN_DIR . '/nova-leads/nova-leads.php';

if (!file_exists($plugin_file)) {
    echo "<p style='color: red;'>❌ El archivo del plugin no existe</p>";
    exit;
}

echo "<p>✅ Archivo del plugin encontrado</p>";

// Verificar sintaxis de cada archivo
$files_to_check = array(
    'nova-leads.php',
    'includes/class-nova-leads-cpt.php',
    'includes/class-nova-leads-admin.php',
    'includes/class-nova-leads-dashboard.php',
    'includes/class-nova-leads-ajax.php',
    'includes/class-nova-leads-tracking.php',
    'includes/class-nova-leads-form.php',
    'includes/class-nova-leads-export.php',
    'includes/class-nova-leads-email.php',
);

echo "<h3>Verificando sintaxis de archivos:</h3>";

$base_path = WP_PLUGIN_DIR . '/nova-leads/';
$all_ok = true;

foreach ($files_to_check as $file) {
    $path = $base_path . $file;
    if (file_exists($path)) {
        $output = array();
        $return_var = 0;
        exec("php -l " . escapeshellarg($path) . " 2>&1", $output, $return_var);
        
        if ($return_var === 0) {
            echo "<p style='color: green;'>✅ $file - OK</p>";
        } else {
            echo "<p style='color: red;'>❌ $file - ERROR: " . implode('<br>', $output) . "</p>";
            $all_ok = false;
        }
    } else {
        echo "<p style='color: orange;'>⚠️ $file - No existe</p>";
    }
}

if ($all_ok) {
    // Intentar activar
    $result = activate_plugin('nova-leads/nova-leads.php');
    
    if (is_wp_error($result)) {
        echo "<p style='color: red;'>❌ Error al activar: " . $result->get_error_message() . "</p>";
    } else {
        echo "<h3 style='color: green;'>✅ Plugin activado correctamente!</h3>";
    }
} else {
    echo "<h3 style='color: red;'>No se puede activar el plugin debido a errores de sintaxis</h3>";
}

echo "<p><a href='/'>Ir al sitio</a> | <a href='/wp-admin/'>Ir al admin</a></p>";
