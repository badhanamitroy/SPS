<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
?>

<!-- SPS Floating Donation Widget (Sanatani 10 Taka Project & SPS Welfare Trust) -->
<aside id="spsDonationWidget" class="sps-floating-donation-wrap" aria-label="<?= $isBn ? 'দান ও সেবা তহবিল' : 'Donation and Welfare Fund' ?>">
    <!-- Floating Action Button (Emblem + Donate Pill Stack) -->
    <button type="button" id="spsDonationTrigger" class="sps-floating-donation-btn" aria-haspopup="dialog" aria-expanded="false" aria-controls="spsDonationModal" title="<?= $isBn ? 'সনাতনী ১০ টাকার প্রজেক্ট ও এসপিএস ওয়েলফেয়ার ট্রাস্টে অনুদান দিন' : 'Donate to Sanatani 10 Taka Project & SPS Welfare Trust' ?>">
        <div class="floating-donate-emblem-wrap">
            <img src="<?= asset('assets/images/brand/sps-10.png') ?>" alt="সনাতনী ১০ টাকার প্রজেক্ট" class="floating-donate-emblem" width="84" height="84" loading="eager">
            <span class="donation-pulse-dot" aria-hidden="true"></span>
        </div>
        <div class="floating-donate-pill">
            <span class="floating-donate-text">Donate</span>
        </div>
    </button>

    <!-- Modal Backdrop -->
    <div id="spsDonationBackdrop" class="sps-donation-backdrop" aria-hidden="true"></div>

    <!-- Interactive Donation Drawer / Modal -->
    <div id="spsDonationModal" class="sps-donation-modal" role="dialog" aria-modal="true" aria-labelledby="donationModalTitle" tabindex="-1">
        <div class="donation-modal-card">
            
            <!-- Modal Header -->
            <div class="donation-modal-header">
                <div class="header-brand-group">
                    <img src="<?= asset('assets/images/brand/sps-logo-white.png') ?>" alt="SPS Logo" class="modal-sps-logo">
                    <div>
                        <h3 id="donationModalTitle" class="modal-main-title">
                            <?= $isBn ? 'সেবা ও মানবকল্যাণ তহবিল' : 'SPS Seva & Welfare Trust' ?>
                        </h3>
                        <p class="modal-sub-title">
                            <?= $isBn 
                                ? 'সনাতনী ১০ টাকার প্রজেক্ট ও এসপিএস ওয়েলফেয়ার ট্রাস্ট' 
                                : 'Sanatani 10 Taka Project & SPS Welfare Initiatives' ?>
                        </p>
                    </div>
                </div>
                <button type="button" id="spsDonationClose" class="donation-close-btn" aria-label="<?= $isBn ? 'বন্ধ করুন' : 'Close' ?>">
                    ✕
                </button>
            </div>

            <!-- Tab Switcher -->
            <div class="donation-tab-bar" role="tablist">
                <button type="button" class="donation-tab active" data-target="#tab10Taka" role="tab" aria-selected="true" id="tab10TakaBtn">
                    <span class="tab-badge">🪙</span>
                    <span><?= $isBn ? 'সনাতনী ১০ টাকা' : 'Sanatani 10 Taka' ?></span>
                </button>
                <button type="button" class="donation-tab" data-target="#tabWelfare" role="tab" aria-selected="false" id="tabWelfareBtn">
                    <span class="tab-badge">🏛️</span>
                    <span><?= $isBn ? 'ওয়েলফেয়ার ট্রাস্ট' : 'Welfare Trust' ?></span>
                </button>
                <button type="button" class="donation-tab" data-target="#tabInvoice" role="tab" aria-selected="false" id="tabInvoiceBtn" style="color: #92400e; font-weight: 800;">
                    <span class="tab-badge">📄</span>
                    <span><?= $isBn ? 'মানি রসিদ' : 'Get Receipt' ?></span>
                </button>
            </div>

            <!-- Modal Body / Tab Panes -->
            <div class="donation-modal-body">
                
                <!-- PANE 1: Sanatani 10 Taka Project -->
                <div id="tab10Taka" class="donation-pane active" role="tabpanel" aria-labelledby="tab10TakaBtn">
                    <div class="project-hero-card project-10taka-hero">
                        <div class="project-hero-left">
                            <img src="<?= asset('assets/images/brand/sps-10.png') ?>" alt="Sanatani 10 Taka Project Logo" class="project-hero-logo">
                        </div>
                        <div class="project-hero-right">
                            <h4 class="project-hero-title"><?= $isBn ? 'প্রতিদিন মাত্র ১০ টাকায় সনাতনী জাগরণ' : 'Sanatani 10 Taka Daily/Monthly Project' ?></h4>
                            <p class="project-hero-desc">
                                <?= $isBn 
                                    ? 'প্রতিদিন ১০ টাকা (বা মাসে ৩০০ টাকা) জমিয়ে সনাতনী শিক্ষা বিস্তার, শাস্ত্র প্রকাশনা ও দুস্থ সমাজের পাশে দাঁড়ান।' 
                                    : 'Contribute just 10 Taka daily (or 300 Taka monthly) to support scripture distribution, youth education & student welfare.' ?>
                            </p>
                        </div>
                    </div>

                    <div class="impact-pills">
                        <div class="impact-pill">📖 <?= $isBn ? 'গীতা ও বেদান্ত বিতরণ' : 'Scripture Distribution' ?></div>
                        <div class="impact-pill">🎓 <?= $isBn ? 'মেধাবী শিক্ষার্থী সহায়তা' : 'Student Stipends' ?></div>
                        <div class="impact-pill">🚩 <?= $isBn ? 'ধর্মসংস্কৃতি সুরক্ষা' : 'Heritage Preservation' ?></div>
                    </div>
                </div>

                <!-- PANE 2: SPS Welfare Trust -->
                <div id="tabWelfare" class="donation-pane" role="tabpanel" aria-labelledby="tabWelfareBtn" style="display: none;">
                    <div class="project-hero-card project-welfare-hero">
                        <div class="project-hero-left">
                            <img src="<?= asset('assets/images/brand/sps-logo.png') ?>" alt="SPS Welfare Logo" class="project-hero-logo brand-mark-dark">
                            <img src="<?= asset('assets/images/brand/sps-logo-white.png') ?>" alt="SPS Welfare Logo" class="project-hero-logo brand-mark-light">
                        </div>
                        <div class="project-hero-right">
                            <h4 class="project-hero-title"><?= $isBn ? 'এসপিএস ওয়েলফেয়ার ট্রাস্টের জরুরি সেবা' : 'SPS Humanitarian & Disaster Relief' ?></h4>
                            <p class="project-hero-desc">
                                <?= $isBn 
                                    ? 'বন্যা, প্রাকৃতিক দুর্যোগ ও সংকটাপন্ন সময়ে দুর্গত এলাকায় খাদ্য, চিকিৎসা সামগ্রী ও পুনর্বাসন সহায়তা প্রদান।' 
                                    : 'Emergency food, medical supplies, and rehabilitation aid during floods, crises, and natural disasters.' ?>
                            </p>
                        </div>
                    </div>

                    <div class="impact-pills">
                        <div class="impact-pill">🌊 <?= $isBn ? 'বন্যা ও দুর্যোগে খাদ্য বিতরণ' : 'Flood & Disaster Relief' ?></div>
                        <div class="impact-pill">💊 <?= $isBn ? 'দরিদ্র রোগীর চিকিৎসা ফান্ড' : 'Emergency Medical Fund' ?></div>
                        <div class="impact-pill">🛕 <?= $isBn ? 'মন্দির ও শ্মশান উন্নয়ন' : 'Sanctuary Renovation' ?></div>
                    </div>
                </div>

                <!-- PANE 3: Instant Invoice & Money Receipt for Members & Donors -->
                <div id="tabInvoice" class="donation-pane" role="tabpanel" aria-labelledby="tabInvoiceBtn" style="display: none;">
                    <div style="background: linear-gradient(135deg, #fffbeb, #fef3c7); border: 1px solid #fde68a; border-radius: var(--radius-lg); padding: 14px 16px; margin-bottom: 16px; display: flex; align-items: flex-start; gap: 12px;">
                        <span style="font-size: 1.6rem; line-height: 1;">📜</span>
                        <div>
                            <h4 style="margin: 0 0 4px; font-size: 0.95rem; font-weight: 800; color: #92400e;">
                                <?= $isBn ? 'সদস্য বা শুভানুধ্যায়ী — যে কেউ যে কোনো পরিমাণ দানের মানি রসিদ পাবেন' : 'Official Money Receipt for Any Donor / Member' ?>
                            </h4>
                            <p style="margin: 0; font-size: 0.82rem; color: #78350f; line-height: 1.4;">
                                <?= $isBn 
                                    ? 'টাকা পাঠানোর পর প্রেরকের নাম, মোবাইল ও TrxID দিয়ে অর্থ সম্পাদকের স্বাক্ষরযুক্ত অফিসিয়াল রসিদ তাৎক্ষণিক সংগ্রহ ও প্রিন্ট করুন।' 
                                    : 'Submit your donation details and TrxID to generate your official signed SPS receipt instantly.' ?>
                            </p>
                        </div>
                    </div>

                    <!-- Instant Receipt Generator Form -->
                    <form action="<?= url('/donation/submit', $currentLocale) ?>" method="POST" style="margin-bottom: 18px;">
                        <?= csrf_field() ?>
                        <div style="display:none!important;" aria-hidden="true">
                            <input type="text" name="_hp_website" value="" tabindex="-1" autocomplete="off">
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                                    <?= $isBn ? 'আপনার পূর্ণ নাম *' : 'Full Name *' ?>
                                </label>
                                <input type="text" name="donor_name" required placeholder="<?= $isBn ? 'উদাঃ অমিত রায়' : 'e.g. Amit Roy' ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-medium); border-radius: var(--radius-md); font-size: 0.86rem; box-sizing: border-box;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                                    <?= $isBn ? 'মোবাইল / প্রেরক নম্বর *' : 'Mobile Phone *' ?>
                                </label>
                                <input type="text" name="donor_phone" required placeholder="017XXXXXXXX" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-medium); border-radius: var(--radius-md); font-size: 0.86rem; box-sizing: border-box;">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                                    <?= $isBn ? 'অনুদানের খাত / প্রজেক্ট *' : 'Purpose / Project *' ?>
                                </label>
                                <select name="purpose" style="width: 100%; padding: 8px 10px; border: 1px solid var(--border-medium); border-radius: var(--radius-md); font-size: 0.84rem; box-sizing: border-box; background: #fff;">
                                    <option value="Sanatani 10 Taka Project"><?= $isBn ? 'সনাতনী ১০ টাকার প্রজেক্ট' : 'Sanatani 10 Taka Project' ?></option>
                                    <option value="SPS Welfare Trust"><?= $isBn ? 'এসপিএস ওয়েলফেয়ার ট্রাস্ট' : 'SPS Welfare Trust' ?></option>
                                    <option value="Scripture Publication Fund"><?= $isBn ? 'শাস্ত্র প্রকাশনা ও প্রচার ফান্ড' : 'Scripture Publication' ?></option>
                                    <option value="Emergency Relief"><?= $isBn ? 'জরুরি ত্রাণ ও চিকিৎসা তহবিল' : 'Emergency Relief' ?></option>
                                    <option value="General Donation"><?= $isBn ? 'সাধারণ কল্যাণ অনুদান' : 'General Donation' ?></option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                                    <?= $isBn ? 'টাকার পরিমাণ (৳) *' : 'Amount Paid (৳) *' ?>
                                </label>
                                <input type="number" name="amount" min="1" step="1" required placeholder="৳ 10, 300, 500, 1000..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-medium); border-radius: var(--radius-md); font-size: 0.86rem; box-sizing: border-box;">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px;">
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                                    <?= $isBn ? 'পেমেন্ট মাধ্যম *' : 'Payment Method *' ?>
                                </label>
                                <select name="payment_method" style="width: 100%; padding: 8px 10px; border: 1px solid var(--border-medium); border-radius: var(--radius-md); font-size: 0.84rem; box-sizing: border-box; background: #fff;">
                                    <option value="bKash">bKash (বিকাশ)</option>
                                    <option value="Nagad">Nagad (নগদ)</option>
                                    <option value="Rocket">Rocket (রকেট)</option>
                                    <option value="Bank Transfer">Bank Transfer (ব্যাংক)</option>
                                    <option value="Cash / Direct">Cash / সরাসরি নগদ</option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                                    <?= $isBn ? 'ট্রানজেকশন আইডি (TrxID) *' : 'Transaction ID (TrxID) *' ?>
                                </label>
                                <input type="text" name="trx_id" required placeholder="<?= $isBn ? 'উদাঃ BL29XK49PQ' : 'e.g. BL29XK49PQ' ?>" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-medium); border-radius: var(--radius-md); font-size: 0.86rem; font-family: monospace; box-sizing: border-box; text-transform: uppercase;">
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <span style="font-size: 0.74rem; color: var(--text-muted);">
                                🔒 <?= $isBn ? 'ফাইন্যান্স অডিটের পর স্বয়ংক্রিয় ভেরিফাইড সিল যুক্ত হবে' : 'Finance audit will seal invoice' ?>
                            </span>
                            <button type="submit" class="btn btn-sm btn-primary" style="background: linear-gradient(135deg, #d97706, #b45309); border: none; font-weight: 800; padding: 8px 18px; border-radius: var(--radius-md); box-shadow: 0 4px 10px rgba(180, 83, 9, 0.3);">
                                📄 <?= $isBn ? 'মানি রসিদ দেখুন ও প্রিন্ট করুন' : 'Generate & View Receipt' ?>
                            </button>
                        </div>
                    </form>

                    <!-- Quick Lookup Form -->
                    <div style="border-top: 1px dashed var(--border-medium); padding-top: 14px;">
                        <span style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 6px;">
                            🔍 <?= $isBn ? 'পূর্বে সংগৃহীত রসিদ দেখতে ট্রানজেকশন বা TrxID দিন:' : 'Search existing receipt by TxID or TrxID:' ?>
                        </span>
                        <form action="<?= url('/invoice', $currentLocale) ?>" method="GET" style="display: flex; gap: 8px; margin: 0;">
                            <input type="text" name="id" placeholder="<?= $isBn ? 'TrxID বা রসিদ আইডি লিখুন...' : 'Enter TrxID or Receipt ID...' ?>" required style="flex: 1; padding: 7px 12px; border: 1px solid var(--border-medium); border-radius: var(--radius-md); font-size: 0.82rem; font-family: monospace;">
                            <button type="submit" class="btn btn-sm btn-secondary" style="font-size: 0.82rem; padding: 7px 14px; font-weight: 700;">
                                <?= $isBn ? 'খুঁজুন' : 'Search' ?>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Payment Accounts & Fast Copy Desk -->
                <div class="donation-payment-desk">
                    <div class="payment-desk-header">
                        <span class="desk-title">💳 <?= $isBn ? 'অনুদানের অফিসিয়াল মাধ্যম (বিকাশ / নগদ / রকেট / ব্যাংক)' : 'Official Donation Channels' ?></span>
                        <span class="desk-tag"><?= $isBn ? '১০০% নিরাপদ ও স্বচ্ছ' : '100% Transparent' ?></span>
                    </div>

                    <div class="payment-methods-grid">
                        <!-- bKash -->
                        <div class="payment-method-card bkash-card">
                            <div class="method-header">
                                <span class="method-badge bkash-badge">bKash</span>
                                <span class="method-type"><?= $isBn ? 'Send Money / Personal' : 'Send Money' ?></span>
                            </div>
                            <div class="method-number-row">
                                <code class="account-number" id="bkashNum">01736-360041</code>
                                <button type="button" class="copy-btn" onclick="copyDonationText('01736360041', this)" title="Copy bKash Number">
                                    <span class="copy-icon">📋</span> <span class="copy-label"><?= $isBn ? 'কপি' : 'Copy' ?></span>
                                </button>
                            </div>
                        </div>

                        <!-- Nagad -->
                        <div class="payment-method-card nagad-card">
                            <div class="method-header">
                                <span class="method-badge nagad-badge">Nagad</span>
                                <span class="method-type"><?= $isBn ? 'Send Money / Personal' : 'Send Money' ?></span>
                            </div>
                            <div class="method-number-row">
                                <code class="account-number" id="nagadNum">01736-360041</code>
                                <button type="button" class="copy-btn" onclick="copyDonationText('01736360041', this)" title="Copy Nagad Number">
                                    <span class="copy-icon">📋</span> <span class="copy-label"><?= $isBn ? 'কপি' : 'Copy' ?></span>
                                </button>
                            </div>
                        </div>

                        <!-- Rocket -->
                        <div class="payment-method-card rocket-card">
                            <div class="method-header">
                                <span class="method-badge rocket-badge">Rocket</span>
                                <span class="method-type"><?= $isBn ? 'Personal' : 'Personal' ?></span>
                            </div>
                            <div class="method-number-row">
                                <code class="account-number" id="rocketNum">01736-360041-0</code>
                                <button type="button" class="copy-btn" onclick="copyDonationText('017363600410', this)" title="Copy Rocket Number">
                                    <span class="copy-icon">📋</span> <span class="copy-label"><?= $isBn ? 'কপি' : 'Copy' ?></span>
                                </button>
                            </div>
                        </div>

                        <!-- Bank Account -->
                        <div class="payment-method-card bank-card">
                            <div class="method-header">
                                <span class="method-badge bank-badge">Bank Transfer</span>
                                <span class="method-type"><?= $isBn ? 'ব্যাংক একাউন্ট' : 'Direct Bank' ?></span>
                            </div>
                            <div class="method-number-row">
                                <div>
                                    <div style="font-size:0.75rem; color:#64748b;"><?= $isBn ? 'হিসাব নাম: ' : 'A/C: ' ?><strong>Sanatan Philosophy & Scripture</strong></div>
                                    <code class="account-number" style="font-size:0.8rem;">A/C: 20503980200547000</code>
                                </div>
                                <button type="button" class="copy-btn" onclick="copyDonationText('20503980200547000', this)" title="Copy Bank Account">
                                    <span class="copy-icon">📋</span> <span class="copy-label"><?= $isBn ? 'কপি' : 'Copy' ?></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Reference Guidance & Confirmation -->
                    <div class="payment-notice-bar">
                        <span class="notice-icon">💡</span>
                        <span>
                            <?= $isBn 
                                ? 'বিকাশ বা নগদে পাঠানোর সময় রেফারেন্সে <strong>10Taka</strong> অথবা <strong>Welfare</strong> লিখুন।' 
                                : 'Please write <strong>10Taka</strong> or <strong>Welfare</strong> in the payment Reference field.' ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="donation-modal-footer">
                <div class="footer-meta-info">
                    <span class="verified-icon">🛡️</span>
                    <span><?= $isBn ? 'সকল আয়-ব্যয়ের হিসাব ওয়েবসাইট ও ফেসবুক পেজে নিয়মিত উন্মুক্ত করা হয়।' : 'All financial reports are published transparently.' ?></span>
                </div>
                <div class="footer-actions">
                    <a href="https://wa.me/8801736360041?text=<?= urlencode($isBn ? 'নমস্কার, আমি সনাতনী ১০ টাকার প্রজেক্ট / এসপিএস ওয়েলফেয়ার ট্রাস্টে অনুদান পাঠিয়েছি।' : 'Hare Krishna, I have sent a donation for SPS 10 Taka / Welfare Trust.') ?>" target="_blank" rel="noopener noreferrer" class="btn-whatsapp-confirm">
                        <span>💬 <?= $isBn ? 'হোয়াটসঅ্যাপে জানান' : 'Confirm on WhatsApp' ?></span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</aside>

