<?php
$currentLocale = current_locale();
?>
<!-- Search Modal -->
<div id="search-modal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="search-modal-title">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="search-modal-title" style="margin-bottom:0; font-size:1.15rem;"><?= e(__('common.actions.search')) ?></h3>
            <button class="btn btn-ghost btn-sm" data-modal-close aria-label="<?= e(__('common.actions.close')) ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <form action="<?= e(url('/search', $currentLocale)) ?>" method="GET" class="search-form">
            <div class="form-group">
                <input type="search" name="q" class="form-control" 
                       placeholder="<?= e(__('common.actions.search_placeholder')) ?>" 
                       autocomplete="off" autofocus>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.82rem; color:var(--text-muted);">
                <span><?= $currentLocale === 'bn' ? 'কীওয়ার্ড: গীতা, উপনিষদ, পাঠশালা, লাইব্রেরি' : 'Suggested: Gita, Upanishad, Seva, Library' ?></span>
                <button type="submit" class="btn btn-primary btn-sm"><?= e(__('common.actions.search')) ?></button>
            </div>
        </form>
    </div>
</div>

<!-- Mobile Navigation Drawer -->
<div id="mobile-nav-drawer" class="mobile-nav-drawer" role="dialog" aria-modal="true" aria-label="Mobile Navigation">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <div style="display:flex; align-items:center; gap:var(--space-xs);">
            <svg width="32" height="32" viewBox="0 0 100 100" fill="none">
                <circle cx="50" cy="50" r="46" stroke="#C65A1E" stroke-width="2" />
                <polygon points="50,34 62,50 50,66 38,50" stroke="#C65A1E" stroke-width="2" fill="#FFFFFF" />
            </svg>
            <span style="font-weight:700; color:var(--text-main);"><?= e(__('common.short_name')) ?></span>
        </div>
        <button data-drawer-close="mobile-nav" class="btn btn-ghost btn-sm" aria-label="<?= e(__('common.actions.close')) ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <div style="margin-top:var(--space-md);">
        <?= \App\Core\View::component('language_toggle') ?>
    </div>

    <nav class="mobile-nav-links">
        <a href="<?= e(url('/', $currentLocale)) ?>"><?= e(__('common.nav.home')) ?></a>
        <a href="<?= e(url('/about', $currentLocale)) ?>"><?= e(__('common.nav.about')) ?></a>
        <a href="<?= e(url('/activities', $currentLocale)) ?>"><?= e(__('common.nav.activities')) ?></a>
        <a href="<?= e(url('/knowledge', $currentLocale)) ?>"><?= e(__('common.nav.knowledge')) ?></a>
        <a href="<?= e(url('/library', $currentLocale)) ?>"><?= e(__('common.nav.library')) ?></a>
        <a href="<?= e(url('/blog', $currentLocale)) ?>"><?= e(__('common.nav.blog')) ?></a>
        <a href="<?= e(url('/get-involved', $currentLocale)) ?>"><?= e(__('common.nav.get_involved')) ?></a>
        <a href="<?= e(url('/transparency', $currentLocale)) ?>"><?= e(__('common.nav.transparency')) ?></a>
        <a href="<?= e(url('/contact', $currentLocale)) ?>"><?= e(__('common.nav.contact')) ?></a>
        <a href="<?= e(url('/components', $currentLocale)) ?>" style="color:var(--accent-gold); font-size:0.95rem;">
            <?= e(__('common.actions.view_components')) ?>
        </a>
    </nav>

    <div style="margin-top:auto; padding-top:var(--space-lg); border-top:1px solid var(--border-medium); display:flex; flex-direction:column; gap:var(--space-sm);">
        <a href="<?= e(url('/auth/google', $currentLocale)) ?>" class="btn btn-secondary" style="width:100%;">
            <?= e(__('common.actions.login')) ?>
        </a>
        <a href="<?= e(url('/get-involved', $currentLocale)) ?>" class="btn btn-primary" style="width:100%;">
            <?= e(__('common.actions.become_member')) ?>
        </a>
    </div>
</div>
