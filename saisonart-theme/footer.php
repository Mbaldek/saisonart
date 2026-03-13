<?php
/**
 * SaisonArt — Custom footer (overrides Storefront).
 */
?>
</div><!-- #content -->

<!-- ═══════ FOOTER ═══════ -->
<footer class="sa-footer" id="sa-footer">
  <div class="sa-ft-grid">
    <!-- Brand -->
    <div>
      <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/logo-saisonart-light.svg'); ?>"
           alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="sa-ft-logo">
      <p class="sa-ft-tagline">&laquo;&nbsp;L'art original accessible — choisi, raconté, livré chez vous.&nbsp;&raquo;</p>
      <div class="sa-ft-social">
        <a href="<?php echo esc_url(home_url('/news/')); ?>" aria-label="Newsletter">
          <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        </a>
      </div>
    </div>

    <!-- Galerie -->
    <div class="sa-ft-col">
      <h4>Galerie</h4>
      <ul>
        <li><a href="<?php echo esc_url(home_url('/boutique/')); ?>">Œuvres originales</a></li>
        <li><a href="<?php echo esc_url(home_url('/boutique/?orderby=popularity')); ?>">Maîtres</a></li>
        <li><a href="<?php echo esc_url(home_url('/boutique/?orderby=date')); ?>">Nouvelles acquisitions</a></li>
        <li><a href="<?php echo esc_url(home_url('/boutique/')); ?>">Tous les tableaux</a></li>
      </ul>
    </div>

    <!-- Magazine -->
    <div class="sa-ft-col">
      <h4>Magazine</h4>
      <ul>
        <li><a href="<?php echo esc_url(home_url('/news/')); ?>">Tous les articles</a></li>
      </ul>
    </div>

    <!-- Info -->
    <div class="sa-ft-col">
      <h4>Informations</h4>
      <ul>
        <li><a href="<?php echo esc_url(home_url('/conseil/')); ?>">Conseil & expertise</a></li>
        <li><a href="<?php echo esc_url(home_url('/contact-us/')); ?>">Contact</a></li>
        <li><a href="<?php echo esc_url(home_url('/mentions-legales/')); ?>">Mentions légales</a></li>
        <li><a href="<?php echo esc_url(home_url('/conditions-generales-de-vente/')); ?>">CGV</a></li>
        <li><a href="<?php echo esc_url(home_url('/politique-de-confidentialite/')); ?>">Confidentialité</a></li>
      </ul>
    </div>
  </div>

  <div class="sa-ft-bottom">
    <div class="sa-ft-copy">&copy; <?php echo date('Y'); ?> <span>SaisonArt</span> — L'art pour tous</div>
  </div>
</footer>

<!-- ═══════ ENGAGEMENT OVERLAYS ═══════ -->
<?php $sa_s = sa_engage_get(); ?>

<?php if (!empty($sa_s['toasts_enabled']) && $sa_s['toasts_enabled'] !== '0') : ?>
<div class="sa-toast-container" id="saToasts"></div>
<?php endif; ?>