<style>
/* -------------------------------------------------------------
 * SPS Floating Donation Widget Styles
 * ------------------------------------------------------------- */
.sps-floating-donation-wrap {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 1040;
    font-family: 'Plus Jakarta Sans', 'Noto Sans Bengali', -apple-system, sans-serif;
}

/* Floating Action Button (Emblem + Donate Pill Stack) */
.sps-floating-donation-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    background: transparent;
    border: none;
    padding: 0;
    cursor: pointer;
    outline: none;
    user-select: none;
    transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), filter 0.3s ease;
    filter: drop-shadow(0 10px 22px rgba(0, 0, 0, 0.25));
}

.sps-floating-donation-btn:hover {
    transform: translateY(-5px) scale(1.05);
    filter: drop-shadow(0 16px 30px rgba(4, 67, 39, 0.38));
}

.sps-floating-donation-btn:active {
    transform: translateY(0) scale(0.97);
}

.floating-donate-emblem-wrap {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: -5px;
    z-index: 2;
}

.floating-donate-emblem {
    width: 84px;
    height: auto;
    max-height: 84px;
    object-fit: contain;
    display: block;
    filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.15));
    transition: transform 0.3s ease;
}

.sps-floating-donation-btn:hover .floating-donate-emblem {
    transform: scale(1.04);
}

