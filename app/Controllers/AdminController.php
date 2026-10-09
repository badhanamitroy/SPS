<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\I18n;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\ActivityService;
use App\Services\AuthService;
use App\Services\BlogService;
use App\Services\GoogleAuthService;
use App\Services\HomepageService;
use App\Services\LibraryService;
use App\Services\MembershipService;
use App\Services\RbacService;
use App\Services\TwoFactorService;

class AdminController extends BaseController
{
    /**
     * Enforce authentication guard on all admin operations.
     */
    private function requireAuth(Request $request): ?Response
    {
        if (!AuthService::check()) {
            $locale = I18n::getLocale();
            $isBn = $locale === 'bn';
            Session::setFlash('error', $isBn 
                ? 'অ্যাডমিন প্যানেলে প্রবেশ করতে অনুগ্রহ করে লগইন করুন।' 
                : 'Please log in with admin credentials to access the admin portal.');
            Session::set('auth_return_url', $request->getPath());
            return $this->redirect(url('/admin/login', $locale));
        }

        // Force password change on initial login with temporary credential (Quarantine State)
        if (AuthService::isQuarantined()) {
            $locale = I18n::getLocale();
            $path = $request->getPath();
            $isForceChangeRoute = str_ends_with($path, '/admin/force-password-change') || str_contains($path, 'force-password-change');
            $isLogoutRoute = str_ends_with($path, '/admin/logout') || str_contains($path, '/logout');

            if (!$isForceChangeRoute && !$isLogoutRoute) {
                if ($request->isAjax() || str_contains((string)$request->getHeader('Accept', ''), 'application/json')) {
                    return $this->json([
                        'success' => false,
                        'error' => '403 Forbidden: Quarantine active. Password change required before accessing administrative endpoints.',
                        'redirect' => url('/admin/force-password-change', $locale)
                    ], 403);
                }

                Session::setFlash('warning', $locale === 'bn' 
                    ? 'নিরাপত্তার স্বার্থে প্রশাসন কর্তৃক প্রদত্ত ওয়ান-টাইম পাসওয়ার্ড (OTP) পরিবর্তন করে আপনার নিজস্ব অনন্য পাসওয়ার্ড সেট করা আবশ্যক।' 
                    : 'Security Notice: Please replace your one-time password (OTP) with your secret unique password to access the admin panel.');
                return $this->redirect(url('/admin/force-password-change', $locale));
            }
        }

        return null;
    }

    /**
     * Admin Login Page
     */
    public function loginPage(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        if (AuthService::check()) {
            return $this->redirect(url('/admin', $locale));
        }

        $isBn = $locale === 'bn';
        $title = $isBn 
            ? 'প্রশাসনিক লগইন | সনাতন ফিলোসফি এন্ড স্ক্রিপচার'
            : 'Admin Portal Login | SPS';

        return $this->render('admin/login', [
            'metaTitle' => $title,
            'googleClientId' => GoogleAuthService::getClientId(),
            'isGoogleConfigured' => GoogleAuthService::isConfigured(),
        ]);
    }

    /**
     * Verify Google ID Token for Administrative Sign-In (POST)
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

        // Verify authorization against SPS Admin accounts
        $authResult = GoogleAuthService::authenticateAdmin($profile);
        if (!($authResult['success'] ?? false)) {
            return $this->json([
                'success' => false,
                'error' => $authResult['error'] ?? ($isBn ? 'অননুমোদিত অ্যাক্সেস।' : 'Unauthorized administrator access.')
            ], 403);
        }

        $user = $authResult['user'];
        $userName = $isBn ? ($user['name_bn'] ?? $user['name_en']) : ($user['name_en'] ?? $user['name_bn']);
        $welcomeMsg = $isBn 
            ? "স্বাগতম, {$userName}! গুগল নিরাপত্তার মাধ্যমে সফলভাবে প্রশাসনিক পোর্টালে প্রবেশ করেছেন।" 
            : "Welcome, {$userName}! Successfully signed into SPS Admin Console via Google.";

        Session::setFlash('success', $welcomeMsg);

        return $this->json([
            'success' => true,
            'message' => $welcomeMsg,
            'redirect' => url('/admin', $locale),
            'user' => [
                'name' => $userName,
                'email' => $user['email'] ?? $profile['email'],
                'role' => $user['role'] ?? 'admin'
            ]
        ]);
    }


    /**
     * Process Admin Login Credentials with 2FA
     */
    public function loginProcess(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $identifier = trim((string)$request->getPost('identifier', ''));
        $password = (string)$request->getPost('password', '');

        if (empty($identifier) || empty($password)) {
            Session::setFlash('error', $isBn ? 'ইউজারনেম/ইমেইল এবং পাসওয়ার্ড দেওয়া আবশ্যক।' : 'Username/email and password are required.');
            return $this->redirect(url('/admin/login', $locale));
        }

        $matchedUser = AuthService::validateCredentials($identifier, $password);
        if (!$matchedUser) {
            Session::setFlash('error', $isBn ? 'ভুল ইউজারনেম/ইমেইল অথবা পাসওয়ার্ড। অনুগ্রহ করে পুনরায় চেষ্টা করুন।' : 'Invalid username/email or password. Please try again.');
            return $this->redirect(url('/admin/login', $locale));
        }

        // Two-Factor Authentication via Email
        TwoFactorService::initiateAdminChallenge($matchedUser);
        $userEmail = $matchedUser['email'] ?? 'admin@sps.org';
        $parts = explode('@', $userEmail);
        $namePart = $parts[0];
        $domain = $parts[1] ?? 'sps.org';
        $maskedEmail = (strlen($namePart) > 2 ? substr($namePart, 0, 2) . str_repeat('*', strlen($namePart) - 2) : $namePart) . '@' . $domain;

        Session::setFlash('info', $isBn 
            ? "দ্বিমুখী নিরাপত্তার জন্য ৬-সংখ্যার ভেরিফিকেশন কোড আপনার ইমেইল ({$maskedEmail})-এ পাঠানো হয়েছে।" 
            : "A 6-digit two-factor verification code has been dispatched to your email ({$maskedEmail}).");

        return $this->redirect(url('/admin/2fa', $locale));
    }

    /**
     * Admin 2FA Code Verification Screen (GET)
     */
    public function twoFactorPage(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        if (AuthService::check()) {
            return $this->redirect(url('/admin', $locale));
        }

        $challenge = TwoFactorService::getPendingAdminChallenge();
        if (!$challenge) {
            Session::setFlash('error', $isBn ? 'লগইন সেশন পাওয়া যায়নি। অনুগ্রহ করে পুনরায় লগইন করুন।' : 'No active login session. Please sign in again.');
            return $this->redirect(url('/admin/login', $locale));
        }

        $title = $isBn ? 'দ্বিমুখী প্রমাণীকরণ (2FA) | এসপিএস অ্যাডমিন' : 'Two-Factor Authentication (2FA) | SPS Admin';

        return $this->render('admin/2fa', [
            'metaTitle' => $title,
            'challenge' => $challenge,
        ]);
    }

