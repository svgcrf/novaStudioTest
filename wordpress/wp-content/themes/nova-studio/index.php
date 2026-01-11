<?php
/**
 * Nova Studio Theme Index
 *
 * @package Nova_Studio
 */

get_header();
?>

<main id="content" class="site-main">
    <?php
    if (have_posts()) :
        while (have_posts()) :
            the_post();
            the_content();
        endwhile;
    else :
        ?>
        <div class="nova-container">
            <p><?php esc_html_e('No content found.', 'nova-studio'); ?></p>
        </div>
        <?php
    endif;
    ?>
</main>

<?php
get_footer();
