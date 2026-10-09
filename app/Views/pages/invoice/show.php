<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$payment = $payment ?? [];
$member = $member ?? null;
$financeExecutive = $financeExecutive ?? [];

$isVerified = ($payment['status'] ?? '') === 'Verified';
$amount = (float)($payment['amount'] ?? 0);

// Helper function for amount in words
function formatAmountInWords(float $amount, bool $isBn): string {
    $intAmount = (int)$amount;
    $wordsBn = [
        10 => 'দশ টাকা', 20 => 'বিশ টাকা', 50 => 'পঞ্চাশ টাকা',
        100 => 'এক শত টাকা', 200 => 'দুই শত টাকা', 300 => 'তিন শত টাকা',
        500 => 'পাঁচ শত টাকা', 1000 => 'এক হাজার টাকা', 2000 => 'দুই হাজার টাকা',
        5000 => 'পাঁচ হাজার টাকা', 10000 => 'দশ হাজার টাকা'
    ];
    $wordsEn = [
        10 => 'Ten Taka', 20 => 'Twenty Taka', 50 => 'Fifty Taka',
        100 => 'One Hundred Taka', 200 => 'Two Hundred Taka', 300 => 'Three Hundred Taka',
        500 => 'Five Hundred Taka', 1000 => 'One Thousand Taka', 2000 => 'Two Thousand Taka',
        5000 => 'Five Thousand Taka', 10000 => 'Ten Thousand Taka'
    ];

    if ($isBn) {
        return ($wordsBn[$intAmount] ?? ($intAmount . ' টাকা')) . ' মাত্র (Only)';
    }
    return ($wordsEn[$intAmount] ?? ($intAmount . ' Taka')) . ' Only';
}

$amountInWords = formatAmountInWords($amount, $isBn);
$payerName = !empty($payment['sender_name']) ? $payment['sender_name'] : ($member ? ($isBn ? $member['name_bn'] : $member['name_en']) : ($isBn ? 'সম্মানিত শুভানুধ্যায়ী' : 'Honorable Contributor'));
$isMember = !empty($payment['member_code']) && $payment['member_code'] !== 'NON-MEMBER';

