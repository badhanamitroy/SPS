<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$currentUser = \App\Services\AuthService::getCurrentUser();
$canCreateExpense = \App\Services\AuthService::can('finance.create_expense');
$canApproveExpense = \App\Services\AuthService::can('finance.approve_expense');
$canVerifyDonation = \App\Services\AuthService::can('finance.verify_donation');
$isAuditor = $currentUser['role'] === 'auditor';
?>

<div class="admin-page-header">
    <div>
        <h2 class="admin-page-title"><?= $isBn ? 'আর্থিক হিসাব ও মেকার-চেকার নিয়ন্ত্রণ' : 'Financial Ledger & Maker-Checker System' ?></h2>
        <p class="admin-page-desc">
            <?= $isBn 
                ? 'স্বচ্ছ আর্থিক ব্যবস্থাপনা: অনুদান যাচাই, ব্যয়ের দ্বৈত অনুমোদন ও গুগল শিটস লাইভ সিঙ্ক।' 
                : 'Transparent financial governance: Donation verification, maker-checker dual approval, and Google Sheets sync.' ?>
        </p>
    </div>
</div>

<!-- Maker-Checker Protocol Card -->
<div style="background:#f0fdf4; border:1px solid #bbf7d0; border-left:4px solid #16a34a; padding:16px 20px; border-radius:var(--radius-md); margin-bottom:var(--space-2xl);">
    <h4 style="margin:0 0 6px; color:#14532d; font-size:1rem; font-weight:700;">
        ⚖️ <?= $isBn ? 'আর্থিক মেকার-চেকার (Maker-Checker) নীতি:' : 'Financial Maker-Checker Separation of Duties:' ?>
    </h4>
    <p style="margin:0; font-size:0.86rem; color:#166534; line-height:1.6;">
        <?= $isBn 
            ? 'যে কর্মকর্তা ব্যয়ের ভাউচার প্রস্তুত বা এন্ট্রি করবেন (Maker), তিনি নিজে সেই ব্যয়ের চূড়ান্ত অনুমোদন (Checker) দিতে পারবেন না। প্রাতিষ্ঠানিক জবাবদিহিতা নিশ্চিত করতে অন্য একজন ক্ষমতাপ্রাপ্ত প্রশাসক এটি যাচাইপূর্বক অনুমোদন করবেন।' 
            : 'The officer who enters an expenditure voucher (Maker) is forbidden from approving it. A separate authorized administrator (Checker) must audit and approve the disbursement to prevent conflicts of interest.' ?>
    </p>
</div>

<!-- Ledger Summary Cards -->
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:var(--space-lg); margin-bottom:var(--space-2xl);">
    <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-md); padding:var(--space-lg); box-shadow:var(--shadow-sm);">
        <div style="font-size:0.82rem; font-weight:600; color:var(--text-muted);"><?= $isBn ? 'চলতি মাসে সংগৃহীত অনুদান' : 'Month Donations' ?></div>
        <div style="font-size:1.8rem; font-weight:800; color:#16a34a; margin:4px 0;">৳ ৪,৮২,৫০০</div>
        <div style="font-size:0.78rem; color:#6b7280;"><?= $isBn ? '১০০% অনলাইন ট্রানজ্যাকশন যাচাইকৃত' : '100% verified online transactions' ?></div>
    </div>

    <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-md); padding:var(--space-lg); box-shadow:var(--shadow-sm);">
        <div style="font-size:0.82rem; font-weight:600; color:var(--text-muted);"><?= $isBn ? 'অনুমোদিত উন্নয়ন ও সেবা ব্যয়' : 'Approved Expenditures' ?></div>
        <div style="font-size:1.8rem; font-weight:800; color:#b91c1c; margin:4px 0;">৳ ৩,১৮,৭৫০</div>
        <div style="font-size:0.78rem; color:#6b7280;"><?= $isBn ? 'প্রকল্পওয়ারি হিসাব সংরক্ষিত' : 'Project allocations verified' ?></div>
    </div>

    <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-md); padding:var(--space-lg); box-shadow:var(--shadow-sm);">
        <div style="font-size:0.82rem; font-weight:600; color:var(--text-muted);"><?= $isBn ? 'গুগল শিটস মিরর স্ট্যাটাস' : 'Google Sheets Mirror' ?></div>
        <div style="font-size:1.2rem; font-weight:700; color:#2563eb; margin:6px 0;">● <?= $isBn ? 'সিঙ্ক্রোনাইজড' : 'Synchronized' ?></div>
        <div style="font-size:0.78rem; color:#6b7280;"><?= $isBn ? 'সর্বশেষ সিঙ্ক: আজ ১৬:০০ ঘটিকায়' : 'Last synced: Today 16:00' ?></div>
    </div>
</div>

