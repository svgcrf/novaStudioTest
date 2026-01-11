<?php
/**
 * Nova Studio Theme Functions
 *
 * @package Nova_Studio
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Force correct home and siteurl - Override database values
 */
add_filter('option_home', function() {
    return 'https://expert-acorn-9wvwjq59x6w2xq96-8080.app.github.dev';
});
add_filter('option_siteurl', function() {
    return 'https://expert-acorn-9wvwjq59x6w2xq96-8080.app.github.dev';
});

/**
 * Theme Setup
 */
function nova_studio_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    
    // Elementor support
    add_theme_support('elementor');
    add_theme_support('header-footer-elementor');
    
    // Register nav menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'nova-studio'),
        'footer'  => __('Footer Menu', 'nova-studio'),
    ));
}
add_action('after_setup_theme', 'nova_studio_setup');

/**
 * Enqueue Styles and Scripts
 */
function nova_studio_scripts() {
    // Parent theme styles
    wp_enqueue_style(
        'hello-elementor',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme('hello-elementor')->get('Version')
    );
    
    // Google Fonts - Plus Jakarta Sans & Inter
    wp_enqueue_style(
        'nova-studio-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap',
        array(),
        null
    );
    
    // Child theme styles
    wp_enqueue_style(
        'nova-studio',
        get_stylesheet_uri(),
        array('hello-elementor', 'nova-studio-fonts'),
        wp_get_theme()->get('Version')
    );
    
    // Utilities CSS (global)
    wp_enqueue_style(
        'nova-utilities',
        get_stylesheet_directory_uri() . '/assets/css/utilities.css',
        array('nova-studio'),
        wp_get_theme()->get('Version')
    );
    
    // UX Enhancements styles (global - WhatsApp, back-to-top, scroll progress)
    wp_enqueue_style(
        'nova-ux-enhancements',
        get_stylesheet_directory_uri() . '/assets/css/ux-enhancements.css',
        array('nova-utilities'),
        wp_get_theme()->get('Version')
    );
    
    // Landing page styles
    if (is_page_template('page-templates/landing-nova-studio.php') || is_front_page()) {
        wp_enqueue_style(
            'nova-landing',
            get_stylesheet_directory_uri() . '/assets/css/landing.css',
            array('nova-studio'),
            wp_get_theme()->get('Version')
        );
        
        // UX Enhancements JS
        wp_enqueue_script(
            'nova-ux-enhancements',
            get_stylesheet_directory_uri() . '/assets/js/ux-enhancements.js',
            array(),
            wp_get_theme()->get('Version'),
            true
        );
    }
    
    // Custom JS
    wp_enqueue_script(
        'nova-studio-js',
        get_stylesheet_directory_uri() . '/assets/js/nova-studio.js',
        array(),
        wp_get_theme()->get('Version'),
        true
    );
}
add_action('wp_enqueue_scripts', 'nova_studio_scripts');

/**
 * Elementor Custom CSS Variables
 */
function nova_studio_elementor_css() {
    if (!class_exists('\Elementor\Plugin')) {
        return;
    }
    
    $css = '
    /* Elementor overrides for Nova Studio */
    .elementor-kit-* {
        --e-global-color-primary: #2563EB;
        --e-global-color-secondary: #0F172A;
        --e-global-color-text: #334155;
        --e-global-color-accent: #F59E0B;
    }
    
    .elementor-section.elementor-section-boxed > .elementor-container {
        max-width: 1200px;
    }
    ';
    
    wp_add_inline_style('nova-studio', $css);
}
add_action('wp_enqueue_scripts', 'nova_studio_elementor_css', 20);

/**
 * Disable Elementor Default Colors & Fonts (use theme's)
 */
add_action('elementor/editor/after_enqueue_scripts', function() {
    wp_add_inline_style('elementor-editor', '
        .elementor-control-default_generic_fonts .elementor-control-field {
            display: none;
        }
    ');
});

/**
 * Register Elementor Custom Widgets Location
 */
function nova_studio_register_elementor_locations($elementor_theme_manager) {
    $elementor_theme_manager->register_all_core_location();
}
add_action('elementor/theme/register_locations', 'nova_studio_register_elementor_locations');

/**
 * Add Custom Body Classes
 */
function nova_studio_body_classes($classes) {
    $classes[] = 'nova-studio';
    
    if (is_front_page()) {
        $classes[] = 'nova-landing';
    }
    
    return $classes;
}
add_filter('body_class', 'nova_studio_body_classes');

/**
 * Remove WordPress emoji scripts (performance)
 */
function nova_studio_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'nova_studio_disable_emojis');

/**
 * Remove unnecessary meta tags (performance)
 */
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_shortlink_wp_head');

/**
 * Preconnect to Google Fonts
 */
function nova_studio_resource_hints($urls, $relation_type) {
    if ($relation_type === 'preconnect') {
        $urls[] = array(
            'href' => 'https://fonts.googleapis.com',
            'crossorigin' => true,
        );
        $urls[] = array(
            'href' => 'https://fonts.gstatic.com',
            'crossorigin' => true,
        );
    }
    return $urls;
}
add_filter('wp_resource_hints', 'nova_studio_resource_hints', 10, 2);