// Purpose description
$purposeTitle = $payment['purpose_title'] ?? '';
if (empty($purposeTitle)) {
    $type = $payment['payment_type'] ?? '';
    if (str_contains($type, '10taka')) {
        $purposeTitle = $isBn ? 'সনাতনী ১০ টাকার প্রজেক্ট (Sanatani 10 Taka Project)' : 'Sanatani 10 Taka Project';
    } elseif (str_contains($type, 'welfare')) {
        $purposeTitle = $isBn ? 'এসপিএস ওয়েলফেয়ার ট্রাস্ট (SPS Welfare Trust)' : 'SPS Welfare Trust';
    } elseif ($type === 'entry') {
        $purposeTitle = $isBn ? 'সদস্যপদ প্রাথমিক ভর্তি ও চাঁদা ফি (Membership Entry Fee)' : 'Membership Entry Fee';
    } elseif ($type === 'yearly') {
        $purposeTitle = $isBn ? 'বাৎসরিক সদস্যপদ নবায়ন ফি (Yearly Membership Fee)' : 'Yearly Membership Fee';
    } elseif ($type === 'lifetime') {
        $purposeTitle = $isBn ? 'আজীবন সদস্যপদ এককালীন অনুদান (Lifetime Membership Fee)' : 'Lifetime Membership Contribution';
    } else {
        $purposeTitle = $isBn ? 'মাসিক সদস্যপদ নিয়মিত চাঁদা (Monthly Membership Dues)' : 'Monthly Membership Dues';
    }
}
?>
<!DOCTYPE html>
<html lang="<?= e($currentLocale) ?>" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($metaTitle ?? 'Official Money Receipt | Sanatan Philosophy & Scripture') ?></title>
    <link rel="icon" type="image/png" href="<?= asset('favicon.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=JetBrains+Mono:wght@500;600;700;800&family=Noto+Sans+Bengali:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-navy: #0f172a;
            --primary-blue: #1e3a8a;
            --accent-gold: #b45309;
            --accent-saffron: #c2410c;
            --verified-green: #15803d;
            --paper-bg: #ffffff;
            --border-line: #cbd5e1;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', 'Noto Sans Bengali', sans-serif;
            background: #e2e8f0;
            background-image: radial-gradient(rgba(15, 23, 42, 0.08) 1px, transparent 1px);
            background-size: 20px 20px;
            color: #1e293b;
            min-height: 100vh;
            padding: 30px 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Non-printable Control Toolbar */
        .no-print-toolbar {
            width: 100%;
            max-width: 820px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 12px 20px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 0.88rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            font-family: inherit;
        }
        .btn-print {
            background: linear-gradient(135deg, #c2410c 0%, #9a3412 100%);
            color: #ffffff;
            border: 1px solid #7c2d12;
            box-shadow: 0 2px 6px rgba(194, 65, 12, 0.3);
        }
        .btn-print:hover {
            background: #7c2d12;
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

        /* The Invoice Physical Paper Canvas */
        .invoice-sheet {
            width: 100%;
            max-width: 820px;
            background: var(--paper-bg);
            border: 1.5px solid #cbd5e1;
            border-radius: 16px;
            padding: 44px 48px;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.12);
            position: relative;
            overflow: hidden;
            box-sizing: border-box;
        }

        /* Outer Decorative Border Ring */
        .invoice-border-frame {
            position: absolute;
            inset: 12px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            pointer-events: none;
        }
        .invoice-inner-gold-line {
            position: absolute;
            inset: 16px;
            border: 0.75px dashed rgba(217, 119, 6, 0.3);
            border-radius: 8px;
            pointer-events: none;
        }

        /* -------------------------------------------------------------
         * HEADER: Big Organization Name in Bengali & English
         * ------------------------------------------------------------- */
        .invoice-header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 20px;
            margin-bottom: 24px;
            position: relative;
            z-index: 2;
        }
        .invoice-brand-logo {
            width: 74px;
            height: 74px;
            object-fit: contain;
            margin: 0 auto 10px;
            display: block;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }
        .invoice-title-bn {
            font-size: 2.1rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.25;
            letter-spacing: 0.5px;
            font-family: 'Noto Sans Bengali', sans-serif;
            margin-bottom: 4px;
        }
        .invoice-title-en {
            font-size: 1.15rem;
            font-weight: 800;
            color: #b45309;
            letter-spacing: 1.8px;
            text-transform: uppercase;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin-bottom: 8px;
        }
        .invoice-subtag {
            font-size: 0.86rem;
            color: #475569;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .invoice-contact-meta {
            font-size: 0.78rem;
            color: #64748b;
            margin-top: 6px;
            font-family: 'JetBrains Mono', monospace;
        }

        /* -------------------------------------------------------------
         * INVOICE BADGE & META BAR
         * ------------------------------------------------------------- */
        .invoice-meta-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            background: rgba(248, 250, 252, 0.25);
            border: 1px solid rgba(203, 213, 225, 0.75);
            border-radius: 8px;
            padding: 10px 16px;
            margin-bottom: 24px;
            position: relative;
            z-index: 2;
            backdrop-filter: blur(0.5px);
        }
        .invoice-receipt-tag {
            font-size: 0.95rem;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .meta-numbers-group {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 0.82rem;
            color: #475569;
            flex-wrap: wrap;
        }
        .meta-numbers-group strong {
            color: #0f172a;
            font-family: 'JetBrains Mono', monospace;
        }

        /* -------------------------------------------------------------
         * SENDER / CONTRIBUTOR DETAILS
         * ------------------------------------------------------------- */
        .invoice-party-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
            position: relative;
            z-index: 2;
        }
        .party-card {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(203, 213, 225, 0.75);
            border-radius: 8px;
            padding: 14px 18px;
            backdrop-filter: blur(0.5px);
            box-shadow: 0 1px 4px rgba(0,0,0,0.02);
        }
        .party-label {
            font-size: 0.74rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 6px;
        }
        .party-name {
            font-size: 1.25rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
            font-family: 'Noto Sans Bengali', 'Plus Jakarta Sans', sans-serif;
        }
        .party-sub {
            font-size: 0.84rem;
            color: #475569;
            line-height: 1.5;
        }
        .member-badge {
            display: inline-block;
            background: #e0f2fe;
            color: #0369a1;
            font-weight: 800;
            font-size: 0.76rem;
            padding: 2px 8px;
            border-radius: 4px;
            font-family: 'JetBrains Mono', monospace;
            border: 1px solid #bae6fd;
            margin-top: 4px;
        }
        .donor-badge {
            display: inline-block;
            background: #fef3c7;
            color: #92400e;
            font-weight: 800;
            font-size: 0.76rem;
            padding: 2px 8px;
            border-radius: 4px;
            border: 1px solid #fde68a;
            margin-top: 4px;
        }

        /* -------------------------------------------------------------
         * LINE ITEMS TABLE
         * ------------------------------------------------------------- */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            position: relative;
            z-index: 2;
        }
        .invoice-table th {
            background: #0f172a;
            color: #ffffff;
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 14px;
            text-align: left;
        }
        .invoice-table th:last-child {
            text-align: right;
        }
        .invoice-table td {
            padding: 14px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.9rem;
            color: #334155;
            vertical-align: top;
        }
        .invoice-table td:last-child {
            text-align: right;
            font-weight: 800;
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.05rem;
            color: #0f172a;
        }
        .item-main-title {
            font-weight: 800;
            color: #0f172a;
            font-size: 1rem;
            margin-bottom: 4px;
        }
        .item-sub-desc {
            font-size: 0.8rem;
            color: #64748b;
            line-height: 1.4;
        }

        /* Total Summary Rows */
        .total-summary-card {
            background: rgba(248, 250, 252, 0.25);
            border-radius: 8px;
            padding: 14px 18px;
            margin-bottom: 26px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            border: 1.5px solid rgba(203, 213, 225, 0.75);
            position: relative;
            z-index: 2;
            backdrop-filter: blur(0.5px);
        }
        .amount-in-words-box {
            max-width: 480px;
        }
        .words-label {
            font-size: 0.72rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
        }
        .words-text {
            font-size: 0.92rem;
            font-weight: 700;
            color: #1e3a8a;
            margin-top: 2px;
            line-height: 1.35;
        }
        .grand-total-box {
            text-align: right;
        }
        .grand-total-label {
            font-size: 0.76rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }
        .grand-total-amount {
            font-size: 1.7rem;
            font-weight: 800;
            color: #15803d;
            font-family: 'JetBrains Mono', monospace;
            line-height: 1.1;
            margin-top: 3px;
        }

        /* -------------------------------------------------------------
         * FULL-PAGE SPS LOGO WATERMARK & TOP RECEIVED STAMP
         * ------------------------------------------------------------- */
        .invoice-watermark-wrap {
            position: absolute;
            inset: 0;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
            overflow: hidden;
            user-select: none;
        }
        .invoice-fullpage-watermark {
            width: 530px;
            max-width: 84%;
            height: auto;
            opacity: 0.13;
            filter: grayscale(8%);
            pointer-events: none;
            user-select: none;
        }

        /* Official Received & Verified Stamp at Top Right of Document */
        .invoice-top-stamp-wrap {
            position: absolute;
            top: 26px;
            right: 28px;
            z-index: 10;
        }
        .invoice-top-stamp {
            padding: 7px 14px;
            border-radius: 8px;
            text-align: center;
            user-select: none;
            line-height: 1.15;
            transition: transform 0.2s ease;
        }
        .invoice-top-stamp.stamp-verified {
            border: 2.5px double #15803d;
            background: rgba(240, 253, 244, 0.96);
            box-shadow: 0 4px 14px rgba(22, 163, 74, 0.12);
            transform: rotate(-3deg);
        }
        .invoice-top-stamp.stamp-verified:hover {
            transform: rotate(0deg) scale(1.02);
        }
        .invoice-top-stamp.stamp-verified .stamp-stars {
            font-size: 0.65rem;
            color: #16a34a;
            letter-spacing: 2px;
        }
        .invoice-top-stamp.stamp-verified .stamp-main-badge {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.35rem;
            font-weight: 900;
            letter-spacing: 2.5px;
            color: #15803d;
            margin: 2px 0 1px;
            line-height: 1;
        }
        .invoice-top-stamp.stamp-verified .stamp-bn-badge {
            font-family: 'Noto Sans Bengali', sans-serif;
            font-size: 0.88rem;
            font-weight: 800;
            color: #166534;
        }
        .invoice-top-stamp.stamp-verified .stamp-footer-note {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.62rem;
            font-weight: 700;
            color: #15803d;
            margin-top: 3px;
            padding-top: 2px;
            border-top: 1px dashed rgba(22, 163, 74, 0.35);
            letter-spacing: 0.4px;
            line-height: 1.25;
        }

        .invoice-top-stamp.stamp-pending {
            border: 2px dashed #d97706;
            background: rgba(254, 243, 199, 0.94);
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.1);
            transform: rotate(2deg);
        }
        .invoice-top-stamp.stamp-pending .stamp-stars {
            font-size: 0.72rem;
            letter-spacing: 2px;
        }
        .invoice-top-stamp.stamp-pending .stamp-main-badge {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.1rem;
            font-weight: 900;
            letter-spacing: 1.5px;
            color: #b45309;
        }
        .invoice-top-stamp.stamp-pending .stamp-bn-badge {
            font-family: 'Noto Sans Bengali', sans-serif;
            font-size: 0.82rem;
            font-weight: 800;
            color: #92400e;
        }
        .invoice-top-stamp.stamp-pending .stamp-footer-note {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.6rem;
            font-weight: 700;
            color: #b45309;
            margin-top: 3px;
        }

        @media (max-width: 768px) {
            .invoice-top-stamp-wrap {
                position: static;
                display: flex;
                justify-content: center;
                margin-bottom: 12px;
            }
            .invoice-top-stamp {
                transform: none !important;
            }
        }

        /* -------------------------------------------------------------
         * SIGNATURE & QR CODE FOOTER
         * ------------------------------------------------------------- */
        .invoice-footer-grid {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-top: 14px;
            position: relative;
            z-index: 2;
        }
        .footer-qr-side {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .invoice-qr-box {
            width: 76px;
            height: 76px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .invoice-qr-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }
        .qr-text-meta {
            font-size: 0.74rem;
            color: #64748b;
            line-height: 1.45;
            max-width: 240px;
        }

        /* Finance Secretary Signature Block */
        .footer-signature-side {
            text-align: center;
            min-width: 220px;
        }
        .finance-signature-img {
            max-height: 48px;
            width: auto;
            object-fit: contain;
            display: block;
            margin: 0 auto 4px;
            filter: contrast(1.15) brightness(0.95);
        }
        .sign-divider-line {
            height: 1px;
            background: #0f172a;
            width: 100%;
            margin-bottom: 6px;
        }
        .sign-name {
            font-size: 0.92rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }
        .sign-title {
            font-size: 0.74rem;
            font-weight: 700;
            color: #b45309;
            margin-top: 1px;
        }
        .sign-org {
            font-size: 0.68rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        /* Invoice Legal Disclaimer */
        .invoice-bottom-disclaimer {
            text-align: center;
            font-size: 0.7rem;
            color: #94a3b8;
            margin-top: 24px;
            padding-top: 10px;
            border-top: 1px solid #f1f5f9;
            position: relative;
            z-index: 2;
        }

        /* -------------------------------------------------------------
         * STRICT PRINT STYLES FOR EXACT A4 PAPER
         * ------------------------------------------------------------- */
        @page {
            size: A4 portrait;
            margin: 10mm;
        }
        @media print {
            html, body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                color: #000000 !important;
            }
            .no-print-toolbar,
            .no-print {
                display: none !important;
            }
            .invoice-sheet {
                border: 1px solid #cbd5e1 !important;
                box-shadow: none !important;
                padding: 24px 30px !important;
                max-width: 100% !important;
                page-break-inside: avoid !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .invoice-watermark-wrap {
                opacity: 1 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .invoice-fullpage-watermark {
                opacity: 0.10 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .party-card,
            .invoice-meta-bar,
            .total-summary-card {
                background: rgba(255, 255, 255, 0.25) !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .invoice-top-stamp {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

    <!-- Non-Printable Control Toolbar -->
    <div class="no-print-toolbar no-print">
        <div>
            <h2 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 0 0 2px;">
                📄 <?= $isBn ? 'অফিসিয়াল পেমেন্ট ইনভয়েস ও মানি রসিদ' : 'Official Money Receipt & Contribution Invoice' ?>
            </h2>
            <p style="font-size: 0.78rem; color: #64748b; margin: 0;">
                <?= $isBn 
                    ? 'অর্থ সম্পাদকের অনুমোদিত স্বাক্ষর ও ভেরিফাইড সিলযুক্ত বৈধ রসিদ।' 
                    : 'Authentic financial receipt bearing official signature and verification watermark.' ?>
            </p>
        </div>

        <div style="display: flex; gap: 8px; align-items: center;">
            <a href="<?= url('/membership/dashboard', $currentLocale) ?>" class="btn-action btn-back">
                ← <?= $isBn ? 'ড্যাশবোর্ড' : 'Dashboard' ?>
            </a>
            <a href="<?= url('/', $currentLocale) ?>" class="btn-action btn-back">
                🏠 <?= $isBn ? 'হোম' : 'Home' ?>
            </a>
            <button onclick="window.print()" class="btn-action btn-print">
                🖨️ <?= $isBn ? 'প্রিন্ট / ডাউনলোড (PDF)' : 'Print / Download' ?>
            </button>
        </div>
    </div>

    <!-- Official Money Receipt Physical Canvas -->
    <div class="invoice-sheet" id="invoicePaper">
        <!-- Inner Border Frame -->
        <div class="invoice-border-frame" aria-hidden="true"></div>
        <div class="invoice-inner-gold-line" aria-hidden="true"></div>

        <!-- Full-Page SPS Logo Watermark -->
        <div class="invoice-watermark-wrap" aria-hidden="true">
            <img src="<?= asset('assets/images/brand/sps-logo.png') ?>" alt="SPS Sacred Seal Watermark" class="invoice-fullpage-watermark">
        </div>

        <?php if ($isVerified): ?>
            <!-- Official Received & Verified Stamp at Top Right of Sheet -->
            <div class="invoice-top-stamp-wrap">
                <div class="invoice-top-stamp stamp-verified">
                    <div class="stamp-stars">★ ★ ★ ★ ★</div>
                    <div class="stamp-main-badge">RECEIVED</div>
                    <div class="stamp-bn-badge">পরিশোধিত ও গৃহীত</div>
                    <div class="stamp-footer-note">
                        SPS CENTRAL TREASURY<br>
                        <?= date('d M Y', strtotime($payment['verified_at'] ?? $payment['payment_date'])) ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Pending Verification Status at Top Right of Sheet -->
            <div class="invoice-top-stamp-wrap">
                <div class="invoice-top-stamp stamp-pending">
                    <div class="stamp-stars">⏳ ⏳ ⏳</div>
                    <div class="stamp-main-badge">PENDING</div>
                    <div class="stamp-bn-badge">যাচাইকরণ অপেক্ষমাণ</div>
                    <div class="stamp-footer-note">AWAITING VERIFICATION</div>
                </div>
            </div>
        <?php endif; ?>

        <!-- -------------------------------------------------------------
         * INVOICE HEADER: Logo & Big Bilingual Organization Full Name
         * ------------------------------------------------------------- -->
        <header class="invoice-header">
            <img src="<?= asset('assets/images/brand/sps-logo.png') ?>" alt="SPS Sacred Logo" class="invoice-brand-logo">
            
            <!-- Big Name in Bangla & English -->
            <h1 class="invoice-title-bn">সনাতন ফিলোসফি এন্ড স্ক্রিপচার</h1>
            <h2 class="invoice-title-en">SANATAN PHILOSOPHY &amp; SCRIPTURE</h2>
            
            <div class="invoice-subtag">
                <span>সনাতনী ঐক্য, প্রচার ও কল্যাণে অবিচল</span>
                <span>•</span>
                <strong>OFFICIAL MONEY RECEIPT</strong>
            </div>

            <div class="invoice-contact-meta">
                রেজিস্ট্রেশন: SPS-BD-TRUST-2024 • হেল্পলাইন: +880 1736-360041 • ইমেইল: contact@sps-platform.org • www.sps.org
            </div>
        </header>

        <!-- Meta Bar: Numbers & Status -->
        <div class="invoice-meta-bar">
            <div class="invoice-receipt-tag">
                <span>📜 মানি রসিদ (MONEY RECEIPT)</span>
                <?php if ($isVerified): ?>
                    <span style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; padding: 2px 10px; border-radius: 999px; font-size: 0.74rem; font-weight: 800;">
                        ✓ VERIFIED &amp; APPROVED
                    </span>
                <?php else: ?>
                    <span style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; padding: 2px 10px; border-radius: 999px; font-size: 0.74rem; font-weight: 800;">
                        ⏳ PENDING REVIEW
                    </span>
                <?php endif; ?>
            </div>

            <div class="meta-numbers-group">
                <div><?= $isBn ? 'ইনভয়েস নম্বর:' : 'Receipt No:' ?> <strong>INV-<?= date('Y', strtotime($payment['payment_date'])) ?>-<?= strtoupper(substr(md5($payment['id'] ?? $payment['transaction_id']), 0, 6)) ?></strong></div>
                <div><?= $isBn ? 'ট্রানজেকশন আইডি:' : 'TxID:' ?> <strong><?= e($payment['transaction_id']) ?></strong></div>
                <div><?= $isBn ? 'তারিখ:' : 'Date:' ?> <strong><?= date('d M Y, h:i A', strtotime($payment['payment_date'])) ?></strong></div>
            </div>
        </div>

        <!-- Contributor / Sender Information Grid -->
        <div class="invoice-party-grid">
            <!-- Payer Details -->
            <div class="party-card">
                <div class="party-label"><?= $isBn ? 'প্রেরক / অনুদানকারীর বিবরণ (PAYER / CONTRIBUTOR)' : 'RECEIVED FROM' ?></div>
                <div class="party-name"><?= e($payerName) ?></div>
                <div class="party-sub">
                    <div><strong>মোবাইল / যোগাযোগ:</strong> <?= e($payment['sender_number'] ?: ($member['phone'] ?? 'N/A')) ?></div>
                    <div><strong>জেলা / ঠিকানা:</strong> <?= e($member['district'] ?? ($member['address'] ?? 'বাংলাদেশ')) ?></div>
                </div>
                <div>
                    <?php if ($isMember): ?>
                        <span class="member-badge">
                            👤 সক্রিয় সদস্য (MEMBER ID: <?= e($payment['member_code']) ?>)
                        </span>
                    <?php else: ?>
                        <span class="donor-badge">
                            ❤️ সম্মানিত শুভানুধ্যায়ী (HONORABLE CONTRIBUTOR / NON-MEMBER)
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Payment Channel Details -->
            <div class="party-card">
                <div class="party-label"><?= $isBn ? 'পেমেন্ট মাধ্যম ও ট্রানজেকশন (PAYMENT METHOD)' : 'TRANSACTION CHANNEL' ?></div>
                <div class="party-sub" style="display: flex; flex-direction: column; gap: 4px;">
                    <div><strong>পেমেন্ট মাধ্যম:</strong> <?= e($payment['payment_method'] ?? 'bKash') ?></div>
                    <div><strong>প্রোভাইডার TrxID:</strong> <code style="font-family:'JetBrains Mono', monospace; font-weight:800; color:#c2410c;"><?= e($payment['trx_id']) ?></code></div>
                    <div><strong>রেফারেন্স কোড:</strong> <?= e($payment['payment_reference'] ?: ($isMember ? $payment['member_code'] : '10Taka')) ?></div>
                    <div><strong>পরিশোধের সময়:</strong> <?= date('d M Y, h:i A', strtotime($payment['payment_time'] ?? $payment['payment_date'])) ?></div>
                </div>
            </div>
        </div>

        <!-- Line Item Particulars Table -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th style="width: 8%;">ক্রম</th>
                    <th style="width: 52%;">বিবরণ ও সেবার খাত (DESCRIPTION / PURPOSE)</th>
                    <th style="width: 20%;">পেমেন্ট চ্যানেল</th>
                    <th style="width: 20%;">পরিমাণ (AMOUNT)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>০১.</strong></td>
                    <td>
                        <div class="item-main-title"><?= e($purposeTitle) ?></div>
                        <div class="item-sub-desc">
                            <?= e($payment['notes'] ?? 'সনাতন ফিলোসফি এন্ড স্ক্রিপচার (SPS)-এর কেন্দ্রীয় কল্যাণ তহবিলে জমাকৃত অর্থ।') ?>
                            <br>
                            <em>TrxID: <?= e($payment['trx_id']) ?> • পেমেন্ট মেথড: <?= e($payment['payment_method']) ?></em>
                        </div>
                    </td>
                    <td>
                        <span style="font-weight: 700; color: #0f172a;"><?= e($payment['payment_method']) ?></span>
                        <div style="font-size: 0.75rem; color: #64748b;">Send Money / Online</div>
                    </td>
                    <td>
                        ৳ <?= number_format($amount, 2) ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Total & Words Summary Card -->
        <div class="total-summary-card">
            <div class="amount-in-words-box">
                <div class="words-label"><?= $isBn ? 'টাকার পরিমাণ (কথায়):' : 'AMOUNT IN WORDS:' ?></div>
                <div class="words-text">
                    “<?= e($amountInWords) ?>”
                </div>
            </div>
            
            <div class="grand-total-box">
                <div class="grand-total-label"><?= $isBn ? 'সর্বমোট গৃহীত অর্থ (TOTAL AMOUNT)' : 'TOTAL PAID' ?></div>
                <div class="grand-total-amount">৳ <?= number_format($amount, 2) ?> BDT</div>
            </div>
        </div>

        <!-- -------------------------------------------------------------
         * SIGNATURES: Official Finance Secretary Signature from Media/Sign
         * ------------------------------------------------------------- -->
        <div class="invoice-footer-grid">
            <!-- Left: Scannable Live Online QR Verification -->
            <div class="footer-qr-side">
                <div class="invoice-qr-box" title="Scan with camera for online receipt verification">
                    <img id="invoiceQrImg" class="sps-qr-code-img" src="" alt="Verified SPS QR Code">
                </div>
                <div class="qr-text-meta">
                    <strong style="color: #0f172a;">অনলাইন সত্যতা যাচাই (LIVE VERIFY)</strong><br>
                    ক্যামেরা দিয়ে স্ক্যান করে যেকোনো সময় এই মানি রসিদের সত্যতা ও অডিট যাচাই করতে পারবেন।
                </div>
            </div>

            <!-- Right: Finance Secretary Signature & Seal -->
            <div class="footer-signature-side">
                <img src="<?= asset('assets/images/signatures/finance-secretary.png') ?>" alt="Finance Secretary Official Signature" class="finance-signature-img">
                <div class="sign-divider-line"></div>
                <div class="sign-name">জয় চক্রবর্তী (Joy Chakraborty)</div>
                <div class="sign-title">অর্থ সম্পাদক ও কোষাধ্যক্ষ (Finance Secretary)</div>
                <div class="sign-org">সনাতন ফিলোসফি এন্ড স্ক্রিপচার (SPS)</div>
            </div>
        </div>

        <!-- Bottom Security Disclaimer -->
        <div class="invoice-bottom-disclaimer">
            এই মানি রসিদটি ‘সনাতন ফিলোসফি এন্ড স্ক্রিপচার (SPS)’-এর অফিসিয়াল সেন্ট্রাল অ্যাকাউন্ট সিস্টেম কর্তৃক স্বয়ংক্রিয়ভাবে প্রস্তুতকৃত। সনাতনীদের প্রতিটি অনুদান ও চাঁদা সর্বোচ্চ ধর্মীয় পবিত্রতা ও আর্থিক সততার সাথে সংরক্ষিত।
        </div>
    </div>

    <!-- QR Code Script -->
    <script src="<?= asset('assets/js/qrcode.min.js') ?>"></script>
    <script src="<?= asset('assets/js/sps-qr.js') ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const verifyUrl = "<?= url('/invoice/' . $payment['transaction_id'], $currentLocale) ?>";
            const logoUrl = "<?= asset('assets/images/brand/sps-logo.png') ?>";
            renderSpsQrCode('invoiceQrImg', verifyUrl, logoUrl);
        });
    </script>
</body>
</html>
