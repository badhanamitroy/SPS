<?php

namespace App\Controllers;

use App\Core\I18n;
use App\Core\Request;
use App\Core\Response;

class SectionController extends BaseController
{
    public function about(Request $request): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $title = $isBn ? 'পরিচিতি ও কার্যনির্বাহী পরিষদ | এসপিএস' : 'About SPS & Executive Committee';

        return $this->render('about', [
            'metaTitle' => $title . ' — ' . config('app.short_name'),
            'metaDescription' => $isBn 
                ? 'সনাতন দর্শন ও শাস্ত্র (SPS)-এর প্রাতিষ্ঠানিক রূপরেখা, মূল আদর্শ ও কেন্দ্রীয় কার্যনির্বাহী পরিষদ।' 
                : 'Institutional framework, core ideals, and the Central Executive Committee of SPS.',
            'activeNav' => 'about',
            'executives' => \App\Services\ExecutiveService::getExecutives(),
            'executivesGrouped' => \App\Services\ExecutiveService::getExecutivesGrouped(),
            'canonicalUrl' => url('/about', $locale),
            'alternateBn' => url('/about', 'bn'),
            'alternateEn' => url('/about', 'en'),
        ]);
    }

    public function activities(Request $request): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $title = $isBn ? 'কার্যক্রম ও সমাজসেবা মহাযজ্ঞ | এসপিএস' : 'Activities & Grassroots Seva';

        return $this->render('activities', [
            'metaTitle' => $title . ' — ' . config('app.short_name'),
            'metaDescription' => $isBn 
                ? 'এসপিএস-এর ২০২০ থেকে ২০২৬ সাল পর্যন্ত দেশব্যাপী সকল শাস্ত্রীয় প্রচার, মেধা বৃত্তি, মন্দির পুনর্নির্মাণ, পরিবেশ ও দুর্যোগ ত্রাণ কার্যক্রমের পূর্ণাঙ্গ ইতিবৃত্ত।' 
                : 'Complete chronological chronicle of SPS grassroots seva, temple restorations, disaster relief, merit scholarships, and scripture education across Bangladesh (2020-2026).',
            'activeNav' => 'activities',
            'activitiesByYear' => \App\Services\ActivityService::getActivitiesByYear(),
            'allActivities' => \App\Services\ActivityService::getActivities(),
            'flagships' => \App\Services\ActivityService::getFlagships(),
            'trackedProjects' => \App\Services\ActivityService::getTrackedProjects(),
            'stats' => \App\Services\ActivityService::getStats(),
            'canonicalUrl' => url('/activities', $locale),
            'alternateBn' => url('/activities', 'bn'),
            'alternateEn' => url('/activities', 'en'),
        ]);
    }

    public function knowledge(Request $request): Response
    {
        return $this->renderSection('knowledge', 'nav.knowledge', 'Sanatan Knowledge & Scriptures');
    }

    public function library(Request $request): Response
    {
        return $this->renderSection('library', 'nav.library', 'Scholarly Digital Library');
    }

    public function blog(Request $request): Response
    {
        return $this->renderSection('blog', 'nav.blog', 'Member Blog & Community');
    }

    public function getInvolved(Request $request): Response
    {
        return $this->renderSection('get-involved', 'nav.get_involved', 'Join SPS & Volunteer');
    }

    public function transparency(Request $request): Response
    {
        return $this->renderSection('transparency', 'nav.transparency', 'Financial Transparency');
    }

    public function contact(Request $request): Response
    {
        return $this->renderSection('contact', 'nav.contact', 'Institutional Contact');
    }

    private function renderSection(string $slug, string $navKey, string $defaultTitle): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $title = __('common.' . $navKey, $defaultTitle);

        return $this->render('section_preview', [
            'metaTitle' => $title . ' | ' . config('app.short_name'),
            'sectionSlug' => $slug,
            'sectionTitle' => $title,
            'activeNav' => $slug,
            'canonicalUrl' => url('/' . $slug, $locale),
            'alternateBn' => url('/' . $slug, 'bn'),
            'alternateEn' => url('/' . $slug, 'en'),
        ]);
    }
}
