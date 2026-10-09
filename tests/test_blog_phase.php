<?php

/**
 * SPS Blog & Thought Journal (Blogspot Clone + Moderation & Social) Verification Suite
 */

declare(strict_types=1);

namespace App\Tests;

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\App;
use App\Core\I18n;
use App\Core\Request;
use App\Core\Router;
use App\Core\Session;
use App\Core\View;
use App\Services\AuthService;
use App\Services\BlogService;
use App\Services\RbacService;

$passed = 0;
$failed = 0;

function assert_test(string $name, bool $condition, string $detail = ''): void {
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo "[PASS] {$name}\n";
    } else {
        $failed++;
        echo "[FAIL] {$name}" . ($detail ? " - {$detail}" : "") . "\n";
    }
}

echo "====================================================\n";
echo "SPS Blog & Blogspot Clone Verification Suite\n";
echo "====================================================\n\n";

$app = new App(dirname(__DIR__));
$langConfig = require dirname(__DIR__) . '/config/languages.php';
I18n::init($langConfig, 'bn', '/bn');
View::init(dirname(__DIR__) . '/app/Views');
$router = new Router();
require dirname(__DIR__) . '/routes/web.php';

// Clean test state
BlogService::clearCache();

// 1. Service Layer Tests
$published = BlogService::getPublishedBlogs();
assert_test("BlogService: Has at least 5 published blogs", count($published) >= 5, "Count: " . count($published));

$pending = BlogService::getPendingBlogs();
assert_test("BlogService: Has at least 2 pending blogs awaiting moderation", count($pending) >= 2, "Count: " . count($pending));

$rejected = BlogService::getRejectedBlogs();
assert_test("BlogService: Has at least 1 rejected blog", count($rejected) >= 1, "Count: " . count($rejected));

$popular = BlogService::getPopularPosts(4);
assert_test("BlogService: Popular posts returns 4 top ranked items", count($popular) === 4);

$archive = BlogService::getArchiveTree();
assert_test("BlogService: Archive tree returns hierarchical year-month groups", !empty($archive) && isset($archive[0]['year']));

$categories = BlogService::getCategories();
assert_test("BlogService: Categories include vedanta, gita, history, seva", isset($categories['vedanta'], $categories['gita'], $categories['history'], $categories['seva']));

$tags = BlogService::getTags();
assert_test("BlogService: Tags extracted with counts", count($tags) >= 5);

// 2. Public Feed & Blogspot Clone UI
$resFeed = $router->dispatch(new Request('GET', '/bn/blog'));
assert_test("Router: /bn/blog returns HTTP 200", $resFeed->getStatusCode() === 200);

$bodyFeed = $resFeed->getContent();
assert_test("Blogspot Feed: Contains masthead & brand mark", str_contains($bodyFeed, 'blog-masthead') && str_contains($bodyFeed, 'blog-om-symbol'));
assert_test("Blogspot Feed: Contains 2-column layout grid", str_contains($bodyFeed, 'blog-layout-grid') && str_contains($bodyFeed, 'blog-main-feed') && str_contains($bodyFeed, 'blog-sidebar'));
assert_test("Blogspot Feed: Contains Date ribbon/header", str_contains($bodyFeed, 'blogspot-date-header') && str_contains($bodyFeed, 'date-ribbon'));
assert_test("Blogspot Feed: Contains Paid Member author badge", str_contains($bodyFeed, 'author-tier-badge'));
assert_test("Blogspot Feed: Contains Facebook share button", str_contains($bodyFeed, 'btn-social-fb') && str_contains($bodyFeed, 'facebook.com/sharer/sharer.php'));
assert_test("Blogspot Feed: Contains Like button counter", str_contains($bodyFeed, 'btn-like-pill'));
assert_test("Blogspot Feed: Contains Comment counter badge", str_contains($bodyFeed, 'btn-comment-pill'));
assert_test("Blogspot Sidebar: Contains Write a Post CTA for Paid Members", str_contains($bodyFeed, 'widget-paid-cta') && str_contains($bodyFeed, '/blog/write'));
assert_test("Blogspot Sidebar: Contains Editorial Desk Gadget", str_contains($bodyFeed, 'widget-author-profile'));
assert_test("Blogspot Sidebar: Contains Popular Posts Gadget", str_contains($bodyFeed, 'widget-popular-posts'));
assert_test("Blogspot Sidebar: Contains Blog Archive Gadget", str_contains($bodyFeed, 'widget-archive') && str_contains($bodyFeed, 'archive-tree'));
assert_test("Blogspot Sidebar: Contains Labels Gadget", str_contains($bodyFeed, 'widget-categories') && str_contains($bodyFeed, 'label-badge'));
assert_test("Blogspot Sidebar: Contains Facebook Community Gadget", str_contains($bodyFeed, 'widget-facebook-community'));

