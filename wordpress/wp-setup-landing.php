<?php
/**
 * Script de instalación de la Landing Page Nova Studio
 * Acceder desde: /wp-setup-landing.php
 */

// Cargar WordPress
require_once('wp-load.php');

// Verificar que estamos en el entorno correcto
if (!defined('ABSPATH')) {
    die('Error: No se pudo cargar WordPress');
}

// Función para crear la página
function nova_install_landing() {
    // Verificar si ya existe
    $existing = get_page_by_title('Home');
    
    if ($existing) {
        echo "⚠️ Ya existe una página 'Home' (ID: {$existing->ID})\n";
        echo "Actualizando template...\n";
        update_post_meta($existing->ID, '_wp_page_template', 'page-templates/landing-nova-studio.php');
        $page_id = $existing->ID;
    } else {
        // Crear nueva página
        $page_id = wp_insert_post([
            'post_title'    => 'Home',
            'post_type'     => 'page',
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_content'  => ''
        ]);
        
        if (!$page_id || is_wp_error($page_id)) {
            echo "❌ Error al crear la página\n";
            return false;
        }
        
        // Asignar template
        update_post_meta($page_id, '_wp_page_template', 'page-templates/landing-nova-studio.php');
        echo "✅ Página creada con ID: {$page_id}\n";
    }
    
    // Configurar como página de inicio
    update_option('show_on_front', 'page');
    update_option('page_on_front', $page_id);
    echo "✅ Configurada como página de inicio\n";
    
    // Limpiar cache
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
    
    // Obtener URL
    $url = get_permalink($page_id);
    echo "\n";
    echo "🎉 ¡Landing page instalada!\n";
    echo "🌐 URL: {$url}\n";
    echo "\n";
    
    return $page_id;
}

// Ejecutar instalación
echo "🚀 Instalando Landing Page Nova Studio...\n\n";
$result = nova_install_landing();

if ($result) {
    echo "✅ Instalación completada exitosamente\n";
    echo "\n";
    echo "📋 Archivos del tema:\n";
    echo "   - Template: page-templates/landing-nova-studio.php\n";
    echo "   - Estilos: assets/css/landing.css\n";
    echo "   - Functions: functions.php (actualizado)\n";
    echo "\n";
    echo "🎨 La landing incluye:\n";
    echo "   ✓ Hero con CTA y estadísticas\n";
    echo "   ✓ Servicios (3 cards)\n";
    echo "   ✓ Diferencial (4 features)\n";
    echo "   ✓ Proceso (4 pasos)\n";
    echo "   ✓ Testimonio con rating\n";
    echo "   ✓ CTA Final con formulario\n";
    echo "   ✓ Footer completo\n";
    echo "\n";
    echo "🔗 Visita el sitio para verlo en acción!\n";
} else {
    echo "❌ Error en la instalación\n";
}
