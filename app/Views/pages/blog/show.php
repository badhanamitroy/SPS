<?php
/**
 * Single Blog Reader Page (Blogspot Clone with SPS Theme, Facebook Share, Likes & FB-style Comments)
 */
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';

$postTitle = $isBn ? ($post['title_bn'] ?? $post['title_en']) : ($post['title_en'] ?? $post['title_bn']);
$postContent = \App\Core\HtmlSanitizer::clean($isBn ? ($post['content_bn'] ?? $post['content_en']) : ($post['content_en'] ?? $post['content_bn']));
$postExcerpt = $isBn ? ($post['excerpt_bn'] ?? $post['excerpt_en']) : ($post['excerpt_en'] ?? $post['excerpt_bn']);
$pubDate = $post['published_at'] ?? $post['created_at'];
$dateFormatted = date('d F Y', strtotime($pubDate));
$dateBn = $isBn ? \App\Core\I18n::formatNumber(date('d', strtotime($pubDate))) . ' ' . 
          [
              '01'=>'জানুয়ারি','02'=>'ফেব্রুয়ারি','03'=>'মার্চ','04'=>'এপ্রিল','05'=>'মে','06'=>'জুন',
              '07'=>'জুলাই','08'=>'আগস্ট','09'=>'সেপ্টেম্বর','10'=>'অক্টোবর','11'=>'নভেম্বর','12'=>'ডিসেম্বর'
          ][date('m', strtotime($pubDate))] . ' ' . \App\Core\I18n::formatNumber(date('Y', strtotime($pubDate)))
          : $dateFormatted;
$author = $post['author'] ?? [];
$authorName = $isBn ? ($author['name_bn'] ?? $author['name_en'] ?? 'লেখক') : ($author['name_en'] ?? $author['name_bn'] ?? 'Author');
$authorTier = $isBn ? ($author['tier_bn'] ?? 'পেইড সদস্য') : ($author['tier_en'] ?? 'Paid Member');
$postUrl = url('/blog/' . $post['slug'], $currentLocale);
$fbShareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($postUrl);
$comments = $post['comments'] ?? [];
$likesCount = (int)($post['likes_count'] ?? 0);
$viewsCount = (int)($post['views_count'] ?? 0);
?>

