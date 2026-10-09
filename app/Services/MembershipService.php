<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\CryptoService;

class MembershipService
{
    public const DEFAULT_ORGANIZATION_DP = 'media/dp/Default-DP.png';
    public const STATUS_PENDING_APPROVAL = 'Pending Finance Approval';
    public const STATUS_ACTIVE = 'Active';
    public const STATUS_LIFETIME_ACTIVE = 'Lifetime Active';
    public const STATUS_REJECTED = 'Rejected';
    public const STATUS_SUSPENDED = 'Suspended';

    private static string $storagePath = '';
    private static ?array $cachedData = null;

    /**
     * Decrypt confidential member fields transparently for application consumption.
     */
    public static function decryptMember(array $m): array
    {
        if (!empty($m['phone']) && CryptoService::isEncrypted((string)$m['phone'])) {
            $m['phone'] = CryptoService::decrypt((string)$m['phone']);
        }
        if (!empty($m['address']) && CryptoService::isEncrypted((string)$m['address'])) {
            $m['address'] = CryptoService::decrypt((string)$m['address']);
        }
        return $m;
    }

    /**
     * Encrypt confidential member fields and generate blind indexes for storage.
     */
    public static function prepareMemberForStorage(array $m): array
    {
        if (isset($m['phone']) && $m['phone'] !== '') {
            $rawPhone = CryptoService::isEncrypted((string)$m['phone'])
                ? CryptoService::decrypt((string)$m['phone'])
                : (string)$m['phone'];
            $cleanDigits = preg_replace('/[^\d]/', '', $rawPhone);
            $m['phone_bidx'] = CryptoService::blindIndex(CryptoService::normalizeSearchTerm($rawPhone));
            if (strlen($cleanDigits) >= 10) {
                $m['phone_last10_bidx'] = CryptoService::blindIndex(substr($cleanDigits, -10));
            }
            $m['phone'] = CryptoService::encrypt($rawPhone);
        }

        if (isset($m['address']) && $m['address'] !== '') {
            $rawAddress = CryptoService::isEncrypted((string)$m['address'])
                ? CryptoService::decrypt((string)$m['address'])
                : (string)$m['address'];
            $m['address'] = CryptoService::encrypt($rawAddress);
        }

        return $m;
    }

    /**
     * Decrypt confidential payment fields transparently.
     */
    public static function decryptPayment(array $p): array
    {
        if (!empty($p['trx_id']) && CryptoService::isEncrypted((string)$p['trx_id'])) {
            $p['trx_id'] = CryptoService::decrypt((string)$p['trx_id']);
        }
        if (!empty($p['sender_number']) && CryptoService::isEncrypted((string)$p['sender_number'])) {
            $p['sender_number'] = CryptoService::decrypt((string)$p['sender_number']);
        }
        return $p;
    }

    /**
     * Encrypt confidential payment fields and generate blind indexes for storage.
     */
    public static function preparePaymentForStorage(array $p): array
    {
        if (isset($p['trx_id']) && $p['trx_id'] !== '') {
            $rawTrx = CryptoService::isEncrypted((string)$p['trx_id'])
                ? CryptoService::decrypt((string)$p['trx_id'])
                : (string)$p['trx_id'];
            $p['trx_id_bidx'] = CryptoService::blindIndex(CryptoService::normalizeSearchTerm($rawTrx));
            $p['trx_id'] = CryptoService::encrypt($rawTrx);
        }

        if (isset($p['sender_number']) && $p['sender_number'] !== '') {
            $rawSender = CryptoService::isEncrypted((string)$p['sender_number'])
                ? CryptoService::decrypt((string)$p['sender_number'])
                : (string)$p['sender_number'];
            $p['sender_number'] = CryptoService::encrypt($rawSender);
        }

        return $p;
    }

    /**
     * Check if a transaction ID (TrxID) has already been recorded in payments/donations.
     * Uses keyed blind indexing to match against stored encrypted records.
     */
    public static function isDuplicateTrxId(string $trxId): bool
    {
        $raw = trim($trxId);
        if ($raw === '') {
            return false;
        }
        $normalized = CryptoService::normalizeSearchTerm($raw);
        $bidx = CryptoService::blindIndex($normalized);
        if (!$bidx) {
            return false;
        }

        $data = self::loadData();
        foreach ($data['payments'] ?? [] as $p) {
            if (!empty($p['trx_id_bidx']) && hash_equals($p['trx_id_bidx'], $bidx)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Transparently rehash a member's password to current Argon2id + Pepper standard.
     */
    public static function updateMemberPasswordHash(string $memberId, string $plaintextPassword): void
    {
        self::withExclusiveLock(function () use ($memberId, $plaintextPassword) {
            $data = self::loadData();
            foreach ($data['members'] as $idx => $m) {
                if (($m['id'] ?? '') === $memberId || ($m['member_code'] ?? '') === $memberId) {
                    $data['members'][$idx]['password_hash'] = CryptoService::hashPassword($plaintextPassword);
                    self::saveData($data);
                    break;
                }
            }
        });
    }

    /**
     * Admin-privileged password reset for a member (no current password required).
     * Intended for Super Admin / Admin forcing a member's credential update.
     * Enforces PasswordPolicy, increments auth_version, and writes audit log.
     */
    public static function adminResetMemberPassword(string $memberIdOrCode, string $newPassword): array
    {
        return self::withExclusiveLock(function () use ($memberIdOrCode, $newPassword) {
            $data = self::loadData();
            $foundIndex = -1;

            foreach ($data['members'] as $idx => $m) {
                if (strcasecmp((string)($m['member_code'] ?? ''), $memberIdOrCode) === 0 || ($m['id'] ?? '') === $memberIdOrCode) {
                    $foundIndex = $idx;
                    break;
                }
            }

            if ($foundIndex === -1) {
                return ['success' => false, 'message' => 'সদস্য খুঁজে পাওয়া যায়নি। (Member record not found.)'];
            }

            $current = $data['members'][$foundIndex];

            // Enforce enterprise password policy
            $policyRes = \App\Core\PasswordPolicy::validate($newPassword, $current);
            if (!$policyRes['valid']) {
                return ['success' => false, 'message' => $policyRes['error_bn'] ?? $policyRes['error']];
            }

            $now = date('Y-m-d H:i:s');
            $current['password_hash'] = CryptoService::hashPassword($newPassword);
            $current['auth_version'] = ($current['auth_version'] ?? 1) + 1;
            $current['password_changed_at'] = $now;
            $current['updated_at'] = $now;
            $data['members'][$foundIndex] = $current;
            self::saveData($data);

            AuditService::log(
                'membership.password_admin_reset',
                'security',
                $current['id'],
                $current['name_en'] ?? $current['name_bn'],
                [],
                ['member_code' => $current['member_code'], 'time' => $now],
                "Admin reset password for member {$current['member_code']}."
            );

            return [
                'success' => true,
                'message' => 'সদস্যের পাসওয়ার্ড সফলভাবে পুনর্নির্ধারণ করা হয়েছে। (Member password successfully reset.)',
                'member' => $current,
                'auth_version' => $current['auth_version']
            ];
        });
    }

    /**
     * Resolve Member Profile Picture (DP) strictly:
     * member_uploaded_dp -> organization_default_dp
     * NEVER falls back to another member's avatar or broken placeholder.
     */
    public static function getMemberAvatar(array|string|null $memberOrAvatar): string
    {
        $avatar = is_array($memberOrAvatar) ? ($memberOrAvatar['avatar'] ?? '') : (string)$memberOrAvatar;
        $avatar = trim((string)$avatar);

        if (empty($avatar) || $avatar === 'null') {
            return self::DEFAULT_ORGANIZATION_DP;
        }

        // Filter out legacy dicebear urls or hardcoded sample 872
        if (str_contains($avatar, 'dicebear.com') || str_contains($avatar, 'member_SPS_000872.jpg')) {
            return self::DEFAULT_ORGANIZATION_DP;
        }

        // Check if file exists in public/ or root
        $clean = ltrim($avatar, '/');
        $pubPath = dirname(__DIR__, 2) . '/public/' . $clean;
        $rootPath = dirname(__DIR__, 2) . '/' . $clean;
        if (file_exists($pubPath) || file_exists($rootPath)) {
            return $clean;
        }

        return self::DEFAULT_ORGANIZATION_DP;
    }

    /**
     * Check if a membership status represents a pending finance verification state.
     */
    public static function isPendingStatus(string $status): bool
    {
        $s = strtolower(trim($status));
        return in_array($s, ['pending', 'pending finance approval', 'pending_finance_approval', 'under_verification', 'under verification']);
    }

    /**
     * Check if a membership status is active.
     */
    public static function isActiveStatus(string $status): bool
    {
        $s = strtolower(trim($status));
        return in_array($s, ['active', 'lifetime active', 'approved', 'finance_approved', 'finance approved']);
    }

    public static function getStoragePath(): string
    {
        if (empty(self::$storagePath)) {
            self::$storagePath = dirname(__DIR__, 2) . '/storage/data/membership.json';
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
                'categories' => [],
                'plans' => [],
                'members' => [],
                'payments' => [],
                'history' => [],
                'volunteers' => [],
            ];
        }

        $json = file_get_contents($path);
        $data = json_decode((string)$json, true);
        self::$cachedData = is_array($data) ? $data : [];
        return self::$cachedData;
    }

    /** @var resource|null */
    private static $lockFp = null;
    private static int $lockDepth = 0;

    /**
     * Perform an operation holding an exclusive advisory lock on the storage lock file.
     * Guarantees the entire read-modify-write sequence is mutually exclusive.
     */
    public static function withExclusiveLock(callable $operation): mixed
    {
        if (self::$lockDepth === 0) {
            $lockPath = dirname(self::getStoragePath()) . '/.membership.lock';
            self::$lockFp = @fopen($lockPath, 'c+');
            if (self::$lockFp) {
                @flock(self::$lockFp, LOCK_EX);
            }
            self::$cachedData = null; // Always fresh read from disk under lock
        }
        self::$lockDepth++;

        try {
            return $operation();
        } finally {
            self::$lockDepth--;
            if (self::$lockDepth === 0) {
                if (self::$lockFp) {
                    @flock(self::$lockFp, LOCK_UN);
                    @fclose(self::$lockFp);
                    self::$lockFp = null;
                }
            }
        }
    }

    /**
     * Atomically write file contents using temporary file and atomic rename.
     */
    public static function atomicWrite(string $path, string $content): bool
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $tmp = $path . '.tmp_' . bin2hex(random_bytes(6));
        if (file_put_contents($tmp, $content) === false) {
            return false;
        }

        if (@rename($tmp, $path)) {
            return true;
        }

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && file_exists($path)) {
            @unlink($path);
            if (@rename($tmp, $path)) {
                return true;
            }
        }

        @unlink($tmp);
        return false;
    }

