<?php
/**
 * SaisonArt — Custom product card for shop grid.
 * Dark design matching the boutique mockup.
 */
defined('ABSPATH') || exit;

global $product;
if (!is_a($product, 'WC_Product')) return;

$image_id  = $product->get_image_id();
$image_url = $image_id ? wp_get_attachment_image_url($image_id, 'large') : wc_placeholder_img_src('large');
$permalink = $product->get_permalink();

// Attributes
$artist     = $product->get_attribute('artiste') ?: $product->get_attribute('artist');
$dimensions = $product->get_attribute('dimensions');
$technique  = $product->get_attribute('technique');
$date_attr  = $product->get_attribute('date') ?: $product->get_attribute('annee');

// Build dims line: "Technique · Date · Dimensions"
$dims_parts = array_filter(array($technique, $date_attr, $dimensions));
$dims_line  = implode(' · ', $dims_parts);

// Stock status
$is_sold = !$product->is_in_stock();

// "New" badge: products created within last 14 days
$created    = strtotime($product->get_date_created());
$is_new     = $created && (time() - $created) < (14 * 86400);
?>

<li <?php wc_product_class('sa-pcard', $product); ?><?php if ($is_sold) echo ' style="opacity:.6;pointer-events:none;"'; ?>>
  <a href="<?php echo esc_url($permalink); ?>" class="sa-pcard-link">
    <div class="sa-pcard-img">
      <img src="<?php echo esc_url($image_url); ?>"
           alt="<?php echo esc_attr($product->get_name() . ($artist ? ' par ' . $artist : '')); ?>"
           loading="lazy" decoding="async">
      <?php if (!$is_sold) : ?>
        <div class="sa-pcard-overlay"></div>
        <div class="sa-pcard-quick">Voir l'&oelig;uvre &rarr;</div>
      <?php endif; ?>

      <?php if ($is_sold) : ?>
        <div class="sa-pcard-badge sold">Vendu</div>
      <?php elseif ($is_new) : ?>
        <div class="sa-pcard-badge new">Nouveau</div>
      <?php endif; ?>
    </div>

    <div class="sa-pcard-body">
      <?php if ($artist) : ?>
        <div class="sa-pcard-artist"><?php echo esc_html($artist); ?></div>
      <?php endif; ?>
      <div class="sa-pcard-title"><?php echo esc_html($product->get_name()); ?></div>
      <?php if ($dims_line) : ?>
        <div class="sa-pcard-dims"><?php echo esc_html($dims_line); ?></div>
      <?php endif; ?>
    </div>

    <div class="sa-pcard-foot">
      <?php if ($is_sold) : ?>
        <div class="sa-pcard-price sold-price"><?php echo $product->get_price_html(); ?></div>
        <span class="sa-pcard-cta sold-cta">Vendu</span>
      <?php else : ?>
        <div class="sa-pcard-price"><?php echo $product->get_price_html(); ?></div>
        <span class="sa-pcard-cta">Voir</span>
      <?php endif; ?>
    </div>
  </a>
</li>
