<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
?>

<div class="container" style="padding-top:var(--space-2xl); padding-bottom:var(--space-4xl);">
    <!-- Breadcrumb & Top Bar -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:var(--space-sm); margin-bottom:var(--space-xl); border-bottom:1px solid var(--border-subtle); padding-bottom:var(--space-md);">
        <div class="breadcrumb" style="margin-bottom:0;">
            <a href="<?= e(url('/', $currentLocale)) ?>"><?= e(__('common.nav.home')) ?></a>
            <span class="breadcrumb-separator">/</span>
            <span style="color:var(--text-main); font-weight:600;"><?= $isBn ? 'গ্রন্থাগার' : 'Library' ?></span>
        </div>

        <!-- Role Simulator Toolbar -->
        <div style="display:flex; align-items:center; gap:var(--space-xs); background:var(--bg-subtle); padding:4px 8px; border-radius:var(--radius-sm); border:1px solid var(--border-medium); font-size:0.82rem;">
            <span style="font-weight:600; color:var(--text-muted); margin-right:4px;">
                <?= $isBn ? 'মোড প্রিভিউ:' : 'View as:' ?>
            </span>
            <a href="<?= e(url('/library/role?role=viewer', $currentLocale)) ?>" 
               class="btn btn-sm <?= $currentRole === 'viewer' ? 'btn-primary' : 'btn-ghost' ?>" 
               style="padding:3px 8px; font-size:0.78rem;">
                👤 <?= $isBn ? 'সাধারণ দর্শক' : 'Viewer (Guest)' ?>
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

    <!-- Active State Notice Banner -->
    <?php if ($currentRole === 'paid_member'): ?>
        <div class="alert alert-success" style="margin-bottom:var(--space-2xl); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:var(--space-sm);">
            <div>
                <strong>✓ <?= $isBn ? 'সাধারণ সদস্য সুবিধা সক্রিয়:' : 'Paid General Member Access Active:' ?></strong>
                <span><?= $isBn ? 'আপনার অ্যাকাউন্টে সকল প্রকাশনার সার্বক্ষণিক অনলাইন ই-বুক পড়ার অধিকার উন্মুক্ত রয়েছে।' : 'Your account holds all-time unrestricted online reading privileges for all archival editions.' ?></span>
            </div>
            <span class="badge badge-success"><?= $isBn ? 'ডিজিটাল রিডার প্রস্তুত' : 'E-Book Reader Ready' ?></span>
        </div>
    <?php elseif ($currentRole === 'admin'): ?>
        <div class="alert alert-info" style="margin-bottom:var(--space-2xl); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:var(--space-sm);">
            <div>
                <strong>🛡️ <?= $isBn ? 'প্রশাসক মোড সক্রিয়:' : 'Administrative Mode Active:' ?></strong>
                <span><?= $isBn ? 'গ্রন্থাগারের সকল ফাইল ও পাঠাধিকার নিয়ন্ত্রণের পূর্ণ এক্সেস রয়েছে।' : 'Full access to read books and inspect reader access governance.' ?></span>
            </div>
            <a href="<?= e(url('/admin/library', $currentLocale)) ?>" class="btn btn-sm btn-secondary" style="border-color:var(--accent-saffron); color:var(--accent-saffron);">
                <?= $isBn ? 'প্রশাসনিক প্যানেল দেখুন →' : 'Go to Admin Portal →' ?>
            </a>
        </div>
    <?php else: ?>
        <div class="alert alert-warning" style="margin-bottom:var(--space-2xl); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:var(--space-sm);">
            <div>
                <strong>🔒 <?= $isBn ? 'সাধারণ দর্শক মোড:' : 'Guest Viewer Mode:' ?></strong>
                <span><?= $isBn ? 'পূর্ণাঙ্গ ই-বুক পড়া কেবল নিয়মিত পেইড সদস্যদের জন্য নির্ধারিত। যেকোনো গ্রন্থের পাঠাধিকার পেতে নিচে "অনুরোধ পাঠান" বোতাম ব্যবহার করুন।' : 'Full e-book reading is reserved for all-time paid general members. Viewers can submit an access request to the admin.' ?></span>
            </div>
            <a href="<?= e(url('/get-involved', $currentLocale)) ?>" class="btn btn-sm btn-primary">
                <?= $isBn ? 'সদস্যপদ গ্রহণ করুন' : 'Become a Member' ?>
            </a>
        </div>
    <?php endif; ?>

    <!-- Library Header Title & Intro -->
    <div style="margin-bottom:var(--space-3xl);">
        <span class="section-tag"><?= $isBn ? 'জ্ঞানপীঠ পাণ্ডুলিপি ও ই-বুক সংগ্রহশালা' : 'Digital Archive & Sacred E-Book Repository' ?></span>
        <h1 style="font-size:clamp(2rem, 3.5vw, 2.7rem); margin-bottom:var(--space-sm);">
            <?= $isBn ? 'ডিজিটাল গ্রন্থাগার ও প্রকাশনাসমূহ' : 'Digital Library & Research Publications' ?>
        </h1>
        <p class="section-subtitle" style="font-size:1.05rem; line-height:1.7;">
            <?= $isBn 
                ? 'এসপিএস-এর মূল মিডিয়া লাইব্রেরি থেকে সংকলিত প্রামাণ্য প্রকাশনা, স্মারক গ্রন্থ ও মাসিক পত্রিকার ডিজিটাল সংগ্রহ। উচ্চমানের অনলাইন ই-বুক রিডারের মাধ্যমে পড়া যায় কোনো ফাইল ডাউনলোড ছাড়া।' 
                : 'Curated scholarly editions, commemorative journals, and monthly periodicals from the SPS digital repository. High-resolution canvas e-book reader with download-restricted protection.' ?>
        </p>
    </div>

    <!-- Publications Books Grid -->
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(340px, 1fr)); gap:var(--space-2xl);">
        <?php foreach ($books as $slug => $book): ?>
            <div class="card" style="display:flex; flex-direction:column; padding:0; overflow:hidden; border:1px solid var(--border-medium); background:var(--bg-surface); transition:transform var(--transition-normal), box-shadow var(--transition-normal);">
                <!-- Cover Image Container with Book Spine Effect -->
                <div style="position:relative; background:#EDE7DF; height:340px; display:flex; align-items:center; justify-content:center; padding:var(--space-lg); border-bottom:1px solid var(--border-subtle); overflow:hidden;">
                    <div style="position:absolute; inset:0; background:radial-gradient(circle, rgba(0,0,0,0.03) 0%, rgba(0,0,0,0.12) 100%); pointer-events:none;"></div>
                    
                    <!-- Book Visual with Realistic Shadow -->
                    <img src="<?= asset($book['cover_image']) ?>" 
                         alt="<?= e($isBn ? $book['title_bn'] : $book['title_en']) ?>"
                         style="max-height:280px; max-width:85%; object-fit:contain; border-radius:2px; box-shadow: -4px 6px 18px rgba(0,0,0,0.22), 0 0 0 1px rgba(0,0,0,0.1); transition:transform var(--transition-fast);"
                         loading="lazy">

                    <!-- Access Tier Badge on Top Left -->
                    <div style="position:absolute; top:var(--space-sm); left:var(--space-sm);">
                        <?php if ($currentRole === 'paid_member' || $currentRole === 'admin'): ?>
                            <span class="badge badge-success" style="box-shadow:var(--shadow-sm);">
                                ✓ <?= $isBn ? 'পড়ার অনুমতি আছে' : 'Reading Permitted' ?>
                            </span>
                        <?php else: ?>
                            <span class="badge" style="background:#5C4334; color:#FFFFFF; box-shadow:var(--shadow-sm);">
                                🔒 <?= $isBn ? 'সদস্য সংরক্ষিত' : 'Paid Members Only' ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- File Format Badge on Top Right -->
                    <div style="position:absolute; top:var(--space-sm); right:var(--space-sm);">
                        <span class="badge badge-scholarly" style="background:rgba(255,255,255,0.92); color:var(--text-main);">
                            PDF • <?= e($book['file_size']) ?>
                        </span>
                    </div>
                </div>

                <!-- Book Body Content -->
                <div style="padding:var(--space-xl); display:flex; flex-direction:column; flex-grow:1;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-2xs); font-size:0.78rem; text-transform:uppercase; letter-spacing:0.06em; color:var(--accent-gold); font-weight:600;">
                        <span><?= e($isBn ? $book['category_bn'] : $book['category_en']) ?></span>
                        <span><?= e($book['publication_year']) ?></span>
                    </div>

                    <h3 style="font-size:1.25rem; line-height:1.35; margin-bottom:var(--space-xs); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;">
                        <a href="<?= e(url('/library/book/' . $slug, $currentLocale)) ?>" style="color:var(--text-main);">
                            <?= e($isBn ? $book['title_bn'] : $book['title_en']) ?>
                        </a>
                    </h3>

                    <div style="font-size:0.86rem; color:var(--accent-brown); margin-bottom:var(--space-sm); font-weight:500;">
                        <?= e($isBn ? $book['author_bn'] : $book['author_en']) ?>
                    </div>

                    <p style="font-size:0.9rem; color:var(--text-body); line-height:1.6; margin-bottom:var(--space-lg); flex-grow:1; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;">
                        <?= e($isBn ? $book['synopsis_bn'] : $book['synopsis_en']) ?>
                    </p>

                    <!-- Technical Specs Metadata -->
                    <div style="display:flex; gap:var(--space-md); padding-top:var(--space-sm); border-top:1px solid var(--border-subtle); margin-bottom:var(--space-lg); font-size:0.8rem; color:var(--text-muted);">
                        <span><strong><?= $isBn ? 'পৃষ্ঠা সংখ্যা:' : 'Pages:' ?></strong> <?= e($book['pages_count']) ?></span>
                        <span>•</span>
                        <span><strong><?= $isBn ? 'ফরম্যাট:' : 'Format:' ?></strong> E-Book (PDF)</span>
                        <span>•</span>
                        <span><strong><?= $isBn ? 'ডাউনলোড:' : 'Download:' ?></strong> <?= $isBn ? 'সংরক্ষিত (নিষিদ্ধ)' : 'Restricted' ?></span>
                    </div>

                    <!-- Actions -->
                    <div style="display:flex; gap:var(--space-sm); align-items:center;">
                        <?php if ($currentRole === 'paid_member' || $currentRole === 'admin'): ?>
                            <a href="<?= e(url('/library/reader/' . $slug, $currentLocale)) ?>" class="btn btn-primary" style="flex:1; justify-content:center;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="margin-right:6px;">
                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                </svg>
                                <span><?= $isBn ? 'অনলাইনে পড়ুন' : 'Read E-Book' ?></span>
                            </a>
                        <?php else: ?>
                            <a href="<?= e(url('/library/book/' . $slug, $currentLocale)) ?>#request-access" class="btn btn-secondary" style="flex:1; justify-content:center; border-color:var(--accent-saffron); color:var(--accent-saffron);">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="margin-right:6px;">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                                <span><?= $isBn ? 'পাঠাধিকার চান' : 'Request Access' ?></span>
                            </a>
                        <?php endif; ?>

                        <a href="<?= e(url('/library/book/' . $slug, $currentLocale)) ?>" class="btn btn-ghost" title="<?= $isBn ? 'গ্রন্থের বিস্তারিত দেখুন' : 'View book details' ?>">
                            <?= $isBn ? 'বিবরণ' : 'Details' ?> →
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Institutional Preservation Statement -->
    <div style="margin-top:var(--space-4xl); padding:var(--space-2xl); background:var(--bg-subtle); border-radius:var(--radius-md); border:1px solid var(--border-medium); display:grid; grid-template-columns:1fr auto; gap:var(--space-xl); align-items:center;">
        <div>
            <h3 style="font-size:1.3rem; margin-bottom:var(--space-xs); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;">
                <?= $isBn ? 'গ্রন্থাগার কপিরাইট ও ডিজিটাল নিরাপত্তা নীতি' : 'Library Access Policy & Protection Model' ?>
            </h3>
            <p style="font-size:0.92rem; color:var(--text-muted); line-height:1.65; margin-bottom:0; max-width:820px;">
                <?= $isBn 
                    ? 'এসপিএস-এর ডিজিটাল লাইব্রেরির সকল প্রকাশনা প্রতিষ্ঠানের নৈতিক ও বুদ্ধিবৃত্তিক সম্পদ। কোনো গ্রন্থ ডাউনলোড বা বাণিজ্যিক ব্যবহারের সুযোগ নেই। কেবলমাত্র নিয়মিত সাধারণ সদস্য এবং গবেষণা প্রয়োজনে অনুমোদিত পাঠকগণ সুরক্ষিত ক্যানভাস রিডারের মাধ্যমে অধ্যয়ন করতে পারেন।' 
                    : 'All volumes hosted within the SPS Digital Archive are ethical and intellectual assets. Direct downloads are strictly restricted. Online reading is enabled through a protected canvas viewer for registered members and accredited scholars.' ?>
            </p>
        </div>
        <div>
            <a href="<?= e(url('/admin/library', $currentLocale)) ?>" class="btn btn-secondary btn-sm" style="white-space:nowrap;">
                🛡️ <?= $isBn ? 'প্রশাসনিক প্যানেল দেখুন' : 'Inspect Admin Controls' ?>
            </a>
        </div>
    </div>
</div>
