<?php

declare(strict_types=1);

namespace App\Core;

class RateLimiter
{
    private static string $storageDir = '';

    private static function getStorageDir(): string
    {
        if (empty(self::$storageDir)) {
            self::$storageDir = dirname(__DIR__, 2) . '/storage/cache/ratelimit';
            if (!is_dir(self::$storageDir)) {
                @mkdir(self::$storageDir, 0755, true);
            }
        }
        return self::$storageDir;
    }

    private static function getFilePath(string $key): string
    {
        $hash = hash('sha256', $key);
        return self::getStorageDir() . '/' . $hash . '.json';
    }

    /**
     * Check if a rate limit key has exceeded maximum allowed attempts.
     * Uses atomic shared lock to prevent race conditions during concurrent checks.
     */
    public static function tooManyAttempts(string $key, int $maxAttempts): bool
    {
        $file = self::getFilePath($key);
        if (!file_exists($file)) {
            return false;
        }

        $fp = @fopen($file, 'r');
        if (!$fp) {
            return false;
        }

        @flock($fp, LOCK_SH);
        $content = stream_get_contents($fp) ?: '';
        @flock($fp, LOCK_UN);
        @fclose($fp);

        if (empty($content)) {
            return false;
        }

        $data = json_decode($content, true);
        if (!is_array($data)) {
            return false;
        }

        $now = time();
        if ($now > ($data['reset_at'] ?? 0)) {
            @unlink($file);
            return false;
        }

        return ($data['attempts'] ?? 0) >= $maxAttempts;
    }

    /**
     * Record a hit/attempt against the specified key with ATOMIC file locking.
     * Prevents race conditions and lost increments under concurrent attack requests.
     */
    public static function hit(string $key, int $decaySeconds = 900): int
    {
        $file = self::getFilePath($key);
        $now = time();

        $fp = @fopen($file, 'c+');
        if (!$fp) {
            return 1;
        }

        @flock($fp, LOCK_EX);

        $content = stream_get_contents($fp) ?: '';
        $data = null;

        if (!empty($content)) {
            $parsed = json_decode($content, true);
            if (is_array($parsed) && ($parsed['reset_at'] ?? 0) > $now) {
                $data = $parsed;
            }
        }

        if ($data === null) {
            $data = [
                'attempts' => 0,
                'reset_at' => $now + $decaySeconds,
                'first_hit' => $now,
                'last_hit' => $now,
            ];
        }

        $data['attempts'] = ($data['attempts'] ?? 0) + 1;
        $data['last_hit'] = $now;

        @ftruncate($fp, 0);
        @rewind($fp);
        @fwrite($fp, json_encode($data, JSON_UNESCAPED_UNICODE));
        @fflush($fp);
        @flock($fp, LOCK_UN);
        @fclose($fp);

        return $data['attempts'];
    }

    /**
     * Clear all recorded attempts for a key (e.g. after successful authentication).
     */
    public static function resetAttempts(string $key): void
    {
        $file = self::getFilePath($key);
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    /**
     * Get remaining available attempts.
     */
    public static function retriesLeft(string $key, int $maxAttempts): int
    {
        $file = self::getFilePath($key);
        if (!file_exists($file)) {
            return $maxAttempts;
        }

        $fp = @fopen($file, 'r');
        if (!$fp) {
            return $maxAttempts;
        }

        @flock($fp, LOCK_SH);
        $content = stream_get_contents($fp) ?: '';
        @flock($fp, LOCK_UN);
        @fclose($fp);

        $data = !empty($content) ? json_decode($content, true) : null;
        if (!is_array($data) || time() > ($data['reset_at'] ?? 0)) {
            return $maxAttempts;
        }

        $remaining = $maxAttempts - ($data['attempts'] ?? 0);
        return max(0, $remaining);
    }

    /**
     * Seconds remaining until the key is unblocked.
     */
    public static function availableIn(string $key): int
    {
        $file = self::getFilePath($key);
        if (!file_exists($file)) {
            return 0;
        }

        $fp = @fopen($file, 'r');
        if (!$fp) {
            return 0;
        }

        @flock($fp, LOCK_SH);
        $content = stream_get_contents($fp) ?: '';
        @flock($fp, LOCK_UN);
        @fclose($fp);

        $data = !empty($content) ? json_decode($content, true) : null;
        if (!is_array($data)) {
            return 0;
        }

        $diff = ($data['reset_at'] ?? 0) - time();
        return max(0, $diff);
    }

    /**
     * Apply progressive delay to mitigate timing attacks and automated brute-force scripts.
     */
    public static function applyProgressiveDelay(string $key, int $threshold = 3): void
    {
        $file = self::getFilePath($key);
        if (!file_exists($file)) {
            return;
        }

        $fp = @fopen($file, 'r');
        if (!$fp) {
            return;
        }

        @flock($fp, LOCK_SH);
        $content = stream_get_contents($fp) ?: '';
        @flock($fp, LOCK_UN);
        @fclose($fp);

        $data = !empty($content) ? json_decode($content, true) : null;
        if (!is_array($data)) {
            return;
        }

        $attempts = $data['attempts'] ?? 0;
        if ($attempts >= $threshold) {
            // Delay 100ms per extra attempt over threshold, capped at 1.5s
            $delayMs = min(1500, ($attempts - $threshold + 1) * 150);
            usleep($delayMs * 1000);
        }
    }
}
