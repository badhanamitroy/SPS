<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$bookTitle = $isBn ? $book['title_bn'] : $book['title_en'];
$author = $isBn ? $book['author_bn'] : $book['author_en'];
$publisher = $isBn ? ($book['publisher_bn'] ?? 'এসপিএস') : ($book['publisher_en'] ?? 'SPS');
$categoryName = $isBn ? $book['category_bn'] : $book['category_en'];
$synopsis = $isBn ? $book['synopsis_bn'] : $book['synopsis_en'];
$pages = $book['pages_count'] ?? 0;
$year = $book['publication_year'] ?? '';
$slug = $book['slug'];

$readingLevel = $readingLevel ?? 'locked';
$downloadInfo = $downloadEligibility ?? ['status' => 'disabled'];
$pStart = $book['preview_start'] ?? 1;
$pEnd = $book['preview_end'] ?? 20;
?>

<div class="container" style="padding-top:var(--space-2xl); padding-bottom:var(--space-4xl);">
    <!-- Breadcrumb & Role Bar -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:var(--space-sm); margin-bottom:var(--space-xl); border-bottom:1px solid var(--border-subtle); padding-bottom:var(--space-md);">
        <div class="breadcrumb" style="margin-bottom:0;">
            <a href="<?= e(url('/', $currentLocale)) ?>"><?= e(__('common.nav.home')) ?></a>
            <span class="breadcrumb-separator">/</span>
            <a href="<?= e(url('/library', $currentLocale)) ?>"><?= e(__('common.nav.library')) ?></a>
            <span class="breadcrumb-separator">/</span>
            <span style="color:var(--text-main); font-weight:600;"><?= e($bookTitle) ?></span>
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
            <strong>▲ <?= $isBn ? 'লক্ষ্য করুন:' : 'Notice:' ?></strong> <?= e($msg) ?>
        </div>
    <?php endif; ?>

    <?php if ($msg = \App\Core\Session::getFlash('error')): ?>
        <div class="alert alert-danger" style="margin-bottom:var(--space-xl);">
            <strong>✕ <?= $isBn ? 'ত্রুটি:' : 'Error:' ?></strong> <?= e($msg) ?>
        </div>
    <?php endif; ?>

    <!-- Main Detail Layout -->
    <div class="book-detail-grid">
        <!-- Left Column: Book Presentation & Actions -->
        <div class="book-detail-sidebar">
            <div class="book-cover-showcase">
                <img src="<?= asset($book['cover_image']) ?>" 
                     alt="<?= e($bookTitle) ?>"
                     class="book-cover-master">
                <div class="book-cover-shadow"></div>
            </div>

            <!-- Reading Access Action Box -->
            <div class="card" style="margin-bottom:var(--space-lg); border-color:var(--border-medium); padding:var(--space-lg);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-sm);">
                    <span style="font-size:0.82rem; font-weight:700; text-transform:uppercase; color:var(--text-muted); letter-spacing:0.05em;">
                        <?= $isBn ? 'অনলাইন পাঠাধিকার' : 'Reading Access' ?>
                    </span>
                    <?php if ($readingLevel === 'full'): ?>
                        <span class="badge badge-success">✓ <?= $isBn ? 'পূর্ণাঙ্গ উন্মুক্ত' : 'Full Access' ?></span>
                    <?php elseif ($readingLevel === 'partial'): ?>
                        <span class="badge badge-warning">📖 <?= $isBn ? "প্রিভিউ (১–{$pEnd} পৃষ্ঠা)" : "Preview (pp. 1–{$pEnd})" ?></span>
                    <?php else: ?>
                        <span class="badge" style="background:#5C4334; color:#FFFFFF;">🔒 <?= $isBn ? 'সদস্য সংরক্ষিত' : 'Members Only' ?></span>
                    <?php endif; ?>
                </div>

                <?php if ($readingLevel === 'full'): ?>
                    <p style="font-size:0.88rem; color:var(--text-body); line-height:1.6; margin-bottom:var(--space-md);">
                        <?= $isBn 
                            ? 'আপনার অ্যাকাউন্ট থেকে এই গ্রন্থটি সম্পূর্ণ অনলাইনে পাঠ করার পূর্ণ অনুমতি রয়েছে।' 
                            : 'Your account is authorized to launch and read this full edition in the browser ebook reader.' ?>
                    </p>
                    <a href="<?= e(url('/library/reader/' . $slug, $currentLocale)) ?>" class="btn btn-primary btn-lg" style="width:100%; justify-content:center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:8px;"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                        <span><?= $isBn ? 'ই-বুক পড়ুন (অনলাইন রিডার)' : 'Launch E-Book Reader' ?></span>
                    </a>
                <?php elseif ($readingLevel === 'partial'): ?>
                    <p style="font-size:0.88rem; color:var(--text-body); line-height:1.6; margin-bottom:var(--space-md);">
                        <?= $isBn 
                            ? "এই গ্রন্থটির পৃষ্ঠা ১ থেকে {$pEnd} পর্যন্ত উন্মুক্ত প্রিভিউ হিসেবে পাঠ্য। পরবর্তী অংশ সাধারণ সদস্যদের জন্য সংরক্ষিত।" 
                            : "Pages 1 to {$pEnd} are freely readable as a public preview. Subsequent pages are available to members." ?>
                    </p>
                    <a href="<?= e(url('/library/reader/' . $slug, $currentLocale)) ?>" class="btn btn-secondary btn-lg" style="width:100%; justify-content:center; border-color:var(--accent-saffron); color:var(--accent-saffron); font-weight:700;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:8px;"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                        <span><?= $isBn ? 'প্রিভিউ পড়ুন (পৃষ্ঠা ১–' . $pEnd . ')' : 'Read Preview (Pages 1–' . $pEnd . ')' ?></span>
                    </a>
                <?php else: ?>
                    <p style="font-size:0.88rem; color:var(--text-body); line-height:1.6; margin-bottom:var(--space-md);">
                        <?= $isBn 
                            ? 'গ্রন্থটি পড়তে সক্রিয় পেইড সাধারণ সদস্যপদ প্রয়োজন। আপনি চাইলে নিচে প্রশাসক বরাবরে গ্রন্থটি অধ্যয়নের কারণ উল্লেখ করে পাঠাধিকারের অনুরোধ জানাতে পারেন।' 
                            : 'Reading access is reserved for active paid general members. You may submit an access request below for administrator approval.' ?>
                    </p>
                    <a href="#request-access" class="btn btn-secondary" style="width:100%; justify-content:center; border-color:var(--accent-saffron); color:var(--accent-saffron);">
                        <span><?= $isBn ? 'পাঠের অনুরোধ জানান ↓' : 'Request Access Below ↓' ?></span>
                    </a>
                <?php endif; ?>

                <div style="font-size:0.76rem; color:var(--text-muted); text-align:center; margin-top:var(--space-sm); border-top:1px solid var(--border-subtle); padding-top:var(--space-xs);">
                    🛡️ <?= $isBn ? 'কপিরাইট সুরক্ষিত • ডিআরএম ওয়াটারমার্কড রিডার' : 'Protected Canvas Stream • Dynamic Watermark' ?>
                </div>
            </div>

            <!-- Download System Box (Separated Dimension) -->
            <div class="card" style="margin-bottom:var(--space-lg); border-color:var(--border-medium); padding:var(--space-lg); background:var(--bg-subtle);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-sm);">
                    <span style="font-size:0.82rem; font-weight:700; text-transform:uppercase; color:var(--text-muted); letter-spacing:0.05em;">
                        <?= $isBn ? 'অফলাইন ডাউনলোড সেবা' : 'Offline Download Policy' ?>
                    </span>
                    <span class="badge <?= ($downloadInfo['status'] === 'direct' || $downloadInfo['status'] === 'approved') ? 'badge-success' : 'badge-scholarly' ?>" style="font-size:0.72rem;">
                        <?= e($isBn ? $downloadInfo['label_bn'] : $downloadInfo['label_en']) ?>
                    </span>
                </div>

                <?php if ($downloadInfo['status'] === 'direct'): ?>
                    <p style="font-size:0.86rem; color:var(--text-body); line-height:1.55; margin-bottom:var(--space-sm);">
                        <?= $isBn ? 'আপনার সক্রিয় সদস্যপদ অনুযায়ী সরাসরি অফলাইন কপি ডাউনলোড করার অধিকার রয়েছে।' : 'Your membership grants direct offline PDF download privileges.' ?>
                    </p>
                    <a href="<?= e(url('/library/download/' . $slug, $currentLocale)) ?>" class="btn btn-secondary btn-sm" style="width:100%; justify-content:center;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        <span><?= $isBn ? 'পিডিএফ ডাউনলোড করুন' : 'Download Master PDF' ?></span>
                    </a>
                <?php elseif ($downloadInfo['status'] === 'approved'): ?>
                    <div style="background:#EBF7EE; border:1px solid #B8E4C2; border-radius:var(--radius-xs); padding:8px 12px; margin-bottom:var(--space-sm); font-size:0.82rem; color:#1B5E20;">
                        <strong>✓ <?= $isBn ? 'ডাউনলোড অনুমতি সক্রিয়:' : 'Download Approved:' ?></strong>
                        <div><?= $isBn ? 'মেয়াদ শেষ: ' : 'Expires: ' ?><strong><?= e($downloadInfo['expires_at'] ?? '') ?></strong></div>
                    </div>
                    <a href="<?= e(url('/library/download/' . $slug . '?token=' . urlencode($downloadInfo['token']), $currentLocale)) ?>" class="btn btn-primary btn-sm" style="width:100%; justify-content:center;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        <span><?= $isBn ? 'অনুমোদিত পিডিএফ ডাউনলোড' : 'Download Approved PDF' ?></span>
                    </a>
                <?php elseif ($downloadInfo['status'] === 'pending'): ?>
                    <div style="background:#FEF8E7; border:1px solid #FCD38D; border-radius:var(--radius-xs); padding:8px 12px; font-size:0.84rem; color:#92400E; line-height:1.5;">
                        ⏳ <strong><?= $isBn ? 'ডাউনলোড আবেদন বিচারাধীন:' : 'Download Request Pending:' ?></strong>
                        <div style="margin-top:2px;"><?= $isBn ? 'আপনার ডাউনলোড আবেদনটি পর্যালোচনার জন্য জমা রয়েছে। প্রশাসন অনুমোদন দিলে অস্থায়ী ডাউনলোড লিংক পাবেন।' : 'Your download request is awaiting admin approval.' ?></div>
                    </div>
                <?php elseif ($downloadInfo['status'] === 'disabled'): ?>
                    <p style="font-size:0.84rem; color:var(--text-muted); line-height:1.5; margin-bottom:0;">
                        <?= $isBn ? 'এই প্রকাশনাটির জন্য অফলাইন ডাউনলোড স্থায়ীভাবে সংরক্ষিত। কেবল অনলাইন রিডারের মাধ্যমে পড়া যাবে।' : 'Download is not currently available for this publication. Readable online only.' ?>
                    </p>
                <?php else: ?>
                    <!-- Can request download -->
                    <p style="font-size:0.84rem; color:var(--text-body); line-height:1.55; margin-bottom:var(--space-sm);">
                        <?= $isBn 
                            ? 'গবেষণা বা বিশেষ প্রয়োজনে অফলাইন কপির জন্য প্রশাসকের কাছে ডাউনলোড আবেদন করতে পারেন।' 
                            : 'You may submit a formal request to obtain a time-limited download pass.' ?>
                    </p>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('download-request-modal').style.display = 'flex'" style="width:100%; justify-content:center;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        <span><?= $isBn ? 'ডাউনলোড আবেদন করুন' : 'Request Download' ?></span>
                    </button>
                <?php endif; ?>
            </div>

            <!-- Technical Specifications Attributes Card -->
            <div class="card" style="background:var(--bg-surface); border-color:var(--border-subtle); font-size:0.86rem; padding:var(--space-md);">
                <h4 style="font-size:0.92rem; font-weight:700; margin-bottom:var(--space-sm); border-bottom:1px solid var(--border-medium); padding-bottom:var(--space-2xs); color:var(--text-main);">
                    <?= $isBn ? 'কারিগরি ও প্রকাশনা বিবরণ' : 'Technical Specifications' ?>
                </h4>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="color:var(--text-muted);"><?= $isBn ? 'সংরক্ষিত ফোল্ডার:' : 'Folder:' ?></span>
                        <span class="badge" style="background:<?= ($book['category_group'] ?? 'sps') === 'sps' ? 'var(--accent-saffron)' : '#4A5568' ?>; color:#FFF; font-size:0.72rem; padding:2px 8px;">
                            📁 <?= e($book['folder'] ?? 'SPS Publications') ?>
                        </span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="color:var(--text-muted);"><?= $isBn ? 'মূল ফাইলনেম:' : 'Filename:' ?></span>
                        <span style="font-family:monospace; font-size:0.76rem; max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; background:rgba(0,0,0,0.04); padding:2px 6px; border-radius:2px;" title="<?= e($book['original_filename']) ?>">
                            <?= e($book['original_filename']) ?>
                        </span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="color:var(--text-muted);"><?= $isBn ? 'মোট পৃষ্ঠা:' : 'Pages:' ?></span>
                        <span style="font-weight:600;"><?= e($pages) ?> <?= $isBn ? 'পৃষ্ঠা' : 'pages' ?></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="color:var(--text-muted);"><?= $isBn ? 'ফাইল সাইজ:' : 'Size:' ?></span>
                        <span><?= e($book['file_size'] ?? '') ?></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="color:var(--text-muted);"><?= $isBn ? 'প্রকাশকাল:' : 'Year:' ?></span>
                        <span><?= e($year) ?></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="color:var(--text-muted);"><?= $isBn ? 'প্রিভিউ পরিধি:' : 'Preview Scope:' ?></span>
                        <span style="font-weight:600; color:var(--accent-gold);">
                            <?= ($book['reading_scope'] ?? 'full') === 'partial' 
                                ? ($isBn ? "১–{$pEnd} পৃষ্ঠা" : "Pages 1–{$pEnd}")
                                : ($isBn ? 'সম্পূর্ণ গ্রন্থ' : 'Full Book') ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Editorial Overview & Access Forms -->
        <div>
            <!-- Header Metadata -->
            <div style="margin-bottom:var(--space-xl);">
                <div style="display:flex; align-items:center; gap:var(--space-xs); margin-bottom:var(--space-xs); flex-wrap:wrap;">
                    <span class="badge badge-scholarly"><?= e($categoryName) ?></span>
                    <span class="badge" style="background:var(--bg-subtle); color:var(--text-muted); font-size:0.75rem;">
                        📅 <?= e($year) ?>
                    </span>
                    <?php if (!empty($book['featured'])): ?>
                        <span class="badge badge-gold">⭐ <?= $isBn ? 'নির্বাচিত প্রকাশনা' : 'Featured Publication' ?></span>
                    <?php endif; ?>
                </div>

                <h1 style="font-size:clamp(1.8rem, 3.2vw, 2.5rem); margin-bottom:var(--space-sm); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>; color:var(--text-main); line-height:1.3;">
                    <?= e($bookTitle) ?>
                </h1>

                <!-- Credits Box -->
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:var(--space-sm); background:var(--bg-subtle); padding:var(--space-md); border-radius:var(--radius-sm); border:1px solid var(--border-subtle); margin-bottom:var(--space-lg);">
                    <div>
                        <div style="font-size:0.76rem; text-transform:uppercase; color:var(--text-muted); font-weight:700;"><?= $isBn ? 'গ্রন্থকার / সংকলক' : 'Author / Compiler' ?></div>
                        <div style="font-weight:600; color:var(--text-main); font-size:0.95rem; margin-top:2px;">
                            ✍️ <?= e($author) ?>
                        </div>
                    </div>
                    <div>
                        <div style="font-size:0.76rem; text-transform:uppercase; color:var(--text-muted); font-weight:700;"><?= $isBn ? 'প্রকাশনা সংস্থা / সেল' : 'Publishing House / Directorate' ?></div>
                        <div style="font-weight:600; color:var(--text-main); font-size:0.95rem; margin-top:2px;">
                            🏛️ <?= e($publisher) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Synopsis / Overview -->
            <div class="card" style="margin-bottom:var(--space-2xl); padding:var(--space-xl); background:var(--bg-surface); border:1px solid var(--border-medium);">
                <h3 style="font-size:1.25rem; margin-bottom:var(--space-md); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>; color:var(--text-main); border-bottom:1px solid var(--border-subtle); padding-bottom:var(--space-xs);">
                    <?= $isBn ? 'গ্রন্থ পরিচিতি ও তাত্ত্বিক সারসংক্ষেপ' : 'Synopsis & Editorial Overview' ?>
                </h3>
                <p style="font-size:1.02rem; line-height:1.8; color:var(--text-body); margin-bottom:var(--space-lg);">
                    <?= nl2br(e($synopsis)) ?>
                </p>

                <!-- Thematic Tags / Topics -->
                <?php if (!empty($book['topics']) && is_array($book['topics'])): ?>
                    <div style="border-top:1px solid var(--border-subtle); padding-top:var(--space-md);">
                        <span style="font-size:0.84rem; font-weight:700; color:var(--text-muted); margin-right:8px;">
                            <?= $isBn ? 'মূল আলোচ্য বিষয়সমূহ:' : 'Thematic Subjects:' ?>
                        </span>
                        <div style="display:inline-flex; flex-wrap:wrap; gap:6px; margin-top:4px;">
                            <?php foreach ($book['topics'] as $topic): ?>
                                <span class="badge" style="background:var(--bg-canvas); border:1px solid var(--border-medium); color:var(--text-secondary); font-size:0.8rem; padding:3px 10px;">
                                    🏷️ <?= e($topic) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Reading Access Request Form (Preserved for backward compatibility with existing tests) -->
            <div class="card" id="request-access" style="border:1px solid var(--border-medium); padding:var(--space-xl); background:var(--bg-surface);">
                <div style="border-bottom:1px solid var(--border-subtle); padding-bottom:var(--space-xs); margin-bottom:var(--space-lg);">
                    <div style="display:inline-block; font-size:0.75rem; text-transform:uppercase; font-weight:700; color:var(--accent-saffron); margin-bottom:2px;">
                        <?= $isBn ? 'বিশেষ পাঠাধিকার ডেস্ক' : 'Academic Scholar Pass Request' ?>
                    </div>
                    <h3 style="font-size:1.25rem; font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>; color:var(--text-main); margin-bottom:0;">
                        <?= $isBn ? 'গবেষক বা পাঠকের পাঠাধিকার আবেদন' : 'Request Reader Access Pass' ?>
                    </h3>
                    <p style="font-size:0.88rem; color:var(--text-muted); margin-top:4px; margin-bottom:0;">
                        <?= $isBn 
                            ? 'আপনি যদি সাধারণ দর্শক বা শিক্ষার্থী গবেষক হন এবং গ্রন্থটি পূর্ণাঙ্গ পাঠ করতে চান, তবে আপনার পরিচয় ও পাঠের উদ্দেশ্য জানিয়ে আবেদন করুন।' 
                            : 'If you are an academic researcher or guest scholar, please submit your credentials to request full online reading authorization.' ?>
                    </p>
                </div>

                <?php if ($pendingReadingRequest): ?>
                    <div class="alert alert-warning" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:var(--space-sm);">
                        <div>
                            <strong>⏳ <?= $isBn ? 'আপনার পাঠাধিকারের আবেদনটি প্রক্রিয়াধীন রয়েছে:' : 'Your reading request is currently pending:' ?></strong>
                            <div style="font-size:0.86rem; margin-top:4px;">
                                <?= $isBn ? 'আবেদনের তারিখ: ' : 'Submitted: ' ?><?= e($pendingReadingRequest['created_at']) ?> • <?= $isBn ? 'অবস্থা: ' : 'Status: ' ?><strong><?= e($pendingReadingRequest['status']) ?></strong>
                            </div>
                        </div>
                        <span class="badge badge-warning"><?= $isBn ? 'প্রশাসক মূল্যায়নের অপেক্ষায়' : 'Awaiting Admin Review' ?></span>
                    </div>
                <?php else: ?>
                    <form method="POST" action="<?= e(url('/library/request/' . $slug, $currentLocale)) ?>" style="display:flex; flex-direction:column; gap:var(--space-md);">
                        <?= csrf_field() ?>
                        <div class="grid-2">
                            <div>
                                <label style="display:block; font-size:0.86rem; font-weight:600; margin-bottom:4px; color:var(--text-main);">
                                    <?= $isBn ? 'আপনার পূর্ণ নাম' : 'Full Name' ?> <span style="color:var(--status-danger);">*</span>
                                </label>
                                <input type="text" name="user_name" required value="<?= e($userContext['user_name'] ?? '') ?>" placeholder="<?= $isBn ? 'উদাঃ ড. সুশান্ত কুমার' : 'e.g. Dr. Sushanta Kumar' ?>" style="width:100%; padding:8px 12px; border:1px solid var(--border-medium); border-radius:var(--radius-xs); background:var(--bg-canvas);">
                            </div>
                            <div>
                                <label style="display:block; font-size:0.86rem; font-weight:600; margin-bottom:4px; color:var(--text-main);">
                                    <?= $isBn ? 'ইমেইল ঠিকানা' : 'Email Address' ?> <span style="color:var(--status-danger);">*</span>
                                </label>
                                <input type="email" name="user_email" required value="<?= e($userContext['user_email'] ?? '') ?>" placeholder="name@university.edu" style="width:100%; padding:8px 12px; border:1px solid var(--border-medium); border-radius:var(--radius-xs); background:var(--bg-canvas);">
                            </div>
                        </div>

                        <div class="grid-2">
                            <div>
                                <label style="display:block; font-size:0.86rem; font-weight:600; margin-bottom:4px; color:var(--text-main);">
                                    <?= $isBn ? 'মোবাইল নম্বর' : 'Phone Number' ?>
                                </label>
                                <input type="text" name="user_phone" placeholder="+880 17XX-XXXXXX" style="width:100%; padding:8px 12px; border:1px solid var(--border-medium); border-radius:var(--radius-xs); background:var(--bg-canvas);">
                            </div>
                            <div>
                                <label style="display:block; font-size:0.86rem; font-weight:600; margin-bottom:4px; color:var(--text-main);">
                                    <?= $isBn ? 'শিক্ষাপ্রতিষ্ঠান বা গবেষণাগার' : 'Institution / Department' ?>
                                </label>
                                <input type="text" name="institution" placeholder="<?= $isBn ? 'উদাঃ ঢাকা বিশ্ববিদ্যালয়, দর্শন বিভাগ' : 'University of Dhaka, Philosophy Dept.' ?>" style="width:100%; padding:8px 12px; border:1px solid var(--border-medium); border-radius:var(--radius-xs); background:var(--bg-canvas);">
                            </div>
                        </div>

                        <div>
                            <label style="display:block; font-size:0.86rem; font-weight:600; margin-bottom:4px; color:var(--text-main);">
                                <?= $isBn ? 'গ্রন্থটি অধ্যয়নের উদ্দেশ্য ও যৌক্তিকতা' : 'Reason & Scholarly Purpose' ?> <span style="color:var(--status-danger);">*</span>
                            </label>
                            <textarea name="reason" rows="3" required placeholder="<?= $isBn ? 'কোন বিষয়ে গবেষণার জন্য এই গ্রন্থটি পাঠ করা জরুরি তা সংক্ষেপে লিখুন...' : 'Briefly describe your scholarly study or research purpose...' ?>" style="width:100%; padding:8px 12px; border:1px solid var(--border-medium); border-radius:var(--radius-xs); background:var(--bg-canvas); resize:vertical; font-family:inherit;"></textarea>
                        </div>

                        <div style="display:flex; justify-content:flex-end;">
                            <button type="submit" class="btn btn-primary">
                                <span><?= $isBn ? 'পাঠাধিকারের আবেদন জমা দিন' : 'Submit Access Request' ?> →</span>
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Download Request Modal -->
<div id="download-request-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); backdrop-filter:blur(3px); z-index:1000; align-items:center; justify-content:center; padding:var(--space-md);">
    <div style="background:var(--bg-surface); border-radius:var(--radius-md); max-width:520px; width:100%; padding:var(--space-xl); border:1px solid var(--border-medium); box-shadow:var(--shadow-lg); position:relative;">
        <button type="button" onclick="document.getElementById('download-request-modal').style.display = 'none'" style="position:absolute; right:16px; top:16px; background:none; border:none; font-size:1.4rem; color:var(--text-muted); cursor:pointer;">
            &times;
        </button>

        <div style="display:flex; align-items:center; gap:8px; margin-bottom:var(--space-xs);">
            <span style="font-size:1.3rem;">📥</span>
            <h3 style="font-size:1.25rem; margin-bottom:0; font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;">
                <?= $isBn ? 'অফলাইন ডাউনলোড আবেদন' : 'Request Offline Download' ?>
            </h3>
        </div>
        <p style="font-size:0.86rem; color:var(--text-muted); margin-bottom:var(--space-md); line-height:1.5;">
            <strong><?= e($bookTitle) ?></strong> — <?= $isBn ? 'অফলাইন গবেষণার প্রয়োজনে সাময়িক সিকিউর ডাউনলোড লিংক পেতে নিচের তথ্য পূরণ করুন।' : 'Submit details to request a temporary signed download pass for scholarly offline review.' ?>
        </p>

        <form method="POST" action="<?= e(url('/library/download-request/' . $slug, $currentLocale)) ?>" style="display:flex; flex-direction:column; gap:var(--space-sm);">
            <?= csrf_field() ?>
            <div>
                <label style="display:block; font-size:0.84rem; font-weight:600; margin-bottom:3px; color:var(--text-main);">
                    <?= $isBn ? 'আপনার নাম' : 'Your Name' ?> <span style="color:var(--status-danger);">*</span>
                </label>
                <input type="text" name="user_name" required value="<?= e($userContext['user_name'] ?? '') ?>" style="width:100%; padding:7px 10px; border:1px solid var(--border-medium); border-radius:var(--radius-xs); background:var(--bg-canvas);">
            </div>

            <div>
                <label style="display:block; font-size:0.84rem; font-weight:600; margin-bottom:3px; color:var(--text-main);">
                    <?= $isBn ? 'ইমেইল ঠিকানা' : 'Email Address' ?> <span style="color:var(--status-danger);">*</span>
                </label>
                <input type="email" name="user_email" required value="<?= e($userContext['user_email'] ?? '') ?>" style="width:100%; padding:7px 10px; border:1px solid var(--border-medium); border-radius:var(--radius-xs); background:var(--bg-canvas);">
            </div>

            <?php if (!empty($userContext['member_code'])): ?>
                <input type="hidden" name="member_code" value="<?= e($userContext['member_code']) ?>">
            <?php endif; ?>

            <div>
                <label style="display:block; font-size:0.84rem; font-weight:600; margin-bottom:3px; color:var(--text-main);">
                    <?= $isBn ? 'অফলাইন কপির প্রয়োজনীয়তার কারণ' : 'Reason for Offline Copy' ?> <span style="color:var(--status-danger);">*</span>
                </label>
                <textarea name="reason" rows="3" required placeholder="<?= $isBn ? 'কেন অফলাইন পিডিএফ কপি প্রয়োজন তা উল্লেখ করুন...' : 'Specify why an offline PDF copy is necessary...' ?>" style="width:100%; padding:7px 10px; border:1px solid var(--border-medium); border-radius:var(--radius-xs); background:var(--bg-canvas); resize:vertical; font-family:inherit; font-size:0.88rem;"></textarea>
            </div>

            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                🔒 <?= $isBn ? 'অনুমোদিত হলে লিংকটি কেবল নির্দিষ্ট সময় পর্যন্ত কার্যকর থাকবে।' : 'Approved links are single-user and time-limited.' ?>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:var(--space-xs); margin-top:var(--space-sm);">
                <button type="button" onclick="document.getElementById('download-request-modal').style.display = 'none'" class="btn btn-ghost btn-sm">
                    <?= $isBn ? 'বাতিল' : 'Cancel' ?>
                </button>
                <button type="submit" class="btn btn-primary btn-sm">
                    <?= $isBn ? 'আবেদন জমা দিন' : 'Submit Request' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.book-detail-grid {
  display: grid;
  grid-template-columns: 360px 1fr;
  gap: var(--space-3xl);
  align-items: start;
}

.book-cover-showcase {
  background: linear-gradient(135deg, #EFE9E0 0%, #DFD5C6 100%);
  border-radius: var(--radius-sm);
  border: 1px solid var(--border-medium);
  padding: var(--space-xl);
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: var(--space-lg);
  box-shadow: var(--shadow-sm);
  position: relative;
}

.book-cover-master {
  max-width: 100%;
  max-height: 460px;
  object-fit: contain;
  border-radius: 2px;
  box-shadow: -8px 10px 28px rgba(0,0,0,0.28), 0 0 0 1px rgba(0,0,0,0.12);
  transition: transform 0.25s ease;
}

.book-cover-showcase:hover .book-cover-master {
  transform: scale(1.02);
}

@media (max-width: 900px) {
  .book-detail-grid {
    grid-template-columns: 1fr;
    gap: var(--space-xl);
  }
  .book-cover-showcase {
    max-width: 400px;
    margin: 0 auto var(--space-lg);
  }
}
</style>
