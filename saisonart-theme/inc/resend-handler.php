<?php
/**
 * SaisonArt — Resend email handler.
 *
 * Receives email captures via AJAX and sends them through the Resend API.
 * Also stores contacts in wp_options as a simple CRM backup.
 */

if (!defined('ABSPATH')) exit;

/* --------------------------------------------------------------------------
   AJAX endpoints (logged-in + non-logged-in visitors)
   -------------------------------------------------------------------------- */
add_action('wp_ajax_sa_capture_email', 'sa_capture_email');
add_action('wp_ajax_nopriv_sa_capture_email', 'sa_capture_email');

function sa_capture_email() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sa_engage_nonce')) {
        wp_send_json_error(array('message' => 'Nonce invalide.'), 403);
    }

    $email  = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $source = isset($_POST['source']) ? sanitize_text_field($_POST['source']) : 'unknown';
    $data   = isset($_POST['data']) ? sanitize_text_field($_POST['data']) : '';

    if (!is_email($email)) {
        wp_send_json_error(array('message' => 'Email invalide.'), 400);
    }

    // 1. Store contact locally (wp_options backup)
    sa_store_contact($email, $source, $data);

    // 2. Send via Resend API
    $settings = sa_engage_get();
    $api_key  = isset($settings['resend_api_key']) ? trim($settings['resend_api_key']) : '';
    $from     = isset($settings['resend_from']) ? trim($settings['resend_from']) : '';
    $to_admin = isset($settings['resend_notify_email']) ? trim($settings['resend_notify_email']) : get_option('admin_email');

    if (empty($api_key) || empty($from)) {
        // No Resend config — still store locally, but skip API
        wp_send_json_success(array('message' => 'Contact enregistré (mode local).'));
    }

    // Send welcome email to subscriber
    $welcome_sent = sa_resend_send($api_key, array(
        'from'    => $from,
        'to'      => array($email),
        'subject' => sa_get_welcome_subject($source),
        'html'    => sa_get_welcome_html($email, $source, $data, $settings),
    ));

    // Notify admin of new capture
    $source_labels = array(
        'quiz'                    => 'Quiz de style',
        'wishlist'                => 'Wishlist / Coeurs',
        'exit'                    => 'Exit-intent popup',
        'newsletter'              => 'Newsletter',
        'conseil-recherche'       => 'Conseil — Recherche d\'œuvre',
        'conseil-identification'  => 'Conseil — Identification tableau',
        'conseil-rdv'             => 'Conseil — Demande de RDV',
        'contact'                 => 'Formulaire de contact',
    );
    $source_label = isset($source_labels[$source]) ? $source_labels[$source] : $source;

    sa_resend_send($api_key, array(
        'from'    => $from,
        'to'      => array($to_admin),
        'subject' => 'Nouveau contact SaisonArt — ' . $source_label,
        'html'    => '<p><strong>Email :</strong> ' . esc_html($email) . '</p>'
                   . '<p><strong>Source :</strong> ' . esc_html($source_label) . '</p>'
                   . ($data ? '<p><strong>Données :</strong> ' . esc_html($data) . '</p>' : '')
                   . '<p><strong>Date :</strong> ' . date_i18n('d/m/Y H:i') . '</p>',
    ));

    if ($welcome_sent) {
        wp_send_json_success(array('message' => 'Email envoyé avec succès.'));
    } else {
        wp_send_json_success(array('message' => 'Contact enregistré (erreur Resend).'));
    }
}

/* --------------------------------------------------------------------------
   Resend API call
   -------------------------------------------------------------------------- */
function sa_resend_send($api_key, $payload) {
    $response = wp_remote_post('https://api.resend.com/emails', array(
        'timeout' => 10,
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type'  => 'application/json',
        ),
        'body' => wp_json_encode($payload),
    ));

    if (is_wp_error($response)) {
        return false;
    }

    $code = wp_remote_retrieve_response_code($response);
    return $code >= 200 && $code < 300;
}

/* --------------------------------------------------------------------------
   Local contact storage (wp_options)
   -------------------------------------------------------------------------- */
