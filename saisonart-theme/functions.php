<?php
/**
 * SaisonArt Child Theme functions.
 */

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

    // JS
    wp_enqueue_script('saisonart-main', get_stylesheet_directory_uri() . '/assets/js/main.js', array('jquery'), $version, true);
}

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
   SEO: Twitter Card tags
   -------------------------------------------------------------------------- */
add_action('wp_head', 'saisonart_twitter_cards', 3);
function saisonart_twitter_cards() {
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
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
        'sameAs'      => array(
            'https://www.instagram.com/saisonart/',
            'https://www.facebook.com/saisonart/',
        ),
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
}
