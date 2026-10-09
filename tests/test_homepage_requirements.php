<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

$appConfig = require dirname(__DIR__) . '/config/app.php';
$langConfig = require dirname(__DIR__) . '/config/languages.php';
\App\Core\View::init(dirname(__DIR__) . '/app/Views');
$router = new \App\Core\Router();
require dirname(__DIR__) . '/routes/web.php';

$passed = 0;
$failed = 0;

function assertTest(bool $condition, string $label, string $details = '') {
    global $passed, $failed;
    if ($condition) {
        echo "[PASS] " . $label . "\n";
        $passed++;
    } else {
        echo "[FAIL] " . $label . ($details ? " -> " . $details : "") . "\n";
        $failed++;
    }
}

echo "====================================================\n";
echo "SPS Homepage Specific Edits Verification Suite\n";
echo "====================================================\n\n";

// Test 1: Fetch Bengali Homepage
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/bn';
\App\Core\I18n::init($langConfig, 'bn', '/bn');
$reqBn = new \App\Core\Request();
$resBn = $router->dispatch($reqBn);
ob_start();
$resBn->send();
$htmlBn = ob_get_clean();

// Test 2: Fetch English Homepage
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/en';
\App\Core\I18n::init($langConfig, 'en', '/en');
$reqEn = new \App\Core\Request();
$resEn = $router->dispatch($reqEn);
ob_start();
$resEn->send();
$htmlEn = ob_get_clean();

// 1. Header Checks: Only SPS beside LOGO, no subtitle
assertTest(strpos($htmlBn, '<span class="brand-title">SPS</span>') !== false, 'Header (BN): Brand title is strictly SPS');
assertTest(strpos($htmlEn, '<span class="brand-title">SPS</span>') !== false, 'Header (EN): Brand title is strictly SPS');
assertTest(strpos($htmlBn, 'class="brand-subtitle"') === false, 'Header (BN): No brand-subtitle present in header');
assertTest(strpos($htmlEn, 'class="brand-subtitle"') === false, 'Header (EN): No brand-subtitle present in header');

// 2. Hero Eyebrow Checks (Must be removed in both versions)
assertTest(strpos($htmlBn, 'hero-pretitle') === false, 'Hero (BN): Eyebrow pretitle is removed');
assertTest(strpos($htmlEn, 'hero-pretitle') === false, 'Hero (EN): Eyebrow pretitle is removed');

// 3. Hero Title & Slogan Checks
assertTest(strpos($htmlBn, 'Sanatan Philosophy and Scripture') !== false, 'Hero (BN): Main heading is SPS full form "Sanatan Philosophy and Scripture"');
assertTest(strpos($htmlEn, 'Sanatan Philosophy and Scripture') !== false, 'Hero (EN): Main heading is SPS full form "Sanatan Philosophy and Scripture"');
assertTest(strpos($htmlBn, 'সনাতনী ঐক্য, প্রচার ও কল্যাণে অবিচল') !== false, 'Hero (BN): Slogan "সনাতনী ঐক্য, প্রচার ও কল্যাণে অবিচল" is present below title');
assertTest(strpos($htmlEn, 'সনাতনী ঐক্য, প্রচার ও কল্যাণে অবিচল') !== false, 'Hero (EN): Slogan "সনাতনী ঐক্য, প্রচার ও কল্যাণে অবিচল" is present below title');

// 4. Verification that old heading is replaced
assertTest(strpos($htmlEn, 'From Scriptural Knowledge to Humanitarian Action') === false, 'Hero (EN): Old heading "From Scriptural Knowledge..." replaced');
assertTest(strpos($htmlBn, 'শাস্ত্রের জ্ঞান থেকে মানবসেবার কর্মযাত্রা') === false, 'Hero (BN): Old heading "শাস্ত্রের জ্ঞান থেকে..." replaced');