    /**
     * Admin 2FA Verification Process (POST)
     */
    public function twoFactorVerify(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $code = trim((string)$request->getPost('code', ''));
        if (empty($code)) {
            Session::setFlash('error', $isBn ? 'অনুগ্রহ করে ৬-সংখ্যার ভেরিফিকেশন কোডটি প্রদান করুন।' : 'Please enter the 6-digit verification code.');
            return $this->redirect(url('/admin/2fa', $locale));
        }

        $result = TwoFactorService::verifyAdminChallenge($code);
        if (!($result['success'] ?? false)) {
            Session::setFlash('error', $result['message']);
            return $this->redirect(url('/admin/2fa', $locale));
        }

        // 2FA passed! Establish admin session
        AuthService::loginAs($result['user_id']);
        $returnUrl = Session::get('auth_return_url');
        Session::remove('auth_return_url');

        Session::setFlash('success', $isBn 
            ? 'দ্বিমুখী প্রমাণীকরণ সফল হয়েছে! প্রশাসনিক নিয়ন্ত্রণকক্ষে স্বাগতম।' 
            : 'Two-factor authentication successful! Welcome to the Admin Console.');

        return $this->redirect($returnUrl ?: url('/admin', $locale));
    }

    /**
     * Resend Admin 2FA OTP Code
     */
    public function twoFactorResend(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $challenge = TwoFactorService::getPendingAdminChallenge();
        if (!$challenge) {
            Session::setFlash('error', $isBn ? 'সেশন পাওয়া যায়নি। পুনরায় লগইন করুন।' : 'Session not found. Please log in.');
            return $this->redirect(url('/admin/login', $locale));
        }

        $user = RbacService::getUser($challenge['user_id']);
        if ($user) {
            TwoFactorService::initiateAdminChallenge($user);
            Session::setFlash('success', $isBn ? 'নতুন ভেরিফিকেশন কোড আপনার ইমেইলে পুনরায় পাঠানো হয়েছে।' : 'A fresh verification code has been re-sent to your email.');
        }

        return $this->redirect(url('/admin/2fa', $locale));
    }

    /**
     * Admin Profile View (Self-Service)
     */
    public function profilePage(Request $request, string $lang = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $currentUser = AuthService::getCurrentUser();
        $currentRole = $currentUser ? RbacService::getRole($currentUser['role'] ?? '') : null;

        $title = $isBn ? 'আমার প্রোফাইল ও নিরাপত্তা ব্যবস্থাপনা | এসপিএস' : 'My Profile & Security | SPS';

        return $this->render('admin/profile', [
            'metaTitle' => $title,
            'activeNav' => 'admin.profile',
            'currentUser' => $currentUser,
            'currentRole' => $currentRole,
        ], 'admin');
    }

    /**
     * Update Admin Profile & Password
     */
    public function updateProfile(Request $request, string $lang = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $currentUser = AuthService::getCurrentUser();
        $userId = $currentUser['id'] ?? '';

        $profileData = [
            'name_bn' => (string)$request->getPost('name_bn', ''),
            'name_en' => (string)$request->getPost('name_en', ''),
            'email' => (string)$request->getPost('email', ''),
            'phone' => (string)$request->getPost('phone', ''),
            'designation_bn' => (string)$request->getPost('designation_bn', ''),
            'designation_en' => (string)$request->getPost('designation_en', ''),
            'bio' => (string)$request->getPost('bio', ''),
        ];

        // Check if password change was requested
        $currentPassword = (string)$request->getPost('current_password', '');
        $newPassword = (string)$request->getPost('new_password', '');
        $confirmPassword = (string)$request->getPost('confirm_password', '');

        if (!empty($newPassword) || !empty($currentPassword)) {
            if (empty($currentPassword)) {
                Session::setFlash('error', $isBn ? 'পাসওয়ার্ড পরিবর্তনের জন্য বর্তমান পাসওয়ার্ড দেওয়া আবশ্যক।' : 'Current password is required to change password.');
                return $this->redirect(url('/admin/profile', $locale));
            }
            if ($newPassword !== $confirmPassword) {
                Session::setFlash('error', $isBn ? 'নতুন পাসওয়ার্ড ও নিশ্চিতকরণ পাসওয়ার্ড মিলছে না।' : 'New password and confirmation password do not match.');
                return $this->redirect(url('/admin/profile', $locale));
            }
            if (mb_strlen($newPassword) < 6) {
                Session::setFlash('error', $isBn ? 'নতুন পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে।' : 'New password must be at least 6 characters.');
                return $this->redirect(url('/admin/profile', $locale));
            }

            $pwdResult = AuthService::updateAdminPassword($userId, $currentPassword, $newPassword);
            if (!($pwdResult['success'] ?? false)) {
                Session::setFlash('error', $pwdResult['message']);
                return $this->redirect(url('/admin/profile', $locale));
            }
        }

        $res = RbacService::updateUserProfile($userId, $profileData);
        if ($res['success'] ?? false) {
            Session::setFlash('success', $isBn 
                ? 'আপনার অ্যাডমিন প্রোফাইল ও নিরাপত্তা তথ্য সফলভাবে সংরক্ষিত হয়েছে।' 
                : 'Your admin profile and security information have been successfully saved.');
        } else {
            Session::setFlash('error', $res['message'] ?? 'Profile update failed.');
        }

        return $this->redirect(url('/admin/profile', $locale));
    }

    /**
     * Admin Logout
     */
    public function logout(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        AuthService::logout();
        TwoFactorService::clearChallenges();
        Session::setFlash('success', $isBn ? 'আপনি সফলভাবে লগআউট হয়েছেন।' : 'You have been successfully logged out.');
        return $this->redirect(url('/admin/login', $locale));
    }

    /**
     * Central Role-Adaptive Admin Dashboard.
     */
    public function dashboard(Request $request, string $lang = ''): Response
    {
        if ($guard = $this->requireAuth($request)) {
            return $guard;
        }
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $title = $isBn 
            ? 'প্রশাসনিক নিয়ন্ত্রণকক্ষ ও ওভারভিউ | এসপিএস'
            : 'Admin Control Overview | SPS';

        return $this->render('admin/dashboard', [
            'metaTitle' => $title,
            'activeNav' => 'admin.dashboard',
        ], 'admin');
    }

    /**
     * SPS Role & Permission Matrix view.
     */
    public function roles(Request $request, string $lang = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        if (!AuthService::can('roles.view')) {
            return $this->forbidden($request, 'roles.view');
        }

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $title = $isBn 
            ? 'রোল ও পারমিশন ম্যাট্রিক্স (RBAC Engine) | এসপিএস'
            : 'Role & Permission Matrix | SPS';

        return $this->render('admin/roles', [
            'metaTitle' => $title,
            'activeNav' => 'admin.roles',
        ], 'admin');
    }

    /**
     * Admin users management.
     */
    public function users(Request $request, string $lang = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        if (!AuthService::can('users.view')) {
            return $this->forbidden($request, 'users.view');
        }

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $title = $isBn 
            ? 'প্রশাসনিক কর্মকর্তা ও ভূমিকা ব্যবস্থাপনা | এসপিএস'
            : 'Admin Users & Role Assignment | SPS';

        return $this->render('admin/users', [
            'metaTitle' => $title,
            'activeNav' => 'admin.users',
        ], 'admin');
    }

