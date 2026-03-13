<?php
/**
 * SaisonArt Child Theme functions.
 */

/* --------------------------------------------------------------------------
   Engagement admin
   -------------------------------------------------------------------------- */
$sa_inc = get_stylesheet_directory() . '/inc/';
if (file_exists($sa_inc . 'engagement-admin.php')) {
    require_once $sa_inc . 'engagement-admin.php';
}
if (file_exists($sa_inc . 'resend-handler.php')) {
    require_once $sa_inc . 'resend-handler.php';
}
// Fallback: ensure sa_engage_get() always exists (prevents fatal in footer/front-page)
if (!function_exists('sa_engage_get')) {
    function sa_engage_get($key = null) {
        return $key ? '' : array();
    }
}


/* --------------------------------------------------------------------------
   Enqueue styles & scripts
   -------------------------------------------------------------------------- */
add_action('wp_enqueue_scripts', 'saisonart_enqueue_styles', 20);
function saisonart_enqueue_styles() {
    $version = wp_get_theme()->get('Version');

    // Google Fonts
    wp_enqueue_style('saisonart-fonts', 'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=DM+Sans:wght@300;400;500;600&display=swap', array(), null);

    // Parent theme
    wp_enqueue_style('storefront-style', get_template_directory_uri() . '/style.css');

    // Child theme
    wp_enqueue_style('saisonart-style', get_stylesheet_uri(), array('storefront-style'), $version);
    wp_enqueue_style('saisonart-main', get_stylesheet_directory_uri() . '/assets/css/main.css', array('saisonart-style'), $version);

    // Homepage-only dark styles
    if (is_front_page()) {
        wp_enqueue_style('saisonart-frontpage', get_stylesheet_directory_uri() . '/assets/css/front-page.css', array('saisonart-main'), $version);
    }

    // Conseil page styles
    if (is_page_template('page-conseil.php') || is_page('conseil')) {
        wp_enqueue_style('saisonart-conseil', get_stylesheet_directory_uri() . '/assets/css/conseil.css', array('saisonart-main'), $version);
        wp_enqueue_script('saisonart-conseil', get_stylesheet_directory_uri() . '/assets/js/conseil.js', array(), $version, true);
    }

    // Blog / Magazine styles
    if (is_home() || is_singular('post') || (is_archive() && !is_post_type_archive('product') && !is_tax('product_cat') && !is_tax('product_tag'))) {
        wp_enqueue_style('saisonart-blog', get_stylesheet_directory_uri() . '/assets/css/blog.css', array('saisonart-main'), $version);
    }

    // Contact page styles
    if (is_page_template('page-contact-us.php') || is_page('contact-us')) {
        wp_enqueue_style('saisonart-contact', get_stylesheet_directory_uri() . '/assets/css/contact.css', array('saisonart-main'), $version);
        wp_enqueue_script('saisonart-contact', get_stylesheet_directory_uri() . '/assets/js/contact.js', array(), $version, true);
    }

    // Boutique & product page styles
    if (is_shop() || is_product_category() || is_product_tag() || is_product()) {
        wp_enqueue_style('saisonart-boutique', get_stylesheet_directory_uri() . '/assets/css/boutique.css', array('saisonart-main'), $version);
        wp_enqueue_script('saisonart-boutique', get_stylesheet_directory_uri() . '/assets/js/boutique.js', array(), $version, true);
    }

    // Checkout funnel styles (cart, checkout, my-account)
    if (is_cart() || is_checkout() || is_account_page()) {
        wp_enqueue_style('saisonart-checkout', get_stylesheet_directory_uri() . '/assets/css/checkout.css', array('saisonart-main'), $version);
    }

    // Engagement styles
    wp_enqueue_style('saisonart-engagement', get_stylesheet_directory_uri() . '/assets/css/engagement.css', array('saisonart-main'), $version);

    // JS
    wp_enqueue_script('saisonart-main', get_stylesheet_directory_uri() . '/assets/js/main.js', array('jquery'), $version, true);

    // Engagement JS + config injection
    wp_enqueue_script('saisonart-engagement', get_stylesheet_directory_uri() . '/assets/js/engagement.js', array('jquery'), $version, true);
    $config = sa_engage_get();
    // Add AJAX URL and nonce (remove sensitive keys from frontend)
    $config['ajax_url'] = admin_url('admin-ajax.php');
    $config['nonce']    = wp_create_nonce('sa_engage_nonce');
    unset($config['resend_api_key'], $config['resend_from'], $config['resend_notify_email']);
    wp_localize_script('saisonart-engagement', 'saEngageConfig', $config);
}

/* --------------------------------------------------------------------------
   Remove WooCommerce sidebar on shop pages (full-width layout)
   -------------------------------------------------------------------------- */
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

/* --------------------------------------------------------------------------
   Theme setup
   -------------------------------------------------------------------------- */
add_action('after_setup_theme', 'saisonart_setup');
function saisonart_setup() {
    add_theme_support('title-tag');
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    register_nav_menus(array(
        'sa-primary' => 'Navigation principale SaisonArt',
    ));
}

/* --------------------------------------------------------------------------
   Checkout funnel: trust badges
   -------------------------------------------------------------------------- */
add_action('woocommerce_after_cart', 'saisonart_checkout_trust');
add_action('woocommerce_review_order_after_submit', 'saisonart_checkout_trust');
function saisonart_checkout_trust() {
    echo '<div class="sa-checkout-trust">';
    echo '<div class="sa-checkout-trust-item"><svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg><span>Paiement sécurisé</span></div>';
    echo '<div class="sa-checkout-trust-item"><svg viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg><span>Livraison assurée 48h</span></div>';
    echo '<div class="sa-checkout-trust-item"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg><span>Retour 14 jours</span></div>';
    echo '</div>';
}

/* --------------------------------------------------------------------------
   SEO: Custom title tags per page type
   -------------------------------------------------------------------------- */
add_filter('document_title_separator', function () {
    return '|';
});

