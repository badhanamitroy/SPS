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
use App\Services\LibraryService;
use App\Services\MembershipService;
use App\Services\RbacService;

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
        ]);
    }

    /**
     * Process Admin Login Credentials
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

        if (AuthService::attempt($identifier, $password)) {
            $returnUrl = Session::get('auth_return_url');
            Session::remove('auth_return_url');
            Session::setFlash('success', $isBn ? 'সফলভাবে লগইন হয়েছে। প্রশাসনিক নিয়ন্ত্রণকক্ষে স্বাগতম!' : 'Successfully signed in. Welcome to the Admin Console!');
            return $this->redirect($returnUrl ?: url('/admin', $locale));
        }

        Session::setFlash('error', $isBn ? 'ভুল ইউজারনেম/ইমেইল অথবা পাসওয়ার্ড। অনুগ্রহ করে পুনরায় চেষ্টা করুন।' : 'Invalid username/email or password. Please try again.');
        return $this->redirect(url('/admin/login', $locale));
    }

    /**
     * Admin Logout
     */
    public function logout(Request $request, string $lang = ''): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        AuthService::logout();
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
            'currentRole' => $currentRole,
            'canonicalUrl' => url('/admin/library', $locale),
            'alternateBn' => url('/admin/library', 'bn'),
            'alternateEn' => url('/admin/library', 'en'),
        ], 'admin');
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
    protected function forbidden(Request $request, string $requiredPermission): Response
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
     * Admin Profile Self-Service Page
     */
    public function profilePage(Request $request, string $lang = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $currentUser = AuthService::getCurrentUser();
        $currentRole = RbacService::getRole($currentUser['role'] ?? '');

        $title = $isBn 
            ? 'আমার প্রোফাইল | সনাতন ফিলোসফি এন্ড স্ক্রিপচার' 
            : 'My Admin Profile | SPS';

        return $this->render('admin/profile', [
            'metaTitle' => $title,
            'activeNav' => 'admin.profile',
            'currentUser' => $currentUser,
            'currentRole' => $currentRole,
            'roles' => RbacService::getRoles(),
        ]);
    }

    /**
     * Update Admin Profile Details (Self-Service)
     */
    public function updateProfile(Request $request, string $lang = ''): Response
    {
        if ($guard = $this->requireAuth($request)) return $guard;

        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';
        $currentUser = AuthService::getCurrentUser();

        $nameBn = trim((string)$request->getPost('name_bn', ''));
        $nameEn = trim((string)$request->getPost('name_en', ''));
        $email = trim((string)$request->getPost('email', ''));
        $phone = trim((string)$request->getPost('phone', ''));
        $bio = trim((string)$request->getPost('bio', ''));
        $newPassword = (string)$request->getPost('new_password', '');
        $confirmPassword = (string)$request->getPost('confirm_password', '');

        if (empty($nameBn) && empty($nameEn)) {
            Session::setFlash('error', $isBn ? 'অনুগ্রহ করে অন্তত একটি ভাষায় আপনার নাম প্রদান করুন।' : 'Please provide your name.');
            return $this->redirect(url('/admin/profile', $locale));
        }

        $profileData = [
            'name_bn' => $nameBn ?: ($currentUser['name_bn'] ?? ''),
            'name_en' => $nameEn ?: ($currentUser['name_en'] ?? ''),
            'email' => $email ?: ($currentUser['email'] ?? ''),
            'phone' => $phone,
            'bio' => $bio,
        ];

        // Password change handling (optional)
        if (!empty($newPassword)) {
            if (strlen($newPassword) < 6) {
                Session::setFlash('error', $isBn ? 'নতুন পাসওয়ার্ড অন্তত ৬ অক্ষরের হতে হবে।' : 'New password must be at least 6 characters.');
                return $this->redirect(url('/admin/profile', $locale));
            }
            if ($newPassword !== $confirmPassword) {
                Session::setFlash('error', $isBn ? 'পাসওয়ার্ড এবং নিশ্চিতকরণ পাসওয়ার্ড মেলেনি।' : 'New password and confirmation do not match.');
                return $this->redirect(url('/admin/profile', $locale));
            }
            $profileData['password'] = $newPassword;
        }

        $res = RbacService::updateUserProfile($currentUser['id'], $profileData);

        if ($res['success'] ?? false) {
            Session::setFlash('success', $isBn 
                ? 'আপনার অ্যাডমিন প্রোফাইল সফলভাবে হালনাগাদ করা হয়েছে।' 
                : 'Your admin profile has been successfully updated.');
        } else {
            Session::setFlash('error', $res['message'] ?? ($isBn ? 'প্রোফাইল আপডেট ব্যর্থ হয়েছে।' : 'Failed to update profile.'));
        }

        return $this->redirect(url('/admin/profile', $locale));
    }
}