    /**
     * Assign role, departmental tasks/scope, and custom allowances (Super Admin Master Authority).
     */
    public function assignRole(Request $request, string $lang = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        if (!AuthService::can('users.manage_roles') && !AuthService::isSuperAdmin()) {
            return $this->forbidden($request, 'users.manage_roles');
        }

        $targetUserId = (string)$request->getPost('target_user_id', '');
        $newRole = (string)$request->getPost('new_role', '');
        $newScope = (string)$request->getPost('scope', '');
        $customPerms = $request->getPost('custom_permissions', []);
        if (!is_array($customPerms)) {
            $customPerms = [];
        }

        $performer = AuthService::getCurrentUser();

        $targetUser = RbacService::getUser($targetUserId);
        if (!$targetUser) {
            Session::setFlash('error', $isBn ? 'ব্যবহারকারী অ্যাকাউন্ট পাওয়া যায়নি।' : 'User account not found.');
            return $this->redirect(url('/admin/users', $locale));
        }

        if (empty($newRole)) {
            $newRole = $targetUser['role'] ?? 'moderator';
        }
        if (empty($newScope) && isset($targetUser['scope'])) {
            $newScope = $targetUser['scope'];
        }

        $result = RbacService::assignUserRoleAndAllowances(
            $targetUserId,
            $newRole,
            $newScope,
            $customPerms,
            $performer['id']
        );

        if ($result['success']) {
            Session::setFlash('success', $result['message']);
        } else {
            Session::setFlash('error', $result['message']);
        }

        return $this->redirect(url('/admin/users', $locale));
    }

    /**
     * Financial ledger & Maker-Checker demonstration.
     */
    public function finance(Request $request, string $lang = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        if (!AuthService::can('finance.view')) {
            return $this->forbidden($request, 'finance.view');
        }

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $title = $isBn 
            ? 'আর্থিক হিসাব ও মেকার-চেকার নিয়ন্ত্রণ | এসপিএস'
            : 'Financial Governance & Maker-Checker | SPS';

        return $this->render('admin/finance', [
            'metaTitle' => $title,
            'activeNav' => 'admin.finance',
        ], 'admin');
    }

    /**
     * Immutable system audit logs.
     */
    public function auditLogs(Request $request, string $lang = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        if (!AuthService::can('audit_logs.view')) {
            return $this->forbidden($request, 'audit_logs.view');
        }

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $title = $isBn 
            ? 'সিস্টেম নিরীক্ষা ও অপরিবর্তনীয় অডিট লগ | এসপিএস'
            : 'Immutable Audit Trail & Governance | SPS';

        return $this->render('admin/audit_logs', [
            'metaTitle' => $title,
            'activeNav' => 'admin.audit_logs',
        ], 'admin');
    }

    /**
     * Switch simulated admin identity in session.
     */
    public function switchUser(Request $request, string $lang = ''): Response
    {
        $isDebug = (bool)(\App\Core\Env::get('APP_DEBUG', \App\Core\App::config('app.debug', false)));
        if (!$isDebug) {
            return new Response('403 Forbidden: User impersonation is disabled.', 403, ['Content-Type' => 'text/plain']);
        }

        if ($guard = $this->requireAuth($request)) return $guard;

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $userId = (string)$request->getPost('user_id', '');

        if ($userId && AuthService::switchUser($userId)) {
            $user = AuthService::getCurrentUser();
            $role = RbacService::getRole($user['role'] ?? '');
            $roleName = $isBn ? ($role['name_bn'] ?? $user['role']) : ($role['name_en'] ?? $user['role']);
            Session::setFlash('success', $isBn 
                ? "ভূমিকা সফলভাবে পরিবর্তিত হয়েছে: {$roleName} হিসেবে সক্রিয়।" 
                : "Active identity switched to: {$roleName}");
        }

        // Return to referrer or dashboard
        $referer = $_SERVER['HTTP_REFERER'] ?? url('/admin', $locale);
        return $this->redirect($referer);
    }

    /**
     * Library & E-book access requests management.
     */
    public function library(Request $request, string $lang = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        if (!AuthService::can('library.view')) {
            return $this->forbidden($request, 'library.view');
        }

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        
        $books = LibraryService::getBooks();
        $requests = LibraryService::getRequests();
        $downloadRequests = LibraryService::getDownloadRequests();
        $currentRole = LibraryService::getCurrentRole();

        $title = $isBn 
            ? 'প্রশাসনিক নিয়ন্ত্রণকক্ষ — গ্রন্থাগার ও ই-বুক অনুমোদন | এসপিএস'
            : 'Admin Portal — Library & Access Control | SPS';

        return $this->render('admin/library', [
            'metaTitle' => $title,
            'metaDescription' => 'SPS Administrative dashboard for managing PDF libraries, reader access tiers, and viewer requests.',
            'activeNav' => 'admin.library',
            'books' => $books,
            'requests' => $requests,
            'downloadRequests' => $downloadRequests,
            'currentRole' => $currentRole,
            'canonicalUrl' => url('/admin/library', $locale),
            'alternateBn' => url('/admin/library', 'bn'),
            'alternateEn' => url('/admin/library', 'en'),
        ], 'admin');
    }

    /**
     * Update library book configuration (reading access, scope, download permissions, metadata).
     */
    public function updateLibraryBook(Request $request, string $lang = '', string $slug = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        $targetSlug = !empty($slug) ? $slug : $lang;
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        if (!AuthService::can('library.view')) {
            return $this->forbidden($request, 'library.view');
        }

        $book = LibraryService::getBook($targetSlug);
        if (!$book) {
            Session::setFlash('error', $isBn ? 'গ্রন্থটি পাওয়া যায়নি।' : 'Book not found.');
            return $this->redirect(url('/admin/library', $locale));
        }

        $updateData = [
            'title_bn' => $request->post('title_bn', $book['title_bn']),
            'title_en' => $request->post('title_en', $book['title_en']),
            'author_bn' => $request->post('author_bn', $book['author_bn']),
            'author_en' => $request->post('author_en', $book['author_en']),
            'publisher_bn' => $request->post('publisher_bn', $book['publisher_bn'] ?? ''),
            'publisher_en' => $request->post('publisher_en', $book['publisher_en'] ?? ''),
            'category_bn' => $request->post('category_bn', $book['category_bn']),
            'category_en' => $request->post('category_en', $book['category_en']),
            'publication_year' => $request->post('publication_year', $book['publication_year']),
            'reading_access' => $request->post('reading_access', $book['reading_access'] ?? 'paid_members'),
            'reading_scope' => $request->post('reading_scope', $book['reading_scope'] ?? 'full'),
            'preview_start' => $request->post('preview_start', $book['preview_start'] ?? 1),
            'preview_end' => $request->post('preview_end', $book['preview_end'] ?? 20),
            'download_permission' => $request->post('download_permission', $book['download_permission'] ?? 'admin_approval_required'),
            'allow_download_request' => $request->post('allow_download_request') === '1' || $request->post('allow_download_request') === 'on',
            'synopsis_bn' => $request->post('synopsis_bn', $book['synopsis_bn']),
            'synopsis_en' => $request->post('synopsis_en', $book['synopsis_en']),
        ];

        LibraryService::updateBook($targetSlug, $updateData);

        Session::setFlash('success', $isBn 
            ? "গ্রন্থ '{$updateData['title_bn']}'-এর এক্সেস ও কনফিগারেশন সফলভাবে হালনাগাদ করা হয়েছে।" 
            : "Publication '{$updateData['title_en']}' access configuration updated successfully.");

        return $this->redirect(url('/admin/library', $locale));
    }

