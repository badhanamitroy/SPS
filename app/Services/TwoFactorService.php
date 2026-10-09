<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;

class TwoFactorService
{
    private const SESSION_ADMIN_KEY = 'sps_2fa_admin_pending';
    private const SESSION_MEMBER_KEY = 'sps_2fa_member_pending';
    private const OTP_EXPIRY_SECONDS = 600; // 10 minutes
    private const MAX_ATTEMPTS = 5;

    /**
     * Test-only: last admin OTP generated (plaintext, never persisted to disk or session).
     * Allows integration test suites to retrieve an OTP that was generated inside a
     * controller dispatch without needing a real email delivery system.
     */
    private static ?string $lastAdminOtp = null;
    private static ?string $lastMemberOtp = null;

    /**
     * Retrieve the last admin OTP generated (test environments only).
     * Returns null if no OTP has been generated yet in this process.
     */
    public static function getLastAdminOtp(): ?string
    {
        return self::$lastAdminOtp;
    }

    /**
     * Retrieve the last member OTP generated (test environments only).
     */
    public static function getLastMemberOtp(): ?string
    {
        return self::$lastMemberOtp;
    }

    /**
     * Start a 2FA challenge for an Admin user.
     */
    public static function initiateAdminChallenge(array $adminUser): array
    {
        Session::start();
        $adminId = (string)($adminUser['id'] ?? 'unknown');
        $rateKey = '2fa:resend:admin:' . $adminId;

        // Resend rate limit: max 3 challenges per 15 minutes
        if (\App\Core\RateLimiter::tooManyAttempts($rateKey, 3)) {
            AuditService::log(
                'auth.2fa_rate_limited',
                'security',
                $adminId,
                $adminUser['name_en'] ?? 'Admin',
                [],
                ['type' => 'admin'],
                "2FA initiation rate limit reached for admin {$adminId}"
            );
            return [
                'user_id' => $adminId,
                'email' => $adminUser['email'] ?? '',
                'code' => '',
                'rate_limited' => true,
                'available_in' => \App\Core\RateLimiter::availableIn($rateKey)
            ];
        }
        \App\Core\RateLimiter::hit($rateKey, 900);

        $code = (string)random_int(100000, 999999);
        $email = trim((string)($adminUser['email'] ?? ''));
        $name = $adminUser['name_en'] ?? $adminUser['name_bn'] ?? 'Admin';

        if (empty($email)) {
            $email = 'admin@sps-platform.org';
        }

        // Store HMAC-SHA256 representation of OTP in session state
        $codeHash = hash_hmac('sha256', $code, \App\Core\CryptoService::getPepper());

        $challenge = [
            'user_id' => $adminUser['id'],
            'email' => $email,
            'code_hash' => $codeHash,
            'code' => $code, // Kept in memory return value for test suite / email dispatcher
            'expires_at' => time() + self::OTP_EXPIRY_SECONDS,
            'attempts' => 0,
            'created_at' => time(),
        ];

        // In session, persist without plaintext code
        $sessionPayload = $challenge;
        unset($sessionPayload['code']);
        Session::set(self::SESSION_ADMIN_KEY, $sessionPayload);

        // Capture OTP in static property for test suite retrieval
        self::$lastAdminOtp = $code;

        // Send OTP via EmailService
        EmailService::send2FaOtp($email, $name, $code, 'Administrator');

        AuditService::log(
            'auth.2fa_challenge_sent',
            'security',
            $adminUser['id'],
            $name,
            [],
            ['email' => $email, 'type' => 'admin'],
            "2FA verification code dispatched to email for admin {$name}"
        );

        return $challenge;
    }