    private static function saveData(array $data): void
    {
        self::$cachedData = $data;
        $path = self::getStoragePath();
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        self::atomicWrite($path, $json);
    }

    public static function clearCache(): void
    {
        self::$cachedData = null;
    }

    /**
     * Remove a member record by internal ID or Member Code (for test isolation/cleanup).
     */
    public static function deleteMember(string $idOrCode): bool
    {
        return self::withExclusiveLock(function () use ($idOrCode) {
            $data = self::loadData();
            $members = $data['members'] ?? [];
            $initialCount = count($members);
            $data['members'] = array_values(array_filter($members, function ($m) use ($idOrCode) {
                return ($m['id'] ?? '') !== $idOrCode && strcasecmp((string)($m['member_code'] ?? ''), $idOrCode) !== 0;
            }));

            if (count($data['members']) < $initialCount) {
                self::saveData($data);
                return true;
            }
            return false;
        });
    }

    /**
     * Get all membership categories (Student, Earning).
     */
    public static function getCategories(): array
    {
        $data = self::loadData();
        return $data['categories'] ?? [];
    }

    /**
     * Get single category by ID.
     */
    public static function getCategory(string $id): ?array
    {
        $categories = self::getCategories();
        foreach ($categories as $cat) {
            if ($cat['id'] === $id) {
                return $cat;
            }
        }
        return null;
    }

    /**
     * Get all defined membership plans.
     */
    public static function getPlans(): array
    {
        $data = self::loadData();
        return $data['plans'] ?? [];
    }

    /**
     * Get single plan by ID.
     */
    public static function getPlan(string $id): ?array
    {
        $plans = self::getPlans();
        foreach ($plans as $plan) {
            if ($plan['id'] === $id) {
                return $plan;
            }
        }
        return null;
    }

    /**
     * Get plans applicable to a specific category.
     */
    public static function getPlansForCategory(string $categoryId): array
    {
        $plans = self::getPlans();
        return array_values(array_filter($plans, function ($plan) use ($categoryId) {
            return ($plan['category_id'] === $categoryId || $plan['category_id'] === 'ALL') && !empty($plan['active']);
        }));
    }

    /**
     * Query all members with flexible filtering.
     */
    public static function getAllMembers(
        ?string $status = null,
        ?string $categoryId = null,
        ?string $planId = null,
        ?string $search = null
    ): array {
        $data = self::loadData();
        $members = $data['members'] ?? [];
        $decryptedMembers = array_map([self::class, 'decryptMember'], $members);

        if ($status !== null && $status !== '' && $status !== 'all') {
            $decryptedMembers = array_filter($decryptedMembers, function ($m) use ($status) {
                return strcasecmp($m['status'] ?? '', $status) === 0;
            });
        }

        if ($categoryId !== null && $categoryId !== '' && $categoryId !== 'all') {
            $decryptedMembers = array_filter($decryptedMembers, function ($m) use ($categoryId) {
                return ($m['category_id'] ?? '') === $categoryId;
            });
        }

        if ($planId !== null && $planId !== '' && $planId !== 'all') {
            $decryptedMembers = array_filter($decryptedMembers, function ($m) use ($planId) {
                return ($m['plan_id'] ?? '') === $planId;
            });
        }

        if ($search !== null && trim($search) !== '') {
            $q = mb_strtolower(trim($search));
            $decryptedMembers = array_filter($decryptedMembers, function ($m) use ($q) {
                return str_contains(mb_strtolower($m['name_bn'] ?? ''), $q) ||
                       str_contains(mb_strtolower($m['name_en'] ?? ''), $q) ||
                       str_contains(mb_strtolower($m['member_code'] ?? ''), $q) ||
                       str_contains(mb_strtolower($m['email'] ?? ''), $q) ||
                       str_contains(mb_strtolower($m['phone'] ?? ''), $q);
            });
        }

        return array_values($decryptedMembers);
    }

    /**
     * Find member by internal ID or Member Code (e.g. SPS-000872).
     */
    public static function getMemberById(string $idOrCode): ?array
    {
        $data = self::loadData();
        foreach ($data['members'] ?? [] as $m) {
            if ($m['id'] === $idOrCode || ($m['member_code'] ?? '') === $idOrCode) {
                return self::decryptMember($m);
            }
        }
        return null;
    }

    /**
     * Find member by Email address.
     */
    public static function getMemberByEmail(string $email): ?array
    {
        $data = self::loadData();
        $email = mb_strtolower(trim($email));
        foreach ($data['members'] ?? [] as $m) {
            if (mb_strtolower(trim($m['email'] ?? '')) === $email) {
                return self::decryptMember($m);
            }
        }
        return null;
    }

