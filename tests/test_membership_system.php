<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Services\MembershipService;
use App\Services\AuthService;

echo "=== SPS COMPLETE MEMBERSHIP SYSTEM TEST SUITE ===\n\n";

$testsPassed = 0;
$totalTests = 0;

function assertCondition(bool $cond, string $message): void {
    global $testsPassed, $totalTests;
    $totalTests++;
    if ($cond) {
        $testsPassed++;
        echo "  [PASS] {$message}\n";
    } else {
        echo "  [FAIL] {$message}\n";
    }
}

// 1. Categories & Plans Inspection
echo "1. Testing Membership Categories & Plans:\n";
$categories = MembershipService::getCategories();
assertCondition(count($categories) >= 2, "At least 2 primary categories exist");
$studentCat = MembershipService::getCategory('STUDENT');
$earningCat = MembershipService::getCategory('EARNING');
assertCondition($studentCat !== null && $studentCat['code'] === 'STUDENT', "Student Member category correctly retrieved");
assertCondition($earningCat !== null && $earningCat['code'] === 'EARNING', "Earning Member category correctly retrieved");

$plans = MembershipService::getPlans();
assertCondition(count($plans) >= 4, "Plans exist: Student Monthly, Earning Monthly, Yearly, Lifetime");

$yearlyPlan = MembershipService::getPlan('YEARLY');
assertCondition($yearlyPlan !== null && (float)$yearlyPlan['fee'] === 1000.0 && (float)$yearlyPlan['entry_fee'] === 0.0, "Yearly Plan is ৳1,000 with 0 extra entry fee");

$lifetimePlan = MembershipService::getPlan('LIFETIME');
assertCondition($lifetimePlan !== null && (float)$lifetimePlan['fee'] === 10000.0 && (float)$lifetimePlan['entry_fee'] === 0.0, "Lifetime Plan is ৳10,000 with 0 extra entry fee");

// 2. Member Application Creation
echo "\n2. Testing New Member Application:\n";
$testEmail = 'test_student_' . time() . '@example.com';
$appData = [
    'name_bn' => 'সৌমেন চক্রবর্তী',
    'name_en' => 'Soumen Chakraborty',
    'email' => $testEmail,
    'phone' => '01711998877',
    'category_id' => 'STUDENT',
    'plan_id' => 'STUDENT_MONTHLY',
    'district' => 'Sylhet',
    'upazila' => 'Kotwali',
    'payment_method' => 'bKash',
    'sender_number' => '01711998877',
    'education' => [
        'institution' => 'Shahjalal University of Science and Technology',
        'department' => 'Physics',
        'class_year' => '4th Year',
    ],
    'apply_volunteer' => true,
    'volunteer_interests' => ['Education', 'IT & Web'],
];

$result = MembershipService::createApplication($appData);
$newMember = $result['member'];
$newPayment = $result['payment'];

assertCondition($newMember !== null, "Member application created");
assertCondition(str_starts_with($newMember['member_code'], 'SPS-'), "Member ID generated with SPS- prefix ({$newMember['member_code']})");
assertCondition($newMember['status'] === 'Pending', "Member initial status is Pending");
assertCondition(str_starts_with($newPayment['transaction_id'], 'SPS-MEM-'), "Transaction ID generated ({$newPayment['transaction_id']})");
assertCondition((float)$newPayment['amount'] === 100.0, "Student monthly initial amount is ৳100 (50 entry + 50 monthly)");

// 3. Member Approval Workflow
echo "\n3. Testing Member Approval:\n";
$adminUser = ['name_bn' => 'প্রশাসক সুধীর', 'email' => 'admin@sps.org', 'role' => 'admin'];
$approved = MembershipService::approveMember($newMember['id'], $adminUser);
assertCondition($approved, "Admin approved member application");

$refreshedMember = MembershipService::getMemberById($newMember['id']);
assertCondition($refreshedMember['status'] === 'Active', "Member status successfully set to Active");
assertCondition(!empty($refreshedMember['expiry_date']), "Member has valid expiry date ({$refreshedMember['expiry_date']})");

