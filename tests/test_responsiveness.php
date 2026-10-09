<?php
/**
 * SPS Responsive Architecture Automated Verification Suite
 * Validates Google Window Size Classes & Apple HIG Viewport Standards
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

$appConfig = require dirname(__DIR__) . '/config/app.php';
$langConfig = require dirname(__DIR__) . '/config/languages.php';
\App\Core\View::init(dirname(__DIR__) . '/app/Views');
$router = new \App\Core\Router();
require dirname(__DIR__) . '/routes/web.php';

$errors = [];
$passes = 0;

function assertCondition(bool $condition, string $message) {
    global $passes, $errors;
    if ($condition) {
        $passes++;
        echo "[\033[32mPASS\033[0m] $message\n";
    } else {
        $errors[] = $message;
        echo "[\033[31mFAIL\033[0m] $message\n";
    }
}

echo "====================================================\n";
echo "SPS Responsive Architecture Verification Suite\n";
echo "====================================================\n\n";

$mainCss = file_get_contents(__DIR__ . '/../public/assets/css/main.css');
$componentsCss = file_get_contents(__DIR__ . '/../public/assets/css/components.css');
$tokensCss = file_get_contents(__DIR__ . '/../public/assets/css/tokens.css');
$homeView = file_get_contents(__DIR__ . '/../app/Views/pages/home.php');

// 1. Check Container Constraint to 1280px
assertCondition(
    strpos($tokensCss, '--container-max: 1280px;') !== false,
    "Tokens: --container-max is set to 1280px"
);
assertCondition(
    strpos($mainCss, 'width: min(100% - 48px, 1280px);') !== false,
    "Main CSS: .container constrained to width: min(100% - 48px, 1280px)"
);
assertCondition(
    strpos($componentsCss, 'width: min(100% - 48px, 1280px);') !== false,
    "Components CSS: .header-inner constrained to width: min(100% - 48px, 1280px)"
);

// 2. Check 5 Window Size Classes Breakpoints in Main CSS
assertCondition(
    strpos($mainCss, '@media (max-width: 599px)') !== false,
    "Breakpoints: Compact Mobile (< 600px) class defined"
);
assertCondition(
    strpos($mainCss, '@media (min-width: 600px) and (max-width: 839px)') !== false,
    "Breakpoints: Medium Tablet (600px – 839px) class defined"
);
assertCondition(
    strpos($mainCss, '@media (min-width: 840px) and (max-width: 1199px)') !== false,
    "Breakpoints: Expanded (840px – 1199px) class defined"
);
assertCondition(
    strpos($mainCss, '@media (min-width: 1200px) and (max-width: 1599px)') !== false,
    "Breakpoints: Desktop (1200px – 1599px) class defined"
);
assertCondition(
    strpos($mainCss, '@media (min-width: 1600px)') !== false,
    "Breakpoints: Large Desktop (>= 1600px) class defined"
);

// 3. Prohibit Device-Specific Media Queries (Anti-Pattern Check)
assertCondition(
    !preg_match('/@media[^{]*\(\s*width:\s*390px\s*\)/i', $mainCss) &&
    !preg_match('/@media[^{]*\(\s*width:\s*393px\s*\)/i', $mainCss) &&
    !preg_match('/@media[^{]*\(\s*width:\s*430px\s*\)/i', $mainCss),
    "Anti-Pattern: No device-specific @media (width: 390px) queries"
);

// 4. Fluid Typography with clamp()
assertCondition(
    strpos($mainCss, 'font-size: clamp(') !== false,
    "Fluid Typography: clamp() used in main.css"
);
assertCondition(
    preg_match('/\.hero-title\s*\{[^}]*font-size:\s*clamp\(/', $mainCss) === 1,
    "Fluid Typography: .hero-title uses clamp()"
);
assertCondition(
    preg_match('/\.section-title\s*\{[^}]*font-size:\s*clamp\(/', $mainCss) === 1,
    "Fluid Typography: .section-title uses clamp()"
);

// 5. Fluid Spacing with clamp()
assertCondition(
    preg_match('/\.editorial-section\s*\{[^}]*padding-block:\s*clamp\(/', $mainCss) === 1,
    "Fluid Spacing: .editorial-section uses clamp() for vertical rhythm"
);

// 6. Touch Target (Apple HIG & WCAG Standard)
assertCondition(
    strpos($tokensCss, '--touch-target-min: 44px;') !== false,
    "Tokens: --touch-target-min is 44px"
);
assertCondition(
    strpos($componentsCss, 'min-height: var(--touch-target-min, 44px);') !== false,
    "Components: .btn has min-height: 44px touch target"
);
assertCondition(
    strpos($componentsCss, 'width: var(--touch-target-min, 44px);') !== false,
    "Components: .mobile-menu-btn has min-size: 44px"
);
assertCondition(
    strpos($homeView, 'id="activity-slide-prev"') !== false &&
    strpos($homeView, 'width:44px; height:44px;') !== false,
    "Components: Activities slider previous button is min 44x44px"
);
assertCondition(
    strpos($homeView, 'id="activity-slide-next"') !== false &&
    strpos($homeView, 'width:44px; height:44px;') !== false,
    "Components: Activities slider next button is min 44x44px"
);

// 7. Semantic Grids and 1-Slide Activities Slider
assertCondition(
    strpos($homeView, 'class="stats-grid"') !== false,
    "Markup: .stats-grid present in home.php"
);
assertCondition(
    strpos($homeView, 'class="pillars-grid"') !== false,
    "Markup: .pillars-grid present in home.php"
);
assertCondition(
    strpos($homeView, 'class="activities-slider-track"') !== false,
    "Markup: .activities-slider-track present in home.php"
);
assertCondition(
    strpos($homeView, 'class="knowledge-grid"') !== false,
    "Markup: .knowledge-grid present in home.php"
);
assertCondition(
    strpos($homeView, 'class="publications-grid"') !== false,
    "Markup: .publications-grid present in home.php"
);
assertCondition(
    strpos($homeView, 'class="blog-grid"') !== false,
    "Markup: .blog-grid present in home.php"
);
assertCondition(
    strpos($homeView, 'class="join-grid"') !== false,
    "Markup: .join-grid present in home.php"
);

// 8. Mobile 1-Slide CSS Rule for Activities Slider
assertCondition(
    strpos($mainCss, 'scroll-snap-type: x mandatory;') !== false,
    "Activities Slider: CSS scroll-snap-type mandatory enabled for smooth touch swipe"
);
assertCondition(
    strpos($mainCss, 'scroll-snap-align: center;') !== false,
    "Activities Slider: CSS scroll-snap-align: center enabled for 1 slide on mobile"
);

// 9. Rendered HTML Check via Router
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/bn';
\App\Core\I18n::init($langConfig, 'bn', '/bn');
$reqBn = new \App\Core\Request();
$resBn = $router->dispatch($reqBn);
ob_start();
$resBn->send();
$renderedBn = ob_get_clean();

assertCondition(
    strpos($renderedBn, 'class="stats-grid"') !== false,
    "Rendered /bn: Contains .stats-grid"
);
assertCondition(
    strpos($renderedBn, 'class="pillars-grid"') !== false,
    "Rendered /bn: Contains .pillars-grid"
);
assertCondition(
    strpos($renderedBn, 'id="activities-slider"') !== false,
    "Rendered /bn: Contains #activities-slider"
);

// 10. Admin Login Center Alignment Verification
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/en/admin/login';
\App\Core\I18n::init($langConfig, 'en', '/en/admin/login');
$reqAdmin = new \App\Core\Request();
$resAdmin = $router->dispatch($reqAdmin);
ob_start();
$resAdmin->send();
$renderedAdmin = ob_get_clean();

assertCondition(
    $resAdmin->getStatusCode() === 200,
    "Admin Login: /en/admin/login returns 200 OK"
);
assertCondition(
    strpos($renderedAdmin, 'class="admin-login-page-wrap"') !== false,
    "Admin Login: Has .admin-login-page-wrap container"
);
assertCondition(
    strpos($renderedAdmin, 'margin: 0 auto;') !== false,
    "Admin Login: .login-card has margin: 0 auto for center alignment"
);
assertCondition(
    strpos($renderedAdmin, 'justify-content: center;') !== false,
    "Admin Login: Wrapper has justify-content: center flex alignment"
);
assertCondition(
    substr_count($renderedAdmin, '<html') === 1,
    "Admin Login: Clean valid document structure (single <html tag)"
);
assertCondition(
    substr_count($renderedAdmin, '<body') === 1,
    "Admin Login: Clean valid document structure (single <body tag)"
);

echo "\n----------------------------------------------------\n";
echo "Total Tests: " . ($passes + count($errors)) . " | Passed: $passes | Failed: " . count($errors) . "\n";
echo "====================================================\n";

if (count($errors) > 0) {
    exit(1);
}
exit(0);
