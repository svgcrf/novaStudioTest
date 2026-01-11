<?php
/**
 * Force correct URLs and prevent redirects
 * Must-Use Plugin - Loads before themes
 */

// Force correct URL in all contexts
add_filter('option_siteurl', function($url) {
    return 'https://expert-acorn-9wvwjq59x6w2xq96-8080.app.github.dev';
}, 1);

add_filter('option_home', function($url) {
    return 'https://expert-acorn-9wvwjq59x6w2xq96-8080.app.github.dev';
}, 1);

// Prevent WordPress from redirecting
remove_action('template_redirect', 'redirect_canonical');

// Fix redirects in wp-admin
add_filter('wp_redirect', function($location) {
    // Replace any 443 port with 8080
    $location = str_replace(':443/', ':8080/', $location);
    $location = str_replace('-443.app.github.dev', '-8080.app.github.dev', $location);
    return $location;
}, 1);

// Fix site URL output
add_filter('site_url', function($url) {
    $url = str_replace(':443/', ':8080/', $url);
    $url = str_replace('-443.app.github.dev', '-8080.app.github.dev', $url);
    return $url;
}, 1);

add_filter('home_url', function($url) {
    $url = str_replace(':443/', ':8080/', $url);
    $url = str_replace('-443.app.github.dev', '-8080.app.github.dev', $url);
    return $url;
}, 1);