    /**
     * Update download request status (approve, reject, revoke).
     */
    public function updateDownloadRequest(Request $request, string $lang = '', string $id = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        $targetId = !empty($id) ? $id : $lang;
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        if (!AuthService::can('library.approve_request') && !AuthService::can('library.view')) {
            return $this->forbidden($request, 'library.approve_request');
        }

        $action = $request->getPost('action', 'approve');
        $adminNote = $request->getPost('admin_note', '');
        $expiryDays = (int)$request->getPost('expiry_days', 2);
        if ($expiryDays < 1) $expiryDays = 2;

        if ($action === 'approve') {
            LibraryService::updateDownloadRequestStatus(
                $targetId, 
                'approved', 
                $adminNote ?: ($isBn ? "প্রশাসক কর্তৃক অনুমোদিত ({$expiryDays} দিনের সাময়িক ডাউনলোড পাস)" : "Approved by Admin ({$expiryDays}-day temporary pass)"),
                $expiryDays
            );
            Session::setFlash('success', $isBn ? 'ডাউনলোড আবেদনটি অনুমোদিত হয়েছে এবং সাময়িক সিকিউর টোকেন জেনারেট করা হয়েছে।' : 'Download request approved. Temporary signed token issued.');
        } elseif ($action === 'reject') {
            LibraryService::updateDownloadRequestStatus(
                $targetId, 
                'rejected', 
                $adminNote ?: ($isBn ? 'বর্তমান নীতি অনুযায়ী অফলাইন ডাউনলোড অনুমতি দেওয়া সম্ভব নয়।' : 'Declined per institutional copyright policy.')
            );
            Session::setFlash('warning', $isBn ? 'ডাউনলোড আবেদনটি প্রত্যাখ্যান করা হয়েছে।' : 'Download request rejected.');
        } elseif ($action === 'revoke') {
            LibraryService::updateDownloadRequestStatus(
                $targetId, 
                'revoked', 
                $adminNote ?: ($isBn ? 'প্রশাসক কর্তৃক ডাউনলোড প্রবেশাধিকার বাতিল করা হয়েছে।' : 'Download token revoked by Admin.')
            );
            Session::setFlash('warning', $isBn ? 'ডাউনলোড পাসটি সফলভাবে বাতিল (Revoke) করা হয়েছে।' : 'Download pass revoked successfully.');
        }

        return $this->redirect(url('/admin/library', $locale));
    }

    /**
     * Update reader access request.
     */
    public function updateRequest(Request $request, string $lang = '', string $id = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        $targetId = !empty($id) ? $id : $lang;
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        if (!AuthService::can('library.approve_request')) {
            return $this->forbidden($request, 'library.approve_request');
        }

        $action = $request->getPost('action', 'approve');
        $adminNote = $request->getPost('admin_note', '');

        if ($action === 'approve') {
            LibraryService::updateRequestStatus($targetId, 'approved', $adminNote ?: ($isBn ? 'প্রশাসক কর্তৃক অনুমোদিত (৩০ দিনের পাঠাধিকার)' : 'Approved by Admin (30-day academic pass)'));
            Session::setFlash('success', $isBn ? 'অনুরোধটি সফলভাবে অনুমোদিত হয়েছে।' : 'Reading request successfully approved.');
        } elseif ($action === 'grant_7day') {
            $until = date('Y-m-d', strtotime('+7 days'));
            LibraryService::updateRequestStatus($targetId, 'approved', $isBn ? 'বিশেষ ৭ দিনের উন্মুক্ত পাঠাধিকার অনুমোদিত' : 'Special 7-Day Academic Pass Granted', $until);
            Session::setFlash('success', $isBn ? 'পাঠকের জন্য ৭ দিনের বিশেষ পাঠাধিকার মঞ্জুর করা হয়েছে।' : '7-Day academic pass granted.');
        } elseif ($action === 'reject') {
            LibraryService::updateRequestStatus($targetId, 'rejected', $adminNote ?: ($isBn ? 'বর্তমান সংস্করণে উন্মুক্ত করার সুযোগ নেই' : 'Declined: Membership required'));
            Session::setFlash('warning', $isBn ? 'অনুরোধটি বাতিল হিসেবে চিহ্নিত করা হয়েছে।' : 'Request marked as rejected.');
        }

        return $this->redirect(url('/admin/library', $locale));
    }

    /**
     * Standard 403 Forbidden response.
     */
    protected function forbidden(Request $request, string $requiredPermission = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $response = $this->render('admin/forbidden', [
            'metaTitle' => $isBn ? 'প্রবেশাধিকার সংরক্ষিত (403 Forbidden) | এসপিএস' : 'Access Forbidden (403) | SPS',
            'requiredPermission' => $requiredPermission,
            'activeNav' => 'forbidden',
        ], 'admin');

        $response->setStatusCode(403);
        return $response;
    }

    /**
     * Activities Management List (Strictly Super Admin & Admin)
     */
    public function activities(Request $request, string $lang = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        if (!AuthService::hasRole(['super_admin', 'admin']) || !AuthService::can('activities.view')) {
            return $this->forbidden($request, 'activities.view');
        }

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $title = $isBn 
            ? 'কার্যক্রম ও সেবা মহাযজ্ঞ পরিচালনা | এসপিএস অ্যাডমিন'
            : 'Activities & Grassroots Seva Management | SPS Admin';

        return $this->render('admin/activities', [
            'metaTitle' => $title,
            'activeNav' => 'admin.activities',
            'activities' => ActivityService::getActivities(),
            'categories' => ActivityService::getCategories(),
            'stats' => ActivityService::getStats(),
            'trackedProjects' => ActivityService::getTrackedProjects(),
        ], 'admin');
    }

    /**
     * Create Activity (Strictly Super Admin & Admin)
     */
    public function createActivity(Request $request, string $lang = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        if (!AuthService::hasRole(['super_admin', 'admin']) || !AuthService::can('activities.create')) {
            return $this->forbidden($request, 'activities.create');
        }

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $titleBn = trim((string)$request->getPost('title_bn', ''));
        $titleEn = trim((string)$request->getPost('title_en', ''));
        $year = (int)$request->getPost('year', date('Y'));
        $category = trim((string)$request->getPost('category', 'humanitarian'));

        if (empty($titleBn) && empty($titleEn)) {
            Session::setFlash('error', $isBn ? 'কার্যক্রমের শিরোনাম দেওয়া আবশ্যক।' : 'Activity title is required.');
            return $this->redirect(url('/admin/activities', $locale));
        }

        $data = [
            'title_bn' => $titleBn ?: $titleEn,
            'title_en' => $titleEn ?: $titleBn,
            'year' => $year,
            'date' => trim((string)$request->getPost('date', (string)$year)),
            'category' => $category,
            'location_bn' => trim((string)$request->getPost('location_bn', 'বাংলাদেশ')),
            'location_en' => trim((string)$request->getPost('location_en', 'Bangladesh')),
            'status' => in_array($request->getPost('status'), ['completed', 'in_progress', 'needs_review'], true) ? $request->getPost('status') : 'completed',
            'description_bn' => trim((string)$request->getPost('description_bn', '')),
            'description_en' => trim((string)$request->getPost('description_en', '')),
            'highlights_bn' => trim((string)$request->getPost('highlights_bn', '')),
            'highlights_en' => trim((string)$request->getPost('highlights_en', '')),
            'badge' => trim((string)$request->getPost('badge', 'General Seva')),
            'icon' => trim((string)$request->getPost('icon', '🚩')),
        ];

        ActivityService::addActivity($data, AuthService::getCurrentUser());
        Session::setFlash('success', $isBn ? 'নতুন কার্যক্রম সফলভাবে যুক্ত করা হয়েছে।' : 'New activity successfully added.');

        return $this->redirect(url('/admin/activities', $locale));
    }