/**
 * Custom Excerpt Length
 */
function nova_studio_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'nova_studio_excerpt_length');

/**
 * SVG Support
 */
function nova_studio_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'nova_studio_mime_types');

/**
 * Add Custom Favicon
 */
function nova_studio_favicon() {
    $favicon_url = get_stylesheet_directory_uri() . '/assets/images/favicon.svg';
    echo '<link rel="icon" type="image/svg+xml" href="' . esc_url($favicon_url) . '">' . "\n";
    echo '<link rel="apple-touch-icon" href="' . esc_url($favicon_url) . '">' . "\n";
}
add_action('wp_head', 'nova_studio_favicon', 1);
add_action('admin_head', 'nova_studio_favicon', 1);

/**
 * Elementor Full Support - Enable for all post types
 */
function nova_studio_elementor_support() {
    // Habilitar Elementor para páginas
    add_post_type_support('page', 'elementor');
    
    // Forzar que Elementor funcione con nuestro tema
    if (did_action('elementor/loaded')) {
        // Registrar ubicaciones de theme builder
        add_action('elementor/theme/register_locations', function($elementor_theme_manager) {
            $elementor_theme_manager->register_location('header');
            $elementor_theme_manager->register_location('footer');
            $elementor_theme_manager->register_location('single');
            $elementor_theme_manager->register_location('archive');
        });
    }
}
add_action('init', 'nova_studio_elementor_support');

/**
 * Elementor Canvas Support - Remove theme header/footer for canvas
 */
function nova_studio_elementor_canvas_support() {
    if (!class_exists('\Elementor\Plugin')) {
        return;
    }
    
    // Verificar si estamos en modo canvas de Elementor
    if (isset($_GET['elementor-preview']) || 
        (is_singular() && \Elementor\Plugin::$instance->preview->is_preview_mode())) {
        // En modo preview de Elementor, no interferir
        return;
    }
}
add_action('template_redirect', 'nova_studio_elementor_canvas_support');

/**
 * Enable Elementor for all page templates
 */
function nova_studio_elementor_page_templates($templates) {
    // Añadir nuestros templates a la lista de Elementor
    $templates['page-templates/elementor-canvas.php'] = __('Nova Studio Elementor', 'nova-studio');
    $templates['page-templates/landing-nova-studio.php'] = __('Nova Studio Landing', 'nova-studio');
    return $templates;
}
add_filter('theme_page_templates', 'nova_studio_elementor_page_templates');

/**
 * Get theme logo URL
 */
function nova_studio_get_logo_url() {
    return get_stylesheet_directory_uri() . '/assets/images/logo-nova-studio.svg';
}

/**
 * Output logo in header
 */
function nova_studio_logo($echo = true) {
    $logo_url = nova_studio_get_logo_url();
    $output = '<a href="' . esc_url(home_url('/')) . '" class="nova-logo-link">';
    $output .= '<img src="' . esc_url($logo_url) . '" alt="Nova Studio" class="nova-logo" width="200" height="60">';
    $output .= '</a>';
    
    if ($echo) {
        echo $output;
    }
    return $output;
}

/**
 * Create required pages on theme activation
 */
function nova_studio_create_pages() {
    // Página de solicitud de presupuesto
    $quote_page = get_page_by_path('solicitar-presupuesto');
    if (!$quote_page) {
        $page_id = wp_insert_post(array(
            'post_title'     => 'Solicitar Presupuesto',
            'post_name'      => 'solicitar-presupuesto',
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'post_content'   => '',
            'page_template'  => 'page-templates/form-premium.php',
            'comment_status' => 'closed',
        ));
        
        if ($page_id && !is_wp_error($page_id)) {
            update_post_meta($page_id, '_wp_page_template', 'page-templates/form-premium.php');
        }
    } else {
        // Asegurar que tiene el template correcto
        update_post_meta($quote_page->ID, '_wp_page_template', 'page-templates/form-premium.php');
    }
}
add_action('after_switch_theme', 'nova_studio_create_pages');

// También crear las páginas al inicializar si no existen
function nova_studio_ensure_pages() {
    if (!get_page_by_path('solicitar-presupuesto')) {
        nova_studio_create_pages();
    }
}
add_action('init', 'nova_studio_ensure_pages', 20);

/**
 * Enqueue form premium styles and scripts
 */
function nova_studio_form_premium_scripts() {
    if (is_page_template('page-templates/form-premium.php')) {
        // Additional form premium styles if needed
        wp_enqueue_style(
            'nova-form-premium',
            get_stylesheet_directory_uri() . '/assets/css/form-premium.css',
            array('nova-studio'),
            wp_get_theme()->get('Version')
        );
    }
}
add_action('wp_enqueue_scripts', 'nova_studio_form_premium_scripts');
