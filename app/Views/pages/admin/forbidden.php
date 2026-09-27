<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$currentUser = \App\Services\AuthService::getCurrentUser();
$currentRole = \App\Services\RbacService::getRole($currentUser['role'] ?? '');
?>

<div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); padding:var(--space-3xl); box-shadow:var(--shadow-md); text-align:center; max-width:680px; margin:var(--space-2xl) auto;">
    <div style="font-size:3.5rem; margin-bottom:var(--space-md);">🚫</div>

    <h2 style="font-size:1.8rem; font-weight:800; color:#b91c1c; margin:0 0 var(--space-xs);">
        <?= $isBn ? 'প্রবেশাধিকার সংরক্ষিত (403 Forbidden)' : 'Access Restricted (403 Forbidden)' ?>
    </h2>

    <p style="font-size:1rem; font-weight:600; color:var(--text-main); margin-bottom:var(--space-md);">
        <?= $isBn 
            ? 'আপনার বর্তমান ভূমিকা এই বিভাগ বা কার্যক্রমে প্রবেশের জন্য অনুমোদিত নয়।' 
            : 'Your active role lacks the necessary authorization for this module.' ?>
    </p>

    <!-- Details Box -->
    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:var(--radius-md); padding:16px; margin-bottom:var(--space-xl); text-align:left; font-size:0.88rem;">
        <div style="margin-bottom:6px;">
            <strong><?= $isBn ? 'লগইনকারী:' : 'Current User:' ?></strong> 
            <?= e($isBn ? $currentUser['name_bn'] : $currentUser['name_en']) ?>
        </div>
        <div style="margin-bottom:6px;">
            <strong><?= $isBn ? 'বর্তমান ভূমিকা:' : 'Active Role:' ?></strong> 
            <span class="role-badge badge-<?= e($currentUser['role']) ?>">
                <?= e($isBn ? ($currentRole['name_bn'] ?? $currentUser['role']) : ($currentRole['name_en'] ?? $currentUser['role'])) ?>
            </span>
        </div>
        <?php if (!empty($requiredPermission)): ?>
            <div>
                <strong><?= $isBn ? 'প্রয়োজনীয় পারমিশন টোকেন:' : 'Required Permission Token:' ?></strong> 
                <code style="background:#fee2e2; color:#991b1b; padding:2px 6px; border-radius:4px; font-weight:700;">
                    <?= e($requiredPermission) ?>
                </code>
            </div>
        <?php endif; ?>
    </div>

    <div style="font-size:0.86rem; color:var(--text-muted); margin-bottom:var(--space-xl); line-height:1.6;">
        💡 <strong><?= $isBn ? 'কেন এই সীমাবদ্ধতা?' : 'Why this restriction?' ?></strong><br>
        <?= $isBn 
            ? 'এসপিএস-এর "Admin ≠ Unlimited Access" ও ন্যূনতম পারমিশন নীতির কারণে দায়িত্ব-নির্দিষ্ট ভূমিকা ব্যতীত অন্য কেউ সংবেদনশীল ডেটা দেখতে বা পরিবর্তন করতে পারে না।' 
            : 'SPS strictly enforces the principle of least privilege. Unauthorized administrative roles cannot view or manipulate modules outside their jurisdiction.' ?>
    </div>

    <div style="display:flex; justify-content:center; gap:var(--space-md);">
        <a href="<?= url('/admin', $currentLocale) ?>" class="btn btn-primary">
            ← <?= $isBn ? 'ড্যাশবোর্ডে ফিরে যান' : 'Return to Dashboard' ?>
        </a>
    </div>
</div>