<?php if (!empty($sa_s['quiz_enabled']) && $sa_s['quiz_enabled'] !== '0') : ?>
<div class="sa-quiz-overlay" id="saQuiz">
  <div class="sa-quiz-sheet">
    <div class="sa-quiz-drag"></div>
    <button class="sa-quiz-close" aria-label="Fermer">
      <svg viewBox="0 0 24 24" width="18" height="18"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
    <div class="sa-quiz-progress">
      <span class="sa-quiz-dot is-active" data-step="0"></span>
      <span class="sa-quiz-dot" data-step="1"></span>
      <span class="sa-quiz-dot" data-step="2"></span>
    </div>

    <div class="sa-quiz-steps">
      <!-- Step 1: Style -->
      <div class="sa-quiz-step is-active" data-step="0">
        <h3>Quel style vous inspire ?</h3>
        <div class="sa-quiz-options sa-quiz-grid">
          <?php
          $styles = array_filter(explode("\n", $sa_s['quiz_styles']));
          foreach ($styles as $style) :
              $style = trim($style);
              if (!$style) continue;
          ?>
            <button class="sa-quiz-option" data-value="<?php echo esc_attr(sanitize_title($style)); ?>"><?php echo esc_html($style); ?></button>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Step 2: Budget -->
      <div class="sa-quiz-step" data-step="1">
        <h3>Votre budget</h3>
        <div class="sa-quiz-options sa-quiz-pills">
          <button class="sa-quiz-option" data-value="under-500">&lt; 500 &euro;</button>
          <button class="sa-quiz-option" data-value="500-1500">500 – 1 500 &euro;</button>
          <button class="sa-quiz-option" data-value="over-1500">&gt; 1 500 &euro;</button>
        </div>
      </div>

      <!-- Step 3: Room -->
      <div class="sa-quiz-step" data-step="2">
        <h3>Pour quelle pi&egrave;ce ?</h3>
        <div class="sa-quiz-options sa-quiz-pills">
          <button class="sa-quiz-option" data-value="salon">Salon</button>
          <button class="sa-quiz-option" data-value="chambre">Chambre</button>
          <button class="sa-quiz-option" data-value="bureau">Bureau</button>
          <button class="sa-quiz-option" data-value="autre">Autre</button>
        </div>
      </div>
    </div>

    <!-- Result / email capture -->
    <div class="sa-quiz-result" id="saQuizResult">
      <div class="sa-quiz-result-icon">
        <svg viewBox="0 0 24 24" width="32" height="32"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
      </div>
      <h3>Vos recommandations arrivent&hellip;</h3>
      <p>Recevez une s&eacute;lection personnalis&eacute;e directement dans votre bo&icirc;te.</p>
      <div class="sa-quiz-email-form">
        <input type="email" class="sa-quiz-email" placeholder="votre@email.fr">
        <button class="sa-quiz-submit">Recevoir mes suggestions</button>
      </div>
      <p class="sa-quiz-note">Pas de spam. D&eacute;sinscription en 1 clic.</p>
    </div>

    <div class="sa-quiz-nav">
      <button class="sa-quiz-prev" style="display:none">Retour</button>
      <button class="sa-quiz-next">Suivant</button>
    </div>
  </div>
</div>
<?php endif; ?>

<?php if (!empty($sa_s['exit_enabled']) && $sa_s['exit_enabled'] !== '0') : ?>
<div class="sa-exit-overlay" id="saExit">
  <div class="sa-exit-modal">
    <button class="sa-exit-close" aria-label="Fermer">
      <svg viewBox="0 0 24 24" width="18" height="18"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
    <div class="sa-exit-badge">OFFRE EXCLUSIVE</div>
    <h3><?php echo esc_html($sa_s['exit_title']); ?></h3>
    <p class="sa-exit-desc"><?php echo esc_html($sa_s['exit_discount']); ?></p>
    <div class="sa-exit-code" id="saExitCode" title="Cliquer pour copier"><?php echo esc_html($sa_s['exit_code']); ?></div>
    <div class="sa-exit-email-form">
      <input type="email" class="sa-exit-email" placeholder="votre@email.fr">
      <button class="sa-exit-submit">Recevoir le code</button>
    </div>
    <p class="sa-exit-note">Valable 48h. Une seule utilisation.</p>
  </div>
</div>
<?php endif; ?>

<?php if (!empty($sa_s['hearts_enabled']) && $sa_s['hearts_enabled'] !== '0') : ?>
<div class="sa-wl-prompt" id="saWlPrompt">
  <button class="sa-wl-prompt-close" aria-label="Fermer">
    <svg viewBox="0 0 24 24" width="14" height="14"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
  </button>
  <div class="sa-wl-prompt-icon">
    <svg viewBox="0 0 24 24" width="20" height="20"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
  </div>
  <h4>Sauvegardez vos coups de c&oelig;ur</h4>
  <div class="sa-wl-prompt-form">
    <input type="email" class="sa-wl-prompt-email" placeholder="votre@email.fr">
    <button class="sa-wl-prompt-submit">Synchroniser</button>
  </div>
</div>
<?php endif; ?>

</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
