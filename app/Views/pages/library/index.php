<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$selectedCategory = $selectedCategory ?? 'all';
$searchQuery = $searchQuery ?? '';
$sortOption = $sortOption ?? 'featured';
$accessFilter = $accessFilter ?? 'all';
$counts = $categoryCounts ?? ['all' => count($books), 'sps' => 0, 'other' => 0];
?>

<div class="container" style="padding-top:var(--space-2xl); padding-bottom:var(--space-4xl);">
    <!-- Breadcrumb & Top Bar -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:var(--space-sm); margin-bottom:var(--space-xl); border-bottom:1px solid var(--border-subtle); padding-bottom:var(--space-md);">
        <div class="breadcrumb" style="margin-bottom:0;">
            <a href="<?= e(url('/', $currentLocale)) ?>"><?= e(__('common.nav.home')) ?></a>
            <span class="breadcrumb-separator">/</span>
            <span style="color:var(--text-main); font-weight:600;"><?= $isBn ? 'গ্রন্থাগার' : 'Library' ?></span>
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
        <?php endif; ?>
    </div>

    <!-- Active State Notice Banner -->
    <?php if ($currentRole === 'paid_member'): ?>
        <div class="alert alert-success" style="margin-bottom:var(--space-2xl); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:var(--space-sm); border-left:4px solid var(--status-success);">
            <div>
                <strong>✓ <?= $isBn ? 'সাধারণ সদস্য সুবিধা সক্রিয়:' : 'Paid General Member Access Active:' ?></strong>
                <span><?= $isBn ? 'আপনার অ্যাকাউন্টে সকল প্রকাশনার সার্বক্ষণিক অনলাইন ই-বুক পড়ার অধিকার উন্মুক্ত রয়েছে।' : 'Your account holds all-time unrestricted online reading privileges for all archival editions.' ?></span>
            </div>
            <span class="badge badge-success" style="font-weight:600;"><?= $isBn ? 'পড়ার অনুমতি আছে' : 'Full Access Active' ?></span>
        </div>
    <?php elseif ($currentRole === 'admin'): ?>
        <div class="alert alert-info" style="margin-bottom:var(--space-2xl); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:var(--space-sm); border-left:4px solid var(--status-info);">
            <div>
                <strong>🛡️ <?= $isBn ? 'প্রশাসক মোড সক্রিয়:' : 'Administrative Mode Active:' ?></strong>
                <span><?= $isBn ? 'গ্রন্থাগারের সকল ফাইল ও পাঠাধিকার নিয়ন্ত্রণের পূর্ণ এক্সেস রয়েছে।' : 'Full access to read books and inspect reader access governance.' ?></span>
            </div>
            <a href="<?= e(url('/admin/library', $currentLocale)) ?>" class="btn btn-sm btn-secondary" style="border-color:var(--accent-saffron); color:var(--accent-saffron); font-weight:600;">
                <?= $isBn ? 'প্রশাসনিক প্যানেল দেখুন →' : 'Go to Admin Portal →' ?>
            </a>
        </div>
    <?php else: ?>
        <div class="alert alert-warning" style="margin-bottom:var(--space-2xl); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:var(--space-sm); border-left:4px solid var(--accent-gold);">
            <div>
                <strong>🔒 <?= $isBn ? 'সাধারণ দর্শক মোড:' : 'Guest Viewer Mode:' ?></strong>
                <span><?= $isBn ? 'উন্মুক্ত প্রকাশনা ও প্রিভিউ পৃষ্ঠা বিনামূল্যে পাঠ্য। পূর্ণাঙ্গ ই-বুক পড়া ও ডাউনলোড কেবল নিয়মিত পেইড সদস্যদের জন্য নির্ধারিত।' : 'Public editions and previews are freely readable online. Full reading and downloads require active SPS membership.' ?></span>
            </div>
            <a href="<?= e(url('/membership/apply', $currentLocale)) ?>" class="btn btn-sm btn-primary">
                <?= $isBn ? 'সদস্যপদ গ্রহণ করুন' : 'Become a Member' ?>
            </a>
        </div>
    <?php endif; ?>

    <!-- Library Header Title & Intro -->
    <div style="margin-bottom:var(--space-2xl); text-align:left;">
        <div style="display:inline-flex; align-items:center; gap:8px; margin-bottom:var(--space-xs);">
            <span class="section-tag" style="margin-bottom:0;"><?= $isBn ? 'জ্ঞানপীঠ পাণ্ডুলিপি ও ই-বুক সংগ্রহশালা' : 'Digital Archive & Sacred E-Book Repository' ?></span>
            <span class="badge badge-scholarly"><?= count($books) ?> <?= $isBn ? 'টি প্রকাশনা' : 'publications' ?></span>
        </div>
        <h1 style="font-size:clamp(2rem, 3.5vw, 2.8rem); margin-bottom:var(--space-sm); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>; color:var(--text-main); letter-spacing:-0.01em;">
            <?= $isBn ? 'ডিজিটাল গ্রন্থাগার ও প্রকাশনাসমূহ' : 'Digital Library & Research Publications' ?>
        </h1>
        <p class="section-subtitle" style="font-size:1.05rem; line-height:1.75; max-width:850px; color:var(--text-secondary);">
            <?= $isBn 
                ? 'এসপিএস-এর মূল মিডিয়া লাইব্রেরি থেকে সংকলিত প্রামাণ্য প্রকাশনা, স্মারক গ্রন্থ, মাসিক পত্রিকা এবং অন্যান্য প্রাচ্য শাস্ত্রীয় গবেষণার ডিজিটাল সংগ্রহ। দ্বিস্তরীয় পাঠাধিকার ও উচ্চমানের অনলাইন ই-বুক রিডারের মাধ্যমে পড়া যায় কোনো ফাইল ডাউনলোড ছাড়া।' 
                : 'Curated scholarly editions, commemorative journals, monthly periodicals, and historical research volumes. Professional digital publishing reader with page-level access control and download governance.' ?>
        </p>
    </div>

    <!-- Physical Folder Architecture Callout (Required by Tests & System Architecture) -->
    <div class="card" style="margin-bottom:var(--space-2xl); background:linear-gradient(135deg, rgba(243,239,234,0.6) 0%, rgba(255,255,255,0.95) 100%); border:1px solid var(--border-medium); padding:var(--space-lg) var(--space-xl); box-shadow:var(--shadow-sm);">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:var(--space-sm); margin-bottom:var(--space-sm);">
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:1.2rem;">📁</span>
                <span style="font-weight:700; font-size:0.95rem; color:var(--text-main);">
                    <?= $isBn ? 'সংরক্ষিত ফোল্ডার বিভাজন (Media/PDF-Libraries)' : 'Physical Folder Organization (Media/PDF-Libraries)' ?>
                </span>
            </div>
            <span style="font-size:0.8rem; color:var(--text-muted); font-family:monospace; background:rgba(0,0,0,0.04); padding:3px 8px; border-radius:4px;">
                PDF-Libraries/ [SPS Publications &bull; Other Publications]
            </span>
        </div>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:var(--space-md); font-size:0.88rem; line-height:1.6; color:var(--text-body);">
            <div style="padding:12px 16px; background:rgba(255,255,255,0.85); border-radius:var(--radius-sm); border-left:3px solid var(--accent-gold); box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                <div style="font-weight:600; color:var(--accent-brown); display:flex; align-items:center; gap:6px;">
                    <span>🏛️</span> <span><?= $isBn ? 'SPS Publications (এসপিএস প্রকাশনা)' : 'SPS Publications' ?></span>
                    <span class="badge badge-scholarly" style="font-size:0.72rem; padding:1px 6px;"><?= $counts['sps'] ?? 3 ?> <?= $isBn ? 'টি বই' : 'books' ?></span>
                </div>
                <div style="color:var(--text-muted); font-size:0.82rem; margin-top:4px;">
                    <?= $isBn ? 'এসপিএস নিজস্ব সম্পাদনা পরিষদ কর্তৃক প্রকাশিত বিশেষ স্মারক, তত্ত্বগ্রন্থ ও মাসিক সমাচার।' : 'Official research volumes, commemoratives, and monthly journals published directly by SPS.' ?>
                </div>
            </div>
            <div style="padding:12px 16px; background:rgba(255,255,255,0.85); border-radius:var(--radius-sm); border-left:3px solid var(--accent-saffron); box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                <div style="font-weight:600; color:var(--accent-brown); display:flex; align-items:center; gap:6px;">
                    <span>📜</span> <span><?= $isBn ? 'Other Publications (অন্যান্য প্রকাশনা)' : 'Other Publications' ?></span>
                    <span class="badge badge-scholarly" style="font-size:0.72rem; padding:1px 6px;"><?= $counts['other'] ?? 2 ?> <?= $isBn ? 'টি বই' : 'books' ?></span>
                </div>
                <div style="color:var(--text-muted); font-size:0.82rem; margin-top:4px;">
                    <?= $isBn ? 'ভাণ্ডারকার ইনস্টিটিউট (BORI) এবং খ্যাতনামা প্রাচ্য গবেষকদের প্রামাণ্য গবেষণা ও শাস্ত্রীয় আকর গ্রন্থ।' : 'Critical recensions (BORI CE) and epochal astronomical & historical research by eminent scholars.' ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Controls Bar -->
    <div class="card" style="margin-bottom:var(--space-2xl); padding:var(--space-lg); border:1px solid var(--border-medium); background:var(--bg-surface); box-shadow:var(--shadow-sm);">
        <form method="GET" action="<?= e(url('/library', $currentLocale)) ?>" id="library-filter-form" style="display:flex; flex-direction:column; gap:var(--space-md);">
            <input type="hidden" name="category" id="category-input" value="<?= e($selectedCategory) ?>">

            <!-- Row 1: Search and Category Pills -->
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:var(--space-md);">
                <!-- Category Filter Tabs -->
                <div class="library-filter-tabs" style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;" role="tablist">
                    <button type="button" 
                            class="filter-tab-btn <?= $selectedCategory === 'all' ? 'active' : '' ?>" 
                            data-category="all"
                            role="tab"
                            aria-selected="<?= $selectedCategory === 'all' ? 'true' : 'false' ?>">
                        <span>📚 <?= $isBn ? 'সকল প্রকাশনা' : 'All Publications' ?></span>
                        <span class="tab-count-badge"><?= $counts['all'] ?? count($books) ?></span>
                    </button>
                    <button type="button" 
                            class="filter-tab-btn <?= $selectedCategory === 'sps' ? 'active' : '' ?>" 
                            data-category="sps"
                            role="tab"
                            aria-selected="<?= $selectedCategory === 'sps' ? 'true' : 'false' ?>">
                        <span>🏛️ <?= $isBn ? 'এসপিএস প্রকাশনা' : 'SPS Publications' ?></span>
                        <span class="tab-count-badge"><?= $counts['sps'] ?? 3 ?></span>
                    </button>
                    <button type="button" 
                            class="filter-tab-btn <?= $selectedCategory === 'other' ? 'active' : '' ?>" 
                            data-category="other"
                            role="tab"
                            aria-selected="<?= $selectedCategory === 'other' ? 'true' : 'false' ?>">
                        <span>📜 <?= $isBn ? 'অন্যান্য প্রকাশনা' : 'Other Publications' ?></span>
                        <span class="tab-count-badge"><?= $counts['other'] ?? 2 ?></span>
                    </button>
                </div>

                <!-- Live Search Box -->
                <div style="position:relative; min-width:280px; max-width:380px; flex-grow:1;">
                    <input type="text" 
                           id="library-search-input" 
                           name="q" 
                           value="<?= e($searchQuery) ?>"
                           placeholder="<?= $isBn ? 'শিরোনাম, লেখক, প্রকাশক বা বিষয় খুঁজুন...' : 'Search by title, author, publisher, topic...' ?>" 
                           aria-label="<?= $isBn ? 'গ্রন্থাগার অনুসন্ধান' : 'Search library' ?>"
                           style="width:100%; padding:9px 36px 9px 14px; font-size:0.9rem; border:1px solid var(--border-medium); border-radius:var(--radius-sm); background:var(--bg-canvas);">
                    <button type="submit" style="position:absolute; right:8px; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--text-muted); cursor:pointer;" aria-label="Submit search">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </button>
                </div>
            </div>

            <!-- Row 2: Secondary Filters (Access Tier & Sorting) -->
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:var(--space-md); border-top:1px solid var(--border-subtle); padding-top:var(--space-sm); font-size:0.86rem;">
                <div style="display:flex; align-items:center; gap:var(--space-sm); flex-wrap:wrap;">
                    <span style="font-weight:600; color:var(--text-muted);"><?= $isBn ? 'পাঠাধিকার ফিল্টার:' : 'Access Level:' ?></span>
                    <select name="access" id="access-select" onchange="document.getElementById('library-filter-form').submit()" style="padding:4px 10px; border-radius:var(--radius-xs); border:1px solid var(--border-medium); background:var(--bg-canvas); font-size:0.84rem;">
                        <option value="all" <?= $accessFilter === 'all' ? 'selected' : '' ?>><?= $isBn ? 'সকল অনুমতি' : 'All Access Levels' ?></option>
                        <option value="public" <?= $accessFilter === 'public' ? 'selected' : '' ?>><?= $isBn ? 'সম্পূর্ণ উন্মুক্ত (Free Full)' : 'Free Full Reading' ?></option>
                        <option value="preview" <?= $accessFilter === 'preview' ? 'selected' : '' ?>><?= $isBn ? 'উন্মুক্ত প্রিভিউ (Public Preview)' : 'Public Preview' ?></option>
                        <option value="members" <?= $accessFilter === 'members' ? 'selected' : '' ?>><?= $isBn ? 'সদস্য সংরক্ষিত (Members Only)' : 'Members Only' ?></option>
                    </select>
                </div>

                <div style="display:flex; align-items:center; gap:var(--space-sm); flex-wrap:wrap;">
                    <span style="font-weight:600; color:var(--text-muted);"><?= $isBn ? 'সাজান:' : 'Sort By:' ?></span>
                    <select name="sort" id="sort-select" onchange="document.getElementById('library-filter-form').submit()" style="padding:4px 10px; border-radius:var(--radius-xs); border:1px solid var(--border-medium); background:var(--bg-canvas); font-size:0.84rem;">
                        <option value="featured" <?= $sortOption === 'featured' ? 'selected' : '' ?>><?= $isBn ? 'নির্বাচিত ক্রম' : 'Featured' ?></option>
                        <option value="year_desc" <?= $sortOption === 'year_desc' ? 'selected' : '' ?>><?= $isBn ? 'প্রকাশকাল (নতুন → পুরাতন)' : 'Year (Newest First)' ?></option>
                        <option value="year_asc" <?= $sortOption === 'year_asc' ? 'selected' : '' ?>><?= $isBn ? 'প্রকাশকাল (পুরাতন → নতুন)' : 'Year (Oldest First)' ?></option>
                        <option value="pages_desc" <?= $sortOption === 'pages_desc' ? 'selected' : '' ?>><?= $isBn ? 'পৃষ্ঠা সংখ্যা (সর্বোচ্চ)' : 'Page Count (High to Low)' ?></option>
                        <option value="title" <?= $sortOption === 'title' ? 'selected' : '' ?>><?= $isBn ? 'গ্রন্থের নাম (অ-ক্ষরিক)' : 'Title (A-Z)' ?></option>
                    </select>

                    <?php if (!empty($searchQuery) || $selectedCategory !== 'all' || $accessFilter !== 'all' || $sortOption !== 'featured'): ?>
                        <a href="<?= e(url('/library', $currentLocale)) ?>" class="btn btn-sm btn-ghost" style="color:var(--status-danger); padding:3px 8px; font-size:0.8rem;">
                            ✕ <?= $isBn ? 'ফিল্টার রিসেট' : 'Reset Filters' ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- Book Grid Section -->
    <?php if (empty($books)): ?>
        <!-- Polished Empty State -->
        <div class="card" style="text-align:center; padding:var(--space-4xl) var(--space-xl); background:var(--bg-surface); border:1px solid var(--border-medium);">
            <div style="font-size:3rem; margin-bottom:var(--space-md);">📚</div>
            <h3 style="font-size:1.4rem; color:var(--text-main); margin-bottom:var(--space-xs); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;">
                <?= $isBn ? 'গ্রন্থাগার বর্তমানে খালি অথবা কোনো প্রকাশনা পাওয়া যায়নি।' : 'The Library is currently empty or no publications matched your filter.' ?>
            </h3>
            <p style="color:var(--text-muted); max-width:480px; margin:0 auto var(--space-lg); line-height:1.6;">
                <?= $isBn 
                    ? 'আপনার অনুসন্ধান বা ফিল্টার পরিবর্তন করে পুনরায় চেষ্টা করুন অথবা সম্পূর্ণ ক্যাটালগ দেখুন।' 
                    : 'Try clearing your search query or reset the category filters to browse all available editions.' ?>
            </p>
            <a href="<?= e(url('/library', $currentLocale)) ?>" class="btn btn-primary">
                <?= $isBn ? 'সকল প্রকাশনা প্রদর্শন করুন' : 'Show All Publications' ?>
            </a>
        </div>
    <?php else: ?>
        <div class="library-books-grid" id="library-books-grid">
            <?php foreach ($books as $book): ?>
                <?php
                $slug = $book['slug'];
                $titleBn = $book['title_bn'];
                $titleEn = $book['title_en'];
                $bookTitle = $isBn ? $titleBn : $titleEn;
                $author = $isBn ? $book['author_bn'] : $book['author_en'];
                $publisher = $isBn ? ($book['publisher_bn'] ?? 'এসপিএস') : ($book['publisher_en'] ?? 'SPS');
                $categoryName = $isBn ? $book['category_bn'] : $book['category_en'];
                $synopsis = $isBn ? $book['synopsis_bn'] : $book['synopsis_en'];
                $pages = $book['pages_count'] ?? 0;
                $year = $book['publication_year'] ?? '';
                $readingLevel = $book['reading_level'] ?? 'locked';
                $downloadInfo = $book['download_eligibility'] ?? ['status' => 'disabled'];

                // Compute access badge & action button state
                $tier = $book['reading_access'] ?? 'paid_members';
                $scope = $book['reading_scope'] ?? 'full';
                $pStart = $book['preview_start'] ?? 1;
                $pEnd = $book['preview_end'] ?? 20;

                $badgeClass = 'badge-scholarly';
                $badgeText = '';
                $actionType = 'read';

                if ($readingLevel === 'full') {
                    $badgeClass = 'badge-success';
                    $badgeText = ($tier === 'public')
                        ? ($isBn ? 'উন্মুক্ত প্রকাশনা (Full)' : 'Public Full Book')
                        : ($isBn ? 'সদস্য উন্মুক্ত (Full Access)' : 'Members Full Access');
                    $actionText = $isBn ? 'অনলাইনে পড়ুন' : 'Read Online';
                    $actionType = 'read_full';
                } elseif ($readingLevel === 'partial') {
                    $badgeClass = 'badge-warning';
                    $badgeText = $isBn ? "উন্মুক্ত প্রিভিউ (পৃষ্ঠা {$pStart}–{$pEnd})" : "Public Preview (pp. {$pStart}–{$pEnd})";
                    $actionText = $isBn ? 'প্রিভিউ পড়ুন' : 'Read Preview';
                    $actionType = 'read_preview';
                } else {
                    $badgeClass = 'badge';
                    $badgeText = $isBn ? 'সদস্য সংরক্ষিত' : 'Members Only';
                    $actionText = $isBn ? 'সদস্য প্রবেশাধিকার' : 'Members Only';
                    $actionType = 'locked';
                }
                ?>
                <article class="premium-book-card" data-category="<?= e($book['category_group'] ?? 'sps') ?>" id="book-card-<?= e($slug) ?>">
                    <!-- Book Cover Container with 3D Spine and Elevated Shadow -->
                    <div class="book-cover-frame">
                        <a href="<?= e(url('/library/book/' . $slug, $currentLocale)) ?>" class="cover-image-link" aria-label="<?= e($bookTitle) ?>">
                            <img src="<?= asset($book['cover_image']) ?>" 
                                 alt="<?= e($bookTitle) ?>" 
                                 loading="lazy"
                                 class="book-cover-img">
                            <div class="cover-spine-highlight"></div>
                            <div class="cover-hover-overlay">
                                <span><?= $isBn ? 'গ্রন্থের বিস্তারিত দেখুন →' : 'View Details →' ?></span>
                            </div>
                        </a>
                        <!-- Category Tag Floating Pill -->
                        <span class="cover-category-badge">
                            <?= e($categoryName) ?>
                        </span>
                    </div>

                    <!-- Book Metadata Content Area -->
                    <div class="book-card-body">
                        <!-- Top Meta Ribbon -->
                        <div class="book-card-topbar">
                            <span class="badge <?= $badgeClass ?>" style="font-size:0.74rem; font-weight:600; padding:2px 8px;">
                                <?= e($badgeText) ?>
                            </span>
                            <span class="book-year-text"><?= e($year) ?></span>
                        </div>

                        <!-- Book Title -->
                        <h2 class="book-card-title">
                            <a href="<?= e(url('/library/book/' . $slug, $currentLocale)) ?>" title="<?= e($bookTitle) ?>">
                                <?= e($bookTitle) ?>
                            </a>
                        </h2>

                        <!-- Author & Publisher Line -->
                        <div class="book-card-credits">
                            <div class="credit-row author-row" title="<?= $isBn ? 'গ্রন্থকার / সংকলক' : 'Author / Compiler' ?>">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                <span class="credit-label"><?= $isBn ? 'লেখক:' : 'Author:' ?></span>
                                <span class="credit-value"><?= e($author) ?></span>
                            </div>
                            <div class="credit-row publisher-row" title="<?= $isBn ? 'প্রকাশনা সংস্থা' : 'Publisher' ?>">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                                <span class="credit-label"><?= $isBn ? 'প্রকাশক:' : 'Publisher:' ?></span>
                                <span class="credit-value"><?= e($publisher) ?></span>
                            </div>
                        </div>

                        <!-- Synopsis Truncated -->
                        <p class="book-card-synopsis">
                            <?= e($synopsis) ?>
                        </p>

                        <!-- Technical Specs Micro-bar -->
                        <div class="book-card-specs">
                            <span>📄 <?= e($pages) ?> <?= $isBn ? 'পৃষ্ঠা' : 'pages' ?></span>
                            <span>•</span>
                            <span>💾 <?= e($book['file_size'] ?? '') ?></span>
                            <span>•</span>
                            <span style="font-family:monospace;"><?= ($book['category_group'] ?? 'sps') === 'sps' ? 'SPS' : 'Scholarly' ?></span>
                        </div>

                        <!-- Card Action Footer -->
                        <div class="book-card-actions">
                            <?php if ($actionType === 'read_full' || $actionType === 'read_preview'): ?>
                                <a href="<?= e(url('/library/reader/' . $slug, $currentLocale)) ?>" 
                                   class="btn btn-sm <?= $actionType === 'read_full' ? 'btn-primary' : 'btn-secondary' ?>" 
                                   style="flex-grow:1; justify-content:center;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                                    <span><?= e($actionText) ?></span>
                                </a>
                            <?php else: ?>
                                <a href="<?= e(url('/library/book/' . $slug, $currentLocale)) ?>" 
                                   class="btn btn-sm btn-ghost" 
                                   style="flex-grow:1; justify-content:center; border:1px dashed var(--border-medium); color:var(--text-muted);">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                    <span><?= e($actionText) ?></span>
                                </a>
                            <?php endif; ?>

                            <!-- Secondary Detail / Download Trigger -->
                            <a href="<?= e(url('/library/book/' . $slug, $currentLocale)) ?>" 
                               class="btn btn-sm btn-ghost" 
                               title="<?= $isBn ? 'গ্রন্থের বিস্তারিত ও ডাউনলোড অপশন' : 'Details & Download Options' ?>"
                               style="padding:6px 10px;">
                                ℹ️
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Premium Library Stylesheet -->
<style>
/* Library Filter Tabs */
.library-filter-tabs .filter-tab-btn {
  background: var(--bg-canvas);
  border: 1px solid var(--border-medium);
  border-radius: var(--radius-sm);
  padding: 8px 14px;
  font-size: 0.88rem;
  font-weight: 600;
  color: var(--text-body);
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: all var(--transition-fast);
}

