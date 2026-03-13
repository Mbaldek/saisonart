<?php
/**
 * Template Name: Contact
 * SaisonArt — Contact page.
 */
get_header(); ?>

<main id="main" class="sa-contact-page">

<!-- ═══════ HERO ═══════ -->
<section class="sa-contact-hero">
  <div class="sa-contact-hero-inner">
    <div class="sa-contact-eyebrow">Contact</div>
    <h1 class="sa-contact-h1">Parlons<br><em>de ce qui vous plaît.</em></h1>
    <p class="sa-contact-desc">Une question sur une œuvre, un projet, une curiosité — écrivez-nous, on vous répond personnellement.</p>
  </div>
</section>

<!-- ═══════ CONTENT ═══════ -->
<section class="sa-contact-content">
  <div class="sa-contact-grid">

    <!-- Form -->
    <div class="sa-contact-form-wrap">
      <h2>Envoyez-nous un message</h2>
      <div class="sa-contact-form">
        <div class="sa-form-field">
          <label>Votre nom</label>
          <input type="text" name="name" placeholder="Marie Dupont">
        </div>
        <div class="sa-form-field">
          <label>Votre email</label>
          <input type="email" name="email" placeholder="vous@exemple.com">
        </div>
        <div class="sa-form-field">
          <label>Sujet</label>
          <select name="subject">
            <option value="" disabled selected>Choisir un sujet</option>
            <option value="oeuvre">Question sur une œuvre</option>
            <option value="commande">Suivi de commande</option>
            <option value="conseil">Demande de conseil</option>
            <option value="partenariat">Partenariat / Presse</option>
            <option value="autre">Autre</option>
          </select>
        </div>
        <div class="sa-form-field">
          <label>Votre message</label>
          <textarea name="message" placeholder="Décrivez votre demande…" rows="5"></textarea>
        </div>
        <div class="sa-form-submit">
          <button class="sa-contact-submit" data-source="contact">Envoyer</button>
        </div>
      </div>
      <div class="sa-contact-success">
        <svg viewBox="0 0 24 24" width="32" height="32"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <h3>Message envoyé.</h3>
        <p>Nous vous répondrons dans les 24 à 48 heures.</p>
      </div>
    </div>

    <!-- Info -->
    <div class="sa-contact-info">
      <div class="sa-contact-info-card">
        <div class="sa-contact-info-item">
          <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <div>
            <h4>Email</h4>
            <a href="mailto:contact@saisonart.com">contact@saisonart.com</a>
          </div>
        </div>
        <div class="sa-contact-info-item">
          <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <div>
            <h4>Adresse</h4>
            <p>75 rue de Lourmel<br>75015 Paris, France</p>
          </div>
        </div>
        <div class="sa-contact-info-item">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <div>
            <h4>Horaires</h4>
            <p>Lun — Ven : 9h – 18h<br>Réponse sous 48h</p>
          </div>
        </div>
      </div>

      <div class="sa-contact-garanties">
        <div class="sa-contact-garantie">
          <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          <span>Réponse personnelle</span>
        </div>
        <div class="sa-contact-garantie">
          <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <span>Données confidentielles</span>
        </div>
      </div>
    </div>

  </div>
</section>

</main>

<?php get_footer(); ?>