    /**
     * Verify Admin 2FA OTP code.
     */
    public static function verifyAdminChallenge(string $inputCode): array
    {
        Session::start();
        $challenge = Session::get(self::SESSION_ADMIN_KEY);

        if (!$challenge || !is_array($challenge)) {
            return [
                'success' => false,
                'message' => 'দ্বিমুখী প্রমাণীকরণের সময় শেষ হয়ে গেছে বা সেশন পাওয়া যায়নি। অনুগ্রহ করে পুনরায় লগইন করুন। (No active 2FA session found. Please log in again.)'
            ];
        }

        $userId = (string)($challenge['user_id'] ?? '');
        $ip = \App\Core\Session::getClientIp();
        $rateKey = '2fa:verify:admin:' . $userId . ':' . $ip;

        // Rate limit: max 10 verify attempts per 15 min
        if (\App\Core\RateLimiter::tooManyAttempts($rateKey, 10)) {
            AuditService::log(
                'auth.2fa_rate_limited',
                'security',
                $userId,
                $challenge['email'] ?? '',
                [],
                ['type' => 'admin', 'ip' => $ip],
                "Admin 2FA verification rate limit exceeded for user {$userId}"
            );
            return [
                'success' => false,
                'message' => 'অতিরিক্ত ভুল প্রচেষ্টার কারণে সাময়িকভাবে ব্লক করা হয়েছে। অনুগ্রহ করে ১৫ মিনিট পর চেষ্টা করুন। (Too many attempts. Please try again in 15 minutes.)'
            ];
        }

        if (time() > ($challenge['expires_at'] ?? 0)) {
            Session::forget(self::SESSION_ADMIN_KEY);
            AuditService::log(
                'auth.2fa_failed',
                'security',
                $userId,
                $challenge['email'] ?? '',
                [],
                ['reason' => 'expired'],
                "Admin 2FA verification failed: OTP expired for user {$userId}"
            );
            return [
                'success' => false,
                'message' => 'কোডের মেয়াদ উত্তীর্ণ হয়ে গেছে। অনুগ্রহ করে পুনরায় কোড পাঠিয়ে চেষ্টা করুন। (Verification code has expired. Please resend code.)'
            ];
        }

        if (($challenge['attempts'] ?? 0) >= self::MAX_ATTEMPTS) {
            Session::forget(self::SESSION_ADMIN_KEY);
            AuditService::log(
                'auth.2fa_failed',
                'security',
                $userId,
                $challenge['email'] ?? '',
                [],
                ['reason' => 'max_attempts_exceeded'],
                "Admin 2FA verification failed: maximum attempts exceeded for user {$userId}"
            );
            return [
                'success' => false,
                'message' => 'সর্বোচ্চ প্রচেষ্টার সীমা অতিক্রান্ত। নিরাপত্তার জন্য পুনরায় লগইন করুন। (Too many incorrect attempts. Please log in again.)'
            ];
        }

        $inputClean = preg_replace('/[^\d]/', '', trim($inputCode));
        $inputHash = hash_hmac('sha256', $inputClean, \App\Core\CryptoService::getPepper());

        $verified = false;
        if (!empty($challenge['code_hash'])) {
            $verified = hash_equals((string)$challenge['code_hash'], $inputHash);
        } elseif (!empty($challenge['code'])) {
            $verified = hash_equals((string)$challenge['code'], $inputClean);
        }

        if (!$verified) {
            \App\Core\RateLimiter::hit($rateKey, 900);
            $challenge['attempts'] = ($challenge['attempts'] ?? 0) + 1;
            Session::set(self::SESSION_ADMIN_KEY, $challenge);
            $remaining = max(0, self::MAX_ATTEMPTS - $challenge['attempts']);

            AuditService::log(
                'auth.2fa_failed',
                'security',
                $userId,
                $challenge['email'] ?? '',
                [],
                ['attempts' => $challenge['attempts']],
                "Admin 2FA failed: invalid OTP code for user {$userId}"
            );

            return [
                'success' => false,
                'message' => "ভুল ভেরিফিকেশন কোড। অবশিষ্ট প্রচেষ্টা: {$remaining} টি। (Invalid verification code. {$remaining} attempts remaining.)"
            ];
        }

        // Successfully verified!
        // Invalidate challenge immediately (guarantees single-use)
        Session::forget(self::SESSION_ADMIN_KEY);
        \App\Core\RateLimiter::resetAttempts($rateKey);

        AuditService::log(
            'auth.2fa_success',
            'security',
            $userId,
            $challenge['email'] ?? '',
            [],
            ['type' => 'admin'],
            "Admin {$userId} successfully verified 2FA code"
        );

        return [
            'success' => true,
            'user_id' => $userId
        ];
    }

