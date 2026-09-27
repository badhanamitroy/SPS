<?php

declare(strict_types=1);

namespace App\Services;

class ActivityService
{
    private static ?array $activities = null;
    private static ?array $flagships = null;
    private static ?array $trackedProjects = null;

    /**
     * Get all structured year-wise activities extracted from official SPS Project Logs.
     */
    private static function getStoragePath(): string
    {
        return dirname(__DIR__, 2) . '/storage/data/activities.json';
    }

    /**
     * Get all structured year-wise activities extracted from official SPS Project Logs.
     */
    public static function getActivities(): array
    {
        if (self::$activities !== null) {
            return self::$activities;
        }

        $path = self::getStoragePath();
        if (file_exists($path)) {
            $data = json_decode((string)file_get_contents($path), true);
            if (is_array($data) && !empty($data)) {
                self::$activities = $data;
                return self::$activities;
            }
        }

        self::$activities = self::getDefaultActivities();
        self::saveActivities(self::$activities);
        return self::$activities;
    }

    public static function getDefaultActivities(): array
    {
        return [
            // 2026
            [
                'id' => 'act-2026-medical',
                'year' => 2026,
                'date' => '2026',
                'category' => 'health',
                'category_bn' => 'চিকিৎসা ও পুনর্বাসন',
                'category_en' => 'Health & Medical Aid',
                'title_bn' => 'অসুস্থ গৌরাঙ্গ চন্দ্র শীলের জরুরি চিকিৎসা ও পুনর্বাসন সহায়তা',
                'title_en' => 'Emergency Medical Treatment Support for Gouranga Chandra Shill',
                'location_bn' => 'বাংলাদেশ',
                'location_en' => 'Bangladesh',
                'status' => 'in_progress',
                'status_bn' => 'চলমান কার্যক্রম',
                'status_en' => 'Active Drive',
                'description_bn' => 'লিভার ও কিডনির গুরুতর সমস্যায় আক্রান্ত সংকটাপন্ন গৌরাঙ্গ চন্দ্র শীলের জীবন বাঁচাতে ও চিকিৎসা ব্যয় বহনে এসপিএস পরিবারের পক্ষ থেকে বিশেষ জরুরি চিকিৎসা সহায়তা তহবিল পরিচালনা।',
                'description_en' => 'Emergency medical relief drive mobilized by SPS to finance life-saving treatments for Gouranga Chandra Shill, who is critically battling severe liver and kidney complications.',
                'highlights_bn' => ['জীবনরক্ষাকারী চিকিৎসা ব্যয় অনুদান', 'এসপিএস ওয়েলফেয়ার ট্রাস্ট ও শুভানুধ্যায়ী সমন্বয়'],
                'highlights_en' => ['Life-saving treatment fund mobilization', 'Coordinated by SPS Welfare Trust & community'],
                'badge' => '2026 Priority',
                'icon' => '🩺',
            ],

            // 2025
            [
                'id' => 'act-2025-nilphamari-temple',
                'year' => 2025,
                'date' => '2025',
                'category' => 'temple',
                'category_bn' => 'মন্দির ও ঐতিহ্য সংরক্ষণ',
                'category_en' => 'Temple & Heritage Preservation',
                'title_bn' => 'নীলফামারী ডোমার শ্রী শ্রী শ্যামা মন্দির নির্মাণ সমাপ্তিকরণ',
                'title_en' => 'Completion of Sri Sri Shyama Temple at Domar, Nilphamari',
                'location_bn' => 'বোড়াগাড়ী, ডোমার, নীলফামারী',
                'location_en' => 'Boragari, Domar, Nilphamari',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => 'আঞ্চলিক ধর্মান্তর প্রতিরোধ ও স্থানীয় সনাতন সমাজের সাংস্কৃতিক নিরাপত্তায় শ্রীমতী গীতা দেবনাথের পৃষ্ঠপোষকতায় এবং "ভালোবাসার উদ্যোগ"-এর সাথে যৌথ বাস্তবায়নে শ্যামা মন্দির নির্মাণ সম্পন্ন।',
                'description_en' => 'Constructed a permanent community temple in Boragari to counter missionary conversion pressures and strengthen local cultural identity, sponsored by Srimati Gita Devnath and implemented jointly with Bhalobashar Udyog.',
                'highlights_bn' => ['স্থায়ী বিগ্রহ প্রতিষ্ঠা ও নাটমন্দির', 'ধর্মীয় ও সামাজিক মিলনকেন্দ্র প্রতিষ্ঠা'],
                'highlights_en' => ['Permanent sanctum & prayer hall', 'Community sanctuary against religious exploitation'],
                'badge' => 'Heritage',
                'icon' => '🛕',
            ],
            [
                'id' => 'act-2025-barishal-wedding',
                'year' => 2025,
                'date' => '10 June 2025',
                'category' => 'livelihood',
                'category_bn' => 'সামাজিক সহায়তা ও কল্যাণ',
                'category_en' => 'Social Welfare & Empowerment',
                'title_bn' => 'বরিশালের অসহায় সনাতনী বোনের বিবাহে পূর্ণাঙ্গ আর্থিক সহায়তা',
                'title_en' => 'Comprehensive Wedding Assistance for Vulnerable Sister in Barishal',
                'location_bn' => 'বরিশাল জেলা',
                'location_en' => 'Barishal District',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => 'পিতৃমাতৃহীন এবং চরম অর্থনৈতিক অনটনে থাকা বরিশালের এক সনাতনী বোনের সম্মানজনক বিবাহ সম্পন্ন করতে এসপিএস ওয়েলফেয়ার ট্রাস্টের মাধ্যমে প্রয়োজনীয় বস্ত্র, অলংকার ও বিয়ের সম্পূর্ণ খরচ প্রদান।',
                'description_en' => 'SPS Welfare Trust fully sponsored the wedding expenses, traditional garments, and household necessities for an underprivileged orphan Sanatani sister in Barishal.',
                'highlights_bn' => ['সম্পূর্ণ বিবাহ ব্যয়ভার বহন', 'মর্যাদাপূর্ণ সামাজিক প্রতিষ্ঠা'],
                'highlights_en' => ['100% wedding expenses sponsored', 'Ensuring dignity and family stability'],
                'badge' => 'Welfare',
                'icon' => '🤝',
            ],
            [
                'id' => 'act-2025-sirajganj-rehab',
                'year' => 2025,
                'date' => '2025',
                'category' => 'livelihood',
                'category_bn' => 'স্বাবলম্বীকরণ ও জীবিকা',
                'category_en' => 'Livelihood & Self-Reliance',
                'title_bn' => 'সিরাজগঞ্জে জন্মপ্রতিবন্ধী পিসি ও নিঃসন্তান ভাইঝির স্থায়ী পুনর্বাসন',
                'title_en' => 'Sustainable Rehabilitation of Blind Aunt & Orphaned Niece in Sirajganj',
                'location_bn' => 'সিরাজগঞ্জ',
                'location_en' => 'Sirajganj',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => 'ভিক্ষাবৃত্তি নির্মূল করে আত্মমর্যাদা প্রতিষ্ঠার লক্ষ্যে দৃষ্টিপ্রতিবন্ধী পিসি ও তাঁর নিঃসন্তান ভাইঝিকে ৩ মাসের খাদ্যসামগ্রী, শীতবস্ত্র, গৃহ সংস্কার এবং জীবিকার জন্য ক্ষুদ্র ব্যবসা প্রতিষ্ঠান স্থাপন করে দেওয়া হয়।',
                'description_en' => 'Eradicated begging for a congenitally visually impaired elder aunt and her vulnerable niece by providing 3 months of food rations, home repairs, winter clothes, and seed capital for a sustainable micro-business.',
                'highlights_bn' => ['ভিক্ষাবৃত্তি থেকে স্থায়ী মুক্তি', 'গৃহ সংস্কার ও ক্ষুদ্র ব্যবসা স্থাপন', '৩ মাসের খাদ্য নিশ্চয়তা'],
                'highlights_en' => ['Eliminated begging with dignity', 'Home reconstruction & shop setup', '3-month nutritional security'],
                'badge' => 'Self-Reliance',
                'icon' => '💼',
            ],

            // 2024
            [
                'id' => 'act-2024-tree-plantation',
                'year' => 2024,
                'date' => '28 March 2024',
                'category' => 'nature',
                'category_bn' => 'পরিবেশ ও বৃক্ষরোপণ',
                'category_en' => 'Nature & Environment',
                'title_bn' => 'রাম নবমীতে দেশব্যাপী বৃক্ষরোপণ মহোৎসব: "একটি গাছ, একটি প্রাণ"',
                'title_en' => 'Nationwide Ram Navami Tree Plantation Drive: "One Tree, One Life"',
                'location_bn' => '৩০+ জেলা (মেধস মুনির আশ্রম, মীরসরাই তীর্থ ইত্যাদি)',
                'location_en' => '30+ Districts (Medhas Munir Ashram, Mirsarai, etc.)',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => 'পবিত্র শ্রী রাম নবমী উপলক্ষে প্রকৃতি সংরক্ষণ ও বৈদিক পরিবেশ সুরক্ষার অংশ হিসেবে বাংলাদেশের ৩০টিরও অধিক জেলায় সাড়ে ৯ হাজারেরও বেশি ফলজ, বনজ ও ঔষধি বৃক্ষ রোপণ ও বিতরণ।',
                'description_en' => 'On the auspicious occasion of Sri Ram Navami, SPS organized an unprecedented environmental initiative planting and distributing over 9,500 medicinal, timber, and fruit trees across 30+ districts.',
                'highlights_bn' => ['৯,৫০০+ ফলজ ও ঔষধি বৃক্ষরোপণ', '৩০টির অধিক জেলায় স্বেচ্ছাসেবক কার্যক্রম', 'ঐতিহাসিক তীর্থক্ষেত্রে বৃক্ষরোপণ'],
                'highlights_en' => ['9,500+ trees planted & distributed', 'Active across 30+ districts nationwide', 'Green coverage for historic pilgrimage shrines'],
                'badge' => '9,500+ Trees',
                'icon' => '🌱',
            ],
            [
                'id' => 'act-2024-kantaji-gita',
                'year' => 2024,
                'date' => '27 April 2024',
                'category' => 'shastra',
                'category_bn' => 'শাস্ত্র ও ধর্মীয় সম্মেলন',
                'category_en' => 'Scripture & Pilgrimage Seva',
                'title_bn' => 'ঐতিহাসিক কান্তজিউ মন্দিরে ২০,০০০+ গীতা পাঠকদের মাঝে সেবা প্রদান',
                'title_en' => 'Seva Drive for 20,000+ Gita Reciters at Historic Kantajew Temple',
                'location_bn' => 'কান্তজিউ মন্দির, কাহারোল, দিনাজপুর',
                'location_en' => 'Kantajew Temple, Kaharole, Dinajpur',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => 'দিনাজপুরের ঐতিহাসিক কান্তজিউ মন্দির প্রাঙ্গণে লক্ষাধিক ভক্তের উপস্থিতিতে অনুষ্ঠিত গীতা পাঠ অনুষ্ঠানে এসপিএস উত্তরবঙ্গ স্কোয়াডের পক্ষ থেকে ২০,০০০+ পুণ্যার্থীদের সুপেয় পানি, খাবার স্যালাইন ও ধর্মীয় পুস্তিকা বিতরণ।',
                'description_en' => 'SPS North Bengal Volunteer Squad managed comprehensive field hospitality for over 20,000 Gita reciters and pilgrims at the historic Kantajew Temple, distributing chilled drinking water, ORS saline packets, and religious reading booklets.',
                'highlights_bn' => ['২০,০০০+ গীতা পাঠকারী পুণ্যার্থী সেবা', 'তীব্র তাপদাহে খাবার স্যালাইন ও বিশুদ্ধ পানি', 'ধর্মীয় প্রচার পুস্তিকা বিতরণ'],
                'highlights_en' => ['Served 20,000+ devotional reciters', 'Heatstroke mitigation with ORS saline & water', 'Educational booklets distributed'],
                'badge' => '20,000+ Pilgrims',
                'icon' => '📖',
            ],
            [
                'id' => 'act-2024-chandranath-seva',
                'year' => 2024,
                'date' => 'Jan - March 2024',
                'category' => 'temple',
                'category_bn' => 'তীর্থ সেবা ও চিকিৎসা',
                'category_en' => 'Pilgrim Seva & Medical',
                'title_bn' => 'চন্দ্রনাথ ধাম মহাশিবরাত্রি সেবা ক্যাম্প, ফ্রি মেডিকেল ও ইতিহাসগ্রন্থ প্রকাশনা',
                'title_en' => 'Chandranath Dham Maha Shivaratri Seva, Free Medical Camp & History Book Launch',
                'location_bn' => 'চন্দ্রনাথ ধাম, সীতাকুণ্ড, চট্টগ্রাম',
                'location_en' => 'Chandranath Dham, Sitakunda, Chattogram',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => '১০০+ স্বেচ্ছাসেবক দ্বারা চন্দ্রনাথ ধামে ১২ ফুট মাহাত্ম্য ব্যানার স্থাপন, সার্বক্ষণিক পরিচ্ছন্নতা রক্ষায় ২০টি স্থায়ী ডাস্টবিন স্থাপন, শত শত পুণ্যার্থীর বিনামূল্যে চিকিৎসা প্রদান এবং এসপিএস শাস্ত্রীয় গবেষণা পরিষদ রচিত ঐতিহাসিক গ্রন্থ "চন্দ্রনাথ ধামের ইতিহাস কথা" প্রকাশ।',
                'description_en' => 'Mobilized 100+ volunteers for sanitation and safety at Chandranath Dham, installed 20+ waste dustbins, conducted a 24/7 free medical camp treating hundreds of mountain pilgrims, and published the authoritative research book "Chandranath Dhamer Itihas Katha".',
                'highlights_bn' => ['১০০+ সক্রিয় স্বেচ্ছাসেবক দল', 'বিনামূল্যে ওষুধ ও চিকিৎসক সেবা', 'চন্দ্রনাথ তীর্থযাত্রী বিশ্রামাগার উদ্বোধন'],
                'highlights_en' => ['100+ active field volunteers', 'Free doctor consultations & emergency medications', 'Advocated & opened permanent pilgrim rest shelter'],
                'badge' => 'Mega Seva',
                'icon' => '🕉️',
            ],
            [
                'id' => 'act-2024-flood-relief',
                'year' => 2024,
                'date' => 'August - September 2024',
                'category' => 'humanitarian',
                'category_bn' => 'দুর্যোগ ও বন্যা ত্রাণ',
                'category_en' => 'Disaster & Flood Relief',
                'title_bn' => '২০২৪ দেশের পূর্বাঞ্চল ও দক্ষিণাঞ্চল ভয়াবহ বন্যা ত্রাণ ও পুনর্বাসন',
                'title_en' => '2024 Devastating Flood Relief & Post-Disaster Rehabilitation',
                'location_bn' => 'ফেনী (ফাজিলপুর), কুমিল্লা (বুড়িচং), লক্ষ্মীপুর (চন্দ্রগঞ্জ), খুলনা',
                'location_en' => 'Feni (Fajilpur), Comilla (Burichang), Lakshmipur, Khulna',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => 'ভয়াবহ বন্যায় পানিবন্দি হাজারো পরিবারের মাঝে শুকনো খাবার, বিশুদ্ধ পানি ও জরুরি ওষুধ সরবরাহ। বন্যা পরবর্তী সময়ে ফাজিলপুরে নগদ পুনর্বাসন অর্থ প্রদান এবং খুলনার ৩৫০ পরিবারকে পুষ্টিকর খাদ্য প্যাকেজ বিতরণ।',
                'description_en' => 'Emergency boat relief and disaster supplies deployed across severely inundated flood districts in Feni, Comilla, Lakshmipur, and Khulna. Followed by direct cash rehabilitation grants in Fajilpur and food packages to 350 families in Khulna.',
                'highlights_bn' => ['ফাজিলপুরে নগদ অর্থ পুনর্বাসন', 'খুলনায় ৩৫০ পরিবারে খাদ্য প্যাকেজ', 'লক্ষ্মীপুর ও কুমিল্লায় জরুরি ত্রাণ'],
                'highlights_en' => ['Direct cash rehabilitation in Fajilpur', '350 family ration kits in Khulna', 'Emergency boat relief in Comilla & Lakshmipur'],
                'badge' => 'Emergency Relief',
                'icon' => '🌊',
            ],
            [
                'id' => 'act-2024-project-nb',
                'year' => 2024,
                'date' => '2024',
                'category' => 'humanitarian',
                'category_bn' => 'মানবাধিকার ও গৃহ নির্মাণ',
                'category_en' => 'Human Rights & Rehabilitation',
                'title_bn' => 'প্রজেক্ট এনবি: রংপুরে অগ্নিসংযোগে ক্ষতিগ্রস্ত ৫টি সনাতনী পরিবারের গৃহ পুনর্নির্মাণ',
                'title_en' => 'Project NB: Rebuilding Homes for 5 Arson-Affected Hindu Families in Rangpur',
                'location_bn' => 'মিঠাপুকুর, রংপুর',
                'location_en' => 'Mithapukur, Rangpur',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => 'রংপুরের মিঠাপুকুরে সাম্প্রদায়িক অগ্নিসংযোগে সর্বস্ব হারানো ৫টি নিরপরাধ সনাতনী পরিবারের ঘরবাড়ি সম্পূর্ণ নতুন করে নির্মাণ এবং নিত্যপ্রয়োজনীয় তৈজসপত্র সরবরাহ করে তাদের পুনর্বাসিত করা হয়।',
                'description_en' => 'Reconstructed housing from the ground up for 5 innocent Hindu families whose dwellings were gutted in communal arson in Mithapukur, Rangpur, providing new tin roofs, bedding, and kitchenware.',
                'highlights_bn' => ['৫টি সম্পূর্ণ গৃহ পুনর্নির্মাণ', 'সম্পূর্ণ অর্থায়ন ও সামগ্রী সহায়তা', 'উত্তরবঙ্গে সুরক্ষা ও মনোবল বৃদ্ধি'],
                'highlights_en' => ['5 complete homes rebuilt', 'Full financing & construction materials', 'Restoring security and communal resilience'],
                'badge' => 'Rehabilitation',
                'icon' => '🏡',
            ],
            [
                'id' => 'act-2024-anti-conversion',
                'year' => 2024,
                'date' => '2024',
                'category' => 'shastra',
                'category_bn' => 'সচেতনতা ও প্রতিরোধ',
                'category_en' => 'Anti-Conversion & Rights Advocacy',
                'title_bn' => 'দেশব্যাপী ধর্মান্তর প্রতিরোধ লিফলেট বিতরণ ও ৮ দফা দাবির সমর্থনে সমাবেশ',
                'title_en' => 'Nationwide Anti-Conversion Awareness Leaflet Campaign & 8-Point Demand Rallies',
                'location_bn' => 'খুলনা, শাহজাদপুর, গাইবান্ধা, সুনামগঞ্জ, কেশবপুর',
                'location_en' => 'Khulna, Shahjadpur, Gaibandha, Sunamganj, Keshabpur',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => 'প্রতারণামূলক ধর্মান্তরকরণ রোধে সাধারণ মানুষের মাঝে বিনামূল্যে সচেতনতামূলক প্রকাশনা বিতরণ এবং বাংলাদেশের সনাতন সমাজের সাংবিধানিক নিরাপত্তা ও ধর্মীয় অধিকার রক্ষায় ৮ দফা দাবির আন্দোলনে জোরালো অংশগ্রহণ।',
                'description_en' => 'Mass distribution of free anti-conversion educational leaflets across vulnerable sub-districts and proactive civic participation in rallies advocating the 8-point constitutional rights charter for minorities.',
                'highlights_bn' => ['হাজার হাজার তথ্যবহুল লিফলেট বিতরণ', 'প্রান্তিক জনপদে গণসচেতনতা তৈরি', '৮ দফা দাবির প্রতি সার্বিক সমর্থন'],
                'highlights_en' => ['Thousands of awareness leaflets distributed', 'Grassroots vigilance in vulnerable regions', 'Support for the 8-point minority rights charter'],
                'badge' => 'Advocacy',
                'icon' => '📢',
            ],

            // 2023
            [
                'id' => 'act-2023-ghar-wapsi',
                'year' => 2023,
                'date' => '27 October 2023',
                'category' => 'shastra',
                'category_bn' => 'বৈদিক প্রত্যাবর্তন ও যজ্ঞ',
                'category_en' => 'Vedic Re-conversion & Yajna',
                'title_bn' => 'মানিকছড়িতে বৈদিক যজ্ঞের মাধ্যমে ১০০ জন ত্রিপুরা জনগোষ্ঠীর সনাতন ধর্মে প্রত্যাবর্তন',
                'title_en' => 'Ghar Wapsi: Welcoming 100 Indigenous Tripura Members Back to Sanatan Dharma',
                'location_bn' => 'মানিকছড়ি, খাগড়াছড়ি পার্বত্য জেলা',
                'location_en' => 'Manikchhari, Khagrachhari Hill Tracts',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => 'খাগড়াছড়ির মানিকছড়িতে বিভিন্ন সময়ে প্ররোচনার শিকার হয়ে ধর্মান্তরিত হওয়া ১০০ জন আদিবাসী ত্রিপুরা সম্প্রদায়ের মানুষকে বৈদিক যজ্ঞ, শুদ্ধিকরণ আচার ও হরিনাম সংকীর্তনের মাধ্যমে সগৌরবে সনাতন ধর্মে ফিরিয়ে আনা হয়।',
                'description_en' => 'Historic reconversion ceremony in Manikchhari where 100 indigenous Tripura community members who had earlier converted to other faiths were respectfully brought back to Sanatan Dharma through sacred Vedic fire rituals and scriptural blessings.',
                'highlights_bn' => ['১০০ জন আদিবাসীর ধর্মে প্রত্যাবর্তন', 'বৈদিক হোম-যজ্ঞ ও নামমালা প্রদান', 'গীতা ও ধর্মগ্রন্থ উপহার প্রদান'],
                'highlights_en' => ['100 indigenous individuals reunited', 'Traditional Vedic Yajna & purification rites', 'Awarded copies of Bhagavad Gita & spiritual books'],
                'badge' => 'Ghar Wapsi',
                'icon' => '🔥',
            ],
            [
                'id' => 'act-2023-keraniganj-shop',
                'year' => 2023,
                'date' => '8 March 2023',
                'category' => 'livelihood',
                'category_bn' => 'স্বাবলম্বীকরণ ও জীবিকা',
                'category_en' => 'Livelihood & Self-Reliance',
                'title_bn' => 'দক্ষিণ কেরানীগঞ্জে পলাশ শর্মাকে আধুনিক চায়ের দোকান তৈরি করে স্বাবলম্বী করা',
                'title_en' => 'Establishment of Modern Tea Stall for Palash Sharma in South Keraniganj',
                'location_bn' => 'দক্ষিণ কেরানীগঞ্জ, ঢাকা',
                'location_en' => 'South Keraniganj, Dhaka',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => 'অর্থনৈতিকভাবে চরম বিপর্যস্ত পলাশ শর্মাকে আত্মনির্ভর করতে এসপিএস-এর অর্থায়নে দোকান ভ্যান নির্মাণ, রান্নার সিলিন্ডার ও সকল প্রারম্ভিক কাঁচামাল সহ একটি স্বয়ংসম্পূর্ণ চায়ের দোকান চালু করে দেওয়া হয়।',
                'description_en' => 'Funded the complete construction and raw material stocking of a commercial tea stall for Palash Sharma in Keraniganj, transforming a struggling family into self-reliant small entrepreneurs.',
                'highlights_bn' => ['সম্পূর্ণ দোকান অবকাঠামো নির্মাণ', 'ব্যবসায়িক সামগ্রী ও গ্যাস সিলিন্ডার প্রদান', 'স্থায়ী উপার্জনের নিশ্চয়তা'],
                'highlights_en' => ['Full stall infrastructure built', 'Equipped with gas cylinders & inventory', 'Guaranteed sustainable daily livelihood'],
                'badge' => 'Self-Reliance',
                'icon' => '☕',
            ],
            [
                'id' => 'act-2023-scholarship-buet',
                'year' => 2023,
                'date' => '2023',
                'category' => 'shastra',
                'category_bn' => 'শিক্ষা ও মেধা বৃত্তি',
                'category_en' => 'Education & Merit Scholarships',
                'title_bn' => 'বুয়েটে চান্সপ্রাপ্ত মেধাবী শিক্ষার্থী বিষ্ণুকে ২৯তম এসপিএস মেধা বৃত্তি প্রদান',
                'title_en' => '29th SPS Merit Scholarship Awarded to BUET Entrant Bishnu',
                'location_bn' => 'বুয়েট, ঢাকা',
                'location_en' => 'BUET, Dhaka',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => 'চরম আর্থিক প্রতিকূলতা জয় করে বাংলাদেশ প্রকৌশল বিশ্ববিদ্যালয়ে (বুয়েট) সুযোগ পাওয়া মেধাবী ছাত্র বিষ্ণুকে শিক্ষাবৃত্তি এবং ৫টি আধ্যাত্মিক ও আত্মউন্নয়নমূলক গ্রন্থ উপহার প্রদান।',
                'description_en' => 'Awarded the 29th prestigious SPS Merit Scholarship to Bishnu upon his admission into Bangladesh University of Engineering and Technology (BUET), complemented with personal development and Vedic philosophy texts.',
                'highlights_bn' => ['বুয়েট ছাত্রের উচ্চশিক্ষা সহায়তা', '২৯তম মেধা বৃত্তি স্মারক', 'সনাতন দর্শনের নির্বাচিত গ্রন্থ প্রদান'],
                'highlights_en' => ['BUET engineering tuition grant', '29th milestone merit scholarship', 'Curated philosophical library'],
                'badge' => 'Scholarship #29',
                'icon' => '🎓',
            ],
            [
                'id' => 'act-2023-mymensingh-orphan',
                'year' => 2023,
                'date' => '8 November 2023',
                'category' => 'livelihood',
                'category_bn' => 'আশ্রয় ও সহায়তা',
                'category_en' => 'Shelter & Humanitarian Aid',
                'title_bn' => 'ময়মনসিংহে গৃহহীন অসহায় প্রবীণ মাকে নতুন বাসস্থান ও সেলাই মেশিন প্রদান',
                'title_en' => 'New Housing & Sewing Machine for Homeless Elder Mother in Mymensingh',
                'location_bn' => 'ময়মনসিংহ',
                'location_en' => 'Mymensingh',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => 'ময়মনসিংহে আশ্রয়হীন এক বৃদ্ধা সনাতনী মাকে নতুন ভাড়া বাসায় স্থানান্তর, এক মাসের পূর্ণাঙ্গ খাদ্য রসদ ও স্থায়ী জীবিকা অর্জনের সুবিধার্থে একটি সেলাই মেশিন উপহার দেওয়া হয়।',
                'description_en' => 'Provided safe residential accommodation, 1-month bulk groceries, and a brand-new sewing machine to an elderly destitute woman in Mymensingh to restore her livelihood and security.',
                'highlights_bn' => ['নিরাপদ বাসস্থান ব্যবস্থা', '১ মাসের শুকনো খাবার ও রসদ', 'সেলাই মেশিন উপহার'],
                'highlights_en' => ['Safe housing secured', '1-month food supplies', 'Heavy-duty sewing machine gifted'],
                'badge' => 'Social Care',
                'icon' => '🧵',
            ],
            [
                'id' => 'act-2023-kurigram-clothes',
                'year' => 2023,
                'date' => '27 September 2023',
                'category' => 'humanitarian',
                'category_bn' => 'বস্ত্র বিতরণ ও সেবা',
                'category_en' => 'Clothing Drive & Community Care',
                'title_bn' => 'কুড়িগ্রামে অসহায় প্রবীণ মায়েদের মাঝে নতুন বস্ত্র বিতরণ',
                'title_en' => 'Festive New Clothes Distribution for Impoverished Mothers in Kurigram',
                'location_bn' => 'কুড়িগ্রাম জেলা',
                'location_en' => 'Kurigram District',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => 'শারদীয় দুর্গোৎসবের প্রাক্কালে উত্তরা রোটারেক্ট ক্লাবের সাথে যৌথ উদ্যোগে কুড়িগ্রামের দুর্গম চরাঞ্চলের প্রবীণ মায়েদের মাঝে নতুন শাড়ি ও উপহার সামগ্রী বিতরণ।',
                'description_en' => 'Distributed festive sarees and gift bundles to dozens of elderly, destitute mothers in remote river-island areas of Kurigram in collaboration with Rotary Club of Abahanikunj.',
                'highlights_bn' => ['দুর্গম চরাঞ্চলে সেবা', 'নতুন শাড়ি ও পুষ্টিকর খাদ্য সামগ্রী'],
                'highlights_en' => ['Remote island riverbanks', 'Traditional sarees & nourishment gifts'],
                'badge' => 'Charity',
                'icon' => '👗',
            ],

            // 2022
            [
                'id' => 'act-2022-coxsbazar-widows',
                'year' => 2022,
                'date' => '3 March 2022',
                'category' => 'livelihood',
                'category_bn' => 'স্বাবলম্বীকরণ ও ভুক্তভোগী পুনর্বাসন',
                'category_en' => 'Victim Rehabilitation & Empowerment',
                'title_bn' => 'কক্সবাজার ট্র্যাজেডিতে নিহত ৬ হিন্দু ভাইয়ের বিধবাদের ৬টি সেলাই মেশিন উপহার',
                'title_en' => '6 Sewing Machines Gifted to 6 Widows of Slain Hindu Brothers in Cox\'s Bazar',
                'location_bn' => 'চকোরিয়া, কক্সবাজার',
                'location_en' => 'Chakaria, Cox\'s Bazar',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => 'কক্সবাজারের চকরিয়ায় পিকআপ চাপা দিয়ে মর্মান্তিকভাবে নিহত সনাতনী ভাইদের ৬ জন বিধবা স্ত্রীকে সম্মানজনকভাবে সন্তানদের ভরণপোষণ ও আয়ের পথ নিশ্চিত করতে ৬টি নতুন সেলাই মেশিন প্রদান করা হয়।',
                'description_en' => 'Provided 6 commercial sewing machines to the grieving widows of the Hindu siblings tragically killed in Chakaria, Cox\'s Bazar, equipping each household with an independent, dignified income source.',
                'highlights_bn' => ['৬ জন বিধবাকে সেলাই মেশিন প্রদান', 'পারিবারিক অর্থনৈতিক নিরাপত্তা বিধান', 'সরাসরি সরেজমিনে গিয়ে সহায়তা'],
                'highlights_en' => ['6 sewing machines gifted', 'Long-term livelihood generation', 'Direct on-site compassionate delivery'],
                'badge' => 'Rehabilitation',
                'icon' => '🧵',
            ],
            [
                'id' => 'act-2022-kuet-admission',
                'year' => 2022,
                'date' => '28 August 2022',
                'category' => 'shastra',
                'category_bn' => 'শিক্ষা ও সনাতনী ১০ টাকা প্রজেক্ট',
                'category_en' => 'Education & Micro-Donations',
                'title_bn' => 'কুয়েটে ভর্তি নিশ্চিতকরণে কৃষ্ণপদ রায়কে "সনাতনী ১০ টাকা প্রজেক্ট" হতে সহায়তা',
                'title_en' => 'KUET University Admission Funding for Krishnapada Roy (10 Taka Project)',
                'location_bn' => 'খুলনা প্রকৌশল ও প্রযুক্তি বিশ্ববিদ্যালয় (KUET)',
                'location_en' => 'Khulna University of Engineering & Technology (KUET)',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => 'অর্থের অভাবে প্রকৌশল বিশ্ববিদ্যালয়ে ভর্তি অনিশ্চিত হয়ে পড়া দিনমজুরের সন্তান মেধাবী কৃষ্ণপদ রায়ের প্রায় ৪০–৪৫ হাজার টাকার সকল ভর্তি ফি এসপিএস-এর "সনাতনী ১০ টাকা প্রজেক্ট" হতে প্রদান।',
                'description_en' => 'Fully mobilized admission funding (~45,000 BDT) for underprivileged student Krishnapada Roy to join Khulna University of Engineering & Technology through the community-backed "Sanatani 10 Taka Project".',
                'highlights_bn' => ['১০ টাকার ক্ষুদ্র সঞ্চয়ে বিশ্ববিদ্যালয়ের স্বপ্নপূরণ', 'সকল ভর্তি ফি ও বই ক্রয়ের অর্থায়ন', 'প্রকৌশল শিক্ষার দ্বার উন্মুক্ত'],
                'highlights_en' => ['10-Taka micro-donations made university possible', '100% admission fees & books covered', 'Empowering indigenous tech talent'],
                'badge' => '10 Taka Project',
                'icon' => '🏫',
            ],
            [
                'id' => 'act-2022-rangamati-gita',
                'year' => 2022,
                'date' => '10 February 2022',
                'category' => 'shastra',
                'category_bn' => 'শাস্ত্র ও শিশু শিক্ষা',
                'category_en' => 'Scripture & Moral Education',
                'title_bn' => 'রাঙ্গামাটি বাঘাইছড়িতে শিশুদের মাঝে "গীতা আদর্শ লিপি" ৩য় সংস্করণ বিতরণ',
                'title_en' => 'Distribution of 3rd Edition "Gita Adarsha Lipi" in Baghaichhari, Rangamati',
                'location_bn' => 'বাঘাইছড়ি (মিজোরাম সীমান্ত), রাঙ্গামাটি',
                'location_en' => 'Baghaichhari (Mizoram Border), Rangamati',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => 'পার্বত্য অঞ্চলের প্রত্যন্ত জনপদে শিশুদের মাতৃভাষা ও ধর্মীয় বর্ণপরিচয় শেখাতে ২১টি শিক্ষণীয় বিষয় সমৃদ্ধ এসপিএস প্রকাশিত "গীতা আদর্শ লিপি"-র ৩য় সংস্করণ বিনামূল্যে বিতরণ।',
                'description_en' => 'Distributed the 3rd revised edition of SPS\'s flagship children\'s scripture book "Gita Adarsha Lipi" (21 foundational moral & scriptural topics) to hundreds of indigenous children along the Mizoram border.',
                'highlights_bn' => ['২১টি ধর্মীয় ও নৈতিক শিক্ষা পরিচ্ছেদ', 'পার্বত্য সীমান্তের শিশুদের বর্ণমালা শিক্ষা', 'বিনামূল্যে ধর্মগ্রন্থ ও শিক্ষা সামগ্রী'],
                'highlights_en' => ['21 chapters of moral & spiritual wisdom', 'Basic literacy & Sanskrit chanting', '100% free distribution in border hills'],
                'badge' => 'Gita Adarsha Lipi',
                'icon' => '📖',
            ],
            [
                'id' => 'act-2022-sust-laptop',
                'year' => 2022,
                'date' => '5 February 2022',
                'category' => 'shastra',
                'category_bn' => 'প্রযুক্তি ও মেধা সহায়তা',
                'category_en' => 'Technology & Student Grant',
                'title_bn' => 'শাবিপ্রবি সিএসই বিভাগের মেধাবী ছাত্র প্রদীপ পাশীকে ল্যাপটপ উপহার',
                'title_en' => 'Brand New Laptop Gifted to SUST Computer Science Student Pradip Pashi',
                'location_bn' => 'শাহজালাল বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (SUST)',
                'location_en' => 'Shahjalal University of Science & Technology (SUST)',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => 'কম্পিউটার সায়েন্স অ্যান্ড ইঞ্জিনিয়ারিং পড়ুয়া চা-শ্রমিক পরিবারের মেধাবী ছাত্র প্রদীপ পাশীর কোডিং ও গবেষণার জন্য একটি ব্র্যান্ড-নিউ ল্যাপটপ উপহার দেওয়া হয়।',
                'description_en' => 'Donated a brand-new high-spec programming laptop to tea-garden community student Pradip Pashi studying Computer Science and Engineering at SUST.',
                'highlights_bn' => ['উচ্চশিক্ষায় ডিজিটাল বৈষম্য দূরীকরণ', 'চা-শ্রমিক সম্প্রদায়ের মেধাবী প্রতিভা বিকাশ'],
                'highlights_en' => ['Bridging the digital divide in STEM', 'Empowering tea-worker community students'],
                'badge' => 'STEM Support',
                'icon' => '💻',
            ],
            [
                'id' => 'act-2022-sattvik-puja',
                'year' => 2022,
                'date' => '22 February 2022',
                'category' => 'shastra',
                'category_bn' => 'সাত্ত্বিক পূজা ও সংস্কৃতি',
                'category_en' => 'Sattvik Puja & Cultural Life',
                'title_bn' => 'দীঘিনালায় সাত্ত্বিক সরস্বতী পূজা ও ১৫০ আদিবাসী ভক্তে প্রসাদ বিতরণ',
                'title_en' => 'Sattvik Saraswati Puja & Prasad Feast for 150 Indigenous Devotees in Dighinala',
                'location_bn' => 'মধ্য বোয়ালখালী, দীঘিনালা, খাগড়াছড়ি',
                'location_en' => 'Middle Boalkhali, Dighinala, Khagrachhari',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => 'পার্বত্য অঞ্চলে বৈদিক ও সাত্ত্বিক পূজার আদর্শ ছড়িয়ে দিতে সরস্বতী পূজার আয়োজন, শিক্ষার্থীদের খাতা-কলম বিতরণ এবং ১৫০ জন আদিবাসী সনাতনীর মাঝে মহা প্রসাদ পরিবেশন।',
                'description_en' => 'Organized an authentic Sattvik Vedic Saraswati Puja in Dighinala, presenting school supplies to tribal children and serving sanctified vegetarian feast (Prasad) to 150 indigenous residents.',
                'highlights_bn' => ['সাত্ত্বিক পূজার আদর্শ প্রচার', 'শিক্ষাসামগ্রী বিতরণ', '১৫০ জনের মহা প্রসাদ'],
                'highlights_en' => ['Vedic Sattvik rituals demonstrated', 'Stationery kit distribution', 'Feast for 150 hill residents'],
                'badge' => 'Sattvik Seva',
                'icon' => '🪷',
            ],
            [
                'id' => 'act-2022-winter-gaibandha',
                'year' => 2022,
                'date' => '9 January 2022',
                'category' => 'humanitarian',
                'category_bn' => 'শীতবস্ত্র ও কম্বল বিতরণ',
                'category_en' => 'Winter Relief Drive',
                'title_bn' => 'গাইবান্ধার প্রত্যন্ত চরাঞ্চলে অসহায় প্রবীণদের মাঝে শীতবস্ত্র ও কম্বল বিতরণ',
                'title_en' => 'Blanket & Warm Clothes Distribution in Remote Gaibandha',
                'location_bn' => 'গাইবান্ধা সরকারি কলেজ সংলগ্ন ও প্রত্যন্ত চরাঞ্চল',
                'location_en' => 'Gaibandha Govt College & Remote River Areas',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => 'তীব্র শৈত্যপ্রবাহে শীতে কাতর গাইবান্ধার দুর্গম এলাকার অসহায় ও বয়োবৃদ্ধ শীতার্ত মানুষদের ঘরে ঘরে গিয়ে উষ্ণ কম্বল পৌঁছে দেওয়া হয়।',
                'description_en' => 'Distributed heavy blankets directly to the doorsteps of shivering elderly citizens during extreme cold waves in rural Gaibandha.',
                'highlights_bn' => ['শৈত্যপ্রবাহে জরুরি কম্বল বিতরণ', 'প্রত্যন্ত চরাঞ্চলে সরাসরি ত্রাণ পৌঁছানো'],
                'highlights_en' => ['Severe cold wave intervention', 'Door-to-door delivery in remote villages'],
                'badge' => 'Winter Relief',
                'icon' => '🧣',
            ],

            // 2021
            [
                'id' => 'act-2021-shalla-relief',
                'year' => 2021,
                'date' => 'March - April 2021',
                'category' => 'humanitarian',
                'category_bn' => 'জরুরি ত্রাণ ও মন্দির পুনর্নির্মাণ',
                'category_en' => 'Emergency Relief & Temple Reconstruction',
                'title_bn' => 'শাল্লা সাম্প্রদায়িক হামলায় ১০০+ পরিবারে খাদ্য ত্রাণ ও ৬টি মন্দির সংস্কার অনুদান',
                'title_en' => 'Emergency Relief for 100+ Families & 6 Temple Rebuilding Grants in Shalla',
                'location_bn' => 'শাল্লা, সুনামগঞ্জ',
                'location_en' => 'Shalla, Sunamganj',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => 'সুনামগঞ্জের শাল্লায় নোয়াগাঁও গ্রামে সংঘটিত সাম্প্রদায়িক তাণ্ডবের পর এসপিএস নেতৃবৃন্দ মাঠে গিয়ে ১০০-এর বেশি ক্ষতিগ্রস্ত পরিবারে খাদ্য ত্রাণ বিতরণ করেন এবং ভাঙচুর হওয়া ৬টি মন্দিরে পুনর্নির্মাণ অনুদান হস্তান্তর করেন।',
                'description_en' => 'Following devastating communal violence in Noagaon, Shalla, SPS dispatched relief teams providing immediate food supplies, psychological support, and temple restoration funds for 6 vandalized village shrines.',
                'highlights_bn' => ['১০০+ পরিবারের জরুরি খাদ্য সরবরাহ', '৬টি ক্ষতিগ্রস্ত মন্দির সংস্কারের আর্থিক অনুদান', 'শিশুদের মাঝে গীতা আদর্শ লিপি বিতরণ'],
                'highlights_en' => ['Emergency food packages to 100+ homes', 'Reconstruction grants for 6 damaged temples', 'Distributed Gita Adarsha Lipi to children'],
                'badge' => 'Emergency Seva',
                'icon' => '🛡️',
            ],
            [
                'id' => 'act-2021-raktakta-sharad',
                'year' => 2021,
                'date' => '22 October 2021',
                'category' => 'humanitarian',
                'category_bn' => 'দেশব্যাপী মানবিক সহায়তা',
                'category_en' => 'Nationwide Crisis Intervention',
                'title_bn' => 'প্রজেক্ট: "রক্তাক্ত শারদ" — ২০২১ দেশব্যাপী সহিংসতার শিকার পরিবারে সহায়তা',
                'title_en' => 'Project "Raktakta Sharad": Nationwide Humanitarian Aid Post-October Violence',
                'location_bn' => 'কুমিল্লা, চাঁদপুর, নোয়াখালী, রংপুর সহ সারাদেশে',
                'location_en' => 'Nationwide (Comilla, Chandpur, Noakhali, Rangpur, etc.)',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => '২০২১ সালের শারদীয় দুর্গাপূজায় দেশব্যাপী মন্দির আক্রমণ ও হত্যাকাণ্ডের শিকার ক্ষতিগ্রস্তদের পাশে দাঁড়াতে এসপিএস দেশব্যাপী "রক্তাক্ত শারদ" মানবিক সহায়তা মিশন পরিচালনা করে।',
                'description_en' => 'Nationwide humanitarian mission mobilized by SPS following the October 2021 violence, delivering legal assistance guidance, emergency groceries, and rehabilitation packages across affected districts.',
                'highlights_bn' => ['দেশব্যাপী আক্রান্ত পরিবারে সরাসরি ত্রাণ', 'চিকিৎসা সহায়তা ও পুনর্বাসন প্যাকেজ'],
                'highlights_en' => ['Direct relief to affected families nationwide', 'Emergency medical & livelihood support'],
                'badge' => 'Nationwide Aid',
                'icon' => '🚨',
            ],
            [
                'id' => 'act-2021-faridpur-stall',
                'year' => 2021,
                'date' => '13 October 2021',
                'category' => 'shastra',
                'category_bn' => 'ধর্মীয় শিক্ষা ও গ্রন্থ বিতরণ',
                'category_en' => 'Religious Literacy & Scripture',
                'title_bn' => 'সালথায় শ্রী শ্রী গীতা আদর্শ লিপির বিশেষ স্টল ও শিক্ষা কর্মসূচি',
                'title_en' => 'Special Gita Adarsha Lipi Book Stall & Educational Drive at Saltha',
                'location_bn' => 'সালথা, ফরিদপুর',
                'location_en' => 'Saltha, Faridpur',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => 'গ্রামাঞ্চলের শিশুদের সঠিক উচ্চারণ, শ্লোক পাঠ ও নৈতিক শিক্ষা দেওয়ার উদ্দেশ্যে ফরিদপুরের সালথায় এসপিএস-এর উন্মুক্ত পুস্তক স্টল স্থাপন ও উপহার বিতরণ।',
                'description_en' => 'Set up a dedicated educational pavilion in Saltha, Faridpur to teach children correct Sanskrit pronunciation, moral principles, and distributed spiritual learning primers.',
                'highlights_bn' => ['উন্মুক্ত গ্রন্থ প্রদর্শনী ও পাঠ', 'শিশুদের মাঝে শিক্ষা সামগ্রী বিতরণ'],
                'highlights_en' => ['Public book reading & recitation pavilion', 'Free educational material for children'],
                'badge' => 'Literacy',
                'icon' => '📚',
            ],
            [
                'id' => 'act-2021-bandarban-lama',
                'year' => 2021,
                'date' => '2021',
                'category' => 'humanitarian',
                'category_bn' => 'আদিবাসী অধিকার ও সহায়তা',
                'category_en' => 'Tribal Rights & Humanitarian Mission',
                'title_bn' => 'সেভ বিডি হিন্দু প্রজেক্ট: লামা বান্দরবানে আদিবাসী পরিবারে মানবিক সহায়তা',
                'title_en' => 'Save BD Hindu Project: Humanitarian Mission for Indigenous Families in Lama',
                'location_bn' => 'লামা, বান্দরবান পার্বত্য জেলা',
                'location_en' => 'Lama, Bandarban Hill Tracts',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => 'বান্দরবানের লামায় দুর্গম পাহাড়ের নিপীড়িত সনাতন আদিবাসী জনগোষ্ঠীর মাঝে খাদ্য, প্রয়োজনীয় বস্ত্র ও চিকিৎসা রসদ পৌঁছে দেওয়ার যৌথ মিশন।',
                'description_en' => 'Conducted humanitarian outreach in the remote hills of Lama, Bandarban, delivering dry rations, warm clothes, and essential first aid to indigenous Sanatani households.',
                'highlights_bn' => ['দুর্গম পাহাড়ে পায়ে হেঁটে ত্রাণ সরবরাহ', 'আদিবাসী সনাতনীদের সাংস্কৃতিক সংহতি'],
                'highlights_en' => ['Trekking into inaccessible hill settlements', 'Cultural solidarity and nutritional aid'],
                'badge' => 'Hill Tracts',
                'icon' => '⛰️',
            ],
            [
                'id' => 'act-2021-sylhet-children',
                'year' => 2021,
                'date' => '2021',
                'category' => 'humanitarian',
                'category_bn' => 'পথশিশু ও পথবাসী সেবা',
                'category_en' => 'Street Children & Underprivileged Care',
                'title_bn' => 'সিলেট নগরীর ছিন্নমূল ও পথশিশুদের খাদ্য ও শিক্ষা সহায়তা',
                'title_en' => 'Nutritional & Educational Care for Underprivileged Street Children in Sylhet',
                'location_bn' => 'সিলেট মহানগর',
                'location_en' => 'Sylhet City',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => 'সিলেটের রাস্তায় বসবাসরত সুবিধাবঞ্চিত পথশিশুদের মাঝে পুষ্টিকর খাবার, পোশাক ও মৌলিক শিক্ষা সামগ্রী বিতরণ করে তাদের সুস্থ জীবনের অনুপ্রেরণা প্রদান।',
                'description_en' => 'Distributed wholesome meals, clothing, and primary study supplies to homeless street children across Sylhet, fostering hope and dignity.',
                'highlights_bn' => ['পুষ্টিকর রান্না করা খাবার বিতরণ', 'মৌলিক পরিচ্ছন্নতা ও শিক্ষাসামগ্রী'],
                'highlights_en' => ['Nutritious cooked meals', 'Hygiene kits & primary school primers'],
                'badge' => 'Child Care',
                'icon' => '👶',
            ],

            // 2020
            [
                'id' => 'act-2020-competition',
                'year' => 2020,
                'date' => '22 October 2020',
                'category' => 'shastra',
                'category_bn' => 'উদ্বোধনী রচনা ও অভিজ্ঞতা প্রতিযোগিতা',
                'category_en' => 'Inaugural Youth Essay Competition',
                'title_bn' => 'এসপিএস-এর সূচনা: "আমার পূজা ছবি ও গল্প" জাতীয় প্রতিযোগিতা',
                'title_en' => 'The Inception of SPS: "#AmarPujaChhobiOGolpo" National Competition',
                'location_bn' => 'অনলাইন / সমগ্র বাংলাদেশ',
                'location_en' => 'Online / Nationwide Bangladesh',
                'status' => 'completed',
                'status_bn' => 'সফলভাবে সমাপ্ত',
                'status_en' => 'Completed',
                'description_bn' => 'করোনাকালে যুবসমাজকে সনাতন সংস্কৃতি ও পূজার অন্তর্নিহিত আধ্যাত্মিক ভাবনার সাথে যুক্ত করতে আয়োজিত জাতীয় রচনা প্রতিযোগিতা। বিজয়ী শীর্ষ ১০ জনকে আকর্ষণীয় পুস্তক ও স্মারক প্রদান।',
                'description_en' => 'SPS\'s founding initiative during the pandemic: an inspiring nationwide youth essay and storytelling competition reflecting on the sacred philosophy of Durga Puja, awarding top 10 young writers with book bundles.',
                'highlights_bn' => ['২০০+ শব্দের রচনা ও ধর্মীয় অভিজ্ঞতা মূল্যায়ন', 'শীর্ষ ১০ বিজয়ীকে শাস্ত্রীয় গ্রন্থ উপহার', 'এসপিএস ডিজিটাল আন্দোলনের সূচনা'],
                'highlights_en' => ['200+ word philosophical reflection essays', 'Top 10 winners awarded sacred books', 'The digital foundation of SPS movement'],
                'badge' => 'The Beginning',
                'icon' => '✍️',
            ],
        ];
    }

