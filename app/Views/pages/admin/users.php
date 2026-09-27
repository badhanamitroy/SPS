<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$users = \App\Services\RbacService::getUsers();
$roles = \App\Services\RbacService::getRoles();
$currentUser = \App\Services\AuthService::getCurrentUser();
$canAssignRoles = \App\Services\AuthService::can('users.manage_roles');
?>

<div class="admin-page-header">
    <div>
        <h2 class="admin-page-title"><?= $isBn ? 'প্রশাসনিক ব্যবহারকারী ও ভূমিকা ব্যবস্থাপনা' : 'Admin Users & Role Assignment' ?></h2>
        <p class="admin-page-desc">
            <?= $isBn 
                ? 'এসপিএস পরিচালনা পর্ষদ ও কর্মকর্তা একাউন্টসমূহ এবং তাদের দায়িত্ব বণ্টন।' 
                : 'Manage administrator accounts, designations, and assign operational roles.' ?>
        </p>
    </div>
</div>

<!-- Escalation Protection Rule Banner -->
<div style="background:#fffbeb; border:1px solid #fef3c7; border-left:4px solid #f59e0b; padding:14px 18px; border-radius:var(--radius-md); margin-bottom:var(--space-2xl); font-size:0.86rem; color:#92400e;">
    <strong>🔒 <?= $isBn ? 'নিরাপত্তা নীতিমালা (Role Escalation Protection):' : 'Security Rule (Role Escalation Protection):' ?></strong>
    <?= $isBn 
        ? 'কোনো অ্যাডমিনিস্ট্রেটর নিজের ভূমিকা স্বয়ং পরিবর্তন করতে পারে না। সুপার অ্যাডমিনিস্ট্রেটর ব্যতীত অন্য কেউ কাউকে সুপার অ্যাডমিন পদে উন্নীত করতে পারে না। প্রতিটি পরিবর্তনের তথ্য অপরিবর্তনীয় অডিট লগে সংরক্ষিত হয়।' 
        : 'Administrators cannot alter their own roles or escalate anyone to Super Administrator. All assignment changes are immutably logged with audit trails.' ?>
</div>

<div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); padding:var(--space-xl); box-shadow:var(--shadow-sm); overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse; font-size:0.88rem;">
        <thead>
            <tr style="background:#f8fafc; border-bottom:2px solid var(--border-medium); text-align:left;">
                <th style="padding:12px;"><?= $isBn ? 'অ্যাডমিন কর্মকর্তা' : 'Officer' ?></th>
                <th style="padding:12px;"><?= $isBn ? 'ইমেইল ও গুগল আইডি' : 'Email & ID' ?></th>
                <th style="padding:12px;"><?= $isBn ? 'বর্তমান ভূমিকা' : 'Assigned Role' ?></th>
                <th style="padding:12px;"><?= $isBn ? 'স্ট্যাটাস' : 'Status' ?></th>
                <th style="padding:12px; text-align:right;"><?= $isBn ? 'ভূমিকা হালনাগাদ' : 'Action' ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): 
                $r = $roles[$u['role']] ?? null;
            ?>
                <tr style="border-bottom:1px solid var(--border-subtle);">
                    <td style="padding:12px;">
                        <div style="display:flex; align-items:center; gap:var(--space-sm);">
                            <img src="<?= e($u['avatar']) ?>" alt="Avatar" style="width:36px; height:36px; border-radius:50%; background:#f1f5f9;">
                            <div>
                                <div style="font-weight:700; color:var(--primary-deep);">
                                    <?= e($isBn ? $u['name_bn'] : $u['name_en']) ?>
                                    <?php if ($u['id'] === $currentUser['id']): ?>
                                        <span style="font-size:0.72rem; background:#dcfce7; color:#15803d; padding:2px 6px; border-radius:4px; font-weight:700; margin-left:4px;">
                                            <?= $isBn ? 'আপনি (বর্তমান লগইন)' : 'You (Current)' ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div style="font-size:0.75rem; color:#64748b;">
                                    ID: <?= e($u['id']) ?> • <?= $isBn ? 'নিযুক্তকারী:' : 'Assigned by:' ?> <?= e($u['assigned_by']) ?>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px; font-family:monospace; color:#334155;">
                        <?= e($u['email']) ?>
                    </td>
                    <td style="padding:12px;">
                        <span class="role-badge badge-<?= e($u['role']) ?>">
                            <?= e($isBn ? ($r['name_bn'] ?? $u['role']) : ($r['name_en'] ?? $u['role'])) ?>
                        </span>
                    </td>
                    <td style="padding:12px;">
                        <span style="display:inline-flex; align-items:center; gap:4px; font-size:0.8rem; color:#15803d; font-weight:600;">
                            ● <?= $isBn ? 'সক্রিয়' : 'Active' ?>
                        </span>
                    </td>
                    <td style="padding:12px; text-align:right;">
                        <?php if ($canAssignRoles): ?>
                            <form action="<?= url('/admin/users/assign-role', $currentLocale) ?>" method="POST" style="display:inline-flex; align-items:center; gap:6px;">
                                <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>
                                <input type="hidden" name="target_user_id" value="<?= e($u['id']) ?>">
                                <select name="new_role" style="padding:4px 8px; border:1px solid var(--border-medium); border-radius:var(--radius-sm); font-size:0.8rem;">
                                    <?php foreach ($roles as $optRole): ?>
                                        <option value="<?= e($optRole['id']) ?>" <?= $optRole['id'] === $u['role'] ? 'selected' : '' ?>>
                                            <?= e($isBn ? $optRole['name_bn'] : $optRole['name_en']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-sm btn-ghost" style="border:1px solid var(--border-medium); padding:4px 10px; font-size:0.78rem;">
                                    <?= $isBn ? 'পরিবর্তন' : 'Update' ?>
                                </button>
                            </form>
                        <?php else: ?>
                            <span style="font-size:0.75rem; color:#94a3b8; font-style:italic;">
                                <?= $isBn ? 'শুধু সুপার অ্যাডমিন পরিবর্তন করতে পারেন' : 'Super Admin only' ?>
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
