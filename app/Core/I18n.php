<?php

namespace App\Core;

class I18n
{
    private static string $locale = 'bn';
    private static array $translations = [];
    private static array $config = [];
    private static string $currentPath = '';

    public static function init(array $config, ?string $urlLocale = null, string $currentPath = ''): void
    {
        self::$config = $config;
        self::$currentPath = $currentPath;

        $supported = array_keys($config['supported'] ?? ['bn' => [], 'en' => []]);

        // Explicit URL locale takes highest precedence
        if ($urlLocale !== null && in_array($urlLocale, $supported, true)) {
            self::setLocale($urlLocale);
            return;
        }

        // Second: check session
        $sessionLocale = Session::get('locale');
        if ($sessionLocale && in_array($sessionLocale, $supported, true)) {
            self::setLocale($sessionLocale);
            return;
        }

        // Third: check cookie
        $cookieName = $config['cookie_name'] ?? 'sps_locale';
        if (isset($_COOKIE[$cookieName]) && in_array($_COOKIE[$cookieName], $supported, true)) {
            self::setLocale($_COOKIE[$cookieName]);
            return;
        }

        // Default: always Bangla (no browser-language guessing as required by spec)
        self::setLocale($config['default'] ?? 'bn');
    }

    public static function setLocale(string $locale): void
    {
        $supported = array_keys(self::$config['supported'] ?? ['bn' => [], 'en' => []]);
        if (!in_array($locale, $supported, true)) {
            $locale = self::$config['default'] ?? 'bn';
        }

        self::$locale = $locale;
        Session::set('locale', $locale);

        // Set persistent cookie for visitor preference
        $cookieName = self::$config['cookie_name'] ?? 'sps_locale';
        $lifetime = time() + ((int)(self::$config['cookie_lifetime_days'] ?? 365) * 86400);
        if (!headers_sent()) {
            setcookie($cookieName, $locale, [
                'expires' => $lifetime,
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                'httponly' => false, // readable by JS for localStorage sync
                'samesite' => 'Lax'
            ]);
        }
    }

    public static function getLocale(): string
    {
        return self::$locale;
    }

    public static function getSupported(): array
    {
        return self::$config['supported'] ?? [];
    }

    public static function isLocale(string $locale): bool
    {
        return self::$locale === $locale;
    }

    /**
     * Load language file if not already loaded and return translation
     */
    public static function get(string $key, string $default = '', array $replace = []): string
    {
        $parts = explode('.', $key);
        $file = array_shift($parts);

        if (!isset(self::$translations[self::$locale][$file])) {
            self::loadFile($file);
        }

        $value = self::$translations[self::$locale][$file] ?? null;

        foreach ($parts as $segment) {
            if (is_array($value) && isset($value[$segment])) {
                $value = $value[$segment];
            } else {
                $value = null;
                break;
            }
        }

        // Fallback to default locale if not found
        if ($value === null && self::$locale !== 'bn') {
            if (!isset(self::$translations['bn'][$file])) {
                self::loadFile($file, 'bn');
            }
            $fallbackValue = self::$translations['bn'][$file] ?? null;
            foreach ($parts as $segment) {
                if (is_array($fallbackValue) && isset($fallbackValue[$segment])) {
                    $fallbackValue = $fallbackValue[$segment];
                } else {
                    $fallbackValue = null;
                    break;
                }
            }
            if ($fallbackValue !== null) {
                $value = $fallbackValue;
            }
        }

        if (!is_string($value)) {
            $value = $default !== '' ? $default : $key;
        }

        // Parameter replacement
        if (!empty($replace)) {
            foreach ($replace as $k => $v) {
                $value = str_replace(':' . $k, (string)$v, $value);
            }
        }

        return $value;
    }

    private static function loadFile(string $file, ?string $locale = null): void
    {
        $targetLocale = $locale ?: self::$locale;
        $path = dirname(__DIR__, 2) . "/lang/{$targetLocale}/{$file}.php";

        if (file_exists($path)) {
            self::$translations[$targetLocale][$file] = require $path;
        } else {
            self::$translations[$targetLocale][$file] = [];
        }
    }

    /**
     * Generate URL for switching to the opposite/target language on the same page
     */
    public static function getSwitchUrl(string $targetLocale): string
    {
        $currentUri = self::$currentPath;
        if (empty($currentUri)) {
            $currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        }

        $trimmed = trim($currentUri, '/');

        // Check if starts with a supported locale
        $supported = array_keys(self::$config['supported'] ?? ['bn' => [], 'en' => []]);
        $pattern = '#^(' . implode('|', $supported) . ')(/.*)?$#';

        if (preg_match($pattern, $trimmed, $matches)) {
            $rest = $matches[2] ?? '';
            return '/' . $targetLocale . $rest;
        }

        if (empty($trimmed)) {
            return '/' . $targetLocale;
        }

        return '/' . $targetLocale . '/' . $trimmed;
    }
}
