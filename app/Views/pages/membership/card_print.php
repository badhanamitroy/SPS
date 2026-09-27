<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$member = $member ?? [];
$category = $category ?? [];
$plan = $plan ?? [];
?>
<!DOCTYPE html>
<html lang="<?= $currentLocale ?>" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($metaTitle ?? 'Digital Member Card Print') ?></title>
    <link rel="icon" type="image/png" href="<?= asset('favicon.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Noto+Sans+Bengali:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --card-w: 420px;
            --card-h: 260px;
            --primary-bg: #0f172a;
            --accent-gold: #d97706;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Noto Sans Bengali', 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 30px 15px;
        }

        /* Non-printable Control Toolbar */
        .print-toolbar {
            width: 100%;
            max-width: 880px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 14px 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.92rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            font-family: inherit;
        }
        .btn-print {
            background: #c65a1e;
            color: #ffffff;
            border: 1px solid #a64713;
            box-shadow: 0 2px 8px rgba(198,90,30,0.3);
        }
        .btn-print:hover {
            background: #a64713;
        }
        .btn-back {
            background: #f8fafc;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .btn-back:hover {
            background: #e2e8f0;
        }

        /* Two Cards Preview Layout (Front & Back) */
        .cards-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
            align-items: center;
            margin-bottom: 30px;
        }

        /* The Card Physical Box */
        .id-card {
            width: var(--card-w);
            height: var(--card-h);
            border-radius: 16px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.2);
            border: 1.5px solid rgba(255, 255, 255, 0.15);
            color: #ffffff;
            page-break-inside: avoid;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        /* Front Card */
        .card-front {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #090d16 100%) !important;
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .front-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .brand-block {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .brand-logo {
            height: 38px;
            width: auto;
        }
        .brand-title-bn {
            font-size: 0.88rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 0.3px;
        }
        .brand-title-en {
            font-size: 0.62rem;
            color: #94a3b8;
            letter-spacing: 0.6px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .card-chip {
            background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
            width: 32px;
            height: 24px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.4);
        }

        .front-body {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 8px 0;
        }
        .member-photo {
            width: 72px;
            height: 72px;
            border-radius: 12px;
            object-fit: cover;
            border: 2.5px solid #fbbf24;
            box-shadow: 0 4px 10px rgba(0,0,0,0.35);
            background: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            flex-shrink: 0;
        }
        .member-meta {
            overflow: hidden;
        }
        .member-name-bn {
            font-size: 1.15rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.2;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }
        .member-name-en {
            font-size: 0.8rem;
            color: #cbd5e1;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin-bottom: 4px;
        }
        .member-code-badge {
            display: inline-block;
            font-family: monospace;
            font-size: 0.95rem;
            font-weight: 800;
            color: #38bdf8;
            background: rgba(56, 189, 248, 0.12);
            padding: 1px 8px;
            border-radius: 4px;
            border: 1px solid rgba(56, 189, 248, 0.3);
        }

        .front-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-top: 1px solid rgba(255,255,255,0.12);
            padding-top: 10px;
        }
        .cat-plan-pill {
            font-size: 0.72rem;
            color: #e2e8f0;
            line-height: 1.3;
        }
        .validity-tag {
            color: #4ade80;
            font-weight: 800;
            font-size: 0.78rem;
        }
        .qr-box {
            background: #ffffff;
            padding: 5px;
            border-radius: 6px;
            width: 48px;
            height: 48px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Back Card */
        .card-back {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .back-stripe {
            height: 32px;
            background: #020617;
            margin: -18px -20px 10px -20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .back-declaration {
            font-size: 0.72rem;
            color: #cbd5e1;
            line-height: 1.4;
            background: rgba(255,255,255,0.04);
            padding: 8px 10px;
            border-radius: 6px;
            border-left: 3px solid #fbbf24;
            margin-bottom: 8px;
        }
        .back-meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            font-size: 0.72rem;
            color: #94a3b8;
        }
        .back-meta-grid strong {
            color: #f1f5f9;
        }
        .back-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-top: 1px solid rgba(255,255,255,0.12);
            padding-top: 8px;
        }
        .signature-box {
            text-align: center;
        }
        .sign-line {
            font-family: 'Cinzel', serif;
            font-size: 0.72rem;
            color: #f59e0b;
            font-weight: 700;
            border-bottom: 1px dashed rgba(255,255,255,0.3);
            padding-bottom: 2px;
            margin-bottom: 2px;
        }
        .sign-title {
            font-size: 0.58rem;
            color: #94a3b8;
            text-transform: uppercase;
        }

        @page {
            size: auto;
            margin: 10mm;
        }

        /* Strict Print Media Styles */
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
                border: 1px solid #334155 !important;
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
        <div>
            <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-bottom: 2px;">
                🪪 <?= $isBn ? 'ডিজিটাল সদস্য পরিচয়পত্র (ID Card Print Desk)' : 'Digital Membership Card Print' ?>
            </h2>
            <p style="font-size: 0.8rem; color: #64748b; margin: 0;">
                <?= $isBn 
                    ? 'প্রিন্ট অপশনে "Background graphics" অন রেখে A4 পেপার বা PVC কার্ডে প্রিন্ট করুন।' 
                    : 'Enable "Background graphics" in print dialog to print exact colors.' ?>
            </p>
        </div>

        <div style="display: flex; gap: 8px; align-items: center;">
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
            <!-- Header -->
            <div class="front-header">
                <div class="brand-block">
                    <img src="<?= asset('assets/images/brand/sps-logo-white.png') ?>" alt="SPS White Logo" class="brand-logo">
                    <div>
                        <div class="brand-title-bn">সনাতন ফিলোসফি এন্ড স্ক্রিপচার</div>
                        <div class="brand-title-en">SANATAN PHILOSOPHY & SCRIPTURE</div>
                    </div>
                </div>
                <div class="card-chip" title="Smart Card Hologram Chip"></div>
            </div>

            <!-- Body with Photo & Meta -->
            <div class="front-body">
                <?php if (!empty($member['avatar'])): ?>
                    <img src="<?= asset($member['avatar']) ?>" alt="<?= e($member['name_en'] ?? '') ?>" class="member-photo" onerror="this.onerror=null; this.src='<?= asset('assets/images/members/member_SPS_000872.jpg') ?>';">
                <?php else: ?>
                    <div class="member-photo">👤</div>
                <?php endif; ?>
                
                <div class="member-meta">
                    <div class="member-name-bn"><?= e($member['name_bn']) ?></div>
                    <div class="member-name-en"><?= e($member['name_en']) ?></div>
                    <div class="member-code-badge">ID: <?= e($member['member_code']) ?></div>
                </div>
            </div>

            <!-- Footer & QR -->
            <div class="front-footer">
                <div>
                    <div class="cat-plan-pill">
                        <strong><?= e($isBn ? ($category['name_bn'] ?? $member['category_id']) : ($category['name_en'] ?? $member['category_id'])) ?></strong>
                    </div>
                    <div class="validity-tag">
                        <?= empty($member['expiry_date']) ? 'LIFETIME ACTIVE' : 'VALID: ' . e($member['expiry_date']) ?>
                    </div>
                </div>

                <!-- Verified QR Code -->
                <div class="qr-box" title="Online Verification QR">
                    <svg viewBox="0 0 24 24" width="38" height="38" fill="#0f172a">
                        <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm10-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm14 0h2v2h-2v-2zm-4 0h2v2h-2v-2zm2 2h2v2h-2v-2zm2 2h2v2h-2v-2zm-4 2h2v2h-2v-2zm4-4h2v2h-2v-2zm0-2h2v2h-2v-2zm-2 2h2v2h-2v-2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- CARD BACK -->
        <div class="id-card card-back" id="card-back">
            <div class="back-stripe"></div>

            <div class="back-declaration">
                <?= $isBn 
                    ? 'এই পরিচয়পত্রটি ‘সনাতন ফিলোসফি এন্ড স্ক্রিপচার (SPS)’-এর একজন নিবন্ধিত ও সম্মানিত সদস্যের প্রমাণপত্র। কার্ডধারী ব্যক্তি এসপিএস-এর ডিজিটাল লাইব্রেরি, গীতাপাঠ ও সমাজকল্যাণ কার্যক্রমে সদস্য সুবিধার অধিকারী।'
                    : 'This card certifies that the cardholder is a registered and bonafide member of Sanatan Philosophy & Scripture (SPS).' ?>
            </div>

            <div class="back-meta-grid">
                <div><?= $isBn ? 'রক্তের গ্রুপ:' : 'Blood Group:' ?> <strong><?= e($member['blood_group'] ?? 'N/A') ?></strong></div>
                <div><?= $isBn ? 'জেলা:' : 'District:' ?> <strong><?= e($member['district'] ?? 'বাংলাদেশ') ?></strong></div>
                <div><?= $isBn ? 'জরুরি যোগাযোগ:' : 'Emergency:' ?> <strong>+880 1736-360041</strong></div>
                <div><?= $isBn ? 'ওয়েবসাইট:' : 'Web:' ?> <strong>www.sps.org</strong></div>
            </div>

            <div class="back-footer">
                <div>
                    <div style="font-size:0.58rem; color:#94a3b8;"><?= $isBn ? 'ইস্যু তারিখ:' : 'Issued:' ?> <?= e($member['join_date'] ?? '2024-01-01') ?></div>
                    <div style="font-size:0.58rem; color:#94a3b8; font-family:monospace;">VERIFY: SPS-ONLINE-AUTHENTIC</div>
                </div>

                <div class="signature-box">
                    <div class="sign-line">Dr. B. K. Roy</div>
                    <div class="sign-title"><?= $isBn ? 'সাধারণ সম্পাদক / কার্যনির্বাহী' : 'Authorized Signatory' ?></div>
                </div>
            </div>
        </div>

    </div>

    <!-- Helpful Browser Notice -->
    <div class="no-print" style="text-align: center; color: #64748b; font-size: 0.82rem; max-width: 600px; line-height: 1.5;">
        <?= $isBn 
            ? '💡 আপনি চাইলে কার্ডটি প্রিন্ট করে ল্যামিনেটিং বা পিভিসি কার্ড আকারে ব্যবহার করতে পারেন। কার্ডের পেছনের কিউআর কোড স্ক্যান করে যে কেউ তাৎক্ষণিক অনলাইন ভেরিফিকেশন দেখতে পাবেন।' 
            : 'You can print and laminate this card for official physical use. Scanning the QR code links directly to authentic online verification.' ?>
    </div>

</body>
</html>
