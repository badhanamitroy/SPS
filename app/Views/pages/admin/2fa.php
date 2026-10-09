<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$error = \App\Core\Session::getFlash('error');
$success = \App\Core\Session::getFlash('success');
$info = \App\Core\Session::getFlash('info');
$challenge = $challenge ?? [];
$rawEmail = $challenge['email'] ?? 'admin@sps.org';
$parts = explode('@', $rawEmail);
$namePart = $parts[0];
$domain = $parts[1] ?? 'sps.org';
$maskedEmail = (strlen($namePart) > 2 ? substr($namePart, 0, 2) . str_repeat('*', strlen($namePart) - 2) : $namePart) . '@' . $domain;

// Local development convenience: detect last generated dev code
// Only shown when real SMTP is NOT configured (SMTP_PASS is empty in .env)
$devLastOtp = \App\Core\Session::get('sps_dev_last_otp');
$showDevHelper = !\App\Services\EmailService::isSmtpConfigured()
    && !empty($devLastOtp['code'])
    && ($devLastOtp['email'] ?? '') === $rawEmail;
?>
<!DOCTYPE html>
<html lang="<?= e($currentLocale) ?>" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isBn ? 'দ্বিমুখী প্রমাণীকরণ (2FA) | সনাতন ফিলোসফি এন্ড স্ক্রিপচার' : 'Two-Factor Authentication (2FA) | SPS' ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= asset('favicon.svg') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/tokens.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/reset.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/typography.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/components.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/main.css') ?>">
    <style>
        body.admin-2fa-body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f7f3ed 0%, #ece5da 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--space-xl) var(--space-md);
            font-family: <?= $isBn ? 'var(--font-bn-sans)' : 'Arial, Helvetica, sans-serif' ?>;
        }
        .twofa-card {
            background: #ffffff;
            border: 1px solid var(--border-medium);
            border-radius: var(--radius-lg);
            box-shadow: 0 16px 36px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 460px;
            overflow: hidden;
        }
        .twofa-header {
            background: #14202e;
            padding: var(--space-xl);
            text-align: center;
            color: #ffffff;
            position: relative;
        }
        .twofa-header::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #b3391b, #c65a1e, #a37e36);
        }
        .twofa-icon {
            font-size: 2.4rem;
            margin-bottom: 8px;
            display: inline-block;
        }
        .twofa-title {
            font-size: 1.35rem;
            font-weight: 700;
            margin: 0 0 6px;
            color: #ffffff;
            font-family: <?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-en-poppins)' ?>;
        }
        .twofa-subtitle {
            font-size: 0.85rem;
            color: #cbd5e1;
            margin: 0;
            line-height: 1.4;
        }
        .twofa-body {
            padding: var(--space-xl);
        }
        .twofa-otp-input {
            width: 100%;
            padding: 14px 16px;
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: 12px;
            text-align: center;
            border: 2px solid var(--border-medium);
            border-radius: var(--radius-md);
            background: #f8fafc;
            color: #0f172a;
            box-sizing: border-box;
            transition: all 0.2s ease;
            font-family: 'Courier New', Courier, monospace;
        }
        .twofa-otp-input:focus {
            outline: none;
            border-color: #c65a1e;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(198, 90, 30, 0.15);
        }
        .btn-verify {
            width: 100%;
            padding: 13px;
            background: #c65a1e;
            color: #ffffff;
            border: none;
            border-radius: var(--radius-md);
            font-size: 1rem;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(198, 90, 30, 0.28);
            transition: background 0.2s;
        }
        .btn-verify:hover {
            background: #a64713;
        }
        .dev-badge {
            background: #fef3c7;
            border: 1px dashed #f59e0b;
            color: #92400e;
            padding: 10px 14px;
            border-radius: var(--radius-md);
            margin-bottom: var(--space-lg);
            font-size: 0.85rem;
            text-align: center;
        }
        .dev-code {
            font-family: monospace;
            font-weight: 800;
            font-size: 1.15rem;
            letter-spacing: 2px;
            color: #b45309;
            background: #fff;
            padding: 2px 8px;
            border-radius: 4px;
            border: 1px solid #fde68a;
            display: inline-block;
            margin-top: 4px;
        }
    </style>
