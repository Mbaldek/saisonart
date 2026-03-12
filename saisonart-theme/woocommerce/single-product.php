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

do_action('woocommerce_before_main_content');

while (have_posts()) {
    the_post();
    wc_get_template_part('content', 'single-product');
}

do_action('woocommerce_after_main_content');
do_action('woocommerce_sidebar');

get_footer('shop');
