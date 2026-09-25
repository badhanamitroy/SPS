<?php

use App\Core\I18n;
use App\Core\Session;

if (!function_exists('__')) {
    /**
     * Translate a given key in format 'file.key'
     */
    function __(string $key, string $default = '', array $replace = []): string
    {
        return I18n::get($key, $default, $replace);
    }
}

if (!function_exists('config')) {
    /**
     * Get a configuration value using dot notation
     */
    function config(string $key, $default = null)
    {
        return \App\Core\App::config($key, $default);
    }
}

if (!function_exists('current_locale')) {
    function current_locale(): string
    {
        return I18n::getLocale();
    }
}

if (!function_exists('url')) {
    /**
     * Generate localized URL
     */
    function url(string $path = '', ?string $locale = null): string
    {
        $lang = $locale ?: current_locale();
        $trimmed = trim($path, '/');
        
        if (empty($trimmed)) {
            return '/' . $lang;
        }

        // If the path already starts with bn/ or en/, do not double prefix
        if (preg_match('#^(bn|en)(/.*)?$#', $trimmed)) {
            return '/' . $trimmed;
        }

        return '/' . $lang . '/' . $trimmed;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return '/' . ltrim($path, '/');
    }
}

if (!function_exists('e')) {
    /**
     * Escape HTML output safely
     */
    function e(?string $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('route_switch_url')) {
    /**
     * Generate URL for switching to the target locale for the current page
     */
    function route_switch_url(string $targetLocale): string
    {
        return I18n::getSwitchUrl($targetLocale);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return Session::getCsrfToken();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
    }
}
