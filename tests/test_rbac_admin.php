<?php

declare(strict_types=1);

namespace App\Tests;

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\App;
use App\Core\I18n;
use App\Core\Request;
use App\Core\Router;
use App\Core\Session;
use App\Core\View;
use App\Services\ActivityService;
use App\Services\AuditService;
use App\Services\AuthService;
use App\Services\RbacService;

$passed = 0;
$failed = 0;

function assert_test(string $name, bool $condition, string $detail = ''): void {
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo "[PASS] {$name}\n";
    } else {
        $failed++;
        echo "[FAIL] {$name} - {$detail}\n";
    }
}

echo "====================================================\n";
echo "SPS Role-Based Admin & RBAC Security Verification\n";
echo "====================================================\n";

$app = new App(dirname(__DIR__));
I18n::init(require dirname(__DIR__) . '/config/languages.php', 'bn', '/bn');
View::init(dirname(__DIR__) . '/app/Views');

// 1. Roles Verification (All 12 Roles)
$roles = RbacService::getRoles();
assert_test("RBAC: Exactly 12 roles defined", count($roles) === 12);
$expectedRoles = [
    'super_admin', 'admin', 'content_editor', 'blog_moderator',
    'project_manager', 'finance_officer', 'library_manager', 'order_manager',
    'membership_officer', 'volunteer_coordinator', 'moderator', 'auditor'
];
foreach ($expectedRoles as $roleId) {
    assert_test("Role exists: {$roleId}", isset($roles[$roleId]));
}

// 2. Permissions Verification
$permissions = RbacService::getPermissions();
assert_test("RBAC: Permissions defined", count($permissions) >= 30);
$grouped = RbacService::getPermissionsGrouped();
assert_test("RBAC: Grouped permissions include finance, library, users, roles", 
    isset($grouped['finance'], $grouped['library'], $grouped['users'], $grouped['roles']));

// 3. Permission Checks for Roles
assert_test("Super Admin has all permissions (finance.create_expense)", RbacService::roleHasPermission('super_admin', 'finance.create_expense'));
assert_test("Super Admin has all permissions (users.manage_roles)", RbacService::roleHasPermission('super_admin', 'users.manage_roles'));
assert_test("Finance Officer has finance.create_expense", RbacService::roleHasPermission('finance_officer', 'finance.create_expense'));
assert_test("Finance Officer does NOT have users.manage_roles", !RbacService::roleHasPermission('finance_officer', 'users.manage_roles'));
assert_test("Finance Officer does NOT have library.approve_request", !RbacService::roleHasPermission('finance_officer', 'library.approve_request'));
assert_test("Library Manager has library.approve_request", RbacService::roleHasPermission('library_manager', 'library.approve_request'));
assert_test("Library Manager does NOT have finance.approve_expense", !RbacService::roleHasPermission('library_manager', 'finance.approve_expense'));
assert_test("Auditor has finance.view (Read-only)", RbacService::roleHasPermission('auditor', 'finance.view'));
assert_test("Auditor does NOT have finance.create_expense", !RbacService::roleHasPermission('auditor', 'finance.create_expense'));
assert_test("Auditor does NOT have blog.publish", !RbacService::roleHasPermission('auditor', 'blog.publish'));

// 4. Role Escalation Security
// Administrator (usr_robin) trying to assign Super Admin should be rejected
$resEscalate = RbacService::assignRole('usr_pranto', 'super_admin', 'usr_robin');
assert_test("Security: Non-SuperAdmin cannot assign SuperAdmin role", $resEscalate['success'] === false);

// User trying to change their own role should be rejected
$resSelf = RbacService::assignRole('usr_robin', 'super_admin', 'usr_robin');
assert_test("Security: User cannot change their own role", $resSelf['success'] === false);

// Super Admin (usr_anik) can assign regular roles
$resValid = RbacService::assignRole('usr_pranto', 'content_editor', 'usr_anik');
assert_test("Security: Super Admin can assign roles", $resValid['success'] === true);

// 5. Maker-Checker Financial Separation
AuthService::switchUser('usr_joy');
assert_test("Maker-Checker: Maker cannot approve own expense", !AuthService::isEligibleChecker('usr_joy'));
AuthService::switchUser('usr_robin');
assert_test("Maker-Checker: Different admin can act as checker", AuthService::isEligibleChecker('usr_joy'));

// 6. Immutable Audit Trail
$logEntry = AuditService::log('test.verify', 'system', 'target_001', 'Test Target', null, ['status' => 'verified'], 'Automated test suite entry');
// Verify by inspecting the returned entry (count() saturates at the 1000-record cap)
$logs = AuditService::all();
$mostRecent = $logs[0] ?? null;
assert_test("Audit Trail: Appended new immutable log",
    !empty($logEntry['id']) &&
    ($mostRecent['action'] ?? '') === 'test.verify' &&
    ($mostRecent['resource'] ?? '') === 'system',
    'Log entry action/resource mismatch or log empty'
);

