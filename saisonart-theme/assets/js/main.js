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
          card.style.width = '360px';
          if (imgEl) imgEl.style.paddingTop = '70%';
        } else if (offset === 1) {
          card.style.transform = 'translateX(calc(-50% + 200px)) translateY(22px) rotateY(-8deg) scale(.95)';
          card.style.opacity = '.75';
          card.style.width = '260px';
          if (imgEl) imgEl.style.paddingTop = '85%';
        } else if (offset === 2) {
          card.style.transform = 'translateX(calc(-50% + 350px)) translateY(40px) rotateY(-14deg) scale(.9)';
          card.style.opacity = '.45';
          card.style.width = '260px';
          if (imgEl) imgEl.style.paddingTop = '85%';
        } else {
          card.style.transform = 'translateX(calc(-50% - 200px)) translateY(22px) rotateY(8deg) scale(.95)';
          card.style.opacity = '.75';
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

  /* ── Newsletter placeholder swap ────────────────────────── */
  function initNewsletter() {
    var btn = document.querySelector('.sa-nl-btn');
    if (!btn) return;

    btn.addEventListener('click', function () {
      var input = document.querySelector('.sa-nl-input');
      if (input && input.value) {
        input.value = '';
        input.placeholder = '✓ Merci — à bientôt dans votre boîte !';
        setTimeout(function () {
          input.placeholder = 'votre@email.fr';
        }, 3000);
      }
    });
  }

  /* ── Init ────────────────────────────────────────────────── */
  $(document).ready(function () {
    initHeaderScroll();
    initBurger();
    initScrollReveal();
    initStackCarousel();
    initNewsletter();
  });
})(jQuery);
