<?php
$currentLocale = current_locale();
$metaTitle = $metaTitle ?? (config('app.name') . ' — ' . config('app.short_name'));
$metaDescription = $metaDescription ?? config('app.motto');
$canonicalUrl = $canonicalUrl ?? url('/', $currentLocale);
$alternateBn = $alternateBn ?? url('/', 'bn');
$alternateEn = $alternateEn ?? url('/', 'en');
?>
<!DOCTYPE html>
<html lang="<?= e($currentLocale) ?>" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Primary SEO Metadata -->
    <title><?= e($metaTitle) ?></title>
    <meta name="description" content="<?= e($metaDescription) ?>">
    <link rel="canonical" href="<?= e($canonicalUrl) ?>">
    <link rel="alternate" hreflang="bn" href="<?= e($alternateBn) ?>">
    <link rel="alternate" hreflang="en" href="<?= e($alternateEn) ?>">
    <link rel="alternate" hreflang="x-default" href="<?= e($alternateBn) ?>">

    <!-- Open Graph & Social Cards -->
    <meta property="og:title" content="<?= e($metaTitle) ?>">
    <meta property="og:description" content="<?= e($metaDescription) ?>">
    <meta property="og:type" content="<?= e($ogType ?? 'website') ?>">
    <meta property="og:url" content="<?= e($canonicalUrl) ?>">
    <meta property="og:site_name" content="<?= e(config('app.name')) ?>">
    <meta property="og:locale" content="<?= $currentLocale === 'bn' ? 'bn_BD' : 'en_US' ?>">
    <meta property="og:image" content="<?= e($ogImage ?? asset('assets/images/brand/sps-logo.png')) ?>">

    <!-- Brand Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= asset('favicon.svg') ?>">
    <link rel="icon" type="image/png" sizes="64x64" href="<?= asset('favicon.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('favicon.png') ?>">

    <!-- FontAwesome 6 Pro/Free Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Design System Stylesheets -->
    <link rel="stylesheet" href="<?= asset('assets/css/tokens.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/reset.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/typography.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/components.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/main.css') ?>">

    <!-- Google Identity Services (GIS) / OAuth 2.0 Web Client -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>

    <!-- Immediate Anti-FOUC Theme Initializer (Syncs with Chrome/Device & localStorage) -->

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
</head>
<body class="sps-body">
    <!-- Accessible Skip Link -->
    <a href="#main-content" class="skip-link">
        <?= $currentLocale === 'bn' ? 'মূল বিষয়বস্তুতে যান' : 'Skip to main content' ?>
    </a>

    <!-- Master Header -->
    <?= \App\Core\View::component('header', ['activeNav' => $activeNav ?? 'home']) ?>

    <!-- Main Content Yield -->
    <main id="main-content">
        <?= $content ?>
    </main>

    <!-- Master Footer -->
    <?= \App\Core\View::component('footer') ?>

    <!-- Modal Elements & Drawers -->
    <?= \App\Core\View::component('search_modal') ?>
    <?= \App\Core\View::component('floating_donation') ?>

    <!-- Client Scripts -->
    <script src="<?= asset('assets/js/theme-toggle.js') ?>"></script>
    <script src="<?= asset('assets/js/i18n-toggle.js') ?>" defer></script>
    <script src="<?= asset('assets/js/components.js') ?>" defer></script>
    <script src="<?= asset('assets/js/main.js') ?>" defer></script>
</body>
</html>
