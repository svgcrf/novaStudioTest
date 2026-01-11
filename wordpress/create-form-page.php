<?php
/**
 * Nova Studio - Setup Pages
 * Run this script to create required pages
 * Usage: wp eval-file create-form-page.php
 */

// Create quote page
$page_exists = get_page_by_path('solicitar-presupuesto');

if (!$page_exists) {
    $page_id = wp_insert_post(array(
        'post_title'     => 'Solicitar Presupuesto',
        'post_name'      => 'solicitar-presupuesto',
        'post_status'    => 'publish',
        'post_type'      => 'page',
        'post_content'   => '',
        'page_template'  => 'page-templates/form-premium.php',
        'comment_status' => 'closed',
        'ping_status'    => 'closed',
    ));
    
    if ($page_id && !is_wp_error($page_id)) {
        update_post_meta($page_id, '_wp_page_template', 'page-templates/form-premium.php');
        echo "✅ Página 'Solicitar Presupuesto' creada con ID: $page_id\n";
    } else {
        echo "❌ Error creando página: " . $page_id->get_error_message() . "\n";
    }
} else {
    // Update the page template
    update_post_meta($page_exists->ID, '_wp_page_template', 'page-templates/form-premium.php');
    echo "✅ Página 'Solicitar Presupuesto' ya existe con ID: " . $page_exists->ID . "\n";
    echo "   Template actualizado a: page-templates/form-premium.php\n";
}

// Create landing page if not exists
$landing_page = get_page_by_path('nova-studio');

if (!$landing_page) {
    $landing_id = wp_insert_post(array(
        'post_title'     => 'Nova Studio - Agencia Digital',
        'post_name'      => 'nova-studio',
        'post_status'    => 'publish',
        'post_type'      => 'page',
        'post_content'   => '',
        'page_template'  => 'page-templates/landing-nova-studio.php',
        'comment_status' => 'closed',
        'ping_status'    => 'closed',
    ));
    
    if ($landing_id && !is_wp_error($landing_id)) {
        update_post_meta($landing_id, '_wp_page_template', 'page-templates/landing-nova-studio.php');
        echo "✅ Página Landing 'Nova Studio' creada con ID: $landing_id\n";
        
        // Set as front page
        update_option('show_on_front', 'page');
        update_option('page_on_front', $landing_id);
        echo "   Configurada como página de inicio\n";
    }
} else {
    echo "✅ Página Landing 'Nova Studio' ya existe con ID: " . $landing_page->ID . "\n";
}

echo "\n📋 URLs disponibles:\n";
echo "   - Landing: " . home_url('/') . "\n";
echo "   - Formulario: " . home_url('/solicitar-presupuesto/') . "\n";
