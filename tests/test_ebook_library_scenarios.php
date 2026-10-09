<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\App;
use App\Core\I18n;
use App\Core\Request;
use App\Core\Router;
use App\Core\Session;
use App\Services\AuthService;
use App\Services\LibraryService;

$passed = 0;
$failed = 0;

function assert_scenario(string $name, bool $condition, string $detail = ''): void {
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo "  [PASS] {$name}\n";
    } else {
        $failed++;
        echo "  [FAIL] {$name} — {$detail}\n";
    }
}

echo "====================================================================\n";
echo "SPS Premium E-Book Library — Final Scenarios A through H Verification\n";
echo "====================================================================\n\n";

$app = new App(dirname(__DIR__));
I18n::init(require dirname(__DIR__) . '/config/languages.php', 'bn', '/bn');

$router = new Router();
require dirname(__DIR__) . '/routes/web.php';

// ====================================================================
// SCENARIO A: Anonymous visitor opens a free book
// ====================================================================
echo "1. Scenario A: Anonymous visitor opens a free book:\n";
LibraryService::setRole('viewer');
Session::set('viewer_request_email', null);

$slugA = 'sps-jagannath-rathyatra';
$accessA = LibraryService::getReadingAccessLevel($slugA);
assert_scenario("Visitor reading access is 'full'", $accessA === 'full');

$streamFileA = LibraryService::getStreamFile($slugA);
assert_scenario("Stream file is master PDF", $streamFileA !== null && str_contains($streamFileA, 'শ্রী শ্রী জগন্নাথ ও রথযাত্রা মাহাত্ম্য.pdf'));

$streamResA = $router->dispatch(new Request('GET', "/bn/library/stream/{$slugA}"));
assert_scenario("Stream endpoint returns HTTP 200 with application/pdf", $streamResA->getStatusCode() === 200 && ($streamResA->getHeaders()['Content-Type'] ?? '') === 'application/pdf');

$dlEligibilityA = LibraryService::getDownloadEligibility($slugA);
assert_scenario("Downloads are permanently disabled", $dlEligibilityA['status'] === 'disabled' && $dlEligibilityA['can_download'] === false);

$dlResA = $router->dispatch(new Request('GET', "/bn/library/download/{$slugA}"));
assert_scenario("Direct download attempt rejected with 403 Forbidden", $dlResA->getStatusCode() === 403);


// ====================================================================
// SCENARIO B: Anonymous visitor opens a preview-only book
// ====================================================================
echo "\n2. Scenario B: Anonymous visitor opens a preview-only book:\n";
LibraryService::setRole('viewer');

$slugB = 'dating-the-era-of-lord-ram';
$bookB = LibraryService::getBook($slugB);
$accessB = LibraryService::getReadingAccessLevel($slugB);
assert_scenario("Visitor reading access is 'partial'", $accessB === 'partial');
assert_scenario("Preview end page is configured as 20", (int)($bookB['preview_end'] ?? 0) === 20);

$streamFileB = LibraryService::getStreamFile($slugB);
assert_scenario("Server slices/delivers preview file (NOT master file)", $streamFileB !== null && file_exists($streamFileB));

// Verify that the streamed preview file has exactly 20 pages
if (file_exists($streamFileB)) {
    $slicePageCount = (int)exec("python -c \"import pypdfium2 as pdfium; print(len(pdfium.PdfDocument(r'{$streamFileB}')))\"");
    assert_scenario("Server-side delivered stream has strictly 20 pages (remaining 165 pages not in stream)", $slicePageCount === 20);
}

$streamResB = $router->dispatch(new Request('GET', "/bn/library/stream/{$slugB}"));
assert_scenario("Stream endpoint returns HTTP 200 for preview slice", $streamResB->getStatusCode() === 200);

$readerResB = $router->dispatch(new Request('GET', "/bn/library/reader/{$slugB}"));
assert_scenario("Reader contains isPartial flag and preview end indicator", str_contains($readerResB->getContent(), 'isPartial') && str_contains($readerResB->getContent(), 'reader-locked-overlay'));


// ====================================================================
// SCENARIO C: Paying member opens a member book
// ====================================================================
echo "\n3. Scenario C: Paying member opens a member book:\n";
LibraryService::setRole('paid_member');
Session::set('current_member_code', 'SPS-000872');

