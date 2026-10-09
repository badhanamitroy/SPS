<?php
/**
 * Test Suite: T5 Security Headers & CSP Verification
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\App;
use App\Core\I18n;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\View;

$app = new App(dirname(__DIR__));
$langConfig = require dirname(__DIR__) . '/config/languages.php';
I18n::init($langConfig, 'bn', '/bn');
View::init(dirname(__DIR__) . '/app/Views');
$router = new Router();
require dirname(__DIR__) . '/routes/web.php';

$testsPassed = 0;
$totalTests = 0;

function assert_test(string $name, bool $condition, string $detail = '') {
    global $testsPassed, $totalTests;
    $totalTests++;
    if ($condition) {
        $testsPassed++;
        echo "  [PASS] {$name}\n";
    } else {
        echo "  [FAIL] {$name}" . ($detail ? " — {$detail}" : "") . "\n";
    }
}

echo "=== SPS T5 SECURITY HEADERS & CSP TEST SUITE ===\n\n";

echo "1. Testing Default Security Headers in HTTP Response:\n";
unset($_SERVER['HTTPS'], $_SERVER['SERVER_PORT'], $_SERVER['HTTP_X_FORWARDED_PROTO']);
$resHttp = new Response('test content', 200);
$headersHttp = $resHttp->getDefaultSecurityHeaders();

assert_test("X-Content-Type-Options is nosniff", ($headersHttp['X-Content-Type-Options'] ?? '') === 'nosniff');
assert_test("X-Frame-Options is SAMEORIGIN", ($headersHttp['X-Frame-Options'] ?? '') === 'SAMEORIGIN');
assert_test("Referrer-Policy is strict-origin-when-cross-origin", ($headersHttp['Referrer-Policy'] ?? '') === 'strict-origin-when-cross-origin');
assert_test("Permissions-Policy restricts camera, microphone, geolocation", ($headersHttp['Permissions-Policy'] ?? '') === 'camera=(), microphone=(), geolocation=()');
assert_test("HSTS is NOT emitted over plain HTTP", !isset($headersHttp['Strict-Transport-Security']));

echo "\n2. Testing Strict-Transport-Security on HTTPS:\n";
$_SERVER['HTTPS'] = 'on';
$resHttps = new Response('test content', 200);
$headersHttps = $resHttps->getDefaultSecurityHeaders();
assert_test("HSTS is emitted when HTTPS is active", isset($headersHttps['Strict-Transport-Security']) && str_contains($headersHttps['Strict-Transport-Security'], 'max-age=31536000'));
unset($_SERVER['HTTPS']);

echo "\n3. Testing Content-Security-Policy Directives:\n";
$csp = $headersHttp['Content-Security-Policy'] ?? '';
assert_test("CSP contains default-src 'self'", str_contains($csp, "default-src 'self'"));
assert_test("CSP allows accounts.google.com in script-src", str_contains($csp, "https://accounts.google.com"));
assert_test("CSP allows accounts.google.com in frame-src", str_contains($csp, "frame-src 'self' https://accounts.google.com"));
assert_test("CSP allows accounts.google.com in connect-src", str_contains($csp, "connect-src 'self' https://accounts.google.com"));
assert_test("CSP allows data: and blob: in img-src", str_contains($csp, "img-src") && str_contains($csp, "data:") && str_contains($csp, "blob:"));
assert_test("CSP allows blob: in worker-src for pdf.js", str_contains($csp, "worker-src") && str_contains($csp, "blob:"));
assert_test("CSP allows unsafe-inline for scripts and styles with TODO note", str_contains($csp, "script-src 'self' 'unsafe-inline'") && str_contains($csp, "style-src 'self' 'unsafe-inline'"));

echo "\n4. Verifying Functional Endpoints (Google Login, QR, PDF Reader, Print Card):\n";
// Admin Google Login page
$resAdminLogin = $router->dispatch(new Request('GET', '/bn/admin/login'));
assert_test("Admin login page returns HTTP 200", $resAdminLogin->getStatusCode() === 200);
assert_test("Admin login page contains Google GIS client script", str_contains($resAdminLogin->getContent(), 'https://accounts.google.com/gsi/client'));

// Member Google Auth page
$resMemberLogin = $router->dispatch(new Request('GET', '/bn/membership/login'));
assert_test("Member login page returns HTTP 200", $resMemberLogin->getStatusCode() === 200);

// Print Card page (QR canvas & print styles)
$resPrintCard = $router->dispatch(new Request('GET', '/bn/membership/card/print?code=SPS-000872'));
assert_test("Print card page returns HTTP 200", $resPrintCard->getStatusCode() === 200);
assert_test("Print card imports qrcode and sps-qr scripts", str_contains($resPrintCard->getContent(), 'qrcode.min.js') && str_contains($resPrintCard->getContent(), 'sps-qr.js'));

// PDF Reader page
$resReader = $router->dispatch(new Request('GET', '/bn/library/reader/sps-ramnavami'));
assert_test("PDF reader returns HTTP 200", $resReader->getStatusCode() === 200);
assert_test("PDF reader contains pdfjsLib and canvas elements", str_contains($resReader->getContent(), 'pdf-render-canvas') && str_contains($resReader->getContent(), 'pdfjsLib'));

echo "\n============================================\n";
echo "SUMMARY: {$testsPassed} / {$totalTests} tests passed.\n";
if ($testsPassed === $totalTests) {
    echo "ALL SECURITY HEADERS TESTS PASSED! ✓\n";
} else {
    echo "SOME TESTS FAILED!\n";
    exit(1);
}