// Filter Tests
$resCat = $router->dispatch(new Request('GET', '/bn/blog?category=vedanta'));
assert_test("Filter: Category filter returns HTTP 200", $resCat->getStatusCode() === 200);
assert_test("Filter: Contains category filter indicator", str_contains($resCat->getContent(), 'active-filter-indicator'));

// 3. Single Article & Social Reactions (Facebook Share, Like, Comments)
$resPost = $router->dispatch(new Request('GET', '/bn/blog/vedanta-and-modern-science'));
assert_test("Router: /bn/blog/vedanta-and-modern-science returns HTTP 200", $resPost->getStatusCode() === 200);

$bodyPost = $resPost->getContent();
assert_test("Single Post: Open Graph og:type is article", str_contains($bodyPost, 'property="og:type" content="article"'));
assert_test("Single Post: Open Graph og:title is present", str_contains($bodyPost, 'property="og:title"'));
assert_test("Single Post: Facebook Share button opens sharer dialog", str_contains($bodyPost, 'btn-share-facebook') && str_contains($bodyPost, 'facebook.com/sharer/sharer.php?u='));
assert_test("Single Post: Copy Link button with toast", str_contains($bodyPost, 'btn-copy-link') && str_contains($bodyPost, 'copyPostLink()'));
assert_test("Single Post: Interactive Like button with counter", str_contains($bodyPost, 'btn-like-interactive') && str_contains($bodyPost, 'likeCounter'));
assert_test("Single Post: Facebook-style comments section", str_contains($bodyPost, 'comments-section') && str_contains($bodyPost, 'facebook-comment-item'));
assert_test("Single Post: Comments form has input fields", str_contains($bodyPost, 'commentAuthorName') && str_contains($bodyPost, 'commentContent'));

// Test Social Like Action
$reqLike = new Request('POST', '/bn/blog/vedanta-and-modern-science/like?format=json');
$resLike = $router->dispatch($reqLike);
assert_test("Like: POST like returns HTTP 200 JSON", $resLike->getStatusCode() === 200);
$likeJson = json_decode($resLike->getContent(), true);
assert_test("Like: Response indicates success", ($likeJson['success'] ?? false) === true);

// Test Social Comment Action
$commentData = [
    'author_name' => 'বিজয় কুমার সরকার (পরীক্ষামূলক পাঠক)',
    'author_email' => 'bijoy@test.com',
    'content' => 'এই ব্লগটি পড়ে অত্যন্ত অনুপ্রাণিত হলাম। চমৎকার বিশ্লেষণ!',
];
$resComment = $router->dispatch(new Request('POST', '/bn/blog/vedanta-and-modern-science/comment', [], $commentData));
assert_test("Comment: POST comment returns 302 redirect", $resComment->getStatusCode() === 302);

$blogAfter = BlogService::findBySlug('vedanta-and-modern-science');
$lastCmt = end($blogAfter['comments']);
assert_test("Comment: New comment persisted in storage", ($lastCmt['author_name'] ?? '') === 'বিজয় কুমার সরকার (পরীক্ষামূলক পাঠক)');

// 4. Paid Member Authorship & Status Check
Session::set('current_member_code', 'SPS-000872');
Session::forget('member_logged_out');
$resWrite = $router->dispatch(new Request('GET', '/bn/blog/write'));
assert_test("Router: /bn/blog/write returns HTTP 200", $resWrite->getStatusCode() === 200);
$bodyWrite = $resWrite->getContent();
assert_test("Write View: Contains author membership verification box", str_contains($bodyWrite, 'membership-tier-card') && str_contains($bodyWrite, 'btnModePaid'));
assert_test("Write View: Contains Image Attachment input & sample presets", str_contains($bodyWrite, 'featuredImageInput') && str_contains($bodyWrite, 'image-preset-bar'));

