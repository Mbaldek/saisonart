<?php
/**
 * SaisonArt — Homepage template.
 * WordPress uses this file automatically for the front page.
 */
get_header(); ?>

<main id="main">

<!-- ═══════ 1. HERO — Vidéo Background ═══════ -->
<section class="sa-hero" id="hero">
  <!-- Hero burger nav -->
  <div class="sa-hero-nav">
    <button class="sa-hero-burger" aria-label="Menu"><span></span><span></span><span></span></button>
    <nav class="sa-hero-menu">
      <a href="<?php echo esc_url(home_url('/')); ?>">Accueil</a>
      <a href="<?php echo esc_url(home_url('/boutique/')); ?>">Œuvres</a>
      <a href="<?php echo esc_url(home_url('/conseil/')); ?>">Conseil</a>
      <a href="<?php echo esc_url(home_url('/news/')); ?>">Magazine</a>
    </nav>
  </div>

  <!-- Vidéo background blurred -->
  <div class="sa-hero-video">
    <video autoplay muted loop playsinline preload="none">
      <source src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/saisonart-hero.mp4'); ?>" type="video/mp4">
    </video>
  </div>

  <div class="sa-hero-content">
    <div class="sa-hero-deconum">S</div>

    <div class="sa-hero-eyebrow">
      <div class="sa-hero-eyebrow-line"></div>
      Galerie d'art en ligne
    </div>
    <h1 class="sa-hero-h1">
      L'art original,
      <em>choisi pour vous</em>
    </h1>
    <p class="sa-hero-desc">
      Peintures de maîtres, école française — XIXe et XXe siècle. Chaque toile expertisée, certifiée, livrée.
    </p>

    <div class="sa-hero-chips">
      <a href="<?php echo esc_url(home_url('/boutique/')); ?>" class="sa-hero-chip primary">Découvrir la boutique</a>
      <a href="<?php echo esc_url(home_url('/conseil/')); ?>" class="sa-hero-chip">Conseil expert</a>
      <a href="<?php echo esc_url(home_url('/news/')); ?>" class="sa-hero-chip">Le Magazine</a>
    </div>

    <div class="sa-hero-meta">
      <?php
      $product_count = wp_count_posts('product');
      $count = $product_count->publish ?? 0;
      ?>
      <span><strong><?php echo esc_html($count); ?></strong> œuvres</span>
      <div class="sa-hero-meta-sep"></div>
      <span>Livraison <strong>48h</strong></span>
      <div class="sa-hero-meta-sep"></div>
      <span>Retour <strong>14j</strong></span>
    </div>
  </div>
</section>