    /**
     * Smart Member Lookup for Login:
     * Accepts Member Code (e.g. SPS-000872 or 872), Email, or Mobile Number.
     * Uses Blind Indexing for encrypted phone lookup.
     */
    public static function findMemberForLogin(string $identifier): ?array
    {
        $identifier = trim($identifier);
        if ($identifier === '') {
            return null;
        }

        $data = self::loadData();
        $members = $data['members'] ?? [];

        // 1. Direct match by member_code (case-insensitive) or internal id
        foreach ($members as $m) {
            if (strcasecmp((string)($m['member_code'] ?? ''), $identifier) === 0 || ($m['id'] ?? '') === $identifier) {
                return self::decryptMember($m);
            }
        }

        // 2. If user entered just numeric digits like "872" or "000872", match "SPS-000872"
        $digitsOnly = preg_replace('/[^\d]/', '', $identifier);
        if ($digitsOnly !== '') {
            $normalizedCode = 'SPS-' . str_pad($digitsOnly, 6, '0', STR_PAD_LEFT);
            foreach ($members as $m) {
                if (strcasecmp((string)($m['member_code'] ?? ''), $normalizedCode) === 0) {
                    return self::decryptMember($m);
                }
            }
        }

        // 3. Match by email (case-insensitive)
        $cleanEmail = mb_strtolower($identifier);
        foreach ($members as $m) {
            if (mb_strtolower(trim($m['email'] ?? '')) === $cleanEmail) {
                return self::decryptMember($m);
            }
        }

        // 4. Match by phone number via Blind Index
        if (strlen($digitsOnly) >= 10) {
            $last10 = substr($digitsOnly, -10);
            $last10Bidx = CryptoService::blindIndex($last10);
            $fullBidx = CryptoService::blindIndex(CryptoService::normalizeSearchTerm($identifier));

            foreach ($members as $m) {
                if (!empty($m['phone_last10_bidx']) && hash_equals($m['phone_last10_bidx'], $last10Bidx)) {
                    return self::decryptMember($m);
                }
                if (!empty($m['phone_bidx']) && hash_equals($m['phone_bidx'], $fullBidx)) {
                    return self::decryptMember($m);
                }
                // Decrypt fallback
                $decrypted = self::decryptMember($m);
                $memberPhone = preg_replace('/[^\d]/', '', (string)($decrypted['phone'] ?? ''));
                if ($memberPhone && (str_ends_with($memberPhone, $last10) || str_ends_with($digitsOnly, substr($memberPhone, -10)))) {
                    return $decrypted;
                }
            }
        }

        return null;
    }

    /**
     * Update Member Profile details anytime (Self-Service or Admin).
     * Allows updating: name_bn, name_en, email, phone, district, upazila, address, blood_group, education/institution, profession/designation, bio/notes.
     * Strictly preserves: id, member_code, category_id, plan_id, status, join_date, expiry_date (immutable core identity).
     */
    public static function updateMemberProfile(string $memberIdentifier, array $profileData): array
    {
        return self::withExclusiveLock(function () use ($memberIdentifier, $profileData) {
            $data = self::loadData();
            $foundIndex = -1;

            foreach ($data['members'] as $idx => $m) {
                if (strcasecmp((string)($m['member_code'] ?? ''), $memberIdentifier) === 0 || ($m['id'] ?? '') === $memberIdentifier) {
                    $foundIndex = $idx;
                    break;
                }
            }

            if ($foundIndex === -1) {
                return ['success' => false, 'message' => 'সদস্য প্রোফাইল খুঁজে পাওয়া যায়নি।'];
            }

            $current = $data['members'][$foundIndex];

            if (isset($profileData['name_bn']) && trim((string)$profileData['name_bn']) !== '') {
                $current['name_bn'] = trim((string)$profileData['name_bn']);
            }
            if (isset($profileData['name_en']) && trim((string)$profileData['name_en']) !== '') {
                $current['name_en'] = trim((string)$profileData['name_en']);
            }
            if (isset($profileData['phone']) && trim((string)$profileData['phone']) !== '') {
                $current['phone'] = trim((string)$profileData['phone']);
            }
            if (isset($profileData['email']) && trim((string)$profileData['email']) !== '') {
                $current['email'] = trim((string)$profileData['email']);
            }
            if (isset($profileData['district'])) {
                $current['district'] = trim((string)$profileData['district']);
            }
            if (isset($profileData['upazila'])) {
                $current['upazila'] = trim((string)$profileData['upazila']);
            }
            if (isset($profileData['address'])) {
                $current['address'] = trim((string)$profileData['address']);
            }
            if (isset($profileData['blood_group'])) {
                $current['blood_group'] = trim((string)$profileData['blood_group']);
            }
            if (isset($profileData['avatar']) && trim((string)$profileData['avatar']) !== '') {
                $current['avatar'] = trim((string)$profileData['avatar']);
            }
            if (isset($profileData['bio'])) {
                $current['bio'] = trim((string)$profileData['bio']);
            }
            if (isset($profileData['notes'])) {
                $current['notes'] = trim((string)$profileData['notes']);
            }

            // Education details (for students or academic records)
            if (isset($profileData['institution']) || isset($profileData['department'])) {
                if (!isset($current['education']) || !is_array($current['education'])) {
                    $current['education'] = [];
                }
                if (isset($profileData['institution'])) {
                    $current['education']['institution'] = trim((string)$profileData['institution']);
                }
                if (isset($profileData['department'])) {
                    $current['department'] = trim((string)$profileData['department']);
                }
                if (isset($profileData['class_year'])) {
                    $current['education']['class_year'] = trim((string)$profileData['class_year']);
                }
            }

            // Profession details (for earning or general professional records)
            if (isset($profileData['profession_institution']) || isset($profileData['designation'])) {
                if (!isset($current['profession']) || !is_array($current['profession'])) {
                    $current['profession'] = [];
                }
                if (isset($profileData['profession_institution'])) {
                    $current['profession']['institution'] = trim((string)$profileData['profession_institution']);
                }
                if (isset($profileData['designation'])) {
                    $current['profession']['designation'] = trim((string)$profileData['designation']);
                }
            }

            $current['updated_at'] = date('Y-m-d H:i:s');
            $current = self::prepareMemberForStorage($current);
            $data['members'][$foundIndex] = $current;
            self::saveData($data);

            // Audit Logging
            AuditService::log(
                'member.profile_updated',
                'membership',
                $current['id'],
                $current['name_en'] ?? $current['name_bn'],
                [],
                [
                    'member_code' => $current['member_code'],
                    'updated_fields' => array_keys($profileData)
                ],
                "Member {$current['member_code']} ({$current['name_bn']}) updated their profile information."
            );

            return ['success' => true, 'member' => self::decryptMember($current)];
        });
    }

    /**
     * Verify a member's password with Argon2id, pepper, and transparent auto-rehashing.
     * Backdoors (sps@member2026, member123) are strictly removed.
     */
    public static function verifyMemberPassword(array $member, string $password): bool
    {
        $hash = $member['password_hash'] ?? '';
        if (empty($hash)) {
            return false;
        }

        $verified = CryptoService::verifyPassword($password, $hash);
        if ($verified && CryptoService::needsRehash($hash)) {
            self::updateMemberPasswordHash($member['id'], $password);
        }

        return $verified;
    }

    /**
     * Update member password with Argon2id hashing and optional current password verification.
     */
    public static function updateMemberPassword(string $memberIdOrCode, string $newPassword, ?string $currentPassword = null): array
    {
        return self::withExclusiveLock(function () use ($memberIdOrCode, $newPassword, $currentPassword) {
            $data = self::loadData();
            $foundIndex = -1;

            foreach ($data['members'] as $idx => $m) {
                if (strcasecmp((string)($m['member_code'] ?? ''), $memberIdOrCode) === 0 || ($m['id'] ?? '') === $memberIdOrCode) {
                    $foundIndex = $idx;
                    break;
                }
            }

            if ($foundIndex === -1) {
                return ['success' => false, 'message' => 'সদস্য খুঁজে পাওয়া যায়নি। (Member record not found.)'];
            }

            $current = $data['members'][$foundIndex];

            // Rate limit password change attempts
            $rateKey = 'pwdchange:member:' . ($current['member_code'] ?? $memberIdOrCode);
            if (\App\Core\RateLimiter::tooManyAttempts($rateKey, 5)) {
                return ['success' => false, 'message' => 'পাসওয়ার্ড পরিবর্তনের অতিরিক্ত ভুল প্রচেষ্টা। অনুগ্রহ করে ১৫ মিনিট পর চেষ্টা করুন। (Too many attempts. Please try again later.)'];
            }

            // If member already has a password, currentPassword is required and verified
            if (!empty($current['password_hash'])) {
                if ($currentPassword === null || $currentPassword === '') {
                    return ['success' => false, 'message' => 'পাসওয়ার্ড পরিবর্তনের জন্য বর্তমান পাসওয়ার্ড দেওয়া আবশ্যক। (Current password is required.)'];
                }
                if (!CryptoService::verifyPassword($currentPassword, $current['password_hash'])) {
                    \App\Core\RateLimiter::hit($rateKey, 900);
                    return ['success' => false, 'message' => 'বর্তমান পাসওয়ার্ডটি সঠিক নয়। (Current password is incorrect.)'];
                }
            }

            \App\Core\RateLimiter::resetAttempts($rateKey);

            // Enforce enterprise password policy (12+ characters, dictionary & identifier checks)
            $policyRes = \App\Core\PasswordPolicy::validate($newPassword, $current);
            if (!$policyRes['valid']) {
                return ['success' => false, 'message' => $policyRes['error_bn'] ?? $policyRes['error']];
            }

            if (!empty($currentPassword) && $newPassword === $currentPassword) {
                return ['success' => false, 'message' => 'নতুন পাসওয়ার্ডটি বর্তমান পাসওয়ার্ড থেকে ভিন্ন হতে হবে। (New password must differ from current password.)'];
            }

            $now = date('Y-m-d H:i:s');
            $current['password_hash'] = CryptoService::hashPassword($newPassword);
            $current['auth_version'] = ($current['auth_version'] ?? 1) + 1; // Increment auth_version to invalidate other sessions
            $current['password_changed_at'] = $now;
            $current['updated_at'] = $now;
            $data['members'][$foundIndex] = $current;
            self::saveData($data);

            // Send security alert email
            if (!empty($current['email'])) {
                EmailService::sendPasswordChangedAlert($current['email'], $current['name_en'] ?? $current['name_bn'], 'Member');
            }

            AuditService::log(
                'membership.password_changed',
                'security',
                $current['id'],
                $current['name_en'] ?? $current['name_bn'],
                [],
                ['member_code' => $current['member_code'], 'time' => $now],
                "Member {$current['member_code']} updated their account password."
            );

            return [
                'success' => true,
                'message' => 'পাসওয়ার্ড সফলভাবে সংরক্ষিত হয়েছে। (Password successfully updated.)',
                'member' => $current,
                'auth_version' => $current['auth_version']
            ];
        });
    }

