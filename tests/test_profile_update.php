<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\App;
use App\Core\Request;
use App\Core\Router;
use App\Core\Session;
use App\Services\AuthService;
use App\Services\MembershipService;
use App\Services\RbacService;

echo "=== SPS PROFILE UPDATE SYSTEM TEST SUITE ===\n\n";

$baseDir = dirname(__DIR__);
$app = new App($baseDir);

$router = new Router();
$routesFile = $baseDir . '/routes/web.php';
require $routesFile;

function assertCondition(bool $condition, string $message): void
{
    if ($condition) {
        echo "  [PASS] {$message}\n";
    } else {
        echo "  [FAIL] {$message}\n";
        exit(1);
    }
}

// ==========================================
// 1. Member Profile Update Service & HTTP
// ==========================================
echo "1. Testing Member Profile Update Service:\n";

$memberCode = 'SPS-000872';
$originalMember = MembershipService::getMemberById($memberCode);
assertCondition($originalMember !== null, "Target member SPS-000872 exists");

$updateResult = MembershipService::updateMemberProfile($memberCode, [
    'name_bn' => 'অমিত সেন শর্মা',
    'phone' => '+8801711999888',
    'district' => 'চট্টগ্রাম',
    'blood_group' => 'O+',
    'institution' => 'বাংলাদেশ প্রকৌশল বিশ্ববিদ্যালয় (বুয়েট)',
    'designation' => 'সিনিয়র রিসার্চ ফেলো',
    'bio' => 'সনাতন বেদান্ত দর্শন ও শ্লোক পর্যালোচনা গবেষক।',
    // Attempt to tamper with immutable fields (should be ignored)
    'member_code' => 'SPS-999999',
    'status' => 'Suspended',
    'category_id' => 'EARNING',
]);

assertCondition($updateResult['success'] === true, "Member profile update succeeded");

$refreshed = MembershipService::getMemberById($memberCode);
assertCondition($refreshed['name_bn'] === 'অমিত সেন শর্মা', "Name BN updated to 'অমিত সেন শর্মা'");
assertCondition($refreshed['phone'] === '+8801711999888', "Phone updated to '+8801711999888'");
assertCondition($refreshed['district'] === 'চট্টগ্রাম', "District updated to 'চট্টগ্রাম'");
assertCondition($refreshed['blood_group'] === 'O+', "Blood group updated to 'O+'");
assertCondition($refreshed['education']['institution'] === 'বাংলাদেশ প্রকৌশল বিশ্ববিদ্যালয় (বুয়েট)', "Institution updated in education profile");
assertCondition($refreshed['member_code'] === 'SPS-000872', "Security: Permanent Member ID remained unchanged (tamper-proof)");
assertCondition($refreshed['status'] === 'Active', "Security: Member status remained Active (tamper-proof)");

// Test HTTP POST /bn/membership/profile/update
Session::set('current_member_code', $memberCode);
Session::forget('member_logged_out');

$postReq = new Request('POST', '/bn/membership/profile/update', [], [
    'member_code' => $memberCode,
    'name_bn' => 'অমিত সেন',
    'name_en' => 'Amit Sen',
    'phone' => '+8801711000872',
    'email' => 'amit.sen@example.com',
    'district' => 'ঢাকা',
    'blood_group' => 'B+',
    'institution' => 'ঢাকা বিশ্ববিদ্যালয়',
    'designation' => 'গবেষক',
    'bio' => 'বেদান্ত দর্শন ও সনাতন সংস্কৃতির একনিষ্ঠ সাধক।',
]);

$postRes = $router->dispatch($postReq);
assertCondition($postRes->getStatusCode() === 302, "HTTP POST /bn/membership/profile/update returns 302 Redirect");
assertCondition(Session::hasFlash('success'), "Member profile update sets success flash notification");

$restored = MembershipService::getMemberById($memberCode);
assertCondition($restored['name_bn'] === 'অমিত সেন', "Name restored via HTTP POST");
assertCondition($restored['blood_group'] === 'B+', "Blood group set to B+ via HTTP POST");

