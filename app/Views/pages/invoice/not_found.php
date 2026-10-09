<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$searchQuery = $searchQuery ?? '';
?>
<!DOCTYPE html>
<html lang="<?= e($currentLocale) ?>" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($metaTitle ?? 'Invoice Not Found | SPS') ?></title>
    <link rel="icon" type="image/png" href="<?= asset('favicon.png') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/tokens.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/components.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/main.css') ?>">
</head>
<body style="background: #f1f5f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; font-family: 'Plus Jakarta Sans', 'Noto Sans Bengali', sans-serif;">
    <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 16px; padding: 36px 32px; max-width: 520px; width: 100%; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.06);">
        <div style="font-size: 3rem; margin-bottom: 12px;">⚠️</div>
        <h2 style="font-size: 1.4rem; font-weight: 800; color: #0f172a; margin-bottom: 8px;">
            <?= $isBn ? 'ইনভয়েস বা রসিদ পাওয়া যায়নি' : 'Money Receipt Not Found' ?>
        </h2>
        <p style="color: #64748b; font-size: 0.9rem; line-height: 1.5; margin-bottom: 24px;">
            <?= $isBn 
                ? 'প্রদত্ত ট্রানজেকশন আইডি বা মেম্বার কোডের সাথে কোনো বৈধ পেমেন্ট রেকর্ড খুঁজে পাওয়া যায়নি। অনুগ্রহ করে কোডটি পুনরায় যাচাই করুন।' 
                : 'No valid payment record matches the provided Transaction ID or TrxID.' ?>
        </p>

        <form action="<?= url('/invoice', $currentLocale) ?>" method="GET" style="margin-bottom: 20px;">
            <div style="display: flex; gap: 8px;">
                <input type="text" name="q" value="<?= e($searchQuery) ?>" placeholder="<?= $isBn ? 'ট্রানজেকশন আইডি বা TrxID দিন...' : 'Enter TxID or TrxID...' ?>" style="flex: 1; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 18px;">
                    🔍 <?= $isBn ? 'অনুসন্ধান' : 'Search' ?>
                </button>
            </div>
        </form>

        <div style="display: flex; justify-content: center; gap: 10px;">
            <a href="<?= url('/membership/dashboard', $currentLocale) ?>" class="btn btn-secondary btn-sm">
                ← <?= $isBn ? 'ড্যাশবোর্ডে ফিরুন' : 'Dashboard' ?>
            </a>
            <a href="<?= url('/', $currentLocale) ?>" class="btn btn-outline btn-sm">
                🏠 <?= $isBn ? 'হোমপেজ' : 'Home' ?>
            </a>
        </div>
    </div>
</body>
</html>
