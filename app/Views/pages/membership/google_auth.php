<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$demoAccounts = $demoGoogleAccounts ?? [];
?>

<div style="max-width: 480px; margin: var(--space-2xl) auto; padding: 0 var(--space-md);">
    
    <div style="background: #ffffff; border: 1px solid var(--border-medium); border-radius: 16px; box-shadow: 0 16px 40px rgba(0,0,0,0.08); overflow: hidden; padding: 32px 28px;">
        
        <!-- Google Header -->
        <div style="text-align: center; margin-bottom: 24px;">
            <svg style="width: 44px; height: 44px; margin-bottom: 12px;" viewBox="0 0 48 48">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.79l7.97-6.2z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
            </svg>
            <h1 style="font-size: 1.35rem; font-weight: 700; color: #1e293b; margin: 0 0 6px;">
                <?= $isBn ? 'গুগল দিয়ে সাইন-ইন করুন' : 'Sign in with Google' ?>
            </h1>
            <div style="font-size: 0.88rem; color: #64748b;">
                <?= $isBn ? 'এসপিএস সনাতন প্ল্যাটফর্মে যুক্ত হতে আপনার গুগল অ্যাকাউন্ট নির্বাচন করুন' : 'to continue to Sanatan Philosophy & Scripture (SPS)' ?>
            </div>
        </div>

        <!-- Choose Existing Account -->
        <div style="margin-bottom: 24px;">
            <div style="font-size: 0.8rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">
                <?= $isBn ? 'নিবন্ধিত অ্যাকাউন্টসমূহ (Quick Select):' : 'Registered SPS Google Accounts:' ?>
            </div>

            <div style="display: flex; flex-direction: column; gap: 8px;">
                <?php foreach ($demoAccounts as $acc): ?>
                    <form action="<?= url('/membership/auth/google/callback', $currentLocale) ?>" method="POST" style="margin: 0;">
                        <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>
                        <input type="hidden" name="google_email" value="<?= e($acc['email']) ?>">
                        <input type="hidden" name="google_name" value="<?= e($acc['name']) ?>">
                        <input type="hidden" name="google_id" value="gid_<?= md5($acc['email']) ?>">
                        <input type="hidden" name="google_avatar" value="<?= e($acc['avatar']) ?>">
                        
                        <button type="submit" style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 10px; cursor: pointer; text-align: left; transition: all 0.2s ease;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <img src="<?= asset($acc['avatar']) ?>" alt="<?= e($acc['name']) ?>" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 1px solid #cbd5e1;">
                                <div>
                                    <div style="font-weight: 700; color: #1e293b; font-size: 0.92rem;"><?= e($acc['name']) ?></div>
                                    <div style="font-size: 0.8rem; color: #64748b;"><?= e($acc['email']) ?></div>
                                </div>
                            </div>
                            <span style="font-family: monospace; font-size: 0.76rem; background: #eff6ff; color: #1d4ed8; padding: 3px 8px; border-radius: 4px; font-weight: 700;">
                                <?= e($acc['linked']) ?>
                            </span>
                        </button>
                    </form>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Real Google Identity Services (GIS) Popup Section -->
        <div style="border-top: 1px solid #e2e8f0; padding-top: 20px;">
            <div style="font-size: 0.8rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; text-align: center;">
                <?= $isBn ? 'অথবা গুগল পপ-আপ দিয়ে নিরাপদ সাইন-ইন করুন:' : 'Or sign in with secure Google popup:' ?>
            </div>

            <div id="googleAuthAlert" style="display:none; padding:10px 14px; border-radius:8px; font-size:0.86rem; margin-bottom:14px; align-items:center; gap:8px;"></div>

            <div style="display: flex; justify-content: center; width: 100%; margin-bottom: 10px;">
                <?php if (!empty($googleClientId)): ?>
                    <div id="g_id_onload"
                         data-client_id="<?= e($googleClientId) ?>"
                         data-context="signin"
                         data-ux_mode="popup"
                         data-callback="handleGoogleCredentialResponse"
                         data-auto_prompt="false">
                    </div>
                    <div class="g_id_signin"
                         data-type="standard"
                         data-shape="rectangular"
                         data-theme="outline"
                         data-text="continue_with"
                         data-size="large"
                         data-logo_alignment="left"
                         data-width="360">
                    </div>
                <?php else: ?>
                    <button type="button" onclick="showGoogleSetupModal()" style="display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 12px 18px; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 8px; color: #1e293b; font-size: 0.95rem; font-weight: 700; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.04);">
                        <svg style="width: 20px; height: 20px;" viewBox="0 0 48 48">
                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.79l7.97-6.2z"/>
                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                        </svg>
                        <span><?= $isBn ? 'গুগল দিয়ে প্রবেশ (Continue with Google)' : 'Continue with Google / Gmail' ?></span>
                    </button>
                <?php endif; ?>
            </div>

            <div style="font-size: 0.74rem; color: #64748b; text-align: center; margin-top: 4px;">
                🛡️ <?= $isBn ? 'ক্রিপ্টোগ্রাফিক সুরক্ষা: কোনো পাসওয়ার্ড ছাড়াই নিরাপদ সাইন-ইন' : 'Zero-Password Cryptographic Protection via Google SSO' ?>
            </div>
        </div>

        <!-- Back to Member Login Link -->
        <div style="text-align: center; margin-top: 24px;">
            <a href="<?= url('/membership/login', $currentLocale) ?>" style="color: #64748b; text-decoration: none; font-size: 0.85rem; font-weight: 600;">
                ← <?= $isBn ? 'সদস্য লগইন পোর্টালে ফিরুন' : 'Back to Member Login' ?>
            </a>
        </div>

    </div>

