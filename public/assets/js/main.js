/**
 * SPS Main Platform Script
 */

(function () {
  'use strict';

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href');
      if (targetId === '#' || targetId === '') return;
      const targetEl = document.querySelector(targetId);
      if (targetEl) {
        e.preventDefault();
        targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // Global search quick-open with keyboard shortcut (Cmd/Ctrl + K)
  document.addEventListener('keydown', function (e) {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
      e.preventDefault();
      const searchModal = document.getElementById('search-modal');
      if (searchModal) {
        searchModal.classList.add('active');
        document.body.style.overflow = 'hidden';
        const input = searchModal.querySelector('input');
        if (input) input.focus();
      }
    }
  });

  console.info('SPS Platform Engine initialized. Locale: ' + (document.documentElement.lang || 'bn'));
})();
