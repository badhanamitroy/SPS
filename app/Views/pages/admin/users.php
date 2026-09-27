<?php
/**
 * SPS Admin Users & Role Assignment Management Portal
 * Super Administrator Exclusive Master Governance Console:
 * Role Delegation, Departmental Task Scopes, and Fine-Grained Modular Allowances.
 */

$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$users = \App\Services\RbacService::getUsers();
$roles = \App\Services\RbacService::getRoles();
$currentUser = \App\Services\AuthService::getCurrentUser();
$isSuperAdmin = \App\Services\AuthService::isSuperAdmin();
$canAssignRoles = \App\Services\AuthService::can('users.manage_roles') || $isSuperAdmin;

// Catalog of assignable permissions grouped for fine-grained allowances
$allowanceModules = [
    'membership' => [
        'icon' => '🪪',
        'title_bn' => 'মেম্বারশিপ ও সদস্যপদ',
        'title_en' => 'Membership System',
        'permissions' => [
            ['id' => 'members.view', 'label_bn' => 'সদস্য তালিকা ও খতিয়ান দেখা', 'label_en' => 'View members & ledger'],
            ['id' => 'members.approve', 'label_bn' => 'সদস্যপদ আবেদন অনুমোদন', 'label_en' => 'Approve applications'],
            ['id' => 'members.reject', 'label_bn' => 'সদস্যপদ আবেদন বাতিল', 'label_en' => 'Reject applications'],
            ['id' => 'members.edit', 'label_bn' => 'সদস্য তথ্য রূপান্তর ও স্থগিতকরণ', 'label_en' => 'Edit / Suspend member'],
        ]
    ],
    'blog' => [
        'icon' => '✍️',
        'title_bn' => 'ব্লগ ও সাহিত্য জার্নাল',
        'title_en' => 'Blog & Literature',
        'permissions' => [
            ['id' => 'blog.view', 'label_bn' => 'খসড়া পাণ্ডুলিপি পর্যালোচনা', 'label_en' => 'Review drafts'],
            ['id' => 'blog.approve', 'label_bn' => 'ব্লগ পোস্ট অনুমোদন', 'label_en' => 'Approve post'],
            ['id' => 'blog.publish', 'label_bn' => 'সরাসরি ব্লগ প্রকাশনা', 'label_en' => 'Publish post directly'],
            ['id' => 'blog.delete', 'label_bn' => 'ব্লগ পোস্ট অপসারণ', 'label_en' => 'Delete blog post'],
        ]
    ],
    'library' => [
        'icon' => '📚',
        'title_bn' => 'ডিজিটাল গ্রন্থাগার',
        'title_en' => 'Digital Library',
        'permissions' => [
            ['id' => 'library.view', 'label_bn' => 'লাইব্রেরি ক্যাটালগ দেখা', 'label_en' => 'View library catalog'],
            ['id' => 'library.manage', 'label_bn' => 'বই ও পিডিএফ আপলোড/সম্পাদনা', 'label_en' => 'Manage books & PDFs'],
            ['id' => 'library.approve_request', 'label_bn' => 'পাঠাধিকার পাস অনুমোদন', 'label_en' => 'Approve reader passes'],
        ]
    ],
    'activities' => [
        'icon' => '🎪',
        'title_bn' => 'কার্যক্রম ও ইভেন্ট',
        'title_en' => 'Activities & Events',
        'permissions' => [
            ['id' => 'activities.view', 'label_bn' => 'ইভেন্ট তালিকা দেখা', 'label_en' => 'View activities'],
            ['id' => 'activities.create', 'label_bn' => 'নতুন ইভেন্ট/কার্যক্রম তৈরি', 'label_en' => 'Create new activity'],
            ['id' => 'activities.edit', 'label_bn' => 'কার্যক্রমের তথ্য আপডেট', 'label_en' => 'Edit activity'],
            ['id' => 'activities.delete', 'label_bn' => 'কার্যক্রম স্থায়ীভাবে মোছা', 'label_en' => 'Delete activity'],
        ]
    ],
    'finance' => [
        'icon' => '🪙',
        'title_bn' => 'আর্থিক হিসাব ও অনুদান',
        'title_en' => 'Finance & Ledger',
        'permissions' => [
            ['id' => 'finance.view', 'label_bn' => 'আর্থিক অডিট রিপোর্ট দেখা', 'label_en' => 'View finance reports'],
            ['id' => 'finance.verify_donation', 'label_bn' => 'অনলাইন অনুদান যাচাই', 'label_en' => 'Verify donations'],
            ['id' => 'finance.create_expense', 'label_bn' => 'ব্যয় ভাউচার তৈরি (Maker)', 'label_en' => 'Create expense voucher'],
        ]
    ],
    'projects' => [
        'icon' => '👥',
        'title_bn' => 'প্রকল্প ও স্বেচ্ছাসেবক',
        'title_en' => 'Projects & Volunteers',
        'permissions' => [
            ['id' => 'projects.create', 'label_bn' => 'সেবামূলক প্রকল্প তৈরি', 'label_en' => 'Create seva project'],
            ['id' => 'volunteers.assign', 'label_bn' => 'স্বেচ্ছাসেবকদের দায়িত্ব বণ্টন', 'label_en' => 'Assign volunteers to project'],
        ]
    ],
    'security' => [
        'icon' => '🛡️',
        'title_bn' => 'অডিট ও নিরাপত্তা',
        'title_en' => 'Audit & System',
        'permissions' => [
            ['id' => 'audit_logs.view', 'label_bn' => 'অপরিবর্তনীয় অডিট লগ দেখা', 'label_en' => 'View audit logs'],
            ['id' => 'users.manage_roles', 'label_bn' => 'ভূমিকা ও পারমিশন বণ্টন', 'label_en' => 'Assign roles & permissions'],
        ]
    ]
];
?>

