<?php

declare(strict_types=1);

use App\Core\I18n;
use App\Core\Session;

$locale = I18n::getLocale();
$isBn = $locale === 'bn';
$currentUser = \App\Services\AuthService::getCurrentUser();
?>
<!DOCTYPE html>
<html lang="<?= $locale ?>" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isBn ? 'স্থায়ী অনন্য পাসওয়ার্ড নির্ধারণ | এসপিএস অ্যাডমিন' : 'Set Your Unique Password | SPS Admin' ?></title>
    <link rel="stylesheet" href="<?= asset('assets/css/main.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/admin.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #091723 0%, #0f273d 50%, #07131d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: <?= $isBn ? "'Hind Siliguri', 'Inter', sans-serif" : "'Inter', sans-serif" ?>;
            color: #ffffff;
        }
        .security-card {
            background: #ffffff;
            color: #0f172a;
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.45);
            max-width: 520px;
            width: 92%;
            margin: 20px auto;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .security-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #ffffff;
            padding: 28px 24px;
            text-align: center;
            position: relative;
            border-bottom: 3px solid #b45309;
        }
        .security-icon {
            width: 68px;
            height: 68px;
            background: rgba(245, 158, 11, 0.15);
            border: 2px solid #f59e0b;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 12px;
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.3);
        }
        .security-body {
            padding: 28px 26px;
        }
        .otp-notice {
            background: #fffbeb;
            border: 1.5px solid #fde68a;
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 22px;
            font-size: 0.85rem;
            color: #92400e;
            line-height: 1.5;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-label {
            display: block;
            font-weight: 800;
            font-size: 0.88rem;
            color: #1e293b;
            margin-bottom: 6px;
        }
        .form-input {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            font-size: 0.95rem;
            box-sizing: border-box;
            transition: all 0.2s;
            font-family: inherit;
        }
        .form-input:focus {
            outline: none;
            border-color: #b45309;
            box-shadow: 0 0 0 3px rgba(180, 83, 9, 0.15);
        }
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #b45309 0%, #92400e 100%);
            color: #ffffff;
            border: none;
            padding: 13px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(180, 83, 9, 0.3);
            transition: all 0.2s;
        }
        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(180, 83, 9, 0.4);
        }
    </style>
