/**
 * SPS Accessible UI Components
 * Mobile Drawer, Accessible Modals, Tabs, Search Modal
 */

(function () {
  'use strict';

  // Mobile Drawer Navigation
  function initMobileDrawer() {
    const trigger = document.querySelector('[data-drawer-trigger="mobile-nav"]');
    const drawer = document.getElementById('mobile-nav-drawer');
    const closeBtn = document.querySelector('[data-drawer-close="mobile-nav"]');

    if (!trigger || !drawer) return;

    function openDrawer() {
      drawer.classList.add('active');
      document.body.style.overflow = 'hidden';
      if (closeBtn) closeBtn.focus();
    }

    function closeDrawer() {
      drawer.classList.remove('active');
      document.body.style.overflow = '';
      trigger.focus();
    }

    trigger.addEventListener('click', openDrawer);
    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);

    // Close on escape
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && drawer.classList.contains('active')) {
        closeDrawer();
      }
    });
  }

  // Modals
  function initModals() {
    const triggers = document.querySelectorAll('[data-modal-target]');

    triggers.forEach(function (btn) {
      btn.addEventListener('click', function () {
        const targetId = this.getAttribute('data-modal-target');
        const modal = document.getElementById(targetId);
        if (modal) {
          modal.classList.add('active');
          document.body.style.overflow = 'hidden';
          const input = modal.querySelector('input, button');
          if (input) input.focus();
        }
      });
    });

    const closeBtns = document.querySelectorAll('[data-modal-close]');
    closeBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        const modal = this.closest('.modal-overlay');
        if (modal) {
          modal.classList.remove('active');
          document.body.style.overflow = '';
        }
      });
    });

    document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
      overlay.addEventListener('click', function (e) {
        if (e.target === this) {
          this.classList.remove('active');
          document.body.style.overflow = '';
        }
      });
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        const activeModal = document.querySelector('.modal-overlay.active');
        if (activeModal) {
          activeModal.classList.remove('active');
          document.body.style.overflow = '';
        }
      }
    });
  }

  // Accessible Tabs
  function initTabs() {
    const tabGroups = document.querySelectorAll('[data-tabs-group]');

    tabGroups.forEach(function (group) {
      const btns = group.querySelectorAll('[data-tab-target]');
      const containerId = group.getAttribute('data-tabs-group');
      const container = document.getElementById(containerId);

      btns.forEach(function (btn) {
        btn.addEventListener('click', function () {
          const targetPaneId = this.getAttribute('data-tab-target');

          // Deactivate all buttons in group
          btns.forEach(b => b.classList.remove('active'));
          this.classList.add('active');

          // Switch panes
          if (container) {
            const panes = container.querySelectorAll('[data-tab-pane]');
            panes.forEach(function (pane) {
              if (pane.id === targetPaneId) {
                pane.style.display = 'block';
              } else {
                pane.style.display = 'none';
              }
            });
          }
        });
      });
    });
  }

  function init() {
    initMobileDrawer();
    initModals();
    initTabs();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
