<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;

class AuthService
{
    private const SESSION_KEY = 'auth_admin_user_id';

    /**
     * Check if an admin user is currently authenticated.
     */
    public static function check(): bool
    {
        Session::start();
        $userId = Session::get(self::SESSION_KEY);
        if (!$userId) {
            return false;
        }
        $user = RbacService::getUser($userId);
        if (!$user) {
            return false;
        }

        // Verify session auth_version against stored user auth_version (Session Invalidation after Password Change)
        $expectedVersion = $user['auth_version'] ?? 1;
        $sessionVersion = Session::get('admin_auth_version');
        if ($sessionVersion !== null && (int)$sessionVersion !== (int)$expectedVersion) {
            AuditService::log(
                'auth.session_invalidated',
                'security',
                $userId,
                $user['name_en'] ?? $user['name_bn'],
                [],
                ['reason' => 'credentials_changed_remotely'],
                "Admin session invalidated due to remote credential update for user {$userId}"
            );
            self::logout();
            return false;
        }

        return true;
    }

    /**
     * Retrieve the currently authenticated admin user.
     * Returns null if not logged in.
     */
    public static function getCurrentUser(): ?array
    {
        Session::start();
        $userId = Session::get(self::SESSION_KEY);
        if ($userId) {
            $user = RbacService::getUser($userId);
            if ($user) {
                return $user;
            }
        }
        return null;
    }

    /**
     * Attempt login with identifier (email, username, or user ID) and password.
     */
    /**
     * Validate credentials without establishing session (used for 2FA pipeline).
     */
    public static function validateCredentials(string $identifier, string $password): ?array
    {
        Session::start();
        $identifier = trim(strtolower($identifier));

        if (empty($identifier) || empty($password)) {
            return null;
        }

        $ip = \App\Core\Session::getClientIp();
        $rateKeyIp = 'login:admin:ip:' . $ip;
        $rateKeyAcct = 'login:admin:acct:' . hash('sha256', $identifier);

        // Enforce rate limiting: 5 failed attempts per 15 minutes
        if (\App\Core\RateLimiter::tooManyAttempts($rateKeyIp, 5) || \App\Core\RateLimiter::tooManyAttempts($rateKeyAcct, 5)) {
            AuditService::log(
                'auth.rate_limited',
                'security',
                'guest',
                $identifier,
                [],
                ['identifier' => $identifier, 'ip' => $ip],
                "Admin login rate limit triggered for '{$identifier}' from IP {$ip}"
            );
            return null;
        }

        // Apply progressive delay to resist timing attacks
        \App\Core\RateLimiter::applyProgressiveDelay($rateKeyIp);

        $users = RbacService::getUsers();
        $matchedUser = null;

        foreach ($users as $user) {
            $userEmail = strtolower($user['email'] ?? '');
            $username = strtolower($user['username'] ?? '');
            $userId = strtolower($user['id'] ?? '');

            if ($identifier === $userEmail || $identifier === $username || $identifier === $userId) {
                $matchedUser = $user;
                break;
            }

            // Also support common role-based shortcuts for usability
            if ($identifier === 'superadmin' && ($user['role'] ?? '') === 'super_admin') {
                $matchedUser = $user;
                break;
            }
            if ($identifier === 'admin' && ($user['role'] ?? '') === 'admin') {
                $matchedUser = $user;
                break;
            }
        }

        if (!$matchedUser) {
            // Constant-time dummy verify prevents user enumeration via response timing
            \App\Core\CryptoService::dummyVerify($password);
            \App\Core\RateLimiter::hit($rateKeyIp);
            \App\Core\RateLimiter::hit($rateKeyAcct);
            AuditService::log(
                'auth.login_failed',
                'security',
                'guest',
                $identifier,
                [],
                ['identifier' => $identifier, 'reason' => 'User not found'],
                "Failed admin login attempt: user '{$identifier}' not found"
            );
            return null;
        }

        // Check if temporary credential has expired
        if (!empty($matchedUser['must_change_password']) && !empty($matchedUser['temporary_credential_expires_at'])) {
            if (time() > (int)$matchedUser['temporary_credential_expires_at']) {
                AuditService::log(
                    'auth.login_failed',
                    'security',
                    $matchedUser['id'],
                    $matchedUser['name_en'] ?? $matchedUser['name_bn'],
                    [],
                    ['identifier' => $identifier, 'reason' => 'Temporary credential expired'],
                    "Failed admin login attempt: temporary credential expired for '{$identifier}'"
                );
                return null;
            }
        }

        // Validate password strictly via CryptoService (Argon2id + pepper + legacy Bcrypt migration)
        $hash = $matchedUser['password_hash'] ?? '';
        $valid = !empty($hash) && \App\Core\CryptoService::verifyPassword($password, $hash);

        if (!$valid) {
            \App\Core\RateLimiter::hit($rateKeyIp);
            \App\Core\RateLimiter::hit($rateKeyAcct);
            AuditService::log(
                'auth.login_failed',
                'security',
                $matchedUser['id'],
                $matchedUser['name_en'] ?? $matchedUser['name_bn'],
                [],
                ['identifier' => $identifier, 'reason' => 'Invalid password'],
                "Failed admin login attempt: invalid password for '{$identifier}'"
            );
            return null;
        }

        // Login valid: reset rate limit attempts
        \App\Core\RateLimiter::resetAttempts($rateKeyIp);
        \App\Core\RateLimiter::resetAttempts($rateKeyAcct);

        // Auto-rehash legacy hash to Argon2id with server-side pepper
        if (\App\Core\CryptoService::needsRehash($hash)) {
            $newHash = \App\Core\CryptoService::hashPassword($password);
            RbacService::updatePasswordHashDirect($matchedUser['id'], $newHash);
            $matchedUser['password_hash'] = $newHash;
        }

        return $matchedUser;
    }

