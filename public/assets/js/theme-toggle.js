/**
 * SPS Theme Toggle Controller
 * Detects device theme via Chrome/browser matchMedia('prefers-color-scheme: dark')
 * Supports manual toggle, persistence in localStorage, and reactive system updates.
 */

(function() {
    'use strict';

    const STORAGE_KEY = 'sps_theme';
    const mediaQuery = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;

    /**
     * Get system preferred theme ('dark' | 'light')
     */
    function getSystemTheme() {
        return (mediaQuery && mediaQuery.matches) ? 'dark' : 'light';
    }

    /**
     * Get the active effective theme
     */
    function getActiveTheme() {
        try {
            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved === 'dark' || saved === 'light') {
                return saved;
            }
        } catch (e) {
            console.warn('[SPS Theme] localStorage inaccessible', e);
        }
        return getSystemTheme();
    }

    /**
     * Apply the theme to the document
     */
    function applyTheme(theme, save = true) {
        const root = document.documentElement;
        const body = document.body;

        root.setAttribute('data-theme', theme);
        if (body) {
            body.setAttribute('data-theme', theme);
            if (theme === 'dark') {
                body.classList.add('dark-theme');
                root.classList.add('dark-theme');
            } else {
                body.classList.remove('dark-theme');
                root.classList.remove('dark-theme');
            }
        }

        if (save) {
            try {
                localStorage.setItem(STORAGE_KEY, theme);
            } catch (e) {}
        }

        // Update all toggle buttons in DOM
        updateButtonsUI(theme);

        // Update all adaptive SPS brand logos according to background contrast
        updateBrandLogos(theme);

        // Dispatch theme changed event
        window.dispatchEvent(new CustomEvent('sps-theme-changed', {
            detail: { theme: theme, isDark: theme === 'dark' }
        }));
    }

    /**
     * Adapt SPS brand logos according to background theme:
     * - Light background -> Dark logo (sps-logo.png)
     * - Dark background -> Light logo (sps-logo-white.png)
     */
    function updateBrandLogos(theme) {
        var isDark = theme === 'dark';
        var darkLogoName = 'sps-logo.png';
        var whiteLogoName = 'sps-logo-white.png';

        document.querySelectorAll('img.brand-mark, img.mobile-drawer-brand-mark, img.admin-brand-logo, img.sidebar-profile-img, img.project-hero-logo, img.sps-adaptive-logo').forEach(function(img) {
            // If already paired via CSS classes .brand-mark-dark and .brand-mark-light, let CSS handle it
            if (img.classList.contains('brand-mark-dark') || img.classList.contains('brand-mark-light')) {
                return;
            }

            var src = img.getAttribute('src');
            if (!src) return;

            // Permanent dark containers (footer, modal dark header) must always keep white logo
            if (img.closest('.site-footer') || img.closest('.donation-modal-header') || img.closest('.dark-surface')) {
                if (!src.includes(whiteLogoName)) {
                    img.setAttribute('src', src.replace(/sps-logo(-dark)?\.png/, whiteLogoName));
                }
                return;
            }

            // Permanent light containers (invoice printable paper) must always keep dark logo
            if (img.closest('.invoice-paper') || img.closest('.light-surface')) {
                if (src.includes(whiteLogoName)) {
                    img.setAttribute('src', src.replace(whiteLogoName, darkLogoName));
                }
                return;
            }

            if (isDark) {
                // Background is dark -> use light logo
                if (!src.includes(whiteLogoName)) {
                    img.setAttribute('src', src.replace(/sps-logo(-dark)?\.png/, whiteLogoName));
                }
            } else {
                // Background is light -> use dark logo
                if (src.includes(whiteLogoName)) {
                    img.setAttribute('src', src.replace(whiteLogoName, darkLogoName));
                }
            }
        });
    }

    /**
     * Update all theme toggle buttons state and accessibility labels
     */
    function updateButtonsUI(theme) {
        const buttons = document.querySelectorAll('.theme-toggle-btn');
        const isDark = theme === 'dark';
        
        buttons.forEach(btn => {
            btn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
            const isBn = document.documentElement.lang === 'bn';
            const titleText = isDark 
                ? (isBn ? 'লাইট মোডে পরিবর্তন করুন' : 'Switch to Light Mode')
                : (isBn ? 'ডার্ক মোডে পরিবর্তন করুন' : 'Switch to Dark Mode');
            btn.setAttribute('title', titleText);
            btn.setAttribute('aria-label', titleText);
        });
    }

    /**
     * Toggle between dark and light
     */
    window.spsToggleTheme = function() {
        const current = document.documentElement.getAttribute('data-theme') || getActiveTheme();
        const next = current === 'dark' ? 'light' : 'dark';
        applyTheme(next, true);
        return next;
    };

    /**
     * Explicitly set theme ('dark', 'light', or 'system')
     */
    window.spsSetTheme = function(mode) {
        if (mode === 'system') {
            try { localStorage.removeItem(STORAGE_KEY); } catch(e) {}
            applyTheme(getSystemTheme(), false);
        } else if (mode === 'dark' || mode === 'light') {
            applyTheme(mode, true);
        }
    };

    // Listen for device/Chrome system theme changes in real time
    if (mediaQuery) {
        const handleSystemChange = function(e) {
            let hasSaved = false;
            try { hasSaved = !!localStorage.getItem(STORAGE_KEY); } catch(err) {}
            
            // If user hasn't explicitly locked in a theme, follow the device theme
            if (!hasSaved) {
                applyTheme(e.matches ? 'dark' : 'light', false);
            }
        };

        if (typeof mediaQuery.addEventListener === 'function') {
            mediaQuery.addEventListener('change', handleSystemChange);
        } else if (typeof mediaQuery.addListener === 'function') {
            mediaQuery.addListener(handleSystemChange);
        }
    }

    // Initialize UI when DOM is ready
    function init() {
        const activeTheme = getActiveTheme();
        applyTheme(activeTheme, false);

        document.querySelectorAll('.theme-toggle-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                window.spsToggleTheme();
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