    /**
     * Persist activities to storage/data/activities.json.
     */
    public static function saveActivities(array $activities): bool
    {
        self::$activities = array_values($activities);
        $path = self::getStoragePath();
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return file_put_contents($path, json_encode(self::$activities, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
    }

    /**
     * Retrieve single activity by ID.
     */
    public static function getActivityById(string $id): ?array
    {
        $all = self::getActivities();
        foreach ($all as $item) {
            if ($item['id'] === $id) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Category metadata dictionary.
     */
    public static function getCategories(): array
    {
        return [
            'shastra' => ['bn' => 'শাস্ত্র ও শিক্ষা', 'en' => 'Scripture & Moral Education', 'icon' => '📖'],
            'temple' => ['bn' => 'মন্দির ও ঐতিহ্য', 'en' => 'Temple & Heritage Preservation', 'icon' => '🛕'],
            'humanitarian' => ['bn' => 'মানবিক ও দুর্যোগ ত্রাণ', 'en' => 'Humanitarian & Disaster Relief', 'icon' => '🤝'],
            'livelihood' => ['bn' => 'স্বাবলম্বীকরণ ও জীবিকা', 'en' => 'Livelihood & Self-Reliance', 'icon' => '💼'],
            'nature' => ['bn' => 'পরিবেশ ও বৃক্ষরোপণ', 'en' => 'Nature & Environment', 'icon' => '🌱'],
            'health' => ['bn' => 'চিকিৎসা ও পুনর্বাসন', 'en' => 'Health & Medical Aid', 'icon' => '🩺'],
        ];
    }

    /**
     * Create a new activity and persist to storage with audit log.
     */
    public static function addActivity(array $data, ?array $userContext = null): array
    {
        $categories = self::getCategories();
        $catKey = $data['category'] ?? 'humanitarian';
        $meta = $categories[$catKey] ?? $categories['humanitarian'];

        $year = (int)($data['year'] ?? date('Y'));
        $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower(trim((string)($data['title_en'] ?? 'act'))));
        $slug = trim((string)$slug, '-');
        if (empty($slug)) {
            $slug = substr(md5(uniqid((string)mt_rand(), true)), 0, 6);
        }
        $id = 'act-' . $year . '-' . $slug;

        // Ensure uniqueness
        if (self::getActivityById($id) !== null) {
            $id .= '-' . substr(md5(uniqid((string)mt_rand(), true)), 0, 4);
        }

        $highlightsBn = [];
        if (!empty($data['highlights_bn'])) {
            $highlightsBn = is_array($data['highlights_bn']) 
                ? $data['highlights_bn'] 
                : array_values(array_filter(array_map('trim', explode("\n", (string)$data['highlights_bn']))));
        }

        $highlightsEn = [];
        if (!empty($data['highlights_en'])) {
            $highlightsEn = is_array($data['highlights_en']) 
                ? $data['highlights_en'] 
                : array_values(array_filter(array_map('trim', explode("\n", (string)$data['highlights_en']))));
        }

        $status = in_array($data['status'] ?? '', ['completed', 'in_progress', 'needs_review'], true) 
            ? $data['status'] 
            : 'completed';

        $statusBn = match($status) {
            'in_progress' => 'চলমান কার্যক্রম',
            'needs_review' => 'পর্যালোচনাধীন',
            default => 'সফলভাবে সমাপ্ত',
        };

        $statusEn = match($status) {
            'in_progress' => 'Active Drive',
            'needs_review' => 'Needs Review',
            default => 'Completed',
        };

        $newActivity = [
            'id' => $id,
            'year' => $year,
            'date' => trim((string)($data['date'] ?? (string)$year)),
            'category' => $catKey,
            'category_bn' => trim((string)($data['category_bn'] ?? $meta['bn'])),
            'category_en' => trim((string)($data['category_en'] ?? $meta['en'])),
            'title_bn' => trim((string)($data['title_bn'] ?? '')),
            'title_en' => trim((string)($data['title_en'] ?? '')),
            'location_bn' => trim((string)($data['location_bn'] ?? 'বাংলাদেশ')),
            'location_en' => trim((string)($data['location_en'] ?? 'Bangladesh')),
            'status' => $status,
            'status_bn' => $statusBn,
            'status_en' => $statusEn,
            'description_bn' => trim((string)($data['description_bn'] ?? '')),
            'description_en' => trim((string)($data['description_en'] ?? '')),
            'highlights_bn' => $highlightsBn,
            'highlights_en' => $highlightsEn,
            'badge' => trim((string)($data['badge'] ?? 'General Seva')),
            'icon' => trim((string)($data['icon'] ?? $meta['icon'])),
        ];

        $activities = self::getActivities();
        array_unshift($activities, $newActivity);
        self::saveActivities($activities);

        // Append audit log
        AuditService::log(
            'activity.create',
            'activities',
            $id,
            $newActivity['title_en'],
            null,
            $newActivity,
            'Created new activity via SPS Admin Console',
            $userContext
        );

        return $newActivity;
    }

    /**
     * Update an existing activity and record audit log.
     */
    public static function updateActivity(string $id, array $data, ?array $userContext = null): bool
    {
        $activities = self::getActivities();
        $foundIndex = null;
        $existing = null;

        foreach ($activities as $idx => $act) {
            if ($act['id'] === $id) {
                $foundIndex = $idx;
                $existing = $act;
                break;
            }
        }

        if ($foundIndex === null || $existing === null) {
            return false;
        }

        $categories = self::getCategories();
        $catKey = $data['category'] ?? $existing['category'];
        $meta = $categories[$catKey] ?? $categories['humanitarian'];

        $highlightsBn = $existing['highlights_bn'] ?? [];
        if (isset($data['highlights_bn'])) {
            $highlightsBn = is_array($data['highlights_bn']) 
                ? $data['highlights_bn'] 
                : array_values(array_filter(array_map('trim', explode("\n", (string)$data['highlights_bn']))));
        }

        $highlightsEn = $existing['highlights_en'] ?? [];
        if (isset($data['highlights_en'])) {
            $highlightsEn = is_array($data['highlights_en']) 
                ? $data['highlights_en'] 
                : array_values(array_filter(array_map('trim', explode("\n", (string)$data['highlights_en']))));
        }

        $status = $data['status'] ?? $existing['status'];
        $statusBn = match($status) {
            'in_progress' => 'চলমান কার্যক্রম',
            'needs_review' => 'পর্যালোচনাধীন',
            default => 'সফলভাবে সমাপ্ত',
        };

        $statusEn = match($status) {
            'in_progress' => 'Active Drive',
            'needs_review' => 'Needs Review',
            default => 'Completed',
        };

        $updated = [
            'id' => $id,
            'year' => isset($data['year']) ? (int)$data['year'] : $existing['year'],
            'date' => isset($data['date']) ? trim((string)$data['date']) : $existing['date'],
            'category' => $catKey,
            'category_bn' => trim((string)($data['category_bn'] ?? $meta['bn'])),
            'category_en' => trim((string)($data['category_en'] ?? $meta['en'])),
            'title_bn' => isset($data['title_bn']) ? trim((string)$data['title_bn']) : $existing['title_bn'],
            'title_en' => isset($data['title_en']) ? trim((string)$data['title_en']) : $existing['title_en'],
            'location_bn' => isset($data['location_bn']) ? trim((string)$data['location_bn']) : $existing['location_bn'],
            'location_en' => isset($data['location_en']) ? trim((string)$data['location_en']) : $existing['location_en'],
            'status' => $status,
            'status_bn' => $statusBn,
            'status_en' => $statusEn,
            'description_bn' => isset($data['description_bn']) ? trim((string)$data['description_bn']) : $existing['description_bn'],
            'description_en' => isset($data['description_en']) ? trim((string)$data['description_en']) : $existing['description_en'],
            'highlights_bn' => $highlightsBn,
            'highlights_en' => $highlightsEn,
            'badge' => isset($data['badge']) ? trim((string)$data['badge']) : ($existing['badge'] ?? 'General Seva'),
            'icon' => isset($data['icon']) ? trim((string)$data['icon']) : ($existing['icon'] ?? $meta['icon']),
        ];

        $activities[$foundIndex] = $updated;
        self::saveActivities($activities);

        AuditService::log(
            'activity.update',
            'activities',
            $id,
            $updated['title_en'],
            $existing,
            $updated,
            'Modified activity information via SPS Admin Console',
            $userContext
        );

        return true;
    }

    /**
     * Delete an existing activity and record audit log.
     */
    public static function deleteActivity(string $id, ?array $userContext = null): bool
    {
        $activities = self::getActivities();
        $target = null;
        $filtered = [];

        foreach ($activities as $act) {
            if ($act['id'] === $id) {
                $target = $act;
            } else {
                $filtered[] = $act;
            }
        }

        if ($target === null) {
            return false;
        }

        self::saveActivities($filtered);

        AuditService::log(
            'activity.delete',
            'activities',
            $id,
            $target['title_en'] ?? $id,
            $target,
            null,
            'Permanently deleted activity via SPS Admin Console',
            $userContext
        );

        return true;
    }

    /**
     * Group activities by year in descending order.
     */
    public static function getActivitiesByYear(): array
    {
        $all = self::getActivities();
        $grouped = [];

        foreach ($all as $act) {
            $grouped[$act['year']][] = $act;
        }

        krsort($grouped);
        return $grouped;
    }

    /**
     * Get flagship, ongoing, and recurring programs.
     */
    public static function getFlagships(): array
    {
        if (self::$flagships !== null) {
            return self::$flagships;
        }

        self::$flagships = [
            [
                'id' => 'flagship-gita-lipi',
                'title_bn' => 'গীতা আদর্শ লিপি প্রকল্প',
                'title_en' => 'Gita Adarsha Lipi Initiative',
                'tagline_bn' => 'শিশুদের হাতে সনাতনী বর্ণমালা ও নৈতিক শিক্ষার আলোকবর্তিকা',
                'tagline_en' => 'Empowering the next generation with scriptural literacy and timeless ethics',
                'icon' => '📖',
                'color' => '#b45309',
                'bg' => '#fffbeb',
                'border' => '#fde68a',
                'metrics_bn' => '২১টি পরিচ্ছেদ • ৩য় সংস্করণ মুদ্রিত • হাজার হাজার শিশু উপকৃত',
                'metrics_en' => '21 Chapters • 3 Editions in Print • Thousands of Children Reached',
                'description_bn' => 'প্রত্যন্ত ও পাহাড়ি জনপদের সনাতনী শিশুদের মাঝে বর্ণমালা ও ভগবদ্গীতার মৌলিক শ্লোক শেখাতে এসপিএস-এর সবচেয়ে সফল দীর্ঘমেয়াদি শিক্ষা প্রকল্প।',
                'description_en' => 'A flagship ongoing education project printing and distributing foundational Sanskrit & Bengali alphabet books containing moral stories and Gita verses in remote borderlands.',
            ],
            [
                'id' => 'flagship-10-taka',
                'title_bn' => 'সনাতনী ১০ টাকা প্রজেক্ট',
                'title_en' => 'Sanatani 10 Taka Micro-Donation Project',
                'tagline_bn' => 'প্রতিদিনের ক্ষুদ্র সঞ্চয়ে উচ্চশিক্ষায় দরিদ্র মেধাবীদের স্বপ্নের বিকাশ',
                'tagline_en' => 'Daily micro-contributions turning university dreams into reality',
                'icon' => '🪙',
                'color' => '#15803d',
                'bg' => '#f0fdf4',
                'border' => '#bbf7d0',
                'metrics_bn' => '১০ টাকার সমন্বিত শক্তি • কুয়েট/ঢাবি ভর্তি অর্থায়ন • শতভাগ স্বচ্ছ',
                'metrics_en' => '10 Taka Micro-Fund • KUET / DU Admissions • 100% Transparent',
                'description_bn' => 'প্রতিটি সনাতনী ভাই-বোনের দিনে ১০ টাকার ক্ষুদ্র অনুদান একত্রিত করে বিশ্ববিদ্যালয়ে সুযোগপ্রাপ্ত হতদরিদ্র মেধাবী শিক্ষার্থীদের সম্পূর্ণ ভর্তি ব্যয় নির্বাহের অনন্য প্ল্যাটফর্ম।',
                'description_en' => 'An innovative micro-philanthropy initiative where small everyday contributions accumulate to sponsor full university admission fees for underprivileged youth.',
            ],
            [
                'id' => 'flagship-scholarship',
                'title_bn' => 'এসপিএস মেধা বৃত্তি ও গ্রন্থাগার',
                'title_en' => 'SPS Merit Scholarship & Wisdom Library',
                'tagline_bn' => 'শীর্ষ প্রকৌশল ও প্রযুক্তি বিশ্ববিদ্যালয়ের শিক্ষার্থীদের আর্থিক ও আধ্যাত্মিক পাথেয়',
                'tagline_en' => 'Nurturing intellectual brilliance and spiritual depth in university scholars',
                'icon' => '🎓',
                'color' => '#1d4ed8',
                'bg' => '#eff6ff',
                'border' => '#bfdbfe',
                'metrics_bn' => '২৯+ মেধা বৃত্তি স্মারক • বুয়েট, ঢাবি, শাবিপ্রবি স্কলার • দার্শনিক গ্রন্থ উপহার',
                'metrics_en' => '29+ Milestone Scholarships • BUET, DU, SUST Scholars • Philosophy Books Gifted',
                'description_bn' => 'বুয়েট, ঢাকা বিশ্ববিদ্যালয়, শাহজালাল বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় সহ দেশের শীর্ষ শিক্ষাপ্রতিষ্ঠানের কৃতী শিক্ষার্থীদের নিয়মিত বৃত্তি ও দার্শনিক গ্রন্থ প্রদান।',
                'description_en' => 'Dedicated annual grants, laptops, and spiritual libraries conferred upon high-achieving university scholars to foster both professional excellence and ethical grounding.',
            ],
            [
                'id' => 'flagship-livelihood',
                'title_bn' => 'স্বাবলম্বীকরণ ও স্থায়ী জীবিকা প্রকল্প',
                'title_en' => 'Livelihood & Dignified Self-Reliance',
                'tagline_bn' => 'ভিক্ষাবৃত্তি ও পরনির্ভরশীলতার অবসান ঘটিয়ে আত্মমর্যাদাপূর্ণ ভবিষ্যৎ',
                'tagline_en' => 'Eradicating begging and dependency through commercial tools and enterprise',
                'icon' => '💼',
                'color' => '#7c3aed',
                'bg' => '#f5f3ff',
                'border' => '#ddd6fe',
                'metrics_bn' => 'সেলাই মেশিন বিতরণ • দোকান ও ক্ষুদ্র ব্যবসা স্থাপন • গৃহ সংস্কার',
                'metrics_en' => 'Sewing Machines • Small Shop Setup • Shelter Reconstruction',
                'description_bn' => 'বিধবা ও অসহায় মায়েদের সেলাই মেশিন প্রদান, কেরানীগঞ্জে চায়ের দোকান নির্মাণ, এবং সিরাজগঞ্জে প্রতিবন্ধী পরিবারের স্থায়ী ব্যবসা প্রতিষ্ঠার মাধ্যমে দারিদ্র্য বিমোচন।',
                'description_en' => 'Empowering widows, single mothers, and disabled individuals with high-grade sewing machines, commercial retail carts, and capital to earn their living proudly.',
            ],
            [
                'id' => 'flagship-temple-preservation',
                'title_bn' => 'ঐতিহাসিক তীর্থ সেবা ও মন্দির পুনর্নির্মাণ',
                'title_en' => 'Historic Shrine Seva & Temple Reconstruction',
                'tagline_bn' => 'মহাতীর্থের পরিচ্ছন্নতা, তীর্থযাত্রীদের চিকিৎসাসেবা ও বিপদাপন্ন দেবালয় রক্ষা',
                'tagline_en' => 'Preserving sacred sanctorums, pilgrim safety, and cultural heritage',
                'icon' => '🛕',
                'color' => '#b91c1c',
                'bg' => '#fef2f2',
                'border' => '#fecaca',
                'metrics_bn' => 'চন্দ্রনাথ ধাম সেবা ক্যাম্প • বিশ্রামাগার স্থাপন • নীলফামারী শ্যামা মন্দির',
                'metrics_en' => 'Chandranath Pilgrimage Camp • Rest Shelter Built • Nilphamari Shyama Temple',
                'description_bn' => 'চন্দ্রনাথ ধামে স্থায়ী বিশ্রামাগার নির্মাণ, বিনামূল্যে মেডিকেল সেবা, শাল্লায় মন্দির সংস্কার এবং নীলফামারীতে ধর্মান্তর প্রতিরোধে নতুন শ্যামা মন্দির স্থাপন।',
                'description_en' => 'Active preservation of sacred spaces through pilgrim welfare camps, advocacy for shrine infrastructure, and constructing temples in vulnerable borderline areas.',
            ],
            [
                'id' => 'flagship-nature',
                'title_bn' => 'পরিবেশ ও বৈদিক প্রকৃতি সুরক্ষা',
                'title_en' => 'Vedic Nature & Environmental Protection',
                'tagline_bn' => 'সনাতন দর্শনে ধরিত্রী মাতার প্রতি শ্রদ্ধাবোধ থেকে সবুজ বাংলাদেশ গড়া',
                'tagline_en' => 'Honoring Mother Earth through nationwide afforestation and ecological care',
                'icon' => '🌱',
                'color' => '#047857',
                'bg' => '#ecfdf5',
                'border' => '#a7f3d0',
                'metrics_bn' => '৯,৫০০+ বৃক্ষরোপণ • ৩০+ জেলায় কার্যক্রম • তীর্থক্ষেত্রে সবুজায়ন',
                'metrics_en' => '9,500+ Trees Planted • 30+ Districts • Shrines Greened',
                'description_bn' => 'রাম নবমীতে "একটি গাছ, একটি প্রাণ" কর্মসূচির আওতায় দেশব্যাপী ফলজ, বনজ ও ঔষধি বৃক্ষরোপণের মাধ্যমে বাস্তুতন্ত্র সুরক্ষা ও জলবায়ু সচেতনতা।',
                'description_en' => 'Massive tree plantation campaigns planting thousands of fruit, timber, and Ayurvedic herbal trees to combat climate vulnerability and green historic ashrams.',
            ],
        ];

        return self::$flagships;
    }

    /**
     * Get real-time project tracker data extracted directly from the SPS Notion Database.
     */
    public static function getTrackedProjects(): array
    {
        if (self::$trackedProjects !== null) {
            return self::$trackedProjects;
        }

        self::$trackedProjects = [
            [
                'id' => 'notion-proj-1',
                'name_bn' => 'শ্রীমতি লক্ষী রানী - ২০২১ প্রজেক্ট',
                'name_en' => 'Srimati Laxmi Rani 2021 Welfare Project',
                'status' => 'in_progress',
                'status_bn' => 'চলমান (In Progress)',
                'status_en' => 'In Progress',
                'priority' => 'high',
                'priority_bn' => 'উচ্চ অগ্রাধিকার (High)',
                'priority_en' => 'High',
                'type' => 'charity',
                'type_bn' => 'দাতব্য ও মানবিক কল্যাণ (Charity works)',
                'type_en' => 'Charity works',
                'lead' => 'SPS Welfare Desk',
            ],
            [
                'id' => 'notion-proj-2',
                'name_bn' => 'জগন্নাথ বিশ্ববিদ্যালয় (JnU) মন্দির প্রকল্প',
                'name_en' => 'Jagannath University (JnU) Temple Project',
                'status' => 'needs_review',
                'status_bn' => 'পর্যালোচনাধীন (Needs Review)',
                'status_en' => 'Needs Review',
                'priority' => 'high',
                'priority_bn' => 'উচ্চ অগ্রাধিকার (High)',
                'priority_en' => 'High',
                'type' => 'temple',
                'type_bn' => 'মন্দির ও দেবালয় (Temple)',
                'type_en' => 'Temple',
                'lead' => 'Anik Kumar Saha',
            ],
            [
                'id' => 'notion-proj-3',
                'name_bn' => 'শ্রী শ্রী শিবমন্দির, টালিপাড়া, দেগাছি, বালিয়াডাঙ্গী, ঠাকুরগাঁও',
                'name_en' => 'Sri Sri Shiva Temple, Talipara, Degachi, Baliadangi, Thakurgaon',
                'status' => 'needs_review',
                'status_bn' => 'পরিকল্পনাধীন (Needs Review)',
                'status_en' => 'Needs Review',
                'priority' => 'low',
                'priority_bn' => 'সাধারণ (Low)',
                'priority_en' => 'Low',
                'type' => 'temple',
                'type_bn' => 'মন্দির ও দেবালয় (Temple)',
                'type_en' => 'Temple',
                'lead' => 'Anik Kumar Saha',
            ],
            [
                'id' => 'notion-proj-4',
                'name_bn' => 'নীলফামারী মন্দির ও শিক্ষাকেন্দ্র প্রজেক্ট',
                'name_en' => 'Nilphamari Temple & Education Center Project',
                'status' => 'done',
                'status_bn' => 'সফলভাবে সমাপ্ত (Done)',
                'status_en' => 'Done',
                'priority' => 'high',
                'priority_bn' => 'উচ্চ অগ্রাধিকার (High)',
                'priority_en' => 'High',
                'type' => 'charity',
                'type_bn' => 'দাতব্য ও নির্মাণ (Charity works)',
                'type_en' => 'Charity works',
                'lead' => 'Robin Dey',
            ],
            [
                'id' => 'notion-proj-5',
                'name_bn' => 'অপ্রকাশিত মাঠপর্যায়ের মানবিক সহায়তা ও অডিট',
                'name_en' => 'Unpublished Field Relief & Transparent Audit Review',
                'status' => 'in_review',
                'status_bn' => 'যাচাইকরণাধীন (In Review)',
                'status_en' => 'In Review',
                'priority' => 'medium',
                'priority_bn' => 'মধ্যম (Medium)',
                'priority_en' => 'Medium',
                'type' => 'charity',
                'type_bn' => 'দাতব্য ও অডিট (Charity works)',
                'type_en' => 'Charity works',
                'lead' => 'Joy Chakraborty & Audit Team',
            ],
        ];

        return self::$trackedProjects;
    }

    /**
     * Get aggregate statistics.
     */
    public static function getStats(): array
    {
        return [
            'years_active' => '6+',
            'districts_covered' => '30+',
            'trees_planted' => '9,500+',
            'gita_reciters_served' => '20,000+',
            'scholarships_awarded' => '29+',
            'temples_assisted' => '8+',
            'flood_families_relieved' => '1,500+',
        ];
    }
}
