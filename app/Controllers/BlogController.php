<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\I18n;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\AuthService;
use App\Services\BlogService;

class BlogController extends BaseController
{
    /**
     * Blogspot Clone Feed & Catalog
     */
    public function index(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $category = (string)$request->getQuery('category', '');
        $tag = (string)$request->getQuery('tag', '');
        $archive = (string)$request->getQuery('archive', '');
        $search = (string)$request->getQuery('q', '');

        $posts = BlogService::getPublishedBlogs(
            $category ?: null,
            $tag ?: null,
            $archive ?: null,
            $search ?: null
        );

        $popularPosts = BlogService::getPopularPosts(4);
        $archiveTree = BlogService::getArchiveTree();
        $categories = BlogService::getCategories();
        $tags = BlogService::getTags();

        $title = $isBn 
            ? 'এসপিএস ব্লগ ও উন্মুক্ত জ্ঞানপীঠ | সনাতন বিদ্যার্থী সংসদ'
            : 'SPS Blog & Thought Journal | Sanatan Vidyarthi Sangsad';

        return $this->render('blog/index', [
            'metaTitle' => $title,
            'metaDescription' => $isBn 
                ? 'সনাতন দর্শন, উপনিষদ, ভগবদগীতা, বাংলার প্রাচীন ঐতিহ্য ও সমাজ সংস্কার বিষয়ক গবেষণামূলক ব্লগ।'
                : 'Scholarly articles and community reflections on Sanatan philosophy, Upanishads, Gita, and cultural heritage.',
            'activeNav' => 'blog',
            'posts' => $posts,
            'popularPosts' => $popularPosts,
            'archiveTree' => $archiveTree,
            'categories' => $categories,
            'tags' => $tags,
            'currentCategory' => $category,
            'currentTag' => $tag,
            'currentArchive' => $archive,
            'searchQuery' => $search,
            'canonicalUrl' => url('/blog', $locale),
            'alternateBn' => url('/blog', 'bn'),
            'alternateEn' => url('/blog', 'en'),
        ]);
    }

    /**
     * Single Blog Post Reader & Discussion
     */
    public function show(Request $request, string $lang = '', string $slug = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $postSlug = $slug ?: (string)$request->getParam('slug', '');

        $post = BlogService::findBySlug($postSlug, false);

        if (!$post) {
            return $this->notFound($request);
        }

        // If not published, only allow Super Admin, Admin, and Literature-Admin to preview
        if (($post['status'] ?? '') !== 'published') {
            if (!BlogService::canModerateBlogs()) {
                Session::setFlash('warning', $isBn 
                    ? 'এই ব্লগটি এখনও প্রকাশিত হয়নি বা পর্যালোচনার অধীনে রয়েছে।' 
                    : 'This blog post is currently pending review and unpublished.');
                return $this->redirect(url('/blog', $locale));
            }
        } else {
            // Increment view count for published posts
            BlogService::incrementViews($postSlug);
        }

        $userIdentifier = Session::get('user_ip') ?: ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
        $hasLiked = BlogService::hasLiked($postSlug, $userIdentifier);

        $popularPosts = BlogService::getPopularPosts(4);
        $archiveTree = BlogService::getArchiveTree();
        $categories = BlogService::getCategories();

        $postTitle = $isBn ? ($post['title_bn'] ?? $post['title_en']) : ($post['title_en'] ?? $post['title_bn']);
        $postExcerpt = $isBn ? ($post['excerpt_bn'] ?? $post['excerpt_en']) : ($post['excerpt_en'] ?? $post['excerpt_bn']);
        $featuredImage = $post['featured_image'] ?? 'assets/images/brand/sps-logo.png';

        return $this->render('blog/show', [
            'metaTitle' => $postTitle . ' | ' . config('app.short_name'),
            'metaDescription' => $postExcerpt,
            'ogType' => 'article',
            'ogImage' => asset($featuredImage),
            'activeNav' => 'blog',
            'post' => $post,
            'hasLiked' => $hasLiked,
            'popularPosts' => $popularPosts,
            'archiveTree' => $archiveTree,
            'categories' => $categories,
            'canonicalUrl' => url('/blog/' . $postSlug, $locale),
            'alternateBn' => url('/blog/' . $postSlug, 'bn'),
            'alternateEn' => url('/blog/' . $postSlug, 'en'),
        ]);
    }

