<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$activeNav = $activeNav ?? 'home';
?>
<header class="site-header" id="site-header">
    <div class="header-inner">
        <!-- Brand Identity -->
        <a href="<?= e(url('/', $currentLocale)) ?>" class="brand-wrapper" aria-label="SPS Home">
            <svg class="brand-mark" width="44" height="44" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <circle cx="50" cy="50" r="46" stroke="#C65A1E" stroke-width="1.5" stroke-opacity="0.4" />
                <circle cx="50" cy="50" r="42" stroke="#A37E36" stroke-width="1" stroke-dasharray="2 3" />
                <path d="M50 12 C54 26, 68 34, 88 50 C68 66, 54 74, 50 88 C46 74, 32 66, 12 50 C32 34, 46 26, 50 12 Z" stroke="#A37E36" stroke-width="1.2" fill="#FAF6F0" fill-opacity="0.8" />
                <path d="M23 23 C38 31, 46 45, 50 50 C46 55, 38 69, 23 77 C31 62, 45 54, 50 50 C45 46, 31 38, 23 23 Z" stroke="#C65A1E" stroke-width="0.8" stroke-opacity="0.5" />
                <path d="M77 23 C62 31, 54 45, 50 50 C54 55, 62 69, 77 77 C69 62, 55 54, 50 50 C55 46, 69 38, 77 23 Z" stroke="#C65A1E" stroke-width="0.8" stroke-opacity="0.5" />
                <polygon points="50,34 62,50 50,66 38,50" stroke="#C65A1E" stroke-width="1.5" fill="#FFFFFF" />
                <circle cx="50" cy="50" r="3.5" fill="#A37E36" />
            </svg>
            <div class="brand-text">
                <span class="brand-title"><?= e(__('common.brand_name')) ?></span>
                <span class="brand-subtitle"><?= e(__('common.motto')) ?></span>
            </div>
        </a>

        <!-- Desktop Navigation -->
        <nav class="nav-desktop" aria-label="Main Navigation">
            <a href="<?= e(url('/', $currentLocale)) ?>" class="nav-link <?= $activeNav === 'home' ? 'active' : '' ?>"><?= e(__('common.nav.home')) ?></a>
            <a href="<?= e(url('/about', $currentLocale)) ?>" class="nav-link <?= $activeNav === 'about' ? 'active' : '' ?>"><?= e(__('common.nav.about')) ?></a>
            <a href="<?= e(url('/activities', $currentLocale)) ?>" class="nav-link <?= $activeNav === 'activities' ? 'active' : '' ?>"><?= e(__('common.nav.activities')) ?></a>
            <a href="<?= e(url('/knowledge', $currentLocale)) ?>" class="nav-link <?= $activeNav === 'knowledge' ? 'active' : '' ?>"><?= e(__('common.nav.knowledge')) ?></a>
            <a href="<?= e(url('/library', $currentLocale)) ?>" class="nav-link <?= $activeNav === 'library' ? 'active' : '' ?>"><?= e(__('common.nav.library')) ?></a>
            <a href="<?= e(url('/blog', $currentLocale)) ?>" class="nav-link <?= $activeNav === 'blog' ? 'active' : '' ?>"><?= e(__('common.nav.blog')) ?></a>
            <a href="<?= e(url('/get-involved', $currentLocale)) ?>" class="nav-link <?= $activeNav === 'get-involved' ? 'active' : '' ?>"><?= e(__('common.nav.get_involved')) ?></a>
            <a href="<?= e(url('/transparency', $currentLocale)) ?>" class="nav-link <?= $activeNav === 'transparency' ? 'active' : '' ?>"><?= e(__('common.nav.transparency')) ?></a>
            <a href="<?= e(url('/contact', $currentLocale)) ?>" class="nav-link <?= $activeNav === 'contact' ? 'active' : '' ?>"><?= e(__('common.nav.contact')) ?></a>
        </nav>

        <!-- Header Actions: Search, Language Switcher, Account, Mobile Drawer -->
        <div class="header-actions">
            <!-- Search Button -->
            <button class="btn btn-ghost btn-icon" data-modal-target="search-modal" aria-label="<?= e(__('common.actions.search')) ?>" title="<?= e(__('common.actions.search')) ?> (Ctrl+K)">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </button>

            <!-- Language Switcher -->
            <?= \App\Core\View::component('language_toggle') ?>

            <!-- Login / Account -->
            <a href="<?= e(url('/auth/google', $currentLocale)) ?>" class="btn btn-secondary btn-sm" style="display:inline-flex;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span><?= e(__('common.actions.login')) ?></span>
            </a>

            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-btn" data-drawer-trigger="mobile-nav" aria-label="Toggle navigation menu">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>
</header>