// 4. Student -> Earning Category Transition
echo "\n4. Testing Student -> Earning Member Transition (History Retention):\n";
$originalCode = $refreshedMember['member_code'];
$transitionSuccess = MembershipService::transitionCategory(
    $refreshedMember['id'],
    'EARNING',
    'EARNING_MONTHLY',
    'Graduated SUST and joined IT firm as Software Engineer',
    $adminUser
);
assertCondition($transitionSuccess, "Transition executed successfully");

$transitionedMember = MembershipService::getMemberById($refreshedMember['id']);
assertCondition($transitionedMember['category_id'] === 'EARNING', "Category updated to EARNING");
assertCondition($transitionedMember['member_code'] === $originalCode, "Permanent Member ID remains unchanged ({$originalCode})");

$history = MembershipService::getMemberHistory($refreshedMember['id']);
assertCondition(count($history) >= 1, "Historical audit log created");
$latestHistory = $history[0];
assertCondition($latestHistory['old_category'] === 'STUDENT' && $latestHistory['new_category'] === 'EARNING', "Historical log records STUDENT -> EARNING transition");

// 5. Member Suspension & Reactivation
echo "\n5. Testing Member Suspension & Reactivation:\n";
$suspended = MembershipService::suspendMember($refreshedMember['id'], 'Payment verification discrepancy', $adminUser);
assertCondition($suspended, "Admin suspended member");

$suspendedMember = MembershipService::getMemberById($refreshedMember['id']);
assertCondition($suspendedMember['status'] === 'Suspended', "Status is now Suspended");

$activated = MembershipService::activateMember($refreshedMember['id'], $adminUser);
assertCondition($activated, "Admin reactivated member");

$activeAgainMember = MembershipService::getMemberById($refreshedMember['id']);
assertCondition($activeAgainMember['status'] === 'Active', "Status is back to Active");

// 6. Security Distinction: Member vs Admin Roles
echo "\n6. Testing Security Boundary (Member != Admin):\n";
// An approved member record does NOT exist in Admin RbacService users unless explicitly granted admin role
$adminUsers = \App\Services\RbacService::getUsers();
$memberInAdmin = false;
foreach ($adminUsers as $u) {
    if ($u['email'] === $testEmail) {
        $memberInAdmin = true;
        break;
    }
}
assertCondition(!$memberInAdmin, "Regular member is strictly quarantined from administrative RBAC roles");

// 7. Testing HTTP Router & View Rendering
echo "\n7. Testing HTTP Router & Views:\n";
$app = new \App\Core\App(dirname(__DIR__));
$langConfig = require dirname(__DIR__) . '/config/languages.php';
\App\Core\I18n::init($langConfig, 'bn', '/bn');
\App\Core\View::init(dirname(__DIR__) . '/app/Views');
$router = new \App\Core\Router();
require dirname(__DIR__) . '/routes/web.php';

// Public Landing
$resHub = $router->dispatch(new \App\Core\Request('GET', '/bn/membership'));
assertCondition($resHub->getStatusCode() === 200, "GET /bn/membership returns HTTP 200");
$contentHub = $resHub->getContent();
assertCondition(str_contains($contentHub, 'এসপিএস প্রাতিষ্ঠানিক সদস্যপদ ব্যবস্থা'), "Hub contains SPS Membership headline");
assertCondition(str_contains($contentHub, 'STUDENT') && str_contains($contentHub, 'EARNING'), "Hub contains STUDENT and EARNING categories");
assertCondition(str_contains($contentHub, '৳১,০০০') && str_contains($contentHub, '৳১০,০০০'), "Hub contains Yearly (৳1,000) & Lifetime (৳10,000) plans");

// Public Apply Form
$resApply = $router->dispatch(new \App\Core\Request('GET', '/bn/membership/apply'));
assertCondition($resApply->getStatusCode() === 200, "GET /bn/membership/apply returns HTTP 200");
$contentApply = $resApply->getContent();
assertCondition(str_contains($contentApply, 'এসপিএস সদস্যপদ আবেদন ফরম'), "Apply form contains title");
assertCondition(str_contains($contentApply, 'category_id') && str_contains($contentApply, 'plan_id'), "Apply form contains category and plan inputs");
assertCondition(str_contains($contentApply, 'student-fields-sec') && str_contains($contentApply, 'earning-fields-sec'), "Apply form contains category-specific sections");