.donation-pulse-dot {
    position: absolute;
    top: 6px;
    right: 6px;
    width: 14px;
    height: 14px;
    background: #22c55e;
    border: 2.5px solid #ffffff;
    border-radius: 50%;
    animation: donationPulse 2s infinite;
}

@keyframes donationPulse {
    0% {
        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
    }
    70% {
        box-shadow: 0 0 0 9px rgba(34, 197, 94, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
    }
}

.floating-donate-pill {
    background: #ffffff;
    border: 3.5px solid #044327;
    border-radius: 9999px;
    padding: 3px 22px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.14);
    z-index: 3;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 104px;
    transition: all 0.25s ease;
}

.sps-floating-donation-btn:hover .floating-donate-pill {
    background: #044327;
    border-color: #044327;
    box-shadow: 0 6px 16px rgba(4, 67, 39, 0.4);
}

.floating-donate-text {
    font-size: 1.15rem;
    font-weight: 800;
    color: #044327;
    letter-spacing: 0.5px;
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    line-height: 1.25;
    transition: color 0.25s ease;
}

.sps-floating-donation-btn:hover .floating-donate-text {
    color: #ffffff;
}

/* Modal Backdrop */
.sps-donation-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    z-index: 1045;
    opacity: 0;
    transition: opacity 0.25s ease;
}
.sps-donation-backdrop.open {
    display: block;
    opacity: 1;
}

