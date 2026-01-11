/**
 * Script para crear automáticamente la página Home
 * Ejecutar desde: wp-admin/admin.php?page=create-landing
 */

// Verificar que se ejecuta desde WordPress
if (!defined('ABSPATH')) {
    require_once('../../../wp-load.php');
}

// Verificar permisos
if (!current_user_can('manage_options')) {
    wp_die('No tienes permisos para acceder a esta página.');
}

// Función para crear la página
function nova_create_landing_page() {
    // Verificar si ya existe una página Home
    $existing_page = get_page_by_title('Home');
    
    if ($existing_page) {
        echo '<div class="notice notice-warning"><p>Ya existe una página llamada "Home" (ID: ' . $existing_page->ID . ')</p></div>';
        return $existing_page->ID;
    }
    
    // Crear la página
    $page_data = array(
        'post_title'    => 'Home',
        'post_content'  => '',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'post_author'   => get_current_user_id(),
        'page_template' => 'page-templates/landing-nova-studio.php'
    );
    
    $page_id = wp_insert_post($page_data);
    
    if ($page_id) {
        // Establecer como página de inicio
        update_option('show_on_front', 'page');
        update_option('page_on_front', $page_id);
        
        echo '<div class="notice notice-success"><p>✅ Página Home creada exitosamente (ID: ' . $page_id . ')</p></div>';
        echo '<div class="notice notice-success"><p>✅ Configurada como página de inicio</p></div>';
        
        return $page_id;
    } else {
        echo '<div class="notice notice-error"><p>Error al crear la página</p></div>';
        return false;
    }
}

// HTML de la página de admin
?>
<!DOCTYPE html>
<html>
<head>
    <title>Crear Landing Page - Nova Studio</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            padding: 40px;
            background: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2563EB;
            margin-bottom: 20px;
        }
        .notice {
            padding: 16px;
            border-radius: 8px;
            margin: 16px 0;
        }
        .notice-success {
            background: #D1FAE5;
            color: #065F46;
            border-left: 4px solid #10B981;
        }
        .notice-warning {
            background: #FEF3C7;
            color: #92400E;
            border-left: 4px solid #F59E0B;
        }
        .notice-error {
            background: #FEE2E2;
            color: #991B1B;
            border-left: 4px solid #EF4444;
        }
        .btn {
            display: inline-block;
            padding: 12px 32px;
            background: #2563EB;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin: 20px 0;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: #1D4ED8;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37,99,235,0.3);
        }
        .info-box {
            background: #F1F5F9;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .info-box h3 {
            margin-top: 0;
            color: #334155;
        }
        .info-box ul {
            line-height: 1.8;
            color: #475569;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Crear Landing Page Nova Studio</h1>
        
        <?php
        if (isset($_GET['action']) && $_GET['action'] === 'create') {
            $page_id = nova_create_landing_page();
            
            if ($page_id) {
                $page_url = get_permalink($page_id);
                echo '<a href="' . $page_url . '" class="btn" target="_blank">Ver Landing Page →</a>';
                echo '<br><a href="' . admin_url() . '" class="btn" style="background: #64748B;">Ir al Dashboard</a>';
            }
        } else {
            ?>
            
            <div class="info-box">
                <h3>📋 Lo que se creará:</h3>
                <ul>
                    <li>✅ Página "Home" con template personalizado</li>
                    <li>✅ 7 secciones completas (Hero, Servicios, Diferencial, Proceso, Testimonio, CTA, Footer)</li>
                    <li>✅ Diseño responsive y moderno</li>
                    <li>✅ Configuración como página de inicio</li>
                    <li>✅ Estilos y animaciones incluidos</li>
                </ul>
            </div>
            
            <a href="?action=create" class="btn">Crear Landing Page Ahora</a>
            
            <div class="info-box" style="margin-top: 40px;">
                <h3>ℹ️ Información:</h3>
                <p>Esta herramienta creará automáticamente la landing page completa de Nova Studio sin necesidad de usar Elementor.</p>
                <p>El template incluye todo el contenido y estilos necesarios para una landing page profesional lista para usar.</p>
            </div>
            
            <?php
        }
        ?>
    </div>
</body>
</html>