<div class="admin-page-header">
    <div class="header-left">
        <h2 class="admin-page-title">
            <span>👑</span>
            <span><?= $isBn ? 'সুপার অ্যাডমিন নিয়ন্ত্রণকক্ষ: কর্মকর্তা ভূমিকা, দায়িত্ব ও ক্ষমতা বণ্টন' : 'Super Admin Console: Role & Task Allowance Governance' ?></span>
        </h2>
        <p class="admin-page-desc">
            <?= $isBn 
                ? 'এসপিএস পরিচালনা পর্ষদ ও কর্মকর্তাদের পদমর্যাদা, নির্দিষ্ট কাজের বিভাগ বা কার্যপরিধি (Assigned Tasks) এবং বিশেষ ক্ষমতা/অনুমতি (Allowances) নির্ধারণের কেন্দ্রীয় ক্ষমতা।' 
                : 'Super Administrator central authority to designate operational roles, departmental scopes, and grant/revoke fine-grained allowances across all officers.' ?>
        </p>
    </div>
    <div class="header-right" style="display:flex; gap:10px; align-items:center;">
        <a href="<?= url('/admin/roles', $currentLocale) ?>" class="btn btn-sm btn-ghost" style="border:1px solid var(--border-medium);">
            📊 <?= $isBn ? 'রোল ও পারমিশন ম্যাট্রিক্স ↗' : 'Role Matrix ↗' ?>
        </a>
        <a href="<?= url('/admin/audit-logs', $currentLocale) ?>" class="btn btn-sm btn-ghost" style="border:1px solid var(--border-medium);">
            📜 <?= $isBn ? 'অডিট লগ খতিয়ান ↗' : 'Audit Logs ↗' ?>
        </a>
    </div>
</div>

