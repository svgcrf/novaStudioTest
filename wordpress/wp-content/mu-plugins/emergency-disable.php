<?php
/**
 * Plugin Name: Nova Leads Activator
 * Description: Activa solo el plugin Nova Leads Simple
 */

// Activar SOLO nova-leads-simple.php
add_filter('option_active_plugins', function($plugins) {
    return array('nova-leads-simple.php');
});
