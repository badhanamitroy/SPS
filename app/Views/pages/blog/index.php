<?php
/**
 * SPS Blog & Thought Journal (Blogspot Clone with SPS Theme)
 */
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
?>

<div class="container blog-page-container sps-blog-wrapper" style="max-width: var(--container-max); padding-top: var(--space-xl); padding-bottom: var(--space-4xl);">
    <!-- Blog Masthead Banner (Card styled consistent with Activities & About) -->
    <header class="blog-masthead blog-masthead-hero" style="background: linear-gradient(135deg, #1b263b 0%, #0d1b2a 100%); color: #ffffff; border-radius: var(--radius-xl); padding: var(--space-2xl) var(--space-xl); margin-bottom: var(--space-2xl); position: relative; overflow: hidden; box-shadow: var(--shadow-lg); border-top: 4px solid var(--accent-gold); text-align: center;">
        <div style="position: absolute; right: -30px; bottom: -30px; font-size: 14rem; opacity: 0.03; user-select: none; pointer-events: none;">
            🪷
        </div>
        <div style="position: relative; z-index: 1;">
            <div class="blog-masthead-content" style="max-width: 950px; margin: 0 auto; text-align: center;">
                <div class="blog-brand-mark" style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 12px;">
                    <span class="blog-om-symbol" style="color: #e5a93c; font-size: 1.5rem; font-weight: 800;">ॐ</span>
                    <span class="blog-tagline-pill" style="background: rgba(229, 169, 60, 0.15); border: 1px solid rgba(229, 169, 60, 0.4); color: #f7d28b; padding: 4px 14px; border-radius: 20px; font-size: 0.82rem; font-weight: 600;">
                        <?= $isBn ? 'সনাতন বিদ্যার্থী সংসদ • উন্মুক্ত সাহিত্য ও জ্ঞানপীঠ' : 'SPS Thought Journal • Philosophy & Discourse' ?>
                    </span>
                </div>
                <h1 class="blog-main-title" style="font-size: 2.25rem; font-weight: 800; color: #ffffff; margin: 0 0 10px; line-height: 1.25;">
                    <?= $isBn ? 'এসপিএস ব্লগ ও চিন্তা-বিতর্ক' : 'The SPS Chronicle & Blog' ?>
                </h1>
                <p class="blog-main-subtitle" style="font-size: 1.05rem; line-height: 1.6; color: #cbd5e1; max-width: 820px; margin: 0 auto 20px;">
                    <?= $isBn 
                        ? 'বেদান্ত, উপনিষদ, ভগবদগীতা, সনাতন ইতিহাস ও সমাজচিন্তার প্রামাণিক নিবন্ধের উন্মুক্ত মিলনমেলা।' 
                        : 'Authentic scholarly inquiries, scriptural reflections, historical explorations, and dharmic perspectives.' ?>
                </p>

                <!-- Search and Active Filters Bar -->
                <div class="blog-filter-bar">
                    <form action="<?= url('/blog', $currentLocale) ?>" method="GET" class="blog-search-form">
                        <div class="blog-search-input-wrap">
                            <span class="search-icon">🔍</span>
                            <input type="text" name="q" value="<?= e($searchQuery ?? '') ?>" 
                                   placeholder="<?= $isBn ? 'ব্লগ অনুসন্ধান করুন (যেমন: বেদান্ত, গীতা, ইতিহাস)...' : 'Search blogs (e.g., Vedanta, Gita, History)...' ?>" 
                                   class="blog-search-input">
                            <?php if (!empty($currentCategory)): ?>
                                <input type="hidden" name="category" value="<?= e($currentCategory) ?>">
                            <?php endif; ?>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <?= $isBn ? 'খুঁজুন' : 'Search' ?>
                            </button>
                        </div>
                    </form>

                    <div class="blog-write-cta-top">
                        <a href="<?= url('/blog/write', $currentLocale) ?>" class="btn btn-sm btn-gold blog-write-btn">
                            <span>✍️</span>
                            <span><?= $isBn ? 'পেইড সদস্য ব্লগ লিখুন' : 'Write a Blog (Paid Member)' ?></span>
                        </a>
                    </div>
                </div>

                <!-- Active Filter Tags Display -->
                <?php if (!empty($currentCategory) || !empty($currentTag) || !empty($currentArchive) || !empty($searchQuery)): ?>
                    <div class="active-filter-indicator">
                        <span><?= $isBn ? 'ফিল্টার করা ফলাফল:' : 'Active Filter:' ?></span>
                        <?php if (!empty($currentCategory)): ?>
                            <span class="filter-pill">
                                📁 <?= e($categories[$currentCategory]['name_' . $currentLocale] ?? $currentCategory) ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($currentTag)): ?>
                            <span class="filter-pill">
                                🏷️ #<?= e($currentTag) ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($currentArchive)): ?>
                            <span class="filter-pill">
                                📅 <?= e($currentArchive) ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($searchQuery)): ?>
                            <span class="filter-pill">
                                🔍 "<?= e($searchQuery) ?>"
                            </span>
                        <?php endif; ?>
                        <a href="<?= url('/blog', $currentLocale) ?>" class="filter-reset-link">
                            ✕ <?= $isBn ? 'সব ফিল্টার মুছুন' : 'Clear Filter' ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main 2-Column Blogspot Layout (Matched to Standard Container Width) -->
    <div class="blog-container" style="width: 100%; max-width: 100%; margin: 0; padding: 0;">
        <div class="blog-layout-grid">
            
            <!-- Left Main Column: Chronological Post Feed (Protected with Anti-Copy DRM) -->
            <main class="blog-main-feed sps-protected-content" id="blogFeed">
                <?php if (empty($posts)): ?>
                    <div class="blog-empty-state">
                        <div class="empty-icon">📜</div>
                        <h3><?= $isBn ? 'কোনো ব্লগ পাওয়া যায়নি' : 'No Blog Posts Found' ?></h3>
                        <p><?= $isBn ? 'নির্বাচিত ফিল্টার বা অনুসন্ধান শব্দে কোনো অনুমোদিত নিবন্ধ পাওয়া যায়নি।' : 'No published articles match your current search or category filter.' ?></p>
                        <a href="<?= url('/blog', $currentLocale) ?>" class="btn btn-secondary">
                            <?= $isBn ? 'সব ব্লগ দেখুন' : 'View All Blogs' ?>
                        </a>
                    </div>
                <?php else: ?>
                    <?php 
                    $lastDateGroup = '';
                    foreach ($posts as $post): 
                        $pubDate = $post['published_at'] ?? $post['created_at'];
                        $dateFormatted = date('d F Y', strtotime($pubDate));
                        $dateBn = $isBn ? \App\Core\I18n::formatNumber(date('d', strtotime($pubDate))) . ' ' . 
                                  [
                                      '01'=>'জানুয়ারি','02'=>'ফেব্রুয়ারি','03'=>'মার্চ','04'=>'এপ্রিল','05'=>'মে','06'=>'জুন',
                                      '07'=>'জুলাই','08'=>'আগস্ট','09'=>'সেপ্টেম্বর','10'=>'অক্টোবর','11'=>'নভেম্বর','12'=>'ডিসেম্বর'
                                  ][date('m', strtotime($pubDate))] . ' ' . \App\Core\I18n::formatNumber(date('Y', strtotime($pubDate)))
                                  : $dateFormatted;
                        $postTitle = $isBn ? ($post['title_bn'] ?? $post['title_en']) : ($post['title_en'] ?? $post['title_bn']);
                        $postExcerpt = $isBn ? ($post['excerpt_bn'] ?? $post['excerpt_en']) : ($post['excerpt_en'] ?? $post['excerpt_bn']);
                        $postUrl = url('/blog/' . $post['slug'], $currentLocale);
                        $author = $post['author'] ?? [];
                        $authorName = $isBn ? ($author['name_bn'] ?? $author['name_en'] ?? 'লেখক') : ($author['name_en'] ?? $author['name_bn'] ?? 'Author');
                        $authorTier = $isBn ? ($author['tier_bn'] ?? 'পেইড সদস্য') : ($author['tier_en'] ?? 'Paid Member');
                        $fbShareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($postUrl);
                    ?>

                        <!-- Blogspot Style Date Header -->
                        <?php if ($lastDateGroup !== $dateFormatted): 
                            $lastDateGroup = $dateFormatted;
                        ?>
                            <div class="blogspot-date-header">
                                <span class="date-ribbon">📅 <?= e($dateBn) ?></span>
                            </div>
                        <?php endif; ?>

                        <!-- Blogspot Post Card Entry -->
                        <article class="blogspot-post-card" id="post-<?= e($post['id']) ?>">
                            <div class="post-header-area">
                                <div class="post-meta-badges">
                                    <span class="category-badge">
                                        <?= e($isBn ? ($post['category_bn'] ?? ucfirst($post['category'])) : ($post['category_en'] ?? ucfirst($post['category']))) ?>
                                    </span>
                                    <span class="read-time-badge">
                                        ⏱️ <?= $isBn ? '৫ মিনিট পাঠ' : '5 min read' ?>
                                    </span>
                                </div>

                                <h2 class="post-title">
                                    <a href="<?= $postUrl ?>" class="post-title-link">
                                        <?= e($postTitle) ?>
                                    </a>
                                </h2>

                                <!-- Author Byline -->
                                <div class="post-byline">
                                    <div class="author-avatar-wrap">
                                        <img src="<?= e($author['avatar'] ?? 'https://api.dicebear.com/7.x/bottts/svg?seed=author') ?>" 
                                             alt="<?= e($authorName) ?>" class="author-avatar">
                                    </div>
                                    <div class="author-info">
                                        <div class="author-name-line">
                                            <span class="author-name"><?= e($authorName) ?></span>
                                            <span class="author-tier-badge">★ <?= e($authorTier) ?></span>
                                        </div>
                                        <div class="post-date-line">
                                            <span><?= e($dateBn) ?></span>
                                            <span>•</span>
                                            <span title="<?= $isBn ? 'পাঠক সংখ্যা' : 'Total Views' ?>">👁️ <?= e($isBn ? \App\Core\I18n::formatNumber((string)$post['views_count']) : $post['views_count']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Featured Thumbnail Image -->
                            <?php if (!empty($post['featured_image'])): ?>
                                <div class="post-featured-image-wrap">
                                    <a href="<?= $postUrl ?>">
                                        <img src="<?= asset($post['featured_image']) ?>" alt="<?= e($postTitle) ?>" class="post-featured-image" loading="lazy">
                                    </a>
                                </div>
                            <?php endif; ?>

                            <!-- Post Excerpt Snippet -->
                            <div class="post-excerpt">
                                <p><?= e($postExcerpt) ?></p>
                            </div>

                            <!-- Labels / Tags -->
                            <?php if (!empty($post['tags'])): ?>
                                <div class="post-labels-cloud">
                                    <span class="labels-label"><?= $isBn ? 'লেবেল:' : 'Labels:' ?></span>
                                    <?php foreach ($post['tags'] as $tag): ?>
                                        <a href="<?= url('/blog', $currentLocale) ?>?tag=<?= urlencode((string)$tag) ?>" class="post-label-tag">
                                            #<?= e($tag) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Blogspot Bottom Action & Social Bar -->
                            <div class="post-footer-actions">
                                <a href="<?= $postUrl ?>" class="btn-read-more">
                                    <span><?= $isBn ? 'আরও পড়ুন' : 'Read More' ?></span>
                                    <span class="arrow">➔</span>
                                </a>

                                <div class="post-social-actions">
                                    <!-- Direct Facebook Share Button -->
                                    <a href="<?= $fbShareUrl ?>" target="_blank" rel="noopener noreferrer" 
                                       class="btn-social-fb" title="<?= $isBn ? 'ফেসবুকে শেয়ার করুন' : 'Share to Facebook' ?>">
                                        <svg class="fb-icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                        <span><?= $isBn ? 'শেয়ার' : 'Share' ?></span>
                                    </a>

                                    <!-- Like / Reaction Button -->
                                    <form action="<?= url('/blog/' . $post['slug'] . '/like', $currentLocale) ?>" method="POST" class="like-form" style="display:inline;">
                                        <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>
                                        <button type="submit" class="btn-like-pill" title="<?= $isBn ? 'এই ব্লগে লাইক দিন' : 'Like this post' ?>">
                                            <span class="heart-icon">❤️</span>
                                            <span class="like-count"><?= e($isBn ? \App\Core\I18n::formatNumber((string)$post['likes_count']) : $post['likes_count']) ?></span>
                                        </button>
                                    </form>

                                    <!-- Comments Count Badge Link -->
                                    <a href="<?= $postUrl ?>#comments" class="btn-comment-pill" title="<?= $isBn ? 'মন্তব্য দেখুন বা লিখুন' : 'Comments' ?>">
                                        <span>💬</span>
                                        <span><?= e($isBn ? \App\Core\I18n::formatNumber((string)count($post['comments'] ?? [])) : count($post['comments'] ?? [])) ?></span>
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </main>

            <!-- Right Sidebar: Classic Blogspot Gadgets & Widgets -->
            <aside class="blog-sidebar">
                
                <!-- Gadget 1: Write a Blog CTA (For Paid Members) -->
                <div class="sidebar-widget widget-paid-cta">
                    <div class="widget-paid-card">
                        <div class="widget-badge">★ <?= $isBn ? 'পেইড সদস্য ডেস্ক' : 'Paid Member Desk' ?></div>
                        <h4 class="widget-paid-title"><?= $isBn ? 'আপনি কি চিন্তা-নিবন্ধ লিখতে চান?' : 'Write for SPS Journal' ?></h4>
                        <p class="widget-paid-desc">
                            <?= $isBn 
                                ? 'এসপিএস-এর অনুমোদিত পেইড সদস্যগণ নিজেদের গবেষণামূলক পাণ্ডুলিপি সরাসরি জমা দিতে পারেন। সাহিত্য সম্পাদক যাচাইয়ের পর তা প্রকাশিত হবে।' 
                                : 'Verified SPS Paid Members can author articles and submit manuscripts for editorial peer review.' ?>
                        </p>
                        <a href="<?= url('/blog/write', $currentLocale) ?>" class="btn btn-gold btn-block">
                            <span>✍️</span>
                            <span><?= $isBn ? 'ব্লগ রচনা করুন' : 'Write a Post' ?></span>
                        </a>
                    </div>
                </div>

                <!-- Gadget 2: About the SPS Editorial Desk ("About Me" Gadget) -->
                <div class="sidebar-widget widget-author-profile">
                    <div class="widget-header">
                        <h3 class="widget-title">
                            <span>📜</span>
                            <span><?= $isBn ? 'সম্পাদকীয় ও সাহিত্য বিভাগ' : 'Editorial Desk' ?></span>
                        </h3>
                    </div>
                    <div class="widget-body text-center">
                        <img src="<?= asset('assets/images/brand/sps-logo.png') ?>" alt="SPS Desk" class="sidebar-profile-img brand-mark-dark">
                        <img src="<?= asset('assets/images/brand/sps-logo-white.png') ?>" alt="SPS Desk" class="sidebar-profile-img brand-mark-light">
                        <h4 class="sidebar-profile-name"><?= $isBn ? 'সনাতন বিদ্যার্থী সংসদ' : 'Sanatan Vidyarthi Sangsad' ?></h4>
                        <div class="sidebar-profile-role"><?= $isBn ? 'শাস্ত্র ও সাহিত্য পরিষদ' : 'Scripture & Literature Board' ?></div>
                        <p class="sidebar-profile-bio">
                            <?= $isBn 
                                ? 'সনাতন ধর্মের শাশ্বত জ্ঞান ও দর্শনের বিস্তার, তরুণ প্রজন্মের আধ্যাত্মিক জাগরণ এবং প্রামাণিক শাস্ত্রীয় গবেষণার প্রাতিষ্ঠানিক মাধ্যম।' 
                                : 'Dedicated to promulgating authentic Vedic wisdom, classical Upanishadic thought, and grassroots cultural renaissance.' ?>
                        </p>
                        <div class="sidebar-motto-tag">
                            <?= config('app.motto') ?>
                        </div>
                    </div>
                </div>

                <!-- Gadget 3: Popular Posts ("Popular Posts" Gadget) -->
                <div class="sidebar-widget widget-popular-posts">
                    <div class="widget-header">
                        <h3 class="widget-title">
                            <span>🔥</span>
                            <span><?= $isBn ? 'সর্বাধিক পঠিত ব্লগ' : 'Popular Posts' ?></span>
                        </h3>
                    </div>
                    <div class="widget-body">
                        <div class="popular-posts-list">
                            <?php 
                            $rank = 1;
                            foreach ($popularPosts as $pop): 
                                $popTitle = $isBn ? ($pop['title_bn'] ?? $pop['title_en']) : ($pop['title_en'] ?? $pop['title_bn']);
                                $popUrl = url('/blog/' . $pop['slug'], $currentLocale);
                            ?>
                                <div class="popular-post-item">
                                    <span class="popular-rank-badge"><?= $isBn ? \App\Core\I18n::formatNumber((string)$rank++) : $rank++ ?></span>
                                    <div class="popular-thumb-wrap">
                                        <a href="<?= $popUrl ?>">
                                            <img src="<?= asset($pop['featured_image'] ?? 'assets/images/brand/sps-logo.png') ?>" 
                                                 alt="<?= e($popTitle) ?>" class="popular-thumb">
                                        </a>
                                    </div>
                                    <div class="popular-info">
                                        <h5 class="popular-title">
                                            <a href="<?= $popUrl ?>"><?= e($popTitle) ?></a>
                                        </h5>
                                        <div class="popular-meta">
                                            <span>❤️ <?= e($isBn ? \App\Core\I18n::formatNumber((string)$pop['likes_count']) : $pop['likes_count']) ?></span>
                                            <span>•</span>
                                            <span>👁️ <?= e($isBn ? \App\Core\I18n::formatNumber((string)$pop['views_count']) : $pop['views_count']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Gadget 4: Blog Archive ("Blog Archive" Hierarchical Gadget) -->
                <div class="sidebar-widget widget-archive">
                    <div class="widget-header">
                        <h3 class="widget-title">
                            <span>📅</span>
                            <span><?= $isBn ? 'ব্লগ আর্কাইভ' : 'Blog Archive' ?></span>
                        </h3>
                    </div>
                    <div class="widget-body">
                        <div class="archive-tree">
                            <?php foreach ($archiveTree as $yData): ?>
                                <div class="archive-year-group">
                                    <div class="archive-year-title">
                                        <span class="toggle-icon">▾</span>
                                        <a href="<?= url('/blog', $currentLocale) ?>?archive=<?= e($yData['year']) ?>" class="year-link">
                                            <?= e($isBn ? \App\Core\I18n::formatNumber((string)$yData['year']) : $yData['year']) ?>
                                        </a>
                                        <span class="count-pill">(<?= e($isBn ? \App\Core\I18n::formatNumber((string)$yData['count']) : $yData['count']) ?>)</span>
                                    </div>
                                    <ul class="archive-month-list">
                                        <?php foreach ($yData['months'] as $mData): ?>
                                            <li class="archive-month-item <?= ($currentArchive === $mData['archive_key']) ? 'active' : '' ?>">
                                                <a href="<?= url('/blog', $currentLocale) ?>?archive=<?= e($mData['archive_key']) ?>">
                                                    <?= e($isBn ? $mData['name_bn'] : $mData['name_en']) ?>
                                                    <span class="month-count">(<?= e($isBn ? \App\Core\I18n::formatNumber((string)$mData['count']) : $mData['count']) ?>)</span>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Gadget 5: Labels / Categories ("Labels" Cloud Gadget) -->
                <div class="sidebar-widget widget-categories">
                    <div class="widget-header">
                        <h3 class="widget-title">
                            <span>🏷️</span>
                            <span><?= $isBn ? 'বিষয়সূচী ও লেবেল' : 'Labels & Categories' ?></span>
                        </h3>
                    </div>
                    <div class="widget-body">
                        <div class="labels-list">
                            <?php foreach ($categories as $catKey => $catData): ?>
                                <a href="<?= url('/blog', $currentLocale) ?>?category=<?= urlencode((string)$catKey) ?>" 
                                   class="label-badge <?= ($currentCategory === $catKey) ? 'active' : '' ?>">
                                    <span class="cat-icon"><?= $catData['icon'] ?></span>
                                    <span class="cat-name"><?= e($isBn ? $catData['name_bn'] : $catData['name_en']) ?></span>
                                    <span class="cat-count"><?= e($isBn ? \App\Core\I18n::formatNumber((string)$catData['count']) : $catData['count']) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>

                        <?php if (!empty($tags)): ?>
                            <div class="tags-cloud-divider">
                                <span><?= $isBn ? 'জনপ্রিয় ট্যাগস' : 'Popular Tags' ?></span>
                            </div>
                            <div class="tags-cloud">
                                <?php foreach (array_slice($tags, 0, 15) as $tagItem): ?>
                                    <a href="<?= url('/blog', $currentLocale) ?>?tag=<?= urlencode((string)$tagItem['name']) ?>" 
                                       class="tag-bubble <?= ($currentTag === $tagItem['name']) ? 'active' : '' ?>">
                                        #<?= e($tagItem['name']) ?> (<?= e($isBn ? \App\Core\I18n::formatNumber((string)$tagItem['count']) : $tagItem['count']) ?>)
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Gadget 6: Facebook Community Follow Gadget -->
                <div class="sidebar-widget widget-facebook-community">
                    <div class="widget-header">
                        <h3 class="widget-title">
                            <span style="color:#1877f2;">👍</span>
                            <span><?= $isBn ? 'ফেসবুক কমিউনিটি' : 'Facebook Community' ?></span>
                        </h3>
                    </div>
                    <div class="widget-body text-center">
                        <div class="fb-preview-box">
                            <div class="fb-page-avatar">ॐ</div>
                            <div class="fb-page-info">
                                <strong>Sanatan Vidyarthi Sangsad</strong>
                                <small>@sps.official.org • 50k+ Followers</small>
                            </div>
                        </div>
                        <p class="fb-callout-text">
                            <?= $isBn ? 'আমাদের অফিসিয়াল ফেসবুক পেজে যুক্ত হয়ে নিয়মিত শাস্ত্রীয় আপডেট, লাইভ আলোচনা ও সেবাকাজের খবর পান।' : 'Follow SPS on Facebook for real-time scriptural discussions and humanitarian project updates.' ?>
                        </p>
                        <a href="https://www.facebook.com" target="_blank" rel="noopener noreferrer" class="btn btn-facebook btn-block">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            <span><?= $isBn ? 'ফেসবুকে ফলো করুন' : 'Follow on Facebook' ?></span>
                        </a>
                    </div>
                </div>

            </aside>
        </div>
    </div>
</div>

<!-- SPS DRM Protection Toast -->
<div id="drmProtectionToast" class="sps-drm-toast" style="display:none;">
    <span>🛡️</span>
    <span id="drmToastMsg">⚠️ <?= $isBn ? 'সনাতন ফিলোসফি এন্ড স্ক্রিপচার (SPS): ব্লগের বিষয়বস্তু কপিরাইট সংরক্ষিত। অননুমোদিত অনুলিপি বা স্ক্রিনশট নেওয়া নিষেধ।' : 'Copyright Protected: Content is protected. Copying and screenshots prohibited.' ?></span>
</div>

<script>
function showDRMToast(msg) {
    const toast = document.getElementById('drmProtectionToast');
    const msgEl = document.getElementById('drmToastMsg');
    if (toast) {
        if (msg && msgEl) msgEl.textContent = msg;
        toast.style.display = 'flex';
        toast.style.opacity = '1';
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => { toast.style.display = 'none'; }, 300);
        }, 2800);
    }
}