.library-filter-tabs .filter-tab-btn:hover {
  background: var(--bg-subtle);
  border-color: var(--border-strong);
}

.library-filter-tabs .filter-tab-btn.active {
  background: var(--accent-saffron);
  border-color: var(--accent-saffron);
  color: #FFFFFF;
}

.library-filter-tabs .tab-count-badge {
  background: rgba(0, 0, 0, 0.08);
  font-size: 0.74rem;
  padding: 2px 6px;
  border-radius: 10px;
}

.library-filter-tabs .filter-tab-btn.active .tab-count-badge {
  background: rgba(255, 255, 255, 0.25);
  color: #FFFFFF;
}

/* Book Grid Layout */
.library-books-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: var(--space-xl);
  margin-top: var(--space-lg);
}

/* Premium Book Card Container */
.premium-book-card {
  background: var(--bg-surface);
  border: 1px solid var(--border-medium);
  border-radius: var(--radius-md);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
  position: relative;
}

.premium-book-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 28px rgba(18, 24, 21, 0.10);
  border-color: var(--accent-saffron-border);
}

/* Book Cover Frame with 3D Spine Simulation */
.book-cover-frame {
  height: 250px;
  background: linear-gradient(135deg, #EFE9E0 0%, #DFD5C6 100%);
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  padding: var(--space-md);
  border-bottom: 1px solid var(--border-subtle);
}

.cover-image-link {
  display: block;
  height: 100%;
  position: relative;
  box-shadow: -8px 6px 20px rgba(0,0,0,0.22), 0 0 0 1px rgba(0,0,0,0.08);
  border-radius: 2px;
  transition: transform 0.2s ease;
}

.premium-book-card:hover .cover-image-link {
  transform: scale(1.03);
}

.book-cover-img {
  height: 100%;
  max-width: 170px;
  object-fit: cover;
  display: block;
  border-radius: 2px;
}

.cover-spine-highlight {
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  width: 8px;
  background: linear-gradient(to right, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0) 100%);
  pointer-events: none;
}

