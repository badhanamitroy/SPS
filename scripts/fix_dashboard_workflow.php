<?php

declare(strict_types=1);

$file = dirname(__DIR__) . '/app/Views/pages/membership/dashboard.php';
$content = file_get_contents($file);

// Fix line 67-73 broken HTML
$brokenNeedle = "<p style=\"color: var(--text-muted); margin-bottom: var(--space-lg);\"><?= \$isBn ? 'অনুগ্রহ করে নতুন সদস্যপদ আবেদন সম্পন্ন         <?php else: ?>";
$cleanNoMember = '<p style="color: var(--text-muted); margin-bottom: var(--space-lg);"><?= $isBn ? \'অনুগ্রহ করে নতুন সদস্যপদ আবেদন সম্পন্ন করুন।\' : \'Please complete a membership application.\' ?></p>' . "\n"
    . '                <a href="<?= url(\'/membership/apply\', $currentLocale) ?>" class="btn btn-primary"><?= $isBn ? \'আবেদন করুন\' : \'Apply Now\' ?></a>' . "\n"
    . '            </div>' . "\n"
    . '        <?php else: ?>';

// In case the replacement string differs slightly, let's search by position
$posNoMember = strpos($content, '<?php if (!$member): ?>');
$posPending = strpos($content, '<?php if ($isPending): ?>');

if ($posNoMember !== false && $posPending !== false) {
    $cleanSection = "<?php if (!\$member): ?>\n"
        . "            <div style=\"text-align: center; padding: var(--space-3xl); background: #ffffff; border-radius: var(--radius-lg); border: 1px solid var(--border-medium);\">\n"
        . "                <h2><?= \$isBn ? 'সদস্য রেকর্ড পাওয়া যায়নি' : 'No Member Profile Found' ?></h2>\n"
        . "                <p style=\"color: var(--text-muted); margin-bottom: var(--space-lg);\"><?= \$isBn ? 'অনুগ্রহ করে নতুন সদস্যপদ আবেদন সম্পন্ন করুন।' : 'Please complete a membership application.' ?></p>\n"
        . "                <a href=\"<?= url('/membership/apply', \$currentLocale) ?>\" class=\"btn btn-primary\"><?= \$isBn ? 'আবেদন করুন' : 'Apply Now' ?></a>\n"
        . "            </div>\n"
        . "        <?php else: ?>\n\n";

    $content = substr($content, 0, $posNoMember) . $cleanSection . substr($content, $posPending);
    file_put_contents($file, $content);
    echo "Fixed broken no-member block.\n";
} else {
    echo "Could not find no-member or pending tags.\n";
}
