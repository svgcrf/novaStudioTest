#!/bin/bash
# =============================================================================
# Nova Studio - Setup WordPress después de Docker
# Ejecutar después de que los contenedores estén corriendo
# =============================================================================

# Configuración - CAMBIAR ESTOS VALORES
SITE_URL="${1:-http://TU_IP_AQUI}"
ADMIN_USER="admin"
ADMIN_PASSWORD="NovaAdmin2024!"
ADMIN_EMAIL="admin@novastudio.dev"
SITE_TITLE="Nova Studio"

echo "=============================================="
echo "  Nova Studio - Configuración WordPress"
echo "=============================================="
echo "URL del sitio: $SITE_URL"
echo ""

# Esperar a que WordPress esté listo
echo "⏳ Esperando a que WordPress esté listo..."
sleep 30

# Instalar WP-CLI en el contenedor
echo "📦 Instalando WP-CLI..."
docker exec nova_wordpress bash -c '
    curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
    chmod +x wp-cli.phar
    mv wp-cli.phar /usr/local/bin/wp
'

# Verificar si WordPress ya está instalado
INSTALLED=$(docker exec nova_wordpress wp core is-installed --allow-root 2>/dev/null && echo "yes" || echo "no")

if [ "$INSTALLED" = "no" ]; then
    echo "🔧 Instalando WordPress..."
    docker exec nova_wordpress wp core install \
        --url="$SITE_URL" \
        --title="$SITE_TITLE" \
        --admin_user="$ADMIN_USER" \
        --admin_password="$ADMIN_PASSWORD" \
        --admin_email="$ADMIN_EMAIL" \
        --skip-email \
        --allow-root
else
    echo "✅ WordPress ya está instalado"
    
    # Actualizar URLs
    echo "🔧 Actualizando URLs..."
    docker exec nova_wordpress wp option update home "$SITE_URL" --allow-root
    docker exec nova_wordpress wp option update siteurl "$SITE_URL" --allow-root
fi

# Activar tema Nova Studio
echo "🎨 Activando tema Nova Studio..."
docker exec nova_wordpress wp theme activate nova-studio --allow-root 2>/dev/null || echo "Tema ya activo o no encontrado"

# Activar plugin Nova Leads
echo "🔌 Activando plugin Nova Leads..."
docker exec nova_wordpress wp plugin activate nova-leads-simple --allow-root 2>/dev/null || echo "Plugin ya activo o no encontrado"

# Configurar permalinks
echo "🔗 Configurando permalinks..."
docker exec nova_wordpress wp rewrite structure '/%postname%/' --allow-root
docker exec nova_wordpress wp rewrite flush --allow-root

# Crear página de inicio (landing)
echo "📄 Configurando página de inicio..."
LANDING_ID=$(docker exec nova_wordpress wp post list --post_type=page --name=inicio --field=ID --allow-root 2>/dev/null)

if [ -z "$LANDING_ID" ]; then
    LANDING_ID=$(docker exec nova_wordpress wp post create \
        --post_type=page \
        --post_title="Inicio" \
        --post_name="inicio" \
        --post_status=publish \
        --page_template="page-templates/landing-nova-studio.php" \
        --porcelain \
        --allow-root)
    echo "✅ Página de inicio creada (ID: $LANDING_ID)"
fi

# Crear página de formulario
echo "📄 Creando página de formulario..."
FORM_ID=$(docker exec nova_wordpress wp post list --post_type=page --name=solicitar-presupuesto --field=ID --allow-root 2>/dev/null)

if [ -z "$FORM_ID" ]; then
    FORM_ID=$(docker exec nova_wordpress wp post create \
        --post_type=page \
        --post_title="Solicitar Presupuesto" \
        --post_name="solicitar-presupuesto" \
        --post_status=publish \
        --page_template="page-templates/form-premium.php" \
        --porcelain \
        --allow-root)
    echo "✅ Página de formulario creada (ID: $FORM_ID)"
fi

# Configurar página de inicio estática
echo "🏠 Configurando página de inicio estática..."
docker exec nova_wordpress wp option update show_on_front page --allow-root
docker exec nova_wordpress wp option update page_on_front $LANDING_ID --allow-root

# Limpiar contenido demo
echo "🧹 Limpiando contenido demo..."
docker exec nova_wordpress wp post delete 1 --force --allow-root 2>/dev/null || true
docker exec nova_wordpress wp post delete 2 --force --allow-root 2>/dev/null || true

# Configurar zona horaria
echo "🕐 Configurando zona horaria..."
docker exec nova_wordpress wp option update timezone_string "Europe/Madrid" --allow-root

echo ""
echo "=============================================="
echo "  ✅ WordPress configurado correctamente!"
echo "=============================================="
echo ""
echo "🌐 URL del sitio: $SITE_URL"
echo "👤 Usuario admin: $ADMIN_USER"
echo "🔑 Contraseña: $ADMIN_PASSWORD"
echo "📧 Email: $ADMIN_EMAIL"
echo ""
echo "📄 Páginas creadas:"
echo "   - Inicio: $SITE_URL/"
echo "   - Formulario: $SITE_URL/solicitar-presupuesto/"
echo ""
echo "🔐 Admin: $SITE_URL/wp-admin/"
echo ""
