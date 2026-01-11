#!/bin/bash
echo "🚀 Configurando Nova Studio WordPress..."

# Instalar WP-CLI
echo "📦 Instalando WP-CLI..."
docker compose exec -T wordpress bash -c "curl -sO https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && chmod +x wp-cli.phar && mv wp-cli.phar /usr/local/bin/wp"

# Esperar a que WordPress esté listo
echo "⏳ Esperando a WordPress..."
sleep 3

# Obtener URL del Codespace
CODESPACE_URL="https://${CODESPACE_NAME}-8080.app.github.dev"
echo "🌐 URL detectada: $CODESPACE_URL"

# Actualizar URLs
echo "🔧 Configurando URLs..."
docker compose exec -T wordpress wp option update home "$CODESPACE_URL" --allow-root
docker compose exec -T wordpress wp option update siteurl "$CODESPACE_URL" --allow-root

# Activar tema
echo "🎨 Activando tema Nova Studio..."
docker compose exec -T wordpress wp theme activate nova-studio --allow-root

# Activar plugins
echo "🔌 Activando plugins..."
docker compose exec -T wordpress wp plugin activate nova-leads-simple --allow-root 2>/dev/null || echo "Plugin ya activo o no encontrado"
docker compose exec -T wordpress wp plugin activate elementor --allow-root 2>/dev/null || echo "Elementor ya activo o no encontrado"

# Crear página de inicio si no existe
echo "📄 Verificando página de inicio..."
docker compose exec -T wordpress wp post list --post_type=page --allow-root

# Limpiar cache
echo "🧹 Limpiando cache..."
docker compose exec -T wordpress wp cache flush --allow-root 2>/dev/null || true

# Mostrar estado
echo ""
echo "✅ Configuración completada!"
echo "🌐 Sitio: $CODESPACE_URL"
echo "🔐 Admin: $CODESPACE_URL/wp-admin/"
echo ""
echo "📋 Estado de plugins:"
docker compose exec -T wordpress wp plugin list --allow-root

echo ""
echo "🎨 Tema activo:"
docker compose exec -T wordpress wp theme list --status=active --allow-root