<!-- ═══════ 2. SÉLECTION DU MOMENT ═══════ -->
<section class="sa-selection" id="selection">
  <!-- Ambient orbs -->
  <div class="sa-sel-orb sa-sel-orb--1"></div>
  <div class="sa-sel-orb sa-sel-orb--2"></div>
  <div class="sa-sel-orb sa-sel-orb--3"></div>

  <div class="sa-sel-header reveal">
    <div class="sa-sel-title-group">
      <div class="sa-sel-eyebrow">Coup de cœur</div>
      <h2 class="sa-sel-h2">Sélection<br><em>du moment</em></h2>
    </div>
    <a href="<?php echo esc_url(home_url('/boutique/')); ?>" class="sa-sel-link">
      Voir toute la boutique
      <svg viewBox="0 0 24 24" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
    </a>
  </div>

  <!-- Stack carousel -->
  <div class="sa-stack" id="saStack">
    <?php
    $products = wc_get_products(array(
        'status'   => 'publish',
        'limit'    => 4,
        'orderby'  => 'date',
        'order'    => 'DESC',
    ));

    if (empty($products)) {
        $products = wc_get_products(array(
            'status' => 'publish',
            'limit'  => 4,
        ));
    }

    foreach ($products as $i => $product) :
        $image_url = wp_get_attachment_url($product->get_image_id());
        if (!$image_url) {
            $image_url = wc_placeholder_img_src('large');
        }
        $artist = $product->get_attribute('artiste');
        if (!$artist) {
            $artist = $product->get_attribute('artist');
        }
        $dimensions_attr = $product->get_attribute('dimensions');
        if (!$dimensions_attr) {
            $dimensions_attr = '';
        }
    ?>
    <div class="sa-card" data-index="<?php echo $i; ?>">
      <div class="sa-card-img">
        <img src="<?php echo esc_url($image_url); ?>"
             alt="<?php echo esc_attr($product->get_name() . ($artist ? ' par ' . $artist : '') . ' — peinture originale'); ?>"
             loading="lazy" decoding="async">
        <div class="sa-card-overlay"></div>
        <div class="sa-card-hover-btn">Voir l'œuvre →</div>
        <?php if (!empty(sa_engage_get('hearts_enabled')) && sa_engage_get('hearts_enabled') !== '0') : ?>
          <button class="sa-heart" data-product-id="<?php echo esc_attr($product->get_id()); ?>" aria-label="Ajouter aux favoris">
            <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
          </button>
        <?php endif; ?>
      </div>
      <div class="sa-card-body">
        <?php if ($artist) : ?>
          <div class="sa-card-artist"><?php echo esc_html($artist); ?></div>
        <?php endif; ?>
        <div class="sa-card-title"><?php echo esc_html($product->get_name()); ?></div>
        <?php if ($dimensions_attr) : ?>
          <div class="sa-card-dims"><?php echo esc_html($dimensions_attr); ?></div>
        <?php endif; ?>
      </div>
      <div class="sa-card-footer">
        <div class="sa-card-price"><?php echo $product->get_price_html(); ?></div>
        <a href="<?php echo esc_url($product->get_permalink()); ?>" class="sa-card-cta">Voir</a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="sa-sel-dots">
    <?php foreach ($products as $i => $p) : ?>
      <div class="sa-sel-dot<?php echo $i === 0 ? ' active' : ''; ?>" data-index="<?php echo $i; ?>"></div>
    <?php endforeach; ?>
  </div>

  <?php
  $sa_sticky = sa_engage_get();
  if (!empty($sa_sticky['sticky_enabled']) && $sa_sticky['sticky_enabled'] !== '0') :
  ?>
  <div class="sa-sticky-bar" id="saStickyBar">
    <span class="sa-sticky-item"><?php echo esc_html($sa_sticky['sticky_arg_1']); ?></span>
    <span class="sa-sticky-sep"></span>
    <span class="sa-sticky-item"><?php echo esc_html($sa_sticky['sticky_arg_2']); ?></span>
    <span class="sa-sticky-sep"></span>
    <span class="sa-sticky-item"><?php echo esc_html($sa_sticky['sticky_arg_3']); ?></span>
    <a href="<?php echo esc_url(home_url($sa_sticky['sticky_url'])); ?>" class="sa-sticky-cta">
      <?php echo esc_html($sa_sticky['sticky_label']); ?>
      <svg viewBox="0 0 24 24" width="14" height="14" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
    </a>
  </div>
  <?php endif; ?>
</section>

<!-- ═══════ 3. POURQUOI SAISONART ═══════ -->
<section class="sa-pourquoi" id="pourquoi">
  <div class="sa-pq-glow"></div>
  <div class="sa-pq-inner">
    <div class="sa-pq-left reveal">
      <div class="sa-pq-eyebrow">Notre engagement</div>
      <h2 class="sa-pq-h2">L'art original<br><em>accessible à tous</em></h2>
      <p class="sa-pq-desc">Chaque tableau proposé sur SaisonArt est sélectionné pour son authenticité et sa qualité — expertisé, documenté, livré avec son certificat.</p>

      <div class="sa-pq-features">
        <div class="sa-pq-feat">
          <div class="sa-pq-feat-icon">
            <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <div class="sa-pq-feat-text">
            <h4>Authenticité garantie</h4>
            <p>Chaque œuvre est expertisée et livrée avec son certificat d'authenticité.</p>
          </div>
        </div>
        <div class="sa-pq-feat">
          <div class="sa-pq-feat-icon">
            <svg viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
          </div>
          <div class="sa-pq-feat-text">
            <h4>Livraison assurée sous 48h</h4>
            <p>Expédition soignée, assurée et tracée — partout en France et en Europe.</p>
          </div>
        </div>
        <div class="sa-pq-feat">
          <div class="sa-pq-feat-icon">
            <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </div>
          <div class="sa-pq-feat-text">
            <h4>Retour sous 14 jours</h4>
            <p>Pas convaincu à la réception ? Retour simple et gratuit, sans question.</p>
          </div>
        </div>
      </div>

      <a href="<?php echo esc_url(home_url('/boutique/')); ?>" class="sa-pq-link">
        Découvrir nos œuvres
        <svg viewBox="0 0 24 24" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </a>
    </div>

    <div class="sa-pq-stats reveal reveal-delay-2">
      <?php
      $stats = array(
          array('num' => $count ?: '6', 'sup' => '+',  'label' => 'Œuvres originales<br>disponibles'),
          array('num' => '48',          'sup' => 'h',  'label' => 'Délai de livraison<br>moyen'),
          array('num' => '14',          'sup' => 'j',  'label' => 'Retour sans frais<br>garanti'),
          array('num' => '100',         'sup' => '%',  'label' => 'Œuvres certifiées<br>authentiques'),
      );
      foreach ($stats as $stat) : ?>
        <div class="sa-pq-stat">
          <span class="sa-pq-stat-num"><?php echo esc_html($stat['num']); ?><sup><?php echo esc_html($stat['sup']); ?></sup></span>
          <div class="sa-pq-stat-label"><?php echo $stat['label']; ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══════ 4. MAGAZINE ═══════ -->
