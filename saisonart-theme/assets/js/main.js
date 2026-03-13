/* SaisonArt - Main JavaScript */
(function ($) {
  'use strict';

  /* ── Header scroll state ────────────────────────────────── */
  function initHeaderScroll() {
    var header = document.querySelector('.sa-header');
    if (!header) return;

    function update() {
      header.classList.toggle('scrolled', window.scrollY > 40);
    }
    window.addEventListener('scroll', update, { passive: true });
    update();
  }

  /* ── Mobile burger menu ─────────────────────────────────── */
  function initBurger() {
    var burger = document.querySelector('.sa-header-burger');
    var menu = document.querySelector('.sa-header-mobile-menu');
    if (!burger || !menu) return;

    burger.addEventListener('click', function () {
      menu.classList.toggle('is-open');
      burger.classList.toggle('is-open');
    });

    menu.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        menu.classList.remove('is-open');
        burger.classList.remove('is-open');
      });
    });
  }

  /* ── Scroll reveal ──────────────────────────────────────── */
  function initScrollReveal() {
    var elements = document.querySelectorAll('.reveal');
    if (!elements.length) return;

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });

    elements.forEach(function (el) {
      observer.observe(el);
    });
  }

  /* ── Stack carousel (homepage only) ─────────────────────── */
  function initStackCarousel() {
    var stack = document.getElementById('saStack');
    if (!stack) return;

    var cards = Array.from(stack.querySelectorAll('.sa-card'));
    var dots = document.querySelectorAll('.sa-sel-dot');
    var currentCard = 0;
    var autoPlay;

    function updateStack(active) {
      var total = cards.length;
      cards.forEach(function (card, i) {
        var offset = (i - active + total) % total;
        var imgEl = card.querySelector('.sa-card-img');
        card.style.zIndex = total - offset;
        card.style.left = '50%';
        if (offset === 0) {
          // Active card — morph wider, landscape ratio
          card.style.transform = 'translateX(-50%) translateY(0) rotateY(0deg) scale(1)';
          card.style.opacity = '1';
          card.style.filter = 'blur(0)';
          card.style.width = '400px';
          if (imgEl) imgEl.style.paddingTop = '70%';
        } else if (offset === 1) {
          card.style.transform = 'translateX(calc(-50% + 220px)) translateY(22px) rotateY(-8deg) scale(.95)';
          card.style.opacity = '.75';
          card.style.filter = 'blur(2px)';
          card.style.width = '260px';
          if (imgEl) imgEl.style.paddingTop = '85%';
        } else if (offset === 2) {
          card.style.transform = 'translateX(calc(-50% + 380px)) translateY(40px) rotateY(-14deg) scale(.9)';
          card.style.opacity = '.45';
          card.style.filter = 'blur(4px)';
          card.style.width = '260px';
          if (imgEl) imgEl.style.paddingTop = '85%';
        } else {
          card.style.transform = 'translateX(calc(-50% - 220px)) translateY(22px) rotateY(8deg) scale(.95)';
          card.style.opacity = '.75';
          card.style.filter = 'blur(2px)';
          card.style.width = '260px';
          if (imgEl) imgEl.style.paddingTop = '85%';
        }
      });
      dots.forEach(function (d, i) {
        d.classList.toggle('active', i === active);
      });
    }

    function goToCard(i) {
      currentCard = i;
      updateStack(i);
    }

    // Click on card
    cards.forEach(function (card, i) {
      card.addEventListener('click', function () {
        if (i !== currentCard) goToCard(i);
      });
    });

    // Click on dot
    dots.forEach(function (dot, i) {
      dot.addEventListener('click', function () {
        goToCard(i);
      });
    });

    // Auto-rotate
    function startAutoPlay() {
      autoPlay = setInterval(function () {
        currentCard = (currentCard + 1) % cards.length;
        updateStack(currentCard);
      }, 3200);
    }

    stack.addEventListener('mouseenter', function () {
      clearInterval(autoPlay);
    });
    stack.addEventListener('mouseleave', function () {
      startAutoPlay();
    });

    updateStack(0);
    startAutoPlay();
  }

  /* ── Stat number count-up ──────────────────────────────── */
  function initCountUp() {
    var stats = document.querySelectorAll('.sa-pq-stat-num');
    if (!stats.length) return;

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          var el = entry.target;
          var sup = el.querySelector('sup');
          var targetText = el.textContent.replace(sup ? sup.textContent : '', '').trim();
          var target = parseInt(targetText, 10);
          if (isNaN(target)) return;

          var duration = 1200;
          var startTime = performance.now();

          function tick(now) {
            var progress = Math.min((now - startTime) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            var current = Math.round(target * eased);
            el.childNodes[0].textContent = current;
            if (progress < 1) requestAnimationFrame(tick);
          }

          el.childNodes[0].textContent = '0';
          requestAnimationFrame(tick);
          observer.unobserve(el);
        }
      });
    }, { threshold: 0.5 });

    stats.forEach(function (el) { observer.observe(el); });
  }

  /* ── Newsletter — send via Resend AJAX ──────────────────── */
  function initNewsletter() {
    var btn = document.querySelector('.sa-nl-btn');
    if (!btn) return;

    btn.addEventListener('click', function () {
      var input = document.querySelector('.sa-nl-input');
      if (!input || !input.value) return;

      var email = input.value.trim();
      if (!email || email.indexOf('@') < 1) {
        input.style.borderColor = 'rgba(139,94,60,.8)';
        setTimeout(function () { input.style.borderColor = ''; }, 1000);
        return;
      }

      btn.textContent = 'Envoi...';
      btn.disabled = true;

      var C = window.saEngageConfig || {};
      if (C.ajax_url && C.nonce) {
        $.ajax({
          url: C.ajax_url,
          type: 'POST',
          data: { action: 'sa_capture_email', nonce: C.nonce, email: email, source: 'newsletter', data: '' },
          complete: function () {
            input.value = '';
            btn.textContent = 'S\'abonner';
            btn.disabled = false;
            input.placeholder = '✓ Merci — à bientôt dans votre boîte !';
            setTimeout(function () { input.placeholder = 'votre@email.fr'; }, 3000);
          }
        });
      } else {
        input.value = '';
        btn.textContent = 'S\'abonner';
        btn.disabled = false;
        input.placeholder = '✓ Merci — à bientôt dans votre boîte !';
        setTimeout(function () { input.placeholder = 'votre@email.fr'; }, 3000);
      }
    });
  }

  /* ── Hero burger (homepage) ─────────────────────────────── */
  function initHeroBurger() {
    var burger = document.querySelector('.sa-hero-burger');
    var menu = document.querySelector('.sa-hero-menu');
    if (!burger || !menu) return;

    burger.addEventListener('click', function (e) {
      e.stopPropagation();
      burger.classList.toggle('is-open');
      menu.classList.toggle('is-open');
    });

    document.addEventListener('click', function (e) {
      if (!e.target.closest('.sa-hero-nav')) {
        burger.classList.remove('is-open');
        menu.classList.remove('is-open');
      }
    });
  }

  /* ── Init ────────────────────────────────────────────────── */
  $(document).ready(function () {
    initHeaderScroll();
    initBurger();
    initHeroBurger();
    initScrollReveal();
    initStackCarousel();
    initCountUp();
    initNewsletter();
  });
})(jQuery);
