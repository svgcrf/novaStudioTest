#!/bin/bash
echo "🔧 Reparación completa del sitio Nova Studio..."

# 1. Activar todos los plugins necesarios
echo "🔌 Activando plugins..."
docker compose exec -T wordpress wp plugin activate nova-leads-simple --allow-root 2>&1 | grep -v "Warning"
docker compose exec -T wordpress wp plugin activate elementor --allow-root 2>&1 | grep -v "Warning"

# 2. Verificar estado de plugins
echo ""
echo "📋 Estado de plugins:"
docker compose exec -T wordpress wp plugin list --allow-root

# 3. Verificar/crear página de formulario
echo ""
echo "📝 Configurando página de formulario..."
FORM_ID=$(docker compose exec -T wordpress wp post list --post_type=page --name=solicitar-presupuesto --field=ID --allow-root 2>/dev/null | tr -d '\r\n ')

if [ -z "$FORM_ID" ] || [ "$FORM_ID" = "0" ]; then
    echo "📝 Creando página de formulario..."
    FORM_ID=$(docker compose exec -T wordpress wp post create \
        --post_type=page \
        --post_title='Solicitar Presupuesto' \
        --post_name='solicitar-presupuesto' \
        --post_status=publish \
        --comment_status=closed \
        --porcelain \
        --allow-root 2>/dev/null | tr -d '\r\n ')
    
    echo "✅ Página creada con ID: $FORM_ID"
    
    # Asignar template
    docker compose exec -T wordpress wp post meta update $FORM_ID _wp_page_template 'page-templates/form-premium.php' --allow-root
else
    echo "✅ Página existe con ID: $FORM_ID"
    # Asegurar template correcto
    docker compose exec -T wordpress wp post meta update $FORM_ID _wp_page_template 'page-templates/form-premium.php' --allow-root
    
    # Republicar
    docker compose exec -T wordpress wp post update $FORM_ID --post_status=publish --allow-root
fi

# 4. Verificar página de inicio
echo ""
echo "🏠 Configurando página de inicio..."
HOME_ID=$(docker compose exec -T wordpress wp post list --post_type=page --name=inicio --field=ID --allow-root 2>/dev/null | tr -d '\r\n ')

if [ -z "$HOME_ID" ] || [ "$HOME_ID" = "0" ]; then
    echo "🏠 Creando página de inicio..."
    HOME_ID=$(docker compose exec -T wordpress wp post create \
        --post_type=page \
        --post_title='Inicio' \
        --post_name='inicio' \
        --post_status=publish \
        --comment_status=closed \
        --porcelain \
        --allow-root 2>/dev/null | tr -d '\r\n ')
    
    docker compose exec -T wordpress wp post meta update $HOME_ID _wp_page_template 'page-templates/landing-nova-studio.php' --allow-root
else
    echo "✅ Página de inicio existe con ID: $HOME_ID"
    docker compose exec -T wordpress wp post meta update $HOME_ID _wp_page_template 'page-templates/landing-nova-studio.php' --allow-root
fi

# Configurar como página de inicio
docker compose exec -T wordpress wp option update show_on_front page --allow-root
docker compose exec -T wordpress wp option update page_on_front $HOME_ID --allow-root

# 5. Flush permalinks y cache
echo ""
echo "🧹 Limpiando cache y permalinks..."
docker compose exec -T wordpress wp rewrite flush --allow-root
docker compose exec -T wordpress wp cache flush --allow-root 2>/dev/null || true

# 6. Verificar URLs
echo ""
echo "🌐 URLs configuradas:"
docker compose exec -T wordpress wp option get home --allow-root
docker compose exec -T wordpress wp option get siteurl --allow-root

echo ""
echo "✅ Reparación completada!"
echo ""
echo "📄 Páginas creadas:"
docker compose exec -T wordpress wp post list --post_type=page --fields=ID,post_title,post_name,page_template --allow-root
echo ""
echo "🔗 Enlaces:"
echo "   🏠 Inicio: https://${CODESPACE_NAME}-8080.app.github.dev/"
echo "   📝 Formulario: https://${CODESPACE_NAME}-8080.app.github.dev/solicitar-presupuesto/"
echo "   🔐 Admin: https://${CODESPACE_NAME}-8080.app.github.dev/wp-admin/"
