<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\App;
use App\Core\I18n;
use App\Core\RateLimiter;
use App\Core\Request;
use App\Core\Router;
use App\Core\Session;
use App\Core\View;

$baseDir = dirname(__DIR__);
$app = new App($baseDir);

$langConfig = require $baseDir . '/config/languages.php';
I18n::init($langConfig, 'bn', '/bn');
View::init($baseDir . '/app/Views');

$router = new Router();
require $baseDir . '/routes/web.php';

$totalTests = 0;
$passedTests = 0;

function assert_t5(string $name, bool $condition, string $detail = ''): void
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
echo "SPS T5 Public POST Rate Limiting Test Suite\n";
echo "====================================================\n\n";

$testIp = '198.51.100.' . mt_rand(10, 250);
$_SERVER['REMOTE_ADDR'] = $testIp;
$_SERVER['HTTP_X_FORWARDED_FOR'] = $testIp;

// 1. Donation Submit Rate Limiting
echo "1. Donation Submit Rate Limiting:\n";
$donationKey = 'ratelimit:donation:' . $testIp;
RateLimiter::resetAttempts($donationKey);

for ($i = 0; $i < 5; $i++) {
    RateLimiter::hit($donationKey, 60);
}
assert_t5("Donation key has hit limit", RateLimiter::tooManyAttempts($donationKey, 5));

Session::start();
Session::setFlash('error', null);
$reqDonation = new Request('POST', '/bn/donation/submit', [], [
    'donor_name' => 'Spam Bot',
    'amount' => 10,
    'trx_id' => 'TRX_' . bin2hex(random_bytes(4)),
]);
$resDonation = $router->dispatch($reqDonation);
assert_t5("Donation request rejected when rate limited", $resDonation->getStatusCode() === 302);
$flashError = Session::getFlash('error');
assert_t5("Bilingual rate limit error displayed for donation", !empty($flashError) && (str_contains($flashError, 'অতিরিক্ত অনুরোধ') || str_contains($flashError, 'Too many donation attempts')));

RateLimiter::resetAttempts($donationKey);

// 2. Blog Comment Rate Limiting
echo "\n2. Blog Comment Rate Limiting:\n";
$commentKey = 'ratelimit:blog:comment:' . $testIp;
RateLimiter::resetAttempts($commentKey);

for ($i = 0; $i < 5; $i++) {
    RateLimiter::hit($commentKey, 60);
}
assert_t5("Blog comment key has hit limit", RateLimiter::tooManyAttempts($commentKey, 5));

Session::setFlash('error', null);
$reqComment = new Request('POST', '/bn/blog/sample-post/comment', [], [
    'author_name' => 'Comment Bot',
    'content' => 'Spam comment text',
]);
$resComment = $router->dispatch($reqComment);
assert_t5("Blog comment rejected when rate limited", $resComment->getStatusCode() === 302);
$commentError = Session::getFlash('error');
assert_t5("Bilingual rate limit error displayed for blog comment", !empty($commentError) && (str_contains($commentError, 'সীমা অতিক্রম') || str_contains($commentError, 'Too many comments')));

RateLimiter::resetAttempts($commentKey);

// 3. Membership Apply Rate Limiting
echo "\n3. Membership Apply Rate Limiting:\n";
$applyKey = 'ratelimit:membership:apply:' . $testIp;
RateLimiter::resetAttempts($applyKey);

for ($i = 0; $i < 5; $i++) {
    RateLimiter::hit($applyKey, 300);
}
assert_t5("Membership apply key has hit limit", RateLimiter::tooManyAttempts($applyKey, 5));

Session::setFlash('error', null);
$reqApply = new Request('POST', '/bn/membership/apply', [], [
    'name_bn' => 'পরীক্ষার্থী',
    'email' => 'bot@example.com',
    'phone' => '01700000000',
    'password' => 'secret123',
]);
$resApply = $router->dispatch($reqApply);
assert_t5("Membership apply rejected when rate limited", $resApply->getStatusCode() === 302);
$applyError = Session::getFlash('error');
assert_t5("Bilingual rate limit error displayed for membership apply", !empty($applyError) && (str_contains($applyError, 'সীমা অতিক্রম') || str_contains($applyError, 'Too many membership application attempts')));

RateLimiter::resetAttempts($applyKey);

echo "\n----------------------------------------------------\n";
echo "Total Tests: {$totalTests} | Passed: {$passedTests} | Failed: " . ($totalTests - $passedTests) . "\n";
echo "====================================================\n";

if ($totalTests !== $passedTests) {
    exit(1);
}
exit(0);
