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
     * Assign a new role to a target user with strict role escalation protection.
     * 
     * Security Guards:
     * 1. Performer cannot be an unauthorized user.
     * 2. Performer cannot assign 'super_admin' unless they themselves are 'super_admin'.
     * 3. Performer cannot assign a role with level >= their own level.
     * 4. Performer cannot escalate or change their own role.
     * 5. Action is immutably logged to AuditService.
     */
    public static function assignRole(string $targetUserId, string $newRoleId, string $performedByUserId): array
    {
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
        if ($targetUserId === $performedByUserId) {
            AuditService::log(
                'security.escalation_attempt',
                'users',
                $targetUserId,
                $targetUser['name_bn'],
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
                $targetUser['name_bn'],
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

        $oldRole = $targetUser['role'];

        // Apply update
        foreach ($data['users'] as &$u) {
            if ($u['id'] === $targetUserId) {
                $u['role'] = $newRoleId;
                $u['assigned_by'] = $performedByUserId;
                $u['updated_at'] = date('Y-m-d H:i:s');
                break;
            }
        }
        unset($u);

        self::saveData($data);

        // Audit the change
        AuditService::log(
            'role.assign',
            'users',
            $targetUserId,
            $targetUser['name_bn'] ?? $targetUser['name_en'],
            ['role' => $oldRole],
            ['role' => $newRoleId],
            sprintf(
                "Role changed from %s to %s by %s",
                $roles[$oldRole]['name_en'] ?? $oldRole,
                $targetNewRole['name_en'],
                $performer['name_en']
            ),
            $performer
        );

        return [
            'success' => true,
            'message' => sprintf(
                "Role successfully changed to %s.",
                $targetNewRole['name_bn']
            )
        ];
    }
}