/* Modal Dialog */
.sps-donation-modal {
    display: none;
    position: fixed;
    right: 24px;
    bottom: 130px;
    width: 480px;
    max-width: calc(100vw - 32px);
    max-height: calc(100vh - 150px);
    z-index: 1050;
    outline: none;
    opacity: 0;
    transform: translateY(18px) scale(0.96);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.sps-donation-modal.open {
    display: block;
    opacity: 1;
    transform: translateY(0) scale(1);
}

.donation-modal-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.35);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    max-height: calc(100vh - 120px);
}

/* Header */
.donation-modal-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    color: #ffffff;
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #d97706;
}
.header-brand-group {
    display: flex;
    align-items: center;
    gap: 12px;
}
.modal-sps-logo {
    height: 38px;
    width: auto;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
}
.modal-main-title {
    font-size: 1.12rem;
    font-weight: 800;
    color: #ffffff;
    margin: 0;
    line-height: 1.25;
}
.modal-sub-title {
    font-size: 0.74rem;
    color: #fde68a;
    margin: 2px 0 0 0;
    font-weight: 600;
}
.donation-close-btn {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #cbd5e1;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.2s;
}
.donation-close-btn:hover {
    background: #ef4444;
    color: #ffffff;
    border-color: #dc2626;
    transform: rotate(90deg);
}

