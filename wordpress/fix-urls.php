<?php
// Script temporal para actualizar URLs en WordPress
require_once('/var/www/html/wp-load.php');

$new_url = 'https://expert-acorn-9wvwjq59x6w2xq96-8080.app.github.dev';

// Actualizar siteurl y home
update_option('siteurl', $new_url);
update_option('home', $new_url);

// Verificar
echo "siteurl: " . get_option('siteurl') . "\n";
echo "home: " . get_option('home') . "\n";
echo "URLs actualizadas correctamente\n";
?>