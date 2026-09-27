<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$currentUser = \App\Services\AuthService::getCurrentUser();
$currentRole = \App\Services\RbacService::getRole($currentUser['role'] ?? '');
$rolesCount = count(\App\Services\RbacService::getRoles());
$usersCount = count(\App\Services\RbacService::getUsers());
$libraryRequests = \App\Services\LibraryService::getRequests();
$pendingRequestsCount = count(array_filter($libraryRequests, fn($r) => $r['status'] === 'pending'));
$recentLogs = \App\Services\AuditService::getLogs(6);
?>

<div class="admin-page-header">
    <div>
        <h2 class="admin-page-title"><?= $isBn ? 'প্রশাসনিক ড্যাশবোর্ড ও ওভারভিউ' : 'Admin Control Overview' ?></h2>
        <p class="admin-page-desc">
            <?= $isBn 
                ? 'এসপিএস-এর ভূমিকা-ভিত্তিক (RBAC) প্রশাসনিক নিয়ন্ত্রণকক্ষে স্বাগতম।' 
                : 'Welcome to the SPS Role-Based Administrative Control Center.' ?>
        </p>
    </div>

    <!-- Active Profile Pill -->
    <div style="background:#ffffff; border:1px solid var(--border-medium); padding:8px 16px; border-radius:var(--radius-md); box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:var(--space-sm);">
        <div style="font-size:1.8rem;">👤</div>
        <div>
            <div style="font-weight:700; color:var(--primary-deep); font-size:0.95rem;">
                <?= e($isBn ? $currentUser['name_bn'] : $currentUser['name_en']) ?>
            </div>
            <div style="font-size:0.8rem; color:var(--text-muted);">
                <?= $isBn ? 'বর্তমান ভূমিকা:' : 'Active Role:' ?> 
                <span class="role-badge badge-<?= e($currentUser['role']) ?>">
                    <?= e($isBn ? ($currentRole['name_bn'] ?? $currentUser['role']) : ($currentRole['name_en'] ?? $currentUser['role'])) ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Architecture & Security Principle Banner -->
<div style="background:linear-gradient(135deg, #0d1b2a, #1b263b); color:#ffffff; padding:var(--space-lg) var(--space-xl); border-radius:var(--radius-lg); margin-bottom:var(--space-2xl); border-left:5px solid var(--accent-saffron); box-shadow:var(--shadow-md);">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:var(--space-md);">
        <div>
            <h3 style="color:#fcd34d; font-size:1.15rem; margin:0 0 var(--space-xs); font-weight:700;">
                🛡️ <?= $isBn ? 'মূল নীতি: "Admin ≠ Unlimited Access" (ন্যূনতম প্রয়োজনীয় পারমিশন নীতি)' : 'Core Principle: "Admin ≠ Unlimited Access" (Least Privilege Model)' ?>
            </h3>
            <p style="color:#cbd5e1; font-size:0.88rem; margin:0; max-width:850px; line-height:1.6;">
                <?= $isBn 
                    ? 'এসপিএস-এ প্রশাসক কেবল একটি একক সাধারণ ভূমিকা নয়; বরং ১২টি বিশেষায়িত দায়িত্ব-নির্দিষ্ট ভূমিকা (Roles) এবং রিসোর্স-অ্যাকশন ভিত্তিক সার্ভার-সাইড পারমিশন দ্বারা প্রতিটি অপারেশন যাচাই করা হয়। ফলে দৈনন্দিন কাজে স্বচ্ছতা বজায় থাকে এবং কোনো একক অ্যাডমিন অপরিমিত ক্ষমতার অপব্যবহার করতে পারে না।' 
                    : 'In SPS, admin is not a single omnibus role. 12 dedicated roles operate under server-side RBAC permissions and strict least privilege rules to ensure total institutional accountability, segregation of duties, and audit trail integrity.' ?>
            </p>
        </div>
        <div style="background:rgba(255,255,255,0.08); padding:8px 14px; border-radius:var(--radius-md); text-align:center; border:1px solid rgba(255,255,255,0.15);">
            <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px; color:#94a3b8;"><?= $isBn ? 'নিরাপত্তা স্তর' : 'Security Level' ?></div>
            <div style="font-size:1.1rem; font-weight:800; color:#38bdf8;"><?= e($currentRole['access_level'] ?? 'Verified') ?></div>
        </div>
    </div>
</div>

