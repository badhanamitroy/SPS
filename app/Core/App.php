<?php

namespace App\Core;

class App
{
    private static array $configs = [];
    private Router $router;
    private Request $request;

    public function __construct(string $baseDir)
    {
        // Load Environment Configuration
        Env::load($baseDir);

        // Load configuration files
        $configDir = $baseDir . '/config';
        self::$configs['app'] = require $configDir . '/app.php';
        self::$configs['languages'] = require $configDir . '/languages.php';
        self::$configs['database'] = require $configDir . '/database.php';

        // Configure error reporting
        if (self::$configs['app']['debug'] ?? false) {
            ini_set('display_errors', '1');
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', '0');
            error_reporting(0);
        }

        date_default_timezone_set(self::$configs['app']['timezone'] ?? 'Asia/Dhaka');

        // Initialize Session
        Session::start();

        // Initialize Request
        $this->request = new Request();

        // Extract potential locale from URL
        $segments = $this->request->getSegments();
        $firstSegment = $segments[0] ?? null;
        $supportedLocales = array_keys(self::$configs['languages']['supported'] ?? []);
        $urlLocale = in_array($firstSegment, $supportedLocales, true) ? $firstSegment : null;

        // Initialize I18n
        I18n::init(self::$configs['languages'], $urlLocale, $this->request->getPath());

        // Initialize Views
        View::init($baseDir . '/app/Views');

        // Initialize Router
        $this->router = new Router();
        $this->loadRoutes($baseDir . '/routes/web.php');
    }

    public static function config(string $key, $default = null)
    {
        $parts = explode('.', $key);
        $file = array_shift($parts);

        if (!isset(self::$configs[$file])) {
            return $default;
        }

        $value = self::$configs[$file];
        foreach ($parts as $segment) {
            if (is_array($value) && isset($value[$segment])) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }

        return $value;
    }

    private function loadRoutes(string $routesFile): void
    {
        $router = $this->router;
        if (file_exists($routesFile)) {
            require $routesFile;
        }
    }

    public function run(): void
    {
        try {
            $response = $this->router->dispatch($this->request);
            $response->send();
        } catch (\Throwable $e) {
            $this->handleException($e);
        }
    }

    private function handleException(\Throwable $e): void
    {
        $debug = self::$configs['app']['debug'] ?? false;
        http_response_code(500);

        if ($debug) {
            echo '<div style="background:#FFF3CD; color:#856404; padding:20px; border:1px solid #FFEEBA; font-family:sans-serif; margin:20px; border-radius:4px;">';
            echo '<h3 style="margin-top:0;">Application Exception (Debug Mode)</h3>';
            echo '<p><strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>';
            echo '<pre style="background:#fff; padding:10px; overflow:auto;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            echo '</div>';
        } else {
            echo '<div style="text-align:center; padding:50px; font-family:sans-serif;">';
            echo '<h1>500 - Server Error</h1>';
            echo '<p>An unexpected error occurred. Please try again later.</p>';
            echo '</div>';
        }
    }
}
