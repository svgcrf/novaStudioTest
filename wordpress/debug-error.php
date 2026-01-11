<?php
/**
 * Diagnóstico de error crítico
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Diagnóstico de Error</h2>";

// Test 1: Verificar sintaxis de todos los archivos del plugin
$base = __DIR__ . '/wp-content/plugins/nova-leads/';
$files = [
    'nova-leads.php',
    'includes/class-nova-leads-cpt.php',
    'includes/class-nova-leads-admin.php',
    'includes/class-nova-leads-dashboard.php',
    'includes/class-nova-leads-ajax.php',
    'includes/class-nova-leads-tracking.php',
    'includes/class-nova-leads-form.php',
    'includes/class-nova-leads-export.php',
    'includes/class-nova-leads-email.php',
];

echo "<h3>Verificando sintaxis PHP:</h3>";

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
        }
    } else {
        echo "<p style='color: orange;'>⚠️ $file - NO EXISTE</p>";
    }
}

// Test 2: Intentar cargar WordPress con output buffering
echo "<h3>Intentando cargar WordPress:</h3>";

ob_start();
try {
    define('WP_USE_THEMES', false);
    require_once __DIR__ . '/wp-load.php';
    ob_end_clean();
    echo "<p style='color: green;'>✅ WordPress cargado correctamente</p>";
} catch (Throwable $e) {
    ob_end_clean();
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Archivo: " . $e->getFile() . "</p>";
    echo "<p>Línea: " . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