$slugC = 'sps-ramnavami';
$accessC = LibraryService::getReadingAccessLevel($slugC);
assert_scenario("Member reading access is 'full'", $accessC === 'full');

$streamFileC = LibraryService::getStreamFile($slugC);
assert_scenario("Stream file is master 64-page PDF", $streamFileC !== null && str_contains($streamFileC, 'SPS রামনবমী সংখ্যা.pdf'));

$streamResC = $router->dispatch(new Request('GET', "/bn/library/stream/{$slugC}"));
assert_scenario("Member stream returns HTTP 200 with full PDF", $streamResC->getStatusCode() === 200);

$dlEligibilityC = LibraryService::getDownloadEligibility($slugC);
assert_scenario("Download is eligible via Admin approval request", $dlEligibilityC['can_request'] === true);


// ====================================================================
// SCENARIO D: Non-paying user tries to access member-only content
// ====================================================================
echo "\n4. Scenario D: Non-paying user tries to access member-only content:\n";
LibraryService::setRole('viewer');
Session::set('viewer_request_email', null);

$slugD = 'sps-ramnavami';
$accessD = LibraryService::getReadingAccessLevel($slugD);
assert_scenario("Visitor reading access is 'locked'", $accessD === 'locked');

$streamFileD = LibraryService::getStreamFile($slugD);
assert_scenario("Stream file is null (no file prepared)", $streamFileD === null);

$streamResD = $router->dispatch(new Request('GET', "/bn/library/stream/{$slugD}"));
assert_scenario("Direct stream returns HTTP 403 Forbidden", $streamResD->getStatusCode() === 403);
assert_scenario("Response does NOT leak PDF bytes", !str_contains($streamResD->getContent(), '%PDF-'));

$readerResD = $router->dispatch(new Request('GET', "/bn/library/reader/{$slugD}"));
assert_scenario("Reader redirects non-member with 302 to book detail", $readerResD->getStatusCode() === 302);


// ====================================================================
// SCENARIO E: Member requests a download
// ====================================================================
echo "\n5. Scenario E: Member requests a download:\n";
LibraryService::setRole('paid_member');
Session::set('current_member_code', 'SPS-000872');

$reqInput = [
    'user_name' => 'অমিত সেন (Amit Sen)',
    'user_email' => 'amit.sen.test@sps.org',
    'member_code' => 'SPS-000872',
    'reason' => 'বেদান্ত দর্শন ও রামকথার তুলনামূলক গবেষণাপত্র প্রস্তুতের জন্য অফলাইন কপি আবশ্যক।',
];

$reqPost = new Request('POST', "/bn/library/download-request/{$slugC}", [], $reqInput);
$reqRes = $router->dispatch($reqPost);
assert_scenario("POST download request returns 302 redirect", $reqRes->getStatusCode() === 302);

// Verify request exists in download requests storage
$allDlReqs = LibraryService::getDownloadRequests();
$foundReq = null;
foreach ($allDlReqs as $r) {
    if ($r['book_slug'] === $slugC && $r['user_email'] === 'amit.sen.test@sps.org') {
        $foundReq = $r;
        break;
    }
}
assert_scenario("Download request persisted in storage with status 'pending'", $foundReq !== null && $foundReq['status'] === 'pending');

// Verify Admin sees the request in the admin library desk
AuthService::loginAs('usr_anik');
$adminResE = $router->dispatch(new Request('GET', '/bn/admin/library'));
assert_scenario("Admin can see the download request in admin console", str_contains($adminResE->getContent(), 'amit.sen.test@sps.org') && str_contains($adminResE->getContent(), 'অমিত সেন'));


// ====================================================================
// SCENARIO F: Admin approves request
// ====================================================================
echo "\n6. Scenario F: Admin approves request:\n";
$reqId = $foundReq['id'] ?? '';
$approvePost = new Request('POST', "/bn/admin/library/download-request/{$reqId}", [], [
    'action' => 'approve',
    'expiry_days' => 2,
    'admin_note' => 'গবেষণার সুবিধার্থে ৪৮ ঘণ্টার জন্য ডাউনলোড অনুমোদন দেওয়া হলো।',
]);
$approveRes = $router->dispatch($approvePost);
assert_scenario("Admin approval POST returns 302 redirect", $approveRes->getStatusCode() === 302);

