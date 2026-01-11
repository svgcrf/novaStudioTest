<?php
/**
 * Desactivar plugin para restaurar sitio
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conectar directamente a la base de datos
$db_host = getenv('WORDPRESS_DB_HOST') ?: 'db';
$db_name = getenv('WORDPRESS_DB_NAME') ?: 'wordpress';
$db_user = getenv('WORDPRESS_DB_USER') ?: 'wordpress';
$db_pass = getenv('WORDPRESS_DB_PASSWORD') ?: 'wordpress';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtener plugins activos
    $stmt = $pdo->query("SELECT option_value FROM wp_options WHERE option_name = 'active_plugins'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $plugins = unserialize($result['option_value']);
        echo "<h2>Plugins activos antes:</h2>";
        echo "<pre>" . print_r($plugins, true) . "</pre>";
        
        // Remover nova-leads
        $plugins = array_filter($plugins, function($p) {
            return strpos($p, 'nova-leads') === false;
        });
        $plugins = array_values($plugins);
        
        // Actualizar
        $serialized = serialize($plugins);
        $stmt = $pdo->prepare("UPDATE wp_options SET option_value = ? WHERE option_name = 'active_plugins'");
        $stmt->execute([$serialized]);
        
        echo "<h2 style='color: green;'>✅ Plugin Nova Leads desactivado</h2>";
        echo "<h2>Plugins activos ahora:</h2>";
        echo "<pre>" . print_r($plugins, true) . "</pre>";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

echo "<p><a href='/'>Ir al sitio</a></p>";
