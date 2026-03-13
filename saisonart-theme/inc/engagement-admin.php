<?php
/**
 * SaisonArt — Engagement Module Admin Page.
 *
 * Adds a "SaisonArt > Engagement" settings page in wp-admin.
 * All settings stored in wp_options under key 'sa_engage_settings'.
 */

if (!defined('ABSPATH')) exit;

/* --------------------------------------------------------------------------
   Defaults
   -------------------------------------------------------------------------- */
function sa_engage_defaults() {
    return array(
        // Module toggles
        'announce_enabled'  => '1',
        'toasts_enabled'    => '1',
        'quiz_enabled'      => '1',
        'hearts_enabled'    => '1',
        'exit_enabled'      => '1',
        'sticky_enabled'    => '1',

        // Announce bar
        'announce_msg_1'    => 'Nouvelle collection disponible',
        'announce_msg_2'    => '-10% première commande : BIENVENUE10',
        'announce_msg_3'    => 'Livraison offerte dès 150 €',
        'announce_bg'       => '#8B5E3C',
        'announce_interval' => '4',

        // Toasts
        'toast_msg_1'       => '3 nouvelles œuvres cette semaine',
        'toast_msg_2'       => '{x} personnes consultent cette œuvre',
        'toast_msg_3'       => 'Livraison offerte dès 150 € d\'achat',
        'toast_msg_4'       => '{name} vient d\'ajouter une œuvre à son panier',
        'toast_first_delay' => '5',
        'toast_interval'    => '8',
        'toast_duration'    => '6',

        // Quiz
        'quiz_delay'        => '30',
        'quiz_scroll'       => '50',
        'quiz_styles'       => "Impressionnisme\nPost-impressionnisme\nFauvisme\nClassique",

        // Exit-intent
        'exit_title'        => 'Avant de partir…',
        'exit_code'         => 'BIENVENUE10',
        'exit_discount'     => '-10% sur votre première œuvre',
        'exit_mobile_delay' => '45',

        // Sticky CTA
        'sticky_arg_1'      => 'Livraison offerte dès 150 €',
        'sticky_arg_2'      => 'Retour 14j',
        'sticky_arg_3'      => 'Authenticité certifiée',
        'sticky_label'      => 'Voir la boutique',
        'sticky_url'        => '/boutique/',

        // Resend
        'resend_api_key'      => '',
        'resend_from'         => '',
        'resend_notify_email' => '',
    );
}

function sa_engage_get($key = null) {
    $settings = get_option('sa_engage_settings', array());
    $defaults = sa_engage_defaults();
    $merged = wp_parse_args($settings, $defaults);
    if ($key) return isset($merged[$key]) ? $merged[$key] : null;
    return $merged;
}

/* --------------------------------------------------------------------------
   Admin menu
   -------------------------------------------------------------------------- */
add_action('admin_menu', 'sa_engage_admin_menu');
function sa_engage_admin_menu() {
    add_menu_page(
        'Engagement',
        'Engagement',
        'manage_options',
        'sa-engagement',
        'sa_engage_render_page',
        'dashicons-megaphone',
        59
    );
}

/* --------------------------------------------------------------------------
   Register settings
   -------------------------------------------------------------------------- */
add_action('admin_init', 'sa_engage_register_settings');
function sa_engage_register_settings() {
    register_setting('sa_engage_group', 'sa_engage_settings', array(
        'type'              => 'array',
        'sanitize_callback' => 'sa_engage_sanitize',
    ));
}

function sa_engage_sanitize($input) {
    $defaults = sa_engage_defaults();
    $clean = array();

    // Toggles (checkboxes)
    $toggles = array('announce_enabled','toasts_enabled','quiz_enabled','hearts_enabled','exit_enabled','sticky_enabled');
    foreach ($toggles as $t) {
        $clean[$t] = !empty($input[$t]) ? '1' : '0';
    }

    // Text fields
    $texts = array(
        'announce_msg_1','announce_msg_2','announce_msg_3','announce_bg',
        'toast_msg_1','toast_msg_2','toast_msg_3','toast_msg_4',
        'quiz_styles',
        'exit_title','exit_code','exit_discount',
        'sticky_arg_1','sticky_arg_2','sticky_arg_3','sticky_label','sticky_url',
        'resend_api_key','resend_from','resend_notify_email',
    );
    foreach ($texts as $f) {
        $clean[$f] = isset($input[$f]) ? sanitize_textarea_field($input[$f]) : $defaults[$f];
    }

    // Numbers
    $numbers = array(
        'announce_interval','toast_first_delay','toast_interval','toast_duration',
        'quiz_delay','quiz_scroll','exit_mobile_delay',
    );
    foreach ($numbers as $n) {
        $clean[$n] = isset($input[$n]) ? absint($input[$n]) : $defaults[$n];
    }

    return $clean;
}

