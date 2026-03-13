<?php
/**
 * SaisonArt Child Theme functions.
 */

/* --------------------------------------------------------------------------
   Enqueue styles & scripts
   -------------------------------------------------------------------------- */
add_action('wp_enqueue_scripts', 'saisonart_enqueue_styles', 20);
function saisonart_enqueue_styles() {
    $version = wp_get_theme()->get('Version');

    // Google Fonts
    wp_enqueue_style('saisonart-fonts', 'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=DM+Sans:wght@300;400;500;600&display=swap', array(), null);

    // Parent theme
    wp_enqueue_style('storefront-style', get_template_directory_uri() . '/style.css');

    // Child theme
    wp_enqueue_style('saisonart-style', get_stylesheet_uri(), array('storefront-style'), $version);
    wp_enqueue_style('saisonart-main', get_stylesheet_directory_uri() . '/assets/css/main.css', array('saisonart-style'), $version);

    // Homepage-only dark styles
    if (is_front_page()) {
        wp_enqueue_style('saisonart-frontpage', get_stylesheet_directory_uri() . '/assets/css/front-page.css', array('saisonart-main'), $version);
    }

    // JS
    wp_enqueue_script('saisonart-main', get_stylesheet_directory_uri() . '/assets/js/main.js', array('jquery'), $version, true);
}

/* --------------------------------------------------------------------------
   Theme setup
   -------------------------------------------------------------------------- */
add_action('after_setup_theme', 'saisonart_setup');
function saisonart_setup() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    register_nav_menus(array(
        'sa-primary' => 'Navigation principale SaisonArt',
    ));
}

/* --------------------------------------------------------------------------
   WooCommerce: product columns & per page
   -------------------------------------------------------------------------- */
add_filter('loop_shop_columns', function () {
    return 3;
});

add_filter('loop_shop_per_page', function () {
    return 12;
});

/* --------------------------------------------------------------------------
   WooCommerce: AJAX cart fragment for custom header badge
   -------------------------------------------------------------------------- */
add_filter('woocommerce_add_to_cart_fragments', 'saisonart_cart_fragment');
function saisonart_cart_fragment($fragments) {
    $count = WC()->cart->get_cart_contents_count();
    if ($count > 0) {
        $fragments['.sa-header-cart-badge'] = '<span class="sa-header-cart-badge">' . esc_html($count) . '</span>';
    } else {
        $fragments['.sa-header-cart-badge'] = '<span class="sa-header-cart-badge" style="display:none"></span>';
    }
    return $fragments;
}

/* --------------------------------------------------------------------------
   Remove default Storefront header/footer actions we override
   -------------------------------------------------------------------------- */
add_action('init', 'saisonart_remove_storefront_actions');
function saisonart_remove_storefront_actions() {
    remove_action('storefront_header', 'storefront_product_search', 25);
}