// Test Unpaid/Visitor Submission Rejection
Session::forget('current_member_code');
Session::set('member_logged_out', true);
$unpaidPostData = [
    'membership_tier' => 'free_visitor',
    'title_bn' => 'অননুমোদিত ব্লগ পোস্ট',
    'content_bn' => 'এটি প্রকাশিত হওয়া উচিত নয়।',
];
$resUnpaid = $router->dispatch(new Request('POST', '/bn/blog/write', [], $unpaidPostData));
assert_test("Write Guard: Free visitor submission rejected with 302", $resUnpaid->getStatusCode() === 302);
assert_test("Write Guard: Flash error indicates paid membership requirement", str_contains(Session::getFlash('error') ?? '', 'পেইড') || str_contains(Session::getFlash('error') ?? '', 'Paid'));

// Test Paid Member Submission
Session::set('current_member_code', 'SPS-000872');
Session::forget('member_logged_out');
$paidPostData = [
    'membership_tier' => 'paid_member',
    'title_bn' => 'সনাতন দর্শনে পঞ্চমহাযজ্ঞ ও পরিবেশ চেতনা',
    'title_en' => 'Pancha Maha Yajna and Ecological Ethics in Sanatan Philosophy',
    'category' => 'vedanta',
    'featured_image' => 'assets/images/library/covers/sps-samachar-feb.jpg',
    'excerpt_bn' => 'বৈদিক পঞ্চমহাযজ্ঞের মাধ্যমে কীভাবে মানুষ ও প্রকৃতির ভারসাম্য রক্ষিত হতো তার বিশ্লেষণ।',
    'excerpt_en' => 'How ancient Vedic yajnas maintained ecological harmony between man and nature.',
    'content_bn' => '<p>দেবযজ্ঞ, পিতৃযজ্ঞ, মনুষ্যযজ্ঞ, ভূতযজ্ঞ এবং ব্রহ্মযজ্ঞ—এই পাঁচটি যজ্ঞের মাধ্যমে পরিবেশ ও মানবজাতির সামগ্রিক কল্যাণ নিশ্চিত করা হতো।</p>',
    'tags' => 'যজ্ঞ, পরিবেশ, বেদান্ত, ধর্ম',
    'author_name_bn' => 'ড. শ্যামল দত্ত',
    'author_name_en' => 'Dr. Shyamal Dutta',
    'author_email' => 'shyamal@example.com',
];
$resPaid = $router->dispatch(new Request('POST', '/bn/blog/write', [], $paidPostData));
assert_test("Write Success: Paid member submission accepted with 302", $resPaid->getStatusCode() === 302);

// Check that new post is in pending status and NOT on public feed
$allPending = BlogService::getPendingBlogs();
$foundNew = false;
$newBlogId = '';
foreach ($allPending as $pb) {
    if (($pb['title_bn'] ?? '') === 'সনাতন দর্শনে পঞ্চমহাযজ্ঞ ও পরিবেশ চেতনা') {
        $foundNew = true;
        $newBlogId = $pb['id'];
        break;
    }
}
assert_test("Moderation Flow: New post is stored with status 'pending'", $foundNew && !empty($newBlogId));

$publishedCheck = BlogService::getPublishedBlogs();
$foundInPublic = false;
foreach ($publishedCheck as $pubB) {
    if (($pubB['title_bn'] ?? '') === 'সনাতন দর্শনে পঞ্চমহাযজ্ঞ ও পরিবেশ চেতনা') {
        $foundInPublic = true;
        break;
    }
}
assert_test("Moderation Flow: Unapproved pending post is NOT visible on public feed", !$foundInPublic);

// 5. Role-Based Moderation: Super Admin, Admin, Literature-Admin vs Unauthorized Roles
// 5a. Unauthorized: Finance-Admin Joy Chakraborty (usr_joy)
AuthService::switchUser('usr_joy');
$resModJoy = $router->dispatch(new Request('GET', '/bn/admin/blogs'));
assert_test("Security: Finance-Admin (/bn/admin/blogs) returns 403 Forbidden", $resModJoy->getStatusCode() === 403);

$resApproveJoy = $router->dispatch(new Request('POST', '/bn/admin/blogs/approve/' . $newBlogId));
assert_test("Security: Finance-Admin cannot approve blog (HTTP 403)", $resApproveJoy->getStatusCode() === 403);

