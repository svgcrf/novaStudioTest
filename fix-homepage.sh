#!/bin/bash
echo "🔍 Diagnóstico de la página de inicio..."

# Ver configuración actual
echo "📋 Configuración actual:"
docker compose exec -T wordpress wp option get show_on_front --allow-root
docker compose exec -T wordpress wp option get page_on_front --allow-root

# Listar páginas
echo ""
echo "📄 Páginas existentes:"
docker compose exec -T wordpress wp post list --post_type=page --fields=ID,post_title,post_name,post_status,page_template --allow-root

# Buscar si existe la landing page
echo ""
echo "🔎 Buscando landing page..."
LANDING_ID=$(docker compose exec -T wordpress wp post list --post_type=page --name=inicio --field=ID --allow-root 2>/dev/null | tr -d '\r')

if [ -z "$LANDING_ID" ]; then
    echo "❌ No existe página de inicio. Creándola..."
    
    # Crear página de inicio con el template correcto
    LANDING_ID=$(docker compose exec -T wordpress wp post create \
        --post_type=page \
        --post_title='Inicio' \
        --post_name='inicio' \
        --post_status=publish \
        --page_template='page-templates/landing-nova-studio.php' \
        --porcelain \
        --allow-root)
    
    echo "✅ Página creada con ID: $LANDING_ID"
else
    echo "✅ Página de inicio existe con ID: $LANDING_ID"
    
    # Asegurar que tenga el template correcto
    docker compose exec -T wordpress wp post meta update $LANDING_ID _wp_page_template 'page-templates/landing-nova-studio.php' --allow-root
fi

# Configurar como página de inicio
echo ""
echo "⚙️ Configurando como página de inicio..."
docker compose exec -T wordpress wp option update show_on_front page --allow-root
docker compose exec -T wordpress wp option update page_on_front $LANDING_ID --allow-root

# Crear página de formulario si no existe
echo ""
echo "📝 Verificando página de formulario..."
FORM_ID=$(docker compose exec -T wordpress wp post list --post_type=page --name=solicitar-presupuesto --field=ID --allow-root 2>/dev/null | tr -d '\r')

if [ -z "$FORM_ID" ]; then
    echo "📝 Creando página de formulario..."
    FORM_ID=$(docker compose exec -T wordpress wp post create \
        --post_type=page \
        --post_title='Solicitar Presupuesto' \
        --post_name='solicitar-presupuesto' \
        --post_status=publish \
        --page_template='page-templates/form-premium.php' \
        --porcelain \
        --allow-root)
    echo "✅ Página de formulario creada con ID: $FORM_ID"
else
    echo "✅ Página de formulario existe con ID: $FORM_ID"
    docker compose exec -T wordpress wp post meta update $FORM_ID _wp_page_template 'page-templates/form-premium.php' --allow-root
fi

# Limpiar cache
echo ""
echo "🧹 Limpiando cache..."
docker compose exec -T wordpress wp cache flush --allow-root 2>/dev/null || true
docker compose exec -T wordpress wp rewrite flush --allow-root

echo ""
echo "✅ Configuración completada!"
echo "🏠 Página de inicio: ID $LANDING_ID"
echo "📝 Página de formulario: ID $FORM_ID"
echo ""
echo "🌐 Ver sitio: https://${CODESPACE_NAME}-8080.app.github.dev/"