/* --------------------------------------------------------------------------
   Render page
   -------------------------------------------------------------------------- */
function sa_engage_render_page() {
    $s = sa_engage_get();
    ?>
    <div class="wrap">
        <h1>SaisonArt — Modules d'engagement</h1>
        <form method="post" action="options.php">
            <?php settings_fields('sa_engage_group'); ?>

            <style>
                .sa-admin-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;max-width:960px;margin-top:20px}
                .sa-admin-card{background:#fff;border:1px solid #ccd0d4;border-radius:8px;padding:20px}
                .sa-admin-card h2{margin:0 0 15px;font-size:15px;border-bottom:1px solid #eee;padding-bottom:10px}
                .sa-admin-card label{display:block;margin-bottom:12px;font-size:13px}
                .sa-admin-card input[type="text"],
                .sa-admin-card input[type="number"],
                .sa-admin-card input[type="url"],
                .sa-admin-card input[type="color"],
                .sa-admin-card textarea{width:100%;margin-top:4px}
                .sa-admin-card input[type="number"]{width:80px}
                .sa-admin-card input[type="color"]{width:60px;height:32px;padding:2px;cursor:pointer}
                .sa-admin-toggles{display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px}
                .sa-admin-toggle{display:flex;align-items:center;gap:8px;padding:10px 14px;background:#f9f9f9;border-radius:6px;border:1px solid #ddd}
                .sa-admin-toggle input{margin:0}
                @media(max-width:782px){.sa-admin-grid{grid-template-columns:1fr}.sa-admin-toggles{grid-template-columns:1fr 1fr}}
            </style>

            <!-- Section 1: Module toggles -->
            <div class="sa-admin-card" style="max-width:960px;margin-top:20px">
                <h2>Modules actifs</h2>
                <div class="sa-admin-toggles">
                    <?php
                    $modules = array(
                        'announce_enabled' => 'Barre d\'annonce',
                        'toasts_enabled'   => 'Toasts contextuels',
                        'quiz_enabled'     => 'Quiz de style',
                        'hearts_enabled'   => 'Cœurs wishlist',
                        'exit_enabled'     => 'Exit-intent popup',
                        'sticky_enabled'   => 'Barre CTA sticky',
                    );
                    foreach ($modules as $key => $label) : ?>
                        <label class="sa-admin-toggle">
                            <input type="checkbox" name="sa_engage_settings[<?php echo $key; ?>]" value="1" <?php checked($s[$key], '1'); ?>>
                            <?php echo esc_html($label); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="sa-admin-grid">
                <!-- Section 2: Announce bar -->
                <div class="sa-admin-card">
                    <h2>Barre d'annonce</h2>
                    <label>Message 1
                        <input type="text" name="sa_engage_settings[announce_msg_1]" value="<?php echo esc_attr($s['announce_msg_1']); ?>">
                    </label>
                    <label>Message 2
                        <input type="text" name="sa_engage_settings[announce_msg_2]" value="<?php echo esc_attr($s['announce_msg_2']); ?>">
                    </label>
                    <label>Message 3
                        <input type="text" name="sa_engage_settings[announce_msg_3]" value="<?php echo esc_attr($s['announce_msg_3']); ?>">
                    </label>
                    <label>Couleur de fond
                        <input type="color" name="sa_engage_settings[announce_bg]" value="<?php echo esc_attr($s['announce_bg']); ?>">
                    </label>
                    <label>Rotation (secondes)
                        <input type="number" name="sa_engage_settings[announce_interval]" value="<?php echo esc_attr($s['announce_interval']); ?>" min="2" max="15">
                    </label>
                </div>

                <!-- Section 3: Toasts -->
                <div class="sa-admin-card">
                    <h2>Toasts contextuels</h2>
                    <label>Message 1 — Nouveautés
                        <input type="text" name="sa_engage_settings[toast_msg_1]" value="<?php echo esc_attr($s['toast_msg_1']); ?>">
                    </label>
                    <label>Message 2 — Social proof <small>({x} = nombre)</small>
                        <input type="text" name="sa_engage_settings[toast_msg_2]" value="<?php echo esc_attr($s['toast_msg_2']); ?>">
                    </label>
                    <label>Message 3 — Livraison
                        <input type="text" name="sa_engage_settings[toast_msg_3]" value="<?php echo esc_attr($s['toast_msg_3']); ?>">
                    </label>
                    <label>Message 4 — Achat récent <small>({name} = prénom)</small>
                        <input type="text" name="sa_engage_settings[toast_msg_4]" value="<?php echo esc_attr($s['toast_msg_4']); ?>">
                    </label>
                    <label>Délai premier toast (s)
                        <input type="number" name="sa_engage_settings[toast_first_delay]" value="<?php echo esc_attr($s['toast_first_delay']); ?>" min="1" max="60">
                    </label>
                    <label>Intervalle entre toasts (s)
                        <input type="number" name="sa_engage_settings[toast_interval]" value="<?php echo esc_attr($s['toast_interval']); ?>" min="3" max="30">
                    </label>
                    <label>Durée d'affichage (s)
                        <input type="number" name="sa_engage_settings[toast_duration]" value="<?php echo esc_attr($s['toast_duration']); ?>" min="3" max="15">
                    </label>
                </div>

                <!-- Section 4: Quiz -->
                <div class="sa-admin-card">
                    <h2>Quiz de style</h2>
                    <label>Délai d'apparition (s)
                        <input type="number" name="sa_engage_settings[quiz_delay]" value="<?php echo esc_attr($s['quiz_delay']); ?>" min="5" max="120">
                    </label>
                    <label>Seuil de scroll (%)
                        <input type="number" name="sa_engage_settings[quiz_scroll]" value="<?php echo esc_attr($s['quiz_scroll']); ?>" min="10" max="90">
                    </label>
                    <label>Styles proposés <small>(1 par ligne)</small>
                        <textarea name="sa_engage_settings[quiz_styles]" rows="4"><?php echo esc_textarea($s['quiz_styles']); ?></textarea>
                    </label>
                </div>

                <!-- Section 5: Exit-intent -->
                <div class="sa-admin-card">
                    <h2>Exit-intent popup</h2>
                    <label>Titre
                        <input type="text" name="sa_engage_settings[exit_title]" value="<?php echo esc_attr($s['exit_title']); ?>">
                    </label>
                    <label>Code promo
                        <input type="text" name="sa_engage_settings[exit_code]" value="<?php echo esc_attr($s['exit_code']); ?>">
                    </label>
                    <label>Texte réduction
                        <input type="text" name="sa_engage_settings[exit_discount]" value="<?php echo esc_attr($s['exit_discount']); ?>">
                    </label>
                    <label>Délai inactivité mobile (s)
                        <input type="number" name="sa_engage_settings[exit_mobile_delay]" value="<?php echo esc_attr($s['exit_mobile_delay']); ?>" min="10" max="120">
                    </label>
                </div>

                <!-- Section 6: Sticky CTA -->
                <div class="sa-admin-card">
                    <h2>Barre CTA sticky</h2>
                    <label>Argument 1
                        <input type="text" name="sa_engage_settings[sticky_arg_1]" value="<?php echo esc_attr($s['sticky_arg_1']); ?>">
                    </label>
                    <label>Argument 2
                        <input type="text" name="sa_engage_settings[sticky_arg_2]" value="<?php echo esc_attr($s['sticky_arg_2']); ?>">
                    </label>
                    <label>Argument 3
                        <input type="text" name="sa_engage_settings[sticky_arg_3]" value="<?php echo esc_attr($s['sticky_arg_3']); ?>">
                    </label>
                    <label>Label du bouton
                        <input type="text" name="sa_engage_settings[sticky_label]" value="<?php echo esc_attr($s['sticky_label']); ?>">
                    </label>
                    <label>URL du bouton
                        <input type="text" name="sa_engage_settings[sticky_url]" value="<?php echo esc_attr($s['sticky_url']); ?>">
                    </label>
                </div>
            </div>

            <!-- Resend API -->
            <div class="sa-admin-card" style="max-width:960px;margin-top:0;grid-column:1/-1;border-left:3px solid #8B5E3C">
                <h2>Resend — Envoi d'emails</h2>
                <p style="font-size:12px;color:#666;margin-bottom:15px">Connectez votre compte <a href="https://resend.com" target="_blank" rel="noopener">Resend</a> pour envoyer automatiquement les emails de bienvenue et recevoir les notifications de nouveaux contacts.</p>
                <label>Clé API Resend
                    <input type="text" name="sa_engage_settings[resend_api_key]" value="<?php echo esc_attr($s['resend_api_key']); ?>" placeholder="re_xxxxxxxx">
                </label>
                <label>Email expéditeur <small>(doit être vérifié dans Resend)</small>
                    <input type="text" name="sa_engage_settings[resend_from]" value="<?php echo esc_attr($s['resend_from']); ?>" placeholder="SaisonArt <contact@saisonart.com>">
                </label>
                <label>Email de notification admin <small>(par défaut : <?php echo esc_html(get_option('admin_email')); ?>)</small>
                    <input type="text" name="sa_engage_settings[resend_notify_email]" value="<?php echo esc_attr($s['resend_notify_email']); ?>" placeholder="<?php echo esc_attr(get_option('admin_email')); ?>">
                </label>
            </div>

            <?php submit_button('Enregistrer les réglages'); ?>
        </form>
    </div>
    <?php
}
