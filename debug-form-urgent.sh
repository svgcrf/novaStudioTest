#!/bin/bash
echo "🚨 DIAGNÓSTICO URGENTE - FORMULARIO NO CARGA"
echo ""

# Ver si la página existe
echo "1. ¿Existe la página?"
FORM_EXISTS=$(docker compose exec -T wordpress wp post list --post_type=page --name=solicitar-presupuesto --format=count --allow-root 2>/dev/null)
echo "   Páginas con slug 'solicitar-presupuesto': $FORM_EXISTS"

if [ "$FORM_EXISTS" = "0" ]; then
    echo "   ❌ LA PÁGINA NO EXISTE - CREÁNDOLA AHORA..."
    
    NEW_ID=$(docker compose exec -T wordpress wp post create \
        --post_type=page \
        --post_title='Solicitar Presupuesto' \
        --post_name='solicitar-presupuesto' \
        --post_status=publish \
        --comment_status=closed \
        --porcelain \
        --allow-root)
    
    echo "   ✅ Página creada con ID: $NEW_ID"
    
    # Asignar template
    docker compose exec -T wordpress wp post meta update $NEW_ID _wp_page_template 'page-templates/form-premium.php' --allow-root
    echo "   ✅ Template asignado"
    
    FORM_ID=$NEW_ID
else
    FORM_ID=$(docker compose exec -T wordpress wp post list --post_type=page --name=solicitar-presupuesto --field=ID --allow-root | tr -d '\r\n ')
    echo "   ✅ Página existe con ID: $FORM_ID"
fi

echo ""
echo "2. Detalles de la página:"
docker compose exec -T wordpress wp post get $FORM_ID --allow-root | grep -E "(ID|post_status|post_name|post_title)"

echo ""
echo "3. Template asignado:"
TEMPLATE=$(docker compose exec -T wordpress wp post meta get $FORM_ID _wp_page_template --allow-root | tr -d '\r\n ')
echo "   Template: $TEMPLATE"

echo ""
echo "4. ¿Existe el archivo del template?"
if [ -f "wordpress/wp-content/themes/nova-studio/page-templates/form-premium.php" ]; then
    echo "   ✅ SÍ existe"
    SIZE=$(ls -lh wordpress/wp-content/themes/nova-studio/page-templates/form-premium.php | awk '{print $5}')
    echo "   Tamaño: $SIZE"
else
    echo "   ❌ NO existe el archivo"
fi

echo ""
echo "5. ¿Hay errores PHP en el template?"
docker compose exec -T wordpress php -l /var/www/html/wp-content/themes/nova-studio/page-templates/form-premium.php

echo ""
echo "6. Configuración de páginas:"
echo -n "   Show on front: "
docker compose exec -T wordpress wp option get show_on_front --allow-root
echo -n "   Page on front: "
docker compose exec -T wordpress wp option get page_on_front --allow-root

echo ""
echo "7. Flushing permalinks..."
docker compose exec -T wordpress wp rewrite flush --allow-root
echo "   ✅ Done"

echo ""
echo "8. Probando URL..."
sleep 2
HTTP=$(curl -sL -w "%{http_code}" -o /tmp/form-test.html "https://${CODESPACE_NAME}-8080.app.github.dev/solicitar-presupuesto/" 2>/dev/null)
echo "   HTTP Status: $HTTP"

if [ "$HTTP" = "200" ]; then
    echo "   ✅ Página responde"
    # Ver si tiene contenido del formulario
    if grep -q "Cuéntanos sobre tu proyecto" /tmp/form-test.html 2>/dev/null; then
        echo "   ✅ Contenido del formulario encontrado"
    else
        echo "   ⚠️ No se encuentra el contenido esperado"
        echo "   Primeras líneas del HTML:"
        head -20 /tmp/form-test.html
    fi
else
    echo "   ❌ Error $HTTP"
    echo "   Contenido recibido:"
    head -30 /tmp/form-test.html
fi

echo ""
echo "9. Verificar redirecciones:"
curl -sI "https://${CODESPACE_NAME}-8080.app.github.dev/solicitar-presupuesto/" 2>/dev/null | grep -E "(HTTP|Location|Status)"

echo ""
echo "================================"
echo "SOLUCIÓN:"
echo "Si sigue redirigiendo, ejecuta:"
echo "  bash force-create-form.sh"
echo "================================"
