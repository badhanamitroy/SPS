<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\App;
use App\Core\Request;
use App\Core\Router;
use App\Core\Session;
use App\Services\AuditService;
use App\Services\BlogService;

$baseDir = dirname(__DIR__);
$app = new App($baseDir);

$langConfig = require $baseDir . '/config/languages.php';
\App\Core\I18n::init($langConfig, 'bn', '/bn');
\App\Core\View::init($baseDir . '/app/Views');

$router = new Router();
require $baseDir . '/routes/web.php';

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

echo "=== SPS T9: SMALL BUG FIXES TEST SUITE ===\n\n";

// ---------------------------------------------------------
// T9.a: AuditService::deleteLogs() throws \RuntimeException
// ---------------------------------------------------------
echo "1. Testing AuditService::deleteLogs() Exception Type:\n";

$caughtException = null;
try {
    AuditService::deleteLogs();
} catch (\Throwable $e) {
    $caughtException = $e;
}

assertCondition($caughtException !== null, "AuditService::deleteLogs() threw an exception");
assertCondition(
    $caughtException instanceof \RuntimeException,
    "Exception is an instance of \\RuntimeException (not undefined \\SecurityException)"
);
assertCondition(
    str_contains($caughtException->getMessage(), "Audit log deletion is strictly prohibited"),
    "Exception message preserves security policy rationale"
);

// ---------------------------------------------------------
// T9.b: BlogService::addComment() stores raw text with raw flag
// ---------------------------------------------------------
echo "\n2. Testing BlogService::addComment() Raw Text & Length Limiting:\n";

$slug = 'vedanta-and-modern-science';
$specialRawContent = 'গীতা ও বিজ্ঞান: 5 < 10 & 10 > 5 "সত্য" \'শাশ্বত\'';
$longContent = str_repeat('খুব ভালো লেখা। ', 300); // > 2000 chars

$cmt = BlogService::addComment($slug, [
    'author_name' => 'পরীক্ষক রঞ্জন',
    'author_email' => 'test_raw@example.com',
    'author_role' => 'visitor',
    'content' => $specialRawContent,
]);

assertCondition($cmt !== null, "Comment added successfully");
assertCondition(($cmt['raw'] ?? false) === true, "Comment record has 'raw' => true marker");
assertCondition($cmt['content'] === $specialRawContent, "Comment stored raw text without premature htmlspecialchars()");
assertCondition(!str_contains($cmt['content'], '&lt;') && !str_contains($cmt['content'], '&amp;'), "Special characters not entity-encoded in storage");

// Test length limiting on content
$cmtLong = BlogService::addComment($slug, [
    'author_name' => 'দীর্ঘ মন্তব্যকারী',
    'author_email' => 'long@example.com',
    'author_role' => 'visitor',
    'content' => $longContent,
]);
assertCondition(mb_strlen($cmtLong['content']) <= 2000, "Comment content properly clamped to 2000 chars limit");

// Verify render via Router
$resShow = $router->dispatch(new Request('GET', '/bn/blog/' . $slug));
assertCondition($resShow->getStatusCode() === 200, "GET /bn/blog/{slug} returns HTTP 200");
$showHtml = $resShow->getContent();

// Rendered HTML must contain properly escaped output with e()
assertCondition(
    str_contains($showHtml, '5 &lt; 10 &amp; 10 &gt; 5 &quot;সত্য&quot;'),
    "Output is properly escaped with e() on render (no raw tags injected)"
);
assertCondition(
    !str_contains($showHtml, '&amp;lt;'),
    "No double-escaping (&amp;lt;) present in rendered output"
);

// ---------------------------------------------------------
// T9.c: Blog Likes Identity (member code vs hashed fingerprint)
// ---------------------------------------------------------
echo "\n3. Testing Blog Likes Identity Management:\n";

// 3a. Member logged in: uses member code
Session::set('current_member_code', 'SPS-000872');
Session::forget('member_logged_out');

$reqMemLike = new Request('POST', '/bn/blog/' . $slug . '/like?format=json');
$resMemLike = $router->dispatch($reqMemLike);
assertCondition($resMemLike->getStatusCode() === 200, "Member like POST returns 200 JSON");

$blogRecord = BlogService::findBySlug($slug, false);
$likedIps = $blogRecord['liked_ips'] ?? [];
assertCondition(
    in_array('member:SPS-000872', $likedIps, true),
    "Logged-in member like identity recorded as 'member:SPS-000872'"
);

// Clean up: toggle again to unlike
$router->dispatch(new Request('POST', '/bn/blog/' . $slug . '/like?format=json'));

// 3b. Anonymous visitor: uses hashed IP+UA fingerprint
Session::forget('current_member_code');
Session::set('member_logged_out', true);

$reqAnonLike = new Request('POST', '/bn/blog/' . $slug . '/like?format=json', [], [], ['User-Agent' => 'SPS-Anon-TestBot/1.0']);
$resAnonLike = $router->dispatch($reqAnonLike);
assertCondition($resAnonLike->getStatusCode() === 200, "Anonymous like POST returns 200 JSON");

$anonData = json_decode($resAnonLike->getContent(), true);
// If it was already liked, toggle it once more to ensure it's in liked state
if (!($anonData['liked'] ?? false)) {
    $resAnonLike = $router->dispatch(new Request('POST', '/bn/blog/' . $slug . '/like?format=json', [], [], ['User-Agent' => 'SPS-Anon-TestBot/1.0']));
}

$blogRecordAnon = BlogService::findBySlug($slug, false);
$likedIpsAnon = $blogRecordAnon['liked_ips'] ?? [];
$hasHashedFingerprint = false;
foreach ($likedIpsAnon as $ident) {
    if (str_starts_with($ident, 'anon:')) {
        $hasHashedFingerprint = true;
        break;
    }
}
assertCondition($hasHashedFingerprint, "Anonymous visitor like identity recorded as hashed fingerprint 'anon:...'");

echo "\n============================================\n";
echo "SUMMARY: {$passedTests} / {$totalTests} tests passed.\n";
if ($passedTests === $totalTests) {
    echo "ALL T9 BUG FIX TESTS PASSED! ✓\n";
    exit(0);
} else {
    echo "SOME TESTS FAILED! ✕\n";
    exit(1);
}
