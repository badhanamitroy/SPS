<?php

declare(strict_types=1);

namespace App\Tests;

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\App;
use App\Core\I18n;
use App\Core\Request;
use App\Core\Router;
use App\Core\View;
use App\Services\ExecutiveService;

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
echo "SPS Executive Committee & Social Channels Suite\n";
echo "====================================================\n";

$app = new App(dirname(__DIR__));
I18n::init(require dirname(__DIR__) . '/config/languages.php', 'bn', '/bn');
View::init(dirname(__DIR__) . '/app/Views');

// 1. Executive Committee Count & Integrity
$executives = ExecutiveService::getExecutives();
assert_test("Executives: Exactly 17 committee members registered", count($executives) === 17);

$expectedNames = [
    'Anik Kumar Saha', 'Robin Dey', 'Likhon Ghosh', 'Joy Chakraborty',
    'Roy Kallol', 'Gourab Chy', 'Ananda Krishna Anu Das', 'Ani Ta', 'Pushpita Roy',
    'Pranto Saha', 'Goutam Dandapat', 'Rahul Kumar Sutradhar',
    'Sanjoy Chandra Das', 'Shyamoli Das', 'Sree Ankan Bhattacharjee',
    'Badhan Roy', 'Partha Pratim Dutta'
];

foreach ($expectedNames as $name) {
    $found = false;
    foreach ($executives as $ex) {
        if ($ex['name_en'] === $name) {
            $found = true;
            break;
        }
    }
    assert_test("Executive registered: {$name}", $found);
}

// 2. Photos Existence in public/assets/images/executives/
$photoKeys = [
    'anik-kumar-saha.png',
    'robin-dey.png',
    'likhon-ghosh.jpg',
    'joy-chakraborty.png',
    'badhan-roy.jpg',
    'partha-pratim-dutta.png'
];
foreach ($photoKeys as $pKey) {
    $fullPath = dirname(__DIR__) . '/public/assets/images/executives/' . $pKey;
    assert_test("Executive photo asset exists: {$pKey}", file_exists($fullPath) && filesize($fullPath) > 5000);
}

// 3. Social Media & Contacts in Configuration
$social = config('app.social');
assert_test("Social: Facebook URL configured", ($social['facebook'] ?? '') === 'https://www.facebook.com/bewithsps');
assert_test("Social: YouTube URL configured", ($social['youtube'] ?? '') === 'https://www.youtube.com/@spsofficial1529');
assert_test("Social: Blog URL configured", ($social['blog'] ?? '') === 'https://sanatanphilosophyandscripture.blogspot.com');
assert_test("Social: Instagram URL configured", ($social['instagram'] ?? '') === 'https://www.instagram.com/bewithsps/');

$contacts = config('app.contacts');
assert_test("Contacts: Primary helpline configured", ($contacts['primary'] ?? '') === '+8801736360041');
assert_test("Contacts: Secondary helpline configured", ($contacts['secondary'] ?? '') === '+880 1782-009415');

// 4. About Page Router & Content Test
$router = new Router();
require dirname(__DIR__) . '/routes/web.php';

$aboutReq = new Request('GET', '/bn/about');
$resAbout = $router->dispatch($aboutReq);
assert_test("Router: /bn/about returns 200", $resAbout->getStatusCode() === 200);

$content = $resAbout->getContent();
assert_test("About Page: Displays Anik Kumar Saha (President)", str_contains($content, 'অনিক কুমার সাহা') && str_contains($content, 'সভাপতি'));
assert_test("About Page: Displays Robin Dey (General Secretary)", str_contains($content, 'রবিন দে') && str_contains($content, 'সাধারণ সম্পাদক'));
assert_test("About Page: Displays Likhon Ghosh (Organizing Secretary)", str_contains($content, 'লিখন ঘোষ') && str_contains($content, 'সাংগঠনিক সম্পাদক'));
assert_test("About Page: Displays Joy Chakraborty (Treasurer)", str_contains($content, 'জয় চক্রবর্তী') && str_contains($content, 'কোষাধ্যক্ষ'));
assert_test("About Page: Displays Pranto Saha (Scripture 1)", str_contains($content, 'প্রান্ত সাহা'));
assert_test("About Page: Displays Badhan Roy (Publishing 1)", str_contains($content, 'বাঁধন রায়'));
assert_test("About Page: Excludes administrative scope and Super-Admin label", !str_contains($content, 'Super-Admin') && !str_contains($content, 'Everything (Full Platform') && !str_contains($content, 'Finance-Admin'));
assert_test("About Page: Displays Official Facebook link", str_contains($content, 'facebook.com/bewithsps'));
assert_test("About Page: Displays Official YouTube link", str_contains($content, 'youtube.com/@spsofficial1529'));
assert_test("About Page: Displays Official Contact number", str_contains($content, '+880 1736-360041'));

\App\Core\I18n::init(config('languages'), 'en', '/en/about');
$aboutReqEn = new Request('GET', '/en/about');
$resAboutEn = $router->dispatch($aboutReqEn);
$enContent = $resAboutEn->getContent();
assert_test("About Page EN: Excludes administrative scope and Super-Admin label", !str_contains($enContent, 'Super-Admin') && !str_contains($enContent, 'Everything (Full Platform'));
assert_test("About Page EN: Displays President designation", str_contains($enContent, 'President') && str_contains($enContent, 'Anik Kumar Saha'));

// 5. Footer Content Test
$footerHtml = View::component('footer');
assert_test("Footer: Contains Facebook link", str_contains($footerHtml, 'facebook.com/bewithsps'));
assert_test("Footer: Contains YouTube link", str_contains($footerHtml, 'youtube.com/@spsofficial1529'));
assert_test("Footer: Contains helpline phone", str_contains($footerHtml, '+880 1736-360041'));

echo "\n----------------------------------------------------\n";
echo "Total Tests: " . ($passed + $failed) . " | Passed: {$passed} | Failed: {$failed}\n";
echo "====================================================\n";

if ($failed > 0) exit(1);
