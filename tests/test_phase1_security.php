<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\App;
use App\Core\Env;
use App\Core\FileUploader;
use App\Core\HtmlSanitizer;
use App\Core\I18n;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\Session;
use App\Core\View;
use App\Services\AuthService;
use App\Services\LibraryService;

$baseDir = dirname(__DIR__);
$app = new App($baseDir);

$langConfig = require $baseDir . '/config/languages.php';
I18n::init($langConfig, 'bn', '/bn');
View::init($baseDir . '/app/Views');

$router = new Router();
require $baseDir . '/routes/web.php';

$totalTests = 0;
$passedTests = 0;

function assert_phase1(string $name, bool $condition, string $detail = ''): void
{
    global $totalTests, $passedTests;
    $totalTests++;
    if ($condition) {
        $passedTests++;
        echo "  [PASS] {$name}\n";
    } else {
        echo "  [FAIL] {$name}" . ($detail ? " — {$detail}" : "") . "\n";
    }
}

echo "====================================================\n";
echo "SPS Phase 1 Security Regression Test Suite\n";
echo "====================================================\n\n";

// ---------------------------------------------------------
// 1. Fail-closed PDF streaming
// ---------------------------------------------------------
echo "1. Fail-Closed PDF Streaming:\n";

// Slicing an invalid or non-existent book must return null, never the original path
$sliceResult = LibraryService::getOrGeneratePreviewPdf('non_existent_book_xyz', 1, 10);
assert_phase1("Slicing non-existent book returns null", $sliceResult === null);

// If slicing fails for a partial access book, streamPdf must return HTTP 503 fail-closed
Session::start();
Session::set('user_simulated_role', 'viewer');

// sps-samachar-feb is configured as public + partial preview
$streamReq = new Request('GET', '/bn/library/stream/sps-samachar-feb');
$streamRes = $router->dispatch($streamReq);

if ($streamRes->getStatusCode() === 503) {
    assert_phase1("Failed preview returns 503 fail-closed response", true);
    assert_phase1("Response body contains fail-closed bilingual notice", str_contains((string)$streamRes->getContent(), '503 Service Unavailable'));
} else {
    // If preview slicer succeeded, it must be 200 with preview PDF
    assert_phase1("Stream returns valid response code (200 preview or 503 fail-closed)", in_array($streamRes->getStatusCode(), [200, 503], true));
}

// ---------------------------------------------------------
// 2. Role Simulator Disabled when APP_DEBUG=false
// ---------------------------------------------------------
echo "\n2. Role Simulator Disabled When APP_DEBUG=false:\n";

$originalDebug = Env::get('APP_DEBUG', 'true');
Env::set('APP_DEBUG', 'false');

// POST /{lang}/library/role must return 403 when APP_DEBUG=false
$reqRolePost = new Request('POST', '/bn/library/role', [], ['role' => 'admin']);
$resRolePost = $router->dispatch($reqRolePost);
assert_phase1("POST /library/role returns 403 when APP_DEBUG=false", $resRolePost->getStatusCode() === 403);

// POST /{lang}/admin/switch-user must return 403 when APP_DEBUG=false
// Authenticate admin session
Session::set('auth_admin_user_id', 'usr_admin_01');
$reqSwitchUser = new Request('POST', '/bn/admin/switch-user', [], ['user_id' => 'usr_mod_01']);
$resSwitchUser = $router->dispatch($reqSwitchUser);
assert_phase1("POST /admin/switch-user returns 403 when APP_DEBUG=false", $resSwitchUser->getStatusCode() === 403);
Session::forget('auth_admin_user_id');

// LibraryService::getCurrentRole() must ignore simulated role when APP_DEBUG=false
Session::set('user_simulated_role', 'admin');
$currentRole = LibraryService::getCurrentRole();
assert_phase1("LibraryService::getCurrentRole() ignores simulated role when APP_DEBUG=false", $currentRole !== 'admin');

// Restore original debug setting
Env::set('APP_DEBUG', $originalDebug);
Session::forget('user_simulated_role');

// ---------------------------------------------------------
// 3. SVG Rejected on Image / Payment Screenshot Upload
// ---------------------------------------------------------
echo "\n3. SVG Rejected on File Upload:\n";

