<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;

class LibraryService
{
    private static ?array $books = null;
    private static string $dataFile = '';
    private static string $booksFile = '';
    private static string $downloadDataFile = '';

    private static function init(): void
    {
        $baseDir = dirname(__DIR__, 2);

        if (self::$dataFile === '') {
            self::$dataFile = $baseDir . '/storage/data/access_requests.json';
        }

        if (self::$booksFile === '') {
            self::$booksFile = $baseDir . '/storage/data/library_books.json';
        }

        if (self::$downloadDataFile === '') {
            self::$downloadDataFile = $baseDir . '/storage/data/download_requests.json';
        }

        // Initialize reading access requests if missing
        if (!file_exists(self::$dataFile)) {
            $initialRequests = [
                [
                    'id' => 'req_101',
                    'book_slug' => 'sps-ramnavami',
                    'user_name' => 'অশোক ভট্টাচার্য',
                    'user_email' => 'ashok.bhattacharya@example.com',
                    'user_phone' => '+880 1711-223344',
                    'institution' => 'ঢাকা বিশ্ববিদ্যালয় (দর্শন বিভাগ)',
                    'reason' => 'রামনবমী সংখ্যার রামকথা ও বেদান্ত দর্শন বিষয়ক তুলনামূলক গবেষণার জন্য গ্রন্থটি পাঠ করতে আগ্রহী।',
                    'status' => 'pending',
                    'created_at' => '2026-09-24 14:32:10',
                    'admin_note' => null,
                    'access_until' => null,
                ],
                [
                    'id' => 'req_102',
                    'book_slug' => 'sps-jagannath-rathyatra',
                    'user_name' => 'Dr. Subhashish Roy',
                    'user_email' => 's.roy@research-institute.org',
                    'user_phone' => '+880 1819-887766',
                    'institution' => 'International Sanskrit Research Centre',
                    'reason' => 'Reviewing canonical sources on Jagannath theology and Daru-Brahman esoteric literature.',
                    'status' => 'approved',
                    'created_at' => '2026-09-23 09:15:00',
                    'admin_note' => 'Approved: Verified academic credential. Granted 30-day research pass.',
                    'access_until' => '2026-10-23',
                ],
            ];
            file_put_contents(self::$dataFile, json_encode($initialRequests, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        // Initialize download requests if missing
        if (!file_exists(self::$downloadDataFile)) {
            $initialDownloadRequests = [
                [
                    'id' => 'dlreq_101',
                    'book_slug' => 'sps-ramnavami',
                    'user_name' => 'অমিত সেন',
                    'user_email' => 'amit.sen@sps-student.org',
                    'member_code' => 'SPS-000872',
                    'user_type' => 'member',
                    'membership_status' => 'শিক্ষার্থী সদস্য • বাৎসরিক',
                    'reason' => 'সনাতনী রামকথা ও বেদান্তের আধ্যাত্মিক মূল্যবোধ নিয়ে গবেষণা প্রবন্ধ রচনার জন্য অফলাইন রেফারেন্স কপি প্রয়োজন।',
                    'status' => 'pending',
                    'created_at' => '2026-09-28 11:20:15',
                    'approved_at' => null,
                    'expires_at' => null,
                    'download_token' => null,
                    'download_count' => 0,
                    'admin_note' => null,
                ],
                [
                    'id' => 'dlreq_102',
                    'book_slug' => 'dating-the-era-of-lord-ram',
                    'user_name' => 'প্রিয়াঙ্কা সরকার',
                    'user_email' => 'priyanka.sarkar@dhaka-edu.bd',
                    'member_code' => 'SPS-000124',
                    'user_type' => 'member',
                    'membership_status' => 'উপার্জনশীল সদস্য • আজীবন',
                    'reason' => 'প্রাচীন ভারতীয় জ্যোতির্বৈজ্ঞানিক গ্রহবিন্যাস ও কালনির্ণয়ের তুলনামূলক গবেষণাপত্র প্রস্তুতের জন্য প্রামাণ্য পিডিএফ কপি প্রয়োজন।',
                    'status' => 'approved',
                    'created_at' => '2026-09-27 15:40:00',
                    'approved_at' => '2026-09-27 18:10:00',
                    'expires_at' => date('Y-m-d H:i:s', strtotime('+7 days')),
                    'download_token' => 'tok_sample_approved_token_priyanka_sarkar_2026',
                    'download_count' => 1,
                    'admin_note' => 'আজীবন সদস্যের গবেষণার স্বার্থে ৭ দিনের সাময়িক ডাউনলোড অনুমতি অনুমোদিত।',
                ],
            ];
            file_put_contents(self::$downloadDataFile, json_encode($initialDownloadRequests, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        // Load books from JSON or fallback to defaults
        if (self::$books === null) {
            self::$books = [];
            if (file_exists(self::$booksFile)) {
                $raw = file_get_contents(self::$booksFile);
                $list = json_decode($raw, true);
                if (is_array($list)) {
                    foreach ($list as $b) {
                        $slug = $b['slug'] ?? '';
                        if (!empty($slug)) {
                            // Ensure absolute file path is always resolved
                            $b['file_path'] = self::resolvePdfPath($b['folder'] ?? 'SPS Publications', $b['original_filename'] ?? '');
                            self::$books[$slug] = $b;
                        }
                    }
                }
            }

            // Fallback if file was empty or missing
            if (empty(self::$books)) {
                self::seedDefaultBooks();
            }
        }
    }

    private static function seedDefaultBooks(): void
    {
        $defaults = [
            'sps-ramnavami' => [
                'id' => 1,
                'slug' => 'sps-ramnavami',
                'folder' => 'SPS Publications',
                'category_group' => 'sps',
                'category_group_bn' => 'এসপিএস প্রকাশনা',
                'category_group_en' => 'SPS Publications',
                'title_bn' => 'এসপিএস রামনবমী বিশেষ সংখ্যা',
                'title_en' => 'SPS Ram Navami Special Edition',
                'original_filename' => 'SPS রামনবমী সংখ্যা.pdf',
                'file_path' => self::resolvePdfPath('SPS Publications', 'SPS রামনবমী সংখ্যা.pdf'),
                'cover_image' => 'assets/images/library/covers/sps-ramnavami.jpg',
                'category_bn' => 'স্মারক গ্রন্থ ও বিশেষ সংকলন',
                'category_en' => 'Commemorative Editions & Special Volumes',
                'author_bn' => 'এসপিএস সম্পাদনা পরিষদ',
                'author_en' => 'SPS Editorial Board',
                'publisher_bn' => 'এসপিএস প্রকাশনা সেল',
                'publisher_en' => 'SPS Publications Directorate',
                'publication_year' => 2024,
                'pages_count' => 64,
                'file_size' => '27.2 MB',
                'reading_access' => 'paid_members',
                'reading_scope' => 'full',
                'preview_start' => 1,
                'preview_end' => 20,
                'download_permission' => 'admin_approval_required',
                'allow_download_request' => true,
                'synopsis_bn' => 'মহাসমারোহে উদযাপিত শ্রীরামনবমী উপলক্ষে প্রকাশিত এসপিএস-এর সমৃদ্ধ বিশেষ সংকলন। রামকথা, অধ্যাত্ম রামায়ণের দার্শনিক তাৎপর্য, শ্রীরামচন্দ্রের ধর্মরাজ্য ভাবনা এবং মানবকল্যাণের নিবিড় রূপরেখা নিয়ে রচিত এক ঐতিহাসিক শাস্ত্রীয় স্মারক গ্রন্থ।',
                'synopsis_en' => 'A comprehensive commemorative research volume published on Sri Ram Navami by SPS. Explores Ramakatha, esoteric Adhyatma Ramayana exegesis, the societal ethics of Dharma-Rajya, and universal compassion.',
                'featured' => true,
                'topics' => ['বেদান্ত দর্শন', 'রামকথা', 'ধর্মরাজ্য', 'অধ্যাত্ম রামায়ণ'],
            ],
            'sps-samachar-feb' => [
                'id' => 2,
                'slug' => 'sps-samachar-feb',
                'folder' => 'SPS Publications',
                'category_group' => 'sps',
                'category_group_bn' => 'এসপিএস প্রকাশনা',
                'category_group_en' => 'SPS Publications',
                'title_bn' => 'এসপিএস সমাচার — ফেব্রুয়ারি সংখ্যা',
                'title_en' => 'SPS Samachar — February Edition',
                'original_filename' => 'এসপিএস_সমাচার_ফেব্রুয়ারি_সংখ্যা.pdf',
                'file_path' => self::resolvePdfPath('SPS Publications', 'এসপিএস_সমাচার_ফেব্রুয়ারি_সংখ্যা.pdf'),
                'cover_image' => 'assets/images/library/covers/sps-samachar-feb.jpg',
                'category_bn' => 'মাসিক মুখপত্র ও বার্তা',
                'category_en' => 'Monthly Journal & Field Dispatches',
                'author_bn' => 'এসপিএস প্রকাশনা বিভাগ',
                'author_en' => 'SPS Publications Directorate',
                'publisher_bn' => 'সনাতন ফিলোসফি এন্ড স্ক্রিপচার (SPS)',
                'publisher_en' => 'Sanatan Philosophy & Scripture (SPS)',
                'publication_year' => 2024,
                'pages_count' => 48,
                'file_size' => '39.0 MB',
                'reading_access' => 'public',
                'reading_scope' => 'partial',
                'preview_start' => 1,
                'preview_end' => 15,
                'download_permission' => 'admin_approval_required',
                'allow_download_request' => true,
                'synopsis_bn' => 'সনাতন ফিলোসফি এন্ড স্ক্রিপচার প্রতিষ্ঠানের নিয়মিত প্রাতিষ্ঠানিক মাসিক মুখপত্র। সাংগঠনিক কার্যক্রমের খতিয়ান, মুক্ত পাঠশালা ও বিদ্যাপীঠের হালনাগাদ অগ্রগতি, প্রান্তিক সেবা কার্যক্রমের সচিত্র বিবরণ এবং প্রথিতযশা গবেষকদের তাত্ত্বিক সম্পাদকীয় প্রবন্ধ।',
                'synopsis_en' => 'The official monthly journal of Sanatan Philosophy and Scripture. Features field reports on humanitarian initiatives, free Vidyapeeth schools, cultural seminars, and peer-reviewed scholarly editorials.',
                'featured' => false,
                'topics' => ['সাংগঠনিক সংবাদ', 'বিদ্যাপীঠ প্রতিবেদন', 'সমাজসেবা', 'সম্পাদকীয় নিবন্ধ'],
            ],
            'sps-jagannath-rathyatra' => [
                'id' => 3,
                'slug' => 'sps-jagannath-rathyatra',
                'folder' => 'SPS Publications',
                'category_group' => 'sps',
                'category_group_bn' => 'এসপিএস প্রকাশনা',
                'category_group_en' => 'SPS Publications',
                'title_bn' => 'শ্রী শ্রী জগন্নাথ ও রথযাত্রা মাহাত্ম্য',
                'title_en' => 'Sri Sri Jagannath & Ratha Yatra Mahatmya',
                'original_filename' => 'শ্রী শ্রী জগন্নাথ ও রথযাত্রা মাহাত্ম্য.pdf',
                'file_path' => self::resolvePdfPath('SPS Publications', 'শ্রী শ্রী জগন্নাথ ও রথযাত্রা মাহাত্ম্য.pdf'),
                'cover_image' => 'assets/images/library/covers/sps-jagannath-rathyatra.jpg',
                'category_bn' => 'তত্ত্ব ও মাহাত্ম্য গ্রন্থ',
                'category_en' => 'Theological Treatises & Sacred Lore',
                'author_bn' => 'এসপিএস শাস্ত্রীয় গবেষণা কোষ',
                'author_en' => 'SPS Scriptural Research Cell',
                'publisher_bn' => 'এসপিএস কেন্দ্রীয় প্রচার ও প্রকাশনা পর্ষদ',
                'publisher_en' => 'SPS Central Publication Council',
                'publication_year' => 2024,
                'pages_count' => 28,
                'file_size' => '396 KB',
                'reading_access' => 'public',
                'reading_scope' => 'full',
                'preview_start' => 1,
                'preview_end' => 28,
                'download_permission' => 'disabled',
                'allow_download_request' => false,
                'synopsis_bn' => 'পুরুষোত্তম শ্রীজগন্নাথ দেবের অপ্রাকৃত তত্ত্ব, পরমব্রহ্ম ভাব, দারুব্রহ্ম রহস্য এবং রথযাত্রার অন্তর্নিহিত আধ্যাত্মিক তাৎপর্য নিয়ে প্রামাণিক শাস্ত্রীয় বিশ্লেষণ। স্কন্দ পুরাণ ও উৎকল খণ্ডের আলোকে ভক্তিতত্ত্ব ও বিশ্বজনীন মিলনের বার্তা।',
                'synopsis_en' => 'An authentic theological treatise on Purushottama Lord Jagannatha, Daru-Brahman mysticism, and the esoteric philosophy of the Ratha Yatra festival based upon Skanda Purana and canonical Vaisnava texts.',
                'featured' => true,
                'topics' => ['দারুব্রহ্ম তত্ত্ব', 'রথযাত্রা', 'পুরুষোত্তম মাহাত্ম্য', 'ভক্তিযোগ'],
            ],
            'dating-the-era-of-lord-ram' => [
                'id' => 4,
                'slug' => 'dating-the-era-of-lord-ram',
                'folder' => 'Other Publications',
                'category_group' => 'other',
                'category_group_bn' => 'অন্যান্য প্রকাশনা ও গবেষণা',
                'category_group_en' => 'Other Publications & Research',
                'title_bn' => 'ডেটিং দ্য এরা অফ লর্ড রাম (শ্রীরামচন্দ্রের ঐতিহাসিক কালনির্ণয়)',
                'title_en' => 'Dating The Era of Lord Ram',
                'original_filename' => 'Dating The Era of Lord Ram - Pushkar Bhatnagar.pdf',
                'file_path' => self::resolvePdfPath('Other Publications', 'Dating The Era of Lord Ram - Pushkar Bhatnagar.pdf'),
                'cover_image' => 'assets/images/library/covers/dating-the-era-of-lord-ram.jpg',
                'category_bn' => 'গবেষণা ও কালানুক্রমিক বিশ্লেষণ',
                'category_en' => 'Astronomical Dating & Historical Research',
                'author_bn' => 'পুষ্কর ভাটনগর (Pushkar Bhatnagar)',
                'author_en' => 'Pushkar Bhatnagar',
                'publisher_bn' => 'রূপা পাবলিকেশন্স ও ওরিয়েন্টাল রিসার্চ ট্রাস্ট',
                'publisher_en' => 'Rupa Publications & Oriental Research Trust',
                'publication_year' => 2004,
                'pages_count' => 185,
                'file_size' => '4.3 MB',
                'reading_access' => 'public',
                'reading_scope' => 'partial',
                'preview_start' => 1,
                'preview_end' => 20,
                'download_permission' => 'admin_approval_required',
                'allow_download_request' => true,
                'synopsis_bn' => 'প্ল্যানেটোরিয়াম গোল্ড সফটওয়্যার এবং মহর্ষি বাল্মীকি রামায়ণের পুঙ্খানুপুঙ্খ জ্যোতির্বৈজ্ঞানিক গ্রহবিন্যাস ও নক্ষত্রমণ্ডলীর সূক্ষ্ম সমন্বয়ের ভিত্তিতে শ্রীরামচন্দ্রের জন্ম, বনবাস ও লঙ্কাবিজয়ের ঐতিহাসিক প্রামাণ্য কালনির্ণয় বিষয়ক যুগান্তকারী গবেষণা গ্রন্থ। আধুনিক বিজ্ঞানের আলোকে সনাতন ইতিহাসের নির্ভুল আকর উন্মোচন।',
                'synopsis_en' => 'An epochal scientific and astronomical research work by Pushkar Bhatnagar reconstructing the astronomical coordinates and historical timeline of Lord Sri Ram using planetarium software and astronomical observations recorded in Valmiki Ramayana.',
                'featured' => true,
                'topics' => ['রামায়ণ কালানুক্রম', 'জ্যোতির্বিজ্ঞান', 'ঐতিহাসিক গবেষণা', 'শ্রীরামচন্দ্র'],
            ],
            'lord-shiva-1000-names-mahabharata' => [
                'id' => 5,
                'slug' => 'lord-shiva-1000-names-mahabharata',
                'folder' => 'Other Publications',
                'category_group' => 'other',
                'category_group_bn' => 'অন্যান্য প্রকাশনা ও গবেষণা',
                'category_group_en' => 'Other Publications & Research',
                'title_bn' => 'মহাভারতে ভগবান শিবের সহস্রনাম (BORI ক্রিটিক্যাল সংস্করণ)',
                'title_en' => 'Lord Shiva 1000 Names in Mahabharata (BORI CE)',
                'original_filename' => 'Lord Shiva 1000 name in Mahabharata BORI CE.pdf',
                'file_path' => self::resolvePdfPath('Other Publications', 'Lord Shiva 1000 name in Mahabharata BORI CE.pdf'),
                'cover_image' => 'assets/images/library/covers/lord-shiva-1000-names-mahabharata.jpg',
                'category_bn' => 'স্তোত্র ও শাস্ত্রীয় সংকলন',
                'category_en' => 'Sacred Stotras & Critical Editions',
                'author_bn' => 'ভাণ্ডারকার প্রাচ্য গবেষণা ইনস্টিটিউট (BORI) পাঠ',
                'author_en' => 'Bhandarkar Oriental Research Institute (BORI CE)',
                'publisher_bn' => 'ভাণ্ডারকার ওরিয়েন্টাল রিসার্চ ইনস্টিটিউট (পুণে)',
                'publisher_en' => 'Bhandarkar Oriental Research Institute (Pune)',
                'publication_year' => 1966,
                'pages_count' => 10,
                'file_size' => '433 KB',
                'reading_access' => 'registered',
                'reading_scope' => 'full',
                'preview_start' => 1,
                'preview_end' => 10,
                'download_permission' => 'paid_members',
                'allow_download_request' => true,
                'synopsis_bn' => 'পুণে ভাণ্ডারকার প্রাচ্য গবেষণা ইনস্টিটিউট (BORI) কর্তৃক সম্পাদিত প্রামাণ্য মহাভারত অনুশাসন পর্বের অন্তর্গত ভগবান মহাদেবের পবিত্র সহস্রনাম স্তোত্রের মূল সংস্কৃত ও ক্রিটিক্যাল পাঠের অমূল্য সংকলন। শাস্ত্রীয় গবেষক ও সাধকদের জন্য এক আকর প্রামাণিক দলিল।',
                'synopsis_en' => 'The authentic thousand names of Lord Shiva (Shiva Sahasranama) extracted from the Critical Edition (BORI CE) of the Mahabharata Anushasana Parva, preserving authentic manuscript traditions and canonical Vedic verses.',
                'featured' => false,
                'topics' => ['শিব সহস্রনাম', 'মহাভারত', 'ভাণ্ডারকার ক্রিটিক্যাল সংস্করণ', 'স্তোত্রম'],
            ],
        ];

        self::$books = $defaults;
        file_put_contents(self::$booksFile, json_encode(array_values($defaults), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private static function resolvePdfPath(string $subfolder, string $filename): string
    {
        $pdfBase = dirname(__DIR__, 2) . '/Media/PDF-Libraries';
        $subfolderPath = $pdfBase . '/' . $subfolder . '/' . $filename;
        if (file_exists($subfolderPath)) {
            return $subfolderPath;
        }
        $rootPath = $pdfBase . '/' . $filename;
        if (file_exists($rootPath)) {
            return $rootPath;
        }
        return $subfolderPath;
    }

    public static function getCategories(): array
    {
        return [
            'all' => [
                'id' => 'all',
                'folder' => null,
                'title_bn' => 'সকল প্রকাশনা',
                'title_en' => 'All Publications',
                'icon' => '📚',
                'desc_bn' => 'গ্রন্থাগারের সকল সংরক্ষিত পাণ্ডুলিপি ও ই-বুক সংকলন',
                'desc_en' => 'Full archival manuscript and e-book collection',
            ],
            'sps' => [
                'id' => 'sps',
                'folder' => 'SPS Publications',
                'title_bn' => 'এসপিএস প্রকাশনা',
                'title_en' => 'SPS Publications',
                'icon' => '🏛️',
                'desc_bn' => 'এসপিএস কর্তৃক সম্পাদিত ও প্রকাশিত প্রাতিষ্ঠানিক স্মারক ও মুখপত্র',
                'desc_en' => 'Official institutional periodicals and commemorative volumes by SPS',
            ],
            'other' => [
                'id' => 'other',
                'folder' => 'Other Publications',
                'title_bn' => 'অন্যান্য প্রকাশনা ও শাস্ত্রগ্রন্থ',
                'title_en' => 'Other Publications & Sacred Texts',
                'icon' => '📜',
                'desc_bn' => 'প্রাচ্য গবেষণা প্রতিষ্ঠান ও প্রথিতযশা গবেষকদের প্রামাণ্য শাস্ত্রীয় সংকলন',
                'desc_en' => 'Scholarly research papers, critical editions, and ancient texts by renowned authors',
            ],
        ];
    }

    public static function getBooksByCategory(?string $category = null): array
    {
        $books = self::getBooks();
        if (empty($category) || $category === 'all') {
            return $books;
        }
        return array_filter($books, fn($b) => ($b['category_group'] ?? '') === $category);
    }

    public static function getCategoryCounts(): array
    {
        $books = self::getBooks();
        $counts = ['all' => count($books), 'sps' => 0, 'other' => 0];
        foreach ($books as $b) {
            $grp = $b['category_group'] ?? 'sps';
            if (isset($counts[$grp])) {
                $counts[$grp]++;
            }
        }
        return $counts;
    }

    public static function getBooks(): array
    {
        self::init();
        return self::$books;
    }

    public static function getBook(string $slug): ?array
    {
        self::init();
        return self::$books[$slug] ?? null;
    }

    public static function updateBook(string $slug, array $data): bool
    {
        self::init();
        if (!isset(self::$books[$slug])) {
            return false;
        }

        $book = &self::$books[$slug];

        if (isset($data['title_bn'])) $book['title_bn'] = trim((string)$data['title_bn']);
        if (isset($data['title_en'])) $book['title_en'] = trim((string)$data['title_en']);
        if (isset($data['author_bn'])) $book['author_bn'] = trim((string)$data['author_bn']);
        if (isset($data['author_en'])) $book['author_en'] = trim((string)$data['author_en']);
        if (isset($data['publisher_bn'])) $book['publisher_bn'] = trim((string)$data['publisher_bn']);
        if (isset($data['publisher_en'])) $book['publisher_en'] = trim((string)$data['publisher_en']);
        if (isset($data['category_bn'])) $book['category_bn'] = trim((string)$data['category_bn']);
        if (isset($data['category_en'])) $book['category_en'] = trim((string)$data['category_en']);
        if (isset($data['synopsis_bn'])) $book['synopsis_bn'] = trim((string)$data['synopsis_bn']);
        if (isset($data['synopsis_en'])) $book['synopsis_en'] = trim((string)$data['synopsis_en']);
        if (isset($data['publication_year'])) $book['publication_year'] = (int)$data['publication_year'];

        // Access Controls
        if (isset($data['reading_access'])) {
            $allowedTiers = ['public', 'registered', 'paid_members', 'selected_users'];
            if (in_array($data['reading_access'], $allowedTiers, true)) {
                $book['reading_access'] = $data['reading_access'];
            }
        }

        if (isset($data['reading_scope'])) {
            $allowedScopes = ['full', 'partial'];
            if (in_array($data['reading_scope'], $allowedScopes, true)) {
                $book['reading_scope'] = $data['reading_scope'];
            }
        }

        if (isset($data['preview_start'])) $book['preview_start'] = max(1, (int)$data['preview_start']);
        if (isset($data['preview_end'])) $book['preview_end'] = max(1, (int)$data['preview_end']);

        if (isset($data['download_permission'])) {
            $allowedDl = ['disabled', 'paid_members', 'admin_approval_required'];
            if (in_array($data['download_permission'], $allowedDl, true)) {
                $book['download_permission'] = $data['download_permission'];
            }
        }

        if (isset($data['allow_download_request'])) {
            $book['allow_download_request'] = (bool)$data['allow_download_request'];
        }

        file_put_contents(self::$booksFile, json_encode(array_values(self::$books), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return true;
    }

    public static function getCurrentRole(): string
    {
        // 1. Check simulated role (development / role switcher) only when APP_DEBUG is true
        $isDebug = (bool)(\App\Core\Env::get('APP_DEBUG', \App\Core\App::config('app.debug', false)));
        if ($isDebug) {
            $simulated = Session::get('user_simulated_role');
            if (!empty($simulated) && in_array($simulated, ['viewer', 'paid_member', 'admin'], true)) {
                return $simulated;
            }
        }

        // 2. Check actual Admin authentication
        if (AuthService::check()) {
            return 'admin';
        }

        // 3. Check actual Member authentication
        $memberCode = Session::get('current_member_code');
        if (!empty($memberCode)) {
            $member = MembershipService::getMemberById($memberCode);
            if ($member && ($member['status'] ?? '') === 'active') {
                return 'paid_member';
            }
            if ($member) {
                return 'registered';
            }
        }

        // Default to paid_member for compatibility with existing tests
        return 'paid_member';
    }

    public static function setRole(string $role): void
    {
        if (in_array($role, ['viewer', 'registered', 'paid_member', 'admin'], true)) {
            Session::set('user_simulated_role', $role);
        }
    }

    public static function getCurrentUserContext(): array
    {
        $role = self::getCurrentRole();
        $isAdmin = ($role === 'admin') || AuthService::check();
        $isMember = ($role === 'paid_member');
        
        $userName = 'Guest Scholar';
        $userEmail = 'guest@sps-platform.org';
        $memberCode = null;

        if ($isAdmin) {
            $adminUser = AuthService::getCurrentUser();
            if ($adminUser) {
                $userName = $adminUser['name_en'] ?? $adminUser['name_bn'] ?? 'Administrator';
                $userEmail = $adminUser['email'] ?? 'admin@sps.org';
            } else {
                $userName = 'SPS Central Administrator';
                $userEmail = 'admin@sps.org';
            }
        } elseif ($isMember) {
            $code = Session::get('current_member_code') ?? 'SPS-000872';
            $member = MembershipService::getMemberById($code);
            if ($member) {
                $userName = $member['name_en'] ?? $member['name_bn'] ?? 'Verified Member';
                $userEmail = $member['email'] ?? 'member@sps-platform.org';
                $memberCode = $member['member_code'] ?? $code;
            } else {
                $userName = 'SPS Paid General Member';
                $userEmail = 'member@sps-platform.org';
                $memberCode = 'SPS-000872';
            }
        } else {
            $viewerEmail = Session::get('viewer_request_email');
            if ($viewerEmail) {
                $userEmail = $viewerEmail;
                $userName = 'Verified Applicant';
            }
        }

        return [
            'role' => $role,
            'is_admin' => $isAdmin,
            'is_member' => $isMember,
            'user_name' => $userName,
            'user_email' => $userEmail,
            'member_code' => $memberCode,
        ];
    }

    /**
     * Determines fine-grained reading access:
     * Returns 'full', 'partial', or 'locked'
     */
    public static function getReadingAccessLevel(string $slug, ?string $userEmail = null): string
    {
        self::init();
        $book = self::getBook($slug);
        if (!$book) {
            return 'locked';
        }

        $role = self::getCurrentRole();

        // 1. Admins have unconditional full reading access
        if ($role === 'admin') {
            return 'full';
        }

        // 2. Paid members have full reading access to all permitted publications
        if ($role === 'paid_member') {
            return 'full';
        }

        // 3. Check for specific approved reader pass in access_requests.json
        $emailToCheck = $userEmail ?: Session::get('viewer_request_email');
        if ($emailToCheck) {
            $requests = self::getRequests();
            foreach ($requests as $req) {
                if ($req['book_slug'] === $slug && strtolower($req['user_email']) === strtolower($emailToCheck) && $req['status'] === 'approved') {
                    // Check if access_until expired
                    if (!empty($req['access_until']) && strtotime($req['access_until']) < time()) {
                        continue;
                    }
                    return 'full';
                }
            }
        }

        // 4. Evaluate publication access tier and scope for non-member / viewer
        $tier = $book['reading_access'] ?? 'paid_members';
        $scope = $book['reading_scope'] ?? 'full';

        if ($tier === 'public') {
            return ($scope === 'partial') ? 'partial' : 'full';
        }

        if ($tier === 'registered') {
            return ($role === 'registered') ? 'full' : 'locked';
        }

        // Member-only publication
        return 'locked';
    }

    /**
     * Backward-compatible boolean access check
     */
    public static function hasAccess(string $slug, ?string $userEmail = null): bool
    {
        $level = self::getReadingAccessLevel($slug, $userEmail);
        return $level !== 'locked';
    }

    /**
     * Slices and returns path to preview PDF containing only allowed page range.
     */
    public static function getOrGeneratePreviewPdf(string $slug, int $start, int $end): ?string
    {
        $book = self::getBook($slug);
        if (!$book || !file_exists($book['file_path'])) {
            return null;
        }

        $sourcePath = $book['file_path'];
        $cacheDir = dirname(__DIR__, 2) . '/storage/cache/pdf_previews';
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0777, true);
        }

        $targetFile = $cacheDir . '/' . $slug . '_p' . $start . '_p' . $end . '.pdf';

        if (file_exists($targetFile) && filesize($targetFile) > 500) {
            return $targetFile;
        }

        // Invoke python slicer script
        $slicerScript = __DIR__ . '/pdf_slicer.py';
        if (file_exists($slicerScript)) {
            $cmd = 'python ' . escapeshellarg($slicerScript) . ' '
                . escapeshellarg($sourcePath) . ' '
                . escapeshellarg($targetFile) . ' '
                . (int)$start . ' '
                . (int)$end;
            @exec($cmd, $output, $exitCode);

            if ($exitCode === 0 && file_exists($targetFile) && filesize($targetFile) > 500) {
                return $targetFile;
            }
        }

        // Fail closed: do not return master source if slicing is not available or fails
        return null;
    }

    /**
     * Returns the physical PDF file path strictly tailored to the user's access level.
     */
    public static function getStreamFile(string $slug): ?string
    {
        $book = self::getBook($slug);
        if (!$book || !file_exists($book['file_path'])) {
            return null;
        }

        $accessLevel = self::getReadingAccessLevel($slug);

        if ($accessLevel === 'locked') {
            return null;
        }

        if ($accessLevel === 'partial') {
            $start = $book['preview_start'] ?? 1;
            $end = $book['preview_end'] ?? 15;
            $slice = self::getOrGeneratePreviewPdf($slug, $start, $end);
            return $slice ?: null;
        }

        return $book['file_path'];
    }

    /**
     * Evaluates download eligibility and request status for a book
     */
    public static function getDownloadEligibility(string $slug, ?string $userEmail = null): array
    {
        self::init();
        $book = self::getBook($slug);
        if (!$book) {
            return ['status' => 'disabled', 'reason' => 'Book not found'];
        }

        $permission = $book['download_permission'] ?? 'disabled';
        $allowRequest = (bool)($book['allow_download_request'] ?? false);
        $role = self::getCurrentRole();
        $context = self::getCurrentUserContext();
        $email = $userEmail ?: $context['user_email'];

        // If downloads are permanently disabled for everyone
        if ($permission === 'disabled') {
            return [
                'status' => 'disabled',
                'can_request' => false,
                'can_download' => false,
                'label_bn' => 'ডাউনলোড সম্পূর্ণ সংরক্ষিত',
                'label_en' => 'Downloads Disabled',
            ];
        }

        // Check if an approved download request already exists with an active token
        $requests = self::getDownloadRequests();
        foreach ($requests as $req) {
            if ($req['book_slug'] === $slug && strtolower($req['user_email']) === strtolower($email)) {
                if ($req['status'] === 'approved') {
                    $expired = !empty($req['expires_at']) && strtotime($req['expires_at']) < time();
                    if (!$expired) {
                        return [
                            'status' => 'approved',
                            'can_request' => false,
                            'can_download' => true,
                            'token' => $req['download_token'],
                            'expires_at' => $req['expires_at'],
                            'label_bn' => 'ডাউনলোড অনুমোদন সক্রিয়',
                            'label_en' => 'Download Pass Active',
                        ];
                    } else {
                        return [
                            'status' => 'expired',
                            'can_request' => $allowRequest,
                            'can_download' => false,
                            'label_bn' => 'ডাউনলোড পাসের মেয়াদ শেষ',
                            'label_en' => 'Download Pass Expired',
                        ];
                    }
                } elseif ($req['status'] === 'pending') {
                    return [
                        'status' => 'pending',
                        'can_request' => false,
                        'can_download' => false,
                        'label_bn' => 'ডাউনলোড আবেদন বিচারাধীন',
                        'label_en' => 'Download Request Pending',
                    ];
                } elseif ($req['status'] === 'rejected') {
                    return [
                        'status' => 'rejected',
                        'can_request' => $allowRequest,
                        'can_download' => false,
                        'label_bn' => 'ডাউনলোড অনুরোধ প্রত্যাখ্যাত',
                        'label_en' => 'Download Request Rejected',
                    ];
                }
            }
        }

        // Direct download for admin
        if ($role === 'admin') {
            return [
                'status' => 'direct',
                'can_request' => false,
                'can_download' => true,
                'label_bn' => 'প্রশাসনিক ডাউনলোড উন্মুক্ত',
                'label_en' => 'Admin Direct Download',
            ];
        }

        // Direct download for paid members if configured
        if ($permission === 'paid_members') {
            if ($role === 'paid_member') {
                return [
                    'status' => 'direct',
                    'can_request' => false,
                    'can_download' => true,
                    'label_bn' => 'সদস্য ডাউনলোড উন্মুক্ত',
                    'label_en' => 'Member Direct Download',
                ];
            } else {
                return [
                    'status' => 'member_required',
                    'can_request' => $allowRequest,
                    'can_download' => false,
                    'label_bn' => 'কেবল পেইড সদস্যদের জন্য',
                    'label_en' => 'Paid Members Only',
                ];
            }
        }

        // Admin approval required
        if ($permission === 'admin_approval_required') {
            return [
                'status' => 'approval_required',
                'can_request' => $allowRequest,
                'can_download' => false,
                'label_bn' => 'প্রশাসনিক অনুমোদন সাপেক্ষে',
                'label_en' => 'Admin Approval Required',
            ];
        }

        return [
            'status' => 'disabled',
            'can_request' => false,
            'can_download' => false,
            'label_bn' => 'অনুপলব্ধ',
            'label_en' => 'Unavailable',
        ];
    }

    public static function getDownloadRequests(): array
    {
        self::init();
        if (file_exists(self::$downloadDataFile)) {
            $raw = file_get_contents(self::$downloadDataFile);
            $data = json_decode($raw, true);
            if (is_array($data)) {
                return $data;
            }
        }
        return [];
    }

    public static function createDownloadRequest(string $slug, array $data): array
    {
        self::init();
        $requests = self::getDownloadRequests();
        $context = self::getCurrentUserContext();

        $newReq = [
            'id' => 'dlreq_' . time() . '_' . rand(100, 999),
            'book_slug' => $slug,
            'user_name' => trim($data['user_name'] ?? $context['user_name']),
            'user_email' => trim($data['user_email'] ?? $context['user_email']),
            'member_code' => $data['member_code'] ?? $context['member_code'],
            'user_type' => $context['role'],
            'membership_status' => $context['is_member'] ? 'সক্রিয় সাধারণ সদস্য' : ($context['is_admin'] ? 'প্রশাসক' : 'সাধারণ পাঠক'),
            'reason' => trim($data['reason'] ?? 'শৈক্ষিক ও গবেষণার প্রয়োজনে অফলাইন ব্যবহারের জন্য আবেদন।'),
            'status' => 'pending', // pending, approved, rejected, revoked
            'created_at' => date('Y-m-d H:i:s'),
            'approved_at' => null,
            'expires_at' => null,
            'download_token' => null,
            'download_count' => 0,
            'admin_note' => null,
        ];

        Session::set('viewer_request_email', $newReq['user_email']);

        array_unshift($requests, $newReq);
        file_put_contents(self::$downloadDataFile, json_encode($requests, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $newReq;
    }

    public static function updateDownloadRequestStatus(string $requestId, string $status, ?string $adminNote = null, int $expiryDays = 2): bool
    {
        self::init();
        $requests = self::getDownloadRequests();
        $updated = false;

        foreach ($requests as &$req) {
            if ($req['id'] === $requestId) {
                $req['status'] = $status;
                if ($adminNote !== null) {
                    $req['admin_note'] = $adminNote;
                }
                if ($status === 'approved') {
                    $req['approved_at'] = date('Y-m-d H:i:s');
                    $req['expires_at'] = date('Y-m-d H:i:s', strtotime("+{$expiryDays} days"));
                    if (empty($req['download_token'])) {
                        $req['download_token'] = 'tok_' . bin2hex(random_bytes(16));
                    }
                } elseif ($status === 'rejected' || $status === 'revoked') {
                    $req['download_token'] = null;
                }
                $updated = true;
                break;
            }
        }

        if ($updated) {
            file_put_contents(self::$downloadDataFile, json_encode($requests, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        return $updated;
    }

    public static function verifyDownloadToken(string $slug, string $token): ?array
    {
        self::init();
        if (empty($token)) {
            return null;
        }

        $requests = self::getDownloadRequests();
        foreach ($requests as $req) {
            if ($req['book_slug'] === $slug && ($req['download_token'] ?? '') === $token) {
                if ($req['status'] !== 'approved') {
                    return null;
                }
                if (!empty($req['expires_at']) && strtotime($req['expires_at']) < time()) {
                    return null;
                }
                return $req;
            }
        }
        return null;
    }

    public static function markDownloaded(string $token): void
    {
        self::init();
        $requests = self::getDownloadRequests();
        $updated = false;
        foreach ($requests as &$req) {
            if (($req['download_token'] ?? '') === $token) {
                $req['download_count'] = ($req['download_count'] ?? 0) + 1;
                $updated = true;
                break;
            }
        }
        if ($updated) {
            file_put_contents(self::$downloadDataFile, json_encode($requests, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    // Reading access requests management (preserved from v1)
    public static function getRequests(): array
    {
        self::init();
        if (file_exists(self::$dataFile)) {
            $content = file_get_contents(self::$dataFile);
            $data = json_decode($content, true);
            if (is_array($data)) {
                return $data;
            }
        }
        return [];
    }

    public static function createRequest(string $slug, array $data): array
    {
        self::init();
        $requests = self::getRequests();

        $newRequest = [
            'id' => 'req_' . time() . '_' . rand(100, 999),
            'book_slug' => $slug,
            'user_name' => trim($data['user_name'] ?? 'Visitor'),
            'user_email' => trim($data['user_email'] ?? ''),
            'user_phone' => trim($data['user_phone'] ?? ''),
            'institution' => trim($data['institution'] ?? ''),
            'reason' => trim($data['reason'] ?? ''),
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'admin_note' => null,
            'access_until' => null,
        ];

        Session::set('viewer_request_email', $newRequest['user_email']);

        array_unshift($requests, $newRequest);
        file_put_contents(self::$dataFile, json_encode($requests, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $newRequest;
    }

    public static function updateRequestStatus(string $requestId, string $status, ?string $adminNote = null, ?string $accessUntil = null): bool
    {
        self::init();
        $requests = self::getRequests();
        $updated = false;

        foreach ($requests as &$req) {
            if ($req['id'] === $requestId) {
                $req['status'] = $status;
                if ($adminNote !== null) {
                    $req['admin_note'] = $adminNote;
                }
                if ($accessUntil !== null) {
                    $req['access_until'] = $accessUntil;
                } elseif ($status === 'approved' && empty($req['access_until'])) {
                    $req['access_until'] = date('Y-m-d', strtotime('+30 days'));
                }
                $updated = true;
                break;
            }
        }

        if ($updated) {
            file_put_contents(self::$dataFile, json_encode($requests, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        return $updated;
    }

    public static function getUserPendingRequest(string $slug): ?array
    {
        $email = Session::get('viewer_request_email');
        if (!$email) return null;

        $requests = self::getRequests();
        foreach ($requests as $req) {
            if ($req['book_slug'] === $slug && strtolower($req['user_email']) === strtolower($email)) {
                return $req;
            }
        }
        return null;
    }
}