    /**
     * Attempt login with identifier and password (direct login without 2FA, backwards-compatible).
     */
    public static function attempt(string $identifier, string $password): bool
    {
        $user = self::validateCredentials($identifier, $password);
        if (!$user) {
            return false;
        }

        // Successful authentication - regenerate session ID to prevent fixation
        Session::regenerate(true);
        Session::set(self::SESSION_KEY, $user['id']);
        Session::set('admin_auth_version', $user['auth_version'] ?? 1);

        if (!empty($user['must_change_password'])) {
            Session::set('admin_must_change_password', true);
            Session::set('admin_pre_auth', true);
        } else {
            Session::remove('admin_must_change_password');
            Session::remove('admin_pre_auth');
        }

        AuditService::log(
            'auth.login_success',
            'security',
            $user['id'],
            $user['name_en'] ?? $user['name_bn'],
            [],
            ['role' => $user['role'], 'login_at' => date('Y-m-d H:i:s')],
            "Admin '{$user['name_en']}' successfully logged into SPS Admin Console",
            $user
        );

        return true;
    }

    /**
     * Update admin password securely with current password verification.
     */
    public static function updateAdminPassword(string $userId, string $currentPassword, string $newPassword): array
    {
        $user = RbacService::getUser($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'অ্যাডমিন অ্যাকাউন্ট খুঁজে পাওয়া যায়নি। (Admin user not found.)'];
        }

        // Rate limit password change attempts
        $rateKey = 'pwdchange:admin:' . $userId;
        if (\App\Core\RateLimiter::tooManyAttempts($rateKey, 5)) {
            return ['success' => false, 'message' => 'পাসওয়ার্ড পরিবর্তনের অতিরিক্ত প্রচেষ্টা। অনুগ্রহ করে ১৫ মিনিট পর চেষ্টা করুন। (Too many attempts. Please try again later.)'];
        }

        // Verify current password strictly against stored Argon2id/Bcrypt hash
        $hash = $user['password_hash'] ?? '';
        $validCurrent = !empty($hash) && \App\Core\CryptoService::verifyPassword($currentPassword, $hash);

        if (!$validCurrent) {
            \App\Core\RateLimiter::hit($rateKey, 900);
            return ['success' => false, 'message' => 'বর্তমান পাসওয়ার্ডটি সঠিক নয়। (Current password is incorrect.)'];
        }

        \App\Core\RateLimiter::resetAttempts($rateKey);

        // Enforce enterprise password policy
        $policyRes = \App\Core\PasswordPolicy::validate($newPassword, $user);
        if (!$policyRes['valid']) {
            return ['success' => false, 'message' => $policyRes['error_bn'] ?? $policyRes['error']];
        }

        if ($newPassword === $currentPassword) {
            return ['success' => false, 'message' => 'নতুন পাসওয়ার্ডটি বর্তমান পাসওয়ার্ড থেকে ভিন্ন হতে হবে। (New password must differ from current password.)'];
        }

        // Update password hash in storage with Argon2id + pepper
        $res = RbacService::updateUserProfile($userId, ['password' => $newPassword]);
        if (!($res['success'] ?? false)) {
            return ['success' => false, 'message' => $res['message'] ?? 'পাসওয়ার্ড হালনাগাদ ব্যর্থ হয়েছে। (Failed to update password.)'];
        }

        $updatedUser = RbacService::getUser($userId);
        $newAuthVersion = $updatedUser['auth_version'] ?? 2;

        // Session ID regeneration upon privilege/credential change & update active session auth_version
        Session::regenerate(true);
        Session::set('admin_auth_version', $newAuthVersion);