add_filter('document_title_parts', 'saisonart_custom_title');
function saisonart_custom_title($title_parts) {
    if (is_front_page()) {
        $title_parts['title'] = 'SaisonArt — Galerie d\'art en ligne | Peintures originales';
        unset($title_parts['tagline']);
    } elseif (function_exists('is_shop') && is_shop()) {
        $title_parts['title'] = 'Boutique | Tableaux originaux de maîtres français';
    } elseif (is_singular('product')) {
        $product = wc_get_product(get_the_ID());
        if ($product) {
            $artist = $product->get_attribute('artiste') ?: $product->get_attribute('artist');
            $title_parts['title'] = $product->get_name() . ($artist ? ' par ' . $artist : '');
        }
    } elseif (is_home()) {
        $title_parts['title'] = 'Magazine | Articles sur l\'art et les collectionneurs';
    } elseif (is_singular('post')) {
        $title_parts['title'] = get_the_title();
        $title_parts['site'] = 'Magazine SaisonArt';
    } elseif (is_product_category()) {
        $term = get_queried_object();
        $title_parts['title'] = $term->name . ' | Boutique';
    }
    return $title_parts;
}

/* --------------------------------------------------------------------------
   SEO: Meta descriptions
   -------------------------------------------------------------------------- */
add_action('wp_head', 'saisonart_meta_description', 1);
function saisonart_meta_description() {
    $desc = '';

    if (is_front_page()) {
        $desc = 'Galerie d\'art en ligne spécialisée dans les peintures originales de maîtres français des XIXe et XXe siècles. Œuvres expertisées, certifiées, livrées sous 48 h. Retour 14 jours.';
    } elseif (function_exists('is_shop') && is_shop()) {
        $desc = 'Découvrez notre collection de tableaux originaux : paysages impressionnistes, portraits, natures mortes. Chaque œuvre certifiée authentique, livraison assurée sous 48 h.';
    } elseif (is_singular('product')) {
        $product = wc_get_product(get_the_ID());
        if ($product) {
            $artist = $product->get_attribute('artiste') ?: $product->get_attribute('artist');
            $dims   = $product->get_attribute('dimensions');
            $desc   = wp_trim_words(wp_strip_all_tags($product->get_short_description()), 20);
            if ($artist) {
                $desc .= ' Par ' . $artist . '.';
            }
            if ($dims) {
                $desc .= ' ' . $dims . '.';
            }
            $desc .= ' Œuvre certifiée authentique, livraison 48 h.';
        }
    } elseif (is_home()) {
        $desc = 'Le Magazine SaisonArt : articles sur l\'impressionnisme, l\'histoire de l\'art, les conseils pour collectionneurs. Culture et art accessibles.';
    } elseif (is_singular('post')) {
        $desc = wp_trim_words(wp_strip_all_tags(get_the_excerpt()), 25);
    } elseif (is_product_category()) {
        $term = get_queried_object();
        $desc = $term->name . ' — Découvrez notre sélection de tableaux. Œuvres certifiées, livraison rapide.';
    }

    if ($desc) {
        echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
    }
}

/* --------------------------------------------------------------------------
   SEO: Canonical URLs
   -------------------------------------------------------------------------- */
add_action('wp_head', 'saisonart_canonical', 1);
function saisonart_canonical() {
    if (is_front_page()) {
        echo '<link rel="canonical" href="' . esc_url(home_url('/')) . '">' . "\n";
    } elseif (is_singular()) {
        echo '<link rel="canonical" href="' . esc_url(get_permalink()) . '">' . "\n";
    } elseif (function_exists('is_shop') && is_shop()) {
        echo '<link rel="canonical" href="' . esc_url(get_permalink(wc_get_page_id('shop'))) . '">' . "\n";
    }
}

/* --------------------------------------------------------------------------
   SEO: Open Graph tags
   -------------------------------------------------------------------------- */
add_action('wp_head', 'saisonart_open_graph', 2);
function saisonart_open_graph() {
    $og = array(
        'og:site_name' => 'SaisonArt',
        'og:locale'    => 'fr_FR',
    );

    if (is_front_page()) {
        $og['og:type']        = 'website';
        $og['og:title']       = 'SaisonArt — Galerie d\'art en ligne';
        $og['og:description'] = 'Peintures originales de maîtres français XIXe-XXe siècle. Expertisées, certifiées, livrées sous 48 h.';
        $og['og:url']         = home_url('/');
        $og['og:image']       = get_stylesheet_directory_uri() . '/assets/images/og-saisonart.jpg';
    } elseif (is_singular('product')) {
        $product = wc_get_product(get_the_ID());
        if ($product) {
            $og['og:type']        = 'product';
            $og['og:title']       = $product->get_name() . ' | SaisonArt';
            $og['og:description'] = wp_trim_words(wp_strip_all_tags($product->get_short_description()), 20);
            $og['og:url']         = get_permalink();
            $image_id = $product->get_image_id();
            if ($image_id) {
                $og['og:image'] = wp_get_attachment_url($image_id);
            }
            $og['product:price:amount']   = $product->get_price();
            $og['product:price:currency'] = 'EUR';
        }
    } elseif (is_singular('post')) {
        $og['og:type']        = 'article';
        $og['og:title']       = get_the_title() . ' | Magazine SaisonArt';
        $og['og:description'] = wp_trim_words(wp_strip_all_tags(get_the_excerpt()), 20);
        $og['og:url']         = get_permalink();
        $thumb = get_the_post_thumbnail_url(get_the_ID(), 'large');
        if ($thumb) {
            $og['og:image'] = $thumb;
        }
        $og['article:published_time'] = get_the_date('c');
        $og['article:section']        = 'Art';
    } elseif (function_exists('is_shop') && is_shop()) {
        $og['og:type']        = 'website';
        $og['og:title']       = 'Boutique | SaisonArt';
        $og['og:description'] = 'Tableaux originaux de maîtres français XIXe-XXe siècle. Œuvres expertisées, certifiées.';
        $og['og:url']         = get_permalink(wc_get_page_id('shop'));
        $og['og:image']       = get_stylesheet_directory_uri() . '/assets/images/og-saisonart.jpg';
    }

    foreach ($og as $property => $content) {
        if ($content) {
            echo '<meta property="' . esc_attr($property) . '" content="' . esc_attr($content) . '">' . "\n";
        }
    }
}

/* --------------------------------------------------------------------------
   SEO: Twitter Card tags (full)
   -------------------------------------------------------------------------- */
