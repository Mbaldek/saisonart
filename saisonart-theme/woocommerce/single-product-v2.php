<?php
/**
 * SaisonArt — Single Product page V2.
 * Uses WooCommerce standard hooks for maximum plugin compatibility.
 * Activated via ?v2 URL parameter.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @version 7.9.0
 */
defined('ABSPATH') || exit;

get_header('shop');

do_action('woocommerce_before_main_content');

while (have_posts()) :
    the_post();
    wc_get_template_part('content', 'single-product');
endwhile;

do_action('woocommerce_after_main_content');

get_footer('shop');
