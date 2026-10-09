<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$currentUser = \App\Services\AuthService::getCurrentUser();
?>

<div class="admin-page-header">
    <div>
        <div style="font-size:0.82rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--accent-saffron); font-weight:700; margin-bottom:4px;">
            <?= $isBn ? 'ওয়েবসাইট ইন্টারফেস ও লেআউট' : 'Website Interface & Layout' ?>
        </div>
        <h1 style="font-size:1.6rem; font-weight:700; color:var(--text-main); margin:0;">
            <?= $isBn ? 'ফ্রন্ট পেজ সেকশন ডায়নামিক কনফিগারেশন' : 'Front Page Dynamic Sections Configuration' ?>
        </h1>
        <p style="font-size:0.9rem; color:var(--text-muted); margin:4px 0 0;">
            <?= $isBn 
                ? 'এডমিন প্যানেল থেকে যেকোনো সেকশন প্রয়োজন অনুযায়ী লাইভ দৃশ্যমান (Active) বা গোপন (Inactive) রাখুন এবং ক্রম পরিবর্তন করুন।' 
                : 'Control the visibility and display priority of landing page sections directly from here without altering code.' ?>
        </p>
    </div>

    <div style="display:flex; gap:10px;">
        <a href="<?= e(url('/', $currentLocale)) ?>" target="_blank" class="btn btn-secondary btn-sm" style="display:inline-flex; align-items:center; gap:6px;">
            <span>🌐</span>
            <span><?= $isBn ? 'লাইভ ফ্রন্ট পেজ প্রিভিউ' : 'Live Front Page Preview' ?> ↗</span>
        </a>
    </div>
</div>

<!-- Notice Bar: Privacy & Sensitive Content Security -->
<div style="background:#fffbeb; border:1px solid #fef3c7; border-left:4px solid #f59e0b; border-radius:var(--radius-sm); padding:var(--space-md) var(--space-lg); margin-bottom:var(--space-xl); display:flex; align-items:flex-start; gap:var(--space-md);">
    <div style="font-size:1.5rem; line-height:1; color:#d97706;">🛡️</div>
    <div style="flex:1;">
        <div style="font-weight:700; font-size:0.95rem; color:#92400e; margin-bottom:4px;">
            <?= $isBn ? 'গোপনীয়তা ও শাস্ত্রীয় অধিকার নীতি (Strict Member Gate)' : 'Confidentiality & Scripture Access Gate Policy' ?>
        </div>
        <div style="font-size:0.86rem; color:#78350f; line-height:1.55;">
            <?= $isBn 
                ? 'এসপিএস-এর ডিজিটাল লাইব্রেরির যেকোনো প্রামাণ্য শাস্ত্র ও ইন্টারনাল গ্রন্থ উন্মুক্ত নয়। সর্বসাধারণের সামনে কোনো অভ্যন্তরীণ লিঙ্ক খোলা রাখা হয় না; সদস্যপদ যাচাই ব্যতীত কোনো অ-সদস্য গ্রন্থ পাঠ করতে পারবেন না।'
                : 'Sacred internal manuscripts and theological library books require verified member status. Public visitors and non-members must verify membership to open full online readers.' ?>
        </div>
    </div>
</div>