    /**
     * Find or auto-provision a Member by verified Google profile.
     */
    public static function findOrCreateMemberByGoogle(array $profile): array
    {
        return self::withExclusiveLock(function () use ($profile) {
            $data = self::loadData();
            $googleId = trim((string)($profile['google_id'] ?? ''));
            $email = mb_strtolower(trim((string)($profile['email'] ?? '')));
            $name = trim((string)($profile['name'] ?? ''));
            $avatar = trim((string)($profile['avatar'] ?? ''));

            // 1. Search by existing google_id
            if ($googleId !== '') {
                foreach ($data['members'] as $idx => $m) {
                    if (($m['google_id'] ?? '') === $googleId) {
                        return ['member' => $m, 'is_new' => false];
                    }
                }
            }

            // 2. Search by verified email
            if ($email !== '') {
                foreach ($data['members'] as $idx => &$m) {
                    if (mb_strtolower(trim($m['email'] ?? '')) === $email) {
                        // Link google_id if not present
                        if (empty($m['google_id']) && $googleId !== '') {
                            $m['google_id'] = $googleId;
                            if (empty($m['avatar']) && $avatar !== '') {
                                $m['avatar'] = $avatar;
                            }
                            $m['updated_at'] = date('Y-m-d H:i:s');
                            $data['members'][$idx] = $m;
                            self::saveData($data);
                        }
                        return ['member' => $m, 'is_new' => false];
                    }
                }
                unset($m);
            }

            // 3. Member does not exist yet -> Auto-provision new member
            $nextCode = self::generateNextMemberCode();
            $newId = 'mem_' . substr(md5(uniqid('', true)), 0, 8);

            $newMember = [
                'id' => $newId,
                'member_code' => $nextCode,
                'google_id' => $googleId,
                'name_bn' => $name ?: 'সনাতনী সদস্য',
                'name_en' => $name ?: 'Sanatan Member',
                'email' => $email,
                'phone' => '+8801700000000',
                'avatar' => $avatar ?: 'assets/images/members/default-avatar.png',
                'category_id' => 'STUDENT',
                'plan_id' => 'YEARLY',
                'status' => 'Active',
                'password_hash' => CryptoService::hashPassword(bin2hex(random_bytes(16))),
                'join_date' => date('Y-m-d'),
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+1 year')),
                'is_lifetime' => false,
                'next_payment_date' => date('Y-m-d', strtotime('+1 year')),
                'entry_fee_paid' => 50,
                'total_paid' => 1050,
                'district' => 'ঢাকা',
                'upazila' => '',
                'address' => '',
                'education' => [
                    'institution' => 'SPS Academy',
                    'department' => '',
                    'class_year' => '',
                ],
                'profession' => [
                    'designation' => 'Member',
                    'institution' => '',
                ],
                'recognitions' => ['Google Authenticated Member'],
                'card_qr_token' => 'SPS-VERIFY-' . substr($nextCode, 4) . '-GOOGLE-' . date('Y'),
                'bio' => 'গুগল অ্যাকাউন্টের মাধ্যমে যুক্ত সম্মানিত সদস্য।',
                'two_factor_enabled' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $storedMember = self::prepareMemberForStorage($newMember);
            $data['members'][] = $storedMember;
            self::saveData($data);

            AuditService::log(
                'membership.google_registered',
                'security',
                $newId,
                $newMember['name_en'],
                [],
                ['email' => $email, 'google_id' => $googleId, 'member_code' => $nextCode],
                "New member registered via Google Authentication with Member ID {$nextCode} ({$email})"
            );

            return ['member' => self::decryptMember($storedMember), 'is_new' => true];
        });
    }

    /**
     * Generate unique lifetime Member ID (e.g. SPS-000873).
     * Retains numerical consistency even if category transitions later.
     */
    public static function generateNextMemberCode(): string
    {
        $data = self::loadData();
        $maxNum = 872;
        foreach ($data['members'] ?? [] as $m) {
            if (preg_match('/SPS-(\d+)/', $m['member_code'] ?? '', $matches)) {
                $num = (int)$matches[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }
        $next = $maxNum + 1;
        return sprintf('SPS-%06d', $next);
    }

    /**
     * Generate unique Transaction ID (e.g. SPS-MEM-2026-000193).
     */
    public static function generateTransactionId(): string
    {
        $year = date('Y');
        $random = mt_rand(100000, 999999);
        return "SPS-MEM-{$year}-{$random}";
    }

    /**
     * Register a new membership application.
     * Enters as 'Pending' until verified and approved.
     * Uses Argon2id + Pepper for password and AES-256-GCM for PII.
     */
    public static function createApplication(array $input): array
    {
        return self::withExclusiveLock(function () use ($input) {
            $data = self::loadData();
            $memberId = 'mem_' . uniqid();
            $memberCode = self::generateNextMemberCode();

            $category = self::getCategory($input['category_id'] ?? 'STUDENT');
            $plan = self::getPlan($input['plan_id'] ?? 'STUDENT_MONTHLY');

            $isLifetime = ($plan['id'] ?? '') === 'LIFETIME';
            $entryFee = (float)($plan['entry_fee'] ?? 0);
            $planFee = (float)($plan['fee'] ?? 0);
            $totalInitialFee = (float)($plan['total_first_payment'] ?? ($entryFee + $planFee));

            $qrToken = "SPS-VERIFY-" . substr($memberCode, 4) . "-" . strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $input['name_en'] ?? 'MEMBER'), 0, 8)) . "-" . date('Y');

            $avatar = !empty($input['avatar']) ? trim((string)$input['avatar']) : self::DEFAULT_ORGANIZATION_DP;
            if (str_contains($avatar, 'dicebear.com') || str_contains($avatar, 'member_SPS_000872')) {
                $avatar = self::DEFAULT_ORGANIZATION_DP;
            }

            $rawPassword = !empty($input['password']) ? (string)$input['password'] : ('SPS-TEMP-' . bin2hex(random_bytes(6)));
            $passwordHash = CryptoService::hashPassword($rawPassword);

            $newMember = [
                'id' => $memberId,
                'member_code' => $memberCode,
                'name_bn' => trim($input['name_bn'] ?? ''),
                'name_en' => trim($input['name_en'] ?? ''),
                'email' => trim($input['email'] ?? ''),
                'phone' => trim($input['phone'] ?? ''),
                'avatar' => $avatar,
                'category_id' => $category['id'] ?? 'STUDENT',
                'plan_id' => $plan['id'] ?? 'STUDENT_MONTHLY',
                'status' => 'Pending',
                'password_hash' => $passwordHash,
                'join_date' => date('Y-m-d'),
                'start_date' => null,
                'end_date' => null,
                'is_lifetime' => $isLifetime,
                'next_payment_date' => null,
                'entry_fee_paid' => 0,
                'total_paid' => 0,
                'district' => trim($input['district'] ?? ''),
                'upazila' => trim($input['upazila'] ?? ''),
                'address' => trim($input['address'] ?? ''),
                'education' => !empty($input['education']) ? $input['education'] : null,
                'profession' => !empty($input['profession']) ? $input['profession'] : null,
                'recognitions' => ['Regular Member'],
                'volunteer_profile' => [
                    'is_volunteer' => !empty($input['apply_volunteer']),
                    'status' => !empty($input['apply_volunteer']) ? 'Applied' : 'Inactive',
                    'interests' => $input['volunteer_interests'] ?? [],
                    'experience' => trim($input['volunteer_experience'] ?? ''),
                ],
                'card_qr_token' => $qrToken,
                'notifications' => [],
                'notification_status' => [
                    'email_status' => 'PENDING',
                    'sent_at' => null,
                ],
                'notes' => 'অনলাইন পোর্টাল থেকে জমাকৃত নতুন সদস্যপদের আবেদন (ফাইন্যান্স ভেরিফিকেশন অপেক্ষমাণ)।',
            ];

            $storedMember = self::prepareMemberForStorage($newMember);
            $data['members'][] = $storedMember;

            // Create Initial Pending Payment Record with TrxID and Screenshot
            $txId = self::generateTransactionId();
            $paymentType = $isLifetime ? 'lifetime' : (($plan['id'] === 'YEARLY') ? 'yearly' : 'entry');
            $trxId = trim((string)($input['trx_id'] ?? ''));
            if (empty($trxId)) {
                $trxId = 'BKA' . strtoupper(substr(md5(uniqid()), 0, 8));
            }
            $paymentScreenshot = trim((string)($input['payment_screenshot'] ?? ''));
            if (empty($paymentScreenshot)) {
                $paymentScreenshot = 'assets/images/payments/bkash-success-sample.svg';
            }

            $newPayment = [
                'id' => 'pay_' . uniqid(),
                'member_id' => $memberId,
                'member_code' => $memberCode,
                'transaction_id' => $txId,
                'trx_id' => $trxId,
                'payment_screenshot' => $paymentScreenshot,
                'payment_type' => $paymentType,
                'amount' => $totalInitialFee,
                'currency' => 'BDT',
                'payment_method' => $input['payment_method'] ?? 'bKash',
                'sender_number' => $input['sender_number'] ?? $input['phone'] ?? '',
                'sender_name' => trim((string)($input['sender_name'] ?? '')),
                'payment_time' => !empty($input['payment_time']) ? trim((string)$input['payment_time']) : date('Y-m-d H:i:s'),
                'payment_reference' => trim((string)($input['payment_reference'] ?? '')),
                'payment_date' => date('Y-m-d H:i:s'),
                'period_start' => null,
                'period_end' => null,
                'status' => 'Pending',
                'verified_by' => null,
                'verified_at' => null,
                'notes' => 'আবেদনকালীন প্রাথমিক ফি প্রদান (ফাইন্যান্স অফিসার কর্তৃক ভেরিফিকেশন অপেক্ষমাণ)।',
            ];

            $storedPayment = self::preparePaymentForStorage($newPayment);
            $data['payments'][] = $storedPayment;

            self::saveData($data);

            return [
                'member' => self::decryptMember($storedMember),
                'payment' => self::decryptPayment($storedPayment),
            ];
        });
    }