<div class="container blog-page-container sps-blog-wrapper" style="max-width: var(--container-max); padding-top: var(--space-xl); padding-bottom: var(--space-4xl);">
    <!-- Breadcrumb Bar (Contained nicely inside page container) -->
    <div class="blog-breadcrumb-box" style="margin-bottom: var(--space-lg); background: #ffffff; border: 1px solid var(--border-medium); border-radius: var(--radius-md); padding: 12px 18px; box-shadow: var(--shadow-sm);">
        <nav class="breadcrumb-nav" aria-label="breadcrumb" style="display:flex; align-items:center; gap:8px; font-size:0.88rem; flex-wrap:wrap;">
            <a href="<?= url('/', $currentLocale) ?>" style="color:var(--text-muted); text-decoration:none;"><?= $isBn ? 'হোম' : 'Home' ?></a>
            <span class="sep" style="color:var(--text-muted);">›</span>
            <a href="<?= url('/blog', $currentLocale) ?>" style="color:var(--text-muted); text-decoration:none;"><?= $isBn ? 'ব্লগ' : 'Blog' ?></a>
            <span class="sep" style="color:var(--text-muted);">›</span>
            <a href="<?= url('/blog', $currentLocale) ?>?category=<?= urlencode((string)$post['category']) ?>" style="color:var(--text-muted); text-decoration:none;">
                <?= e($isBn ? ($post['category_bn'] ?? ucfirst($post['category'])) : ($post['category_en'] ?? ucfirst($post['category']))) ?>
            </a>
            <span class="sep" style="color:var(--text-muted);">›</span>
            <span class="current" style="color:var(--primary-deep); font-weight:700;"><?= e(mb_substr($postTitle, 0, 40)) ?>...</span>
        </nav>
    </div>

    <!-- Main 2-Column Reader Layout (Contained Width) -->
    <div class="blog-container" style="width: 100%; max-width: 100%; margin: 0; padding: 0;">
        <div class="blog-layout-grid">
            
            <!-- Left Main Column: Full Post Canvas & Discussion -->
            <main class="blog-single-content sps-protected-content">
                
                <article class="single-article-card sps-protected-article">
                    <!-- Post Header -->
                    <header class="single-post-header">
                        <div class="post-header-badges">
                            <span class="date-ribbon">📅 <?= e($dateBn) ?></span>
                            <span class="category-badge">
                                <?= e($isBn ? ($post['category_bn'] ?? ucfirst($post['category'])) : ($post['category_en'] ?? ucfirst($post['category']))) ?>
                            </span>
                            <?php if (($post['status'] ?? '') === 'pending'): ?>
                                <span class="badge-pending">⏳ <?= $isBn ? 'পর্যালোচনার অধীনে (Pending)' : 'Under Review' ?></span>
                            <?php endif; ?>
                        </div>

                        <h1 class="single-post-title">
                            <?= e($postTitle) ?>
                        </h1>

                        <!-- Author Card -->
                        <div class="single-author-bar">
                            <img src="<?= e($author['avatar'] ?? 'https://api.dicebear.com/7.x/bottts/svg?seed=author') ?>" 
                                 alt="<?= e($authorName) ?>" class="single-author-avatar">
                            <div class="single-author-meta">
                                <div class="single-author-name-row">
                                    <span class="single-author-name"><?= e($authorName) ?></span>
                                    <span class="author-tier-badge">★ <?= e($authorTier) ?></span>
                                </div>
                                <div class="single-post-date-row">
                                    <span><?= $isBn ? 'প্রকাশিত:' : 'Published:' ?> <?= e($dateBn) ?></span>
                                    <span>•</span>
                                    <span>👁️ <?= e($isBn ? \App\Core\I18n::formatNumber((string)$viewsCount) : $viewsCount) ?> <?= $isBn ? 'বার পঠিত' : 'reads' ?></span>
                                    <?php if (!empty($post['approved_by'])): ?>
                                        <span>•</span>
                                        <span class="approved-by-tag">✓ <?= $isBn ? 'অনুমোদন:' : 'Approved by:' ?> <?= e($post['approved_by']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Social Action Sticky Ribbon -->
                        <div class="social-share-ribbon" id="reactions">
                            <div class="share-group-left">
                                <!-- Facebook Share Button -->
                                <a href="<?= $fbShareUrl ?>" target="_blank" rel="noopener noreferrer" 
                                   class="btn-share-facebook" id="btnShareFb" title="<?= $isBn ? 'ফেসবুকে শেয়ার করুন' : 'Share to Facebook' ?>">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                    <span><?= $isBn ? 'ফেসবুকে শেয়ার করুন' : 'Share to Facebook' ?></span>
                                </a>

                                <!-- Copy Link Button with Toast -->
                                <button type="button" class="btn-copy-link" id="btnCopyLink" title="<?= $isBn ? 'লিংক কপি করুন' : 'Copy Link' ?>" onclick="copyPostLink()">
                                    <span>🔗</span>
                                    <span><?= $isBn ? 'লিংক কপি' : 'Copy Link' ?></span>
                                </button>
                                <span id="copyToast" class="copy-toast" style="display:none;"><?= $isBn ? 'লিংক কপি হয়েছে!' : 'Link Copied!' ?></span>
                            </div>

                            <div class="share-group-right">
                                <!-- Interactive Like Button -->
                                <form action="<?= url('/blog/' . $post['slug'] . '/like', $currentLocale) ?>" method="POST" id="likeForm" style="margin:0;">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn-like-interactive <?= $hasLiked ? 'liked' : '' ?>" id="likeBtn">
                                        <span class="like-heart"><?= $hasLiked ? '❤️' : '🤍' ?></span>
                                        <span class="like-label"><?= $isBn ? 'লাইক' : 'Like' ?></span>
                                        <span class="like-counter" id="likeCounter"><?= e($isBn ? \App\Core\I18n::formatNumber((string)$likesCount) : $likesCount) ?></span>
                                    </button>
                                </form>

                                <!-- Comment Anchor -->
                                <a href="#comments" class="btn-jump-comment">
                                    <span>💬</span>
                                    <span><?= e($isBn ? \App\Core\I18n::formatNumber((string)count($comments)) : count($comments)) ?></span>
                                </a>
                            </div>
                        </div>
                    </header>

                    <!-- Featured Banner Image -->
                    <?php if (!empty($post['featured_image'])): ?>
                        <div class="single-featured-image-box">
                            <img src="<?= asset($post['featured_image']) ?>" alt="<?= e($postTitle) ?>" class="single-featured-image">
                            <div class="image-caption">
                                <span>📷</span>
                                <span><?= e($postTitle) ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Main Article Body Content (Protected by Multi-Layer DRM & Copyright Watermark) -->
                    <div class="single-post-body sps-protected-content" id="singlePostBody">
                        <!-- Dynamic Security Watermark Mesh -->
                        <div class="sps-watermark-mesh" aria-hidden="true">
                            <span>সনাতন ফিলোসফি এন্ড স্ক্রিপচার (SPS) • কপিরাইট সংরক্ষিত • অননুমোদিত অনুলিপি নিষিদ্ধ • COPYRIGHT SPS • DO NOT REPRODUCE • </span>
                        </div>
                        <div class="article-inner-content">
                            <?= $postContent ?>
                        </div>
                    </div>

                    <!-- Post Tags Footer -->
                    <?php if (!empty($post['tags'])): ?>
                        <div class="single-post-tags-footer">
                            <span class="tag-title"><?= $isBn ? 'বিষয়সূচী লেবেল:' : 'Labels & Tags:' ?></span>
                            <?php foreach ($post['tags'] as $tag): ?>
                                <a href="<?= url('/blog', $currentLocale) ?>?tag=<?= urlencode((string)$tag) ?>" class="post-label-tag">
                                    #<?= e($tag) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Bottom Social Reaction Bar -->
                    <div class="single-post-bottom-actions">
                        <div class="bottom-fb-share">
                            <span><?= $isBn ? 'লেখাটি ভালো লাগলে ফেসবুক বন্ধুদের সাথে শেয়ার করুন:' : 'Share this thought-provoking article with friends on Facebook:' ?></span>
                            <a href="<?= $fbShareUrl ?>" target="_blank" rel="noopener noreferrer" class="btn-share-facebook-lg">
                                <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                <span>Facebook-এ শেয়ার</span>
                            </a>
                        </div>
                    </div>
                </article>

                <!-- Facebook-Style Comments Section -->
                <section class="comments-section" id="comments">
                    <div class="comments-header">
                        <div class="comments-title-wrap">
                            <h3 class="comments-title">
                                <span>💬</span>
                                <span><?= $isBn ? 'পাঠক মতামত ও আলোচনা' : 'Comments & Discussion' ?></span>
                                <span class="comments-count-pill">(<?= e($isBn ? \App\Core\I18n::formatNumber((string)count($comments)) : count($comments)) ?>)</span>
                            </h3>
                            <p class="comments-subtitle">
                                <?= $isBn ? 'সকল সদস্য, সাধারণ পাঠক ও শুভানুধ্যায়ী ফেসবুকের মতো স্বাধীনভাবে গঠনমূলক মন্তব্য ও আলোচনা করতে পারেন।' : 'All members, visitors, and admins are welcome to share feedback and thoughtful discussions.' ?>
                            </p>
                        </div>
                    </div>

                    <!-- Comment Submission Form -->
                    <div class="comment-form-card" id="comment-box">
                        <div class="form-avatar-col">
                            <div class="form-user-avatar">👤</div>
                        </div>
                        <div class="form-input-col">
                            <form action="<?= url('/blog/' . $post['slug'] . '/comment', $currentLocale) ?>" method="POST" id="commentPostForm">
                                <?= csrf_field() ?>
                                <div style="display:none!important;" aria-hidden="true">
                                    <input type="text" name="_hp_website" value="" tabindex="-1" autocomplete="off">
                                </div>
                                
                                <div class="comment-author-fields">
                                    <div class="field-item">
                                        <input type="text" name="author_name" required 
                                               placeholder="<?= $isBn ? 'আপনার নাম (আবশ্যক)*' : 'Your Name (Required)*' ?>" 
                                               class="comment-input-name" id="commentAuthorName">
                                    </div>
                                    <div class="field-item">
                                        <input type="email" name="author_email" 
                                               placeholder="<?= $isBn ? 'ইমেইল (ঐচ্ছিক)' : 'Email (Optional)' ?>" 
                                               class="comment-input-email" id="commentAuthorEmail">
                                    </div>
                                    <div class="field-checkbox">
                                        <label class="paid-member-check">
                                            <input type="checkbox" name="is_paid_member" value="1">
                                            <span><?= $isBn ? 'আমি একজন এসপিএস সদস্য' : 'I am an SPS Member' ?></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="comment-textarea-wrap">
                                    <textarea name="content" required rows="3" 
                                              placeholder="<?= $isBn ? 'একটি গঠনমূলক মন্তব্য লিখুন... (ফেসবুক বা সোশ্যাল মিডিয়ার ন্যায় প্রাসঙ্গিক মতামত দিন)' : 'Write a constructive comment or thought...' ?>" 
                                              class="comment-textarea" id="commentContent"></textarea>
                                </div>

                                <div class="comment-form-footer">
                                    <span class="comment-guide-note">
                                        <?= $isBn ? '🔒 আপনার মন্তব্য তাৎক্ষণিকভাবে সবার জন্য দৃশ্যমান হবে।' : '🔒 Your comment will appear immediately in the discussion feed.' ?>
                                    </span>
                                    <button type="submit" class="btn btn-primary btn-submit-comment" id="btnSubmitComment">
                                        <span>মন্তব্য প্রকাশ করুন</span>
                                        <span>➔</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Existing Comments Stream (Facebook Speech Bubble Style) -->
                    <div class="comments-stream" id="commentsStream">
                        <?php if (empty($comments)): ?>
                            <div class="no-comments-box">
                                <span class="no-cmt-icon">🗨️</span>
                                <p><?= $isBn ? 'এখনও কোনো মন্তব্য করা হয়নি। আপনিই প্রথম আপনার মূল্যবান মতামত জানান!' : 'No comments yet. Be the first to share your reflections on this article!' ?></p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($comments as $cmt): 
                                $isRaw = !empty($cmt['raw']);
                                $cmtAuthor = (string)($cmt['author_name'] ?? 'পাঠক');
                                $cmtContent = (string)($cmt['content'] ?? '');
                                if (!$isRaw) {
                                    $cmtAuthor = htmlspecialchars_decode($cmtAuthor, ENT_QUOTES);
                                    $cmtContent = htmlspecialchars_decode($cmtContent, ENT_QUOTES);
                                }
                                $cmtAvatar = $cmt['author_avatar'] ?? ('https://api.dicebear.com/7.x/bottts/svg?seed=' . urlencode($cmtAuthor));
                                $cmtRole = $cmt['author_role'] ?? 'visitor';
                                $cmtDate = date('d M Y, h:i A', strtotime($cmt['created_at'] ?? 'now'));
                            ?>
                                <div class="facebook-comment-item" id="comment-<?= e($cmt['id'] ?? '') ?>">
                                    <div class="cmt-avatar-wrap">
                                        <img src="<?= e($cmtAvatar) ?>" alt="<?= e($cmtAuthor) ?>" class="cmt-avatar">
                                    </div>
                                    <div class="cmt-content-wrap">
                                        <div class="cmt-speech-bubble">
                                            <div class="cmt-author-line">
                                                <span class="cmt-author-name"><?= e($cmtAuthor) ?></span>
                                                <?php if ($cmtRole === 'paid_member'): ?>
                                                    <span class="cmt-badge badge-member">★ <?= $isBn ? 'পেইড সদস্য' : 'Paid Member' ?></span>
                                                <?php elseif ($cmtRole === 'literature_admin' || $cmtRole === 'content_editor'): ?>
                                                    <span class="cmt-badge badge-admin">★ <?= $isBn ? 'সাহিত্য সম্পাদক' : 'Literature Admin' ?></span>
                                                <?php elseif ($cmtRole === 'super_admin' || $cmtRole === 'admin'): ?>
                                                    <span class="cmt-badge badge-admin">★ <?= $isBn ? 'প্রশাসক' : 'Admin' ?></span>
                                                <?php else: ?>
                                                    <span class="cmt-badge badge-visitor"><?= $isBn ? 'পাঠক' : 'Visitor' ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="cmt-text">
                                                <?= nl2br(e($cmtContent)) ?>
                                            </div>
                                        </div>

                                        <div class="cmt-action-bar">
                                            <button type="button" class="btn-cmt-like" onclick="likeComment(this)">
                                                <?= $isBn ? 'লাইক' : 'Like' ?>
                                            </button>
                                            <span class="sep">•</span>
                                            <button type="button" class="btn-cmt-reply" onclick="replyToComment('<?= e(addslashes($cmtAuthor)) ?>')">
                                                <?= $isBn ? 'উত্তর দিন' : 'Reply' ?>
                                            </button>
                                            <span class="sep">•</span>
                                            <span class="cmt-time"><?= e($cmtDate) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

            </main>

            <!-- Right Sidebar: Classic Blogspot Widgets -->
            <aside class="blog-sidebar">
                
                <!-- Gadget 1: Write CTA -->
                <div class="sidebar-widget widget-paid-cta">
                    <div class="widget-paid-card">
                        <div class="widget-badge">★ <?= $isBn ? 'পেইড সদস্য ডেস্ক' : 'Paid Member Desk' ?></div>
                        <h4 class="widget-paid-title"><?= $isBn ? 'নতুন ব্লগ লিখতে চান?' : 'Write a Blog Post' ?></h4>
                        <p class="widget-paid-desc">
                            <?= $isBn 
                                ? 'এসপিএস পেইড সদস্যগণ নিজেদের গবেষণামূলক চিন্তা সরাসরি প্রকাশ করতে পারেন।' 
                                : 'Paid members can submit original philosophical or historical research.' ?>
                        </p>
                        <a href="<?= url('/blog/write', $currentLocale) ?>" class="btn btn-gold btn-block">
                            <span>✍️</span>
                            <span><?= $isBn ? 'ব্লগ রচনা করুন' : 'Write a Post' ?></span>
                        </a>
                    </div>
                </div>

                <!-- Gadget 2: About SPS Editorial Board -->
                <div class="sidebar-widget widget-author-profile">
                    <div class="widget-header">
                        <h3 class="widget-title">
                            <span>📜</span>
                            <span><?= $isBn ? 'সম্পাদকীয় বিভাগ' : 'Editorial Board' ?></span>
                        </h3>
                    </div>
                    <div class="widget-body text-center">
                        <img src="<?= asset('assets/images/brand/sps-logo.png') ?>" alt="SPS Desk" class="sidebar-profile-img brand-mark-dark">
                        <img src="<?= asset('assets/images/brand/sps-logo-white.png') ?>" alt="SPS Desk" class="sidebar-profile-img brand-mark-light">
                        <h4 class="sidebar-profile-name"><?= $isBn ? 'সনাতন বিদ্যার্থী সংসদ' : 'Sanatan Vidyarthi Sangsad' ?></h4>
                        <div class="sidebar-profile-role"><?= $isBn ? 'শাস্ত্র ও সাহিত্য পরিষদ' : 'Scripture & Literature Board' ?></div>
                        <p class="sidebar-profile-bio">
                            <?= $isBn 
                                ? 'সনাতন বিদ্যার্থী সংসদের উন্মুক্ত জ্ঞানপীঠ ও লেখক ফোরাম। শাস্ত্রীয় তত্ত্ব, ইতিহাস ও সমাজচিন্তার মেলবন্ধন।' 
                                : 'SPS open journal uniting scriptural inquiry, classical heritage, and dharmic reflection.' ?>
                        </p>
                        <div class="sidebar-motto-tag">
                            <?= config('app.motto') ?>
                        </div>
                    </div>
                </div>

                <!-- Gadget 3: Popular Posts -->
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

                <!-- Gadget 4: Blog Archive -->
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
                                            <li class="archive-month-item">
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

            </aside>
        </div>
    </div>
</div>

<!-- Blackout DRM Shield on PrintScreen / Capture Detection -->
<div id="spsDrmShield" class="sps-drm-screen-shield" style="display:none;">
    <div class="shield-modal">
        <div style="font-size: 3.5rem; margin-bottom: 12px;">🛡️</div>
        <h3 style="font-size: 1.45rem; font-weight: 800; color: #f87171; margin-bottom: 8px;">
            <?= $isBn ? 'স্ক্রিনশট ও অনুলিপি গ্রহণ নিষিদ্ধ' : 'Screenshot & Copying Prohibited' ?>
        </h3>
        <p style="font-size: 0.95rem; color: #cbd5e1; line-height: 1.6; margin-bottom: 0;">
            <?= $isBn 
                ? 'সনাতন ফিলোসফি এন্ড স্ক্রিপচার (SPS)-এর প্রকাশনা ও বৌদ্ধিক সম্পত্তি সংরক্ষিত। কোনো লেখক, সদস্য বা দর্শনার্থীর এই লেখা অনুলিপি বা স্ক্রিনশট নেওয়ার অনুমতি নেই।' 
                : 'SPS published treatises and manuscripts are strictly copyright-protected. Screen capture and duplication are strictly forbidden.' ?>
        </p>
    </div>
</div>

<!-- Snipping Tool Focus Loss Protection Overlay -->
<div id="spsSnippingShield" class="sps-snipping-shield" style="display:none;">
    <div class="snipping-badge">
        <span>🔒</span>
        <span><?= $isBn ? 'স্ক্রিনশট প্রতিরক্ষা সক্রিয় — পাঠ অব্যাহত রাখতে পেজে ক্লিক করুন' : 'Anti-Capture Shield Active — Click page to resume reading' ?></span>
    </div>
</div>

<!-- DRM Protection Toast -->
<div id="drmProtectionToast" class="sps-drm-toast" style="display:none;">
    <span>🛡️</span>
    <span id="drmToastMsg">⚠️ <?= $isBn ? 'সনাতন ফিলোসফি এন্ড স্ক্রিপচার (SPS): ব্লগের বিষয়বস্তু কপিরাইট সংরক্ষিত। অননুমোদিত অনুলিপি বা স্ক্রিনশট নেওয়া নিষেধ।' : 'Copyright Protected: Content is protected. Copying and screenshots prohibited.' ?></span>
</div>

<script>
// DRM Anti-Copy, Anti-Select & Anti-Screenshot System
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

function flashScreenshotShield() {
    const shield = document.getElementById('spsDrmShield');
    if (shield) {
        shield.style.display = 'flex';
        setTimeout(() => {
            shield.style.display = 'none';
        }, 2400);
    }
}

// 1. Right Click / Contextmenu Prevention on protected areas
document.addEventListener('contextmenu', function(e) {
    if (e.target.closest('.single-article-card, .single-post-body, .sps-protected-content')) {
        e.preventDefault();
        showDRMToast('<?= $isBn ? "⚠️ কপিরাইট সুরক্ষা: এই প্রবন্ধের বিষয়বস্তু কপিরাইট সংরক্ষিত। অননুমোদিত কপি বা স্ক্রিনশট নিষিদ্ধ।" : "⚠️ Copyright Protected: SPS literature copying & screenshots are strictly disabled." ?>');
    }
});

// 2. Clipboard & Copy / Cut / Drag Prevention
document.addEventListener('copy', function(e) {
    if (e.target.closest('.single-article-card, .single-post-body, .sps-protected-content')) {
        e.preventDefault();
        if (e.clipboardData) {
            e.clipboardData.setData('text/plain', '⚠️ SPS Protected Literature - Copying Disabled.');
        }
        showDRMToast('<?= $isBn ? "⚠️ অনুলিপি নিষিদ্ধ: লেখাটি কপিরাইট আইনের আওতায় সংরক্ষিত।" : "⚠️ Copying Prohibited: Manuscript is copyright-protected." ?>');
    }
});

document.addEventListener('cut', function(e) {
    if (e.target.closest('.single-article-card, .single-post-body, .sps-protected-content')) {
        e.preventDefault();
    }
});

document.addEventListener('selectstart', function(e) {
    if (e.target.closest('.single-article-card, .single-post-body, .sps-protected-content')) {
        e.preventDefault();
    }
});

document.addEventListener('dragstart', function(e) {
    if (e.target.closest('.single-article-card, .single-post-body, .sps-protected-content')) {
        e.preventDefault();
    }
});

// 3. Keyboard Shortcut Trapping (Ctrl+C, Ctrl+U, Ctrl+S, Ctrl+P, F12)
document.addEventListener('keydown', function(e) {
    const key = e.key.toLowerCase();
    if ((e.ctrlKey || e.metaKey) && ['c', 'u', 's', 'p', 'a'].includes(key)) {
        if (e.target.closest('.sps-protected-content, .single-article-card') || ['u', 's', 'p'].includes(key)) {
            e.preventDefault();
            showDRMToast('<?= $isBn ? "⚠️ শর্টকাট কমান্ডটি নিষিদ্ধ: ব্লগের উপাদান সম্পূর্ণ সংরক্ষিত।" : "⚠️ Shortcut Prohibited: Content is copyright-protected." ?>');
        }
    }
    if (e.key === 'F12' || ((e.ctrlKey || e.metaKey) && e.shiftKey && ['i', 'j', 'c'].includes(key))) {
        e.preventDefault();
    }
});

// 4. PrintScreen Key Trapping & Clipboard Overwrite
window.addEventListener('keyup', function(e) {
    if (e.key === 'PrintScreen' || e.keyCode === 44) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText('⚠️ SPS Protected Literature - Screenshot Prohibited.');
        }
        flashScreenshotShield();
        showDRMToast('<?= $isBn ? "🛡️ স্ক্রিনশট গ্রহণ নিষিদ্ধ: বৌদ্ধিক সম্পত্তি সুরক্ষা সক্রিয়।" : "🛡️ Screenshot Prohibited: Intellectual property protection active." ?>');
    }
});

