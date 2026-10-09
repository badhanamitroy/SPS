<?php

declare(strict_types=1);

namespace App\Core;

class FileUploader
{
    public const ALLOWED_IMAGE_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    public const MAX_IMAGE_SIZE = 3145728; // 3 MB

    /**
     * Validate and securely save an uploaded image file.
     *
     * @param array $file $_FILES entry
     * @param string $destinationDir Target directory (absolute path)
     * @param int $maxSizeBytes Maximum allowed size
     * @return array [success => bool, path => string, error => string]
     */
    public static function uploadImage(array $file, string $destinationDir, int $maxSizeBytes = self::MAX_IMAGE_SIZE, string $prefix = 'img_'): array
    {
        $isUploaded = isset($file['tmp_name']) && (is_uploaded_file($file['tmp_name']) || (PHP_SAPI === 'cli' && file_exists($file['tmp_name'])));
        if (empty($file) || !$isUploaded) {
            return ['success' => false, 'error' => 'No valid uploaded file found.'];
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Upload error code: ' . ($file['error'] ?? 'unknown')];
        }

        // Check file size
        $size = filesize($file['tmp_name']);
        if ($size === false || $size > $maxSizeBytes) {
            return ['success' => false, 'error' => 'File size exceeds maximum allowed limit (3 MB).'];
        }

        // Inspect actual MIME type from content using fileinfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? finfo_file($finfo, $file['tmp_name']) : false;
        if ($finfo) {
            finfo_close($finfo);
        }

        if (!$mime || !isset(self::ALLOWED_IMAGE_MIMES[$mime])) {
            return ['success' => false, 'error' => 'Invalid file format. Only JPG, PNG, and WebP images are allowed (SVG and executables are blocked).'];
        }

        $canonicalExt = self::ALLOWED_IMAGE_MIMES[$mime];

        // Content scan for embedded executable payloads
        $contentSample = @file_get_contents($file['tmp_name'], false, null, 0, 4096);
        if ($contentSample !== false) {
            $lower = strtolower($contentSample);
            if (str_contains($lower, '<?php') || str_contains($lower, '<?=') || str_contains($lower, '<script') || str_contains($lower, '<svg')) {
                return ['success' => false, 'error' => 'Security violation: Malicious payload detected inside file body.'];
            }
        }

        if (!is_dir($destinationDir)) {
            @mkdir($destinationDir, 0755, true);
        }

        // Generate cryptographically random filename
        $safeFilename = $prefix . bin2hex(random_bytes(16)) . '.' . $canonicalExt;
        $targetPath = rtrim($destinationDir, '/\\') . '/' . $safeFilename;

        $saved = (PHP_SAPI === 'cli')
            ? @copy($file['tmp_name'], $targetPath)
            : move_uploaded_file($file['tmp_name'], $targetPath);

        if (!$saved) {
            return ['success' => false, 'error' => 'Failed to save uploaded file to destination.'];
        }

        // Set restrictive file permissions (read/write for owner, read for group/others, non-executable)
        @chmod($targetPath, 0644);

        return [
            'success' => true,
            'filename' => $safeFilename,
            'absolute_path' => $targetPath,
            'mime' => $mime,
            'size' => $size,
        ];
    }
}