// Member Dashboard
$resDash = $router->dispatch(new \App\Core\Request('GET', '/bn/membership/dashboard'));
assertCondition($resDash->getStatusCode() === 200, "GET /bn/membership/dashboard returns HTTP 200");
$contentDash = $resDash->getContent();
assertCondition(str_contains($contentDash, 'SPS-000872'), "Dashboard contains member ID (SPS-000872)");
assertCondition(str_contains($contentDash, 'printableCard'), "Dashboard contains Digital Membership Card");

// Public QR Card Verify
$resVerify = $router->dispatch(new \App\Core\Request('GET', '/bn/membership/verify', ['code' => 'SPS-000872']));
assertCondition($resVerify->getStatusCode() === 200, "GET /bn/membership/verify?code=SPS-000872 returns HTTP 200");
$contentVerify = $resVerify->getContent();
assertCondition(str_contains($contentVerify, 'যাচাইকৃত অফিসিয়াল ডিজিটাল কার্ড'), "Verify page displays verified authentic status");
assertCondition(str_contains($contentVerify, 'SPS-000872'), "Verify page displays Member ID");
// Ensure privacy: no phone or email leaked on public verification page
assertCondition(!str_contains($contentVerify, '01711-872001') && !str_contains($contentVerify, 'amit.sen@sps.org'), "Verify page preserves privacy (no phone/email exposed)");

// English Version
\App\Core\I18n::init($langConfig, 'en', '/en');
$resEn = $router->dispatch(new \App\Core\Request('GET', '/en/membership'));
assertCondition($resEn->getStatusCode() === 200, "GET /en/membership returns HTTP 200");
assertCondition(str_contains($resEn->getContent(), 'SPS Complete Membership System'), "English page renders correctly");
\App\Core\I18n::init($langConfig, 'bn', '/bn');

// Admin Membership Portal (Authenticated as Super Admin)
\App\Services\AuthService::switchUser('usr_anik');
$resAdmin = $router->dispatch(new \App\Core\Request('GET', '/bn/admin/members'));
assertCondition($resAdmin->getStatusCode() === 200, "GET /bn/admin/members returns HTTP 200 for authenticated admin");
$contentAdmin = $resAdmin->getContent();
assertCondition(str_contains($contentAdmin, 'সদস্য প্রশাসন ও মেম্বারশিপ পোর্টাল'), "Admin members page renders with header");
assertCondition(str_contains($contentAdmin, 'transitionModal'), "Admin page contains Student -> Earning transition modal");

// 8. Testing Finance Officer Payment Verification & Supervisory Monitoring
echo "\n8. Testing Finance Officer Verification & Supervisory Monitoring:\n";

// A. Create an applicant with custom TrxID and bKash Sent Money screenshot
$appWithTrx = [
    'name_bn' => 'অনিন্দ্য রায়',
    'name_en' => 'Anindya Roy',
    'email' => 'anindya_' . time() . '@example.com',
    'phone' => '01811223344',
    'category_id' => 'EARNING',
    'plan_id' => 'EARNING_MONTHLY',
    'payment_method' => 'bKash',
    'sender_number' => '01811223344',
    'trx_id' => 'BKA99X77QW',
    'payment_screenshot' => 'assets/images/payments/bkash-success-sample.svg',
];
$resTrx = MembershipService::createApplication($appWithTrx);
$trxMember = $resTrx['member'];
$trxPayment = $resTrx['payment'];

assertCondition($trxPayment['trx_id'] === 'BKA99X77QW', "Applicant TrxID properly saved as BKA99X77QW");
assertCondition($trxPayment['payment_screenshot'] === 'assets/images/payments/bkash-success-sample.svg', "bKash confirmation screenshot attached");
assertCondition($trxPayment['status'] === 'Pending', "Payment status starts as Pending awaiting Finance Officer");

