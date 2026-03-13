/* ═══════════════════════════════════════════
   SaisonArt — Contact Page Interactions
   Form submits via sa_capture_email AJAX
═══════════════════════════════════════════ */
(function () {
  'use strict';

  var C = window.saEngageConfig || {};

  var btn = document.querySelector('.sa-contact-submit');
  if (!btn) return;

  btn.addEventListener('click', function () {
    var wrap = btn.closest('.sa-contact-form-wrap');
    if (!wrap) return;

    // Gather fields
    var nameIn = wrap.querySelector('input[name="name"]');
    var emailIn = wrap.querySelector('input[name="email"]');
    var subjectIn = wrap.querySelector('select[name="subject"]');
    var messageIn = wrap.querySelector('textarea[name="message"]');

    // Validate email
    if (!emailIn || !emailIn.value || emailIn.value.indexOf('@') < 1) {
      if (emailIn) {
        emailIn.style.borderColor = 'rgba(200,80,80,.5)';
        setTimeout(function () { emailIn.style.borderColor = ''; }, 2000);
      }
      return;
    }

    // Validate message
    if (!messageIn || !messageIn.value.trim()) {
      messageIn.style.borderColor = 'rgba(200,80,80,.5)';
      setTimeout(function () { messageIn.style.borderColor = ''; }, 2000);
      return;
    }

    var email = emailIn.value.trim();
    var data = {
      name: nameIn ? nameIn.value.trim() : '',
      subject: subjectIn ? subjectIn.value : '',
      message: messageIn ? messageIn.value.trim() : ''
    };

    // Disable
    btn.disabled = true;
    var originalText = btn.textContent;
    btn.textContent = 'Envoi...';

    if (C.ajax_url && C.nonce) {
      var xhr = new XMLHttpRequest();
      xhr.open('POST', C.ajax_url, true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
          wrap.classList.add('is-sent');
        }
      };
      xhr.send(
        'action=sa_capture_email' +
        '&nonce=' + encodeURIComponent(C.nonce) +
        '&email=' + encodeURIComponent(email) +
        '&source=contact' +
        '&data=' + encodeURIComponent(JSON.stringify(data))
      );
    } else {
      wrap.classList.add('is-sent');
    }
  });
})();
