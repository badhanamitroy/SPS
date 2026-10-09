<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\I18n;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\LibraryService;

class LibraryController extends BaseController
{
    public function index(Request $request): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $selectedCategory = $request->get('category', 'all');
        if (!in_array($selectedCategory, ['all', 'sps', 'other'], true)) {
            $selectedCategory = 'all';
        }

        $searchQuery = trim((string)$request->get('q', ''));
        $sortOption = (string)$request->get('sort', 'featured');
        $accessFilter = (string)$request->get('access', 'all');

        $allBooks = LibraryService::getBooks();
        $categories = LibraryService::getCategories();
        $categoryCounts = LibraryService::getCategoryCounts();
        $currentRole = LibraryService::getCurrentRole();
        $userContext = LibraryService::getCurrentUserContext();

        // Filter by category
        $filteredBooks = $allBooks;
        if ($selectedCategory !== 'all') {
            $filteredBooks = array_filter($filteredBooks, fn($b) => ($b['category_group'] ?? '') === $selectedCategory);
        }

        // Filter by search query
        if (!empty($searchQuery)) {
            $q = mb_strtolower($searchQuery);
            $filteredBooks = array_filter($filteredBooks, function ($b) use ($q) {
                $titleBn = mb_strtolower($b['title_bn'] ?? '');
                $titleEn = mb_strtolower($b['title_en'] ?? '');
                $authorBn = mb_strtolower($b['author_bn'] ?? '');
                $authorEn = mb_strtolower($b['author_en'] ?? '');
                $publisherBn = mb_strtolower($b['publisher_bn'] ?? '');
                $publisherEn = mb_strtolower($b['publisher_en'] ?? '');
                $synopsisBn = mb_strtolower($b['synopsis_bn'] ?? '');
                $synopsisEn = mb_strtolower($b['synopsis_en'] ?? '');
                $topics = mb_strtolower(implode(' ', $b['topics'] ?? []));

                return str_contains($titleBn, $q) || str_contains($titleEn, $q)
                    || str_contains($authorBn, $q) || str_contains($authorEn, $q)
                    || str_contains($publisherBn, $q) || str_contains($publisherEn, $q)
                    || str_contains($synopsisBn, $q) || str_contains($synopsisEn, $q)
                    || str_contains($topics, $q);
            });
        }

        // Filter by access type
        if ($accessFilter === 'public') {
            $filteredBooks = array_filter($filteredBooks, fn($b) => ($b['reading_access'] ?? '') === 'public' && ($b['reading_scope'] ?? '') === 'full');
        } elseif ($accessFilter === 'preview') {
            $filteredBooks = array_filter($filteredBooks, fn($b) => ($b['reading_access'] ?? '') === 'public' && ($b['reading_scope'] ?? '') === 'partial');
        } elseif ($accessFilter === 'members') {
            $filteredBooks = array_filter($filteredBooks, fn($b) => ($b['reading_access'] ?? '') === 'paid_members');
        }

        // Sorting
        $sortedBooks = array_values($filteredBooks);
        if ($sortOption === 'year_desc') {
            usort($sortedBooks, fn($a, $b) => ($b['publication_year'] ?? 0) <=> ($a['publication_year'] ?? 0));
        } elseif ($sortOption === 'year_asc') {
            usort($sortedBooks, fn($a, $b) => ($a['publication_year'] ?? 0) <=> ($b['publication_year'] ?? 0));
        } elseif ($sortOption === 'pages_desc') {
            usort($sortedBooks, fn($a, $b) => ($b['pages_count'] ?? 0) <=> ($a['pages_count'] ?? 0));
        } elseif ($sortOption === 'title') {
            usort($sortedBooks, fn($a, $b) => strcmp($isBn ? ($a['title_bn'] ?? '') : ($a['title_en'] ?? ''), $isBn ? ($b['title_bn'] ?? '') : ($b['title_en'] ?? '')));
        }

        // Enrich books with user's access level and download eligibility
        foreach ($sortedBooks as &$book) {
            $slug = $book['slug'];
            $book['reading_level'] = LibraryService::getReadingAccessLevel($slug);
            $book['download_eligibility'] = LibraryService::getDownloadEligibility($slug);
        }
        unset($book);

        $title = $isBn 
            ? 'ডিজিটাল গ্রন্থাগার ও ই-বুক প্রকাশনা কোষ | এসপিএস'
            : 'Digital E-Book Library & Archival Publications | SPS';

        $description = $isBn
            ? 'সনাতন দর্শন, উপনিষদ, বেদান্ত ও প্রামাণ্য শাস্ত্রগ্রন্থের ডিজিটাল সংগ্রহ। দ্বিস্তরীয় পাঠাধিকার ও সুরক্ষিত রিডার সম্বলিত ই-বুক পোর্টাল।'
            : 'Digital editions of authentic Sanatan philosophy and scriptures. Editorial ebook platform with fine-grained reading and download governance.';

