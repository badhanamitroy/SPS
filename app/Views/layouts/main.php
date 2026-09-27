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

    <!-- Design System Stylesheets -->
    <link rel="stylesheet" href="<?= asset('assets/css/tokens.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/reset.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/typography.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/components.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/main.css') ?>">
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

    <!-- Client Scripts -->
    <script src="<?= asset('assets/js/i18n-toggle.js') ?>" defer></script>
    <script src="<?= asset('assets/js/components.js') ?>" defer></script>
    <script src="<?= asset('assets/js/main.js') ?>" defer></script>
</body>
</html>
