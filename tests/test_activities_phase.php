<?php

/**
 * SPS Activities & Project Logs Verification Suite
 */

declare(strict_types=1);

namespace App\Tests;

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\App;
use App\Core\I18n;
use App\Core\Request;
use App\Core\Router;
use App\Core\View;
use App\Services\ActivityService;

$passed = 0;
$failed = 0;

function assert_test(string $name, bool $condition, string $detail = ''): void {
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo "[PASS] {$name}\n";
    } else {
        $failed++;
        echo "[FAIL] {$name}" . ($detail ? " - {$detail}" : "") . "\n";
    }
}

echo "====================================================\n";
echo "SPS Activities & Grassroots Seva Verification Suite\n";
echo "====================================================\n\n";

$app = new App(dirname(__DIR__));
$langConfig = require dirname(__DIR__) . '/config/languages.php';
I18n::init($langConfig, 'bn', '/bn');
View::init(dirname(__DIR__) . '/app/Views');

// 1. Service Layer Tests
$all = ActivityService::getActivities();
assert_test("ActivityService: Multiple activities registered", count($all) >= 15, "Count: " . count($all));

$byYear = ActivityService::getActivitiesByYear();
assert_test("ActivityService: Covers years 2020 through 2026", isset($byYear[2020], $byYear[2021], $byYear[2022], $byYear[2023], $byYear[2024], $byYear[2025], $byYear[2026]));

$flagships = ActivityService::getFlagships();
assert_test("ActivityService: Exactly 6 flagship programs registered", count($flagships) === 6);

$tracked = ActivityService::getTrackedProjects();
assert_test("ActivityService: Notion database projects tracked", count($tracked) >= 5);

$stats = ActivityService::getStats();
assert_test("ActivityService: Stats include trees planted (9,500+)", ($stats['trees_planted'] ?? '') === '9,500+');
assert_test("ActivityService: Stats include pilgrims served (20,000+)", ($stats['gita_reciters_served'] ?? '') === '20,000+');

// 2. Router & View Rendering Tests (Bengali)
$router = new Router();
require dirname(__DIR__) . '/routes/web.php';

I18n::init($langConfig, 'bn', '/bn/activities');
$reqBn = new Request('GET', '/bn/activities');
$resBn = $router->dispatch($reqBn);

assert_test("Router: /bn/activities returns HTTP 200", $resBn->getStatusCode() === 200);

$htmlBn = $resBn->getContent();
assert_test("View BN: Contains hero headline", str_contains($htmlBn, 'সেবাই সনাতন ধর্ম'));
assert_test("View BN: Contains 2024 Ram Navami Tree Plantation", str_contains($htmlBn, 'একটি গাছ, একটি প্রাণ') && str_contains($htmlBn, '৯,৫০০+'));
assert_test("View BN: Contains Kantaji Temple Gita Seva", str_contains($htmlBn, 'কান্তজিউ') && str_contains($htmlBn, '২০,০০০+'));
assert_test("View BN: Contains Chandranath Dham Seva", str_contains($htmlBn, 'চন্দ্রনাথ ধাম') && str_contains($htmlBn, 'মহাশিবরাত্রি'));
assert_test("View BN: Contains 2023 Ghar Wapsi in Manikchhari", str_contains($htmlBn, 'মানিকছড়ি') && str_contains($htmlBn, 'ত্রিপুরা'));
assert_test("View BN: Contains 2026 Medical Priority (Gouranga Shill)", str_contains($htmlBn, 'গৌরাঙ্গ চন্দ্র শীল'));
assert_test("View BN: Contains 2021 Shalla Relief", str_contains($htmlBn, 'শাল্লা'));
assert_test("View BN: Contains 2020 Inception Contest", str_contains($htmlBn, 'আমার পূজা ছবি ও গল্প'));
assert_test("View BN: Contains Flagship Gita Adarsha Lipi", str_contains($htmlBn, 'গীতা আদর্শ লিপি'));
assert_test("View BN: Contains Flagship 10 Taka Project", str_contains($htmlBn, 'সনাতনী ১০ টাকা প্রজেক্ট'));
assert_test("View BN: Contains Central Project Tracker Table", str_contains($htmlBn, 'এসপিএস কেন্দ্রীয় প্রজেক্ট ট্র্যাকার'));
assert_test("View BN: Excludes Notion external link", !str_contains($htmlBn, 'economic-sodalite-62f.notion.site'));
assert_test("View BN: Contains Visitor Guide box", str_contains($htmlBn, 'আপনি যদি সাধারণ পরিদর্শক বা শুভানুধ্যায়ী হন'));
assert_test("View BN: Contains Member Guide box", str_contains($htmlBn, 'এসপিএস সাধারণ সদস্য ও জেলা ইউনিটের জন্য'));

// 3. Router & View Rendering Tests (English)
I18n::init($langConfig, 'en', '/en/activities');
$reqEn = new Request('GET', '/en/activities');
$resEn = $router->dispatch($reqEn);

assert_test("Router: /en/activities returns HTTP 200", $resEn->getStatusCode() === 200);

$htmlEn = $resEn->getContent();
assert_test("View EN: Contains English headline", str_contains($htmlEn, 'Sanatan Dharma in Action'));
assert_test("View EN: Contains Tree Plantation", str_contains($htmlEn, 'One Tree, One Life'));
assert_test("View EN: Contains Kantajew Temple Seva", str_contains($htmlEn, 'Kantajew Temple'));
assert_test("View EN: Contains Chandranath Dham", str_contains($htmlEn, 'Chandranath Dham'));
assert_test("View EN: Excludes Notion external link", !str_contains($htmlEn, 'economic-sodalite-62f.notion.site'));
assert_test("View EN: Contains Orientation for Visitors", str_contains($htmlEn, 'For General Visitors & Well-Wishers'));
assert_test("View EN: Contains Orientation for Active Members", str_contains($htmlEn, 'For Registered Members & District Units'));

echo "\n----------------------------------------------------\n";
echo "Total Tests: " . ($passed + $failed) . " | Passed: {$passed} | Failed: {$failed}\n";
echo "====================================================\n";

if ($failed > 0) exit(1);