    /**
     * Approve a pending member application & activate membership.
     */
    public static function approveMember(string $memberId, ?array $admin = null): bool
    {
        return self::withExclusiveLock(function () use ($memberId, $admin) {
            $data = self::loadData();
            $found = false;
            $targetMember = null;
            $targetMemberIdx = -1;

            $adminName = $admin['name_bn'] ?? $admin['name_en'] ?? 'Joy Chakraborty (Treasurer / Finance Officer)';

            foreach ($data['members'] as $idx => &$m) {
                if ($m['id'] === $memberId || $m['member_code'] === $memberId) {
                    $plan = self::getPlan($m['plan_id']);
                    $isLifetime = ($plan['id'] ?? '') === 'LIFETIME';

                    $now = date('Y-m-d');
                    $m['status'] = $isLifetime ? 'Lifetime Active' : 'Active';
                    $m['start_date'] = $now;

                    if ($isLifetime) {
                        $m['end_date'] = null;
                        $m['expiry_date'] = null;
                        $m['next_payment_date'] = null;
                        if (!in_array('Lifetime Member', $m['recognitions'] ?? [])) {
                            $m['recognitions'][] = 'Lifetime Member';
                        }
                    } elseif ($plan['duration_type'] === 'yearly') {
                        $m['end_date'] = date('Y-m-d', strtotime('+12 months -1 day'));
                        $m['expiry_date'] = $m['end_date'];
                        $m['next_payment_date'] = $m['end_date'];
                    } else {
                        $m['end_date'] = date('Y-m-d', strtotime('+1 month'));
                        $m['expiry_date'] = $m['end_date'];
                        $m['next_payment_date'] = $m['end_date'];
                    }

                    $m['entry_fee_paid'] = $plan['entry_fee'] ?? 0;
                    $m['total_paid'] = $plan['total_first_payment'] ?? $plan['fee'] ?? 0;
                    $m['notes'] = "আবেদনটি {$adminName} কর্তৃক পর্যালোচিত ও অনুমোদিত হয়েছে।";
                    $found = true;
                    $targetMemberIdx = $idx;
                    break;
                }
            }
            unset($m);

            if (!$found || $targetMemberIdx < 0) {
                return false;
            }

            // Also mark any pending initial payment as Verified
            $verifiedPayment = null;
            foreach ($data['payments'] as &$p) {
                if (($p['member_id'] === $memberId || ($p['member_code'] ?? '') === $memberId) && $p['status'] === 'Pending') {
                    $p['status'] = 'Verified';
                    $p['verified_by'] = $adminName;
                    $p['verified_at'] = date('Y-m-d H:i:s');
                    $plan = self::getPlan($data['members'][$targetMemberIdx]['plan_id']);
                    $isLifetime = ($plan['id'] ?? '') === 'LIFETIME';
                    $p['period_start'] = date('Y-m-d');
                    $p['period_end'] = $isLifetime ? null : ($plan['duration_type'] === 'yearly' ? date('Y-m-d', strtotime('+12 months -1 day')) : date('Y-m-d', strtotime('+1 month')));
                    $verifiedPayment = $p;
                    break;
                }
            }
            unset($p);

            // Fallback: If no pending payment, find latest payment
            if (!$verifiedPayment) {
                foreach ($data['payments'] as $p) {
                    if ($p['member_id'] === $memberId || ($p['member_code'] ?? '') === $memberId) {
                        $verifiedPayment = $p;
                        break;
                    }
                }
            }

            if ($verifiedPayment) {
                self::dispatchApprovalNotification($data['members'][$targetMemberIdx], $verifiedPayment, $adminName);
            }

            self::saveData($data);
            return true;
        });
    }

    /**
     * Dispatch email notification and persist in-app notification upon Finance Officer approval.
     */
    private static function dispatchApprovalNotification(array &$member, array $payment, string $adminName): void
    {
        $emailRes = EmailService::sendMembershipApprovedEmail($member, $payment);

        if (!isset($member['notifications']) || !is_array($member['notifications'])) {
            $member['notifications'] = [];
        }

        $txId = $payment['transaction_id'] ?? '';
        $member['notifications'][] = [
            'id' => 'notif_' . uniqid(),
            'type' => 'payment_approved',
            'title' => 'সদস্যপদ সক্রিয় ও পেমেন্ট ভেরিফিকেশন সম্পন্ন',
            'title_en' => 'Membership Activated & Payment Verified',
            'message' => "ফাইন্যান্স অফিসার ({$adminName}) কর্তৃক আপনার সদস্যপদ আবেদন ও পেমেন্ট অনুমোদিত হয়েছে। আপনার সদস্যপদ এখন সম্পূর্ণ সক্রিয়।",
            'message_en' => "Your membership application and payment have been verified and approved by the Finance Officer ({$adminName}). Your membership is now active.",
            'transaction_id' => $txId,
            'invoice_id' => $txId,
            'invoice_url' => '/bn/invoice/' . urlencode($txId),
            'created_at' => date('Y-m-d H:i:s'),
            'read' => false,
        ];

        $member['notification_status'] = [
            'email_status' => $emailRes['status'] ?? 'SENT',
            'sent_at' => date('Y-m-d H:i:s'),
            'delivered' => $emailRes['delivered'] ?? true,
        ];

        AuditService::log(
            'member.finance_approved',
            'finance',
            $member['member_code'],
            $member['name_en'] ?: $member['name_bn'],
            ['status' => 'Pending Finance Approval'],
            [
                'status' => $member['status'],
                'verified_by' => $adminName,
                'transaction_id' => $txId,
                'email_status' => $member['notification_status']['email_status'],
            ],
            "Finance Officer {$adminName} approved payment & activated membership for {$member['member_code']}"
        );
    }

