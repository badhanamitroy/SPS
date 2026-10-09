<?php

declare(strict_types=1);

$file = dirname(__DIR__) . '/app/Views/pages/admin/users.php';
$content = file_get_contents($file);

// 1. Table header button
$targetBar = '<span style="font-size:0.82rem; font-weight:700; color:var(--text-muted); background:var(--bg-surface); padding:4px 12px; border-radius:var(--radius-full);">
            <?= $isBn ? \'মোট কর্মকর্তা:\' : \'Total Officers:\' ?> <?= count($users) ?>
        </span>';

$replBar = '<div style="display:flex; align-items:center; gap:10px;">
            <span style="font-size:0.82rem; font-weight:700; color:var(--text-muted); background:var(--bg-surface); padding:4px 12px; border-radius:var(--radius-full);">
                <?= $isBn ? \'মোট কর্মকর্তা:\' : \'Total Officers:\' ?> <?= count($users) ?>
            </span>
            <?php if ($canAssignRoles): ?>
                <button type="button" onclick="openCreateOfficerModal()" class="btn btn-sm btn-primary" style="font-weight:800; display:inline-flex; align-items:center; gap:6px; background:linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);">
                    <span>➕</span>
                    <span><?= $isBn ? \'নতুন কর্মকর্তা যুক্ত করুন (OTP সহ)\' : \'Add Officer (with OTP)\' ?></span>
                </button>
            <?php endif; ?>
        </div>';

$content = str_replace($targetBar, $replBar, $content);

// 2. Table TH
$targetTh = '<th style="padding:12px 14px; text-align:center;"><?= $isBn ? \'স্ট্যাটাস\' : \'Status\' ?></th>
                    <th style="padding:12px 16px; text-align:right;"><?= $isBn ? \'সুপার অ্যাডমিন অ্যাকশন\' : \'Super Admin Action\' ?></th>';

$replTh = '<th style="padding:12px 14px; text-align:center;"><?= $isBn ? \'পাসওয়ার্ড নিরাপত্তা\' : \'Auth & OTP\' ?></th>
                    <th style="padding:12px 14px; text-align:center;"><?= $isBn ? \'স্ট্যাটাস\' : \'Status\' ?></th>
                    <th style="padding:12px 16px; text-align:right;"><?= $isBn ? \'সুপার অ্যাডমিন অ্যাকশন\' : \'Super Admin Action\' ?></th>';

$content = str_replace($targetTh, $replTh, $content);

// 3. Table TD for row
$targetTd = '                        <!-- Status -->
                        <td style="padding:12px 14px; text-align:center;">
                            <span style="display:inline-flex; align-items:center; gap:4px; font-size:0.75rem; color:#15803d; background:#dcfce7; font-weight:800; padding:2px 8px; border-radius:var(--radius-full); border:1px solid #86efac;">
                                ● <?= $isBn ? \'সক্রিয়\' : \'Active\' ?>
                            </span>
                        </td>

                        <!-- Super Admin Actions -->
                        <td style="padding:12px 16px; text-align:right;">
                            <?php if ($canAssignRoles): ?>
                                <button type="button" 
                                        onclick="openAssignmentModal(<?= $userJson ?>)"
                                        class="btn btn-sm btn-primary" 
                                        style="font-size:0.78rem; font-weight:800; padding:5px 12px; display:inline-flex; align-items:center; gap:6px;"
                                        title="<?= $isBn ? \'সুপার অ্যাডমিন হিসেবে রোল, কাজের পরিধি ও বিশেষ অনুমতি নির্ধারণ করুন\' : \'Assign role, tasks scope & allowances\' ?>">
                                    <span>⚙️</span>
                                    <span><?= $isBn ? \'দায়িত্ব ও ক্ষমতা নির্ধারণ\' : \'Assign Role & Tasks\' ?></span>
                                </button>
                            <?php else: ?>
                                <span style="font-size:0.75rem; color:#94a3b8; font-style:italic;">
                                    🔒 <?= $isBn ? \'সুপার অ্যাডমিন এক্তিয়ার\' : \'Super Admin only\' ?>
                                </span>
                            <?php endif; ?>
                        </td>';