add_action('wp_head', 'saisonart_twitter_cards', 3);
function saisonart_twitter_cards() {
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";

    if (is_front_page()) {
        $title = 'SaisonArt — Galerie d\'art en ligne';
        $desc  = 'Peintures originales de maîtres français XIXe-XXe siècle. Expertisées, certifiées, livrées sous 48 h.';
        $image = get_stylesheet_directory_uri() . '/assets/images/og-saisonart.jpg';
    } elseif (is_singular('product')) {
        $product = wc_get_product(get_the_ID());
        $title = $product ? $product->get_name() . ' | SaisonArt' : get_the_title();
        $desc  = $product ? wp_trim_words(wp_strip_all_tags($product->get_short_description()), 20) : '';
        $image_id = $product ? $product->get_image_id() : 0;
        $image = $image_id ? wp_get_attachment_url($image_id) : '';
    } elseif (is_singular('post')) {
        $title = get_the_title() . ' | Magazine SaisonArt';
        $desc  = wp_trim_words(wp_strip_all_tags(get_the_excerpt()), 20);
        $image = get_the_post_thumbnail_url(get_the_ID(), 'large') ?: '';
    } else {
        $title = wp_get_document_title();
        $desc  = get_bloginfo('description');
        $image = get_stylesheet_directory_uri() . '/assets/images/og-saisonart.jpg';
    }

    if (!empty($title)) echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    if (!empty($desc))  echo '<meta name="twitter:description" content="' . esc_attr($desc) . '">' . "\n";
    if (!empty($image)) echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";
}

/* --------------------------------------------------------------------------
   SEO: noindex on checkout funnel & theme-color
   -------------------------------------------------------------------------- */
add_action('wp_head', 'saisonart_meta_extras', 1);
function saisonart_meta_extras() {
    // Theme color for mobile browsers
    echo '<meta name="theme-color" content="#0d1610">' . "\n";

    // noindex checkout funnel pages
    if (function_exists('is_cart') && (is_cart() || is_checkout() || is_account_page())) {
        echo '<meta name="robots" content="noindex, nofollow">' . "\n";
    }
}

/* --------------------------------------------------------------------------
   SEO: JSON-LD Structured Data
   -------------------------------------------------------------------------- */
add_action('wp_head', 'saisonart_jsonld', 5);
function saisonart_jsonld() {
    $schemas = array();

    // Organization — always present
    $schemas[] = array(
        '@type'       => 'Organization',
        '@id'         => home_url('/#organization'),
        'name'        => 'SaisonArt',
        'url'         => home_url('/'),
        'logo'        => array(
            '@type' => 'ImageObject',
            'url'   => get_stylesheet_directory_uri() . '/assets/images/logo-saisonart-dark.png',
            'width' => 200,
            'height' => 50,
        ),
        'description' => 'Galerie d\'art en ligne spécialisée dans les peintures originales de maîtres français des XIXe et XXe siècles.',
        'contactPoint' => array(
            '@type'             => 'ContactPoint',
            'contactType'       => 'customer service',
            'availableLanguage' => 'French',
        ),
    );

    // WebSite with SearchAction — always present
    $schemas[] = array(
        '@type'           => 'WebSite',
        '@id'             => home_url('/#website'),
        'name'            => 'SaisonArt',
        'url'             => home_url('/'),
        'description'     => 'L\'art original accessible — choisi, raconté, livré chez vous.',
        'inLanguage'      => 'fr-FR',
        'publisher'       => array('@id' => home_url('/#organization')),
        'potentialAction'  => array(
            '@type'       => 'SearchAction',
            'target'      => home_url('/?s={search_term_string}'),
            'query-input' => 'required name=search_term_string',
        ),
    );

    // Product + VisualArtwork — on single product pages
    if (is_singular('product')) {
        $product   = wc_get_product(get_the_ID());
        if ($product) {
            $artist    = $product->get_attribute('artiste') ?: $product->get_attribute('artist');
            $dims      = $product->get_attribute('dimensions');
            $image_id  = $product->get_image_id();
            $image_url = $image_id ? wp_get_attachment_url($image_id) : '';

            $product_schema = array(
                '@type'       => array('Product', 'VisualArtwork'),
                'name'        => $product->get_name(),
                'description' => wp_strip_all_tags($product->get_short_description()),
                'image'       => $image_url,
                'url'         => get_permalink(),
                'sku'         => $product->get_sku(),
                'category'    => 'Peinture originale',
                'artMedium'   => 'Peinture',
                'offers'      => array(
                    '@type'           => 'Offer',
                    'url'             => get_permalink(),
                    'priceCurrency'   => 'EUR',
                    'price'           => $product->get_price(),
                    'availability'    => $product->is_in_stock()
                        ? 'https://schema.org/InStock'
                        : 'https://schema.org/OutOfStock',
                    'seller'          => array('@id' => home_url('/#organization')),
                    'itemCondition'   => 'https://schema.org/UsedCondition',
                    'shippingDetails' => array(
                        '@type'               => 'OfferShippingDetails',
                        'shippingDestination'  => array(
                            '@type'          => 'DefinedRegion',
                            'addressCountry' => 'FR',
                        ),
                        'deliveryTime' => array(
                            '@type'        => 'ShippingDeliveryTime',
                            'handlingTime' => array(
                                '@type'    => 'QuantitativeValue',
                                'minValue' => 1,
                                'maxValue' => 2,
                                'unitCode' => 'DAY',
                            ),
                        ),
                    ),
                ),
            );

            if ($artist) {
                $product_schema['brand'] = array('@type' => 'Brand', 'name' => $artist);
                $product_schema['artist'] = array('@type' => 'Person', 'name' => $artist);
            }
            if ($dims) {
                $product_schema['size'] = $dims;
            }

            $schemas[] = $product_schema;
        }
    }

    // BreadcrumbList — everywhere except homepage
    if (!is_front_page()) {
        $breadcrumbs = array(
            array('name' => 'Accueil', 'url' => home_url('/')),
        );

        if ((function_exists('is_shop') && is_shop()) || is_product_category() || is_singular('product')) {
            $breadcrumbs[] = array('name' => 'Boutique', 'url' => get_permalink(wc_get_page_id('shop')));
        }
        if (is_product_category()) {
            $term = get_queried_object();
            $breadcrumbs[] = array('name' => $term->name, 'url' => get_term_link($term));
        }
        if (is_singular('product')) {
            $breadcrumbs[] = array('name' => get_the_title(), 'url' => get_permalink());
        }
        if (is_home() || is_singular('post') || is_category()) {
            $breadcrumbs[] = array('name' => 'Magazine', 'url' => home_url('/news/'));
        }
        if (is_singular('post')) {
            $breadcrumbs[] = array('name' => get_the_title(), 'url' => get_permalink());
        }

        $items = array();
        foreach ($breadcrumbs as $i => $crumb) {
            $items[] = array(
                '@type'    => 'ListItem',
                'position' => $i + 1,
                'name'     => $crumb['name'],
                'item'     => $crumb['url'],
            );
        }

        $schemas[] = array(
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $items,
        );
    }

    // Article — on blog posts
    if (is_singular('post')) {
        $schemas[] = array(
            '@type'            => 'Article',
            'headline'         => get_the_title(),
            'description'      => wp_trim_words(wp_strip_all_tags(get_the_excerpt()), 25),
            'image'            => get_the_post_thumbnail_url(get_the_ID(), 'large'),
            'datePublished'    => get_the_date('c'),
            'dateModified'     => get_the_modified_date('c'),
            'author'           => array(
                '@type' => 'Organization',
                'name'  => 'SaisonArt',
                '@id'   => home_url('/#organization'),
            ),
            'publisher'        => array('@id' => home_url('/#organization')),
            'mainEntityOfPage' => get_permalink(),
        );
    }

    // Output
    if (!empty($schemas)) {
        $output = array(
            '@context' => 'https://schema.org',
            '@graph'   => $schemas,
        );
        echo '<script type="application/ld+json">' . "\n";
        echo wp_json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        echo "\n</script>\n";
    }
}