    /**
     * Reject a membership application with formal reasoning.
     */
    public static function rejectMember(string $memberId, string $reason, ?array $admin = null): bool
    {
        return self::withExclusiveLock(function () use ($memberId, $reason, $admin) {
            $data = self::loadData();
            $adminName = $admin['name_bn'] ?? $admin['name_en'] ?? 'Administrator';

            foreach ($data['members'] as &$m) {
                if ($m['id'] === $memberId || $m['member_code'] === $memberId) {
                    $m['status'] = 'Rejected';
                    $m['notes'] = "বাতিলকারী: {$adminName}। কারণ: {$reason}";
                    self::saveData($data);
                    return true;
                }
            }
            return false;
        });
    }

    /**
     * Suspend an active member for misconduct or administrative policy breach.
     */
    public static function suspendMember(string $memberId, string $reason, ?array $admin = null): bool
    {
        return self::withExclusiveLock(function () use ($memberId, $reason, $admin) {
            $data = self::loadData();
            $adminName = $admin['name_bn'] ?? $admin['name_en'] ?? 'Administrator';

            foreach ($data['members'] as &$m) {
                if ($m['id'] === $memberId || $m['member_code'] === $memberId) {
                    $prevStatus = $m['status'];
                    $m['status'] = 'Suspended';
                    $m['notes'] = "স্থগিতকারী: {$adminName} (পূর্ববর্তী স্ট্যাটাস: {$prevStatus})। কারণ: {$reason}";

                    // Log into history
                    $data['history'][] = [
                        'id' => 'hist_' . uniqid(),
                        'member_id' => $m['id'],
                        'member_code' => $m['member_code'],
                        'old_category' => $m['category_id'],
                        'new_category' => $m['category_id'],
                        'old_plan' => $m['plan_id'],
                        'new_plan' => $m['plan_id'],
                        'reason' => "সদস্যপদ সাময়িক স্থগিতকরণ: {$reason}",
                        'changed_by' => $adminName,
                        'changed_at' => date('Y-m-d H:i:s'),
                    ];

                    self::saveData($data);
                    return true;
                }
            }
            return false;
        });
    }

    /**
     * Reactivate a suspended or expired member.
     */
    public static function activateMember(string $memberId, ?array $admin = null): bool
    {
        return self::withExclusiveLock(function () use ($memberId, $admin) {
            $data = self::loadData();
            $adminName = $admin['name_bn'] ?? $admin['name_en'] ?? 'Administrator';

            foreach ($data['members'] as &$m) {
                if ($m['id'] === $memberId || $m['member_code'] === $memberId) {
                    $m['status'] = !empty($m['is_lifetime']) ? 'Lifetime Active' : 'Active';
                    $m['notes'] = "পুনরায় সক্রিয়কারী: {$adminName}।";
                    self::saveData($data);
                    return true;
                }
            }
            return false;
        });
    }

    /**
     * Transition a Student Member to Earning Member upon graduation/employment.
     * Retains the permanent unique Member ID (e.g. SPS-000872), while recording
     * immutable historical transition records.
     */
    public static function transitionCategory(
        string $memberId,
        string $newCategoryId,
        string $newPlanId,
        string $reason,
        ?array $admin = null
    ): bool {
        return self::withExclusiveLock(function () use ($memberId, $newCategoryId, $newPlanId, $reason, $admin) {
            $data = self::loadData();
            $adminName = $admin['name_bn'] ?? $admin['name_en'] ?? 'Administrator';
            $found = false;

            foreach ($data['members'] as &$m) {
                if ($m['id'] === $memberId || $m['member_code'] === $memberId) {
                    $oldCategory = $m['category_id'];
                    $oldPlan = $m['plan_id'];

                    $m['category_id'] = $newCategoryId;
                    $m['plan_id'] = $newPlanId;
                    if ($newPlanId === 'LIFETIME') {
                        $m['is_lifetime'] = true;
                        $m['status'] = 'Lifetime Active';
                        $m['end_date'] = null;
                        if (!in_array('Lifetime Member', $m['recognitions'] ?? [])) {
                            $m['recognitions'][] = 'Lifetime Member';
                        }
                    }

                    // Append to immutable history log
                    $data['history'][] = [
                        'id' => 'hist_' . uniqid(),
                        'member_id' => $m['id'],
                        'member_code' => $m['member_code'],
                        'old_category' => $oldCategory,
                        'new_category' => $newCategoryId,
                        'old_plan' => $oldPlan,
                        'new_plan' => $newPlanId,
                        'reason' => $reason,
                        'changed_by' => $adminName,
                        'changed_at' => date('Y-m-d H:i:s'),
                    ];

                    $found = true;
                    break;
                }
            }
            unset($m);

            if ($found) {
                self::saveData($data);
                return true;
            }
            return false;
        });
    }

    /**
     * Record a new membership payment (renewal, monthly, yearly, lifetime).
     */
    public static function recordPayment(array $paymentInput, ?array $admin = null): array
    {
        return self::withExclusiveLock(function () use ($paymentInput, $admin) {
            $data = self::loadData();
            $member = self::getMemberById($paymentInput['member_id']);
            if (!$member) {
                throw new \InvalidArgumentException('Member not found');
            }

            $adminName = $admin ? ($admin['name_bn'] ?? $admin['name_en'] ?? 'Admin') : null;
            $isAutoVerified = !empty($paymentInput['auto_verify']) || $admin !== null;

            $txId = self::generateTransactionId();
            $paymentType = $paymentInput['payment_type'] ?? 'monthly';
            $amount = (float)($paymentInput['amount'] ?? 0);

            $startDate = $paymentInput['period_start'] ?? date('Y-m-d');
            $endDate = null;

            if ($paymentType === 'yearly') {
                $endDate = date('Y-m-d', strtotime($startDate . ' +12 months -1 day'));
            } elseif ($paymentType === 'monthly') {
                $endDate = date('Y-m-d', strtotime($startDate . ' +1 month'));
            } elseif ($paymentType === 'lifetime') {
                $endDate = null;
            }

            $trxId = trim((string)($paymentInput['trx_id'] ?? ''));
            if (empty($trxId)) {
                $trxId = 'BKA' . strtoupper(substr(md5(uniqid()), 0, 8));
            }
            $paymentScreenshot = trim((string)($paymentInput['payment_screenshot'] ?? ''));
            if (empty($paymentScreenshot)) {
                $paymentScreenshot = 'assets/images/payments/bkash-success-sample.svg';
            }

            $payment = [
                'id' => 'pay_' . uniqid(),
                'member_id' => $member['id'],
                'member_code' => $member['member_code'],
                'transaction_id' => $txId,
                'trx_id' => $trxId,
                'payment_screenshot' => $paymentScreenshot,
                'payment_type' => $paymentType,
                'amount' => $amount,
                'currency' => 'BDT',
                'payment_method' => $paymentInput['payment_method'] ?? 'bKash',
                'sender_number' => $paymentInput['sender_number'] ?? '',
                'sender_name' => trim((string)($paymentInput['sender_name'] ?? '')),
                'payment_time' => !empty($paymentInput['payment_time']) ? trim((string)$paymentInput['payment_time']) : date('Y-m-d H:i:s'),
                'payment_reference' => trim((string)($paymentInput['payment_reference'] ?? '')),
                'payment_date' => date('Y-m-d H:i:s'),
                'period_start' => $startDate,
                'period_end' => $endDate,
                'status' => $isAutoVerified ? 'Verified' : 'Pending',
                'verified_by' => $isAutoVerified ? $adminName : null,
                'verified_at' => $isAutoVerified ? date('Y-m-d H:i:s') : null,
                'notes' => $paymentInput['notes'] ?? 'সদস্যপদ ফি লেনদেন।',
            ];

            $storedPayment = self::preparePaymentForStorage($payment);
            $data['payments'][] = $storedPayment;

            // If verified, update member validity dates
            if ($isAutoVerified) {
                foreach ($data['members'] as &$m) {
                    if ($m['id'] === $member['id']) {
                        $m['total_paid'] = (float)($m['total_paid'] ?? 0) + $amount;
                        if ($paymentType === 'lifetime') {
                            $m['status'] = 'Lifetime Active';
                            $m['is_lifetime'] = true;
                            $m['plan_id'] = 'LIFETIME';
                            $m['end_date'] = null;
                            $m['expiry_date'] = null;
                            $m['next_payment_date'] = null;
                        } elseif ($paymentType === 'yearly') {
                            $m['status'] = 'Active';
                            $m['plan_id'] = 'YEARLY';
                            $m['end_date'] = $endDate;
                            $m['expiry_date'] = $endDate;
                            $m['next_payment_date'] = $endDate;
                        } else {
                            $m['status'] = 'Active';
                            $m['end_date'] = $endDate;
                            $m['expiry_date'] = $endDate;
                            $m['next_payment_date'] = $endDate;
                        }
                        break;
                    }
                }
                unset($m);
            }

            self::saveData($data);
            return self::decryptPayment($storedPayment);
        });
    }

