<?php
/**
 * SaisonArt — Custom header (overrides Storefront).
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://i0.wp.com" crossorigin>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="hfeed site">

<!-- ═══════ ANNOUNCEMENT BAR (hidden on homepage) ═══════ -->
<?php
$sa_s = sa_engage_get();
if (!is_front_page() && !empty($sa_s['announce_enabled']) && $sa_s['announce_enabled'] !== '0') :
    $sa_msgs = array_filter(array($sa_s['announce_msg_1'], $sa_s['announce_msg_2'], $sa_s['announce_msg_3']));
    if (!empty($sa_msgs)) :
?>
<div class="sa-announce" style="--sa-announce-bg:<?php echo esc_attr($sa_s['announce_bg']); ?>">
  <div class="sa-announce-track">
    <?php foreach ($sa_msgs as $i => $msg) : ?>
      <span class="sa-announce-text<?php echo $i === 0 ? ' is-active' : ''; ?>"><?php echo esc_html($msg); ?></span>
    <?php endforeach; ?>
  </div>
  <button class="sa-announce-close" aria-label="Fermer">
    <svg viewBox="0 0 24 24" width="14" height="14"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
  </button>
</div>
<?php endif; endif; ?>

<!-- ═══════ HEADER ═══════ -->
<header class="sa-header <?php echo is_front_page() ? 'sa-header--dark' : 'sa-header--light'; ?>" id="sa-header">
  <a href="<?php echo esc_url(home_url('/')); ?>" class="sa-header-logo">
    <?php
    $logo_variant = is_front_page() ? 'light' : 'dark';
    $logo_url = get_stylesheet_directory_uri() . '/assets/images/logo-saisonart-' . $logo_variant . '.svg';
    ?>
    <img src="<?php echo esc_url($logo_url); ?>"
         alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
  </a>

  <nav class="sa-nav" aria-label="Navigation principale">
    <?php
    $menu_items = array(
        '/boutique/'   => 'Œuvres',
        '/conseil/'    => 'Conseil',
        '/news/'       => 'Magazine',
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
<nav class="sa-header-mobile-menu" aria-label="Menu mobile">
  <a href="<?php echo esc_url(home_url('/boutique/')); ?>">Œuvres</a>
  <a href="<?php echo esc_url(home_url('/conseil/')); ?>">Conseil</a>
  <a href="<?php echo esc_url(home_url('/news/')); ?>">Magazine</a>
  <a href="<?php echo esc_url(home_url('/contact-us/')); ?>">Contact</a>
  <a href="<?php echo esc_url(wc_get_cart_url()); ?>">Panier</a>
</nav>

<div id="content" class="site-content" tabindex="-1">
