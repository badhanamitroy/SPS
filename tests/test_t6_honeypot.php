<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\App;
use App\Core\I18n;
use App\Core\Request;
use App\Core\Router;
use App\Core\Session;
use App\Core\View;
use App\Services\AuditService;
use App\Services\BlogService;
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

function assert_t6(string $name, bool $condition, string $detail = ''): void
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
echo "SPS T6 Honeypot Protection Test Suite\n";
echo "====================================================\n\n";

// 1. Template Honeypot Fields
echo "1. Template Verification:\n";
$commentFormHtml = file_get_contents($baseDir . '/app/Views/pages/blog/show.php');
assert_t6("blog/show.php contains honeypot field", str_contains($commentFormHtml, 'name="_hp_website"'));

$donationFormHtml = file_get_contents($baseDir . '/app/Views/components/floating_donation.php');
assert_t6("floating_donation.php contains honeypot field", str_contains($donationFormHtml, 'name="_hp_website"'));

$applyFormHtml = file_get_contents($baseDir . '/app/Views/pages/membership/apply.php');
assert_t6("apply.php contains honeypot field", str_contains($applyFormHtml, 'name="_hp_website"'));

// 2. Blog Comment Honeypot Silent Rejection & Logging
echo "\n2. Blog Comment Honeypot Rejection:\n";
Session::start();
$posts = BlogService::getBlogs();
$testPostSlug = !empty($posts) ? $posts[0]['slug'] : 'sample-post';
$testBlogBefore = BlogService::findBySlug($testPostSlug);
$initialCommentsCount = count($testBlogBefore['comments'] ?? []);

$reqSpamComment = new Request('POST', "/bn/blog/{$testPostSlug}/comment", [], [
    'author_name' => 'Spam Bot',
    'content' => 'Buy cheap pills online',
    '_hp_website' => 'http://spam-link.ru',
]);
$resSpamComment = $router->dispatch($reqSpamComment);
assert_t6("Bot comment request returns redirect (silent success)", $resSpamComment->getStatusCode() === 302);

BlogService::clearCache();
$testBlogAfter = BlogService::findBySlug($testPostSlug);
$afterCommentsCount = count($testBlogAfter['comments'] ?? []);
assert_t6("No comment was added to blog comments store", $afterCommentsCount === $initialCommentsCount);

// 3. Donation Honeypot Silent Rejection & Logging
echo "\n3. Donation Honeypot Rejection:\n";
$paymentsBefore = MembershipService::getAllPayments();
$paymentsCountBefore = count($paymentsBefore);

$reqSpamDonation = new Request('POST', '/bn/donation/submit', [], [
    'donor_name' => 'Bot Donor',
    'amount' => 50,
    'trx_id' => 'BKA_SPAM_' . bin2hex(random_bytes(4)),
    '_hp_website' => 'http://bot-site.com',
]);
$resSpamDonation = $router->dispatch($reqSpamDonation);
assert_t6("Bot donation request returns redirect (silent success)", $resSpamDonation->getStatusCode() === 302);

$paymentsAfter = MembershipService::getAllPayments();
$paymentsCountAfter = count($paymentsAfter);
assert_t6("No payment record was created for trapped donation", $paymentsCountAfter === $paymentsCountBefore);

// 4. Membership Apply Honeypot Silent Rejection & Logging
echo "\n4. Membership Apply Honeypot Rejection:\n";
$membersBefore = MembershipService::getAllMembers();
$membersCountBefore = count($membersBefore);

$reqSpamApply = new Request('POST', '/bn/membership/apply', [], [
    'name_bn' => 'বট অ্যাপ্লিক্যান্ট',
    'email' => 'bot_' . bin2hex(random_bytes(4)) . '@spam.org',
    'phone' => '01799999999',
    'password' => 'secret123',
    'trx_id' => 'BKA_SPAM_' . bin2hex(random_bytes(4)),
    '_hp_website' => 'http://bot-network.biz',
]);
$resSpamApply = $router->dispatch($reqSpamApply);
assert_t6("Bot apply request returns redirect (silent success)", $resSpamApply->getStatusCode() === 302);

$membersAfter = MembershipService::getAllMembers();
$membersCountAfter = count($membersAfter);
assert_t6("No member record was created for trapped application", $membersCountAfter === $membersCountBefore);

// 5. Verify Audit Logs Recorded
echo "\n5. Audit Logs Verification:\n";
$auditLogs = AuditService::getLogs(10);
$foundHoneypotLog = false;
foreach ($auditLogs as $log) {
    if (($log['action'] ?? '') === 'honeypot.triggered') {
        $foundHoneypotLog = true;
        break;
    }
}
assert_t6("Audit log contains honeypot.triggered record", $foundHoneypotLog);

echo "\n----------------------------------------------------\n";
echo "Total Tests: {$totalTests} | Passed: {$passedTests} | Failed: " . ($totalTests - $passedTests) . "\n";
echo "====================================================\n";

if ($totalTests !== $passedTests) {
    exit(1);
}
exit(0);
