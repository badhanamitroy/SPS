<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

echo "====================================================\n";
echo "SPS Phase 1 Automated Verification Suite\n";
echo "====================================================\n\n";

$passed = 0;
$failed = 0;

function assert_test(string $name, bool $condition, string $detail = ''): void {
    global $passed, $failed;
    if ($condition) {
        echo "[PASS] {$name}\n";
        $passed++;
    } else {
        echo "[FAIL] {$name} - {$detail}\n";
        $failed++;
    }
}

// Test 1: Config loading
$appConfig = require dirname(__DIR__) . '/config/app.php';
$langConfig = require dirname(__DIR__) . '/config/languages.php';
assert_test("Config loading: app name defined", !empty($appConfig['name']));
assert_test("Config loading: default locale is 'bn'", ($langConfig['default'] ?? '') === 'bn');
assert_test("Config loading: supported locales include bn and en", isset($langConfig['supported']['bn'], $langConfig['supported']['en']));

// Test 2: Helper functions
assert_test("Helper function exists: __()", function_exists('__'));
assert_test("Helper function exists: url()", function_exists('url'));
assert_test("Helper function exists: e()", function_exists('e'));
assert_test("Helper function exists: current_locale()", function_exists('current_locale'));

// Test 3: I18n Engine
\App\Core\I18n::init($langConfig, 'bn', '/bn');
assert_test("I18n: Locale initialized to 'bn'", \App\Core\I18n::getLocale() === 'bn');
assert_test("I18n: Bengali translation lookup", str_contains(__('common.brand_name'), 'সনাতন'));
assert_test("I18n: Switch URL to 'en' generates '/en'", \App\Core\I18n::getSwitchUrl('en') === '/en');

\App\Core\I18n::init($langConfig, 'en', '/en');
assert_test("I18n: Locale switched to 'en'", \App\Core\I18n::getLocale() === 'en');
assert_test("I18n: English translation lookup", str_contains(__('common.brand_name'), 'Sanatan'));
assert_test("I18n: Switch URL to 'bn' generates '/bn'", \App\Core\I18n::getSwitchUrl('bn') === '/bn');

// Test 4: Router Dispatching (Internal simulation)
\App\Core\View::init(dirname(__DIR__) . '/app/Views');
$router = new \App\Core\Router();
require dirname(__DIR__) . '/routes/web.php';

// Simulate root '/'
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/';
\App\Core\I18n::init($langConfig, null, '/');
$reqRoot = new \App\Core\Request();
$resRoot = $router->dispatch($reqRoot);
assert_test("Router: '/' redirects with 302", $resRoot->getStatusCode() === 302);

// Simulate '/bn'
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/bn';
\App\Core\I18n::init($langConfig, 'bn', '/bn');
$reqBn = new \App\Core\Request();
$resBn = $router->dispatch($reqBn);
assert_test("Router: '/bn' returns 200", $resBn->getStatusCode() === 200);

// Capture output
ob_start();
$resBn->send();
$bnHtml = ob_get_clean();
assert_test("View: '/bn' contains Bengali brand title", str_contains($bnHtml, 'সনাতন দর্শন ও শাস্ত্র'));
assert_test("View: '/bn' contains language toggle", str_contains($bnHtml, 'lang-toggle'));
assert_test("View: '/bn' contains canonical link with /bn", str_contains($bnHtml, 'hreflang="bn"'));
assert_test("View: '/bn' contains scripture spotlight", str_contains($bnHtml, 'কর্মণ্যেবাধিকারস্তে'));

// Simulate '/en'
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/en';
\App\Core\I18n::init($langConfig, 'en', '/en');
$reqEn = new \App\Core\Request();
$resEn = $router->dispatch($reqEn);
assert_test("Router: '/en' returns 200", $resEn->getStatusCode() === 200);

ob_start();
$resEn->send();
$enHtml = ob_get_clean();
assert_test("View: '/en' contains English brand title", str_contains($enHtml, 'Sanatan Philosophy and Scripture'));
assert_test("View: '/en' contains English motto", str_contains($enHtml, 'Steadfast in Sanatan Unity'));

// Simulate '/bn/components'
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/bn/components';
\App\Core\I18n::init($langConfig, 'bn', '/bn/components');
$reqComp = new \App\Core\Request();
$resComp = $router->dispatch($reqComp);
assert_test("Router: '/bn/components' returns 200", $resComp->getStatusCode() === 200);

// Simulate non-existent route '/bn/random-page-xyz'
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/bn/random-page-xyz';
\App\Core\I18n::init($langConfig, 'bn', '/bn/random-page-xyz');
$req404 = new \App\Core\Request();
$res404 = $router->dispatch($req404);
assert_test("Router: 404 handler returns 404 status code", $res404->getStatusCode() === 404);

// Test 5: Assets existence & validity
$cssFiles = ['tokens.css', 'reset.css', 'typography.css', 'components.css', 'main.css'];
foreach ($cssFiles as $file) {
    $path = dirname(__DIR__) . "/public/assets/css/{$file}";
    assert_test("Asset: CSS file {$file} exists", file_exists($path) && filesize($path) > 100);
}

$jsFiles = ['i18n-toggle.js', 'components.js', 'main.js'];
foreach ($jsFiles as $file) {
    $path = dirname(__DIR__) . "/public/assets/js/{$file}";
    assert_test("Asset: JS file {$file} exists", file_exists($path) && filesize($path) > 50);
}

$svgFiles = ['assets/images/brand/sps-mark.svg', 'favicon.svg'];
foreach ($svgFiles as $file) {
    $path = dirname(__DIR__) . "/public/{$file}";
    $xmlValid = false;
    if (file_exists($path)) {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_file($path);
        $xmlValid = ($xml !== false);
        libxml_clear_errors();
    }
    assert_test("Asset: SVG file {$file} is valid XML", $xmlValid);
}

echo "\n----------------------------------------------------\n";
echo "Total Tests: " . ($passed + $failed) . " | Passed: {$passed} | Failed: {$failed}\n";
echo "====================================================\n";

if ($failed > 0) {
    exit(1);
}
