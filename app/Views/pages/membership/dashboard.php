<?php
/**
 * SPS Member Dashboard & Self-Service Portal
 * Digital Membership Card, Payment Ledger, Transition Engine & Activities
 */

$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';

$member = $member ?? null;
$category = $category ?? null;
$plan = $plan ?? null;
$payments = $payments ?? [];
$history = $history ?? [];
$allMembers = $allMembers ?? [];
?>

<section class="section" style="padding: var(--space-2xl) 0 var(--space-3xl); background: var(--bg-surface);">
    <div class="container" style="max-width: 1140px;">

        <!-- Member Quick Switcher for Pair-Programming & Evaluation -->
        <div style="background: #ffffff; border: 1px solid var(--border-medium); border-radius: var(--radius-lg); padding: 12px 18px; margin-bottom: var(--space-xl); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; box-shadow: var(--shadow-sm);">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 1.2rem;">👤</span>
                <div>
                    <span style="font-size: 0.82rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;"><?= $isBn ? 'সদস্য টেস্ট সুইচবার:' : 'Member Switcher (Demo):' ?></span>
                    <strong style="color: var(--primary-deep); font-size: 0.92rem; margin-left: 4px;"><?= e($isBn ? ($member['name_bn'] ?? '') : ($member['name_en'] ?? '')) ?> (<?= e($member['member_code'] ?? '') ?>)</strong>
                </div>
            </div>

            <form method="GET" action="<?= url('/membership/dashboard', $currentLocale) ?>" style="margin: 0; display: flex; align-items: center; gap: 8px;">
                <select name="as" onchange="this.form.submit()" style="padding: 6px 12px; border-radius: var(--radius-md); border: 1px solid var(--border-medium); font-size: 0.85rem; background: #f8fafc; font-weight: 600;">
                    <?php foreach ($allMembers as $m): ?>
                        <option value="<?= e($m['member_code']) ?>" <?= ($member['member_code'] ?? '') === $m['member_code'] ? 'selected' : '' ?>>
                            <?= e($m['member_code']) ?> — <?= e($m['name_bn']) ?> (<?= e($m['category_id']) ?>, <?= e($m['status']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <noscript><button type="submit" class="btn btn-sm btn-secondary"><?= $isBn ? 'পরিবর্তন' : 'Switch' ?></button></noscript>
            </form>
        </div>

        <!-- Flash alerts -->
        <?php if ($success = \App\Core\Session::getFlash('success')): ?>
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: var(--space-md) var(--space-lg); border-radius: var(--radius-md); margin-bottom: var(--space-xl); display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 1.3rem;">✓</span>
                <div><?= e($success) ?></div>
            </div>
        <?php endif; ?>

        <?php if ($error = \App\Core\Session::getFlash('error')): ?>
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: var(--space-md) var(--space-lg); border-radius: var(--radius-md); margin-bottom: var(--space-xl); display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 1.3rem;">⚠️</span>
                <div><?= e($error) ?></div>
            </div>
        <?php endif; ?>

        <?php if (!$member): ?>
            <div style="text-align: center; padding: var(--space-3xl); background: #ffffff; border-radius: var(--radius-lg); border: 1px solid var(--border-medium);">
                <h2><?= $isBn ? 'সদস্য রেকর্ড পাওয়া যায়নি' : 'No Member Profile Found' ?></h2>
                <p style="color: var(--text-muted); margin-bottom: var(--space-lg);"><?= $isBn ? 'অনুগ্রহ করে নতুন সদস্যপদ আবেদন সম্পন্ন করুন।' : 'Please submit a new membership application.' ?></p>
                <a href="<?= url('/membership/apply', $currentLocale) ?>" class="btn btn-primary"><?= $isBn ? 'আবেদন ফরম' : 'Apply Now' ?></a>
            </div>
        <?php else: ?>

            <!-- Member Identity Banner -->
            <div style="background: #ffffff; border: 1px solid var(--border-medium); border-radius: var(--radius-xl); padding: var(--space-xl); margin-bottom: var(--space-2xl); box-shadow: var(--shadow-sm);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: var(--space-lg);">
                    <div style="display: flex; gap: var(--space-lg); align-items: center;">
                        <div style="width: 76px; height: 76px; border-radius: var(--radius-lg); background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; border: 2px solid var(--border-medium);">
                            <?= $member['category_id'] === 'STUDENT' ? '🎓' : '💼' ?>
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                <h1 style="font-size: 1.6rem; font-weight: 800; color: var(--primary-deep); margin: 0;">
                                    <?= e($isBn ? $member['name_bn'] : $member['name_en']) ?>
                                </h1>
                                <span style="font-family: monospace; font-size: 0.95rem; font-weight: 800; background: #e0f2fe; color: #0369a1; padding: 2px 10px; border-radius: var(--radius-full); border: 1px solid #bae6fd;">
                                    <?= e($member['member_code']) ?>
                                </span>
                            </div>
                            <div style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 4px;">
                                <?= e($isBn ? ($category['name_bn'] ?? '') : ($category['name_en'] ?? '')) ?> • 
                                <span style="font-weight: 700; color: #b45309;"><?= e($isBn ? ($plan['name_bn'] ?? '') : ($plan['name_en'] ?? '')) ?></span>
                            </div>
                            <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">
                                <?= $isBn ? 'যোগদানের তারিখ:' : 'Joined:' ?> <?= e($member['joined_at'] ?? '2024') ?> | 
                                <?= e($member['email'] ?? '') ?> | <?= e($member['phone'] ?? '') ?>
                            </div>
                        </div>
                    </div>

                    <!-- Status Badge & Action -->
                    <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                        <?php
                            $status = $member['status'] ?? 'Active';
                            $statusBg = '#dcfce7'; $statusColor = '#15803d'; $statusLabel = $isBn ? 'সক্রিয় সদস্য (Active)' : 'Active Member';
                            if ($status === 'Lifetime Active') {
                                $statusBg = '#fef3c7'; $statusColor = '#b45309'; $statusLabel = $isBn ? '👑 আজীবন সক্রিয় (Lifetime Active)' : '👑 Lifetime Active';
                            } elseif ($status === 'Payment Due') {
                                $statusBg = '#fee2e2'; $statusColor = '#b91c1c'; $statusLabel = $isBn ? 'পেমেন্ট বকেয়া (Payment Due)' : 'Payment Due';
                            } elseif ($status === 'Pending') {
                                $statusBg = '#f1f5f9'; $statusColor = '#475569'; $statusLabel = $isBn ? 'অনুমোদনাধীন (Pending)' : 'Pending Review';
                            } elseif ($status === 'Suspended') {
                                $statusBg = '#fef2f2'; $statusColor = '#991b1b'; $statusLabel = $isBn ? 'স্থগিত (Suspended)' : 'Suspended';
                            }
                        ?>
                        <span style="background: <?= $statusBg ?>; color: <?= $statusColor ?>; padding: 6px 16px; border-radius: var(--radius-full); font-weight: 800; font-size: 0.85rem; border: 1px solid currentColor;">
                            ● <?= $statusLabel ?>
                        </span>

                        <div style="font-size: 0.82rem; color: var(--text-muted);">
                            <?= $isBn ? 'মেয়াদ উত্তীর্ণ:' : 'Valid Until:' ?> 
                            <strong style="color: var(--primary-deep);"><?= empty($member['expiry_date']) ? ($isBn ? 'আজীবন / কোনো মেয়াদ নেই' : 'Lifetime / No Expiry') : e($member['expiry_date']) ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Two Columns: Left = Digital Card & Actions, Right = Details, History & Ledger -->
            <div style="display: grid; grid-template-columns: 380px 1fr; gap: var(--space-2xl); align-items: start;">
                
                <!-- Left Column: Digital Card Presentation -->
                <div>
                    <!-- Digital Membership Card Component -->
                    <div id="printableCard" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-radius: 18px; padding: 24px; color: #ffffff; box-shadow: 0 16px 36px rgba(15, 23, 42, 0.25); border: 1px solid rgba(255,255,255,0.12); position: relative; overflow: hidden; margin-bottom: var(--space-lg);">
                        <!-- Card Header -->
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="<?= asset('assets/images/brand/sps-logo.png') ?>" alt="SPS" style="height: 36px; width: auto;">
                                <div>
                                    <div style="font-size: 0.85rem; font-weight: 800; letter-spacing: 0.5px; color: #ffffff;">সনাতন দর্শন ও শাস্ত্র</div>
                                    <div style="font-size: 0.65rem; color: #94a3b8; letter-spacing: 0.8px;">SANATAN PHILOSOPHY & SCRIPTURE</div>
                                </div>
                            </div>
                            <span style="background: rgba(255,255,255,0.12); color: #f8fafc; font-size: 0.68rem; font-weight: 700; padding: 2px 8px; border-radius: var(--radius-full);">
                                SPS CARD
                            </span>
                        </div>

                        <!-- Card Body -->
                        <div style="display: flex; gap: 16px; align-items: center; margin-bottom: 20px;">
                            <div style="width: 60px; height: 60px; border-radius: 10px; background: #334155; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; border: 2px solid rgba(255,255,255,0.2);">
                                👤
                            </div>
                            <div>
                                <div style="font-size: 1.15rem; font-weight: 800; color: #ffffff; line-height: 1.2;">
                                    <?= e($member['name_bn']) ?>
                                </div>
                                <div style="font-size: 0.8rem; color: #94a3b8;">
                                    <?= e($member['name_en']) ?>
                                </div>
                                <div style="font-size: 0.9rem; color: #38bdf8; font-weight: 800; font-family: monospace; margin-top: 4px;">
                                    ID: <?= e($member['member_code']) ?>
                                </div>
                            </div>
                        </div>

                        <div style="background: rgba(255,255,255,0.06); border-radius: 8px; padding: 10px 14px; margin-bottom: 16px; font-size: 0.8rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                <span style="color: #94a3b8;"><?= $isBn ? 'ক্যাটাগরি:' : 'Category:' ?></span>
                                <strong style="color: #f1f5f9;"><?= e($isBn ? ($category['name_bn'] ?? $member['category_id']) : ($category['name_en'] ?? $member['category_id'])) ?></strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #94a3b8;"><?= $isBn ? 'প্ল্যান:' : 'Plan:' ?></span>
                                <strong style="color: #fde047;"><?= e($isBn ? ($plan['name_bn'] ?? $member['plan_id']) : ($plan['name_en'] ?? $member['plan_id'])) ?></strong>
                            </div>
                        </div>

                        <!-- Card Footer & QR Code -->
                        <div style="display: flex; justify-content: space-between; align-items: flex-end; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.12);">
                            <div>
                                <div style="font-size: 0.65rem; color: #94a3b8; text-transform: uppercase;"><?= $isBn ? 'মেয়াদ / স্থায়িত্ব:' : 'Validity:' ?></div>
                                <div style="font-size: 0.85rem; font-weight: 800; color: #4ade80;">
                                    <?= empty($member['expiry_date']) ? 'LIFETIME ACTIVE' : e($member['expiry_date']) ?>
                                </div>
                            </div>

                            <!-- Live Safe QR Code Link -->
                            <div style="text-align: right;">
                                <a href="<?= url('/membership/verify?code=' . e($member['member_code']), $currentLocale) ?>" target="_blank" style="display: inline-block; background: #ffffff; padding: 6px; border-radius: 6px;" title="<?= $isBn ? 'অনলাইন কিউআর যাচাই পেজ' : 'Live QR Verify' ?>">
                                    <div style="width: 44px; height: 44px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 2px;">
                                        <div style="background: #0f172a;"></div><div style="background: #0f172a;"></div><div style="background: #0f172a;"></div>
                                        <div style="background: #0f172a;"></div><div style="background: #ffffff;"></div><div style="background: #0f172a;"></div>
                                        <div style="background: #0f172a;"></div><div style="background: #0f172a;"></div><div style="background: #0f172a;"></div>
                                    </div>
                                </a>
                                <div style="font-size: 0.62rem; color: #94a3b8; margin-top: 2px;">QR Code Verify</div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Action Buttons -->
                    <div style="display: flex; gap: var(--space-sm); margin-bottom: var(--space-xl);">
                        <button onclick="window.print()" class="btn btn-secondary btn-sm" style="flex: 1;">
                            🖨️ <?= $isBn ? 'কার্ড প্রিন্ট / সংরক্ষণ' : 'Print Card' ?>
                        </button>
                        <a href="<?= url('/membership/verify?code=' . e($member['member_code']), $currentLocale) ?>" target="_blank" class="btn btn-outline btn-sm" style="flex: 1;">
                            🔗 <?= $isBn ? 'কিউআর প্রিভিউ' : 'QR Link' ?>
                        </a>
                    </div>

                    <!-- Member Renewal / Payment Form Box -->
                    <div style="background: #ffffff; border: 1px solid var(--border-medium); border-radius: var(--radius-lg); padding: var(--space-lg); margin-bottom: var(--space-xl); box-shadow: var(--shadow-sm);">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                            <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--primary-deep); margin: 0;">
                                💳 <?= $isBn ? 'সদস্যপদ ফি নবায়ন / আপগ্রেড' : 'Renew / Upgrade' ?>
                            </h3>
                            <span style="font-size: 0.75rem; background: #e0f2fe; color: #0284c7; padding: 2px 8px; border-radius: var(--radius-full); font-weight: 700;">
                                🪙 Finance Desk
                            </span>
                        </div>
                        <p style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: var(--space-md); line-height: 1.4;">
                            <?= $isBn 
                                ? 'নিয়মিত মাসিক বা বাৎসরিক ফি প্রদান করে আপনার মেম্বারশিপ সক্রিয় রাখুন।' 
                                : 'Pay regular monthly or annual fees to keep your membership active.' ?>
                        </p>

                        <div style="background: linear-gradient(135deg, #fdf2f8 0%, #fff1f2 100%); border: 1px solid #fbcfe8; border-radius: 8px; padding: 12px; margin-bottom: 14px; font-size: 0.8rem; color: #831843;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; flex-wrap: wrap; gap: 4px;">
                                <strong style="color: #9d174d;">📱 <?= $isBn ? 'অফিসিয়াল বিকাশ নম্বর:' : 'Official bKash:' ?></strong>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <code style="background: #ffffff; padding: 2px 6px; border-radius: 4px; font-weight: 800; color: #be185d;">01700-000000</code>
                                    <button type="button" onclick="navigator.clipboard && navigator.clipboard.writeText('01700-000000'); alert('কপি করা হয়েছে: 01700-000000');" style="border: 1px solid #fbcfe8; background: #fff; border-radius: 4px; font-size: 0.7rem; padding: 1px 6px; cursor: pointer; color: #9d174d;">
                                        📋 কপি
                                    </button>
                                </div>
                            </div>
                            <div style="font-size: 0.75rem; color: #9d174d; line-height: 1.4;">
                                <?= $isBn 
                                    ? 'বিকাশ অ্যাপ থেকে Send Money করার পর TrxID এবং সেন্ট মানি কনফার্মেশন স্ক্রিনশট (Sent SS) নিচে আপলোড করুন।' 
                                    : 'Send fee via bKash Send Money, enter TrxID, and upload the Sent SS confirmation.' ?>
                            </div>
                        </div>

                        <!-- Finance Officer Verification Advisory -->
                        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 10px 12px; margin-bottom: 14px; font-size: 0.8rem; color: #166534; display: flex; gap: 8px; align-items: flex-start;">
                            <span style="font-size: 1.1rem; line-height: 1;">🪙</span>
                            <div>
                                <strong><?= $isBn ? 'ফাইন্যান্স অফিসার (কোষাধ্যক্ষ) যাচাইকরণ:' : 'Finance Officer (Treasurer) Verification:' ?></strong>
                                <?= $isBn 
                                    ? 'মেম্বারশিপের প্রতিটি পেমেন্ট সরাসরি ফাইন্যান্স অফিসার (কোষাধ্যক্ষ - জয় চক্রবর্তী) অডিট করে ভেরিফাই করবেন। সুপার অ্যাডমিন ও অন্য ২ জন অ্যাডমিন সার্বক্ষণিক এটি পর্যবেক্ষণ ও অডিট করছেন।' 
                                    : 'Payments are audited & verified solely by the Finance Officer (Joy Chakraborty) under Super Admin & Admins monitoring.' ?>
                            </div>
                        </div>

                        <form action="<?= url('/membership/payment', $currentLocale) ?>" method="POST" enctype="multipart/form-data" id="dashPaymentForm">
                            <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>
                            <input type="hidden" name="member_id" value="<?= e($member['id']) ?>">
                            <input type="hidden" name="payment_screenshot_url" id="dash_preset_screenshot" value="assets/images/payments/bkash-success-sample.svg">

                            <div style="margin-bottom: var(--space-sm);">
                                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 2px;">
                                    <?= $isBn ? 'পেমেন্ট ধরন' : 'Payment Type' ?>
                                </label>
                                <select name="payment_type" class="form-select" style="width: 100%; padding: 8px 12px; font-size: 0.85rem; border: 1px solid var(--border-medium); border-radius: var(--radius-md);">
                                    <option value="monthly"><?= $isBn ? 'মাসিক সদস্যপদ নবায়ন' : 'Monthly Renewal' ?></option>
                                    <option value="yearly"><?= $isBn ? 'বাৎসরিক প্ল্যান (৳১,০০০ / ১ বছর)' : 'Yearly Renewal (৳1,000)' ?></option>
                                    <option value="lifetime"><?= $isBn ? 'আজীবন সদস্যপদ আপগ্রেড (৳১০,০০০)' : 'Upgrade to Lifetime (৳10,000)' ?></option>
                                </select>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-xs); margin-bottom: var(--space-sm);">
                                <div>
                                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 2px;">
                                        <?= $isBn ? 'মাধ্যম' : 'Method' ?>
                                    </label>
                                    <select name="payment_method" id="dash_payment_method" class="form-select" style="width: 100%; padding: 8px 10px; font-size: 0.85rem; border: 1px solid var(--border-medium); border-radius: var(--radius-md); font-weight: 700;">
                                        <option value="bKash" selected>🌸 bKash (বিকাশ)</option>
                                        <option value="Nagad">🔥 Nagad (নগদ)</option>
                                        <option value="Rocket">🚀 Rocket (রকেট)</option>
                                        <option value="Bank">🏦 Bank Transfer</option>
                                    </select>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 2px;">
                                        <?= $isBn ? 'টাকার পরিমাণ' : 'Amount' ?>
                                    </label>
                                    <input type="number" name="amount" placeholder="<?= $member['category_id'] === 'STUDENT' ? '50' : '100' ?>" style="width: 100%; padding: 8px 10px; font-size: 0.85rem; border: 1px solid var(--border-medium); border-radius: var(--radius-md);">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-xs); margin-bottom: var(--space-sm);">
                                <div>
                                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 2px;">
                                        <?= $isBn ? 'প্রেরক নম্বর *' : 'Sender Phone *' ?>
                                    </label>
                                    <input type="text" name="sender_number" required value="<?= e($member['phone'] ?? '') ?>" placeholder="01XXXXXXXXX" style="width: 100%; padding: 8px 10px; font-size: 0.85rem; border: 1px solid var(--border-medium); border-radius: var(--radius-md); font-family: monospace;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 2px;">
                                        <?= $isBn ? 'কার একাউন্ট / নাম' : 'Sender Name' ?>
                                    </label>
                                    <input type="text" name="sender_name" placeholder="<?= $isBn ? 'উদাঃ অমিত সেন' : 'Sender account name' ?>" style="width: 100%; padding: 8px 10px; font-size: 0.85rem; border: 1px solid var(--border-medium); border-radius: var(--radius-md);">
                                </div>
                            </div>

                            <!-- Provider TrxID Field -->
                            <div style="margin-bottom: var(--space-sm);">
                                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 2px;">
                                    <?= $isBn ? 'ট্রানজেকশন আইডি (TrxID) *' : 'Transaction ID (TrxID) *' ?>
                                    <span style="font-size: 0.72rem; color: #dc2626; font-weight: 800;">(বাধ্যতামূলক)</span>
                                </label>
                                <div style="display: flex; gap: 6px;">
                                    <input type="text" name="trx_id" id="dash_trx_id" required placeholder="e.g. BKA8X92JQK" style="flex: 1; padding: 8px 12px; font-size: 0.9rem; font-family: monospace; font-weight: 900; border: 1.5px solid #fdba74; border-radius: var(--radius-md); text-transform: uppercase; background: #fffaf5; color: #9a3412;">
                                    <button type="button" onclick="document.getElementById('dash_trx_id').value = 'BKA' + Math.random().toString(36).substring(2, 8).toUpperCase();" class="btn btn-sm btn-ghost" style="border: 1px solid var(--border-medium); font-size: 0.75rem; padding: 4px 8px; white-space: nowrap;" title="<?= $isBn ? 'টেস্ট TrxID জেনারেট করুন' : 'Generate Demo TrxID' ?>">
                                        🎲 টেস্ট TrxID
                                    </button>
                                </div>
                            </div>

                            <!-- Payment Reference & Time -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-xs); margin-bottom: var(--space-sm);">
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 2px;">
                                        <?= $isBn ? 'রেফারেন্স নোট' : 'Reference' ?>
                                    </label>
                                    <input type="text" name="payment_reference" placeholder="<?= $isBn ? 'উদাঃ নবায়ন ফি' : 'e.g. Renewal' ?>" style="width: 100%; padding: 6px 10px; font-size: 0.82rem; border: 1px solid var(--border-medium); border-radius: var(--radius-md);">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 2px;">
                                        <?= $isBn ? 'পেমেন্ট সময়' : 'Time' ?>
                                    </label>
                                    <input type="text" name="payment_time" value="<?= date('Y-m-d H:i') ?>" style="width: 100%; padding: 6px 10px; font-size: 0.82rem; border: 1px solid var(--border-medium); border-radius: var(--radius-md); font-family: monospace;">
                                </div>
                            </div>

                            <!-- Payment Screenshot Upload & Live Preview -->
                            <div style="margin-bottom: var(--space-md);">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                    <label style="font-size: 0.8rem; font-weight: 800; color: #9d174d; margin: 0;">
                                        📸 <?= $isBn ? 'সেন্ট মানি স্ক্রিনশট (Sent SS) *' : 'Sent Money Screenshot (Sent SS) *' ?>
                                    </label>
                                    <span style="font-size: 0.7rem; background: #be185d; color: #fff; padding: 1px 8px; border-radius: 999px; font-weight: 800;">
                                        <?= $isBn ? 'বিকাশে আবশ্যক' : 'bKash Mandatory' ?>
                                    </span>
                                </div>
                                
                                <div style="border: 2px dashed #f472b6; border-radius: 8px; padding: 12px; text-align: center; background: #fdf2f8; position: relative;">
                                    <input type="file" name="payment_screenshot" id="dash_screenshot_file" accept="image/*" onchange="previewDashScreenshot(this)" style="display: none;">
                                    
                                    <div id="dash_screenshot_empty" onclick="document.getElementById('dash_screenshot_file').click();" style="cursor: pointer; padding: 6px 0;">
                                        <div style="font-size: 1.5rem; color: #be185d;">📸</div>
                                        <div style="font-size: 0.82rem; font-weight: 700; color: #9d174d; margin-top: 2px;">
                                            <?= $isBn ? 'স্ক্রিনশট আপলোড করতে ক্লিক করুন' : 'Click to Upload Sent SS' ?>
                                        </div>
                                        <div style="font-size: 0.72rem; color: var(--text-muted);">PNG, JPG, WebP অথবা SVG</div>
                                    </div>

                                    <!-- Live Preview Container -->
                                    <div id="dash_screenshot_preview_box" style="display: block; position: relative; margin-top: 6px;">
                                        <img id="dash_screenshot_preview_img" src="<?= asset('assets/images/payments/bkash-success-sample.svg') ?>" alt="Payment Receipt" style="max-height: 130px; max-width: 100%; border-radius: 6px; border: 1px solid var(--border-medium); box-shadow: 0 2px 6px rgba(0,0,0,0.06); object-fit: contain; background: #fff;">
                                        <div style="margin-top: 6px; display: flex; justify-content: center; gap: 8px;">
                                            <button type="button" onclick="document.getElementById('dash_screenshot_file').click();" class="btn btn-sm btn-ghost" style="padding: 2px 8px; font-size: 0.72rem; border: 1px solid var(--border-medium);">
                                                🔄 <?= $isBn ? 'ছবি পরিবর্তন' : 'Change' ?>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Demo Screenshot Presets -->
                                    <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #fbcfe8; display: flex; align-items: center; justify-content: center; gap: 6px; flex-wrap: wrap;">
                                        <span style="font-size: 0.72rem; color: #9d174d;"><?= $isBn ? 'টেস্ট স্যাম্পল:' : 'Demo Presets:' ?></span>
                                        <button type="button" onclick="setDashPresetScreenshot('assets/images/payments/bkash-success-sample.svg')" class="btn btn-xs btn-ghost" style="font-size: 0.7rem; padding: 2px 6px; border: 1px solid #fbcfe8; color: #be185d; background: #fff;">
                                            🌸 bKash রসিদ
                                        </button>
                                        <button type="button" onclick="setDashPresetScreenshot('assets/images/payments/nagad-success-sample.svg')" class="btn btn-xs btn-ghost" style="font-size: 0.7rem; padding: 2px 6px; border: 1px solid #fed7aa; color: #c2410c; background: #fff;">
                                            🔥 Nagad রসিদ
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; font-weight: 800; padding: 11px; font-size: 0.92rem; box-shadow: 0 4px 10px rgba(180, 83, 9, 0.2);">
                                🚀 <?= $isBn ? 'পেমেন্ট রসিদ সাবমিট করুন' : 'Submit Payment' ?>
                            </button>
                        </form>
                    </div>

                    <script>
                    function previewDashScreenshot(input) {
                        if (input.files && input.files[0]) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                document.getElementById('dash_screenshot_preview_img').src = e.target.result;
                                document.getElementById('dash_screenshot_preview_box').style.display = 'block';
                                document.getElementById('dash_preset_screenshot').value = '';
                            };
                            reader.readAsDataURL(input.files[0]);
                        }
                    }

                    function setDashPresetScreenshot(path) {
                        document.getElementById('dash_screenshot_preview_img').src = '<?= asset('') ?>' + path;
                        document.getElementById('dash_preset_screenshot').value = path;
                        document.getElementById('dash_screenshot_file').value = '';
                    }
                    </script>

                    <!-- Member Quick Shortcuts: Blog, Volunteer, Library -->
                    <div style="background: #ffffff; border: 1px solid var(--border-medium); border-radius: var(--radius-lg); padding: var(--space-lg);">
                        <h4 style="font-size: 0.95rem; font-weight: 800; color: var(--primary-deep); margin: 0 0 var(--space-sm);">
                            <?= $isBn ? 'সদস্য এক্টিভিটিজ ও সুযোগ' : 'Member Shortcuts' ?>
                        </h4>
                        <div style="display: flex; flex-direction: column; gap: 8px; font-size: 0.88rem;">
                            <a href="<?= url('/blog/write', $currentLocale) ?>" style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; background: var(--bg-surface); border-radius: var(--radius-md); text-decoration: none; color: var(--primary-deep); font-weight: 600;">
                                <span>✍️ <?= $isBn ? 'আমার ব্লগ ও নতুন লেখা' : 'Write a Blog Post' ?></span>
                                <span style="color: var(--text-muted);">→</span>
                            </a>
                            <a href="<?= url('/activities', $currentLocale) ?>" style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; background: var(--bg-surface); border-radius: var(--radius-md); text-decoration: none; color: var(--primary-deep); font-weight: 600;">
                                <span>🚩 <?= $isBn ? 'সেবামূলক কার্যক্রম ও প্রকল্প' : 'SPS Activities & Projects' ?></span>
                                <span style="color: var(--text-muted);">→</span>
                            </a>
                            <a href="<?= url('/library', $currentLocale) ?>" style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; background: var(--bg-surface); border-radius: var(--radius-md); text-decoration: none; color: var(--primary-deep); font-weight: 600;">
                                <span>📚 <?= $isBn ? 'গ্রন্থাগার ও শাস্ত্র সংগ্রহ' : 'Scripture Library' ?></span>
                                <span style="color: var(--text-muted);">→</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Transition Engine, History & Payment Ledger -->
                <div>
                    <!-- Student -> Earning Category Transition Card (if student) -->
                    <?php if ($member['category_id'] === 'STUDENT'): ?>
                        <div style="background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); border: 2px solid #fdba74; border-radius: var(--radius-xl); padding: var(--space-xl); margin-bottom: var(--space-2xl); position: relative;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: var(--space-md); flex-wrap: wrap;">
                                <div>
                                    <div style="display: inline-flex; align-items: center; gap: 6px; background: #c2410c; color: #ffffff; padding: 2px 10px; border-radius: var(--radius-full); font-size: 0.72rem; font-weight: 800; margin-bottom: 6px;">
                                        🎓 ➔ 💼 <?= $isBn ? 'ক্যাটাগরি রূপান্তর ব্যবস্থা' : 'Category Transition' ?>
                                    </div>
                                    <h3 style="font-size: 1.25rem; font-weight: 800; color: #9a3412; margin: 0 0 6px;">
                                        <?= $isBn ? 'শিক্ষার্থী থেকে উপার্জনশীল সদস্যপদে রূপান্তর' : 'Transition to Earning Member' ?>
                                    </h3>
                                    <p style="color: #7c2d12; font-size: 0.88rem; line-height: 1.5; margin: 0 0 var(--space-md); max-width: 580px;">
                                        <?= $isBn 
                                            ? 'আপনি কি পড়াশোনা সম্পন্ন করে কর্মজীবনে বা পেশায় পদার্পণ করেছেন? আপনার স্থায়ী মেম্বার আইডি ('.$member['member_code'].') অক্ষুণ্ণ রেখে সিস্টেমে ক্যাটাগরি রূপান্তর সম্পন্ন করুন।' 
                                            : 'Graduated and entered professional life? Transition smoothly while keeping your permanent Member ID ('.$member['member_code'].').' ?>
                                    </p>
                                </div>

                                <button onclick="document.getElementById('transition-form-box').style.display = document.getElementById('transition-form-box').style.display === 'none' ? 'block' : 'none';" class="btn btn-primary btn-sm" style="background: #c2410c; border-color: #c2410c; font-weight: 800;">
                                    <?= $isBn ? 'রূপান্তর আবেদন করুন ➔' : 'Apply Transition ➔' ?>
                                </button>
                            </div>

                            <!-- Expandable Transition Form -->
                            <div id="transition-form-box" style="display: none; background: #ffffff; border-radius: var(--radius-lg); padding: var(--space-lg); margin-top: var(--space-md); border: 1px solid #fed7aa;">
                                <form action="<?= url('/membership/transition', $currentLocale) ?>" method="POST">
                                    <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>
                                    <input type="hidden" name="member_id" value="<?= e($member['id']) ?>">
                                    <input type="hidden" name="new_category" value="EARNING">

                                    <div style="margin-bottom: var(--space-md);">
                                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                                            <?= $isBn ? 'পরবর্তী মেম্বারশিপ প্ল্যান' : 'Select New Plan' ?>
                                        </label>
                                        <select name="new_plan" class="form-select" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-medium); border-radius: var(--radius-md);">
                                            <option value="EARNING_MONTHLY"><?= $isBn ? 'উপার্জনশীল সদস্য - মাসিক (৳১০০/মাস)' : 'Earning Member - Monthly (৳100/mo)' ?></option>
                                            <option value="YEARLY"><?= $isBn ? 'বাৎসরিক সদস্যপদ (৳১,০০০/বছর)' : 'Yearly Member (৳1,000/yr)' ?></option>
                                            <option value="LIFETIME"><?= $isBn ? 'আজীবন সদস্যপদ (৳১০,০০০)' : 'Lifetime Member (৳10,000)' ?></option>
                                        </select>
                                    </div>

                                    <div style="margin-bottom: var(--space-md);">
                                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                                            <?= $isBn ? 'রূপান্তরের কারণ / বর্তমান কর্মবিবরণ' : 'Reason / Professional Update' ?>
                                        </label>
                                        <textarea name="reason" rows="2" placeholder="<?= $isBn ? 'উদাঃ শিক্ষাজীবন সমাপ্ত করে চাকরি/ব্যবসায় যোগদান হেতু উপার্জনশীল সদস্যপদে রূপান্তর।' : 'Graduated and entered professional employment.' ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-medium); border-radius: var(--radius-md); font-size: 0.88rem;"></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-sm" style="background: #c2410c; border-color: #c2410c; font-weight: 800;">
                                        <?= $isBn ? 'রূপান্তর নিশ্চিত করুন' : 'Confirm Transition' ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Historical Transition & Status Audit Trail -->
                    <?php if (!empty($history)): ?>
                        <div style="background: #ffffff; border: 1px solid var(--border-medium); border-radius: var(--radius-xl); padding: var(--space-xl); margin-bottom: var(--space-2xl); box-shadow: var(--shadow-sm);">
                            <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--primary-deep); margin: 0 0 var(--space-md); display: flex; align-items: center; gap: 8px;">
                                <span>📜</span>
                                <span><?= $isBn ? 'সদস্যপদ রূপান্তর ইতিহাস (Membership History)' : 'Membership Transition History' ?></span>
                            </h3>

                            <div style="position: relative; padding-left: 20px; border-left: 2px solid var(--border-medium); display: flex; flex-direction: column; gap: var(--space-md);">
                                <?php foreach ($history as $h): ?>
                                    <div style="position: relative;">
                                        <div style="position: absolute; left: -26px; top: 2px; width: 10px; height: 10px; border-radius: 50%; background: #0284c7; border: 2px solid #ffffff;"></div>
                                        <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">
                                            <?= e($h['changed_at'] ?? '') ?>
                                        </div>
                                        <div style="font-size: 0.92rem; font-weight: 700; color: var(--primary-deep); margin-top: 2px;">
                                            <?= e($h['old_category'] ?? '') ?> ➔ <span style="color: #c2410c;"><?= e($h['new_category'] ?? '') ?></span>
                                            (<?= e($h['old_plan'] ?? '') ?> ➔ <?= e($h['new_plan'] ?? '') ?>)
                                        </div>
                                        <div style="font-size: 0.82rem; color: var(--text-secondary); margin-top: 2px;">
                                            <?= e($h['reason'] ?? '') ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Payment Ledger & Financial Transactions Table -->
                    <div style="background: #ffffff; border: 1px solid var(--border-medium); border-radius: var(--radius-xl); padding: var(--space-xl); box-shadow: var(--shadow-sm);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-md); flex-wrap: wrap; gap: 10px;">
                            <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--primary-deep); margin: 0; display: flex; align-items: center; gap: 8px;">
                                <span>💰</span>
                                <span><?= $isBn ? 'পেমেন্ট ও চাঁদা খতিয়ান (Payment History)' : 'Payment Ledger & Transactions' ?></span>
                            </h3>
                            <span style="font-size: 0.8rem; color: var(--text-muted); background: var(--bg-surface); padding: 3px 10px; border-radius: var(--radius-full);">
                                <?= $isBn ? 'মোট লেনদেন:' : 'Transactions:' ?> <?= count($payments) ?>
                            </span>
                        </div>

                        <?php if (empty($payments)): ?>
                            <div style="text-align: center; padding: var(--space-xl); color: var(--text-muted); font-size: 0.9rem;">
                                <?= $isBn ? 'এখনো কোনো পেমেন্ট রেকর্ড পাওয়া যায়নি।' : 'No payment records available.' ?>
                            </div>
                        <?php else: ?>
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse; font-size: 0.88rem;">
                                    <thead>
                                        <tr style="border-bottom: 2px solid var(--border-medium); text-align: left; background: var(--bg-surface); color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">
                                            <th style="padding: 10px 12px;"><?= $isBn ? 'ট্রানজেকশন ও TrxID' : 'TxID & TrxID' ?></th>
                                            <th style="padding: 10px 12px;"><?= $isBn ? 'তারিখ' : 'Date' ?></th>
                                            <th style="padding: 10px 12px;"><?= $isBn ? 'প্ল্যান ধরন' : 'Type' ?></th>
                                            <th style="padding: 10px 12px;"><?= $isBn ? 'পরিমাণ' : 'Amount' ?></th>
                                            <th style="padding: 10px 12px;"><?= $isBn ? 'মাধ্যম ও প্রেরক' : 'Method & Sender' ?></th>
                                            <th style="padding: 10px 12px; text-align: center;"><?= $isBn ? 'রসিদ স্ক্রিনশট' : 'Receipt' ?></th>
                                            <th style="padding: 10px 12px;"><?= $isBn ? 'স্ট্যাটাস ও ভেরিফিকেশন' : 'Status & Verification' ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($payments as $p): ?>
                                            <tr style="border-bottom: 1px solid var(--border-subtle); vertical-align: middle;">
                                                <td style="padding: 10px 12px;">
                                                    <div style="font-family: monospace; font-weight: 700; color: #0284c7; font-size: 0.85rem;">
                                                        <?= e($p['transaction_id'] ?? '') ?>
                                                    </div>
                                                    <?php if (!empty($p['trx_id'])): ?>
                                                        <div style="display: flex; align-items: center; gap: 4px; margin-top: 3px;">
                                                            <span style="font-size: 0.72rem; color: var(--text-muted);">TrxID:</span>
                                                            <span style="font-family: monospace; font-weight: 800; font-size: 0.8rem; background: #f1f5f9; padding: 1px 6px; border-radius: 4px; color: var(--primary-deep);">
                                                                <?= e($p['trx_id']) ?>
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td style="padding: 10px 12px; color: var(--text-muted); font-size: 0.82rem; white-space: nowrap;">
                                                    <?= e($p['payment_date'] ?? $p['created_at'] ?? '') ?>
                                                </td>
                                                <td style="padding: 10px 12px;">
                                                    <span style="background: #f1f5f9; padding: 2px 8px; border-radius: var(--radius-sm); font-size: 0.75rem; font-weight: 700;">
                                                        <?= e($p['payment_type'] ?? '') ?>
                                                    </span>
                                                </td>
                                                <td style="padding: 10px 12px; font-weight: 800; color: var(--primary-deep); font-size: 0.95rem;">
                                                    ৳<?= number_format((float)($p['amount'] ?? 0)) ?>
                                                </td>
                                                <td style="padding: 10px 12px; font-size: 0.82rem;">
                                                    <div style="font-weight: 700; color: var(--text-primary);"><?= e($p['payment_method'] ?? 'bKash') ?></div>
                                                    <div style="color: var(--text-muted); font-size: 0.75rem; font-family: monospace;"><?= e($p['sender_number'] ?? '') ?></div>
                                                </td>
                                                <td style="padding: 10px 12px; text-align: center;">
                                                    <?php 
                                                        $screenshot = $p['payment_screenshot'] ?? '';
                                                        $imgSrc = !empty($screenshot) ? (str_starts_with($screenshot, 'http') ? $screenshot : asset($screenshot)) : asset('assets/images/payments/bkash-success-sample.svg');
                                                    ?>
                                                    <button type="button" onclick="openReceiptZoomModal('<?= $imgSrc ?>', '<?= e($p['trx_id'] ?? $p['transaction_id']) ?>')" style="background: none; border: none; cursor: pointer; padding: 0;" title="<?= $isBn ? 'স্ক্রিনশট বড় করে দেখুন' : 'Click to Zoom Receipt' ?>">
                                                        <img src="<?= $imgSrc ?>" alt="Receipt" style="width: 42px; height: 42px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-medium); box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: transform 0.2s ease;">
                                                    </button>
                                                </td>
                                                <td style="padding: 10px 12px; font-size: 0.82rem;">
                                                    <?php if (($p['status'] ?? '') === 'Verified'): ?>
                                                        <span style="color: #15803d; background: #dcfce7; font-size: 0.75rem; font-weight: 800; padding: 3px 10px; border-radius: var(--radius-full); display: inline-flex; align-items: center; gap: 4px;">
                                                            ✓ <?= $isBn ? 'যাচাইকৃত' : 'Verified' ?>
                                                        </span>
                                                        <div style="font-size: 0.72rem; color: #166534; margin-top: 3px;">
                                                            <?= e($p['verified_by'] ?? ($isBn ? 'ফাইন্যান্স অফিসার' : 'Finance Officer')) ?>
                                                        </div>
                                                    <?php elseif (($p['status'] ?? '') === 'Rejected'): ?>
                                                        <span style="color: #991b1b; background: #fee2e2; font-size: 0.75rem; font-weight: 800; padding: 3px 10px; border-radius: var(--radius-full); display: inline-flex; align-items: center; gap: 4px;">
                                                            ✕ <?= $isBn ? 'বাতিলকৃত' : 'Rejected' ?>
                                                        </span>
                                                        <div style="font-size: 0.72rem; color: #991b1b; margin-top: 3px;">
                                                            <?= e($p['notes'] ?? '') ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <span style="color: #b45309; background: #fef3c7; font-size: 0.75rem; font-weight: 800; padding: 3px 10px; border-radius: var(--radius-full); display: inline-flex; align-items: center; gap: 4px;">
                                                            ⌛ <?= $isBn ? 'যাচাইয়ের অপেক্ষায়' : 'Pending Verification' ?>
                                                        </span>
                                                        <div style="font-size: 0.72rem; color: #b45309; margin-top: 3px;">
                                                            <?= $isBn ? 'ফাইন্যান্স অফিসার কর্তৃক পর্যালোচনায়' : 'Awaiting Finance Officer' ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

        <?php endif; ?>
    </div>
