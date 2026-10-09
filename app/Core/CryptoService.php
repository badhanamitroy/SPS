<?php

declare(strict_types=1);

namespace App\Core;

class CryptoService
{
    private const CIPHER = 'aes-256-gcm';
    private const GCM_TAG_LENGTH = 16;
    private const GCM_IV_LENGTH = 12;

    /**
     * Argon2id Production Configuration Parameters.
     * memory_cost = 65536 KiB (64 MiB)
     * time_cost = 4 iterations
     * threads = 2 parallel threads
     */
    public const ARGON2_OPTIONS = [
        'memory_cost' => 65536,
        'time_cost' => 4,
        'threads' => 2,
    ];

    /**
     * Derive binary encryption key for a given key version.
     */
    public static function getEncryptionKey(?string $version = null): string
    {
        $version = $version ?? (string)Env::get('APP_KEY_ACTIVE', 'v1');
        $envKey = (string)Env::get('APP_KEY_' . strtoupper($version), Env::get('APP_KEY', ''));

        if (empty($envKey)) {
            // Fallback emergency seed if environment missing in test sandbox
            $envKey = '463d4e2d8ab0ee6237a7314d4bc5d50b3c6bb7d6ca014e4aeeb01553ee8f96d2';
        }

        // Return 32 binary bytes
        if (ctype_xdigit($envKey) && strlen($envKey) === 64) {
            return hex2bin($envKey);
        }
        return hash('sha256', $envKey, true);
    }

    private static ?string $dummyHash = null;

    /**
     * Get server-side password pepper binary bytes from environment.
     * 
     * FAIL-CLOSED ARCHITECTURE:
     * - PASSWORD_PEPPER exists and is valid -> use it.
     * - PASSWORD_PEPPER missing or invalid  -> FAIL CLOSED immediately.
     * 
     * Never silently falls back to a known or hardcoded secret.
     * Never leaks pepper in error messages or logs.
     *
     * @return string 32 binary bytes.
     * @throws \RuntimeException If pepper is missing, empty, or invalid format.
     */
    public static function getPepper(): string
    {
        $pepper = trim((string)Env::get('PASSWORD_PEPPER', ''));

        if ($pepper === '') {
            throw new \RuntimeException(
                'Critical Security Failure: PASSWORD_PEPPER environment variable is missing or empty. ' .
                'Secure password operations cannot proceed.'
            );
        }

        // Validate strictly: must be 64-character hexadecimal (256-bit) or 32 raw bytes
        if (strlen($pepper) === 64 && ctype_xdigit($pepper)) {
            $bin = hex2bin($pepper);
            if ($bin !== false && strlen($bin) === 32) {
                return $bin;
            }
        }

        if (strlen($pepper) === 32) {
            return $pepper;
        }

        throw new \RuntimeException(
            'Critical Security Failure: PASSWORD_PEPPER does not satisfy strict cryptographic requirements. ' .
            'Must be a 64-character hexadecimal string (256-bit) or 32 binary bytes.'
        );
    }