    /**
     * Update Activity (Strictly Super Admin & Admin)
     */
    public function updateActivity(Request $request, string $lang = '', string $id = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        if (!AuthService::hasRole(['super_admin', 'admin']) || !AuthService::can('activities.edit')) {
            return $this->forbidden($request, 'activities.edit');
        }

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $targetId = $id ?: ($request->getParam('id') ?? (string)$request->getPost('id', ''));

        if (empty($targetId)) {
            Session::setFlash('error', $isBn ? 'কার্যক্রম আইডি খুঁজে পাওয়া যায়নি।' : 'Activity ID not specified.');
            return $this->redirect(url('/admin/activities', $locale));
        }

        $data = [
            'title_bn' => trim((string)$request->getPost('title_bn', '')),
            'title_en' => trim((string)$request->getPost('title_en', '')),
            'year' => (int)$request->getPost('year', date('Y')),
            'date' => trim((string)$request->getPost('date', '')),
            'category' => trim((string)$request->getPost('category', 'humanitarian')),
            'location_bn' => trim((string)$request->getPost('location_bn', 'বাংলাদেশ')),
            'location_en' => trim((string)$request->getPost('location_en', 'Bangladesh')),
            'status' => in_array($request->getPost('status'), ['completed', 'in_progress', 'needs_review'], true) ? $request->getPost('status') : 'completed',
            'description_bn' => trim((string)$request->getPost('description_bn', '')),
            'description_en' => trim((string)$request->getPost('description_en', '')),
            'highlights_bn' => trim((string)$request->getPost('highlights_bn', '')),
            'highlights_en' => trim((string)$request->getPost('highlights_en', '')),
            'badge' => trim((string)$request->getPost('badge', 'General Seva')),
            'icon' => trim((string)$request->getPost('icon', '🚩')),
        ];

        $updated = ActivityService::updateActivity($targetId, $data, AuthService::getCurrentUser());
        if ($updated) {
            Session::setFlash('success', $isBn ? 'কার্যক্রমের তথ্য সফলভাবে হালনাগাদ করা হয়েছে।' : 'Activity successfully updated.');
        } else {
            Session::setFlash('error', $isBn ? 'কার্যক্রম আপডেট করতে সমস্যা হয়েছে।' : 'Failed to update activity.');
        }

        return $this->redirect(url('/admin/activities', $locale));
    }

    /**
     * Delete Activity (Strictly Super Admin & Admin)
     */
    public function deleteActivity(Request $request, string $lang = '', string $id = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        if (!AuthService::hasRole(['super_admin', 'admin']) || !AuthService::can('activities.delete')) {
            return $this->forbidden($request, 'activities.delete');
        }

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $targetId = $id ?: ($request->getParam('id') ?? (string)$request->getPost('id', ''));

        if (empty($targetId)) {
            Session::setFlash('error', $isBn ? 'কার্যক্রম আইডি খুঁজে পাওয়া যায়নি।' : 'Activity ID not specified.');
            return $this->redirect(url('/admin/activities', $locale));
        }

        $deleted = ActivityService::deleteActivity($targetId, AuthService::getCurrentUser());
        if ($deleted) {
            Session::setFlash('success', $isBn ? 'কার্যক্রম সফলভাবে মুছে ফেলা হয়েছে।' : 'Activity permanently deleted.');
        } else {
            Session::setFlash('error', $isBn ? 'কার্যক্রম মুছতে সমস্যা হয়েছে।' : 'Failed to delete activity.');
        }

        return $this->redirect(url('/admin/activities', $locale));
    }

    /**
     * Blog Moderation Portal (Super Admin, Admin, and Literature-Admin)
     */
    public function blogs(Request $request, string $lang = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        if (!BlogService::canModerateBlogs()) {
            return $this->forbidden($request, 'blog.moderate');
        }

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $pending = BlogService::getPendingBlogs();
        $published = BlogService::getPublishedBlogs();
        $rejected = BlogService::getRejectedBlogs();

        $title = $isBn 
            ? 'ব্লগ মডারেশন ও অনুমোদন | সনাতন ফিলোসফি এন্ড স্ক্রিপচার' 
            : 'Blog Editorial Moderation | SPS Admin Console';

        return $this->render('admin/blogs', [
            'metaTitle' => $title,
            'activeNav' => 'admin.blogs',
            'pendingBlogs' => $pending,
            'publishedBlogs' => $published,
            'rejectedBlogs' => $rejected,
            'currentUser' => AuthService::getCurrentUser(),
        ], 'admin');
    }

    /**
     * Approve Blog Post (Super Admin, Admin, and Literature-Admin)
     */
    public function approveBlog(Request $request, string $lang = '', string $id = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        if (!BlogService::canModerateBlogs()) {
            return $this->forbidden($request, 'blog.approve');
        }

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $targetId = $id ?: ($request->getParam('id') ?? (string)$request->getPost('id', ''));

        if (empty($targetId)) {
            Session::setFlash('error', $isBn ? 'ব্লগ আইডি পাওয়া যায়নি।' : 'Blog ID not provided.');
            return $this->redirect(url('/admin/blogs', $locale));
        }

        $admin = AuthService::getCurrentUser();
        $success = BlogService::approveBlog($targetId, $admin);

        if ($success) {
            Session::setFlash('success', $isBn 
                ? 'ব্লগটি সফলভাবে অনুমোদিত ও উন্মুক্তভাবে প্রকাশিত হয়েছে।' 
                : 'The blog post has been successfully approved and published live.');
        } else {
            Session::setFlash('error', $isBn ? 'ব্লগ অনুমোদন ব্যর্থ হয়েছে।' : 'Failed to approve blog post.');
        }

        return $this->redirect(url('/admin/blogs', $locale));
    }

    /**
     * Reject Blog Post (Super Admin, Admin, and Literature-Admin)
     */
    public function rejectBlog(Request $request, string $lang = '', string $id = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        if (!BlogService::canModerateBlogs()) {
            return $this->forbidden($request, 'blog.reject');
        }

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $targetId = $id ?: ($request->getParam('id') ?? (string)$request->getPost('id', ''));
        $reason = trim((string)$request->getPost('reason', ''));

        if (empty($targetId)) {
            Session::setFlash('error', $isBn ? 'ব্লগ আইডি পাওয়া যায়নি।' : 'Blog ID not provided.');
            return $this->redirect(url('/admin/blogs', $locale));
        }

        $admin = AuthService::getCurrentUser();
        $success = BlogService::rejectBlog($targetId, $admin, $reason);

        if ($success) {
            Session::setFlash('warning', $isBn 
                ? 'ব্লগটি বাতিল হিসেবে চিহ্নিত করা হয়েছে এবং কারণ সংরক্ষণ করা হয়েছে।' 
                : 'The blog post has been rejected with feedback notes.');
        } else {
            Session::setFlash('error', $isBn ? 'ব্লগ বাতিল করতে সমস্যা হয়েছে।' : 'Failed to reject blog.');
        }

        return $this->redirect(url('/admin/blogs', $locale));
    }