/* --------------------------------------------------------------------------
   SEO: Enriched alt text for product images
   -------------------------------------------------------------------------- */
add_filter('wp_get_attachment_image_attributes', 'saisonart_product_alt', 10, 3);
function saisonart_product_alt($attr, $attachment, $size) {
    if (!is_singular('product') && !(function_exists('is_shop') && is_shop())) {
        return $attr;
    }
    $product_id = get_the_ID();
    if (!$product_id || get_post_type($product_id) !== 'product') {
        return $attr;
    }
    $product = wc_get_product($product_id);
    if (!$product) {
        return $attr;
    }
    $artist = $product->get_attribute('artiste') ?: $product->get_attribute('artist');
    $alt = $product->get_name();
    if ($artist) {
        $alt .= ' par ' . $artist;
    }
    $alt .= ' — peinture originale';
    $attr['alt'] = $alt;
    return $attr;
}

/* --------------------------------------------------------------------------
   SEO: Enable WebP upload support
   -------------------------------------------------------------------------- */
add_filter('upload_mimes', function ($mimes) {
    $mimes['webp'] = 'image/webp';
    return $mimes;
});

/* --------------------------------------------------------------------------
   Performance: Defer main JS
   -------------------------------------------------------------------------- */
add_filter('script_loader_tag', 'saisonart_defer_js', 10, 3);
function saisonart_defer_js($tag, $handle, $src) {
    if ($handle === 'saisonart-main' || $handle === 'saisonart-engagement') {
        return str_replace(' src', ' defer src', $tag);
    }
    return $tag;
}

/* --------------------------------------------------------------------------
   WooCommerce: product columns & per page
   -------------------------------------------------------------------------- */
add_filter('loop_shop_columns', function () {
    return 3;
});

add_filter('loop_shop_per_page', function () {
    return 12;
});

/* --------------------------------------------------------------------------
   WooCommerce: AJAX cart fragment for custom header badge
   -------------------------------------------------------------------------- */
add_filter('woocommerce_add_to_cart_fragments', 'saisonart_cart_fragment');
function saisonart_cart_fragment($fragments) {
    $count = WC()->cart->get_cart_contents_count();
    if ($count > 0) {
        $fragments['.sa-header-cart-badge'] = '<span class="sa-header-cart-badge">' . esc_html($count) . '</span>';
    } else {
        $fragments['.sa-header-cart-badge'] = '<span class="sa-header-cart-badge" style="display:none"></span>';
    }
    return $fragments;
}

/* --------------------------------------------------------------------------
   SEO: Custom robots.txt
   -------------------------------------------------------------------------- */
add_filter('robots_txt', 'saisonart_robots_txt', 10, 2);
function saisonart_robots_txt($output, $public) {
    $output  = "User-agent: *\n";
    $output .= "Allow: /\n";
    $output .= "Disallow: /cart/\n";
    $output .= "Disallow: /checkout/\n";
    $output .= "Disallow: /my-account/\n";
    $output .= "Disallow: /wp-admin/\n";
    $output .= "Disallow: /*?add-to-cart=*\n";
    $output .= "Disallow: /*?orderby=*\n\n";
    $output .= "Sitemap: " . home_url('/wp-sitemap.xml') . "\n";
    return $output;
}

/* --------------------------------------------------------------------------
   SEO: Include products in WordPress native sitemap
   -------------------------------------------------------------------------- */
add_filter('wp_sitemaps_post_types', 'saisonart_sitemap_post_types');
function saisonart_sitemap_post_types($post_types) {
    if (post_type_exists('product')) {
        $post_types['product'] = get_post_type_object('product');
    }
    return $post_types;
}

/* --------------------------------------------------------------------------
   Engagement: heart button on WooCommerce product cards
   -------------------------------------------------------------------------- */
add_action('woocommerce_before_shop_loop_item_title', 'saisonart_heart_button', 15);
function saisonart_heart_button() {
    $s = sa_engage_get();
    if (empty($s['hearts_enabled']) || $s['hearts_enabled'] === '0') return;
    global $product;
    if (!$product) return;
    echo '<button class="sa-heart" data-product-id="' . esc_attr($product->get_id()) . '" aria-label="Ajouter aux favoris">'
       . '<svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>'
       . '</button>';
}

/* --------------------------------------------------------------------------
   Remove default Storefront header/footer actions we override
   -------------------------------------------------------------------------- */
