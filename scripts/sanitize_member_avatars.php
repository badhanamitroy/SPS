<?php
$file = __DIR__ . '/../storage/data/membership.json';
$data = json_decode(file_get_contents($file), true);

$updated = 0;
$keptValid = 0;

foreach ($data['members'] as &$m) {
    $av = $m['avatar'] ?? '';
    // If valid uploaded local file that exists, keep it
    if (!empty($av) && !str_contains($av, 'dicebear') && file_exists(__DIR__ . '/../public/' . ltrim($av, '/'))) {
        $keptValid++;
        continue;
    }
    
    // Otherwise sanitize to default organization DP
    if (str_contains($av, 'dicebear') || empty($av) || $av === 'assets/images/members/default-avatar.png') {
        $m['avatar'] = 'media/dp/Default-DP.png';
        $updated++;
    }
}
unset($m);

echo "Dry run: Kept {$keptValid} valid uploaded images, updated {$updated} images to media/dp/Default-DP.png.\n";

// Write back safely with backup
$backup = __DIR__ . '/../storage/data/membership.json.bak_' . time();
copy($file, $backup);
file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
echo "Saved successfully! Backup created at {$backup}\n";