// 7. Route and Authorization Enforcement
$router = new Router();
require dirname(__DIR__) . '/routes/web.php';

// Super Admin accessing all admin areas
AuthService::switchUser('usr_anik');
$resDash = $router->dispatch(new Request('GET', '/bn/admin'));
assert_test("Router: /bn/admin returns 200 for Super Admin", $resDash->getStatusCode() === 200);

$resRoles = $router->dispatch(new Request('GET', '/bn/admin/roles'));
assert_test("Router: /bn/admin/roles returns 200 for Super Admin", $resRoles->getStatusCode() === 200);

$resUsers = $router->dispatch(new Request('GET', '/bn/admin/users'));
assert_test("Router: /bn/admin/users returns 200 for Super Admin", $resUsers->getStatusCode() === 200);

$resFinance = $router->dispatch(new Request('GET', '/bn/admin/finance'));
assert_test("Router: /bn/admin/finance returns 200 for Super Admin", $resFinance->getStatusCode() === 200);

$resAudit = $router->dispatch(new Request('GET', '/bn/admin/audit-logs'));
assert_test("Router: /bn/admin/audit-logs returns 200 for Super Admin", $resAudit->getStatusCode() === 200);

// Switch user to Literature/Content Editor (usr_pranto) and test 403 Forbidden enforcement!
AuthService::switchUser('usr_pranto');
$resEditorDash = $router->dispatch(new Request('GET', '/bn/admin'));
assert_test("Router: /bn/admin returns 200 for Content Editor", $resEditorDash->getStatusCode() === 200);

// Literature Admin trying to access /bn/admin/finance must be blocked with 403 Forbidden!
$resEditorFinance = $router->dispatch(new Request('GET', '/bn/admin/finance'));
assert_test("Security: /bn/admin/finance returns 403 Forbidden for Content Editor", $resEditorFinance->getStatusCode() === 403);
assert_test("403 Page: Contains forbidden notice and required permission", 
    str_contains($resEditorFinance->getContent(), '403 Forbidden') && str_contains($resEditorFinance->getContent(), 'finance.view'));

// Literature Admin trying to access /bn/admin/users must be blocked with 403 Forbidden!
$resEditorUsers = $router->dispatch(new Request('GET', '/bn/admin/users'));
assert_test("Security: /bn/admin/users returns 403 Forbidden for Content Editor", $resEditorUsers->getStatusCode() === 403);

// 8. Activities Management RBAC (Strictly Super-Admin & Admin)
AuthService::switchUser('usr_anik'); // Super Admin
$resSuperAct = $router->dispatch(new Request('GET', '/bn/admin/activities'));
assert_test("Router: /bn/admin/activities returns 200 for Super Admin", $resSuperAct->getStatusCode() === 200);
assert_test("View: /bn/admin/activities contains Add Activity button", str_contains($resSuperAct->getContent(), 'নতুন কার্যক্রম যুক্ত করুন'));

AuthService::switchUser('usr_robin'); // Admin
$resAdminAct = $router->dispatch(new Request('GET', '/bn/admin/activities'));
assert_test("Router: /bn/admin/activities returns 200 for Admin", $resAdminAct->getStatusCode() === 200);

AuthService::switchUser('usr_pranto'); // Content Editor
$resEditorAct = $router->dispatch(new Request('GET', '/bn/admin/activities'));
assert_test("Security: /bn/admin/activities returns 403 Forbidden for Content Editor", $resEditorAct->getStatusCode() === 403);

// Content Editor POST create must be blocked
$resEditorPost = $router->dispatch(new Request('POST', '/bn/admin/activities/create', ['title_bn' => 'অননুমোদিত প্রজেক্ট']));
assert_test("Security: POST /bn/admin/activities/create returns 403 for Content Editor", $resEditorPost->getStatusCode() === 403);

// Content Editor POST update must be blocked
$resEditorUpdate = $router->dispatch(new Request('POST', '/bn/admin/activities/update/act_notion_2024_ramnavami_tree', ['title_bn' => 'হ্যাকড']));
assert_test("Security: POST /bn/admin/activities/update returns 403 for Content Editor", $resEditorUpdate->getStatusCode() === 403);

// Content Editor POST delete must be blocked
$resEditorDelete = $router->dispatch(new Request('POST', '/bn/admin/activities/delete/act_notion_2024_ramnavami_tree'));
assert_test("Security: POST /bn/admin/activities/delete returns 403 for Content Editor", $resEditorDelete->getStatusCode() === 403);