// DRM Anti-Copy & Anti-Screenshot Handlers
document.addEventListener('contextmenu', function(e) {
    if (e.target.closest('.blogspot-post-card, .blog-main-feed, .sps-protected-content')) {
        e.preventDefault();
        showDRMToast('<?= $isBn ? "⚠️ কপিরাইট সুরক্ষা: এসপিএস ব্লগের লেখা কপি করা সম্পূর্ণ নিষিদ্ধ।" : "⚠️ Copyright Protected: SPS blog content copying is strictly disabled." ?>');
    }
});

document.addEventListener('copy', function(e) {
    if (e.target.closest('.blogspot-post-card, .blog-main-feed, .sps-protected-content')) {
        e.preventDefault();
        showDRMToast('<?= $isBn ? "⚠️ অনুলিপি নিষিদ্ধ: লেখাটি কপিরাইট আইনের আওতায় সংরক্ষিত।" : "⚠️ Copying Disabled: Content is protected under copyright law." ?>');
    }
});

document.addEventListener('keydown', function(e) {
    // Block Ctrl+C, Ctrl+U, Ctrl+S, Ctrl+P on protected areas
    if ((e.ctrlKey || e.metaKey) && ['c', 'u', 's', 'p'].includes(e.key.toLowerCase())) {
        if (e.target.closest('.sps-protected-content, .blogspot-post-card') || ['u', 's', 'p'].includes(e.key.toLowerCase())) {
            e.preventDefault();
            showDRMToast('<?= $isBn ? "⚠️ কমান্ডটি নিষিদ্ধ: ব্লগের উপাদান সুরক্ষিত।" : "⚠️ Action Prohibited: Blog literature is copyright protected." ?>');
        }
    }
});

