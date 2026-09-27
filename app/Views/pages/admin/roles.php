<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$roles = \App\Services\RbacService::getRoles();
$permissionsGrouped = \App\Services\RbacService::getPermissionsGrouped();
?>

<div class="admin-page-header">
    <div>
        <h2 class="admin-page-title"><?= $isBn ? 'এসপিএস রোল ও পারমিশন ম্যাট্রিক্স (RBAC Engine)' : 'SPS Role & Permission Matrix' ?></h2>
        <p class="admin-page-desc">
            <?= $isBn 
                ? 'প্ল্যাটফর্মের ১২টি বিশেষায়িত ভূমিকা এবং সার্ভার-সাইড রিসোর্স পারমিশনের পূর্ণাঙ্গ বিশ্লেষণ।' 
                : 'Complete mapping of all 12 operational roles and server-side resource-action permissions.' ?>
        </p>
    </div>
</div>

<!-- Architecture Notice -->
<div style="background:#f8fafc; border:1px solid #cbd5e1; border-left:4px solid #3b82f6; padding:16px 20px; border-radius:var(--radius-md); margin-bottom:var(--space-2xl);">
    <h4 style="margin:0 0 6px; color:#1e3a8a; font-size:1rem; font-weight:700;">
        ⚙️ <?= $isBn ? 'সার্ভার-সাইড পারমিশন মডেল (Resource-Action Pattern)' : 'Server-Side Permission Model' ?>
    </h4>
    <p style="margin:0; font-size:0.86rem; color:#475569; line-height:1.6;">
        <?= $isBn 
            ? 'এসপিএস-এ প্রতিটি অ্যাকশন <code>resource.action</code> প্যাটার্নে পরিচালিত হয় (যেমন: <code>blog.approve</code>, <code>finance.verify_donation</code>, <code>library.approve_request</code>)। শুধু ইউআই হাইড নয়, বরং প্রতিটি কন্ট্রোলার ও রাউট সার্ভার লেভেলে অ্যাক্সেস ভেরিফাই করে।' 
            : 'Every action is protected via <code>resource.action</code> authorization tokens. Authorization is strictly enforced server-side before execution.' ?>
    </p>
</div>

