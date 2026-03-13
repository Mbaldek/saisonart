<?php
/**
 * SaisonArt — Custom Cart Page.
 * Dark card-based layout matching the gallery aesthetic.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @version 7.9.0
 */
defined('ABSPATH') || exit;

do_action('woocommerce_before_cart'); ?>

<div class="sa-cart-wrap">

  <!-- ═══════ CART ITEMS ═══════ -->
  <div class="sa-cart-items">

    <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
      <?php do_action('woocommerce_before_cart_table'); ?>
      <?php do_action('woocommerce_before_cart_contents'); ?>

      <?php
      foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
          $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
          $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

          if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
              $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
              $thumbnail         = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('medium'), $cart_item, $cart_item_key);
              $product_name      = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
              $artist            = $_product->get_attribute('artiste') ?: $_product->get_attribute('artist');
              ?>
              <div class="sa-cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">

                <div class="sa-cart-item-img">
                  <?php if ($product_permalink) : ?>
                    <a href="<?php echo esc_url($product_permalink); ?>"><?php echo $thumbnail; ?></a>
                  <?php else : ?>
                    <?php echo $thumbnail; ?>
                  <?php endif; ?>
                </div>

                <div class="sa-cart-item-info">
                  <?php if ($artist) : ?>
                    <span class="sa-cart-item-artist"><?php echo esc_html($artist); ?></span>
                  <?php endif; ?>
                  <h3 class="sa-cart-item-name">
                    <?php if ($product_permalink) : ?>
                      <a href="<?php echo esc_url($product_permalink); ?>"><?php echo esc_html($product_name); ?></a>
                    <?php else : ?>
                      <?php echo esc_html($product_name); ?>
                    <?php endif; ?>
                  </h3>
                  <?php echo wc_get_formatted_cart_item_data($cart_item); ?>
                  <div class="sa-cart-item-price">
                    <?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?>
                  </div>
                </div>

                <div class="sa-cart-item-actions">
                  <?php
                  echo apply_filters(
                      'woocommerce_cart_item_remove_link',
                      sprintf(
                          '<a href="%s" class="sa-cart-remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">
                            <svg viewBox="0 0 24 24" width="18" height="18"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                          </a>',
                          esc_url(wc_get_cart_remove_url($cart_item_key)),
                          esc_html__('Remove this item', 'woocommerce'),
                          esc_attr($product_id),
                          esc_attr($_product->get_sku())
                      ),
                      $cart_item_key
                  );
                  ?>
                </div>

              </div>
              <?php
          }
      }
      ?>

      <?php do_action('woocommerce_cart_contents'); ?>

      <?php if (wc_coupons_enabled()) : ?>
        <div class="sa-cart-coupon">
          <label for="coupon_code" class="screen-reader-text"><?php esc_html_e('Coupon:', 'woocommerce'); ?></label>
          <input type="text" name="coupon_code" class="sa-cart-coupon-input" id="coupon_code" value="" placeholder="Code promo" />
          <button type="submit" class="sa-cart-coupon-btn" name="apply_coupon" value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>">Appliquer</button>
          <?php do_action('woocommerce_cart_coupon'); ?>
        </div>
      <?php endif; ?>

      <button type="submit" class="sa-cart-update" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>" style="display:none;"><?php esc_html_e('Update cart', 'woocommerce'); ?></button>
      <?php do_action('woocommerce_cart_actions'); ?>
      <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>

      <?php do_action('woocommerce_after_cart_contents'); ?>
      <?php do_action('woocommerce_after_cart_table'); ?>
    </form>

  </div>

  <!-- ═══════ SIDEBAR — TOTALS ═══════ -->
  <aside class="sa-cart-sidebar">
    <div class="sa-cart-totals">
      <h3 class="sa-cart-totals-title">R&eacute;capitulatif</h3>

      <div class="sa-cart-totals-rows">
        <div class="sa-cart-totals-row">
          <span>Sous-total</span>
          <span><?php wc_cart_totals_subtotal_html(); ?></span>
        </div>

        <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
          <div class="sa-cart-totals-row sa-cart-discount">
            <span>Remise (<?php echo esc_html($code); ?>)</span>
            <span><?php wc_cart_totals_coupon_html($coupon); ?></span>
          </div>
        <?php endforeach; ?>

        <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
          <?php
          $sa_shipping = WC()->shipping();
          $packages = $sa_shipping ? $sa_shipping->get_packages() : array();
          if (!empty($packages)) :
              foreach ($packages as $i => $package) {
                  $chosen_method = isset(WC()->session->chosen_shipping_methods[$i]) ? WC()->session->chosen_shipping_methods[$i] : '';
                  $available     = isset($package['rates']) ? $package['rates'] : array();
                  if ($available) {
                      $rate = $chosen_method && isset($available[$chosen_method]) ? $available[$chosen_method] : reset($available);
                      ?>
                      <div class="sa-cart-totals-row">
                        <span>Livraison</span>
                        <span><?php echo wp_kses_post($rate->get_label()); ?><?php if ($rate->cost > 0) : ?> — <?php echo wc_price($rate->cost); ?><?php else : ?> — Offerte<?php endif; ?></span>
                      </div>
                      <?php
                  }
              }
          else : ?>
              <div class="sa-cart-totals-row">
                <span>Livraison</span>
                <span>Calculée à l'étape suivante</span>
              </div>
          <?php endif; ?>
        <?php endif; ?>

        <?php foreach (WC()->cart->get_fees() as $fee) : ?>
          <div class="sa-cart-totals-row">
            <span><?php echo esc_html($fee->name); ?></span>
            <span><?php wc_cart_totals_fee_html($fee); ?></span>
          </div>
        <?php endforeach; ?>

        <?php if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()) : ?>
          <?php foreach (WC()->cart->get_tax_totals() as $tax) : ?>
            <div class="sa-cart-totals-row">
              <span><?php echo esc_html($tax->label); ?></span>
              <span><?php echo wp_kses_post($tax->formatted_amount); ?></span>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <div class="sa-cart-totals-total">
        <span>Total</span>
        <span><?php wc_cart_totals_order_total_html(); ?></span>
      </div>
    </div>

    <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="sa-cart-checkout-btn">
      <svg viewBox="0 0 24 24" width="16" height="16"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      Passer commande
    </a>

    <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="sa-cart-continue">
      <svg viewBox="0 0 24 24" width="14" height="14"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
      Continuer mes achats
    </a>
  </aside>

</div>

<?php do_action('woocommerce_after_cart'); ?>
