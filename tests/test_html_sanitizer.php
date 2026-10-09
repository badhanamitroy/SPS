<?php
/**
 * Test Suite: T6 Blog HTML Sanitizer
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\HtmlSanitizer;
use App\Services\BlogService;

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

echo "=== SPS T6 HTML SANITIZER TEST SUITE ===\n\n";

echo "1. Testing Whitelisted Safe HTML Tags:\n";
$safeInput = '<h2>শিরোনাম</h2><p>এটি একটি <strong>গুরুত্বপূর্ণ</strong> এবং <em>গবেষণামূলক</em> অনুচ্ছেদ।</p><blockquote>উদ্ধৃতি</blockquote><ul><li>পয়েন্ট ১</li><li>পয়েন্ট ২</li></ul><hr><a href="https://sps.org/article" title="SPS Link">লিংক</a><img src="/assets/img/photo.jpg" alt="ছবি"><pre><code>echo "hello";</code></pre>';
$cleanedSafe = HtmlSanitizer::clean($safeInput);

assert_test("Allowed tags preserved (h2, p, strong, em)", str_contains($cleanedSafe, '<h2>শিরোনাম</h2>') && str_contains($cleanedSafe, '<strong>গুরুত্বপূর্ণ</strong>') && str_contains($cleanedSafe, '<em>গবেষণামূলক</em>'));
assert_test("Allowed lists preserved (ul, li)", str_contains($cleanedSafe, '<ul>') && str_contains($cleanedSafe, '<li>পয়েন্ট ১</li>'));
assert_test("Allowed links with safe href/title preserved", str_contains($cleanedSafe, 'href="https://sps.org/article"') && str_contains($cleanedSafe, 'title="SPS Link"'));
assert_test("Allowed images with safe src/alt preserved", str_contains($cleanedSafe, 'src="/assets/img/photo.jpg"') && str_contains($cleanedSafe, 'alt="ছবি"'));
assert_test("Allowed code/pre/hr preserved", str_contains($cleanedSafe, '<pre><code>') && str_contains($cleanedSafe, '<hr>'));

echo "\n2. Testing Stripping of Dangerous Tags (script, style, iframe, svg):\n";
$dirtyScript = '<p>Safe Text</p><script>alert("XSS Attack!");</script>';
assert_test("<script> tag and contents stripped", !str_contains(HtmlSanitizer::clean($dirtyScript), 'script') && !str_contains(HtmlSanitizer::clean($dirtyScript), 'XSS Attack!'));

$dirtyStyle = '<style>body { display: none; }</style><p>Content</p>';
assert_test("<style> tag and contents stripped", !str_contains(HtmlSanitizer::clean($dirtyStyle), 'style') && !str_contains(HtmlSanitizer::clean($dirtyStyle), 'display: none'));

$dirtyIframe = '<p>Before</p><iframe src="https://attacker.site/phish"></iframe><p>After</p>';
assert_test("<iframe> tag stripped", !str_contains(HtmlSanitizer::clean($dirtyIframe), 'iframe') && !str_contains(HtmlSanitizer::clean($dirtyIframe), 'attacker.site'));

echo "\n3. Testing Stripping of Event Handlers (on* attributes):\n";
$dirtyEvents = '<img src="/valid.jpg" alt="valid" onerror="alert(document.cookie)"><p onmouseover="eval(123)">Hover</p><a href="https://safe.org" onclick="malicious()">Click</a>';
$cleanedEvents = HtmlSanitizer::clean($dirtyEvents);
assert_test("onerror attribute removed from img", !str_contains($cleanedEvents, 'onerror') && str_contains($cleanedEvents, 'src="/valid.jpg"'));
assert_test("onmouseover attribute removed from p", !str_contains($cleanedEvents, 'onmouseover'));
assert_test("onclick attribute removed from a", !str_contains($cleanedEvents, 'onclick') && str_contains($cleanedEvents, 'href="https://safe.org"'));

echo "\n4. Testing Stripping of Dangerous URI Protocols (javascript:, data:):\n";
$dirtyJsUrl = '<a href="javascript:alert(1)" title="Malicious">Bad Link</a>';
$cleanedJs = HtmlSanitizer::clean($dirtyJsUrl);
assert_test("javascript: URL removed from href", !str_contains($cleanedJs, 'javascript:') && !str_contains($cleanedJs, 'href='));

$dirtyDataUrl = '<a href="data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg==">Data URI</a>';
assert_test("data: URL removed from href", !str_contains(HtmlSanitizer::clean($dirtyDataUrl), 'data:'));

$dirtyImgData = '<img src="data:image/svg+xml;base64,PHN2Zy9vbmxvYWQ9YWxlcnQoMSk+" alt="hacked">';
assert_test("data: URL removed from img src", !str_contains(HtmlSanitizer::clean($dirtyImgData), 'data:'));

echo "\n5. Testing BlogService Integration:\n";
$xssSubmission = [
    'title_bn' => 'পরীক্ষামূলক ব্লগ ' . time(),
    'title_en' => 'Test Blog ' . time(),
    'category' => 'vedanta',
    'excerpt_bn' => 'সংক্ষিপ্ত বিবরণ',
    'excerpt_en' => 'Short Excerpt',
    'content_bn' => '<p>নিরাপদ লেখা</p><script>alert("pwned")</script><img src="/img.png" alt="valid" onerror="bad()">',
    'content_en' => 'Safe english text <iframe src="evil.com"></iframe> with <a href="javascript:bad()">link</a>',
];
$created = BlogService::createBlog($xssSubmission);
assert_test("BlogService::createBlog sanitizes content_bn", !str_contains($created['content_bn'], 'script') && !str_contains($created['content_bn'], 'onerror'));
assert_test("BlogService::createBlog sanitizes content_en", !str_contains($created['content_en'], 'iframe') && !str_contains($created['content_en'], 'javascript:'));

$fetched = BlogService::findBySlug($created['slug']);
assert_test("BlogService::findBySlug returns sanitized content", !str_contains($fetched['content_bn'], 'script') && str_contains($fetched['content_bn'], 'নিরাপদ লেখা'));

echo "\n============================================\n";
echo "SUMMARY: {$testsPassed} / {$totalTests} tests passed.\n";
if ($testsPassed === $totalTests) {
    echo "ALL HTML SANITIZER TESTS PASSED! ✓\n";
} else {
    echo "SOME TESTS FAILED!\n";
    exit(1);
}
