<?php
/**
 * SaisonArt Child Theme functions.
 */

add_action('wp_enqueue_scripts', 'saisonart_enqueue_styles');
function saisonart_enqueue_styles() {
    wp_enqueue_style('storefront-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('saisonart-style', get_stylesheet_uri(), array('storefront-style'), wp_get_theme()->get('Version'));
    wp_enqueue_style('saisonart-main', get_stylesheet_directory_uri() . '/assets/css/main.css', array('saisonart-style'), wp_get_theme()->get('Version'));
    wp_enqueue_script('saisonart-main', get_stylesheet_directory_uri() . '/assets/js/main.js', array('jquery'), wp_get_theme()->get('Version'), true);
}
