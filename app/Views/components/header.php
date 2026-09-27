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

            <?php
            $currentMemberCode = \App\Core\Session::get('current_member_code');
            $isMemberLoggedIn = !empty($currentMemberCode) && !\App\Core\Session::get('member_logged_out');
            $currentMember = $isMemberLoggedIn ? \App\Services\MembershipService::getMemberById($currentMemberCode) : null;
            ?>

            <?php if ($isMemberLoggedIn && $currentMember): ?>
                <!-- Logged In Member Badge & Logout -->
                <div class="header-member-logged-wrap" style="display:inline-flex; align-items:center; gap:6px;">
                    <a href="<?= e(url('/membership/dashboard', $currentLocale)) ?>" class="btn btn-secondary btn-sm header-login-btn" style="display:inline-flex; align-items:center; gap:6px; background:#f0fdf4; border-color:#86efac; color:#166534;" title="<?= $isBn ? 'আমার সদস্য ড্যাশবোর্ড' : 'My Member Dashboard' ?>">
                        <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#22c55e;"></span>
                        <span style="font-weight:700;"><?= e($currentMember['member_code']) ?></span>
                    </a>
                    <a href="<?= e(url('/membership/logout', $currentLocale)) ?>" class="btn btn-ghost btn-sm" style="color:#b91c1c; padding:4px 8px; font-size:0.8rem; display:inline-flex; align-items:center; gap:4px;" title="<?= $isBn ? 'লগআউট করুন' : 'Logout' ?>">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        <span><?= $isBn ? 'লগআউট' : 'Logout' ?></span>
                    </a>
                </div>
            <?php else: ?>
                <!-- Member Login Link -->
                <a href="<?= e(url('/membership/login', $currentLocale)) ?>" class="btn btn-primary btn-sm header-login-btn" style="display:inline-flex; align-items:center; gap:6px; background:var(--primary-deep); color:#ffffff; font-weight:700; padding:6px 14px; border-radius:var(--radius-full); box-shadow:0 2px 6px rgba(0,0,0,0.15);" title="<?= $isBn ? 'সদস্য লগইন পোর্টাল' : 'Member Login Portal' ?>">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <polyline points="10 17 15 12 10 7"></polyline>
                        <line x1="15" y1="12" x2="3" y2="12"></line>
                    </svg>
                    <span><?= $isBn ? 'সদস্য লগইন' : 'Member Login' ?></span>
                </a>
            <?php endif; ?>

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
