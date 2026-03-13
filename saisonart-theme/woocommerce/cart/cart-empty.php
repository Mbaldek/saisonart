<?php
/**
 * SaisonArt — Empty Cart page.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @version 7.0.1
 */
defined('ABSPATH') || exit;

do_action('woocommerce_cart_is_empty');

if (wc_get_page_id('shop') > 0) : ?>

<div class="sa-cart-empty">

  <svg class="sa-cart-empty-icon" viewBox="0 0 64 64" width="64" height="64" aria-hidden="true">
    <circle cx="24" cy="56" r="4"/>
    <circle cx="48" cy="56" r="4"/>
    <path d="M2 2h10l8 40h36l6-24H18"/>
  </svg>

  <p class="sa-cart-empty-title">Votre panier est vide</p>
  <p class="sa-cart-empty-sub">Explorez notre collection et laissez-vous inspirer par l'art.</p>

  <a class="sa-cart-empty-btn" href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">
    <svg viewBox="0 0 24 24" width="16" height="16"><polyline points="5 12 12 5 19 12"/><line x1="12" y1="5" x2="12" y2="19"/></svg>
    Découvrir la galerie
  </a>

</div>

<?php endif; ?>
