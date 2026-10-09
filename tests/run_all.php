<?php

declare(strict_types=1);

/**
 * SPS Test Runner
 * Runs every tests/test_*.php via PHP CLI, prints pass/fail per file, exits non-zero on failure.
 */

$testDir = __DIR__;
$testFiles = glob($testDir . '/test_*.php');
sort($testFiles);

if (empty($testFiles)) {
    echo "No test files matching test_*.php found in {$testDir}\n";
    exit(1);
}

$phpBinary = PHP_BINARY ?: 'php';

echo "====================================================================\n";
echo "SPS COMPREHENSIVE AUTOMATED TEST RUNNER\n";
echo "Found " . count($testFiles) . " test suites in {$testDir}\n";
echo "PHP Binary: {$phpBinary}\n";
echo "====================================================================\n\n";

$passedCount = 0;
$failedCount = 0;
$results = [];
$startTime = microtime(true);

foreach ($testFiles as $testFile) {
    $basename = basename($testFile);
    $cmd = escapeshellarg($phpBinary) . ' ' . escapeshellarg($testFile);

    $fileStart = microtime(true);
    
    // Execute command with captured stdout & stderr
    $descriptors = [
        1 => ['pipe', 'w'], // stdout
        2 => ['pipe', 'w'], // stderr
    ];

    $process = proc_open($cmd, $descriptors, $pipes, dirname($testDir));
    $stdout = '';
    $stderr = '';
    $exitCode = -1;

    if (is_resource($process)) {
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);
    } else {
        $exitCode = 1;
        $stderr = "Failed to launch process for {$cmd}";
    }

    $elapsed = round(microtime(true) - $fileStart, 2);

    $isPass = ($exitCode === 0);
    $isSkipped = str_contains($stdout, 'SKIPPED:');

    if ($isPass) {
        $passedCount++;
        $statusStr = $isSkipped ? "[SKIP/PASS]" : "[PASS]";
        echo sprintf(" %-11s %-38s (%0.2fs)\n", $statusStr, $basename, $elapsed);
    } else {
        $failedCount++;
        echo sprintf(" [FAIL]      %-38s (%0.2fs, exit %d)\n", $basename, $elapsed, $exitCode);
        
        // Print snippet of failure output
        echo "   --- Output Snippet ---\n";
        $lines = array_filter(explode("\n", trim($stdout . "\n" . $stderr)));
        $tail = array_slice($lines, -10);
        foreach ($tail as $line) {
            echo "   | " . trim($line) . "\n";
        }
        echo "   ----------------------\n";
    }

    $results[$basename] = [
        'passed' => $isPass,
        'exitCode' => $exitCode,
        'elapsed' => $elapsed,
        'skipped' => $isSkipped,
    ];
}

$totalElapsed = round(microtime(true) - $startTime, 2);

echo "\n====================================================================\n";
echo sprintf(
    "RESULTS: %d / %d Test Files Passed (%d Failed) in %0.2fs\n",
    $passedCount,
    count($testFiles),
    $failedCount,
    $totalElapsed
);
echo "====================================================================\n";

if ($failedCount > 0) {
    echo ">>> SOME TEST SUITES FAILED! <<<\n";
    exit(1);
} else {
    echo ">>> ALL TEST SUITES PASSED SUCCESSFULLY! ✓ <<<\n";
    exit(0);
}
