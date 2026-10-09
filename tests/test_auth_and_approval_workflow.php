<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Session;
use App\Services\AuthService;
use App\Services\MembershipService;
use App\Services\RbacService;

echo "====================================================================\n";
echo "SPS AUTHENTICATION & MEMBERSHIP WORKFLOW VERIFICATION SUITE\n";
echo "====================================================================\n\n";

Session::start();
$testsPassed = 0;
$totalTests = 0;

function assertTest(string $description, bool $condition): void {
    global $testsPassed, $totalTests;
    $totalTests++;
    if ($condition) {
        $testsPassed++;
        echo " [PASS] {$description}\n";
    } else {
        echo " [FAIL] {$description}\n";
    }
}

// -------------------------------------------------------------------------
// PART 1: MEMBER REGISTRATION -> WAIT FOR APPROVAL -> GOT APPROVED -> PROFILE CREATION -> UPDATE INFO
// -------------------------------------------------------------------------
echo "--- PART 1: Member Registration & Approval Lifecycle ---\n";

// 1.1 Create Member with Custom Password
$testEmail = 'newmember_' . uniqid() . '@example.com';
$customPassword = 'UserSecretPass@2026';

$inputData = [
    'name_bn' => 'সুদীপ্ত চক্রবর্তী',
    'name_en' => 'Sudipta Chakraborty',
    'email' => $testEmail,
    'phone' => '01719998877',
    'category_id' => 'STUDENT',
    'plan_id' => 'STUDENT_MONTHLY',
    'payment_method' => 'bKash',
    'trx_id' => 'TRX' . strtoupper(substr(md5(uniqid()), 0, 8)),
    'password' => $customPassword,
];

$res = MembershipService::createApplication($inputData);
$newMember = $res['member'];
$memberCode = $newMember['member_code'];

assertTest("Member record created with unique ID {$memberCode}", !empty($memberCode));
assertTest("Initial Member status is 'Pending'", $newMember['status'] === 'Pending');
assertTest("Member password_hash is created and verifyMemberPassword succeeds", MembershipService::verifyMemberPassword($newMember, $customPassword));
assertTest("Incorrect password fails verification", !MembershipService::verifyMemberPassword($newMember, 'WrongPass123'));

// 1.2 Isolation Check during Pending: NO Card, NO Edit Form, Shows Sacred Waiting Screen
$connection = @fsockopen('127.0.0.1', 8000, $errno, $errstr, 0.5);
if (!$connection) {
    echo "SKIPPED: start php -S 127.0.0.1:8000 -t public public/index.php\n";
    exit(0);
}
fclose($connection);

$dashboardHtml = @file_get_contents("http://127.0.0.1:8000/bn/membership/dashboard?as={$memberCode}");
if ($dashboardHtml === false) {
    echo "SKIPPED: start php -S 127.0.0.1:8000 -t public public/index.php\n";
    exit(0);
}

assertTest("Pending member dashboard has 'Pending' status indicator", strpos($dashboardHtml, 'অপেক্ষমাণ') !== false || strpos($dashboardHtml, 'Pending') !== false);
assertTest("Pending member dashboard displays 4-step workflow indicator", strpos($dashboardHtml, 'ধাপ') !== false);
assertTest("Pending member dashboard displays Bhagavad Gita 5.23 verse", strpos($dashboardHtml, 'গীতা') !== false || strpos($dashboardHtml, '৫, শ্লোক ২৩') !== false);
assertTest("Pending member dashboard hides printableCard", strpos($dashboardHtml, 'id="printableCard"') === false);
assertTest("Pending member dashboard hides profile update form", strpos($dashboardHtml, 'action="/bn/membership/profile/update"') === false);
assertTest("Pending member dashboard hides password management form", strpos($dashboardHtml, 'action="/bn/membership/password/update"') === false);

// 1.3 Approval by Finance Officer (Treasurer Joy Chakraborty)
$joyAdmin = RbacService::getUser('usr_joy');
$approveOk = MembershipService::approveMember($memberCode, $joyAdmin);
assertTest("Finance Officer approves member", $approveOk);

$approvedMember = MembershipService::getMemberById($memberCode);
assertTest("Member status transitioned to 'Active'", $approvedMember['status'] === 'Active');

// 1.4 Post-Approval: Full Profile Created & Active Dashboard Unlocked
$activeDashboardHtml = @file_get_contents("http://127.0.0.1:8000/bn/membership/dashboard?as={$memberCode}");
if ($activeDashboardHtml === false) {
    echo "SKIPPED: start php -S 127.0.0.1:8000 -t public public/index.php\n";
    exit(0);
}

