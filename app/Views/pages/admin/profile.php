<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$currentUser = $currentUser ?? [];
$currentRole = $currentRole ?? [];

$error = \App\Core\Session::getFlash('error');
$success = \App\Core\Session::getFlash('success');
$warning = \App\Core\Session::getFlash('warning');
?>

<div style="max-width: 1040px; margin: 0 auto; padding-bottom: var(--space-3xl);">

    <!-- Header Breadcrumb & Title -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-xl); flex-wrap: wrap; gap: var(--space-md);">
        <div>
            <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 4px;">
                <a href="<?= url('/admin', $currentLocale) ?>" style="color: var(--text-muted); text-decoration: none;"><?= $isBn ? 'ড্যাশবোর্ড' : 'Dashboard' ?></a>
                <span style="margin: 0 6px;">/</span>
                <span style="color: var(--primary-deep); font-weight: 700;"><?= $isBn ? 'আমার প্রোফাইল' : 'My Profile' ?></span>
            </div>
            <h1 style="font-size: 1.65rem; font-weight: 800; color: var(--primary-deep); margin: 0; display: flex; align-items: center; gap: 10px;">
                <span>👤</span>
                <span><?= $isBn ? 'অ্যাডমিন প্রোফাইল ও নিরাপত্তা ব্যবস্থাপনা' : 'Admin Profile & Security Management' ?></span>
            </h1>
        </div>

        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="<?= url('/admin', $currentLocale) ?>" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
                <span>←</span>
                <span><?= $isBn ? 'ড্যাশবোর্ডে ফিরুন' : 'Back to Dashboard' ?></span>
            </a>
        </div>
    </div>

    <!-- Flash Alerts -->
    <?php if (!empty($error)): ?>
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 16px; border-radius: var(--radius-md); margin-bottom: var(--space-lg); font-size: 0.9rem; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 1.2rem;">⚠️</span>
            <div><?= e($error) ?></div>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px 16px; border-radius: var(--radius-md); margin-bottom: var(--space-lg); font-size: 0.9rem; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 1.2rem;">✓</span>
            <div><?= e($success) ?></div>
        </div>
    <?php endif; ?>

    <!-- Two-Column Layout: Left = Governance Identity, Right = Profile & Password Edit Forms -->
    <div style="display: grid; grid-template-columns: 320px 1fr; gap: var(--space-xl); align-items: start;">
        
        <!-- Left Column: Identity & Governance Card -->
        <div style="display: flex; flex-direction: column; gap: var(--space-lg);">
            
            <!-- User Info Card -->
            <div style="background: #ffffff; border: 1px solid var(--border-medium); border-radius: var(--radius-lg); padding: var(--space-xl); box-shadow: var(--shadow-sm); text-align: center;">
                <div style="position: relative; display: inline-block; margin-bottom: var(--space-md);">
                    <?php if (!empty($currentUser['avatar'])): ?>
                        <img src="<?= asset($currentUser['avatar']) ?>" alt="<?= e($currentUser['name_en'] ?? '') ?>" style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 3px solid var(--accent-saffron); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <?php else: ?>
                        <div style="width: 90px; height: 90px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 2.8rem; margin: 0 auto; border: 3px solid var(--border-medium);">
                            👤
                        </div>
                    <?php endif; ?>
                    <span style="position: absolute; bottom: 4px; right: 4px; width: 14px; height: 14px; background: #22c55e; border: 2px solid #ffffff; border-radius: 50%;" title="Active"></span>
                </div>

                <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--primary-deep); margin: 0 0 4px;">
                    <?= e($isBn ? ($currentUser['name_bn'] ?? '') : ($currentUser['name_en'] ?? '')) ?>
                </h3>
                <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 12px;">
                    <?= e($currentUser['email'] ?? '') ?>
                </div>

                <!-- Verified Administrative Role Badge -->
                <div style="margin-bottom: var(--space-md);">
                    <span style="display: inline-flex; align-items: center; gap: 6px; background: #fdf2f8; color: #9d174d; border: 1px solid #fbcfe8; padding: 4px 12px; border-radius: var(--radius-full); font-weight: 800; font-size: 0.82rem;">
                        ★ <?= e($isBn ? ($currentRole['name_bn'] ?? $currentUser['role'] ?? '') : ($currentRole['name_en'] ?? $currentUser['role'] ?? '')) ?>
                    </span>
                </div>

                <div style="border-top: 1px solid var(--border-subtle); padding-top: var(--space-md); text-align: left; font-size: 0.82rem; display: flex; flex-direction: column; gap: 8px;">
                    <div>
                        <span style="color: var(--text-muted);"><?= $isBn ? 'ইউজার আইডি:' : 'User ID:' ?></span>
                        <code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-weight: 700; color: var(--primary-deep); margin-left: 4px;"><?= e($currentUser['id'] ?? '') ?></code>
                    </div>
                    <div>
                        <span style="color: var(--text-muted);"><?= $isBn ? 'দায়িত্বের পরিধি:' : 'Scope:' ?></span>
                        <strong style="color: var(--primary-deep); margin-left: 4px;"><?= e($currentUser['scope'] ?? 'General') ?></strong>
                    </div>
                    <div>
                        <span style="color: var(--text-muted);"><?= $isBn ? 'পদবি:' : 'Designation:' ?></span>
                        <span style="margin-left: 4px;"><?= e($isBn ? ($currentUser['designation_bn'] ?? '') : ($currentUser['designation_en'] ?? '')) ?></span>
                    </div>
                    <?php if (!empty($currentUser['phone'])): ?>
                        <div>
                            <span style="color: var(--text-muted);"><?= $isBn ? 'ফোন:' : 'Phone:' ?></span>
                            <span style="margin-left: 4px;"><?= e($currentUser['phone']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Role Immutability Security Notice -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: var(--radius-md); padding: var(--space-md); font-size: 0.78rem; color: #475569; line-height: 1.5;">
                <div style="font-weight: 800; color: #1e293b; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                    <span>🛡️</span>
                    <span><?= $isBn ? 'নিরাপত্তা ও পদাধিকার নীতি' : 'Security & RBAC Policy' ?></span>
                </div>
                <?= $isBn 
                    ? 'প্রশাসনিক ভূমিকা (Role) ও বিশেষ অনুমতি (Custom Allowances) স্বয়ং পরিবর্তনযোগ্য নয়। এটি প্ল্যাটফর্মের সুপার অ্যাডমিনিস্ট্রেটর কর্তৃক নির্ধারিত হয়।' 
                    : 'Administrative roles and scopes cannot be self-escalated. Managed exclusively by Super Administrator.' ?>
            </div>
        </div>

        <!-- Right Column: Profile Edit & Password Change Form -->
        <div style="background: #ffffff; border: 1px solid var(--border-medium); border-radius: var(--radius-lg); padding: var(--space-xl); box-shadow: var(--shadow-sm);">
            
            <form action="<?= url('/admin/profile', $currentLocale) ?>" method="POST" id="adminProfileForm">
                <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>

                <div style="margin-bottom: var(--space-xl);">
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--primary-deep); margin: 0 0 4px; display: flex; align-items: center; gap: 8px;">
                        <span>📝</span>
                        <span><?= $isBn ? 'ব্যক্তিগত ও যোগাযোগ তথ্য' : 'Personal & Contact Information' ?></span>
                    </h3>
                    <p style="font-size: 0.82rem; color: var(--text-muted); margin: 0 0 var(--space-lg);">
                        <?= $isBn ? 'যে কোনো সময় আপনার নাম, ইমেইল, মোবাইল ও পরিচিতি হালনাগাদ করতে পারেন।' : 'Update your personal details, email, contact phone and bio anytime.' ?>
                    </p>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: var(--space-md); margin-bottom: var(--space-md);">
                        <div>
                            <label for="admin_name_bn" style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                                <?= $isBn ? 'পূর্ণ নাম (বাংলা) *' : 'Full Name (Bengali) *' ?>
                            </label>
                            <input type="text" id="admin_name_bn" name="name_bn" value="<?= e($currentUser['name_bn'] ?? '') ?>" required style="width: 100%; padding: 9px 12px; border: 1px solid var(--border-medium); border-radius: var(--radius-sm); font-size: 0.9rem; box-sizing: border-box;">
                        </div>

                        <div>
                            <label for="admin_name_en" style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                                <?= $isBn ? 'পূর্ণ নাম (ইংরেজি) *' : 'Full Name (English) *' ?>
                            </label>
                            <input type="text" id="admin_name_en" name="name_en" value="<?= e($currentUser['name_en'] ?? '') ?>" required style="width: 100%; padding: 9px 12px; border: 1px solid var(--border-medium); border-radius: var(--radius-sm); font-size: 0.9rem; box-sizing: border-box;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: var(--space-md); margin-bottom: var(--space-md);">
                        <div>
                            <label for="admin_email" style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                                <?= $isBn ? 'অফিসিয়াল ইমেইল *' : 'Official Email *' ?>
                            </label>
                            <input type="email" id="admin_email" name="email" value="<?= e($currentUser['email'] ?? '') ?>" required style="width: 100%; padding: 9px 12px; border: 1px solid var(--border-medium); border-radius: var(--radius-sm); font-size: 0.9rem; box-sizing: border-box;">
                        </div>

                        <div>
                            <label for="admin_phone" style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                                <?= $isBn ? 'মোবাইল নম্বর' : 'Mobile Phone' ?>
                            </label>
                            <input type="text" id="admin_phone" name="phone" value="<?= e($currentUser['phone'] ?? '') ?>" placeholder="+880 17XXXXXXXX" style="width: 100%; padding: 9px 12px; border: 1px solid var(--border-medium); border-radius: var(--radius-sm); font-size: 0.9rem; box-sizing: border-box;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: var(--space-md); margin-bottom: var(--space-md);">
                        <div>
                            <label for="admin_designation_bn" style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                                <?= $isBn ? 'পদবি (বাংলা)' : 'Designation (Bengali)' ?>
                            </label>
                            <input type="text" id="admin_designation_bn" name="designation_bn" value="<?= e($currentUser['designation_bn'] ?? '') ?>" style="width: 100%; padding: 9px 12px; border: 1px solid var(--border-medium); border-radius: var(--radius-sm); font-size: 0.9rem; box-sizing: border-box;">
                        </div>

                        <div>
                            <label for="admin_designation_en" style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                                <?= $isBn ? 'পদবি (ইংরেজি)' : 'Designation (English)' ?>
                            </label>
                            <input type="text" id="admin_designation_en" name="designation_en" value="<?= e($currentUser['designation_en'] ?? '') ?>" style="width: 100%; padding: 9px 12px; border: 1px solid var(--border-medium); border-radius: var(--radius-sm); font-size: 0.9rem; box-sizing: border-box;">
                        </div>
                    </div>

                    <div style="margin-bottom: var(--space-lg);">
                        <label for="admin_bio" style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                            <?= $isBn ? 'সংক্ষিপ্ত পরিচিতি ও দায়িত্বের নোট' : 'Bio & Notes' ?>
                        </label>
                        <textarea id="admin_bio" name="bio" rows="3" placeholder="<?= $isBn ? 'এসপিএস কার্যক্রমে আপনার দায়িত্ব বা বিশেষ অভিজ্ঞতা...' : 'Your role focus or notes...' ?>" style="width: 100%; padding: 9px 12px; border: 1px solid var(--border-medium); border-radius: var(--radius-sm); font-size: 0.9rem; box-sizing: border-box; font-family: inherit;"><?= e($currentUser['bio'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Password Change Section (Optional) -->
                <div style="border-top: 1px solid var(--border-subtle); padding-top: var(--space-xl); margin-bottom: var(--space-xl);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; flex-wrap: wrap; gap: 8px;">
                        <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--primary-deep); margin: 0; display: flex; align-items: center; gap: 8px;">
                            <span>🔒</span>
                            <span><?= $isBn ? 'পাসওয়ার্ড পরিবর্তন ও নিরাপত্তা (Password Security)' : 'Change Password & Security' ?></span>
                        </h3>
                        <span style="display: inline-flex; align-items: center; gap: 6px; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 4px 10px; border-radius: var(--radius-full); font-size: 0.78rem; font-weight: 700;">
                            🛡️ <?= $isBn ? 'ইমেইল 2FA সক্রিয়' : 'Email 2FA Active' ?>
                        </span>
                    </div>
                    <p style="font-size: 0.82rem; color: var(--text-muted); margin: 0 0 var(--space-lg);">
                        <?= $isBn ? 'পাসওয়ার্ড পরিবর্তনের ক্ষেত্রে বর্তমান পাসওয়ার্ড প্রদান আবশ্যক। অপরিবর্তিত রাখতে চাইলে নিচের ঘরগুলো খালি রাখুন।' : 'Current password is required to set a new password. Leave blank to keep existing password.' ?>
                    </p>

                    <div style="margin-bottom: var(--space-md);">
                        <label for="admin_current_password" style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                            <?= $isBn ? 'বর্তমান পাসওয়ার্ড' : 'Current Password' ?>
                        </label>
                        <input type="password" id="admin_current_password" name="current_password" placeholder="<?= $isBn ? 'আপনার বর্তমান পাসওয়ার্ড দিন' : 'Enter your current password' ?>" style="width: 100%; max-width: 480px; padding: 9px 12px; border: 1px solid var(--border-medium); border-radius: var(--radius-sm); font-size: 0.9rem; box-sizing: border-box;">
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: var(--space-md);">
                        <div>
                            <label for="admin_new_password" style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                                <?= $isBn ? 'নতুন পাসওয়ার্ড' : 'New Password' ?>
                            </label>
                            <input type="password" id="admin_new_password" name="new_password" placeholder="<?= $isBn ? 'কমপক্ষে ৬ অক্ষর' : 'At least 6 characters' ?>" style="width: 100%; padding: 9px 12px; border: 1px solid var(--border-medium); border-radius: var(--radius-sm); font-size: 0.9rem; box-sizing: border-box;">
                        </div>

                        <div>
                            <label for="admin_confirm_password" style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                                <?= $isBn ? 'নতুন পাসওয়ার্ড নিশ্চিতকরণ' : 'Confirm New Password' ?>
                            </label>
                            <input type="password" id="admin_confirm_password" name="confirm_password" placeholder="<?= $isBn ? 'পুনরায় টাইপ করুন' : 'Retype password' ?>" style="width: 100%; padding: 9px 12px; border: 1px solid var(--border-medium); border-radius: var(--radius-sm); font-size: 0.9rem; box-sizing: border-box;">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div style="display: flex; justify-content: flex-end; gap: 12px; align-items: center;">
                    <a href="<?= url('/admin', $currentLocale) ?>" class="btn btn-secondary">
                        <?= $isBn ? 'বাতিল' : 'Cancel' ?>
                    </a>
                    <button type="submit" class="btn btn-primary" id="saveAdminProfileBtn" style="background-color: var(--accent-saffron, #C65A1E); border-color: var(--accent-saffron-hover, #A64713); font-weight: 800; padding: 10px 24px; box-shadow: 0 2px 8px rgba(198, 90, 30, 0.28);">
                        💾 <?= $isBn ? 'অ্যাডমিন প্রোফাইল সংরক্ষণ করুন' : 'Save Admin Profile' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