function sa_store_contact($email, $source, $data) {
    $contacts = get_option('sa_contacts', array());

    // Check for existing contact
    $found = false;
    foreach ($contacts as &$contact) {
        if ($contact['email'] === $email) {
            $contact['sources'][] = array(
                'source' => $source,
                'data'   => $data,
                'date'   => current_time('mysql'),
            );
            $found = true;
            break;
        }
    }
    unset($contact);

    if (!$found) {
        $contacts[] = array(
            'email'   => $email,
            'sources' => array(
                array(
                    'source' => $source,
                    'data'   => $data,
                    'date'   => current_time('mysql'),
                ),
            ),
            'created' => current_time('mysql'),
        );
    }

    update_option('sa_contacts', $contacts, false);
}

/* --------------------------------------------------------------------------
   Welcome email templates
   -------------------------------------------------------------------------- */
function sa_get_welcome_subject($source) {
    $subjects = array(
        'quiz'                   => 'Vos recommandations personnalisées — SaisonArt',
        'wishlist'               => 'Vos coups de cœur sauvegardés — SaisonArt',
        'exit'                   => 'Votre code promo exclusif — SaisonArt',
        'newsletter'             => 'Bienvenue dans l\'atelier — SaisonArt',
        'conseil-recherche'      => 'Votre demande de recherche — SaisonArt',
        'conseil-identification' => 'Votre demande d\'identification — SaisonArt',
        'conseil-rdv'            => 'Votre demande de rendez-vous — SaisonArt',
        'contact'                => 'Nous avons bien reçu votre message — SaisonArt',
    );
    return isset($subjects[$source]) ? $subjects[$source] : 'Bienvenue — SaisonArt';
}

