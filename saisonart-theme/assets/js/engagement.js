/* ═══════════════════════════════════════════
   SaisonArt — Engagement System
   Reads config from window.saEngageConfig
═══════════════════════════════════════════ */
(function ($) {
  'use strict';

  var C = window.saEngageConfig || {};

  /* ── Helpers ──────────────────────────── */
  function ls(key, val) {
    try {
      if (val === undefined) return localStorage.getItem(key);
      if (val === null) { localStorage.removeItem(key); return; }
      localStorage.setItem(key, typeof val === 'object' ? JSON.stringify(val) : val);
    } catch (e) { return null; }
  }
  function lsJson(key) {
    try { return JSON.parse(localStorage.getItem(key)); } catch (e) { return null; }
  }
  function ss(key, val) {
    try {
      if (val === undefined) return sessionStorage.getItem(key);
      sessionStorage.setItem(key, typeof val === 'object' ? JSON.stringify(val) : val);
    } catch (e) { return null; }
  }
  function ssJson(key) {
    try { return JSON.parse(sessionStorage.getItem(key)); } catch (e) { return null; }
  }
  function isMobile() { return window.innerWidth <= 768; }
  function daysSince(ts) { return ts ? (Date.now() - Number(ts)) / 86400000 : Infinity; }

  /* ── Send email via AJAX → Resend ──── */
  function sendEmail(email, source, data, onSuccess, onError) {
    if (!C.ajax_url || !C.nonce) {
      if (onSuccess) onSuccess();
      return;
    }
    $.ajax({
      url: C.ajax_url,
      type: 'POST',
      data: {
        action: 'sa_capture_email',
        nonce: C.nonce,
        email: email,
        source: source,
        data: data || ''
      },
      success: function (res) {
        if (onSuccess) onSuccess(res);
      },
      error: function () {
        if (onError) onError();
      }
    });
  }

  /* ══════════════════════════════════════
     1. ANNOUNCEMENT BAR
  ══════════════════════════════════════ */
  function initAnnounce() {
    if (C.announce_enabled === '0') return;
    var bar = document.querySelector('.sa-announce');
    if (!bar) return;

    // Check dismissed
    var dismissed = ls('sa_announce_dismissed');
    if (dismissed && daysSince(dismissed) < 3) {
      bar.classList.add('is-dismissed');
      return;
    }

    // Close button
    var closeBtn = bar.querySelector('.sa-announce-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', function () {
        bar.classList.add('is-dismissed');
        ls('sa_announce_dismissed', Date.now());
      });
    }

    // Rotation (desktop only, multiple messages)
    var texts = bar.querySelectorAll('.sa-announce-text');
    if (texts.length > 1 && !isMobile()) {
      var current = 0;
      var interval = (parseInt(C.announce_interval, 10) || 4) * 1000;
      setInterval(function () {
        texts[current].classList.remove('is-active');
        current = (current + 1) % texts.length;
        texts[current].classList.add('is-active');
      }, interval);
    }
    // Mobile: only show first message
    if (isMobile() && texts.length > 0) {
      texts[0].classList.add('is-mobile-primary');
    }
  }

  /* ══════════════════════════════════════
     2. TOAST SYSTEM
  ══════════════════════════════════════ */
  var toastQueue = {
    queue: [],
    isShowing: false,
    lastShown: 0,
    container: null,
    minInterval: (parseInt(C.toast_interval, 10) || 8) * 1000,
    displayTime: (parseInt(C.toast_duration, 10) || 6) * 1000,

    init: function () {
      if (C.toasts_enabled === '0') return;
      this.container = document.getElementById('saToasts');
      if (!this.container) return;

      var shown = ssJson('sa_toasts_shown') || [];
      var firstDelay = (parseInt(C.toast_first_delay, 10) || 5) * 1000;
      var self = this;

      // Toast 1: Nouveautes (after firstDelay)
      if (shown.indexOf('info') === -1) {
        setTimeout(function () {
          self.add({
            type: 'info',
            icon: 'palette',
            title: 'Nouveautés',
            text: C.toast_msg_1 || '3 nouvelles œuvres cette semaine'
          });
        }, firstDelay);
      }

      // Toast 2: Social proof on scroll past .sa-selection
      var selSection = document.querySelector('.sa-selection');
      if (selSection && shown.indexOf('social') === -1) {
        var obs = new IntersectionObserver(function (entries) {
          if (!entries[0].isIntersecting) {
            var x = Math.floor(Math.random() * 10) + 3;
            var msg = (C.toast_msg_2 || '{x} personnes consultent cette œuvre').replace('{x}', x);
            self.add({ type: 'social', icon: 'users', title: 'Populaire', text: msg });
            obs.disconnect();
          }
        }, { threshold: 0 });
        obs.observe(selSection);
      }

      // Toast 3: Shipping (after 20s)
      if (shown.indexOf('shipping') === -1) {
        setTimeout(function () {
          self.add({
            type: 'shipping',
            icon: 'truck',
            title: 'Livraison offerte',
            text: C.toast_msg_3 || 'Dès 150 € d\'achat, livraison gratuite en France'
          });
        }, 20000);
      }

      // Toast 4: Social proof purchase (after 40s)
      if (shown.indexOf('purchase') === -1) {
        setTimeout(function () {
          var names = ['Marie', 'Sophie', 'Pierre', 'Antoine', 'Claire', 'Isabelle', 'Thomas', 'Julie', 'Nicolas', 'Camille'];
          var name = names[Math.floor(Math.random() * names.length)];
          var msg = (C.toast_msg_4 || '{name} vient d\'ajouter une œuvre à son panier').replace('{name}', name);
          self.add({ type: 'purchase', icon: 'cart', title: '', text: msg });
        }, 40000);
      }
    },

    add: function (toast) {
      this.queue.push(toast);
      this.process();
    },

    process: function () {
      if (this.isShowing || !this.queue.length) return;
      var elapsed = Date.now() - this.lastShown;
      var self = this;
      if (elapsed < this.minInterval) {
        setTimeout(function () { self.process(); }, this.minInterval - elapsed);
        return;
      }
      this.show(this.queue.shift());
    },

    show: function (toast) {
      var self = this;
      this.isShowing = true;
      this.lastShown = Date.now();

      // Mark as shown in session
      var shown = ssJson('sa_toasts_shown') || [];
      shown.push(toast.type);
      ss('sa_toasts_shown', shown);

      var iconSvg = this.getIcon(toast.icon);
      var el = document.createElement('div');
      el.className = 'sa-toast';
      el.style.setProperty('--sa-toast-dur', (this.displayTime / 1000) + 's');
      el.innerHTML =
        '<div class="sa-toast-icon">' + iconSvg + '</div>' +
        '<div class="sa-toast-body">' +
          (toast.title ? '<div class="sa-toast-title">' + toast.title + '</div>' : '') +
          '<div class="sa-toast-text">' + toast.text + '</div>' +
        '</div>' +
        '<button class="sa-toast-close" aria-label="Fermer">' +
          '<svg viewBox="0 0 24 24" width="12" height="12"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>' +
        '</button>' +
        '<div class="sa-toast-progress"></div>';

      this.container.appendChild(el);

      // Close button
      el.querySelector('.sa-toast-close').addEventListener('click', function () {
        self.dismiss(el);
      });

      // Show
      requestAnimationFrame(function () {
        requestAnimationFrame(function () {
          el.classList.add('is-visible');
        });
      });

      // Auto dismiss
      setTimeout(function () {
        self.dismiss(el);
      }, this.displayTime);
    },

    dismiss: function (el) {
      if (el.dataset.dismissed) return;
      el.dataset.dismissed = '1';
      var self = this;
      el.classList.remove('is-visible');
      el.classList.add('is-exiting');
      setTimeout(function () {
        if (el.parentNode) el.parentNode.removeChild(el);
        self.isShowing = false;
        self.process();
      }, 450);
    },

    getIcon: function (type) {
      var icons = {
        palette: '<svg viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c1.1 0 2-.9 2-2 0-.5-.2-1-.5-1.3-.3-.3-.5-.8-.5-1.3 0-1.1.9-2 2-2h2.4c3 0 5.6-2.5 5.6-5.6C23 6 18 2 12 2z"/><circle cx="7.5" cy="11" r="1.5"/><circle cx="10" cy="7" r="1.5"/><circle cx="15" cy="7" r="1.5"/><circle cx="17.5" cy="11" r="1.5"/></svg>',
        users: '<svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>',
        truck: '<svg viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
        cart: '<svg viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>'
      };
      return icons[type] || icons.palette;
    }
  };

  /* ══════════════════════════════════════
     3. STYLE QUIZ
  ══════════════════════════════════════ */
  function initQuiz() {
    if (C.quiz_enabled === '0') return;
    var overlay = document.getElementById('saQuiz');
    if (!overlay) return;

    // Already completed or recently dismissed?
    if (ls('sa_quiz_completed')) return;
    var dismissed = ls('sa_quiz_dismissed');
    if (dismissed && daysSince(dismissed) < 7) return;

    var sheet = overlay.querySelector('.sa-quiz-sheet');
    var steps = overlay.querySelectorAll('.sa-quiz-step');
    var dots = overlay.querySelectorAll('.sa-quiz-dot');
    var prevBtn = overlay.querySelector('.sa-quiz-prev');
    var nextBtn = overlay.querySelector('.sa-quiz-next');
    var resultPanel = document.getElementById('saQuizResult');
    var currentStep = 0;
    var answers = {};
    var triggered = false;

    function openQuiz() {
      if (triggered) return;
      triggered = true;
      overlay.classList.add('is-open');
      document.body.style.overflow = 'hidden';
    }

    function closeQuiz(completed) {
      overlay.classList.remove('is-open');
      document.body.style.overflow = '';
      if (completed) {
        ls('sa_quiz_completed', Date.now());
      } else {
        ls('sa_quiz_dismissed', Date.now());
      }
    }

    // Triggers
    var delay = (parseInt(C.quiz_delay, 10) || 30) * 1000;
    var scrollThreshold = (parseInt(C.quiz_scroll, 10) || 50) / 100;
    var timer = setTimeout(openQuiz, delay);

    window.addEventListener('scroll', function quizScroll() {
      var scrollRatio = window.scrollY / (document.documentElement.scrollHeight - window.innerHeight);
      if (scrollRatio >= scrollThreshold) {
        openQuiz();
        window.removeEventListener('scroll', quizScroll);
        clearTimeout(timer);
      }
    }, { passive: true });

    // Close
    overlay.querySelector('.sa-quiz-close').addEventListener('click', function () {
      closeQuiz(false);
    });
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) closeQuiz(false);
    });

    // Option selection
    overlay.addEventListener('click', function (e) {
      var opt = e.target.closest('.sa-quiz-option');
      if (!opt) return;
      var step = opt.closest('.sa-quiz-step');
      step.querySelectorAll('.sa-quiz-option').forEach(function (o) {
        o.classList.remove('is-selected');
      });
      opt.classList.add('is-selected');
      var stepIdx = parseInt(step.dataset.step, 10);
      answers['step' + stepIdx] = opt.dataset.value;
    });

    // Navigation
    function goToStep(idx) {
      steps.forEach(function (s) { s.classList.remove('is-active'); });
      dots.forEach(function (d, i) {
        d.classList.remove('is-active');
        if (i < idx) d.classList.add('is-done');
        else d.classList.remove('is-done');
      });
      if (idx < steps.length) {
        steps[idx].classList.add('is-active');
        dots[idx].classList.add('is-active');
      }
      currentStep = idx;
      prevBtn.style.display = idx === 0 ? 'none' : '';
      if (idx >= steps.length) {
        // Show result
        overlay.querySelector('.sa-quiz-steps').style.display = 'none';
        overlay.querySelector('.sa-quiz-nav').style.display = 'none';
        resultPanel.classList.add('is-active');
      } else {
        nextBtn.textContent = idx === steps.length - 1 ? 'Voir mes suggestions' : 'Suivant';
      }
    }

    nextBtn.addEventListener('click', function () {
      if (!answers['step' + currentStep]) {
        // Flash options to hint selection needed
        var activeStep = steps[currentStep];
        activeStep.querySelectorAll('.sa-quiz-option').forEach(function (o) {
          o.style.borderColor = 'rgba(139,94,60,.5)';
          setTimeout(function () { o.style.borderColor = ''; }, 600);
        });
        return;
      }
      goToStep(currentStep + 1);
    });

    prevBtn.addEventListener('click', function () {
      if (currentStep > 0) goToStep(currentStep - 1);
    });

    // Email submit
    var submitBtn = overlay.querySelector('.sa-quiz-submit');
    var emailInput = overlay.querySelector('.sa-quiz-email');
    submitBtn.addEventListener('click', function () {
      var email = emailInput.value.trim();
      if (email && email.indexOf('@') > 0) {
        ls('sa_quiz_email', email);
        ls('sa_quiz_data', answers);
        submitBtn.textContent = 'Envoi...';
        submitBtn.disabled = true;
        var dataStr = Object.keys(answers).map(function (k) { return answers[k]; }).join(', ');
        sendEmail(email, 'quiz', dataStr, function () {
          submitBtn.textContent = 'Merci !';
          setTimeout(function () { closeQuiz(true); }, 1500);
        });
      } else {
        emailInput.style.borderColor = 'rgba(139,94,60,.8)';
        setTimeout(function () { emailInput.style.borderColor = ''; }, 1000);
      }
    });

    // Mobile drag to dismiss
    if (isMobile()) {
      var drag = overlay.querySelector('.sa-quiz-drag');
      var startY = 0;
      var currentY = 0;
      drag.addEventListener('touchstart', function (e) {
        startY = e.touches[0].clientY;
      }, { passive: true });
      drag.addEventListener('touchmove', function (e) {
        currentY = e.touches[0].clientY;
        var dy = currentY - startY;
        if (dy > 0) {
          sheet.style.transform = 'translateY(' + dy + 'px)';
        }
      }, { passive: true });
      drag.addEventListener('touchend', function () {
        var dy = currentY - startY;
        if (dy > 100) {
          closeQuiz(false);
        }
        sheet.style.transform = '';
        startY = 0;
        currentY = 0;
      });
    }
  }

  /* ══════════════════════════════════════
     4. HEARTS / WISHLIST
  ══════════════════════════════════════ */
  function initHearts() {
    if (C.hearts_enabled === '0') return;

    var wishlist = lsJson('sa_wishlist') || [];
    var prompt = document.getElementById('saWlPrompt');

    // Restore state on page load
    document.querySelectorAll('.sa-heart').forEach(function (btn) {
      var pid = btn.dataset.productId;
      if (wishlist.indexOf(pid) > -1) {
        btn.classList.add('is-liked');
      }
    });

    // Event delegation for hearts (survives AJAX pagination)
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.sa-heart');
      if (!btn) return;
      e.preventDefault();
      e.stopPropagation();

      var pid = btn.dataset.productId;
      var idx = wishlist.indexOf(pid);

      if (idx > -1) {
        wishlist.splice(idx, 1);
        btn.classList.remove('is-liked');
      } else {
        wishlist.push(pid);
        btn.classList.add('is-liked');
      }
      ls('sa_wishlist', wishlist);

      // Show email prompt on 2nd heart
      if (wishlist.length >= 2 && !ls('sa_quiz_email') && !ls('sa_wishlist_email') && !ls('sa_wl_prompt_dismissed') && prompt) {
        setTimeout(function () {
          prompt.classList.add('is-visible');
        }, 500);
      }
    });

    // Wishlist prompt handlers
    if (prompt) {
      prompt.querySelector('.sa-wl-prompt-close').addEventListener('click', function () {
        prompt.classList.remove('is-visible');
        ls('sa_wl_prompt_dismissed', Date.now());
      });

      prompt.querySelector('.sa-wl-prompt-submit').addEventListener('click', function () {
        var input = prompt.querySelector('.sa-wl-prompt-email');
        var submitWl = prompt.querySelector('.sa-wl-prompt-submit');
        var email = input.value.trim();
        if (email && email.indexOf('@') > 0) {
          ls('sa_wishlist_email', email);
          submitWl.textContent = 'Envoi...';
          submitWl.disabled = true;
          sendEmail(email, 'wishlist', wishlist.join(','), function () {
            prompt.querySelector('h4').textContent = 'Synchronisé !';
            setTimeout(function () {
              prompt.classList.remove('is-visible');
            }, 1500);
          });
        } else {
          input.style.borderColor = 'rgba(139,94,60,.8)';
          setTimeout(function () { input.style.borderColor = ''; }, 1000);
        }
      });
    }
  }

  /* ══════════════════════════════════════
     5. EXIT-INTENT
  ══════════════════════════════════════ */
  function initExitIntent() {
    if (C.exit_enabled === '0') return;
    var overlay = document.getElementById('saExit');
    if (!overlay) return;

    // Already shown this session or dismissed permanently
    if (ss('sa_exit_shown') || ls('sa_exit_dismissed')) return;

    var codeEl = document.getElementById('saExitCode');

    function showExit() {
      if (ss('sa_exit_shown')) return;
      // Don't show if quiz is open
      var quizOverlay = document.getElementById('saQuiz');
      if (quizOverlay && quizOverlay.classList.contains('is-open')) return;

      ss('sa_exit_shown', '1');
      overlay.classList.add('is-open');
      document.body.style.overflow = 'hidden';
    }

    function closeExit() {
      overlay.classList.remove('is-open');
      document.body.style.overflow = '';
      ls('sa_exit_dismissed', '1');
    }

    // Desktop: mouseleave
    if (!isMobile()) {
      document.documentElement.addEventListener('mouseleave', function (e) {
        if (e.clientY < 0) showExit();
      });
    } else {
      // Mobile: inactivity timer
      var mobileDelay = (parseInt(C.exit_mobile_delay, 10) || 45) * 1000;
      var inactivityTimer = setTimeout(showExit, mobileDelay);
      var resetTimer = function () {
        clearTimeout(inactivityTimer);
        inactivityTimer = setTimeout(showExit, mobileDelay);
      };
      window.addEventListener('touchstart', resetTimer, { passive: true });
      window.addEventListener('scroll', resetTimer, { passive: true });
    }

    // Close handlers
    overlay.querySelector('.sa-exit-close').addEventListener('click', closeExit);
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) closeExit();
    });

    // Copy code
    if (codeEl) {
      codeEl.addEventListener('click', function () {
        var code = codeEl.textContent.trim();
        if (navigator.clipboard) {
          navigator.clipboard.writeText(code);
        }
        var orig = codeEl.textContent;
        codeEl.textContent = 'Copié !';
        codeEl.classList.add('is-copied');
        setTimeout(function () {
          codeEl.textContent = orig;
          codeEl.classList.remove('is-copied');
        }, 2000);
      });
    }

    // Email submit
    var submitBtn = overlay.querySelector('.sa-exit-submit');
    var emailInput = overlay.querySelector('.sa-exit-email');
    if (submitBtn && emailInput) {
      submitBtn.addEventListener('click', function () {
        var email = emailInput.value.trim();
        if (email && email.indexOf('@') > 0) {
          ls('sa_exit_email', email);
          submitBtn.textContent = 'Envoi...';
          submitBtn.disabled = true;
          sendEmail(email, 'exit', '', function () {
            submitBtn.textContent = 'Envoyé !';
            setTimeout(closeExit, 1500);
          });
        } else {
          emailInput.style.borderColor = 'rgba(139,94,60,.8)';
          setTimeout(function () { emailInput.style.borderColor = ''; }, 1000);
        }
      });
    }
  }

  /* ══════════════════════════════════════
     6. STICKY CTA BAR
  ══════════════════════════════════════ */
  function initStickyCTA() {
    if (C.sticky_enabled === '0') return;
    var bar = document.getElementById('saStickyBar');
    var section = document.querySelector('.sa-selection');
    if (!bar || !section) return;

    var observer = new IntersectionObserver(function (entries) {
      bar.classList.toggle('is-fixed', !entries[0].isIntersecting);
    }, { threshold: 0 });

    observer.observe(section);
  }

  /* ══════════════════════════════════════
     7. VISIT COUNTER
  ══════════════════════════════════════ */
  function trackVisit() {
    var count = parseInt(ls('sa_visit_count'), 10) || 0;
    ls('sa_visit_count', count + 1);
  }

  /* ══════════════════════════════════════
     INIT
  ══════════════════════════════════════ */
  $(document).ready(function () {
    trackVisit();
    initAnnounce();
    initHearts();
    initStickyCTA();

    // Delayed inits
    toastQueue.init();
    setTimeout(initQuiz, 1000);
    setTimeout(initExitIntent, 5000);
  });

})(jQuery);
