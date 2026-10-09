<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$error = \App\Core\Session::getFlash('error');
$success = \App\Core\Session::getFlash('success');
$info = \App\Core\Session::getFlash('info');
$challenge = $challenge ?? [];
$rawEmail = $challenge['email'] ?? 'member@sps-platform.org';
$parts = explode('@', $rawEmail);
$namePart = $parts[0];
$domain = $parts[1] ?? 'sps-platform.org';
$maskedEmail = (strlen($namePart) > 2 ? substr($namePart, 0, 2) . str_repeat('*', strlen($namePart) - 2) : $namePart) . '@' . $domain;

// Local development convenience: only shown when SMTP_PASS is NOT configured in .env
$devLastOtp = \App\Core\Session::get('sps_dev_last_otp');
$showDevHelper = !\App\Services\EmailService::isSmtpConfigured()
    && !empty($devLastOtp['code'])
    && ($devLastOtp['email'] ?? '') === $rawEmail;
?>

<div style="max-width: 480px; margin: var(--space-2xl) auto; padding: 0 var(--space-md);">
    
    <div style="background: #ffffff; border: 1px solid var(--border-medium); border-radius: var(--radius-xl); box-shadow: 0 12px 32px rgba(0,0,0,0.06); overflow: hidden;">
        
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #1e3a8a 0%, #1e293b 100%); color: #ffffff; padding: var(--space-xl); text-align: center; position: relative;">
            <div style="font-size: 2.2rem; margin-bottom: 6px;">🛡️</div>
            <h1 style="font-size: 1.3rem; font-weight: 800; color: #ffffff; margin: 0 0 6px;">
                <?= $isBn ? 'সদস্য দ্বিমুখী প্রমাণীকরণ (2FA)' : 'Member 2FA Verification' ?>
            </h1>
            <p style="font-size: 0.85rem; color: #93c5fd; margin: 0; line-height: 1.4;">
                <?= $isBn 
                    ? 'আপনার সদস্য অ্যাকাউন্টের সুরক্ষার জন্য ইমেইলে প্রেরিত ওটিপি কোডটি প্রদান করুন।' 
                    : 'Enter the one-time verification code sent to your registered email.' ?>
            </p>
        </div>

        <!-- Body -->
        <div style="padding: var(--space-xl);">
            
            <?php if (!empty($error)): ?>
                <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px; border-radius: var(--radius-md); margin-bottom: var(--space-md); font-size: 0.88rem; display: flex; gap: 8px;">
                    <span>⚠️</span>
                    <div><?= e($error) ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px; border-radius: var(--radius-md); margin-bottom: var(--space-md); font-size: 0.88rem; display: flex; gap: 8px;">
                    <span>✓</span>
                    <div><?= e($success) ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($info)): ?>
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 12px; border-radius: var(--radius-md); margin-bottom: var(--space-md); font-size: 0.88rem; display: flex; gap: 8px;">
                    <span>ℹ️</span>
                    <div><?= e($info) ?></div>
                </div>
            <?php endif; ?>

            <!-- Destination Info Box -->
            <div style="background: #f8fafc; border: 1px solid var(--border-medium); border-radius: var(--radius-md); padding: 12px 16px; margin-bottom: var(--space-lg); font-size: 0.86rem; color: var(--text-secondary);">
                <div style="font-weight: 700; color: var(--primary-deep); margin-bottom: 2px;">
                    <?= $isBn ? 'নিবন্ধিত ইমেইল ঠিকানা:' : 'Sent to registered email:' ?>
                </div>
                <div style="font-family: monospace; font-weight: 700; color: #0284c7; font-size: 0.92rem;">
                    <?= e($maskedEmail) ?>
                </div>
            </div>

            <?php if ($showDevHelper): ?>
                <div style="background: #fef3c7; border: 1px dashed #f59e0b; color: #92400e; padding: 10px 14px; border-radius: var(--radius-md); margin-bottom: var(--space-lg); font-size: 0.85rem; text-align: center; cursor: pointer;" id="devOtpBox">
                    <div><?= $isBn ? '⚡ লোকাল ডেভেলপমেন্ট ভিউ (Local Dev Sandbox):' : '⚡ Local Dev OTP Sandbox:' ?></div>
                    <div style="font-family: monospace; font-weight: 800; font-size: 1.2rem; letter-spacing: 3px; color: #b45309; background: #fff; padding: 2px 8px; border-radius: 4px; border: 1px solid #fde68a; display: inline-block; margin-top: 4px;">
                        <?= e($devLastOtp['code']) ?>
                    </div>
                    <div style="font-size: 0.75rem; margin-top: 2px; color: #a16207;">
                        <?= $isBn ? '(কোডটি পূরণ করতে এখানে ক্লিক করুন)' : '(Click here to auto-fill)' ?>
                    </div>
                </div>
            <?php endif; ?>

            <form action="<?= url('/membership/2fa', $currentLocale) ?>" method="POST" id="member2faForm">
                <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>

                <div style="margin-bottom: var(--space-xl);">
                    <label for="code" style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 8px; text-align: center;">
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
                        style="width: 100%; padding: 12px 16px; font-size: 1.8rem; font-weight: 800; letter-spacing: 10px; text-align: center; border: 2px solid var(--border-medium); border-radius: var(--radius-md); background: #f8fafc; color: #0f172a; box-sizing: border-box; font-family: monospace;"
                    >
                    <div style="font-size: 0.78rem; color: var(--text-muted); text-align: center; margin-top: 6px;">
                        <?= $isBn ? 'কোডের মেয়াদ ১০ মিনিট।' : 'Code remains valid for 10 minutes.' ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" id="member2faSubmitBtn" style="width: 100%; padding: 12px; font-weight: 800; font-size: 1rem; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; gap: 8px; background: #b45309; border-color: #b45309; box-shadow: 0 4px 12px rgba(180, 83, 9, 0.25);">
                    <span>🔓</span>
                    <span><?= $isBn ? 'যাচাই সম্পন্ন করে ড্যাশবোর্ডে প্রবেশ করুন' : 'Verify & Open Dashboard' ?></span>
                </button>
            </form>

            <!-- Resend Form -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: var(--space-xl); padding-top: var(--space-md); border-top: 1px solid var(--border-subtle); font-size: 0.84rem;">
                <form action="<?= url('/membership/2fa/resend', $currentLocale) ?>" method="POST" style="margin: 0;">
                    <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>
                    <button type="submit" class="btn btn-ghost btn-sm" style="color: #c65a1e; font-weight: 700; padding: 4px 8px;">
                        🔄 <?= $isBn ? 'কোড পুনরায় পাঠান' : 'Resend Code' ?>
                    </button>
                </form>

                <a href="<?= url('/membership/login', $currentLocale) ?>" style="color: var(--text-muted); text-decoration: none; font-weight: 600;">
                    ← <?= $isBn ? 'লগইন পেজে ফিরুন' : 'Back to Login' ?>
                </a>
            </div>

        </div>
    </div>

</div>

<?php if ($showDevHelper): ?>
<script>
    document.getElementById('devOtpBox')?.addEventListener('click', function() {
        var input = document.getElementById('code');
        if (input) {
            input.value = '<?= e($devLastOtp['code']) ?>';
            input.focus();
        }
    });
</script>
<?php endif; ?>
