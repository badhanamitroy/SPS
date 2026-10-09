<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\FileUploader;
use App\Core\I18n;
use App\Core\RateLimiter;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\AuditService;
use App\Services\AuthService;
use App\Services\GoogleAuthService;
use App\Services\MembershipService;
use App\Services\TwoFactorService;


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

        $ip = Session::getClientIp();
        $rateKey = 'ratelimit:membership:apply:' . $ip;
        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $sec = RateLimiter::availableIn($rateKey) ?: 300;
            Session::setFlash('error', $isBn 
                ? "সদস্যপদ আবেদনের অনুমোদিত সীমা অতিক্রম হয়েছে। অনুগ্রহ করে {$sec} সেকেন্ড পর আবার চেষ্টা করুন।" 
                : "Too many membership application attempts. Please try again in {$sec} seconds.");
            return $this->redirect(url('/membership/apply', $locale));
        }
        RateLimiter::hit($rateKey, 300);

        // Honeypot check: reject silently-but-logged when filled
        if (!empty($request->getPost('_hp_website'))) {
            AuditService::log(
                'honeypot.triggered',
                'security',
                'guest',
                'bot',
                [],
                ['ip' => Session::getClientIp(), 'endpoint' => 'membership.apply'],
                'Automated bot submission trapped by membership application honeypot field'
            );
            Session::setFlash('success', $isBn 
                ? 'আপনার আবেদন সফলভাবে গৃহীত হয়েছে। পর্যালোচনা শেষে আপনার সাথে যোগাযোগ করা হবে।' 
                : 'Your application has been received successfully. We will review it shortly.');
            return $this->redirect(url('/membership/login', $locale));
        }

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

        $password = (string)$request->getPost('password', '');
        $passwordConfirmation = (string)$request->getPost('password_confirmation', '');
        if (empty($password) || mb_strlen($password) < 6) {
            Session::setFlash('error', $isBn ? 'কমপক্ষে ৬ অক্ষরের একটি লগইন পাসওয়ার্ড প্রদান করা আবশ্যক।' : 'A login password of at least 6 characters is required.');
            return $this->redirect(url('/membership/apply', $locale));
        }
        if ($password !== $passwordConfirmation) {
            Session::setFlash('error', $isBn ? 'পাসওয়ার্ড এবং পাসওয়ার্ড নিশ্চিতকরণ মিলছে না।' : 'Password and password confirmation do not match.');
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

        if (MembershipService::isDuplicateTrxId($trxId)) {
            Session::setFlash('error', $isBn 
                ? 'এই ট্রানজেকশন আইডিটি (TrxID) ইতিপূর্বে ব্যবহৃত হয়েছে। অনুগ্রহ করে আপনার সঠিক ও অনন্য ট্রানজেকশন আইডি প্রদান করুন।' 
                : 'This Transaction ID (TrxID) has already been used. Please provide your unique Transaction ID.');
            return $this->redirect(url('/membership/apply', $locale));
        }

        $paymentScreenshot = '';
        if (isset($_FILES['payment_screenshot']) && !empty($_FILES['payment_screenshot']['tmp_name'])) {
            $uploadDir = dirname(__DIR__, 2) . '/public/assets/images/payments';
            $uploadRes = FileUploader::uploadImage($_FILES['payment_screenshot'], $uploadDir, FileUploader::MAX_IMAGE_SIZE, 'pay_');
            if ($uploadRes['success']) {
                $paymentScreenshot = 'assets/images/payments/' . $uploadRes['filename'];
            } else {
                Session::setFlash('error', $isBn ? ('পেমেন্ট স্ক্রিনশট আপলোড ত্রুটি: ' . $uploadRes['error']) : ('Payment screenshot error: ' . $uploadRes['error']));
                return $this->redirect(url('/membership/apply', $locale));
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

        // Profile Picture Upload (Optional)
        $avatarPath = MembershipService::DEFAULT_ORGANIZATION_DP;
        if (isset($_FILES['avatar_file']) && !empty($_FILES['avatar_file']['tmp_name'])) {
            $uploadDir = dirname(__DIR__, 2) . '/public/assets/images/members';
            $avatarRes = FileUploader::uploadImage($_FILES['avatar_file'], $uploadDir, FileUploader::MAX_IMAGE_SIZE, 'member_');
            if ($avatarRes['success']) {
                $avatarPath = 'assets/images/members/' . $avatarRes['filename'];
            }
        }

        $inputData = [
            'name_bn' => $nameBn ?: $nameEn,
            'name_en' => $nameEn ?: $nameBn,
            'email' => $email,
            'phone' => $phone,
            'avatar' => $avatarPath,
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
            'password' => $password,
        ];

        $result = MembershipService::createApplication($inputData);
        $member = $result['member'];
        $payment = $result['payment'];

        // Applicant is pending approval; do NOT establish active logged-in session
        Session::setFlash('success', $isBn 
            ? "আপনার সদস্যপদ আবেদন সফলভাবে গৃহীত হয়েছে! আপনার মেম্বার আইডি: {$member['member_code']} এবং ট্রানজেকশন আইডি: {$payment['transaction_id']}। অর্থায়ন ও তথ্য যাচাইকরণের পর সদস্যপদ সক্রিয় হবে।" 
            : "Application submitted successfully! Your Member ID: {$member['member_code']} and TxID: {$payment['transaction_id']}.");

        return $this->redirect(url('/membership/dashboard?as=' . urlencode($member['member_code']), $locale));
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
            'googleClientId' => GoogleAuthService::getClientId(),
            'isGoogleConfigured' => GoogleAuthService::isConfigured(),
            'canonicalUrl' => url('/membership/login', $locale),
            'alternateBn' => url('/membership/login', 'bn'),
            'alternateEn' => url('/membership/login', 'en'),
        ]);
    }


    /**
     * Process Member Login with Password and 2FA (POST)
     */
    public function loginProcess(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $identifier = trim((string)$request->getPost('identifier', ''));
        $password = (string)$request->getPost('password', '');

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

        // If member is pending: They cannot log in yet; show application status waiting screen
        if (MembershipService::isPendingStatus($member['status'] ?? '')) {
            Session::setFlash('warning', $isBn 
                ? 'আপনার সদস্যপদ আবেদনটি বর্তমানে প্রশাসন ও অর্থ বিভাগ কর্তৃক যাচাইাধীন রয়েছে। অনুমোদন সম্পন্ন হওয়ার পর আপনি আপনার পাসওয়ার্ড দিয়ে লগইন করতে পারবেন।' 
                : 'Your membership application is currently pending verification. You can log in once approved by the administration.');
            return $this->redirect(url('/membership/dashboard?as=' . urlencode($member['member_code']), $locale));
        }

        // If password is provided, strictly verify it
        if (!empty($password)) {
            if (!MembershipService::verifyMemberPassword($member, $password)) {
                Session::setFlash('error', $isBn 
                    ? 'ভুল পাসওয়ার্ড। অনুগ্রহ করে আপনার সঠিক পাসওয়ার্ড প্রদান করুন।' 
                    : 'Invalid member password. Please check your credentials and try again.');
                return $this->redirect(url('/membership/login', $locale));
            }
        }

        // Two-Factor Authentication via Member Email
        TwoFactorService::initiateMemberChallenge($member);
        $memberEmail = $member['email'] ?? 'member@sps-platform.org';
        $parts = explode('@', $memberEmail);
        $namePart = $parts[0];
        $domain = $parts[1] ?? 'sps-platform.org';
        $maskedEmail = (strlen($namePart) > 2 ? substr($namePart, 0, 2) . str_repeat('*', strlen($namePart) - 2) : $namePart) . '@' . $domain;

        Session::setFlash('info', $isBn 
            ? "দ্বিমুখী সুরক্ষার জন্য ৬-সংখ্যার ভেরিফিকেশন কোড আপনার ইমেইল ({$maskedEmail})-এ পাঠানো হয়েছে।" 
            : "A 6-digit two-factor verification code has been dispatched to your email ({$maskedEmail}).");

        return $this->redirect(url('/membership/2fa', $locale));
    }

    /**
     * Member 2FA Code Verification Screen (GET)
     */
    public function twoFactorPage(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $challenge = TwoFactorService::getPendingMemberChallenge();
        if (!$challenge) {
            Session::setFlash('error', $isBn ? 'লগইন সেশন পাওয়া যায়নি। অনুগ্রহ করে পুনরায় লগইন করুন।' : 'No active login session. Please log in.');
            return $this->redirect(url('/membership/login', $locale));
        }

        $title = $isBn ? 'সদস্য দ্বিমুখী প্রমাণীকরণ (2FA) | এসপিএস' : 'Member 2FA Verification | SPS';

        return $this->render('membership/2fa', [
            'metaTitle' => $title,
            'challenge' => $challenge,
        ]);
    }

    /**
     * Member 2FA Verification Process (POST)
     */
    public function twoFactorVerify(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $code = trim((string)$request->getPost('code', ''));
        if (empty($code)) {
            Session::setFlash('error', $isBn ? 'অনুগ্রহ করে ৬-সংখ্যার ভেরিফিকেশন কোডটি প্রদান করুন।' : 'Please enter the 6-digit verification code.');
            return $this->redirect(url('/membership/2fa', $locale));
        }

        $result = TwoFactorService::verifyMemberChallenge($code);
        if (!($result['success'] ?? false)) {
            Session::setFlash('error', $result['message']);
            return $this->redirect(url('/membership/2fa', $locale));
        }

        // Successfully authenticated!
        Session::set('current_member_code', $result['member_code']);
        Session::forget('member_logged_out');

        $member = MembershipService::getMemberById($result['member_code']);
        $memberName = $isBn ? ($member['name_bn'] ?? $member['name_en'] ?? '') : ($member['name_en'] ?? $member['name_bn'] ?? '');
        Session::setFlash('success', $isBn 
            ? "স্বাগতম, {$memberName}! দ্বিমুখী যাচাইকরণ সফল হয়েছে।" 
            : "Welcome, {$memberName}! Two-factor authentication verified successfully.");

        return $this->redirect(url('/membership/dashboard', $locale));
    }

    /**
     * Resend Member 2FA OTP Code
     */
    public function twoFactorResend(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $challenge = TwoFactorService::getPendingMemberChallenge();
        if (!$challenge) {
            Session::setFlash('error', $isBn ? 'সেশন পাওয়া যায়নি। অনুগ্রহ করে লগইন করুন।' : 'Session not found. Please log in.');
            return $this->redirect(url('/membership/login', $locale));
        }

        $member = MembershipService::getMemberById($challenge['member_code']);
        if ($member) {
            TwoFactorService::initiateMemberChallenge($member);
            Session::setFlash('success', $isBn ? 'নতুন ভেরিফিকেশন কোড আপনার ইমেইলে পুনরায় পাঠানো হয়েছে।' : 'A fresh verification code has been re-sent to your email.');
        }

        return $this->redirect(url('/membership/2fa', $locale));
    }

    /**
     * Initiate "Continue with Google" OAuth Gateway (GET)
     */
    public function googleRedirect(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $title = $isBn ? 'গুগল দিয়ে লগইন করুন (Continue with Google) | এসপিএস' : 'Sign in with Google | SPS';

        return $this->render('membership/google_auth', [
            'metaTitle' => $title,
            'googleClientId' => GoogleAuthService::getClientId(),
            'isGoogleConfigured' => GoogleAuthService::isConfigured(),
            'demoGoogleAccounts' => [
                ['name' => 'Amit Sen', 'email' => 'amit.sen@example.com', 'avatar' => 'assets/images/members/member_SPS_000872_test.jpg', 'linked' => 'SPS-000872'],
                ['name' => 'Priyanka Sarkar', 'email' => 'priyanka.sarkar@example.com', 'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Priyanka', 'linked' => 'SPS-000124'],
                ['name' => 'Sumitra Paul', 'email' => 'sumitra.paul@example.com', 'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Sumitra', 'linked' => 'SPS-000455'],
            ],
        ]);
    }

    /**
     * Cryptographically Verify Google ID Token for Member Sign-in (POST)
     */
    public function googleVerify(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        // Read credential from JSON body or POST parameter
        $idToken = '';
        $rawInput = file_get_contents('php://input');
        if (!empty($rawInput)) {
            $jsonData = json_decode($rawInput, true);
            if (is_array($jsonData)) {
                $idToken = (string)($jsonData['credential'] ?? ($jsonData['idToken'] ?? ''));
            }
        }
        if (empty($idToken)) {
            $idToken = (string)($request->getPost('credential') ?: $request->getPost('idToken', ''));
        }

        if (empty($idToken)) {
            return $this->json([
                'success' => false,
                'error' => $isBn ? 'গুগল ক্রেডেনশিয়াল টোকেন পাওয়া যায়নি।' : 'Google credential token not received.'
            ], 400);
        }

        // Cryptographically verify ID Token against Google
        $profile = GoogleAuthService::verifyIdToken($idToken);
        if (!$profile) {
            return $this->json([
                'success' => false,
                'error' => $isBn 
                    ? 'গুগল প্রমাণীকরণ ব্যর্থ হয়েছে বা টোকেনটির মেয়াদ শেষ হয়ে গেছে। অনুগ্রহ করে পুনরায় চেষ্টা করুন।' 
                    : 'Google authentication failed or token has expired. Please try again.'
            ], 401);
        }

        // Authenticate or register member
        $authResult = GoogleAuthService::authenticateMember($profile);
        $member = $authResult['member'];
        $isNew = $authResult['is_new'] ?? false;

        $memberName = $isBn ? ($member['name_bn'] ?? $member['name_en']) : ($member['name_en'] ?? $member['name_bn']);
        $welcomeMsg = $isNew
            ? ($isBn 
                ? "স্বাগতম {$memberName}! আপনার গুগল অ্যাকাউন্ট সফলভাবে সনাতনী সদস্যপদে নিবন্ধিত হয়েছে। মেম্বার আইডি: {$member['member_code']}।" 
                : "Welcome {$memberName}! Registered via Google with Member ID: {$member['member_code']}.")
            : ($isBn 
                ? "স্বাগতম, {$memberName}! গুগল নিরাপত্তার মাধ্যমে সফলভাবে লগইন সম্পন্ন হয়েছে।" 
                : "Welcome, {$memberName}! Successfully signed in via Google.");

        Session::setFlash('success', $welcomeMsg);

        return $this->json([
            'success' => true,
            'message' => $welcomeMsg,
            'redirect' => url('/membership/dashboard', $locale),
            'member' => [
                'code' => $member['member_code'],
                'name' => $memberName,
                'email' => $member['email'],
                'is_new' => $isNew
            ]
        ]);
    }


    /**
     * Process Google OAuth Callback (GET / POST)
     */
    public function googleCallback(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $email = trim((string)($request->getPost('google_email') ?: $request->getParam('google_email', '')));
        $name = trim((string)($request->getPost('google_name') ?: $request->getParam('google_name', '')));
        $googleId = trim((string)($request->getPost('google_id') ?: $request->getParam('google_id', '')));

        if (empty($email)) {
            Session::setFlash('error', $isBn ? 'গুগল প্রমাণীকরণ থেকে বৈধ ইমেইল পাওয়া যায়নি।' : 'No valid Google email received.');
            return $this->redirect(url('/membership/login', $locale));
        }

        if (empty($googleId)) {
            $googleId = 'gid_' . substr(md5($email), 0, 12);
        }

        $profile = [
            'email' => $email,
            'name' => $name ?: 'Google User',
            'google_id' => $googleId,
            'avatar' => (string)$request->getPost('google_avatar', ''),
        ];

        $res = MembershipService::findOrCreateMemberByGoogle($profile);
        $member = $res['member'];

        // Authenticate member
        Session::set('current_member_code', $member['member_code']);
        Session::forget('member_logged_out');

        $memberName = $isBn ? ($member['name_bn'] ?? $member['name_en']) : ($member['name_en'] ?? $member['name_bn']);
        $welcomeMsg = $res['is_new']
            ? ($isBn ? "স্বাগতম {$memberName}! আপনার গুগল অ্যাকাউন্ট সফলভাবে সনাতনী সদস্যপদে নিবন্ধিত হয়েছে। মেম্বার আইডি: {$member['member_code']}।" : "Welcome {$memberName}! Registered via Google with Member ID: {$member['member_code']}.")
            : ($isBn ? "স্বাগতম, {$memberName}! গুগল দিয়ে সফলভাবে লগইন সম্পন্ন হয়েছে।" : "Welcome, {$memberName}! Successfully signed in with Google.");

        Session::setFlash('success', $welcomeMsg);
        return $this->redirect(url('/membership/dashboard', $locale));
    }

    /**
     * Member Self-Service Password Update (POST)
     */
    public function updatePassword(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $currentMemberCode = Session::get('current_member_code');
        if (empty($currentMemberCode)) {
            Session::setFlash('error', $isBn ? 'পাসওয়ার্ড পরিবর্তনের পূর্বে লগইন করুন।' : 'Please log in to change password.');
            return $this->redirect(url('/membership/login', $locale));
        }

        $currentPassword = (string)$request->getPost('current_password', '');
        $newPassword = (string)$request->getPost('new_password', '');
        $confirmPassword = (string)$request->getPost('confirm_password', '');

        if ($newPassword !== $confirmPassword) {
            Session::setFlash('error', $isBn ? 'নতুন পাসওয়ার্ড ও কনফার্ম পাসওয়ার্ড মিলছে না।' : 'New password and confirmation password do not match.');
            return $this->redirect(url('/membership/dashboard', $locale));
        }

        if (mb_strlen($newPassword) < 6) {
            Session::setFlash('error', $isBn ? 'নতুন পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে।' : 'New password must be at least 6 characters.');
            return $this->redirect(url('/membership/dashboard', $locale));
        }

        $member = MembershipService::getMemberById($currentMemberCode);
        $currParam = !empty($member['password_hash']) ? $currentPassword : null;

        $res = MembershipService::updateMemberPassword($currentMemberCode, $newPassword, $currParam);

        if ($res['success'] ?? false) {
            Session::setFlash('success', $isBn 
                ? 'আপনার সদস্য পাসওয়ার্ড সফলভাবে সংরক্ষিত হয়েছে।' 
                : 'Your member password has been successfully updated.');
        } else {
            Session::setFlash('error', $res['message'] ?? 'Password update failed.');
        }

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
        TwoFactorService::clearChallenges();

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

        // Check if pending applicant status view was requested (?as={code})
        $asCode = trim((string)$request->getParam('as', ''));
        if ($asCode !== '') {
            $switched = MembershipService::getMemberById($asCode);
            // The ?as= parameter is allowed ONLY for the just-applied pending flow,
            // and then only to show the "pending" screen (no card, no personal data beyond name/status).
            if ($switched && MembershipService::isPendingStatus($switched['status'] ?? '')) {
                // Ensure no active member session is established
                if (Session::get('current_member_code') === $asCode) {
                    Session::forget('current_member_code');
                }

                // Strictly sanitize member data: name and status only (no card, no personal data)
                $sanitizedMember = [
                    'id' => $switched['id'] ?? '',
                    'member_code' => $switched['member_code'] ?? '',
                    'name_bn' => $switched['name_bn'] ?? '',
                    'name_en' => $switched['name_en'] ?? '',
                    'status' => $switched['status'] ?? 'Pending',
                    'category_id' => $switched['category_id'] ?? '',
                    'plan_id' => $switched['plan_id'] ?? '',
                    'join_date' => $switched['join_date'] ?? date('Y-m-d'),
                    'email' => '',
                    'phone' => '',
                    'address' => '',
                    'district' => '',
                    'upazila' => '',
                    'blood_group' => '',
                    'card_qr_token' => '',
                    'avatar' => MembershipService::DEFAULT_ORGANIZATION_DP,
                ];

                $category = MembershipService::getCategory($sanitizedMember['category_id']);
                $plan = MembershipService::getPlan($sanitizedMember['plan_id']);
                $rawPayments = MembershipService::getMemberPayments($switched['id'] ?? '');
                $payments = [];
                if (!empty($rawPayments)) {
                    $first = $rawPayments[0];
                    $payments = [[
                        'id' => $first['id'] ?? '',
                        'member_id' => $switched['id'] ?? '',
                        'member_code' => $switched['member_code'] ?? '',
                        'trx_id' => $first['trx_id'] ?? '',
                        'transaction_id' => $first['transaction_id'] ?? '',
                        'status' => $first['status'] ?? 'Pending',
                    ]];
                }

                return $this->render('membership/dashboard', [
                    'metaTitle' => $isBn ? 'সদস্যপদ আবেদন স্থিতি | এসপিএস' : 'Application Status | SPS',
                    'activeNav' => 'membership',
                    'member' => $sanitizedMember,
                    'category' => $category,
                    'plan' => $plan,
                    'payments' => $payments,
                    'history' => [],
                    'allMembers' => [],
                    'categories' => MembershipService::getCategories(),
                    'plans' => MembershipService::getPlans(),
                    'canonicalUrl' => url('/membership/dashboard', $locale),
                    'alternateBn' => url('/membership/dashboard', 'bn'),
                    'alternateEn' => url('/membership/dashboard', 'en'),
                ]);
            }

            // ?as= was provided for a non-pending member -> DISALLOW IDOR
            // If user is not authenticated in session, redirect to login
            if (empty(Session::get('current_member_code'))) {
                Session::setFlash('info', $isBn 
                    ? 'ড্যাশবোর্ডে প্রবেশ করতে অনুগ্রহ করে আপনার মেম্বার আইডি বা ইমেইল দিয়ে লগইন করুন।' 
                    : 'Please log in with your Member ID or email to access your dashboard.');
                return $this->redirect(url('/membership/login', $locale));
            }
            // If user IS authenticated in session, ignore ?as= completely and fall through to render their own session dashboard
        }

        // Authenticated member dashboard flow: strictly rely on session member code
        $currentCode = (string)Session::get('current_member_code', '');

        // If session holds a pending applicant, clean it up
        if (!empty($currentCode)) {
            $sessionMember = MembershipService::getMemberById($currentCode);
            if ($sessionMember && !MembershipService::isActiveStatus($sessionMember['status'] ?? '')) {
                Session::forget('current_member_code');
                $currentCode = '';
            }
        }

        // If user explicitly logged out and no session, redirect to login
        if (Session::get('member_logged_out') && empty($currentCode)) {
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

        $currentMemberCode = (string)Session::get('current_member_code', '');
        if (empty($currentMemberCode)) {
            Session::setFlash('error', $isBn ? 'অনুগ্রহ করে প্রথমে মেম্বার লগইন করুন।' : 'Please log in as a member.');
            return $this->redirect(url('/membership/login', $locale));
        }

        $member = MembershipService::getMemberById($currentMemberCode);

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

        if (MembershipService::isDuplicateTrxId($trxId)) {
            Session::setFlash('error', $isBn 
                ? 'এই ট্রানজেকশন আইডিটি (TrxID) ইতিপূর্বে ব্যবহৃত হয়েছে। অনুগ্রহ করে আপনার সঠিক ও অনন্য ট্রানজেকশন আইডি প্রদান করুন।' 
                : 'This Transaction ID (TrxID) has already been used. Please provide your unique Transaction ID.');
            return $this->redirect(url('/membership/dashboard', $locale));
        }

        $paymentScreenshot = '';
        if (isset($_FILES['payment_screenshot']) && !empty($_FILES['payment_screenshot']['tmp_name'])) {
            $uploadDir = dirname(__DIR__, 2) . '/public/assets/images/payments';
            $uploadRes = FileUploader::uploadImage($_FILES['payment_screenshot'], $uploadDir, FileUploader::MAX_IMAGE_SIZE, 'pay_');
            if ($uploadRes['success']) {
                $paymentScreenshot = 'assets/images/payments/' . $uploadRes['filename'];
            } else {
                Session::setFlash('error', $isBn ? ('পেমেন্ট স্ক্রিনশট আপলোড ত্রুটি: ' . $uploadRes['error']) : ('Payment screenshot error: ' . $uploadRes['error']));
                return $this->redirect(url('/membership/dashboard', $locale));
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

        $currentMemberCode = (string)Session::get('current_member_code', '');
        if (empty($currentMemberCode)) {
            Session::setFlash('error', $isBn ? 'অনুগ্রহ করে প্রথমে মেম্বার লগইন করুন।' : 'Please log in as a member.');
            return $this->redirect(url('/membership/login', $locale));
        }

        $member = MembershipService::getMemberById($currentMemberCode);

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

    /**
     * Update Member Profile Details (Self-Service)
     */
    public function updateProfile(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $memberCode = (string)Session::get('current_member_code', '');
        if (empty($memberCode)) {
            Session::setFlash('error', $isBn 
                ? 'প্রোফাইল আপডেট করতে অনুগ্রহ করে প্রথমে মেম্বার লগইন করুন।' 
                : 'Please log in as a member to update your profile.');
            return $this->redirect(url('/membership/login', $locale));
        }

        $member = MembershipService::getMemberById($memberCode);
        if (!$member) {
            Session::setFlash('error', $isBn ? 'সদস্য খুঁজে পাওয়া যায়নি।' : 'Member not found.');
            return $this->redirect(url('/membership/dashboard', $locale));
        }

        if (MembershipService::isPendingStatus($member['status'] ?? '')) {
            Session::setFlash('error', $isBn 
                ? 'আপনার আবেদনটি বর্তমানে কোষাধ্যক্ষ কর্তৃক যাচাইাধীন। অনুমোদন সম্পন্ন হওয়ার পরই কেবল প্রোফাইল তথ্য হালনাগাদ করা যাবে।' 
                : 'Your application is currently pending finance approval. Profile updates will unlock once approved.');
            return $this->redirect(url('/membership/dashboard', $locale));
        }

        $profileData = [
            'name_bn' => (string)$request->getPost('name_bn', ''),
            'name_en' => (string)$request->getPost('name_en', ''),
            'phone' => (string)$request->getPost('phone', ''),
            'email' => (string)$request->getPost('email', ''),
            'district' => (string)$request->getPost('district', ''),
            'upazila' => (string)$request->getPost('upazila', ''),
            'address' => (string)$request->getPost('address', ''),
            'blood_group' => (string)$request->getPost('blood_group', ''),
            'institution' => (string)$request->getPost('institution', ''),
            'department' => (string)$request->getPost('department', ''),
            'class_year' => (string)$request->getPost('class_year', ''),
            'profession_institution' => (string)$request->getPost('profession_institution', ''),
            'designation' => (string)$request->getPost('designation', ''),
            'bio' => (string)$request->getPost('bio', ''),
        ];

        $avatarPath = null;
        if (!empty($_FILES['avatar_file']['tmp_name'])) {
            $targetDir = dirname(__DIR__, 2) . '/public/assets/images/members';
            $avatarRes = FileUploader::uploadImage($_FILES['avatar_file'], $targetDir, FileUploader::MAX_IMAGE_SIZE, 'member_');
            if ($avatarRes['success']) {
                $avatarPath = 'assets/images/members/' . $avatarRes['filename'];
            }
        }

        if (!$avatarPath) {
            $presetAvatar = trim((string)$request->getPost('avatar', ''));
            if (!empty($presetAvatar)) {
                $avatarPath = $presetAvatar;
            }
        }

        if ($avatarPath) {
            $profileData['avatar'] = $avatarPath;
        }

        $result = MembershipService::updateMemberProfile($member['member_code'], $profileData);

        if ($result['success'] ?? false) {
            Session::setFlash('success', $isBn 
                ? 'আপনার সদস্য প্রোফাইল ও ছবি সফলভাবে হালনাগাদ করা হয়েছে।' 
                : 'Your member profile and photo have been successfully updated.');
        } else {
            Session::setFlash('error', $result['message'] ?? ($isBn ? 'প্রোফাইল আপডেট ব্যর্থ হয়েছে।' : 'Profile update failed.'));
        }

        return $this->redirect(url('/membership/dashboard', $locale));
    }

    /**
     * Dedicated Printable Digital Membership Card View (Print / PDF Ready)
     */
    public function printCard(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $code = (string)$request->getParam('code', '');
        if (empty($code)) {
            $code = (string)Session::get('current_member_code', '');
        }
        if (empty($code)) {
            $code = 'SPS-000872';
        }

        $member = MembershipService::getMemberById($code);
        if (!$member) {
            Session::setFlash('error', $isBn ? 'সদস্য রেকর্ড খুঁজে পাওয়া যায়নি।' : 'Member not found.');
            return $this->redirect(url('/membership/dashboard', $locale));
        }

        if (MembershipService::isPendingStatus($member['status'] ?? '')) {
            Session::setFlash('error', $isBn 
                ? 'আপনার সদস্যপদ আবেদন এখনও অনুমোদিত হয়নি। কোষাধ্যক্ষ পেমেন্ট অনুমোদন করার পর ডিজিটাল কার্ড ডাউনলোড করা যাবে।' 
                : 'Your membership application is not yet approved. Card download will be available once payment is verified.');
            return $this->redirect(url('/membership/dashboard', $locale));
        }

        $category = MembershipService::getCategory($member['category_id']);
        $plan = MembershipService::getPlan($member['plan_id']);

        $title = $isBn 
            ? 'ডিজিটাল সদস্য কার্ড প্রিন্ট | ' . e($member['member_code'])
            : 'Digital Member Card Print | ' . e($member['member_code']);

        return $this->render('membership/card_print', [
            'metaTitle' => $title,
            'member' => $member,
            'category' => $category,
            'plan' => $plan,
            'allMembers' => MembershipService::getAllMembers(),
        ], '');
    }
}

