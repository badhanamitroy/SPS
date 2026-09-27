<?php
/**
 * SPS Public Digital Membership Card Live QR Verification View
 * Privacy-Preserving: Strictly verifies credentials without leaking private phone, email or financial ledger.
 */

$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';

$member = $member ?? null;
$category = $category ?? null;
$plan = $plan ?? null;
?>

<section class="section" style="padding: var(--space-3xl) 0; background: radial-gradient(circle at 50% 10%, rgba(217, 119, 6, 0.05) 0%, rgba(248, 250, 252, 0) 70%), var(--bg-surface); min-height: 75vh; display: flex; align-items: center;">
    <div class="container" style="max-width: 600px; width: 100%;">

        <?php if ($member): ?>
            <!-- Authentic Verification Card -->
            <div style="background: #ffffff; border: 2px solid #22c55e; border-radius: var(--radius-xl); padding: var(--space-2xl); box-shadow: 0 12px 32px rgba(34, 197, 94, 0.12); text-align: center; position: relative;">
                
                <!-- Verification Stamp Header -->
                <div style="width: 72px; height: 72px; border-radius: 50%; background: #dcfce7; color: #16a34a; font-size: 2.2rem; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-md); border: 2px solid #86efac; box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);">
                    ✓
                </div>

                <div style="display: inline-block; background: #dcfce7; color: #15803d; padding: 4px 14px; border-radius: var(--radius-full); font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                    <?= $isBn ? 'যাচাইকৃত অফিসিয়াল ডিজিটাল কার্ড' : 'VERIFIED OFFICIAL SPS MEMBER CARD' ?>
                </div>

                <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--primary-deep); margin: 0 0 4px;">
                    <?= e($member['name_bn']) ?>
                </h1>
                <div style="font-size: 0.95rem; color: var(--text-muted); font-weight: 600; margin-bottom: var(--space-lg);">
                    <?= e($member['name_en']) ?>
                </div>

                <!-- Verified Data Grid -->
                <div style="background: var(--bg-surface); border: 1px solid var(--border-medium); border-radius: var(--radius-lg); padding: var(--space-lg); text-align: left; margin-bottom: var(--space-xl); display: flex; flex-direction: column; gap: 12px; font-size: 0.92rem;">
                    
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border-subtle); padding-bottom: 8px;">
                        <span style="color: var(--text-muted);"><?= $isBn ? 'সদস্যপদ পরিচিতি (ID):' : 'Member ID:' ?></span>
                        <strong style="color: #0284c7; font-family: monospace; font-size: 1.05rem;"><?= e($member['member_code']) ?></strong>
                    </div>

                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border-subtle); padding-bottom: 8px;">
                        <span style="color: var(--text-muted);"><?= $isBn ? 'সদস্যপদ ক্যাটাগরি:' : 'Category:' ?></span>
                        <strong style="color: var(--primary-deep);"><?= e($isBn ? ($category['name_bn'] ?? $member['category_id']) : ($category['name_en'] ?? $member['category_id'])) ?></strong>
                    </div>

                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border-subtle); padding-bottom: 8px;">
                        <span style="color: var(--text-muted);"><?= $isBn ? 'মেম্বারশিপ প্ল্যান:' : 'Membership Plan:' ?></span>
                        <strong style="color: #b45309;"><?= e($isBn ? ($plan['name_bn'] ?? $member['plan_id']) : ($plan['name_en'] ?? $member['plan_id'])) ?></strong>
                    </div>

                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border-subtle); padding-bottom: 8px;">
                        <span style="color: var(--text-muted);"><?= $isBn ? 'বর্তমান স্ট্যাটাস:' : 'Current Status:' ?></span>
                        <strong style="color: #16a34a;">● <?= e($member['status'] ?? 'Active') ?></strong>
                    </div>

                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);"><?= $isBn ? 'বৈধতার মেয়াদ:' : 'Validity Period:' ?></span>
                        <strong style="color: var(--primary-deep);">
                            <?= empty($member['expiry_date']) ? ($isBn ? 'আজীবন সক্রিয় (Lifetime Active)' : 'Lifetime Active') : e($member['expiry_date']) ?>
                        </strong>
                    </div>
                </div>

                <!-- Privacy & Authenticity Disclaimer -->
                <div style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.5; margin-bottom: var(--space-lg);">
                    🔒 <em><?= $isBn 
                        ? 'সদস্যের ব্যক্তিগত গোপনীয়তা নীতি অনুযায়ী ফোন নম্বর, ইমেইল কিংবা লেনদেনের পরিমাণ জনসম্মুখে প্রদর্শন করা হয় না।' 
                        : 'In accordance with privacy policies, sensitive personal contact info and financial transactions remain strictly confidential.' ?></em>
                </div>

                <div style="display: flex; justify-content: center; gap: var(--space-md);">
                    <a href="<?= url('/membership', $currentLocale) ?>" class="btn btn-secondary btn-sm">
                        <?= $isBn ? 'এসপিএস মেম্বারশিপ' : 'About Membership' ?>
                    </a>
                    <a href="<?= url('/membership/apply', $currentLocale) ?>" class="btn btn-primary btn-sm">
                        <?= $isBn ? 'নতুন আবেদন করুন' : 'Apply for SPS Card' ?>
                    </a>
                </div>
            </div>

        <?php else: ?>
            <!-- Invalid / Not Found Card -->
            <div style="background: #ffffff; border: 2px solid #ef4444; border-radius: var(--radius-xl); padding: var(--space-2xl); box-shadow: 0 12px 32px rgba(239, 68, 68, 0.12); text-align: center;">
                <div style="width: 72px; height: 72px; border-radius: 50%; background: #fee2e2; color: #dc2626; font-size: 2.2rem; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-md); border: 2px solid #fca5a5;">
                    ✕
                </div>

                <h1 style="font-size: 1.4rem; font-weight: 800; color: #991b1b; margin: 0 0 var(--space-sm);">
                    <?= $isBn ? 'সদস্যপদ কার্ড যাচাই ব্যর্থ হয়েছে' : 'Membership Verification Failed' ?>
                </h1>

                <p style="color: var(--text-muted); font-size: 0.92rem; margin-bottom: var(--space-xl);">
                    <?= $isBn 
                        ? 'প্রদত্ত মেম্বার কোড বা কিউআর টোকেন দিয়ে কোনো অনুমোদিত সক্রিয় সদস্য রেকর্ড পাওয়া যায়নি। অনুগ্রহ করে কোডটি পুনরায় পরীক্ষা করুন।' 
                        : 'No valid active membership record matches the requested Member Code or QR token.' ?>
                </p>

                <a href="<?= url('/membership', $currentLocale) ?>" class="btn btn-primary">
                    <?= $isBn ? 'মেম্বারশিপ হোমে ফিরে যান' : 'Back to Membership Hub' ?>
                </a>
            </div>
        <?php endif; ?>

    </div>
</section>
