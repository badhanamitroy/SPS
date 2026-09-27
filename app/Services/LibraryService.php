<?php

namespace App\Services;

use App\Core\Session;

class LibraryService
{
    private static ?array $books = null;
    private static string $dataFile = '';

    private static function init(): void
    {
        if (self::$dataFile === '') {
            self::$dataFile = dirname(__DIR__, 2) . '/storage/data/access_requests.json';
            if (!file_exists(self::$dataFile)) {
                // Initialize default sample requests for realistic admin experience
                $initialRequests = [
                    [
                        'id' => 'req_101',
                        'book_slug' => 'sps-ramnavami',
                        'user_name' => 'অশোক ভট্টাচার্য',
                        'user_email' => 'ashok.bhattacharya@example.com',
                        'user_phone' => '+880 1711-223344',
                        'institution' => 'ঢাকা বিশ্ববিদ্যালয় (দর্শন বিভাগ)',
                        'reason' => 'রামনবমী সংখ্যার রামকথা ও বেদান্ত দর্শন বিষয়ক তুলনামূলক গবেষণার জন্য গ্রন্থটি পাঠ করতে আগ্রহী।',
                        'status' => 'pending', // pending, approved, rejected
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
        }

        if (self::$books === null) {
            $pdfBase = dirname(__DIR__, 2) . '/Media/PDF-Libraries';

            self::$books = [
                'sps-ramnavami' => [
                    'id' => 1,
                    'slug' => 'sps-ramnavami',
                    'title_bn' => 'এসপিএস রামনবমী বিশেষ সংখ্যা',
                    'title_en' => 'SPS Ram Navami Special Edition',
                    'original_filename' => 'SPS রামনবমী সংখ্যা.pdf',
                    'file_path' => $pdfBase . '/SPS রামনবমী সংখ্যা.pdf',
                    'cover_image' => 'assets/images/library/covers/sps-ramnavami.jpg',
                    'category_bn' => 'স্মারক গ্রন্থ ও বিশেষ সংকলন',
                    'category_en' => 'Commemorative Editions & Special Volumes',
                    'author_bn' => 'এসপিএস সম্পাদনা পরিষদ',
                    'author_en' => 'SPS Editorial Board',
                    'publication_year' => 2024,
                    'pages_count' => 64,
                    'file_size' => '27.2 MB',
                    'access_tier' => 'paid_members', // 'paid_members' = Paid General Members Only
                    'synopsis_bn' => 'মহাসমারোহে উদযাপিত শ্রীরামনবমী উপলক্ষে প্রকাশিত এসপিএস-এর সমৃদ্ধ বিশেষ সংকলন। রামকথা, অধ্যাত্ম রামায়ণের দার্শনিক তাৎপর্য, শ্রীরামচন্দ্রের ধর্মরাজ্য ভাবনা এবং মানবকল্যাণের নিবিড় রূপরেখা নিয়ে রচিত এক ঐতিহাসিক শাস্ত্রীয় স্মারক গ্রন্থ।',
                    'synopsis_en' => 'A comprehensive commemorative research volume published on Sri Ram Navami by SPS. Explores Ramakatha, esoteric Adhyatma Ramayana exegesis, the societal ethics of Dharma-Rajya, and universal compassion.',
                    'featured' => true,
                    'topics' => ['বেদান্ত দর্শন', 'রামকথা', 'ধর্মরাজ্য', 'অধ্যাত্ম রামায়ণ'],
                ],
                'sps-samachar-feb' => [
                    'id' => 2,
                    'slug' => 'sps-samachar-feb',
                    'title_bn' => 'এসপিএস সমাচার — ফেব্রুয়ারি সংখ্যা',
                    'title_en' => 'SPS Samachar — February Edition',
                    'original_filename' => 'এসপিএস_সমাচার_ফেব্রুয়ারি_সংখ্যা.pdf',
                    'file_path' => $pdfBase . '/এসপিএস_সমাচার_ফেব্রুয়ারি_সংখ্যা.pdf',
                    'cover_image' => 'assets/images/library/covers/sps-samachar-feb.jpg',
                    'category_bn' => 'মাসিক মুখপত্র ও বার্তা',
                    'category_en' => 'Monthly Journal & Field Dispatches',
                    'author_bn' => 'এসপিএস প্রকাশনা বিভাগ',
                    'author_en' => 'SPS Publications Directorate',
                    'publication_year' => 2024,
                    'pages_count' => 48,
                    'file_size' => '39.0 MB',
                    'access_tier' => 'paid_members',
                    'synopsis_bn' => 'সনাতন দর্শন ও শাস্ত্র প্রতিষ্ঠানের নিয়মিত প্রাতিষ্ঠানিক মাসিক মুখপত্র। সাংগঠনিক কার্যক্রমের খতিয়ান, মুক্ত পাঠশালা ও বিদ্যাপীঠের হালনাগাদ অগ্রগতি, প্রান্তিক সেবা কার্যক্রমের সচিত্র বিবরণ এবং প্রথিতযশা গবেষকদের তাত্ত্বিক সম্পাদকীয় প্রবন্ধ।',
                    'synopsis_en' => 'The official monthly journal of Sanatan Philosophy and Scripture. Features field reports on humanitarian initiatives, free Vidyapeeth schools, cultural seminars, and peer-reviewed scholarly editorials.',
                    'featured' => false,
                    'topics' => ['সাংগঠনিক সংবাদ', 'বিদ্যাপীঠ প্রতিবেদন', 'সমাজসেবা', 'সম্পাদকীয় নিবন্ধ'],
                ],
                'sps-jagannath-rathyatra' => [
                    'id' => 3,
                    'slug' => 'sps-jagannath-rathyatra',
                    'title_bn' => 'শ্রী শ্রী জগন্নাথ ও রথযাত্রা মাহাত্ম্য',
                    'title_en' => 'Sri Sri Jagannath & Ratha Yatra Mahatmya',
                    'original_filename' => 'শ্রী শ্রী জগন্নাথ ও রথযাত্রা মাহাত্ম্য.pdf',
                    'file_path' => $pdfBase . '/শ্রী শ্রী জগন্নাথ ও রথযাত্রা মাহাত্ম্য.pdf',
                    'cover_image' => 'assets/images/library/covers/sps-jagannath-rathyatra.jpg',
                    'category_bn' => 'তত্ত্ব ও মাহাত্ম্য গ্রন্থ',
                    'category_en' => 'Theological Treatises & Sacred Lore',
                    'author_bn' => 'এসপিএস শাস্ত্রীয় গবেষণা কোষ',
                    'author_en' => 'SPS Scriptural Research Cell',
                    'publication_year' => 2024,
                    'pages_count' => 28,
                    'file_size' => '396 KB',
                    'access_tier' => 'paid_members',
                    'synopsis_bn' => 'পুরুষোত্তম শ্রীজগন্নাথ দেবের অপ্রাকৃত তত্ত্ব, পরমব্রহ্ম ভাব, দারুব্রহ্ম রহস্য এবং রথযাত্রার অন্তর্নিহিত আধ্যাত্মিক তাৎপর্য নিয়ে প্রামাণিক শাস্ত্রীয় বিশ্লেষণ। স্কন্দ পুরাণ ও উৎকল খণ্ডের আলোকে ভক্তিতত্ত্ব ও বিশ্বজনীন মিলনের বার্তা।',
                    'synopsis_en' => 'An authentic theological treatise on Purushottama Lord Jagannatha, Daru-Brahman mysticism, and the esoteric philosophy of the Ratha Yatra festival based upon Skanda Purana and canonical Vaisnava texts.',
                    'featured' => true,
                    'topics' => ['দারুব্রহ্ম তত্ত্ব', 'রথযাত্রা', 'পুরুষোত্তম মাহাত্ম্য', 'ভক্তিযোগ'],
                ],
            ];
        }
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

    public static function getCurrentRole(): string
    {
        // Allowed roles: 'viewer', 'paid_member', 'admin'
        return Session::get('user_simulated_role', 'paid_member');
    }

    public static function setRole(string $role): void
    {
        if (in_array($role, ['viewer', 'paid_member', 'admin'], true)) {
            Session::set('user_simulated_role', $role);
        }
    }

    public static function hasAccess(string $slug, ?string $userEmail = null): bool
    {
        self::init();
        $role = self::getCurrentRole();

        // Admins and All-Time Paid General Members have full reading access
        if ($role === 'admin' || $role === 'paid_member') {
            return true;
        }

        // Check if an approved access request exists for this user / book
        $requests = self::getRequests();
        $emailToCheck = $userEmail ?: Session::get('viewer_request_email');

        if ($emailToCheck) {
            foreach ($requests as $req) {
                if ($req['book_slug'] === $slug && strtolower($req['user_email']) === strtolower($emailToCheck) && $req['status'] === 'approved') {
                    return true;
                }
            }
        }

        return false;
    }

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

        // Store email in session to recognize viewer's active request
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