<!-- Pending Expenses (Maker-Checker Demonstration) -->
<div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); padding:var(--space-xl); box-shadow:var(--shadow-sm); margin-bottom:var(--space-2xl);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-md); border-bottom:1px solid var(--border-subtle); padding-bottom:var(--space-sm);">
        <div>
            <h3 style="font-size:1.15rem; font-weight:700; color:var(--primary-deep); margin:0;">
                📝 <?= $isBn ? 'ব্যয়ের ভাউচার অনুমোদন অপেক্ষমাণ (Maker-Checker Queue)' : 'Expense Vouchers Pending Approval' ?>
            </h3>
            <div style="font-size:0.82rem; color:var(--text-muted);">
                <?= $isBn ? 'মেকার কর্তৃক এন্ট্রিকৃত ভাউচারসমূহ চেকার অনুমোদনের অপেক্ষায় রয়েছে।' : 'Vouchers submitted by Makers awaiting Checker approval.' ?>
            </div>
        </div>

        <?php if ($canCreateExpense): ?>
            <button class="btn btn-sm btn-primary" style="font-size:0.82rem;">
                + <?= $isBn ? 'নতুন ভাউচার এন্ট্রি (মেকার)' : 'New Voucher Entry' ?>
            </button>
        <?php endif; ?>
    </div>

    <table style="width:100%; border-collapse:collapse; font-size:0.85rem;">
        <thead>
            <tr style="background:#f8fafc; border-bottom:2px solid var(--border-medium); text-align:left;">
                <th style="padding:10px;"><?= $isBn ? 'ভাউচার আইডি ও খাত' : 'Voucher & Purpose' ?></th>
                <th style="padding:10px;"><?= $isBn ? 'পরিমাণ' : 'Amount' ?></th>
                <th style="padding:10px;"><?= $isBn ? 'এন্ট্রিদাতা (Maker)' : 'Maker' ?></th>
                <th style="padding:10px;"><?= $isBn ? 'স্ট্যাটাস' : 'Status' ?></th>
                <th style="padding:10px; text-align:right;"><?= $isBn ? 'চেকার অ্যাকশন' : 'Checker Action' ?></th>
            </tr>
        </thead>
        <tbody>
            <tr style="border-bottom:1px solid var(--border-subtle);">
                <td style="padding:12px;">
                    <div style="font-weight:700; color:var(--primary-deep);">#EXP-4021 — শরৎকালীন বস্ত্র বিতরণ ও কম্বল ক্রয়</div>
                    <div style="font-size:0.75rem; color:#64748b;">প্রকল্প: চট্টগ্রাম গ্রামীণ সেবা • রসিদ সংযুক্ত: <code>receipt_4021.pdf</code></div>
                </td>
                <td style="padding:12px; font-weight:700; color:#b91c1c; font-family:monospace; font-size:0.95rem;">
                    ৳ ১,৮৭,৫০০
                </td>
                <td style="padding:12px;">
                    <div style="font-weight:600;">বিকাশ তালুকদার</div>
                    <div style="font-size:0.75rem; color:#64748b;">(usr_finance • ফাইন্যান্স অফিসার)</div>
                </td>
                <td style="padding:12px;">
                    <span style="background:#fef3c7; color:#92400e; padding:3px 8px; border-radius:4px; font-size:0.78rem; font-weight:600;">
                        ⏳ <?= $isBn ? 'চেকার অনুমোদন অপেক্ষমাণ' : 'Pending Checker' ?>
                    </span>
                </td>
                <td style="padding:12px; text-align:right;">
                    <?php if ($canApproveExpense): ?>
                        <?php if ($currentUser['id'] === 'usr_finance'): ?>
                            <span style="font-size:0.75rem; background:#fee2e2; color:#991b1b; padding:4px 8px; border-radius:4px; font-weight:600;">
                                🚫 <?= $isBn ? 'মেকার নিজের এন্ট্রি অনুমোদন করতে পারে না' : 'Maker cannot self-approve' ?>
                            </span>
                        <?php else: ?>
                            <button class="btn btn-sm btn-primary" style="padding:4px 10px; font-size:0.78rem;">
                                ✓ <?= $isBn ? 'অনুমোদন করুন (চেকার)' : 'Approve as Checker' ?>
                            </button>
                        <?php endif; ?>
                    <?php elseif ($isAuditor): ?>
                        <span style="font-size:0.78rem; color:#64748b; font-style:italic;">
                            👁️ <?= $isBn ? 'অডিটর দর্শন মোড' : 'Auditor View Mode' ?>
                        </span>
                    <?php else: ?>
                        <span style="font-size:0.78rem; color:#94a3b8;">
                            <?= $isBn ? 'অনুমোদনের ক্ষমতা নেই' : 'No Permission' ?>
                        </span>
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