$updatedReq = LibraryService::verifyDownloadToken($slugC, ''); // Check record
$dlRequestsAfter = LibraryService::getDownloadRequests();
$approvedReq = null;
foreach ($dlRequestsAfter as $r) {
    if ($r['id'] === $reqId) {
        $approvedReq = $r;
        break;
    }
}
assert_scenario("Request status is now 'approved'", $approvedReq !== null && $approvedReq['status'] === 'approved');
assert_scenario("Signed temporary download token generated", !empty($approvedReq['download_token']) && str_starts_with($approvedReq['download_token'], 'tok_'));
assert_scenario("Valid expiration timestamp set", !empty($approvedReq['expires_at']) && strtotime($approvedReq['expires_at']) > time());

$activeToken = $approvedReq['download_token'];
$tokenVerified = LibraryService::verifyDownloadToken($slugC, $activeToken);
assert_scenario("Token verification succeeds for book and token", $tokenVerified !== null);

// User downloads file with valid token
LibraryService::setRole('viewer'); // Even as a viewer with token!
$dlWithTokenRes = $router->dispatch(new Request('GET', "/bn/library/download/{$slugC}?token=" . urlencode($activeToken)));
assert_scenario("Download with valid token returns HTTP 200", $dlWithTokenRes->getStatusCode() === 200);
assert_scenario("Content-Disposition is attachment", str_contains($dlWithTokenRes->getHeaders()['Content-Disposition'] ?? '', 'attachment'));
assert_scenario("Downloaded content contains valid PDF signature", str_starts_with($dlWithTokenRes->getContent(), '%PDF-'));


// ====================================================================
// SCENARIO G: Temporary link expires
// ====================================================================
echo "\n7. Scenario G: Temporary link expires:\n";
// Manually expire the token in storage
$dlRequestsExpired = LibraryService::getDownloadRequests();
foreach ($dlRequestsExpired as &$r) {
    if ($r['id'] === $reqId) {
        $r['expires_at'] = date('Y-m-d H:i:s', strtotime('-1 hour'));
        break;
    }
}
unset($r);
file_put_contents(dirname(__DIR__) . '/storage/data/download_requests.json', json_encode($dlRequestsExpired, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

$expiredTokenVerification = LibraryService::verifyDownloadToken($slugC, $activeToken);
assert_scenario("Expired token verification returns null", $expiredTokenVerification === null);

$expiredDlRes = $router->dispatch(new Request('GET', "/bn/library/download/{$slugC}?token=" . urlencode($activeToken)));
assert_scenario("Download attempt with expired token returns 403 Forbidden", $expiredDlRes->getStatusCode() === 403);
assert_scenario("Response indicates link expiration", str_contains($expiredDlRes->getContent(), 'Expired') || str_contains($expiredDlRes->getContent(), 'মেয়াদোত্তীর্ণ'));


// ====================================================================
// SCENARIO H: Direct URL tampering / bypass attempts rejected
// ====================================================================
echo "\n8. Scenario H: User attempts to bypass frontend restrictions:\n";
LibraryService::setRole('viewer');
Session::set('viewer_request_email', null);

// Attempt 1: Fake token download
$tamperedRes1 = $router->dispatch(new Request('GET', "/bn/library/download/{$slugC}?token=hacked_fake_token_12345"));
assert_scenario("Fake token download rejected with HTTP 403", $tamperedRes1->getStatusCode() === 403);

// Attempt 2: Stream locked book directly
$tamperedRes2 = $router->dispatch(new Request('GET', "/bn/library/stream/{$slugC}"));
assert_scenario("Direct stream of locked book rejected with HTTP 403", $tamperedRes2->getStatusCode() === 403);

// Attempt 3: Admin configuration POST without admin authentication
AuthService::logout();
$tamperedRes3 = $router->dispatch(new Request('POST', "/bn/admin/library/book/{$slugC}/update", [], [
    'reading_access' => 'public',
]));
assert_scenario("Unauthorized book config modification redirects to login (302)", $tamperedRes3->getStatusCode() === 302);


echo "\n--------------------------------------------------------------------\n";
echo "Total Scenario Tests: " . ($passed + $failed) . " | Passed: {$passed} | Failed: {$failed}\n";
echo "====================================================================\n";

if ($failed > 0) exit(1);