    /**
     * Paid Member Write Blog Page
     */
    public function writePage(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        // Check simulated or active session mode
        $sessionMode = Session::get('member_mode') ?? 'paid_member';
        $currentUser = AuthService::getCurrentUser();

        $title = $isBn 
            ? 'পেইড সদস্য ব্লগ রচনা ও পাণ্ডুলিপি সাবমিট | এসপিএস'
            : 'Write Blog Post & Submit Manuscript | SPS Paid Member Desk';

        return $this->render('blog/write', [
            'metaTitle' => $title,
            'activeNav' => 'blog',
            'currentUser' => $currentUser,
            'memberMode' => $sessionMode,
            'categories' => BlogService::getCategories(),
            'canonicalUrl' => url('/blog/write', $locale),
            'alternateBn' => url('/blog/write', 'bn'),
            'alternateEn' => url('/blog/write', 'en'),
        ]);
    }

    /**
     * Submit New Blog Post (By Paid Member)
     */
    public function submitPost(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        // Membership Verification: only paid members can write
        $membershipTier = (string)$request->getPost('membership_tier', 'paid_member');
        $simulatedMode = (string)$request->getPost('simulate_mode', 'paid_member');

        if ($membershipTier === 'free_visitor' || $simulatedMode === 'free_visitor') {
            Session::setFlash('error', $isBn 
                ? 'দুঃখিত! ব্লগ লেখার সুবিধাটি শুধুমাত্র এসপিএস-এর অনুমোদিত পেইড ও আজীবন সদস্যদের জন্য সংরক্ষিত। অনুগ্রহ করে পেইড সদস্যপদে যুক্ত হোন।' 
                : 'Access Denied: Writing blogs is an exclusive privilege reserved for SPS Paid & Lifetime Members. Please upgrade your membership.');
            return $this->redirect(url('/blog/write', $locale));
        }

        $titleBn = trim((string)$request->getPost('title_bn', ''));
        $titleEn = trim((string)$request->getPost('title_en', ''));
        $contentBn = trim((string)$request->getPost('content_bn', ''));
        $contentEn = trim((string)$request->getPost('content_en', ''));

        if (empty($titleBn) && empty($titleEn)) {
            Session::setFlash('error', $isBn ? 'ব্লগের শিরোনাম প্রদান করা আবশ্যক।' : 'Blog title is required.');
            return $this->redirect(url('/blog/write', $locale));
        }

        if (empty($contentBn) && empty($contentEn)) {
            Session::setFlash('error', $isBn ? 'ব্লগের বিস্তারিত বিষয়বস্তু লেখা আবশ্যক।' : 'Blog content is required.');
            return $this->redirect(url('/blog/write', $locale));
        }

        // Enforce 10,000 words limit (Bilingual & Unicode word count)
        $cleanContent = strip_tags($contentBn . ' ' . $contentEn);
        preg_match_all('/[\p{L}\p{N}]+/u', $cleanContent, $matches);
        $totalWords = !empty($matches[0]) ? count($matches[0]) : 0;

        if ($totalWords > 10000) {
            $formattedWords = $isBn ? I18n::formatNumber((string)$totalWords) : number_format($totalWords);
            Session::setFlash('error', $isBn 
                ? "ব্লগের শব্দসংখ্যা সর্বোচ্চ ১০,০০০ শব্দের মধ্যে হতে হবে। আপনার বর্তমান লেখার আকার: {$formattedWords} শব্দ।" 
                : "Blog content exceeds the maximum limit of 10,000 words. Current count: {$formattedWords} words.");
            return $this->redirect(url('/blog/write', $locale));
        }

        $authorUser = AuthService::getCurrentUser();
        $authorData = [
            'name_bn' => trim((string)$request->getPost('author_name_bn', $authorUser['name_bn'] ?? 'শ্রী সুমিত কুমার রায়')),
            'name_en' => trim((string)$request->getPost('author_name_en', $authorUser['name_en'] ?? 'Sumit Kumar Roy')),
            'role' => 'paid_member',
            'tier_bn' => 'পেইড সদস্য ও লেখক',
            'tier_en' => 'Paid Member & Author',
            'avatar' => $authorUser['avatar'] ?? ('https://api.dicebear.com/7.x/bottts/svg?seed=' . urlencode((string)$request->getPost('author_name_en', 'author'))),
            'email' => trim((string)$request->getPost('author_email', $authorUser['email'] ?? 'member@sps.org')),
        ];

        $postData = [
            'title_bn' => $titleBn ?: $titleEn,
            'title_en' => $titleEn ?: $titleBn,
            'category' => (string)$request->getPost('category', 'vedanta'),
            'featured_image' => trim((string)$request->getPost('featured_image', 'assets/images/library/covers/sps-samachar-feb.jpg')),
            'excerpt_bn' => trim((string)$request->getPost('excerpt_bn', '')),
            'excerpt_en' => trim((string)$request->getPost('excerpt_en', '')),
            'content_bn' => $contentBn ?: $contentEn,
            'content_en' => $contentEn ?: $contentBn,
            'tags' => (string)$request->getPost('tags', ''),
        ];

        $created = BlogService::createBlog($postData, $authorData);

        Session::setFlash('success', $isBn 
            ? 'আপনার ব্লগটি সফলভাবে জমা হয়েছে! এটি এখন মডারেশন পর্যায়ে রয়েছে। সুপার-অ্যাডমিন, অ্যাডমিন অথবা সাহিত্য বিষয়ক সম্পাদক (Literature-Admin) অনুমোদনের পর এটি সবার জন্য প্রকাশিত হবে।' 
            : 'Your blog post has been successfully submitted! It is now pending moderation. A Super Admin, Admin, or Literature-Admin will review and publish it.');

        return $this->redirect(url('/blog', $locale));
    }

