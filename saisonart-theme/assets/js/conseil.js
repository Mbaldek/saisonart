/* ═══════════════════════════════════════════
   SaisonArt — Conseil Page Interactions
   Forms submit via sa_capture_email AJAX
═══════════════════════════════════════════ */
(function () {
  'use strict';

  var C = window.saEngageConfig || {};

  /* ── Scroll-reveal (cards + garanties) ──── */
  var revealObserver = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        revealObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15 });

  document.querySelectorAll('.sa-sit').forEach(function (el) {
    revealObserver.observe(el);
  });
  document.querySelectorAll('.sa-garantie').forEach(function (el) {
    revealObserver.observe(el);
  });

  /* ── Toggle situation cards ─────────────── */
  document.querySelectorAll('.sa-sit-trigger').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      var targetId = btn.getAttribute('data-target');
      var target = document.getElementById(targetId);
      if (!target) return;

      // Close others
      document.querySelectorAll('.sa-sit').forEach(function (sit) {
        if (sit.id !== targetId) sit.classList.remove('is-open');
      });

      target.classList.toggle('is-open');

      if (target.classList.contains('is-open')) {
        setTimeout(function () {
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 80);
      }
    });
  });

  /* ── RDV slot selection ─────────────────── */
  document.querySelectorAll('.sa-rdv-slot').forEach(function (slot) {
    slot.addEventListener('click', function () {
      slot.closest('.sa-rdv-slots').querySelectorAll('.sa-rdv-slot').forEach(function (s) {
        s.classList.remove('selected');
      });
      slot.classList.add('selected');
    });
  });

  /* ── Upload zone ────────────────────────── */
  var uploadZone = document.getElementById('saUploadZone');
  if (uploadZone) {
    var fileInput = uploadZone.querySelector('input[type="file"]');

    uploadZone.addEventListener('click', function () {
      fileInput.click();
    });

    fileInput.addEventListener('change', function () {
      if (fileInput.files && fileInput.files.length > 0) {
        var fileName = fileInput.files[0].name;
        uploadZone.classList.add('has-file');
        uploadZone.querySelector('span').textContent = fileName;
      }
    });
  }

  /* ── Form submission ────────────────────── */
  document.querySelectorAll('.sa-btn-submit').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var sit = btn.closest('.sa-sit');
      var source = btn.getAttribute('data-source');
      if (!sit || !source) return;

      // Gather email
      var emailInput = sit.querySelector('input[type="email"]');
      if (!emailInput || !emailInput.value || emailInput.value.indexOf('@') < 1) {
        if (emailInput) {
          emailInput.style.borderColor = 'rgba(200,80,80,.5)';
          setTimeout(function () { emailInput.style.borderColor = ''; }, 2000);
        }
        return;
      }

      var email = emailInput.value.trim();

      // Gather form data per source type
      var data = {};

      if (source === 'conseil-recherche') {
        var desc = sit.querySelector('textarea[name="description"]');
        var budget = sit.querySelector('select[name="budget"]');
        data.description = desc ? desc.value : '';
        data.budget = budget ? budget.value : '';
      } else if (source === 'conseil-identification') {
        var desc2 = sit.querySelector('textarea[name="description"]');
        data.description = desc2 ? desc2.value : '';
        // Photo: just note that a file was attached (actual upload handled server-side in v2)
        var fileIn = sit.querySelector('input[type="file"]');
        data.has_photo = (fileIn && fileIn.files && fileIn.files.length > 0) ? 'oui' : 'non';
        if (data.has_photo === 'oui') {
          data.photo_name = fileIn.files[0].name;
        }
      } else if (source === 'conseil-rdv') {
        var nameIn = sit.querySelector('input[name="name"]');
        var subjectIn = sit.querySelector('select[name="subject"]');
        var modeIn = sit.querySelector('select[name="mode"]');
        var selectedSlot = sit.querySelector('.sa-rdv-slot.selected');
        data.name = nameIn ? nameIn.value : '';
        data.subject = subjectIn ? subjectIn.value : '';
        data.mode = modeIn ? modeIn.value : '';
        data.slot = selectedSlot ? selectedSlot.getAttribute('data-slot') : '';
      }

      // Disable button
      btn.disabled = true;
      var originalText = btn.textContent;
      btn.textContent = 'Envoi...';

      // Send via AJAX (reuse existing sa_capture_email endpoint)
      if (C.ajax_url && C.nonce) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', C.ajax_url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
          if (xhr.readyState === 4) {
            sit.classList.add('is-sent');
          }
        };
        xhr.send(
          'action=sa_capture_email' +
          '&nonce=' + encodeURIComponent(C.nonce) +
          '&email=' + encodeURIComponent(email) +
          '&source=' + encodeURIComponent(source) +
          '&data=' + encodeURIComponent(JSON.stringify(data))
        );
      } else {
        // Fallback: just show success
        sit.classList.add('is-sent');
      }
    });
  });
})();
