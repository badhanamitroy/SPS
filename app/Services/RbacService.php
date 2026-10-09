<?php

declare(strict_types=1);

namespace App\Services;

class RbacService
{
    private static string $storagePath = '';
    private static ?array $cachedData = null;

    private static function getStoragePath(): string
    {
        if (empty(self::$storagePath)) {
            self::$storagePath = dirname(__DIR__, 2) . '/storage/data/rbac.json';
        }
        return self::$storagePath;
    }

    private static function loadData(): array
    {
        if (self::$cachedData !== null) {
            return self::$cachedData;
        }

        $path = self::getStoragePath();
        if (!file_exists($path)) {
            return [
                'roles' => [],
                'permissions' => [],
                'role_permissions' => [],
                'users' => []
            ];
        }

        $data = json_decode((string)file_get_contents($path), true);
        self::$cachedData = is_array($data) ? $data : [];
        return self::$cachedData;
    }

    private static function saveData(array $data): void
    {
        self::$cachedData = $data;
        $path = self::getStoragePath();
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Retrieve all available roles.
     */
    public static function getRoles(): array
    {
        $data = self::loadData();
        $roles = [];
        foreach ($data['roles'] ?? [] as $role) {
            $roles[$role['id']] = $role;
        }
        return $roles;
    }

    /**
     * Get specific role details by ID.
     */
    public static function getRole(string $roleId): ?array
    {
        $roles = self::getRoles();
        return $roles[$roleId] ?? null;
    }

    /**
     * Retrieve all available permissions.
     */
    public static function getPermissions(): array
    {
        $data = self::loadData();
        return $data['permissions'] ?? [];
    }

    /**
     * Group permissions by their parent resource module.
     */
    public static function getPermissionsGrouped(): array
    {
        $permissions = self::getPermissions();
        $grouped = [];
        foreach ($permissions as $perm) {
            $resource = $perm['resource'] ?? 'general';
            $grouped[$resource][] = $perm;
        }
        return $grouped;
    }

    /**
     * Get list of permission IDs for a specific role.
     */
    public static function getRolePermissions(string $roleId): array
    {
        $data = self::loadData();
        // Super Admin has inherently full access to all permissions
        if ($roleId === 'super_admin') {
            return array_column($data['permissions'] ?? [], 'id');
        }
        return $data['role_permissions'][$roleId] ?? [];
    }

    /**
     * Check if a role possesses a specific permission string.
     */
    public static function roleHasPermission(string $roleId, string $permission): bool
    {
        if ($roleId === 'super_admin') {
            return true;
        }
        $perms = self::getRolePermissions($roleId);
        return in_array($permission, $perms, true);
    }

    /**
     * Retrieve all admin users.
     */
    public static function getUsers(): array
    {
        $data = self::loadData();
        $users = [];
        foreach ($data['users'] ?? [] as $user) {
            $users[$user['id']] = $user;
        }
        return $users;
    }

    /**
     * Get a specific user by ID.
     */
    public static function getUser(string $userId): ?array
    {
        $users = self::getUsers();
        return $users[$userId] ?? null;
    }

    /**
     * Get effective permissions for a specific user (role permissions + custom assigned allowances).
     */
    public static function getUserEffectivePermissions(string $userId): array
    {
        $user = self::getUser($userId);
        if (!$user) {
            return [];
        }
        $roleId = $user['role'] ?? 'guest';
        $rolePerms = self::getRolePermissions($roleId);
        $customPerms = $user['custom_permissions'] ?? [];
        if (!is_array($customPerms)) {
            $customPerms = [];
        }
        return array_values(array_unique(array_merge($rolePerms, $customPerms)));
    }

    /**
     * Assign a new role to a target user (backwards compatible wrapper).
     */
    public static function assignRole(string $targetUserId, string $newRoleId, string $performedByUserId): array
    {
        $targetUser = self::getUser($targetUserId);
        $scope = $targetUser['scope'] ?? '';
        $customPerms = $targetUser['custom_permissions'] ?? [];
        return self::assignUserRoleAndAllowances($targetUserId, $newRoleId, $scope, $customPerms, $performedByUserId);
    }

    /**
     * Assign Role, Tasks/Scope, and Custom Allowances to a user.
     * Controlled by Super Administrator with audit logging.
     *
     * Security Guards:
     * 1. Performer cannot be an unauthorized user.
     * 2. Performer cannot assign 'super_admin' unless they themselves are 'super_admin'.
     * 3. Performer cannot assign a role with level >= their own level.
     * 4. Performer cannot escalate or change their own role.
     * 5. Action is immutably logged to AuditService.
     */
    public static function assignUserRoleAndAllowances(
        string $targetUserId,
        string $newRoleId,
        string $newScope,
        array $customPermissions,
        string $performedByUserId
    ): array {
        $data = self::loadData();
        $roles = self::getRoles();

        if (!isset($roles[$newRoleId])) {
            return ['success' => false, 'message' => 'Invalid role specified.'];
        }

        $performer = self::getUser($performedByUserId);
        if (!$performer) {
            return ['success' => false, 'message' => 'Action performer not found.'];
        }

        $targetUser = self::getUser($targetUserId);
        if (!$targetUser) {
            return ['success' => false, 'message' => 'Target user not found.'];
        }

        $performerRole = $roles[$performer['role']] ?? null;
        $targetNewRole = $roles[$newRoleId] ?? null;

        if (!$performerRole || !$targetNewRole) {
            return ['success' => false, 'message' => 'Role resolution error.'];
        }

        // Rule 1: Cannot change your own role
        if ($targetUserId === $performedByUserId && $targetUser['role'] !== $newRoleId) {
            AuditService::log(
                'security.escalation_attempt',
                'users',
                $targetUserId,
                $targetUser['name_bn'] ?? $targetUser['name_en'],
                ['role' => $targetUser['role']],
                ['attempted_role' => $newRoleId],
                'User attempted to alter their own role. Blocked by security policy.',
                $performer
            );
            return ['success' => false, 'message' => 'Security Violation: Administrators cannot alter their own role.'];
        }

        // Rule 2: Non-SuperAdmin cannot create or assign SuperAdmin
        if ($newRoleId === 'super_admin' && $performer['role'] !== 'super_admin') {
            AuditService::log(
                'security.superadmin_escalation_attempt',
                'users',
                $targetUserId,
                $targetUser['name_bn'] ?? $targetUser['name_en'],
                ['role' => $targetUser['role']],
                ['attempted_role' => $newRoleId],
                'Non-SuperAdmin attempted to grant Super Administrator privileges. Blocked by security policy.',
                $performer
            );
            return ['success' => false, 'message' => 'Security Violation: Only Super Administrators can grant Super Administrator role.'];
        }

        // Rule 3: Performer cannot assign a role equal to or higher in authority level than their own
        if ($performer['role'] !== 'super_admin' && ($targetNewRole['level'] >= $performerRole['level'])) {
            return ['success' => false, 'message' => 'Security Violation: You cannot assign a role of equal or higher authority level than your own.'];
        }

        // Sanitize custom permissions against valid permissions list
        $allPermissions = array_column(self::getPermissions(), 'id');
        $validCustomPerms = array_values(array_intersect($customPermissions, $allPermissions));

        $oldRole = $targetUser['role'];
        $oldScope = $targetUser['scope'] ?? '';
        $oldCustom = $targetUser['custom_permissions'] ?? [];

        // Apply update to data
        foreach ($data['users'] as &$u) {
            if ($u['id'] === $targetUserId) {
                $u['role'] = $newRoleId;
                $u['scope'] = trim($newScope);
                $u['custom_permissions'] = $validCustomPerms;
                $u['assigned_by'] = $performedByUserId;
                $u['updated_at'] = date('Y-m-d H:i:s');
                break;
            }
        }
        unset($u);

        self::saveData($data);

        // Audit the change
        AuditService::log(
            'role.assign_allowance',
            'users',
            $targetUserId,
            $targetUser['name_bn'] ?? $targetUser['name_en'],
            [
                'role' => $oldRole,
                'scope' => $oldScope,
                'custom_permissions' => $oldCustom,
            ],
            [
                'role' => $newRoleId,
                'scope' => $newScope,
                'custom_permissions' => $validCustomPerms,
            ],
            sprintf(
                "Super Admin %s updated role to '%s', scope to '%s', with %d custom allowances for %s",
                $performer['name_en'],
                $targetNewRole['name_en'],
                $newScope,
                count($validCustomPerms),
                $targetUser['name_en']
            ),
            $performer
        );

        return [
            'success' => true,
            'message' => sprintf(
                "%s-এর ভূমিকা '%s', দায়িত্ব পরিধি ও কাজের অনুমতি সফলভাবে হালনাগাদ করা হয়েছে।",
                $targetUser['name_bn'] ?? $targetUser['name_en'],
                $targetNewRole['name_bn'] ?? $targetNewRole['name_en']
            )
        ];
    }

    /**
     * Update Admin User Profile details (Self-Service or Super Admin).
     * Allows updating: name_bn, name_en, email, phone, bio, designation_bn, designation_en, password.
     * Strictly preserves: id, role, adminship, scope, status, permissions (no privilege self-escalation).
     */
    public static function updateUserProfile(string $userId, array $profileData): array
    {
        $data = self::loadData();
        $foundIndex = -1;

        foreach ($data['users'] as $idx => $u) {
            if ($u['id'] === $userId) {
                $foundIndex = $idx;
                break;
            }
        }

        if ($foundIndex === -1) {
            return ['success' => false, 'message' => 'অ্যাডমিন ইউজার খুঁজে পাওয়া যায়নি।'];
        }

        $current = $data['users'][$foundIndex];

        if (isset($profileData['name_bn']) && trim((string)$profileData['name_bn']) !== '') {
            $current['name_bn'] = trim((string)$profileData['name_bn']);
        }
        if (isset($profileData['name_en']) && trim((string)$profileData['name_en']) !== '') {
            $current['name_en'] = trim((string)$profileData['name_en']);
        }
        if (isset($profileData['email']) && trim((string)$profileData['email']) !== '') {
            $current['email'] = trim((string)$profileData['email']);
        }
        if (isset($profileData['phone'])) {
            $current['phone'] = trim((string)$profileData['phone']);
        }
        if (isset($profileData['bio'])) {
            $current['bio'] = trim((string)$profileData['bio']);
        }
        if (isset($profileData['designation_bn']) && trim((string)$profileData['designation_bn']) !== '') {
            $current['designation_bn'] = trim((string)$profileData['designation_bn']);
        }
        if (isset($profileData['designation_en']) && trim((string)$profileData['designation_en']) !== '') {
            $current['designation_en'] = trim((string)$profileData['designation_en']);
        }
        if (!empty($profileData['password'])) {
            $newPwd = (string)$profileData['password'];
            $policyRes = \App\Core\PasswordPolicy::validate($newPwd, $current);
            if (!$policyRes['valid']) {
                return ['success' => false, 'message' => $policyRes['error_bn'] ?? $policyRes['error']];
            }
            $current['password_hash'] = \App\Core\CryptoService::hashPassword($newPwd);
            $current['auth_version'] = ($current['auth_version'] ?? 1) + 1;
            $current['password_changed_at'] = date('Y-m-d H:i:s');
        }

        $current['updated_at'] = date('Y-m-d H:i:s');
        $data['users'][$foundIndex] = $current;
        self::saveData($data);

        AuditService::log(
            'user.profile_updated',
            'security',
            $userId,
            $current['name_en'] ?? $current['name_bn'],
            [],
            [
                'user_id' => $userId,
                'updated_fields' => array_keys($profileData)
            ],
            "Admin user {$userId} ({$current['name_en']}) updated their account profile details."
        );

        return ['success' => true, 'user' => $current];
    }

    /**
     * Directly update a user's password hash in storage (used for transparent Argon2id auto-migration).
     */
    public static function updatePasswordHashDirect(string $userId, string $newHash): bool
    {
        $data = self::loadData();
        foreach ($data['users'] as &$u) {
            if ($u['id'] === $userId) {
                $u['password_hash'] = $newHash;
                $u['updated_at'] = date('Y-m-d H:i:s');
                self::saveData($data);
                return true;
            }
        }
        return false;
    }

    /**
     * Generate a cryptographically secure, high-entropy One-Time Temporary Password for admin onboarding/reset.
     * Guaranteed minimum 48 bits of cryptographic entropy.
     */
    public static function generateTemporaryPassword(): string
    {
        $p1 = strtoupper(bin2hex(random_bytes(2)));
        $p2 = strtoupper(bin2hex(random_bytes(2)));
        $p3 = strtoupper(bin2hex(random_bytes(2)));
        return "SPS-OTP-{$p1}-{$p2}-{$p3}";
    }

    /**
     * Backwards-compatible alias for existing test suites.
     */
    public static function generateOtp(): string
    {
        return self::generateTemporaryPassword();
    }

    /**
     * Create a new administrative user with a temporary credential.
     * The admin MUST change this password upon first login to establish their unique personal password.
     * SECURITY INVARIANT: The temporary password is NEVER stored in plaintext in the database.
     */
    public static function createAdminUserWithOtp(array $userData, string $creatorId): array
    {
        $data = self::loadData();
        $username = trim(strtolower((string)($userData['username'] ?? '')));
        $email = trim(strtolower((string)($userData['email'] ?? '')));

        if (empty($username)) {
            return ['success' => false, 'message' => 'ইউজারনেম আবশ্যক। (Username is required.)'];
        }

        // Check unique username and email
        foreach ($data['users'] as $u) {
            if (strtolower($u['username'] ?? '') === $username) {
                return ['success' => false, 'message' => 'এই ইউজারনেম ইতোমধ্যেই ব্যবহৃত হচ্ছে। (Username already exists.)'];
            }
            if (!empty($email) && strtolower($u['email'] ?? '') === $email) {
                return ['success' => false, 'message' => 'এই ইমেইল দিয়ে ইতোমধ্যেই অ্যাডমিন অ্যাকাউন্ট রয়েছে। (Email already in use.)'];
            }
        }

        $userId = 'usr_' . preg_replace('/[^a-z0-9]/', '', $username);
        if (self::getUser($userId)) {
            $userId .= '_' . substr(uniqid(), 0, 4);
        }

        $tempPassword = self::generateTemporaryPassword();
        $now = date('Y-m-d H:i:s');
        $expiresAt = time() + 86400; // 24-hour expiration window

        $newUser = [
            'id' => $userId,
            'name_bn' => trim((string)($userData['name_bn'] ?? $userData['name_en'] ?? $username)),
            'name_en' => trim((string)($userData['name_en'] ?? $userData['name_bn'] ?? $username)),
            'designation_bn' => trim((string)($userData['designation_bn'] ?? 'প্রশাসনিক কর্মকর্তা')),
            'designation_en' => trim((string)($userData['designation_en'] ?? 'Administrative Officer')),
            'email' => $email,
            'phone' => trim((string)($userData['phone'] ?? '')),
            'avatar' => trim((string)($userData['avatar'] ?? 'assets/images/executives/joy-chakraborty.png')),
            'role' => (string)($userData['role'] ?? 'moderator'),
            'adminship' => 'Admin Officer',
            'scope' => trim((string)($userData['scope'] ?? 'General Administration')),
            'status' => 'active',
            'assigned_by' => $creatorId,
            'created_at' => $now,
            'username' => $username,
            'password_hash' => \App\Core\CryptoService::hashPassword($tempPassword),
            'must_change_password' => true,
            'is_otp' => true,
            'temporary_credential_expires_at' => $expiresAt,
            'auth_version' => 1,
            'otp_created_at' => $now,
            'bio' => trim((string)($userData['bio'] ?? 'এসপিএস পরিচালনা পর্ষদ কর্তৃক নিযুক্ত কর্মকর্তা।')),
            'updated_at' => $now,
        ];

        $data['users'][] = $newUser;
        self::saveData($data);

        AuditService::log(
            'auth.temporary_password_created',
            'security',
            $creatorId,
            'Super Admin',
            [],
            ['target_user_id' => $userId, 'role' => $newUser['role']],
            "New admin user '{$newUser['name_en']}' created with secure temporary credential by Super Admin."
        );

        return [
            'success' => true,
            'user' => $newUser,
            'otp' => $tempPassword,
            'message' => "নতুন কর্মকর্তা সফলভাবে যুক্ত হয়েছেন! সাময়িক পাসওয়ার্ড (Temporary Password): {$tempPassword}"
        ];
    }

    /**
     * Reset an admin officer's credentials with a fresh temporary password.
     */
    public static function resetAdminOtp(string $userId, string $performerId): array
    {
        $data = self::loadData();
        $foundIndex = -1;

        foreach ($data['users'] as $idx => $u) {
            if ($u['id'] === $userId) {
                $foundIndex = $idx;
                break;
            }
        }

        if ($foundIndex === -1) {
            return ['success' => false, 'message' => 'কর্মকর্তা অ্যাকাউন্ট খুঁজে পাওয়া যায়নি। (User not found.)'];
        }

        $tempPassword = self::generateTemporaryPassword();
        $now = date('Y-m-d H:i:s');
        $expiresAt = time() + 86400;

        $data['users'][$foundIndex]['password_hash'] = \App\Core\CryptoService::hashPassword($tempPassword);
        $data['users'][$foundIndex]['must_change_password'] = true;
        $data['users'][$foundIndex]['is_otp'] = true;
        $data['users'][$foundIndex]['temporary_credential_expires_at'] = $expiresAt;
        $data['users'][$foundIndex]['auth_version'] = ($data['users'][$foundIndex]['auth_version'] ?? 1) + 1; // Invalidate old sessions
        unset($data['users'][$foundIndex]['initial_otp']); // Strictly ensure no plaintext password in DB
        $data['users'][$foundIndex]['otp_created_at'] = $now;
        $data['users'][$foundIndex]['updated_at'] = $now;

        self::saveData($data);

        AuditService::log(
            'auth.admin_password_reset',
            'security',
            $performerId,
            'Super Admin',
            [],
            ['target_user_id' => $userId],
            "Admin user {$userId} password was reset with a new temporary credential by Super Admin."
        );

        return [
            'success' => true,
            'otp' => $tempPassword,
            'message' => "সাময়িক পাসওয়ার্ড (Temporary Password) সফলভাবে তৈরি হয়েছে: {$tempPassword}"
        ];
    }

    /**
     * Set admin's personal unique password upon initial login (or temporary credential reset), clearing temporary status.
     */
    public static function setAdminUniquePassword(string $userId, string $currentPassword, string $newPassword): array
    {
        $data = self::loadData();
        $foundIndex = -1;

        foreach ($data['users'] as $idx => $u) {
            if ($u['id'] === $userId) {
                $foundIndex = $idx;
                break;
            }
        }

        if ($foundIndex === -1) {
            return ['success' => false, 'message' => 'অ্যাডমিন অ্যাকাউন্ট খুঁজে পাওয়া যায়নি। (Admin user not found.)'];
        }

        $user = $data['users'][$foundIndex];

        // Check if temporary credential has expired
        if (!empty($user['temporary_credential_expires_at']) && time() > (int)$user['temporary_credential_expires_at']) {
            return ['success' => false, 'message' => 'সাময়িক পাসওয়ার্ডটির মেয়াদ শেষ হয়ে গেছে। সুপার অ্যাডমিনের সাথে যোগাযোগ করুন। (Temporary password has expired.)'];
        }

        // Verify current password strictly against stored Argon2id/Bcrypt hash
        $hash = $user['password_hash'] ?? '';
        if (empty($hash) || !\App\Core\CryptoService::verifyPassword($currentPassword, $hash)) {
            return ['success' => false, 'message' => 'বর্তমান সাময়িক পাসওয়ার্ডটি সঠিক নয়। (Current temporary password is incorrect.)'];
        }

        // Enforce enterprise password policy
        $policyRes = \App\Core\PasswordPolicy::validate($newPassword, $user);
        if (!$policyRes['valid']) {
            return ['success' => false, 'message' => $policyRes['error_bn'] ?? $policyRes['error']];
        }

        if ($newPassword === $currentPassword) {
            return ['success' => false, 'message' => 'নতুন পাসওয়ার্ডটি সাময়িক পাসওয়ার্ড থেকে ভিন্ন ও স্বতন্ত্র হতে হবে। (New password must differ from temporary password.)'];
        }

        $now = date('Y-m-d H:i:s');
        $data['users'][$foundIndex]['password_hash'] = \App\Core\CryptoService::hashPassword($newPassword);
        $data['users'][$foundIndex]['must_change_password'] = false;
        $data['users'][$foundIndex]['is_otp'] = false;
        $data['users'][$foundIndex]['auth_version'] = ($data['users'][$foundIndex]['auth_version'] ?? 1) + 1; // Invalidate any other concurrent sessions
        unset($data['users'][$foundIndex]['initial_otp']);
        unset($data['users'][$foundIndex]['temporary_credential_expires_at']);
        $data['users'][$foundIndex]['password_changed_at'] = $now;
        $data['users'][$foundIndex]['updated_at'] = $now;

        self::saveData($data);

        AuditService::log(
            'auth.temporary_password_consumed',
            'security',
            $userId,
            $user['name_en'] ?? $user['name_bn'],
            [],
            ['user_id' => $userId, 'time' => $now],
            "Admin '{$user['name_en']}' successfully replaced temporary credential and established their unique personal password."
        );

        if (!empty($user['email'])) {
            EmailService::sendPasswordChangedAlert($user['email'], $user['name_en'] ?? $user['name_bn'], 'Administrator (Unique Password Established)');
        }

        return [
            'success' => true,
            'message' => 'আপনার নিজস্ব অনন্য পাসওয়ার্ড সফলভাবে নির্ধারিত হয়েছে! (Your unique personal password has been established.)',
            'auth_version' => $data['users'][$foundIndex]['auth_version']
        ];
    }
}