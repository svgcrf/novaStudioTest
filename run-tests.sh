#!/bin/bash
echo "🧪 PRUEBAS FINALES - Nova Studio"
echo "=================================="
echo ""

# Test 1: Contenedores
echo "✅ TEST 1: Contenedores Docker"
docker compose ps
echo ""

# Test 2: Plugins
echo "✅ TEST 2: Estado de Plugins"
docker compose exec -T wordpress wp plugin list --allow-root
echo ""

# Test 3: Tema activo
echo "✅ TEST 3: Tema Activo"
docker compose exec -T wordpress wp theme list --status=active --allow-root
echo ""

# Test 4: Páginas
echo "✅ TEST 4: Páginas Creadas"
docker compose exec -T wordpress wp post list --post_type=page --fields=ID,post_title,post_name,post_status --allow-root
echo ""

# Test 5: Configuración de inicio
echo "✅ TEST 5: Configuración de Página de Inicio"
echo -n "Show on front: "
docker compose exec -T wordpress wp option get show_on_front --allow-root
echo -n "Page on front ID: "
docker compose exec -T wordpress wp option get page_on_front --allow-root
echo ""

# Test 6: URLs
echo "✅ TEST 6: URLs Configuradas"
echo -n "Home URL: "
docker compose exec -T wordpress wp option get home --allow-root
echo -n "Site URL: "
docker compose exec -T wordpress wp option get siteurl --allow-root
echo ""

# Test 7: Verificar archivos del tema
echo "✅ TEST 7: Templates del Tema"
ls -lh wordpress/wp-content/themes/nova-studio/page-templates/*.php 2>/dev/null | awk '{print $9, "(" $5 ")"}'
echo ""

# Test 8: Verificar plugin simple
echo "✅ TEST 8: Plugin Nova Leads Simple"
ls -lh wordpress/wp-content/plugins/nova-leads-simple.php 2>/dev/null | awk '{print $9, "(" $5 ")"}'
echo ""

# Test 9: Probar endpoints
echo "✅ TEST 9: Prueba de Conectividad"
echo -n "Homepage (200): "
curl -s -o /dev/null -w "%{http_code}" "https://${CODESPACE_NAME}-8080.app.github.dev/" 2>/dev/null || echo "ERROR"
echo ""
echo -n "Formulario (200): "
curl -s -o /dev/null -w "%{http_code}" "https://${CODESPACE_NAME}-8080.app.github.dev/solicitar-presupuesto/" 2>/dev/null || echo "ERROR"
echo ""
echo -n "Admin (200/302): "
curl -s -o /dev/null -w "%{http_code}" "https://${CODESPACE_NAME}-8080.app.github.dev/wp-admin/" 2>/dev/null || echo "ERROR"
echo ""

# Test 10: Verificar errores PHP
echo "✅ TEST 10: Últimos Errores PHP (si hay)"
docker compose exec -T wordpress tail -n 20 /var/www/html/wp-content/debug.log 2>/dev/null || echo "No hay archivo de debug o no hay errores"
echo ""

echo "=================================="
echo "🎉 PRUEBAS COMPLETADAS"
echo ""
echo "🌐 ACCEDE A:"
echo "   Homepage: https://${CODESPACE_NAME}-8080.app.github.dev/"
echo "   Formulario: https://${CODESPACE_NAME}-8080.app.github.dev/solicitar-presupuesto/"
echo "   Admin: https://${CODESPACE_NAME}-8080.app.github.dev/wp-admin/"
echo "   User: admin / Pass: admin"
echo ""
