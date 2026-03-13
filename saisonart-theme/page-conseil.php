<?php
/**
 * Template Name: Conseil
 * SaisonArt — Conseil & expertise page.
 */
get_header(); ?>

<main id="main" class="sa-conseil-page">

<!-- ═══════ HERO ═══════ -->
<section class="sa-conseil-hero">
  <div class="sa-conseil-hero-inner">
    <div class="sa-conseil-eyebrow">Conseil & expertise</div>
    <h1 class="sa-conseil-h1">On est l&agrave;<br><em>pour vous aider.</em></h1>
    <p class="sa-conseil-desc">Une question sur une &oelig;uvre, un projet de d&eacute;coration, un tableau &agrave; identifier &mdash; dites-nous ce que vous avez en t&ecirc;te.</p>
  </div>
</section>

<!-- ═══════ 3 SITUATIONS ═══════ -->
<section class="sa-conseil-sits">

  <!-- Situation 1 — Chercher une œuvre -->
  <div class="sa-sit" id="saSit1">
    <span class="sa-sit-num">01</span>
    <div class="sa-sit-icon">
      <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    </div>
    <h2>Vous cherchez<br>une &oelig;uvre pr&eacute;cise</h2>
    <p>Un style, une &eacute;poque, une ambiance, un budget &mdash; d&eacute;crivez ce que vous imaginez, on regarde ce qu'on peut trouver.</p>
    <button class="sa-sit-trigger" data-target="saSit1">
      Nous &eacute;crire
      <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    </button>

    <div class="sa-sit-form">
      <div class="sa-form-field">
        <label>Ce que vous cherchez</label>
        <textarea name="description" placeholder="Ex. : un paysage marin, format moyen, tons bleus/gris, pour un salon contemporain&hellip;"></textarea>
      </div>
      <div class="sa-form-field">
        <label>Budget indicatif</label>
        <select name="budget">
          <option value="" disabled selected>S&eacute;lectionner</option>
          <option value="under-500">Moins de 500 &euro;</option>
          <option value="500-1500">500 &ndash; 1 500 &euro;</option>
          <option value="1500-5000">1 500 &ndash; 5 000 &euro;</option>
          <option value="over-5000">Plus de 5 000 &euro;</option>
          <option value="undefined">Pas encore d&eacute;fini</option>
        </select>
      </div>
      <div class="sa-form-field">
        <label>Votre email</label>
        <input type="email" name="email" placeholder="vous@exemple.com">
      </div>
      <div class="sa-form-submit">
        <span class="sa-form-note">Nous lisons chaque message<br>et revenons vers vous.</span>
        <button class="sa-btn-submit" data-source="conseil-recherche">Envoyer</button>
      </div>
    </div>
    <div class="sa-sit-success">Bien re&ccedil;u. Nous reviendrons vers vous apr&egrave;s avoir regard&eacute; notre s&eacute;lection.</div>
  </div>

  <!-- Situation 2 — Identifier un tableau -->
  <div class="sa-sit" id="saSit2">
    <span class="sa-sit-num">02</span>
    <div class="sa-sit-icon">
      <svg viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
    </div>
    <h2>Vous avez un tableau<br>&agrave; identifier</h2>
    <p>H&eacute;ritage, achat d'occasion, &oelig;uvre de famille &mdash; envoyez-nous une photo et ce que vous savez, on vous dit ce qu'on en pense.</p>
    <button class="sa-sit-trigger" data-target="saSit2">
      Envoyer une photo
      <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    </button>

    <div class="sa-sit-form">
      <div class="sa-form-field">
        <label>Photo de l'&oelig;uvre</label>
        <div class="sa-upload-zone" id="saUploadZone">
          <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
          <span>Cliquer pour ajouter une photo</span>
          <small>JPG, PNG &mdash; 10 Mo max</small>
          <input type="file" name="photo" accept="image/*" style="display:none">
        </div>
      </div>
      <div class="sa-form-field">
        <label>Ce que vous savez sur cette &oelig;uvre</label>
        <textarea name="description" placeholder="Provenance, signature, date, dimensions, &eacute;tat&hellip;" style="min-height:72px;"></textarea>
      </div>
      <div class="sa-form-field">
        <label>Votre email</label>
        <input type="email" name="email" placeholder="vous@exemple.com">
      </div>
      <div class="sa-form-submit">
        <span class="sa-form-note">Analyse gratuite et<br>sans engagement.</span>
        <button class="sa-btn-submit" data-source="conseil-identification">Envoyer</button>
      </div>
    </div>
    <div class="sa-sit-success">Bien re&ccedil;u. Nous examinerons votre tableau et reviendrons vers vous.</div>
  </div>

  <!-- Situation 3 — Prendre RDV -->
  <div class="sa-sit" id="saSit3">
    <span class="sa-sit-num">03</span>
    <div class="sa-sit-icon">
      <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
    </div>
    <h2>Vous voulez en parler<br>&agrave; quelqu'un</h2>
    <p>Un &eacute;change de 20 minutes avec un membre de la galerie &mdash; par t&eacute;l&eacute;phone ou visio, selon votre pr&eacute;f&eacute;rence.</p>
    <button class="sa-sit-trigger" data-target="saSit3">
      Demander un rendez-vous
      <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    </button>

    <div class="sa-sit-form">
      <div class="sa-form-field">
        <label>Votre pr&eacute;nom et nom</label>
        <input type="text" name="name" placeholder="Marie Dupont">
      </div>
      <div class="sa-form-field">
        <label>Votre email</label>
        <input type="email" name="email" placeholder="vous@exemple.com">
      </div>
      <div class="sa-form-field">
        <label>Sujet du rendez-vous</label>
        <select name="subject">
          <option value="" disabled selected>S&eacute;lectionner</option>
          <option value="recherche">Trouver une &oelig;uvre pour mon int&eacute;rieur</option>
          <option value="identification">Identifier ou estimer un tableau</option>
          <option value="collection">Un projet de collection</option>
          <option value="autre">Autre</option>
        </select>
      </div>
      <div class="sa-form-field">
        <label>Quand &ecirc;tes-vous disponible ?</label>
        <div class="sa-rdv-slots">
          <div class="sa-rdv-slot" data-slot="lundi-matin">Lundi matin</div>
          <div class="sa-rdv-slot" data-slot="lundi-aprem">Lundi apr&egrave;s-midi</div>
          <div class="sa-rdv-slot" data-slot="mardi-matin">Mardi matin</div>
          <div class="sa-rdv-slot" data-slot="mercredi">Mercredi</div>
          <div class="sa-rdv-slot" data-slot="jeudi-matin">Jeudi matin</div>
          <div class="sa-rdv-slot" data-slot="vendredi">Vendredi</div>
        </div>
      </div>
      <div class="sa-form-field">
        <label>T&eacute;l&eacute;phone ou visio ?</label>
        <select name="mode">
          <option value="telephone">T&eacute;l&eacute;phone</option>
          <option value="visio">Visio (Zoom ou Google Meet)</option>
          <option value="indifferent">Indiff&eacute;rent</option>
        </select>
      </div>
      <div class="sa-form-submit">
        <span class="sa-form-note">Nous vous confirmons<br>le cr&eacute;neau par email.</span>
        <button class="sa-btn-submit" data-source="conseil-rdv">Demander le RDV</button>
      </div>
    </div>
    <div class="sa-sit-success">Demande re&ccedil;ue. Nous vous confirmons un cr&eacute;neau par email.</div>
  </div>

</section>

<!-- ═══════ GARANTIES ═══════ -->
<section class="sa-conseil-garanties">
  <div class="sa-garantie">
    <svg viewBox="0 0 24 24" stroke-width="1.6"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
    <div class="sa-garantie-text">
      <h4>Gratuit et sans engagement</h4>
      <p>Conseil, identification, &eacute;change &mdash; aucun de ces services n'est factur&eacute;.</p>
    </div>
  </div>
  <div class="sa-garantie">
    <svg viewBox="0 0 24 24" stroke-width="1.6"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
    <div class="sa-garantie-text">
      <h4>Un interlocuteur r&eacute;el</h4>
      <p>Vos messages sont lus et trait&eacute;s par l'&eacute;quipe de la galerie, pas un bot.</p>
    </div>
  </div>
  <div class="sa-garantie">
    <svg viewBox="0 0 24 24" stroke-width="1.6"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
    <div class="sa-garantie-text">
      <h4>Vos donn&eacute;es restent priv&eacute;es</h4>
      <p>Les photos et informations que vous partagez ne sont pas diffus&eacute;es.</p>
    </div>
  </div>
</section>

</main>

<?php get_footer(); ?>