    /**
     * Get all payments recorded in the system, optionally filtered by status.
     * Payments are decrypted for application view.
     */
    public static function getAllPayments(?string $status = null): array
    {
        $data = self::loadData();
        $payments = $data['payments'] ?? [];
        $decrypted = array_map([self::class, 'decryptPayment'], $payments);

        if ($status && $status !== 'all') {
            $decrypted = array_filter($decrypted, fn($p) => ($p['status'] ?? '') === $status);
        }
        usort($decrypted, fn($a, $b) => strcmp($b['payment_date'] ?? $b['created_at'] ?? '', $a['payment_date'] ?? $a['created_at'] ?? ''));
        return array_values($decrypted);
    }

    /**
     * Get pending payments awaiting Finance Officer verification.
     */
    public static function getPendingPayments(): array
    {
        return self::getAllPayments('Pending');
    }

    /**
     * Verify an existing pending payment.
     * Accessible by Finance Officer (primary operator), Super Admin, and Admin (supervisory monitoring).
     */
    public static function verifyPayment(string $paymentId, ?array $admin = null): bool
    {
        return self::withExclusiveLock(function () use ($paymentId, $admin) {
            $data = self::loadData();
            $adminName = $admin ? ($admin['name_bn'] ?? $admin['name_en'] ?? 'Finance Officer') : 'Joy Chakraborty (Treasurer / Finance Officer)';
            $foundPayment = null;

            foreach ($data['payments'] as &$p) {
                if ($p['id'] === $paymentId || $p['transaction_id'] === $paymentId) {
                    $p['status'] = 'Verified';
                    $p['verified_by'] = $adminName;
                    $p['verified_at'] = date('Y-m-d H:i:s');
                    $foundPayment = $p;
                    break;
                }
            }
            unset($p);

            if (!$foundPayment) {
                return false;
            }

            // Apply validity extension & member activation
            $targetMemberIdx = -1;
            $wasPending = false;
            foreach ($data['members'] as $idx => &$m) {
                if ($m['id'] === $foundPayment['member_id']) {
                    $wasPending = self::isPendingStatus($m['status'] ?? '');
                    $targetMemberIdx = $idx;
                    $m['total_paid'] = (float)($m['total_paid'] ?? 0) + (float)$foundPayment['amount'];
                    if (empty($m['start_date'])) {
                        $m['start_date'] = date('Y-m-d');
                    }

                    if ($foundPayment['payment_type'] === 'lifetime') {
                        $m['status'] = 'Lifetime Active';
                        $m['is_lifetime'] = true;
                        $m['plan_id'] = 'LIFETIME';
                        $m['end_date'] = null;
                        $m['expiry_date'] = null;
                        $m['next_payment_date'] = null;
                        if (!in_array('Lifetime Member', $m['recognitions'] ?? [])) {
                            $m['recognitions'][] = 'Lifetime Member';
                        }
                    } elseif ($foundPayment['payment_type'] === 'yearly') {
                        $m['status'] = 'Active';
                        $m['plan_id'] = 'YEARLY';
                        $m['end_date'] = date('Y-m-d', strtotime('+12 months -1 day'));
                        $m['expiry_date'] = $m['end_date'];
                        $m['next_payment_date'] = $m['end_date'];
                    } else {
                        $m['status'] = 'Active';
                        $m['end_date'] = date('Y-m-d', strtotime('+1 month'));
                        $m['expiry_date'] = $m['end_date'];
                        $m['next_payment_date'] = $m['end_date'];
                    }

                    $m['notes'] = "পেমেন্ট {$adminName} কর্তৃক যাচাইকৃত ও সদস্যপদ হালনাগাদকৃত।";
                    break;
                }
            }
            unset($m);

            if ($targetMemberIdx >= 0 && $wasPending) {
                self::dispatchApprovalNotification($data['members'][$targetMemberIdx], $foundPayment, $adminName);
            }

            self::saveData($data);
            return true;
        });
    }

    /**
     * Reject a fraudulent or unmatched payment.
     */
    public static function rejectPayment(string $paymentId, string $reason, ?array $admin = null): bool
    {
        return self::withExclusiveLock(function () use ($paymentId, $reason, $admin) {
            $data = self::loadData();
            $adminName = $admin ? ($admin['name_bn'] ?? $admin['name_en'] ?? 'Finance Officer') : 'Joy Chakraborty (Treasurer / Finance Officer)';
            $found = false;

            foreach ($data['payments'] as &$p) {
                if ($p['id'] === $paymentId || $p['transaction_id'] === $paymentId) {
                    $p['status'] = 'Rejected';
                    $p['verified_by'] = $adminName;
                    $p['verified_at'] = date('Y-m-d H:i:s');
                    $p['notes'] = "পেমেন্ট বাতিলকারী: {$adminName}। কারণ: {$reason}";
                    $found = true;
                    break;
                }
            }
            unset($p);

            if ($found) {
                self::saveData($data);
                return true;
            }
            return false;
        });
    }

    /**
     * Get single payment record by internal ID, Transaction ID, or Provider TrxID.
     * Uses Blind Indexing for search on encrypted TrxID.
     */
    public static function getPaymentById(string $identifier): ?array
    {
        $data = self::loadData();
        $identifier = trim($identifier);
        if ($identifier === '') {
            return null;
        }

        $trxBidx = CryptoService::blindIndex(CryptoService::normalizeSearchTerm($identifier));

        foreach ($data['payments'] ?? [] as $p) {
            if (
                ($p['id'] ?? '') === $identifier ||
                strcasecmp((string)($p['transaction_id'] ?? ''), $identifier) === 0
            ) {
                return self::decryptPayment($p);
            }
            if (!empty($p['trx_id_bidx']) && hash_equals($p['trx_id_bidx'], $trxBidx)) {
                return self::decryptPayment($p);
            }
            $decrypted = self::decryptPayment($p);
            if (strcasecmp((string)($decrypted['trx_id'] ?? ''), $identifier) === 0) {
                return $decrypted;
            }
        }
        return null;
    }

