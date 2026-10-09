<?php

declare(strict_types=1);

namespace App\Services;

class HomepageService
{
    private static string $storagePath = '';
    private static string $settingsPath = '';
    private static ?array $cachedSections = null;
    private static ?array $cachedSettings = null;

    private static function getStoragePath(): string
    {
        if (empty(self::$storagePath)) {
            self::$storagePath = dirname(__DIR__, 2) . '/storage/data/homepage_sections.json';
        }
        return self::$storagePath;
    }

    private static function getSettingsPath(): string
    {
        if (empty(self::$settingsPath)) {
            self::$settingsPath = dirname(__DIR__, 2) . '/storage/data/site_settings.json';
        }
        return self::$settingsPath;
    }

    /**
     * Default 12 sections schema matching the SPS Homepage Final Guideline.
     */
    public static function getDefaultSections(): array
    {
        return [
            [
                'id' => 'hero',
                'key' => 'hero',
                'name_bn' => '১. হিরো সেকশন (শাস্ত্রের জ্ঞান থেকে মানবসেবার কর্মযাত্রা)',
                'name_en' => '1. Hero Section (From Knowledge to Action)',
                'enabled' => true,
                'order' => 1,
                'description_bn' => 'প্রধান শিরোনাম, দর্শন ও মানবসেবার ব্রতবাক্য, কল-টু-অ্যাকশন বোতাম এবং নেপথ্য বেদান্ত সূত্র।'
            ],
            [
                'id' => 'stats',
                'key' => 'stats',
                'name_bn' => '২. প্রভাব পরিসংখ্যান (সদস্য, গ্রন্থ, সেবা ও কেন্দ্র)',
                'name_en' => '2. Quick Impact Numbers (Members, Books, Seva & Centers)',
                'enabled' => true,
                'order' => 2,
                'description_bn' => 'সদস্য, শাস্ত্রীয় গ্রন্থ, সেবা-প্রাপ্ত মানুষ ও সক্রিয় পাঠশালা কেন্দ্রের লাইভ পরিসংখ্যান।'
            ],
            [
                'id' => 'about',
                'key' => 'about',
                'name_bn' => '৩. এসপিএস পরিচিতি (সংক্ষিপ্ত প্রাতিষ্ঠানিক পরিচয়)',
                'name_en' => '3. What is SPS? (Concise Institutional Overview)',
                'enabled' => true,
                'order' => 3,
                'description_bn' => 'শাশ্বত জ্ঞান ও সমকালীন মানবিক কর্মের মিলনস্থল হিসেবে এসপিএস-এর মূল লক্ষ্য।'
            ],
            [
                'id' => 'pillars',
                'key' => 'pillars',
                'name_bn' => '৪. আমরা কী করি? (SPS-এর ৪টি স্তম্ভ)',
                'name_en' => '4. What We Do (The Four Pillars of SPS)',
                'enabled' => true,
                'order' => 4,
                'description_bn' => 'জ্ঞান ও দর্শন, শাস্ত্র সংরক্ষণ, শিক্ষা ও মানবসেবা, এবং সমাজ ও সংস্কৃতি।'
            ],
            [
                'id' => 'activities',
                'key' => 'activities',
                'name_bn' => '৫. চলমান কার্যক্রম স্লাইডার (জ্ঞান থেকে কর্মে)',
                'name_en' => '5. Live Activities Slider (From Knowledge to Seva)',
                'enabled' => true,
                'order' => 5,
                'description_bn' => 'মাঠপর্যায়ে চলমান পাঠশালা, ভ্রাম্যমাণ স্বাস্থ্যসেবা ও সেবাকাজের ডায়নামিক স্লাইডার।'
            ],
            [
                'id' => 'knowledge',
                'key' => 'knowledge',
                'name_bn' => '৬. এসপিএস জ্ঞানভাণ্ডার (গীতা, উপনিষদ, বেদান্ত ও মহাফেজখানা)',
                'name_en' => '6. Knowledge & Scripture Gateway (Gita, Upanishads, Vedanta & Archives)',
                'enabled' => true,
                'order' => 6,
                'description_bn' => 'ভগবদ্গীতা, উপনিষদ, বেদান্ত দর্শন এবং ডিজিটাল পাণ্ডুলিপি মহাফেজখানায় প্রবেশের ৪টি মূল পোর্টাল।'
            ],
            [
                'id' => 'scripture',
                'key' => 'scripture',
                'name_bn' => '৭. আজকের শাস্ত্রীয় আলোকপাত (নির্বাচিত শ্লোক ও তাৎপর্য)',
                'name_en' => '7. Today\'s Scripture Spotlight (Featured Verse & Exegesis)',
                'enabled' => true,
                'order' => 7,
                'description_bn' => 'শ্রীমদ্ভগবদ্গীতার নির্বাচিত শ্লোক, দেবনাগরী ও বঙ্গানুবাদ এবং দার্শনিক তাৎপর্য।'
            ],
            [
                'id' => 'publications',
                'key' => 'publications',
                'name_bn' => '৮. সর্বশেষ প্রকাশনা ও গবেষণা (মিডিয়া লাইব্রেরি থেকে)',
                'name_en' => '8. Latest from SPS (Archival Publications & Research)',
                'enabled' => true,
                'order' => 8,
                'description_bn' => 'এসপিএস রামনবমী সংখ্যা, সমাচার এবং রামায়ণ কালনির্ণয়সহ লাইব্রেরির শীর্ষ প্রকাশনাসমূহ।'
            ],
            [
                'id' => 'blog',
                'key' => 'blog',
                'name_bn' => '৯. সদস্যদের চিন্তাধারা (কমিউনিটি প্রবন্ধ ও চিন্তাশীল লেখা)',
                'name_en' => '9. Members\' Voices & Journal (Scholarly & Community Essays)',
                'enabled' => true,
                'order' => 9,
                'description_bn' => 'সদস্য ও গবেষকদের চিন্তাশীল দর্শন ও ইতিহাস বিষয়ক প্রবন্ধের আধুনিক কার্ড।'
            ],
            [
                'id' => 'transparency',
                'key' => 'transparency',
                'name_bn' => '১০. আমাদের প্রভাব ও আর্থিক স্বচ্ছতা (আর্থিক অডিট ও অনুপাত)',
                'name_en' => '10. Impact & Financial Transparency (Audited Ledger & Allocation)',
                'enabled' => true,
                'order' => 10,
                'description_bn' => 'মোট প্রাপ্ত অনুদান, সেবায় ব্যয়, জরুরি তহবিল এবং খাতভিত্তিক ব্যয়ের প্রকাশ্য অনুপাত।'
            ],
            [
                'id' => 'community',
                'key' => 'community',
                'name_bn' => '১১. সোশ্যাল কমিউনিটি সংযোগ (ফেসবুক পেজ ও গ্রুপ)',
                'name_en' => '11. Social Community (Official Facebook Page & Group)',
                'enabled' => true,
                'order' => 11,
                'description_bn' => 'এসপিএস অফিসিয়াল ফেসবুক পেজ এবং ফেসবুক গ্রুপের সাথে যুক্ত হওয়ার আনুষ্ঠানিক আমন্ত্রণ।'
            ],
            [
                'id' => 'join',
                'key' => 'join',
                'name_bn' => '১২. এসপিএস-এ যুক্ত হোন (সদস্য, স্বেচ্ছাসেবক ও সহায়তা)',
                'name_en' => '12. Join SPS (Member, Volunteer & Donate Pathways)',
                'enabled' => true,
                'order' => 12,
                'description_bn' => 'সদস্যপদ গ্রহণ, স্বেচ্ছাসেবক হিসেবে যোগদান এবং সমাজসেবা প্রকল্পে অংশগ্রহণের তিনটি স্পষ্ট পথ।'
            ]
        ];
    }