// B. Verify payment using Finance Officer account (usr_joy - Joy Chakraborty)
$financeOfficerUser = \App\Services\RbacService::getUser('usr_joy');
assertCondition($financeOfficerUser !== null && $financeOfficerUser['role'] === 'finance_officer', "Finance Officer user (usr_joy) found with role 'finance_officer'");

$verified = MembershipService::verifyPayment($trxPayment['id'], $financeOfficerUser);
assertCondition($verified, "Finance Officer successfully verified payment");

$verifiedMember = MembershipService::getMemberById($trxMember['id']);
assertCondition($verifiedMember['status'] === 'Active', "Member activated upon Finance Officer verification");

$memberPayments = MembershipService::getMemberPayments($trxMember['id']);
assertCondition(!empty($memberPayments) && $memberPayments[0]['status'] === 'Verified', "Payment ledger status updated to Verified");
assertCondition(str_contains($memberPayments[0]['verified_by'] ?? '', 'জয় চক্রবর্তী'), "Verifier recorded as Joy Chakraborty (Finance Officer)");

// C. Test Rejection Flow with audit notes
$rejPayment = MembershipService::recordPayment([
    'member_id' => $trxMember['id'],
    'payment_type' => 'monthly',
    'amount' => 100,
    'payment_method' => 'bKash',
    'sender_number' => '01999999999',
    'trx_id' => 'FAKE123456',
    'payment_screenshot' => 'assets/images/payments/bkash-success-sample.svg',
    'auto_verify' => false,
]);
assertCondition($rejPayment['status'] === 'Pending', "Second payment recorded as Pending");

$rejectSuccess = MembershipService::rejectPayment($rejPayment['id'], 'প্রদত্ত TrxID বিকাশ স্টেটমেন্টের সাথে মেলেনি', $financeOfficerUser);
assertCondition($rejectSuccess, "Payment successfully rejected with reason");

$allMemberPayments = MembershipService::getMemberPayments($trxMember['id']);
$foundRej = null;
foreach ($allMemberPayments as $p) {
    if ($p['id'] === $rejPayment['id']) { $foundRej = $p; break; }
}
assertCondition($foundRej !== null && $foundRej['status'] === 'Rejected', "Payment ledger reflects Rejected status");
assertCondition(str_contains($foundRej['notes'] ?? '', 'প্রদত্ত TrxID'), "Rejection note properly preserved in immutable ledger");

// D. Test Finance Officer view rendered via Router
\App\Services\AuthService::switchUser('usr_joy');
$resFinanceDesk = $router->dispatch(new \App\Core\Request('GET', '/bn/admin/members', ['tab' => 'payments']));
assertCondition($resFinanceDesk->getStatusCode() === 200, "Finance Officer can access /bn/admin/members?tab=payments");
$contentFinanceDesk = $resFinanceDesk->getContent();
assertCondition(str_contains($contentFinanceDesk, 'কোষাধ্যক্ষ / ফাইন্যান্স অফিসার নিয়ন্ত্রণ ডেস্ক'), "Finance Officer sees dedicated Finance Officer Exclusive Desk banner");
assertCondition(str_contains($contentFinanceDesk, 'adminScreenshotModal'), "Finance Officer desk includes payment screenshot inspection modal");

// E. Test Super Admin Supervisory Monitoring View
\App\Services\AuthService::switchUser('usr_anik');
$resSuperMonitor = $router->dispatch(new \App\Core\Request('GET', '/bn/admin/members', ['tab' => 'payments']));
assertCondition($resSuperMonitor->getStatusCode() === 200, "Super Admin can monitor payments desk");
$contentSuperMonitor = $resSuperMonitor->getContent();
assertCondition(str_contains($contentSuperMonitor, 'সুপার-অ্যাডমিন ও অ্যাডমিন তত্ত্বাবধান ডেস্ক'), "Super Admin sees Supervisory Monitoring Mode banner");

