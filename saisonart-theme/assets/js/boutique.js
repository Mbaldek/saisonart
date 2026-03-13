/* ═══════════════════════════════════════════
   SaisonArt — Boutique & Product Page JS
   Gallery thumbnails
═══════════════════════════════════════════ */
(function () {
  'use strict';

  /* ── Gallery thumbnail switching ────────── */
  var thumbs = document.querySelectorAll('.sa-gallery-thumb');
  var mainImg = document.getElementById('saMainImg');

  if (thumbs.length && mainImg) {
    thumbs.forEach(function (thumb) {
      thumb.addEventListener('click', function () {
        thumbs.forEach(function (t) { t.classList.remove('active'); });
        thumb.classList.add('active');
        var fullUrl = thumb.getAttribute('data-full');
        if (fullUrl) mainImg.src = fullUrl;
      });
    });
  }
})();
