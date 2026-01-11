#!/bin/bash
# Script para actualizar URLs de WordPress

echo "=== Actualizando URLs en WordPress ==="

# Actualizar en la base de datos
docker exec wordpress_db mysql -uwordpress -pwordpress wordpress -e "UPDATE wp_options SET option_value='https://expert-acorn-9wvwjq59x6w2xq96-8080.app.github.dev' WHERE option_name IN ('siteurl', 'home');"

# Verificar
echo -e "\n=== URLs actuales ==="
docker exec wordpress_db mysql -uwordpress -pwordpress wordpress -e "SELECT option_name, option_value FROM wp_options WHERE option_name IN ('siteurl', 'home');"

# Ejecutar script PHP
echo -e "\n=== Ejecutando script PHP ==="
docker exec wordpress_app php /var/www/html/fix-urls.php

# Limpiar cache
echo -e "\n=== Limpiando cache de WordPress ==="
docker exec wordpress_app rm -rf /var/www/html/wp-content/cache/*

echo -e "\n✅ Proceso completado"
