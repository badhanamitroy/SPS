<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Enterprise Password Policy Engine
 *
 * Enforces NIST SP 800-63B aligned password requirements:
 * 1. Minimum 12 characters.
 * 2. Allows long passphrases (up to 4096 characters, no arbitrary small limit).
 * 3. Prohibits common, breached, or dictionary passwords.
 * 4. Prohibits user identifiers (username, email components, full name, member code).
 * 5. Prohibits trivial repetitive or sequential character runs.
 * 6. Never logs passwords or sensitive credential fragments.
 */
class PasswordPolicy
{
    public const MIN_LENGTH = 12;
    public const MAX_LENGTH = 4096;

    /**
     * Top common/obvious passwords and organization-specific weak defaults.
     */
    private const COMMON_PASSWORDS = [
        'password',
        'password123',
        'password1234',
        'password12345',
        'password123456',
        'password1234567',
        'password12345678',
        '123456789012',
        '1234567890123',
        '12345678901234',
        'admin1234567',
        'admin12345678',
        'administrator',
        'sps@admin2026',
        'sps@member2026',
        'spsadmin2026',
        'spsmember2026',
        'qwertyuiop12',
        'qwertyuiopas',
        'welcome12345',
        'welcome2026!',
        'letmein12345',
        'iloveyou1234',
        'sanatan2026!',
        'sanatandharma',
        'bangladesh2026',
        'dharmikbrotherhood',
        'changeme1234',
        'masterpassword',
        'defaultpassword',
    ];

    /**
     * Validate password according to policy.
     *
     * @param string $password The candidate password.
     * @param array|null $userContext Optional user/member record for contextual checks.
     * @return array [
     *   'valid' => bool,
     *   'error' => ?string,     // English error message
     *   'error_bn' => ?string  // Bengali error message
     * ]
     */
    public static function validate(string $password, ?array $userContext = null): array
    {
        $len = mb_strlen($password);

        // 1. Length constraints
        if ($len < self::MIN_LENGTH) {
            return [
                'valid' => false,
                'error' => sprintf('Password must be at least %d characters in length.', self::MIN_LENGTH),
                'error_bn' => sprintf('পাসওয়ার্ড কমপক্ষে %d অক্ষরের হতে হবে।', self::MIN_LENGTH),
            ];
        }

        if ($len > self::MAX_LENGTH) {
            return [
                'valid' => false,
                'error' => sprintf('Password exceeds maximum allowed length of %d characters.', self::MAX_LENGTH),
                'error_bn' => sprintf('পাসওয়ার্ড সর্বোচ্চ %d অক্ষরের মধ্যে সীমাবদ্ধ থাকতে হবে।', self::MAX_LENGTH),
            ];
        }

        $lowerPwd = mb_strtolower($password);

        // 2. Reject obvious / common passwords
        if (in_array($lowerPwd, self::COMMON_PASSWORDS, true)) {
            return [
                'valid' => false,
                'error' => 'This password is too common or easily guessable. Please choose a more complex passphrase.',
                'error_bn' => 'এই পাসওয়ার্ডটি অত্যন্ত সাধারণ ও সহজে অনুমানযোগ্য। অনুগ্রহ করে একটি জটিল ও শক্তিশালী পাসফ্রেজ নির্বাচন করুন।',
            ];
        }

        // 3. Reject repetitive characters (e.g. "aaaaaaaaaaaa" or "111111111111")
        if (preg_match('/^(.)\1{11,}$/u', $password)) {
            return [
                'valid' => false,
                'error' => 'Password cannot consist solely of repetitive characters.',
                'error_bn' => 'পাসওয়ার্ড শুধুমাত্র একই অক্ষরের পুনরাবৃত্তি দিয়ে গঠন করা যাবে না।',
            ];
        }

        // 4. Reject simple alphabetical or numerical sequences
        $sequences = [
            '012345678901',
            '123456789012',
            '234567890123',
            '987654321098',
            'abcdefghijkl',
            'bcdefghijklm',
            'cdefghijklmn',
            'lkjihgfedcba',
            'qwertyuiopas',
        ];
        foreach ($sequences as $seq) {
            if (str_contains($lowerPwd, $seq)) {
                return [
                    'valid' => false,
                    'error' => 'Password contains simple sequential characters.',
                    'error_bn' => 'পাসওয়ার্ডে সহজ ধারাবাহিক বা ক্রমিক অক্ষর/সংখ্যা ব্যবহার করা যাবে না।',
                ];
            }
        }

        // 5. Reject passwords containing personal identifiers
        if (!empty($userContext) && is_array($userContext)) {
            $identifierTokens = self::extractIdentifierTokens($userContext);
            foreach ($identifierTokens as $token) {
                if (mb_strlen($token) >= 3 && str_contains($lowerPwd, $token)) {
                    return [
                        'valid' => false,
                        'error' => 'Password must not contain your username, email, name, or member ID.',
                        'error_bn' => 'পাসওয়ার্ডে আপনার ইউজারনেম, ইমেইল, নাম বা মেম্বার আইডি অন্তর্ভুক্ত করা যাবে না।',
                    ];
                }
            }
        }

        return [
            'valid' => true,
            'error' => null,
            'error_bn' => null,
        ];
    }

    /**
     * Assert that a password satisfies policy, throwing an exception if invalid.
     */
    public static function assertValid(string $password, ?array $userContext = null): void
    {
        $res = self::validate($password, $userContext);
        if (!$res['valid']) {
            throw new \InvalidArgumentException($res['error'] ?? 'Password violates security policy.');
        }
    }

    /**
     * Extract searchable lower-case tokens from user context.
     */
    private static function extractIdentifierTokens(array $context): array
    {
        $tokens = [];

        $fields = ['username', 'id', 'member_code', 'name_en', 'name_bn'];
        foreach ($fields as $field) {
            if (!empty($context[$field]) && is_string($context[$field])) {
                $val = mb_strtolower(trim($context[$field]));
                if ($val !== '') {
                    $tokens[] = $val;
                    // Also extract individual name words (e.g. "anik" from "Anik Roy")
                    $parts = preg_split('/[\s_\-\.\@]+/u', $val);
                    foreach ($parts as $p) {
                        if (mb_strlen($p) >= 3) {
                            $tokens[] = $p;
                        }
                    }
                }
            }
        }

        // Email user component
        if (!empty($context['email']) && is_string($context['email'])) {
            $emailParts = explode('@', mb_strtolower(trim($context['email'])));
            if (!empty($emailParts[0]) && mb_strlen($emailParts[0]) >= 3) {
                $tokens[] = $emailParts[0];
            }
        }

        return array_values(array_unique(array_filter($tokens)));
    }
}
