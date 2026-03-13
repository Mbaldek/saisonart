<?php
/**
 * SaisonArt — Custom footer (overrides Storefront).
 */
?>
</div><!-- #content -->

<!-- ═══════ FOOTER ═══════ -->
<footer class="sa-footer" id="colophon">
  <div class="sa-ft-grid">
    <!-- Brand -->
    <div>
      <?php
      $logo_id = get_theme_mod('custom_logo');
      if ($logo_id) {
          echo wp_get_attachment_image($logo_id, 'full', false, array('class' => 'sa-ft-logo', 'alt' => get_bloginfo('name')));
      } else {
      ?>
        <img src="https://i0.wp.com/saisonart.com/wp-content/uploads/2025/08/cropped-logo_siteweb-1.jpg?fit=1241%2C124&ssl=1"
             alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="sa-ft-logo">
      <?php } ?>
      <p class="sa-ft-tagline">&laquo;&nbsp;L'art original accessible — choisi, raconté, livré chez vous.&nbsp;&raquo;</p>
      <div class="sa-ft-social">
        <a href="#" aria-label="Instagram">
          <svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
        </a>
        <a href="#" aria-label="Facebook">
          <svg viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
        </a>
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
        <li><a href="<?php echo esc_url(home_url('/boutique/')); ?>">Maîtres</a></li>
        <li><a href="<?php echo esc_url(home_url('/boutique/')); ?>">Nouvelles acquisitions</a></li>
        <li><a href="<?php echo esc_url(home_url('/boutique/')); ?>">Tous les tableaux</a></li>
      </ul>
    </div>

    <!-- Magazine -->
    <div class="sa-ft-col">
      <h4>Magazine</h4>
      <ul>
        <li><a href="<?php echo esc_url(home_url('/news/')); ?>">Tous les articles</a></li>
        <li><a href="<?php echo esc_url(home_url('/news/')); ?>">Impressionnistes</a></li>
        <li><a href="<?php echo esc_url(home_url('/news/')); ?>">Collectionner</a></li>
        <li><a href="<?php echo esc_url(home_url('/news/')); ?>">Histoire de l'art</a></li>
      </ul>
    </div>

    <!-- Info -->
    <div class="sa-ft-col">
      <h4>Informations</h4>
      <ul>
        <li><a href="<?php echo esc_url(home_url('/contact-us/')); ?>">Contact</a></li>
        <li><a href="<?php echo esc_url(home_url('/contact-us/')); ?>">Expédition & livraison</a></li>
        <li><a href="<?php echo esc_url(home_url('/contact-us/')); ?>">Authenticité</a></li>
        <li><a href="<?php echo esc_url(home_url('/contact-us/')); ?>">FAQ</a></li>
      </ul>
    </div>
  </div>

  <div class="sa-ft-bottom">
    <div class="sa-ft-copy">&copy; <?php echo date('Y'); ?> <span>SaisonArt</span> — L'art pour tous</div>
    <div class="sa-ft-legal">
      <a href="<?php echo esc_url(home_url('/contact-us/')); ?>">Mentions légales</a>
      <a href="<?php echo esc_url(home_url('/contact-us/')); ?>">CGV</a>
      <a href="<?php echo esc_url(home_url('/contact-us/')); ?>">Confidentialité</a>
    </div>
  </div>
</footer>

</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