// F. Test Regular Admin 1 (usr_robin) & Admin 2 (usr_likhon) Supervisory Monitoring View
\App\Services\AuthService::switchUser('usr_robin');
$resRobinMonitor = $router->dispatch(new \App\Core\Request('GET', '/bn/admin/members', ['tab' => 'payments']));
assertCondition($resRobinMonitor->getStatusCode() === 200, "Admin (usr_robin) can monitor payments desk");
assertCondition(str_contains($resRobinMonitor->getContent(), 'সুপার-অ্যাডমিন ও অ্যাডমিন তত্ত্বাবধান ডেস্ক'), "Admin 1 sees Supervisory Monitoring Mode banner");

\App\Services\AuthService::switchUser('usr_likhon');
$resLikhonMonitor = $router->dispatch(new \App\Core\Request('GET', '/bn/admin/members', ['tab' => 'payments']));
assertCondition($resLikhonMonitor->getStatusCode() === 200, "Admin (usr_likhon) can monitor payments desk");
assertCondition(str_contains($resLikhonMonitor->getContent(), 'সুপার-অ্যাডমিন ও অ্যাডমিন তত্ত্বাবধান ডেস্ক'), "Admin 2 sees Supervisory Monitoring Mode banner");

// G. Test Enhanced Payment Info Persistence (sender_name, payment_time, payment_reference)
echo "\n9. Testing Enhanced Payment Info (sender_name, payment_time, payment_reference):\n";
$richAppData = [
    'name_bn' => 'প্রীতম সাহা',
    'name_en' => 'Pritam Saha',
    'email' => 'pritam_' . time() . '@example.com',
    'phone' => '01712345678',
    'category_id' => 'STUDENT',
    'plan_id' => 'STUDENT_MONTHLY',
    'payment_method' => 'bKash',
    'sender_number' => '01712345678',
    'sender_name' => 'Pritam Kumar Saha',
    'payment_time' => '2026-09-28 10:15 AM',
    'payment_reference' => 'SPS-PRITAM-SEP',
    'trx_id' => 'BK789456XYZ',
    'payment_screenshot' => 'assets/images/payments/bkash-success-sample.svg',
];
$richAppRes = MembershipService::createApplication($richAppData);
$richMem = $richAppRes['member'];
$richPay = $richAppRes['payment'];

assertCondition($richPay['sender_name'] === 'Pritam Kumar Saha', "Applicant sender_name recorded correctly");
assertCondition($richPay['payment_time'] === '2026-09-28 10:15 AM', "Applicant payment_time recorded correctly");
assertCondition($richPay['payment_reference'] === 'SPS-PRITAM-SEP', "Applicant payment_reference recorded correctly");

// Renewal payment with enhanced fields
$renewalPay = MembershipService::recordPayment([
    'member_id' => $richMem['id'],
    'payment_type' => 'monthly',
    'amount' => 50,
    'payment_method' => 'bKash',
    'sender_number' => '01712345678',
    'sender_name' => 'Pritam Kumar Saha',
    'payment_time' => '2026-10-28 11:30 AM',
    'payment_reference' => 'SPS-RENEW-OCT',
    'trx_id' => 'BK998877AAB',
    'payment_screenshot' => 'assets/images/payments/bkash-success-sample.svg',
    'auto_verify' => false,
]);
assertCondition($renewalPay['sender_name'] === 'Pritam Kumar Saha', "Renewal payment sender_name recorded");
assertCondition($renewalPay['payment_time'] === '2026-10-28 11:30 AM', "Renewal payment payment_time recorded");
assertCondition($renewalPay['payment_reference'] === 'SPS-RENEW-OCT', "Renewal payment payment_reference recorded");

// H. Test Strict Controller HTTP Access Controls: Finance Officer Exclusive vs Super Admin & 2 Admins Monitoring
echo "\n10. Testing HTTP POST Authority Enforcement (Finance Officer vs Supervisory Admins):\n";