        AuditService::log(
            'auth.password_changed',
            'security',
            $userId,
            $user['name_en'] ?? $user['name_bn'],
            [],
            ['user_id' => $userId, 'updated_at' => date('Y-m-d H:i:s')],
            "Admin user '{$user['name_en']}' successfully changed their password"
        );

        // Dispatch email alert
        if (!empty($user['email'])) {
            EmailService::sendPasswordChangedAlert($user['email'], $user['name_en'] ?? $user['name_bn'], 'Administrator');
        }

        return ['success' => true, 'message' => 'পাসওয়ার্ড সফলভাবে হালনাগাদ করা হয়েছে। (Password successfully updated.)'];
    }

    /**
     * Terminate active admin session completely.
     */
    public static function logout(): void
    {
        Session::start();
        $user = self::getCurrentUser();
        if ($user) {
            AuditService::log(
                'auth.logout',
                'security',
                $user['id'],
                $user['name_en'] ?? $user['name_bn'],
                [],
                ['logout_at' => date('Y-m-d H:i:s')],
                "Admin '{$user['name_en']}' logged out",
                $user
            );
        }
        Session::remove(self::SESSION_KEY);
        Session::remove('admin_must_change_password');
        Session::remove('admin_pre_auth');
        Session::destroy();
        Session::start();
    }

    /**
     * Authenticate explicitly as a given user (for automated test suites and session management).
     */
    public static function loginAs(string $userId): bool
    {
        Session::start();
        $target = RbacService::getUser($userId);
        if (!$target) {
            return false;
        }
        Session::regenerate(true);
        Session::set(self::SESSION_KEY, $userId);
        Session::set('admin_auth_version', $target['auth_version'] ?? 1);
        if (!empty($target['must_change_password'])) {
            Session::set('admin_must_change_password', true);
            Session::set('admin_pre_auth', true);
        } else {
            Session::remove('admin_must_change_password');
            Session::remove('admin_pre_auth');
        }
        return true;
    }

    /**
     * Switch active admin user (backwards-compatible alias for test suites).
     */
    public static function switchUser(string $userId): bool
    {
        return self::loginAs($userId);
    }

    /**
     * Check if the current user has a specific permission.
     */
    public static function can(string $permission): bool
    {
        $user = self::getCurrentUser();
        if (!$user) {
            return false;
        }
        $roleId = $user['role'] ?? 'guest';
        if ($roleId === 'super_admin') {
            return true;
        }
        // Discretionary custom allowance granted directly by Super Admin
        if (!empty($user['custom_permissions']) && is_array($user['custom_permissions'])) {
            if (in_array($permission, $user['custom_permissions'], true)) {
                return true;
            }
        }
        return RbacService::roleHasPermission($roleId, $permission);
    }

    /**
     * Check if the current user has any of the specified permissions.
     */
    public static function canAny(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (self::can($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the current user has a specific role or any role in the provided list.
     */
    public static function hasRole(string|array $roleId): bool
    {
        $user = self::getCurrentUser();
        if (!$user) {
            return false;
        }
        $userRole = $user['role'] ?? '';
        if (is_array($roleId)) {
            return in_array($userRole, $roleId, true);
        }
        return $userRole === $roleId;
    }

    public static function hasAnyRole(array $roleIds): bool
    {
        return self::hasRole($roleIds);
    }

    /**
     * Check if current user is Super Admin.
     */
    public static function isSuperAdmin(): bool
    {
        return self::hasRole('super_admin');
    }

    /**
     * Enforces Maker-Checker rule:
     * Returns true if current user is a valid Checker (different from the Maker who created the entry).
     */
    public static function isEligibleChecker(string $makerUserId): bool
    {
        $currentUser = self::getCurrentUser();
        if (!$currentUser) {
            return false;
        }
        // Maker cannot approve their own entry
        return $currentUser['id'] !== $makerUserId;
    }

    /**
     * Check if the authenticated admin must change their initial one-time password (OTP).
     */
    public static function mustChangePassword(): bool
    {
        Session::start();
        if (Session::get('admin_must_change_password') === true) {
            return true;
        }
        $user = self::getCurrentUser();
        return !empty($user['must_change_password']);
    }

    /**
     * Establish unique personal password and clear temporary credential constraint.
     */
    public static function completeInitialPasswordChange(string $userId, string $currentPassword, string $newPassword): array
    {
        $result = RbacService::setAdminUniquePassword($userId, $currentPassword, $newPassword);
        if ($result['success'] ?? false) {
            Session::start();
            Session::remove('admin_must_change_password');
            Session::remove('admin_pre_auth');
            Session::set('admin_auth_version', $result['auth_version'] ?? 2);
            Session::regenerate(true);
        }
        return $result;
    }

    /**
     * Check if session is in restricted first-login quarantine state.
     */
    public static function isQuarantined(): bool
    {
        Session::start();
        return Session::get('admin_pre_auth') === true || self::mustChangePassword();
    }
}