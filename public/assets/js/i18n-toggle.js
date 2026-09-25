/**
 * SPS Bilingual Language Switcher & Persistence
 */

(function () {
  'use strict';

  function setLanguagePreference(locale) {
    if (!['bn', 'en'].includes(locale)) return;

    try {
      localStorage.setItem('sps_preferred_locale', locale);
    } catch (e) {
      console.warn('localStorage not available for locale saving');
    }

    // Set 1-year cookie for visitor persistence
    const expires = new Date(Date.now() + 365 * 864e5).toUTCString();
    document.cookie = `sps_locale=${locale}; expires=${expires}; path=/; SameSite=Lax`;
  }

  function initLanguageToggles() {
    const toggleLinks = document.querySelectorAll('.lang-toggle-link, [data-switch-lang]');
    
    toggleLinks.forEach(function (link) {
      link.addEventListener('click', function (e) {
        const targetLocale = this.getAttribute('data-lang') || 
                             (this.textContent.trim().toLowerCase().includes('english') ? 'en' : 'bn');
        setLanguagePreference(targetLocale);
      });
    });

    // Check if current URL matches stored preference if visiting bare root
    const currentPath = window.location.pathname;
    if (currentPath === '/' || currentPath === '') {
      try {
        const stored = localStorage.getItem('sps_preferred_locale');
        if (stored && ['bn', 'en'].includes(stored)) {
          window.location.replace('/' + stored);
        }
      } catch (e) {}
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLanguageToggles);
  } else {
    initLanguageToggles();
  }
})();
