<?php
/**
 * SPS Admin Membership Administration Portal
 * Finance Officer Verification Desk, Super Admin & Admin Supervisory Monitoring Console
 */

$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';

$members = $members ?? [];
$stats = $stats ?? [];
$categories = $categories ?? [];
$plans = $plans ?? [];
$payments = $payments ?? [];
$pendingPayments = $pendingPayments ?? [];
$statusFilter = $statusFilter ?? 'all';
$categoryFilter = $categoryFilter ?? 'all';
$planFilter = $planFilter ?? 'all';
$searchQuery = $searchQuery ?? '';
$currentUser = $currentUser ?? [];
$currentTab = $currentTab ?? 'directory';

$isFinanceOfficer = ($currentUser['role'] ?? '') === 'finance_officer';
$isSuperAdmin = ($currentUser['role'] ?? '') === 'super_admin';
$isAdmin = ($currentUser['role'] ?? '') === 'admin';
$isMonitoring = $isSuperAdmin || $isAdmin;

// Build lookup map for quick member association in payment ledger
$memberLookup = [];
foreach ($members as $m) {
    $memberLookup[$m['id']] = $m;
    $memberLookup[$m['member_code']] = $m;
}
?>

<div class="admin-page-header">
    <div class="header-left">
        <h2 class="admin-page-title">
            <span>🪪</span>
            <span><?= $isBn ? 'সদস্য প্রশাসন ও মেম্বারশিপ পোর্টাল' : 'Membership Administration Portal' ?></span>
        </h2>
        <p class="admin-page-desc">
            <?= $isBn 
                ? 'এসপিএস সনাতনী পরিবারের সকল শিক্ষার্থী ও উপার্জনশীল সদস্যদের আবেদন, ফাইন্যান্স অফিসার কর্তৃক পেমেন্ট ভেরিফিকেশন ও স্ট্যাটাস ব্যবস্থাপনা।' 
                : 'Manage student & earning member applications, Finance Officer payment verification desk, and digital memberships.' ?>
        </p>
    </div>
    <div class="header-right" style="display:flex; gap:10px;">
        <a href="<?= url('/membership', $currentLocale) ?>" target="_blank" class="btn btn-sm btn-ghost" style="border:1px solid var(--border-medium);">
            🌐 <?= $isBn ? 'পাবলিক মেম্বারশিপ সাইট ↗' : 'Public Membership Site ↗' ?>
        </a>
        <a href="<?= url('/membership/apply', $currentLocale) ?>" target="_blank" class="btn btn-sm btn-primary">
            + <?= $isBn ? 'নতুন আবেদন ফরম' : 'New Application Form' ?>
        </a>
    </div>
</div>

<!-- Authority Governance Banner -->
<div class="moderation-auth-banner" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; background:#f8fafc; border:1px solid var(--border-medium); border-radius:var(--radius-lg); padding:12px 18px; margin-bottom:20px;">
    <div style="display:flex; align-items:center; gap:10px;">
        <div style="font-size:1.4rem;">
            <?= $isFinanceOfficer ? '🪙' : '🛡️' ?>
        </div>
        <div>
            <div style="font-size:0.88rem; font-weight:700; color:var(--primary-deep);">
                <?= $isFinanceOfficer 
                    ? ($isBn ? 'ফাইন্যান্স অফিসার নিয়ন্ত্রণ ডেস্ক (কোষাধ্যক্ষ এক্তিয়ার):' : 'Finance Officer Control Desk (Treasurer Authority):')
                    : ($isBn ? 'প্রশাসনিক ও তত্ত্বাবধান এক্তিয়ার (Governance Desk):' : 'Governance & Supervisory Desk:') ?>
            </div>
            <div style="font-size:0.78rem; color:var(--text-muted);">
                <?= $isFinanceOfficer 
                    ? ($isBn ? 'পেমেন্ট ও TrxID ভেরিফিকেশনের পূর্ণ ক্ষমতা কোষাধ্যক্ষ / ফাইন্যান্স অফিসারের হাতে ন্যস্ত।' : 'Exclusive authority over member payment audit and verification.')
                    : ($isBn ? 'পেমেন্ট ভেরিফিকেশন কোষাধ্যক্ষের দায়িত্বে; সুপার-অ্যাডমিন ও অ্যাডমিনগণ সার্বিক তত্ত্বাবধান করছেন।' : 'Super Admin & Admins supervise while Treasurer verifies member payments.') ?>
            </div>
        </div>
    </div>
    <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-full); padding:4px 12px; font-size:0.82rem; font-weight:700; color:var(--primary-deep);">
        <?= $isBn ? 'লগইনকৃত অ্যাডমিন:' : 'Logged Admin:' ?> <?= e($isBn ? ($currentUser['name_bn'] ?? '') : ($currentUser['name_en'] ?? '')) ?> (<?= e($currentUser['role'] ?? 'Admin') ?>)
    </div>
</div>

<!-- Flash Alerts -->
<?php if ($success = \App\Core\Session::getFlash('success')): ?>
    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <span style="font-size:1.2rem;">✓</span>
        <div><?= e($success) ?></div>
    </div>
<?php endif; ?>

<?php if ($warning = \App\Core\Session::getFlash('warning')): ?>
    <div style="background: #fffbeb; border: 1px solid #fde68a; color: #92400e; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <span style="font-size:1.2rem;">⚠️</span>
        <div><?= e($warning) ?></div>
    </div>
<?php endif; ?>

<?php if ($error = \App\Core\Session::getFlash('error')): ?>
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <span style="font-size:1.2rem;">✕</span>
        <div><?= e($error) ?></div>
    </div>
<?php endif; ?>

<!-- Top 6 Statistical Dashboard Counters -->
<div class="admin-metrics-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:14px; margin-bottom:24px;">
    
    <div style="background:#fff; border:1px solid var(--border-medium); border-radius:8px; padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <div style="font-size:0.78rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">
            👥 <?= $isBn ? 'মোট সদস্য' : 'Total Members' ?>
        </div>
        <div style="font-size:1.85rem; font-weight:800; color:var(--primary-deep); margin-top:4px;">
            <?= e($isBn ? \App\Core\I18n::formatNumber((string)($stats['total_members'] ?? 0)) : ($stats['total_members'] ?? 0)) ?>
        </div>
        <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
            <?= $isBn ? 'নিবন্ধিত ডাটাবেজ রেকর্ড' : 'All recorded members' ?>
        </div>
    </div>

    <div style="background:#fff; border:1px solid #bbf7d0; border-radius:8px; padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <div style="font-size:0.78rem; font-weight:700; color:#16a34a; text-transform:uppercase;">
            ● <?= $isBn ? 'সক্রিয় সদস্য' : 'Active Members' ?>
        </div>
        <div style="font-size:1.85rem; font-weight:800; color:#15803d; margin-top:4px;">
            <?= e($isBn ? \App\Core\I18n::formatNumber((string)($stats['active_members'] ?? 0)) : ($stats['active_members'] ?? 0)) ?>
        </div>
        <div style="font-size:0.75rem; color:#166534; margin-top:2px;">
            <?= $isBn ? 'যথাযথ ভেরিফাইড সদস্যপদ' : 'Valid & in good standing' ?>
        </div>
    </div>

    <!-- Prominent Finance Verification Alert Card -->
    <a href="<?= url('/admin/members?tab=payments', $currentLocale) ?>" style="text-decoration:none; display:block; background:#fff; border:2px solid <?= count($pendingPayments) > 0 ? '#f97316' : '#fed7aa' ?>; border-radius:8px; padding:16px; box-shadow:0 2px 6px rgba(249, 115, 22, 0.12); transition:all 0.2s ease;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div style="font-size:0.78rem; font-weight:800; color:#c2410c; text-transform:uppercase;">
                🪙 <?= $isBn ? 'অপেক্ষমাণ পেমেন্ট' : 'Pending Payments' ?>
            </div>
            <?php if (count($pendingPayments) > 0): ?>
                <span style="font-size:0.7rem; background:#ffedd5; color:#c2410c; padding:2px 8px; border-radius:var(--radius-full); font-weight:800;">
                    <?= $isBn ? 'ভেরিফিকেশন প্রয়োজন' : 'Action Required' ?>
                </span>
            <?php endif; ?>
        </div>
        <div style="font-size:1.85rem; font-weight:800; color:#c2410c; margin-top:4px;">
            <?= e($isBn ? \App\Core\I18n::formatNumber((string)count($pendingPayments)) : count($pendingPayments)) ?>
        </div>
        <div style="font-size:0.75rem; color:#9a3412; margin-top:2px;">
            <?= $isBn ? 'ফাইন্যান্স অফিসার কর্তৃক যাচাই' : 'Finance Officer Queue' ?> ➔
        </div>
    </a>

    <div style="background:#fff; border:1px solid #bbf7d0; border-radius:8px; padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <div style="font-size:0.78rem; font-weight:700; color:#166534; text-transform:uppercase;">
            💰 <?= $isBn ? 'আদায়কৃত চাঁদা' : 'Collected Fees' ?>
        </div>
        <div style="font-size:1.85rem; font-weight:800; color:#14532d; margin-top:4px;">
            ৳<?= number_format((float)($stats['total_collected'] ?? 0)) ?>
        </div>
        <div style="font-size:0.75rem; color:#166534; margin-top:2px;">
            <?= $isBn ? 'যাচাইকৃত পেমেন্ট সমষ্টি' : 'Total verified ledger' ?>
        </div>
    </div>

    <div style="background:#fff; border:1px solid #bae6fd; border-radius:8px; padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <div style="font-size:0.78rem; font-weight:700; color:#0284c7; text-transform:uppercase;">
            🎓 <?= $isBn ? 'শিক্ষার্থী সদস্য' : 'Student Members' ?>
        </div>
        <div style="font-size:1.85rem; font-weight:800; color:#0369a1; margin-top:4px;">
            <?= e($isBn ? \App\Core\I18n::formatNumber((string)($stats['student_members'] ?? 0)) : ($stats['student_members'] ?? 0)) ?>
        </div>
        <div style="font-size:0.75rem; color:#075985; margin-top:2px;">
            <?= $isBn ? '৳৫০/মাসিক ছাত্র প্ল্যান' : 'Student category' ?>
        </div>
    </div>

    <div style="background:#fff; border:1px solid #fde68a; border-radius:8px; padding:16px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <div style="font-size:0.78rem; font-weight:700; color:#b45309; text-transform:uppercase;">
            👑 <?= $isBn ? 'বাৎসরিক ও আজীবন' : 'Yearly & Lifetime' ?>
        </div>
        <div style="font-size:1.85rem; font-weight:800; color:#b45309; margin-top:4px;">
            <?= e($isBn ? \App\Core\I18n::formatNumber((string)(($stats['yearly_members'] ?? 0) + ($stats['lifetime_members'] ?? 0))) : (($stats['yearly_members'] ?? 0) + ($stats['lifetime_members'] ?? 0))) ?>
        </div>
        <div style="font-size:0.75rem; color:#78350f; margin-top:2px;">
            বাৎসরিক: <?= $stats['yearly_members'] ?? 0 ?> | আজীবন: <?= $stats['lifetime_members'] ?? 0 ?>
        </div>
    </div>