</div>

<!-- Google Setup Modal -->
<div id="googleSetupModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.6); z-index:9999; align-items:center; justify-content:center; padding:16px;">
    <div style="background:#ffffff; max-width:480px; width:100%; border-radius:12px; box-shadow:0 20px 40px rgba(0,0,0,0.25); overflow:hidden; padding:24px;">
        <h3 style="margin-top:0; font-size:1.15rem; color:#1e293b;"><?= $isBn ? 'গুগল সাইন-ইন নির্দেশিকা' : 'Google Setup Guide' ?></h3>
        <p style="font-size:0.88rem; color:#475569; line-height:1.5;">
            <?= $isBn ? 'গুগল ক্লাউড কনসোল থেকে OAuth 2.0 Web Client ID তৈরি করে <code>config/app.php</code> ফাইলের <code>[\'google\'][\'client_id\']</code> এ বসিয়ে দিলেই আসল পপ-আপ কাজ শুরু করবে।' : 'Configure your Google OAuth Client ID in config/app.php to enable the live popup.' ?>
        </p>
        <div style="text-align:right; margin-top:16px;">
            <button type="button" onclick="closeGoogleSetupModal()" style="padding:8px 16px; background:#1a73e8; color:#fff; border:none; border-radius:6px; cursor:pointer;">
                <?= $isBn ? 'বুঝেছি' : 'Got it' ?>
            </button>
        </div>
    </div>
</div>

<script>
function showGoogleSetupModal() {
    const modal = document.getElementById('googleSetupModal');
    if (modal) modal.style.display = 'flex';
}
function closeGoogleSetupModal() {
    const modal = document.getElementById('googleSetupModal');
    if (modal) modal.style.display = 'none';
}

function handleGoogleCredentialResponse(response) {
    const alertBox = document.getElementById('googleAuthAlert');
    if (alertBox) {
        alertBox.style.display = 'flex';
        alertBox.style.background = '#eff6ff';
        alertBox.style.border = '1px solid #bfdbfe';
        alertBox.style.color = '#1e40af';
        alertBox.innerHTML = '<span>⏳</span> <span><?= $isBn ? "গুগল ডিজিটাল স্বাক্ষর ও নিরাপত্তা যাচাই করা হচ্ছে..." : "Verifying Google cryptographic security proof..." ?></span>';
    }

    const verifyUrl = '<?= url("/membership/auth/google/verify", $currentLocale) ?>';

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
                window.location.href = data.redirect || '<?= url("/membership/dashboard", $currentLocale) ?>';
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

