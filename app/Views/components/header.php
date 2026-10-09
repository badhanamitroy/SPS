<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$activeNav = $activeNav ?? 'home';
?>
<header class="site-header" id="site-header">
    <div class="header-inner">
        <!-- Brand Identity -->
        <a href="<?= e(url('/', $currentLocale)) ?>" class="brand-wrapper" aria-label="SPS Home">
            <img src="<?= asset('assets/images/brand/sps-logo.png') ?>" alt="SPS" class="brand-mark brand-mark-dark" width="62" height="44" loading="eager">
            <img src="<?= asset('assets/images/brand/sps-logo-white.png') ?>" alt="SPS" class="brand-mark brand-mark-light" width="62" height="44" loading="eager">
            <div class="brand-text">
                <span class="brand-title">SPS</span>
            </div>
        </a>

        <!-- Desktop Navigation: About us, Our Activities, Blogs, SPS Library, Join us -->
        <nav class="nav-desktop" aria-label="Main Navigation">
            <a href="<?= e(url('/about', $currentLocale)) ?>" class="nav-link <?= ($activeNav ?? '') === 'about' ? 'active' : '' ?>"><?= $isBn ? 'আমাদের সম্পর্কে' : 'About us' ?></a>
            <a href="<?= e(url('/activities', $currentLocale)) ?>" class="nav-link <?= ($activeNav ?? '') === 'activities' ? 'active' : '' ?>"><?= $isBn ? 'আমাদের কার্যক্রম' : 'Our Activities' ?></a>
            <a href="<?= e(url('/blog', $currentLocale)) ?>" class="nav-link <?= ($activeNav ?? '') === 'blog' ? 'active' : '' ?>"><?= $isBn ? 'ব্লগ' : 'Blogs' ?></a>
            <a href="<?= e(url('/library', $currentLocale)) ?>" class="nav-link <?= ($activeNav ?? '') === 'library' ? 'active' : '' ?>" title="<?= $isBn ? 'SPS ডিজিটাল লাইব্রেরি' : 'SPS Digital Library' ?>"><?= $isBn ? 'SPS লাইব্রেরি' : 'SPS Library' ?></a>
            <a href="<?= e(url('/get-involved', $currentLocale)) ?>" class="nav-link <?= ($activeNav ?? '') === 'get-involved' ? 'active' : '' ?>"><?= $isBn ? 'যোগ দিন' : 'Join us' ?></a>
        </nav>

        <!-- Header Actions: Social Pill, Search, Theme Switcher, Language Switcher, Account, Mobile Drawer -->
        <div class="header-actions">
            <!-- Official Facebook Social Presence Pill -->
            <div class="header-social-pill hide-mobile" aria-label="Official Social Channels">
                <a href="https://www.facebook.com/bewithsps?utm_source=chatgpt.com" target="_blank" rel="noopener noreferrer" class="header-social-link link-fb-page" title="SPS Official Facebook Page" aria-label="Facebook Page">
                    <i class="fa-brands fa-facebook-f"></i>
                </a>
                <a href="https://www.facebook.com/groups/278337526756568?utm_source=chatgpt.com" target="_blank" rel="noopener noreferrer" class="header-social-link link-fb-group" title="SPS Official Community Group" aria-label="Facebook Group">
                    <i class="fa-solid fa-users"></i>
                </a>
            </div>

            <!-- Search Button -->
            <button class="header-icon-btn" data-modal-target="search-modal" aria-label="<?= e(__('common.actions.search')) ?>" title="<?= e(__('common.actions.search')) ?> (Ctrl+K)">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>

            <!-- Theme Switcher (Dark/Light) -->
            <?= \App\Core\View::component('theme_toggle') ?>

            <!-- Language Switcher -->
            <?= \App\Core\View::component('language_toggle') ?>

            <?php
            $currentMemberCode = \App\Core\Session::get('current_member_code');
            $isMemberLoggedIn = !empty($currentMemberCode) && !\App\Core\Session::get('member_logged_out');
            $currentMember = $isMemberLoggedIn ? \App\Services\MembershipService::getMemberById($currentMemberCode) : null;
            $isMemberActive = $currentMember && \App\Services\MembershipService::isActiveStatus($currentMember['status'] ?? '');
            ?>

            <?php if ($isMemberLoggedIn && $currentMember && $isMemberActive): ?>
                <!-- Logged In Member Badge & Logout -->
                <div class="header-member-logged-wrap" style="display:inline-flex; align-items:center; gap:6px;">
                    <a href="<?= e(url('/membership/dashboard', $currentLocale)) ?>" class="header-member-badge" title="<?= $isBn ? 'আমার সদস্য ড্যাশবোর্ড' : 'My Member Dashboard' ?>">
                        <span class="status-dot"></span>
                        <span><?= e($currentMember['member_code']) ?></span>
                    </a>
                    <a href="<?= e(url('/membership/logout', $currentLocale)) ?>" class="header-logout-btn" title="<?= $isBn ? 'লগআউট করুন' : 'Logout' ?>">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        <span><?= $isBn ? 'লগআউট' : 'Logout' ?></span>
                    </a>
                </div>
            <?php else: ?>
                <!-- Member Login Link (High Contrast & Clear) -->
                <a href="<?= e(url('/membership/login', $currentLocale)) ?>" class="header-login-btn" style="background-color:#c65a1e !important; color:#ffffff !important; text-decoration:none !important;" title="<?= $isBn ? 'সদস্য লগইন পোর্টাল' : 'Member Login Portal' ?>">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <polyline points="10 17 15 12 10 7"></polyline>
                        <line x1="15" y1="12" x2="3" y2="12"></line>
                    </svg>
                    <span style="color:#ffffff !important; font-weight:700 !important;"><?= $isBn ? 'সদস্য লগইন' : 'Member Login' ?></span>
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
