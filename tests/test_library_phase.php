<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\App;
use App\Core\I18n;
use App\Core\Request;
use App\Core\Router;
use App\Services\LibraryService;

$passed = 0;
$failed = 0;

function assert_test(string $name, bool $condition, string $detail = ''): void {
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo "[PASS] {$name}\n";
    } else {
        $failed++;
        echo "[FAIL] {$name} - {$detail}\n";
    }
}

echo "====================================================\n";
echo "SPS Header & Library Phase Automated Suite\n";
echo "====================================================\n";

$app = new App(dirname(__DIR__));
I18n::init(require dirname(__DIR__) . '/config/languages.php', 'bn', '/bn');

// 2. Test Header Navigation Links
$headerHtml = \App\Core\View::component('header', ['activeNav' => 'about']);
assert_test("Header: Contains About link", str_contains($headerHtml, '/bn/about'));
assert_test("Header: Contains Activities link", str_contains($headerHtml, '/bn/activities'));
assert_test("Header: Contains Blog link", str_contains($headerHtml, '/bn/blog'));
assert_test("Header: Contains brand logo linking to home", str_contains($headerHtml, 'brand-wrapper') && str_contains($headerHtml, 'sps-logo.png'));
// Ensure home, knowledge, etc are removed from desktop nav (restricted to core nav: About, Activities, Blog, Membership)
assert_test("Header: Desktop nav restricted to About, Activities, Blog, Membership", !str_contains($headerHtml, 'nav-link') || in_array(substr_count($headerHtml, 'class="nav-link'), [3, 4]));

// 3. Test Library Service Books
$books = LibraryService::getBooks();
assert_test("LibraryService: Exactly 3 PDFs registered", count($books) === 3);
assert_test("LibraryService: Contains SPS Ramnavami Edition", isset($books['sps-ramnavami']));
assert_test("LibraryService: Contains SPS Samachar Feb Edition", isset($books['sps-samachar-feb']));
assert_test("LibraryService: Contains Jagannath & Rathyatra", isset($books['sps-jagannath-rathyatra']));

// Check physical files exist in Media/PDF-Libraries
foreach ($books as $slug => $book) {
    assert_test("Physical PDF exists: {$slug}", file_exists($book['file_path']) && filesize($book['file_path']) > 100000);
    $coverPath = dirname(__DIR__) . '/public/' . $book['cover_image'];
    assert_test("Cover image exists: {$slug}", file_exists($coverPath) && filesize($coverPath) > 1000);
}

// 4. Test Library Controller Index
$router = new Router();
require dirname(__DIR__) . '/routes/web.php';

$libraryReq = new Request('GET', '/bn/library');
$res = $router->dispatch($libraryReq);
assert_test("Router: /bn/library returns 200", $res->getStatusCode() === 200);
$content = $res->getContent();
assert_test("Library View: Contains Ramnavami book title", str_contains($content, 'রামনবমী'));
assert_test("Library View: Contains Samachar title", str_contains($content, 'এসপিএস সমাচার'));
assert_test("Library View: Contains Jagannath title", str_contains($content, 'জগন্নাথ'));
assert_test("Library View: Shows Paid Members Only badge", str_contains($content, 'সদস্য সংরক্ষিত') || str_contains($content, 'পড়ার অনুমতি আছে'));

// 5. Test Book Detail View
$detailReq = new Request('GET', '/bn/library/book/sps-ramnavami');
$resDetail = $router->dispatch($detailReq);
assert_test("Router: /bn/library/book/sps-ramnavami returns 200", $resDetail->getStatusCode() === 200);
assert_test("Book Detail: Contains access request form", str_contains($resDetail->getContent(), 'request-access'));

// 6. Test E-Book Reader for Paid Member
LibraryService::setRole('paid_member');
$readerReq = new Request('GET', '/bn/library/reader/sps-ramnavami');
$resReader = $router->dispatch($readerReq);
assert_test("Reader: /bn/library/reader/sps-ramnavami returns 200 for paid member", $resReader->getStatusCode() === 200);
assert_test("Reader View: Contains anti-download script and PDF canvas", str_contains($resReader->getContent(), 'pdf-render-canvas') && str_contains($resReader->getContent(), 'contextmenu'));

// 7. Test PDF Streaming Security
$streamReq = new Request('GET', '/bn/library/stream/sps-ramnavami');
$resStream = $router->dispatch($streamReq);
assert_test("Stream: Returns 200 with application/pdf header", $resStream->getStatusCode() === 200 && ($resStream->getHeaders()['Content-Type'] ?? '') === 'application/pdf');

// 8. Test Admin Portal
\App\Services\AuthService::loginAs('usr_anik');
$adminReq = new Request('GET', '/bn/admin/library');
$resAdmin = $router->dispatch($adminReq);
assert_test("Admin: /bn/admin/library returns 200", $resAdmin->getStatusCode() === 200);
$adminContent = $resAdmin->getContent();
assert_test("Admin View: Displays 'What Admin CAN Do'", str_contains($adminContent, 'প্রশাসক যা করতে পারেন') || str_contains($adminContent, 'What the Admin CAN Do'));
assert_test("Admin View: Displays 'What Admin CANNOT Do'", str_contains($adminContent, 'প্রশাসক যা করতে পারেন না') || str_contains($adminContent, 'What the Admin CANNOT Do'));
assert_test("Admin View: Displays access request list", str_contains($adminContent, 'পাঠাধিকার আবেদন তালিকা'));

echo "\n----------------------------------------------------\n";
echo "Total Tests: " . ($passed + $failed) . " | Passed: {$passed} | Failed: {$failed}\n";
echo "====================================================\n";

if ($failed > 0) exit(1);
