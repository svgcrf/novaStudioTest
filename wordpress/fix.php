<?php
// Script ultra básico - NO carga WordPress
header('Content-Type: text/html; charset=utf-8');
echo "<h1>🔧 Reparación de Emergencia</h1>";

$db_host = 'db';
$db_name = 'wordpress';
$db_user = 'wordpress';
$db_pass = 'wordpress';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color:green'>✅ Conexión a BD OK</p>";
    
    // Desactivar TODOS los plugins inmediatamente
    $stmt = $pdo->prepare("UPDATE wp_options SET option_value = ? WHERE option_name = 'active_plugins'");
    $stmt->execute(['a:0:{}']);
    
    echo "<h2 style='color:green'>✅ TODOS los plugins han sido desactivados</h2>";
    
    // Verificar
    $stmt = $pdo->query("SELECT option_value FROM wp_options WHERE option_name = 'active_plugins'");
    $result = $stmt->fetch();
    echo "<p>Plugins activos ahora: <code>" . htmlspecialchars($result['option_value']) . "</code></p>";
    
    echo "<hr>";
    echo "<h2>Prueba el sitio ahora:</h2>";
    echo "<p><a href='/' style='font-size: 20px; padding: 15px 30px; background: #0073aa; color: white; text-decoration: none; border-radius: 5px;'>🏠 Ir al Sitio</a></p>";
    echo "<p><a href='/wp-admin/' style='font-size: 20px; padding: 15px 30px; background: #23282d; color: white; text-decoration: none; border-radius: 5px;'>⚙️ Ir al Admin</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Error de BD: " . htmlspecialchars($e->getMessage()) . "</p>";
}
