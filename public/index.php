<?php

declare(strict_types=1);

// Measure start time
define('SPS_START_TIME', microtime(true));

// Serve static assets directly when using PHP built-in server
if (php_sapi_name() === 'cli-server') {
    $requestedPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
    $filePath = __DIR__ . $requestedPath;
    if ($requestedPath !== '/' && is_file($filePath)) {
        return false;
    }
    // Also support root-level Media folder if requested directly via /media/...
    if (str_starts_with(strtolower($requestedPath), '/media/')) {
        $rootMediaPath = dirname(__DIR__) . '/Media' . substr($requestedPath, 6);
        if (is_file($rootMediaPath)) {
            $mime = mime_content_type($rootMediaPath) ?: 'application/octet-stream';
            header("Content-Type: {$mime}");
            readfile($rootMediaPath);
            exit;
        }
    }
}

// Load Composer Autoloader
$autoloader = dirname(__DIR__) . '/vendor/autoload.php';

if (!file_exists($autoloader)) {
    die("Autoloader not found. Please run 'composer install' in the project root.");
}

require_once $autoloader;

// Bootstrap & Run SPS Application
$app = new \App\Core\App(dirname(__DIR__));
$app->run();