// PrintScreen key prevention
window.addEventListener('keyup', function(e) {
    if (e.key === 'PrintScreen' || e.keyCode === 44) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText('⚠️ SPS Protected Literature - Screenshot Prohibited.');
        }
        showDRMToast('<?= $isBn ? "🛡️ স্ক্রিনশট গ্রহণ নিষিদ্ধ: বৌদ্ধিক সম্পত্তি সুরক্ষা সক্রিয়।" : "🛡️ Screenshot Prohibited: Intellectual property protection active." ?>');
    }
});
</script>

<style>
.sps-drm-toast {
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    background: #1e293b;
    color: #f8fafc;
    border: 1px solid #ef4444;
    border-radius: 8px;
    padding: 12px 20px;
    font-size: 0.9rem;
    font-weight: 600;
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    z-index: 99999;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: opacity 0.3s ease;
}

/* SPS Blogspot Clone Styling */
.sps-blog-wrapper {
    background-color: var(--bg-canvas, #fcfbf7);
    min-height: 80vh;
    padding-bottom: var(--space-3xl, 3rem);
}

/* English Version Font Overrides */
html[lang="en"] .sps-blog-wrapper {
    font-family: Arial, "Helvetica Neue", sans-serif;
}
html[lang="en"] .sps-blog-wrapper h1,
html[lang="en"] .sps-blog-wrapper h2,
html[lang="en"] .sps-blog-wrapper h3,
html[lang="en"] .sps-blog-wrapper h4,
html[lang="en"] .sps-blog-wrapper .post-title {
    font-family: Poppins, "Segoe UI", sans-serif;
}
html[lang="en"] .sps-blog-wrapper blockquote,
html[lang="en"] .sps-blog-wrapper .post-excerpt {
    font-family: "Times New Roman", Times, serif;
}

/* Masthead */
.blog-masthead {
    background: linear-gradient(135deg, #2b1d0c 0%, #1a1208 100%);
    color: #ffffff;
    padding: var(--space-2xl, 2.5rem) 0 var(--space-xl, 2rem);
    border-bottom: 3px solid var(--accent-gold, #c5a059);
    position: relative;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}
.blog-masthead-content {
    max-width: 950px;
    margin: 0 auto;
    text-align: center;
}
.blog-brand-mark {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-bottom: 12px;
}
.blog-om-symbol {
    font-size: 1.5rem;
    color: #e5a93c;
    font-weight: 800;
}
.blog-tagline-pill {
    background: rgba(229, 169, 60, 0.15);
    border: 1px solid rgba(229, 169, 60, 0.4);
    color: #f7d28b;
    padding: 3px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.5px;
}
.blog-main-title {
    font-size: 2.2rem;
    font-weight: 800;
    color: #fff;
    margin: 0 0 10px;
    letter-spacing: -0.5px;
}
.blog-main-subtitle {
    font-size: 1rem;
    color: #d1c7b7;
    margin: 0 0 24px;
    max-width: 720px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
}

/* Filter and Search Bar */
.blog-filter-bar {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    flex-wrap: wrap;
    margin-top: 15px;
}
.blog-search-form {
    flex: 1;
    max-width: 550px;
}
.blog-search-input-wrap {
    display: flex;
    align-items: center;
    background: #ffffff;
    border-radius: 30px;
    padding: 4px 6px 4px 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
.search-icon {
    font-size: 1rem;
    margin-right: 8px;
    color: #666;
}
.blog-search-input {
    border: none;
    outline: none;
    flex: 1;
    font-size: 0.92rem;
    color: #333;
    padding: 6px 0;
}
.blog-write-btn {
    border-radius: 30px;
    padding: 8px 18px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 2px 10px rgba(197, 160, 89, 0.4);
}

.active-filter-indicator {
    margin-top: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    flex-wrap: wrap;
    font-size: 0.85rem;
    color: #e5e0d8;
}
.filter-pill {
    background: #3c2a13;
    border: 1px solid #7c5825;
    padding: 2px 10px;
    border-radius: 12px;
    color: #ffd899;
}
.filter-reset-link {
    color: #ff9d80;
    text-decoration: underline;
    margin-left: 6px;
    cursor: pointer;
}

/* 2-Column Blogspot Grid */
.blog-container {
    max-width: 1200px;
    margin: 30px auto;
    padding: 0 20px;
}
.blog-layout-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 350px;
    gap: 32px;
}

/* Blogspot Date Ribbon */
.blogspot-date-header {
    margin: 20px 0 12px;
    display: flex;
    align-items: center;
}
.date-ribbon {
    background: #eadecb;
    color: #553e21;
    font-weight: 700;
    font-size: 0.8rem;
    padding: 3px 12px;
    border-radius: 14px;
    border: 1px solid #d4c4ab;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

/* Blogspot Post Card */
.blogspot-post-card {
    background: #ffffff;
    border: 1px solid #e7dfd3;
    border-radius: 10px;
    padding: 26px 28px;
    margin-bottom: 28px;
    box-shadow: 0 2px 12px rgba(43, 29, 12, 0.05);
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}
.blogspot-post-card:hover {
    box-shadow: 0 8px 24px rgba(43, 29, 12, 0.1);
    border-color: #d1b88e;
}
.post-meta-badges {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}
.category-badge {
    background: #fff4e5;
    color: #b35309;
    border: 1px solid #fcd34d;
    font-size: 0.76rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 4px;
    text-transform: uppercase;
}
.read-time-badge {
    font-size: 0.78rem;
    color: #887a6b;
}
.post-title {
    font-size: 1.45rem;
    font-weight: 800;
    line-height: 1.35;
    margin: 4px 0 14px;
}
.post-title-link {
    color: #1f2937;
    text-decoration: none;
    transition: color 0.15s ease;
}
.post-title-link:hover {
    color: #b45309;
    text-decoration: underline;
}

/* Author Byline */
.post-byline {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 18px;
    padding-bottom: 14px;
    border-bottom: 1px solid #f0e9df;
}
.author-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #fdfaf4;
    border: 2px solid #e7ded0;
    object-fit: cover;
}
.author-name-line {
    display: flex;
    align-items: center;
    gap: 8px;
}
.author-name {
    font-weight: 700;
    font-size: 0.92rem;
    color: #2b1d0c;
}
.author-tier-badge {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 10px;
}
.post-date-line {
    font-size: 0.8rem;
    color: #786b5c;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Featured Image */
.post-featured-image-wrap {
    margin-bottom: 18px;
    border-radius: 8px;
    overflow: hidden;
    background: #f7f3ec;
    border: 1px solid #ebe2d3;
    max-height: 360px;
}
.post-featured-image {
    width: 100%;
    height: auto;
    max-height: 360px;
    object-fit: cover;
    display: block;
    transition: transform 0.3s ease;
}
.post-featured-image-wrap:hover .post-featured-image {
    transform: scale(1.02);
}

/* Excerpt */
.post-excerpt {
    font-size: 1rem;
    color: #4b4237;
    line-height: 1.7;
    margin-bottom: 16px;
}

/* Labels Cloud */
.post-labels-cloud {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 18px;
    font-size: 0.82rem;
}
.labels-label {
    color: #8c7e6e;
    font-weight: 600;
}
.post-label-tag {
    background: #f5eedf;
    color: #754f24;
    padding: 2px 8px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 500;
    border: 1px solid #e4dac7;
    transition: all 0.15s ease;
}
.post-label-tag:hover {
    background: #b45309;
    color: #fff;
    border-color: #b45309;
}

/* Post Footer & Social Bar */
.post-footer-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 14px;
    border-top: 1px dashed #e7ded0;
    flex-wrap: wrap;
    gap: 12px;
}
.btn-read-more {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 700;
    font-size: 0.92rem;
    color: #b45309;
    text-decoration: none;
    transition: transform 0.2s ease, color 0.2s ease;
}
.btn-read-more:hover {
    color: #92400e;
    transform: translateX(3px);
}
.btn-read-more .arrow {
    transition: transform 0.2s ease;
}
.btn-read-more:hover .arrow {
    transform: translateX(4px);
}

.post-social-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}
.btn-social-fb {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #1877f2;
    color: #ffffff;
    font-size: 0.8rem;
    font-weight: 700;
    padding: 5px 12px;
    border-radius: 4px;
    text-decoration: none;
    transition: background 0.15s ease;
}
.btn-social-fb:hover {
    background: #1464c8;
}
.btn-like-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #fff1f2;
    border: 1px solid #fecdd3;
    color: #e11d48;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.82rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s ease;
}
.btn-like-pill:hover {
    background: #ffe4e6;
    border-color: #fda4af;
    transform: scale(1.05);
}
.btn-comment-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    color: #4b5563;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.82rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.15s ease;
}
.btn-comment-pill:hover {
    background: #e5e7eb;
    color: #1f2937;
}

