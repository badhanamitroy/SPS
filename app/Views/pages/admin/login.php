<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$error = \App\Core\Session::getFlash('error');
$success = \App\Core\Session::getFlash('success');
?>
<style>
    .admin-login-page-wrap {
        min-height: calc(100vh - 180px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: clamp(2.5rem, 5vw, 4.5rem) var(--space-md);
        background: linear-gradient(135deg, rgba(247, 243, 237, 0.7) 0%, rgba(236, 229, 218, 0.9) 100%);
        width: 100%;
    }
    .login-card {
        background: #ffffff;
        border: 1px solid var(--border-medium);
        border-radius: var(--radius-lg);
        box-shadow: 0 16px 36px rgba(0,0,0,0.08);
        width: 100%;
        max-width: 460px;
        margin: 0 auto;
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
        display: block;
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
        min-height: var(--touch-target-min, 44px);
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

    /* Dark Mode Polish */
    [data-theme="dark"] .admin-login-page-wrap {
        background: var(--bg-canvas);
    }
    [data-theme="dark"] .login-card {
        background: var(--bg-surface);
        border-color: var(--border-medium);
        box-shadow: 0 16px 36px rgba(0,0,0,0.5);
    }
    [data-theme="dark"] .login-form-input {
        background: var(--bg-subtle, #131826);
        border-color: var(--border-medium);
        color: var(--text-main);
    }
    [data-theme="dark"] .login-hint-box {
        background: var(--bg-subtle);
        border-color: var(--border-medium);
        color: var(--text-secondary);
    }
    [data-theme="dark"] .login-hint-title {
        color: var(--text-main);
    }
</style>

<div class="admin-login-page-wrap">
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

            <!-- Dynamic Google Auth Alert Box -->
            <div id="googleAuthAlert" style="display:none; padding:10px 14px; border-radius:var(--radius-sm); font-size:0.86rem; margin-bottom:var(--space-md); align-items:center; gap:8px;"></div>

            <!-- Google Identity Services (GIS) Admin SSO -->
            <div class="admin-google-sso-wrapper" style="margin-bottom: var(--space-md);">
                <?php if (!empty($googleClientId)): ?>
                    <div id="g_id_onload"
                         data-client_id="<?= e($googleClientId) ?>"
                         data-context="signin"
                         data-ux_mode="popup"
                         data-callback="handleAdminGoogleCredentialResponse"
                         data-auto_prompt="false">
                    </div>
                    <div style="display:flex; justify-content:center; width:100%;">
                        <div class="g_id_signin"
                             data-type="standard"
                             data-shape="rectangular"
                             data-theme="outline"
                             data-text="continue_with"
                             data-size="large"
                             data-logo_alignment="left"
                             data-width="380">
                        </div>
                    </div>
                <?php else: ?>
                    <button type="button" id="googleSetupBtn" onclick="showGoogleSetupModal()" style="display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 11px 16px; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: var(--radius-sm); color: #1e293b; font-weight: 700; font-size: 0.92rem; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.04); transition: all 0.2s ease;">
                        <svg style="width: 18px; height: 18px;" viewBox="0 0 48 48">
                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.79l7.97-6.2z"/>
                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                        </svg>
                        <span><?= $isBn ? 'গুগল দিয়ে প্রবেশ (Continue with Google)' : 'Continue with Google / Gmail' ?></span>
                    </button>
                <?php endif; ?>

                <div style="font-size: 0.74rem; color: #64748b; text-align: center; margin-top: 6px; display: flex; align-items: center; justify-content: center; gap: 4px;">
                    <span>🛡️</span>
                    <span><?= $isBn ? 'ক্রিপ্টোগ্রাফিক সুরক্ষা: কোনো পাসওয়ার্ড ছাড়াই নিরাপদ সাইন-ইন' : 'Zero-Password Cryptographic Protection via Google SSO' ?></span>
                </div>
            </div>

            <!-- Divider -->
            <div style="display: flex; align-items: center; margin-bottom: var(--space-md); text-align: center;">
                <div style="flex: 1; border-bottom: 1px solid var(--border-medium);"></div>
                <span style="padding: 0 10px; font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">
                    <?= $isBn ? 'অথবা ইউজারনেম ও পাসওয়ার্ড' : 'OR USERNAME & PASSWORD' ?>
                </span>
                <div style="flex: 1; border-bottom: 1px solid var(--border-medium);"></div>
            </div>

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

    <!-- Google OAuth Setup Modal (Displayed if GOOGLE_CLIENT_ID is not yet configured) -->
    <div id="googleSetupModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.6); z-index:9999; align-items:center; justify-content:center; padding:16px;">
        <div style="background:#ffffff; max-width:500px; width:100%; border-radius:var(--radius-lg); box-shadow:0 20px 40px rgba(0,0,0,0.25); overflow:hidden;">
            <div style="background:#1e293b; color:#ffffff; padding:16px 20px; display:flex; align-items:center; justify-content:space-between;">
                <div style="font-weight:700; font-size:1rem; display:flex; align-items:center; gap:8px;">
                    <span>🛡️</span>
                    <span><?= $isBn ? 'গুগল সাইন-ইন কনফিগারেশন নির্দেশিকা' : 'Google Identity Services Setup Guide' ?></span>
                </div>
                <button type="button" onclick="closeGoogleSetupModal()" style="background:none; border:none; color:#cbd5e1; font-size:1.2rem; cursor:pointer;">✕</button>
            </div>
            <div style="padding:20px; font-size:0.88rem; color:#334155; line-height:1.6;">
                <p style="margin-top:0;">
                    <?= $isBn 
                        ? 'এসপিএস সিস্টেমে হ্যাকিং ও পাসওয়ার্ড ক্র্যাক রোধে গুগল ক্রিপ্টোগ্রাফিক অথেন্টিকেশন কোড সম্পূর্ণ প্রস্তুত। ব্রাউজারে গুগল পপ-আপ সক্রিয় করতে মাত্র ২টি ধাপ সম্পন্ন করুন:' 
                        : 'Google cryptographic OAuth protection is completely implemented. To activate the browser popup on your domain, complete these 2 simple steps:' ?>
                </p>
                <ol style="padding-left:20px; margin-bottom:16px;">
                    <li><strong>Google Cloud Console</strong> (<a href="https://console.cloud.google.com/apis/credentials" target="_blank" style="color:#b3391b;">console.cloud.google.com</a>) এ গিয়ে একটি ফ্রি <strong>OAuth 2.0 Client ID (Web Application)</strong> তৈরি করুন।</li>
                    <li>Authorized JavaScript Origins-এ আপনার ওয়েবসাইটের URL (যেমন: <code>http://localhost:8000</code> বা আপনার লাইভ ডোমেন) দিন।</li>
                    <li>প্রাপ্ত Client ID টি <code style="background:#f1f5f9; padding:2px 6px; border-radius:4px; font-weight:700;">config/app.php</code> এর <code>['google']['client_id']</code> এ অথবা <code>.env</code> ফাইলে <code>GOOGLE_CLIENT_ID</code> হিসেবে বসিয়ে দিন।</li>
                </ol>
                <div style="background:#f8fafc; border:1px dashed #cbd5e1; border-radius:var(--radius-sm); padding:10px 12px; font-size:0.8rem; color:#475569;">
                    💡 <strong>দ্রুত পরীক্ষা:</strong> আপনার জিমেইল অ্যাকাউন্ট (<code>badhanamitroy571@gmail.com</code>) ইতোমধ্যে সিস্টেমে <strong>প্রকাশনা সম্পাদক (Publication Secretary)</strong> হিসেবে অনুমোদিত তালিকায় লিংক করা হয়েছে।
                </div>
                <div style="margin-top:16px; text-align:right;">
                    <button type="button" onclick="closeGoogleSetupModal()" class="login-btn-submit" style="display:inline-flex; width:auto; padding:8px 18px; margin-top:0; font-size:0.88rem;">
                        <?= $isBn ? 'বুঝেছি, ধন্যবাদ' : 'Understood' ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div><!-- /.admin-login-page-wrap -->

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

        function showGoogleSetupModal() {
            const modal = document.getElementById('googleSetupModal');
            if (modal) modal.style.display = 'flex';
        }

        function closeGoogleSetupModal() {
            const modal = document.getElementById('googleSetupModal');
            if (modal) modal.style.display = 'none';
        }

        // Handle Google Identity Services (GIS) Response
        function handleAdminGoogleCredentialResponse(response) {
            const alertBox = document.getElementById('googleAuthAlert');
            if (alertBox) {
                alertBox.style.display = 'flex';
                alertBox.style.background = '#eff6ff';
                alertBox.style.border = '1px solid #bfdbfe';
                alertBox.style.color = '#1e40af';
                alertBox.innerHTML = '<span>⏳</span> <span><?= $isBn ? "গুগল ডিজিটাল স্বাক্ষর ও নিরাপত্তা যাচাই করা হচ্ছে..." : "Cryptographically verifying Google security proof..." ?></span>';
            }

            const verifyUrl = '<?= url("/admin/auth/google/verify", $currentLocale) ?>';
            
            fetch(verifyUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    credential: response.credential
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (alertBox) {
                        alertBox.style.background = '#f0fdf4';
                        alertBox.style.border = '1px solid #bbf7d0';
                        alertBox.style.color = '#166534';
                        alertBox.innerHTML = '<span>✓</span> <span>' + (data.message || 'স্বাগতম!') + '</span>';
                    }
                    setTimeout(() => {
                        window.location.href = data.redirect || '<?= url("/admin", $currentLocale) ?>';
                    }, 500);
                } else {
                    if (alertBox) {
                        alertBox.style.background = '#fef2f2';
                        alertBox.style.border = '1px solid #f87171';
                        alertBox.style.color = '#991b1b';
                        alertBox.innerHTML = '<span>⚠️</span> <span>' + (data.error || 'গুগল প্রমাণীকরণ ব্যর্থ হয়েছে') + '</span>';
                    }
                }
            })
            .catch(err => {
                if (alertBox) {
                    alertBox.style.background = '#fef2f2';
                    alertBox.style.border = '1px solid #f87171';
                    alertBox.style.color = '#991b1b';
                    alertBox.innerHTML = '<span>⚠️</span> <span>সার্ভারের সাথে সংযোগ স্থাপন করা সম্ভব হয়নি।</span>';
                }
            });
        }
    </script>

