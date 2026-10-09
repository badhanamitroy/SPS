<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$searchQuery = $searchQuery ?? '';
?>

<section class="section" style="padding: var(--space-3xl) 0; min-height: 70vh; display: flex; align-items: center;">
    <div class="container" style="max-width: 620px;">
        <div style="background: #ffffff; border: 1px solid var(--border-medium); border-radius: var(--radius-xl); padding: var(--space-2xl); box-shadow: var(--shadow-md); text-align: center;">
            
            <div style="width: 64px; height: 64px; border-radius: 50%; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 16px;">
                📜
            </div>

            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--primary-deep); margin: 0 0 8px;">
                <?= $isBn ? 'অফিসিয়াল ইনভয়েস ও রসিদ যাচাই' : 'Verify Official Receipt & Invoice' ?>
            </h1>

            <p style="color: var(--text-secondary); font-size: 0.92rem; line-height: 1.6; margin-bottom: var(--space-xl);">
                <?= $isBn 
                    ? 'মেম্বারশিপ ফি বা সনাতনী সেবা প্রজেক্টে অনুদানকৃত যেকোনো পেমেন্টের ট্রানজেকশন আইডি (TxID) অথবা bKash/Nagad TrxID লিখে আপনার অফিসিয়াল ডিজিটাল মানি রসিদ ডাউনলোড ও প্রিন্ট করুন।' 
                    : 'Search and download your official money receipt using your Transaction ID (TxID) or Mobile Banking TrxID.' ?>
            </p>

            <form action="<?= url('/invoice', $currentLocale) ?>" method="GET" style="margin-bottom: var(--space-xl);">
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <input type="text" name="q" value="<?= e($searchQuery) ?>" placeholder="<?= $isBn ? 'উদাঃ SPS-MEM-2026-524247 অথবা TrxID' : 'e.g. SPS-MEM-2026-524247 or TrxID' ?>" required style="flex: 1; min-width: 240px; padding: 12px 16px; border: 1.5px solid var(--border-medium); border-radius: var(--radius-md); font-size: 0.95rem; font-family: 'JetBrains Mono', monospace;">
                    <button type="submit" class="btn btn-primary" style="padding: 12px 24px; font-weight: 800;">
                        🔍 <?= $isBn ? 'রসিদ খুঁজুন' : 'Find Invoice' ?>
                    </button>
                </div>
            </form>

            <div style="background: var(--bg-surface); border: 1px dashed var(--border-subtle); border-radius: var(--radius-md); padding: 12px 16px; font-size: 0.82rem; color: var(--text-muted); text-align: left;">
                <strong>💡 টিপস:</strong> আপনি যদি একজন নিবন্ধিত সদস্য হন, তবে আপনার <a href="<?= url('/membership/dashboard', $currentLocale) ?>" style="color: var(--primary-deep); font-weight: 700;">মেম্বার ড্যাশবোর্ডে</a> প্রতিটি পেমেন্টের পাশে সরাসরি "রসিদ / Invoice" বাটন পেয়ে যাবেন।
            </div>
        </div>
    </div>
</section>
