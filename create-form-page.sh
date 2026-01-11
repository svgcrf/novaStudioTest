#!/bin/bash
echo "🔧 CREANDO PÁGINA DE FORMULARIO..."

# Buscar si existe
FORM_PAGE=$(docker compose exec -T wordpress wp post list --post_type=page --name=solicitar-presupuesto --format=ids --allow-root 2>/dev/null | tr -d '\r\n ')

if [ -n "$FORM_PAGE" ]; then
    echo "📄 Página existe con ID: $FORM_PAGE"
    echo "🗑️ Eliminando para recrear..."
    docker compose exec -T wordpress wp post delete $FORM_PAGE --force --allow-root
fi

echo "📝 Creando nueva página..."
NEW_ID=$(docker compose exec -T wordpress wp post create \
    --post_type=page \
    --post_title='Solicitar Presupuesto' \
    --post_name='solicitar-presupuesto' \
    --post_status=publish \
    --comment_status=closed \
    --ping_status=closed \
    --porcelain \
    --allow-root 2>/dev/null | tr -d '\r\n ')

echo "✅ Página creada con ID: $NEW_ID"

# Asignar template
echo "🎨 Asignando template..."
docker compose exec -T wordpress wp post meta update $NEW_ID _wp_page_template 'page-templates/form-premium.php' --allow-root

# Verificar
echo ""
echo "📋 Verificación:"
docker compose exec -T wordpress wp post get $NEW_ID --field=post_status --allow-root
docker compose exec -T wordpress wp post meta get $NEW_ID _wp_page_template --allow-root

# Flush permalinks
echo ""
echo "🔄 Flush permalinks..."
docker compose exec -T wordpress wp rewrite flush --allow-root

echo ""
echo "✅ COMPLETADO"
echo "🔗 URL: https://${CODESPACE_NAME}-8080.app.github.dev/solicitar-presupuesto/"
echo ""
echo "🧪 Probando endpoint..."
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "https://${CODESPACE_NAME}-8080.app.github.dev/solicitar-presupuesto/" 2>/dev/null)
echo "HTTP Status: $HTTP_CODE"

if [ "$HTTP_CODE" = "200" ]; then
    echo "✅ Página cargando correctamente"
else
    echo "⚠️ Problema detectado. Verificando..."
    docker compose exec -T wordpress wp post list --post_type=page --fields=ID,post_title,post_name,post_status --allow-root
fi
