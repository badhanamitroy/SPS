<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\App;
use App\Core\Request;
use App\Core\Router;
use App\Core\Session;
use App\Services\MembershipService;

$baseDir = dirname(__DIR__);
$app = new App($baseDir);

$langConfig = require $baseDir . '/config/languages.php';
\App\Core\I18n::init($langConfig, 'bn', '/bn');
\App\Core\View::init($baseDir . '/app/Views');

$router = new Router();
require $baseDir . '/routes/web.php';

$totalTests = 0;
$passedTests = 0;

function assertCondition(bool $condition, string $message): void
{
    global $totalTests, $passedTests;
    $totalTests++;
    if ($condition) {
        $passedTests++;
        echo "  [PASS] {$message}\n";
    } else {
        echo "  [FAIL] {$message}\n";
    }
}

echo "=== SPS T8: MEMBER DASHBOARD IDOR PREVENTION TEST SUITE ===\n\n";

// ---------------------------------------------------------
// 1. Unauthenticated IDOR Attempt via ?as={active_member}
// ---------------------------------------------------------
echo "1. Testing Unauthenticated IDOR Prevention (?as=ActiveMember):\n";

Session::forget('current_member_code');
Session::forget('member_logged_out');

// Attempt to view Active Member (SPS-000872) dashboard via ?as=
$reqIdor = new Request('GET', '/bn/membership/dashboard', ['as' => 'SPS-000872']);
$resIdor = $router->dispatch($reqIdor);

assertCondition($resIdor->getStatusCode() === 302, "Unauthenticated access with ?as=ActiveMember redirects (HTTP 302)");
assertCondition(
    str_contains($resIdor->getHeader('Location') ?? '', '/membership/login'),
    "Redirect targets /membership/login instead of exposing dashboard"
);
assertCondition(
    Session::get('current_member_code') !== 'SPS-000872',
    "Session current_member_code was NOT hijacked by ?as="
);

// ---------------------------------------------------------
// 2. Cross-Account IDOR Attempt when Logged In as Another Member
// ---------------------------------------------------------
echo "\n2. Testing Cross-Account IDOR Prevention for Logged-in Members:\n";

// Log in as Member 124
$loggedMemberCode = 'SPS-000124';
Session::set('current_member_code', $loggedMemberCode);
Session::forget('member_logged_out');

// Attempt to view Member 872's dashboard while logged in as Member 124
$reqCross = new Request('GET', '/bn/membership/dashboard', ['as' => 'SPS-000872']);
$resCross = $router->dispatch($reqCross);

assertCondition($resCross->getStatusCode() === 200, "Authenticated dashboard request returns HTTP 200");
$contentCross = $resCross->getContent();

assertCondition(
    str_contains($contentCross, $loggedMemberCode),
    "Dashboard displays logged-in member's ID ({$loggedMemberCode})"
);
assertCondition(
    !str_contains($contentCross, 'SPS-000872'),
    "Dashboard strictly ignores ?as= and does NOT show victim member (SPS-000872)"
);
assertCondition(
    Session::get('current_member_code') === $loggedMemberCode,
    "Session remained strictly bound to logged-in member"
);

// ---------------------------------------------------------
// 3. Allowed Pending Applicant View (?as=PendingMember)
// ---------------------------------------------------------
echo "\n3. Testing Allowed Pending Flow (?as=PendingMember):\n";

// Clear session
Session::forget('current_member_code');
Session::forget('member_logged_out');

// Create a pending applicant to test
$testApp = [
    'name_bn' => 'পরীক্ষার্থী সদস্য',
    'name_en' => 'Applicant Test Member',
    'email' => 'applicant_idor_test_' . time() . '@example.com',
    'phone' => '01799887766',
    'category_id' => 'STUDENT',
    'plan_id' => 'STUDENT_MONTHLY',
    'payment_method' => 'bKash',
    'sender_number' => '01799887766',
    'trx_id' => 'TRX_PENDING_TEST',
    'address' => 'গোপন ঠিকানা ১২৩/এ',
    'district' => 'ঢাকা',
];
$created = MembershipService::createApplication($testApp);
$pendingMember = $created['member'];
$pendingCode = $pendingMember['member_code'];

$reqPending = new Request('GET', '/bn/membership/dashboard', ['as' => $pendingCode]);
$resPending = $router->dispatch($reqPending);