<!-- Dynamic Role-Specific KPI Cards -->
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:var(--space-lg); margin-bottom:var(--space-2xl);">
    
    <!-- Roles Metric -->
    <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-md); padding:var(--space-lg); box-shadow:var(--shadow-sm); border-top:4px solid #3b82f6;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div style="font-size:0.85rem; font-weight:600; color:var(--text-muted);"><?= $isBn ? 'মোট সিস্টেম ভূমিকা' : 'System Roles' ?></div>
            <span style="font-size:1.3rem;">🛡️</span>
        </div>
        <div style="font-size:2rem; font-weight:800; color:var(--primary-deep); margin:var(--space-xs) 0;"><?= $rolesCount ?></div>
        <div style="font-size:0.8rem; color:var(--text-muted);">
            <a href="<?= url('/admin/roles', $currentLocale) ?>" style="color:#2563eb; text-decoration:none; font-weight:600;">
                <?= $isBn ? 'পারমিশন ম্যাট্রিক্স দেখুন →' : 'View RBAC Matrix →' ?>
            </a>
        </div>
    </div>

    <!-- Admin Accounts -->
    <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-md); padding:var(--space-lg); box-shadow:var(--shadow-sm); border-top:4px solid #10b981;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div style="font-size:0.85rem; font-weight:600; color:var(--text-muted);"><?= $isBn ? 'সক্রিয় অ্যাডমিন ইউজার' : 'Active Admin Users' ?></div>
            <span style="font-size:1.3rem;">👥</span>
        </div>
        <div style="font-size:2rem; font-weight:800; color:var(--primary-deep); margin:var(--space-xs) 0;"><?= $usersCount ?></div>
        <div style="font-size:0.8rem; color:var(--text-muted);">
            <?php if (\App\Services\AuthService::can('users.view')): ?>
                <a href="<?= url('/admin/users', $currentLocale) ?>" style="color:#059669; text-decoration:none; font-weight:600;">
                    <?= $isBn ? 'ইউজার তালিকা পরিচালনা →' : 'Manage Admin Users →' ?>
                </a>
            <?php else: ?>
                <span style="color:#94a3b8;"><?= $isBn ? 'ভিউ-অনলি সীমাবদ্ধ' : 'View Restricted' ?></span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Library Requests Pending -->
    <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-md); padding:var(--space-lg); box-shadow:var(--shadow-sm); border-top:4px solid #f59e0b;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div style="font-size:0.85rem; font-weight:600; color:var(--text-muted);"><?= $isBn ? 'অপেক্ষমাণ পাঠাধিকার আবেদন' : 'Pending Reader Requests' ?></div>
            <span style="font-size:1.3rem;">📖</span>
        </div>
        <div style="font-size:2rem; font-weight:800; color:#d97706; margin:var(--space-xs) 0;"><?= $pendingRequestsCount ?></div>
        <div style="font-size:0.8rem; color:var(--text-muted);">
            <?php if (\App\Services\AuthService::can('library.view')): ?>
                <a href="<?= url('/admin/library', $currentLocale) ?>" style="color:#d97706; text-decoration:none; font-weight:600;">
                    <?= $isBn ? 'আবেদনসমূহ পরীক্ষা করুন →' : 'Review Requests Inbox →' ?>
                </a>
            <?php else: ?>
                <span style="color:#94a3b8;"><?= $isBn ? 'গ্রন্থাগার ম্যানেজারের অধীন' : 'Managed by Library' ?></span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Financial Safeguard Status -->
    <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-md); padding:var(--space-lg); box-shadow:var(--shadow-sm); border-top:4px solid #8b5cf6;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div style="font-size:0.85rem; font-weight:600; color:var(--text-muted);"><?= $isBn ? 'আর্থিক নিরাপত্তা ব্যবস্থা' : 'Financial Controls' ?></div>
            <span style="font-size:1.3rem;">⚖️</span>
        </div>
        <div style="font-size:1.15rem; font-weight:700; color:var(--primary-deep); margin:var(--space-sm) 0 4px;">
            <?= $isBn ? 'মেকার-চেকার সক্রিয়' : 'Maker-Checker Active' ?>
        </div>
        <div style="font-size:0.8rem; color:#6b7280;">
            <?= $isBn ? 'গুগল শিটস মিরর সিঙ্ক চালু' : 'Google Sheets Mirror synced' ?>
        </div>
    </div>
</div>

