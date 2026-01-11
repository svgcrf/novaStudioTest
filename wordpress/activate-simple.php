<?php
/**
 * Activar el plugin simple de Nova Leads
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    
    $plugins = $result ? unserialize($result['option_value']) : array();
    
    // Remover nova-leads complejo si existe
    $plugins = array_filter($plugins, function($p) {
        return strpos($p, 'nova-leads/') === false;
    });
    
    // Añadir nova-leads-simple
    if (!in_array('nova-leads-simple.php', $plugins)) {
        $plugins[] = 'nova-leads-simple.php';
    }
    
    $plugins = array_values($plugins);
    
    // Actualizar
    $serialized = serialize($plugins);
    $stmt = $pdo->prepare("UPDATE wp_options SET option_value = ? WHERE option_name = 'active_plugins'");
    $stmt->execute([$serialized]);
    
    echo "<h2 style='color: green;'>✅ Plugin Nova Leads Simple activado</h2>";
    echo "<h3>Plugins activos:</h3>";
    echo "<pre>" . print_r($plugins, true) . "</pre>";
    
    echo "<p><a href='/'>🏠 Ver Sitio</a> | <a href='/wp-admin/admin.php?page=nova-leads'>📊 Dashboard de Leads</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