function sa_get_welcome_html($email, $source, $data, $settings) {
    $shop_url = home_url('/boutique/');
    $code = isset($settings['exit_code']) ? $settings['exit_code'] : 'BIENVENUE10';

    $style = 'font-family:"DM Sans",system-ui,sans-serif;color:#1C1917;line-height:1.6;';
    $accent = '#8B5E3C';
    $btn = 'display:inline-block;padding:14px 28px;background:' . $accent . ';color:#fff;text-decoration:none;border-radius:999px;font-weight:600;font-size:14px;';

    $header = '<div style="text-align:center;padding:32px 0 24px;border-bottom:1px solid #eee;margin-bottom:24px;">'
            . '<img src="' . esc_url(get_stylesheet_directory_uri() . '/assets/images/logo-saisonart-dark.png') . '" alt="SaisonArt" width="140" style="max-width:140px;">'
            . '</div>';

    $footer = '<div style="text-align:center;padding:24px 0;margin-top:32px;border-top:1px solid #eee;font-size:12px;color:#999;">'
            . '<p>SaisonArt — L\'art original accessible</p>'
            . '<p><a href="' . esc_url(home_url('/')) . '" style="color:' . $accent . ';">saisonart.com</a></p>'
            . '</div>';

    $body = '';

    switch ($source) {
        case 'quiz':
            $body = '<h2 style="font-family:\'Playfair Display\',Georgia,serif;color:#2C3D35;font-size:24px;">Merci pour vos réponses !</h2>'
                  . '<p>Nous avons bien reçu vos préférences et préparons une sélection personnalisée rien que pour vous.</p>'
                  . ($data ? '<p style="padding:12px 16px;background:#f7f3ec;border-radius:8px;font-size:13px;"><strong>Vos choix :</strong> ' . esc_html($data) . '</p>' : '')
                  . '<p>En attendant, découvrez notre collection :</p>'
                  . '<p style="text-align:center;padding:16px 0;"><a href="' . esc_url($shop_url) . '" style="' . $btn . '">Voir la boutique</a></p>';
            break;

        case 'wishlist':
            $body = '<h2 style="font-family:\'Playfair Display\',Georgia,serif;color:#2C3D35;font-size:24px;">Vos favoris sont sauvegardés</h2>'
                  . '<p>Vos coups de cœur vous attendent. Retrouvez-les à tout moment sur notre boutique.</p>'
                  . '<p style="text-align:center;padding:16px 0;"><a href="' . esc_url($shop_url) . '" style="' . $btn . '">Retrouver mes favoris</a></p>';
            break;

        case 'exit':
            $body = '<h2 style="font-family:\'Playfair Display\',Georgia,serif;color:#2C3D35;font-size:24px;">Votre code promo exclusif</h2>'
                  . '<p>Profitez de votre réduction sur votre première œuvre :</p>'
                  . '<div style="text-align:center;padding:20px;margin:16px 0;background:rgba(139,94,60,.06);border:1px dashed ' . $accent . ';border-radius:8px;">'
                  . '<span style="font-family:\'Playfair Display\',Georgia,serif;font-size:28px;font-weight:700;color:' . $accent . ';">' . esc_html($code) . '</span>'
                  . '</div>'
                  . '<p style="font-size:12px;color:#999;">Valable 48h. Une seule utilisation.</p>'
                  . '<p style="text-align:center;padding:16px 0;"><a href="' . esc_url($shop_url) . '" style="' . $btn . '">Utiliser mon code</a></p>';
            break;

        case 'conseil-recherche':
            $body = '<h2 style="font-family:\'Playfair Display\',Georgia,serif;color:#2C3D35;font-size:24px;">Votre demande est bien reçue</h2>'
                  . '<p>Nous avons pris note de ce que vous recherchez et allons parcourir notre sélection pour trouver des œuvres qui correspondent à vos critères.</p>'
                  . '<p>Un membre de la galerie vous recontactera sous 48h avec une sélection personnalisée.</p>'
                  . '<p style="text-align:center;padding:16px 0;"><a href="' . esc_url($shop_url) . '" style="' . $btn . '">Explorer la boutique en attendant</a></p>';
            break;

        case 'conseil-identification':
            $body = '<h2 style="font-family:\'Playfair Display\',Georgia,serif;color:#2C3D35;font-size:24px;">Votre demande d\'identification est enregistrée</h2>'
                  . '<p>Nous avons bien reçu les informations concernant votre tableau. Notre équipe va l\'examiner avec attention.</p>'
                  . '<p>Nous reviendrons vers vous avec nos observations — généralement sous 48 à 72 heures.</p>'
                  . '<p style="font-size:12px;color:#999;">Ce service est gratuit et sans engagement.</p>';
            break;

        case 'conseil-rdv':
            $body = '<h2 style="font-family:\'Playfair Display\',Georgia,serif;color:#2C3D35;font-size:24px;">Votre demande de rendez-vous</h2>'
                  . '<p>Nous avons bien reçu votre demande. Un membre de la galerie vous confirmera un créneau par email dans les prochaines 24 heures.</p>'
                  . '<p>L\'échange dure environ 20 minutes et est entièrement gratuit.</p>'
                  . '<p style="text-align:center;padding:16px 0;"><a href="' . esc_url($shop_url) . '" style="' . $btn . '">Découvrir nos œuvres</a></p>';
            break;

        case 'contact':
            $body = '<h2 style="font-family:\'Playfair Display\',Georgia,serif;color:#2C3D35;font-size:24px;">Merci pour votre message</h2>'
                  . '<p>Nous avons bien reçu votre demande et vous répondrons dans les 24 à 48 heures.</p>'
                  . '<p>En attendant, n\'hésitez pas à parcourir notre collection :</p>'
                  . '<p style="text-align:center;padding:16px 0;"><a href="' . esc_url($shop_url) . '" style="' . $btn . '">Découvrir la boutique</a></p>';
            break;

        case 'newsletter':
        default:
            $body = '<h2 style="font-family:\'Playfair Display\',Georgia,serif;color:#2C3D35;font-size:24px;">Bienvenue dans l\'atelier</h2>'
                  . '<p>Vous recevrez chaque mois nos nouvelles acquisitions, des portraits d\'artistes et des conseils pour collectionneurs.</p>'
                  . '<p style="text-align:center;padding:16px 0;"><a href="' . esc_url($shop_url) . '" style="' . $btn . '">Découvrir la boutique</a></p>';
            break;
    }

    return '<div style="max-width:560px;margin:0 auto;padding:0 20px;' . $style . '">'
         . $header . $body . $footer
         . '</div>';
}
