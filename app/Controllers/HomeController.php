<?php

namespace App\Controllers;

use App\Core\I18n;
use App\Core\Request;
use App\Core\Response;

class HomeController extends BaseController
{
    public function index(Request $request): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $title = $isBn 
            ? 'সনাতন দর্শন ও শাস্ত্র (SPS) — সনাতনী ঐক্য, প্রচার ও কল্যাণে অবিচল'
            : 'Sanatan Philosophy and Scripture (SPS) — Steadfast in Sanatan Unity, Propagation & Welfare';

        $description = $isBn
            ? 'বেদান্ত, উপনিষদ ও শাশ্বত সনাতন দর্শনের প্রামাণিক সংরক্ষণ, গভীর তাত্ত্বিক পর্যালোচনা এবং আর্তমানবতার নিঃস্বার্থ সেবায় নিবেদিত এক মানবিক বিদ্যাপীঠ।'
            : 'Dedicated to the rigorous preservation of Vedanta, Upanishadic exegesis, and timeless Sanatan philosophy, coupled with selfless social service for humanity.';

        return $this->render('home', [
            'metaTitle' => $title,
            'metaDescription' => $description,
            'activeNav' => 'home',
            'canonicalUrl' => url('/', $locale),
            'alternateBn' => url('/', 'bn'),
            'alternateEn' => url('/', 'en'),
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