<!-- Super Admin Master Authority Banner -->
<div style="background:linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border:2px solid #93c5fd; border-radius:var(--radius-xl); padding:16px 22px; margin-bottom:24px; box-shadow:0 4px 12px rgba(30, 64, 175, 0.08); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
    <div style="display:flex; align-items:center; gap:14px;">
        <div style="width:48px; height:48px; border-radius:12px; background:#1e40af; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.6rem; box-shadow:0 4px 10px rgba(30, 64, 175, 0.25);">
            👑
        </div>
        <div>
            <div style="font-size:0.78rem; font-weight:800; text-transform:uppercase; color:#1e40af; letter-spacing:0.5px;">
                Super Administrator Master Authority
            </div>
            <h3 style="margin:2px 0 0; font-size:1.1rem; font-weight:800; color:#1e3a8a;">
                <?= $isBn ? 'সভাপতি কর্তৃক নির্বাহী দায়িত্ব অর্পণ ও এলাউন্স ডেস্ক' : 'Presidential Executive Assignment & Allowance Desk' ?>
            </h3>
            <p style="margin:3px 0 0; font-size:0.82rem; color:#1e40af; max-width:720px; line-height:1.4;">
                <?= $isBn 
                    ? 'সুপার অ্যাডমিন (সভাপতি) হিসেবে আপনি যেকোনো কর্মকর্তাকে কোন ভূমিকায় (Role) নিয়োগ করবেন, তাদের কোন কাজের দায়িত্ব (Scope) দেবেন এবং রোল ছাড়াও অতিরিক্ত কোন কোন কাজের বিশেষ অনুমতি (Allowance) দেবেন তা সরাসরি নিয়ন্ত্রণ করতে পারবেন।' 
                    : 'As Super Administrator (President), you have sovereign control to assign roles, specify departmental tasks (Scope), and grant fine-grained permissions (Allowances) to any official.' ?>
            </p>
        </div>
    </div>
    <div style="background:#ffffff; border:1px solid #93c5fd; border-radius:12px; padding:8px 16px; text-align:right; box-shadow:0 2px 6px rgba(0,0,0,0.04);">
        <div style="font-size:0.72rem; color:#1e40af; font-weight:700; text-transform:uppercase;"><?= $isBn ? 'বর্তমান ব্যবহারকারী:' : 'Logged User:' ?></div>
        <div style="font-size:0.95rem; font-weight:800; color:#1e3a8a;"><?= e($isBn ? ($currentUser['name_bn'] ?? '') : ($currentUser['name_en'] ?? '')) ?></div>
        <div style="font-size:0.74rem; color:#2563eb; font-weight:700; font-family:monospace;"><?= e($currentUser['designation_bn'] ?? 'সভাপতি') ?> (<?= e($currentUser['role'] ?? '') ?>)</div>
    </div>
</div>

<!-- Flash Alerts -->
<?php if ($success = \App\Core\Session::getFlash('success')): ?>
    <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:12px 16px; border-radius:8px; margin-bottom:20px; display:flex; align-items:center; gap:10px;">
        <span style="font-size:1.2rem;">✓</span>
        <div><?= e($success) ?></div>
    </div>
<?php endif; ?>

<?php if ($error = \App\Core\Session::getFlash('error') ?? \App\Core\Session::getFlash('danger')): ?>
    <div style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:12px 16px; border-radius:8px; margin-bottom:20px; display:flex; align-items:center; gap:10px;">
        <span style="font-size:1.2rem;">✕</span>
        <div><?= e($error) ?></div>
    </div>
<?php endif; ?>