// 9. Functional CRUD Verification as Super Admin & Admin
AuthService::switchUser('usr_anik'); // Super Admin
$newActData = [
    'title_bn' => 'টেস্ট সেবামূলক কার্যক্রম ২০২৬',
    'title_en' => 'Test Seva Activity 2026',
    'year' => 2026,
    'date' => 'মার্চ ২০২৬',
    'category' => 'humanitarian',
    'location_bn' => 'ঢাকা, বাংলাদেশ',
    'location_en' => 'Dhaka, Bangladesh',
    'status' => 'completed',
    'description_bn' => 'টেস্টিং বিস্তারিত বিবরণ',
    'description_en' => 'Testing description details',
    'badge' => 'Special Test',
    'icon' => '🌟',
];
$resCreate = $router->dispatch(new Request('POST', '/bn/admin/activities/create', [], $newActData));
assert_test("Super Admin: Create activity returns 302 Redirect", $resCreate->getStatusCode() === 302);

// Verify activity created in storage
$allActs = ActivityService::getActivities();
$createdAct = null;
foreach ($allActs as $act) {
    if (($act['title_bn'] ?? '') === 'টেস্ট সেবামূলক কার্যক্রম ২০২৬') {
        $createdAct = $act;
        break;
    }
}
assert_test("Storage: Created activity exists in database", $createdAct !== null);
$createdId = $createdAct['id'] ?? '';

// Admin updates the created activity
AuthService::switchUser('usr_robin'); // Admin
$updateData = $newActData;
$updateData['title_bn'] = 'হালনাগাদকৃত সেবামূলক কার্যক্রম ২০২৬';
$resUpdate = $router->dispatch(new Request('POST', "/bn/admin/activities/update/{$createdId}", [], $updateData));
assert_test("Admin: Update activity returns 302 Redirect", $resUpdate->getStatusCode() === 302);

$updatedAct = ActivityService::getActivityById($createdId);
assert_test("Storage: Activity title successfully updated", ($updatedAct['title_bn'] ?? '') === 'হালনাগাদকৃত সেবামূলক কার্যক্রম ২০২৬');

// Admin deletes the activity
$resDelete = $router->dispatch(new Request('POST', "/bn/admin/activities/delete/{$createdId}"));
assert_test("Admin: Delete activity returns 302 Redirect", $resDelete->getStatusCode() === 302);

$deletedCheck = ActivityService::getActivityById($createdId);
assert_test("Storage: Activity successfully deleted from database", $deletedCheck === null);

// 10. Admin Authentication & Login Guard Verification
AuthService::logout();
assert_test("Auth: Logged out user has check() = false", !AuthService::check());

// Unauthenticated access must redirect to /bn/admin/login
$resGuestAdmin = $router->dispatch(new Request('GET', '/bn/admin'));
assert_test("Security: Unauthenticated /bn/admin redirects with 302", $resGuestAdmin->getStatusCode() === 302);
assert_test("Security: Redirects to /bn/admin/login", str_contains($resGuestAdmin->getHeaders()['Location'] ?? '', '/bn/admin/login'));

$resGuestActivities = $router->dispatch(new Request('GET', '/bn/admin/activities'));
assert_test("Security: Unauthenticated /bn/admin/activities redirects with 302", $resGuestActivities->getStatusCode() === 302);

// Login Page rendering
$resLoginPage = $router->dispatch(new Request('GET', '/bn/admin/login'));
assert_test("Router: /bn/admin/login returns 200", $resLoginPage->getStatusCode() === 200);
assert_test("View: Login page contains username/email and password inputs", 
    str_contains($resLoginPage->getContent(), 'name="identifier"') && str_contains($resLoginPage->getContent(), 'name="password"'));

// SETUP: Ensure admin password at baseline and rate limits cleared for this test block
\App\Core\RateLimiter::resetAttempts('login:admin:acct:' . hash('sha256', 'anik'));
\App\Core\RateLimiter::resetAttempts('2fa:resend:admin:usr_anik');
\App\Core\RateLimiter::resetAttempts('2fa:verify:admin:usr_anik:127.0.0.1');
\App\Services\RbacService::updatePasswordHashDirect('usr_anik', \App\Core\CryptoService::hashPassword('sps@admin2026'));

// Attempt login with invalid credentials
$resFailedLogin = $router->dispatch(new Request('POST', '/bn/admin/login', [], ['identifier' => 'anik', 'password' => 'wrongpass']));
assert_test("Security: Invalid credentials login fails with 302", $resFailedLogin->getStatusCode() === 302);
assert_test("Auth: Remains unauthenticated after failed login", !AuthService::check());

// Attempt login with valid credentials (username + password triggers 2FA)
$resValidLogin = $router->dispatch(new Request('POST', '/bn/admin/login', [], ['identifier' => 'anik', 'password' => 'sps@admin2026']));
assert_test("Auth: Valid login credentials redirect to 2FA verification", $resValidLogin->getStatusCode() === 302 && str_contains($resValidLogin->getHeaders()['Location'] ?? '', '/admin/2fa'));