<!-- High Level Module Comparison Matrix Table -->
<div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); padding:var(--space-xl); box-shadow:var(--shadow-sm); margin-bottom:var(--space-2xl); overflow-x:auto;">
    <h3 style="font-size:1.15rem; font-weight:700; color:var(--primary-deep); margin:0 0 var(--space-md);">
        📊 <?= $isBn ? 'মডিউল-ভিত্তিক একনজরে অ্যাক্সেস স্তর (High-Level Matrix)' : 'High-Level Access Matrix' ?>
    </h3>

    <table style="width:100%; border-collapse:collapse; font-size:0.84rem; text-align:center;">
        <thead>
            <tr style="background:#0d1b2a; color:#ffffff;">
                <th style="padding:10px 12px; text-align:left;"><?= $isBn ? 'মডিউল' : 'Module' ?></th>
                <th style="padding:10px 8px;"><?= $isBn ? 'সুপার অ্যাডমিন' : 'Super Admin' ?></th>
                <th style="padding:10px 8px;"><?= $isBn ? 'অ্যাডমিন' : 'Admin' ?></th>
                <th style="padding:10px 8px;"><?= $isBn ? 'এডিটর' : 'Editor' ?></th>
                <th style="padding:10px 8px;"><?= $isBn ? 'ফাইন্যান্স' : 'Finance' ?></th>
                <th style="padding:10px 8px;"><?= $isBn ? 'প্রজেক্ট' : 'Project' ?></th>
                <th style="padding:10px 8px;"><?= $isBn ? 'লাইব্রেরি' : 'Library' ?></th>
                <th style="padding:10px 8px;"><?= $isBn ? 'মডারেটর' : 'Moderator' ?></th>
                <th style="padding:10px 8px;"><?= $isBn ? 'অডিটর' : 'Auditor' ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $matrixRows = [
                ['module' => $isBn ? 'ইউজার ও রোল (Users & Roles)' : 'Users & Roles', 'super' => 'Full', 'admin' => 'Limited', 'editor' => '—', 'finance' => '—', 'project' => '—', 'library' => '—', 'mod' => '—', 'auditor' => '—'],
                ['module' => $isBn ? 'সদস্যপদ (Members)' : 'Members', 'super' => 'Full', 'admin' => 'Full', 'editor' => 'View', 'finance' => '—', 'project' => 'View', 'library' => '—', 'mod' => '—', 'auditor' => '—'],
                ['module' => $isBn ? 'ব্লগ ও লেখা (Blog Posts)' : 'Blog Posts', 'super' => 'Full', 'admin' => 'Full', 'editor' => 'Edit / Publish', 'finance' => '—', 'project' => '—', 'library' => '—', 'mod' => 'Moderate', 'auditor' => '—'],
                ['module' => $isBn ? 'জ্ঞানপীঠ ও শাস্ত্র (Knowledge & Scripture)' : 'Knowledge & Scripture', 'super' => 'Full', 'admin' => 'Full', 'editor' => 'Full', 'finance' => '—', 'project' => '—', 'library' => '—', 'mod' => '—', 'auditor' => '—'],
                ['module' => $isBn ? 'সেবামূলক প্রকল্প (Projects)' : 'Projects', 'super' => 'Full', 'admin' => 'Full', 'editor' => 'View', 'finance' => 'View', 'project' => 'Full', 'library' => '—', 'mod' => '—', 'auditor' => 'View'],
                ['module' => $isBn ? 'অনুদান হিসাব (Donations)' : 'Donations', 'super' => 'Full', 'admin' => 'Full', 'editor' => '—', 'finance' => 'Full', 'project' => 'View', 'library' => '—', 'mod' => '—', 'auditor' => 'View'],
                ['module' => $isBn ? 'ব্যয় ও ভাউচার (Expenses)' : 'Expenses', 'super' => 'Full', 'admin' => 'Full', 'editor' => '—', 'finance' => 'Maker', 'project' => 'Project-level', 'library' => '—', 'mod' => '—', 'auditor' => 'View'],
                ['module' => $isBn ? 'গ্রন্থাগার ও ই-বুক (Library)' : 'Library', 'super' => 'Full', 'admin' => 'Full', 'editor' => '—', 'finance' => '—', 'project' => '—', 'library' => 'Full', 'mod' => '—', 'auditor' => '—'],
                ['module' => $isBn ? 'বই অর্ডার ও শিপিং (Orders)' : 'Orders', 'super' => 'Full', 'admin' => 'Full', 'editor' => '—', 'finance' => 'View', 'project' => '—', 'library' => 'Full', 'mod' => '—', 'auditor' => 'View'],
                ['module' => $isBn ? 'মন্তব্য ও রিপোর্ট (Comments)' : 'Comments', 'super' => 'Full', 'admin' => 'Full', 'editor' => '—', 'finance' => '—', 'project' => '—', 'library' => '—', 'mod' => 'Full', 'auditor' => '—'],
                ['module' => $isBn ? 'অডিট লগ (Audit Logs)' : 'Audit Logs', 'super' => 'Full', 'admin' => 'View', 'editor' => '—', 'finance' => 'Finance logs', 'project' => 'Project logs', 'library' => 'Library logs', 'mod' => '—', 'auditor' => 'Full (Read)'],
                ['module' => $isBn ? 'সিস্টেম সেটিংস (Settings)' : 'Settings', 'super' => 'Full', 'admin' => 'Limited', 'editor' => '—', 'finance' => '—', 'project' => '—', 'library' => '—', 'mod' => '—', 'auditor' => '—'],
            ];

            foreach ($matrixRows as $idx => $row):
                $bg = $idx % 2 === 0 ? '#f8fafc' : '#ffffff';
            ?>
                <tr style="background:<?= $bg ?>; border-bottom:1px solid #e2e8f0;">
                    <td style="padding:10px 12px; text-align:left; font-weight:700; color:var(--primary-deep);"><?= $row['module'] ?></td>
                    <td style="padding:10px 8px;"><span style="background:#fee2e2; color:#991b1b; padding:2px 8px; border-radius:3px; font-weight:700;"><?= $row['super'] ?></span></td>
                    <td style="padding:10px 8px;"><span style="background:#e0e7ff; color:#3730a3; padding:2px 8px; border-radius:3px; font-weight:600;"><?= $row['admin'] ?></span></td>
                    <td style="padding:10px 8px;"><span style="color:#0369a1; font-weight:600;"><?= $row['editor'] ?></span></td>
                    <td style="padding:10px 8px;"><span style="color:#15803d; font-weight:600;"><?= $row['finance'] ?></span></td>
                    <td style="padding:10px 8px;"><span style="color:#7e22ce; font-weight:600;"><?= $row['project'] ?></span></td>
                    <td style="padding:10px 8px;"><span style="color:#a16207; font-weight:600;"><?= $row['library'] ?></span></td>
                    <td style="padding:10px 8px;"><span style="color:#475569; font-weight:600;"><?= $row['mod'] ?></span></td>
                    <td style="padding:10px 8px;"><span style="color:#334155; font-weight:600; font-family:monospace;"><?= $row['auditor'] ?></span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Detailed Role Descriptions (All 12 Roles) -->
<h3 style="font-size:1.25rem; font-weight:800; color:var(--primary-deep); margin-bottom:var(--space-lg);">
    📋 <?= $isBn ? '১২টি প্রশাসনিক ভূমিকার বিশদ রূপরেখা ও সুরক্ষা সীমা' : 'Comprehensive Profiles of all 12 Admin Roles' ?>
</h3>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(360px, 1fr)); gap:var(--space-lg);">
    <?php foreach ($roles as $r): ?>
        <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-md); padding:var(--space-lg); box-shadow:var(--shadow-sm); display:flex; flex-direction:column;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:var(--space-xs);">
                <div>
                    <h4 style="font-size:1.05rem; font-weight:800; color:var(--primary-deep); margin:0;">
                        <?= e($isBn ? $r['name_bn'] : $r['name_en']) ?>
                    </h4>
                    <span style="font-family:monospace; font-size:0.75rem; color:#64748b;">[id: <?= e($r['id']) ?> • Level: <?= $r['level'] ?>]</span>
                </div>
                <span class="role-badge badge-<?= e($r['id']) ?>">
                    <?= e($r['access_level']) ?>
                </span>
            </div>

            <p style="font-size:0.86rem; color:var(--text-muted); line-height:1.5; margin:var(--space-xs) 0 var(--space-md); flex-grow:1;">
                <?= e($isBn ? $r['description_bn'] : $r['description_en']) ?>
            </p>

            <div style="border-top:1px solid var(--border-subtle); padding-top:var(--space-xs); font-size:0.78rem; color:#475569;">
                <strong><?= $isBn ? 'অনুমোদিত পারমিশন সংখ্যা:' : 'Assigned Permissions:' ?></strong> 
                <span style="font-weight:700; color:var(--primary-deep);">
                    <?= count(\App\Services\RbacService::getRolePermissions($r['id'])) ?>
                </span>
            </div>
        </div>
    <?php endforeach; ?>
</div>
