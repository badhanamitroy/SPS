<?php

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
        $books = LibraryService::getBooks();
        $currentRole = LibraryService::getCurrentRole();

        $title = $isBn 
            ? 'ডিজিটাল গ্রন্থাগার ও পাণ্ডুলিপি সংগ্রহ | এসপিএস'
            : 'Digital Library & Manuscript Archive | SPS';

        $description = $isBn
            ? 'সনাতন দর্শন, উপনিষদ, বেদান্ত ও প্রামাণ্য শাস্ত্রগ্রন্থের ডিজিটাল সংস্করণ। নিয়মিত সদস্যগণের জন্য ডিজিটাল ই-বুক পড়ার সুব্যবস্থা।'
            : 'Digital editions of authentic Sanatan philosophy, Upanishads, and sacred scriptures. Interactive online e-book reading for registered members.';

        return $this->render('library/index', [
            'metaTitle' => $title,
            'metaDescription' => $description,
            'activeNav' => 'library',
            'books' => $books,
            'currentRole' => $currentRole,
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
        $hasAccess = LibraryService::hasAccess($targetSlug);
        $pendingRequest = LibraryService::getUserPendingRequest($targetSlug);

        $bookTitle = $isBn ? $book['title_bn'] : $book['title_en'];

        return $this->render('library/show', [
            'metaTitle' => $bookTitle . ' | ' . config('app.short_name'),
            'metaDescription' => $isBn ? $book['synopsis_bn'] : $book['synopsis_en'],
            'activeNav' => 'library',
            'book' => $book,
            'currentRole' => $currentRole,
            'hasAccess' => $hasAccess,
            'pendingRequest' => $pendingRequest,
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

        // Enforce reading access rule: Paid General Members or approved viewers only
        if (!LibraryService::hasAccess($targetSlug)) {
            Session::setFlash('warning', $isBn 
                ? 'এই গ্রন্থটি পূর্ণাঙ্গ পাঠ করতে সক্রিয় সাধারণ সদস্যপদ অথবা অনুমোদিত পাঠাধিকার প্রয়োজন। নিচে আপনার পাঠের কারণ জানিয়ে অনুরোধ পাঠাতে পারেন।'
                : 'Full reading access is restricted to verified Paid General Members or approved scholar passes. Please submit an access request below.');
            return $this->redirect(url('/library/book/' . $targetSlug, $locale));
        }

        $bookTitle = $isBn ? $book['title_bn'] : $book['title_en'];

        return $this->render('library/reader', [
            'metaTitle' => ($isBn ? 'ই-বুক পাঠাগার: ' : 'E-Book Reader: ') . $bookTitle,
            'metaDescription' => $isBn ? $book['synopsis_bn'] : $book['synopsis_en'],
            'activeNav' => 'library',
            'book' => $book,
            'pdfStreamUrl' => url('/library/stream/' . $targetSlug, $locale),
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
        if (!$book || !file_exists($book['file_path'])) {
            return new Response('PDF file not found', 404, ['Content-Type' => 'text/plain']);
        }

        // Security gate: strictly verify access before streaming bytes
        if (!LibraryService::hasAccess($targetSlug)) {
            return new Response('403 Forbidden: Reading access required', 403, ['Content-Type' => 'text/plain']);
        }

        $filePath = $book['file_path'];
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

        // Output raw file bytes
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

    public function switchRole(Request $request): Response
    {
        $locale = I18n::getLocale();
        $role = $request->get('role') ?? $request->post('role', 'paid_member');
        LibraryService::setRole($role);

        $ref = $_SERVER['HTTP_REFERER'] ?? url('/library', $locale);
        return $this->redirect($ref);
    }
}
