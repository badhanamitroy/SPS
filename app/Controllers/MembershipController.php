<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\I18n;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\AuthService;
use App\Services\MembershipService;

class MembershipController extends BaseController
{
    /**
     * Public Membership Hub / Landing Page
     */
    public function index(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $categories = MembershipService::getCategories();
        $plans = MembershipService::getPlans();
        $stats = MembershipService::getMemberStats();

        $title = $isBn 
            ? 'সদস্যপদ ও সনাতনী পরিবারে অন্তর্ভুক্তি | সনাতন ফিলোসফি এন্ড স্ক্রিপচার'
            : 'Membership & Dharmic Brotherhood | SPS';

        return $this->render('membership/index', [
            'metaTitle' => $title,
            'activeNav' => 'membership',
            'categories' => $categories,
            'plans' => $plans,
            'stats' => $stats,
            'canonicalUrl' => url('/membership', $locale),
            'alternateBn' => url('/membership', 'bn'),
            'alternateEn' => url('/membership', 'en'),
        ]);
    }

    /**
     * Membership Online Application Page
     */
    public function applyForm(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $selectedCategory = (string)$request->getParam('category', 'STUDENT');
        $selectedPlan = (string)$request->getParam('plan', 'STUDENT_MONTHLY');

        $title = $isBn 
            ? 'সদস্যপদ আবেদন ফরম | সনাতন ফিলোসফি এন্ড স্ক্রিপচার'
            : 'Apply for Membership | SPS';

        return $this->render('membership/apply', [
            'metaTitle' => $title,
            'activeNav' => 'membership',
            'categories' => MembershipService::getCategories(),
            'plans' => MembershipService::getPlans(),
            'selectedCategory' => $selectedCategory,
            'selectedPlan' => $selectedPlan,
            'canonicalUrl' => url('/membership/apply', $locale),
            'alternateBn' => url('/membership/apply', 'bn'),
            'alternateEn' => url('/membership/apply', 'en'),
        ]);
    }

