<?php
// Diagnóstico simple
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnóstico</h1>";

// 1. Verificar conexión a BD
$db_host = getenv('WORDPRESS_DB_HOST') ?: 'db';
$db_name = getenv('WORDPRESS_DB_NAME') ?: 'wordpress';
$db_user = getenv('WORDPRESS_DB_USER') ?: 'wordpress';
$db_pass = getenv('WORDPRESS_DB_PASSWORD') ?: 'wordpress';

echo "<h2>1. Base de datos</h2>";
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    echo "<p style='color:green'>✅ Conexión OK</p>";
    
    // Ver plugins activos
    $stmt = $pdo->query("SELECT option_value FROM wp_options WHERE option_name = 'active_plugins'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $plugins = $result ? unserialize($result['option_value']) : [];
    
    echo "<h3>Plugins activos:</h3><pre>" . print_r($plugins, true) . "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
}

// 2. Verificar archivos
echo "<h2>2. Archivos de plugins</h2>";
$plugin_dir = __DIR__ . '/wp-content/plugins/';

$files_to_check = [
    'nova-leads-simple.php',
    'nova-leads/nova-leads.php',
];

foreach ($files_to_check as $file) {
    $path = $plugin_dir . $file;
    if (file_exists($path)) {
        echo "<p style='color:green'>✅ $file existe</p>";
        
        // Verificar sintaxis
        $output = [];
        exec("php -l " . escapeshellarg($path) . " 2>&1", $output, $ret);
        if ($ret === 0) {
            echo "<p style='color:green; margin-left:20px'>  Sintaxis OK</p>";
        } else {
            echo "<p style='color:red; margin-left:20px'>  Error: " . implode(' ', $output) . "</p>";
        }
    } else {
        echo "<p style='color:orange'>⚠️ $file NO existe</p>";
    }
}

// 3. Desactivar TODOS los plugins y probar
echo "<h2>3. Desactivar todos los plugins</h2>";
echo "<p><a href='?action=disable_all'>Click aquí para desactivar TODOS los plugins</a></p>";

if (isset($_GET['action']) && $_GET['action'] === 'disable_all') {
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $stmt = $pdo->prepare("UPDATE wp_options SET option_value = ? WHERE option_name = 'active_plugins'");
        $stmt->execute([serialize([])]);
        echo "<p style='color:green'>✅ Todos los plugins desactivados</p>";
        echo "<p><a href='/'>Probar sitio ahora</a></p>";
    } catch (Exception $e) {
        echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
    }
}

// 4. Activar solo el plugin simple
echo "<h2>4. Activar Nova Leads Simple</h2>";
echo "<p><a href='?action=enable_simple'>Click aquí para activar SOLO Nova Leads Simple</a></p>";

if (isset($_GET['action']) && $_GET['action'] === 'enable_simple') {
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $stmt = $pdo->prepare("UPDATE wp_options SET option_value = ? WHERE option_name = 'active_plugins'");
        $stmt->execute([serialize(['nova-leads-simple.php'])]);
        echo "<p style='color:green'>✅ Nova Leads Simple activado</p>";
        echo "<p><a href='/'>Probar sitio ahora</a></p>";
    } catch (Exception $e) {
        echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
    }
}

// 5. Ver errores de WordPress
echo "<h2>5. Intentar cargar WordPress</h2>";

if (isset($_GET['action']) && $_GET['action'] === 'test_wp') {
    try {
        define('WP_USE_THEMES', false);
        require_once __DIR__ . '/wp-load.php';
        echo "<p style='color:green'>✅ WordPress cargó correctamente</p>";
    } catch (Throwable $e) {
        echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
        echo "<p>Archivo: " . $e->getFile() . " línea " . $e->getLine() . "</p>";
    }
}

echo "<p><a href='?action=test_wp'>Probar carga de WordPress</a></p>";

echo "<hr><p><a href='/'>🏠 Ir al sitio</a></p>";
