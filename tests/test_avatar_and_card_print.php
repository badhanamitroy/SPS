<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\App;
use App\Core\Request;
use App\Core\Router;
use App\Core\Session;
use App\Services\MembershipService;

$baseDir = dirname(__DIR__);
$app = new App($baseDir);

$router = new Router();
$routesFile = $baseDir . '/routes/web.php';
require $routesFile;

$totalTests = 0;
$passedTests = 0;

function assertCondition(bool $condition, string $message): void
{
    global $totalTests, $passedTests;
    $totalTests++;
    if ($condition) {
        $passedTests++;
        echo "  [PASS] {$message}\n";
    } else {
        echo "  [FAIL] {$message}\n";
    }
}

echo "=== SPS MEMBER AVATAR & CARD PRINT TEST SUITE ===\n\n";

// 1. Test Avatar Update in MembershipService
echo "1. Testing Avatar Storage & Update:\n";
$targetMemberCode = 'SPS-000872';
$sampleAvatarPath = 'assets/images/members/member_SPS_000872_test.jpg';

$res = MembershipService::updateMemberProfile($targetMemberCode, [
    'avatar' => $sampleAvatarPath
]);
assertCondition($res['success'] === true, "Avatar successfully stored in member profile");

$member = MembershipService::getMemberById($targetMemberCode);
assertCondition(($member['avatar'] ?? '') === $sampleAvatarPath, "Member record returns avatar path");

// 2. Test Dedicated Card Print Endpoint
echo "\n2. Testing Dedicated Digital Card Print Endpoint:\n";
$printRequest = new Request(
    method: 'GET',
    uri: '/bn/membership/card/print',
    queryParams: ['code' => $targetMemberCode]
);
$printResponse = $router->dispatch($printRequest);

assertCondition($printResponse->getStatusCode() === 200, "GET /bn/membership/card/print returns HTTP 200");
$printBody = $printResponse->getBody();
assertCondition(str_contains($printBody, 'SPS-000872'), "Print card renders member code");
assertCondition(str_contains($printBody, 'সনাতন ফিলোসফি এন্ড স্ক্রিপচার'), "Print card contains SPS Bengali title");
assertCondition(str_contains($printBody, 'SANATAN PHILOSOPHY & SCRIPTURE'), "Print card contains SPS English title");
assertCondition(str_contains($printBody, 'sps-logo-white.png'), "Print card uses SPS white logo on dark background");
assertCondition(str_contains($printBody, 'card-front'), "Print card contains Front Side");
assertCondition(str_contains($printBody, 'card-back'), "Print card contains Back Side");
assertCondition(str_contains($printBody, '@media print'), "Print card contains dedicated print CSS rules");
assertCondition(str_contains($printBody, 'print-color-adjust: exact'), "Print card forces color/gradient preservation in printer");
assertCondition(str_contains($printBody, 'member_SPS_000872_test.jpg'), "Print card renders updated member avatar image");

// 3. Test Dashboard Displays Avatar and Print Link
echo "\n3. Testing Member Dashboard Avatar & Print Link:\n";
Session::set('current_member_code', $targetMemberCode);
Session::forget('member_logged_out');

$dashRequest = new Request(
    method: 'GET',
    uri: '/bn/membership/dashboard'
);
$dashResponse = $router->dispatch($dashRequest);
assertCondition($dashResponse->getStatusCode() === 200, "Dashboard returns HTTP 200");
$dashBody = $dashResponse->getBody();
assertCondition(str_contains($dashBody, 'member_SPS_000872_test.jpg'), "Dashboard displays member avatar in identity banner");
assertCondition(str_contains($dashBody, '/membership/card/print?code='), "Dashboard card action button links to dedicated print desk");
assertCondition(str_contains($dashBody, 'name="avatar_file"'), "Dashboard profile form contains avatar file upload input");
assertCondition(str_contains($dashBody, 'previewAvatar'), "Dashboard contains live avatar preview script");

// 4. Test Authentic QR Code Service & Verification Link with Center Logo
echo "\n4. Testing Authentic Scannable QR Code Service & Integration:\n";
$verifyUrl = \App\Services\QrCodeService::getVerificationUrl($targetMemberCode, 'bn');
assertCondition(str_contains($verifyUrl, '/bn/membership/verify?code=' . $targetMemberCode), "QrCodeService produces valid canonical verify URL");

$qrConfig = \App\Services\QrCodeService::getQrConfig($targetMemberCode, 'bn');
assertCondition($qrConfig['level'] === 'H', "QR Error Correction Level is H (30% tolerance for center logo)");
assertCondition(str_contains($qrConfig['logo'], 'sps-logo.png'), "QR config references official SPS brand logo");

assertCondition(str_contains($printBody, 'printCardQrImg'), "Print card contains printCardQrImg container");
assertCondition(str_contains($printBody, 'qrcode.min.js'), "Print card imports qrcode.min.js");
assertCondition(str_contains($printBody, 'sps-qr.js'), "Print card imports sps-qr.js");
assertCondition(str_contains($printBody, 'renderSpsQrCode'), "Print card executes renderSpsQrCode");

assertCondition(str_contains($dashBody, 'dashboardCardQrImg'), "Dashboard card contains dashboardCardQrImg");
assertCondition(str_contains($dashBody, 'qrcode.min.js'), "Dashboard imports qrcode.min.js");
assertCondition(str_contains($dashBody, 'sps-qr.js'), "Dashboard imports sps-qr.js");
assertCondition(str_contains($dashBody, 'renderSpsQrCode'), "Dashboard executes renderSpsQrCode");

echo "\n============================================\n";
echo "SUMMARY: {$passedTests} / {$totalTests} tests passed.\n";
if ($passedTests === $totalTests) {
    echo "ALL AVATAR & CARD PRINT TESTS PASSED! ✓\n";
} else {
    echo "SOME TESTS FAILED!\n";
    exit(1);
}