    /**
     * Record a Contribution / Donation from Member or Non-Member.
     * Ensures every payer receives an official invoice with Finance Secretary sign.
     */
    public static function recordDonation(array $input, ?array $admin = null): array
    {
        return self::withExclusiveLock(function () use ($input, $admin) {
            $data = self::loadData();
            $txId = self::generateTransactionId();
            $trxId = trim((string)($input['trx_id'] ?? ''));
            if (empty($trxId)) {
                $trxId = 'BKA' . strtoupper(substr(md5(uniqid()), 0, 8));
            }

            $senderName = trim((string)($input['sender_name'] ?? $input['donor_name'] ?? ''));
            if (empty($senderName)) {
                $senderName = 'সম্মানিত শুভানুধ্যায়ী (Honorable Contributor)';
            }

            $senderPhone = trim((string)($input['sender_number'] ?? $input['phone'] ?? ''));
            $amount = (float)($input['amount'] ?? 10);
            if ($amount <= 0) {
                $amount = 10.0;
            }

            $projectType = trim((string)($input['project_type'] ?? '10_taka'));
            $purposeTitle = match ($projectType) {
                '10_taka', 'donation_10taka' => 'সনাতনী ১০ টাকার প্রজেক্ট (Sanatani 10 Taka Project)',
                'welfare', 'donation_welfare' => 'এসপিএস ওয়েলফেয়ার ট্রাস্ট (SPS Welfare Trust)',
                default => 'সাধারণ মানবকল্যাণ ও ধর্মসেবা অনুদান (General Seva Donation)',
            };

            // Check if member code was supplied or if member logged in
            $memberId = trim((string)($input['member_id'] ?? ''));
            $memberCode = trim((string)($input['member_code'] ?? ''));
            if ($memberId !== '' || $memberCode !== '') {
                $m = self::getMemberById($memberCode ?: $memberId);
                if ($m) {
                    $memberId = $m['id'];
                    $memberCode = $m['member_code'];
                    if ($senderName === 'সম্মানিত শুভানুধ্যায়ী (Honorable Contributor)') {
                        $senderName = $m['name_bn'] ?: $m['name_en'];
                    }
                }
            } else {
                $memberId = 'non_member_' . uniqid();
                $memberCode = 'NON-MEMBER';
            }

            $isAutoVerified = !empty($input['auto_verify']) || $admin !== null;
            $adminName = $admin ? ($admin['name_bn'] ?? $admin['name_en'] ?? 'Joy Chakraborty') : 'Joy Chakraborty (Treasurer / Finance Officer)';

            $payment = [
                'id' => 'pay_' . uniqid(),
                'member_id' => $memberId,
                'member_code' => $memberCode,
                'transaction_id' => $txId,
                'trx_id' => $trxId,
                'payment_screenshot' => $input['payment_screenshot'] ?? 'assets/images/payments/bkash-success-sample.svg',
                'payment_type' => $projectType === 'welfare' ? 'donation_welfare' : ($projectType === '10_taka' ? 'donation_10taka' : 'general_donation'),
                'purpose_title' => $purposeTitle,
                'amount' => $amount,
                'currency' => 'BDT',
                'payment_method' => $input['payment_method'] ?? 'bKash',
                'sender_number' => $senderPhone,
                'sender_name' => $senderName,
                'payment_time' => !empty($input['payment_time']) ? trim((string)$input['payment_time']) : date('Y-m-d H:i:s'),
                'payment_reference' => trim((string)($input['payment_reference'] ?? ($projectType === 'welfare' ? 'Welfare' : '10Taka'))),
                'payment_date' => date('Y-m-d H:i:s'),
                'period_start' => date('Y-m-d'),
                'period_end' => null,
                'status' => $isAutoVerified ? 'Verified' : 'Pending',
                'verified_by' => $isAutoVerified ? $adminName : null,
                'verified_at' => $isAutoVerified ? date('Y-m-d H:i:s') : null,
                'notes' => $input['notes'] ?? "{$purposeTitle} তহবিলে অনুদান জমা।",
            ];

            $storedPayment = self::preparePaymentForStorage($payment);
            $data['payments'][] = $storedPayment;

            if ($isAutoVerified && $memberCode !== 'NON-MEMBER') {
                foreach ($data['members'] as &$m) {
                    if ($m['id'] === $memberId || $m['member_code'] === $memberCode) {
                        $m['total_paid'] = (float)($m['total_paid'] ?? 0) + $amount;
                        break;
                    }
                }
                unset($m);
            }

            self::saveData($data);
            return self::decryptPayment($storedPayment);
        });
    }

    /**
     * Submit volunteer interest.
     */
    public static function applyForVolunteer(string $memberId, array $interests, string $experience): bool
    {
        return self::withExclusiveLock(function () use ($memberId, $interests, $experience) {
            $data = self::loadData();
            foreach ($data['members'] as &$m) {
                if ($m['id'] === $memberId || $m['member_code'] === $memberId) {
                    $m['volunteer_profile'] = [
                        'is_volunteer' => true,
                        'status' => 'Applied',
                        'interests' => $interests,
                        'experience' => $experience,
                    ];

                    $data['volunteers'][] = [
                        'id' => 'vol_' . uniqid(),
                        'member_id' => $m['id'],
                        'member_code' => $m['member_code'],
                        'name' => $m['name_en'] ?? $m['name_bn'],
                        'status' => 'Applied',
                        'interests' => $interests,
                        'applied_at' => date('Y-m-d'),
                        'approved_at' => null,
                    ];

                    self::saveData($data);
                    return true;
                }
            }
            return false;
        });
    }

    /**
     * Retrieve all payment records for a member.
     */
    public static function getMemberPayments(string $memberId): array
    {
        $data = self::loadData();
        $payments = [];
        foreach ($data['payments'] ?? [] as $p) {
            if ($p['member_id'] === $memberId || $p['member_code'] === $memberId) {
                $payments[] = $p;
            }
        }
        // sort latest first
        usort($payments, fn($a, $b) => strcmp($b['payment_date'], $a['payment_date']));
        return $payments;
    }

    /**
     * Retrieve category / plan transition history for a member.
     */
    public static function getMemberHistory(string $memberId): array
    {
        $data = self::loadData();
        $history = [];
        foreach ($data['history'] ?? [] as $h) {
            if ($h['member_id'] === $memberId || $h['member_code'] === $memberId) {
                $history[] = $h;
            }
        }
        usort($history, fn($a, $b) => strcmp($b['changed_at'], $a['changed_at']));
        return $history;
    }

    /**
     * Get aggregate statistics for admin dashboard.
     */
    public static function getMemberStats(): array
    {
        $data = self::loadData();
        $members = $data['members'] ?? [];

        $stats = [
            'total' => count($members),
            'active' => 0,
            'student' => 0,
            'earning' => 0,
            'monthly' => 0,
            'yearly' => 0,
            'lifetime' => 0,
            'payment_due' => 0,
            'expiring_soon' => 0,
            'pending' => 0,
            'suspended' => 0,
            'volunteers' => 0,
            'total_collected' => 0,
        ];

        $now = time();
        $thirtyDays = $now + (30 * 86400);

        foreach ($members as $m) {
            $status = $m['status'] ?? '';
            $cat = $m['category_id'] ?? '';
            $plan = $m['plan_id'] ?? '';

            if ($status === 'Active' || $status === 'Lifetime Active') {
                $stats['active']++;
            }
            if ($cat === 'STUDENT') {
                $stats['student']++;
            }
            if ($cat === 'EARNING') {
                $stats['earning']++;
            }
            if ($plan === 'STUDENT_MONTHLY' || $plan === 'EARNING_MONTHLY') {
                $stats['monthly']++;
            }
            if ($plan === 'YEARLY') {
                $stats['yearly']++;
            }
            if ($plan === 'LIFETIME' || !empty($m['is_lifetime'])) {
                $stats['lifetime']++;
            }
            if ($status === 'Payment Due') {
                $stats['payment_due']++;
            }
            if ($status === 'Pending') {
                $stats['pending']++;
            }
            if ($status === 'Suspended') {
                $stats['suspended']++;
            }
            if (!empty($m['volunteer_profile']['is_volunteer']) && ($m['volunteer_profile']['status'] ?? '') === 'Active') {
                $stats['volunteers']++;
            }

            // check expiry within 30 days
            if (!empty($m['end_date']) && $status === 'Active') {
                $endTs = strtotime($m['end_date']);
                if ($endTs >= $now && $endTs <= $thirtyDays) {
                    $stats['expiring_soon']++;
                }
            }
        }

        foreach ($data['payments'] ?? [] as $p) {
            if (($p['status'] ?? '') === 'Verified') {
                $stats['total_collected'] += (float)($p['amount'] ?? 0);
            }
        }

        return $stats;
    }

    /**
     * Check if a member is eligible to author blogs (must be Active or Lifetime Active).
     */
    public static function isEligibleAuthor(string $emailOrCode): bool
    {
        $member = self::getMemberByEmail($emailOrCode) ?? self::getMemberById($emailOrCode);
        if (!$member) {
            return false;
        }
        return in_array($member['status'], ['Active', 'Lifetime Active'], true);
    }
}