    /**
     * Delete Blog Post (Super Admin, Admin, and Literature-Admin)
     */
    public function deleteBlog(Request $request, string $lang = '', string $id = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        if (!BlogService::canModerateBlogs()) {
            return $this->forbidden($request, 'blog.delete');
        }

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $targetId = $id ?: ($request->getParam('id') ?? (string)$request->getPost('id', ''));

        if (empty($targetId)) {
            Session::setFlash('error', $isBn ? 'ব্লগ আইডি পাওয়া যায়নি।' : 'Blog ID not provided.');
            return $this->redirect(url('/admin/blogs', $locale));
        }

        $admin = AuthService::getCurrentUser();
        $success = BlogService::deleteBlog($targetId, $admin);

        if ($success) {
            Session::setFlash('success', $isBn ? 'ব্লগটি স্থায়ীভাবে মুছে ফেলা হয়েছে।' : 'The blog post has been permanently removed.');
        } else {
            Session::setFlash('error', $isBn ? 'ব্লগ মুছতে ব্যর্থ হয়েছে।' : 'Failed to delete blog.');
        }

        return $this->redirect(url('/admin/blogs', $locale));
    }

    /**
     * Membership Management Portal (Super Admin, Admin, and Membership Officer)
     */
    public function members(Request $request, string $lang = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        if (!AuthService::canAny(['members.view', 'members.manage', 'finance.view']) && !AuthService::hasRole(['super_admin', 'admin', 'membership_officer', 'finance_officer'])) {
            return $this->forbidden($request, 'members.view');
        }

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $statusFilter = (string)$request->getParam('status', 'all');
        $categoryFilter = (string)$request->getParam('category', 'all');
        $planFilter = (string)$request->getParam('plan', 'all');
        $search = (string)$request->getParam('q', '');

        $currentUser = AuthService::getCurrentUser();
        $defaultTab = ($currentUser['role'] ?? '') === 'finance_officer' ? 'payments' : 'directory';
        $currentTab = (string)$request->getParam('tab', $defaultTab);

        $allMembers = MembershipService::getAllMembers(
            $statusFilter === 'all' ? null : $statusFilter,
            $categoryFilter === 'all' ? null : $categoryFilter,
            $planFilter === 'all' ? null : $planFilter,
            $search ?: null
        );

        $stats = MembershipService::getMemberStats();
        $categories = MembershipService::getCategories();
        $plans = MembershipService::getPlans();
        $payments = MembershipService::getAllPayments();
        $pendingPayments = MembershipService::getPendingPayments();

        $title = $isBn 
            ? 'সদস্য প্রশাসন ও মেম্বারশিপ পোর্টাল | এসপিএস অ্যাডমিন' 
            : 'Membership Administration Portal | SPS Admin';

        return $this->render('admin/members', [
            'metaTitle' => $title,
            'activeNav' => 'admin.members',
            'members' => $allMembers,
            'stats' => $stats,
            'categories' => $categories,
            'plans' => $plans,
            'payments' => $payments,
            'pendingPayments' => $pendingPayments,
            'statusFilter' => $statusFilter,
            'categoryFilter' => $categoryFilter,
            'planFilter' => $planFilter,
            'searchQuery' => $search,
            'currentTab' => $currentTab,
            'currentUser' => $currentUser,
        ], 'admin');
    }

    /**
     * Approve Member Application (Exclusive authority: Finance Officer)
     */
    public function approveMember(Request $request, string $lang = '', string $id = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $currentUser = AuthService::getCurrentUser();
        $isFinanceOfficer = ($currentUser['role'] ?? '') === 'finance_officer';

        // Strict Policy: Membership approval is solely in the hands of Finance Officer
        if (!$isFinanceOfficer) {
            Session::setFlash('error', $isBn 
                ? 'মেম্বারশিপ অনুমোদন ও পেমেন্ট ভেরিফিকেশনের পূর্ণ ক্ষমতা ফাইন্যান্স অফিসার (কোষাধ্যক্ষ)-এর হাতে ন্যস্ত। সুপার অ্যাডমিন ও অন্য ২ জন অ্যাডমিন শুধুমাত্র মনিটর করতে পারবেন।' 
                : 'Membership approval and verification is exclusively managed by the Finance Officer (Treasurer). Super Admin and Admins have supervisory monitoring access only.');
            return $this->redirect(url('/admin/members', $locale));
        }

        $targetId = $id ?: ($request->getParam('id') ?? (string)$request->getPost('id', ''));

        if (empty($targetId)) {
            Session::setFlash('error', $isBn ? 'সদস্য আইডি পাওয়া যায়নি।' : 'Member ID not provided.');
            return $this->redirect(url('/admin/members', $locale));
        }

        $admin = AuthService::getCurrentUser();
        $success = MembershipService::approveMember($targetId, $admin);

        if ($success) {
            Session::setFlash('success', $isBn 
                ? 'সদস্যপদ আবেদনটি ফাইন্যান্স অফিসার কর্তৃক অনুমোদিত হয়েছে এবং সদস্য সক্রিয় করা হয়েছে।' 
                : 'Membership application successfully approved and member activated by Finance Officer.');
        } else {
            Session::setFlash('error', $isBn ? 'সদস্যপদ অনুমোদন ব্যর্থ হয়েছে।' : 'Failed to approve member.');
        }

        return $this->redirect(url('/admin/members', $locale));
    }

    /**
     * Reject Member Application (Exclusive authority: Finance Officer)
     */
    public function rejectMember(Request $request, string $lang = '', string $id = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $currentUser = AuthService::getCurrentUser();
        $isFinanceOfficer = ($currentUser['role'] ?? '') === 'finance_officer';

        if (!$isFinanceOfficer) {
            Session::setFlash('error', $isBn 
                ? 'মেম্বারশিপ আবেদন বাতিলের পূর্ণ ক্ষমতা ফাইন্যান্স অফিসার (কোষাধ্যক্ষ)-এর হাতে ন্যস্ত। সুপার অ্যাডমিন ও অন্য ২ জন অ্যাডমিন শুধুমাত্র মনিটর করতে পারবেন।' 
                : 'Membership application rejection is exclusively managed by the Finance Officer (Treasurer). Super Admin and Admins have supervisory monitoring access only.');
            return $this->redirect(url('/admin/members', $locale));
        }

        $targetId = $id ?: ($request->getParam('id') ?? (string)$request->getPost('id', ''));
        $reason = trim((string)$request->getPost('reason', ''));

        if (empty($targetId)) {
            Session::setFlash('error', $isBn ? 'সদস্য আইডি পাওয়া যায়নি।' : 'Member ID not provided.');
            return $this->redirect(url('/admin/members', $locale));
        }

        $admin = AuthService::getCurrentUser();
        $success = MembershipService::rejectMember($targetId, $reason, $admin);

        if ($success) {
            Session::setFlash('warning', $isBn 
                ? 'সদস্যপদ আবেদনটি ফাইন্যান্স অফিসার কর্তৃক বাতিল হিসেবে চিহ্নিত করা হয়েছে।' 
                : 'Membership application has been rejected by Finance Officer with audit notes.');
        } else {
            Session::setFlash('error', $isBn ? 'সদস্যপদ বাতিল করতে সমস্যা হয়েছে।' : 'Failed to reject member.');
        }

        return $this->redirect(url('/admin/members', $locale));
    }

