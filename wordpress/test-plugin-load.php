<?php
/**
 * Test de activación manual del plugin
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Headers
header('Content-Type: text/html; charset=utf-8');

echo "<h2>🔧 Test de Carga del Plugin Nova Leads</h2>";

// Definir constantes necesarias
define('ABSPATH', __DIR__ . '/');
define('WPINC', 'wp-includes');

// Simular WordPress básico
$_SERVER['HTTP_HOST'] = 'localhost';

// Cargar el plugin directamente y ver errores
$plugin_file = __DIR__ . '/wp-content/plugins/nova-leads/nova-leads.php';

echo "<h3>1. Verificando sintaxis PHP de todos los archivos:</h3>";

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
);

$base = __DIR__ . '/wp-content/plugins/nova-leads/';
$all_ok = true;

foreach ($files as $file) {
    $path = $base . $file;
    if (file_exists($path)) {
        $output = [];
        $ret = 0;
        exec("php -l " . escapeshellarg($path) . " 2>&1", $output, $ret);
        
        if ($ret === 0) {
            echo "<p style='color: green;'>✅ $file</p>";
        } else {
            echo "<p style='color: red;'>❌ $file: " . htmlspecialchars(implode(' ', $output)) . "</p>";
            $all_ok = false;
        }
    } else {
        echo "<p style='color: orange;'>⚠️ $file no existe</p>";
    }
}

if ($all_ok) {
    echo "<h3 style='color: green;'>✅ Todos los archivos tienen sintaxis válida</h3>";
    
    echo "<h3>2. Intentando cargar WordPress y activar plugin:</h3>";
    
    // Intentar cargar WordPress
    try {
        define('WP_USE_THEMES', false);
        require_once __DIR__ . '/wp-load.php';
        
        echo "<p style='color: green;'>✅ WordPress cargado correctamente</p>";
        
        // Ver estado del plugin
        $active_plugins = get_option('active_plugins', array());
        $plugin_basename = 'nova-leads/nova-leads.php';
        
        if (in_array($plugin_basename, $active_plugins)) {
            echo "<p style='color: blue;'>ℹ️ El plugin ya está activo</p>";
        } else {
            echo "<p>Intentando activar...</p>";
            
            $result = activate_plugin($plugin_basename);
            
            if (is_wp_error($result)) {
                echo "<p style='color: red;'>❌ Error: " . $result->get_error_message() . "</p>";
            } else {
                echo "<p style='color: green;'>✅ Plugin activado exitosamente!</p>";
            }
        }
        
        // Verificar que las clases existan
        echo "<h3>3. Verificando clases del plugin:</h3>";
        
        $classes = array(
            'Nova_Leads',
            'Nova_Leads_CPT',
            'Nova_Leads_Admin', 
            'Nova_Leads_Dashboard',
            'Nova_Leads_Ajax',
            'Nova_Leads_Tracking',
            'Nova_Leads_Form',
            'Nova_Leads_Export',
            'Nova_Leads_Email'
        );
        
        foreach ($classes as $class) {
            if (class_exists($class)) {
                echo "<p style='color: green;'>✅ $class</p>";
            } else {
                echo "<p style='color: red;'>❌ $class no existe</p>";
            }
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    } catch (Error $e) {
        echo "<p style='color: red;'>❌ Error fatal: " . $e->getMessage() . " en " . $e->getFile() . ":" . $e->getLine() . "</p>";
    }
}

echo "<hr>";
echo "<p><a href='/'>🏠 Ir al sitio</a> | <a href='/wp-admin/'>⚙️ Ir al admin</a></p>";