add_action('init', 'saisonart_remove_storefront_actions');
function saisonart_remove_storefront_actions() {
    // Remove ALL Storefront header hooks (we use custom header.php)
    remove_action('storefront_header', 'storefront_header_container', 0);
    remove_action('storefront_header', 'storefront_skip_links', 5);
    remove_action('storefront_header', 'storefront_site_branding', 20);
    remove_action('storefront_header', 'storefront_product_search', 25);
    remove_action('storefront_header', 'storefront_secondary_navigation', 30);
    remove_action('storefront_header', 'storefront_header_container_close', 41);
    remove_action('storefront_header', 'storefront_primary_navigation_wrapper', 42);
    remove_action('storefront_header', 'storefront_primary_navigation', 50);
    remove_action('storefront_header', 'storefront_primary_navigation_wrapper_close', 68);
    // Remove ALL Storefront footer hooks (we use custom footer.php)
    remove_action('storefront_footer', 'storefront_footer_widgets', 10);
    remove_action('storefront_footer', 'storefront_credit', 20);

    // Remove Storefront inline customizer CSS that injects background-color on body
    remove_action('wp_enqueue_scripts', 'storefront_add_customizer_css', 130);
}

/* --------------------------------------------------------------------------
   WooCommerce: Force classic shortcode cart/checkout (not block-based)
   WC 8+ defaults to React blocks which bypass our PHP template overrides.
   This replaces block rendering with classic shortcodes so cart.php works.
   -------------------------------------------------------------------------- */
// 1) Replace block HTML with classic shortcode output
add_filter('render_block', 'saisonart_classic_cart_checkout', 10, 2);
function saisonart_classic_cart_checkout($block_content, $block) {
    static $rendering = false;
    if ($rendering) {
        return '';
    }
    if ($block['blockName'] === 'woocommerce/cart') {
        $rendering = true;
        $out = do_shortcode('[woocommerce_cart]');
        $rendering = false;
        return $out;
    }
    if ($block['blockName'] === 'woocommerce/checkout') {
        $rendering = true;
        $out = do_shortcode('[woocommerce_checkout]');
        $rendering = false;
        return $out;
    }
    return $block_content;
}

// 2) Dequeue WC block scripts/styles — they crash when the React blocks are replaced
add_action('wp_enqueue_scripts', 'saisonart_dequeue_wc_blocks', 99);
function saisonart_dequeue_wc_blocks() {
    if (!(function_exists('is_cart') && is_cart()) && !(function_exists('is_checkout') && is_checkout())) {
        return;
    }
    // Remove all WC block scripts by pattern
    global $wp_scripts, $wp_styles;
    if (isset($wp_scripts->registered)) {
        foreach ($wp_scripts->registered as $handle => $script) {
            if (
                strpos($handle, 'wc-blocks') !== false ||
                strpos($handle, 'wc-cart') !== false ||
                strpos($handle, 'wc-checkout') !== false ||
                strpos($handle, 'WCPAY_BLOCKS') !== false ||
                strpos($handle, 'wc-cart-checkout') !== false
            ) {
                wp_dequeue_script($handle);
                wp_deregister_script($handle);
            }
        }
    }
    // Remove block styles
    if (isset($wp_styles->registered)) {
        foreach ($wp_styles->registered as $handle => $style) {
            if (
                strpos($handle, 'wc-blocks') !== false ||
                strpos($handle, 'wc-cart') !== false ||
                strpos($handle, 'wc-checkout') !== false
            ) {
                wp_dequeue_style($handle);
                wp_deregister_style($handle);
            }
        }
    }
}

/* --------------------------------------------------------------------------
   Product page V2 test — add ?v2 to any product URL to use single-product-v2.php
   -------------------------------------------------------------------------- */
add_filter('template_include', function ($template) {
    if (is_product() && isset($_GET['v2'])) {
        $v2 = get_stylesheet_directory() . '/woocommerce/single-product-v2.php';
        if (file_exists($v2)) {
            return $v2;
        }
    }
    return $template;
}, 99);

/* --------------------------------------------------------------------------
   Product V2 — CSS enqueue
   -------------------------------------------------------------------------- */
add_action('wp_enqueue_scripts', function () {
    if (is_product() && isset($_GET['v2'])) {
        wp_enqueue_style(
            'sa-product-v2',
            get_stylesheet_directory_uri() . '/assets/css/product-v2.css',
            array(),
            filemtime(get_stylesheet_directory() . '/assets/css/product-v2.css')
        );
    }
});

/* --------------------------------------------------------------------------
   Product V2 — WooCommerce hooks (only active with ?v2)
   -------------------------------------------------------------------------- */
add_action('init', function () {
    if (!isset($_GET['v2'])) return;

    // ── Reorder summary hooks ──
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);

    add_action('woocommerce_single_product_summary', 'sav2_sidebar_eyebrow', 3);
    add_action('woocommerce_single_product_summary', 'sav2_sidebar_meta_chips', 15);
    add_action('woocommerce_single_product_summary', 'sav2_sidebar_price_info', 21);
    add_action('woocommerce_single_product_summary', 'sav2_sidebar_trust', 35);

    add_action('woocommerce_after_add_to_cart_button', 'sav2_contact_button');
    add_action('woocommerce_before_single_product_summary', 'sav2_gallery_badge', 5);
    add_action('woocommerce_after_single_product_summary', 'sav2_custom_sections', 5);
    add_action('woocommerce_after_single_product', 'sav2_mobile_bar', 10);

    // Utilities
    add_filter('woocommerce_product_description_heading', '__return_empty_string');
    add_filter('woocommerce_product_additional_information_heading', '__return_empty_string');
    // Remove "Additional Information" tab — attributes shown as chips in sidebar
    add_filter('woocommerce_product_tabs', function ($tabs) {
        unset($tabs['additional_information']);
        return $tabs;
    });
    add_filter('woocommerce_quantity_input_args', function ($args, $product) {
        if (is_product() && $product && $product->get_stock_quantity() <= 1) {
            $args['min_value'] = 1;
            $args['max_value'] = 1;
            $args['input_value'] = 1;
        }
        return $args;
    }, 10, 2);

    // Fix caption shortcodes
    add_filter('the_content', 'sav2_fix_captions', 1);
    add_filter('woocommerce_short_description', 'sav2_fix_captions', 1);
});

/* ── V2 Hook functions ─────────────────────────────────────── */

