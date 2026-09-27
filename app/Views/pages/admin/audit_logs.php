<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$logs = \App\Services\AuditService::all();
?>

<div class="admin-page-header">
    <div>
        <h2 class="admin-page-title"><?= $isBn ? 'সিস্টেম নিরীক্ষা ও অপরিবর্তনীয় অডিট লগ' : 'System Audit Trail & Governance' ?></h2>
        <p class="admin-page-desc">
            <?= $isBn 
                ? 'এসপিএস প্ল্যাটফর্মের প্রতিটি প্রশাসনিক কার্যক্রম, অনুমোদন ও পরিবর্তনের নির্ভরযোগ্য ক্রিপ্টোগ্রাফিক রেকর্ড।' 
                : 'Immutable audit trail logging every administrative authorization, modification, and sensitive event.' ?>
        </p>
    </div>
</div>

<!-- Immutability Security Banner -->
<div style="background:#eff6ff; border:1px solid #bfdbfe; border-left:4px solid #2563eb; padding:14px 18px; border-radius:var(--radius-md); margin-bottom:var(--space-2xl); font-size:0.86rem; color:#1e40af;">
    <strong>🔒 <?= $isBn ? 'অপরিবর্তনীয়তার নীতি (Append-Only Guarantee):' : 'Immutability Guarantee (Append-Only):' ?></strong>
    <?= $isBn 
        ? 'এই অডিট লগের কোনো এন্ট্রি ব্যাকডেটেড পরিবর্তন বা মোছা সম্পূর্ণ অসম্ভব। সুপার অ্যাডমিনিস্ট্রেটর সহ কোনো ব্যবহারকারীরই অডিট লগ মোছার অনুমতি নেই।' 
        : 'Entries are strictly append-only. Hard deletion, truncation, or backdated edits are architecturally blocked.' ?>
</div>

<div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); padding:var(--space-xl); box-shadow:var(--shadow-sm); overflow-x:auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-md);">
        <h3 style="font-size:1.1rem; font-weight:700; color:var(--primary-deep); margin:0;">
            📜 <?= $isBn ? 'সকল রেকর্ড তালিকা' : 'All Audit Entries' ?> (<?= count($logs) ?>)
        </h3>
        <span style="font-size:0.78rem; background:#f1f5f9; color:#475569; padding:4px 8px; border-radius:var(--radius-sm); border:1px solid #cbd5e1;">
            <?= $isBn ? 'সর্বশেষ রেকর্ড প্রথমে প্রদর্শিত' : 'Sorted: Newest First' ?>
        </span>
    </div>

    <table style="width:100%; border-collapse:collapse; font-size:0.84rem;">
        <thead>
            <tr style="background:#0d1b2a; color:#ffffff; text-align:left;">
                <th style="padding:10px 12px;"><?= $isBn ? 'সময় ও আইডি' : 'Timestamp & ID' ?></th>
                <th style="padding:10px 12px;"><?= $isBn ? 'কর্মকর্তা ও ভূমিকা' : 'Actor & Role' ?></th>
                <th style="padding:10px 12px;"><?= $isBn ? 'অ্যাকশন ও রিসোর্স' : 'Action & Resource' ?></th>
                <th style="padding:10px 12px;"><?= $isBn ? 'টার্গেট ও বিবরণ' : 'Target & Notes' ?></th>
                <th style="padding:10px 12px;"><?= $isBn ? 'পরিবর্তন ডিটেইল' : 'Value Changes' ?></th>
                <th style="padding:10px 12px; text-align:right;"><?= $isBn ? 'আইপি' : 'IP' ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr style="border-bottom:1px solid var(--border-subtle); vertical-align:top;">
                    <td style="padding:12px; font-family:monospace; color:#334155;">
                        <div style="font-weight:600;"><?= e($log['created_at']) ?></div>
                        <div style="font-size:0.72rem; color:#94a3b8;"><?= e($log['id']) ?></div>
                    </td>
                    <td style="padding:12px;">
                        <div style="font-weight:700; color:var(--primary-deep);"><?= e($log['user_name']) ?></div>
                        <span class="role-badge badge-<?= e($log['role']) ?>" style="font-size:0.72rem; padding:2px 6px;">
                            <?= e($log['role']) ?>
                        </span>
                    </td>
                    <td style="padding:12px;">
                        <span style="font-family:monospace; background:#f1f5f9; padding:3px 6px; border-radius:3px; font-weight:700; color:#0f172a; font-size:0.78rem;">
                            <?= e($log['action']) ?>
                        </span>
                        <div style="font-size:0.75rem; color:#64748b; margin-top:2px;">[<?= e($log['resource']) ?>]</div>
                    </td>
                    <td style="padding:12px;">
                        <div style="font-weight:600; color:var(--text-main);">
                            <?= e($log['target_name'] ?? $log['target_id'] ?? '—') ?>
                        </div>
                        <?php if (!empty($log['notes'])): ?>
                            <div style="font-size:0.78rem; color:#64748b; font-style:italic; margin-top:2px;">
                                "<?= e($log['notes']) ?>"
                            </div>
                        <?php endif; ?>
                    </td>
                    <td style="padding:12px; font-family:monospace; font-size:0.76rem;">
                        <?php if (!empty($log['old_values']) || !empty($log['new_values'])): ?>
                            <?php if (!empty($log['old_values'])): ?>
                                <div style="color:#b91c1c;">- <?= json_encode($log['old_values'], JSON_UNESCAPED_UNICODE) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($log['new_values'])): ?>
                                <div style="color:#15803d;">+ <?= json_encode($log['new_values'], JSON_UNESCAPED_UNICODE) ?></div>
                            <?php endif; ?>
                        <?php else: ?>
                            <span style="color:#94a3b8;">—</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:12px; text-align:right; font-family:monospace; font-size:0.76rem; color:#64748b;">
                        <?= e($log['ip_address']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