    /**
     * Load sections list sorted by order.
     */
    public static function getSections(): array
    {
        if (self::$cachedSections !== null) {
            return self::$cachedSections;
        }

        $path = self::getStoragePath();
        if (!file_exists($path)) {
            $default = self::getDefaultSections();
            self::saveSections($default);
            self::$cachedSections = $default;
            return $default;
        }

        $json = @file_get_contents($path);
        $data = json_decode((string)$json, true);
        if (!is_array($data) || empty($data)) {
            $data = self::getDefaultSections();
            self::saveSections($data);
        }

        // Sort by 'order' ascending
        usort($data, static function ($a, $b) {
            return ($a['order'] ?? 0) <=> ($b['order'] ?? 0);
        });

        self::$cachedSections = $data;
        return $data;
    }

    /**
     * Get sections as an associative array keyed by section key for fast lookup.
     */
    public static function getSectionsMap(): array
    {
        $sections = self::getSections();
        $map = [];
        foreach ($sections as $sec) {
            $key = $sec['key'] ?? $sec['id'];
            $map[$key] = $sec;
        }
        return $map;
    }

    /**
     * Check if a specific section is enabled on the homepage.
     */
    public static function isSectionEnabled(string $key): bool
    {
        $map = self::getSectionsMap();
        if (!isset($map[$key])) {
            return true; // Default fallback to visible
        }
        return (bool)($map[$key]['enabled'] ?? true);
    }

