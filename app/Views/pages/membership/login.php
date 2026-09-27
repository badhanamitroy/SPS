<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$error = \App\Core\Session::getFlash('error');
$success = \App\Core\Session::getFlash('success');
$info = \App\Core\Session::getFlash('info');
?>

<div class="container" style="max-width: 960px; margin: var(--space-2xl) auto var(--space-3xl); padding: 0 var(--space-md);">

    <!-- Breadcrumb -->
    <nav style="margin-bottom: var(--space-lg); font-size: 0.85rem; color: var(--text-muted);" aria-label="Breadcrumb">
        <a href="<?= url('/', $currentLocale) ?>" style="color: var(--text-muted); text-decoration: none;"><?= $isBn ? 'প্রচ্ছদ' : 'Home' ?></a>
        <span style="margin: 0 8px;">/</span>
        <a href="<?= url('/membership', $currentLocale) ?>" style="color: var(--text-muted); text-decoration: none;"><?= $isBn ? 'সদস্যপদ হাব' : 'Membership Hub' ?></a>
        <span style="margin: 0 8px;">/</span>
        <span style="color: var(--primary-deep); font-weight: 700;"><?= $isBn ? 'সদস্য লগইন' : 'Member Login' ?></span>
    </nav>

    <!-- Two-Column Layout: Left = Login Card, Right = Non-Member / Join Guide -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: var(--space-2xl); align-items: start;">
        
        <!-- Left Column: Member Login Card -->
        <div class="card" style="background: #ffffff; border: 1px solid var(--border-medium); border-radius: var(--radius-xl); box-shadow: var(--shadow-md); overflow: hidden; padding: 0;">
            
            <!-- Card Header with Brand Aesthetics -->
            <div style="background: #14202e; color: #ffffff; padding: var(--space-xl) var(--space-xl) var(--space-lg); text-align: center; position: relative;">
                <div style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-bottom: var(--space-xs);">
                    <img src="<?= asset('assets/images/brand/sps-logo-white.png') ?>" alt="SPS White Logo" style="height: 38px; width: auto;">
                    <div style="text-align: left;">
                        <div style="font-size: 0.95rem; font-weight: 800; letter-spacing: 0.5px;"><?= $isBn ? 'সনাতন ফিলোসফি এন্ড স্ক্রিপচার' : 'SANATAN PHILOSOPHY & SCRIPTURE' ?></div>
                        <div style="font-size: 0.72rem; color: #94a3b8; letter-spacing: 0.8px;">MEMBER SELF-SERVICE DESK</div>
                    </div>
                </div>
                <h1 style="font-size: 1.45rem; font-weight: 800; margin: 12px 0 4px; color: #ffffff;">
                    <?= $isBn ? 'সদস্য লগইন পোর্টাল' : 'Member Access Portal' ?>
                </h1>
                <p style="font-size: 0.85rem; color: #cbd5e1; margin: 0;">
                    <?= $isBn ? 'আপনার প্রোফাইল, সদস্য কার্ড ও পেমেন্ট হিস্ট্রিতে প্রবেশ করুন' : 'Access your member profile, digital card & payment history' ?>
                </p>
            </div>

            <!-- Card Body Form -->
            <div style="padding: var(--space-xl);">

                <!-- Flash Notifications -->
                <?php if (!empty($error)): ?>
                    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 14px; border-radius: var(--radius-md); margin-bottom: var(--space-lg); font-size: 0.88rem; display: flex; align-items: flex-start; gap: 8px;">
                        <span style="font-size: 1.1rem; line-height: 1;">⚠️</span>
                        <div><?= e($error) ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px 14px; border-radius: var(--radius-md); margin-bottom: var(--space-lg); font-size: 0.88rem; display: flex; align-items: flex-start; gap: 8px;">
                        <span style="font-size: 1.1rem; line-height: 1;">✓</span>
                        <div><?= e($success) ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($info)): ?>
                    <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 12px 14px; border-radius: var(--radius-md); margin-bottom: var(--space-lg); font-size: 0.88rem; display: flex; align-items: flex-start; gap: 8px;">
                        <span style="font-size: 1.1rem; line-height: 1;">ℹ️</span>
                        <div><?= e($info) ?></div>
                    </div>
                <?php endif; ?>

                <form action="<?= url('/membership/login', $currentLocale) ?>" method="POST" id="memberLoginForm">
                    <div style="margin-bottom: var(--space-lg);">
                        <label for="memberIdentifier" style="display: block; font-weight: 700; font-size: 0.92rem; color: var(--primary-deep); margin-bottom: 6px;">
                            <?= $isBn ? 'মেম্বার আইডি, ইমেইল অথবা মোবাইল নম্বর *' : 'Member ID, Email or Phone Number *' ?>
                        </label>
                        <div style="position: relative;">
                            <input type="text" 
                                   id="memberIdentifier" 
                                   name="identifier" 
                                   required 
                                   autofocus
                                   placeholder="<?= $isBn ? 'যেমন: SPS-000872 বা amit.sen@example.com বা 017XXXXXXXX' : 'e.g. SPS-000872 or email@example.com or 017XXXXXXXX' ?>" 
                                   class="form-input" 
                                   style="width: 100%; padding: 12px 14px 12px 40px; border: 1.5px solid var(--border-medium); border-radius: var(--radius-md); font-size: 0.95rem; box-sizing: border-box;">
                            <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-size: 1.1rem; color: var(--text-muted); pointer-events: none;">
                                🪪
                            </span>
                        </div>
                        <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 5px;">
                            <?= $isBn ? '💡 আপনি আপনার মেম্বার কোডের নম্বর (যেমন: 872) অথবা পূর্ণ আইডি (SPS-000872) দিতে পারেন।' : '💡 You can enter your numeric code (e.g. 872) or full ID (SPS-000872).' ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" id="memberLoginSubmitBtn" style="width: 100%; padding: 12px; font-weight: 800; font-size: 1rem; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 12px rgba(180, 83, 9, 0.25);">
                        <span><?= $isBn ? 'সদস্য ড্যাশবোর্ডে প্রবেশ করুন' : 'Log in to Member Dashboard' ?></span>
                        <span>→</span>
                    </button>
                </form>

                <!-- Quick Demo Logins for Pair-Programming & Evaluation -->
                <div style="margin-top: var(--space-xl); padding-top: var(--space-lg); border-top: 1px dashed var(--border-medium);">
                    <div style="font-size: 0.78rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                        <?= $isBn ? '⚡ দ্রুত পরীক্ষামূলক লগইন (Demo Quick Select):' : '⚡ Quick Demo Select:' ?>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        <?php foreach ($demoMembers as $dm): ?>
                            <button type="button" 
                                    onclick="document.getElementById('memberIdentifier').value='<?= e($dm['code']) ?>'; document.getElementById('memberLoginForm').submit();" 
                                    class="btn btn-ghost btn-sm" 
                                    style="justify-content: space-between; text-align: left; padding: 6px 10px; background: #f8fafc; border: 1px solid var(--border-medium); border-radius: var(--radius-md); font-size: 0.8rem; color: var(--text-body);">
                                <span style="font-weight: 700; color: var(--primary-deep);"><?= e($dm['name']) ?></span>
                                <span style="font-family: monospace; font-weight: 800; color: #0284c7; background: #e0f2fe; padding: 1px 6px; border-radius: 4px;"><?= e($dm['code']) ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>

        <!-- Right Column: Membership Essentials & How to Become a Member -->
        <div>
            
            <!-- Not a Member Yet CTA Card -->
            <div class="card" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 2px solid #fde68a; border-radius: var(--radius-xl); padding: var(--space-xl); margin-bottom: var(--space-xl); box-shadow: var(--shadow-sm);">
                <div style="display: inline-flex; align-items: center; gap: 6px; background: #fef08a; color: #854d0e; padding: 4px 12px; border-radius: var(--radius-full); font-size: 0.78rem; font-weight: 800; text-transform: uppercase; margin-bottom: 12px;">
                    <span>✨</span>
                    <span><?= $isBn ? 'নতুন সদস্যদের জন্য' : 'For New Applicants' ?></span>
                </div>
                <h2 style="font-size: 1.35rem; font-weight: 800; color: #92400e; margin: 0 0 8px; line-height: 1.3;">
                    <?= $isBn ? 'আপনি কি এখনো সদস্য নন?' : 'Not a registered member yet?' ?>
                </h2>
                <p style="font-size: 0.95rem; color: #78350f; line-height: 1.6; margin: 0 0 var(--space-lg);">
                    <?= $isBn 
                        ? 'এসপিএস সনাতনী পরিবারে যুক্ত হতে মাত্র কয়েক মিনিটে নির্ধারিত তথ্যসমূহ ও ফি পরিশোধ সম্পন্ন করে অনলাইনে আবেদন জমা দিন। ফিন্যান্স ভেরিফিকেশনের পর সক্রিয় হবে আপনার আজীবন সদস্য আইডি ও ডিজিটাল কার্ড।' 
                        : 'Join the SPS family in just a few minutes. Complete your essential details, submit entry/monthly fee via bKash/Nagad with TrxID & screenshot, and receive your verified digital membership card.' ?>
                </p>

                <div style="background: #ffffff; border: 1px solid #fde68a; border-radius: var(--radius-lg); padding: 14px 16px; margin-bottom: var(--space-lg);">
                    <div style="font-weight: 800; font-size: 0.88rem; color: #92400e; margin-bottom: 8px;">
                        <?= $isBn ? '📋 সদস্যপদ গ্রহণের সহজ ৩ ধাপ:' : '📋 Easy 3-Step Process:' ?>
                    </div>
                    <ul style="margin: 0; padding-left: 20px; font-size: 0.84rem; color: #78350f; line-height: 1.7;">
                        <li><strong><?= $isBn ? 'ধাপ ১:' : 'Step 1:' ?></strong> <?= $isBn ? 'শিক্ষার্থী অথবা উপার্জনশীল ক্যাটাগরি ও প্ল্যান নির্বাচন।' : 'Choose Student or Earning category and plan.' ?></li>
                        <li><strong><?= $isBn ? 'ধাপ ২:' : 'Step 2:' ?></strong> <?= $isBn ? 'ব্যক্তিগত তথ্য, শিক্ষাগত/পেশাগত পরিচিতি প্রদান।' : 'Fill personal, academic or professional credentials.' ?></li>
                        <li><strong><?= $isBn ? 'ধাপ ৩:' : 'Step 3:' ?></strong> <?= $isBn ? 'বিকাশ/নগদে ফি পরিশোধ করে TrxID ও পেমেন্ট স্ক্রিনশট দিয়ে আবেদন জমা।' : 'Pay fee via bKash/Nagad with TrxID and screenshot proof.' ?></li>
                    </ul>
                </div>

                <a href="<?= url('/membership/apply', $currentLocale) ?>" class="btn btn-primary" style="display: flex; align-items: center; justify-content: center; gap: 8px; font-weight: 800; padding: 12px 18px; border-radius: var(--radius-md); text-decoration: none; background: #b45309; border-color: #b45309; box-shadow: 0 4px 12px rgba(180, 83, 9, 0.3);">
                    <span><?= $isBn ? 'সদস্যপদের আবেদন ফরম পূরণ করুন' : 'Apply for SPS Membership Now' ?></span>
                    <span>→</span>
                </a>
            </div>

            <!-- Public Verification & Help Desk Box -->
            <div style="background: #ffffff; border: 1px solid var(--border-medium); border-radius: var(--radius-xl); padding: var(--space-lg); box-shadow: var(--shadow-sm);">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                    <span style="font-size: 1.4rem;">🔍</span>
                    <div>
                        <div style="font-weight: 800; font-size: 0.95rem; color: var(--primary-deep);"><?= $isBn ? 'ডিজিটাল কার্ডের কিউআর কোড যাচাই' : 'Public QR Card Verification' ?></div>
                        <div style="font-size: 0.78rem; color: var(--text-muted);"><?= $isBn ? 'যেকোনো সদস্য কার্ডের কিউআর স্ক্যান করে সত্যতা নিশ্চিত করা যায়' : 'Verify any official member ID directly' ?></div>
                    </div>
                </div>
                <div style="font-size: 0.84rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 12px;">
                    <?= $isBn ? 'অন্য কারো সদস্যপদ যাচাই করতে কার্ডে মুদ্রিত কিউআর কোড স্ক্যান করুন অথবা যাচাই পেজে যান।' : 'Scan the QR code printed on the card to inspect official status.' ?>
                </div>
                <a href="<?= url('/membership/verify', $currentLocale) ?>?code=SPS-000872" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
                    <span><?= $isBn ? 'নমুনা কার্ড যাচাই পেজ দেখুন' : 'View Sample Verification' ?></span>
                    <span>↗</span>
                </a>
            </div>

        </div>

    </div>

</div>