</div>

<!-- Primary Navigation Tabs (Directory vs. Finance Desk vs. All Payments) -->
<div style="display:flex; gap:10px; margin-bottom:20px; border-bottom:2px solid var(--border-medium); padding-bottom:12px; flex-wrap:wrap;">
    
    <a href="<?= url('/admin/members?tab=directory', $currentLocale) ?>" 
       style="display:flex; align-items:center; gap:8px; padding:10px 20px; border-radius:var(--radius-lg); font-size:0.92rem; font-weight:800; text-decoration:none; transition:all 0.2s ease; <?= $currentTab === 'directory' ? 'background:var(--primary-deep); color:#ffffff; box-shadow:var(--shadow-sm);' : 'background:#ffffff; color:var(--text-secondary); border:1px solid var(--border-medium);' ?>">
        <span>👥</span>
        <span><?= $isBn ? 'সদস্য রেজিস্ট্রি ডিরেক্টরি' : 'Members Directory' ?></span>
        <span style="font-size:0.75rem; background:rgba(0,0,0,0.08); padding:2px 8px; border-radius:var(--radius-full);">
            <?= count($members) ?>
        </span>
    </a>

    <a href="<?= url('/admin/members?tab=payments', $currentLocale) ?>" 
       style="display:flex; align-items:center; gap:8px; padding:10px 20px; border-radius:var(--radius-lg); font-size:0.92rem; font-weight:800; text-decoration:none; transition:all 0.2s ease; <?= $currentTab === 'payments' ? 'background:#c2410c; color:#ffffff; box-shadow:0 4px 12px rgba(194, 65, 12, 0.25);' : 'background:#fff7ed; color:#c2410c; border:1px solid #fdba74;' ?>">
        <span>🪙</span>
        <span><?= $isBn ? 'পেমেন্ট ভেরিফিকেশন ডেস্ক (Finance Desk)' : 'Payment Verification Desk' ?></span>
        <?php if (count($pendingPayments) > 0): ?>
            <span style="font-size:0.75rem; background:#fee2e2; color:#991b1b; padding:2px 8px; border-radius:var(--radius-full); font-weight:900;">
                ⏳ <?= count($pendingPayments) ?> <?= $isBn ? 'টি অপেক্ষমাণ' : 'Pending' ?>
            </span>
        <?php else: ?>
            <span style="font-size:0.75rem; background:rgba(0,0,0,0.08); padding:2px 8px; border-radius:var(--radius-full);">
                0
            </span>
        <?php endif; ?>
    </a>

    <a href="<?= url('/admin/members?tab=all_payments', $currentLocale) ?>" 
       style="display:flex; align-items:center; gap:8px; padding:10px 20px; border-radius:var(--radius-lg); font-size:0.92rem; font-weight:800; text-decoration:none; transition:all 0.2s ease; <?= $currentTab === 'all_payments' ? 'background:#0369a1; color:#ffffff; box-shadow:var(--shadow-sm);' : 'background:#ffffff; color:var(--text-secondary); border:1px solid var(--border-medium);' ?>">
        <span>📜</span>
        <span><?= $isBn ? 'সকল পেমেন্ট ও চাঁদা খতিয়ান' : 'All Payments Ledger' ?></span>
        <span style="font-size:0.75rem; background:rgba(0,0,0,0.08); padding:2px 8px; border-radius:var(--radius-full);">
            <?= count($payments) ?>
        </span>
    </a>
</div>