<!-- Two-Column Section: Role Privileges vs Recent Audit Logs -->
<div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-xl); align-items:start;">

    <!-- Left: Current Role Powers & Restrictions -->
    <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); padding:var(--space-xl); box-shadow:var(--shadow-sm);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-md); border-bottom:1px solid var(--border-subtle); padding-bottom:var(--space-sm);">
            <h3 style="font-size:1.15rem; font-weight:700; color:var(--primary-deep); margin:0;">
                🔍 <?= $isBn ? 'আপনার সক্রিয় ভূমিকার ক্ষমতা ও সীমা' : 'Active Role Powers & Boundary' ?>
            </h3>
            <span class="role-badge badge-<?= e($currentUser['role']) ?>">
                <?= e($currentUser['role']) ?>
            </span>
        </div>

        <p style="font-size:0.88rem; color:var(--text-muted); line-height:1.6; margin-bottom:var(--space-md);">
            <?= e($isBn ? $currentRole['description_bn'] : $currentRole['description_en']) ?>
        </p>

        <!-- Granted Permissions Preview -->
        <div style="margin-bottom:var(--space-md);">
            <div style="font-size:0.82rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px;">
                <?= $isBn ? 'অনুমোদিত রিসোর্স পারমিশনসমূহ:' : 'Granted Resource Permissions:' ?>
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:6px;">
                <?php 
                $perms = \App\Services\RbacService::getRolePermissions($currentUser['role']);
                $displayPerms = array_slice($perms, 0, 10);
                foreach ($displayPerms as $p): 
                ?>
                    <span style="font-size:0.75rem; background:#f1f5f9; color:#1e293b; padding:3px 8px; border-radius:var(--radius-sm); border:1px solid #cbd5e1; font-family:monospace;">
                        <?= e($p) ?>
                    </span>
                <?php endforeach; ?>
                <?php if (count($perms) > 10): ?>
                    <span style="font-size:0.75rem; background:#e2e8f0; color:#475569; padding:3px 8px; border-radius:var(--radius-sm); font-weight:600;">
                        +<?= count($perms) - 10 ?> <?= $isBn ? 'অন্যান্য...' : 'more...' ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div style="background:#f8fafc; border:1px solid #e2e8f0; padding:12px; border-radius:var(--radius-sm); font-size:0.82rem; color:#475569;">
            <strong>💡 <?= $isBn ? 'পরামর্শ:' : 'Tip:' ?></strong> 
            <?= $isBn 
                ? 'উপরের হেডার থেকে "রোল সিমুলেটর" ব্যবহার করে অন্য কোনো অ্যাকাউন্টে সুইচ করলে বাম পাশের সাইডবার ও বাটনগুলো স্বয়ংক্রিয়ভাবে পরিবর্তিত হয়ে যাবে।' 
                : 'Use the Role Simulator dropdown in the top bar to switch to another role and see the sidebar and permissions instantly adapt.' ?>
        </div>
    </div>

    <!-- Right: Real-time Immutable Audit Logs -->
    <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); padding:var(--space-xl); box-shadow:var(--shadow-sm);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-md); border-bottom:1px solid var(--border-subtle); padding-bottom:var(--space-sm);">
            <h3 style="font-size:1.15rem; font-weight:700; color:var(--primary-deep); margin:0;">
                📜 <?= $isBn ? 'সাম্প্রতিক অডিট লগ' : 'Recent Audit Trail' ?>
            </h3>
            <?php if (\App\Services\AuthService::can('audit_logs.view')): ?>
                <a href="<?= url('/admin/audit-logs', $currentLocale) ?>" style="font-size:0.82rem; color:#2563eb; text-decoration:none; font-weight:600;">
                    <?= $isBn ? 'সব দেখুন →' : 'View All →' ?>
                </a>
            <?php endif; ?>
        </div>

        <div style="display:flex; flex-direction:column; gap:var(--space-sm);">
            <?php foreach ($recentLogs as $log): ?>
                <div style="border:1px solid var(--border-subtle); border-radius:var(--radius-sm); padding:10px 14px; background:#ffffff; font-size:0.84rem;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                        <span style="font-weight:700; color:var(--primary-deep);">
                            <?= e($log['user_name']) ?> 
                            <span style="font-size:0.75rem; color:var(--text-muted); font-weight:normal;">(<?= e($log['role']) ?>)</span>
                        </span>
                        <span style="font-size:0.74rem; color:var(--text-muted); font-family:monospace;">
                            <?= e($log['created_at']) ?>
                        </span>
                    </div>
                    <div style="display:flex; align-items:center; gap:6px; margin-bottom:3px;">
                        <span style="font-size:0.75rem; font-family:monospace; background:#f1f5f9; padding:2px 6px; border-radius:3px; color:#0f172a; font-weight:600;">
                            <?= e($log['action']) ?>
                        </span>
                        <span style="color:var(--text-main); font-size:0.82rem;">
                            <?= e($log['target_name'] ?? $log['target_id'] ?? '') ?>
                        </span>
                    </div>
                    <?php if (!empty($log['notes'])): ?>
                        <div style="font-size:0.78rem; color:#64748b; font-style:italic;">
                            "<?= e($log['notes']) ?>"
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
