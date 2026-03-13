<?php
/**
 * SaisonArt — Single Product page (V2 design).
 * Custom template matching the FINAL mockup design.
 * V1 backup saved as single-product-v1.php
 */
defined('ABSPATH') || exit;

get_header('shop');

while (have_posts()) : the_post();

global $product;
if (!is_a($product, 'WC_Product')) continue;

// ── Product data ──
$main_image_id  = $product->get_image_id();
$main_image_url = $main_image_id ? wp_get_attachment_image_url($main_image_id, 'full') : wc_placeholder_img_src('full');
$gallery_ids    = $product->get_gallery_image_ids();
$all_images     = array_merge($main_image_id ? array($main_image_id) : array(), $gallery_ids);

// Attributes (flexible matching like V1)
$artist     = $product->get_attribute('artiste') ?: $product->get_attribute('artist');
$technique  = $product->get_attribute('technique');
$dimensions = $product->get_attribute('dimensions');
$periode    = $product->get_attribute('periode') ?: $product->get_attribute('période');
$ecole      = $product->get_attribute('ecole') ?: $product->get_attribute('école');
$etat       = $product->get_attribute('etat') ?: $product->get_attribute('état');

// Custom meta
$artist_name  = get_post_meta(get_the_ID(), '_sa_artiste_nom', true) ?: $artist;
$artist_dates = get_post_meta(get_the_ID(), '_sa_artiste_dates', true);
$artist_bio   = get_post_meta(get_the_ID(), '_sa_artiste_bio', true);
$certified    = get_post_meta(get_the_ID(), '_sa_certified', true);

// Fallback meta for attributes
if (!$technique)  $technique  = get_post_meta(get_the_ID(), '_sa_technique', true);
if (!$dimensions) $dimensions = get_post_meta(get_the_ID(), '_sa_dimensions', true);
if (!$periode)    $periode    = get_post_meta(get_the_ID(), '_sa_periode', true);
if (!$ecole)      $ecole      = get_post_meta(get_the_ID(), '_sa_ecole', true);
if (!$etat)       $etat       = get_post_meta(get_the_ID(), '_sa_etat', true);

// Categories
$terms    = get_the_terms(get_the_ID(), 'product_cat');
$category = (!empty($terms) && !is_wp_error($terms)) ? $terms[0]->name : '';

// Description
$desc       = $product->get_description();
$short_desc = $product->get_short_description();

// Artist initials
$initials = '';
if ($artist_name) {
    foreach (explode(' ', $artist_name) as $word) {
        if ($word) $initials .= mb_strtoupper(mb_substr($word, 0, 1));
    }
    $initials = mb_substr($initials, 0, 2);
}

// Contact URL
$contact_url = home_url('/conseil/');
$contact_url = add_query_arg('sujet', urlencode('Renseignement : ' . $product->get_name()), $contact_url);

// Price
$price_html = $product->get_price_html();
?>

<!-- Lightbox -->
<div class="lightbox" id="sa-lb" onclick="saCloseLB()">
  <button class="lb-close" onclick="saCloseLB()">&times;</button>
  <img id="sa-lb-img" src="" alt="">
  <div class="lb-caption"><?php echo esc_html($product->get_name()); ?> &mdash; Cliquer pour fermer</div>
</div>