    /**
     * Start a 2FA challenge for a Member.
     */
    public static function initiateMemberChallenge(array $member): array
    {
        Session::start();
        $memberCode = (string)($member['member_code'] ?? 'unknown');
        $rateKey = '2fa:resend:member:' . $memberCode;

        // Resend rate limit: max 3 challenges per 15 minutes
        if (\App\Core\RateLimiter::tooManyAttempts($rateKey, 3)) {
            AuditService::log(
                'membership.2fa_rate_limited',
                'security',
                $memberCode,
                $member['name_en'] ?? 'Member',
                [],
                ['type' => 'member'],
                "2FA initiation rate limit reached for member {$memberCode}"
            );
            return [
                'member_code' => $memberCode,
                'email' => $member['email'] ?? '',
                'code' => '',
                'rate_limited' => true,
                'available_in' => \App\Core\RateLimiter::availableIn($rateKey)
            ];
        }
        \App\Core\RateLimiter::hit($rateKey, 900);

        $code = (string)random_int(100000, 999999);
        $email = trim((string)($member['email'] ?? ''));
        $name = $member['name_en'] ?? $member['name_bn'] ?? 'Member';

        if (empty($email)) {
            $email = 'member@sps-platform.org';
        }

        $codeHash = hash_hmac('sha256', $code, \App\Core\CryptoService::getPepper());

        $challenge = [
            'member_code' => $member['member_code'],
            'email' => $email,
            'code_hash' => $codeHash,
            'code' => $code, // Kept in memory return value for test suite / email dispatcher
            'expires_at' => time() + self::OTP_EXPIRY_SECONDS,
            'attempts' => 0,
            'created_at' => time(),
        ];

        // In session, persist without plaintext code
        $sessionPayload = $challenge;
        unset($sessionPayload['code']);
        Session::set(self::SESSION_MEMBER_KEY, $sessionPayload);

        // Capture OTP in static property for test suite retrieval
        self::$lastMemberOtp = $code;

        // Send OTP via EmailService
        EmailService::send2FaOtp($email, $name, $code, 'Member');

        AuditService::log(
            'membership.2fa_challenge_sent',
            'security',
            $member['id'] ?? $member['member_code'],
            $name,
            [],
            ['email' => $email, 'type' => 'member'],
            "2FA verification code dispatched to email for member {$name}"
        );

        return $challenge;
    }

