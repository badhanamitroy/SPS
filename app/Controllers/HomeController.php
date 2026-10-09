<?php

namespace App\Controllers;

use App\Core\I18n;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\ActivityService;
use App\Services\BlogService;
use App\Services\HomepageService;
use App\Services\LibraryService;
use App\Services\MembershipService;

class HomeController extends BaseController
{
    public function index(Request $request): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $title = $isBn 
            ? 'সনাতন ফিলোসফি এন্ড স্ক্রিপচার (SPS) — সনাতনী ঐক্য, প্রচার ও কল্যাণে অবিচল'
            : 'Sanatan Philosophy and Scripture (SPS) — Steadfast in Sanatan Unity, Dissemination & Welfare';

        $description = $isBn
            ? 'সনাতন দর্শন, শাস্ত্র, গবেষণা ও মানবসেবাকে একত্র করে জ্ঞানচর্চা, ঐতিহ্য সংরক্ষণ এবং সমাজকল্যাণে কাজ করে SPS।'
            : 'Uniting Sanatan philosophy, scriptural exegesis, research, and selfless seva for knowledge preservation and societal welfare.';

        $sectionsMap = HomepageService::getSectionsMap();
        $statistics = HomepageService::getSiteStatistics();
        $featuredScripture = HomepageService::getFeaturedScripture();
        $transparency = HomepageService::getTransparencySummary();
        $socialLinks = HomepageService::getSocialLinks();

        // 1. Live Activities Slider (from ActivityService)
        $allActivities = ActivityService::getActivities();
        $liveActivities = array_slice($allActivities, 0, 6);

        // 2. Latest Publications (from LibraryService)
        $allBooks = LibraryService::getBooks();
        $featuredBooks = array_slice($allBooks, 0, 4);

        // 3. Members' Blog Articles (from BlogService)
        $allBlogs = BlogService::getBlogs(true);
        $featuredBlogs = array_slice($allBlogs, 0, 3);

        $currentMemberCode = Session::get('current_member_code');
        $isMemberLoggedIn = !empty($currentMemberCode) && !Session::get('member_logged_out');
        $currentMember = $isMemberLoggedIn ? MembershipService::getMemberById($currentMemberCode) : null;

        return $this->render('home', [
            'metaTitle' => $title,
            'metaDescription' => $description,
            'activeNav' => 'home',
            'canonicalUrl' => url('/', $locale),
            'alternateBn' => url('/', 'bn'),
            'alternateEn' => url('/', 'en'),
            'sections' => $sectionsMap,
            'statistics' => $statistics,
            'featuredScripture' => $featuredScripture,
            'transparency' => $transparency,
            'socialLinks' => $socialLinks,
            'activities' => $liveActivities,
            'featuredBooks' => $featuredBooks,
            'featuredBlogs' => $featuredBlogs,
            'isMemberLoggedIn' => $isMemberLoggedIn,
            'currentMember' => $currentMember,
        ]);
    }

    public function componentsShowcase(Request $request): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $title = $isBn 
            ? 'ডিজাইন সিস্টেম ও কম্পোনেন্ট লাইব্রেরি | এসপিএস'
            : 'Design System & Component Library | SPS';

        return $this->render('components_showcase', [
            'metaTitle' => $title,
            'metaDescription' => 'Editorial design tokens, responsive typography, and reusable UI components for SPS platform.',
            'activeNav' => 'components',
            'canonicalUrl' => url('/components', $locale),
            'alternateBn' => url('/components', 'bn'),
            'alternateEn' => url('/components', 'en'),
        ]);
    }
}