<?php if ($currentTab === 'payments' || $currentTab === 'all_payments'): ?>

    <!-- ========================================================================= -->
    <!-- FINANCE OFFICER PAYMENT VERIFICATION DESK & SUPERVISORY MONITORING VIEW   -->
    <!-- ========================================================================= -->

    <!-- Role Context Banner -->
    <?php if ($isFinanceOfficer): ?>
        <div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 2px solid #86efac; border-radius: var(--radius-xl); padding: 18px 24px; margin-bottom: 24px; box-shadow: 0 4px 12px rgba(22, 101, 52, 0.08);">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 52px; height: 52px; border-radius: 12px; background: #166534; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #fff; box-shadow: 0 4px 10px rgba(22, 101, 52, 0.25);">
                        🪙
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 0.75rem; font-weight: 800; background: #166534; color: #fff; padding: 2px 10px; border-radius: 999px; text-transform: uppercase;">
                                Finance Officer Exclusive Desk
                            </span>
                            <h3 style="font-size: 1.15rem; font-weight: 800; color: #14532d; margin: 0;">
                                <?= $isBn ? 'কোষাধ্যক্ষ / ফাইন্যান্স অফিসার নিয়ন্ত্রণ ডেস্ক' : 'Treasurer / Finance Officer Verification Console' ?>
                            </h3>
                        </div>
                        <p style="color: #166534; font-size: 0.88rem; margin: 4px 0 0; max-width: 680px;">
                            <?= $isBn 
                                ? 'মেম্বারশিপের সকল পেমেন্ট (bKash/Nagad), প্রেরিত TrxID ও পেমেন্ট স্ক্রিনশট যাচাই করে মেম্বারশিপ সক্রিয় করা বা বাতিল করার প্রাথমিক দায়িত্ব আপনার একক তত্ত্বাবধানে পরিচালিত।' 
                                : 'You are the primary financial officer authorized to verify bKash/Nagad transactions, TrxIDs, and screenshots to activate memberships.' ?>
                        </p>
                    </div>
                </div>
                <div style="background: #ffffff; border: 1px solid #86efac; border-radius: 12px; padding: 10px 18px; text-align: right; box-shadow: 0 2px 6px rgba(0,0,0,0.04);">
                    <div style="font-size: 0.72rem; color: #166534; font-weight: 700; text-transform: uppercase;"><?= $isBn ? 'দায়িত্বপ্রাপ্ত কোষাধ্যক্ষ:' : 'Active Treasurer:' ?></div>
                    <div style="font-size: 1rem; font-weight: 800; color: #14532d;"><?= e($isBn ? ($currentUser['name_bn'] ?? '') : ($currentUser['name_en'] ?? '')) ?></div>
                    <div style="font-size: 0.75rem; color: #15803d; font-family: monospace; font-weight: 700;"><?= e($currentUser['designation_bn'] ?? 'কোষাধ্যক্ষ') ?> (finance_officer)</div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 2px solid #93c5fd; border-radius: var(--radius-xl); padding: 18px 24px; margin-bottom: 24px; box-shadow: 0 4px 12px rgba(30, 64, 175, 0.08);">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 52px; height: 52px; border-radius: 12px; background: #1e40af; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #fff; box-shadow: 0 4px 10px rgba(30, 64, 175, 0.25);">
                        🛡️
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 0.75rem; font-weight: 800; background: #1e40af; color: #fff; padding: 2px 10px; border-radius: 999px; text-transform: uppercase;">
                                Supervisory Monitoring Mode
                            </span>
                            <h3 style="font-size: 1.15rem; font-weight: 800; color: #1e3a8a; margin: 0;">
                                <?= $isBn ? 'সুপার-অ্যাডমিন ও অ্যাডমিন তত্ত্বাবধান ডেস্ক' : 'Super Admin & Admin Supervisory Monitoring' ?>
                            </h3>
                        </div>
                        <p style="color: #1e40af; font-size: 0.88rem; margin: 4px 0 0; max-width: 680px;">
                            <?= $isBn 
                                ? 'মেম্বারশিপ পেমেন্ট ভেরিফিকেশনের মূল দায়িত্ব ফাইন্যান্স অফিসার (কোষাধ্যক্ষ - জয় চক্রবর্তী)-এর হাতে ন্যস্ত। সুপার অ্যাডমিন (অনিক কুমার সাহা) ও অন্য ২ জন অ্যাডমিন (রবিন দে ও লিখন ঘোষ) হিসেবে আপনি ট্রানজেকশন কিউ ও অডিট লগ পর্যবেক্ষণ ও অডিট করছেন।' 
                                : 'Membership payment verification is managed primarily by the Finance Officer (Joy Chakraborty). Super Admin and the 2 Admins monitor the live queue and immutable audit trail.' ?>
                        </p>
                    </div>
                </div>
                <div style="background: #ffffff; border: 1px solid #93c5fd; border-radius: 12px; padding: 10px 18px; text-align: right; box-shadow: 0 2px 6px rgba(0,0,0,0.04);">
                    <div style="font-size: 0.72rem; color: #1e40af; font-weight: 700; text-transform: uppercase;"><?= $isBn ? 'তত্ত্বাবধানকারী কর্মকর্তা:' : 'Monitoring Officer:' ?></div>
                    <div style="font-size: 1rem; font-weight: 800; color: #1e3a8a;"><?= e($isBn ? ($currentUser['name_bn'] ?? '') : ($currentUser['name_en'] ?? '')) ?></div>
                    <div style="font-size: 0.75rem; color: #2563eb; font-family: monospace; font-weight: 700;"><?= e($currentUser['role'] ?? '') ?></div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Target payments list depending on tab -->
    <?php 
        $activePaymentList = ($currentTab === 'payments') ? $pendingPayments : $payments;
    ?>

    <div style="background:#fff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); overflow:hidden; box-shadow:var(--shadow-sm); margin-bottom:24px;">
        <div style="padding:16px 20px; border-bottom:1px solid var(--border-subtle); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:1.2rem;">🪙</span>
                <h3 style="font-size:1.05rem; font-weight:800; color:var(--primary-deep); margin:0;">
                    <?= $currentTab === 'payments' 
                        ? ($isBn ? 'অপেক্ষমাণ পেমেন্ট ভেরিফিকেশন কিউ (Pending Verification)' : 'Pending Payment Verification Queue')
                        : ($isBn ? 'এসপিএস সকল পেমেন্ট ও চাঁদা খতিয়ান (All Financial Transactions)' : 'All Membership Payment Ledger') ?>
                </h3>
            </div>
            <span style="font-size:0.82rem; font-weight:700; color:var(--text-muted); background:var(--bg-surface); padding:4px 12px; border-radius:var(--radius-full);">
                <?= $isBn ? 'রেকর্ড সংখ্যা:' : 'Total Records:' ?> <?= count($activePaymentList) ?>
            </span>
        </div>

        <?php if (empty($activePaymentList)): ?>
            <div style="text-align:center; padding:var(--space-3xl); color:var(--text-muted);">
                <div style="font-size:2.8rem; margin-bottom:8px;">🎉</div>
                <div style="font-size:1.15rem; font-weight:800; color:var(--primary-deep);">
                    <?= $currentTab === 'payments' 
                        ? ($isBn ? 'বর্তমানে কোনো অপেক্ষমাণ পেমেন্ট নেই!' : 'No pending payments awaiting verification!')
                        : ($isBn ? 'কোনো পেমেন্ট রেকর্ড পাওয়া যায়নি।' : 'No payment records found.') ?>
                </div>
                <p style="font-size:0.85rem; margin-top:4px;">
                    <?= $isBn ? 'সকল সদস্যের পেমেন্ট যাচাইকৃত ও হালনাগাদ রয়েছে।' : 'All membership transactions have been processed and up to date.' ?>
                </p>
                <?php if ($currentTab === 'payments'): ?>
                    <a href="<?= url('/admin/members?tab=all_payments', $currentLocale) ?>" class="btn btn-sm btn-ghost" style="margin-top:10px; border:1px solid var(--border-medium);">
                        📜 <?= $isBn ? 'সকল পেমেন্ট হিস্টোরি দেখুন' : 'View Full Payment History' ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:0.88rem;">
                    <thead>
                        <tr style="background:var(--bg-surface); border-bottom:2px solid var(--border-medium); text-align:left; font-size:0.78rem; text-transform:uppercase; color:var(--text-muted);">
                            <th style="padding:12px 14px;"><?= $isBn ? 'সদস্য পরিচিতি' : 'Member Details' ?></th>
                            <th style="padding:12px 14px;"><?= $isBn ? 'ট্রানজেকশন ও TrxID' : 'TxID & TrxID' ?></th>
                            <th style="padding:12px 14px;"><?= $isBn ? 'প্ল্যান ও পরিমাণ' : 'Plan & Amount' ?></th>
                            <th style="padding:12px 14px;"><?= $isBn ? 'মাধ্যম ও প্রেরক' : 'Method & Sender' ?></th>
                            <th style="padding:12px 14px; text-align:center;"><?= $isBn ? 'সেন্ট মানি স্ক্রিনশট' : 'Receipt SS' ?></th>
                            <th style="padding:12px 14px;"><?= $isBn ? 'স্ট্যাটাস' : 'Status' ?></th>
                            <th style="padding:12px 14px; text-align:right;"><?= $isBn ? 'অডিট অ্যাকশন' : 'Audit Action' ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activePaymentList as $p): ?>
                            <?php 
                                $mem = $memberLookup[$p['member_id']] ?? $memberLookup[$p['member_code']] ?? null;
                                $screenshot = $p['payment_screenshot'] ?? '';
                                $imgSrc = !empty($screenshot) ? (str_starts_with($screenshot, 'http') ? $screenshot : asset($screenshot)) : asset('assets/images/payments/bkash-success-sample.svg');
                            ?>
                            <tr style="border-bottom:1px solid var(--border-subtle); vertical-align:middle; transition:background 0.15s ease;">
                                
                                <!-- Member Details -->
                                <td style="padding:12px 14px;">
                                    <div style="font-weight:800; color:var(--primary-deep); font-size:0.95rem;">
                                        <?= e($mem['name_bn'] ?? $p['member_code']) ?>
                                    </div>
                                    <div style="display:flex; align-items:center; gap:6px; margin-top:2px;">
                                        <span style="font-family:monospace; font-weight:800; color:#0284c7; background:#e0f2fe; padding:1px 6px; border-radius:4px; font-size:0.78rem;">
                                            <?= e($p['member_code']) ?>
                                        </span>
                                        <?php if ($mem): ?>
                                            <span style="font-size:0.72rem; color:var(--text-muted);">
                                                (<?= $mem['category_id'] === 'STUDENT' ? '🎓 ছাত্র' : '💼 উপার্জনশীল' ?>)
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($mem): ?>
                                        <div style="font-size:0.76rem; color:var(--text-muted); margin-top:2px;">
                                            <?= e($mem['phone'] ?? '') ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Transaction IDs -->
                                <td style="padding:12px 14px;">
                                    <div style="font-family:monospace; font-weight:700; color:#0284c7; font-size:0.82rem;">
                                        <?= e($p['transaction_id'] ?? '') ?>
                                    </div>
                                    
                                    <!-- Provider TrxID with Quick Copy -->
                                    <div style="display:flex; align-items:center; gap:6px; margin-top:4px;">
                                        <span style="font-size:0.72rem; color:var(--text-muted); font-weight:700;">TrxID:</span>
                                        <span id="trx_text_<?= e($p['id']) ?>" style="font-family:monospace; font-weight:900; color:#b45309; background:#fef3c7; border:1px solid #fde68a; padding:1px 6px; border-radius:4px; font-size:0.82rem;">
                                            <?= e($p['trx_id'] ?? 'N/A') ?>
                                        </span>
                                        <?php if (!empty($p['trx_id'])): ?>
                                            <button type="button" onclick="copyTrxText('<?= e($p['trx_id']) ?>', this)" style="background:none; border:none; cursor:pointer; font-size:0.8rem; color:#64748b;" title="<?= $isBn ? 'কপি করুন' : 'Copy TrxID' ?>">
                                                📋
                                            </button>
                                        <?php endif; ?>
                                    </div>

                                    <div style="font-size:0.74rem; color:var(--text-muted); margin-top:2px;">
                                        <?= e($p['payment_date'] ?? $p['created_at'] ?? '') ?>
                                    </div>
                                </td>

                                <!-- Plan & Amount -->
                                <td style="padding:12px 14px;">
                                    <div>
                                        <span style="font-weight:800; color:var(--primary-deep); font-size:1.05rem;">
                                            ৳<?= number_format((float)($p['amount'] ?? 0)) ?>
                                        </span>
                                    </div>
                                    <span style="display:inline-block; margin-top:2px; background:#f1f5f9; padding:1px 6px; border-radius:var(--radius-sm); font-size:0.75rem; font-weight:700; color:var(--text-secondary);">
                                        <?= e($p['payment_type'] ?? '') ?>
                                    </span>
                                </td>

                                <!-- Method & Sender -->
                                <td style="padding:12px 14px; font-size:0.84rem;">
                                    <?php
                                        $pm = strtolower($p['payment_method'] ?? 'bkash');
                                        $methodBg = '#fdf2f8'; $methodCol = '#be185d';
                                        if (str_contains($pm, 'nagad')) { $methodBg = '#fff7ed'; $methodCol = '#c2410c'; }
                                        elseif (str_contains($pm, 'rocket')) { $methodBg = '#faf5ff'; $methodCol = '#7e22ce'; }
                                        elseif (str_contains($pm, 'bank')) { $methodBg = '#eff6ff'; $methodCol = '#1d4ed8'; }
                                    ?>
                                    <span style="background:<?= $methodBg ?>; color:<?= $methodCol ?>; padding:2px 8px; border-radius:var(--radius-full); font-size:0.75rem; font-weight:800; border:1px solid currentColor;">
                                        <?= e($p['payment_method'] ?? 'bKash') ?>
                                    </span>
                                    <div style="font-family:monospace; font-weight:700; color:var(--text-primary); margin-top:4px;">
                                        <?= e($p['sender_number'] ?? 'N/A') ?>
                                    </div>
                                    <?php if (!empty($p['sender_name'])): ?>
                                        <div style="font-size:0.74rem; color:var(--text-muted); margin-top:2px;">
                                            👤 <?= e($p['sender_name']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($p['payment_reference'])): ?>
                                        <div style="font-size:0.72rem; color:#0369a1; margin-top:1px;">
                                            🏷️ <?= e($p['payment_reference']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Screenshot Thumbnail -->
                                <td style="padding:12px 14px; text-align:center;">
                                    <button type="button" onclick="openAdminScreenshotModal('<?= $imgSrc ?>', '<?= e($p['trx_id'] ?? $p['transaction_id']) ?>', '<?= e($mem['name_bn'] ?? $p['member_code']) ?>', '৳<?= number_format((float)$p['amount']) ?>', '<?= e($p['sender_number'] ?? '') ?>', '<?= e($p['id']) ?>', '<?= e($p['status']) ?>', '<?= e($p['sender_name'] ?? '') ?>', '<?= e($p['payment_time'] ?? '') ?>', '<?= e($p['payment_reference'] ?? '') ?>')" style="background:none; border:none; cursor:pointer; padding:0; position:relative;" title="<?= $isBn ? 'স্ক্রিনশট ও পেমেন্ট বিস্তারিত নিরীক্ষা করুন' : 'Click to inspect screenshot & payment details' ?>">
                                        <img src="<?= $imgSrc ?>" alt="Receipt" style="width:52px; height:52px; object-fit:cover; border-radius:8px; border:2px solid var(--border-medium); box-shadow:0 2px 5px rgba(0,0,0,0.1); transition:transform 0.2s ease;">
                                        <div style="font-size:0.68rem; color:#0284c7; font-weight:700; margin-top:2px;">🔍 <?= $isBn ? 'জুম' : 'Zoom' ?></div>
                                    </button>
                                </td>

                                <!-- Status -->
                                <td style="padding:12px 14px; font-size:0.82rem;">
                                    <?php if (($p['status'] ?? '') === 'Verified'): ?>
                                        <span style="color:#15803d; background:#dcfce7; font-size:0.75rem; font-weight:800; padding:3px 10px; border-radius:var(--radius-full); display:inline-flex; align-items:center; gap:4px; border:1px solid #86efac;">
                                            ✓ <?= $isBn ? 'যাচাইকৃত (Verified)' : 'Verified' ?>
                                        </span>
                                        <div style="font-size:0.72rem; color:#166534; margin-top:3px; font-weight:600;">
                                            <?= e($p['verified_by'] ?? ($isBn ? 'ফাইন্যান্স অফিসার' : 'Finance Officer')) ?>
                                        </div>
                                    <?php elseif (($p['status'] ?? '') === 'Rejected'): ?>
                                        <span style="color:#991b1b; background:#fee2e2; font-size:0.75rem; font-weight:800; padding:3px 10px; border-radius:var(--radius-full); display:inline-flex; align-items:center; gap:4px; border:1px solid #fca5a5;">
                                            ✕ <?= $isBn ? 'বাতিল (Rejected)' : 'Rejected' ?>
                                        </span>
                                        <div style="font-size:0.72rem; color:#991b1b; margin-top:3px;">
                                            <?= e($p['notes'] ?? '') ?>
                                        </div>
                                    <?php else: ?>
                                        <span style="color:#c2410c; background:#fff7ed; font-size:0.75rem; font-weight:800; padding:3px 10px; border-radius:var(--radius-full); display:inline-flex; align-items:center; gap:4px; border:1px solid #fed7aa;">
                                            ⏳ <?= $isBn ? 'ভেরিফিকেশন অপেক্ষমাণ' : 'Pending Review' ?>
                                        </span>
                                        <div style="font-size:0.72rem; color:#c2410c; margin-top:3px;">
                                            <?= $isBn ? 'ফাইন্যান্স অফিসার অডিট প্রয়োজন' : 'Action needed' ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Audit Actions -->
                                <td style="padding:12px 14px; text-align:right;">
                                    <div style="display:flex; justify-content:flex-end; gap:6px; flex-wrap:wrap;">
                                        
                                        <?php if (($p['status'] ?? '') === 'Pending'): ?>
                                            <?php if ($isFinanceOfficer): ?>
                                                <!-- Verify Button (Triggers membership activation) -->
                                                <form action="<?= url('/admin/members/payment/verify/' . e($p['id']), $currentLocale) ?>" method="POST" style="margin:0;" onsubmit="return confirm('<?= $isBn ? 'পেমেন্ট অনুমোদন নিশ্চিতকরণ: আপনি কি নিশ্চিত যে এই সদস্যপদ ফি/পেমেন্ট অ্যাকাউন্টে জমা হয়েছে এবং যাচাই সম্পন্ন হয়েছে?' : 'Confirm Payment Approval: Are you sure this payment has been received and verified?' ?>');">
                                                    <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>
                                                    <button type="submit" class="btn btn-sm btn-primary" style="background:#15803d; border-color:#15803d; padding:4px 10px; font-size:0.75rem; font-weight:800;" title="<?= $isBn ? 'TrxID ও রসিদ যাচাইপূর্বক মেম্বারশিপ সক্রিয় করুন' : 'Verify payment and activate member' ?>">
                                                        ✓ <?= $isBn ? 'ভেরিফাই করুন' : 'Verify' ?>
                                                    </button>
                                                </form>

                                                <!-- Reject Button -->
                                                <button type="button" onclick="openPaymentRejectModal('<?= e($p['id']) ?>', '<?= e($p['trx_id'] ?? '') ?>', '<?= e($mem['name_bn'] ?? $p['member_code']) ?>')" class="btn btn-sm btn-danger" style="padding:4px 10px; font-size:0.75rem;" title="<?= $isBn ? 'অসঙ্গতি হেতু পেমেন্ট বাতিল করুন' : 'Reject payment' ?>">
                                                    ✕ <?= $isBn ? 'বাতিল' : 'Reject' ?>
                                                </button>
                                            <?php else: ?>
                                                <!-- Supervisory monitoring mode for Super Admin & other 2 Admins -->
                                                <span style="font-size:0.74rem; font-weight:700; color:#1e40af; background:#eff6ff; padding:3px 8px; border-radius:var(--radius-sm); border:1px solid #bfdbfe; display:inline-flex; align-items:center; gap:4px;" title="<?= $isBn ? 'সুপার অ্যাডমিন ও অন্য ২ জন অ্যাডমিন শুধুমাত্র মনিটর করতে পারেন' : 'Monitoring mode' ?>">
                                                    🛡️ <?= $isBn ? 'তত্ত্বাবধান' : 'Monitoring' ?>
                                                </span>
                                                <button type="button" onclick="openAdminScreenshotModal('<?= $imgSrc ?>', '<?= e($p['trx_id'] ?? $p['transaction_id']) ?>', '<?= e($mem['name_bn'] ?? $p['member_code']) ?>', '৳<?= number_format((float)$p['amount']) ?>', '<?= e($p['sender_number'] ?? '') ?>', '<?= e($p['id']) ?>', '<?= e($p['status']) ?>', '<?= e($p['sender_name'] ?? '') ?>', '<?= e($p['payment_time'] ?? '') ?>', '<?= e($p['payment_reference'] ?? '') ?>')" class="btn btn-sm btn-ghost" style="border:1px solid var(--border-medium); padding:4px 8px; font-size:0.75rem;" title="<?= $isBn ? 'পেমেন্ট ও TrxID বিস্তারিত নিরীক্ষা করুন' : 'Inspect Details' ?>">
                                                    🔍 <?= $isBn ? 'নিরীক্ষা' : 'Inspect' ?>
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <!-- Official Signed Invoice shortcut -->
                                        <a href="<?= url('/invoice/' . e($p['transaction_id']), $currentLocale) ?>" target="_blank" class="btn btn-sm btn-ghost" style="border:1px solid #fde68a; background:#fffbeb; color:#b45309; font-weight:800; padding:4px 8px; font-size:0.75rem;" title="<?= $isBn ? 'অর্থ সম্পাদকের স্বাক্ষরযুক্ত অফিসিয়াল মানি রসিদ দেখুন / প্রিন্ট করুন' : 'View / Print Official Signed Money Receipt' ?>">
                                            📄 <?= $isBn ? 'রসিদ' : 'Invoice' ?>
                                        </a>

                                        <!-- View Member Dashboard shortcut -->
                                        <a href="<?= url('/membership/dashboard?as=' . e($p['member_code']), $currentLocale) ?>" target="_blank" class="btn btn-sm btn-ghost" style="border:1px solid var(--border-medium); padding:4px 8px; font-size:0.75rem;" title="<?= $isBn ? 'সদস্যের ডিজিটাল কার্ড ও প্রোফাইল দেখুন' : 'View Member Dashboard' ?>">
                                            👁️
                                        </a>

                                    </div>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

<?php else: ?>

    <!-- ========================================================================= -->
    <!-- MEMBER DIRECTORY & REGISTRY VIEW                                         -->
    <!-- ========================================================================= -->

    <!-- Search, Tabs & Filter Bar -->
    <div style="background:#fff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); padding:16px; margin-bottom:20px; box-shadow:var(--shadow-sm);">
        
        <!-- Filter Tabs -->
        <div style="display:flex; gap:6px; flex-wrap:wrap; border-bottom:1px solid var(--border-subtle); padding-bottom:12px; margin-bottom:14px;">
            <?php
                $tabs = [
                    'all' => ['label_bn' => 'সকল সদস্য', 'label_en' => 'All Members'],
                    'Pending' => ['label_bn' => 'অপেক্ষমাণ আবেদন', 'label_en' => 'Pending Review'],
                    'Active' => ['label_bn' => 'সক্রিয় সদস্য', 'label_en' => 'Active Members'],
                    'Monthly' => ['label_bn' => 'মাসিক প্ল্যান', 'label_en' => 'Monthly Plans'],
                    'Yearly' => ['label_bn' => 'বাৎসরিক প্ল্যান', 'label_en' => 'Yearly Plans'],
                    'Lifetime' => ['label_bn' => 'আজীবন সদস্য', 'label_en' => 'Lifetime Members'],
                    'Payment Due' => ['label_bn' => 'পেমেন্ট বকেয়া', 'label_en' => 'Payment Due'],
                    'Suspended' => ['label_bn' => 'স্থগিতকৃত', 'label_en' => 'Suspended'],
                ];
            ?>
            <?php foreach ($tabs as $key => $t): ?>
                <a href="<?= url('/admin/members?tab=directory&status=' . $key . ($categoryFilter !== 'all' ? '&category=' . $categoryFilter : '') . ($searchQuery ? '&q=' . urlencode($searchQuery) : ''), $currentLocale) ?>" 
                   style="padding:6px 14px; border-radius:var(--radius-full); font-size:0.82rem; font-weight:700; text-decoration:none; transition:all 0.2s ease; <?= $statusFilter === $key ? 'background:var(--primary-deep); color:#fff;' : 'background:var(--bg-surface); color:var(--text-secondary); border:1px solid var(--border-medium);' ?>">
                    <?= $isBn ? $t['label_bn'] : $t['label_en'] ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Search & Select Filters -->
        <form method="GET" action="<?= url('/admin/members', $currentLocale) ?>" style="display:grid; grid-template-columns: 1fr 180px 180px auto; gap:10px; margin:0;">
            <input type="hidden" name="tab" value="directory">
            <input type="hidden" name="status" value="<?= e($statusFilter) ?>">
            
            <div>
                <input type="text" name="q" value="<?= e($searchQuery) ?>" placeholder="<?= $isBn ? 'নাম, মেম্বার আইডি (e.g. SPS-000872), মোবাইল বা ইমেইল দিয়ে খুঁজুন...' : 'Search by name, member ID, phone, or email...' ?>" style="width:100%; padding:8px 14px; border:1px solid var(--border-medium); border-radius:var(--radius-md); font-size:0.88rem;">
            </div>

            <div>
                <select name="category" onchange="this.form.submit()" style="width:100%; padding:8px 12px; border:1px solid var(--border-medium); border-radius:var(--radius-md); font-size:0.88rem;">
                    <option value="all"><?= $isBn ? 'সকল ক্যাটাগরি' : 'All Categories' ?></option>
                    <option value="STUDENT" <?= $categoryFilter === 'STUDENT' ? 'selected' : '' ?>><?= $isBn ? 'শিক্ষার্থী (Student)' : 'Student' ?></option>
                    <option value="EARNING" <?= $categoryFilter === 'EARNING' ? 'selected' : '' ?>><?= $isBn ? 'উপার্জনশীল (Earning)' : 'Earning' ?></option>
                </select>
            </div>

            <div>
                <select name="plan" onchange="this.form.submit()" style="width:100%; padding:8px 12px; border:1px solid var(--border-medium); border-radius:var(--radius-md); font-size:0.88rem;">
                    <option value="all"><?= $isBn ? 'সকল প্ল্যান' : 'All Plans' ?></option>
                    <option value="STUDENT_MONTHLY" <?= $planFilter === 'STUDENT_MONTHLY' ? 'selected' : '' ?>>Student Monthly</option>
                    <option value="EARNING_MONTHLY" <?= $planFilter === 'EARNING_MONTHLY' ? 'selected' : '' ?>>Earning Monthly</option>
                    <option value="YEARLY" <?= $planFilter === 'YEARLY' ? 'selected' : '' ?>>Yearly (৳1,000)</option>
                    <option value="LIFETIME" <?= $planFilter === 'LIFETIME' ? 'selected' : '' ?>>Lifetime (৳10,000)</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn btn-secondary btn-sm" style="height:100%; padding:0 16px;">
                    🔍 <?= $isBn ? 'অনুসন্ধান' : 'Search' ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Members Table -->
    <div style="background:#fff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); overflow:hidden; box-shadow:var(--shadow-sm);">
        <?php if (empty($members)): ?>
            <div style="text-align:center; padding:var(--space-3xl); color:var(--text-muted);">
                <div style="font-size:2.5rem; margin-bottom:8px;">🔍</div>
                <div style="font-size:1.1rem; font-weight:700; color:var(--primary-deep);"><?= $isBn ? 'কোনো সদস্য রেকর্ড পাওয়া যায়নি' : 'No Members Match Criteria' ?></div>
                <div style="font-size:0.85rem; margin-top:4px;"><?= $isBn ? 'অনুগ্রহ করে ফিল্টার পরিবর্তন বা রিসেট করুন।' : 'Try resetting your filter parameters.' ?></div>
                <a href="<?= url('/admin/members?tab=directory', $currentLocale) ?>" class="btn btn-sm btn-ghost" style="margin-top:12px; border:1px solid var(--border-medium);"><?= $isBn ? 'ফিল্টার রিসেট' : 'Reset Filter' ?></a>
            </div>
        <?php else: ?>
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:0.88rem;">
                    <thead>
                        <tr style="background:var(--bg-surface); border-bottom:2px solid var(--border-medium); text-align:left; font-size:0.78rem; text-transform:uppercase; color:var(--text-muted);">
                            <th style="padding:12px 14px;"><?= $isBn ? 'সদস্য পরিচিতি (ID & Name)' : 'Member ID & Name' ?></th>
                            <th style="padding:12px 14px;"><?= $isBn ? 'ক্যাটাগরি ও প্ল্যান' : 'Category & Plan' ?></th>
                            <th style="padding:12px 14px;"><?= $isBn ? 'যোগাযোগ' : 'Contact Details' ?></th>
                            <th style="padding:12px 14px;"><?= $isBn ? 'স্ট্যাটাস' : 'Status' ?></th>
                            <th style="padding:12px 14px;"><?= $isBn ? 'মেয়াদ' : 'Validity' ?></th>
                            <th style="padding:12px 14px; text-align:right;"><?= $isBn ? 'অ্যাডমিন অ্যাকশন' : 'Admin Actions' ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $m): ?>
                            <tr style="border-bottom:1px solid var(--border-subtle); transition:background 0.15s ease;">
                                
                                <!-- ID & Name -->
                                <td style="padding:12px 14px;">
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <div style="width:36px; height:36px; border-radius:50%; background:#f1f5f9; display:flex; align-items:center; justify-content:center; font-size:1.1rem; border:1px solid var(--border-medium);">
                                            <?= $m['category_id'] === 'STUDENT' ? '🎓' : '💼' ?>
                                        </div>
                                        <div>
                                            <div style="font-weight:800; color:var(--primary-deep); font-size:0.95rem;">
                                                <?= e($m['name_bn']) ?>
                                            </div>
                                            <div style="font-size:0.78rem; color:var(--text-muted);">
                                                <?= e($m['name_en']) ?>
                                            </div>
                                            <div style="display:flex; align-items:center; gap:6px; margin-top:2px;">
                                                <span style="font-family:monospace; font-size:0.8rem; font-weight:800; color:#0284c7; background:#e0f2fe; padding:1px 6px; border-radius:4px;">
                                                    <?= e($m['member_code']) ?>
                                                </span>
                                                <a href="<?= url('/membership/verify?code=' . e($m['member_code']), $currentLocale) ?>" target="_blank" style="font-size:0.72rem; color:#64748b; text-decoration:none;" title="<?= $isBn ? 'ডিজিটাল কার্ড কিউআর যাচাই' : 'Live Card Verify' ?>">
                                                    🪪 QR
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Category & Plan -->
                                <td style="padding:12px 14px;">
                                    <div>
                                        <span style="display:inline-block; padding:2px 8px; border-radius:var(--radius-full); font-size:0.75rem; font-weight:700; <?= $m['category_id'] === 'STUDENT' ? 'background:#e0f2fe; color:#0284c7;' : 'background:#ffedd5; color:#c2410c;' ?>">
                                            <?= $m['category_id'] === 'STUDENT' ? '🎓 শিক্ষার্থী' : '💼 উপার্জনশীল' ?>
                                        </span>
                                    </div>
                                    <div style="font-size:0.8rem; color:var(--text-secondary); font-weight:600; margin-top:4px;">
                                        <?= e($m['plan_id']) ?>
                                    </div>
                                    <?php if (!empty($m['is_lifetime'])): ?>
                                        <span style="font-size:0.7rem; color:#b45309; font-weight:800;">👑 LIFETIME</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Contact -->
                                <td style="padding:12px 14px; font-size:0.82rem;">
                                    <div style="color:var(--text-primary); font-weight:600;"><?= e($m['phone']) ?></div>
                                    <div style="color:var(--text-muted); font-size:0.78rem;"><?= e($m['email']) ?></div>
                                    <?php if (!empty($m['district'])): ?>
                                        <div style="color:var(--text-muted); font-size:0.74rem;">📍 <?= e($m['district']) ?></div>
                                    <?php endif; ?>
                                </td>

                                <!-- Status -->
                                <td style="padding:12px 14px;">
                                    <?php
                                        $st = $m['status'] ?? 'Active';
                                        $bg = '#dcfce7'; $col = '#15803d';
                                        if ($st === 'Lifetime Active') {
                                            $bg = '#fef3c7'; $col = '#b45309';
                                        } elseif ($st === 'Payment Due') {
                                            $bg = '#fee2e2'; $col = '#b91c1c';
                                        } elseif ($st === 'Pending') {
                                            $bg = '#f1f5f9'; $col = '#475569';
                                        } elseif ($st === 'Suspended') {
                                            $bg = '#fef2f2'; $col = '#991b1b';
                                        }
                                    ?>
                                    <span style="background:<?= $bg ?>; color:<?= $col ?>; padding:3px 10px; border-radius:var(--radius-full); font-size:0.75rem; font-weight:800; display:inline-block;">
                                        ● <?= e($st) ?>
                                    </span>
                                </td>

                                <!-- Validity -->
                                <td style="padding:12px 14px; font-size:0.82rem;">
                                    <div>
                                        <strong style="color:var(--primary-deep);"><?= empty($m['expiry_date']) ? 'LIFETIME' : e($m['expiry_date']) ?></strong>
                                    </div>
                                    <div style="font-size:0.74rem; color:var(--text-muted);">
                                        <?= $isBn ? 'ভর্তি:' : 'Join:' ?> <?= e($m['joined_at'] ?? '2024') ?>
                                    </div>
                                </td>

                                <!-- Admin Actions -->
                                <td style="padding:12px 14px; text-align:right;">
                                    <div style="display:flex; justify-content:flex-end; gap:6px; flex-wrap:wrap;">
                                        
                                        <!-- If Pending: Approve & Reject buttons (Strictly Finance Officer) -->
                                        <?php if ($m['status'] === 'Pending'): ?>
                                            <?php if ($isFinanceOfficer): ?>
                                                <form action="<?= url('/admin/members/approve/' . e($m['id']), $currentLocale) ?>" method="POST" style="margin:0;" onsubmit="return confirm('<?= $isBn ? 'পেমেন্ট ও সদস্যপদ অনুমোদন নিশ্চিতকরণ: আপনি কি নিশ্চিত যে এই সদস্যপদ ফি অ্যাকাউন্টে জমা হয়েছে এবং সদস্যপদ সক্রিয় করতে চান?' : 'Confirm Payment Approval: Are you sure this payment has been received and verified?' ?>');">
                                                    <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>
                                                    <button type="submit" class="btn btn-sm btn-primary" style="background:#15803d; border-color:#15803d; padding:4px 10px; font-size:0.75rem;" title="<?= $isBn ? 'সদস্যপদ অনুমোদন করুন' : 'Approve Application' ?>">
                                                        ✓ <?= $isBn ? 'অনুমোদন' : 'Approve' ?>
                                                    </button>
                                                </form>

                                                <form action="<?= url('/admin/members/reject/' . e($m['id']), $currentLocale) ?>" method="POST" style="margin:0;" onsubmit="return confirm('<?= $isBn ? 'আপনি কি নিশ্চিত এই আবেদনটি বাতিল করতে চান?' : 'Are you sure you want to reject this application?' ?>');">
                                                    <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>
                                                    <button type="submit" class="btn btn-sm btn-danger" style="padding:4px 10px; font-size:0.75rem;" title="<?= $isBn ? 'আবেদন বাতিল' : 'Reject Application' ?>">
                                                        ✕ <?= $isBn ? 'বাতিল' : 'Reject' ?>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span style="font-size:0.74rem; font-weight:700; color:#b45309; background:#fef3c7; padding:3px 8px; border-radius:var(--radius-sm); border:1px solid #fde68a; display:inline-flex; align-items:center; gap:4px;" title="<?= $isBn ? 'ফাইন্যান্স অফিসার কর্তৃক যাচাইয়ের অপেক্ষায় (সুপার অ্যাডমিন ও অ্যাডমিন শুধুমাত্র পর্যবেক্ষণ করছেন)' : 'Awaiting Finance Officer audit (Super Admin & Admin monitoring)' ?>">
                                                    ⏳ <?= $isBn ? 'ফাইন্যান্স অডিট অপেক্ষমাণ' : 'Awaiting FO Audit' ?>
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <!-- If Student: Transition Category Button -->
                                        <?php if ($m['category_id'] === 'STUDENT' && $m['status'] !== 'Pending'): ?>
                                            <button onclick="openTransitionModal('<?= e($m['id']) ?>', '<?= e($m['member_code']) ?>', '<?= e($m['name_bn']) ?>')" class="btn btn-sm btn-outline" style="border-color:#c2410c; color:#c2410c; padding:4px 8px; font-size:0.75rem;" title="<?= $isBn ? 'ছাত্র থেকে উপার্জনশীলে রূপান্তর' : 'Transition to Earning' ?>">
                                                🎓➔💼 <?= $isBn ? 'রূপান্তর' : 'Transition' ?>
                                            </button>
                                        <?php endif; ?>

                                        <!-- If Active: Suspend button -->
                                        <?php if ($m['status'] === 'Active' || $m['status'] === 'Lifetime Active'): ?>
                                            <form action="<?= url('/admin/members/suspend/' . e($m['id']), $currentLocale) ?>" method="POST" style="margin:0;" onsubmit="return confirm('<?= $isBn ? 'সদস্যপদ স্থগিত করতে চান? অডিট লগে কারণ লিপিবদ্ধ হবে।' : 'Confirm suspending member? Audit log will be generated.' ?>');">
                                                <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>
                                                <input type="hidden" name="reason" value="<?= $isBn ? 'প্রশাসনিক পর্যালোচনা হেতু স্থগিতকরণ' : 'Suspended by admin review' ?>">
                                                <button type="submit" class="btn btn-sm btn-ghost" style="color:#b91c1c; border:1px solid #fca5a5; padding:4px 8px; font-size:0.75rem;" title="<?= $isBn ? 'সদস্যপদ স্থগিত করুন' : 'Suspend Member' ?>">
                                                    ⏸ <?= $isBn ? 'স্থগিত' : 'Suspend' ?>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- If Suspended: Reactivate button -->
                                        <?php if ($m['status'] === 'Suspended'): ?>
                                            <form action="<?= url('/admin/members/activate/' . e($m['id']), $currentLocale) ?>" method="POST" style="margin:0;">
                                                <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>
                                                <button type="submit" class="btn btn-sm btn-primary" style="background:#0284c7; border-color:#0284c7; padding:4px 8px; font-size:0.75rem;" title="<?= $isBn ? 'সদস্যপদ পুনরায় সক্রিয় করুন' : 'Reactivate Member' ?>">
                                                    ▶ <?= $isBn ? 'সক্রিয়' : 'Activate' ?>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- View Member Dashboard Alias -->
                                        <a href="<?= url('/membership/dashboard?as=' . e($m['member_code']), $currentLocale) ?>" target="_blank" class="btn btn-sm btn-ghost" style="border:1px solid var(--border-medium); padding:4px 8px; font-size:0.75rem;" title="<?= $isBn ? 'সদস্য ড্যাশবোর্ড ও ডিজিটাল কার্ড দেখুন' : 'View Member Dashboard' ?>">
                                            👁️
                                        </a>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