// ==========================================
// 2. Admin Profile Update Service & HTTP
// ==========================================
echo "\n2. Testing Admin Profile Update Service & Views:\n";

AuthService::switchUser('usr_anik'); // Super Admin
$currentAdmin = AuthService::getCurrentUser();
assertCondition($currentAdmin['id'] === 'usr_anik', "Super Admin logged in as usr_anik");

// Test GET /bn/admin/profile view
$profileGetReq = new Request('GET', '/bn/admin/profile');
$profileGetRes = $router->dispatch($profileGetReq);
assertCondition($profileGetRes->getStatusCode() === 200, "GET /bn/admin/profile returns HTTP 200");
$profileBody = $profileGetRes->getContent();
assertCondition(str_contains($profileBody, 'অ্যাডমিন প্রোফাইল ও নিরাপত্তা ব্যবস্থাপনা'), "Profile page contains Bengali title");
assertCondition(str_contains($profileBody, 'usr_anik'), "Profile page displays admin user ID");
assertCondition(str_contains($profileBody, 'সুপার অ্যাডমিনিস্ট্রেটর'), "Profile page displays role badge");

// Test Service Update
$adminUpdateRes = RbacService::updateUserProfile('usr_anik', [
    'name_bn' => 'অনিক মুখার্জী (সুপার অ্যাডমিন)',
    'phone' => '+8801711112233',
    'bio' => 'এসপিএস প্ল্যাটফর্মের সার্বিক নিরাপত্তা ও পরিকাঠামো প্রধান।',
    'new_password' => 'sps@super2026',
    // Attempt role tampering (should be ignored)
    'role' => 'auditor',
    'id' => 'usr_hacked',
]);

assertCondition($adminUpdateRes['success'] === true, "Admin profile update succeeded via RbacService");
$anik = RbacService::getUser('usr_anik');
assertCondition($anik['name_bn'] === 'অনিক মুখার্জী (সুপার অ্যাডমিন)', "Admin name updated");
assertCondition($anik['phone'] === '+8801711112233', "Admin phone updated");
assertCondition($anik['role'] === 'super_admin', "Security: Admin role is tamper-proof (remained super_admin)");
assertCondition($anik['id'] === 'usr_anik', "Security: User ID is immutable");

// Test HTTP POST /bn/admin/profile
$adminPostReq = new Request('POST', '/bn/admin/profile', [], [
    'name_bn' => 'অনিক মুখার্জী',
    'name_en' => 'Anik Mukherjee',
    'email' => 'anik@sps.org',
    'phone' => '+8801711112233',
    'bio' => 'সিস্টেম আর্কিটেক্ট ও চিফ এডমিনিস্ট্রেটর।',
]);

$adminPostRes = $router->dispatch($adminPostReq);
assertCondition($adminPostRes->getStatusCode() === 302, "HTTP POST /bn/admin/profile returns 302 Redirect");
assertCondition(Session::hasFlash('success'), "Admin profile update sets success flash notification");

$anikRestored = RbacService::getUser('usr_anik');
assertCondition($anikRestored['name_bn'] === 'অনিক মুখার্জী', "Admin name restored via HTTP POST");

// Test Finance Officer (usr_joy) Profile Update
AuthService::switchUser('usr_joy');
$joyProfileRes = $router->dispatch(new Request('GET', '/bn/admin/profile'));
assertCondition($joyProfileRes->getStatusCode() === 200, "Finance Officer (usr_joy) can access /admin/profile");
assertCondition(str_contains($joyProfileRes->getContent(), 'ফাইন্যান্স অফিসার'), "Finance Officer profile displays role badge");

// Test Regular Admin 1 (usr_robin) Profile Update
AuthService::switchUser('usr_robin');
$robinProfileRes = $router->dispatch(new Request('GET', '/bn/admin/profile'));
assertCondition($robinProfileRes->getStatusCode() === 200, "Admin (usr_robin) can access /admin/profile");

echo "\n============================================\n";
echo "SUMMARY: ALL PROFILE UPDATE TESTS PASSED SUCCESSFULLY! ✓\n";