<section class="sa-mag-c" id="magazine">
  <div class="sa-mag-header reveal">
    <div>
      <div class="sa-mag-eyebrow">Culture & Art</div>
      <h2 class="sa-mag-h2">Le Magazine<br><em>SaisonArt</em></h2>
    </div>
    <a href="<?php echo esc_url(home_url('/news/')); ?>" class="sa-mag-link">
      Tous les articles
      <svg viewBox="0 0 24 24" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
    </a>
  </div>

  <?php
  $mag_query = new WP_Query(array(
      'posts_per_page' => 3,
      'post_status'    => 'publish',
      'orderby'        => 'date',
      'order'          => 'DESC',
  ));

  if ($mag_query->have_posts()) : ?>
  <div class="sa-mag-grid reveal reveal-delay-1">
    <?php
    $post_index = 0;
    while ($mag_query->have_posts()) : $mag_query->the_post();
        $cats = get_the_category();
        $cat_name = !empty($cats) ? $cats[0]->name : 'Article';
        $thumb = get_the_post_thumbnail_url(get_the_ID(), 'large');

        if ($post_index === 0) : // Featured article ?>
          <article class="sa-mag-featured">
            <a href="<?php the_permalink(); ?>" style="display:block;position:absolute;inset:0">
              <?php if ($thumb) : ?>
                <img src="<?php echo esc_url($thumb); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" decoding="async">
              <?php endif; ?>
              <div class="sa-mag-featured-overlay"></div>
              <div class="sa-mag-featured-content">
                <span class="sa-mag-tag"><?php echo esc_html($cat_name); ?></span>
                <h3><?php the_title(); ?></h3>
                <div class="sa-mag-featured-meta">
                  <span>
                    <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <?php echo get_the_date(); ?>
                  </span>
                </div>
              </div>
            </a>
          </article>

        <?php else : // Secondary articles ?>
          <article class="sa-mag-secondary">
            <a href="<?php the_permalink(); ?>" style="display:flex;gap:0;text-decoration:none;color:inherit;width:100%">
              <div class="sa-mag-secondary-img">
                <?php if ($thumb) : ?>
                  <img src="<?php echo esc_url($thumb); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" decoding="async">
                <?php endif; ?>
              </div>
              <div class="sa-mag-secondary-body">
                <span class="sa-mag-tag"><?php echo esc_html($cat_name); ?></span>
                <h4><?php the_title(); ?></h4>
                <p><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                <div class="sa-mag-secondary-meta"><?php echo get_the_date(); ?></div>
              </div>
            </a>
          </article>
        <?php endif;

        $post_index++;
    endwhile;
    wp_reset_postdata();
    ?>
  </div>
  <?php endif; ?>
</section>

<!-- ═══════ 5. NEWSLETTER ═══════ -->
<section class="sa-newsletter" id="newsletter">
  <div class="sa-nl-glow"></div>
  <div class="sa-nl-inner reveal">
    <div class="sa-nl-icon">
      <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
    </div>
    <h2 class="sa-nl-h2">Restez dans<br><em style="font-style:italic">l'atelier</em></h2>
    <p class="sa-nl-desc">Nouvelles acquisitions, portraits d'artistes, conseils de collectionneurs — reçus chaque mois dans votre boîte.</p>
    <div class="sa-nl-form">
      <input type="email" class="sa-nl-input" placeholder="votre@email.fr">
      <button class="sa-nl-btn">S'abonner</button>
    </div>
    <p class="sa-nl-note">Pas de spam. Désabonnement en un clic. Discrétion garantie.</p>
  </div>
</section>

</main>

<?php get_footer(); ?>
