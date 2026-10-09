<?php

declare(strict_types=1);

namespace App\Core;

class Session
{
    private static bool $started = false;
    private const DEFAULT_IDLE_TIMEOUT = 1800; // 30 minutes
    private const ABSOLUTE_TIMEOUT = 43200;    // 12 hours

    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            self::checkTimeout();
            return;
        }

        if (headers_sent()) {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                @session_start();
            }
            self::$started = true;
            self::checkTimeout();
            return;
        }

        $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $trustedProxies = array_filter(array_map('trim', explode(',', (string)Env::get('TRUSTED_PROXIES', ''))));
        $isTrustedProxy = !empty($trustedProxies) && in_array($remoteAddr, $trustedProxies, true);

        // Determine if HTTPS is active (explicit trusted proxy check prevents spoofed SSL termination)
        $isHttps = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on')
            || ($isTrustedProxy && isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
            || (bool)Env::get('SESSION_SECURE_COOKIE', false);

        // Configure secure cookie parameters (HttpOnly, SameSite, Secure)
        $cookieParams = [
            'lifetime' => 0, // In-memory session cookie (expires on browser close)
            'path' => '/',
            'domain' => (string)Env::get('SESSION_COOKIE_DOMAIN', ''),
            'secure' => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax'
        ];

        session_set_cookie_params($cookieParams);
        @session_start();
        self::$started = true;

        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        if (empty($_SESSION['_session_created_at'])) {
            $_SESSION['_session_created_at'] = time();
        }

        self::checkTimeout();
    }

    /**
     * Check idle and absolute session timeouts.
     */
    private static function checkTimeout(): void
    {
        $now = time();
        $idleTimeout = (int)Env::get('SESSION_LIFETIME', self::DEFAULT_IDLE_TIMEOUT);

        // Check idle inactivity timeout
        if (isset($_SESSION['_last_activity']) && ($now - $_SESSION['_last_activity'] > $idleTimeout)) {
            // Preserve flash message if any for the timeout notification
            $flash = $_SESSION['_flash'] ?? [];
            self::destroy();
            self::start();
            $_SESSION['_flash'] = $flash;
            self::setFlash('warning', 'নিরাপত্তার স্বার্থে দীর্ঘক্ষণ নিষ্ক্রিয় থাকায় সেশনের মেয়াদ শেষ হয়েছে। অনুগ্রহ করে পুনরায় লগইন করুন। (Session expired due to inactivity. Please log in again.)');
            return;
        }

        // Check absolute maximum session lifetime
        if (isset($_SESSION['_session_created_at']) && ($now - $_SESSION['_session_created_at'] > self::ABSOLUTE_TIMEOUT)) {
            self::regenerate(true);
            $_SESSION['_session_created_at'] = $now;
        }

        $_SESSION['_last_activity'] = $now;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
        $_SESSION['_last_activity'] = time();
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function forget(string $key): void
    {
        self::remove($key);
    }

    public static function setFlash(string $key, mixed $value): void
    {
        self::start();
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        self::start();
        if (isset($_SESSION['_flash'][$key])) {
            $val = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]);
            return $val;
        }
        return $default;
    }

    public static function hasFlash(string $key): bool
    {
        self::start();
        return isset($_SESSION['_flash'][$key]);
    }

    public static function getCsrfToken(): string
    {
        self::start();
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    public static function validateCsrfToken(?string $token): bool
    {
        self::start();
        if (empty($token) || empty($_SESSION['_csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['_csrf_token'], $token);
    }

    /**
     * Regenerate session ID to prevent session fixation.
     */
    public static function regenerate(bool $deleteOldSession = true): void
    {
        self::start();
        if (session_status() === PHP_SESSION_ACTIVE && !headers_sent()) {
            session_regenerate_id($deleteOldSession);
        }
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['_session_created_at'] = time();
        $_SESSION['_last_activity'] = time();
    }

    /**
     * Completely destroy the current session and invalidate cookies.
     */
    public static function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];

            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'] ?? '/',
                    $params['domain'] ?? '',
                    $params['secure'] ?? false,
                    $params['httponly'] ?? true
                );
            }

            @session_destroy();
        }
        self::$started = false;
    }

    /**
     * Resolve the client IP address strictly, without blindly trusting spoofable headers.
     */
    public static function getClientIp(): string
    {
        $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $trustedProxies = array_filter(array_map('trim', explode(',', (string)Env::get('TRUSTED_PROXIES', ''))));

        if (!empty($trustedProxies) && in_array($remoteAddr, $trustedProxies, true)) {
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ips = array_map('trim', explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
                if (!empty($ips[0]) && filter_var($ips[0], FILTER_VALIDATE_IP)) {
                    return $ips[0];
                }
            }
        }

        return $remoteAddr;
    }
}
