#!/bin/bash
# Simple script to create home page using docker exec

echo "🚀 Creando página Home..."

docker exec wordpress_app bash -c "cd /var/www/html && php -r \"
require_once 'wp-load.php';

// Crear página
\\\$page_id = wp_insert_post([
    'post_title' => 'Home',
    'post_type' => 'page',
    'post_status' => 'publish',
    'post_author' => 1
]);

if (\\\$page_id) {
    update_post_meta(\\\$page_id, '_wp_page_template', 'page-templates/landing-nova-studio.php');
    update_option('show_on_front', 'page');
    update_option('page_on_front', \\\$page_id);
    echo '✅ Página creada con ID: ' . \\\$page_id . PHP_EOL;
    echo '🌐 URL: ' . get_permalink(\\\$page_id) . PHP_EOL;
} else {
    echo '❌ Error al crear página' . PHP_EOL;
}
\""

echo ""
echo "✅ ¡Listo!"
