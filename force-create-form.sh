#!/bin/bash
echo "🔨 FORZANDO CREACIÓN DE PÁGINA DE FORMULARIO"
echo ""

# 1. Eliminar cualquier página existente con ese slug
echo "1. Limpiando páginas antiguas..."
EXISTING=$(docker compose exec -T wordpress wp post list --post_type=page --name=solicitar-presupuesto --format=ids --allow-root | tr -d '\r\n ')
if [ -n "$EXISTING" ]; then
    echo "   Eliminando ID(s): $EXISTING"
    for ID in $EXISTING; do
        docker compose exec -T wordpress wp post delete $ID --force --allow-root
    done
fi

# 2. Crear página nueva
echo ""
echo "2. Creando página nueva..."
NEW_ID=$(docker compose exec -T wordpress wp post create \
    --post_type=page \
    --post_title='Solicitar Presupuesto' \
    --post_name='solicitar-presupuesto' \
    --post_content='' \
    --post_status=publish \
    --comment_status=closed \
    --ping_status=closed \
    --porcelain \
    --allow-root 2>&1 | tail -1 | tr -d '\r\n ')

echo "   ✅ Página creada: ID $NEW_ID"

# 3. Asignar template
echo ""
echo "3. Asignando template..."
docker compose exec -T wordpress wp post meta update $NEW_ID _wp_page_template 'page-templates/form-premium.php' --allow-root

# Verificar
ASSIGNED=$(docker compose exec -T wordpress wp post meta get $NEW_ID _wp_page_template --allow-root | tr -d '\r\n ')
echo "   Template asignado: $ASSIGNED"

# 4. Verificar estado
echo ""
echo "4. Verificando estado..."
docker compose exec -T wordpress wp post get $NEW_ID --fields=ID,post_title,post_name,post_status --allow-root

# 5. Flush everything
echo ""
echo "5. Limpiando cache y permalinks..."
docker compose exec -T wordpress wp cache flush --allow-root 2>/dev/null || true
docker compose exec -T wordpress wp rewrite flush --hard --allow-root
docker compose exec -T wordpress wp rewrite structure '/%postname%/' --allow-root

# 6. Restart containers para asegurar
echo ""
echo "6. Reiniciando WordPress..."
docker compose restart wordpress

echo ""
echo "   ⏳ Esperando que WordPress reinicie..."
sleep 5

# 7. Test final
echo ""
echo "7. TEST FINAL..."
HTTP=$(curl -s -o /dev/null -w "%{http_code}" "https://${CODESPACE_NAME}-8080.app.github.dev/solicitar-presupuesto/" 2>/dev/null)
echo "   HTTP Status: $HTTP"

if [ "$HTTP" = "200" ]; then
    echo "   ✅ ¡FUNCIONA!"
else
    echo "   ⚠️ Todavía hay problema"
    echo ""
    echo "   Verificando headers completos..."
    curl -sI "https://${CODESPACE_NAME}-8080.app.github.dev/solicitar-presupuesto/" 2>/dev/null
fi

echo ""
echo "================================"
echo "✅ PROCESO COMPLETADO"
echo ""
echo "🔗 Prueba ahora:"
echo "   https://${CODESPACE_NAME}-8080.app.github.dev/solicitar-presupuesto/"
echo "================================"
