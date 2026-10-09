<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\App;
use App\Core\Session;
use App\Services\AuthService;
use App\Services\MembershipService;
use App\Services\TwoFactorService;
use App\Services\RbacService;
use App\Core\CryptoService;
use App\Core\RateLimiter;
use App\Core\PasswordPolicy;

$app = new App(dirname(__DIR__));
Session::start();

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

// ============================================================
// SETUP: Ensure admin password is at known baseline
// ============================================================
RateLimiter::resetAttempts('login:admin:acct:' . hash('sha256', 'anik'));
RateLimiter::resetAttempts('login:admin:acct:' . hash('sha256', 'usr_anik'));
RateLimiter::resetAttempts('pwdchange:admin:usr_anik');
$adminBaselinePwd = 'sps@admin2026';
RbacService::updatePasswordHashDirect('usr_anik', CryptoService::hashPassword($adminBaselinePwd));

echo "=== Running SPS Authentication & Security Test Suite ===\n\n";

// 1. Admin Credential Validation
$validAdmin = AuthService::validateCredentials('anik', $adminBaselinePwd);
assert_test('Admin: Valid credentials validate successfully', $validAdmin !== null && ($validAdmin['id'] ?? '') === 'usr_anik');

$invalidAdmin = AuthService::validateCredentials('anik', 'wrongpassword123');
assert_test('Admin: Invalid password rejected', $invalidAdmin === null);

// 2. Admin 2FA Challenge & Verification
$challenge = TwoFactorService::initiateAdminChallenge($validAdmin);
assert_test('Admin 2FA: Challenge created with 6-digit code', !empty($challenge['code']) && strlen($challenge['code']) === 6);

$verifyFail = TwoFactorService::verifyAdminChallenge('000000');
assert_test('Admin 2FA: Incorrect OTP code rejected', $verifyFail['success'] === false);

$verifySuccess = TwoFactorService::verifyAdminChallenge($challenge['code']);
assert_test('Admin 2FA: Correct OTP code verifies successfully', $verifySuccess['success'] === true && $verifySuccess['user_id'] === 'usr_anik');

// 3. Admin Self-Service Password Change (uses AuthService::updateAdminPassword)
$newAdminPwd = 'NewSpsAdmin2026!';
$pwdUpdateRes = AuthService::updateAdminPassword('usr_anik', $adminBaselinePwd, $newAdminPwd);
assert_test('Admin Password: Self-service update succeeds with correct current password', $pwdUpdateRes['success'] === true, $pwdUpdateRes['message'] ?? '');

// Verify new password works
$newAdminCheck = AuthService::validateCredentials('anik', $newAdminPwd);
assert_test('Admin Password: New password authenticates successfully', $newAdminCheck !== null);

// TEARDOWN: reset admin password to baseline via direct hash update (avoids rate-limit cascade)
RateLimiter::resetAttempts('pwdchange:admin:usr_anik');
RbacService::updatePasswordHashDirect('usr_anik', CryptoService::hashPassword($adminBaselinePwd));

// 4. Member Record Exists
$sampleMember = MembershipService::getMemberById('SPS-000872');
assert_test('Member: Sample member SPS-000872 found', $sampleMember !== null);

// 5. Admin Reset Member Password (admin privilege path - no current password needed)
// Password must NOT contain member identifier tokens (e.g. 'sps' from code SPS-000872)
$memberTestPwd = 'HinduDharma@2026';
$memberTestPwdNew = 'Bhagavad@Gita2026';
RateLimiter::resetAttempts('pwdchange:member:SPS-000872');
$memAdminResetRes = MembershipService::adminResetMemberPassword('SPS-000872', $memberTestPwd);
assert_test('Member Password (Admin Reset): Password reset successfully via admin path', $memAdminResetRes['success'] === true, $memAdminResetRes['message'] ?? '');

// 6. Verify new password
$updatedMember = MembershipService::getMemberById('SPS-000872');
$memValid = MembershipService::verifyMemberPassword($updatedMember, $memberTestPwd);
assert_test('Member Password: New password verified correctly', $memValid === true);

$memInvalid = MembershipService::verifyMemberPassword($updatedMember, 'WrongPassword!');
assert_test('Member Password: Wrong password rejected', $memInvalid === false);

// 7. Member Self-Service Password Change (requires current password)
RateLimiter::resetAttempts('pwdchange:member:SPS-000872');
$memSelfChangeRes = MembershipService::updateMemberPassword('SPS-000872', $memberTestPwdNew, $memberTestPwd);
assert_test('Member Password (Self-Service): Change succeeds with correct current password', $memSelfChangeRes['success'] === true, $memSelfChangeRes['message'] ?? '');

$memSelfChangeBadCurrent = MembershipService::updateMemberPassword('SPS-000872', 'AnotherStrong@2026', 'WrongCurrentPassword');
assert_test('Member Password (Self-Service): Rejected with incorrect current password', $memSelfChangeBadCurrent['success'] === false);

// TEARDOWN: restore member password to a known state
RateLimiter::resetAttempts('pwdchange:member:SPS-000872');
MembershipService::adminResetMemberPassword('SPS-000872', $memberTestPwd);

// 8. Member 2FA Challenge & Verification
$memChallenge = TwoFactorService::initiateMemberChallenge($updatedMember);
assert_test('Member 2FA: Challenge created with 6-digit OTP', !empty($memChallenge['code']) && strlen($memChallenge['code']) === 6);

$memVerifyFail = TwoFactorService::verifyMemberChallenge('999999');
assert_test('Member 2FA: Invalid OTP rejected', $memVerifyFail['success'] === false);

$memVerifyOk = TwoFactorService::verifyMemberChallenge($memChallenge['code']);
assert_test('Member 2FA: Valid OTP verified successfully', $memVerifyOk['success'] === true && $memVerifyOk['member_code'] === 'SPS-000872');

// 9. PasswordPolicy enforcement
$policyShort = PasswordPolicy::validate('short');
assert_test('PasswordPolicy: Short password rejected (<12 chars)', $policyShort['valid'] === false);

$policyCommon = PasswordPolicy::validate('password123456');
assert_test('PasswordPolicy: Common password blocked', $policyCommon['valid'] === false);

$policyGood = PasswordPolicy::validate('Tr0ub4dor&3-Staple');
assert_test('PasswordPolicy: Strong passphrase accepted', $policyGood['valid'] === true);

// 10. Rate Limiter smoke test
$rl = RateLimiter::tooManyAttempts('test:smoke:' . uniqid(), 5);
assert_test('RateLimiter: Fresh key is not rate-limited', $rl === false);

echo "\nTest Results: {$passedTests} / {$totalTests} passed.\n";
if ($passedTests === $totalTests) {
    echo "ALL TESTS PASSED SUCCESSFULLY!\n";
    exit(0);
} else {
    echo "SOME TESTS FAILED!\n";
    exit(1);
}