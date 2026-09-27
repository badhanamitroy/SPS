<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';

$pendingCount = 0;
$approvedCount = 0;
foreach ($requests as $r) {
    if ($r['status'] === 'pending') $pendingCount++;
    if ($r['status'] === 'approved') $approvedCount++;
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
                <?= $isBn ? 'সিস্টেম ভার্সন ১.০' : 'System v1.0' ?>
            </span>
        </div>
        <h1 style="font-size:clamp(1.8rem, 3.2vw, 2.4rem); margin-bottom:var(--space-xs); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;">
            <?= $isBn ? 'গ্রন্থাগার ও ই-বুক পাঠাধিকার নিয়ন্ত্রণকক্ষ' : 'Library & Reader Access Control Console' ?>
        </h1>
        <p class="section-subtitle" style="font-size:1rem; line-height:1.65;">
            <?= $isBn 
                ? 'এসপিএস-এর মূল মিডিয়া ডিরেক্টরির সংরক্ষিত পিডিএফ প্রকাশনাগুলোর পাঠাধিকার তত্ত্বাবধান, সাধারণ দর্শকদের আবেদন পর্যালোচনা এবং প্রাতিষ্ঠানিক নীতি পরিচালনা।' 
                : 'Manage PDF library items, evaluate viewer access requests, and enforce institutional copyright and governance policies.' ?>
        </p>
    </div>

    <!-- Metric Counters -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:var(--space-md); margin-bottom:var(--space-3xl);">
        <div class="card" style="padding:var(--space-lg); border-left:4px solid var(--accent-gold);">
            <div style="font-size:0.8rem; text-transform:uppercase; color:var(--text-muted); font-weight:600;"><?= $isBn ? 'মোট সংরক্ষিত ই-বুক' : 'Total Archival PDFs' ?></div>
            <div style="font-size:1.8rem; font-weight:700; color:var(--text-main); margin-top:4px;">3</div>
            <div style="font-size:0.78rem; color:var(--text-muted);"><?= $isBn ? 'মিডিয়া ফোল্ডারে সংরক্ষিত' : 'Active in Media/PDF-Libraries' ?></div>
        </div>

        <div class="card" style="padding:var(--space-lg); border-left:4px solid var(--status-warning);">
            <div style="font-size:0.8rem; text-transform:uppercase; color:var(--text-muted); font-weight:600;"><?= $isBn ? 'বিচারাধীন আবেদন' : 'Pending Requests' ?></div>
            <div style="font-size:1.8rem; font-weight:700; color:var(--accent-saffron); margin-top:4px;"><?= $pendingCount ?></div>
            <div style="font-size:0.78rem; color:var(--text-muted);"><?= $isBn ? 'অনুমোদনের অপেক্ষায়' : 'Awaiting admin evaluation' ?></div>
        </div>

        <div class="card" style="padding:var(--space-lg); border-left:4px solid var(--status-success);">
            <div style="font-size:0.8rem; text-transform:uppercase; color:var(--text-muted); font-weight:600;"><?= $isBn ? 'অনুমোদিত পাঠাধিকার' : 'Approved Passes' ?></div>
            <div style="font-size:1.8rem; font-weight:700; color:var(--status-success); margin-top:4px;"><?= $approvedCount ?></div>
            <div style="font-size:0.78rem; color:var(--text-muted);"><?= $isBn ? 'সক্রিয় পাঠক অনুমতি' : 'Granted academic access' ?></div>
        </div>

        <div class="card" style="padding:var(--space-lg); border-left:4px solid var(--accent-brown);">
            <div style="font-size:0.8rem; text-transform:uppercase; color:var(--text-muted); font-weight:600;"><?= $isBn ? 'সংরক্ষিত ডেটা সাইজ' : 'Total Storage Size' ?></div>
            <div style="font-size:1.8rem; font-weight:700; color:var(--text-main); margin-top:4px;">66.6 MB</div>
            <div style="font-size:0.78rem; color:var(--text-muted);"><?= $isBn ? 'সর্বমোট ৩টি প্রকাশনা' : 'Across 3 master publications' ?></div>
        </div>
    </div>

    <!-- Special Highlight: What Admin CAN and CANNOT Do -->
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
            <div class="card" style="border:1px solid #BCDBCB; background:#FAFDFB;">
                <div style="display:flex; align-items:center; gap:var(--space-xs); margin-bottom:var(--space-md); border-bottom:1px solid #D5E8DD; padding-bottom:var(--space-xs);">
                    <span style="font-size:1.2rem;">✓</span>
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
            <div class="card" style="border:1px solid #F1BDB8; background:#FDF8F8;">
                <div style="display:flex; align-items:center; gap:var(--space-xs); margin-bottom:var(--space-md); border-bottom:1px solid #F5D2CF; padding-bottom:var(--space-xs);">
                    <span style="font-size:1.2rem;">✕</span>
                    <h3 style="font-size:1.15rem; color:var(--status-danger); margin-bottom:0;">
                        <?= $isBn ? 'প্রশাসক যা করতে পারেন না (সুরক্ষা প্রাচীর)' : 'What the Admin CANNOT Do' ?>
                    </h3>
                </div>
                <ul style="display:flex; flex-direction:column; gap:var(--space-sm); font-size:0.9rem; line-height:1.6; color:var(--text-body);">
                    <li style="display:flex; gap:8px;">
                        <span style="color:var(--status-danger); font-weight:700;">1.</span>
                        <span><strong><?= $isBn ? 'ফাইল ডাউনলোড লিংক উন্মুক্ত করা নিষিদ্ধ:' : 'Cannot Expose Direct PDF Downloads:' ?></strong> <?= $isBn ? 'সিস্টেম আর্কিটেকচার দ্বারা সুরক্ষিত; কোনো পাঠককে ডাউনলোড লিংক প্রদান বা অনুমতি দেওয়া সম্পূর্ণ নিষিদ্ধ।' : 'Strict architectural safeguard preventing raw file downloads to preserve institutional copyright.' ?></span>
                    </li>
                    <li style="display:flex; gap:8px;">
                        <span style="color:var(--status-danger); font-weight:700;">2.</span>
                        <span><strong><?= $isBn ? 'আর্থিক হিসাব পরিবর্তন করা নিষিদ্ধ:' : 'Cannot Alter Immutable Financial Ledgers:' ?></strong> <?= $isBn ? 'পেইড সদস্যপদের পেমেন্ট বা অনুদানের হিসাব অপরিবর্তনীয় (Append-only ledger); ব্যাকডেটেড পরিবর্তন অসম্ভব।' : 'Financial ledgers are append-only. No admin can delete or alter audit voucher records.' ?></span>
                    </li>
                    <li style="display:flex; gap:8px;">
                        <span style="color:var(--status-danger); font-weight:700;">3.</span>
                        <span><strong><?= $isBn ? 'বিনা নিরীক্ষায় প্রকাশনা মুছে ফেলা নিষিদ্ধ:' : 'Cannot Erase Published Manuscripts:' ?></strong> <?= $isBn ? 'পরিচালনা পরিষদের সম্মিলিত সিদ্ধান্ত ও অডিট লগ ব্যতিরেকে কোনো মূল আর্কাইভ মোছা যায় না।' : 'Permanent deletion requires Governing Council consensus and audit logging.' ?></span>
                    </li>
                    <li style="display:flex; gap:8px;">
                        <span style="color:var(--status-danger); font-weight:700;">4.</span>
                        <span><strong><?= $isBn ? 'ব্যবহারকারীর গোপন তথ্যে প্রবেশ নিষিদ্ধ:' : 'Cannot Access User Private Credentials:' ?></strong> <?= $isBn ? 'গুগল অথেন্টিকেশন থাকায় কোনো পাসওয়ার্ড বা ব্যাংক কার্ডের তথ্যে প্রশাসকের প্রবেশাধিকার নেই।' : 'Zero-credential access architecture protects user Google OAuth tokens.' ?></span>
                    </li>
                    <li style="display:flex; gap:8px;">
                        <span style="color:var(--status-danger); font-weight:700;">5.</span>
                        <span><strong><?= $isBn ? 'নিরীক্ষা বহির্ভূত ক্ষমতা নেই:' : 'Cannot Bypass Audit Trails:' ?></strong> <?= $isBn ? 'প্রশাসকের প্রতিটি অনুমোদন ও প্রত্যাখ্যান সিস্টেম অডিট লগে অপরিবর্তনীয়ভাবে লিপিবদ্ধ হয়।' : 'Every approval, rejection, and role adjustment is permanently recorded in system audit logs.' ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Section: Reading Access Requests Inbox -->
    <div style="margin-bottom:var(--space-3xl);">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:var(--space-sm); border-bottom:1px solid var(--border-medium); padding-bottom:var(--space-sm); margin-bottom:var(--space-lg);">
            <div>
                <h2 style="font-size:1.35rem; font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>; margin-bottom:0;">
                    <?= $isBn ? 'পাঠাধিকার আবেদন তালিকা' : 'Viewer Reading Access Requests' ?>
                </h2>
                <span style="font-size:0.84rem; color:var(--text-muted);">
                    <?= $isBn ? 'সাধারণ দর্শকদের নিকট থেকে প্রাপ্ত আবেদন ও সিদ্ধান্তের ইনবক্স' : 'Pending and resolved applications from scholarly viewers' ?>
                </span>
            </div>
            <span class="badge badge-scholarly">
                <?= count($requests) ?> <?= $isBn ? 'টি আবেদন নথিভুক্ত' : 'Total Requests' ?>
            </span>
        </div>

        <?php if (empty($requests)): ?>
            <div class="card" style="text-align:center; padding:var(--space-2xl); color:var(--text-muted);">
                <?= $isBn ? 'বর্তমানে কোনো নতুন পাঠাধিকার আবেদন জমা নেই।' : 'No access requests currently on record.' ?>
            </div>
        <?php else: ?>
            <div style="display:flex; flex-direction:column; gap:var(--space-md);">
                <?php foreach ($requests as $req): 
                    $targetBook = $books[$req['book_slug']] ?? null;
                    $bookName = $targetBook ? ($isBn ? $targetBook['title_bn'] : $targetBook['title_en']) : $req['book_slug'];
                ?>
                    <div class="card" style="border:1px solid var(--border-medium); background:var(--bg-surface); padding:var(--space-lg); border-left:4px solid <?= $req['status'] === 'approved' ? 'var(--status-success)' : ($req['status'] === 'rejected' ? 'var(--status-danger)' : 'var(--status-warning)') ?>;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:var(--space-sm); margin-bottom:var(--space-xs);">
                            <div>
                                <span style="font-weight:700; font-size:1.05rem; color:var(--text-main);">
                                    <?= e($req['user_name']) ?>
                                </span>
                                <span style="color:var(--text-muted); font-size:0.85rem; margin-left:6px;">
                                    &lt;<?= e($req['user_email']) ?>&gt;
                                </span>
                                <?php if (!empty($req['institution'])): ?>
                                    <span class="badge" style="background:var(--bg-subtle); font-size:0.75rem; margin-left:6px;">
                                        <?= e($req['institution']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div style="display:flex; align-items:center; gap:var(--space-xs);">
                                <?php if ($req['status'] === 'approved'): ?>
                                    <span class="badge badge-success">✓ <?= $isBn ? 'অনুমোদিত' : 'Approved' ?> (<?= e($req['access_until'] ?? '30 Days') ?>)</span>
                                <?php elseif ($req['status'] === 'rejected'): ?>
                                    <span class="badge badge-danger">✕ <?= $isBn ? 'বাতিল' : 'Rejected' ?></span>
                                <?php else: ?>
                                    <span class="badge badge-warning">⏳ <?= $isBn ? 'পর্যালোচনাধীন' : 'Pending Decision' ?></span>
                                <?php endif; ?>
                                <span style="font-size:0.78rem; color:var(--text-muted);"><?= e($req['created_at']) ?></span>
                            </div>
                        </div>

                        <!-- Target Book -->
                        <div style="font-size:0.88rem; margin-bottom:var(--space-xs);">
                            <span style="color:var(--text-muted);"><?= $isBn ? 'অনুরোধকৃত গ্রন্থ:' : 'Requested Book:' ?></span>
                            <strong style="color:var(--accent-saffron);">📖 <?= e($bookName) ?></strong>
                        </div>

                        <!-- User Reason -->
                        <div style="background:var(--bg-subtle); padding:10px 14px; border-radius:var(--radius-xs); border:1px solid var(--border-subtle); margin-bottom:var(--space-md); font-size:0.88rem; line-height:1.6; color:var(--text-body);">
                            <strong><?= $isBn ? 'পাঠের উদ্দেশ্য:' : 'Reason / Scholarly Purpose:' ?></strong> <?= e($req['reason']) ?>
                        </div>

                        <?php if (!empty($req['admin_note'])): ?>
                            <div style="font-size:0.82rem; color:var(--accent-brown); margin-bottom:var(--space-sm);">
                                <strong><?= $isBn ? 'প্রশাসকের গৃহীত সিদ্ধান্ত:' : 'Recorded Admin Note:' ?></strong> <?= e($req['admin_note']) ?>
                            </div>
                        <?php endif; ?>

                        <!-- Action Buttons -->
                        <div style="display:flex; gap:var(--space-xs); flex-wrap:wrap; align-items:center; border-top:1px solid var(--border-subtle); padding-top:var(--space-sm);">
                            <form action="<?= e(url('/admin/request/' . $req['id'], $currentLocale)) ?>" method="POST" style="display:inline-flex;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="btn btn-sm btn-primary" style="background:var(--status-success); border-color:var(--status-success);">
                                    ✓ <?= $isBn ? 'অনুমোদন করুন (৩০ দিন)' : 'Approve (30 Days)' ?>
                                </button>
                            </form>

                            <form action="<?= e(url('/admin/request/' . $req['id'], $currentLocale)) ?>" method="POST" style="display:inline-flex;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="grant_7day">
                                <button type="submit" class="btn btn-sm btn-secondary" style="border-color:var(--accent-gold); color:var(--accent-brown);">
                                    ⏱️ <?= $isBn ? '৭ দিনের জরুরি পাস' : '7-Day Academic Pass' ?>
                                </button>
                            </form>

                            <form action="<?= e(url('/admin/request/' . $req['id'], $currentLocale)) ?>" method="POST" style="display:inline-flex;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="btn btn-sm btn-ghost" style="color:var(--status-danger);">
                                    ✕ <?= $isBn ? 'বাতিল করুন' : 'Decline' ?>
                                </button>
                            </form>

                            <?php if ($targetBook): ?>
                                <a href="<?= e(url('/library/reader/' . $targetBook['slug'], $currentLocale)) ?>" class="btn btn-sm btn-ghost" style="margin-left:auto;" target="_blank">
                                    📖 <?= $isBn ? 'রিডারে পরীক্ষা করুন' : 'Test in Reader' ?> ↗
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Section: Physical PDF Inventory in Media/PDF-Libraries -->
    <div>
        <div style="border-bottom:1px solid var(--border-medium); padding-bottom:var(--space-sm); margin-bottom:var(--space-lg);">
            <h2 style="font-size:1.35rem; font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>; margin-bottom:0;">
                <?= $isBn ? 'মিডিয়া ফোল্ডারের মূল পিডিএফ সংগ্রহ' : 'Media/PDF-Libraries File Inventory' ?>
            </h2>
            <span style="font-size:0.84rem; color:var(--text-muted);">
                <?= $isBn ? 'ডিরেক্টরি: SPS > Media > PDF-Libraries-এ সংরক্ষিত ফাইলসমূহ' : 'Master physical files stored in SPS/Media/PDF-Libraries' ?>
            </span>
        </div>

        <div style="overflow-x:auto;">
            <table style="width:100%; border:1px solid var(--border-medium); background:var(--bg-surface); font-size:0.88rem; border-collapse:collapse;">
                <thead>
                    <tr style="background:var(--bg-subtle); border-bottom:1px solid var(--border-medium); text-align:left;">
                        <th style="padding:10px 14px;"><?= $isBn ? 'কভার' : 'Cover' ?></th>
                        <th style="padding:10px 14px;"><?= $isBn ? 'ফাইল নাম ও শিরোনাম' : 'Filename & Title' ?></th>
                        <th style="padding:10px 14px;"><?= $isBn ? 'আকার' : 'File Size' ?></th>
                        <th style="padding:10px 14px;"><?= $isBn ? 'পৃষ্ঠা' : 'Pages' ?></th>
                        <th style="padding:10px 14px;"><?= $isBn ? 'অ্যাক্সেস টিয়ার' : 'Access Tier' ?></th>
                        <th style="padding:10px 14px;"><?= $isBn ? 'অ্যাকশন' : 'Actions' ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $slug => $b): ?>
                        <tr style="border-bottom:1px solid var(--border-subtle);">
                            <td style="padding:10px 14px; width:60px;">
                                <img src="<?= asset($b['cover_image']) ?>" alt="" style="width:40px; height:55px; object-fit:contain; border-radius:2px; border:1px solid var(--border-medium);">
                            </td>
                            <td style="padding:10px 14px;">
                                <strong><?= e($isBn ? $b['title_bn'] : $b['title_en']) ?></strong><br>
                                <code style="font-size:0.78rem; color:var(--text-muted);"><?= e($b['original_filename']) ?></code>
                            </td>
                            <td style="padding:10px 14px; font-weight:600;"><?= e($b['file_size']) ?></td>
                            <td style="padding:10px 14px;"><?= e($b['pages_count']) ?></td>
                            <td style="padding:10px 14px;">
                                <span class="badge" style="background:#5C4334; color:#FFF; font-size:0.75rem;">
                                    🔒 <?= $isBn ? 'পেইড সদস্য সংরক্ষিত' : 'Paid Members Only' ?>
                                </span>
                            </td>
                            <td style="padding:10px 14px;">
                                <a href="<?= e(url('/library/reader/' . $slug, $currentLocale)) ?>" class="btn btn-sm btn-primary" style="padding:4px 10px; font-size:0.8rem;">
                                    📖 <?= $isBn ? 'রিডার চালান' : 'Launch Reader' ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