<form action="<?= e(url('/admin/homepage', $currentLocale)) ?>" method="POST" id="homepageSectionsForm">
    <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>

    <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-sm); overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.05); margin-bottom:var(--space-xl);">
        <div style="padding:var(--space-md) var(--space-lg); background:#f8fafc; border-bottom:1px solid var(--border-medium); display:flex; justify-content:space-between; align-items:center;">
            <div style="font-weight:700; font-size:0.95rem; color:var(--text-main);">
                <?= $isBn ? 'সেকশনসমূহের তালিকা ও নিয়ন্ত্রণ প্যানেল' : 'Homepage Sections & Controls' ?>
            </div>
            <div style="font-size:0.8rem; color:var(--text-muted);">
                <?= $isBn ? 'মোট ১১টি কনফিগারযোগ্য মডিউল' : 'Total 11 Configurable Modules' ?>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="table" style="width:100%; border-collapse:collapse; text-align:left; font-size:0.88rem;">
                <thead>
                    <tr style="background:#f1f5f9; border-bottom:1px solid var(--border-medium);">
                        <th style="padding:12px 16px; width:70px; text-align:center;"><?= $isBn ? 'ক্রম (Order)' : 'Order' ?></th>
                        <th style="padding:12px 16px; width:130px;"><?= $isBn ? 'স্ট্যাটাস (Status)' : 'Status' ?></th>
                        <th style="padding:12px 16px; width:220px;"><?= $isBn ? 'সেকশনের নাম' : 'Section Name' ?></th>
                        <th style="padding:12px 16px;"><?= $isBn ? 'বিবরণ ও বিষয়বস্তু' : 'Description & Scope' ?></th>
                        <th style="padding:12px 16px; width:110px; text-align:center;"><?= $isBn ? 'আইডি' : 'DOM ID' ?></th>
                    </tr>
                </thead>
                <tbody id="sectionsTableBody">
                    <?php foreach ($sections as $index => $sec): ?>
                        <?php 
                        $key = $sec['key'] ?? $sec['id'];
                        $isEnabled = !empty($sec['enabled']);
                        $order = $sec['order'] ?? ($index + 1);
                        ?>
                        <tr style="border-bottom:1px solid var(--border-subtle); transition:background 0.15s ease;" class="section-row <?= $isEnabled ? '' : 'row-disabled' ?>">
                            <td style="padding:12px 16px; text-align:center;">
                                <input type="number" 
                                       name="sections[<?= e($key) ?>][order]" 
                                       value="<?= e((string)$order) ?>" 
                                       min="1" 
                                       max="50" 
                                       style="width:54px; padding:4px 6px; text-align:center; border:1px solid var(--border-medium); border-radius:var(--radius-sm); font-weight:600; font-size:0.9rem;"
                                       title="<?= $isBn ? 'প্রদর্শনের ক্রম পরিবর্তন করুন' : 'Change sort order' ?>">
                            </td>
                            <td style="padding:12px 16px;">
                                <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer;">
                                    <input type="checkbox" 
                                           name="sections[<?= e($key) ?>][enabled]" 
                                           value="1" 
                                           <?= $isEnabled ? 'checked' : '' ?>
                                           style="width:18px; height:18px; cursor:pointer; accent-color:var(--accent-saffron);">
                                    <span style="font-weight:600; font-size:0.84rem; color:<?= $isEnabled ? '#16a34a' : '#94a3b8' ?>;">
                                        <?= $isEnabled ? ($isBn ? 'সক্রিয় (Active)' : 'Active') : ($isBn ? 'লুকায়িত (Hidden)' : 'Hidden') ?>
                                    </span>
                                </label>
                            </td>
                            <td style="padding:12px 16px;">
                                <div style="font-weight:700; color:var(--text-main);">
                                    <?= e($isBn ? ($sec['name_bn'] ?? $sec['name_en']) : ($sec['name_en'] ?? $sec['name_bn'])) ?>
                                </div>
                            </td>
                            <td style="padding:12px 16px; color:var(--text-secondary); font-size:0.84rem; line-height:1.5;">
                                <?= e($isBn ? ($sec['description_bn'] ?? '') : ($sec['description_bn'] ?? '')) ?>
                            </td>
                            <td style="padding:12px 16px; text-align:center;">
                                <code style="background:#f1f5f9; padding:2px 6px; border-radius:3px; font-size:0.78rem; color:#475569;">#<?= e($sec['id']) ?></code>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="padding:var(--space-md) var(--space-lg); background:#f8fafc; border-top:1px solid var(--border-medium); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:var(--space-md);">
            <div style="font-size:0.84rem; color:var(--text-muted);">
                💡 <?= $isBn 
                    ? 'টিপস: যেকোনো সেকশনের চেকবক্স টিকচিহ্ন সরিয়ে দিয়ে সেভ করলে তা মূল ফ্রন্ট পেজ থেকে তাৎক্ষণিক অদৃশ্য হয়ে যাবে।' 
                    : 'Tip: Unchecking any section will immediately remove it from the public homepage upon saving.' ?>
            </div>
            <button type="submit" class="btn btn-primary" style="padding:8px 24px; font-weight:700; font-size:0.92rem; display:inline-flex; align-items:center; gap:8px;">
                <span>💾</span>
                <span><?= $isBn ? 'পরিবর্তনসমূহ সংরক্ষণ করুন' : 'Save Changes' ?></span>
            </button>
        </div>
    </div>
</form>

<style>
.section-row:hover {
    background: #f8fafc;
}
.row-disabled {
    background: #fafafa;
    opacity: 0.75;
}
</style>
