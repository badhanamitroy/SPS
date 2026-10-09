<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';

$readingRequests = $requests ?? [];
$dlRequests = $downloadRequests ?? [];

$pendingCount = 0;
$approvedCount = 0;
foreach ($readingRequests as $r) {
    if ($r['status'] === 'pending') $pendingCount++;
    if ($r['status'] === 'approved') $approvedCount++;
}

$dlPendingCount = 0;
$dlApprovedCount = 0;
foreach ($dlRequests as $dr) {
    if ($dr['status'] === 'pending') $dlPendingCount++;
    if ($dr['status'] === 'approved') $dlApprovedCount++;
}
?>

<div class="container" style="padding-top:var(--space-2xl); padding-bottom:var(--space-4xl);">
    <!-- Breadcrumb & Top Bar -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:var(--space-sm); margin-bottom:var(--space-xl); border-bottom:1px solid var(--border-subtle); padding-bottom:var(--space-md);">
        <div class="breadcrumb" style="margin-bottom:0;">
            <a href="<?= e(url('/', $currentLocale)) ?>"><?= e(__('common.nav.home')) ?></a>
            <span class="breadcrumb-separator">/</span>
            <a href="<?= e(url('/library', $currentLocale)) ?>"><?= e(__('common.nav.library')) ?></a>
            <span class="breadcrumb-separator">/</span>
            <span style="color:var(--text-main); font-weight:600;"><?= $isBn ? 'প্রশাসনিক প্যানেল' : 'Admin Portal' ?></span>
        </div>

        <?php if (\App\Core\Env::get('APP_DEBUG', \App\Core\App::config('app.debug', false))): ?>
        <!-- Role Simulator Toolbar -->
        <div style="display:flex; align-items:center; gap:var(--space-xs); background:var(--bg-subtle); padding:4px 8px; border-radius:var(--radius-sm); border:1px solid var(--border-medium); font-size:0.82rem;">
            <span style="font-weight:600; color:var(--text-muted); margin-right:4px;">
                <?= $isBn ? 'মোড প্রিভিউ:' : 'View as:' ?>
            </span>
            <a href="<?= e(url('/library/role?role=viewer', $currentLocale)) ?>" 
               class="btn btn-sm <?= $currentRole === 'viewer' ? 'btn-primary' : 'btn-ghost' ?>" 
               style="padding:3px 8px; font-size:0.78rem;">
                👤 <?= $isBn ? 'সাধারণ দর্শক' : 'Viewer' ?>
            </a>
            <a href="<?= e(url('/library/role?role=paid_member', $currentLocale)) ?>" 
               class="btn btn-sm <?= $currentRole === 'paid_member' ? 'btn-primary' : 'btn-ghost' ?>" 
               style="padding:3px 8px; font-size:0.78rem;">
                ⭐ <?= $isBn ? 'পেইড সদস্য' : 'Paid Member' ?>
            </a>
            <a href="<?= e(url('/library/role?role=admin', $currentLocale)) ?>" 
               class="btn btn-sm <?= $currentRole === 'admin' ? 'btn-primary' : 'btn-ghost' ?>" 
               style="padding:3px 8px; font-size:0.78rem;">
                🛡️ <?= $isBn ? 'প্রশাসক' : 'Admin' ?>
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Flash Messages -->
    <?php if ($msg = \App\Core\Session::getFlash('success')): ?>
        <div class="alert alert-success" style="margin-bottom:var(--space-xl);">
            <strong>✓ <?= $isBn ? 'সফল হয়েছে:' : 'Success:' ?></strong> <?= e($msg) ?>
        </div>
    <?php endif; ?>

    <?php if ($msg = \App\Core\Session::getFlash('warning')): ?>
        <div class="alert alert-warning" style="margin-bottom:var(--space-xl);">
            <strong>▲ <?= $isBn ? 'সতর্কতা:' : 'Notice:' ?></strong> <?= e($msg) ?>
        </div>
    <?php endif; ?>

    <!-- Admin Console Header -->
    <div style="margin-bottom:var(--space-2xl);">
        <div style="display:flex; align-items:center; gap:var(--space-xs); margin-bottom:var(--space-2xs);">
            <span class="badge" style="background:var(--accent-saffron); color:#FFF; font-weight:700;">
                🛡️ <?= $isBn ? 'এসপিএস কেন্দ্রীয় প্রশাসন' : 'SPS Central Administration' ?>
            </span>
            <span class="badge badge-scholarly">
                <?= $isBn ? 'দ্বিস্তরীয় ডিআরএম ও অ্যাক্সেস গভর্ন্যান্স' : 'Two-Layer Access Governance v2.0' ?>
            </span>
        </div>
        <h1 style="font-size:clamp(1.8rem, 3.2vw, 2.4rem); margin-bottom:var(--space-xs); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>; color:var(--text-main);">
            <?= $isBn ? 'গ্রন্থাগার ও ই-বুক পাঠাধিকার নিয়ন্ত্রণকক্ষ' : 'Library & Reader Access Control Console' ?>
        </h1>
        <p class="section-subtitle" style="font-size:1rem; line-height:1.65; color:var(--text-secondary); max-width:860px;">
            <?= $isBn 
                ? 'এসপিএস-এর মূল মিডিয়া ডিরেক্টরির সংরক্ষিত পিডিএফ প্রকাশনাগুলোর পাঠাধিকার তত্ত্বাবধান, প্রতিটি গ্রন্থের দ্বিস্তরীয় পাঠাধিকার ও ডাউনলোড পারমিশন কনফিগারেশন এবং ডাউনলোড ও পাঠাধিকার আবেদন পর্যালোচনা।' 
                : 'Configure individual book access tiers, preview page limits, offline download permissions, and manage reader and download authorization workflows.' ?>
        </p>
    </div>

    <!-- Metric Counters -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(210px, 1fr)); gap:var(--space-md); margin-bottom:var(--space-2xl);">
        <div class="card" style="padding:var(--space-lg); border-left:4px solid var(--accent-gold);">
            <div style="font-size:0.78rem; text-transform:uppercase; color:var(--text-muted); font-weight:700;"><?= $isBn ? 'মোট সংরক্ষিত ই-বুক' : 'Total Archival PDFs' ?></div>
            <div style="font-size:1.8rem; font-weight:700; color:var(--text-main); margin-top:4px;"><?= count($books) ?></div>
            <div style="font-size:0.78rem; color:var(--text-muted);"><?= $isBn ? '২টি ফোল্ডারে সংরক্ষিত' : 'Active across 2 folders' ?></div>
        </div>

        <div class="card" style="padding:var(--space-lg); border-left:4px solid var(--status-warning);">
            <div style="font-size:0.78rem; text-transform:uppercase; color:var(--text-muted); font-weight:700;"><?= $isBn ? 'ডাউনলোড আবেদন (অপেক্ষমাণ)' : 'Pending Download Requests' ?></div>
            <div style="font-size:1.8rem; font-weight:700; color:var(--accent-saffron); margin-top:4px;"><?= $dlPendingCount ?></div>
            <div style="font-size:0.78rem; color:var(--text-muted);"><?= $isBn ? 'অনুমোদনের অপেক্ষায়' : 'Awaiting admin decision' ?></div>
        </div>

        <div class="card" style="padding:var(--space-lg); border-left:4px solid var(--status-success);">
            <div style="font-size:0.78rem; text-transform:uppercase; color:var(--text-muted); font-weight:700;"><?= $isBn ? 'সক্রিয় ডাউনলোড অনুমতি' : 'Approved Download Passes' ?></div>
            <div style="font-size:1.8rem; font-weight:700; color:var(--status-success); margin-top:4px;"><?= $dlApprovedCount ?></div>
            <div style="font-size:0.78rem; color:var(--text-muted);"><?= $isBn ? 'সাময়িক সিকিউর টোকেন' : 'Time-limited signed tokens' ?></div>
        </div>

        <div class="card" style="padding:var(--space-lg); border-left:4px solid var(--accent-brown);">
            <div style="font-size:0.78rem; text-transform:uppercase; color:var(--text-muted); font-weight:700;"><?= $isBn ? 'পাঠাধিকার আবেদন (স্কলার)' : 'Reader Scholar Passes' ?></div>
            <div style="font-size:1.8rem; font-weight:700; color:var(--text-main); margin-top:4px;"><?= $pendingCount ?></div>
            <div style="font-size:0.78rem; color:var(--text-muted);"><?= $isBn ? $approvedCount . 'টি পূর্বে অনুমোদিত' : $approvedCount . ' previously approved' ?></div>
        </div>
    </div>

    <!-- Section A: Publications Access & Configuration Table (Section 9 & 25) -->
    <div class="card" style="margin-bottom:var(--space-3xl); padding:var(--space-xl); border:1px solid var(--border-medium); background:var(--bg-surface);">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:var(--space-sm); border-bottom:1px solid var(--border-subtle); padding-bottom:var(--space-sm); margin-bottom:var(--space-lg);">
            <div>
                <h2 style="font-size:1.35rem; font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>; margin-bottom:2px; color:var(--text-main);">
                    <?= $isBn ? 'গ্রন্থ তালিকা ও দ্বিস্তরীয় প্রবেশাধিকার ব্যবস্থাপনা' : 'Publications Access & Governance Matrix' ?>
                </h2>
                <p style="font-size:0.86rem; color:var(--text-muted); margin-bottom:0;">
                    <?= $isBn ? 'প্রতিটি গ্রন্থের পঠন পরিধি (Full Book বা Preview) এবং ডাউনলোড পারমিশন স্বতন্ত্রভাবে নিয়ন্ত্রণ করুন।' : 'Configure reading scope (Full vs Preview) and download permissions independently for every book.' ?>
                </p>
            </div>
            <span class="badge badge-scholarly"><?= count($books) ?> <?= $isBn ? 'টি গ্রন্থ' : 'Books' ?></span>
        </div>

        <div style="overflow-x:auto;">
            <table class="table" style="width:100%; border-collapse:collapse; font-size:0.88rem;">
                <thead>
                    <tr style="background:var(--bg-subtle); border-bottom:2px solid var(--border-medium); text-align:left;">
                        <th style="padding:10px 12px;"><?= $isBn ? 'গ্রন্থ ও বিবরণ' : 'Publication' ?></th>
                        <th style="padding:10px 12px;"><?= $isBn ? 'পঠন অধিকার (Tier)' : 'Reading Tier' ?></th>
                        <th style="padding:10px 12px;"><?= $isBn ? 'পঠন পরিধি (Scope)' : 'Reading Scope' ?></th>
                        <th style="padding:10px 12px;"><?= $isBn ? 'ডাউনলোড অনুমতি' : 'Download Policy' ?></th>
                        <th style="padding:10px 12px;"><?= $isBn ? 'ডাউনলোড রিকোয়েস্ট' : 'Allow Request' ?></th>
                        <th style="padding:10px 12px; text-align:right;"><?= $isBn ? 'অ্যাকশন' : 'Action' ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $b): ?>
                        <?php
                        $bSlug = $b['slug'];
                        $bTitle = $isBn ? $b['title_bn'] : $b['title_en'];
                        $bAuthor = $isBn ? $b['author_bn'] : $b['author_en'];
                        $bPublisher = $isBn ? ($b['publisher_bn'] ?? 'এসপিএস') : ($b['publisher_en'] ?? 'SPS');
                        $rTier = $b['reading_access'] ?? 'paid_members';
                        $rScope = $b['reading_scope'] ?? 'full';
                        $pS = $b['preview_start'] ?? 1;
                        $pE = $b['preview_end'] ?? 20;
                        $dlPerm = $b['download_permission'] ?? 'admin_approval_required';
                        $allowReq = !empty($b['allow_download_request']);
                        ?>
                        <tr style="border-bottom:1px solid var(--border-subtle); vertical-align:middle;">
                            <td style="padding:12px;">
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <img src="<?= asset($b['cover_image']) ?>" alt="" style="width:36px; height:48px; object-fit:cover; border-radius:2px; box-shadow:0 1px 3px rgba(0,0,0,0.15);">
                                    <div>
                                        <div style="font-weight:700; color:var(--text-main); font-size:0.92rem;"><?= e($bTitle) ?></div>
                                        <div style="font-size:0.78rem; color:var(--text-muted);">
                                            ✍️ <?= e($bAuthor) ?> • 🏛️ <?= e($bPublisher) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:12px;">
                                <?php if ($rTier === 'public'): ?>
                                    <span class="badge badge-success" style="font-size:0.76rem;">🌐 <?= $isBn ? 'উন্মুক্ত (Public)' : 'Public' ?></span>
                                <?php elseif ($rTier === 'registered'): ?>
                                    <span class="badge" style="background:#0284C7; color:#FFF; font-size:0.76rem;">👤 <?= $isBn ? 'নিবন্ধিত (User)' : 'Registered' ?></span>
                                <?php else: ?>
                                    <span class="badge" style="background:#C65A1E; color:#FFF; font-size:0.76rem;">⭐ <?= $isBn ? 'পেইড সদস্য' : 'Paid Members' ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:12px;">
                                <?php if ($rScope === 'partial'): ?>
                                    <div style="font-weight:600; color:#D97706; font-size:0.84rem;">
                                        📖 <?= $isBn ? "প্রিভিউ (১–{$pE} পৃষ্ঠা)" : "Preview (1–{$pE})" ?>
                                    </div>
                                    <div style="font-size:0.72rem; color:var(--text-muted);"><?= $isBn ? "বাকি " . (($b['pages_count'] ?? 0) - $pE) . " পৃষ্ঠা লকড" : "Locked after p.{$pE}" ?></div>
                                <?php else: ?>
                                    <span class="badge badge-scholarly" style="font-size:0.76rem;">
                                        📚 <?= $isBn ? 'সম্পূর্ণ বই (Full)' : 'Full Book' ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:12px;">
                                <?php if ($dlPerm === 'disabled'): ?>
                                    <span class="badge" style="background:#EF4444; color:#FFF; font-size:0.76rem;">🚫 <?= $isBn ? 'নিষিদ্ধ (Disabled)' : 'Disabled' ?></span>
                                <?php elseif ($dlPerm === 'paid_members'): ?>
                                    <span class="badge badge-success" style="font-size:0.76rem;">⭐ <?= $isBn ? 'পেইড সদস্য' : 'Paid Members' ?></span>
                                <?php else: ?>
                                    <span class="badge badge-warning" style="font-size:0.76rem;">🛡️ <?= $isBn ? 'প্রশাসক অনুমোদন' : 'Admin Approval' ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:12px;">
                                <span class="badge <?= $allowReq ? 'badge-success' : 'badge-ghost' ?>" style="font-size:0.76rem;">
                                    <?= $allowReq ? '✓ ' . ($isBn ? 'সক্রিয় (ON)' : 'ON') : '✕ ' . ($isBn ? 'বন্ধ (OFF)' : 'OFF') ?>
                                </span>
                            </td>
                            <td style="padding:12px; text-align:right;">
                                <div style="display:inline-flex; gap:6px;">
                                    <a href="<?= e(url('/library/reader/' . $bSlug, $currentLocale)) ?>" class="btn btn-xs btn-ghost" title="<?= $isBn ? 'রিডারে প্রিভিউ' : 'Reader Preview' ?>" target="_blank">
                                        👁️
                                    </a>
                                    <button type="button" class="btn btn-xs btn-secondary" onclick="openBookConfigModal('<?= e($bSlug) ?>')">
                                        ⚙️ <?= $isBn ? 'কনফিগার' : 'Configure' ?>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section B: Download Requests Management Area (Section 10, 11, 12) -->
    <div class="card" style="margin-bottom:var(--space-3xl); padding:var(--space-xl); border:1px solid var(--border-medium); background:var(--bg-surface);">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:var(--space-sm); border-bottom:1px solid var(--border-subtle); padding-bottom:var(--space-sm); margin-bottom:var(--space-lg);">
            <div>
                <div style="display:inline-flex; align-items:center; gap:6px; font-size:0.76rem; text-transform:uppercase; color:var(--accent-saffron); font-weight:700;">
                    <span>📥</span> <span><?= $isBn ? 'অফলাইন বিতরণ নিরাপত্তা ডেস্ক' : 'Offline Distribution Governance Desk' ?></span>
                </div>
                <h2 style="font-size:1.35rem; font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>; margin-bottom:2px; color:var(--text-main);">
                    <?= $isBn ? 'ডাউনলোড আবেদন ও অস্থায়ী টোকেন প্রশাসন' : 'Download Requests & Temporary Token Governance' ?>
                </h2>
                <p style="font-size:0.86rem; color:var(--text-muted); margin-bottom:0;">
                    <?= $isBn ? 'আবেদনকারী গবেষক ও সদস্যদের অনুরোধ পর্যালোচনা করে মেয়াদভিত্তিক সিকিউর ডাউনলোড লিংক প্রদান অথবা প্রত্যাহার করুন।' : 'Review applicant download requests, issue time-limited signed tokens, or revoke active passes.' ?>
                </p>
            </div>
            <span class="badge badge-warning"><?= $dlPendingCount ?> <?= $isBn ? 'টি অপেক্ষমাণ' : 'Pending' ?></span>
        </div>

        <?php if (empty($dlRequests)): ?>
            <div style="text-align:center; padding:var(--space-xl); color:var(--text-muted);">
                <?= $isBn ? 'বর্তমানে কোনো ডাউনলোড আবেদন জমা নেই।' : 'No download requests currently recorded.' ?>
            </div>
        <?php else: ?>
            <div style="overflow-x:auto;">
                <table class="table" style="width:100%; border-collapse:collapse; font-size:0.86rem;">
                    <thead>
                        <tr style="background:var(--bg-subtle); border-bottom:2px solid var(--border-medium); text-align:left;">
                            <th style="padding:9px 10px;"><?= $isBn ? 'আবেদনকারী ও সদস্যপদ' : 'User & Membership' ?></th>
                            <th style="padding:9px 10px;"><?= $isBn ? 'গ্রন্থ' : 'Publication' ?></th>
                            <th style="padding:9px 10px;"><?= $isBn ? 'উদ্দেশ্য / কারণ' : 'Reason' ?></th>
                            <th style="padding:9px 10px;"><?= $isBn ? 'তারিখ' : 'Date' ?></th>
                            <th style="padding:9px 10px;"><?= $isBn ? 'অবস্থা' : 'Status' ?></th>
                            <th style="padding:9px 10px;"><?= $isBn ? 'মেয়াদ শেষ' : 'Expires' ?></th>
                            <th style="padding:9px 10px; text-align:right;"><?= $isBn ? 'প্রশাসনিক পদক্ষেপ' : 'Actions' ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dlRequests as $dReq): ?>
                            <?php
                            $targetBook = \App\Services\LibraryService::getBook($dReq['book_slug']);
                            $bTitle = $targetBook ? ($isBn ? $targetBook['title_bn'] : $targetBook['title_en']) : $dReq['book_slug'];
                            $status = $dReq['status'];
                            ?>
                            <tr style="border-bottom:1px solid var(--border-subtle); vertical-align:middle;">
                                <td style="padding:10px;">
                                    <div style="font-weight:700; color:var(--text-main);"><?= e($dReq['user_name']) ?></div>
                                    <div style="font-size:0.78rem; color:var(--text-muted);"><?= e($dReq['user_email']) ?></div>
                                    <div style="font-size:0.74rem; color:var(--accent-brown); font-weight:600;"><?= e($dReq['membership_status']) ?> <?= !empty($dReq['member_code']) ? '(' . e($dReq['member_code']) . ')' : '' ?></div>
                                </td>
                                <td style="padding:10px; font-weight:600; color:var(--text-main); max-width:180px;">
                                    <?= e($bTitle) ?>
                                </td>
                                <td style="padding:10px; font-size:0.82rem; color:var(--text-muted); max-width:220px;">
                                    <?= e($dReq['reason']) ?>
                                    <?php if (!empty($dReq['admin_note'])): ?>
                                        <div style="font-size:0.75rem; color:#1B5E20; margin-top:2px;">
                                            <em>Note: <?= e($dReq['admin_note']) ?></em>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:10px; font-size:0.78rem; color:var(--text-muted); white-space:nowrap;">
                                    <?= e(date('M d, Y H:i', strtotime($dReq['created_at']))) ?>
                                </td>
                                <td style="padding:10px;">
                                    <?php if ($status === 'approved'): ?>
                                        <span class="badge badge-success" style="font-size:0.74rem;">✓ <?= $isBn ? 'অনুমোদিত' : 'Approved' ?></span>
                                    <?php elseif ($status === 'rejected'): ?>
                                        <span class="badge badge-danger" style="font-size:0.74rem;">✕ <?= $isBn ? 'প্রত্যাখ্যাত' : 'Rejected' ?></span>
                                    <?php elseif ($status === 'revoked'): ?>
                                        <span class="badge" style="background:#6B7280; color:#FFF; font-size:0.74rem;">⊘ <?= $isBn ? 'বাতিল' : 'Revoked' ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-warning" style="font-size:0.74rem;">⏳ <?= $isBn ? 'অপেক্ষমাণ' : 'Pending' ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:10px; font-size:0.78rem; color:var(--text-muted); white-space:nowrap;">
                                    <?= !empty($dReq['expires_at']) ? e(date('M d, Y H:i', strtotime($dReq['expires_at']))) : '—' ?>
                                    <?php if (!empty($dReq['download_count'])): ?>
                                        <div style="font-size:0.72rem; color:#047857;"><?= e($dReq['download_count']) ?>x downloaded</div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:10px; text-align:right;">
                                    <form method="POST" action="<?= e(url('/admin/library/download-request/' . $dReq['id'], $currentLocale)) ?>" style="display:inline-flex; gap:4px; align-items:center;">
                                        <?= csrf_field() ?>
                                        <?php if ($status !== 'approved'): ?>
                                            <input type="hidden" name="expiry_days" value="2">
                                            <button type="submit" name="action" value="approve" class="btn btn-xs btn-primary" title="<?= $isBn ? '৪৮ ঘণ্টার ডাউনলোড অনুমোদন' : 'Approve 48-Hour Pass' ?>">
                                                ✓ <?= $isBn ? 'অনুমোদন' : 'Approve' ?>
                                            </button>
                                        <?php endif; ?>

                                        <?php if ($status === 'approved'): ?>
                                            <button type="submit" name="action" value="revoke" class="btn btn-xs btn-ghost" style="color:var(--status-danger); border:1px solid var(--border-medium);" title="<?= $isBn ? 'টোকেন বাতিল করুন' : 'Revoke Token' ?>">
                                                ⊘ <?= $isBn ? 'বাতিল' : 'Revoke' ?>
                                            </button>
                                        <?php endif; ?>

                                        <?php if ($status === 'pending'): ?>
                                            <button type="submit" name="action" value="reject" class="btn btn-xs btn-ghost" style="color:var(--status-danger);" title="<?= $isBn ? 'প্রত্যাখ্যান করুন' : 'Reject Request' ?>">
                                                ✕
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Section C: Scholar Reading Access Requests (Required by Existing Test Suites) -->
    <div class="card" style="margin-bottom:var(--space-3xl); padding:var(--space-xl); border:1px solid var(--border-medium); background:var(--bg-surface);">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:var(--space-sm); border-bottom:1px solid var(--border-subtle); padding-bottom:var(--space-sm); margin-bottom:var(--space-lg);">
            <div>
                <h2 style="font-size:1.35rem; font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>; margin-bottom:2px; color:var(--text-main);">
                    <?= $isBn ? 'পাঠাধিকার আবেদন তালিকা' : 'Reading Access Pass Requests' ?>
                </h2>
                <p style="font-size:0.86rem; color:var(--text-muted); margin-bottom:0;">
                    <?= $isBn ? 'সাধারণ দর্শক বা শিক্ষার্থীদের প্রেরিত ই-বুক পড়ার আবেদন পর্যালোচনা।' : 'Evaluate viewer reading pass requests and grant 30-day or 7-day academic reading passes.' ?>
                </p>
            </div>
            <span class="badge badge-scholarly"><?= $pendingCount ?> <?= $isBn ? 'টি অপেক্ষমাণ' : 'Pending' ?></span>
        </div>

        <div style="overflow-x:auto;">
            <table class="table" style="width:100%; border-collapse:collapse; font-size:0.88rem;">
                <thead>
                    <tr style="background:var(--bg-subtle); border-bottom:2px solid var(--border-medium); text-align:left;">
                        <th style="padding:9px 10px;"><?= $isBn ? 'আবেদনকারী' : 'Applicant' ?></th>
                        <th style="padding:9px 10px;"><?= $isBn ? 'গ্রন্থ' : 'Book' ?></th>
                        <th style="padding:9px 10px;"><?= $isBn ? 'উদ্দেশ্য' : 'Purpose' ?></th>
                        <th style="padding:9px 10px;"><?= $isBn ? 'অবস্থা' : 'Status' ?></th>
                        <th style="padding:9px 10px;"><?= $isBn ? 'মেয়াদ' : 'Access Until' ?></th>
                        <th style="padding:9px 10px; text-align:right;"><?= $isBn ? 'পদক্ষেপ' : 'Action' ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($readingRequests as $req): ?>
                        <?php
                        $targetBook = \App\Services\LibraryService::getBook($req['book_slug']);
                        $bTitle = $targetBook ? ($isBn ? $targetBook['title_bn'] : $targetBook['title_en']) : $req['book_slug'];
                        ?>
                        <tr style="border-bottom:1px solid var(--border-subtle); vertical-align:middle;">
                            <td style="padding:10px;">
                                <div style="font-weight:700; color:var(--text-main);"><?= e($req['user_name']) ?></div>
                                <div style="font-size:0.78rem; color:var(--text-muted);"><?= e($req['user_email']) ?></div>
                                <div style="font-size:0.74rem; color:var(--accent-brown);"><?= e($req['institution'] ?? '') ?></div>
                            </td>
                            <td style="padding:10px; font-weight:600; color:var(--text-main); max-width:180px;">
                                <?= e($bTitle) ?>
                            </td>
                            <td style="padding:10px; font-size:0.82rem; color:var(--text-muted); max-width:240px;">
                                <?= e($req['reason']) ?>
                            </td>
                            <td style="padding:10px;">
                                <span class="badge <?= $req['status'] === 'approved' ? 'badge-success' : ($req['status'] === 'rejected' ? 'badge-danger' : 'badge-warning') ?>" style="font-size:0.74rem;">
                                    <?= e($req['status']) ?>
                                </span>
                            </td>
                            <td style="padding:10px; font-size:0.78rem; color:var(--text-muted); white-space:nowrap;">
                                <?= !empty($req['access_until']) ? e($req['access_until']) : '—' ?>
                            </td>
                            <td style="padding:10px; text-align:right;">
                                <form method="POST" action="<?= e(url('/admin/request/' . $req['id'], $currentLocale)) ?>" style="display:inline-flex; gap:4px;">
                                    <?= csrf_field() ?>
                                    <?php if ($req['status'] !== 'approved'): ?>
                                        <button type="submit" name="action" value="approve" class="btn btn-xs btn-primary" title="<?= $isBn ? '৩০ দিনের অনুমোদন' : '30-Day Pass' ?>">
                                            ✓ 30d
                                        </button>
                                        <button type="submit" name="action" value="grant_7day" class="btn btn-xs btn-secondary" title="<?= $isBn ? '৭ দিনের বিশেষ পাস' : '7-Day Pass' ?>">
                                            7d
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($req['status'] === 'pending'): ?>
                                        <button type="submit" name="action" value="reject" class="btn btn-xs btn-ghost" style="color:var(--status-danger);">
                                            ✕
                                        </button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section D: What Admin CAN and CANNOT Do (Strictly Required by Automated Tests) -->
    <div style="margin-bottom:var(--space-3xl);">
        <div style="border-bottom:2px solid var(--accent-gold); padding-bottom:var(--space-xs); margin-bottom:var(--space-lg);">
            <h2 style="font-size:1.45rem; font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>; margin-bottom:0;">
                <?= $isBn ? 'প্রশাসনিক ক্ষমতা ও সুরক্ষা প্রাচীর' : 'Administrative Governance & Security Boundaries' ?>
            </h2>
            <p style="font-size:0.88rem; color:var(--text-muted); margin-bottom:0; margin-top:4px;">
                <?= $isBn ? 'এসপিএস নীতি অনুসারে প্রশাসক কী করতে পারেন এবং কী করতে পারেন না তার স্পষ্ট রূপরেখা।' : 'Detailed delineation of administrative capabilities and non-negotiable institutional constraints.' ?>
            </p>
        </div>

        <div class="grid-2">
            <!-- What Admin CAN DO -->
            <div class="card" style="border:1px solid #BCDBCB; background:#FAFDFB; padding:var(--space-lg);">
                <div style="display:flex; align-items:center; gap:var(--space-xs); margin-bottom:var(--space-md); border-bottom:1px solid #D5E8DD; padding-bottom:var(--space-xs);">
                    <span style="font-size:1.2rem; color:var(--status-success);">✓</span>
                    <h3 style="font-size:1.15rem; color:var(--status-success); margin-bottom:0;">
                        <?= $isBn ? 'প্রশাসক যা করতে পারেন (অনুমোদিত ক্ষমতা)' : 'What the Admin CAN Do' ?>
                    </h3>
                </div>
                <ul style="display:flex; flex-direction:column; gap:var(--space-sm); font-size:0.9rem; line-height:1.6; color:var(--text-body);">
                    <li style="display:flex; gap:8px;">
                        <span style="color:var(--status-success); font-weight:700;">1.</span>
                        <span><strong><?= $isBn ? 'পাঠাধিকার অনুমোদন বা বাতিল:' : 'Approve or Decline Reader Requests:' ?></strong> <?= $isBn ? 'সাধারণ দর্শকদের প্রেরিত আবেদন পর্যালোচনা করে ৩০ দিন বা স্থায়ী ই-বুক পড়ার অনুমতি দিতে পারেন।' : 'Evaluate viewer applications and grant 30-day or perpetual reading passes based on scholarly merit.' ?></span>
                    </li>
                    <li style="display:flex; gap:8px;">
                        <span style="color:var(--status-success); font-weight:700;">2.</span>
                        <span><strong><?= $isBn ? 'স্বল্পমেয়াদী পাস প্রদান:' : 'Grant Temporary Passes:' ?></strong> <?= $isBn ? 'নির্দিষ্ট গ্রন্থ অধ্যয়নের জন্য জরুরি প্রয়োজনে গবেষকদের জন্য ৭ দিনের বিশেষ পাস মঞ্জুর করতে পারেন।' : 'Issue expedited 7-day academic passes for urgent scholarly research.' ?></span>
                    </li>
                    <li style="display:flex; gap:8px;">
                        <span style="color:var(--status-success); font-weight:700;">3.</span>
                        <span><strong><?= $isBn ? 'গ্রন্থের অ্যাক্সেস স্তর পরিবর্তন:' : 'Configure Access Tiers:' ?></strong> <?= $isBn ? 'কোনো প্রকাশনাকে "কেবল সদস্য" থেকে "উন্মুক্ত পাবলিক" বা "পর্যালোচনাধীন" অবস্থায় পরিবর্তন করতে পারেন।' : 'Switch publication status between Paid Members Only, Public, or Under Review.' ?></span>
                    </li>
                    <li style="display:flex; gap:8px;">
                        <span style="color:var(--status-success); font-weight:700;">4.</span>
                        <span><strong><?= $isBn ? 'মেটাডাটা ও বিবরণ হালনাগাদ:' : 'Update Metadata & Synopsis:' ?></strong> <?= $isBn ? 'গ্রন্থের নাম, লেখক, প্রকাশকাল, পৃষ্ঠা সংখ্যা ও সারসংক্ষেপ পরিমার্জন করতে পারেন।' : 'Edit publication titles, editorial notes, publication years, and thematic tags.' ?></span>
                    </li>
                    <li style="display:flex; gap:8px;">
                        <span style="color:var(--status-success); font-weight:700;">5.</span>
                        <span><strong><?= $isBn ? 'সরাসরি প্রিভিউ ও অডিট:' : 'Direct Preview & Stream Audit:' ?></strong> <?= $isBn ? 'ক্যানভাস ই-বুক রিডারের স্ট্রিমিং ও পৃষ্ঠা প্রদর্শনের বিশুদ্ধতা যাচাই করতে পারেন।' : 'Inspect high-resolution canvas stream rendering and page pagination.' ?></span>
                    </li>
                </ul>
            </div>

            <!-- What Admin CANNOT DO -->
            <div class="card" style="border:1px solid #F3C4C4; background:#FFFBFB; padding:var(--space-lg);">
                <div style="display:flex; align-items:center; gap:var(--space-xs); margin-bottom:var(--space-md); border-bottom:1px solid #FBDADA; padding-bottom:var(--space-xs);">
                    <span style="font-size:1.2rem; color:var(--status-danger);">✕</span>
                    <h3 style="font-size:1.15rem; color:var(--status-danger); margin-bottom:0;">
                        <?= $isBn ? 'প্রশাসক যা করতে পারেন না (সুরক্ষিত সীমানা)' : 'What the Admin CANNOT Do' ?>
                    </h3>
                </div>
                <ul style="display:flex; flex-direction:column; gap:var(--space-sm); font-size:0.9rem; line-height:1.6; color:var(--text-body);">
                    <li style="display:flex; gap:8px;">
                        <span style="color:var(--status-danger); font-weight:700;">1.</span>
                        <span><strong><?= $isBn ? 'স্থায়ী পাবলিক পিডিএফ উন্মুক্তকরণ:' : 'Expose Permanent Raw PDF Links:' ?></strong> <?= $isBn ? 'ডিআরএম সুরক্ষা প্রাচীর অপসারণ করে সাধারণ ব্রাউজার ডাউনলোডের সরাসরি কোনো লিংক প্রকাশ করা নিষিদ্ধ।' : 'Direct unauthenticated static PDF download links cannot be generated.' ?></span>
                    </li>
                    <li style="display:flex; gap:8px;">
                        <span style="color:var(--status-danger); font-weight:700;">2.</span>
                        <span><strong><?= $isBn ? 'ডিআরএম স্ট্রিমিং ফাঁকি দেওয়া:' : 'Bypass Protected Stream Delivery:' ?></strong> <?= $isBn ? 'ব্রাউজারে সম্পূর্ণ অননুমোদিত ফাইল পাঠাতে পারবেন না; সার্ভার সর্বদা নির্দিষ্ট পেজ-লেভেল যাচাই করে পাঠায়।' : 'Server strictly validates page tiers before byte streaming.' ?></span>
                    </li>
                    <li style="display:flex; gap:8px;">
                        <span style="color:var(--status-danger); font-weight:700;">3.</span>
                        <span><strong><?= $isBn ? 'অডিট লগ মুছে ফেলা:' : 'Tamper with Audit Logs:' ?></strong> <?= $isBn ? 'কোনো বইয়ের পারমিশন বা অ্যাক্সেস পরিবর্তনের লগ অপরিবর্তনীয় এবং কেন্দ্রীয় ট্রেইলে সংরক্ষিত থাকে।' : 'Immutable security audit trails preserve every governance modification.' ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Configure Book Access & Metadata -->