/* Sidebar Gadgets */
.blog-sidebar {
    display: flex;
    flex-direction: column;
    gap: 24px;
}
.sidebar-widget {
    background: #ffffff;
    border: 1px solid #e7ded0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(43, 29, 12, 0.04);
}
.widget-header {
    background: #f7f2ea;
    border-bottom: 1px solid #e7ded0;
    padding: 12px 18px;
}
.widget-title {
    font-size: 0.98rem;
    font-weight: 800;
    color: #2b1d0c;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.widget-body {
    padding: 18px;
}

/* Gadget: Paid CTA */
.widget-paid-card {
    background: linear-gradient(135deg, #fdf8ed 0%, #faecd2 100%);
    border: 1px solid #f0d5a3;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
}
.widget-badge {
    display: inline-block;
    background: #b45309;
    color: #fff;
    font-size: 0.72rem;
    font-weight: 800;
    padding: 2px 10px;
    border-radius: 12px;
    margin-bottom: 8px;
}
.widget-paid-title {
    font-size: 1.15rem;
    font-weight: 800;
    color: #451a03;
    margin: 0 0 8px;
}
.widget-paid-desc {
    font-size: 0.85rem;
    color: #78350f;
    line-height: 1.5;
    margin: 0 0 16px;
}
.btn-gold {
    background: #c5a059;
    color: #1a1208;
    border: 1px solid #b28a42;
    font-weight: 700;
}
.btn-gold:hover {
    background: #b89045;
}
.btn-block {
    display: flex;
    width: 100%;
    justify-content: center;
    align-items: center;
    gap: 6px;
    padding: 9px 16px;
    border-radius: 6px;
    text-decoration: none;
}

/* Gadget: Profile */
.sidebar-profile-img {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    margin: 0 auto 10px;
    display: block;
    border: 3px solid #e7ded0;
    padding: 3px;
    background: #fff;
}
.sidebar-profile-name {
    font-size: 1.05rem;
    font-weight: 800;
    color: #2b1d0c;
    margin: 0 0 2px;
}
.sidebar-profile-role {
    font-size: 0.8rem;
    color: #b45309;
    font-weight: 600;
    margin-bottom: 10px;
}
.sidebar-profile-bio {
    font-size: 0.84rem;
    color: #554a3e;
    line-height: 1.5;
    margin-bottom: 12px;
}
.sidebar-motto-tag {
    background: #f7f2ea;
    border: 1px dashed #d5c8b5;
    padding: 6px 10px;
    font-size: 0.78rem;
    font-weight: 700;
    color: #000;
    border-radius: 4px;
}

/* Gadget: Popular Posts */
.popular-posts-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.popular-post-item {
    display: flex;
    align-items: center;
    gap: 10px;
    position: relative;
    padding-bottom: 12px;
    border-bottom: 1px solid #f2ece3;
}
.popular-post-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
.popular-rank-badge {
    font-size: 0.8rem;
    font-weight: 800;
    color: #b45309;
    background: #fff4e5;
    border: 1px solid #fed7aa;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.popular-thumb-wrap {
    width: 56px;
    height: 56px;
    border-radius: 6px;
    overflow: hidden;
    flex-shrink: 0;
    border: 1px solid #e7ded0;
}
.popular-thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.popular-info {
    flex: 1;
}
.popular-title {
    font-size: 0.88rem;
    font-weight: 700;
    line-height: 1.35;
    margin: 0 0 4px;
}
.popular-title a {
    color: #2b1d0c;
    text-decoration: none;
}
.popular-title a:hover {
    color: #b45309;
    text-decoration: underline;
}
.popular-meta {
    font-size: 0.74rem;
    color: #8c7e6e;
    display: flex;
    gap: 6px;
}

/* Gadget: Archive */
.archive-year-group {
    margin-bottom: 10px;
}
.archive-year-title {
    font-weight: 700;
    font-size: 0.92rem;
    color: #2b1d0c;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 4px;
}
.archive-year-title .year-link {
    color: #2b1d0c;
    text-decoration: none;
}
.archive-year-title .count-pill {
    font-size: 0.76rem;
    color: #8c7e6e;
}
.archive-month-list {
    list-style: none;
    padding-left: 18px;
    margin: 4px 0 0;
}
.archive-month-item {
    padding: 3px 0;
    font-size: 0.85rem;
}
.archive-month-item a {
    color: #554a3e;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.archive-month-item a:hover,
.archive-month-item.active a {
    color: #b45309;
    font-weight: 700;
}

/* Gadget: Labels */
.labels-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.label-badge {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 10px;
    border-radius: 6px;
    background: #fbf8f3;
    border: 1px solid #ede4d7;
    text-decoration: none;
    color: #453b30;
    font-size: 0.85rem;
    transition: all 0.15s ease;
}
.label-badge:hover,
.label-badge.active {
    background: #fff4e5;
    border-color: #fcd34d;
    color: #b45309;
    font-weight: 700;
}
.cat-count {
    background: #ede4d7;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 10px;
}
.tags-cloud-divider {
    font-size: 0.76rem;
    font-weight: 800;
    text-transform: uppercase;
    color: #8c7e6e;
    margin: 16px 0 8px;
    letter-spacing: 0.5px;
    border-top: 1px solid #f0e8dc;
    padding-top: 12px;
}
.tags-cloud {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.tag-bubble {
    font-size: 0.78rem;
    background: #f7f2ea;
    border: 1px solid #e4dac7;
    color: #554a3e;
    padding: 3px 8px;
    border-radius: 12px;
    text-decoration: none;
}
.tag-bubble:hover,
.tag-bubble.active {
    background: #b45309;
    color: #fff;
    border-color: #b45309;
}

/* Gadget: Facebook Community */
.fb-preview-box {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #f0f2f5;
    border-radius: 8px;
    padding: 10px;
    margin-bottom: 12px;
    text-align: left;
}
.fb-page-avatar {
    width: 40px;
    height: 40px;
    background: #1877f2;
    color: #fff;
    font-size: 1.3rem;
    font-weight: 800;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.fb-page-info strong {
    display: block;
    font-size: 0.85rem;
    color: #050505;
}
.fb-page-info small {
    color: #65676b;
    font-size: 0.75rem;
}
.fb-callout-text {
    font-size: 0.82rem;
    color: #554a3e;
    margin: 0 0 14px;
    line-height: 1.5;
}
.btn-facebook {
    background: #1877f2;
    color: #fff;
    border: none;
    font-weight: 700;
}
.btn-facebook:hover {
    background: #1464c8;
}

/* Responsive */
@media (max-width: 900px) {
    .blog-layout-grid {
        grid-template-columns: 1fr;
    }
    .blog-sidebar {
        margin-top: 20px;
    }
}
</style>
