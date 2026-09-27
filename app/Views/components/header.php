<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$activeNav = $activeNav ?? 'home';
?>
<header class="site-header" id="site-header">
    <div class="header-inner">
        <!-- Brand Identity -->
        <a href="<?= e(url('/', $currentLocale)) ?>" class="brand-wrapper" aria-label="SPS Home">
            <img src="<?= asset('assets/images/brand/sps-logo.png') ?>" alt="<?= e(__('common.brand_name')) ?>" class="brand-mark" width="62" height="44" loading="eager">
            <div class="brand-text">
                <span class="brand-title"><?= e(__('common.brand_name')) ?></span>
                <span class="brand-subtitle"><?= e(__('common.motto')) ?></span>
            </div>
        </a>

        <!-- Desktop Navigation: About, Activities, Blogs, Membership -->
        <nav class="nav-desktop" aria-label="Main Navigation">
            <a href="<?= e(url('/about', $currentLocale)) ?>" class="nav-link <?= ($activeNav ?? '') === 'about' ? 'active' : '' ?>"><?= e(__('common.nav.about')) ?></a>
            <a href="<?= e(url('/activities', $currentLocale)) ?>" class="nav-link <?= ($activeNav ?? '') === 'activities' ? 'active' : '' ?>"><?= e(__('common.nav.activities')) ?></a>
            <a href="<?= e(url('/blog', $currentLocale)) ?>" class="nav-link <?= ($activeNav ?? '') === 'blog' ? 'active' : '' ?>"><?= e(__('common.nav.blog')) ?></a>
            <a href="<?= e(url('/membership', $currentLocale)) ?>" class="nav-link <?= ($activeNav ?? '') === 'membership' ? 'active' : '' ?>"><?= $isBn ? 'সদস্যপদ' : 'Membership' ?></a>
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

            <!-- Member Dashboard / Account -->
            <a href="<?= e(url('/membership/dashboard', $currentLocale)) ?>" class="btn btn-secondary btn-sm header-login-btn" style="display:inline-flex;" title="<?= $isBn ? 'সদস্য ড্যাশবোর্ড ও ডিজিটাল কার্ড' : 'Member Dashboard & Digital Card' ?>">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span><?= $isBn ? 'সদস্য ড্যাশবোর্ড' : 'Member Desk' ?></span>
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
