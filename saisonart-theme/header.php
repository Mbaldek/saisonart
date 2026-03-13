<?php
/**
 * SaisonArt — Custom header (overrides Storefront).
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="hfeed site">

<!-- ═══════ HEADER ═══════ -->
<header class="sa-header <?php echo is_front_page() ? 'sa-header--dark' : 'sa-header--light'; ?>" id="masthead">
  <a href="<?php echo esc_url(home_url('/')); ?>" class="sa-header-logo">
    <?php
    $logo_id = get_theme_mod('custom_logo');
    if ($logo_id) {
        echo wp_get_attachment_image($logo_id, 'full', false, array('alt' => get_bloginfo('name')));
    } else {
    ?>
      <img src="https://i0.wp.com/saisonart.com/wp-content/uploads/2025/08/cropped-logo_siteweb-1.jpg?fit=1241%2C124&ssl=1"
           alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
    <?php } ?>
  </a>

  <nav class="sa-nav">
    <?php
    $menu_items = array(
        '/boutique/' => 'Boutique',
        '/news/'     => 'Magazine',
        '/contact-us/' => 'Contact',
    );
    foreach ($menu_items as $path => $label) {
        $url    = home_url($path);
        $active = (is_page(trim($path, '/')) || (strpos($_SERVER['REQUEST_URI'], $path) === 0)) ? ' class="active"' : '';
        echo '<a href="' . esc_url($url) . '"' . $active . '>' . esc_html($label) . '</a>';
    }
    ?>
  </nav>

  <div class="sa-header-right">
    <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="sa-header-cart" aria-label="Panier">
      <svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
      <?php
      $count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
      if ($count > 0) {
          echo '<span class="sa-header-cart-badge">' . esc_html($count) . '</span>';
      } else {
          echo '<span class="sa-header-cart-badge" style="display:none"></span>';
      }
      ?>
    </a>

    <!-- Burger mobile -->
    <button class="sa-header-burger" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<!-- Mobile menu -->
<div class="sa-header-mobile-menu">
  <a href="<?php echo esc_url(home_url('/boutique/')); ?>">Boutique</a>
  <a href="<?php echo esc_url(home_url('/news/')); ?>">Magazine</a>
  <a href="<?php echo esc_url(home_url('/contact-us/')); ?>">Contact</a>
  <a href="<?php echo esc_url(wc_get_cart_url()); ?>">Panier</a>
</div>

<div id="content" class="site-content" tabindex="-1">
