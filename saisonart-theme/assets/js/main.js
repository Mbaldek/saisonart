/* SaisonArt - Main JavaScript */
(function ($) {
  'use strict';

  /* ── Header scroll state ────────────────────────────────── */
  function initHeaderScroll() {
    var header = document.querySelector('.sa-header');
    if (!header) return;

    function update() {
      header.classList.toggle('is-scrolled', window.scrollY > 60);
    }
    window.addEventListener('scroll', update, { passive: true });
    update();
  }

  /* ── Mobile burger menu ─────────────────────────────────── */
  function initBurger() {
    var burger = document.querySelector('.sa-header__burger');
    var menu = document.querySelector('.sa-header__mobile-menu');
    if (!burger || !menu) return;

    burger.addEventListener('click', function () {
      menu.classList.toggle('is-open');
    });

    // Close on link click
    menu.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        menu.classList.remove('is-open');
      });
    });
  }

  /* ── Scroll reveal ──────────────────────────────────────── */
  function initScrollReveal() {
    var elements = document.querySelectorAll('.sa-reveal, .sa-reveal-fade, .sa-reveal-scale');
    if (!elements.length) return;

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    elements.forEach(function (el) {
      observer.observe(el);
    });
  }

  /* ── Auto-tag animatable elements ───────────────────────── */
  function tagAnimatedElements() {
    document.querySelectorAll(
      '.woocommerce ul.products li.product, ' +
      '.sa-feature, ' +
      '.sa-section-header'
    ).forEach(function (el) {
      if (!el.classList.contains('sa-reveal')) {
        el.classList.add('sa-reveal');
      }
    });
  }

  /* ── Smooth scroll for anchors ──────────────────────────── */
  function initSmoothScroll() {
    $('a[href^="#"]').on('click', function (e) {
      var target = $(this.getAttribute('href'));
      if (target.length) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: target.offset().top - 80 }, 600);
      }
    });
  }

  /* ── Init ────────────────────────────────────────────────── */
  $(document).ready(function () {
    initHeaderScroll();
    initBurger();
    tagAnimatedElements();
    initScrollReveal();
    initSmoothScroll();
  });
})(jQuery);
