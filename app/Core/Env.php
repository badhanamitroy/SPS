<?php

declare(strict_types=1);

namespace App\Core;

class Env
{
    private static bool $loaded = false;
    private static array $variables = [];

    /**
     * Load environment variables from .env file if it exists.
     */
    public static function load(?string $baseDir = null): void
    {
        if (self::$loaded) {
            return;
        }

        $baseDir = $baseDir ?? dirname(__DIR__, 2);
        $envFile = $baseDir . '/.env';

        if (file_exists($envFile) && is_readable($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#')) {
                    continue;
                }

                $parts = explode('=', $line, 2);
                if (count($parts) === 2) {
                    $key = trim($parts[0]);
                    $val = trim($parts[1]);

                    // Strip surrounding quotes
                    if ((str_starts_with($val, '"') && str_ends_with($val, '"')) ||
                        (str_starts_with($val, "'") && str_ends_with($val, "'"))) {
                        $val = substr($val, 1, -1);
                    }

                    self::$variables[$key] = $val;
                    if (!isset($_ENV[$key])) {
                        $_ENV[$key] = $val;
                    }
                    if (!isset($_SERVER[$key])) {
                        $_SERVER[$key] = $val;
                    }
                }
            }
        }

        self::$loaded = true;
    }

    /**
     * Get an environment variable with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::load();

        if (isset(self::$variables[$key])) {
            return self::parseValue(self::$variables[$key]);
        }

        $envVal = getenv($key);
        if ($envVal !== false) {
            return self::parseValue($envVal);
        }

        return $default;
    }

    /**
     * Set or override an environment variable at runtime.
     */
    public static function set(string $key, mixed $val): void
    {
        self::load();
        self::$variables[$key] = (string)$val;
        $_ENV[$key] = (string)$val;
        $_SERVER[$key] = (string)$val;
        putenv("{$key}=" . (string)$val);
    }

    private static function parseValue(string $val): mixed
    {
        $lower = strtolower($val);
        if ($lower === 'true') return true;
        if ($lower === 'false') return false;
        if ($lower === 'null') return null;
        if (is_numeric($val) && !str_starts_with($val, '0x')) {
            return str_contains($val, '.') ? (float)$val : (int)$val;
        }
        return $val;
    }
}
