<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$currentUser = \App\Services\AuthService::getCurrentUser();
$currentRole = \App\Services\RbacService::getRole($currentUser['role'] ?? '');
$allUsers = \App\Services\RbacService::getUsers();
?>
<!DOCTYPE html>
<html lang="<?= e($currentLocale) ?>" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($metaTitle ?? 'SPS Admin Console') ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= asset('favicon.svg') ?>">
    <!-- FontAwesome 6 Pro/Free Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="<?= asset('assets/css/tokens.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/reset.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/typography.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/components.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/main.css') ?>">

    <!-- Immediate Anti-FOUC Theme Initializer -->
    <script>
    (function() {
        try {
            var stored = localStorage.getItem('sps_theme');
            var systemDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            var theme = stored ? stored : (systemDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
            if (theme === 'dark') {
                document.documentElement.classList.add('dark-theme');
            } else {
                document.documentElement.classList.remove('dark-theme');
            }
        } catch(e) {}
    })();
    </script>
    <style>
        :root {
            --admin-sidebar-width: 270px;
            --admin-header-height: 68px;
            --admin-sidebar-bg: #0d1b2a;
            --admin-sidebar-hover: #1b2d45;
            --admin-sidebar-active: #b3391b;
        }
        body.admin-body {
            background-color: #f4f6f9;
            color: var(--text-main);
            font-family: <?= $isBn ? 'var(--font-bn-sans)' : 'var(--font-en-sans)' ?>;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
        }
        html[lang="en"] .admin-body {
            font-family: Arial, Helvetica, sans-serif;
        }
        html[lang="en"] .admin-sidebar,
        html[lang="en"] .admin-topbar,
        html[lang="en"] .admin-brand-text,
        html[lang="en"] .admin-nav-item,
        html[lang="en"] .stat-card-title,
        html[lang="en"] .admin-card-title,
        html[lang="en"] h1,
        html[lang="en"] h2,
        html[lang="en"] h3 {
            font-family: 'Poppins', Arial, sans-serif;
        }
        .admin-layout {
            display: flex;
            min-height: calc(100vh - var(--admin-header-height));
        }
        /* Admin Top Bar */
        .admin-topbar {
            height: var(--admin-header-height);
            background: #ffffff;
            border-bottom: 1px solid var(--border-medium);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 var(--space-xl);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-sm);
        }
        .admin-brand-area {
            display: flex;
            align-items: center;
            gap: var(--space-md);
        }
        .admin-brand-logo {
            height: 40px;
            width: auto;
        }
        .admin-brand-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-deep);
            line-height: 1.2;
            margin: 0;
        }
        .admin-brand-subtitle {
            font-size: 0.76rem;
            color: var(--text-muted);
            letter-spacing: 0.5px;
        }
        .admin-user-bar {
            display: flex;
            align-items: center;
            gap: var(--space-md);
        }
        .admin-user-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            border: 1px solid var(--border-medium);
            padding: 4px 12px;
            border-radius: var(--radius-full);
            font-size: 0.84rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .admin-user-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid var(--border-medium);
        }
        .admin-user-name {
            font-weight: 700;
            color: var(--primary-deep);
            font-size: 0.86rem;
        }
        .btn-logout {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            color: #b3391b;
            border: 1px solid #f87171;
            padding: 6px 14px;
            border-radius: var(--radius-sm);
            font-size: 0.82rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: inherit;
        }
        .btn-logout:hover {
            background: #fee2e2;
            color: #991b1b;
            border-color: #ef4444;
        }
        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: var(--radius-full);
            font-size: 0.78rem;
            font-weight: 600;
        }
        .badge-super_admin { background: #fee2e2; color: #991b1b; border: 1px solid #f87171; }
        .badge-admin { background: #e0e7ff; color: #3730a3; border: 1px solid #818cf8; }
        .badge-finance_officer { background: #dcfce7; color: #166534; border: 1px solid #4ade80; }
        .badge-library_manager { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
        .badge-content_editor { background: #e0f2fe; color: #075985; border: 1px solid #38bdf8; }
        .badge-project_manager { background: #f3e8ff; color: #6b21a8; border: 1px solid #c084fc; }
        .badge-auditor { background: #f1f5f9; color: #334155; border: 1px solid #94a3b8; }
        .badge-membership_officer { background: #ffedd5; color: #9a3412; border: 1px solid #fb923c; }

        /* Sidebar */
        .admin-sidebar {
            width: var(--admin-sidebar-width);
            background: var(--admin-sidebar-bg);
            color: #e2e8f0;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #1e293b;
        }
        .sidebar-section-title {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            padding: var(--space-md) var(--space-lg) var(--space-xs);
        }
        .sidebar-nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-nav-item a {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            padding: 10px var(--space-lg);
            color: #cbd5e1;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 500;
            transition: all var(--transition-fast);
            border-left: 3px solid transparent;
        }
        .sidebar-nav-item a:hover {
            background: var(--admin-sidebar-hover);
            color: #ffffff;
            border-left-color: var(--accent-saffron);
        }
        .sidebar-nav-item.active a {
            background: var(--admin-sidebar-hover);
            color: #ffffff;
            font-weight: 600;
            border-left-color: var(--primary-deep);
        }
        .sidebar-nav-icon {
            font-size: 1.15rem;
            width: 22px;
            text-align: center;
        }
        .sidebar-restricted-badge {
            margin-left: auto;
            font-size: 0.68rem;
            background: #334155;
            color: #94a3b8;
            padding: 2px 6px;
            border-radius: var(--radius-sm);
        }

        /* Main Content */
        .admin-main {
            flex-grow: 1;
            padding: var(--space-2xl);
            max-width: 1380px;
            width: 100%;
            margin: 0 auto;
        }
        .admin-page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: var(--space-md);
            margin-bottom: var(--space-xl);
            padding-bottom: var(--space-md);
            border-bottom: 1px solid var(--border-medium);
        }
        .admin-page-title {
            font-size: 1.65rem;
            font-weight: 800;
            color: var(--primary-deep);
            margin: 0 0 var(--space-2xs);
        }
        .admin-page-desc {
            font-size: 0.92rem;
            color: var(--text-muted);
            margin: 0;
        }
        .admin-footer {
            background: #ffffff;
            border-top: 1px solid var(--border-subtle);
            padding: var(--space-md) var(--space-xl);
            font-size: 0.82rem;
            color: var(--text-muted);
            text-align: center;
        }
        @media (max-width: 900px) {
            .admin-layout { flex-direction: column; }
            .admin-sidebar { width: 100%; }
        }
    </style>
</head>
<body class="admin-body">

    <!-- Top Admin Bar -->
    <header class="admin-topbar">
        <div class="admin-brand-area">
            <a href="<?= url('/admin', $currentLocale) ?>" style="display:flex; align-items:center; gap:var(--space-sm); text-decoration:none;">
                <img src="<?= asset('assets/images/brand/sps-logo.png') ?>" alt="SPS Logo" class="admin-brand-logo brand-mark-dark">
                <img src="<?= asset('assets/images/brand/sps-logo-white.png') ?>" alt="SPS Logo" class="admin-brand-logo brand-mark-light">
                <div>
                    <h1 class="admin-brand-title"><?= $isBn ? 'সনাতন ফিলোসফি এন্ড স্ক্রিপচার' : 'Sanatan Philosophy & Scripture' ?></h1>
                    <div class="admin-brand-subtitle"><?= $isBn ? 'প্রশাসনিক নিয়ন্ত্রণকক্ষ (Admin Console)' : 'SPS Administrative Control' ?></div>
                </div>
            </a>
        </div>

        <div class="admin-user-bar">
            <!-- Authenticated Admin User Identity Pill (Links to Profile) -->
            <a href="<?= url('/admin/profile', $currentLocale) ?>" class="admin-user-pill" style="text-decoration:none; cursor:pointer;" title="<?= $isBn ? 'আমার প্রোফাইল ও নিরাপত্তা সম্পাদন করুন' : 'Edit My Profile & Security' ?>">
                <?php if (!empty($currentUser['avatar'])): ?>
                    <img src="<?= asset($currentUser['avatar']) ?>" alt="<?= e($currentUser['name_en'] ?? '') ?>" class="admin-user-avatar">
                <?php else: ?>
                    <span style="font-size:1.1rem;">👤</span>
                <?php endif; ?>
                <span class="admin-user-name">
                    <?= e($isBn ? ($currentUser['name_bn'] ?? '') : ($currentUser['name_en'] ?? '')) ?>
                </span>
                <!-- Verified Role Badge -->
                <span class="role-badge badge-<?= e($currentUser['role'] ?? '') ?>">
                    ★ <?= e($isBn ? ($currentRole['name_bn'] ?? $currentUser['role'] ?? '') : ($currentRole['name_en'] ?? $currentUser['role'] ?? '')) ?>
                </span>
            </a>

            <!-- Back to Public Site -->
            <a href="<?= url('/', $currentLocale) ?>" class="btn btn-sm btn-ghost" style="border:1px solid var(--border-medium); font-size:0.82rem;" target="_blank" title="<?= $isBn ? 'মূল ওয়েবসাইট' : 'Public Site' ?>">
                🌐 <?= $isBn ? 'পাবলিক সাইট' : 'Public Site' ?> ↗
            </a>

            <!-- Logout Button Form -->
            <form action="<?= url('/admin/logout', $currentLocale) ?>" method="POST" style="margin:0;">
                <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>
                <button type="submit" class="btn-logout" id="adminLogoutBtn" title="<?= $isBn ? 'অ্যাডমিন সেশন থেকে লগআউট করুন' : 'Log out of admin session' ?>">
                    <span>🚪</span>
                    <span><?= $isBn ? 'লগআউট' : 'Logout' ?></span>
                </button>
            </form>
        </div>
    </header>

    <div class="admin-layout">
        <!-- Sidebar Navigation (Role-filtered) -->
        <aside class="admin-sidebar">
            <div class="sidebar-section-title"><?= $isBn ? 'মূল নিয়ন্ত্রণ' : 'Core Navigation' ?></div>
            <ul class="sidebar-nav-list">
                <li class="sidebar-nav-item <?= ($activeNav ?? '') === 'admin.dashboard' ? 'active' : '' ?>">
                    <a href="<?= url('/admin', $currentLocale) ?>">
                        <span class="sidebar-nav-icon">📊</span>
                        <span><?= $isBn ? 'ড্যাশবোর্ড' : 'Dashboard' ?></span>
                    </a>
                </li>
                <li class="sidebar-nav-item <?= ($activeNav ?? '') === 'admin.profile' ? 'active' : '' ?>">
                    <a href="<?= url('/admin/profile', $currentLocale) ?>">
                        <span class="sidebar-nav-icon">👤</span>
                        <span><?= $isBn ? 'আমার প্রোফাইল' : 'My Profile' ?></span>
                    </a>
                </li>
            </ul>

            <!-- Users & Roles (Permission gated: users.view or roles.view) -->
            <?php if (\App\Services\AuthService::canAny(['users.view', 'roles.view'])): ?>
                <div class="sidebar-section-title"><?= $isBn ? 'নিরাপত্তা ও ভূমিকা' : 'Security & RBAC' ?></div>
                <ul class="sidebar-nav-list">
                    <?php if (\App\Services\AuthService::can('roles.view')): ?>
                        <li class="sidebar-nav-item <?= ($activeNav ?? '') === 'admin.roles' ? 'active' : '' ?>">
                            <a href="<?= url('/admin/roles', $currentLocale) ?>">
                                <span class="sidebar-nav-icon">🛡️</span>
                                <span><?= $isBn ? 'রোল ও পারমিশন ম্যাট্রিক্স' : 'Role Matrix' ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (\App\Services\AuthService::can('users.view')): ?>
                        <li class="sidebar-nav-item <?= ($activeNav ?? '') === 'admin.users' ? 'active' : '' ?>">
                            <a href="<?= url('/admin/users', $currentLocale) ?>">
                                <span class="sidebar-nav-icon">👥</span>
                                <span><?= $isBn ? 'অ্যাডমিন ইউজারবৃন্দ' : 'Admin Users' ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            <?php endif; ?>

            <!-- Library & E-books (Permission gated: library.view) -->
            <?php if (\App\Services\AuthService::can('library.view')): ?>
                <div class="sidebar-section-title"><?= $isBn ? 'গ্রন্থাগার ও প্রকাশনা' : 'Library & Reader' ?></div>
                <ul class="sidebar-nav-list">
                    <li class="sidebar-nav-item <?= ($activeNav ?? '') === 'admin.library' ? 'active' : '' ?>">
                        <a href="<?= url('/admin/library', $currentLocale) ?>">
                            <span class="sidebar-nav-icon">📚</span>
                            <span><?= $isBn ? 'ই-বুক ও পাঠাধিকার অনুমোদন' : 'E-Book & Access' ?></span>
                        </a>
                    </li>
                </ul>
            <?php endif; ?>

            <!-- Blog Moderation (Strictly Super Admin, Admin, and Literature-Admin) -->
            <?php if (\App\Services\BlogService::canModerateBlogs()): ?>
                <div class="sidebar-section-title"><?= $isBn ? 'ব্লগ ও সাহিত্য প্রকাশনা' : 'Blog & Editorial Desk' ?></div>
                <ul class="sidebar-nav-list">
                    <li class="sidebar-nav-item <?= ($activeNav ?? '') === 'admin.blogs' ? 'active' : '' ?>">
                        <a href="<?= url('/admin/blogs', $currentLocale) ?>">
                            <span class="sidebar-nav-icon">✍️</span>
                            <span><?= $isBn ? 'ব্লগ অনুমোদন ও মডারেশন' : 'Blog Moderation' ?></span>
                        </a>
                    </li>
                </ul>
            <?php endif; ?>

            <!-- Website CMS & Front Page Control -->
            <?php if (\App\Services\AuthService::canAny(['content.view', 'content.edit', 'roles.manage']) || \App\Services\AuthService::hasRole(['super_admin', 'admin', 'content_editor'])): ?>
                <div class="sidebar-section-title"><?= $isBn ? 'ওয়েবসাইট ও ফ্রন্ট পেজ' : 'Website & Front Page' ?></div>
                <ul class="sidebar-nav-list">
                    <li class="sidebar-nav-item <?= ($activeNav ?? '') === 'admin.homepage' ? 'active' : '' ?>">
                        <a href="<?= url('/admin/homepage', $currentLocale) ?>">
                            <span class="sidebar-nav-icon">⚙️</span>
                            <span><?= $isBn ? 'ফ্রন্ট পেজ সেকশন কনফিগ' : 'Front Page Sections' ?></span>
                        </a>
                    </li>
                </ul>
            <?php endif; ?>

            <!-- Membership Administration & Payment Verification (Finance Officer, Super Admin, Admin, Membership Officer) -->
            <?php if (\App\Services\AuthService::canAny(['members.view', 'members.manage', 'finance.view']) || \App\Services\AuthService::hasRole(['super_admin', 'admin', 'membership_officer', 'finance_officer'])): ?>
                <div class="sidebar-section-title"><?= $isBn ? 'সদস্যপদ ও পেমেন্ট' : 'Members & Payments' ?></div>
                <ul class="sidebar-nav-list">
                    <li class="sidebar-nav-item <?= ($activeNav ?? '') === 'admin.members' ? 'active' : '' ?>">
                        <a href="<?= url('/admin/members', $currentLocale) ?>">
                            <span class="sidebar-nav-icon">🪪</span>
                            <span><?= $isBn ? 'সদস্য ও পেমেন্ট ভেরিফিকেশন' : 'Members & Payments' ?></span>
                        </a>
                    </li>
                </ul>
            <?php endif; ?>

            <!-- Activities Management (Strictly Super Admin & Admin) -->
            <?php if (\App\Services\AuthService::can('activities.view') && \App\Services\AuthService::hasRole(['super_admin', 'admin'])): ?>
                <div class="sidebar-section-title"><?= $isBn ? 'কার্যক্রম ও সেবা মহাযজ্ঞ' : 'Activities & Seva' ?></div>
                <ul class="sidebar-nav-list">
                    <li class="sidebar-nav-item <?= ($activeNav ?? '') === 'admin.activities' ? 'active' : '' ?>">
                        <a href="<?= url('/admin/activities', $currentLocale) ?>">
                            <span class="sidebar-nav-icon">🚩</span>
                            <span><?= $isBn ? 'কার্যক্রম পরিচালনা ও লগ' : 'Manage Activities' ?></span>
                        </a>
                    </li>
                </ul>
            <?php endif; ?>

            <!-- Finance (Permission gated: finance.view) -->
            <?php if (\App\Services\AuthService::can('finance.view')): ?>
                <div class="sidebar-section-title"><?= $isBn ? 'আর্থিক হিসাব' : 'Finance & Ledger' ?></div>
                <ul class="sidebar-nav-list">
                    <li class="sidebar-nav-item <?= ($activeNav ?? '') === 'admin.finance' ? 'active' : '' ?>">
                        <a href="<?= url('/admin/finance', $currentLocale) ?>">
                            <span class="sidebar-nav-icon">💰</span>
                            <span><?= $isBn ? 'অনুদান ও মেকার-চেকার ব্যয়' : 'Donations & Expenses' ?></span>
                        </a>
                    </li>
                </ul>
            <?php endif; ?>

            <!-- Audit Logs (Permission gated: audit_logs.view) -->
            <?php if (\App\Services\AuthService::can('audit_logs.view')): ?>
                <div class="sidebar-section-title"><?= $isBn ? 'নিরীক্ষা ও গভর্নেন্স' : 'Governance & Audit' ?></div>
                <ul class="sidebar-nav-list">
                    <li class="sidebar-nav-item <?= ($activeNav ?? '') === 'admin.audit_logs' ? 'active' : '' ?>">
                        <a href="<?= url('/admin/audit-logs', $currentLocale) ?>">
                            <span class="sidebar-nav-icon">📜</span>
                            <span><?= $isBn ? 'অপরিবর্তনীয় অডিট লগ' : 'Immutable Audit Log' ?></span>
                        </a>
                    </li>
                </ul>
            <?php endif; ?>

            <div style="margin-top:auto; padding:var(--space-md) var(--space-lg); border-top:1px solid #1e293b; font-size:0.75rem; color:#64748b;">
                <div><?= $isBn ? 'লগইনকারী:' : 'Logged as:' ?></div>
                <div style="color:#e2e8f0; font-weight:600;"><?= e($isBn ? $currentUser['name_bn'] : $currentUser['name_en']) ?></div>
                <div style="font-family:monospace; color:#38bdf8;"><?= e($currentUser['email']) ?></div>
            </div>
        </aside>

        <!-- Main Workspace -->
        <main class="admin-main">
            <!-- Flash Notification Messages -->
            <?php if ($success = \App\Core\Session::getFlash('success')): ?>
                <div class="alert alert-success" style="margin-bottom:var(--space-lg);">
                    ✓ <?= e($success) ?>
                </div>
            <?php endif; ?>
            <?php if ($warning = \App\Core\Session::getFlash('warning')): ?>
                <div class="alert alert-warning" style="margin-bottom:var(--space-lg);">
                    ⚠ <?= e($warning) ?>
                </div>
            <?php endif; ?>
            <?php if ($danger = \App\Core\Session::getFlash('danger')): ?>
                <div class="alert alert-danger" style="margin-bottom:var(--space-lg);">
                    ✕ <?= e($danger) ?>
                </div>
            <?php endif; ?>

            <?= $content ?>
        </main>
    </div>

    <footer class="admin-footer">
        <?= $isBn 
            ? 'সনাতন ফিলোসফি এন্ড স্ক্রিপচার (SPS) — অভ্যন্তরীণ প্রশাসনিক নিয়ন্ত্রণ ব্যবস্থা • রোল-ভিত্তিক পারমিশন ও ক্রিপ্টোগ্রাফিক অডিট ট্রেইল' 
            : 'Sanatan Philosophy & Scripture (SPS) — Role-Based Administrative Control System • Cryptographic Audit Trail' ?>
    </footer>
    <script src="<?= asset('assets/js/theme-toggle.js') ?>"></script>
</body>
</html>
