<?php
/**
 * SaisonArt — Single blog post.
 */
get_header(); ?>

<main id="main" class="sa-single-post">

<?php while (have_posts()) : the_post();
  $cats = get_the_category();
  $cat_name = !empty($cats) ? $cats[0]->name : 'Article';
  $thumb = get_the_post_thumbnail_url(get_the_ID(), 'full');
?>

<!-- ═══════ POST HEADER ═══════ -->
<header class="sa-post-header">
  <div class="sa-post-header-inner">
    <span class="sa-post-tag"><?php echo esc_html($cat_name); ?></span>
    <h1 class="sa-post-title"><?php the_title(); ?></h1>
    <div class="sa-post-meta">
      <span>
        <svg viewBox="0 0 24 24" width="14" height="14"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <?php echo get_the_date(); ?>
      </span>
      <span>&middot;</span>
      <span><?php echo esc_html(ceil(str_word_count(wp_strip_all_tags(get_the_content())) / 200)); ?> min de lecture</span>
    </div>
  </div>
</header>

<?php if ($thumb) : ?>
<div class="sa-post-hero-img">
  <img src="<?php echo esc_url($thumb); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" decoding="async">
</div>
<?php endif; ?>

<!-- ═══════ POST CONTENT ═══════ -->
<article class="sa-post-content reveal">
  <?php the_content(); ?>
</article>

<!-- ═══════ POST FOOTER ═══════ -->
<footer class="sa-post-footer">
  <div class="sa-post-footer-inner">
    <div class="sa-post-share">
      <span>Partager :</span>
      <a href="mailto:?subject=<?php echo rawurlencode(get_the_title()); ?>&body=<?php echo rawurlencode(get_permalink()); ?>" aria-label="Partager par email">
        <svg viewBox="0 0 24 24" width="18" height="18"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
      </a>
    </div>
    <a href="<?php echo esc_url(home_url('/news/')); ?>" class="sa-post-back">
      <svg viewBox="0 0 24 24" width="16" height="16"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
      Tous les articles
    </a>
  </div>
</footer>

<?php endwhile; ?>

<!-- ═══════ RELATED POSTS ═══════ -->
<?php
$related = new WP_Query(array(
  'posts_per_page' => 3,
  'post__not_in'   => array(get_the_ID()),
  'orderby'        => 'rand',
));
if ($related->have_posts()) : ?>
<section class="sa-post-related">
  <h2>À lire aussi</h2>
  <div class="sa-post-related-grid">
    <?php while ($related->have_posts()) : $related->the_post();
      $r_thumb = get_the_post_thumbnail_url(get_the_ID(), 'medium_large');
      $r_cats = get_the_category();
      $r_cat = !empty($r_cats) ? $r_cats[0]->name : 'Article';
    ?>
    <article class="sa-mag-card">
      <a href="<?php the_permalink(); ?>">
        <div class="sa-mag-card-img">
          <?php if ($r_thumb) : ?>
            <img src="<?php echo esc_url($r_thumb); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" decoding="async">
          <?php endif; ?>
        </div>
        <div class="sa-mag-card-body">
          <span class="sa-mag-card-tag"><?php echo esc_html($r_cat); ?></span>
          <h3><?php the_title(); ?></h3>
          <div class="sa-mag-card-meta"><?php echo get_the_date(); ?></div>
        </div>
      </a>
    </article>
    <?php endwhile; wp_reset_postdata(); ?>
  </div>
</section>
<?php endif; ?>

</main>

<?php get_footer(); ?>
