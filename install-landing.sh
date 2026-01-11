#!/bin/bash

echo "🚀 Instalando Landing Page Nova Studio..."
echo ""

# Ejecutar el script PHP desde el contenedor
docker exec wordpress_app php /var/www/html/wp-setup-landing.php

echo ""
echo "✅ ¡Proceso completado!"
echo ""
echo "🌐 Accede a tu sitio: https://expert-acorn-9wvwjq59x6w2xq96-8080.app.github.dev/"
echo ""
