<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\App;
use App\Core\I18n;
use App\Core\Request;
use App\Core\Router;
use App\Core\Session;
use App\Core\View;
use App\Services\MembershipService;

$baseDir = dirname(__DIR__);
$app = new App($baseDir);

$langConfig = require $baseDir . '/config/languages.php';
I18n::init($langConfig, 'bn', '/bn');
View::init($baseDir . '/app/Views');

$router = new Router();
require $baseDir . '/routes/web.php';

$totalTests = 0;
$passedTests = 0;

function assert_t4(string $name, bool $condition, string $detail = ''): void
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
echo "SPS T4 Double-Submit & Duplicate TrxID Test Suite\n";
echo "====================================================\n\n";

// 1. Client-Side Double Submit JS
echo "1. Client-Side Double Submit JS in main.js:\n";
$mainJsPath = $baseDir . '/public/assets/js/main.js';
$jsContent = file_get_contents($mainJsPath);
assert_t4("main.js contains initDoubleSubmitProtection", str_contains($jsContent, 'initDoubleSubmitProtection'));
assert_t4("main.js covers /membership/apply", str_contains($jsContent, 'membership/apply'));
assert_t4("main.js covers /donation/submit", str_contains($jsContent, 'donation/submit'));
assert_t4("main.js covers /membership/payment", str_contains($jsContent, 'membership/payment'));
assert_t4("main.js covers /admin/activities/create", str_contains($jsContent, 'admin/activities/create'));
assert_t4("main.js covers /blog/write", str_contains($jsContent, 'blog/write'));

// 2. Duplicate TrxID Detection
echo "\n2. Duplicate TrxID Detection:\n";
$payments = MembershipService::getAllPayments();
$existingTrx = !empty($payments) ? ($payments[0]['trx_id'] ?? '') : '';

if (!empty($existingTrx)) {
    assert_t4("MembershipService detects existing duplicate TrxID", MembershipService::isDuplicateTrxId($existingTrx) === true);
} else {
    assert_t4("MembershipService payments found", false, "No payments in database");
}

$freshTrx = 'UNIQUE_TRX_' . bin2hex(random_bytes(6));
assert_t4("MembershipService permits new unique TrxID", MembershipService::isDuplicateTrxId($freshTrx) === false);

// 3. Donation Controller Rejection on Duplicate TrxID
echo "\n3. Donation Controller Rejection on Duplicate TrxID:\n";
Session::start();
Session::setFlash('error', null);

$reqDonation = new Request('POST', '/bn/donation/submit', [], [
    'donor_name' => 'Test Donor',
    'sender_number' => '01700000000',
    'amount' => 50,
    'trx_id' => $existingTrx,
    'payment_method' => 'bKash',
    'project_type' => '10_taka'
]);

$resDonation = $router->dispatch($reqDonation);
assert_t4("Donation with duplicate TrxID redirects", $resDonation->getStatusCode() === 302);
$flashError = Session::getFlash('error');
assert_t4("Donation sets duplicate TrxID error message", !empty($flashError) && (str_contains($flashError, 'ইতিপূর্বে') || str_contains($flashError, 'already been')));

// 4. Membership Payment Controller Rejection on Duplicate TrxID
echo "\n4. Membership Payment Controller Rejection on Duplicate TrxID:\n";
Session::set('current_member_code', 'SPS-000872');
Session::set('member_id', 'mem_default_01');
Session::setFlash('error', null);

$reqPay = new Request('POST', '/bn/membership/payment', [], [
    'payment_type' => 'monthly',
    'payment_method' => 'bKash',
    'trx_id' => $existingTrx,
    'amount' => 100,
]);

$resPay = $router->dispatch($reqPay);
assert_t4("Membership payment with duplicate TrxID redirects", $resPay->getStatusCode() === 302);
$flashPayError = Session::getFlash('error');
assert_t4("Membership payment sets duplicate TrxID error message", !empty($flashPayError) && (str_contains($flashPayError, 'ইতিপূর্বে') || str_contains($flashPayError, 'already been')));

echo "\n----------------------------------------------------\n";
echo "Total Tests: {$totalTests} | Passed: {$passedTests} | Failed: " . ($totalTests - $passedTests) . "\n";
echo "====================================================\n";

if ($totalTests !== $passedTests) {
    exit(1);
}
exit(0);