/* Tab Bar */
.donation-tab-bar {
    display: grid;
    grid-template-columns: 1fr 1fr;
    background: #f1f5f9;
    padding: 6px;
    gap: 6px;
    border-bottom: 1px solid #e2e8f0;
}
.donation-tab {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px 12px;
    border: none;
    border-radius: 10px;
    background: transparent;
    color: #475569;
    font-weight: 700;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s;
    font-family: inherit;
}
.donation-tab.active {
    background: #ffffff;
    color: #b45309;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    border: 1px solid #e2e8f0;
}

/* Body */
.donation-modal-body {
    padding: 18px 20px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

/* Hero Cards */
.project-hero-card {
    border-radius: 14px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 14px;
}
.project-10taka-hero {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    border: 1.5px solid #fde68a;
}
.project-welfare-hero {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 1.5px solid #bbf7d0;
}
.project-hero-logo {
    width: 54px;
    height: 54px;
    object-fit: contain;
    filter: drop-shadow(0 2px 6px rgba(0,0,0,0.12));
    flex-shrink: 0;
}
.project-hero-title {
    font-size: 0.96rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 3px 0;
    line-height: 1.3;
}
.project-hero-desc {
    font-size: 0.78rem;
    color: #475569;
    margin: 0;
    line-height: 1.45;
}

/* Impact Pills */
.impact-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: -6px;
}
.impact-pill {
    font-size: 0.74rem;
    font-weight: 700;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #334155;
    padding: 4px 10px;
    border-radius: 9999px;
}