// 1. Super Admin (usr_anik) attempts to POST verify payment -> must be rejected with monitoring notice
\App\Services\AuthService::switchUser('usr_anik');
\App\Core\Session::start();
$postSuperVerify = $router->dispatch(new \App\Core\Request('POST', '/bn/admin/members/payment/verify/' . $richPay['id']));
assertCondition($postSuperVerify->getStatusCode() === 302, "Super Admin POST payment verify returns redirect (forbidden to execute)");
assertCondition(str_contains(\App\Core\Session::getFlash('error') ?? '', 'ফাইন্যান্স অফিসার'), "Super Admin blocked with message that verification is exclusively in Finance Officer's hands");

// 2. Admin 1 (usr_robin) attempts to POST verify payment -> must be rejected
\App\Services\AuthService::switchUser('usr_robin');
$postRobinVerify = $router->dispatch(new \App\Core\Request('POST', '/bn/admin/members/payment/verify/' . $richPay['id']));
assertCondition($postRobinVerify->getStatusCode() === 302, "Admin 1 (usr_robin) POST payment verify returns redirect");
assertCondition(str_contains(\App\Core\Session::getFlash('error') ?? '', 'ফাইন্যান্স অফিসার'), "Admin 1 blocked with monitoring role explanation");

// 3. Admin 2 (usr_likhon) attempts to POST verify payment -> must be rejected
\App\Services\AuthService::switchUser('usr_likhon');
$postLikhonVerify = $router->dispatch(new \App\Core\Request('POST', '/bn/admin/members/payment/verify/' . $richPay['id']));
assertCondition($postLikhonVerify->getStatusCode() === 302, "Admin 2 (usr_likhon) POST payment verify returns redirect");
assertCondition(str_contains(\App\Core\Session::getFlash('error') ?? '', 'ফাইন্যান্স অফিসার'), "Admin 2 blocked with monitoring role explanation");

// 4. Super Admin attempts to POST approve pending member -> must be rejected
\App\Services\AuthService::switchUser('usr_anik');
$postSuperApprove = $router->dispatch(new \App\Core\Request('POST', '/bn/admin/members/approve/' . $richMem['id']));
assertCondition($postSuperApprove->getStatusCode() === 302, "Super Admin POST member approve returns redirect");
assertCondition(str_contains(\App\Core\Session::getFlash('error') ?? '', 'ফাইন্যান্স অফিসার'), "Super Admin cannot directly approve member; restricted to Finance Officer");

// 5. Finance Officer (usr_joy) executes POST verify payment -> SUCCESS!
\App\Services\AuthService::switchUser('usr_joy');
$postJoyVerify = $router->dispatch(new \App\Core\Request('POST', '/bn/admin/members/payment/verify/' . $richPay['id']));
assertCondition($postJoyVerify->getStatusCode() === 302, "Finance Officer POST verify completes with redirect");
assertCondition(str_contains(\App\Core\Session::getFlash('success') ?? '', 'সফলভাবে ভেরিফাই'), "Finance Officer receives success confirmation for payment verification");

$verifiedRichMem = MembershipService::getMemberById($richMem['id']);
assertCondition($verifiedRichMem['status'] === 'Active', "Member status transitioned to Active via Finance Officer POST verify");

// 6. Test Finance Officer POST approve for a pending member
$anotherApp = MembershipService::createApplication([
    'name_bn' => 'দীপ্ত বণিক',
    'name_en' => 'Dipto Banik',
    'email' => 'dipto_' . time() . '@example.com',
    'phone' => '01511223344',
    'category_id' => 'STUDENT',
    'plan_id' => 'STUDENT_MONTHLY',
    'payment_method' => 'bKash',
    'sender_number' => '01511223344',
    'trx_id' => 'BKDIPTO123',
    'payment_screenshot' => 'assets/images/payments/bkash-success-sample.svg',
]);
$diptoMem = $anotherApp['member'];
assertCondition($diptoMem['status'] === 'Pending', "Dipto starts in Pending status");

\App\Services\AuthService::switchUser('usr_joy');
$postJoyApprove = $router->dispatch(new \App\Core\Request('POST', '/bn/admin/members/approve/' . $diptoMem['id']));
assertCondition($postJoyApprove->getStatusCode() === 302, "Finance Officer POST approve completes with redirect");
assertCondition(str_contains(\App\Core\Session::getFlash('success') ?? '', 'অনুমোদিত হয়েছে'), "Finance Officer receives success message for application approval");

