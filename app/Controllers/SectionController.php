<?php

namespace App\Controllers;

use App\Core\I18n;
use App\Core\Request;
use App\Core\Response;

class SectionController extends BaseController
{
    public function about(Request $request): Response
    {
        return $this->renderSection('about', 'nav.about', 'About SPS');
    }

    public function activities(Request $request): Response
    {
        return $this->renderSection('activities', 'nav.activities', 'Activities & Seva');
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
