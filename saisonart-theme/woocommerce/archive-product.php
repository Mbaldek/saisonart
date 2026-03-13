<?php
/**
 * SaisonArt — Boutique (Shop Archive) page.
 * Dark theme, hero + filter bar + custom product grid.
 */
defined('ABSPATH') || exit;
get_header('shop');

// Get product categories for filter chips
$product_cats = get_terms(array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
    'exclude'    => array(get_option('default_product_cat')), // exclude "Uncategorized"
));

// Count products
$product_count = wp_count_posts('product');
$total_count   = $product_count->publish ?? 0;

// Current filter
$current_cat = isset($_GET['product_cat']) ? sanitize_text_field($_GET['product_cat']) : '';
?>

<!-- Breadcrumb -->
<nav class="sa-bc" aria-label="Fil d'Ariane">
  <div class="sa-bc-inner">
    <a href="<?php echo esc_url(home_url('/')); ?>">Accueil</a>
    <span class="sa-bc-sep">&rsaquo;</span>
    <span>Boutique</span>
  </div>
</nav>

<!-- Hero -->
<section class="sa-shop-hero">
  <div class="sa-shop-hero-inner">
    <div class="sa-shop-eyebrow">Galerie en ligne</div>
    <h1 class="sa-shop-h1">&OElig;uvres originales<br><em>XIXe &amp; XXe si&egrave;cle</em></h1>
    <p class="sa-shop-desc">Peintures de ma&icirc;tres de l'&eacute;cole fran&ccedil;aise &mdash; chaque tableau expertis&eacute;, certifi&eacute;, livr&eacute; avec son certificat d'authenticit&eacute;.</p>
    <div class="sa-shop-meta">
      <span><strong><?php echo esc_html($total_count); ?></strong> &oelig;uvres disponibles</span>
      <div class="sa-shop-meta-sep"></div>
      <span>Livraison <strong>48h</strong> assur&eacute;e</span>
      <div class="sa-shop-meta-sep"></div>
      <span>Retour <strong>14j</strong> gratuit</span>
    </div>
  </div>
</section>

<!-- Toolbar -->
<div class="sa-toolbar">
  <div class="sa-filter-group">
    <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>"
       class="sa-filter-chip<?php echo empty($current_cat) ? ' active' : ''; ?>">Tous</a>
    <?php if (!empty($product_cats) && !is_wp_error($product_cats)) :
      foreach ($product_cats as $cat) : ?>
        <a href="<?php echo esc_url(get_term_link($cat)); ?>"
           class="sa-filter-chip<?php echo ($current_cat === $cat->slug) ? ' active' : ''; ?>">
          <?php echo esc_html($cat->name); ?>
        </a>
      <?php endforeach;
    endif; ?>
  </div>
  <div class="sa-toolbar-right">
    <span class="sa-result-count"><?php echo esc_html($total_count); ?> r&eacute;sultats</span>
    <?php woocommerce_catalog_ordering(); ?>
  </div>
</div>

<!-- Product grid -->
<div class="sa-grid-wrap">
  <?php
  if (woocommerce_product_loop()) {
      woocommerce_product_loop_start();

      if (wc_get_loop_prop('total')) {
          while (have_posts()) {
              the_post();
              wc_get_template_part('content', 'product');
          }
      }

      woocommerce_product_loop_end();
      ?>
      <div class="sa-pagination">
        <?php woocommerce_pagination(); ?>
      </div>
      <?php
  } else {
      ?>
      <div class="sa-shop-empty">
        <p>Aucune &oelig;uvre pour le moment. Revenez bient&ocirc;t&nbsp;!</p>
      </div>
      <?php
  }
  ?>
</div>

<?php get_footer('shop'); ?>