function sav2_fix_captions($content) {
    if (!is_singular('product') && !doing_filter('woocommerce_short_description')) return $content;
    if (strpos($content, '[caption') !== false) {
        $content = do_shortcode($content);
    }
    return preg_replace('/\[caption[^\]]*\](.*?)\[\/caption\]/s', '$1', $content);
}

/* Eyebrow — catégorie chip + badge certifié */
function sav2_sidebar_eyebrow() {
    if (!is_product()) return;
    $terms = get_the_terms(get_the_ID(), 'product_cat');
    $category = (!empty($terms) && !is_wp_error($terms)) ? esc_html($terms[0]->name) : 'Œuvre';
    $certified = get_post_meta(get_the_ID(), '_sa_certified', true);
    ?>
    <div class="sa-v2-eyebrow">
        <span class="sa-v2-chip sa-v2-chip-accent"><?php echo $category; ?></span>
        <?php if ($certified) : ?>
            <span class="sa-v2-chip" style="gap:5px;">
                <svg viewBox="0 0 24 24" width="12" height="12"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Œuvre certifiée
            </span>
        <?php endif; ?>
    </div>
    <?php
}

/* Meta chips — flexible matching for all WC attribute slug variants */
function sav2_sidebar_meta_chips() {
    if (!is_product()) return;
    $product = wc_get_product(get_the_ID());
    if (!$product) return;

    // Keyword → SVG icon (matched via strpos on normalized slug)
    $icon_kw = array(
        'technique'   => '<svg viewBox="0 0 24 24"><circle cx="13.5" cy="6.5" r="2.5"/><circle cx="17" cy="12" r="1.5"/><circle cx="8" cy="8" r="2"/><circle cx="6" cy="14" r="1.5"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.9 0 1.5-.7 1.5-1.5 0-.4-.1-.7-.4-1-.3-.3-.4-.6-.4-1 0-.8.7-1.5 1.5-1.5H16c3.3 0 6-2.7 6-6 0-5.5-4.5-9-10-9z"/></svg>',
        'medium'      => '<svg viewBox="0 0 24 24"><circle cx="13.5" cy="6.5" r="2.5"/><circle cx="17" cy="12" r="1.5"/><circle cx="8" cy="8" r="2"/><circle cx="6" cy="14" r="1.5"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.9 0 1.5-.7 1.5-1.5 0-.4-.1-.7-.4-1-.3-.3-.4-.6-.4-1 0-.8.7-1.5 1.5-1.5H16c3.3 0 6-2.7 6-6 0-5.5-4.5-9-10-9z"/></svg>',
        'dimension'   => '<svg viewBox="0 0 24 24"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>',
        'format'      => '<svg viewBox="0 0 24 24"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>',
        'taille'      => '<svg viewBox="0 0 24 24"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>',
        'periode'     => '<svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
        'date'        => '<svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
        'annee'       => '<svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
        'ecole'       => '<svg viewBox="0 0 24 24"><line x1="6" y1="20" x2="6" y2="9"/><line x1="10" y1="20" x2="10" y2="9"/><line x1="14" y1="20" x2="14" y2="9"/><line x1="18" y1="20" x2="18" y2="9"/><path d="M3 20h18"/><path d="M2 9h20l-2-4H4L2 9z"/></svg>',
        'etat'        => '<svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="1"/><rect x="7" y="7" width="10" height="10" rx="1"/></svg>',
        'condition'   => '<svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="1"/><rect x="7" y="7" width="10" height="10" rx="1"/></svg>',
        'signature'   => '<svg viewBox="0 0 24 24"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>',
        'encadrement' => '<svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="1"/><rect x="5" y="5" width="14" height="14" rx="1"/></svg>',
    );
    $default_icon = '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>';

    // Skip these (not useful as chips)
    $skip = array('prix', 'price', 'couleur', 'color', 'statut', 'status', 'sujet', 'subject', 'artiste', 'artist', 'dominant');

    $chips = array();

    // Read ALL WC attributes, match flexibly
    $attributes = $product->get_attributes();
    foreach ($attributes as $slug => $attribute) {
        $slug_norm = strtolower(str_replace(array('pa_', 'pa-', '-', '_'), '', $slug));

        // Skip irrelevant
        $do_skip = false;
        foreach ($skip as $s) { if (strpos($slug_norm, $s) !== false) { $do_skip = true; break; } }
        if ($do_skip) continue;

        // Get values
        if ($attribute->is_taxonomy()) {
            $values = wc_get_product_terms(get_the_ID(), $attribute->get_name(), array('fields' => 'names'));
        } else {
            $values = $attribute->get_options();
        }
        if (empty($values)) continue;

        // Match icon
        $icon = $default_icon;
        foreach ($icon_kw as $kw => $svg) {
            if (strpos($slug_norm, $kw) !== false) { $icon = $svg; break; }
        }

        $chips[] = array(
            'icon'  => $icon,
            'value' => implode(', ', array_map('esc_html', (array)$values)),
        );
    }

    // Fallback: custom meta
    if (empty($chips)) {
        $meta_map = array(
            '_sa_technique'  => 'technique',
            '_sa_dimensions' => 'dimension',
            '_sa_periode'    => 'periode',
            '_sa_ecole'      => 'ecole',
            '_sa_etat'       => 'etat',
        );
        foreach ($meta_map as $key => $kw) {
            $val = get_post_meta(get_the_ID(), $key, true);
            if ($val) {
                $chips[] = array('icon' => $icon_kw[$kw], 'value' => esc_html($val));
            }
        }
    }

    if (empty($chips)) return;

    echo '<div class="sa-v2-meta-chips">';
    foreach ($chips as $chip) {
        printf('<span class="sa-v2-chip">%s<span>%s</span></span>', $chip['icon'], $chip['value']);
    }
    echo '</div>';
}

/* Sous-texte prix */
function sav2_sidebar_price_info() {
    if (!is_product()) return;
    echo '<p class="sa-v2-price-info">TVA incluse &middot; Livraison offerte en France m&eacute;tropolitaine</p>';
}

