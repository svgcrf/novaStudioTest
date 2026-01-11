#!/bin/bash
echo "🔍 DIAGNÓSTICO COMPLETO DEL FORMULARIO"
echo "======================================"
echo ""

# 1. Verificar template existe
echo "📁 Template form-premium.php:"
if [ -f "wordpress/wp-content/themes/nova-studio/page-templates/form-premium.php" ]; then
    echo "✅ Existe"
    ls -lh wordpress/wp-content/themes/nova-studio/page-templates/form-premium.php | awk '{print "   Tamaño: " $5}'
else
    echo "❌ NO EXISTE"
fi
echo ""

# 2. Verificar tema activo
echo "🎨 Tema activo:"
docker compose exec -T wordpress wp theme list --status=active --field=name --allow-root
echo ""

# 3. Listar todas las páginas
echo "📄 Páginas en WordPress:"
docker compose exec -T wordpress wp post list --post_type=page --format=table --allow-root
echo ""

# 4. Buscar página específica
echo "🔎 Buscando página 'solicitar-presupuesto':"
FORM_ID=$(docker compose exec -T wordpress wp post list --post_type=page --name=solicitar-presupuesto --field=ID --allow-root 2>/dev/null | tr -d '\r\n ')
if [ -n "$FORM_ID" ]; then
    echo "✅ Encontrada con ID: $FORM_ID"
    echo ""
    echo "📋 Detalles de la página:"
    docker compose exec -T wordpress wp post get $FORM_ID --allow-root
    echo ""
    echo "🎨 Template asignado:"
    docker compose exec -T wordpress wp post meta get $FORM_ID _wp_page_template --allow-root
else
    echo "❌ NO ENCONTRADA - Necesita crearse"
fi
echo ""

# 5. Configuración de permalinks
echo "🔗 Estructura de permalinks:"
docker compose exec -T wordpress wp option get permalink_structure --allow-root
echo ""

# 6. Probar URL directamente
echo "🌐 Probando URL:"
URL="https://${CODESPACE_NAME}-8080.app.github.dev/solicitar-presupuesto/"
echo "URL: $URL"
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$URL" 2>/dev/null)
echo "HTTP Status: $HTTP_CODE"

if [ "$HTTP_CODE" = "200" ]; then
    echo "✅ Página responde correctamente"
elif [ "$HTTP_CODE" = "404" ]; then
    echo "❌ Página no encontrada (404)"
elif [ "$HTTP_CODE" = "301" ] || [ "$HTTP_CODE" = "302" ]; then
    echo "⚠️ Redirección detectada"
else
    echo "⚠️ Respuesta inesperada"
fi
echo ""

# 7. Verificar logs de error
echo "📝 Últimos errores de WordPress (si hay):"
docker compose exec -T wordpress tail -n 10 /var/www/html/wp-content/debug.log 2>/dev/null || echo "   No hay errores registrados"
echo ""

echo "======================================"
echo "FIN DEL DIAGNÓSTICO"