// 5. Pillars heading checks
assertTest(strpos($htmlEn, 'The Four Pillars of SPS') !== false, 'Pillars (EN): Section heading is "The Four Pillars of SPS"');
assertTest(strpos($htmlBn, 'SPS-এর ৪টি স্তম্ভ') !== false, 'Pillars (BN): Section heading is "SPS-এর ৪টি স্তম্ভ"');

// 6. Official Social Presence Checks
assertTest(strpos($htmlBn, 'https://www.facebook.com/bewithsps?utm_source=chatgpt.com') !== false, 'Social: Official Facebook Page link present in header/footer/community');
assertTest(strpos($htmlBn, 'https://www.facebook.com/groups/278337526756568?utm_source=chatgpt.com') !== false, 'Social: Official Facebook Group link present in header/footer/community');

// 7. 12 Sections check
assertTest(strpos($htmlBn, 'id="hero"') !== false, 'Sections: Hero section present');
assertTest(strpos($htmlBn, 'id="stats"') !== false, 'Sections: Quick impact numbers section present');
assertTest(strpos($htmlBn, 'id="about"') !== false, 'Sections: What is SPS section present');
assertTest(strpos($htmlBn, 'id="pillars"') !== false, 'Sections: 4 Pillars section present');
assertTest(strpos($htmlBn, 'id="activities"') !== false, 'Sections: Live activities slider section present');
assertTest(strpos($htmlBn, 'id="knowledge"') !== false, 'Sections: Knowledge and scripture section present');
assertTest(strpos($htmlBn, 'id="scripture"') !== false, 'Sections: Today scripture section present');
assertTest(strpos($htmlBn, 'id="publications"') !== false, 'Sections: Latest publications section present');
assertTest(strpos($htmlBn, 'id="blog"') !== false, 'Sections: Member blog section present');
assertTest(strpos($htmlBn, 'id="transparency"') === false, 'Sections: Financial transparency section removed from homepage');
assertTest(strpos($htmlBn, 'id="community"') !== false, 'Sections: Social community section present');
assertTest(strpos($htmlBn, 'id="join"') !== false, 'Sections: Join / Participate section present');

// 8. Navigation check: Exact 5 requested items
assertTest(strpos($htmlEn, 'About us') !== false, 'Header (EN): "About us" link present');
assertTest(strpos($htmlEn, 'Our Activities') !== false, 'Header (EN): "Our Activities" link present');
assertTest(strpos($htmlEn, 'Blogs') !== false, 'Header (EN): "Blogs" link present');
assertTest(strpos($htmlEn, 'SPS Library') !== false, 'Header (EN): "SPS Library" link present');
assertTest(strpos($htmlEn, 'Join us') !== false, 'Header (EN): "Join us" link present');

assertTest(strpos($htmlBn, 'আমাদের সম্পর্কে') !== false, 'Header (BN): "আমাদের সম্পর্কে" link present');
assertTest(strpos($htmlBn, 'আমাদের কার্যক্রম') !== false, 'Header (BN): "আমাদের কার্যক্রম" link present');
assertTest(strpos($htmlBn, 'ব্লগ') !== false, 'Header (BN): "ব্লগ" link present');
assertTest(strpos($htmlBn, 'SPS লাইব্রেরি') !== false, 'Header (BN): "SPS লাইব্রেরি" link present');
assertTest(strpos($htmlBn, 'যোগ দিন') !== false, 'Header (BN): "যোগ দিন" link present');

assertTest(strpos($htmlBn, 'class="nav-desktop"') !== false && !preg_match('/<nav class="nav-desktop">.*?\/transparency.*?<\/nav>/s', $htmlBn), 'Header (BN): Transparency link removed from header nav');
assertTest(strpos($htmlEn, 'class="nav-desktop"') !== false && !preg_match('/<nav class="nav-desktop">.*?\/transparency.*?<\/nav>/s', $htmlEn), 'Header (EN): Transparency link removed from header nav');

echo "\n----------------------------------------------------\n";
echo "Total Tests: " . ($passed + $failed) . " | Passed: $passed | Failed: $failed\n";
echo "====================================================\n";
exit($failed > 0 ? 1 : 0);