    /**
     * Check if a valid password pepper is configured in the environment.
     */
    public static function hasValidPepper(): bool
    {
        try {
            self::getPepper();
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Perform constant-time dummy password verification to mitigate account-enumeration timing attacks.
     * Ensures nonexistent account lookup takes indistinguishable CPU time from an existing account.
     */
    public static function dummyVerify(string $password): bool
    {
        try {
            if (self::$dummyHash === null) {
                self::$dummyHash = self::hashPassword('sps_dummy_timing_pad_entropy_salt_2026');
            }
            return self::verifyPassword($password, self::$dummyHash);
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Get blind index HMAC key binary bytes from environment.
     */
    public static function getBlindIndexKey(): string
    {
        $key = (string)Env::get('BLIND_INDEX_KEY', '');
        if (empty($key)) {
            $key = '99a718690ca5ccfc8513cb5eb4cce41783ff4f747201b10a27c8b8e043cd0906';
        }
        if (ctype_xdigit($key) && strlen($key) === 64) {
            return hex2bin($key);
        }
        return hash('sha256', $key, true);
    }

    /**
     * Compute a HMAC-SHA256 peppered representation of a password.
     * Protects against length limits and creates a uniform 32-byte digest before Argon2id.
     */
    public static function pepperPassword(string $password): string
    {
        $pepper = self::getPepper();
        return hash_hmac('sha256', $password, $pepper);
    }

    /**
     * Hash a password using Argon2id with server-side secret pepper.
     */
    public static function hashPassword(string $password): string
    {
        $peppered = self::pepperPassword($password);
        return password_hash($peppered, PASSWORD_ARGON2ID, self::ARGON2_OPTIONS);
    }

    /**
     * Verify a password against a stored hash.
     * Supports:
     * 1. Modern Argon2id hashes with server pepper
     * 2. Legacy Bcrypt hashes without pepper (for seamless zero-downtime migration)
     */
    public static function verifyPassword(string $password, string $storedHash): bool
    {
        if (empty($password) || empty($storedHash)) {
            return false;
        }

        // Modern Argon2id hash check with pepper
        if (str_starts_with($storedHash, '$argon2id$') || str_starts_with($storedHash, '$argon2i$')) {
            $peppered = self::pepperPassword($password);
            return password_verify($peppered, $storedHash);
        }

        // Legacy Bcrypt check ($2y$ or $2a$ or $2b$)
        if (str_starts_with($storedHash, '$2y$') || str_starts_with($storedHash, '$2a$') || str_starts_with($storedHash, '$2b$')) {
            return password_verify($password, $storedHash);
        }

        // Fallback standard verification
        return password_verify($password, $storedHash);
    }

    /**
     * Check if a hash needs to be upgraded/rehashed to Argon2id.
     */
    public static function needsRehash(string $storedHash): bool
    {
        if (empty($storedHash)) {
            return true;
        }
        // If it is not an Argon2id hash, it must be migrated
        if (!str_starts_with($storedHash, '$argon2id$')) {
            return true;
        }
        return password_needs_rehash($storedHash, PASSWORD_ARGON2ID, self::ARGON2_OPTIONS);
    }

    /**
     * Encrypt sensitive data using Authenticated Encryption (AES-256-GCM).
     * Output format: enc:<version>:gcm:<base64(iv)>:<base64(tag)>:<base64(ciphertext)>
     */
    public static function encrypt(?string $plaintext, ?string $version = null): ?string
    {
        if ($plaintext === null || $plaintext === '') {
            return $plaintext;
        }

        $version = $version ?? (string)Env::get('APP_KEY_ACTIVE', 'v1');
        $key = self::getEncryptionKey($version);
        $iv = random_bytes(self::GCM_IV_LENGTH);
        $tag = '';

        $ciphertext = openssl_encrypt(
            $plaintext,
            self::CIPHER,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            $version, // Authenticated Additional Data (AAD)
            self::GCM_TAG_LENGTH
        );

        if ($ciphertext === false) {
            throw new \RuntimeException("Encryption failure in AES-256-GCM engine.");
        }

        return sprintf(
            'enc:%s:gcm:%s:%s:%s',
            $version,
            base64_encode($iv),
            base64_encode($tag),
            base64_encode($ciphertext)
        );
    }

    /**
     * Decrypt data encrypted with Authenticated Encryption (AES-256-GCM).
     * If the string is not encrypted, returns the original string for graceful compatibility.
     */
    public static function decrypt(?string $payload): ?string
    {
        if ($payload === null || $payload === '') {
            return $payload;
        }

        if (!str_starts_with($payload, 'enc:')) {
            return $payload; // Unencrypted legacy plaintext
        }

        $parts = explode(':', $payload);
        if (count($parts) !== 6 || $parts[0] !== 'enc' || $parts[2] !== 'gcm') {
            return $payload;
        }

        $version = $parts[1];
        $iv = base64_decode($parts[3], true);
        $tag = base64_decode($parts[4], true);
        $ciphertext = base64_decode($parts[5], true);

        if ($iv === false || $tag === false || $ciphertext === false) {
            return null;
        }

        $key = self::getEncryptionKey($version);

        $plaintext = openssl_decrypt(
            $ciphertext,
            self::CIPHER,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            $version // Authenticated Additional Data (AAD)
        );

        return $plaintext !== false ? $plaintext : null;
    }

    /**
     * Compute a keyed blind index (HMAC-SHA256) for confidential fields.
     * Allows equality queries (e.g. search by phone) without storing or exposing plaintext.
     */
    public static function blindIndex(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }
        $normalized = self::normalizeSearchTerm($value);
        $key = self::getBlindIndexKey();
        return hash_hmac('sha256', $normalized, $key);
    }

    /**
     * Check if a string is encrypted in the enc:v<N>:gcm:... format.
     */
    public static function isEncrypted(?string $value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }
        return str_starts_with($value, 'enc:');
    }

    /**
     * Normalize search terms for consistent blind indexing.
     */
    public static function normalizeSearchTerm(string $value): string
    {
        return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', trim($value)));
    }
}