/* Bouton contact + badges paiement */
function sav2_contact_button() {
    if (!is_product()) return;
    $contact_url = esc_url(home_url('/conseil/'));
    $contact_url = add_query_arg('sujet', urlencode('Renseignement : ' . get_the_title()), $contact_url);
    ?>
    <a href="<?php echo esc_url($contact_url); ?>" class="sa-v2-contact-btn">
        <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        Contacter pour plus d'infos
    </a>
    <div class="sa-v2-payment-row">
        <span class="sa-v2-pay-badge">
            <svg viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            CB / Visa
        </span>
        <span class="sa-v2-pay-badge">
            <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            3&times; sans frais
        </span>
    </div>
    <?php
}

/* Trust badges sidebar */
function sav2_sidebar_trust() {
    if (!is_product()) return;
    ?>
    <ul class="sa-v2-trust-list">
        <li>
            <svg viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            Livraison offerte &middot; Emballage mus&eacute;al
        </li>
        <li>
            <svg viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
            Retour 14 jours &middot; Satisfait ou rembours&eacute;
        </li>
        <li>
            <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            Certificat d'authenticit&eacute; inclus
        </li>
    </ul>
    <?php
}

/* Badge certifié sur la galerie */
function sav2_gallery_badge() {
    if (!is_product()) return;
    $certified = get_post_meta(get_the_ID(), '_sa_certified', true);
    if (!$certified) return;
    ?>
    <div class="sa-gallery-badge">
        <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Œuvre certifi&eacute;e
    </div>
    <?php
}

/* Sections sous la galerie : artiste + certificat + garanties */
function sav2_custom_sections() {
    if (!is_product()) return;

    $product_id   = get_the_ID();
    $artist_name  = get_post_meta($product_id, '_sa_artiste_nom', true);
    $artist_dates = get_post_meta($product_id, '_sa_artiste_dates', true);
    $artist_bio   = get_post_meta($product_id, '_sa_artiste_bio', true);

    // Fallback: try WC attributes for artist name
    if (!$artist_name) {
        $product = wc_get_product($product_id);
        if ($product) {
            $artist_name = $product->get_attribute('artiste') ?: $product->get_attribute('artist');
        }
    }

    $initials = '';
    if ($artist_name) {
        foreach (explode(' ', $artist_name) as $word) {
            $initials .= mb_strtoupper(mb_substr($word, 0, 1));
        }
        $initials = mb_substr($initials, 0, 2);
    }

    echo '<div class="sa-product-sections">';

    // ─ Artiste ─
    if ($artist_name) :
    ?>
        <div class="sa-v2-section-label" style="margin-top:40px;">L'artiste</div>
        <div class="sa-v2-artist-block">
            <div class="sa-v2-artist-avatar"><?php echo esc_html($initials ?: '?'); ?></div>
            <div class="sa-v2-artist-info">
                <h3><?php echo esc_html($artist_name); ?></h3>
                <?php if ($artist_dates) : ?>
                    <div class="sa-v2-artist-dates"><?php echo esc_html($artist_dates); ?></div>
                <?php endif; ?>
                <?php if ($artist_bio) : ?>
                    <p><?php echo wp_kses_post($artist_bio); ?></p>
                <?php endif; ?>
                <?php
                $artist_cat = get_term_by('name', $artist_name, 'product_cat');
                if (!$artist_cat) {
                    $artist_tag = get_term_by('name', $artist_name, 'product_tag');
                }
                $artist_url = '';
                if (!empty($artist_cat) && !is_wp_error($artist_cat)) {
                    $artist_url = get_term_link($artist_cat);
                } elseif (!empty($artist_tag) && !is_wp_error($artist_tag)) {
                    $artist_url = get_term_link($artist_tag);
                }
                if ($artist_url && !is_wp_error($artist_url)) : ?>
                    <a href="<?php echo esc_url($artist_url); ?>" class="sa-v2-artist-link">
                        Voir toutes ses œuvres &rarr;
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif;

    // ─ Certificat + Garanties ─
    ?>
    <div class="sa-v2-section-label" style="margin-top:<?php echo $artist_name ? '8px' : '40px'; ?>;">Garanties</div>

    <div class="sa-v2-cert-block">
        <div class="sa-v2-cert-icon">
            <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </div>
        <div class="sa-v2-cert-text">
            <strong>Certificat d'authenticit&eacute; inclus</strong>
            <span>Expertise r&eacute;alis&eacute;e par un commissaire-priseur agr&eacute;&eacute;. Document joint &agrave; l'&oelig;uvre &agrave; la livraison.</span>
        </div>
    </div>

    <ul class="sa-v2-guarantees">
        <li>
            <svg viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            Livraison offerte en France m&eacute;tropolitaine &middot; Emballage mus&eacute;al
        </li>
        <li>
            <svg viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
            Retour accept&eacute; sous 14 jours &mdash; Garantie satisfait ou rembours&eacute;
        </li>
        <li>
            <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            Conseil personnalis&eacute; disponible &mdash; R&eacute;ponse en moins de 4h
        </li>
    </ul>
    <?php
    echo '</div>';
}

/* Barre sticky mobile */
function sav2_mobile_bar() {
    if (!is_product()) return;

    global $product;
    if (!$product) $product = wc_get_product(get_the_ID());
    if (!$product) return;

    $price       = $product->get_price_html();
    $cart_url    = $product->add_to_cart_url();
    $contact_url = esc_url(home_url('/conseil/'));
    ?>
    <div class="sa-v2-mobile-bar">
        <div class="sa-v2-mobile-price"><?php echo wp_kses_post($price); ?></div>
        <a href="<?php echo esc_url($cart_url); ?>"
           class="sa-v2-mobile-cta"
           data-product_id="<?php echo esc_attr(get_the_ID()); ?>"
           data-quantity="1">
            Acheter
        </a>
        <a href="<?php echo esc_url($contact_url); ?>" class="sa-v2-mobile-contact">
            Infos
        </a>
    </div>
    <?php
}

/* --------------------------------------------------------------------------
   Sales funnel: stay on product page after add-to-cart
   -------------------------------------------------------------------------- */
add_filter('woocommerce_add_to_cart_redirect', function () {
    return wp_get_referer() ?: wc_get_cart_url();
});

/* ══════════════════════════════════════════════════════════════
   Cart Empty v2 — Hero éditorial + trust strip + recommandations
   ══════════════════════════════════════════════════════════════ */

