<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$sectionSlug = $sectionSlug ?? 'about';
$sectionTitle = $sectionTitle ?? __('common.nav.' . $sectionSlug, ucfirst($sectionSlug));
?>

<div class="container" style="padding-top:var(--space-3xl); padding-bottom:var(--space-4xl);">
    <div class="breadcrumb">
        <a href="<?= e(url('/', $currentLocale)) ?>"><?= e(__('common.nav.home')) ?></a>
        <span class="breadcrumb-separator">/</span>
        <span style="color:var(--text-main); font-weight:600;"><?= e($sectionTitle) ?></span>
    </div>

    <div class="card card-tinted" style="padding:var(--space-3xl); max-width:860px; margin:0 auto; text-align:center;">
        <span class="section-tag" style="justify-content:center;"><?= e(__('common.short_name')) ?> • <?= e($sectionTitle) ?></span>
        <h1 style="margin-bottom:var(--space-md);"><?= e($sectionTitle) ?></h1>
        
        <p class="lead" style="color:var(--text-body); max-width:680px; margin:0 auto var(--space-xl);">
            <?php if ($isBn): ?>
                এই বিভাগটি <strong>সনাতন ফিলোসফি এন্ড স্ক্রিপচার (SPS)</strong> প্ল্যাটফর্মের অন্যতম মূল স্তম্ভ। ফেজ ১-এর ভিত্তি প্রস্তর সফলভাবে সমাপ্ত হয়েছে। মূল ডেটাবেজ, সদস্য মডারেশন ও সার্ভিসেস পরবর্তী ফেজে কার্যকর করা হবে।
            <?php else: ?>
                This section represents a core module of the <strong>Sanatan Philosophy and Scripture (SPS)</strong> platform. With Phase 1 design foundation active, comprehensive database models and services will be deployed in the upcoming phases according to our master architecture roadmap.
            <?php endif; ?>
        </p>

        <div style="display:flex; justify-content:center; gap:var(--space-md); flex-wrap:wrap;">
            <a href="<?= e(url('/', $currentLocale)) ?>" class="btn btn-primary">
                ← <?= $isBn ? 'প্রচ্ছদে ফিরে যান' : 'Return to Home' ?>
            </a>
            <a href="<?= e(url('/components', $currentLocale)) ?>" class="btn btn-secondary">
                <?= e(__('common.actions.view_components')) ?>
            </a>
        </div>
    </div>
</div>
