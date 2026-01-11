<?php
/**
 * Script de diagnóstico de errores
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Diagnóstico Nova Leads</h2>";

// Verificar archivos del plugin
$plugin_path = __DIR__ . '/wp-content/plugins/nova-leads/';
$files = array(
    'nova-leads.php',
    'includes/class-nova-leads-cpt.php',
    'includes/class-nova-leads-admin.php',
    'includes/class-nova-leads-dashboard.php',
    'includes/class-nova-leads-ajax.php',
    'includes/class-nova-leads-tracking.php',
    'includes/class-nova-leads-form.php',
    'includes/class-nova-leads-export.php',
    'includes/class-nova-leads-email.php',
    'includes/class-nova-leads-elementor.php',
);

echo "<h3>Verificando archivos del plugin:</h3>";
echo "<ul>";
foreach ($files as $file) {
    $full_path = $plugin_path . $file;
    $exists = file_exists($full_path);
    $color = $exists ? 'green' : 'red';
    echo "<li style='color: $color;'>$file: " . ($exists ? '✅ OK' : '❌ NO EXISTE') . "</li>";
}
echo "</ul>";

// Intentar cargar WordPress y capturar errores
echo "<h3>Intentando cargar WordPress...</h3>";

try {
    // Desactivar el plugin temporalmente para diagnóstico
    $active_plugins_file = __DIR__ . '/wp-content/active-plugins-backup.txt';
    
    // Cargar WP
    define('WP_USE_THEMES', false);
    require_once __DIR__ . '/wp-load.php';
    
    echo "<p style='color: green;'>✅ WordPress cargado correctamente</p>";
    
    // Ver plugins activos
    $active = get_option('active_plugins');
    echo "<h3>Plugins activos:</h3><pre>" . print_r($active, true) . "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p style='color: red;'>❌ Error fatal: " . $e->getMessage() . "</p>";
    echo "<p>Archivo: " . $e->getFile() . "</p>";
    echo "<p>Línea: " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