// 5. Snipping Tool / Window Blur Protection
window.addEventListener('blur', function() {
    const bodyEl = document.getElementById('singlePostBody');
    const snipping = document.getElementById('spsSnippingShield');
    if (bodyEl) {
        bodyEl.classList.add('sps-snipping-blurred');
    }
    if (snipping) {
        snipping.style.display = 'flex';
    }
});

window.addEventListener('focus', function() {
    const bodyEl = document.getElementById('singlePostBody');
    const snipping = document.getElementById('spsSnippingShield');
    if (bodyEl) {
        bodyEl.classList.remove('sps-snipping-blurred');
    }
    if (snipping) {
        snipping.style.display = 'none';
    }
});

// Interactive Link Copy with Toast Notification
function copyPostLink() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        const toast = document.getElementById('copyToast');
        if (toast) {
            toast.style.display = 'inline-block';
            setTimeout(() => { toast.style.display = 'none'; }, 2500);
        }
    }).catch(err => {
        console.error('Failed to copy', err);
    });
}

// AJAX Like Toggle (Instant optimistic update)
document.addEventListener('DOMContentLoaded', function() {
    const likeForm = document.getElementById('likeForm');
    const likeBtn = document.getElementById('likeBtn');
    const likeCounter = document.getElementById('likeCounter');
    
    if (likeForm) {
        likeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const action = likeForm.getAttribute('action') + '?format=json';
            const formData = new FormData(likeForm);
            
            fetch(action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': '<?= csrf_token() ?>'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.success) {
                    if (likeCounter) {
                        likeCounter.textContent = data.likes_count;
                    }
                    if (data.liked) {
                        likeBtn.classList.add('liked');
                        likeBtn.querySelector('.like-heart').textContent = '❤️';
                    } else {
                        likeBtn.classList.remove('liked');
                        likeBtn.querySelector('.like-heart').textContent = '🤍';
                    }
                }
            })
            .catch(() => {
                // Fallback to normal form submit
                likeForm.submit();
            });
        });
    }

    const commentForm = document.getElementById('commentPostForm');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const action = commentForm.getAttribute('action') + '?format=json';
            const formData = new FormData(commentForm);

            fetch(action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': '<?= csrf_token() ?>'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.success) {
                    window.location.reload();
                } else {
                    commentForm.submit();
                }
            })
            .catch(() => {
                commentForm.submit();
            });
        });
    }
});