</head>
<body>

    <div class="security-card">
        <div class="security-header">
            <div class="security-icon">🛡️</div>
            <h2 style="margin: 0 0 6px; font-size: 1.4rem; font-weight: 800;">
                <?= $isBn ? 'স্থায়ী ও অনন্য পাসওয়ার্ড নির্ধারণ' : 'Set Your Unique Password' ?>
            </h2>
            <div style="font-size: 0.84rem; color: #94a3b8;">
                <?= $isBn ? 'প্রশাসনিক কর্মকর্তা প্রথমবার লগইন প্রোটোকল' : 'Admin Officer Initial Login Protocol' ?>
            </div>
            <div style="margin-top: 10px; display: inline-flex; align-items: center; gap: 8px; background: rgba(255, 255, 255, 0.1); padding: 4px 12px; border-radius: 9999px; font-size: 0.8rem;">
                <span>👤 <?= e($currentUser['name_bn'] ?? $currentUser['name_en'] ?? 'Admin') ?></span>
                <span style="font-family: monospace; opacity: 0.7;">(<?= e($currentUser['username'] ?? '') ?>)</span>
            </div>
        </div>

        <div class="security-body">
            <!-- Flash Message Alerts -->
            <?php if ($flashErr = Session::getFlash('error')): ?>
                <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 10px 14px; border-radius: 8px; margin-bottom: 16px; font-size: 0.86rem; display: flex; align-items: center; gap: 8px;">
                    <span>⚠️</span>
                    <div><?= e($flashErr) ?></div>
                </div>
            <?php endif; ?>
            <?php if ($flashWarn = Session::getFlash('warning')): ?>
                <div style="background: #fffbeb; border: 1px solid #fde68a; color: #92400e; padding: 10px 14px; border-radius: 8px; margin-bottom: 16px; font-size: 0.86rem; display: flex; align-items: center; gap: 8px;">
                    <span>🔒</span>
                    <div><?= e($flashWarn) ?></div>
                </div>
            <?php endif; ?>

            <div class="otp-notice">
                <div style="font-weight: 800; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                    <span>🔑</span>
                    <span><?= $isBn ? 'কেন এটি আবশ্যক?' : 'Why is this mandatory?' ?></span>
                </div>
                <?= $isBn 
                    ? 'আপনাকে প্রশাসন কর্তৃক একটি ওয়ান-টাইম পাসওয়ার্ড (OTP) প্রদান করা হয়েছিল। সিস্টেমের সর্বোচ্চ নিরাপত্তা, জবাবদিহিতা ও আপনার স্বকীয়তা (uniqueness) রক্ষার জন্য ড্যাশবোর্ডে প্রবেশের পূর্বে এই OTP পরিবর্তন করে আপনার নিজস্ব একান্ত ব্যক্তিগত পাসওয়ার্ড নির্ধারণ করা বাধ্যতামূলক।' 
                    : 'You were provided a One-Time Password (OTP) for initial access. To ensure maximum administrative security, accountability, and uniqueness, you must replace the temporary OTP with your secret personal password.' ?>
            </div>

            <form action="<?= url('/admin/force-password-change', $locale) ?>" method="POST" id="forcePasswordForm">
                <?= Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.Session::getCsrfToken().'">' : '' ?>

                <div class="form-group">
                    <label class="form-label" for="current_otp">
                        <?= $isBn ? 'বর্তমান ওয়ান-টাইম পাসওয়ার্ড (Current OTP) *' : 'Current One-Time Password (OTP) *' ?>
                    </label>
                    <input type="password" id="current_otp" name="current_password" required placeholder="SPS-OTP-XXXXXX" class="form-input" autocomplete="current-password">
                    <span style="font-size: 0.74rem; color: #64748b; margin-top: 3px; display: block;">
                        <?= $isBn ? 'লগইন করার সময় ব্যবহৃত অস্থায়ী ওয়ান-টাইম পাসওয়ার্ডটি দিন' : 'Enter the temporary OTP provided to you' ?>
                    </span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_unique_password">
                        <?= $isBn ? 'নতুন নিজস্ব অনন্য পাসওয়ার্ড (New Unique Password) *' : 'New Unique Personal Password *' ?>
                    </label>
                    <input type="password" id="new_unique_password" name="new_password" required minlength="8" placeholder="••••••••••••" class="form-input" autocomplete="new-password">
                    <span style="font-size: 0.74rem; color: #64748b; margin-top: 3px; display: block;">
                        <?= $isBn ? 'কমপক্ষে ৮ অক্ষরের শক্তিশালী ও অনন্য পাসওয়ার্ড দিন' : 'Minimum 8 characters (letters, numbers, symbols)' ?>
                    </span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_password_confirmation">
                        <?= $isBn ? 'নতুন পাসওয়ার্ড নিশ্চিত করুন (Confirm New Password) *' : 'Confirm New Password *' ?>
                    </label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" required minlength="8" placeholder="••••••••••••" class="form-input" autocomplete="new-password">
                    <span style="font-size: 0.74rem; color: #64748b; margin-top: 3px; display: block;">
                        <?= $isBn ? 'একই নতুন পাসওয়ার্ডটি পুনরায় টাইপ করুন' : 'Retype the new unique password' ?>
                    </span>
                </div>

                <button type="submit" class="btn-submit">
                    🔐 <?= $isBn ? 'আমার অনন্য পাসওয়ার্ড সংরক্ষণ ও ড্যাশবোর্ডে প্রবেশ ➔' : 'Save Unique Password & Access Dashboard ➔' ?>
                </button>
            </form>

            <div style="margin-top: 18px; text-align: center;">
                <a href="<?= url('/admin/logout', $locale) ?>" style="color: #ef4444; font-size: 0.85rem; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                    <span>🚪</span>
                    <span><?= $isBn ? 'লগআউট করুন' : 'Log out from session' ?></span>
                </a>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('forcePasswordForm').addEventListener('submit', function(e) {
        const pass = document.getElementById('new_unique_password').value;
        const passConf = document.getElementById('new_password_confirmation').value;
        const currentOtp = document.getElementById('current_otp').value;

        if (pass.length < 8) {
            alert('নতুন পাসওয়ার্ড কমপক্ষে ৮ অক্ষরের হতে হবে।');
            e.preventDefault();
            document.getElementById('new_unique_password').focus();
            return false;
        }

        if (pass !== passConf) {
            alert('নতুন পাসওয়ার্ড এবং নিশ্চিতকরণ পাসওয়ার্ড মিলছে না!');
            e.preventDefault();
            document.getElementById('new_password_confirmation').focus();
            return false;
        }

        if (pass === currentOtp) {
            alert('নতুন পাসওয়ার্ডটি ওয়ান-টাইম পাসওয়ার্ডের (OTP) চেয়ে ভিন্ন ও অনন্য হতে হবে!');
            e.preventDefault();
            document.getElementById('new_unique_password').focus();
            return false;
        }
    });
    </script>
</body>
</html>
