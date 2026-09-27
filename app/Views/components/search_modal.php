<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
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
            <img src="<?= asset('assets/images/brand/sps-logo.png') ?>" alt="<?= e(__('common.short_name')) ?>" class="mobile-drawer-brand-mark" width="45" height="32" style="object-fit:contain;">
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
        <a href="<?= e(url('/blog', $currentLocale)) ?>"><?= e(__('common.nav.blog')) ?></a>
        <a href="<?= e(url('/library', $currentLocale)) ?>" style="color:var(--accent-gold); font-weight:600;">📖 <?= e(__('common.nav.library')) ?></a>
        <a href="<?= e(url('/admin/library', $currentLocale)) ?>" style="color:var(--accent-saffron); font-size:0.9rem;">🛡️ <?= $currentLocale === 'bn' ? 'প্রশাসনিক প্যানেল (Admin)' : 'Admin Dashboard' ?></a>
    </nav>

    <div style="margin-top:auto; padding-top:var(--space-lg); border-top:1px solid var(--border-medium); display:flex; flex-direction:column; gap:var(--space-sm);">
        <?php
        $drawerMemberCode = \App\Core\Session::get('current_member_code');
        $isDrawerMemberLoggedIn = !empty($drawerMemberCode) && !\App\Core\Session::get('member_logged_out');
        ?>
        <?php if ($isDrawerMemberLoggedIn): ?>
            <a href="<?= e(url('/membership/dashboard', $currentLocale)) ?>" class="btn btn-secondary" style="width:100%; text-align:center; display:flex; align-items:center; justify-content:center; gap:8px;">
                <span>👤</span>
                <span><?= $isBn ? 'সদস্য ড্যাশবোর্ড (' . e($drawerMemberCode) . ')' : 'Member Dashboard (' . e($drawerMemberCode) . ')' ?></span>
            </a>
            <a href="<?= e(url('/membership/logout', $currentLocale)) ?>" class="btn btn-ghost" style="width:100%; text-align:center; color:#b91c1c;">
                <?= $isBn ? 'লগআউট' : 'Logout' ?>
            </a>
        <?php else: ?>
            <a href="<?= e(url('/membership/login', $currentLocale)) ?>" class="btn btn-secondary" style="width:100%; text-align:center;">
                🔑 <?= $isBn ? 'সদস্য লগইন' : 'Member Login' ?>
            </a>
            <a href="<?= e(url('/membership/apply', $currentLocale)) ?>" class="btn btn-primary" style="width:100%; text-align:center;">
                ✨ <?= $isBn ? 'সদস্যপদের আবেদন' : 'Apply for Membership' ?>
            </a>
        <?php endif; ?>
    </div>
</div>