<?php endif; ?>

<!-- ========================================================================= -->
<!-- MODALS SECTION                                                            -->
<!-- ========================================================================= -->

<!-- Modal: Student -> Earning Category Transition (Historical preservation) -->
<div id="transitionModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#ffffff; border-radius:var(--radius-xl); max-width:520px; width:100%; padding:24px; box-shadow:0 20px 40px rgba(0,0,0,0.3); border:1px solid var(--border-medium);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="margin:0; font-size:1.2rem; font-weight:800; color:var(--primary-deep);">
                🎓 ➔ 💼 <?= $isBn ? 'সদস্য ক্যাটাগরি রূপান্তর (Student ➔ Earning)' : 'Member Category Transition' ?>
            </h3>
            <button onclick="closeTransitionModal()" style="background:none; border:none; font-size:1.4rem; cursor:pointer; color:var(--text-muted);">&times;</button>
        </div>

        <div style="background:var(--bg-surface); padding:12px; border-radius:var(--radius-md); margin-bottom:16px; font-size:0.85rem;">
            <div><?= $isBn ? 'টার্গেট সদস্য:' : 'Target Member:' ?> <strong id="modalMemberName" style="color:var(--primary-deep);"></strong></div>
            <div><?= $isBn ? 'স্থায়ী মেম্বার আইডি:' : 'Permanent Member ID:' ?> <strong id="modalMemberCode" style="color:#0284c7; font-family:monospace;"></strong> (অপরিবর্তিত থাকবে)</div>
        </div>

        <form id="transitionModalForm" action="" method="POST">
            <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>
            <input type="hidden" name="new_category" value="EARNING">

            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:0.85rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                    <?= $isBn ? 'নতুন মেম্বারশিপ প্ল্যান *' : 'New Membership Plan *' ?>
                </label>
                <select name="new_plan" class="form-select" style="width:100%; padding:8px 12px; border:1px solid var(--border-medium); border-radius:var(--radius-md); font-size:0.88rem;">
                    <option value="EARNING_MONTHLY"><?= $isBn ? 'উপার্জনশীল সদস্য - মাসিক (৳১০০/মাস)' : 'Earning Member - Monthly (৳100/mo)' ?></option>
                    <option value="YEARLY"><?= $isBn ? 'বাৎসরিক সদস্যপদ (৳১,০০০/বছর)' : 'Yearly Member (৳1,000/yr)' ?></option>
                    <option value="LIFETIME"><?= $isBn ? 'আজীবন সদস্যপদ (৳১০,০০০)' : 'Lifetime Member (৳10,000)' ?></option>
                </select>
            </div>

            <div style="margin-bottom:18px;">
                <label style="display:block; font-size:0.85rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                    <?= $isBn ? 'রূপান্তরের কারণ / অডিট নোট *' : 'Reason / Historical Audit Note *' ?>
                </label>
                <textarea name="reason" rows="3" required placeholder="<?= $isBn ? 'উদাঃ গ্র্যাজুয়েশন সম্পন্ন করে পেশাজীবনে পদার্পণ।' : 'Graduated from academic studies and started professional employment.' ?>" style="width:100%; padding:8px 12px; border:1px solid var(--border-medium); border-radius:var(--radius-md); font-size:0.88rem;"></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="closeTransitionModal()" class="btn btn-sm btn-ghost" style="border:1px solid var(--border-medium);">
                    <?= $isBn ? 'বাতিল' : 'Cancel' ?>
                </button>
                <button type="submit" class="btn btn-sm btn-primary" style="background:#c2410c; border-color:#c2410c; font-weight:700;">
                    <?= $isBn ? 'রূপান্তর সম্পন্ন করুন' : 'Execute Transition' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Payment Screenshot Zoom & Quick Verify -->