    /**
     * Save updated sections array.
     */
    public static function saveSections(array $sections): bool
    {
        self::$cachedSections = $sections;
        $path = self::getStoragePath();
        $encoded = json_encode($sections, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return (bool)file_put_contents($path, $encoded);
    }

    /**
     * Update sections configuration from admin submission.
     */
    public static function updateSections(array $input, ?array $adminUser = null): bool
    {
        $currentSections = self::getSections();
        $updated = [];

        foreach ($currentSections as $sec) {
            $key = $sec['key'] ?? $sec['id'];
            if (isset($input[$key])) {
                $sec['enabled'] = !empty($input[$key]['enabled']);
                if (isset($input[$key]['order'])) {
                    $sec['order'] = (int)$input[$key]['order'];
                }
            } else {
                $sec['enabled'] = false;
            }
            $updated[] = $sec;
        }

        usort($updated, static function ($a, $b) {
            return ($a['order'] ?? 0) <=> ($b['order'] ?? 0);
        });

        return self::saveSections($updated);
    }

    /**
     * Load site settings (Statistics, Social Links, Featured Scripture, Transparency).
     */
    public static function getSiteSettings(): array
    {
        if (self::$cachedSettings !== null) {
            return self::$cachedSettings;
        }

        $path = self::getSettingsPath();
        if (file_exists($path)) {
            $raw = @file_get_contents($path);
            $decoded = json_decode((string)$raw, true);
            if (is_array($decoded) && !empty($decoded)) {
                self::$cachedSettings = $decoded;
                return self::$cachedSettings;
            }
        }

        $default = [
            'statistics' => [
                ['id' => 'stat_members', 'number' => '1,250+', 'number_bn' => '১,২৫০+', 'label_bn' => 'সদস্য ও গবেষক', 'label_en' => 'Members & Scholars', 'icon' => '👥', 'order' => 1],
                ['id' => 'stat_books', 'number' => '450+', 'number_bn' => '৪৫০+', 'label_bn' => 'গ্রন্থ ও পাণ্ডুলিপি', 'label_en' => 'Scriptures & Manuscripts', 'icon' => '📜', 'order' => 2],
                ['id' => 'stat_beneficiaries', 'number' => '18,000+', 'number_bn' => '১৮,০০০+', 'label_bn' => 'সেবা-প্রাপ্ত মানুষ', 'label_en' => 'Beneficiaries Served', 'icon' => '🤝', 'order' => 3],
                ['id' => 'stat_centers', 'number' => '5', 'number_bn' => '৫', 'label_bn' => 'চলমান পাঠশালা কেন্দ্র', 'label_en' => 'Active Vidyapeeth Centers', 'icon' => '🏫', 'order' => 4],
            ],
            'social_links' => [
                'facebook_page' => 'https://www.facebook.com/bewithsps?utm_source=chatgpt.com',
                'facebook_group' => 'https://www.facebook.com/groups/278337526756568?utm_source=chatgpt.com',
                'youtube' => 'https://www.youtube.com/@spsofficial1529',
                'blogspot' => 'https://sanatanphilosophyandscripture.blogspot.com',
            ],
            'featured_scripture' => [
                'source_book_bn' => 'শ্রীমদ্ভগবদ্গীতা',
                'source_book_en' => 'Srimad Bhagavad Gita',
                'verse_no' => '২.৪৭ (Chapter 2, Verse 47)',
                'sanskrit_devanagari' => "कर्मण्येवाधिकारस्ते मा फलेषु कदाचन ।\nमा कर्मफलहेतुर्भूर्मा ते सङ्गोऽस्त्वकर्मणि ॥",
                'sanskrit_bengali' => "কর্মণ্যেবাধিকারস্তে মা ফলেষু কদাচন ।\nমা কর্মফলহেতুর্ভূর্মা তে সঙ্গোঽস্ত্বকর্মাণি ॥",
                'translation_bn' => 'কর্মে তোমার অধিকার আছে, কিন্তু তার ফলে কখনো নয়। কর্মফলের প্রতি যেন তোমার আসক্তি না থাকে, আবার কর্মত্যাগেও যেন প্রবৃত্তি না হয়।',
                'translation_en' => 'You have a right only to work, never to its fruits; let not the fruits of action be your motive, nor let there be any attachment to inaction.',
                'exegesis_bn' => 'নিষ্কাম কর্মযোগের মূল ভিত্তি। ফলাফল বা ব্যক্তিগত লাভের প্রত্যাশা ত্যাগ করে নৈতিক ও আধ্যাত্মিক দায়িত্ব পালনের আহ্বান, যা এসপিএস-এর সকল সেবাকর্মের মূল প্রেরণা।',
                'exegesis_en' => 'The foundational principle of Nishkama Karma Yoga—selfless action devoid of ego and desire for reward, which forms the ethical heartbeat of SPS humanitarian initiatives.',
            ],
            'transparency' => [
                'total_received_bn' => '৳ ৪৫,৮০,০০০',
                'total_received_en' => '৳ 4,580,000',
                'total_spent_bn' => '৳ ৩৮,১০,০০০',
                'total_spent_en' => '৳ 3,810,000',
                'emergency_reserve_bn' => '৳ ৭,৭০,০০০',
                'emergency_reserve_en' => '৳ 770,000',
                'allocations' => [
                    ['name_bn' => 'শিক্ষা ও পাঠশালা', 'name_en' => 'Education & Vidyapeeth', 'percentage' => 35, 'color' => 'var(--accent-saffron)'],
                    ['name_bn' => 'স্বাস্থ্যসেবা ও পুনর্বাসন', 'name_en' => 'Healthcare & Relief', 'percentage' => 28, 'color' => 'var(--status-success)'],
                    ['name_bn' => 'শাস্ত্রীয় গবেষণা ও লাইব্রেরি', 'name_en' => 'Scriptures & Library', 'percentage' => 24, 'color' => 'var(--accent-gold)'],
                    ['name_bn' => 'প্রশাসন ও পরিচালনা', 'name_en' => 'Administration & Operations', 'percentage' => 13, 'color' => 'var(--accent-brown)'],
                ],
            ],
        ];

        file_put_contents($path, json_encode($default, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        self::$cachedSettings = $default;
        return $default;
    }

    public static function getSiteStatistics(): array
    {
        $settings = self::getSiteSettings();
        return $settings['statistics'] ?? [];
    }

    public static function getFeaturedScripture(): array
    {
        $settings = self::getSiteSettings();
        return $settings['featured_scripture'] ?? [];
    }

    public static function getTransparencySummary(): array
    {
        $settings = self::getSiteSettings();
        return $settings['transparency'] ?? [];
    }

    public static function getSocialLinks(): array
    {
        $settings = self::getSiteSettings();
        return $settings['social_links'] ?? [
            'facebook_page' => 'https://www.facebook.com/bewithsps?utm_source=chatgpt.com',
            'facebook_group' => 'https://www.facebook.com/groups/278337526756568?utm_source=chatgpt.com',
        ];
    }
}