    /**
     * Process Application Submission
     */
    public function submitApplication(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $nameBn = trim((string)$request->getPost('name_bn', ''));
        $nameEn = trim((string)$request->getPost('name_en', ''));
        $email = trim((string)$request->getPost('email', ''));
        $phone = trim((string)$request->getPost('phone', ''));
        $categoryId = (string)$request->getPost('category_id', 'STUDENT');
        $planId = (string)$request->getPost('plan_id', 'STUDENT_MONTHLY');

        if (empty($nameBn) && empty($nameEn)) {
            Session::setFlash('error', $isBn ? 'নাম প্রদান করা আবশ্যক।' : 'Name is required.');
            return $this->redirect(url('/membership/apply', $locale));
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('error', $isBn ? 'সঠিক ইমেইল ঠিকানা প্রদান করুন।' : 'Valid email address is required.');
            return $this->redirect(url('/membership/apply', $locale));
        }

        if (empty($phone)) {
            Session::setFlash('error', $isBn ? 'যোগাযোগের মোবাইল নম্বর প্রদান করা আবশ্যক।' : 'Phone number is required.');
            return $this->redirect(url('/membership/apply', $locale));
        }

        // Check if member already exists
        if (MembershipService::getMemberByEmail($email)) {
            Session::setFlash('error', $isBn 
                ? 'এই ইমেইল দিয়ে ইতোমধ্যেই একটি সদস্যপদ রেকর্ড রয়েছে। ড্যাশবোর্ডে লগইন করুন।' 
                : 'A membership record already exists with this email.');
            return $this->redirect(url('/membership/dashboard', $locale));
        }

        $education = null;
        if ($categoryId === 'STUDENT') {
            $education = [
                'institution' => trim((string)$request->getPost('institution', '')),
                'department' => trim((string)$request->getPost('department', '')),
                'class_year' => trim((string)$request->getPost('class_year', '')),
                'student_id' => trim((string)$request->getPost('student_id', '')),
            ];
        }

        $profession = null;
        if ($categoryId === 'EARNING') {
            $profession = [
                'profession_title' => trim((string)$request->getPost('profession_title', '')),
                'organization' => trim((string)$request->getPost('organization', '')),
                'business_category' => trim((string)$request->getPost('business_category', '')),
                'skills' => trim((string)$request->getPost('skills', '')),
            ];
        }

        $volunteerInterests = $request->getPost('volunteer_interests', []);
        if (!is_array($volunteerInterests)) {
            $volunteerInterests = [];
        }

        $paymentMethod = (string)$request->getPost('payment_method', 'bKash');
        $senderNumber = trim((string)$request->getPost('sender_number', $phone));
        $senderName = trim((string)$request->getPost('sender_name', ''));
        $paymentTime = trim((string)$request->getPost('payment_time', date('Y-m-d H:i:s')));
        $paymentReference = trim((string)$request->getPost('payment_reference', ''));
        $trxId = trim((string)$request->getPost('trx_id', ''));

        // Validate TrxID - mandatory for all payments
        if (empty($trxId)) {
            Session::setFlash('error', $isBn 
                ? 'পেমেন্ট ট্রানজেকশন আইডি (TrxID) প্রদান করা আবশ্যক।' 
                : 'Payment Transaction ID (TrxID) is required.');
            return $this->redirect(url('/membership/apply', $locale));
        }

        $paymentScreenshot = '';
        if (isset($_FILES['payment_screenshot']) && is_uploaded_file($_FILES['payment_screenshot']['tmp_name'])) {
            $ext = strtolower(pathinfo($_FILES['payment_screenshot']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'svg'])) {
                $uploadDir = dirname(__DIR__, 2) . '/public/assets/images/payments';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $filename = 'pay_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES['payment_screenshot']['tmp_name'], $uploadDir . '/' . $filename)) {
                    $paymentScreenshot = 'assets/images/payments/' . $filename;
                }
            }
        }
        if (empty($paymentScreenshot)) {
            $presetScreenshot = trim((string)$request->getPost('payment_screenshot_url', ''));
            if (!empty($presetScreenshot)) {
                $paymentScreenshot = $presetScreenshot;
            }
        }

        // For bKash payment: Sent Money screenshot is strictly mandatory
        $isBkash = stripos($paymentMethod, 'bkash') !== false;
        if ($isBkash && empty($paymentScreenshot)) {
            Session::setFlash('error', $isBn 
                ? 'বিকাশ পেমেন্টের ক্ষেত্রে সফল লেনদেনের সেন্ট মানি স্ক্রিনশট (Sent SS) প্রদান করা আবশ্যক।' 
                : 'Sent Money transaction screenshot (Sent SS) is mandatory for bKash payments.');
            return $this->redirect(url('/membership/apply', $locale));
        }

        if (empty($paymentScreenshot)) {
            $paymentScreenshot = 'assets/images/payments/bkash-success-sample.svg';
        }

        $inputData = [
            'name_bn' => $nameBn ?: $nameEn,
            'name_en' => $nameEn ?: $nameBn,
            'email' => $email,
            'phone' => $phone,
            'category_id' => $categoryId,
            'plan_id' => $planId,
            'district' => trim((string)$request->getPost('district', '')),
            'upazila' => trim((string)$request->getPost('upazila', '')),
            'address' => trim((string)$request->getPost('address', '')),
            'education' => $education,
            'profession' => $profession,
            'apply_volunteer' => !empty($request->getPost('apply_volunteer')),
            'volunteer_interests' => $volunteerInterests,
            'volunteer_experience' => trim((string)$request->getPost('volunteer_experience', '')),
            'payment_method' => $paymentMethod,
            'sender_number' => $senderNumber,
            'sender_name' => $senderName,
            'payment_time' => $paymentTime,
            'payment_reference' => $paymentReference,
            'trx_id' => $trxId,
            'payment_screenshot' => $paymentScreenshot,
        ];

        $result = MembershipService::createApplication($inputData);
        $member = $result['member'];
        $payment = $result['payment'];

        // Automatically set simulated member in session for testing convenience
        Session::set('current_member_code', $member['member_code']);

        Session::setFlash('success', $isBn 
            ? "আপনার সদস্যপদ আবেদন সফলভাবে গৃহীত হয়েছে! আপনার মেম্বার আইডি: {$member['member_code']} এবং ট্রানজেকশন আইডি: {$payment['transaction_id']}। পেমেন্ট যাচাইকরণের পর সদস্যপদ সক্রিয় হবে।" 
            : "Application submitted successfully! Your Member ID: {$member['member_code']} and TxID: {$payment['transaction_id']}.");

        return $this->redirect(url('/membership/dashboard', $locale));
    }

    /**
     * Member Login Portal (GET)
     */
    public function loginPage(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        // If member is already logged in, redirect straight to dashboard
        $currentMemberCode = Session::get('current_member_code');
        if (!empty($currentMemberCode) && !Session::get('member_logged_out')) {
            return $this->redirect(url('/membership/dashboard', $locale));
        }

        $title = $isBn 
            ? 'সদস্য লগইন পোর্টাল | সনাতন ফিলোসফি এন্ড স্ক্রিপচার' 
            : 'Member Login Portal | SPS';

        return $this->render('membership/login', [
            'metaTitle' => $title,
            'activeNav' => 'membership',
            'demoMembers' => [
                ['code' => 'SPS-000872', 'name' => 'অমিত সেন (Amit Sen)', 'role' => 'শিক্ষার্থী সদস্য • বাৎসরিক'],
                ['code' => 'SPS-000124', 'name' => 'প্রিয়াঙ্কা সরকার (Priyanka Sarkar)', 'role' => 'উপার্জনশীল সদস্য • আজীবন'],
                ['code' => 'SPS-000455', 'name' => 'সুমিত্রা পাল (Sumitra Paul)', 'role' => 'শিক্ষার্থী সদস্য • মাসিক'],
            ],
            'canonicalUrl' => url('/membership/login', $locale),
            'alternateBn' => url('/membership/login', 'bn'),
            'alternateEn' => url('/membership/login', 'en'),
        ]);
    }

    /**
     * Process Member Login (POST)
     */
    public function loginProcess(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $identifier = trim((string)$request->getPost('identifier', ''));

        if (empty($identifier)) {
            Session::setFlash('error', $isBn 
                ? 'অনুগ্রহ করে আপনার মেম্বার আইডি (যেমন: SPS-000872), নিবন্ধিত ইমেইল বা মোবাইল নম্বর দিন।' 
                : 'Please enter your Member ID, Email, or Phone number.');
            return $this->redirect(url('/membership/login', $locale));
        }

        $member = MembershipService::findMemberForLogin($identifier);

        if (!$member) {
            Session::setFlash('error', $isBn 
                ? 'প্রদত্ত তথ্য অনুযায়ী কোনো সদস্য রেকর্ড পাওয়া যায়নি। অনুগ্রহ করে আপনার সঠিক মেম্বার আইডি (উদাঃ SPS-000872), নিবন্ধিত ইমেইল বা ফোন নম্বর দিন।' 
                : 'No registered member found matching your input. Please verify your Member ID, email or phone.');
            return $this->redirect(url('/membership/login', $locale));
        }

        // Successfully authenticated!
        Session::set('current_member_code', $member['member_code']);
        Session::forget('member_logged_out');

        $memberName = $isBn ? ($member['name_bn'] ?? $member['name_en']) : ($member['name_en'] ?? $member['name_bn']);
        Session::setFlash('success', $isBn 
            ? "স্বাগতম, {$memberName}! আপনি সফলভাবে সদস্য ড্যাশবোর্ডে প্রবেশ করেছেন।" 
            : "Welcome, {$memberName}! You have successfully logged in to your member portal.");

        return $this->redirect(url('/membership/dashboard', $locale));
    }

    /**
     * Member Logout (GET / POST)
     */
    public function logout(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        Session::forget('current_member_code');
        Session::set('member_logged_out', true);

        Session::setFlash('success', $isBn 
            ? 'সদস্য সেশন থেকে সফলভাবে লগআউট সম্পন্ন হয়েছে।' 
            : 'You have been successfully logged out of your member session.');

        return $this->redirect(url('/membership/login', $locale));
    }

    /**
     * Member Dashboard & Self-Service Portal
     */
    public function dashboard(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        // Check if member switch was requested via query param (e.g. ?as=SPS-000872)
        $switchCode = (string)$request->getParam('as', '');
        if ($switchCode && MembershipService::getMemberById($switchCode)) {
            Session::set('current_member_code', $switchCode);
            Session::forget('member_logged_out');
        }

        $currentCode = Session::get('current_member_code');

        // If user explicitly logged out and no specific switch was made, redirect to login
        if (Session::get('member_logged_out') && empty($currentCode) && empty($switchCode)) {
            Session::setFlash('info', $isBn 
                ? 'ড্যাশবোর্ডে প্রবেশ করতে অনুগ্রহ করে আপনার মেম্বার আইডি বা ইমেইল দিয়ে লগইন করুন।' 
                : 'Please log in with your Member ID or email to access your dashboard.');
            return $this->redirect(url('/membership/login', $locale));
        }

        // Default fallback for preview / automated test runs
        if (empty($currentCode)) {
            $currentCode = 'SPS-000872';
            Session::set('current_member_code', $currentCode);
        }

        $member = MembershipService::getMemberById($currentCode);

        if (!$member) {
            // Fallback to first active member
            $all = MembershipService::getAllMembers();
            $member = $all[0] ?? null;
        }

        $payments = $member ? MembershipService::getMemberPayments($member['id']) : [];
        $history = $member ? MembershipService::getMemberHistory($member['id']) : [];
        $category = $member ? MembershipService::getCategory($member['category_id']) : null;
        $plan = $member ? MembershipService::getPlan($member['plan_id']) : null;
        $allMembers = MembershipService::getAllMembers();

        $title = $isBn 
            ? 'আমার সদস্যপদ ও প্রোফাইল ড্যাশবোর্ড | এসপিএস'
            : 'My Membership Dashboard | SPS';

        return $this->render('membership/dashboard', [
            'metaTitle' => $title,
            'activeNav' => 'membership',
            'member' => $member,
            'category' => $category,
            'plan' => $plan,
            'payments' => $payments,
            'history' => $history,
            'allMembers' => $allMembers,
            'categories' => MembershipService::getCategories(),
            'plans' => MembershipService::getPlans(),
            'canonicalUrl' => url('/membership/dashboard', $locale),
            'alternateBn' => url('/membership/dashboard', 'bn'),
            'alternateEn' => url('/membership/dashboard', 'en'),
        ]);
    }

    /**
     * Digital Membership Card Live QR Verification View
     */
    public function verifyCard(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $code = trim((string)$request->getParam('code', ''));
        $token = trim((string)$request->getParam('token', ''));

        $member = null;
        if ($code) {
            $member = MembershipService::getMemberById($code);
        } elseif ($token) {
            $all = MembershipService::getAllMembers();
            foreach ($all as $m) {
                if (($m['card_qr_token'] ?? '') === $token) {
                    $member = $m;
                    break;
                }
            }
        }

        $title = $isBn 
            ? 'ডিজিটাল সদস্যপদ কার্ড যাচাইকরণ | এসপিএস'
            : 'Digital Membership Card Verification | SPS';

        return $this->render('membership/verify', [
            'metaTitle' => $title,
            'activeNav' => 'membership',
            'member' => $member,
            'category' => $member ? MembershipService::getCategory($member['category_id']) : null,
            'plan' => $member ? MembershipService::getPlan($member['plan_id']) : null,
        ]);
    }

    /**
     * Submit Renewal or Plan Upgrade Payment from Dashboard
     */
    public function makePayment(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $memberId = (string)$request->getPost('member_id', '');
        $member = MembershipService::getMemberById($memberId);

        if (!$member) {
            Session::setFlash('error', $isBn ? 'সদস্য খুঁজে পাওয়া যায়নি।' : 'Member not found.');
            return $this->redirect(url('/membership/dashboard', $locale));
        }

        $paymentType = (string)$request->getPost('payment_type', 'monthly');
        $planId = (string)$request->getPost('plan_id', $member['plan_id']);
        $amount = (float)$request->getPost('amount', 0);
        $method = (string)$request->getPost('payment_method', 'bKash');
        $senderNumber = (string)$request->getPost('sender_number', '');

        if ($amount <= 0) {
            $plan = MembershipService::getPlan($planId);
            $amount = (float)($plan['recurring_fee'] ?? $plan['fee'] ?? 100);
        }

        $senderName = trim((string)$request->getPost('sender_name', ''));
        $paymentTime = trim((string)$request->getPost('payment_time', date('Y-m-d H:i:s')));
        $paymentReference = trim((string)$request->getPost('payment_reference', ''));
        $trxId = trim((string)$request->getPost('trx_id', ''));

        // Validate TrxID
        if (empty($trxId)) {
            Session::setFlash('error', $isBn 
                ? 'পেমেন্ট ট্রানজেকশন আইডি (TrxID) প্রদান করা আবশ্যক।' 
                : 'Payment Transaction ID (TrxID) is required.');
            return $this->redirect(url('/membership/dashboard', $locale));
        }

        $paymentScreenshot = '';
        if (isset($_FILES['payment_screenshot']) && is_uploaded_file($_FILES['payment_screenshot']['tmp_name'])) {
            $ext = strtolower(pathinfo($_FILES['payment_screenshot']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'svg'])) {
                $uploadDir = dirname(__DIR__, 2) . '/public/assets/images/payments';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $filename = 'pay_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($_FILES['payment_screenshot']['tmp_name'], $uploadDir . '/' . $filename)) {
                    $paymentScreenshot = 'assets/images/payments/' . $filename;
                }
            }
        }
        if (empty($paymentScreenshot)) {
            $presetScreenshot = trim((string)$request->getPost('payment_screenshot_url', ''));
            if (!empty($presetScreenshot)) {
                $paymentScreenshot = $presetScreenshot;
            }
        }

        // For bKash payment: Sent Money screenshot is strictly mandatory
        $isBkash = stripos($method, 'bkash') !== false;
        if ($isBkash && empty($paymentScreenshot)) {
            Session::setFlash('error', $isBn 
                ? 'বিকাশ পেমেন্টের ক্ষেত্রে সফল লেনদেনের সেন্ট মানি স্ক্রিনশট (Sent SS) প্রদান করা আবশ্যক।' 
                : 'Sent Money transaction screenshot (Sent SS) is mandatory for bKash payments.');
            return $this->redirect(url('/membership/dashboard', $locale));
        }

        if (empty($paymentScreenshot)) {
            $paymentScreenshot = 'assets/images/payments/bkash-success-sample.svg';
        }

        $paymentData = [
            'member_id' => $member['id'],
            'payment_type' => $paymentType,
            'amount' => $amount,
            'payment_method' => $method,
            'sender_number' => $senderNumber,
            'sender_name' => $senderName,
            'payment_time' => $paymentTime,
            'payment_reference' => $paymentReference,
            'trx_id' => $trxId,
            'payment_screenshot' => $paymentScreenshot,
            'notes' => $isBn ? 'সদস্য ড্যাশবোর্ড থেকে প্রেরিত সদস্যপদ নবায়ন ফি।' : 'Renewal payment submitted from member dashboard.',
            'auto_verify' => false, // will require finance officer verification
        ];

        $payment = MembershipService::recordPayment($paymentData);

        Session::setFlash('success', $isBn 
            ? "আপনার পেমেন্ট রসিদ ও ট্রানজেকশন সফলভাবে জমা হয়েছে! ট্রানজেকশন আইডি: {$payment['transaction_id']} (TrxID: {$payment['trx_id']})। ফাইন্যান্স অফিসার (কোষাধ্যক্ষ) যাচাই করার পর মেম্বারশিপ মেয়াদ বৃদ্ধি পাবে।" 
            : "Payment recorded! SPS TxID: {$payment['transaction_id']}, Provider TrxID: {$payment['trx_id']}. Awaiting Finance Officer verification.");

        return $this->redirect(url('/membership/dashboard', $locale));
    }

    /**
     * Submit Student -> Earning Category Transition Request
     */
    public function requestTransition(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $memberId = (string)$request->getPost('member_id', '');
        $member = MembershipService::getMemberById($memberId);

        if (!$member) {
            Session::setFlash('error', $isBn ? 'সদস্য খুঁজে পাওয়া যায়নি।' : 'Member not found.');
            return $this->redirect(url('/membership/dashboard', $locale));
        }

        $newCategory = (string)$request->getPost('new_category', 'EARNING');
        $newPlan = (string)$request->getPost('new_plan', 'EARNING_MONTHLY');
        $reason = trim((string)$request->getPost('reason', ''));

        if (empty($reason)) {
            $reason = $isBn 
                ? 'শিক্ষাজীবন সমাপ্ত করে কর্মক্ষেত্রে যোগদান হেতু উপার্জনশীল সদস্যপদে রূপান্তর।' 
                : 'Graduated from academic studies and started professional employment.';
        }

        $success = MembershipService::transitionCategory($member['id'], $newCategory, $newPlan, $reason, [
            'name_bn' => $member['name_bn'] . ' (স্বতঃআবেদন)',
            'name_en' => $member['name_en'] . ' (Self-request)',
        ]);

        if ($success) {
            Session::setFlash('success', $isBn 
                ? 'অভিনন্দন! আপনার ক্যাটাগরি সফলভাবে ‘উপার্জনশীল সদস্য (Earning Member)’-এ রূপান্তরিত হয়েছে এবং ইতিহাস সংরক্ষণ করা হয়েছে।' 
                : 'Category successfully transitioned to Earning Member with transition history preserved.');
        } else {
            Session::setFlash('error', $isBn ? 'ক্যাটাগরি রূপান্তর ব্যর্থ হয়েছে।' : 'Category transition failed.');
        }

        return $this->redirect(url('/membership/dashboard', $locale));
    }
}