// Reply helper to set commenter mention
function replyToComment(authorName) {
    const textarea = document.getElementById('commentContent');
    if (textarea) {
        textarea.value = '@' + authorName + ' ' + textarea.value;
        textarea.focus();
    }
}

function likeComment(btn) {
    if (!btn.classList.contains('liked')) {
        btn.classList.add('liked');
        btn.style.color = '#1877f2';
        btn.style.fontWeight = 'bold';
        btn.textContent = '✓ লাইকড';
    } else {
        btn.classList.remove('liked');
        btn.style.color = '#65676b';
        btn.style.fontWeight = 'normal';
        btn.textContent = 'লাইক';
    }
}
</script>

<style>
/* Security & Anti-Screenshot DRM Overlays */
.sps-watermark-mesh {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
    opacity: 0.035;
    font-size: 1.05rem;
    font-weight: 800;
    line-height: 4.5;
    letter-spacing: 2px;
    color: #000000;
    overflow: hidden;
    word-break: break-all;
    user-select: none;
}
.single-post-body {
    position: relative;
}
.article-inner-content {
    position: relative;
    z-index: 2;
}
.sps-snipping-blurred {
    filter: blur(14px) !important;
    opacity: 0.15 !important;
    pointer-events: none !important;
    transition: filter 0.15s ease, opacity 0.15s ease;
}
.sps-drm-screen-shield {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(10, 15, 29, 0.96);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999999;
    padding: 24px;
    text-align: center;
    backdrop-filter: blur(12px);
}
.shield-modal {
    background: #1e293b;
    border: 2px solid #ef4444;
    border-radius: 12px;
    padding: 32px 28px;
    max-width: 480px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.5);
}
.sps-snipping-shield {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 999999;
    display: flex;
    align-items: center;
}
.snipping-badge {
    background: #0f172a;
    color: #f59e0b;
    border: 1px solid #f59e0b;
    padding: 10px 18px;
    border-radius: 30px;
    font-size: 0.85rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.25);
}
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

