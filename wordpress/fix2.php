<?php
// Script que usa mysqli en lugar de PDO
header('Content-Type: text/html; charset=utf-8');
echo "<h1>🔧 Reparación de Emergencia</h1>";

// Intentar con mysqli
$db_host = 'db';
$db_name = 'wordpress';
$db_user = 'wordpress';
$db_pass = 'wordpress';

// Verificar extensiones disponibles
echo "<h3>Extensiones PHP disponibles:</h3>";
echo "<p>PDO: " . (extension_loaded('pdo') ? '✅' : '❌') . "</p>";
echo "<p>PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✅' : '❌') . "</p>";
echo "<p>MySQLi: " . (extension_loaded('mysqli') ? '✅' : '❌') . "</p>";

if (extension_loaded('mysqli')) {
    echo "<h3>Intentando con MySQLi...</h3>";
    
    $conn = @new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    if ($conn->connect_error) {
        echo "<p style='color:red'>❌ Error de conexión: " . $conn->connect_error . "</p>";
    } else {
        echo "<p style='color:green'>✅ Conexión exitosa con MySQLi</p>";
        
        // Desactivar plugins
        $sql = "UPDATE wp_options SET option_value = 'a:0:{}' WHERE option_name = 'active_plugins'";
        if ($conn->query($sql)) {
            echo "<h2 style='color:green'>✅ Todos los plugins desactivados</h2>";
        } else {
            echo "<p style='color:red'>Error: " . $conn->error . "</p>";
        }
        
        $conn->close();
    }
} else {
    echo "<p style='color:red'>❌ MySQLi no disponible</p>";
    
    // Última opción: crear archivo mu-plugin para desactivar
    echo "<h3>Creando mu-plugin de emergencia...</h3>";
    
    $mu_dir = __DIR__ . '/wp-content/mu-plugins';
    if (!is_dir($mu_dir)) {
        mkdir($mu_dir, 0755, true);
    }
    
    $mu_content = '<?php
// Desactivar todos los plugins de emergencia
add_filter("option_active_plugins", function() { return array(); });
';
    
    if (file_put_contents($mu_dir . '/emergency-disable.php', $mu_content)) {
        echo "<p style='color:green'>✅ MU-Plugin creado para desactivar plugins</p>";
    }
}

echo "<hr>";
echo "<p><a href='/'>🏠 Probar el Sitio</a></p>";
echo "<p><a href='/wp-admin/'>⚙️ Ir al Admin</a></p>";
