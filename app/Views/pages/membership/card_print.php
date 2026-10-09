<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$member = $member ?? [];
$category = $category ?? [];
$plan = $plan ?? [];
$allMembers = $allMembers ?? [];
?>
<!DOCTYPE html>
<html lang="<?= $currentLocale ?>" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($metaTitle ?? 'Digital Member Card Print | Sanatan Philosophy & Scripture') ?></title>
    <link rel="icon" type="image/png" href="<?= asset('favicon.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=JetBrains+Mono:wght@600;700;800&family=Noto+Sans+Bengali:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --card-w: 480px;
            --card-h: 302px;
            --gold-border: #bfa054;
            --gold-accent: #d4af37;
            --neon-blue: #0284c7;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', 'Noto Sans Bengali', sans-serif;
            background: #cbd5e1;
            background-image: 
                radial-gradient(rgba(15, 23, 42, 0.08) 1px, transparent 1px),
                linear-gradient(to bottom, #dbe2e8 0%, #cbd5e1 100%);
            background-size: 20px 20px, 100% 100%;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 26px 16px;
        }

        /* Non-printable Control Toolbar */
        .print-toolbar {
            width: 100%;
            max-width: 1010px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            padding: 14px 22px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        }
        .toolbar-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .toolbar-title h2 {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 2px;
        }
        .toolbar-title p {
            font-size: 0.8rem;
            color: #64748b;
            margin: 0;
        }
        .toolbar-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        .member-select {
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 0.85rem;
            font-weight: 600;
            background: #f8fafc;
            color: #0f172a;
            cursor: pointer;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            font-family: inherit;
        }
        .btn-print {
            background: linear-gradient(135deg, #c65a1e 0%, #a64713 100%);
            color: #ffffff;
            border: 1px solid #8c360b;
            box-shadow: 0 2px 8px rgba(198, 90, 30, 0.35);
        }
        .btn-print:hover {
            background: #8c360b;
            transform: translateY(-1px);
        }
        .btn-back {
            background: #f8fafc;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .btn-back:hover {
            background: #e2e8f0;
        }

        /* Two Cards Layout Container (Front & Back) */
        .cards-container {
            display: flex;
            flex-wrap: wrap;
            gap: 32px;
            justify-content: center;
            align-items: center;
            margin-bottom: 28px;
            perspective: 1000px;
        }

        /* The Card Physical Box */
        .id-card {
            width: var(--card-w);
            height: var(--card-h);
            border-radius: 18px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.42), 0 4px 14px rgba(0, 0, 0, 0.28);
            border: 2px solid var(--gold-border);
            color: #ffffff;
            page-break-inside: avoid;
            break-inside: avoid;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px 22px;
            background: 
                radial-gradient(ellipse 120% 70% at 50% 35%, rgba(56, 189, 248, 0.08) 0%, transparent 65%),
                repeating-linear-gradient(90deg, rgba(255, 255, 255, 0.012) 0px, rgba(255, 255, 255, 0.012) 1px, transparent 1px, transparent 3px),
                linear-gradient(175deg, #091723 0%, #122838 30%, #19354a 50%, #102332 70%, #07131d 100%) !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        /* ---------------- FRONT CARD ---------------- */
        .card-front {
            position: relative;
        }
        .card-watermark {
            position: absolute;
            right: 14px;
            top: 52%;
            transform: translateY(-50%);
            width: 180px;
            height: 180px;
            opacity: 0.13;
            pointer-events: none;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-watermark img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: brightness(1.2) contrast(1.1);
            display: block;
        }

        /* Front Header */
        .front-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
            z-index: 2;
        }
        .brand-block {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .brand-logo {
            height: 38px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.4));
        }
        .brand-title-bn {
            font-size: 1.05rem;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.25;
            letter-spacing: 0.3px;
            font-family: 'Noto Sans Bengali', sans-serif;
            text-shadow: 0 1px 3px rgba(0,0,0,0.5);
        }
        .brand-title-en {
            font-size: 0.68rem;
            font-weight: 600;
            color: #cbd5e1;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin-top: 1px;
        }

        /* EMV Golden Chip */
        .card-chip-wrap {
            width: 44px;
            height: 34px;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.45);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 0.5px solid rgba(255, 255, 255, 0.25);
        }

        /* Front Body: Photo & Member Details */
        .front-body {
            display: flex;
            align-items: center;
            gap: 18px;
            margin: 8px 0;
            position: relative;
            z-index: 2;
        }
        .member-photo-frame {
            width: 82px;
            height: 82px;
            border-radius: 13px;
            border: 2.5px solid #c59b27;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.45);
            background: #1e293b;
            flex-shrink: 0;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .member-photo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .member-photo-placeholder {
            font-size: 2.4rem;
            color: #cbd5e1;
        }
        .member-meta {
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
        }
        .member-name-bn {
            font-size: 1.35rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.2;
            letter-spacing: 0.2px;
            font-family: 'Noto Sans Bengali', sans-serif;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.45);
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }
        .member-name-en {
            font-size: 0.95rem;
            font-weight: 500;
            color: #e2e8f0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin-top: 3px;
            margin-bottom: 6px;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }
        .member-id-pill {
            display: inline-flex;
            align-items: center;
            align-self: flex-start;
            background: #9fc6e2;
            color: #082136;
            font-family: 'JetBrains Mono', 'Plus Jakarta Sans', monospace;
            font-size: 0.95rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            padding: 3px 13px;
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 0 10px rgba(159, 198, 226, 0.35);
        }

        /* Front Footer: Category, Validity & QR */
        .front-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            position: relative;
            z-index: 2;
        }
        .front-footer-meta {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .member-category-title {
            font-size: 0.98rem;
            font-weight: 700;
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            letter-spacing: 0.3px;
        }
        .member-validity-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: #22c55e;
            letter-spacing: 0.5px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .card-qr-box {
            background: #ffffff;
            padding: 4px;
            border-radius: 8px;
            width: 62px;
            height: 62px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
            box-sizing: border-box;
        }
        .card-qr-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
            border-radius: 4px;
        }

        /* ---------------- BACK CARD ---------------- */
        .card-back {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .back-header {
            text-align: center;
            margin-bottom: 2px;
        }
        .back-brand-bn {
            font-size: 1.15rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.4px;
            font-family: 'Noto Sans Bengali', sans-serif;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.45);
        }
        .back-brand-en {
            font-size: 0.8rem;
            font-weight: 600;
            color: #cbd5e1;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin-top: 1px;
        }

        /* Neon Cyan Border Certification Box */
        .back-cert-box {
            background: rgba(6, 17, 27, 0.75);
            border: 1.5px solid var(--neon-blue);
            border-radius: 8px;
            padding: 9px 14px;
            box-shadow: 0 0 16px rgba(2, 132, 199, 0.4), inset 0 0 8px rgba(2, 132, 199, 0.12);
            color: #ffffff;
            font-size: 0.82rem;
            font-weight: 400;
            line-height: 1.45;
            font-family: 'Plus Jakarta Sans', sans-serif;
            text-align: left;
            margin: 6px 0 8px;
        }

        /* Information Grid */
        .back-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            row-gap: 5px;
            column-gap: 16px;
            font-size: 0.85rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin-bottom: 2px;
        }
        .info-cell {
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }
        .info-label {
            color: #cbd5e1;
            font-weight: 500;
        }
        .info-value {
            color: #ffffff;
            font-weight: 700;
        }

        /* Glowing Cyan Divider Line */
        .back-divider-line {
            height: 1px;
            width: 100%;
            background: linear-gradient(90deg, transparent 0%, rgba(56, 189, 248, 0.3) 20%, rgba(56, 189, 248, 0.7) 50%, rgba(56, 189, 248, 0.3) 80%, transparent 100%);
            margin: 6px 0;
        }

        /* Back Footer: Issued / Verify & Signatory */
        .back-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-top: 2px;
        }
        .back-footer-left {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .issued-date {
            font-size: 0.76rem;
            color: #94a3b8;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .verify-code {
            font-size: 0.72rem;
            color: #94a3b8;
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: 0.4px;
        }
        .back-footer-right {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .signatory-name {
            font-family: 'Cinzel', serif;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--gold-accent);
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.6);
            padding-bottom: 2px;
            margin-bottom: 2px;
            width: 100%;
            text-align: center;
        }
        .signatory-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.62rem;
            font-weight: 600;
            color: #94a3b8;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        @page {
            size: auto;
            margin: 10mm;
        }

        /* Strict Print Styles */
        @media print {
            html, body {
                display: block !important;
                visibility: visible !important;
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                height: auto !important;
                overflow: visible !important;
            }
            .no-print,
            .print-toolbar {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .cards-container {
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: wrap !important;
                gap: 20px !important;
                margin: 10mm auto !important;
                justify-content: center !important;
                align-items: center !important;
                width: 100% !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .id-card {
                box-shadow: none !important;
                border: 2px solid #bfa054 !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

    <!-- Control Toolbar (Not Printed) -->
    <div class="print-toolbar no-print">
        <div class="toolbar-title">
            <div>
                <h2>🪪 <?= $isBn ? 'ডিজিটাল সদস্য পরিচয়পত্র (Official Member Card Desk)' : 'Digital Membership Card Print Desk' ?></h2>
                <p>
                    <?= $isBn 
                        ? 'প্রিন্ট অপশনে "Background graphics" অন রেখে A4 পেপার বা PVC প্লাস্টিক কার্ডে প্রিন্ট করুন।' 
                        : 'Enable "Background graphics" in your print dialog for exact color and metallic gradient output.' ?>
                </p>
            </div>
        </div>

        <div class="toolbar-actions">
            <!-- Member Switcher -->
            <?php if (!empty($allMembers)): ?>
                <form method="GET" action="<?= url('/membership/card/print', $currentLocale) ?>" style="margin: 0;">
                    <select name="code" class="member-select" onchange="this.form.submit()" title="Switch Member for Live Preview">
                        <?php foreach ($allMembers as $m): ?>
                            <option value="<?= e($m['member_code']) ?>" <?= ($member['member_code'] ?? '') === $m['member_code'] ? 'selected' : '' ?>>
                                <?= e($m['member_code']) ?> — <?= e($m['name_bn']) ?> (<?= e($m['category_id']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            <?php endif; ?>

            <a href="<?= url('/membership/dashboard', $currentLocale) ?>" class="btn btn-back">
                ← <?= $isBn ? 'ড্যাশবোর্ডে ফিরুন' : 'Back to Dashboard' ?>
            </a>
            <button onclick="window.print()" class="btn btn-print">
                🖨️ <?= $isBn ? 'এখনই প্রিন্ট করুন' : 'Print Card Now' ?>
            </button>
        </div>
    </div>

    <!-- Printable Cards Container (Front and Back) -->
    <div class="cards-container">
        
        <!-- CARD FRONT -->
        <div class="id-card card-front" id="card-front">
            <!-- Background Watermark Logo -->
            <div class="card-watermark" aria-hidden="true">
                <img src="<?= asset('assets/images/brand/sps-logo-white.png') ?>" alt="SPS Emblem Watermark">
            </div>

            <!-- Header -->
            <div class="front-header">
                <div class="brand-block">
                    <img src="<?= asset('assets/images/brand/sps-logo-white.png') ?>" alt="SPS White Logo" class="brand-logo">
                    <div>
                        <div class="brand-title-bn">সনাতন ফিলোসফি এন্ড স্ক্রিপচার</div>
                        <div class="brand-title-en">SANATAN PHILOSOPHY & SCRIPTURE</div>
                    </div>
                </div>
                
                <!-- Smart EMV Gold Chip -->
                <div class="card-chip-wrap" title="Smart Card EMV Chip">
                    <svg width="44" height="34" viewBox="0 0 44 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="chipGoldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#f5d77f" />
                                <stop offset="35%" stop-color="#d4af37" />
                                <stop offset="70%" stop-color="#aa771c" />
                                <stop offset="100%" stop-color="#e6c86e" />
                            </linearGradient>
                        </defs>
                        <rect width="44" height="34" rx="5" fill="url(#chipGoldGrad)" stroke="#8f6b1e" stroke-width="0.8" />
                        <rect x="2" y="2" width="40" height="30" rx="3.5" fill="none" stroke="#7a5512" stroke-width="0.7" opacity="0.65" />
                        <rect x="14" y="9" width="16" height="16" rx="3" fill="none" stroke="#7a5512" stroke-width="0.75" />
                        <line x1="2" y1="17" x2="42" y2="17" stroke="#7a5512" stroke-width="0.75" />
                        <line x1="14" y1="2" x2="14" y2="32" stroke="#7a5512" stroke-width="0.75" />
                        <line x1="30" y1="2" x2="30" y2="32" stroke="#7a5512" stroke-width="0.75" />
                    </svg>
                </div>
            </div>

            <!-- Body with Photo & Meta -->
            <div class="front-body">
                <div class="member-photo-frame">
                    <?php 
                        $memberAvatar = \App\Services\MembershipService::getMemberAvatar($member);
                    ?>
                    <img src="<?= asset($memberAvatar) ?>" alt="<?= e($member['name_en'] ?? '') ?>" class="member-photo-img" onerror="this.onerror=null; this.src='<?= asset(\App\Services\MembershipService::DEFAULT_ORGANIZATION_DP) ?>';">
                </div>
                
                <div class="member-meta">
                    <div class="member-name-bn"><?= e($member['name_bn']) ?></div>
                    <div class="member-name-en"><?= e($member['name_en']) ?></div>
                    <div class="member-id-pill">
                        <span>ID : <?= e($member['member_code']) ?></span>
                    </div>
                </div>
            </div>

            <!-- Footer & QR -->
            <div class="front-footer">
                <div class="front-footer-meta">
                    <div class="member-category-title">
                        <?= e($category['name_en'] ?? ($member['category_id'] === 'STUDENT' ? 'Student Member' : 'Earning Member')) ?>
                    </div>
                    <div class="member-validity-title">
                        <?= empty($member['expiry_date']) ? 'VALID: LIFETIME' : 'VALID: ' . e($member['expiry_date']) ?>
                    </div>
                </div>

                <!-- Verified Authentic Scannable QR Code -->
                <div class="card-qr-box" title="<?= $isBn ? 'অনলাইন সদস্য ভেরিফিকেশন কিউআর কোড (ক্যামেরা দিয়ে স্ক্যান করুন)' : 'Scan with camera for live online verification' ?>">
                    <img id="printCardQrImg" class="sps-qr-code-img" src="" alt="SPS Verified QR Code">
                </div>
            </div>
        </div>

        <!-- CARD BACK -->
        <div class="id-card card-back" id="card-back">
            <!-- Header -->
            <div class="back-header">
                <div class="back-brand-bn">সনাতন ফিলোসফি এন্ড স্ক্রিপচার</div>
                <div class="back-brand-en">SANATAN PHILOSOPHY & SCRIPTURE</div>
            </div>

            <!-- Glowing Neon Blue Box -->
            <div class="back-cert-box">
                This card certifies that the cardholder is a registered and bonafide member of Sanatan Philosophy & Scripture (SPS).
            </div>

            <!-- Back Meta Grid -->
            <div class="back-info-grid">
                <div class="info-cell">
                    <span class="info-label">Blood Group:</span>
                    <span class="info-value"><?= e($member['blood_group'] ?? 'N/A') ?></span>
                </div>
                <div class="info-cell">
                    <span class="info-label">District:</span>
                    <span class="info-value"><?= e($member['district'] ?? 'Sylhet') ?></span>
                </div>
                <div class="info-cell">
                    <span class="info-label">Emergency:</span>
                    <span class="info-value">+880 1736-360041</span>
                </div>
                <div class="info-cell">
                    <span class="info-label">Web:</span>
                    <span class="info-value">www.sps.org</span>
                </div>
            </div>

            <!-- Glowing Divider Line -->
            <div class="back-divider-line"></div>

            <!-- Back Footer -->
            <div class="back-footer">
                <div class="back-footer-left">
                    <div class="issued-date">Issued: <?= e($member['join_date'] ?? '2026-09-27') ?></div>
                    <div class="verify-code">VERIFY: SPS-ONLINE-AUTHENTIC</div>
                </div>

                <div class="back-footer-right">
                    <div class="signatory-name">DR. B. K. ROY</div>
                    <div class="signatory-title">AUTHORISED SIGNATORY</div>
                </div>
            </div>
        </div>

    </div>

    <!-- Helpful Browser Notice -->
    <div class="no-print" style="text-align: center; color: #475569; font-size: 0.84rem; max-width: 680px; line-height: 1.6; margin-top: 8px;">
        <?= $isBn 
            ? '💡 আপনি চাইলে কার্ডটি স্ট্যান্ডার্ড CR-80 পিভিসি প্লাস্টিক কার্ড বা ফটো পেপারে প্রিন্ট করে ল্যামিনেটিং আকারে ব্যবহার করতে পারেন। কার্ডের কিউআর কোড স্ক্যান করে যে কেউ তাৎক্ষণিক অনলাইন ভেরিফিকেশন দেখতে পাবেন।' 
            : 'You can print this card directly on standard CR-80 PVC plastic ID card stock or photo paper. Scanning the QR code links directly to authentic live online verification.' ?>
    </div>

    <script src="<?= asset('assets/js/qrcode.min.js') ?>"></script>
    <script src="<?= asset('assets/js/sps-qr.js') ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const verifyUrl = "<?= \App\Services\QrCodeService::getVerificationUrl($member['member_code'], $currentLocale) ?>";
            const logoUrl = "<?= asset('assets/images/brand/sps-logo.png') ?>";
            renderSpsQrCode('printCardQrImg', verifyUrl, logoUrl);
        });
    </script>
</body>
</html>