    /**
     * Like / Reaction Toggle
     */
    public function toggleLike(Request $request, string $lang = '', string $slug = ''): Response
    {
        $locale = I18n::getLocale();
        $postSlug = $slug ?: (string)$request->getParam('slug', '');

        $userIdentifier = Session::get('user_ip') ?: ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
        $currentUser = AuthService::getCurrentUser();
        if ($currentUser) {
            $userIdentifier = $currentUser['id'];
        }

        $result = BlogService::toggleLike($postSlug, $userIdentifier);

        // Check if AJAX
        if ($request->isAjax() || $request->getQuery('format') === 'json') {
            return (new Response())->json($result);
        }

        return $this->redirect(url('/blog/' . $postSlug, $locale) . '#reactions');
    }

    /**
     * Add Facebook-style Comment
     */
    public function addComment(Request $request, string $lang = '', string $slug = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $postSlug = $slug ?: (string)$request->getParam('slug', '');

        $name = trim((string)$request->getPost('author_name', ''));
        $content = trim((string)$request->getPost('content', ''));
        $email = trim((string)$request->getPost('author_email', ''));

        if (empty($name) || empty($content)) {
            Session::setFlash('error', $isBn ? 'নাম এবং মন্তব্যের বিবরণ প্রদান করা আবশ্যক।' : 'Name and comment message are required.');
            return $this->redirect(url('/blog/' . $postSlug, $locale) . '#comment-box');
        }

        // Determine commenter role badge
        $authorRole = 'visitor';
        $authorAvatar = null;
        if ($admin = AuthService::getCurrentUser()) {
            $authorRole = $admin['role'] ?? 'admin';
            $authorAvatar = $admin['avatar'] ?? null;
            if (empty($name)) {
                $name = $isBn ? $admin['name_bn'] : $admin['name_en'];
            }
        } elseif (!empty($request->getPost('is_paid_member'))) {
            $authorRole = 'paid_member';
        }

        $comment = BlogService::addComment($postSlug, [
            'author_name' => $name,
            'author_email' => $email,
            'author_role' => $authorRole,
            'author_avatar' => $authorAvatar,
            'content' => $content,
        ]);

        if ($request->isAjax() || $request->getQuery('format') === 'json') {
            return (new Response())->json([
                'success' => $comment !== null,
                'comment' => $comment,
            ]);
        }

        Session::setFlash('success', $isBn ? 'আপনার মন্তব্য সফলভাবে প্রকাশিত হয়েছে!' : 'Your comment has been posted successfully!');
        return $this->redirect(url('/blog/' . $postSlug, $locale) . '#comments');
    }
}
