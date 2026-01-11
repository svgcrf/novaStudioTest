<?php
/**
 * Template Name: Nova Studio Elementor
 * Template Post Type: page
 * Description: Template editable con Elementor Free
 * 
 * @package Nova_Studio
 */

if (!defined('ABSPATH')) {
    exit;
}

// Verificar si Elementor está activo y la página fue editada con Elementor
$elementor_active = did_action('elementor/loaded');
$is_elementor_page = false;

if ($elementor_active && class_exists('\Elementor\Plugin')) {
    $is_elementor_page = \Elementor\Plugin::$instance->documents->get(get_the_ID())->is_built_with_elementor();
}

get_header();
?>

<div id="nova-elementor-page" class="nova-elementor-page">
    <?php
    if ($is_elementor_page) {
        // Si fue editada con Elementor, mostrar el contenido de Elementor
        the_content();
    } else {
        // Si no fue editada, mostrar contenido por defecto editable
        ?>
        <div class="nova-default-content">
            <div class="nova-container">
                <div class="elementor-notice">
                    <h2>¡Edita esta página con Elementor!</h2>
                    <p>Esta página está lista para ser editada con Elementor Free.</p>
                    <p>Haz clic en "Editar con Elementor" para comenzar a diseñar tu landing page.</p>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<?php
get_footer();
?>