$approvedDipto = MembershipService::getMemberById($diptoMem['id']);
assertCondition($approvedDipto['status'] === 'Active', "Member activated via Finance Officer POST approve");

echo "\n11. Testing Member Self-Service Login & Logout Portal:\n";
// Clear any test session state
\App\Core\Session::forget('current_member_code');
\App\Core\Session::forget('member_logged_out');

// 11.1 GET /bn/membership/login
$resMemLogin = $router->dispatch(new \App\Core\Request('GET', '/bn/membership/login'));
assertCondition($resMemLogin->getStatusCode() === 200, "GET /bn/membership/login returns HTTP 200");
$contentMemLogin = $resMemLogin->getContent();
assertCondition(str_contains($contentMemLogin, 'সদস্য লগইন পোর্টাল'), "Login page contains Member Login Portal title");
assertCondition(str_contains($contentMemLogin, 'আপনি কি এখনো সদস্য নন?'), "Login page contains non-member prompt");
assertCondition(str_contains($contentMemLogin, '/membership/apply'), "Login page provides direct link to apply form");

// 11.2 POST /bn/membership/login with invalid input
$resBadLogin = $router->dispatch(new \App\Core\Request('POST', '/bn/membership/login', [], ['identifier' => 'INVALID-9999']));
assertCondition($resBadLogin->getStatusCode() === 302, "Invalid member login returns redirect");
assertCondition(str_contains(\App\Core\Session::getFlash('error') ?? '', 'কোনো সদস্য রেকর্ড পাওয়া যায়নি'), "Invalid member login returns descriptive error flash");

// 11.3 POST /bn/membership/login with valid Member Code
$resGoodCodeLogin = $router->dispatch(new \App\Core\Request('POST', '/bn/membership/login', [], ['identifier' => 'SPS-000872']));
assertCondition($resGoodCodeLogin->getStatusCode() === 302, "Valid Member Code login redirects to dashboard");
assertCondition(\App\Core\Session::get('current_member_code') === 'SPS-000872', "Session current_member_code is set to SPS-000872");
assertCondition(str_contains(\App\Core\Session::getFlash('success') ?? '', 'অমিত সেন'), "Login flash acknowledges member by name");

// 11.4 POST /bn/membership/login with valid Email
$resGoodEmailLogin = $router->dispatch(new \App\Core\Request('POST', '/bn/membership/login', [], ['identifier' => 'amit.sen@example.com']));
assertCondition($resGoodEmailLogin->getStatusCode() === 302, "Valid Email login redirects to dashboard");
assertCondition(\App\Core\Session::get('current_member_code') === 'SPS-000872', "Email login sets correct member session");

// 11.5 Member Logout
$resLogout = $router->dispatch(new \App\Core\Request('GET', '/bn/membership/logout'));
assertCondition($resLogout->getStatusCode() === 302, "Member logout returns redirect");
assertCondition(\App\Core\Session::get('current_member_code') === null, "Logout clears current_member_code from session");
assertCondition(\App\Core\Session::get('member_logged_out') === true, "Logout marks member_logged_out flag");

// 11.6 Accessing dashboard after logout redirects to login
$resDashAfterLogout = $router->dispatch(new \App\Core\Request('GET', '/bn/membership/dashboard'));
assertCondition($resDashAfterLogout->getStatusCode() === 302, "Accessing dashboard after logout redirects");
assertCondition(str_contains($resDashAfterLogout->getHeader('Location') ?? '', '/membership/login'), "Redirect targets /membership/login");

// Reset session cleanly for future requests
\App\Core\Session::forget('member_logged_out');
\App\Core\Session::set('current_member_code', 'SPS-000872');

// Summary
echo "\n============================================\n";
echo "SUMMARY: {$testsPassed} / {$totalTests} tests passed.\n";
if ($testsPassed === $totalTests) {
    echo "ALL TESTS PASSED SUCCESSFULLY! ✓\n";
    exit(0);
} else {
    echo "SOME TESTS FAILED! ✕\n";
    exit(1);
}

