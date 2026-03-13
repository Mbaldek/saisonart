<?php
/**
 * The Template for displaying single products.
 *
 * Override this template by copying it to saisonart-theme/woocommerce/single-product.php
 *
 * @see https://woocommerce.com/document/template-structure/
 */

defined('ABSPATH') || exit;

get_header('shop');
?>
<nav class="sa-breadcrumbs" aria-label="Fil d'Ariane">
    <?php woocommerce_breadcrumb(array(
        'delimiter'   => ' <span class="sa-bc-sep">&rsaquo;</span> ',
        'wrap_before' => '<div class="sa-bc-inner">',
        'wrap_after'  => '</div>',
        'before'      => '<span>',
        'after'       => '</span>',
        'home'        => 'Accueil',
    )); ?>
</nav>
<?php
do_action('woocommerce_before_main_content');

while (have_posts()) {
    the_post();
    wc_get_template_part('content', 'single-product');
}

do_action('woocommerce_after_main_content');
do_action('woocommerce_sidebar');

get_footer('shop');