assertCondition($resPending->getStatusCode() === 200, "Pending view via ?as= returns HTTP 200");
$contentPending = $resPending->getContent();

assertCondition(
    str_contains($contentPending, 'আবেদন ও পেমেন্ট যাচাই প্রক্রিয়াধীন') || str_contains($contentPending, 'Under Verification'),
    "Pending view displays verification status indicator"
);
assertCondition(
    str_contains($contentPending, $pendingMember['name_bn']),
    "Pending view displays applicant name"
);
assertCondition(
    !str_contains($contentPending, 'id="printableCard"'),
    "Pending view contains NO printable digital membership card"
);
assertCondition(
    !str_contains($contentPending, '01799887766'),
    "Pending view strictly does NOT expose applicant phone number"
);
assertCondition(
    !str_contains($contentPending, $testApp['email']),
    "Pending view strictly does NOT expose applicant email"
);
assertCondition(
    !str_contains($contentPending, 'গোপন ঠিকানা ১২৩/এ'),
    "Pending view strictly does NOT expose applicant physical address"
);
assertCondition(
    empty(Session::get('current_member_code')),
    "Pending view does NOT establish an active session"
);

// ---------------------------------------------------------
// 4. Member POST Actions Binding to Session (No POST IDOR)
// ---------------------------------------------------------
echo "\n4. Testing Member POST Actions Bound to Session:\n";

// 4a. Unauthenticated POST to updateProfile must be rejected
Session::forget('current_member_code');
Session::forget('member_logged_out');

$postUnauthProfile = new Request('POST', '/bn/membership/profile/update', [], [
    'member_code' => 'SPS-000872',
    'name_bn' => 'হ্যাকড নাম',
]);
$resUnauthProfile = $router->dispatch($postUnauthProfile);
assertCondition($resUnauthProfile->getStatusCode() === 302, "Unauthenticated POST /profile/update redirects to login");

// Verify SPS-000872 was untouched
$mem872 = MembershipService::getMemberById('SPS-000872');
assertCondition($mem872['name_bn'] !== 'হ্যাকড নাম', "Target member was not modified by unauthenticated POST");

// 4b. Authenticated as Member 124 attempting to update Member 872 via POST param
Session::set('current_member_code', 'SPS-000124');
$original872Name = $mem872['name_bn'];

$postTamperProfile = new Request('POST', '/bn/membership/profile/update', [], [
    'member_code' => 'SPS-000872', // Malicious attempt to target 872
    'name_bn' => 'প্রিয়াঙ্কা সরকার হালনাগাদ',
    'bio' => 'আপডেটেড বায়ো',
]);
$resTamperProfile = $router->dispatch($postTamperProfile);
assertCondition($resTamperProfile->getStatusCode() === 302, "Authenticated POST /profile/update returns 302");

// Verify 872 was untouched
$mem872After = MembershipService::getMemberById('SPS-000872');
assertCondition($mem872After['name_bn'] === $original872Name, "Member 872 was completely immune to POST member_code tampering");

// 4c. Unauthenticated POST makePayment must be rejected
Session::forget('current_member_code');
$postUnauthPay = new Request('POST', '/bn/membership/payment', [], [
    'member_id' => 'mem_001',
    'trx_id' => 'BK11223344',
]);
$resUnauthPay = $router->dispatch($postUnauthPay);
assertCondition($resUnauthPay->getStatusCode() === 302, "Unauthenticated POST /payment redirects to login");

// 4d. Unauthenticated POST transition must be rejected
$postUnauthTrans = new Request('POST', '/bn/membership/transition', [], [
    'member_id' => 'mem_001',
    'new_category' => 'EARNING',
]);
$resUnauthTrans = $router->dispatch($postUnauthTrans);
assertCondition($resUnauthTrans->getStatusCode() === 302, "Unauthenticated POST /transition redirects to login");

// Clean up
Session::forget('current_member_code');
Session::forget('member_logged_out');

echo "\n============================================\n";
echo "SUMMARY: {$passedTests} / {$totalTests} tests passed.\n";
if ($passedTests === $totalTests) {
    echo "ALL T8 DASHBOARD IDOR TESTS PASSED! ✓\n";
    exit(0);
} else {
    echo "SOME TESTS FAILED! ✕\n";
    exit(1);
}
