<?php

declare(strict_types=1);

/**
 * Test T7: JSON Store Safety (Exclusive flock spanning read-modify-write + Atomic writes)
 */

require_once __DIR__ . '/../app/Core/Env.php';
require_once __DIR__ . '/../app/Core/Session.php';
require_once __DIR__ . '/../app/Core/CryptoService.php';
require_once __DIR__ . '/../app/Core/HtmlSanitizer.php';
require_once __DIR__ . '/../app/Services/AuditService.php';
require_once __DIR__ . '/../app/Services/MembershipService.php';
require_once __DIR__ . '/../app/Services/BlogService.php';

use App\Services\MembershipService;
use App\Services\BlogService;

$passed = 0;
$failed = 0;

function assertCondition(bool $cond, string $msg): void {
    global $passed, $failed;
    if ($cond) {
        echo "[PASS] {$msg}\n";
        $passed++;
    } else {
        echo "[FAIL] {$msg}\n";
        $failed++;
    }
}

echo "=== Running T7: JSON Store Safety Tests ===\n\n";

// Test 1: Atomic write creates file with exact content
$testDir = __DIR__ . '/../storage/data';
$testPath = $testDir . '/_test_atomic_' . bin2hex(random_bytes(4)) . '.json';
$testData = ['test_key' => 'atomic_val_1', 'timestamp' => microtime(true)];

$writeRes = MembershipService::atomicWrite($testPath, json_encode($testData));
assertCondition($writeRes === true, "MembershipService::atomicWrite returns true on write");
assertCondition(file_exists($testPath), "Atomic write file exists on disk");
$readBack = json_decode((string)file_get_contents($testPath), true);
assertCondition(is_array($readBack) && ($readBack['test_key'] ?? '') === 'atomic_val_1', "Atomic write preserved data integrity");

// Test 2: Atomic write overwrites existing file without leaving temp files behind
$testData2 = ['test_key' => 'atomic_val_overwritten', 'timestamp' => microtime(true)];
$writeRes2 = BlogService::atomicWrite($testPath, json_encode($testData2));
assertCondition($writeRes2 === true, "BlogService::atomicWrite returns true on overwrite");
$readBack2 = json_decode((string)file_get_contents($testPath), true);
assertCondition(is_array($readBack2) && ($readBack2['test_key'] ?? '') === 'atomic_val_overwritten', "Atomic overwrite replaced content cleanly");

// Check no temp files leaked in directory
$matchingTmp = glob($testPath . '.tmp_*');
assertCondition(empty($matchingTmp), "No temporary write files remain in storage directory");
@unlink($testPath);

// Test 3: withExclusiveLock reentrancy
$reentrantExecuted = false;
$lockResult = MembershipService::withExclusiveLock(function () use (&$reentrantExecuted) {
    return MembershipService::withExclusiveLock(function () use (&$reentrantExecuted) {
        $reentrantExecuted = true;
        return 'nested_lock_ok';
    });
});
assertCondition($reentrantExecuted === true && $lockResult === 'nested_lock_ok', "MembershipService::withExclusiveLock supports reentrant nested calls");

// Test 4: BlogService withExclusiveLock reentrancy
$blogReentrant = false;
$blogLockResult = BlogService::withExclusiveLock(function () use (&$blogReentrant) {
    return BlogService::withExclusiveLock(function () use (&$blogReentrant) {
        $blogReentrant = true;
        return 'blog_nested_ok';
    });
});
assertCondition($blogReentrant === true && $blogLockResult === 'blog_nested_ok', "BlogService::withExclusiveLock supports reentrant nested calls");

// Test 5: Verify lock file existence
$memLockFile = dirname(MembershipService::getStoragePath()) . '/.membership.lock';
$blogLockFile = dirname(BlogService::getStoragePath()) . '/.blogs.lock';
assertCondition(file_exists($memLockFile), "Membership lock file exists");
assertCondition(file_exists($blogLockFile), "Blog lock file exists");

// Test 6: Verify BlogService read-modify-write under lock preserves valid JSON and format
$initialBlogs = BlogService::getBlogs(false);
$testSlug = 'test-lock-post-' . bin2hex(random_bytes(4));
$createdBlog = BlogService::createBlog([
    'title_en' => 'Test Concurrency Post',
    'title_bn' => 'টেস্ট পোস্ট',
    'category' => 'vedanta',
    'content_en' => '<p>Test content under lock</p>',
    'content_bn' => '<p>টেস্ট বিষয়বস্তু</p>',
    'tags' => 'test,concurrency'
], [
    'name_en' => 'Tester',
    'name_bn' => 'টেস্টার',
    'email' => 'tester@sps.org',
    'role' => 'paid_member'
]);
assertCondition(!empty($createdBlog['id']) && $createdBlog['status'] === 'pending', "BlogService::createBlog executes cleanly under lock");

// Toggle like under lock
$likeRes = BlogService::toggleLike($createdBlog['slug'], '127.0.0.1');
assertCondition(!empty($likeRes['success']) && $likeRes['liked'] === true, "BlogService::toggleLike executes under lock");

// Verify blogs.json remains completely valid JSON on disk
$rawBlogJson = file_get_contents(BlogService::getStoragePath());
$decodedBlogs = json_decode($rawBlogJson, true);
assertCondition(is_array($decodedBlogs) && count($decodedBlogs) > count($initialBlogs), "Storage file remains completely valid JSON after locked operations");

// Clean up created blog
$adminUser = ['role' => 'super_admin', 'super_role' => 'super_admin', 'name_en' => 'Test Admin'];
BlogService::deleteBlog($createdBlog['id'], $adminUser);

$finalBlogs = BlogService::getBlogs(false);
assertCondition(count($finalBlogs) === count($initialBlogs), "BlogService::deleteBlog cleans up successfully under lock");

echo "\nSummary: Passed: {$passed}, Failed: {$failed}\n";
exit($failed > 0 ? 1 : 0);
