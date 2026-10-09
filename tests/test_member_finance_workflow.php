<?php
/**
 * Comprehensive Acceptance Test Suite for Member Registration & Finance Approval Workflow
 * Tests Scenarios A through G as specified in the prompt requirements.
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/../app/Core/helpers.php';

use App\Services\MembershipService;
use App\Services\EmailService;

echo "========================================================================\n";
echo "SPS ACCEPTANCE TEST SUITE: MEMBER REGISTRATION & FINANCE APPROVAL\n";
echo "========================================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest(bool $condition, string $testName, string $details = ''): void {
    global $passCount, $failCount;
    if ($condition) {
        $passCount++;
        echo "  [PASS] {$testName}" . ($details ? " ({$details})" : "") . "\n";
    } else {
        $failCount++;
        echo "  [FAIL] {$testName}" . ($details ? " -- REASON: {$details}" : "") . "\n";
    }
}

// -------------------------------------------------------------------------
// TEST A: New member without DP
// -------------------------------------------------------------------------
echo "--- Scenario A: New Member Without Uploaded DP ---\n";
$time = time();
$appA = [
    'name_bn' => "টেস্ট সদস্য এ {$time}",
    'name_en' => "Test Member A {$time}",
    'phone' => '01711' . rand(100000, 999999),
    'email' => "member_a_{$time}@example.com",
    'category_id' => 'STUDENT',
    'plan_id' => 'STUDENT_MONTHLY',
    'trx_id' => "TRX-A-{$time}",
    'payment_method' => 'bKash',
    'sender_number' => '01711111111',
    'district' => 'Dhaka',
    'avatar' => null // No DP uploaded
];

$resA = MembershipService::createApplication($appA);
assertTest(!empty($resA['member']['member_code']), 'Member A created successfully', "Code: " . ($resA['member']['member_code'] ?? ''));
$memA = MembershipService::getMemberById($resA['member']['member_code']);
$avatarA = MembershipService::getMemberAvatar($memA);

assertTest($memA !== null, 'Member A record exists in store');
assertTest($avatarA === MembershipService::DEFAULT_ORGANIZATION_DP, 'Member A receives default organization DP from media/dp/', "Got: {$avatarA}");
assertTest(!str_contains($avatarA, '000872'), 'Member A does NOT inherit Member 872 image');
assertTest(!str_contains($avatarA, 'dicebear'), 'Member A does NOT use external DiceBear URL');
assertTest(MembershipService::isPendingStatus($memA['status']), 'Member A status is Pending Finance Approval', "Status: {$memA['status']}");

// -------------------------------------------------------------------------
// TEST B: New member with DP
// -------------------------------------------------------------------------
echo "\n--- Scenario B: New Member With Uploaded DP ---\n";
// Create a temporary mock uploaded DP file
$mockImgPath = "assets/images/members/test_member_b_{$time}.jpg";
file_put_contents(__DIR__ . '/../public/' . $mockImgPath, 'MOCK_JPEG_CONTENT');

$appB = [
    'name_bn' => "টেস্ট সদস্য বি {$time}",
    'name_en' => "Test Member B {$time}",
    'phone' => '01811' . rand(100000, 999999),
    'email' => "member_b_{$time}@example.com",
    'category_id' => 'EARNING',
    'plan_id' => 'EARNING_MONTHLY',
    'trx_id' => "TRX-B-{$time}",
    'payment_method' => 'Nagad',
    'sender_number' => '01822222222',
    'district' => 'Chattogram',
    'avatar' => $mockImgPath // Explicitly uploaded image
];

$resB = MembershipService::createApplication($appB);
assertTest(!empty($resB['member']['member_code']), 'Member B created successfully', "Code: " . ($resB['member']['member_code'] ?? ''));
$memB = MembershipService::getMemberById($resB['member']['member_code']);
$avatarB = MembershipService::getMemberAvatar($memB);

assertTest($avatarB === $mockImgPath, "Member B resolves to Member B's uploaded image", "Got: {$avatarB}");
assertTest($avatarB !== $avatarA, 'Member B image is strictly different from Member A image');

// -------------------------------------------------------------------------
// TEST C: Existing member isolation
// -------------------------------------------------------------------------
echo "\n--- Scenario C: Existing Member Isolation ---\n";
// Check existing Member 872
$mem872 = MembershipService::getMemberById('SPS-000872');
assertTest($mem872 !== null, 'Existing Member 872 exists');
$avatar872 = MembershipService::getMemberAvatar($mem872);
assertTest(str_contains($avatar872, '000872'), 'Existing Member 872 keeps his uploaded image', "Got: {$avatar872}");

// Re-fetch Member A & B to ensure zero state bleeding
$memARefresh = MembershipService::getMemberById($resA['member']['member_code']);
$memBRefresh = MembershipService::getMemberById($resB['member']['member_code']);
assertTest(MembershipService::getMemberAvatar($memARefresh) === MembershipService::DEFAULT_ORGANIZATION_DP, 'Member A still has default DP');
assertTest(MembershipService::getMemberAvatar($memBRefresh) === $mockImgPath, "Member B still has Member B's DP");
assertTest(MembershipService::getMemberAvatar($memARefresh) !== $avatar872, 'Member A does not share image with Member 872');

// -------------------------------------------------------------------------
// TEST D: Pending registration and access restrictions
// -------------------------------------------------------------------------
echo "\n--- Scenario D: Pending Registration & Experience Restrictions ---\n";
$appC = [
    'name_bn' => "পরীক্ষা সদস্য সি {$time}",
    'name_en' => "Test Member C {$time}",
    'phone' => '01911' . rand(100000, 999999),
    'email' => "member_c_{$time}@example.com",
    'category_id' => 'STUDENT',
    'plan_id' => 'STUDENT_MONTHLY',
    'trx_id' => "TRX-C-{$time}",
    'payment_method' => 'bKash',
    'sender_number' => '01933333333',
    'district' => 'Sylhet'
];
$resC = MembershipService::createApplication($appC);
$memC = MembershipService::getMemberById($resC['member']['member_code']);
assertTest(MembershipService::isPendingStatus($memC['status']), 'Member C is in Pending verification state');
assertTest(!MembershipService::isActiveStatus($memC['status']), 'Member C is NOT active yet');

// Check that pending status locks approved actions
$paymentsC = MembershipService::getMemberPayments($memC['member_code']);
assertTest(count($paymentsC) > 0, 'Member C has a pending payment record awaiting FO audit');
assertTest($paymentsC[0]['status'] === 'Pending', 'Payment status is Pending');

// -------------------------------------------------------------------------
// TEST E: Finance approval workflow
// -------------------------------------------------------------------------
echo "\n--- Scenario E: Finance Officer Approval Workflow ---\n";
$paymentIdC = $paymentsC[0]['id'];
$verifyRes = MembershipService::verifyPayment($paymentIdC, [
    'name_bn' => 'জয় চক্রবর্তী',
    'name_en' => 'Joy Chakraborty',
    'role' => 'finance_officer',
    'notes' => 'TrxID and bank statement matched 100%'
]);
assertTest($verifyRes === true, 'Finance Officer verifies payment successfully');

// Re-check Member C
$memCApproved = MembershipService::getMemberById($resC['member']['member_code']);
assertTest(MembershipService::isActiveStatus($memCApproved['status']), 'Member C status transitioned to Active', "Status: {$memCApproved['status']}");
assertTest(!empty($memCApproved['start_date']), 'Membership start date initialized upon approval');
assertTest(!empty($memCApproved['notifications']), 'Member C received in-system notifications upon approval');
$latestNotif = end($memCApproved['notifications']);
assertTest($latestNotif['type'] === 'payment_approved', 'Notification type is payment_approved');
assertTest(!empty($latestNotif['invoice_id']), "Notification links to official invoice: {$latestNotif['invoice_id']}");

// -------------------------------------------------------------------------
// TEST F: Email failure simulation & in-app fallback
// -------------------------------------------------------------------------
echo "\n--- Scenario F: Email Failure Simulation & Fallback ---\n";
// Create Member D
$appD = [
    'name_bn' => "সদস্য ডি {$time}",
    'name_en' => "Test Member D {$time}",
    'phone' => '01511' . rand(100000, 999999),
    'email' => "member_d_sim_fail_{$time}@example.com",
    'category_id' => 'STUDENT',
    'plan_id' => 'STUDENT_MONTHLY',
    'trx_id' => "TRX-D-{$time}",
    'payment_method' => 'bKash',
    'sender_number' => '01544444444'
];
$resD = MembershipService::createApplication($appD);
$memD = MembershipService::getMemberById($resD['member']['member_code']);
$paymentsD = MembershipService::getMemberPayments($memD['member_code']);
$paymentIdD = $paymentsD[0]['id'];

// Enable email simulation failure
EmailService::$simulateFailure = true;
$verifyResD = MembershipService::verifyPayment($paymentIdD, [
    'name_bn' => 'জয় চক্রবর্তী',
    'name_en' => 'Joy Chakraborty',
    'role' => 'finance_officer',
    'notes' => 'Simulated email outage test'
]);
EmailService::$simulateFailure = false; // Reset

assertTest($verifyResD === true, 'Verification succeeds even when email fails');
$memDApproved = MembershipService::getMemberById($resD['member']['member_code']);
assertTest(MembershipService::isActiveStatus($memDApproved['status']), 'Member D is activated despite email failure');
assertTest(($memDApproved['notification_status']['email_status'] ?? '') === 'FAILED', 'Notification status flagged as FAILED/fallback', "Got: " . ($memDApproved['notification_status']['email_status'] ?? ''));
assertTest(!empty($memDApproved['notifications']), 'In-app notification is reliably present in member profile');

// -------------------------------------------------------------------------
// TEST G: Strict Data Isolation
// -------------------------------------------------------------------------
echo "\n--- Scenario G: Strict Data Isolation ---\n";
// 1. DP isolation
assertTest(MembershipService::getMemberAvatar($memA) !== MembershipService::getMemberAvatar($memB), 'Member A & B DP isolated');
// 2. Payments ledger isolation
$paymentsA = MembershipService::getMemberPayments($memA['member_code']);
$paymentsB = MembershipService::getMemberPayments($memB['member_code']);
$pIdsA = array_column($paymentsA, 'id');
$pIdsB = array_column($paymentsB, 'id');
$overlap = array_intersect($pIdsA, $pIdsB);
assertTest(empty($overlap), 'Payment records have zero overlap between Member A and Member B');

// 3. Member C invoice only matches Member C transaction ID
$invoiceIdC = $paymentsC[0]['transaction_id'];
$paymentsDIds = array_column(MembershipService::getMemberPayments($memD['member_code']), 'transaction_id');
assertTest(!in_array($invoiceIdC, $paymentsDIds), 'Invoice of Member C is never accessible in Member D payments ledger');

// 4. Notification isolation
$notifsC = $memCApproved['notifications'] ?? [];
$notifsD = $memDApproved['notifications'] ?? [];
assertTest(json_encode($notifsC) !== json_encode($notifsD), 'Notifications are isolated per member ID');

// Clean up temporary mock image
if (file_exists(__DIR__ . '/../public/' . $mockImgPath)) {
    unlink(__DIR__ . '/../public/' . $mockImgPath);
}

echo "\n========================================================================\n";
echo "TEST RESULTS: {$passCount} PASSED, {$failCount} FAILED\n";
echo "========================================================================\n";

if ($failCount > 0) {
    exit(1);
}
exit(0);