/* Single Article Specific Styling */
.blog-breadcrumb-bar {
    background: #f4eee2;
    border-bottom: 1px solid #e5dbcb;
    padding: 10px 0;
    font-size: 0.84rem;
}
.breadcrumb-nav {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    color: #786b5c;
}
.breadcrumb-nav a {
    color: #8c5b24;
    text-decoration: none;
}
.breadcrumb-nav a:hover {
    text-decoration: underline;
}
.breadcrumb-nav .sep {
    color: #b5a999;
}
.breadcrumb-nav .current {
    color: #4b4237;
    font-weight: 600;
}

.single-article-card {
    background: #ffffff;
    border: 1px solid #e7dfd3;
    border-radius: 12px;
    padding: 34px 38px;
    box-shadow: 0 4px 20px rgba(43, 29, 12, 0.05);
    margin-bottom: 35px;
}
.post-header-badges {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 14px;
    flex-wrap: wrap;
}
.badge-pending {
    background: #fef3c7;
    color: #92400e;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 4px;
    border: 1px solid #fde68a;
}
.single-post-title {
    font-size: 2.1rem;
    font-weight: 800;
    color: #1a1208;
    line-height: 1.35;
    margin: 6px 0 20px;
}

/* Single Author Bar */
.single-author-bar {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 18px;
    background: #fbf8f2;
    border-radius: 8px;
    border: 1px solid #ede3d4;
    margin-bottom: 22px;
}
.single-author-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    border: 2px solid #d4c4ab;
    object-fit: cover;
}
.single-author-name-row {
    display: flex;
    align-items: center;
    gap: 8px;
}
.single-author-name {
    font-size: 1rem;
    font-weight: 800;
    color: #2b1d0c;
}
.single-post-date-row {
    font-size: 0.82rem;
    color: #786b5c;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 3px;
}
.approved-by-tag {
    color: #047857;
    font-weight: 600;
    background: #ecfdf5;
    padding: 1px 6px;
    border-radius: 4px;
}

