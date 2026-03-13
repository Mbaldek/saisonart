<?php
/**
 * The Template for displaying product archives.
 *
 * Override this template by copying it to saisonart-theme/woocommerce/archive-product.php
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

if (woocommerce_product_loop()) {
    do_action('woocommerce_before_shop_loop');
    woocommerce_product_loop_start();

    if (wc_get_loop_prop('total')) {
        while (have_posts()) {
            the_post();
            do_action('woocommerce_shop_loop');
            wc_get_template_part('content', 'product');
        }
    }

    woocommerce_product_loop_end();
    do_action('woocommerce_after_shop_loop');
} else {
    do_action('woocommerce_no_products_found');
}

do_action('woocommerce_after_main_content');
do_action('woocommerce_sidebar');

get_footer('shop');
