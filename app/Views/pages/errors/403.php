<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
?>

<div class="container" style="padding:var(--space-4xl) var(--space-lg); text-align:center;">
    <div style="max-width:560px; margin:0 auto;">
        <span class="badge badge-planned" style="margin-bottom:var(--space-md); font-size:0.9rem; background:#fee2e2; color:#991b1b; border:1px solid #fca5a5;">403 • <?= $isBn ? 'অননুমোদিত অনুরোধ' : 'Access Forbidden' ?></span>
        <h1 style="font-size:2.4rem; margin-bottom:var(--space-sm); color:var(--primary-deep);"><?= e($title ?? ($isBn ? 'অননুমোদিত অনুরোধ' : '403 Forbidden')) ?></h1>
        <p style="color:var(--text-muted); font-size:1.05rem; margin-bottom:var(--space-xl); line-height:1.6;">
            <?= e($description ?? ($isBn ? 'অনুরোধটি নিরাপত্তা সুরক্ষার কারণে প্রত্যাখ্যাত হয়েছে।' : 'Security validation failed or you do not have permission to access this resource.')) ?>
        </p>
        <div style="display:flex; justify-content:center; gap:var(--space-md); flex-wrap:wrap;">
            <a href="<?= e(url('/', $currentLocale)) ?>" class="btn btn-primary btn-lg">
                <?= $isBn ? 'প্রচ্ছদে ফিরে যান' : 'Return to Home' ?>
            </a>
            <button type="button" onclick="window.history.back()" class="btn btn-secondary btn-lg">
                <?= $isBn ? 'পূর্বের পৃষ্ঠায় ফিরুন' : 'Go Back' ?>
            </button>
        </div>
    </div>
</div>