    /**
     * Suspend an active member
     */
    public function suspendMember(Request $request, string $lang = '', string $id = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        if (!AuthService::hasRole(['super_admin', 'admin', 'membership_officer'])) {
            return $this->forbidden($request, 'members.manage');
        }

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $targetId = $id ?: ($request->getParam('id') ?? (string)$request->getPost('id', ''));
        $reason = trim((string)$request->getPost('reason', ''));

        if (empty($targetId)) {
            Session::setFlash('error', $isBn ? 'সদস্য আইডি পাওয়া যায়নি।' : 'Member ID not provided.');
            return $this->redirect(url('/admin/members', $locale));
        }

        $admin = AuthService::getCurrentUser();
        $success = MembershipService::suspendMember($targetId, $reason, $admin);

        if ($success) {
            Session::setFlash('warning', $isBn 
                ? 'সদস্যপদ সফলভাবে সাময়িক স্থগিত (Suspended) করা হয়েছে এবং অডিট লগ সংরক্ষিত হয়েছে।' 
                : 'Member status changed to Suspended with audit log preserved.');
        } else {
            Session::setFlash('error', $isBn ? 'সদস্যপদ স্থগিত করতে সমস্যা হয়েছে।' : 'Failed to suspend member.');
        }

        return $this->redirect(url('/admin/members', $locale));
    }

    /**
     * Reactivate a suspended member
     */
    public function activateMember(Request $request, string $lang = '', string $id = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        if (!AuthService::hasRole(['super_admin', 'admin', 'membership_officer'])) {
            return $this->forbidden($request, 'members.manage');
        }

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $targetId = $id ?: ($request->getParam('id') ?? (string)$request->getPost('id', ''));

        $admin = AuthService::getCurrentUser();
        $success = MembershipService::activateMember($targetId, $admin);

        if ($success) {
            Session::setFlash('success', $isBn 
                ? 'সদস্যপদ পুনরায় সক্রিয় (Active) করা হয়েছে।' 
                : 'Member status successfully reactivated to Active.');
        } else {
            Session::setFlash('error', $isBn ? 'সদস্যপদ সক্রিয় করতে সমস্যা হয়েছে।' : 'Failed to activate member.');
        }

        return $this->redirect(url('/admin/members', $locale));
    }

    /**
     * Transition a Student Member to Earning Member
     */
    public function transitionMember(Request $request, string $lang = '', string $id = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        if (!AuthService::hasRole(['super_admin', 'admin', 'membership_officer'])) {
            return $this->forbidden($request, 'members.manage');
        }

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $targetId = $id ?: ($request->getParam('id') ?? (string)$request->getPost('id', ''));
        $newCategory = (string)$request->getPost('new_category', 'EARNING');
        $newPlan = (string)$request->getPost('new_plan', 'EARNING_MONTHLY');
        $reason = trim((string)$request->getPost('reason', 'গ্র্যাজুয়েশন সম্পন্ন করে পেশাজীবনে পদার্পণ'));

        $admin = AuthService::getCurrentUser();
        $success = MembershipService::transitionCategory($targetId, $newCategory, $newPlan, $reason, $admin);

        if ($success) {
            Session::setFlash('success', $isBn 
                ? 'সদস্যের ক্যাটাগরি সফলভাবে ‘উপার্জনশীল সদস্য (Earning Member)’-এ রূপান্তরিত হয়েছে এবং হিস্টোরি সংরক্ষিত হয়েছে।' 
                : 'Member category transitioned to Earning Member with historical audit log.');
        } else {
            Session::setFlash('error', $isBn ? 'ক্যাটাগরি রূপান্তর ব্যর্থ হয়েছে।' : 'Category transition failed.');
        }

        return $this->redirect(url('/admin/members', $locale));
    }

    /**
     * Verify a member payment (Strictly Finance Officer exclusive authority; Super Admin & 2 Admins monitor)
     */
    public function verifyMemberPayment(Request $request, string $lang = '', string $id = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $currentUser = AuthService::getCurrentUser();
        $isFinanceOfficer = ($currentUser['role'] ?? '') === 'finance_officer';

        // Strict Policy: Verification is in the hands of the Finance Officer
        if (!$isFinanceOfficer) {
            Session::setFlash('error', $isBn 
                ? 'মেম্বারশিপ পেমেন্ট ও TrxID ভেরিফিকেশনের পূর্ণ ক্ষমতা ফাইন্যান্স অফিসার (কোষাধ্যক্ষ)-এর হাতে ন্যস্ত। সুপার অ্যাডমিন ও অন্য ২ জন অ্যাডমিন শুধুমাত্র মনিটর করতে পারবেন।' 
                : 'Membership payment verification is strictly reserved for the Finance Officer (Treasurer). Super Admin and Admins have supervisory monitoring access only.');
            return $this->redirect(url('/admin/members', $locale));
        }

        $paymentId = $id ?: ($request->getParam('payment_id') ?? (string)$request->getPost('payment_id', ''));

        $admin = AuthService::getCurrentUser();
        $success = MembershipService::verifyPayment($paymentId, $admin);

        if ($success) {
            Session::setFlash('success', $isBn 
                ? 'পেমেন্ট ও TrxID সফলভাবে ভেরিফাই করা হয়েছে এবং সদস্যের মেয়াদ সক্রিয়/হালনাগাদ করা হয়েছে।' 
                : 'Payment & TrxID successfully verified and membership period extended.');
        } else {
            Session::setFlash('error', $isBn ? 'পেমেন্ট ভেরিফিকেশন ব্যর্থ হয়েছে।' : 'Failed to verify payment.');
        }

        return $this->redirect(url('/admin/members', $locale));
    }

    /**
     * Reject a member payment (Strictly Finance Officer exclusive authority; Super Admin & 2 Admins monitor)
     */
    public function rejectMemberPayment(Request $request, string $lang = '', string $id = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $currentUser = AuthService::getCurrentUser();
        $isFinanceOfficer = ($currentUser['role'] ?? '') === 'finance_officer';

        if (!$isFinanceOfficer) {
            Session::setFlash('error', $isBn 
                ? 'পেমেন্ট বাতিলের পূর্ণ ক্ষমতা ফাইন্যান্স অফিসার (কোষাধ্যক্ষ)-এর হাতে ন্যস্ত। সুপার অ্যাডমিন ও অন্য ২ জন অ্যাডমিন শুধুমাত্র মনিটর করতে পারবেন।' 
                : 'Payment rejection is strictly reserved for the Finance Officer (Treasurer). Super Admin and Admins have supervisory monitoring access only.');
            return $this->redirect(url('/admin/members', $locale));
        }

        $paymentId = $id ?: ($request->getParam('payment_id') ?? (string)$request->getPost('payment_id', ''));
        $reason = trim((string)$request->getPost('reason', 'প্রদত্ত TrxID অথবা পেমেন্ট রসিদ ভেরিফিকেশনে অমিল পাওয়া গিয়েছে।'));

        $admin = AuthService::getCurrentUser();
        $success = MembershipService::rejectPayment($paymentId, $reason, $admin);

        if ($success) {
            Session::setFlash('warning', $isBn 
                ? 'পেমেন্টটি ফাইন্যান্স অফিসার কর্তৃক বাতিল (Rejected) হিসেবে চিহ্নিত করা হয়েছে এবং কারণ সংরক্ষণ করা হয়েছে।' 
                : 'Payment marked as rejected by Finance Officer with audit notes.');
        } else {
            Session::setFlash('error', $isBn ? 'পেমেন্ট বাতিল করতে সমস্যা হয়েছে।' : 'Failed to reject payment.');
        }

        return $this->redirect(url('/admin/members', $locale));
    }

