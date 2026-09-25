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