/* Social Share Ribbon */
.social-share-ribbon {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 18px;
    background: #ffffff;
    border: 1px solid #e7ded0;
    border-radius: 8px;
    margin-bottom: 28px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    flex-wrap: wrap;
    gap: 12px;
}
.share-group-left, .share-group-right {
    display: flex;
    align-items: center;
    gap: 10px;
}
.btn-share-facebook {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #1877f2;
    color: #ffffff;
    padding: 7px 16px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 0.88rem;
    text-decoration: none;
    box-shadow: 0 2px 6px rgba(24, 119, 242, 0.3);
    transition: background 0.15s ease, transform 0.15s ease;
}
.btn-share-facebook:hover {
    background: #1464c8;
    transform: translateY(-1px);
}
.btn-copy-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f3f4f6;
    border: 1px solid #d1d5db;
    color: #374151;
    padding: 7px 14px;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s ease;
}
.btn-copy-link:hover {
    background: #e5e7eb;
}
.copy-toast {
    background: #10b981;
    color: #fff;
    font-size: 0.78rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 12px;
}

.btn-like-interactive {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #fff;
    border: 1px solid #f43f5e;
    color: #e11d48;
    padding: 7px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
}
.btn-like-interactive:hover,
.btn-like-interactive.liked {
    background: #ffe4e6;
    border-color: #e11d48;
    box-shadow: 0 2px 8px rgba(225, 29, 72, 0.2);
}
.btn-jump-comment {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #334155;
    padding: 7px 14px;
    border-radius: 20px;
    font-size: 0.88rem;
    font-weight: 700;
    text-decoration: none;
}
.btn-jump-comment:hover {
    background: #e2e8f0;
}