// 5b. Authorized: Literature-Admin Pranto Saha (usr_pranto, content_editor)
AuthService::switchUser('usr_pranto');
$resModPranto = $router->dispatch(new Request('GET', '/bn/admin/blogs'));
assert_test("Literature-Admin: Pranto Saha accesses /bn/admin/blogs with HTTP 200", $resModPranto->getStatusCode() === 200);
assert_test("Literature-Admin View: Contains pending blogs tabs & action buttons", str_contains($resModPranto->getContent(), 'panePending') && str_contains($resModPranto->getContent(), 'btn-approve'));

// Literature-Admin Approves the New Post
$resApprovePranto = $router->dispatch(new Request('POST', '/bn/admin/blogs/approve/' . $newBlogId));
assert_test("Literature-Admin: Approval returns 302 redirect", $resApprovePranto->getStatusCode() === 302);

$approvedBlog = BlogService::findById($newBlogId);
assert_test("Moderation State: Post status is now 'published'", ($approvedBlog['status'] ?? '') === 'published');
assert_test("Moderation State: Approved by Pranto Saha", str_contains($approvedBlog['approved_by'] ?? '', 'প্রান্ত সাহা') || str_contains($approvedBlog['approved_by'] ?? '', 'Pranto'));

// Verify it now appears on public feed
BlogService::clearCache();
$publishedNow = BlogService::getPublishedBlogs();
$foundInPublicNow = false;
foreach ($publishedNow as $pubB) {
    if (($pubB['title_bn'] ?? '') === 'সনাতন দর্শনে পঞ্চমহাযজ্ঞ ও পরিবেশ চেতনা') {
        $foundInPublicNow = true;
        break;
    }
}
assert_test("Public Visibility: Approved post is now live on public feed", $foundInPublicNow);

// 5c. Authorized: Admin Robin Dey (usr_robin) rejects a post
AuthService::switchUser('usr_robin');
$resModRobin = $router->dispatch(new Request('GET', '/bn/admin/blogs'));
assert_test("Admin: Robin Dey accesses /bn/admin/blogs with HTTP 200", $resModRobin->getStatusCode() === 200);

$resRejectRobin = $router->dispatch(new Request('POST', '/bn/admin/blogs/reject/' . $newBlogId, [], ['reason' => 'বানান ও তথ্যের সামান্য পরিমার্জন প্রয়োজন।']));
assert_test("Admin: Rejection returns 302 redirect", $resRejectRobin->getStatusCode() === 302);

$rejectedBlog = BlogService::findById($newBlogId);
assert_test("Moderation State: Post status is now 'rejected'", ($rejectedBlog['status'] ?? '') === 'rejected');
assert_test("Moderation State: Rejection feedback preserved", str_contains($rejectedBlog['rejection_reason'] ?? '', 'পরিমার্জন'));

// 5d. Authorized: Super Admin Anik Kumar Saha (usr_anik) deletes post
AuthService::switchUser('usr_anik');
$resDeleteAnik = $router->dispatch(new Request('POST', '/bn/admin/blogs/delete/' . $newBlogId));
assert_test("Super Admin: Delete returns 302 redirect", $resDeleteAnik->getStatusCode() === 302);
assert_test("Storage: Blog permanently deleted", BlogService::findById($newBlogId) === null);

// 6. English Version Typography & Content Test
I18n::init($langConfig, 'en', '/en');
$resEn = $router->dispatch(new Request('GET', '/en/blog'));
assert_test("English Feed: /en/blog returns HTTP 200", $resEn->getStatusCode() === 200);
$bodyEn = $resEn->getContent();
assert_test("English Feed: Has Arial, Poppins, Times New Roman CSS rule", str_contains($bodyEn, 'Arial') && str_contains($bodyEn, 'Poppins') && str_contains($bodyEn, 'Times New Roman'));
assert_test("English Feed: Contains English headings & buttons", str_contains($bodyEn, 'Write a Blog') || str_contains($bodyEn, 'Read More'));

// 7. Width, 10,000 Word Limit & DRM Protection Tests
I18n::init($langConfig, 'bn', '/bn');

// 7a. Container Width Consistency
$resFeedBn = $router->dispatch(new Request('GET', '/bn/blog'));
$feedContent = $resFeedBn->getContent();
assert_test("Container Width: /bn/blog uses standard container class & max-width", 
    str_contains($feedContent, 'blog-page-container') && str_contains($feedContent, 'var(--container-max)'));

