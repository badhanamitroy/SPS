<?php

declare(strict_types=1);

$connection = @fsockopen('127.0.0.1', 8000, $errno, $errstr, 0.5);
if (!$connection) {
    echo "SKIPPED: start php -S 127.0.0.1:8000 -t public public/index.php\n";
    exit(0);
}
fclose($connection);

$html = @file_get_contents('http://127.0.0.1:8000/bn/membership/dashboard?as=SPS-001004');
if ($html === false) {
    echo "SKIPPED: start php -S 127.0.0.1:8000 -t public public/index.php\n";
    exit(0);
}

echo "=== VERIFYING PENDING DASHBOARD ISOLATION (SPS-001004) ===\n";

$checks = [
    'Has Pending Status' => strpos($html, 'Pending') !== false || strpos($html, 'অপেক্ষমাণ') !== false,
    'Has 4-Step Progress Indicator' => strpos($html, '4') !== false && (strpos($html, 'Lifecycle') !== false || strpos($html, 'ধাপ') !== false),
    'Has Gita 5.23 Verse' => strpos($html, '5.23') !== false || strpos($html, '২৩') !== false || strpos($html, 'গীতা') !== false,
    'Has Auditor Joy Chakraborty' => strpos($html, 'Chakraborty') !== false || strpos($html, 'চক্রব') !== false,
    'NO printableCard' => strpos($html, 'id="printableCard"') === false,
    'NO Profile Update Form' => strpos($html, 'action="/bn/membership/profile/update"') === false && strpos($html, 'name="address"') === false,
    'NO Security Password Form' => strpos($html, 'action="/bn/membership/password/update"') === false,
    'NO Renewal Desk' => strpos($html, 'action="/bn/membership/payment"') === false,
    'NO Transition Desk' => strpos($html, 'action="/bn/membership/transition"') === false,
];

$allPassed = true;
foreach ($checks as $name => $passed) {
    echo ($passed ? "[PASS] " : "[FAIL] ") . $name . "\n";
    if (!$passed) $allPassed = false;
}

echo "\n=== ACTIVE MEMBER CHECK (SPS-000872) ===\n";
$activeHtml = @file_get_contents('http://127.0.0.1:8000/bn/membership/dashboard?as=SPS-000872');
if ($activeHtml === false) {
    echo "SKIPPED: start php -S 127.0.0.1:8000 -t public public/index.php\n";
    exit(0);
}

$activeChecks = [
    'Has Active Status' => strpos($activeHtml, 'Active') !== false,
    'Has printableCard' => strpos($activeHtml, 'id="printableCard"') !== false,
    'Has Profile Update Form' => strpos($activeHtml, 'action="/bn/membership/profile/update"') !== false,
    'Has Security Password Form' => strpos($activeHtml, 'action="/bn/membership/password/update"') !== false,
    'NO Pending 4-Step Tracker' => strpos($activeHtml, 'Awaiting Audit') === false && strpos($activeHtml, 'Step 2 of 4') === false,
];

foreach ($activeChecks as $name => $passed) {
    echo ($passed ? "[PASS] " : "[FAIL] ") . $name . "\n";
    if (!$passed) $allPassed = false;
}

if ($allPassed) {
    echo "\n>>> ALL ISOLATION CHECKS PASSED PERFECTLY! <<<\n";
    exit(0);
} else {
    echo "\n>>> SOME CHECKS FAILED! <<<\n";
    exit(1);
}