/* Featured Image */
.single-featured-image-box {
    margin-bottom: 28px;
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid #e7ded0;
    background: #f9f6f0;
}
.single-featured-image {
    width: 100%;
    max-height: 480px;
    object-fit: cover;
    display: block;
}
.image-caption {
    background: #f4eee2;
    padding: 8px 16px;
    font-size: 0.82rem;
    color: #786b5c;
    display: flex;
    align-items: center;
    gap: 6px;
    font-style: italic;
}

/* Article Body Typography */
.single-post-body {
    font-size: 1.15rem;
    line-height: 1.85;
    color: #2b241c;
    margin-bottom: 30px;
}
.single-post-body p {
    margin-bottom: 20px;
}
.single-post-body p.lead {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1a1208;
    line-height: 1.75;
}
.single-post-body blockquote {
    background: #fbf6ec;
    border-left: 4px solid #c5a059;
    padding: 18px 24px;
    margin: 28px 0;
    border-radius: 0 8px 8px 0;
    font-style: italic;
    color: #553e21;
    font-size: 1.18rem;
    box-shadow: 0 2px 8px rgba(197, 160, 89, 0.08);
}
.single-post-body h2,
.single-post-body h3 {
    color: #2b1d0c;
    margin: 28px 0 14px;
    font-weight: 800;
}

/* Tags Footer */
.single-post-tags-footer {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    padding: 16px 0;
    border-top: 1px solid #f0e8dc;
    border-bottom: 1px solid #f0e8dc;
    margin-bottom: 24px;
}
.tag-title {
    font-weight: 700;
    color: #786b5c;
    font-size: 0.88rem;
}

