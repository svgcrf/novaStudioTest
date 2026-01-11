#!/bin/bash

echo "🚀 Creando página Home de Nova Studio..."
echo ""

# Crear página usando WP-CLI dentro del container
docker exec wordpress_app wp post create \
    --post_type=page \
    --post_title='Home' \
    --post_status=publish \
    --post_author=1 \
    --meta_input='{"_wp_page_template":"page-templates/landing-nova-studio.php"}' \
    --porcelain

# Obtener el ID de la página
PAGE_ID=$(docker exec wordpress_app wp post list --post_type=page --post_title='Home' --field=ID --format=csv)

echo ""
echo "✅ Página creada con ID: $PAGE_ID"
echo ""

# Configurar como página de inicio
echo "📌 Configurando como página de inicio..."
docker exec wordpress_app wp option update show_on_front 'page'
docker exec wordpress_app wp option update page_on_front "$PAGE_ID"

echo ""
echo "🎉 ¡Listo! Landing page de Nova Studio creada"
echo ""
echo "🌐 Ver en: https://expert-acorn-9wvwjq59x6w2xq96-8080.app.github.dev/"
echo ""