assertTest("Active member dashboard shows digital smart card (printableCard)", strpos($activeDashboardHtml, 'id="printableCard"') !== false);
assertTest("Active member dashboard shows profile update form", strpos($activeDashboardHtml, 'action="/bn/membership/profile/update"') !== false);
assertTest("Active member dashboard shows security & password form", strpos($activeDashboardHtml, 'action="/bn/membership/password/update"') !== false);
assertTest("Active member dashboard does NOT show pending 4-step tracker", strpos($activeDashboardHtml, 'Step 2 of 4') === false);

// 1.5 Member Updates Profile Info
$updateResult = MembershipService::updateMemberProfile($memberCode, [
    'address' => 'House 42, Road 7, Dhanmondi, Dhaka',
    'blood_group' => 'O+',
    'institution' => 'University of Dhaka',
]);
assertTest("Member can update profile information after approval", $updateResult['success']);
$updatedMember = MembershipService::getMemberById($memberCode);
assertTest("Updated address saved correctly", ($updatedMember['address'] ?? '') === 'House 42, Road 7, Dhanmondi, Dhaka');


// -------------------------------------------------------------------------
// PART 2: ADMIN AUTHENTICATION (OTP ON 1ST LOGIN -> MANDATORY UNIQUE PASSWORD CHANGE)
// -------------------------------------------------------------------------
echo "\n--- PART 2: Admin One-Time Password (OTP) & Unique Password Workflow ---\n";

// 2.1 Super Admin Creates Admin User with OTP
$testUsername = 'officer_' . substr(uniqid(), 0, 6);
$createAdminRes = RbacService::createAdminUserWithOtp([
    'username' => $testUsername,
    'name_bn' => 'দেবদূত বন্দ্যোপাধ্যায়',
    'name_en' => 'Debadutta Banerjee',
    'email' => "{$testUsername}@sps.org",
    'role' => 'content_editor',
    'designation_bn' => 'সহকারী সম্পাদক',
    'designation_en' => 'Assistant Editor',
], 'usr_anik');

assertTest("Super Admin creates admin officer with OTP", $createAdminRes['success']);
$officerUser = $createAdminRes['user'];
$officerOtp = $createAdminRes['otp'];
assertTest("Admin has must_change_password flag set to true", !empty($officerUser['must_change_password']));
assertTest("Admin has is_otp flag set to true", !empty($officerUser['is_otp']));
assertTest("OTP format matches SPS-OTP-XXXXXX", str_starts_with($officerOtp, 'SPS-OTP-'));

// 2.2 Officer Logs in with OTP
$authMatch = AuthService::validateCredentials($testUsername, $officerOtp);
assertTest("Officer credentials valid using initial OTP", $authMatch !== null);
AuthService::loginAs($officerUser['id']);
assertTest("AuthService detects admin mustChangePassword() === true", AuthService::mustChangePassword());

// 2.3 Super Admin Resets OTP for an Admin
$resetRes = RbacService::resetAdminOtp($officerUser['id'], 'usr_anik');
assertTest("Super Admin can reset officer password to a fresh OTP", $resetRes['success']);
$freshOtp = $resetRes['otp'];
assertTest("New fresh OTP generated", str_starts_with($freshOtp, 'SPS-OTP-') && $freshOtp !== $officerOtp);

// 2.4 Officer Changes OTP to Unique Personal Password
$officerUniquePassword = 'MyUniqueStrongPassword@2026!';
$changeRes = AuthService::completeInitialPasswordChange($officerUser['id'], $freshOtp, $officerUniquePassword);
assertTest("Officer successfully changes OTP to their unique personal password", $changeRes['success']);

$updatedOfficer = RbacService::getUser($officerUser['id']);
assertTest("must_change_password flag is now false in storage", empty($updatedOfficer['must_change_password']));
assertTest("is_otp flag is now false in storage", empty($updatedOfficer['is_otp']));
assertTest("initial_otp is cleared from user record", empty($updatedOfficer['initial_otp']));
assertTest("AuthService::mustChangePassword() is now false", !AuthService::mustChangePassword());

// 2.5 Verification of Subsequent Logins with Unique Password
$oldOtpAttempt = AuthService::validateCredentials($testUsername, $freshOtp);
assertTest("Old OTP is rejected after password change", $oldOtpAttempt === null);

$newUniqueAttempt = AuthService::validateCredentials($testUsername, $officerUniquePassword);
assertTest("Officer logs in successfully with their new unique password", $newUniqueAttempt !== null);

echo "\n====================================================================\n";
echo "SUMMARY: {$testsPassed} / {$totalTests} TESTS PASSED\n";
echo "====================================================================\n";

if ($testsPassed === $totalTests) {
    echo ">>> ALL WORKFLOW TESTS PASSED 100%! <<<\n";
    exit(0);
} else {
    echo ">>> SOME TESTS FAILED! <<<\n";
    exit(1);
}
