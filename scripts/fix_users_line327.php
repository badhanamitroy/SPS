<?php

declare(strict_types=1);

$file = dirname(__DIR__) . '/app/Views/pages/admin/users.php';
$content = file_get_contents($file);

$needle = 'onsubmit="return confirm(\'<?= $isBn ? \"আপনি কি নিশ্চিত যে এই কর্মকর্তার পাসওয়ার্ড রিসেট করে নতুন OTP তৈরি করতে চান?\" : \"Reset this officer password to a new OTP?\" ?>\');"';
$replacement = 'onsubmit="return confirm(\'Reset this officer password to a new OTP?\');"';

$content = str_replace($needle, $replacement, $content);
file_put_contents($file, $content);
echo "Cleaned line 327 in users.php\n";
