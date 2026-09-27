<?php
/**
 * SPS Admin Activities Management View
 * Strictly restricted to Super-Admin and Admin roles.
 */

$isBn = ($locale ?? 'bn') === 'bn';
$currentLocale = current_locale();
$csrfToken = \App\Core\Session::getCsrfToken();
$flashSuccess = \App\Core\Session::getFlash('success');
$flashError = \App\Core\Session::getFlash('error');
?>

<div class="admin-main">

    <!-- Flash Notifications -->
    <?php if ($flashSuccess): ?>
        <div style="background:#dcfce7; border:1px solid #86efac; color:#15803d; padding:var(--space-md); border-radius:var(--radius-md); margin-bottom:var(--space-lg); display:flex; align-items:center; gap:8px;">
            <span>✅</span>
            <span><?= e($flashSuccess) ?></span>
        </div>
    <?php endif; ?>

    <?php if ($flashError): ?>
        <div style="background:#fee2e2; border:1px solid #fca5a5; color:#b91c1c; padding:var(--space-md); border-radius:var(--radius-md); margin-bottom:var(--space-lg); display:flex; align-items:center; gap:8px;">
            <span>⚠️</span>
            <span><?= e($flashError) ?></span>
        </div>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="admin-page-header">
        <div>
            <div style="display:inline-flex; align-items:center; gap:6px; background:#fee2e2; color:#991b1b; padding:3px 10px; border-radius:var(--radius-full); font-size:0.75rem; font-weight:700; margin-bottom:var(--space-2xs); border:1px solid #f87171;">
                <span>🔒</span>
                <span><?= $isBn ? 'সুপার-অ্যাডমিন ও অ্যাডমিন সংরক্ষিত' : 'Super-Admin & Admin Controlled' ?></span>
            </div>
            <h1 class="admin-page-title">
                <?= $isBn ? 'কার্যক্রম ও সেবা মহাযজ্ঞ পরিচালনা' : 'Activities & Seva Management' ?>
            </h1>
            <p class="admin-page-desc">
                <?= $isBn 
                    ? 'ওয়েবসাইটের কার্যক্রম পেজে প্রদর্শিত সকল ঐতিহাসিক ও চলমান সেবামূলক প্রকল্পের তথ্য হালনাগাদ, সংশোধন ও নতুন এন্ট্রি পরিচালনা করুন।' 
                    : 'Manage, update, modify, and delete all field activities, historic milestones, and humanitarian drives displayed on the public Activities page.' ?>
            </p>
        </div>

        <div style="display:flex; gap:var(--space-sm); align-items:center;">
            <button type="button" class="btn btn-primary" onclick="openAddModal()" style="display:inline-flex; align-items:center; gap:6px; font-weight:700;">
                <span>➕</span>
                <span><?= $isBn ? 'নতুন কার্যক্রম যুক্ত করুন' : 'Add New Activity' ?></span>
            </button>
            <a href="<?= url('/activities', $currentLocale) ?>" target="_blank" class="btn btn-outline" style="font-size:0.85rem; border-color:var(--border-medium); display:inline-flex; align-items:center; gap:6px;">
                <span>👁️</span>
                <span><?= $isBn ? 'পাবলিক পেজ দেখুন' : 'View Public Page' ?></span>
            </a>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:var(--space-md); margin-bottom:var(--space-xl);">
        <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); padding:var(--space-md); box-shadow:var(--shadow-xs);">
            <div style="font-size:0.8rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">
                <?= $isBn ? 'মোট কার্যক্রম' : 'Total Activities' ?>
            </div>
            <div style="font-size:1.85rem; font-weight:800; color:var(--primary-deep); margin-top:4px;">
                <?= count($activities) ?>
            </div>
            <div style="font-size:0.75rem; color:var(--text-secondary); margin-top:2px;">
                <?= $isBn ? '২০২০ থেকে বর্তমান পর্যন্ত' : 'From 2020 to present' ?>
            </div>
        </div>

        <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); padding:var(--space-md); box-shadow:var(--shadow-xs);">
            <div style="font-size:0.8rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">
                <?= $isBn ? 'চলমান প্রকল্প' : 'Active Projects' ?>
            </div>
            <div style="font-size:1.85rem; font-weight:800; color:#2563eb; margin-top:4px;">
                <?php 
                    $activeCount = count(array_filter($activities, fn($a) => ($a['status'] ?? '') === 'in_progress'));
                    echo $activeCount;
                ?>
            </div>
            <div style="font-size:0.75rem; color:var(--text-secondary); margin-top:2px;">
                <?= $isBn ? 'মাঠপর্যায়ে চলমান ড্রাইভ' : 'Active field drives' ?>
            </div>
        </div>

        <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); padding:var(--space-md); box-shadow:var(--shadow-xs);">
            <div style="font-size:0.8rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">
                <?= $isBn ? 'সফল সমাপ্ত' : 'Completed' ?>
            </div>
            <div style="font-size:1.85rem; font-weight:800; color:#15803d; margin-top:4px;">
                <?php 
                    $completedCount = count(array_filter($activities, fn($a) => ($a['status'] ?? '') === 'completed'));
                    echo $completedCount;
                ?>
            </div>
            <div style="font-size:0.75rem; color:var(--text-secondary); margin-top:2px;">
                <?= $isBn ? 'অডিট ও ভেরিফাইড' : 'Verified & archived' ?>
            </div>
        </div>

        <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); padding:var(--space-md); box-shadow:var(--shadow-xs);">
            <div style="font-size:0.8rem; font-weight:700; color:var(--text-muted); text-transform:uppercase;">
                <?= $isBn ? 'পরিবেশ ও বৃক্ষরোপণ' : 'Trees Planted' ?>
            </div>
            <div style="font-size:1.85rem; font-weight:800; color:#047857; margin-top:4px;">
                <?= $stats['trees_planted'] ?? '9,500+' ?>
            </div>
            <div style="font-size:0.75rem; color:var(--text-secondary); margin-top:2px;">
                <?= $isBn ? '৩০+ জেলায় বৃক্ষরোপণ' : 'Nationwide 30+ districts' ?>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); padding:var(--space-md) var(--space-lg); margin-bottom:var(--space-xl); display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:var(--space-md); box-shadow:var(--shadow-xs);">
        
        <div style="display:flex; align-items:center; gap:var(--space-sm); flex-wrap:wrap; flex-grow:1; max-width:650px;">
            <div style="position:relative; flex-grow:1; min-width:240px;">
                <input type="text" id="adminActivitySearch" placeholder="<?= $isBn ? 'শিরোনাম, জেলা বা বিবরণ দিয়ে খুঁজুন...' : 'Search by title, location or description...' ?>" style="width:100%; padding:8px 12px 8px 34px; font-size:0.88rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); outline:none;">
                <span style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:0.9rem;">🔍</span>
            </div>

            <select id="adminYearFilter" style="padding:8px 12px; font-size:0.88rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); background:#ffffff; outline:none; font-family:inherit;">
                <option value="all"><?= $isBn ? 'সকল সাল (All Years)' : 'All Years' ?></option>
                <?php 
                    $years = array_unique(array_map(fn($a) => (int)$a['year'], $activities));
                    rsort($years);
                    foreach ($years as $yr): 
                ?>
                    <option value="<?= $yr ?>"><?= $yr ?></option>
                <?php endforeach; ?>
            </select>

            <select id="adminCatFilter" style="padding:8px 12px; font-size:0.88rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); background:#ffffff; outline:none; font-family:inherit;">
                <option value="all"><?= $isBn ? 'সকল ক্যাটাগরি' : 'All Categories' ?></option>
                <?php foreach ($categories as $ckey => $cmeta): ?>
                    <option value="<?= $ckey ?>"><?= $cmeta['icon'] ?> <?= e($isBn ? $cmeta['bn'] : $cmeta['en']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="font-size:0.82rem; font-weight:600; color:var(--text-muted);">
            <?= sprintf($isBn ? 'প্রদর্শিত কার্যক্রম: %dটি' : 'Showing: %d activities', count($activities)) ?>
        </div>
    </div>

    <!-- Activities Table -->
    <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); overflow:hidden; box-shadow:var(--shadow-xs); margin-bottom:var(--space-3xl);">
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; text-align:left; font-size:0.88rem;" id="adminActivitiesTable">
                <thead>
                    <tr style="background:var(--bg-subtle); border-bottom:2px solid var(--border-medium); color:var(--text-muted); font-size:0.8rem; text-transform:uppercase; letter-spacing:0.5px;">
                        <th style="padding:12px 14px; width:70px;"><?= $isBn ? 'সাল' : 'Year' ?></th>
                        <th style="padding:12px 14px; width:130px;"><?= $isBn ? 'ক্যাটাগরি' : 'Category' ?></th>
                        <th style="padding:12px 14px;"><?= $isBn ? 'শিরোনাম ও বিবরণ' : 'Title & Summary' ?></th>
                        <th style="padding:12px 14px; width:150px;"><?= $isBn ? 'স্থান' : 'Location' ?></th>
                        <th style="padding:12px 14px; width:110px;"><?= $isBn ? 'অবস্থা' : 'Status' ?></th>
                        <th style="padding:12px 14px; width:140px; text-align:right;"><?= $isBn ? 'অ্যাকশন' : 'Actions' ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activities as $act): 
                        $statusStyle = match($act['status']) {
                            'completed' => 'background:#dcfce7; color:#15803d; border:1px solid #86efac;',
                            'in_progress' => 'background:#dbeafe; color:#1d4ed8; border:1px solid #93c5fd;',
                            default => 'background:#fef3c7; color:#b45309; border:1px solid #fde68a;',
                        };
                    ?>
                        <tr class="activity-row" 
                            data-id="<?= e($act['id']) ?>"
                            data-year="<?= e($act['year']) ?>"
                            data-category="<?= e($act['category']) ?>"
                            data-text="<?= strtolower(e(($act['title_bn'] ?? '') . ' ' . ($act['title_en'] ?? '') . ' ' . ($act['location_bn'] ?? '') . ' ' . ($act['location_en'] ?? ''))) ?>"
                            style="border-bottom:1px solid var(--border-subtle); transition:background 0.15s ease;">
                            
                            <td style="padding:14px; font-weight:700; color:var(--primary-deep); font-size:0.92rem;">
                                <?= e($act['year']) ?>
                                <div style="font-size:0.75rem; color:var(--text-muted); font-weight:normal;"><?= e($act['date']) ?></div>
                            </td>

                            <td style="padding:14px;">
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <span style="font-size:1.1rem;"><?= e($act['icon'] ?? '🚩') ?></span>
                                    <span style="font-size:0.75rem; font-weight:700; color:#b45309; background:#fffbeb; padding:2px 6px; border-radius:var(--radius-sm); border:1px solid #fde68a;">
                                        <?= e($isBn ? $act['category_bn'] : $act['category_en']) ?>
                                    </span>
                                </div>
                            </td>

                            <td style="padding:14px;">
                                <div style="font-weight:700; color:var(--primary-deep); font-size:0.95rem; margin-bottom:3px;">
                                    <?= e($isBn ? $act['title_bn'] : $act['title_en']) ?>
                                </div>
                                <div style="font-size:0.8rem; color:var(--text-muted); margin-bottom:6px;">
                                    <?= e($act['title_en']) ?>
                                </div>
                                <p style="font-size:0.84rem; color:var(--text-secondary); line-height:1.5; margin:0 0 6px; max-width:600px;">
                                    <?= e(mb_strimwidth($isBn ? $act['description_bn'] : $act['description_en'], 0, 150, '...')) ?>
                                </p>
                                <span style="font-size:0.72rem; font-weight:600; color:#475569; background:#f1f5f9; padding:2px 8px; border-radius:var(--radius-sm);">
                                    🏷️ <?= e($act['badge'] ?? 'General') ?>
                                </span>
                            </td>

                            <td style="padding:14px; font-size:0.85rem; color:var(--text-secondary);">
                                📍 <?= e($isBn ? $act['location_bn'] : $act['location_en']) ?>
                            </td>

                            <td style="padding:14px;">
                                <span style="display:inline-block; font-size:0.75rem; font-weight:700; padding:2px 8px; border-radius:var(--radius-full); <?= $statusStyle ?>">
                                    <?= e($isBn ? $act['status_bn'] : $act['status_en']) ?>
                                </span>
                            </td>

                            <td style="padding:14px; text-align:right;">
                                <div style="display:flex; justify-content:flex-end; gap:6px;">
                                    <!-- Edit Trigger Button -->
                                    <button type="button" class="btn btn-outline btn-sm" 
                                            onclick='openEditModal(<?= json_encode($act, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>)'
                                            style="padding:4px 8px; font-size:0.78rem; font-weight:600;">
                                        ✏️ <?= $isBn ? 'সম্পাদনা' : 'Edit' ?>
                                    </button>

                                    <!-- Delete Trigger Form -->
                                    <form action="<?= url('/admin/activities/delete/' . urlencode($act['id']), $currentLocale) ?>" method="POST" onsubmit="return confirm('<?= $isBn ? 'আপনি কি নিশ্চিত যে এই কার্যক্রমটি মুছে ফেলতে চান? এটি অডিট লগে রেকর্ড করা হবে।' : 'Are you sure you want to permanently delete this activity? This will be recorded in audit logs.' ?>');" style="display:inline;">
                                        <input type="hidden" name="_csrf" value="<?= e($csrfToken) ?>">
                                        <button type="submit" class="btn btn-ghost btn-sm" style="padding:4px 8px; font-size:0.78rem; color:#dc2626;" title="<?= $isBn ? 'ডিলিট করুন' : 'Delete' ?>">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: Add New Activity -->
