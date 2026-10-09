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

  // Double-submit protection for key forms
  function initDoubleSubmitProtection() {
    var selectors = [
      '#membershipApplyForm',
      '#dashPaymentForm',
      'form[action*="/membership/apply"]',
      'form[action*="/membership/payment"]',
      'form[action*="/donation/submit"]',
      'form[action*="/admin/activities/create"]',
      'form[action*="/blog/write"]',
      '#blogWriteForm'
    ];

    document.querySelectorAll(selectors.join(',')).forEach(function (form) {
      form.addEventListener('submit', function (e) {
        if (form.dataset.submitting === 'true') {
          e.preventDefault();
          return false;
        }
        if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
          return;
        }
        form.dataset.submitting = 'true';
        var submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
        if (submitBtn) {
          setTimeout(function () {
            submitBtn.disabled = true;
          }, 10);
        }
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDoubleSubmitProtection);
  } else {
    initDoubleSubmitProtection();
  }

  console.info('SPS Platform Engine initialized. Locale: ' + (document.documentElement.lang || 'bn'));
})();
