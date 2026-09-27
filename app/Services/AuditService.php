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
     * Appends an immutable audit log entry.
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
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'notes' => $notes,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
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

        file_put_contents($path, json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
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
        throw new \SecurityException("Audit log deletion is strictly prohibited by SPS Security Policy.");
    }
}