<div id="addActivityModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1000; align-items:center; justify-content:center; padding:var(--space-md); overflow-y:auto;">
    <div style="background:#ffffff; border-radius:var(--radius-xl); width:100%; max-width:760px; max-height:92vh; overflow-y:auto; padding:var(--space-xl); box-shadow:var(--shadow-lg); position:relative;">
        
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-lg); border-bottom:1px solid var(--border-medium); padding-bottom:var(--space-sm);">
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:1.3rem;">➕</span>
                <h3 style="font-size:1.3rem; font-weight:800; color:var(--primary-deep); margin:0;">
                    <?= $isBn ? 'নতুন কার্যক্রম যুক্ত করুন' : 'Add New Activity' ?>
                </h3>
            </div>
            <button type="button" onclick="closeAddModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-muted);">&times;</button>
        </div>

        <form action="<?= url('/admin/activities/create', $currentLocale) ?>" method="POST">
            <input type="hidden" name="_csrf" value="<?= e($csrfToken) ?>">

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-md); margin-bottom:var(--space-md);">
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                        <?= $isBn ? 'শিরোনাম (বাংলা) *' : 'Title (Bengali) *' ?>
                    </label>
                    <input type="text" name="title_bn" required style="width:100%; padding:8px 10px; font-size:0.9rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); outline:none;">
                </div>
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                        <?= $isBn ? 'শিরোনাম (English) *' : 'Title (English) *' ?>
                    </label>
                    <input type="text" name="title_en" required style="width:100%; padding:8px 10px; font-size:0.9rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); outline:none;">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(160px, 1fr)); gap:var(--space-md); margin-bottom:var(--space-md);">
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                        <?= $isBn ? 'সাল (Year) *' : 'Year *' ?>
                    </label>
                    <input type="number" name="year" value="<?= date('Y') ?>" min="2018" max="2035" required style="width:100%; padding:8px 10px; font-size:0.9rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); outline:none;">
                </div>
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                        <?= $isBn ? 'তারিখ বা সময়কাল' : 'Date / Date String' ?>
                    </label>
                    <input type="text" name="date" placeholder="e.g. 28 March 2024" style="width:100%; padding:8px 10px; font-size:0.9rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); outline:none;">
                </div>
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                        <?= $isBn ? 'ক্যাটাগরি *' : 'Category *' ?>
                    </label>
                    <select name="category" required style="width:100%; padding:8px 10px; font-size:0.88rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); background:#ffffff; outline:none;">
                        <?php foreach ($categories as $ckey => $cmeta): ?>
                            <option value="<?= $ckey ?>"><?= $cmeta['icon'] ?> <?= e($isBn ? $cmeta['bn'] : $cmeta['en']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                        <?= $isBn ? 'অবস্থা (Status) *' : 'Status *' ?>
                    </label>
                    <select name="status" required style="width:100%; padding:8px 10px; font-size:0.88rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); background:#ffffff; outline:none;">
                        <option value="completed"><?= $isBn ? 'সফলভাবে সমাপ্ত (Completed)' : 'Completed' ?></option>
                        <option value="in_progress"><?= $isBn ? 'চলমান কার্যক্রম (Active)' : 'Active Drive' ?></option>
                        <option value="needs_review"><?= $isBn ? 'পর্যালোচনাধীন (Needs Review)' : 'Needs Review' ?></option>
                    </select>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-md); margin-bottom:var(--space-md);">
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                        <?= $isBn ? 'স্থান (বাংলা)' : 'Location (Bengali)' ?>
                    </label>
                    <input type="text" name="location_bn" placeholder="e.g. কান্তজিউ মন্দির, দিনাজপুর" style="width:100%; padding:8px 10px; font-size:0.9rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); outline:none;">
                </div>
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                        <?= $isBn ? 'স্থান (English)' : 'Location (English)' ?>
                    </label>
                    <input type="text" name="location_en" placeholder="e.g. Kantajew Temple, Dinajpur" style="width:100%; padding:8px 10px; font-size:0.9rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); outline:none;">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-md); margin-bottom:var(--space-md);">
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                        <?= $isBn ? 'ব্যাজ (Badge Tag)' : 'Badge Tag' ?>
                    </label>
                    <input type="text" name="badge" placeholder="e.g. Mega Seva, 9,500+ Trees" style="width:100%; padding:8px 10px; font-size:0.9rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); outline:none;">
                </div>
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                        <?= $isBn ? 'আইকন ইমোজি (Icon)' : 'Icon Emoji' ?>
                    </label>
                    <input type="text" name="icon" placeholder="e.g. 🛕, 📖, 🤝, 🩺, 🌱" style="width:100%; padding:8px 10px; font-size:0.9rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); outline:none;">
                </div>
            </div>

            <div style="margin-bottom:var(--space-md);">
                <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                    <?= $isBn ? 'কার্যক্রম বিবরণ (বাংলা) *' : 'Description (Bengali) *' ?>
                </label>
                <textarea name="description_bn" rows="3" required style="width:100%; padding:8px 10px; font-size:0.88rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); outline:none; font-family:inherit;"></textarea>
            </div>

            <div style="margin-bottom:var(--space-md);">
                <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                    <?= $isBn ? 'কার্যক্রম বিবরণ (English) *' : 'Description (English) *' ?>
                </label>
                <textarea name="description_en" rows="3" required style="width:100%; padding:8px 10px; font-size:0.88rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); outline:none; font-family:inherit;"></textarea>
            </div>

            <div style="margin-bottom:var(--space-lg);">
                <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                    <?= $isBn ? 'মূল সাফল্য ও ফলাফল (প্রতি লাইনে একটি পয়েন্ট)' : 'Key Outcomes / Highlights (One per line)' ?>
                </label>
                <textarea name="highlights_bn" rows="3" placeholder="২০,০০০+ ভক্তদের মাঝে খাবার স্যালাইন বিতরণ&#10;১০০+ স্বেচ্ছাসেবক দল সক্রিয়ভাবে নিয়োজিত" style="width:100%; padding:8px 10px; font-size:0.88rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); outline:none; font-family:inherit;"></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:var(--space-sm); border-top:1px solid var(--border-subtle); padding-top:var(--space-md);">
                <button type="button" onclick="closeAddModal()" class="btn btn-secondary">
                    <?= $isBn ? 'বাতিল' : 'Cancel' ?>
                </button>
                <button type="submit" class="btn btn-primary" style="font-weight:700;">
                    <?= $isBn ? 'সংরক্ষণ ও প্রকাশ করুন' : 'Save & Publish' ?>
                </button>
            </div>
        </form>

    </div>