$tempSvgPath = sys_get_temp_dir() . '/test_attack_' . bin2hex(random_bytes(4)) . '.svg';
file_put_contents($tempSvgPath, '<svg xmlns="http://www.w3.org/2000/svg"><script>alert("xss")</script></svg>');

$fakeSvgFile = [
    'name' => 'malicious.svg',
    'type' => 'image/svg+xml',
    'tmp_name' => $tempSvgPath,
    'error' => UPLOAD_ERR_OK,
    'size' => filesize($tempSvgPath),
];

$uploadResult = FileUploader::uploadImage($fakeSvgFile, $baseDir . '/storage/data');
assert_phase1("FileUploader::uploadImage rejects SVG file", $uploadResult['success'] === false);
assert_phase1("FileUploader returns invalid format error message", !empty($uploadResult['error']));

if (file_exists($tempSvgPath)) {
    @unlink($tempSvgPath);
}

// ---------------------------------------------------------
// 4. CSRF Enforcement Without Token
// ---------------------------------------------------------
echo "\n4. CSRF Enforcement Without Token:\n";

$_SERVER['HTTP_X_TEST_ENFORCE_CSRF'] = '1';
Session::start();
Session::regenerate();

// Attempt POST without CSRF token
$reqNoCsrf = new Request('POST', '/bn/admin/login', [], ['identifier' => 'admin@sps.org', 'password' => 'test']);
$resNoCsrf = $router->dispatch($reqNoCsrf);
assert_phase1("POST without CSRF token returns HTTP 403 Forbidden", $resNoCsrf->getStatusCode() === 403);

unset($_SERVER['HTTP_X_TEST_ENFORCE_CSRF']);

// ---------------------------------------------------------
// 5. HtmlSanitizer Strips <script> and Inline Handlers
// ---------------------------------------------------------
echo "\n5. HtmlSanitizer Stripping Malicious HTML:\n";

$dirty1 = '<script>alert("xss")</script><p>Sanatan Philosophy</p>';
$clean1 = HtmlSanitizer::clean($dirty1);
assert_phase1("HtmlSanitizer strips <script> tags", !str_contains($clean1, '<script>') && str_contains($clean1, '<p>Sanatan Philosophy</p>'));

$dirty2 = '<img src="valid.jpg" onerror="alert(document.cookie)" onload="alert(1)">';
$clean2 = HtmlSanitizer::clean($dirty2);
assert_phase1("HtmlSanitizer strips onerror/onload attributes", !str_contains($clean2, 'onerror') && !str_contains($clean2, 'onload'));

$dirty3 = '<a href="javascript:alert(1)">Click Me</a>';
$clean3 = HtmlSanitizer::clean($dirty3);
assert_phase1("HtmlSanitizer strips javascript: URIs", !str_contains($clean3, 'javascript:'));

$dirty4 = '<iframe src="https://evil.com"></iframe>';
$clean4 = HtmlSanitizer::clean($dirty4);
assert_phase1("HtmlSanitizer removes <iframe> tags", !str_contains($clean4, '<iframe'));

// ---------------------------------------------------------
// 6. Dashboard IDOR Blocked
// ---------------------------------------------------------
echo "\n6. Dashboard IDOR Blocked:\n";

// Ensure guest cannot access dashboard via ?as=
Session::forget('current_member_code');
Session::forget('member_id');
Session::forget('member_user');

$reqIdorGuest = new Request('GET', '/bn/membership/dashboard', ['as' => 'SPS-000872']);
$resIdorGuest = $router->dispatch($reqIdorGuest);
assert_phase1("Unauthenticated request with ?as= redirects to login", $resIdorGuest->getStatusCode() === 302);
assert_phase1("Session is not hijacked with ?as= member code", Session::get('current_member_code') !== 'SPS-000872');

// ---------------------------------------------------------
// Results
// ---------------------------------------------------------
echo "\n----------------------------------------------------\n";
echo "Total Tests: {$totalTests} | Passed: {$passedTests} | Failed: " . ($totalTests - $passedTests) . "\n";
echo "====================================================\n";

if ($totalTests !== $passedTests) {
    exit(1);
}
exit(0);