<!-- Users Management Table -->
<div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-xl); box-shadow:var(--shadow-sm); overflow:hidden; margin-bottom:var(--space-2xl);">
    
    <div style="padding:16px 20px; border-bottom:1px solid var(--border-subtle); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:8px;">
            <span style="font-size:1.2rem;">👥</span>
            <h3 style="font-size:1.05rem; font-weight:800; color:var(--primary-deep); margin:0;">
                <?= $isBn ? 'এসপিএস নির্বাহী কর্মকর্তা ও প্রশাসক তালিকা' : 'SPS Executive Officers & Administrators' ?>
            </h3>
        </div>
        <span style="font-size:0.82rem; font-weight:700; color:var(--text-muted); background:var(--bg-surface); padding:4px 12px; border-radius:var(--radius-full);">
            <?= $isBn ? 'মোট কর্মকর্তা:' : 'Total Officers:' ?> <?= count($users) ?>
        </span>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.88rem;">
            <thead>
                <tr style="background:var(--bg-surface); border-bottom:2px solid var(--border-medium); text-align:left; font-size:0.78rem; text-transform:uppercase; color:var(--text-muted);">
                    <th style="padding:12px 16px;"><?= $isBn ? 'কর্মকর্তা ও পদবি' : 'Officer & Designation' ?></th>
                    <th style="padding:12px 14px;"><?= $isBn ? 'নিযুক্ত ভূমিকা (Role)' : 'Assigned Role' ?></th>
                    <th style="padding:12px 14px;"><?= $isBn ? 'কাজের বিভাগ / কার্যপরিধি' : 'Assigned Tasks & Scope' ?></th>
                    <th style="padding:12px 14px;"><?= $isBn ? 'বিশেষ কাজের অনুমতি (Allowances)' : 'Task Allowances' ?></th>
                    <th style="padding:12px 14px; text-align:center;"><?= $isBn ? 'স্ট্যাটাস' : 'Status' ?></th>
                    <th style="padding:12px 16px; text-align:right;"><?= $isBn ? 'সুপার অ্যাডমিন অ্যাকশন' : 'Super Admin Action' ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): 
                    $r = $roles[$u['role']] ?? null;
                    $customPerms = $u['custom_permissions'] ?? [];
                    if (!is_array($customPerms)) $customPerms = [];
                    $userJson = htmlspecialchars(json_encode([
                        'id' => $u['id'],
                        'name_bn' => $u['name_bn'] ?? '',
                        'name_en' => $u['name_en'] ?? '',
                        'designation_bn' => $u['designation_bn'] ?? '',
                        'designation_en' => $u['designation_en'] ?? '',
                        'email' => $u['email'] ?? '',
                        'avatar' => $u['avatar'] ?? '',
                        'role' => $u['role'] ?? '',
                        'scope' => $u['scope'] ?? '',
                        'custom_permissions' => $customPerms,
                    ]), ENT_QUOTES, 'UTF-8');
                ?>
                    <tr style="border-bottom:1px solid var(--border-subtle); vertical-align:middle; transition:background 0.15s ease;">
                        
                        <!-- Officer Profile -->
                        <td style="padding:12px 16px;">
                            <div style="display:flex; align-items:center; gap:12px;">
                                <img src="<?= e(!empty($u['avatar']) ? (str_starts_with($u['avatar'], 'http') ? $u['avatar'] : asset($u['avatar'])) : 'https://api.dicebear.com/7.x/bottts/svg?seed=' . e($u['id'])) ?>" 
                                     alt="Avatar" 
                                     style="width:42px; height:42px; border-radius:10px; object-fit:cover; background:#f1f5f9; border:2px solid var(--border-medium); flex-shrink:0;">
                                <div>
                                    <div style="font-weight:800; color:var(--primary-deep); font-size:0.95rem; display:flex; align-items:center; gap:6px;">
                                        <span><?= e($isBn ? ($u['name_bn'] ?? $u['name_en']) : ($u['name_en'] ?? $u['name_bn'])) ?></span>
                                        <?php if ($u['id'] === $currentUser['id']): ?>
                                            <span style="font-size:0.68rem; background:#dcfce7; color:#15803d; padding:1px 6px; border-radius:4px; font-weight:800; border:1px solid #86efac;">
                                                <?= $isBn ? 'আপনি' : 'You' ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-size:0.78rem; color:var(--primary-deep); font-weight:700; margin-top:1px;">
                                        <?= e($isBn ? ($u['designation_bn'] ?? '') : ($u['designation_en'] ?? '')) ?>
                                    </div>
                                    <div style="font-size:0.72rem; color:var(--text-muted); font-family:monospace; margin-top:1px;">
                                        ID: <?= e($u['id']) ?> • <?= e($u['email'] ?? '') ?>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Assigned Role -->
                        <td style="padding:12px 14px;">
                            <?php
                                $roleId = $u['role'] ?? 'guest';
                                $badgeBg = '#f1f5f9'; $badgeCol = '#334155';
                                if ($roleId === 'super_admin') { $badgeBg = '#fee2e2'; $badgeCol = '#991b1b'; }
                                elseif ($roleId === 'admin') { $badgeBg = '#eff6ff'; $badgeCol = '#1e40af'; }
                                elseif ($roleId === 'finance_officer') { $badgeBg = '#dcfce7'; $badgeCol = '#166534'; }
                                elseif ($roleId === 'content_editor') { $badgeBg = '#e0f2fe'; $badgeCol = '#0369a1'; }
                                elseif ($roleId === 'library_manager') { $badgeBg = '#fef3c7'; $badgeCol = '#92400e'; }
                                elseif ($roleId === 'membership_officer') { $badgeBg = '#fff7ed'; $badgeCol = '#c2410c'; }
                            ?>
                            <div style="display:inline-flex; align-items:center; gap:6px; background:<?= $badgeBg ?>; color:<?= $badgeCol ?>; padding:3px 10px; border-radius:var(--radius-full); font-size:0.78rem; font-weight:800; border:1px solid currentColor;">
                                <span><?= $roleId === 'super_admin' ? '👑' : ($roleId === 'finance_officer' ? '🪙' : ($roleId === 'admin' ? '🛡️' : '⚙️')) ?></span>
                                <span><?= e($isBn ? ($r['name_bn'] ?? $roleId) : ($r['name_en'] ?? $roleId)) ?></span>
                            </div>
                            <div style="font-size:0.72rem; color:var(--text-muted); margin-top:3px;">
                                Level: <strong><?= $r['level'] ?? 50 ?></strong>
                            </div>
                        </td>

                        <!-- Assigned Tasks & Scope -->
                        <td style="padding:12px 14px;">
                            <div style="display:flex; align-items:flex-start; gap:6px;">
                                <span style="font-size:0.9rem; line-height:1.2;">📌</span>
                                <div>
                                    <div style="font-weight:700; color:var(--text-primary); font-size:0.84rem;">
                                        <?= e($u['scope'] ?? 'সাধারণ দায়িত্ব') ?>
                                    </div>
                                    <div style="font-size:0.72rem; color:var(--text-muted); margin-top:2px;">
                                        <?= $isBn ? 'নিযুক্তকারী:' : 'Assigned by:' ?> <span style="font-family:monospace;"><?= e($u['assigned_by'] ?? 'Constitution') ?></span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Custom Task Allowances -->
                        <td style="padding:12px 14px;">
                            <?php if (!empty($customPerms)): ?>
                                <div style="display:flex; gap:4px; flex-wrap:wrap; max-width:240px;">
                                    <?php foreach ($customPerms as $cp): ?>
                                        <span style="font-size:0.7rem; font-family:monospace; font-weight:700; background:#f0fdf4; color:#15803d; border:1px solid #86efac; padding:1px 6px; border-radius:4px;" title="<?= $isBn ? 'সুপার অ্যাডমিন কর্তৃক প্রদত্ত বিশেষ অনুমতি' : 'Granted allowance' ?>">
                                            + <?= e($cp) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <span style="font-size:0.75rem; color:var(--text-muted); background:var(--bg-surface); padding:2px 8px; border-radius:4px; border:1px solid var(--border-subtle);">
                                    <?= $isBn ? 'রোল ডিফল্ট' : 'Standard' ?>
                                </span>
                            <?php endif; ?>
                        </td>

                        <!-- Status -->
                        <td style="padding:12px 14px; text-align:center;">
                            <span style="display:inline-flex; align-items:center; gap:4px; font-size:0.75rem; color:#15803d; background:#dcfce7; font-weight:800; padding:2px 8px; border-radius:var(--radius-full); border:1px solid #86efac;">
                                ● <?= $isBn ? 'সক্রিয়' : 'Active' ?>
                            </span>
                        </td>

                        <!-- Super Admin Actions -->
                        <td style="padding:12px 16px; text-align:right;">
                            <?php if ($canAssignRoles): ?>
                                <button type="button" 
                                        onclick="openAssignmentModal(<?= $userJson ?>)"
                                        class="btn btn-sm btn-primary" 
                                        style="font-size:0.78rem; font-weight:800; padding:5px 12px; display:inline-flex; align-items:center; gap:6px;"
                                        title="<?= $isBn ? 'সুপার অ্যাডমিন হিসেবে রোল, কাজের পরিধি ও বিশেষ অনুমতি নির্ধারণ করুন' : 'Assign role, tasks scope & allowances' ?>">
                                    <span>⚙️</span>
                                    <span><?= $isBn ? 'দায়িত্ব ও ক্ষমতা নির্ধারণ' : 'Assign Role & Tasks' ?></span>
                                </button>
                            <?php else: ?>
                                <span style="font-size:0.75rem; color:#94a3b8; font-style:italic;">
                                    🔒 <?= $isBn ? 'সুপার অ্যাডমিন এক্তিয়ার' : 'Super Admin only' ?>
                                </span>
                            <?php endif; ?>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<!-- ========================================================================= -->
