<?php

declare(strict_types=1);

namespace App\Services;

class QrCodeService
{
    /**
     * Get the absolute, canonical verification URL for a member
     */
    public static function getVerificationUrl(string $memberCode, string $locale = 'bn'): string
    {
        $code = trim($memberCode);
        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? '127.0.0.1:8000';
        $base = rtrim(config('app.url', "{$scheme}://{$host}"), '/');

        // Prefer dynamic request host if running locally
        if (!empty($_SERVER['HTTP_HOST'])) {
            $base = "{$scheme}://{$_SERVER['HTTP_HOST']}";
        }

        $loc = in_array($locale, ['bn', 'en'], true) ? $locale : 'bn';
        return "{$base}/{$loc}/membership/verify?code=" . rawurlencode($code);
    }

    /**
     * Get QR Code rendering configuration and metadata
     */
    public static function getQrConfig(string $memberCode, string $locale = 'bn'): array
    {
        $url = self::getVerificationUrl($memberCode, $locale);

        return [
            'url' => $url,
            'member_code' => $memberCode,
            'level' => 'H', // High error correction (30% recovery)
            'logo' => asset('assets/images/brand/sps-logo.png'),
            'logo_white' => asset('assets/images/brand/sps-logo-white.png'),
            'size' => 200,
            'logo_size' => 44,
        ];
    }
}