</div>

<!-- Modal: Edit Existing Activity -->
<div id="editActivityModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1000; align-items:center; justify-content:center; padding:var(--space-md); overflow-y:auto;">
    <div style="background:#ffffff; border-radius:var(--radius-xl); width:100%; max-width:760px; max-height:92vh; overflow-y:auto; padding:var(--space-xl); box-shadow:var(--shadow-lg); position:relative;">
        
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-lg); border-bottom:1px solid var(--border-medium); padding-bottom:var(--space-sm);">
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:1.3rem;">✏️</span>
                <h3 style="font-size:1.3rem; font-weight:800; color:var(--primary-deep); margin:0;">
                    <?= $isBn ? 'কার্যক্রম সম্পাদনা ও সংশোধন' : 'Edit Activity Information' ?>
                </h3>
            </div>
            <button type="button" onclick="closeEditModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-muted);">&times;</button>
        </div>

        <form id="editActivityForm" action="" method="POST">
            <input type="hidden" name="_csrf" value="<?= e($csrfToken) ?>">
            <input type="hidden" name="id" id="edit_id" value="">

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-md); margin-bottom:var(--space-md);">
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                        <?= $isBn ? 'শিরোনাম (বাংলা) *' : 'Title (Bengali) *' ?>
                    </label>
                    <input type="text" name="title_bn" id="edit_title_bn" required style="width:100%; padding:8px 10px; font-size:0.9rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); outline:none;">
                </div>
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                        <?= $isBn ? 'শিরোনাম (English) *' : 'Title (English) *' ?>
                    </label>
                    <input type="text" name="title_en" id="edit_title_en" required style="width:100%; padding:8px 10px; font-size:0.9rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); outline:none;">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(160px, 1fr)); gap:var(--space-md); margin-bottom:var(--space-md);">
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                        <?= $isBn ? 'সাল (Year) *' : 'Year *' ?>
                    </label>
                    <input type="number" name="year" id="edit_year" min="2018" max="2035" required style="width:100%; padding:8px 10px; font-size:0.9rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); outline:none;">
                </div>
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                        <?= $isBn ? 'তারিখ বা সময়কাল' : 'Date / Date String' ?>
                    </label>
                    <input type="text" name="date" id="edit_date" style="width:100%; padding:8px 10px; font-size:0.9rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); outline:none;">
                </div>
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                        <?= $isBn ? 'ক্যাটাগরি *' : 'Category *' ?>
                    </label>
                    <select name="category" id="edit_category" required style="width:100%; padding:8px 10px; font-size:0.88rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); background:#ffffff; outline:none;">
                        <?php foreach ($categories as $ckey => $cmeta): ?>
                            <option value="<?= $ckey ?>"><?= $cmeta['icon'] ?> <?= e($isBn ? $cmeta['bn'] : $cmeta['en']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                        <?= $isBn ? 'অবস্থা (Status) *' : 'Status *' ?>
                    </label>
                    <select name="status" id="edit_status" required style="width:100%; padding:8px 10px; font-size:0.88rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); background:#ffffff; outline:none;">
                        <option value="completed"><?= $isBn ? 'সফলভাবে সমাপ্ত (Completed)' : 'Completed' ?></option>
                        <option value="in_progress"><?= $isBn ? 'চলমান কার্যক্রম (Active)' : 'Active Drive' ?></option>
                        <option value="needs_review"><?= $isBn ? 'পর্যালোচনাধীন (Needs Review)' : 'Needs Review' ?></option>
                    </select>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-md); margin-bottom:var(--space-md);">
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                        <?= $isBn ? 'স্থান (বাংলা)' : 'Location (Bengali)' ?>
                    </label>
                    <input type="text" name="location_bn" id="edit_location_bn" style="width:100%; padding:8px 10px; font-size:0.9rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); outline:none;">
                </div>
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                        <?= $isBn ? 'স্থান (English)' : 'Location (English)' ?>
                    </label>
                    <input type="text" name="location_en" id="edit_location_en" style="width:100%; padding:8px 10px; font-size:0.9rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); outline:none;">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-md); margin-bottom:var(--space-md);">
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                        <?= $isBn ? 'ব্যাজ (Badge Tag)' : 'Badge Tag' ?>
                    </label>
                    <input type="text" name="badge" id="edit_badge" style="width:100%; padding:8px 10px; font-size:0.9rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); outline:none;">
                </div>
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                        <?= $isBn ? 'আইকন ইমোজি (Icon)' : 'Icon Emoji' ?>
                    </label>
                    <input type="text" name="icon" id="edit_icon" style="width:100%; padding:8px 10px; font-size:0.9rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); outline:none;">
                </div>
            </div>

            <div style="margin-bottom:var(--space-md);">
                <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                    <?= $isBn ? 'কার্যক্রম বিবরণ (বাংলা) *' : 'Description (Bengali) *' ?>
                </label>
                <textarea name="description_bn" id="edit_description_bn" rows="3" required style="width:100%; padding:8px 10px; font-size:0.88rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); outline:none; font-family:inherit;"></textarea>
            </div>

            <div style="margin-bottom:var(--space-md);">
                <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                    <?= $isBn ? 'কার্যক্রম বিবরণ (English) *' : 'Description (English) *' ?>
                </label>
                <textarea name="description_en" id="edit_description_en" rows="3" required style="width:100%; padding:8px 10px; font-size:0.88rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); outline:none; font-family:inherit;"></textarea>
            </div>

            <div style="margin-bottom:var(--space-lg);">
                <label style="display:block; font-size:0.82rem; font-weight:700; color:var(--primary-deep); margin-bottom:4px;">
                    <?= $isBn ? 'মূল সাফল্য ও ফলাফল (প্রতি লাইনে একটি পয়েন্ট)' : 'Key Outcomes / Highlights (One per line)' ?>
                </label>
                <textarea name="highlights_bn" id="edit_highlights_bn" rows="3" style="width:100%; padding:8px 10px; font-size:0.88rem; border:1px solid var(--border-medium); border-radius:var(--radius-md); outline:none; font-family:inherit;"></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:var(--space-sm); border-top:1px solid var(--border-subtle); padding-top:var(--space-md);">
                <button type="button" onclick="closeEditModal()" class="btn btn-secondary">
                    <?= $isBn ? 'বাতিল' : 'Cancel' ?>
                </button>
                <button type="submit" class="btn btn-primary" style="font-weight:700;">
                    <?= $isBn ? 'আপডেট সংরক্ষণ করুন' : 'Save Changes' ?>
                </button>
            </div>
        </form>

    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('addActivityModal').style.display = 'flex';
}