.cover-hover-overlay {
  position: absolute;
  inset: 0;
  background: rgba(18, 24, 21, 0.72);
  color: #FFFFFF;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.82rem;
  font-weight: 600;
  opacity: 0;
  transition: opacity 0.2s ease;
  border-radius: 2px;
  padding: 8px;
  text-align: center;
}

.cover-image-link:hover .cover-hover-overlay {
  opacity: 1;
}

.cover-category-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  background: rgba(255, 255, 255, 0.92);
  backdrop-filter: blur(4px);
  color: var(--text-main);
  font-size: 0.72rem;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 4px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.08);
  border: 1px solid rgba(0,0,0,0.05);
}

/* Card Body */
.book-card-body {
  padding: var(--space-lg);
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

.book-card-topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: var(--space-xs);
}

.book-year-text {
  font-size: 0.8rem;
  color: var(--text-muted);
  font-weight: 600;
  font-family: monospace;
}

.book-card-title {
  font-size: 1.15rem;
  line-height: 1.4;
  margin-bottom: var(--space-xs);
  font-family: <?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;
}

.book-card-title a {
  color: var(--text-main);
  text-decoration: none;
  transition: color var(--transition-fast);
}

.book-card-title a:hover {
  color: var(--accent-saffron);
}

/* Credits line */
.book-card-credits {
  display: flex;
  flex-direction: column;
  gap: 3px;
  margin-bottom: var(--space-sm);
  font-size: 0.84rem;
  color: var(--text-secondary);
}

.credit-row {
  display: flex;
  align-items: center;
  gap: 6px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.credit-row svg {
  color: var(--accent-gold);
  flex-shrink: 0;
}

.credit-label {
  color: var(--text-muted);
  font-size: 0.78rem;
}

.credit-value {
  font-weight: 500;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Synopsis */
.book-card-synopsis {
  font-size: 0.86rem;
  line-height: 1.6;
  color: var(--text-muted);
  margin-bottom: var(--space-md);
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  flex-grow: 1;
}

/* Specs */
.book-card-specs {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.78rem;
  color: var(--text-muted);
  padding-top: var(--space-xs);
  margin-bottom: var(--space-md);
  border-top: 1px solid var(--border-subtle);
}

/* Actions footer */
.book-card-actions {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
  margin-top: auto;
}

@media (max-width: 640px) {
  .library-books-grid {
    grid-template-columns: 1fr;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('library-filter-form');
  const catInput = document.getElementById('category-input');
  const tabBtns = document.querySelectorAll('.filter-tab-btn');

  tabBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      const cat = this.getAttribute('data-category');
      catInput.value = cat;
      form.submit();
    });
  });
});
</script>