// Complete 2FA verification using static OTP capture (test-only mechanism)
$otpCode = \App\Services\TwoFactorService::getLastAdminOtp();
$res2fa = $router->dispatch(new Request('POST', '/bn/admin/2fa', [], ['code' => $otpCode ?? '']));
assert_test("Auth: Valid 2FA code redirects to admin console", $res2fa->getStatusCode() === 302);
assert_test("Auth: Session successfully authenticated as Super Admin", AuthService::check() && AuthService::isSuperAdmin());

// Topbar verification: Role simulator removed, Logout button present
$resAdminView = $router->dispatch(new Request('GET', '/bn/admin'));
assert_test("View: Admin topbar does NOT contain Role Simulator", !str_contains($resAdminView->getContent(), 'রোল সিমুলেটর:') && !str_contains($resAdminView->getContent(), 'name="user_id"'));
assert_test("View: Admin topbar contains Logout button", str_contains($resAdminView->getContent(), 'adminLogoutBtn') || str_contains($resAdminView->getContent(), 'লগআউট'));

// Logout process
$resLogout = $router->dispatch(new Request('POST', '/bn/admin/logout'));
assert_test("Auth: Logout returns 302 redirect", $resLogout->getStatusCode() === 302);
assert_test("Auth: Logged out successfully", !AuthService::check());

// 11. Super Admin Role, Task Scope & Custom Allowance Assignment
AuthService::switchUser('usr_anik'); // Super Admin

// A. Super Admin assigns specific tasks and custom permission allowances to an officer
$assignmentResult = \App\Services\RbacService::assignUserRoleAndAllowances(
    'usr_goutam',
    'content_editor',
    'শাস্ত্রীয় সাহিত্য ও ডিজিটাল গবেষণা বিভাগ',
    ['blog.publish', 'library.manage'],
    'usr_anik'
);
assert_test("Super Admin: Successfully assigns role, scope, and allowances via RbacService", $assignmentResult['success']);

$goutam = \App\Services\RbacService::getUser('usr_goutam');
assert_test("Storage: User scope updated to 'শাস্ত্রীয় সাহিত্য ও ডিজিটাল গবেষণা বিভাগ'", $goutam['scope'] === 'শাস্ত্রীয় সাহিত্য ও ডিজিটাল গবেষণা বিভাগ');
assert_test("Storage: User has custom permissions ['blog.publish', 'library.manage']", 
    in_array('blog.publish', $goutam['custom_permissions'] ?? [], true) && 
    in_array('library.manage', $goutam['custom_permissions'] ?? [], true)
);

// B. Verify that AuthService honors the Super Admin's custom allowances
AuthService::switchUser('usr_goutam');
assert_test("Auth: User now has 'blog.publish' through Super Admin custom allowance", AuthService::can('blog.publish'));
assert_test("Auth: User now has 'library.manage' through Super Admin custom allowance", AuthService::can('library.manage'));
assert_test("Auth: User does NOT have unauthorized permission 'finance.approve_expense'", !AuthService::can('finance.approve_expense'));

// C. Test Super Admin assignment via Router HTTP POST
AuthService::switchUser('usr_anik');
$postAssign = $router->dispatch(new Request('POST', '/bn/admin/users/assign-role', [], [
    'target_user_id' => 'usr_rahul',
    'new_role' => 'project_manager',
    'scope' => 'সেবা ও শিক্ষা তহবিল ব্যবস্থাপনা',
    'custom_permissions' => ['activities.create', 'volunteers.assign'],
]));
assert_test("Router: Super Admin POST /bn/admin/users/assign-role returns 302", $postAssign->getStatusCode() === 302);

$rahul = \App\Services\RbacService::getUser('usr_rahul');
assert_test("Storage: Rahul role updated to project_manager", $rahul['role'] === 'project_manager');
assert_test("Storage: Rahul scope updated to 'সেবা ও শিক্ষা তহবিল ব্যবস্থাপনা'", $rahul['scope'] === 'সেবা ও শিক্ষা তহবিল ব্যবস্থাপনা');
assert_test("Storage: Rahul granted custom allowances ['activities.create', 'volunteers.assign']",
    in_array('activities.create', $rahul['custom_permissions'] ?? [], true) &&
    in_array('volunteers.assign', $rahul['custom_permissions'] ?? [], true)
);

// Switch back to Super Admin for test completion
AuthService::switchUser('usr_anik');

echo "\n----------------------------------------------------\n";
echo "Total Tests: " . ($passed + $failed) . " | Passed: {$passed} | Failed: {$failed}\n";
echo "====================================================\n";

if ($failed > 0) exit(1);

