<?php

declare(strict_types=1);

namespace App\Services;

class AuditService
{
    private static string $storagePath = '';

    private static function getStoragePath(): string
    {
        if (empty(self::$storagePath)) {
            self::$storagePath = dirname(__DIR__, 2) . '/storage/data/audit_logs.json';
        }
        return self::$storagePath;
    }

    /**
     * Keys strictly forbidden from being logged in audit logs.
     */
    private const SENSITIVE_KEY_PATTERN = '/^(password|passwd|new_password|current_password|confirm_password|password_hash|hash|token|reset_token|otp|code|initial_otp|secret|pepper|key|auth_token|access_token|id_token|session_id|credential)$/i';

    /**
     * Recursively redact sensitive values from audit dictionaries.
     */
    public static function redactSensitiveData(?array $data): ?array
    {
        if ($data === null) {
            return null;
        }

        $clean = [];
        foreach ($data as $key => $val) {
            if (is_string($key) && preg_match(self::SENSITIVE_KEY_PATTERN, $key)) {
                $clean[$key] = '[REDACTED]';
                continue;
            }

            if (is_array($val)) {
                $clean[$key] = self::redactSensitiveData($val);
            } elseif (is_string($val)) {
                // Redact values containing password hashes or secrets
                if (str_starts_with($val, '$argon2') || str_starts_with($val, '$2y$') || str_starts_with($val, '$2a$')) {
                    $clean[$key] = '[REDACTED_HASH]';
                } elseif (strlen($val) === 64 && ctype_xdigit($val) && (str_contains(strtolower((string)$key), 'key') || str_contains(strtolower((string)$key), 'pepper') || str_contains(strtolower((string)$key), 'token'))) {
                    $clean[$key] = '[REDACTED_SECRET]';
                } else {
                    $clean[$key] = $val;
                }
            } else {
                $clean[$key] = $val;
            }
        }

        return $clean;
    }

    /**
     * Sanitize note string against accidental secret leakage.
     */
    public static function redactNoteText(?string $notes): ?string
    {
        if ($notes === null || $notes === '') {
            return $notes;
        }

        // Redact Argon2 or Bcrypt hash representations from free-text notes
        $notes = preg_replace('/\$argon2[^\s]+/i', '[REDACTED_HASH]', $notes);
        $notes = preg_replace('/\$2[yab]\$[^\s]+/i', '[REDACTED_HASH]', $notes);

        return $notes;
    }

    /**
     * Appends an immutable audit log entry with strict credential redaction.
     */
    public static function log(
        string $action,
        string $resource,
        ?string $targetId = null,
        ?string $targetName = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $notes = null,
        ?array $userContext = null
    ): array {
        $path = self::getStoragePath();
        $logs = self::all();

        $currentUser = $userContext ?? AuthService::getCurrentUser();

        $entry = [
            'id' => 'audit_' . (count($logs) + 1001) . '_' . substr(md5(uniqid((string)mt_rand(), true)), 0, 6),
            'user_id' => $currentUser['id'] ?? 'system',
            'user_name' => $currentUser['name_bn'] ?? ($currentUser['name_en'] ?? 'সিস্টেম সার্ভিস'),
            'role' => $currentUser['role'] ?? 'guest',
            'action' => $action,
            'resource' => $resource,
            'target_id' => $targetId,
            'target_name' => $targetName,
            'old_values' => self::redactSensitiveData($oldValues),
            'new_values' => self::redactSensitiveData($newValues),
            'notes' => self::redactNoteText($notes),
            'ip_address' => \App\Core\Session::getClientIp(),
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? 'Internal Agent/CLI', 0, 200),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // Prepend newest first
        array_unshift($logs, $entry);

        // Keep maximum 1000 logs in JSON storage for performant execution
        if (count($logs) > 1000) {
            $logs = array_slice($logs, 0, 1000);
        }

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($path, json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
        return $entry;
    }

    /**
     * Retrieve all immutable audit logs.
     */
    public static function all(): array
    {
        $path = self::getStoragePath();
        if (!file_exists($path)) {
            return [];
        }
        $data = json_decode((string)file_get_contents($path), true);
        return is_array($data) ? $data : [];
    }

    /**
     * Filter audit logs by resource or action.
     */
    public static function getLogs(int $limit = 50, ?string $resource = null, ?string $action = null): array
    {
        $all = self::all();
        if ($resource !== null) {
            $all = array_filter($all, fn($log) => ($log['resource'] ?? '') === $resource);
        }
        if ($action !== null) {
            $all = array_filter($all, fn($log) => ($log['action'] ?? '') === $action);
        }
        return array_slice(array_values($all), 0, $limit);
    }

    /**
     * Hard-deletion of audit logs is forbidden by policy.
     */
    public static function deleteLogs(): void
    {
        throw new \RuntimeException("Audit log deletion is strictly prohibited by SPS Security Policy.");
    }
}