/* Payment Desk */
.donation-payment-desk {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 14px;
}
.payment-desk-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    font-size: 0.84rem;
}
.desk-title {
    font-weight: 800;
    color: #0f172a;
}
.desk-tag {
    font-size: 0.7rem;
    font-weight: 800;
    color: #16a34a;
    background: #dcfce7;
    padding: 2px 8px;
    border-radius: 9999px;
}

.payment-methods-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}
.payment-method-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    padding: 8px 10px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 6px;
    transition: all 0.2s;
}
.payment-method-card:hover {
    border-color: #b45309;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}
.payment-method-card.bank-card {
    grid-column: 1 / -1;
}

.method-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.method-badge {
    font-size: 0.72rem;
    font-weight: 800;
    padding: 1px 6px;
    border-radius: 4px;
    text-transform: uppercase;
}
.bkash-badge { background: #fdf2f8; color: #be185d; border: 1px solid #fbcfe8; }
.nagad-badge { background: #fff7ed; color: #ea580c; border: 1px solid #ffedd5; }
.rocket-badge { background: #faf5ff; color: #7e22ce; border: 1px solid #f3e8ff; }
.bank-badge { background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; }

.method-type {
    font-size: 0.68rem;
    color: #64748b;
}
.method-number-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 6px;
}
.account-number {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.86rem;
    font-weight: 800;
    color: #0f172a;
    background: #f1f5f9;
    padding: 2px 6px;
    border-radius: 4px;
    user-select: all;
}
.copy-btn {
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    color: #334155;
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 0.72rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.2s;
    font-family: inherit;
}
.copy-btn:hover {
    background: #b45309;
    color: #ffffff;
    border-color: #b45309;
}
.copy-btn.copied {
    background: #16a34a !important;
    color: #ffffff !important;
    border-color: #16a34a !important;
}

.payment-notice-bar {
    margin-top: 10px;
    background: #fefce8;
    border: 1px dashed #facc15;
    border-radius: 8px;
    padding: 8px 10px;
    font-size: 0.75rem;
    color: #854d0e;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Footer */
.donation-modal-footer {
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    padding: 12px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}
.footer-meta-info {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.74rem;
    color: #64748b;
    max-width: 250px;
    line-height: 1.3;
}
.btn-whatsapp-confirm {
    background: #25d366;
    color: #ffffff !important;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 800;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 2px 6px rgba(37, 211, 102, 0.35);
    transition: all 0.2s;
}
.btn-whatsapp-confirm:hover {
    background: #1eb857;
    transform: translateY(-1px);
}

/* Mobile Responsiveness */
@media (max-width: 600px) {
    .sps-floating-donation-wrap {
        bottom: 18px;
        right: 18px;
    }
    .sps-floating-donation-btn {
        padding: 6px 14px 6px 8px;
    }
    .sps-floating-donation-wrap {
        bottom: 16px;
        right: 16px;
    }
    .floating-donate-emblem {
        width: 68px;
        max-height: 68px;
    }
    .floating-donate-pill {
        padding: 2px 16px;
        border-width: 3px;
        min-width: 86px;
    }
    .floating-donate-text {
        font-size: 0.98rem;
    }
    .sps-donation-modal {
        right: 12px;
        bottom: 110px;
        width: calc(100vw - 24px);
    }
    .payment-methods-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const trigger = document.getElementById('spsDonationTrigger');
    const modal = document.getElementById('spsDonationModal');
    const backdrop = document.getElementById('spsDonationBackdrop');
    const closeBtn = document.getElementById('spsDonationClose');
    const tabs = document.querySelectorAll('.donation-tab');
    const panes = document.querySelectorAll('.donation-pane');

    if (!trigger || !modal) return;

    function openModal() {
        modal.classList.add('open');
        backdrop.classList.add('open');
        trigger.setAttribute('aria-expanded', 'true');
    }

    function closeModal() {
        modal.classList.remove('open');
        backdrop.classList.remove('open');
        trigger.setAttribute('aria-expanded', 'false');
    }

    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        if (modal.classList.contains('open')) {
            closeModal();
        } else {
            openModal();
        }
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }
    if (backdrop) {
        backdrop.addEventListener('click', closeModal);
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('open')) {
            closeModal();
        }
    });

    // Tab Switching
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            tabs.forEach(t => {
                t.classList.remove('active');
                t.setAttribute('aria-selected', 'false');
            });
            panes.forEach(p => {
                p.classList.remove('active');
                p.style.display = 'none';
            });

            this.classList.add('active');
            this.setAttribute('aria-selected', 'true');
            const targetId = this.getAttribute('data-target');
            const targetPane = document.querySelector(targetId);
            if (targetPane) {
                targetPane.classList.add('active');
                targetPane.style.display = 'block';
            }
        });
    });
});

// Copy Payment Text helper
window.copyDonationText = function(text, btnElement) {
    if (!navigator.clipboard) {
        const temp = document.createElement('input');
        temp.value = text;
        document.body.appendChild(temp);
        temp.select();
        document.execCommand('copy');
        document.body.removeChild(temp);
    } else {
        navigator.clipboard.writeText(text);
    }

    if (btnElement) {
        const originalHtml = btnElement.innerHTML;
        btnElement.classList.add('copied');
        btnElement.innerHTML = '<span class="copy-icon">✓</span> <span>কপি হয়েছে</span>';
        setTimeout(() => {
            btnElement.classList.remove('copied');
            btnElement.innerHTML = originalHtml;
        }, 2000);
    }
};
</script>