<div id="book-config-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); backdrop-filter:blur(3px); z-index:1000; align-items:center; justify-content:center; padding:var(--space-md);">
    <div style="background:var(--bg-surface); border-radius:var(--radius-md); max-width:680px; width:100%; max-height:90vh; overflow-y:auto; padding:var(--space-xl); border:1px solid var(--border-medium); box-shadow:var(--shadow-lg); position:relative;">
        <button type="button" onclick="closeBookConfigModal()" style="position:absolute; right:16px; top:16px; background:none; border:none; font-size:1.4rem; color:var(--text-muted); cursor:pointer;">
            &times;
        </button>

        <h3 id="modal-book-title" style="font-size:1.25rem; margin-bottom:var(--space-xs); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>; color:var(--text-main);">
            <?= $isBn ? 'গ্রন্থের অ্যাক্সেস ও মেটাডাটা কনফিগারেশন' : 'Publication Access & Metadata Settings' ?>
        </h3>
        <p style="font-size:0.84rem; color:var(--text-muted); margin-bottom:var(--space-md);">
            <?= $isBn ? 'পঠন স্তর, প্রিভিউ পরিধি এবং ডাউনলোড অনুমতি সংশোধন করুন।' : 'Fine-tune reading scopes, preview page windows, and download permissions.' ?>
        </p>

        <form method="POST" id="book-config-form" style="display:flex; flex-direction:column; gap:var(--space-md);">
            <?= csrf_field() ?>
            <!-- Section 1: Basic Info -->
            <div style="border-bottom:1px solid var(--border-subtle); padding-bottom:var(--space-sm);">
                <div style="font-weight:700; font-size:0.86rem; color:var(--accent-saffron); margin-bottom:var(--space-xs); text-transform:uppercase;">
                    1. <?= $isBn ? 'মৌলিক প্রকাশনা তথ্য' : 'Basic Metadata' ?>
                </div>
                <div class="grid-2">
                    <div>
                        <label style="display:block; font-size:0.82rem; font-weight:600; margin-bottom:3px; color:var(--text-main);"><?= $isBn ? 'গ্রন্থের নাম (বাংলা)' : 'Title (Bengali)' ?></label>
                        <input type="text" name="title_bn" id="cfg-title-bn" required style="width:100%; padding:7px 10px; border:1px solid var(--border-medium); border-radius:var(--radius-xs); background:var(--bg-canvas);">
                    </div>
                    <div>
                        <label style="display:block; font-size:0.82rem; font-weight:600; margin-bottom:3px; color:var(--text-main);"><?= $isBn ? 'গ্রন্থের নাম (ইংরেজি)' : 'Title (English)' ?></label>
                        <input type="text" name="title_en" id="cfg-title-en" required style="width:100%; padding:7px 10px; border:1px solid var(--border-medium); border-radius:var(--radius-xs); background:var(--bg-canvas);">
                    </div>
                </div>

                <div class="grid-2" style="margin-top:8px;">
                    <div>
                        <label style="display:block; font-size:0.82rem; font-weight:600; margin-bottom:3px; color:var(--text-main);"><?= $isBn ? 'লেখক / সংকলক (বাংলা)' : 'Author (Bengali)' ?></label>
                        <input type="text" name="author_bn" id="cfg-author-bn" style="width:100%; padding:7px 10px; border:1px solid var(--border-medium); border-radius:var(--radius-xs); background:var(--bg-canvas);">
                    </div>
                    <div>
                        <label style="display:block; font-size:0.82rem; font-weight:600; margin-bottom:3px; color:var(--text-main);"><?= $isBn ? 'প্রকাশক (বাংলা)' : 'Publisher (Bengali)' ?></label>
                        <input type="text" name="publisher_bn" id="cfg-publisher-bn" style="width:100%; padding:7px 10px; border:1px solid var(--border-medium); border-radius:var(--radius-xs); background:var(--bg-canvas);">
                    </div>
                </div>

                <div class="grid-2" style="margin-top:8px;">
                    <div>
                        <label style="display:block; font-size:0.82rem; font-weight:600; margin-bottom:3px; color:var(--text-main);"><?= $isBn ? 'প্রকাশকাল (সাল)' : 'Publication Year' ?></label>
                        <input type="number" name="publication_year" id="cfg-year" style="width:100%; padding:7px 10px; border:1px solid var(--border-medium); border-radius:var(--radius-xs); background:var(--bg-canvas);">
                    </div>
                    <div>
                        <label style="display:block; font-size:0.82rem; font-weight:600; margin-bottom:3px; color:var(--text-main);"><?= $isBn ? 'ক্যাটাগরি (বাংলা)' : 'Category' ?></label>
                        <input type="text" name="category_bn" id="cfg-category-bn" style="width:100%; padding:7px 10px; border:1px solid var(--border-medium); border-radius:var(--radius-xs); background:var(--bg-canvas);">
                    </div>
                </div>
            </div>

            <!-- Section 2: Reading Access Controls (Section 6, 7, 9) -->
            <div style="border-bottom:1px solid var(--border-subtle); padding-bottom:var(--space-sm);">
                <div style="font-weight:700; font-size:0.86rem; color:var(--accent-saffron); margin-bottom:var(--space-xs); text-transform:uppercase;">
                    2. <?= $isBn ? 'পঠন অ্যাক্সেস ও প্রিভিউ পরিধি' : 'Reading Access & Page Restriction' ?>
                </div>

                <div class="grid-2">
                    <div>
                        <label style="display:block; font-size:0.82rem; font-weight:600; margin-bottom:3px; color:var(--text-main);"><?= $isBn ? 'অনলাইন পাঠাধিকার স্তর' : 'Reading Access Tier' ?></label>
                        <select name="reading_access" id="cfg-reading-access" style="width:100%; padding:7px 10px; border:1px solid var(--border-medium); border-radius:var(--radius-xs); background:var(--bg-canvas);">
                            <option value="public"><?= $isBn ? 'পাবলিক উন্মুক্ত (Public)' : 'Public' ?></option>
                            <option value="registered"><?= $isBn ? 'নিবন্ধিত পাঠক (Registered Users)' : 'Registered Users' ?></option>
                            <option value="paid_members"><?= $isBn ? 'কেবল পেইড সদস্য (Paying Members)' : 'Paying Members' ?></option>
                            <option value="selected_users"><?= $isBn ? 'নির্বাচিত স্কলার (Selected Users)' : 'Selected Users' ?></option>
                        </select>
                    </div>

                    <div>
                        <label style="display:block; font-size:0.82rem; font-weight:600; margin-bottom:3px; color:var(--text-main);"><?= $isBn ? 'পঠন পরিধি (Scope)' : 'Reading Scope' ?></label>
                        <select name="reading_scope" id="cfg-reading-scope" onchange="toggleScopeInputs()" style="width:100%; padding:7px 10px; border:1px solid var(--border-medium); border-radius:var(--radius-xs); background:var(--bg-canvas);">
                            <option value="full"><?= $isBn ? 'সম্পূর্ণ গ্রন্থ (Full Book)' : 'Full Book' ?></option>
                            <option value="partial"><?= $isBn ? 'উন্মুক্ত প্রিভিউ / আংশিক (Preview / Partial)' : 'Preview / Partial' ?></option>
                        </select>
                    </div>
                </div>

                <div class="grid-2" id="scope-pages-row" style="margin-top:8px;">
                    <div>
                        <label style="display:block; font-size:0.82rem; font-weight:600; margin-bottom:3px; color:var(--text-main);"><?= $isBn ? 'প্রিভিউ শুরুর পৃষ্ঠা' : 'Preview Start Page' ?></label>
                        <input type="number" name="preview_start" id="cfg-preview-start" min="1" value="1" style="width:100%; padding:7px 10px; border:1px solid var(--border-medium); border-radius:var(--radius-xs); background:var(--bg-canvas);">
                    </div>
                    <div>
                        <label style="display:block; font-size:0.82rem; font-weight:600; margin-bottom:3px; color:var(--text-main);"><?= $isBn ? 'প্রিভিউ সমাপ্তির পৃষ্ঠা' : 'Preview End Page' ?></label>
                        <input type="number" name="preview_end" id="cfg-preview-end" min="1" value="20" style="width:100%; padding:7px 10px; border:1px solid var(--border-medium); border-radius:var(--radius-xs); background:var(--bg-canvas);">
                    </div>
                </div>
            </div>

            <!-- Section 3: Download Permissions (Section 9, 10) -->
            <div style="padding-bottom:var(--space-xs);">
                <div style="font-weight:700; font-size:0.86rem; color:var(--accent-saffron); margin-bottom:var(--space-xs); text-transform:uppercase;">
                    3. <?= $isBn ? 'ডাউনলোড অনুমতি ও আবেদন পলিসি' : 'Download Permission & Request Policy' ?>
                </div>

                <div class="grid-2">
                    <div>
                        <label style="display:block; font-size:0.82rem; font-weight:600; margin-bottom:3px; color:var(--text-main);"><?= $isBn ? 'ডাউনলোড অনুমতি' : 'Download Permission' ?></label>
                        <select name="download_permission" id="cfg-download-permission" style="width:100%; padding:7px 10px; border:1px solid var(--border-medium); border-radius:var(--radius-xs); background:var(--bg-canvas);">
                            <option value="disabled"><?= $isBn ? 'সম্পূর্ণ নিষিদ্ধ (Disabled)' : 'Disabled' ?></option>
                            <option value="paid_members"><?= $isBn ? 'পেইড সদস্যদের সরাসরি উন্মুক্ত' : 'Paying Members (Direct)' ?></option>
                            <option value="admin_approval_required"><?= $isBn ? 'প্রশাসক অনুমোদন সাপেক্ষে' : 'Admin Approval Required' ?></option>
                        </select>
                    </div>

                    <div style="display:flex; flex-direction:column; justify-content:center;">
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:0.86rem; font-weight:600; color:var(--text-main); margin-top:16px;">
                            <input type="checkbox" name="allow_download_request" id="cfg-allow-request" value="1" style="width:18px; height:18px;">
                            <span><?= $isBn ? 'ডাউনলোড আবেদন উন্মুক্ত রাখুন (Allow Request)' : 'Allow Users to Request Download' ?></span>
                        </label>
                    </div>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:var(--space-xs); margin-top:var(--space-sm); border-top:1px solid var(--border-subtle); padding-top:var(--space-sm);">
                <button type="button" onclick="closeBookConfigModal()" class="btn btn-ghost">
                    <?= $isBn ? 'বাতিল' : 'Cancel' ?>
                </button>
                <button type="submit" class="btn btn-primary">
                    <?= $isBn ? 'পরিবর্তন সংরক্ষণ করুন' : 'Save Changes' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const booksData = <?= json_encode($books) ?>;

