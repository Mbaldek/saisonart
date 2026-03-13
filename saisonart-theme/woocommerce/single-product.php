<?php
/**
 * SaisonArt — Single Product page.
 * Split layout: sticky gallery left + scrollable info right.
 */
defined('ABSPATH') || exit;
get_header('shop');

while (have_posts()) : the_post();

global $product;
if (!is_a($product, 'WC_Product')) continue;

// Images
$main_image_id  = $product->get_image_id();
$main_image_url = $main_image_id ? wp_get_attachment_image_url($main_image_id, 'full') : wc_placeholder_img_src('full');
$gallery_ids    = $product->get_gallery_image_ids();
$all_images     = array_merge(array($main_image_id), $gallery_ids);

// Attributes
$artist      = $product->get_attribute('artiste') ?: $product->get_attribute('artist');
$date_attr   = $product->get_attribute('date') ?: $product->get_attribute('annee');
$dimensions  = $product->get_attribute('dimensions');
$technique   = $product->get_attribute('technique');
$signature   = $product->get_attribute('signature');
$ecole       = $product->get_attribute('ecole') ?: $product->get_attribute('école');
$encadrement = $product->get_attribute('encadrement');
$etat        = $product->get_attribute('etat') ?: $product->get_attribute('état');

// Categories
$cats = wc_get_product_category_list($product->get_id(), ' · ');
$cats_plain = wp_strip_all_tags($cats);
?>

<!-- Breadcrumb -->
<nav class="sa-bc" aria-label="Fil d'Ariane">
  <div class="sa-bc-inner">
    <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">Boutique</a>
    <span class="sa-bc-sep">&rsaquo;</span>
    <span><?php echo esc_html($product->get_name()); ?></span>
  </div>
</nav>

<div class="sa-product" id="product-<?php echo esc_attr($product->get_id()); ?>">

  <!-- ═══════ GALLERY (left, sticky) ═══════ -->
  <div class="sa-product-gallery">
    <div class="sa-gallery-main">
      <img id="saMainImg" src="<?php echo esc_url($main_image_url); ?>"
           alt="<?php echo esc_attr($product->get_name()); ?>">
    </div>
    <?php if (count($all_images) > 1) : ?>
      <div class="sa-gallery-thumbs">
        <?php foreach ($all_images as $i => $img_id) :
          if (!$img_id) continue;
          $thumb_url = wp_get_attachment_image_url($img_id, 'medium');
          $full_url  = wp_get_attachment_image_url($img_id, 'full');
        ?>
          <div class="sa-gallery-thumb<?php echo $i === 0 ? ' active' : ''; ?>"
               data-full="<?php echo esc_url($full_url); ?>">
            <img src="<?php echo esc_url($thumb_url); ?>" alt="" loading="lazy" decoding="async">
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- ═══════ INFO (right, scrollable) ═══════ -->
  <div class="sa-product-info">

    <?php if ($cats_plain) : ?>
      <div class="sa-product-eyebrow"><?php echo esc_html($cats_plain); ?><?php if ($ecole) echo ' · ' . esc_html($ecole); ?></div>
    <?php endif; ?>

    <h1 class="sa-product-h1"><?php echo esc_html($product->get_name()); ?></h1>

    <?php if ($artist) : ?>
      <p class="sa-product-artist">par <strong><?php echo esc_html($artist); ?></strong><?php if ($date_attr) echo ' — ' . esc_html($date_attr); ?></p>
    <?php endif; ?>

    <div class="sa-product-dims-row">
      <?php if ($dimensions) : ?>
        <span>
          <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="1"/></svg>
          <?php echo esc_html($dimensions); ?>
        </span>
      <?php endif; ?>
      <?php if ($technique) : ?>
        <span>
          <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
          <?php echo esc_html($technique); ?>
        </span>
      <?php endif; ?>
      <span>
        <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Certifi&eacute;e
      </span>
    </div>

    <!-- Price block -->
    <div class="sa-price-block">
      <div class="sa-price-main"><?php echo $product->get_price_html(); ?></div>
      <p class="sa-price-note">TVA incluse · Livraison offerte en France m&eacute;tropolitaine</p>
    </div>

    <!-- CTAs -->
    <div class="sa-cta-group">
      <?php if ($product->is_in_stock()) : ?>
        <?php woocommerce_template_single_add_to_cart(); ?>
      <?php else : ?>
        <div class="sa-sold-notice">&OElig;uvre vendue</div>
      <?php endif; ?>
      <a href="<?php echo esc_url(home_url('/conseil/')); ?>" class="sa-btn-secondary">
        <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        Contacter pour plus d'infos
      </a>
    </div>

    <!-- Trust badges -->
    <div class="sa-trust">
      <div class="sa-trust-item">
        <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Authenticit&eacute; garantie
      </div>
      <div class="sa-trust-item">
        <svg viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        Livraison 48h assur&eacute;e
      </div>
      <div class="sa-trust-item">
        <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
        Retour 14j gratuit
      </div>
    </div>

    <div class="sa-divider"></div>

    <!-- Certificate -->
    <div class="sa-cert">
      <div class="sa-cert-icon">
        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      </div>
      <div class="sa-cert-text">
        <h4>Certificat d'authenticit&eacute; inclus</h4>
        <p>Expertise r&eacute;alis&eacute;e par un commissaire-priseur agr&eacute;&eacute;. Document joint &agrave; l'&oelig;uvre &agrave; la livraison.</p>
      </div>
    </div>

    <!-- Specs -->
    <?php
    $specs = array_filter(array(
        'Artiste'      => $artist,
        'Date'         => $date_attr,
        'Technique'    => $technique,
        'Format'       => $dimensions,
        'Signature'    => $signature,
        'École'        => $ecole,
        'Encadrement'  => $encadrement,
        'État'         => $etat,
    ));
    if (!empty($specs)) : ?>
      <div class="sa-spec-group">
        <div class="sa-spec-title">Fiche technique</div>
        <div class="sa-spec-grid">
          <?php foreach ($specs as $label => $value) : ?>
            <div class="sa-spec-row">
              <span class="sa-spec-label"><?php echo esc_html($label); ?></span>
              <span class="sa-spec-value"><?php echo esc_html($value); ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Description -->
    <?php
    $desc = $product->get_description();
    $short_desc = $product->get_short_description();
    if ($desc || $short_desc) : ?>
      <div class="sa-divider" style="margin-top:28px;"></div>
      <div class="sa-product-desc">
        <?php
        if ($short_desc) echo wpautop($short_desc);
        if ($desc) echo wpautop($desc);
        ?>
      </div>
    <?php endif; ?>

  </div><!-- .sa-product-info -->
</div><!-- .sa-product -->

<?php endwhile;

get_footer('shop'); ?>