<div id="adminScreenshotModal" style="display:none; position:fixed; inset:0; background:rgba(15, 23, 42, 0.75); z-index:9999; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(4px);">
    <div style="background:#ffffff; border-radius:var(--radius-xl); max-width:540px; width:100%; padding:24px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.5); border:1px solid var(--border-medium);">
        
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; border-bottom:1px solid var(--border-subtle); padding-bottom:10px;">
            <div>
                <div style="font-size:0.75rem; text-transform:uppercase; font-weight:800; color:#166534;">
                    🪙 <?= $isBn ? 'পেমেন্ট রসিদ অডিট' : 'Payment Receipt Inspection' ?>
                </div>
                <h3 id="modalPaymentTitle" style="margin:2px 0 0; font-size:1.1rem; font-weight:800; color:var(--primary-deep); font-family:monospace;"></h3>
            </div>
            <button onclick="closeAdminScreenshotModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-muted);">&times;</button>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; background:var(--bg-surface); padding:10px 14px; border-radius:var(--radius-md); margin-bottom:14px; font-size:0.84rem;">
            <div><?= $isBn ? 'সদস্য:' : 'Member:' ?> <strong id="modalMemName" style="color:var(--primary-deep);"></strong></div>
            <div><?= $isBn ? 'টাকা:' : 'Amount:' ?> <strong id="modalPayAmount" style="color:#15803d; font-size:0.95rem;"></strong></div>
            <div><?= $isBn ? 'প্রেরক নম্বর:' : 'Sender Phone:' ?> <strong id="modalSenderNum" style="font-family:monospace;"></strong></div>
            <div><?= $isBn ? 'প্রেরকের নাম:' : 'Sender Name:' ?> <strong id="modalSenderName" style="color:var(--text-primary);"></strong></div>
            <div><?= $isBn ? 'পেমেন্টের সময়:' : 'Payment Time:' ?> <strong id="modalPayTime" style="font-family:monospace;"></strong></div>
            <div><?= $isBn ? 'রেফারেন্স:' : 'Reference:' ?> <strong id="modalPayRef" style="color:#0369a1;"></strong></div>
            <div style="grid-column: 1 / -1; border-top:1px dashed var(--border-subtle); padding-top:6px; margin-top:2px;">
                <?= $isBn ? 'দায়িত্বপ্রাপ্ত ভেরিফায়ার:' : 'Designated Verifier:' ?> <strong style="color:#166534;"><?= $isBn ? 'জয় চক্রবর্তী (কোষাধ্যক্ষ / ফাইন্যান্স অফিসার)' : 'Joy Chakraborty (Treasurer / Finance Officer)' ?></strong>
            </div>
        </div>

        <div style="text-align:center; background:#0f172a; border-radius:var(--radius-lg); padding:14px; margin-bottom:16px;">
            <img id="modalPaymentImg" src="" alt="Payment Receipt" style="max-height:360px; max-width:100%; object-fit:contain; border-radius:6px; box-shadow:0 4px 12px rgba(0,0,0,0.3);">
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
            <div style="display:flex; gap:8px; align-items:center;">
                <button type="button" onclick="closeAdminScreenshotModal()" class="btn btn-sm btn-ghost" style="border:1px solid var(--border-medium);">
                    <?= $isBn ? 'বন্ধ করুন' : 'Close' ?>
                </button>
                <a id="modalOfficialInvoiceLink" href="#" target="_blank" class="btn btn-sm" style="display:inline-flex; align-items:center; gap:5px; background:#fffbeb; color:#b45309; border:1px solid #fde68a; font-weight:700; text-decoration:none; padding:5px 12px; font-size:0.78rem;">
                    <span>📄</span>
                    <span><?= $isBn ? 'অফিসিয়াল ইনভয়েস' : 'Official Invoice' ?></span>
                </a>
            </div>
            <div id="modalVerifyActionContainer" style="display:flex; gap:8px;">
                <?php if ($isFinanceOfficer): ?>
                    <form id="modalVerifyForm" action="" method="POST" style="margin:0;" onsubmit="return confirm('<?= $isBn ? 'পেমেন্ট অনুমোদন নিশ্চিতকরণ: আপনি কি নিশ্চিত যে এই সদস্যপদ ফি/পেমেন্ট অ্যাকাউন্টে জমা হয়েছে এবং যাচাই সম্পন্ন হয়েছে?' : 'Confirm Payment Approval: Are you sure this payment has been received and verified?' ?>');">
                        <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>
                        <button type="submit" class="btn btn-sm btn-primary" style="background:#15803d; border-color:#15803d; font-weight:800;">
                            ✓ <?= $isBn ? 'যাচাই ও সক্রিয় করুন' : 'Verify & Activate' ?>
                        </button>
                    </form>
                <?php else: ?>
                    <div style="font-size:0.75rem; color:#1e40af; background:#eff6ff; border:1px solid #bfdbfe; padding:6px 12px; border-radius:var(--radius-md); font-weight:700; display:flex; align-items:center; gap:6px;">
                        <span>🛡️</span>
                        <span><?= $isBn ? 'তত্ত্বাবধান মোড: শুধুমাত্র ফাইন্যান্স অফিসার ভেরিফাই করতে পারবেন।' : 'Supervisory Mode: Only Finance Officer can execute verification.' ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Payment Rejection Reason Prompt -->
