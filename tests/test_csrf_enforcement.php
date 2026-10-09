<?php
/**
 * Test Suite: T4 CSRF Enforcement for All POST Routes
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\App;
use App\Core\I18n;
use App\Core\Request;
use App\Core\Router;
use App\Core\Session;
use App\Core\View;

$app = new App(dirname(__DIR__));
$langConfig = require dirname(__DIR__) . '/config/languages.php';
I18n::init($langConfig, 'bn', '/bn');
View::init(dirname(__DIR__) . '/app/Views');
$router = new Router();
require dirname(__DIR__) . '/routes/web.php';

Session::start();
Session::regenerate();
$validToken = Session::getCsrfToken();

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

echo "=== SPS T4 CSRF ENFORCEMENT TEST SUITE ===\n\n";

echo "1. Testing Helper Output (csrf_field):\n";
$fieldHtml = csrf_field();
assert_test("csrf_field() produces _csrf input", str_contains($fieldHtml, 'name="_csrf"'));
assert_test("csrf_field() produces _token input", str_contains($fieldHtml, 'name="_token"'));
assert_test("csrf_field() embeds current valid token", str_contains($fieldHtml, $validToken));

echo "\n2. Testing POST Rejection with Invalid / Tampered CSRF Token:\n";
$_SERVER['HTTP_X_TEST_ENFORCE_CSRF'] = '1';

// Tampered token via POST body _token
$reqBadToken = new Request('POST', '/bn/admin/login', [], ['_token' => 'tampered_token_xyz', 'identifier' => 'test', 'password' => 'test']);
$resBad = $router->dispatch($reqBadToken);
assert_test("POST with tampered _token returns HTTP 403", $resBad->getStatusCode() === 403);

// Tampered token via POST body _csrf
$reqBadCsrf = new Request('POST', '/bn/admin/login', [], ['_csrf' => 'attacker_crafted_csrf', 'identifier' => 'test', 'password' => 'test']);
$resBadCsrf = $router->dispatch($reqBadCsrf);
assert_test("POST with tampered _csrf returns HTTP 403", $resBadCsrf->getStatusCode() === 403);

// Tampered token on AJAX request returns JSON 403
$reqBadAjax = new Request('POST', '/bn/blog/slug-test/like?format=json', [], ['_csrf' => 'invalid']);
$resBadAjax = $router->dispatch($reqBadAjax);
assert_test("AJAX POST with tampered CSRF returns HTTP 403 JSON", $resBadAjax->getStatusCode() === 403 && str_contains($resBadAjax->getContent(), 'Invalid or missing CSRF token'));

echo "\n3. Testing Missing CSRF Token in Enforced Mode:\n";
$reqMissing = new Request('POST', '/bn/admin/login', [], ['identifier' => 'test', 'password' => 'test']);
$resMissing = $router->dispatch($reqMissing);
assert_test("POST missing CSRF token in enforced mode returns HTTP 403", $resMissing->getStatusCode() === 403);

echo "\n4. Testing POST Acceptance with Valid CSRF Tokens:\n";
// Valid token via _token
$reqValidToken = new Request('POST', '/bn/admin/login', [], ['_token' => $validToken, 'identifier' => 'nonexistent', 'password' => 'invalid']);
$resValidToken = $router->dispatch($reqValidToken);
assert_test("POST with valid _token is processed through (HTTP != 403)", $resValidToken->getStatusCode() !== 403);

// Valid token via _csrf
$reqValidCsrf = new Request('POST', '/bn/admin/login', [], ['_csrf' => $validToken, 'identifier' => 'nonexistent', 'password' => 'invalid']);
$resValidCsrf = $router->dispatch($reqValidCsrf);
assert_test("POST with valid _csrf is processed through (HTTP != 403)", $resValidCsrf->getStatusCode() !== 403);

// Valid token via X-CSRF-Token HTTP header
$_SERVER['HTTP_X_CSRF_TOKEN'] = $validToken;
$reqValidHeader = new Request('POST', '/bn/admin/login', [], ['identifier' => 'nonexistent', 'password' => 'invalid']);
$resValidHeader = $router->dispatch($reqValidHeader);
assert_test("POST with valid X-CSRF-Token header is accepted (HTTP != 403)", $resValidHeader->getStatusCode() !== 403);
unset($_SERVER['HTTP_X_CSRF_TOKEN']);

echo "\n5. Testing Google Verification Whitelist Exemption:\n";
// Google Verify route should NOT be blocked by 403 even without CSRF token
$reqGoogle = new Request('POST', '/bn/membership/auth/google/verify', [], ['credential' => 'dummy_invalid_token']);
$resGoogle = $router->dispatch($reqGoogle);
assert_test("Google verify endpoint bypasses CSRF check (HTTP != 403)", $resGoogle->getStatusCode() !== 403);

unset($_SERVER['HTTP_X_TEST_ENFORCE_CSRF']);

echo "\n============================================\n";
echo "SUMMARY: {$testsPassed} / {$totalTests} tests passed.\n";
if ($testsPassed === $totalTests) {
    echo "ALL CSRF ENFORCEMENT TESTS PASSED! ✓\n";
} else {
    echo "SOME TESTS FAILED!\n";
    exit(1);
}