</head>
<body class="admin-2fa-body">
    <div class="twofa-card">
        <div class="twofa-header">
            <div class="twofa-icon">🛡️</div>
            <h1 class="twofa-title"><?= $isBn ? 'দ্বিমুখী নিরাপত্তা যাচাই (2FA)' : 'Two-Factor Authentication' ?></h1>
            <p class="twofa-subtitle">
                <?= $isBn 
                    ? 'আপনার সুরক্ষার জন্য ৬-সংখ্যার ওটিপি কোডটি নিচে প্রদান করুন।' 
                    : 'Enter the 6-digit one-time verification code dispatched to your email.' ?>
            </p>
        </div>

        <div class="twofa-body">
            <?php if (!empty($error)): ?>
                <div style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:12px; border-radius:var(--radius-md); margin-bottom:var(--space-md); font-size:0.88rem; display:flex; gap:8px;">
                    <span>⚠️</span>
                    <div><?= e($error) ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:12px; border-radius:var(--radius-md); margin-bottom:var(--space-md); font-size:0.88rem; display:flex; gap:8px;">
                    <span>✓</span>
                    <div><?= e($success) ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($info)): ?>
                <div style="background:#eff6ff; border:1px solid #bfdbfe; color:#1e40af; padding:12px; border-radius:var(--radius-md); margin-bottom:var(--space-md); font-size:0.88rem; display:flex; gap:8px;">
                    <span>ℹ️</span>
                    <div><?= e($info) ?></div>
                </div>
            <?php endif; ?>

            <!-- Destination Info Box -->
            <div style="background:#f8fafc; border:1px solid var(--border-medium); border-radius:var(--radius-md); padding:12px 16px; margin-bottom:var(--space-lg); font-size:0.86rem; color:var(--text-secondary);">
                <div style="font-weight:700; color:var(--primary-deep); margin-bottom:2px;">
                    <?= $isBn ? 'প্রেরিত ইমেইল ঠিকানা:' : 'Verification sent to:' ?>
                </div>
                <div style="font-family:monospace; font-weight:700; color:#0284c7; font-size:0.92rem;">
                    <?= e($maskedEmail) ?>
                </div>
            </div>

            <?php if ($showDevHelper): ?>
                <div class="dev-badge">
                    <div><?= $isBn ? '⚡ লোকাল ডেভেলপমেন্ট ভিউ (Local Dev Sandbox):' : '⚡ Local Dev OTP Sandbox:' ?></div>
                    <div class="dev-code"><?= e($devLastOtp['code']) ?></div>
                    <div style="font-size:0.75rem; margin-top:2px; color:#a16207;">
                        <?= $isBn ? '(কোডটি স্বয়ংক্রিয়ভাবে প্রবেশ করাতে নিচে ক্লিক করুন)' : '(Click to auto-fill)' ?>
                    </div>
                </div>
            <?php endif; ?>

            <form action="<?= url('/admin/2fa', $currentLocale) ?>" method="POST" id="admin2faForm">
                <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>

                <div style="margin-bottom:var(--space-xl);">
                    <label for="code" style="display:block; font-size:0.86rem; font-weight:700; color:var(--primary-deep); margin-bottom:8px; text-align:center;">
                        <?= $isBn ? '৬-সংখ্যার ওটিপি কোড (OTP Code)' : '6-Digit Verification Code' ?>
                    </label>
                    <input 
                        type="text" 
                        id="code" 
                        name="code" 
                        maxlength="6" 
                        inputmode="numeric" 
                        pattern="[0-9]*" 
                        required 
                        autofocus 
                        autocomplete="one-time-code"
                        placeholder="••••••" 
                        class="twofa-otp-input"
                    >
                    <div style="font-size:0.78rem; color:var(--text-muted); text-align:center; margin-top:6px;">
                        <?= $isBn ? 'কোডের মেয়াদ ১০ মিনিট।' : 'Code remains valid for 10 minutes.' ?>
                    </div>
                </div>

                <button type="submit" class="btn-verify" id="admin2faSubmitBtn">
                    <span>🔓</span>
                    <span><?= $isBn ? 'যাচাই সম্পন্ন করে প্রবেশ করুন' : 'Verify Code & Sign In' ?></span>
                </button>
            </form>

            <!-- Resend Form -->
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:var(--space-xl); padding-top:var(--space-md); border-top:1px solid var(--border-subtle); font-size:0.84rem;">
                <form action="<?= url('/admin/2fa/resend', $currentLocale) ?>" method="POST" style="margin:0;">
                    <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>
                    <button type="submit" class="btn btn-ghost btn-sm" style="color:#c65a1e; font-weight:700; padding:4px 8px;">
                        🔄 <?= $isBn ? 'কোড পুনরায় পাঠান' : 'Resend Code' ?>
                    </button>
                </form>

                <a href="<?= url('/admin/login', $currentLocale) ?>" style="color:var(--text-muted); text-decoration:none; font-weight:600;">
                    ← <?= $isBn ? 'লগইন পেজে ফিরুন' : 'Back to Login' ?>
                </a>
            </div>
        </div>
    </div>

    <?php if ($showDevHelper): ?>
        <script>
            document.querySelector('.dev-badge')?.addEventListener('click', function() {
                var input = document.getElementById('code');
                if (input) {
                    input.value = '<?= e($devLastOtp['code']) ?>';
                    input.focus();
                }
            });
        </script>
    <?php endif; ?>
</body>
</html>