/* Bottom Facebook Share Banner */
.single-post-bottom-actions {
    background: #f0f7ff;
    border: 1px solid #bae6fd;
    border-radius: 8px;
    padding: 18px 22px;
}
.bottom-fb-share {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    font-size: 0.95rem;
    font-weight: 600;
    color: #0369a1;
}
.btn-share-facebook-lg {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #1877f2;
    color: #ffffff;
    font-weight: 700;
    padding: 9px 20px;
    border-radius: 6px;
    text-decoration: none;
    box-shadow: 0 2px 8px rgba(24, 119, 242, 0.35);
    transition: background 0.15s ease;
}
.btn-share-facebook-lg:hover {
    background: #1464c8;
}

/* Facebook-Style Comments Section */
.comments-section {
    background: #ffffff;
    border: 1px solid #e7dfd3;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 4px 18px rgba(43, 29, 12, 0.04);
}
.comments-header {
    border-bottom: 1px solid #f0e8dc;
    padding-bottom: 16px;
    margin-bottom: 24px;
}
.comments-title {
    font-size: 1.35rem;
    font-weight: 800;
    color: #2b1d0c;
    margin: 0 0 4px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.comments-count-pill {
    background: #fff4e5;
    color: #b45309;
    font-size: 0.88rem;
    padding: 2px 8px;
    border-radius: 12px;
}
.comments-subtitle {
    font-size: 0.86rem;
    color: #786b5c;
    margin: 0;
}

/* Comment Form */
.comment-form-card {
    display: flex;
    gap: 14px;
    background: #fdfaf5;
    border: 1px solid #ede3d4;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 30px;
}
.form-user-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #e7dfd3;
    color: #554a3e;
    font-size: 1.4rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.form-input-col {
    flex: 1;
}
.comment-author-fields {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 12px;
}
.field-item {
    flex: 1;
    min-width: 200px;
}
.comment-input-name,
.comment-input-email {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #d4c8b7;
    border-radius: 6px;
    font-size: 0.88rem;
    outline: none;
    background: #fff;
}
.comment-input-name:focus,
.comment-input-email:focus,
.comment-textarea:focus {
    border-color: #b45309;
    box-shadow: 0 0 0 3px rgba(180, 83, 9, 0.1);
}
.field-checkbox {
    display: flex;
    align-items: center;
}
.paid-member-check {
    font-size: 0.82rem;
    font-weight: 600;
    color: #78350f;
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
}
.comment-textarea-wrap {
    margin-bottom: 12px;
}
.comment-textarea {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #d4c8b7;
    border-radius: 8px;
    font-size: 0.92rem;
    font-family: inherit;
    resize: vertical;
    outline: none;
    background: #fff;
    min-height: 80px;
}
.comment-form-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}
.comment-guide-note {
    font-size: 0.78rem;
    color: #786b5c;
}
.btn-submit-comment {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #b45309;
    color: #fff;
    border: none;
    padding: 8px 18px;
    border-radius: 6px;
    font-weight: 700;
    cursor: pointer;
    font-size: 0.88rem;
}
.btn-submit-comment:hover {
    background: #92400e;
}

/* Comments Stream - Facebook Style Speech Bubbles */
.comments-stream {
    display: flex;
    flex-direction: column;
    gap: 20px;
}
.no-comments-box {
    text-align: center;
    padding: 30px;
    color: #786b5c;
    background: #faf6ef;
    border-radius: 8px;
}
.no-cmt-icon {
    font-size: 2rem;
    display: block;
    margin-bottom: 8px;
}
.facebook-comment-item {
    display: flex;
    gap: 12px;
}
.cmt-avatar-wrap {
    flex-shrink: 0;
}
.cmt-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 1px solid #d4c8b7;
    object-fit: cover;
    background: #fff;
}
.cmt-content-wrap {
    flex: 1;
    max-width: 90%;
}
.cmt-speech-bubble {
    background: #f0f2f5;
    border-radius: 18px;
    padding: 10px 16px;
    display: inline-block;
}
.cmt-author-line {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 4px;
}
.cmt-author-name {
    font-weight: 700;
    font-size: 0.92rem;
    color: #050505;
}
.cmt-badge {
    font-size: 0.68rem;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 8px;
}
.badge-member {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
}
.badge-admin {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}
.badge-visitor {
    background: #e2e8f0;
    color: #475569;
}
.cmt-text {
    font-size: 0.9rem;
    color: #050505;
    line-height: 1.45;
}
.cmt-action-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.78rem;
    color: #65676b;
    margin-top: 4px;
    padding-left: 10px;
}
.btn-cmt-like, .btn-cmt-reply {
    background: none;
    border: none;
    color: #65676b;
    font-weight: 700;
    padding: 0;
    cursor: pointer;
    font-size: 0.78rem;
}
.btn-cmt-like:hover, .btn-cmt-reply:hover {
    text-decoration: underline;
    color: #1877f2;
}

@media (max-width: 768px) {
    .single-article-card {
        padding: 20px 16px;
    }
    .single-post-title {
        font-size: 1.5rem;
    }
    .social-share-ribbon {
        flex-direction: column;
        align-items: stretch;
    }
    .share-group-left, .share-group-right {
        justify-content: space-between;
    }
}
</style>
