<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$error = \App\Core\Session::getFlash('error');
$success = \App\Core\Session::getFlash('success');
?>
<!DOCTYPE html>
<html lang="<?= e($currentLocale) ?>" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isBn ? 'প্রশাসনিক লগইন | সনাতন ফিলোসফি এন্ড স্ক্রিপচার (SPS)' : 'Admin Portal Login | Sanatan Philosophy & Scripture (SPS)' ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= asset('favicon.svg') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/tokens.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/reset.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/typography.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/components.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/main.css') ?>">
    <style>
        body.admin-login-body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f7f3ed 0%, #ece5da 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--space-xl) var(--space-md);
            font-family: <?= $isBn ? 'var(--font-bn-sans)' : 'Arial, Helvetica, sans-serif' ?>;
        }
        .login-card {
            background: #ffffff;
            border: 1px solid var(--border-medium);
            border-radius: var(--radius-lg);
            box-shadow: 0 16px 36px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
        }
        .login-header {
            background: #14202e;
            padding: var(--space-xl) var(--space-xl) var(--space-lg);
            text-align: center;
            color: #ffffff;
            position: relative;
        }
        .login-header::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #b3391b, #c65a1e, #a37e36);
        }
        .login-brand-logo {
            height: 48px;
            width: auto;
            margin: 0 auto var(--space-sm);
        }
        .login-title {
            font-size: 1.35rem;
            font-weight: 700;
            margin: 0 0 4px;
            color: #ffffff;
            font-family: <?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-en-poppins)' ?>;
        }
        .login-subtitle {
            font-size: 0.82rem;
            color: #a0aec0;
            margin: 0;
            font-family: <?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-en-serif)' ?>;
        }
        .login-body {
            padding: var(--space-xl);
        }
        .login-form-group {
            margin-bottom: var(--space-md);
        }
        .login-form-label {
            display: block;
            font-size: 0.86rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 6px;
        }
        .login-form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border-medium);
            border-radius: var(--radius-sm);
            font-size: 0.94rem;
            color: var(--text-main);
            background: #fafafa;
            transition: all 0.2s ease;
        }
        .login-form-input:focus {
            outline: none;
            border-color: #b3391b;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(179, 57, 27, 0.12);
        }
        .login-pwd-wrapper {
            position: relative;
        }
        .login-pwd-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            font-size: 0.85rem;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
        }
        .login-btn-submit {
            width: 100%;
            padding: 12px;
            background: #b3391b;
            color: #ffffff;
            font-size: 1rem;
            font-weight: 700;
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: background 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: var(--space-md);
            font-family: <?= $isBn ? 'var(--font-bn-sans)' : 'var(--font-en-poppins)' ?>;
        }
        .login-btn-submit:hover {
            background: #962f15;
        }
        .login-hint-box {
            margin-top: var(--space-lg);
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: var(--radius-sm);
            padding: var(--space-sm) var(--space-md);
            font-size: 0.78rem;
            color: #475569;
        }
        .login-hint-title {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .login-footer-links {
            margin-top: var(--space-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.82rem;
            color: var(--text-muted);
        }
        .login-footer-links a {
            color: #b3391b;
            text-decoration: none;
            font-weight: 600;
        }
        .login-footer-links a:hover {
            text-decoration: underline;
        }
        .login-alert-error {
            background: #fef2f2;
            border: 1px solid #f87171;
            color: #991b1b;
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            font-size: 0.86rem;
            margin-bottom: var(--space-md);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .login-alert-success {
            background: #f0fdf4;
            border: 1px solid #4ade80;
            color: #166534;
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            font-size: 0.86rem;
            margin-bottom: var(--space-md);
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body class="admin-login-body">

    <div class="login-card">
        <!-- Header -->
        <div class="login-header">
            <img src="<?= asset('assets/images/brand/sps-logo-white.png') ?>" alt="SPS Logo" class="login-brand-logo">
            <h1 class="login-title"><?= $isBn ? 'এসপিএস প্রশাসনিক প্রবেশদ্বার' : 'SPS Administrative Portal' ?></h1>
            <p class="login-subtitle"><?= $isBn ? 'সনাতনী ঐক্য, প্রচার ও কল্যাণে অবিচল।' : 'Steadfast in Sanatan Unity, Propagation & Welfare' ?></p>
        </div>

        <!-- Body -->
        <div class="login-body">
            <?php if (!empty($error)): ?>
                <div class="login-alert-error">
                    <span>⚠️</span>
                    <span><?= e($error) ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="login-alert-success">
                    <span>✓</span>
                    <span><?= e($success) ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= url('/admin/login', $currentLocale) ?>" method="POST" id="adminLoginForm">
                <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>

                <!-- Username or Email -->
                <div class="login-form-group">
                    <label for="identifier" class="login-form-label">
                        <?= $isBn ? 'ইউজারনেম বা ইমেইল' : 'Username or Email' ?>
                    </label>
                    <input 
                        type="text" 
                        id="identifier" 
                        name="identifier" 
                        class="login-form-input" 
                        placeholder="<?= $isBn ? 'যেমন: anik অথবা president@sps.org' : 'e.g. anik or president@sps.org' ?>" 
                        required 
                        autofocus
                        autocomplete="username"
                    >
                </div>

                <!-- Password -->
                <div class="login-form-group">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                        <label for="password" class="login-form-label" style="margin-bottom:0;">
                            <?= $isBn ? 'পাসওয়ার্ড' : 'Password' ?>
                        </label>
                    </div>
                    <div class="login-pwd-wrapper">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="login-form-input" 
                            placeholder="••••••••••••" 
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="login-pwd-toggle" id="pwdToggleBtn" aria-label="Toggle password visibility">👁️</button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:var(--space-md);">
                    <input type="checkbox" id="remember" name="remember" value="1" style="cursor:pointer;">
                    <label for="remember" style="font-size:0.84rem; color:var(--text-muted); cursor:pointer;">
                        <?= $isBn ? 'আমাকে মনে রাখুন' : 'Remember me on this browser' ?>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="login-btn-submit" id="loginSubmitBtn">
                    <span>🔐</span>
                    <span><?= $isBn ? 'প্রশাসনিক লগইন করুন' : 'Sign In to Admin Console' ?></span>
                </button>
            </form>

            <!-- Quick Credentials Reference Hint -->
            <div class="login-hint-box">
                <div class="login-hint-title">
                    <span>🔑</span>
                    <span><?= $isBn ? 'প্রশাসনিক অ্যাকাউন্ট নির্দেশিকা:' : 'Admin Access Credentials Reference:' ?></span>
                </div>
                <div style="display:grid; grid-template-columns:1fr; gap:4px; margin-top:6px;">
                    <div>• <strong>Super Admin:</strong> <code>anik</code> বা <code>president@sps.org</code></div>
                    <div>• <strong>Admin:</strong> <code>robin</code> বা <code>general.secretary@sps.org</code></div>
                    <div>• <strong>Default Password:</strong> <code>sps@admin2026</code></div>
                </div>
            </div>

            <!-- Footer Links -->
            <div class="login-footer-links">
                <a href="<?= url('/', $currentLocale) ?>">
                    ← <?= $isBn ? 'মূল ওয়েবসাইটে ফিরে যান' : 'Back to Public Website' ?>
                </a>
                <span style="font-size:0.75rem; color:#94a3b8;">SPS RBAC v2.4</span>
            </div>
        </div>
    </div>

    <script>
        const pwdInput = document.getElementById('password');
        const pwdToggleBtn = document.getElementById('pwdToggleBtn');
        if (pwdToggleBtn && pwdInput) {
            pwdToggleBtn.addEventListener('click', () => {
                const isPassword = pwdInput.type === 'password';
                pwdInput.type = isPassword ? 'text' : 'password';
                pwdToggleBtn.textContent = isPassword ? '🙈' : '👁️';
            });
        }
    </script>
</body>
</html>