<div id="paymentRejectModal" style="display:none; position:fixed; inset:0; background:rgba(15, 23, 42, 0.75); z-index:9999; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(4px);">
    <div style="background:#ffffff; border-radius:var(--radius-xl); max-width:480px; width:100%; padding:24px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.5); border:1px solid var(--border-medium);">
        
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; border-bottom:1px solid var(--border-subtle); padding-bottom:10px;">
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:1.3rem; color:#dc2626;">⚠️</span>
                <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:#991b1b;">
                    <?= $isBn ? 'পেমেন্ট বাতিল নিশ্চিতকরণ (Reject Payment)' : 'Reject Payment Confirmation' ?>
                </h3>
            </div>
            <button onclick="closePaymentRejectModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-muted);">&times;</button>
        </div>

        <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:var(--radius-md); padding:10px 14px; margin-bottom:14px; font-size:0.85rem; color:#991b1b;">
            <?= $isBn 
                ? 'সদস্য: <strong id="rejectModalMemberName"></strong> (TrxID: <strong id="rejectModalTrx"></strong>)' 
                : 'Target Member: <strong id="rejectModalMemberName"></strong>' ?>
        </div>

        <form id="paymentRejectForm" action="" method="POST">
            <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>
            
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:0.85rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                    <?= $isBn ? 'বাতিলের কারণ / অডিট নোট *' : 'Reason for Rejection *' ?>
                </label>
                <textarea id="rejectReasonInput" name="reason" rows="3" required placeholder="<?= $isBn ? 'উদাঃ প্রেরিত TrxID এর সাথে বিকাশ স্টেটমেন্টের মিল পাওয়া যায়নি।' : 'TrxID does not match bank/bKash statement.' ?>" style="width:100%; padding:8px 12px; border:1px solid var(--border-medium); border-radius:var(--radius-md); font-size:0.88rem;"></textarea>
            </div>

            <!-- Quick Reason Suggestions -->
            <div style="margin-bottom:16px;">
                <span style="font-size:0.75rem; color:var(--text-muted);"><?= $isBn ? 'দ্রুত কারণ নির্বাচন:' : 'Quick Presets:' ?></span>
                <div style="display:flex; gap:6px; flex-wrap:wrap; margin-top:4px;">
                    <button type="button" onclick="document.getElementById('rejectReasonInput').value = 'প্রদত্ত TrxID বিকাশ স্টেটমেন্টে পাওয়া যায়নি।';" class="btn btn-xs btn-ghost" style="border:1px solid #fca5a5; font-size:0.72rem; padding:2px 8px;">
                        TrxID অমিল
                    </button>
                    <button type="button" onclick="document.getElementById('rejectReasonInput').value = 'আপলোডকৃত স্ক্রিনশট অস্পষ্ট অথবা তথ্য পাঠযোগ্য নয়।';" class="btn btn-xs btn-ghost" style="border:1px solid #fca5a5; font-size:0.72rem; padding:2px 8px;">
                        স্ক্রিনশট অস্পষ্ট
                    </button>
                    <button type="button" onclick="document.getElementById('rejectReasonInput').value = 'টাকার পরিমাণ এবং প্ল্যান ফি এর মধ্যে অসঙ্গতি রয়েছে।';" class="btn btn-xs btn-ghost" style="border:1px solid #fca5a5; font-size:0.72rem; padding:2px 8px;">
                        টাকার পরিমাণ অমিল
                    </button>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="closePaymentRejectModal()" class="btn btn-sm btn-ghost" style="border:1px solid var(--border-medium);">
                    <?= $isBn ? 'ফিরে যান' : 'Cancel' ?>
                </button>
                <button type="submit" class="btn btn-sm btn-danger" style="font-weight:700;">
                    ✕ <?= $isBn ? 'পেমেন্ট বাতিল করুন' : 'Confirm Rejection' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openTransitionModal(id, code, name) {
    document.getElementById('modalMemberName').textContent = name;
    document.getElementById('modalMemberCode').textContent = code;
    document.getElementById('transitionModalForm').action = '<?= url('/admin/members/transition/', $currentLocale) ?>' + id;
    const modal = document.getElementById('transitionModal');
    modal.style.display = 'flex';
}

