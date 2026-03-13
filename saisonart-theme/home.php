<?php
/**
 * SaisonArt — Blog archive (Magazine).
 * WordPress uses this when the "Posts page" is set in Settings > Reading.
 */
get_header(); ?>

<main id="main" class="sa-magazine-page">

<!-- ═══════ HERO ═══════ -->
<section class="sa-mag-hero">
  <div class="sa-mag-hero-inner">
    <div class="sa-mag-hero-eyebrow">Culture & Art</div>
    <h1 class="sa-mag-hero-h1">Le Magazine<br><em>SaisonArt</em></h1>
    <p class="sa-mag-hero-desc">Histoires d'artistes, coulisses de la galerie, conseils pour collectionneurs — un regard sur l'art qui vous ressemble.</p>
  </div>
</section>

<!-- ═══════ ARTICLES ═══════ -->
<section class="sa-mag-articles">
  <?php if (have_posts()) : ?>

  <div class="sa-mag-grid reveal">
    <?php
    $post_index = 0;
    while (have_posts()) : the_post();
      $cats = get_the_category();
      $cat_name = !empty($cats) ? $cats[0]->name : 'Article';
      $thumb = get_the_post_thumbnail_url(get_the_ID(), 'large');
    ?>

    <?php if ($post_index === 0) : ?>
    <article class="sa-mag-card sa-mag-card--featured">
      <a href="<?php the_permalink(); ?>">
        <div class="sa-mag-card-img">
          <?php if ($thumb) : ?>
            <img src="<?php echo esc_url($thumb); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" decoding="async">
          <?php endif; ?>
          <div class="sa-mag-card-overlay"></div>
        </div>
        <div class="sa-mag-card-body">
          <span class="sa-mag-card-tag"><?php echo esc_html($cat_name); ?></span>
          <h2><?php the_title(); ?></h2>
          <p><?php echo wp_trim_words(get_the_excerpt(), 30); ?></p>
          <div class="sa-mag-card-meta">
            <svg viewBox="0 0 24 24" width="14" height="14"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <?php echo get_the_date(); ?>
          </div>
        </div>
      </a>
    </article>

    <?php else : ?>
    <article class="sa-mag-card">
      <a href="<?php the_permalink(); ?>">
        <div class="sa-mag-card-img">
          <?php if ($thumb) : ?>
            <img src="<?php echo esc_url($thumb); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" decoding="async">
          <?php endif; ?>
        </div>
        <div class="sa-mag-card-body">
          <span class="sa-mag-card-tag"><?php echo esc_html($cat_name); ?></span>
          <h3><?php the_title(); ?></h3>
          <p><?php echo wp_trim_words(get_the_excerpt(), 18); ?></p>
          <div class="sa-mag-card-meta"><?php echo get_the_date(); ?></div>
        </div>
      </a>
    </article>
    <?php endif; ?>

    <?php
      $post_index++;
    endwhile;
    ?>
  </div>

  <!-- Pagination -->
  <div class="sa-mag-pagination">
    <?php
    the_posts_pagination(array(
      'mid_size'  => 2,
      'prev_text' => '&larr; Précédent',
      'next_text' => 'Suivant &rarr;',
    ));
    ?>
  </div>

  <?php else : ?>
  <div class="sa-mag-empty">
    <p>Aucun article pour l'instant. Revenez bientôt.</p>
  </div>
  <?php endif; ?>
</section>

</main>

<?php get_footer(); ?>