/* Enqueue cart-empty CSS */
add_action('wp_enqueue_scripts', function () {
    if (is_cart()) {
        $version = filemtime(get_stylesheet_directory() . '/assets/css/cart-empty.css');
        wp_enqueue_style(
            'saisonart-cart-empty',
            get_stylesheet_directory_uri() . '/assets/css/cart-empty.css',
            array('saisonart-main'),
            $version
        );
    }
});

/* Hook principal — Injecter le hero + trust + reco quand le panier est vide */
add_action('woocommerce_cart_is_empty', 'saison_cart_empty_content', 5);

function saison_cart_empty_content() {

    $shop_url    = get_permalink(wc_get_page_id('shop'));
    $contact_url = saison_get_contact_url();
    ?>

    <!-- HERO ÉDITORIAL -->
    <div class="sa-cart-hero">

        <div class="sa-cart-hero__left">
            <div class="sa-cart-eyebrow">Votre sélection</div>

            <h1 class="sa-cart-title">
                Votre panier<br>attend une <em>œuvre</em>
            </h1>

            <p class="sa-cart-subtitle">
                Chaque tableau que vous ajoutez raconte quelque chose de vous.
                Prenez le temps de choisir — nos experts sont là pour vous guider.
            </p>

            <div class="sa-cart-ctas">
                <a href="<?php echo esc_url($shop_url); ?>" class="sa-btn-primary">
                    Découvrir la boutique →
                </a>
                <a href="<?php echo esc_url($contact_url); ?>" class="sa-btn-ghost">
                    💬 Parler à un expert
                </a>
            </div>
        </div>

        <div class="sa-cart-hero__right">
            <div class="sa-deco-char">∅</div>
            <div class="sa-empty-frame">
                <div class="sa-frame-hook"></div>
                <div class="sa-frame-outer"></div>
                <div class="sa-frame-inner">
                    <div class="sa-frame-inner-icon">🖼</div>
                    <div class="sa-frame-inner-text">
                        Votre prochaine<br>acquisition vous attend
                    </div>
                </div>
                <div class="sa-frame-shadow"></div>
            </div>
        </div>

    </div>

    <?php
    saison_cart_trust_strip();
    saison_cart_recommendations();
}

/* Trust strip — 4 garanties */
function saison_cart_trust_strip() {
    $items = array(
        array('icon' => '📄', 'label' => 'Certifiées authentiques',  'desc' => 'Chaque œuvre est expertisée'),
        array('icon' => '🚚', 'label' => 'Livraison offerte',        'desc' => 'Emballage muséal sécurisé'),
        array('icon' => '↩️',  'label' => 'Retour 14 jours',          'desc' => 'Satisfait ou remboursé'),
        array('icon' => '💬', 'label' => 'Conseil expert',            'desc' => 'Réponse en moins de 4h'),
    );
    ?>
    <div class="sa-cart-trust">
        <div class="sa-cart-trust__inner">
            <?php foreach ($items as $item) : ?>
                <div class="sa-trust-item">
                    <span class="sa-trust-item__icon"><?php echo $item['icon']; ?></span>
                    <div>
                        <div class="sa-trust-item__label"><?php echo esc_html($item['label']); ?></div>
                        <div class="sa-trust-item__desc"><?php echo esc_html($item['desc']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

/* Recommandations — Grille 4 produits (featured > récents) */
function saison_cart_recommendations() {

    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 4,
        'orderby'        => 'rand',
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_visibility',
                'field'    => 'name',
                'terms'    => 'featured',
            ),
        ),
    );

    $query = new WP_Query($args);

    if ($query->post_count < 4) {
        $args_fallback = array(
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => 4,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );
        $query = new WP_Query($args_fallback);
    }

    if (!$query->have_posts()) return;

    $shop_url = get_permalink(wc_get_page_id('shop'));
    ?>

    <div class="sa-cart-reco">
        <div class="sa-cart-reco__header">
            <div>
                <h2 class="sa-cart-reco__title">Œuvres qui pourraient vous inspirer</h2>
                <p class="sa-cart-reco__subtitle">Sélectionnées par nos experts · Disponibles immédiatement</p>
            </div>
            <a href="<?php echo esc_url($shop_url); ?>" class="sa-cart-reco__link">
                Voir toute la boutique →
            </a>
        </div>

        <ul class="sa-reco-grid products">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <?php
                $product     = wc_get_product(get_the_ID());
                $price_html  = $product ? $product->get_price_html() : '';
                $product_url = get_permalink();
                $img         = has_post_thumbnail()
                               ? get_the_post_thumbnail(get_the_ID(), 'woocommerce_thumbnail')
                               : '<div style="aspect-ratio:4/3;background:var(--sa-darker);display:flex;align-items:center;justify-content:center;font-size:28px;opacity:0.25;">🖼</div>';

                $terms    = get_the_terms(get_the_ID(), 'product_cat');
                $cat_name = (!empty($terms) && !is_wp_error($terms)) ? $terms[0]->name : '';
                ?>
                <li class="product">
                    <a href="<?php echo esc_url($product_url); ?>">
                        <?php echo $img; ?>
                        <?php if ($cat_name) : ?>
                            <div style="padding:12px 16px 0;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--sa-accent);">
                                <?php echo esc_html($cat_name); ?>
                            </div>
                        <?php endif; ?>
                        <h3 class="woocommerce-loop-product__title">
                            <?php the_title(); ?>
                        </h3>
                        <div class="price"><?php echo wp_kses_post($price_html); ?></div>
                    </a>
                </li>
            <?php endwhile; wp_reset_postdata(); ?>
        </ul>
    </div>

    <?php
}

/* URL page contact avec sujet pré-rempli */
function saison_get_contact_url($subject = '') {
    $contact_page = get_page_by_path('contact');
    $url = $contact_page ? get_permalink($contact_page->ID) : home_url('/contact/');
    if ($subject) {
        $url = add_query_arg('sujet', urlencode($subject), $url);
    }
    return $url;
}

/* Filtrer les notices WooCommerce vides (corrige icône ⓘ orpheline) */
add_filter('woocommerce_add_message', 'saison_filter_empty_notices');
add_filter('woocommerce_add_error',   'saison_filter_empty_notices');
add_filter('woocommerce_add_notice',  'saison_filter_empty_notices');

function saison_filter_empty_notices($message) {
    return trim($message) ? $message : '';
}