<!-- PRODUCT LAYOUT -->
<main class="product-layout">

  <!-- ════ COLONNE GAUCHE ════ -->
  <div class="product-left">

    <!-- Galerie -->
    <div class="gallery-main" onclick="saOpenLB()">
      <img
        src="<?php echo esc_url($main_image_url); ?>"
        alt="<?php echo esc_attr($product->get_name()); ?>"
        id="sa-main-img"
        loading="eager"
      >
      <?php if ($certified) : ?>
        <div class="gallery-badge"><span class="badge-star">&#9733;</span> &OElig;uvre certifi&eacute;e</div>
      <?php endif; ?>
      <div class="gallery-zoom">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><circle cx="5" cy="5" r="3.5" stroke="currentColor" stroke-width="1.2"/><path d="M8 8l3 3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/><path d="M5 3.5v3M3.5 5h3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
        Zoom
      </div>
    </div>

    <!-- Thumbnails -->
    <?php if (count($all_images) > 1) : ?>
      <div class="thumb-row">
        <?php foreach ($all_images as $i => $img_id) :
          if (!$img_id) continue;
          $thumb_url = wp_get_attachment_image_url($img_id, 'medium');
          $full_url  = wp_get_attachment_image_url($img_id, 'full');
        ?>
          <div class="thumb<?php echo $i === 0 ? ' active' : ''; ?>" onclick="saSetThumb(this, '<?php echo esc_url($full_url); ?>')">
            <img src="<?php echo esc_url($thumb_url); ?>" alt="" loading="lazy">
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- ① L'oeuvre -->
    <?php if ($desc || $short_desc || $technique || $dimensions) : ?>
      <div class="section" style="margin-top:0;">
        <div class="section-label">L'&oelig;uvre</div>

        <?php if ($short_desc) : ?>
          <h2 class="desc-title"><?php echo wp_kses_post($short_desc); ?></h2>
        <?php endif; ?>

        <?php if ($desc) : ?>
          <div class="desc-body">
            <?php echo wpautop(wp_kses_post($desc)); ?>
          </div>
        <?php endif; ?>

        <?php
        $chips = array();
        if ($technique)  $chips[] = array('icon' => '&#127912;', 'label' => 'Technique', 'value' => $technique);
        if ($dimensions) $chips[] = array('icon' => '&#128208;', 'label' => 'Dimensions', 'value' => $dimensions);
        if ($periode)    $chips[] = array('icon' => '&#128197;', 'label' => 'P&eacute;riode', 'value' => $periode);
        if ($ecole)      $chips[] = array('icon' => '&#127963;', 'label' => '&Eacute;cole', 'value' => $ecole);
        if ($etat)       $chips[] = array('icon' => '&#128444;', 'label' => '&Eacute;tat', 'value' => $etat);
        if (!empty($chips)) :
        ?>
          <div class="meta-chips">
            <?php foreach ($chips as $chip) : ?>
              <div class="chip">
                <span class="chip-em"><?php echo $chip['icon']; ?></span>
                <span><strong><?php echo $chip['label']; ?> :</strong> <?php echo esc_html($chip['value']); ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <!-- ② L'artiste -->
    <?php if ($artist_name) : ?>
      <div class="section">
        <div class="section-label">L'artiste</div>
        <div class="artist-block">
          <div class="artist-avatar"><?php echo esc_html($initials ?: '?'); ?></div>
          <div class="artist-info">
            <div class="artist-name"><?php echo esc_html($artist_name); ?></div>
            <?php if ($artist_dates) : ?>
              <div class="artist-dates"><?php echo esc_html($artist_dates); ?></div>
            <?php endif; ?>
            <?php if ($artist_bio) : ?>
              <p class="artist-bio"><?php echo wp_kses_post($artist_bio); ?></p>
            <?php endif; ?>
            <?php
            $artist_cat = get_term_by('name', $artist_name, 'product_cat');
            if (!$artist_cat) $artist_tag = get_term_by('name', $artist_name, 'product_tag');
            $artist_url = '';
            if (!empty($artist_cat) && !is_wp_error($artist_cat)) {
                $artist_url = get_term_link($artist_cat);
            } elseif (!empty($artist_tag) && !is_wp_error($artist_tag)) {
                $artist_url = get_term_link($artist_tag);
            }
            if ($artist_url && !is_wp_error($artist_url)) : ?>
              <a href="<?php echo esc_url($artist_url); ?>" class="artist-link">
                Voir toutes ses &oelig;uvres disponibles
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 7h10M8 3l4 4-4 4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- ③ Certificat + Garanties -->
    <div class="section">
      <div class="section-label">Garanties</div>
      <div class="cert-block">
        <div class="cert-icon">&#128196;</div>
        <div class="cert-body">
          <div class="cert-title">Certificat d'authenticit&eacute; inclus</div>
          <div class="cert-desc">Expertise r&eacute;alis&eacute;e par un commissaire-priseur agr&eacute;&eacute;. Document joint &agrave; l'&oelig;uvre &agrave; la livraison. Identifiant unique trac&eacute; en base de donn&eacute;es.</div>
        </div>
      </div>
      <ul class="guarantees">
        <li><span class="g-icon">&#128666;</span> Livraison offerte en France m&eacute;tropolitaine &middot; Emballage mus&eacute;al s&eacute;curis&eacute;</li>
        <li><span class="g-icon">&#8617;&#65039;</span> Retour accept&eacute; sous 14 jours &mdash; Garantie satisfait ou rembours&eacute;</li>
        <li><span class="g-icon">&#128172;</span> Conseil personnalis&eacute; disponible &mdash; R&eacute;ponse en moins de 4h</li>
      </ul>
    </div>

    <!-- ④ Oeuvres similaires -->
    <div class="section">
      <div class="section-label">Vous pourriez aussi aimer</div>
      <?php
      $related_ids = wc_get_related_products($product->get_id(), 3);
      if (!empty($related_ids)) :
      ?>
        <div class="related-grid">
          <?php foreach ($related_ids as $rel_id) :
            $rel = wc_get_product($rel_id);
            if (!$rel) continue;
            $rel_img_id = $rel->get_image_id();
            $rel_terms  = get_the_terms($rel_id, 'product_cat');
            $rel_cat    = (!empty($rel_terms) && !is_wp_error($rel_terms)) ? $rel_terms[0]->name : '';
            $rel_artist = $rel->get_attribute('artiste') ?: $rel->get_attribute('artist');
          ?>
            <a class="related-card" href="<?php echo esc_url(get_permalink($rel_id)); ?>">
              <div class="related-img">
                <?php if ($rel_img_id) : ?>
                  <img src="<?php echo esc_url(wp_get_attachment_image_url($rel_img_id, 'medium')); ?>" alt="<?php echo esc_attr($rel->get_name()); ?>">
                <?php else : ?>
                  <div class="ri-placeholder"><span>&#128444;</span><span><?php echo esc_html($rel_cat ?: 'OEuvre'); ?></span></div>
                <?php endif; ?>
              </div>
              <div class="related-body">
                <?php if ($rel_cat) : ?>
                  <div class="related-cat"><?php echo esc_html($rel_cat); ?></div>
                <?php endif; ?>
                <div class="related-title"><?php echo esc_html($rel->get_name()); ?></div>
                <?php if ($rel_artist) : ?>
                  <div class="related-artist"><?php echo esc_html($rel_artist); ?></div>
                <?php endif; ?>
                <div class="related-price"><?php echo wp_kses_post($rel->get_price_html()); ?></div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

  </div><!-- /product-left -->


  <!-- ════ SIDEBAR STICKY ════ -->
  <div class="product-right">
    <div class="sidebar">

      <!-- Eyebrow -->
      <div class="sb-eyebrow">
        <?php if ($category) : ?>
          <span class="sb-cat"><?php echo esc_html($category); ?></span>
        <?php endif; ?>
        <?php if ($certified) : ?>
          <span class="sb-certified"><span class="sb-certified-star">&#9733;</span> Certifi&eacute;e</span>
        <?php endif; ?>
      </div>

      <!-- Titre -->
      <div class="sb-title-wrap">
        <div class="sb-title"><?php echo esc_html($product->get_name()); ?></div>
        <?php if ($artist_name) : ?>
          <div class="sb-artist-sub"><?php echo esc_html($artist_name); ?><?php if ($ecole) echo ' &middot; ' . esc_html($ecole); ?></div>
        <?php endif; ?>
      </div>

      <!-- Chips -->
      <?php
      $sb_chips = array_filter(array($technique, $dimensions, $etat, $periode));
      if (!empty($sb_chips)) :
      ?>
        <div class="sb-chips">
          <?php foreach ($sb_chips as $val) : ?>
            <span class="sb-chip"><?php echo esc_html($val); ?></span>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Prix -->
      <div class="sb-price-wrap">
        <div class="sb-price"><?php echo wp_kses_post($price_html); ?></div>
        <div class="sb-price-info">
          <span>TVA incluse</span>
          <span>Livraison offerte</span>
        </div>

        <!-- CTAs -->
        <div class="sb-cta-wrap">
          <?php if ($product->is_in_stock()) : ?>
            <?php woocommerce_template_single_add_to_cart(); ?>
          <?php else : ?>
            <div style="text-align:center;padding:14px;color:var(--muted);font-size:14px;">&OElig;uvre vendue</div>
          <?php endif; ?>
          <a href="<?php echo esc_url($contact_url); ?>" class="btn-contact">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 2h10a1 1 0 011 1v7a1 1 0 01-1 1H5L2 13V3a1 1 0 011-1z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/></svg>
            Contacter pour plus d'infos
          </a>
        </div>
      </div>

      <!-- Paiement -->
      <div class="sb-payment">
        <span class="pay-badge">&#128179; CB / Visa</span>
        <span class="pay-badge">&#128274; 3&times; sans frais</span>
      </div>

      <!-- Trust -->
      <ul class="sb-trust">
        <li><span class="sb-t-icon">&#128666;</span> Livraison offerte &middot; France m&eacute;tropolitaine</li>
        <li><span class="sb-t-icon">&#8617;&#65039;</span> Retour 14 jours garanti</li>
        <li><span class="sb-t-icon">&#128196;</span> Certificat d'authenticit&eacute; inclus</li>
      </ul>

    </div><!-- /sidebar -->
  </div>

</main>

<!-- MOBILE BAR -->
<div class="mobile-bar">
  <div class="mb-price"><?php echo wp_kses_post($price_html); ?></div>
  <?php if ($product->is_in_stock()) : ?>
    <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" class="mb-buy"
       data-product_id="<?php echo esc_attr(get_the_ID()); ?>" data-quantity="1">
      Acheter
    </a>
  <?php endif; ?>
  <a href="<?php echo esc_url($contact_url); ?>" class="mb-contact">Infos</a>
</div>

<script>
/* Thumbnails */
function saSetThumb(el, src) {
  document.querySelectorAll('.thumb').forEach(function(t) { t.classList.remove('active'); });
  el.classList.add('active');
  if (src) document.getElementById('sa-main-img').src = src;
}
/* Lightbox */
function saOpenLB() {
  var src = document.getElementById('sa-main-img').src;
  document.getElementById('sa-lb-img').src = src;
  document.getElementById('sa-lb').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function saCloseLB() {
  document.getElementById('sa-lb').classList.remove('open');
  document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') saCloseLB(); });
</script>

<?php endwhile;

get_footer('shop'); ?>