$replTd = '                        <!-- Auth & OTP Status -->
                        <td style="padding:12px 14px; text-align:center;">
                            <?php if (!empty($u[\'must_change_password\'])): ?>
                                <span style="display:inline-flex; align-items:center; gap:4px; font-size:0.72rem; color:#b45309; background:#fffbeb; font-weight:800; padding:3px 8px; border-radius:var(--radius-full); border:1px solid #fde68a;" title="<?= $isBn ? \'কর্মকর্তা ওয়ান-টাইম পাসওয়ার্ড ব্যবহার করছেন, প্রথমবার লগইনে পরিবর্তন আবশ্যক\' : \'OTP Active, must change on first login\' ?>">
                                    ⏳ <?= $isBn ? \'OTP সক্রিয় (প্রথম লগইন)\' : \'OTP (Initial)\' ?>
                                </span>
                                <?php if (!empty($u[\'initial_otp\'])): ?>
                                    <div style="font-size:0.68rem; font-family:monospace; color:#92400e; margin-top:2px;">
                                        <?= e($u[\'initial_otp\']) ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span style="display:inline-flex; align-items:center; gap:4px; font-size:0.72rem; color:#15803d; background:#f0fdf4; font-weight:800; padding:3px 8px; border-radius:var(--radius-full); border:1px solid #86efac;">
                                    ✓ <?= $isBn ? \'অনন্য পাসওয়ার্ড\' : \'Unique Password\' ?>
                                </span>
                            <?php endif; ?>
                        </td>

                        <!-- Status -->
                        <td style="padding:12px 14px; text-align:center;">
                            <span style="display:inline-flex; align-items:center; gap:4px; font-size:0.75rem; color:#15803d; background:#dcfce7; font-weight:800; padding:2px 8px; border-radius:var(--radius-full); border:1px solid #86efac;">
                                ● <?= $isBn ? \'সক্রিয়\' : \'Active\' ?>
                            </span>
                        </td>

                        <!-- Super Admin Actions -->
                        <td style="padding:12px 16px; text-align:right;">
                            <?php if ($canAssignRoles): ?>
                                <div style="display:inline-flex; gap:6px; align-items:center;">
                                    <button type="button" 
                                            onclick="openAssignmentModal(<?= $userJson ?>)"
                                            class="btn btn-sm btn-primary" 
                                            style="font-size:0.78rem; font-weight:800; padding:5px 10px; display:inline-flex; align-items:center; gap:5px;"
                                            title="<?= $isBn ? \'দায়িত্ব ও কাজের পরিধি নির্ধারণ\' : \'Assign role, tasks scope & allowances\' ?>">
                                        <span>⚙️</span>
                                        <span><?= $isBn ? \'দায়িত্ব\' : \'Role\' ?></span>
                                    </button>
                                    <form action="<?= url(\'/admin/users/reset-otp\', $currentLocale) ?>" method="POST" style="margin:0; display:inline;" onsubmit="return confirm(\'<?= $isBn ? \"আপনি কি নিশ্চিত যে এই কর্মকর্তার পাসওয়ার্ড রিসেট করে নতুন OTP তৈরি করতে চান?\" : \"Reset this officer password to a new OTP?\" ?>\');">
                                        <?= \App\Core\Session::getCsrfToken() ? \'<input type="hidden" name="_csrf" value="\'.\App\Core\Session::getCsrfToken().\'">\' : \'\' ?>
                                        <input type="hidden" name="target_user_id" value="<?= e($u[\'id\']) ?>">
                                        <button type="submit" class="btn btn-sm btn-secondary" style="font-size:0.74rem; font-weight:700; padding:5px 8px; color:#b45309; border-color:#fde68a; background:#fffbeb;" title="<?= $isBn ? \'নতুন ওয়ান-টাইম পাসওয়ার্ড (OTP) তৈরি করুন\' : \'Reset with OTP\' ?>">
                                            🔑 OTP
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span style="font-size:0.75rem; color:#94a3b8; font-style:italic;">
                                    🔒 <?= $isBn ? \'সুপার অ্যাডমিন এক্তিয়ার\' : \'Super Admin only\' ?>
                                </span>
                            <?php endif; ?>
                        </td>';

$content = str_replace($targetTd, $replTd, $content);
file_put_contents($file, $content);
echo "users.php updated successfully!\n";