        return $this->render('library/index', [
            'metaTitle' => $title,
            'metaDescription' => $description,
            'activeNav' => 'library',
            'books' => $sortedBooks,
            'allBooksCount' => count($allBooks),
            'categories' => $categories,
            'categoryCounts' => $categoryCounts,
            'selectedCategory' => $selectedCategory,
            'searchQuery' => $searchQuery,
            'sortOption' => $sortOption,
            'accessFilter' => $accessFilter,
            'currentRole' => $currentRole,
            'userContext' => $userContext,
            'canonicalUrl' => url('/library', $locale),
            'alternateBn' => url('/library', 'bn'),
            'alternateEn' => url('/library', 'en'),
        ]);
    }

    public function show(Request $request, string $lang = '', string $slug = ''): Response
    {
        $targetSlug = !empty($slug) ? $slug : $lang;
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $book = LibraryService::getBook($targetSlug);

        if (!$book) {
            return (new ErrorController())->notFound($request);
        }

        $currentRole = LibraryService::getCurrentRole();
        $userContext = LibraryService::getCurrentUserContext();
        $readingLevel = LibraryService::getReadingAccessLevel($targetSlug);
        $hasAccess = ($readingLevel !== 'locked');
        $downloadEligibility = LibraryService::getDownloadEligibility($targetSlug);

        $pendingReadingRequest = LibraryService::getUserPendingRequest($targetSlug);

        // Check if there is a pending download request
        $downloadRequests = LibraryService::getDownloadRequests();
        $pendingDownloadRequest = null;
        $approvedDownloadRequest = null;
        $userEmail = $userContext['user_email'];

        foreach ($downloadRequests as $dReq) {
            if ($dReq['book_slug'] === $targetSlug && strtolower($dReq['user_email']) === strtolower($userEmail)) {
                if ($dReq['status'] === 'pending') {
                    $pendingDownloadRequest = $dReq;
                } elseif ($dReq['status'] === 'approved') {
                    $expired = !empty($dReq['expires_at']) && strtotime($dReq['expires_at']) < time();
                    if (!$expired) {
                        $approvedDownloadRequest = $dReq;
                    }
                }
            }
        }

        $bookTitle = $isBn ? $book['title_bn'] : $book['title_en'];

        return $this->render('library/show', [
            'metaTitle' => $bookTitle . ' | ' . config('app.short_name'),
            'metaDescription' => $isBn ? $book['synopsis_bn'] : $book['synopsis_en'],
            'activeNav' => 'library',
            'book' => $book,
            'currentRole' => $currentRole,
            'userContext' => $userContext,
            'readingLevel' => $readingLevel,
            'hasAccess' => $hasAccess,
            'downloadEligibility' => $downloadEligibility,
            'pendingReadingRequest' => $pendingReadingRequest,
            'pendingRequest' => $pendingReadingRequest,
            'pendingDownloadRequest' => $pendingDownloadRequest,
            'approvedDownloadRequest' => $approvedDownloadRequest,
            'canonicalUrl' => url('/library/book/' . $targetSlug, $locale),
            'alternateBn' => url('/library/book/' . $targetSlug, 'bn'),
            'alternateEn' => url('/library/book/' . $targetSlug, 'en'),
        ]);
    }

    public function reader(Request $request, string $lang = '', string $slug = ''): Response
    {
        $targetSlug = !empty($slug) ? $slug : $lang;
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $book = LibraryService::getBook($targetSlug);

        if (!$book) {
            return (new ErrorController())->notFound($request);
        }

        $readingLevel = LibraryService::getReadingAccessLevel($targetSlug);

        // Strict enforcement: completely locked books redirect with a clear explanation
        if ($readingLevel === 'locked') {
            Session::setFlash('warning', $isBn 
                ? 'এই প্রকাশনাটি পূর্ণাঙ্গ পাঠ করতে সক্রিয় সাধারণ সদস্যপদ অথবা অনুমোদিত পাঠাধিকার প্রয়োজন। নিচে আপনার পাঠের কারণ জানিয়ে অনুরোধ পাঠাতে পারেন।'
                : 'Full reading access is restricted to verified Paid Members. Please submit an access request below.');
            return $this->redirect(url('/library/book/' . $targetSlug, $locale));
        }

        $bookTitle = $isBn ? $book['title_bn'] : $book['title_en'];
        $userContext = LibraryService::getCurrentUserContext();
        $downloadEligibility = LibraryService::getDownloadEligibility($targetSlug);

        // Dynamic dynamic anti-screenshot and attribution watermark
        $watermarkText = $userContext['is_member'] 
            ? "Licensed to: {$userContext['user_name']} • ID: {$userContext['member_code']} • SPS Digital Archive"
            : ($userContext['is_admin']
                ? "SPS Administrative Console • {$userContext['user_name']} • Supervised Preview"
                : "SPS Public Scholarly Preview • Licensed for On-Screen Reading • No Redistribution");

        return $this->render('library/reader', [
            'metaTitle' => ($isBn ? 'ই-বুক পাঠাগার: ' : 'E-Book Reader: ') . $bookTitle,
            'metaDescription' => $isBn ? $book['synopsis_bn'] : $book['synopsis_en'],
            'activeNav' => 'library',
            'book' => $book,
            'readingLevel' => $readingLevel,
            'readingScope' => $book['reading_scope'] ?? 'full',
            'previewStart' => (int)($book['preview_start'] ?? 1),
            'previewEnd' => (int)($book['preview_end'] ?? ($book['pages_count'] ?? 20)),
            'totalPages' => (int)($book['pages_count'] ?? 1),
            'pdfStreamUrl' => url('/library/stream/' . $targetSlug, $locale),
            'downloadEligibility' => $downloadEligibility,
            'userWatermark' => $watermarkText,
            'userContext' => $userContext,
            'currentRole' => LibraryService::getCurrentRole(),
            'canonicalUrl' => url('/library/reader/' . $targetSlug, $locale),
            'alternateBn' => url('/library/reader/' . $targetSlug, 'bn'),
            'alternateEn' => url('/library/reader/' . $targetSlug, 'en'),
        ]);
    }

    public function streamPdf(Request $request, string $lang = '', string $slug = ''): Response
    {
        $targetSlug = !empty($slug) ? $slug : $lang;
        $book = LibraryService::getBook($targetSlug);
        if (!$book) {
            return new Response('PDF file not found', 404, ['Content-Type' => 'text/plain']);
        }

        // Security gate: strictly verify access before streaming bytes
        $readingLevel = LibraryService::getReadingAccessLevel($targetSlug);
        if ($readingLevel === 'locked') {
            return new Response('403 Forbidden: Reading access required', 403, ['Content-Type' => 'text/plain']);
        }

        // Get tailored file path (sliced preview for partial readers, master PDF for full readers)
        $filePath = LibraryService::getStreamFile($targetSlug);
        if (!$filePath || !file_exists($filePath)) {
            if ($readingLevel === 'partial') {
                return new Response(
                    "503 Service Unavailable: প্রিভিউ তৈরি করা সম্ভব হয়নি, অনুগ্রহ করে কিছুক্ষণ পর চেষ্টা করুন। / Preview generation failed, please try again later.",
                    503,
                    ['Content-Type' => 'text/plain; charset=utf-8']
                );
            }
            return new Response('PDF file not found', 404, ['Content-Type' => 'text/plain']);
        }

        $fileSize = filesize($filePath);

        // Security headers: prevent file download, prevent framing outside SPS, inline streaming only
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
            'Content-Length' => (string)$fileSize,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'private, max-age=1800, no-transform',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
        ];

        $content = file_get_contents($filePath);
        return new Response($content, 200, $headers);
    }

    public function requestAccess(Request $request, string $lang = '', string $slug = ''): Response
    {
        $targetSlug = !empty($slug) ? $slug : $lang;
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $book = LibraryService::getBook($targetSlug);

        if (!$book) {
            return (new ErrorController())->notFound($request);
        }

        $userName = $request->post('user_name', '');
        $userEmail = $request->post('user_email', '');
        $userPhone = $request->post('user_phone', '');
        $institution = $request->post('institution', '');
        $reason = $request->post('reason', '');

        if (empty($userName) || empty($userEmail) || empty($reason)) {
            Session::setFlash('error', $isBn 
                ? 'অনুগ্রহ করে আপনার নাম, ইমেইল এবং গ্রন্থ পাঠের উদ্দেশ্য সঠিকভাবে পূরণ করুন।'
                : 'Please provide your full name, email, and reason for requesting reading access.');
            return $this->redirect(url('/library/book/' . $targetSlug, $locale));
        }

        LibraryService::createRequest($targetSlug, [
            'user_name' => $userName,
            'user_email' => $userEmail,
            'user_phone' => $userPhone,
            'institution' => $institution,
            'reason' => $reason,
        ]);

        Session::setFlash('success', $isBn 
            ? 'আপনার পাঠাধিকারের অনুরোধটি সফলভাবে গৃহীত হয়েছে। এসপিএস প্রশাসক পর্যালোচনা করে অনুমোদন প্রদান করবেন।'
            : 'Your reading access request has been submitted successfully. The SPS administrator will review and grant access shortly.');

        return $this->redirect(url('/library/book/' . $targetSlug, $locale));
    }

    public function requestDownload(Request $request, string $lang = '', string $slug = ''): Response
    {
        $targetSlug = !empty($slug) ? $slug : $lang;
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $book = LibraryService::getBook($targetSlug);

        if (!$book) {
            return (new ErrorController())->notFound($request);
        }

        $eligibility = LibraryService::getDownloadEligibility($targetSlug);
        if ($eligibility['status'] === 'disabled') {
            Session::setFlash('error', $isBn 
                ? 'এই প্রকাশনাটির জন্য ডাউনলোড সম্পূর্ণ সংরক্ষিত।' 
                : 'Downloads are disabled for this publication.');
            return $this->redirect(url('/library/book/' . $targetSlug, $locale));
        }

        $userName = trim((string)$request->post('user_name', ''));
        $userEmail = trim((string)$request->post('user_email', ''));
        $reason = trim((string)$request->post('reason', ''));
        $memberCode = trim((string)$request->post('member_code', ''));

        if (empty($userName) || empty($userEmail) || empty($reason)) {
            Session::setFlash('error', $isBn 
                ? 'ডাউনলোড আবেদন করতে আপনার নাম, ইমেইল এবং সুনির্দিষ্ট কারণ উল্লেখ করা বাধ্যতামূলক।'
                : 'Name, email, and research/reading purpose are required to submit a download request.');
            return $this->redirect(url('/library/book/' . $targetSlug, $locale));
        }

        LibraryService::createDownloadRequest($targetSlug, [
            'user_name' => $userName,
            'user_email' => $userEmail,
            'member_code' => $memberCode,
            'reason' => $reason,
        ]);

        Session::setFlash('success', $isBn 
            ? 'আপনার অফলাইন ডাউনলোড আবেদনটি গৃহীত হয়েছে। এসপিএস প্রশাসন পর্যালোচনা করে সাময়িক সুরক্ষিত ডাউনলোড পাস প্রদান করবে।'
            : 'Download request submitted successfully. The SPS administration will review and grant a time-limited download pass.');

        return $this->redirect(url('/library/book/' . $targetSlug, $locale));
    }

    public function downloadFile(Request $request, string $lang = '', string $slug = ''): Response
    {
        $targetSlug = !empty($slug) ? $slug : $lang;
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $book = LibraryService::getBook($targetSlug);

        if (!$book || !file_exists($book['file_path'])) {
            return (new ErrorController())->notFound($request);
        }

        $token = (string)$request->get('token', '');
        $role = LibraryService::getCurrentRole();
        $isDirectAllowed = false;

        // Admin can download directly
        if ($role === 'admin') {
            $isDirectAllowed = true;
        }

        // Direct download for paid members if configured
        if (($book['download_permission'] ?? '') === 'paid_members' && $role === 'paid_member') {
            $isDirectAllowed = true;
        }

        // If not direct, strictly verify temporary signed token
        if (!$isDirectAllowed) {
            $verifiedReq = LibraryService::verifyDownloadToken($targetSlug, $token);
            if (!$verifiedReq) {
                return new Response(
                    $isBn 
                        ? '<h3>৪০৩ — অননুমোদিত বা মেয়াদোত্তীর্ণ ডাউনলোড লিংক</h3><p>এই ডাউনলোড লিংকটি সঠিক নয় অথবা এর সাময়িক মেয়াদের সময়সীমা শেষ হয়ে গেছে। অনুগ্রহ করে গ্রন্থাগার থেকে নতুন আবেদন করুন।</p>' 
                        : '<h3>403 — Unauthorized or Expired Download Link</h3><p>This download token is invalid or has expired. Please submit a new request from the Library.</p>',
                    403,
                    ['Content-Type' => 'text/html; charset=UTF-8']
                );
            }
            LibraryService::markDownloaded($token);
        }

        $filePath = $book['file_path'];
        $downloadFilename = ($isBn ? $book['title_bn'] : $book['title_en']) . '.pdf';

        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . rawurlencode($downloadFilename) . '"',
            'Content-Length' => (string)filesize($filePath),
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        return new Response(file_get_contents($filePath), 200, $headers);
    }

    public function switchRole(Request $request): Response
    {
        $isDebug = (bool)(\App\Core\Env::get('APP_DEBUG', \App\Core\App::config('app.debug', false)));
        if (!$isDebug) {
            return new Response('403 Forbidden: Development role switching is disabled.', 403, ['Content-Type' => 'text/plain']);
        }

        $locale = I18n::getLocale();
        $role = $request->get('role') ?? $request->post('role', 'paid_member');
        LibraryService::setRole($role);

        $ref = $_SERVER['HTTP_REFERER'] ?? url('/library', $locale);
        return $this->redirect($ref);
    }
}