function closeAddModal() {
    document.getElementById('addActivityModal').style.display = 'none';
}

function openEditModal(act) {
    const form = document.getElementById('editActivityForm');
    form.action = '<?= url("/admin/activities/update/", $currentLocale) ?>' + encodeURIComponent(act.id);
    
    document.getElementById('edit_id').value = act.id || '';
    document.getElementById('edit_title_bn').value = act.title_bn || '';
    document.getElementById('edit_title_en').value = act.title_en || '';
    document.getElementById('edit_year').value = act.year || '<?= date("Y") ?>';
    document.getElementById('edit_date').value = act.date || '';
    document.getElementById('edit_category').value = act.category || 'humanitarian';
    document.getElementById('edit_status').value = act.status || 'completed';
    document.getElementById('edit_location_bn').value = act.location_bn || '';
    document.getElementById('edit_location_en').value = act.location_en || '';
    document.getElementById('edit_badge').value = act.badge || '';
    document.getElementById('edit_icon').value = act.icon || '🚩';
    document.getElementById('edit_description_bn').value = act.description_bn || '';
    document.getElementById('edit_description_en').value = act.description_en || '';

    let hl = '';
    if (Array.isArray(act.highlights_bn)) {
        hl = act.highlights_bn.join('\n');
    }
    document.getElementById('edit_highlights_bn').value = hl;

    document.getElementById('editActivityModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editActivityModal').style.display = 'none';
}

// Client-side search and filtering
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('adminActivitySearch');
    const yearFilter = document.getElementById('adminYearFilter');
    const catFilter = document.getElementById('adminCatFilter');
    const rows = document.querySelectorAll('.activity-row');

    function filterTable() {
        const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
        const year = yearFilter ? yearFilter.value : 'all';
        const cat = catFilter ? catFilter.value : 'all';

        rows.forEach(row => {
            const rYear = row.getAttribute('data-year');
            const rCat = row.getAttribute('data-category');
            const rText = row.getAttribute('data-text') || '';

            const matchQuery = !query || rText.includes(query);
            const matchYear = (year === 'all' || year === rYear);
            const matchCat = (cat === 'all' || cat === rCat);

            if (matchQuery && matchYear && matchCat) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (searchInput) searchInput.addEventListener('input', filterTable);
    if (yearFilter) yearFilter.addEventListener('change', filterTable);
    if (catFilter) catFilter.addEventListener('change', filterTable);
});
</script>