</section>

<!-- Receipt Zoom Lightbox Modal -->
<div id="receiptZoomModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.75); z-index: 9999; align-items: center; justify-content: center; padding: 20px; backdrop-filter: blur(4px);">
    <div style="background: #ffffff; border-radius: var(--radius-xl); max-width: 480px; width: 100%; padding: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4); border: 1px solid var(--border-medium); position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 10px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 1.2rem;">🧾</span>
                <strong id="receiptModalTitle" style="color: var(--primary-deep); font-size: 0.95rem; font-family: monospace;"></strong>
            </div>
            <button type="button" onclick="closeReceiptZoomModal()" style="background: none; border: none; font-size: 1.4rem; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>
        
        <div style="text-align: center; background: #0f172a; border-radius: var(--radius-lg); padding: 12px; overflow: hidden;">
            <img id="receiptModalImg" src="" alt="Payment Receipt" style="max-height: 480px; max-width: 100%; object-fit: contain; border-radius: 8px;">
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 14px; font-size: 0.8rem; color: var(--text-muted);">
            <span><?= $isBn ? 'ফাইন্যান্স অফিসার ভেরিফিকেশন রেকর্ড' : 'Finance Officer Audit Record' ?></span>
            <button type="button" onclick="closeReceiptZoomModal()" class="btn btn-sm btn-secondary">
                <?= $isBn ? 'বন্ধ করুন' : 'Close' ?>
            </button>
        </div>
    </div>
</div>

<script>
function openReceiptZoomModal(src, id) {
    document.getElementById('receiptModalImg').src = src;
    document.getElementById('receiptModalTitle').textContent = 'Trx: ' + id;
    const modal = document.getElementById('receiptZoomModal');
    modal.style.display = 'flex';
}

function closeReceiptZoomModal() {
    const modal = document.getElementById('receiptZoomModal');
    modal.style.display = 'none';
}
</script>
