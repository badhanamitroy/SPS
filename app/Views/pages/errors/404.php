<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
?>

<div class="container" style="padding:var(--space-4xl) var(--space-lg); text-align:center;">
    <div style="max-width:560px; margin:0 auto;">
        <span class="badge badge-planned" style="margin-bottom:var(--space-md); font-size:0.9rem;">404 • <?= $isBn ? 'পৃষ্ঠা ত্রুটি' : 'Not Found' ?></span>
        <h1 style="font-size:2.4rem; margin-bottom:var(--space-sm);"><?= e($title ?? ($isBn ? 'পৃষ্ঠাটি পাওয়া যায়নি' : 'Page Not Found')) ?></h1>
        <p style="color:var(--text-muted); font-size:1.05rem; margin-bottom:var(--space-xl);">
            <?= e($description ?? ($isBn ? 'আপনি যে পৃষ্ঠাটি খুঁজছেন তা স্থানান্তরিত বা মুছে ফেলা হয়েছে।' : 'The requested URL was not found on this server.')) ?>
        </p>
        <a href="<?= e(url('/', $currentLocale)) ?>" class="btn btn-primary btn-lg">
            <?= e($homeText ?? ($isBn ? 'প্রচ্ছদে ফিরে যান' : 'Return to Home')) ?>
        </a>
    </div>
</div>