    /**
     * Verify Member 2FA OTP code.
     */
    public static function verifyMemberChallenge(string $inputCode): array
    {
        Session::start();
        $challenge = Session::get(self::SESSION_MEMBER_KEY);

        if (!$challenge || !is_array($challenge)) {
            return [
                'success' => false,
                'message' => 'দ্বিমুখী প্রমাণীকরণের সময় শেষ হয়ে গেছে বা সেশন পাওয়া যায়নি। অনুগ্রহ করে পুনরায় লগইন করুন।'
            ];
        }

        $memberCode = (string)($challenge['member_code'] ?? '');
        $ip = \App\Core\Session::getClientIp();
        $rateKey = '2fa:verify:member:' . $memberCode . ':' . $ip;

        // Rate limit: max 10 verify attempts per 15 min
        if (\App\Core\RateLimiter::tooManyAttempts($rateKey, 10)) {
            AuditService::log(
                'membership.2fa_rate_limited',
                'security',
                $memberCode,
                $challenge['email'] ?? '',
                [],
                ['type' => 'member', 'ip' => $ip],
                "Member 2FA verification rate limit exceeded for {$memberCode}"
            );
            return [
                'success' => false,
                'message' => 'অতিরিক্ত ভুল প্রচেষ্টার কারণে সাময়িকভাবে ব্লক করা হয়েছে। অনুগ্রহ করে ১৫ মিনিট পর চেষ্টা করুন।'
            ];
        }

        if (time() > ($challenge['expires_at'] ?? 0)) {
            Session::forget(self::SESSION_MEMBER_KEY);
            AuditService::log(
                'membership.2fa_failed',
                'security',
                $memberCode,
                $challenge['email'] ?? '',
                [],
                ['reason' => 'expired'],
                "Member 2FA verification failed: OTP expired for {$memberCode}"
            );
            return [
                'success' => false,
                'message' => 'কোডের মেয়াদ উত্তীর্ণ হয়ে গেছে। অনুগ্রহ করে পুনরায় কোড পাঠিয়ে চেষ্টা করুন।'
            ];
        }

        if (($challenge['attempts'] ?? 0) >= self::MAX_ATTEMPTS) {
            Session::forget(self::SESSION_MEMBER_KEY);
            AuditService::log(
                'membership.2fa_failed',
                'security',
                $memberCode,
                $challenge['email'] ?? '',
                [],
                ['reason' => 'max_attempts_exceeded'],
                "Member 2FA verification failed: maximum attempts exceeded for {$memberCode}"
            );
            return [
                'success' => false,
                'message' => 'সর্বোচ্চ প্রচেষ্টার সীমা অতিক্রান্ত। নিরাপত্তার জন্য পুনরায় লগইন করুন।'
            ];
        }

        $inputClean = preg_replace('/[^\d]/', '', trim($inputCode));
        $inputHash = hash_hmac('sha256', $inputClean, \App\Core\CryptoService::getPepper());

        $verified = false;
        if (!empty($challenge['code_hash'])) {
            $verified = hash_equals((string)$challenge['code_hash'], $inputHash);
        } elseif (!empty($challenge['code'])) {
            $verified = hash_equals((string)$challenge['code'], $inputClean);
        }

        if (!$verified) {
            \App\Core\RateLimiter::hit($rateKey, 900);
            $challenge['attempts'] = ($challenge['attempts'] ?? 0) + 1;
            Session::set(self::SESSION_MEMBER_KEY, $challenge);
            $remaining = max(0, self::MAX_ATTEMPTS - $challenge['attempts']);

            AuditService::log(
                'membership.2fa_failed',
                'security',
                $memberCode,
                $challenge['email'] ?? '',
                [],
                ['attempts' => $challenge['attempts']],
                "Member 2FA failed: invalid OTP code for {$memberCode}"
            );

            return [
                'success' => false,
                'message' => "ভুল ভেরিফিকেশন কোড। অবশিষ্ট প্রচেষ্টা: {$remaining} টি।"
            ];
        }

        // Successfully verified! Invalidate immediately
        Session::forget(self::SESSION_MEMBER_KEY);
        \App\Core\RateLimiter::resetAttempts($rateKey);

        AuditService::log(
            'membership.2fa_success',
            'security',
            $memberCode,
            $challenge['email'] ?? '',
            [],
            ['type' => 'member'],
            "Member {$memberCode} successfully verified 2FA code"
        );

        return [
            'success' => true,
            'member_code' => $memberCode
        ];
    }

    /**
     * Check if an active Admin 2FA challenge exists in session.
     */
    public static function getPendingAdminChallenge(): ?array
    {
        Session::start();
        $c = Session::get(self::SESSION_ADMIN_KEY);
        return is_array($c) ? $c : null;
    }

    /**
     * Check if an active Member 2FA challenge exists in session.
     */
    public static function getPendingMemberChallenge(): ?array
    {
        Session::start();
        $c = Session::get(self::SESSION_MEMBER_KEY);
        return is_array($c) ? $c : null;
    }

    /**
     * Clear challenges
     */
    public static function clearChallenges(): void
    {
        Session::start();
        Session::forget(self::SESSION_ADMIN_KEY);
        Session::forget(self::SESSION_MEMBER_KEY);
    }
}