<!-- SUPER ADMIN ROLE & TASK ALLOWANCE ASSIGNMENT MODAL                        -->
<!-- ========================================================================= -->
<div id="superAdminAllowanceModal" style="display:none; position:fixed; inset:0; background:rgba(15, 23, 42, 0.75); z-index:9999; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(4px); overflow-y:auto;">
    <div style="background:#ffffff; border-radius:var(--radius-xl); max-width:680px; width:100%; max-height:92vh; display:flex; flex-direction:column; box-shadow:0 25px 50px -12px rgba(0,0,0,0.5); border:1px solid var(--border-medium); overflow:hidden;">
        
        <!-- Modal Header -->
        <div style="background:linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); color:#ffffff; padding:18px 24px; display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center; gap:12px;">
                <span style="font-size:1.6rem;">👑</span>
                <div>
                    <div style="font-size:0.72rem; text-transform:uppercase; font-weight:800; color:#bfdbfe; letter-spacing:0.5px;">
                        Super Admin Master Governance
                    </div>
                    <h3 style="margin:2px 0 0; font-size:1.15rem; font-weight:800; color:#ffffff;">
                        <?= $isBn ? 'কর্মকর্তার ভূমিকা, কার্যপরিধি ও কাজের অনুমতি নির্ধারণ' : 'Assign Role, Tasks Scope & Allowances' ?>
                    </h3>
                </div>
            </div>
            <button type="button" onclick="closeAssignmentModal()" style="background:none; border:none; font-size:1.6rem; color:#ffffff; cursor:pointer; line-height:1;">&times;</button>
        </div>

        <!-- Target Officer Card -->
        <div style="padding:14px 24px; background:#f8fafc; border-bottom:1px solid var(--border-subtle); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
            <div style="display:flex; align-items:center; gap:10px;">
                <img id="modalOfficerAvatar" src="" alt="Avatar" style="width:42px; height:42px; border-radius:8px; object-fit:cover; border:2px solid #cbd5e1;">
                <div>
                    <div id="modalOfficerName" style="font-weight:800; font-size:1rem; color:var(--primary-deep);"></div>
                    <div id="modalOfficerDesignation" style="font-size:0.8rem; color:#1e40af; font-weight:700;"></div>
                </div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase;"><?= $isBn ? 'অ্যাকাউন্ট আইডি:' : 'Account ID:' ?></div>
                <div id="modalOfficerId" style="font-family:monospace; font-weight:700; color:#334155; font-size:0.85rem;"></div>
            </div>
        </div>

        <!-- Modal Form Body (Scrollable) -->
        <form id="allowanceForm" action="<?= url('/admin/users/assign-role', $currentLocale) ?>" method="POST" style="overflow-y:auto; padding:20px 24px; margin:0; flex:1;">
            <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>
            <input type="hidden" id="modalTargetUserId" name="target_user_id" value="">

            <!-- 1. Select Role -->
            <div style="margin-bottom:18px;">
                <label style="display:block; font-size:0.86rem; font-weight:800; color:var(--primary-deep); margin-bottom:6px;">
                    <?= $isBn ? '১. নির্ধারিত প্রশাসনিক ভূমিকা (Role) *' : '1. Assigned Administrative Role *' ?>
                </label>
                <select id="modalRoleSelect" name="new_role" required style="width:100%; padding:9px 12px; border:1px solid var(--border-medium); border-radius:var(--radius-md); font-size:0.9rem; font-weight:700; color:var(--primary-deep); background:#ffffff;">
                    <?php foreach ($roles as $opt): ?>
                        <option value="<?= e($opt['id']) ?>">
                            <?= e($isBn ? $opt['name_bn'] : $opt['name_en']) ?> (Level: <?= $opt['level'] ?>) - <?= e($opt['access_level'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div style="font-size:0.74rem; color:var(--text-muted); margin-top:4px;">
                    <?= $isBn 
                        ? 'ভূমিকা অনুযায়ী কর্মকর্তা উক্ত পদের মৌলিক এক্তিয়ার লাভ করবেন।' 
                        : 'The officer automatically inherits foundational authorities of this role.' ?>
                </div>
            </div>

            <!-- 2. Scope & Departmental Tasks -->
            <div style="margin-bottom:18px;">
                <label style="display:block; font-size:0.86rem; font-weight:800; color:var(--primary-deep); margin-bottom:6px;">
                    <?= $isBn ? '২. কাজের বিভাগ ও কার্যপরিধি (Assigned Tasks & Department) *' : '2. Departmental Scope & Duties *' ?>
                </label>
                <input type="text" id="modalScopeInput" name="scope" required placeholder="<?= $isBn ? 'উদাঃ সাহিত্য ও প্রকাশনা বিভাগ, হিসাব শাখা' : 'e.g. Literature, Accounts, Public Relations' ?>" style="width:100%; padding:9px 12px; border:1px solid var(--border-medium); border-radius:var(--radius-md); font-size:0.88rem; font-weight:600;">
                
                <!-- Quick Department Preset Chips -->
                <div style="margin-top:8px;">
                    <div style="font-size:0.72rem; color:var(--text-muted); font-weight:700; margin-bottom:4px;"><?= $isBn ? 'দ্রুত বিভাগ নির্বাচন করুন:' : 'Quick Presets:' ?></div>
                    <div style="display:flex; gap:6px; flex-wrap:wrap;">
                        <button type="button" onclick="setPresetScope('সাহিত্য ও প্রকাশনা বিভাগ')" class="btn btn-xs btn-ghost" style="border:1px solid #cbd5e1; font-size:0.72rem; padding:2px 8px;">
                            📚 সাহিত্য ও প্রকাশনা
                        </button>
                        <button type="button" onclick="setPresetScope('হিসাব ও অডিট শাখা')" class="btn btn-xs btn-ghost" style="border:1px solid #cbd5e1; font-size:0.72rem; padding:2px 8px;">
                            🪙 হিসাব ও অডিট
                        </button>
                        <button type="button" onclick="setPresetScope('ডিজিটাল লাইব্রেরি ও আর্কাইভ')" class="btn btn-xs btn-ghost" style="border:1px solid #cbd5e1; font-size:0.72rem; padding:2px 8px;">
                            📖 ডিজিটাল লাইব্রেরি
                        </button>
                        <button type="button" onclick="setPresetScope('মেম্বারশিপ ও সদস্য যোগাযোগ')" class="btn btn-xs btn-ghost" style="border:1px solid #cbd5e1; font-size:0.72rem; padding:2px 8px;">
                            🪪 মেম্বারশিপ ডেস্ক
                        </button>
                        <button type="button" onclick="setPresetScope('সেবামূলক প্রকল্প ও ভলান্টিয়ার্স')" class="btn btn-xs btn-ghost" style="border:1px solid #cbd5e1; font-size:0.72rem; padding:2px 8px;">
                            🤝 সেবা প্রকল্প
                        </button>
                        <button type="button" onclick="setPresetScope('সার্বিক প্রাতিষ্ঠানিক প্রশাসন')" class="btn btn-xs btn-ghost" style="border:1px solid #cbd5e1; font-size:0.72rem; padding:2px 8px;">
                            👑 সার্বিক প্রশাসন
                        </button>
                    </div>
                </div>
            </div>

            <!-- 3. Fine-Grained Modular Allowances (Checkbox Grid) -->
            <div style="margin-bottom:14px; border-top:1px dashed var(--border-medium); padding-top:16px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                    <label style="font-size:0.86rem; font-weight:800; color:var(--primary-deep); margin:0;">
                        <?= $isBn ? '৩. বিশেষ কাজের অনুমতি ও মডুলার এলাউন্স (Task Allowances)' : '3. Module Tasks & Allowances' ?>
                    </label>
                    <span style="font-size:0.72rem; background:#eff6ff; color:#1e40af; padding:2px 8px; border-radius:4px; font-weight:700;">
                        Super Admin Custom Grants
                    </span>
                </div>
                <p style="font-size:0.78rem; color:var(--text-muted); margin-bottom:12px; line-height:1.4;">
                    <?= $isBn 
                        ? 'নির্বাচিত ভূমিকার অতিরিক্ত হিসেবে এই কর্মকর্তার অ্যাকাউন্টে যে যে নির্দিষ্ট কাজের বিশেষ অনুমতি বরাদ্দ (Allowance) দিতে চান, তা নির্বাচন করুন:' 
                        : 'Grant custom task allowances in addition to their default role capabilities:' ?>
                </p>

                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:12px;">
                    <?php foreach ($allowanceModules as $modKey => $mod): ?>
                        <div style="background:#f8fafc; border:1px solid var(--border-medium); border-radius:8px; padding:10px 12px;">
                            <div style="display:flex; align-items:center; gap:6px; font-weight:800; font-size:0.82rem; color:var(--primary-deep); margin-bottom:8px; border-bottom:1px solid #e2e8f0; padding-bottom:4px;">
                                <span><?= $mod['icon'] ?></span>
                                <span><?= e($isBn ? $mod['title_bn'] : $mod['title_en']) ?></span>
                            </div>
                            <div style="display:flex; flex-direction:column; gap:6px;">
                                <?php foreach ($mod['permissions'] as $p): ?>
                                    <label style="display:flex; align-items:flex-start; gap:8px; font-size:0.78rem; color:#334155; cursor:pointer; user-select:none;">
                                        <input type="checkbox" name="custom_permissions[]" value="<?= e($p['id']) ?>" class="allowance-checkbox" style="margin-top:2px;">
                                        <div>
                                            <strong style="color:var(--text-primary);"><?= e($isBn ? $p['label_bn'] : $p['label_en']) ?></strong>
                                            <div style="font-size:0.68rem; color:#64748b; font-family:monospace;"><?= e($p['id']) ?></div>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Modal Footer -->
            <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border-subtle); padding-top:16px; margin-top:16px;">
                <button type="button" onclick="closeAssignmentModal()" class="btn btn-sm btn-ghost" style="border:1px solid var(--border-medium);">
                    <?= $isBn ? 'বাতিল' : 'Cancel' ?>
                </button>
                <button type="submit" class="btn btn-sm btn-primary" style="background:#1e40af; border-color:#1e40af; font-weight:800; padding:6px 18px;">
                    ✓ <?= $isBn ? 'দায়িত্ব ও ক্ষমতা সংরক্ষণ করুন' : 'Save Role & Allowances' ?>
                </button>
            </div>
        </form>

    </div>
</div>

<script>
function openAssignmentModal(user) {
    document.getElementById('modalTargetUserId').value = user.id || '';
    document.getElementById('modalOfficerName').textContent = user.name_bn || user.name_en || '';
    document.getElementById('modalOfficerDesignation').textContent = user.designation_bn || user.designation_en || '';
    document.getElementById('modalOfficerId').textContent = user.id || '';
    
    // Set Avatar
    const avatarImg = document.getElementById('modalOfficerAvatar');
    if (user.avatar) {
        avatarImg.src = user.avatar.startsWith('http') ? user.avatar : ('/' + user.avatar);
    } else {
        avatarImg.src = 'https://api.dicebear.com/7.x/bottts/svg?seed=' + user.id;
    }

    // Set Role in dropdown
    const roleSelect = document.getElementById('modalRoleSelect');
    if (roleSelect) {
        roleSelect.value = user.role || 'moderator';
    }

    // Set Scope
    document.getElementById('modalScopeInput').value = user.scope || '';

    // Clear and check custom permission checkboxes
    const userPerms = Array.isArray(user.custom_permissions) ? user.custom_permissions : [];
    const checkboxes = document.querySelectorAll('.allowance-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = userPerms.includes(cb.value);
    });

    const modal = document.getElementById('superAdminAllowanceModal');
    modal.style.display = 'flex';
}

function closeAssignmentModal() {
    const modal = document.getElementById('superAdminAllowanceModal');
    modal.style.display = 'none';
}

function setPresetScope(text) {
    document.getElementById('modalScopeInput').value = text;
}

// Close modal when clicking outside
window.addEventListener('click', function(e) {
    const modal = document.getElementById('superAdminAllowanceModal');
    if (e.target === modal) {
        closeAssignmentModal();
    }
});

// Close modal on ESC key
window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAssignmentModal();
    }
});
</script>