function closeTransitionModal() {
    const modal = document.getElementById('transitionModal');
    modal.style.display = 'none';
}

function openAdminScreenshotModal(imgSrc, trx, name, amount, sender, paymentId, status, senderName, payTime, payRef) {
    document.getElementById('modalPaymentImg').src = imgSrc;
    document.getElementById('modalPaymentTitle').textContent = 'TrxID: ' + (trx || 'N/A');
    document.getElementById('modalMemName').textContent = name || '';
    document.getElementById('modalPayAmount').textContent = amount || '';
    document.getElementById('modalSenderNum').textContent = sender || 'N/A';
    document.getElementById('modalSenderName').textContent = senderName || '—';
    document.getElementById('modalPayTime').textContent = payTime || '—';
    document.getElementById('modalPayRef').textContent = payRef || '—';
    
    const invoiceLink = document.getElementById('modalOfficialInvoiceLink');
    if (invoiceLink) {
        invoiceLink.href = '<?= url('/invoice/', $currentLocale) ?>' + (trx || paymentId);
    }
    
    const verifyContainer = document.getElementById('modalVerifyActionContainer');
    if (status === 'Pending') {
        verifyContainer.style.display = 'flex';
        const verifyForm = document.getElementById('modalVerifyForm');
        if (verifyForm) {
            verifyForm.action = '<?= url('/admin/members/payment/verify/', $currentLocale) ?>' + paymentId;
        }
    } else {
        verifyContainer.style.display = 'none';
    }

    const modal = document.getElementById('adminScreenshotModal');
    modal.style.display = 'flex';
}

function closeAdminScreenshotModal() {
    const modal = document.getElementById('adminScreenshotModal');
    modal.style.display = 'none';
}

function openPaymentRejectModal(id, trx, name) {
    document.getElementById('rejectModalMemberName').textContent = name;
    document.getElementById('rejectModalTrx').textContent = trx;
    document.getElementById('paymentRejectForm').action = '<?= url('/admin/members/payment/reject/', $currentLocale) ?>' + id;
    const modal = document.getElementById('paymentRejectModal');
    modal.style.display = 'flex';
}

function closePaymentRejectModal() {
    const modal = document.getElementById('paymentRejectModal');
    modal.style.display = 'none';
}

function copyTrxText(text, btn) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function() {
            const orig = btn.textContent;
            btn.textContent = '✓ কপিড!';
            setTimeout(function() { btn.textContent = orig; }, 1500);
        });
    } else {
        alert('TrxID: ' + text);
    }
}
</script>