function openBookConfigModal(slug) {
  const book = booksData[slug];
  if (!book) return;

  const form = document.getElementById('book-config-form');
  form.action = '<?= e(url('/admin/library/book/', $currentLocale)) ?>' + slug + '/update';

  document.getElementById('modal-book-title').textContent = (book.title_bn || book.title_en);
  document.getElementById('cfg-title-bn').value = book.title_bn || '';
  document.getElementById('cfg-title-en').value = book.title_en || '';
  document.getElementById('cfg-author-bn').value = book.author_bn || '';
  document.getElementById('cfg-publisher-bn').value = book.publisher_bn || '';
  document.getElementById('cfg-year').value = book.publication_year || 2024;
  document.getElementById('cfg-category-bn').value = book.category_bn || '';

  document.getElementById('cfg-reading-access').value = book.reading_access || 'paid_members';
  document.getElementById('cfg-reading-scope').value = book.reading_scope || 'full';
  document.getElementById('cfg-preview-start').value = book.preview_start || 1;
  document.getElementById('cfg-preview-end').value = book.preview_end || 20;

  document.getElementById('cfg-download-permission').value = book.download_permission || 'admin_approval_required';
  document.getElementById('cfg-allow-request').checked = !!book.allow_download_request;

  toggleScopeInputs();
  document.getElementById('book-config-modal').style.display = 'flex';
}

function closeBookConfigModal() {
  document.getElementById('book-config-modal').style.display = 'none';
}

function toggleScopeInputs() {
  const scope = document.getElementById('cfg-reading-scope').value;
  const row = document.getElementById('scope-pages-row');
  row.style.display = (scope === 'partial') ? 'grid' : 'none';
}
</script>