$resSingleBn = $router->dispatch(new Request('GET', '/bn/blog/vedanta-and-modern-science'));
$singleContent = $resSingleBn->getContent();
assert_test("Container Width: /bn/blog/{slug} uses standard container class & max-width", 
    str_contains($singleContent, 'blog-page-container') && str_contains($singleContent, 'var(--container-max)'));

Session::set('current_member_code', 'SPS-000872');
Session::forget('member_logged_out');
$resWriteBn = $router->dispatch(new Request('GET', '/bn/blog/write'));
$writeContent = $resWriteBn->getContent();
assert_test("Container Width: /bn/blog/write uses standard container class & max-width", 
    str_contains($writeContent, 'blog-page-container') && str_contains($writeContent, 'var(--container-max)'));

// 7b. Admin Blogs Layout Consistency (uses admin layout with sidebar)
AuthService::switchUser('usr_pranto');
$resAdminBlogs = $router->dispatch(new Request('GET', '/bn/admin/blogs'));
$adminBlogsContent = $resAdminBlogs->getContent();
assert_test("Admin Layout: /bn/admin/blogs rendered with admin sidebar & topbar", 
    str_contains($adminBlogsContent, 'admin-sidebar') && str_contains($adminBlogsContent, 'admin-topbar'));
assert_test("Admin Layout: /bn/admin/blogs contains admin-main workspace", 
    str_contains($adminBlogsContent, 'admin-main'));
AuthService::logout();

// 7c. 10,000 Word Counter & Backend Validation
assert_test("Word Counter UI: /bn/blog/write contains live 10,000 word counter", 
    str_contains($writeContent, 'id="wordCounter"') && str_contains($writeContent, '10000'));
assert_test("Word Counter UI: Contains live wordLimitAlert element", 
    str_contains($writeContent, 'id="wordLimitAlert"'));

// Test over-limit manuscript submission (> 10,000 words)
Session::set('current_member_code', 'SPS-000872');
Session::forget('member_logged_out');
$longManuscript = str_repeat('বেদান্ত দর্শন শাশ্বত সত্য ও বিশ্বজনীন অহিংসার বাণী প্রচার করে। ', 1500); // ~12,000 words
$overlimitPostData = [
    'membership_tier' => 'paid_member',
    'title_bn' => 'অতি দীর্ঘ গবেষণাপত্র',
    'content_bn' => $longManuscript,
    'excerpt_bn' => 'সারসংক্ষেপ',
];
$resOverlimit = $router->dispatch(new Request('POST', '/bn/blog/write', [], $overlimitPostData));
assert_test("Word Limit Guard: Submission exceeding 10,000 words is rejected with 302", 
    $resOverlimit->getStatusCode() === 302);
$flashErr = Session::getFlash('error') ?? '';
assert_test("Word Limit Guard: Flash error indicates 10,000 words limit violation", 
    str_contains($flashErr, '১০,০০০') || str_contains($flashErr, '10,000'));

// 7d. Content DRM Protection (Anti-Copy & Anti-Screenshot)
assert_test("DRM Protection: Single article has sps-protected-content class", 
    str_contains($singleContent, 'sps-protected-content'));
assert_test("DRM Protection: Single article has anti-screenshot blackout shield modal", 
    str_contains($singleContent, 'sps-drm-screen-shield'));
assert_test("DRM Protection: Single article has snipping tool focus loss shield", 
    str_contains($singleContent, 'sps-snipping-shield'));
assert_test("DRM Protection: Single article has security watermark mesh", 
    str_contains($singleContent, 'sps-watermark-mesh'));
assert_test("DRM Protection: Single article has PrintScreen key clear script", 
    str_contains($singleContent, 'PrintScreen') && str_contains($singleContent, 'clipboard.writeText'));
assert_test("DRM Protection: Single article has right-click contextmenu prevention", 
    str_contains($singleContent, 'contextmenu') && str_contains($singleContent, 'preventDefault'));

assert_test("DRM Protection: Blog feed has sps-protected-content class", 
    str_contains($feedContent, 'sps-protected-content'));
assert_test("DRM Protection: Blog feed has DRM toast alert", 
    str_contains($feedContent, 'drmProtectionToast'));

echo "\n----------------------------------------------------\n";
echo "Total Tests: " . ($passed + $failed) . " | Passed: {$passed} | Failed: {$failed}\n";
echo "====================================================\n";

exit($failed > 0 ? 1 : 0);