    /**
     * Homepage Sections Management Page (Super Admin, Admin, Content Editor)
     */
    public function homepageSections(Request $request, string $lang = ''): Response
    {
        if ($res = $this->requireAuth($request)) {
            return $res;
        }

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $sections = HomepageService::getSections();

        $title = $isBn 
            ? 'ফ্রন্ট পেজ সেকশন কনফিগারেশন | অ্যাডমিন পোর্টাল' 
            : 'Front Page Sections Configuration | Admin Portal';

        return $this->render('admin/homepage', [
            'metaTitle' => $title,
            'activeNav' => 'admin.homepage',
            'sections' => $sections,
        ]);
    }

    /**
     * Update Homepage Sections (Enabled/Disabled, Order)
     */
    public function updateHomepageSections(Request $request, string $lang = ''): Response
    {
        if ($res = $this->requireAuth($request)) {
            return $res;
        }

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $sectionsInput = $request->getPost('sections', []);
        $currentUser = AuthService::getCurrentUser();

        if (is_array($sectionsInput)) {
            HomepageService::updateSections($sectionsInput, $currentUser);
            Session::setFlash('success', $isBn 
                ? 'ফ্রন্ট পেজের সেকশনগুলোর স্ট্যাটাস ও ক্রমবিন্যাস সফলভাবে সংরক্ষিত হয়েছে।' 
                : 'Homepage sections configuration and ordering have been saved successfully.');
        } else {
            Session::setFlash('error', $isBn ? 'কোনো তথ্য পরিবর্তন করা হয়নি।' : 'No data was provided.');
        }

        return $this->redirect(url('/admin/homepage', $locale));
    }

    /**
     * Admin First-Login Force Password Change Screen (GET)
     */
    public function forcePasswordChangePage(Request $request, string $lang = ''): Response
    {
        if (!AuthService::check()) {
            return $this->redirect(url('/admin/login', I18n::getLocale()));
        }

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $title = $isBn ? 'অনন্য পাসওয়ার্ড নির্ধারণ | এসপিএস অ্যাডমিন' : 'Set Unique Password | SPS Admin';

        return $this->render('admin/force_password_change', [
            'metaTitle' => $title,
        ]);
    }

    /**
     * Process Admin First-Login Force Password Change (POST)
     */
    public function forcePasswordChangeSubmit(Request $request, string $lang = ''): Response
    {
        if (!AuthService::check()) {
            return $this->redirect(url('/admin/login', I18n::getLocale()));
        }

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        // Enforce CSRF check
        if (!$this->validateCsrf($request)) {
            Session::setFlash('error', $isBn ? 'নিরাপত্তা টোকেন অকার্যকর। অনুগ্রহ করে ফর্মটি পুনরায় সাবমিট করুন।' : 'Invalid security token (CSRF). Please resubmit.');
            return $this->redirect(url('/admin/force-password-change', $locale));
        }

        $currentPassword = (string)$request->getPost('current_password', '');
        $newPassword = (string)$request->getPost('new_password', '');
        $newPasswordConfirmation = (string)$request->getPost('new_password_confirmation', '');

        if (empty($currentPassword) || empty($newPassword)) {
            Session::setFlash('error', $isBn ? 'বর্তমান সাময়িক পাসওয়ার্ড এবং নতুন পাসওয়ার্ড উভয়টি দেওয়া আবশ্যক।' : 'Both current temporary password and new password are required.');
            return $this->redirect(url('/admin/force-password-change', $locale));
        }

        if ($newPassword !== $newPasswordConfirmation) {
            Session::setFlash('error', $isBn ? 'নতুন পাসওয়ার্ড এবং নিশ্চিতকরণ পাসওয়ার্ড মিলছে না।' : 'New password and confirmation do not match.');
            return $this->redirect(url('/admin/force-password-change', $locale));
        }

        if (mb_strlen($newPassword) < 10) {
            Session::setFlash('error', $isBn ? 'নতুন নিজস্ব পাসওয়ার্ড কমপক্ষে ১০ অক্ষরের হতে হবে।' : 'New password must be at least 10 characters.');
            return $this->redirect(url('/admin/force-password-change', $locale));
        }

        if ($newPassword === $currentPassword) {
            Session::setFlash('error', $isBn ? 'নতুন পাসওয়ার্ডটি সাময়িক পাসওয়ার্ড থেকে ভিন্ন হতে হবে।' : 'New password must differ from temporary password.');
            return $this->redirect(url('/admin/force-password-change', $locale));
        }

        $user = AuthService::getCurrentUser();
        $result = AuthService::completeInitialPasswordChange($user['id'], $currentPassword, $newPassword);

        if (!($result['success'] ?? false)) {
            Session::setFlash('error', $result['message']);
            return $this->redirect(url('/admin/force-password-change', $locale));
        }

        Session::setFlash('success', $isBn 
            ? 'আপনার নিজস্ব অনন্য পাসওয়ার্ড সফলভাবে নির্ধারিত হয়েছে! প্রশাসনিক ড্যাশবোর্ডে স্বাগতম।' 
            : 'Your unique personal password has been successfully established! Welcome to the Admin Console.');

        return $this->redirect(url('/admin', $locale));
    }

    /**
     * Super Admin: Create a new admin officer with a generated One-Time Password (OTP) (POST)
     */
    public function createAdminUser(Request $request, string $lang = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        if (!AuthService::isSuperAdmin()) {
            Session::setFlash('error', $isBn ? 'শুধুমাত্র সুপার অ্যাডমিন নতুন প্রশাসনিক কর্মকর্তা যুক্ত করতে পারেন।' : 'Only Super Admin can create admin users.');
            return $this->redirect(url('/admin/users', $locale));
        }

        $currentAdmin = AuthService::getCurrentUser();
        $userData = [
            'name_bn' => trim((string)$request->getPost('name_bn', '')),
            'name_en' => trim((string)$request->getPost('name_en', '')),
            'username' => trim((string)$request->getPost('username', '')),
            'email' => trim((string)$request->getPost('email', '')),
            'phone' => trim((string)$request->getPost('phone', '')),
            'role' => (string)$request->getPost('role', 'moderator'),
            'designation_bn' => trim((string)$request->getPost('designation_bn', '')),
            'designation_en' => trim((string)$request->getPost('designation_en', '')),
            'scope' => trim((string)$request->getPost('scope', '')),
        ];

        $res = \App\Services\RbacService::createAdminUserWithOtp($userData, $currentAdmin['id']);
        if ($res['success']) {
            Session::setFlash('success', $res['message']);
        } else {
            Session::setFlash('error', $res['message']);
        }

        return $this->redirect(url('/admin/users', $locale));
    }

    /**
     * Super Admin: Reset an officer's password to a fresh One-Time Password (OTP) (POST)
     */
    public function resetAdminOtp(Request $request, string $lang = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        if (!AuthService::isSuperAdmin()) {
            Session::setFlash('error', $isBn ? 'শুধুমাত্র সুপার অ্যাডমিন পাসওয়ার্ড রিসেট করতে পারেন।' : 'Only Super Admin can reset admin passwords.');
            return $this->redirect(url('/admin/users', $locale));
        }

        $targetUserId = (string)$request->getPost('target_user_id', '');
        $currentAdmin = AuthService::getCurrentUser();

        $res = \App\Services\RbacService::resetAdminOtp($targetUserId, $currentAdmin['id']);
        if ($res['success']) {
            Session::setFlash('success', $res['message']);
        } else {
            Session::setFlash('error', $res['message']);
        }

        return $this->redirect(url('/admin/users', $locale));
    }
}