<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\App;
use App\Core\Request;
use App\Core\Session;
use App\Controllers\AdminController;
use App\Controllers\MembershipController;
use App\Services\AuthService;
use App\Services\GoogleAuthService;
use App\Services\MembershipService;
use App\Services\RbacService;

$app = new App(dirname(__DIR__));
Session::start();
ob_start();

$totalTests = 0;
$passedTests = 0;

function assert_test(string $name, bool $condition, string $detail = '') {
    global $totalTests, $passedTests;
    $totalTests++;
    if ($condition) {
        $passedTests++;
        echo "[PASS] {$name}\n";
    } else {
        echo "[FAIL] {$name} - {$detail}\n";
    }
}

echo "=== Running SPS Google Identity & Security Proof Test Suite ===\n\n";

// 1. Google Configuration & Helper methods
$allowedAdmins = GoogleAuthService::getAllowedAdminEmails();
assert_test('Config: Allowed admin emails list is populated', in_array('badhanamitroy571@gmail.com', $allowedAdmins, true));

// 2. Reject empty or forged ID Token
$emptyTokenRes = GoogleAuthService::verifyIdToken('');
assert_test('Security Proof: Empty token strictly rejected', $emptyTokenRes === null);

$forgedTokenRes = GoogleAuthService::verifyIdToken('invalid.jwt.token.here');
assert_test('Security Proof: Forged / Fake JWT token strictly rejected by Google API', $forgedTokenRes === null);

// 3. Admin Authentication via Verified Profile
// Case A: User badhanamitroy571@gmail.com matched with usr_badhan (Publication Secretary)
$validAdminProfile = [
    'google_id' => 'google_sps_16_verified',
    'email' => 'badhanamitroy571@gmail.com',
    'email_verified' => true,
    'name' => 'Badhan Roy',
    'avatar' => 'https://lh3.googleusercontent.com/a/test-avatar'
];

$adminAuthRes = GoogleAuthService::authenticateAdmin($validAdminProfile);
assert_test('Admin Auth: Badhan Roy successfully authenticated via Google Email', $adminAuthRes['success'] === true && ($adminAuthRes['user']['id'] ?? '') === 'usr_badhan');
assert_test('Admin Auth: Active Admin Session established in AuthService', AuthService::check() === true && (AuthService::getCurrentUser()['id'] ?? '') === 'usr_badhan');

// Cleanup session
AuthService::logout();
assert_test('Admin Auth: Logout terminates active session', AuthService::check() === false);

// Case B: Unknown Google Email attempted on Admin Portal
$unauthorizedProfile = [
    'google_id' => 'google_random_hacker',
    'email' => 'random_attacker@gmail.com',
    'email_verified' => true,
    'name' => 'Unknown Person',
    'avatar' => ''
];
$unauthRes = GoogleAuthService::authenticateAdmin($unauthorizedProfile);
assert_test('Admin Security: Unauthorized Gmail address rejected from admin portal', $unauthRes['success'] === false && !empty($unauthRes['error']));

// 4. Member Authentication via Verified Google Profile
$memberProfile = [
    'google_id' => 'gid_test_verified_123',
    'email' => 'amit.sen@example.com',
    'email_verified' => true,
    'name' => 'Amit Sen',
    'avatar' => ''
];
$memberAuthRes = GoogleAuthService::authenticateMember($memberProfile);
assert_test('Member Auth: Existing member successfully linked & authenticated', $memberAuthRes['success'] === true && ($memberAuthRes['member']['member_code'] ?? '') === 'SPS-000872');
assert_test('Member Auth: Member Session active in Session store', Session::get('current_member_code') === 'SPS-000872');

// Case C: Auto-provisioning new member via Google
$randomGoogleId = 'gid_test_' . bin2hex(random_bytes(4));
$randomEmail = 'new.google.donor.' . bin2hex(random_bytes(4)) . '@gmail.com';
$newMemberProfile = [
    'google_id' => $randomGoogleId,
    'email' => $randomEmail,
    'email_verified' => true,
    'name' => 'New Google Supporter',
    'avatar' => ''
];
$newMemberAuthRes = GoogleAuthService::authenticateMember($newMemberProfile);
assert_test('Member Auth: New member automatically provisioned with Member Code', $newMemberAuthRes['success'] === true && $newMemberAuthRes['is_new'] === true && !empty($newMemberAuthRes['member']['member_code']));

// Remove the created test member afterwards for test isolation
if (!empty($newMemberAuthRes['member']['member_code'])) {
    MembershipService::deleteMember($newMemberAuthRes['member']['member_code']);
}

// 5. Controller Endpoints Validation
$adminController = new AdminController();
$membershipController = new MembershipController();

$reqEmpty = new Request();
$adminResponseEmpty = $adminController->googleVerify($reqEmpty, 'bn');
assert_test('AdminController: Missing credential returns 400 Bad Request', $adminResponseEmpty->getStatusCode() === 400);

$memberResponseEmpty = $membershipController->googleVerify($reqEmpty, 'bn');
assert_test('MembershipController: Missing credential returns 400 Bad Request', $memberResponseEmpty->getStatusCode() === 400);

echo "\nTest Results: {$passedTests} / {$totalTests} passed.\n";
if ($passedTests === $totalTests) {
    echo "ALL GOOGLE IDENTITY TESTS PASSED PERFECTLY! ✓\n";
    exit(0);
} else {
    echo "SOME TESTS FAILED!\n";
    exit(1);
}
