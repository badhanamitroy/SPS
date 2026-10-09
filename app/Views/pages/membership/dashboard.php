<?php
/**
 * SPS Member Portal — feed-centred dashboard
 * Left nav • Feed / sections • Right summary panel • Mobile bottom nav
 * Keeps all existing routes, form fields and services.
 */

$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$t = fn(string $bn, string $en): string => $isBn ? $bn : $en;

$member     = $member ?? null;
$category   = $category ?? null;
$plan       = $plan ?? null;
$payments   = $payments ?? [];
$history    = $history ?? [];
$allMembers = $allMembers ?? [];

// Optional data providers (wire to DB later; safe defaults keep the page working)
$feedPosts     = $feedPosts ?? [];
$notices       = $notices ?? [];
$events        = $events ?? [];
$myArticles    = $myArticles ?? [];
$notifications = $member['notifications'] ?? [];
$nid = fn(array $n): string => (string)($n['id'] ?? md5(($n['title'] ?? '') . ($n['message'] ?? '')));
$unread = count(array_filter($notifications, fn($n) => empty($n['read'])));

$isPending = $member ? \App\Services\MembershipService::isPendingStatus($member['status'] ?? '') : false;
$resolvedAvatar = $member ? \App\Services\MembershipService::getMemberAvatar($member) : \App\Services\MembershipService::DEFAULT_ORGANIZATION_DP;
$defaultDp = asset(\App\Services\MembershipService::DEFAULT_ORGANIZATION_DP);
$csrf = csrf_field();

$status = $member['status'] ?? 'Active';
$statusMap = [
    'Lifetime Active' => ['#fef3c7', '#b45309', $t('👑 আজীবন সক্রিয়', '👑 Lifetime Active')],
    'Payment Due'     => ['#fee2e2', '#b91c1c', $t('পেমেন্ট বকেয়া', 'Payment Due')],
    'Suspended'       => ['#fef2f2', '#991b1b', $t('স্থগিত', 'Suspended')],
];
[$stBg, $stColor, $stLabel] = $statusMap[$status] ?? ['#dcfce7', '#15803d', $t('সক্রিয় সদস্য', 'Active Member')];
$memberName = $member ? ($isBn ? ($member['name_bn'] ?? '') : ($member['name_en'] ?? '')) : '';
$expiry = empty($member['expiry_date'] ?? '') ? $t('আজীবন / কোনো মেয়াদ নেই', 'Lifetime / No Expiry') : $member['expiry_date'];
?>
<style>
.spd{--ink:var(--primary-deep,#12324a);--line:var(--border-medium,#d8dee6);--sun:var(--accent-saffron,#C65A1E);--paper:#fff;background:var(--bg-surface,#f6f4ef);padding:24px 0 80px}
.spd *{box-sizing:border-box}
.spd-shell{display:grid;grid-template-columns:232px minmax(0,1fr) 296px;gap:22px;align-items:start;max-width:1280px;margin:0 auto;padding:0 16px}
.spd-side,.spd-right{position:sticky;top:16px;display:flex;flex-direction:column;gap:14px}
.spd-box{background:var(--paper);border:1px solid var(--line);border-radius:12px;padding:18px}
.spd-box h3{margin:0 0 10px;font-size:1.05rem;font-weight:800;color:var(--ink)}
.spd-box h4{margin:0 0 8px;font-size:.92rem;font-weight:800;color:var(--ink)}
.spd-muted{color:var(--text-muted,#64748b);font-size:.82rem}
.spd-id{display:flex;gap:10px;align-items:center;margin-bottom:12px}
.spd-av{width:48px;height:48px;border-radius:50%;object-fit:cover;border:2px solid var(--line);flex-shrink:0}
.spd-nav{display:flex;flex-direction:column;gap:2px}
.spd-nav-g{font-size:.74rem;font-weight:700;color:var(--text-muted,#64748b);margin:12px 8px 4px}
.spd-nav button{display:flex;align-items:center;gap:10px;width:100%;padding:9px 10px;border:0;background:none;border-radius:8px;font:600 .9rem inherit;color:var(--ink);cursor:pointer;text-align:left}
.spd-nav button:hover{background:#f1f5f9}
.spd-nav button.on{background:#fff3e8;color:#9a3c0c;box-shadow:inset 3px 0 0 var(--sun)}
.spd-badge{margin-left:auto;background:var(--sun);color:#fff;border-radius:999px;font-size:.7rem;padding:1px 7px}
.spd-sec{display:none;flex-direction:column;gap:16px}.spd-sec.on{display:flex}
.spd-compose{display:flex;gap:10px;align-items:center}
.spd-compose a{flex:1;padding:10px 16px;border:1px solid var(--line);border-radius:999px;background:#f8fafc;color:var(--text-muted,#64748b);text-decoration:none;font-size:.9rem}
.spd-post-h{display:flex;gap:10px;align-items:center;margin-bottom:10px}
.spd-tag{font-size:.72rem;font-weight:800;padding:2px 9px;border-radius:999px;background:#e0f2fe;color:#0369a1}
.spd-tag.official{background:#fff3e8;color:#9a3c0c}
.spd-post-f{display:flex;gap:6px;border-top:1px solid var(--line);margin-top:12px;padding-top:8px}
.spd-post-f button{flex:1;border:0;background:none;padding:6px;border-radius:6px;font-weight:600;font-size:.84rem;color:var(--text-muted,#64748b);cursor:pointer}
.spd-post-f button:hover{background:#f1f5f9}
.spd-empty{text-align:center;padding:26px 12px;color:var(--text-muted,#64748b);font-size:.9rem}
.spd-notice{border-left:4px solid var(--sun);background:#fffaf5}
.spd-grid2{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px}
.spd label{display:block;font-size:.8rem;font-weight:700;color:var(--ink);margin-bottom:3px}
.spd input:not([type=checkbox]):not([type=file]),.spd select,.spd textarea{width:100%;padding:8px 11px;border:1px solid var(--line);border-radius:8px;font-size:.9rem;font-family:inherit;background:#fff}
.spd input:focus,.spd select:focus,.spd textarea:focus,.spd-nav button:focus-visible{outline:2px solid var(--sun);outline-offset:1px}
.spd-btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:9px 18px;border-radius:8px;border:1px solid var(--line);background:#f8fafc;font-weight:700;font-size:.88rem;color:var(--ink);text-decoration:none;cursor:pointer}
.spd-btn.pri{background:var(--sun);border-color:#a64713;color:#fff}
.spd-table{width:100%;border-collapse:collapse;font-size:.86rem}
.spd-table th{text-align:left;padding:9px 10px;background:var(--bg-surface,#f6f4ef);font-size:.76rem;color:var(--text-muted,#64748b)}
.spd-table td{padding:10px;border-bottom:1px solid #eef1f5;vertical-align:middle}
.spd-pill{display:inline-block;padding:2px 10px;border-radius:999px;font-size:.74rem;font-weight:800}
.spd-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;text-align:center}
.spd-stats b{display:block;font-size:1.3rem;color:var(--ink)}
.spd-card{border:2px solid #bfa054;border-radius:16px;padding:18px;color:#fff;position:relative;overflow:hidden;background:linear-gradient(175deg,#091723,#19354a 50%,#07131d)}
.spd-bottom{display:none}
.spd-notif{display:flex;gap:10px;align-items:flex-start;width:100%;text-align:left;padding:12px 10px;border:0;border-bottom:1px solid #eef1f5;background:none;font:inherit;cursor:pointer;border-radius:8px}
.spd-notif:hover{background:#f8fafc}
.spd-notif .spd-dot{width:9px;height:9px;border-radius:50%;background:transparent;margin-top:6px;flex-shrink:0}
.spd-notif.unread{background:#fff8f1}.spd-notif.unread .spd-dot{background:var(--sun)}
.spd-notif:not(.unread) strong{font-weight:600;color:var(--text-muted,#64748b)}
@media(max-width:1100px){.spd-shell{grid-template-columns:200px minmax(0,1fr)}.spd-right{display:none}}
@media(max-width:760px){
 .spd-shell{grid-template-columns:1fr}.spd-side{display:none}
 .spd-bottom{display:flex;position:fixed;left:0;right:0;bottom:0;z-index:50;background:#fff;border-top:1px solid var(--line);padding:4px 0 env(safe-area-inset-bottom,0)}
 .spd-bottom button{flex:1;border:0;background:none;padding:8px 2px;font-size:.68rem;font-weight:700;color:var(--text-muted,#64748b);display:flex;flex-direction:column;align-items:center;gap:2px}
 .spd-bottom button.on{color:var(--sun)}.spd-bottom span{font-size:1.25rem}
 .spd-stats{grid-template-columns:repeat(2,1fr)}
}
</style>

<section class="spd">
<div class="container" style="max-width:1280px;">

<?php if ($success = \App\Core\Session::getFlash('success')): ?>
  <div class="spd-box" style="background:#f0fdf4;border-color:#bbf7d0;color:#166534;margin:0 16px 14px;padding:12px 16px;">✓ <?= e($success) ?></div>
<?php endif; ?>
<?php if ($error = \App\Core\Session::getFlash('error')): ?>
  <div class="spd-box" style="background:#fef2f2;border-color:#fecaca;color:#991b1b;margin:0 16px 14px;padding:12px 16px;">⚠️ <?= e($error) ?></div>
<?php endif; ?>

<?php if (!$member): ?>
  <div class="spd-box" style="text-align:center;padding:48px;max-width:560px;margin:0 auto;">
    <h2><?= $t('সদস্য রেকর্ড পাওয়া যায়নি', 'No Member Profile Found') ?></h2>
    <p class="spd-muted"><?= $t('অনুগ্রহ করে নতুন সদস্যপদ আবেদন সম্পন্ন করুন।', 'Please complete a membership application.') ?></p>
    <a href="<?= url('/membership/apply', $currentLocale) ?>" class="spd-btn pri"><?= $t('আবেদন করুন', 'Apply Now') ?></a>
  </div>

<?php elseif ($isPending): ?>
  <?php
    $recentPayment = null;
    foreach ($payments as $p) {
        if (($p['member_code'] ?? '') === ($member['member_code'] ?? '') || ($p['member_id'] ?? '') === ($member['id'] ?? '')) { $recentPayment = $p; break; }
    }
    $steps = [
      [$t('১. আবেদন ও TrxID জমা', '1. Application'), $t('সফলভাবে গৃহীত', 'Completed'), 'done'],
      [$t('২. প্রশাসন কর্তৃক যাচাই', '2. Verification'), $t('বর্তমানে প্রক্রিয়াধীন', 'In Progress'), 'now'],
      [$t('৩. অনুমোদন ও সক্রিয়করণ', '3. Approval'), $t('যাচাই শেষে কার্যকর', 'Pending'), 'wait'],
      [$t('৪. স্মার্ট কার্ড ও প্রোফাইল', '4. Active Profile'), $t('লগইনের পর উন্মুক্ত', 'Unlocked on Login'), 'wait'],
    ];
    $sty = ['done'=>'background:#f0fdf4;border:1px solid #86efac;color:#166534','now'=>'background:#fffbeb;border:1.5px solid #f59e0b;color:#92400e','wait'=>'background:#f8fafc;border:1px dashed #cbd5e1;color:#64748b'];
  ?>
  <div class="spd-box" style="max-width:880px;margin:0 auto;padding:30px 34px;">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:14px;padding-bottom:18px;border-bottom:1px solid var(--line);margin-bottom:22px;">
      <div class="spd-id" style="margin:0">
        <img class="spd-av" style="width:58px;height:58px;border-color:#f59e0b" src="<?= asset($resolvedAvatar) ?>" alt="" onerror="this.onerror=null;this.src='<?= $defaultDp ?>'">
        <div>
          <strong style="font-size:1.25rem;color:var(--ink)"><?= e($memberName) ?></strong>
          <code style="margin-left:6px;background:#f1f5f9;padding:2px 8px;border-radius:4px"><?= e($member['member_code']) ?></code>
          <div class="spd-muted"><?= e($isBn ? ($category['name_bn'] ?? '') : ($category['name_en'] ?? '')) ?> • <b style="color:#b45309"><?= e($isBn ? ($plan['name_bn'] ?? '') : ($plan['name_en'] ?? '')) ?></b></div>
        </div>
      </div>
      <span class="spd-pill" style="background:#fffbeb;color:#b45309;border:1px solid #fde68a;padding:6px 14px">⏳ <?= $t('আবেদন ও পেমেন্ট যাচাই প্রক্রিয়াধীন', 'Under Verification') ?></span>
    </div>

    <div class="spd-grid2" style="margin-bottom:22px;grid-template-columns:repeat(auto-fit,minmax(170px,1fr))">
      <?php foreach ($steps as [$ti, $sub, $k]): ?>
        <div style="<?= $sty[$k] ?>;border-radius:8px;padding:10px 12px;font-size:.82rem"><strong><?= $k==='done'?'✓ ':($k==='now'?'⏳ ':'🔒 ') ?><?= $ti ?></strong><div style="font-size:.72rem"><?= $sub ?></div></div>
      <?php endforeach; ?>
    </div>

    <div style="background:linear-gradient(135deg,#fffbeb,#fef3c7);border:1px solid #fde68a;border-radius:12px;padding:18px 24px;margin-bottom:20px;text-align:center">
      <div style="font-size:.78rem;font-weight:800;color:#92400e;margin-bottom:6px"><?= $t('ধৈর্য ও আত্মসংযমের শাশ্বত শাস্ত্রীয় বার্তা', 'Eternal Message of Patience') ?></div>
      <div style="font-family:'Tiro Devanagari Sanskrit','Noto Serif Devanagari','Sanskrit Text',serif;font-size:1.15rem;font-weight:700;color:#78350f;line-height:1.6">शक्नोतीहैव यः सोढुं प्राक्शरीरविमोक्षणात्। कामक्रोधोद्भवं वेगं स युक्तः स सुखी नरः।।</div>
      <div style="font-size:.82rem;font-weight:800;color:#b45309;margin:4px 0 8px"><?= $t('শ্রীমদ্ভগবদ্গীতা — অধ্যায় ৫, শ্লোক ২৩', 'Bhagavad Gita — Chapter 5, Verse 23') ?></div>
      <div style="font-size:.84rem;color:#78350f;line-height:1.5;border-top:1px dashed #d97706;padding-top:8px">
        <?= $t('দেহত্যাগের পূর্ব পর্যন্ত যিনি ধৈর্যের সাথে অন্তরের বেগ সহ্য করতে পারেন, তিনিই প্রকৃত যোগী ও সুখী। আপনার সদস্যপদ আবেদন ও পেমেন্ট তথ্য সফলভাবে সংরক্ষিত রয়েছে। প্রশাসন কর্তৃক যাচাই ও অনুমোদন সম্পন্ন হওয়ামাত্র আপনার প্রোফাইল ও লগইন সুবিধা সক্রিয় করা হবে।',
                'One who can tolerate the impulses of desire and anger before quitting the body is a yogi and is truly happy. Your application has been received and is under review. Once approved, your profile and login access will be activated.') ?>
      </div>
    </div>

    <div style="background:#f8fafc;border:1px solid var(--line);border-radius:8px;padding:12px 18px;margin-bottom:18px;display:flex;justify-content:space-between;flex-wrap:wrap;gap:12px;font-size:.82rem">
      <div><span class="spd-muted"><?= $t('প্ল্যান ও ফি:', 'Plan & Fee:') ?></span> <strong style="color:#b45309"><?= e($isBn ? ($plan['name_bn'] ?? '') : ($plan['name_en'] ?? '')) ?> (৳<?= number_format($plan['fee'] ?? 50) ?>)</strong></div>
      <div><span class="spd-muted">TrxID:</span> <strong style="font-family:monospace;color:#0284c7"><?= e($recentPayment['trx_id'] ?? $recentPayment['transaction_id'] ?? $member['notes'] ?? 'Pending') ?></strong></div>
      <div><span class="spd-muted"><?= $t('তারিখ:', 'Date:') ?></span> <strong><?= e($member['join_date'] ?? date('Y-m-d')) ?></strong></div>
      <div><span class="spd-muted"><?= $t('যাচাইকারী কর্তৃপক্ষ:', 'Reviewing Authority:') ?></span> <strong style="color:#166534"><?= $t('এসপিএস প্রশাসন ও অর্থ বিভাগ', 'SPS Administration & Finance Desk') ?></strong></div>
    </div>
    <div style="text-align:center"><a href="<?= url('/', $currentLocale) ?>" class="spd-btn">← <?= $t('মূল ওয়েবসাইটে ফিরে যান', 'Back to Home') ?></a></div>
  </div>

<?php else: ?>
<div class="spd-shell">

  <!-- ============ LEFT NAV ============ -->
  <aside class="spd-side">
    <div class="spd-box">
      <div class="spd-id">
        <img class="spd-av" src="<?= asset($resolvedAvatar) ?>" alt="" onerror="this.onerror=null;this.src='<?= $defaultDp ?>'">
        <div style="min-width:0">
          <strong style="color:var(--ink);display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= e($memberName) ?></strong>
          <code style="font-size:.76rem"><?= e($member['member_code']) ?></code>
        </div>
      </div>
      <span class="spd-pill" style="background:<?= $stBg ?>;color:<?= $stColor ?>">● <?= $stLabel ?></span>
    </div>

    <nav class="spd-box spd-nav" style="padding:8px" aria-label="<?= $t('সদস্য মেনু', 'Member menu') ?>">
      <div class="spd-nav-g"><?= $t('প্রধান', 'Main') ?></div>
      <button data-go="feed" class="on">🏠 <?= $t('ড্যাশবোর্ড', 'Dashboard') ?></button>
      <button data-go="notices">📢 <?= $t('নোটিশ', 'Notices') ?></button>
      <button data-go="events">📅 <?= $t('ইভেন্ট', 'Events') ?></button>
      <div class="spd-nav-g"><?= $t('আমার SPS', 'My SPS') ?></div>
      <button data-go="profile">👤 <?= $t('আমার প্রোফাইল', 'My Profile') ?></button>
      <button data-go="membership">💳 <?= $t('সদস্যপদ ও ফি', 'Membership & Fees') ?></button>
      <button data-go="notifications">🔔 <?= $t('বিজ্ঞপ্তি', 'Notifications') ?><span class="spd-badge" id="spd-unread-badge" <?= $unread ? '' : 'hidden' ?>><?= $unread ?></span></button>
      <div class="spd-nav-g"><?= $t('কনটেন্ট', 'Content') ?></div>
      <button data-go="writing">✍️ <?= $t('আমার লেখা', 'My Writing') ?></button>
      <a href="<?= url('/library', $currentLocale) ?>" style="text-decoration:none"><button type="button">📚 <?= $t('গ্রন্থাগার', 'SPS Library') ?></button></a>
      <div class="spd-nav-g"><?= $t('অন্যান্য', 'Other') ?></div>
      <button data-go="settings">⚙️ <?= $t('সেটিংস', 'Settings') ?></button>
      <a href="<?= url('/membership/logout', $currentLocale) ?>" style="text-decoration:none"><button type="button" style="color:#b91c1c">🚪 <?= $t('লগআউট', 'Logout') ?></button></a>
    </nav>

  </aside>

  <!-- ============ CENTER ============ -->
  <main>

    <!-- FEED -->
    <div class="spd-sec on" id="sec-feed">
      <?php foreach (array_reverse($notifications) as $n): ?>
        <div class="spd-box" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-color:#86efac;display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:center">
          <div><strong style="color:#166534">🎉 <?= e($n['title'] ?? $t('সদস্যপদ সক্রিয় হয়েছে', 'Membership Approved')) ?></strong><div style="color:#15803d;font-size:.85rem"><?= e($n['message'] ?? '') ?></div></div>
          <?php if (!empty($n['invoice_id'])): ?><a target="_blank" class="spd-btn pri" style="background:#16a34a;border-color:#15803d" href="<?= url('/invoice/' . urlencode($n['invoice_id']), $currentLocale) ?>">🧾 <?= $t('মানি রিসিট দেখুন', 'View Payment Receipt') ?></a><?php endif; ?>
        </div>
      <?php endforeach; ?>

      <?php if ($status === 'Payment Due'): ?>
        <div class="spd-box spd-notice"><strong>💳 <?= $t('আপনার সদস্যপদ ফি বকেয়া আছে', 'Your membership fee is due') ?></strong>
          <div class="spd-muted"><?= $t('সদস্যপদ সক্রিয় রাখতে ফি প্রদান করুন।', 'Pay your fee to keep your membership active.') ?></div>
          <button class="spd-btn pri" style="margin-top:8px" data-go="membership"><?= $t('ফি প্রদান করুন', 'Pay fee') ?></button></div>
      <?php endif; ?>

      <div class="spd-box spd-compose">
        <img class="spd-av" style="width:40px;height:40px" src="<?= asset($resolvedAvatar) ?>" alt="" onerror="this.onerror=null;this.src='<?= $defaultDp ?>'">
        <a href="<?= url('/blog/write', $currentLocale) ?>"><?= $t('SPS-এ কী ঘটছে? লেখা শুরু করুন…', "What's happening at SPS? Write an article…") ?></a>
      </div>

      <?php if (empty($feedPosts)): ?>
        <div class="spd-box spd-empty">
          <div style="font-size:2rem">📰</div>
          <strong style="color:var(--ink)"><?= $t('এখনও কোনো আপডেট নেই', 'No updates yet') ?></strong><br>
          <?= $t('SPS-এর প্রকল্প, খবর ও ঘোষণা প্রকাশিত হলে এখানে দেখাবে।', 'Projects, news and announcements from SPS will appear here.') ?>
        </div>
      <?php else: foreach ($feedPosts as $post): $official = !empty($post['official']); ?>
        <article class="spd-box">
          <div class="spd-post-h">
            <img class="spd-av" style="width:40px;height:40px" src="<?= asset('assets/images/brand/sps-logo.png') ?>" alt="SPS">
            <div><strong style="color:var(--ink)">SPS</strong> <span class="spd-tag <?= $official ? 'official' : '' ?>"><?= e($official ? $t('অফিসিয়াল আপডেট', 'Official SPS Update') : ($post['category'] ?? '')) ?></span>
              <div class="spd-muted"><?= e($post['date'] ?? '') ?></div></div>
          </div>
          <?php if (!empty($post['image'])): ?><img src="<?= asset($post['image']) ?>" alt="" style="width:100%;border-radius:8px;margin-bottom:10px;max-height:320px;object-fit:cover"><?php endif; ?>
          <h3><?= e($post['title'] ?? '') ?></h3>
          <p style="margin:0;line-height:1.6;font-size:.92rem"><?= e($post['excerpt'] ?? '') ?></p>
          <?php if (!empty($post['url'])): ?><a href="<?= e($post['url']) ?>" style="font-weight:700;color:var(--sun);font-size:.88rem"><?= $t('আরও পড়ুন', 'Read more') ?></a><?php endif; ?>
          <div class="spd-post-f"><button>👍 <?= $t('লাইক', 'Like') ?></button><button>💬 <?= $t('মন্তব্য', 'Comment') ?></button><button>🔖 <?= $t('সংরক্ষণ', 'Save') ?></button><button>🔗 <?= $t('শেয়ার', 'Share') ?></button></div>
        </article>
      <?php endforeach; endif; ?>

      <div class="spd-box">
        <h4>📚 <?= $t('গ্রন্থাগার থেকে', 'From the Library') ?></h4>
        <p class="spd-muted"><?= $t('পাবলিক, সদস্য ও প্রিমিয়াম সংগ্রহ ব্রাউজ করুন।', 'Browse public, member and premium collections.') ?></p>
        <a class="spd-btn" href="<?= url('/library', $currentLocale) ?>"><?= $t('গ্রন্থাগার খুলুন', 'Open library') ?></a>
      </div>
    </div>

    <!-- NOTICES -->
    <div class="spd-sec" id="sec-notices">
      <div class="spd-box"><h3>📢 <?= $t('নোটিশ ও ঘোষণা', 'Notices & Announcements') ?></h3>
        <?php if (empty($notices)): ?><div class="spd-empty"><?= $t('বর্তমানে কোনো নোটিশ নেই।', 'No notices right now.') ?></div>
        <?php else: foreach ($notices as $n): ?>
          <div class="spd-box spd-notice" style="margin-bottom:10px"><strong><?= e($n['title'] ?? '') ?></strong><div class="spd-muted"><?= e($n['date'] ?? '') ?></div><p style="margin:6px 0 0"><?= e($n['body'] ?? '') ?></p></div>
        <?php endforeach; endif; ?></div>
    </div>

    <!-- EVENTS -->
    <div class="spd-sec" id="sec-events">
      <div class="spd-box"><h3>📅 <?= $t('আসন্ন ইভেন্ট', 'Upcoming Events') ?></h3>
        <?php if (empty($events)): ?><div class="spd-empty"><?= $t('কোনো আসন্ন ইভেন্ট নেই।', 'No upcoming events.') ?></div>
        <?php else: ?><div class="spd-grid2"><?php foreach ($events as $ev): ?>
          <div class="spd-box" style="padding:14px"><strong style="color:var(--sun)"><?= e($ev['date'] ?? '') ?></strong><div style="font-weight:800;color:var(--ink)"><?= e($ev['title'] ?? '') ?></div><div class="spd-muted"><?= e($ev['time'] ?? '') ?> <?= !empty($ev['place']) ? '📍 ' . e($ev['place']) : '' ?></div></div>
        <?php endforeach; ?></div><?php endif; ?></div>
    </div>

    <!-- NOTIFICATIONS -->
    <div class="spd-sec" id="sec-notifications">
      <div class="spd-box"><div style="display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:8px">
        <h3 style="margin:0">🔔 <?= $t('বিজ্ঞপ্তি', 'Notifications') ?></h3>
        <button type="button" id="spd-mark-all" class="spd-btn" style="padding:5px 12px;font-size:.8rem" <?= $unread ? '' : 'hidden' ?>>✓ <?= $t('সব পড়া হয়েছে', 'Mark all as read') ?></button></div>
        <?php if (empty($notifications)): ?><div class="spd-empty"><?= $t('কোনো নতুন বিজ্ঞপ্তি নেই।', 'You are all caught up.') ?></div>
        <?php else: foreach (array_reverse($notifications) as $n): ?>
          <button type="button" class="spd-notif" data-nid="<?= e($nid($n)) ?>" data-read="<?= empty($n['read']) ? '0' : '1' ?>">
            <span class="spd-dot"></span>
            <span><strong><?= e($n['title'] ?? '') ?></strong><span class="spd-muted" style="display:block"><?= e($n['message'] ?? '') ?></span></span>
          </button>
        <?php endforeach; endif; ?></div>
    </div>

    <!-- WRITING -->
    <div class="spd-sec" id="sec-writing">
      <div class="spd-box"><div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px"><h3 style="margin:0">✍️ <?= $t('আমার লেখা', 'My Writing') ?></h3>
        <a class="spd-btn pri" href="<?= url('/blog/write', $currentLocale) ?>">+ <?= $t('নতুন লেখা', 'Write new article') ?></a></div>
        <?php $cnt = ['draft'=>0,'pending'=>0,'published'=>0,'rejected'=>0]; foreach ($myArticles as $a) { $k = strtolower($a['status'] ?? 'draft'); if (isset($cnt[$k])) $cnt[$k]++; } ?>
        <div class="spd-stats" style="margin:16px 0">
          <div><b><?= $cnt['draft'] ?></b><span class="spd-muted"><?= $t('খসড়া', 'Drafts') ?></span></div>
          <div><b><?= $cnt['pending'] ?></b><span class="spd-muted"><?= $t('অপেক্ষমাণ', 'Pending') ?></span></div>
          <div><b><?= $cnt['published'] ?></b><span class="spd-muted"><?= $t('প্রকাশিত', 'Published') ?></span></div>
          <div><b><?= $cnt['rejected'] ?></b><span class="spd-muted"><?= $t('বাতিল', 'Rejected') ?></span></div>
        </div>
        <p class="spd-muted"><?= $t('লেখা জমা দিলে সম্পাদক পর্যালোচনা করে প্রকাশ করবেন।', 'Submitted articles are reviewed by an editor before they are published.') ?></p>
        <?php foreach ($myArticles as $a): ?><div style="padding:8px 0;border-top:1px solid #eef1f5"><strong><?= e($a['title'] ?? '') ?></strong> <span class="spd-tag"><?= e($a['status'] ?? '') ?></span></div><?php endforeach; ?>
      </div>
    </div>

    <!-- PROFILE -->
    <div class="spd-sec" id="sec-profile">
      <div class="spd-box" style="display:flex;gap:16px;align-items:center;flex-wrap:wrap">
        <img class="spd-av" style="width:84px;height:84px" src="<?= asset($resolvedAvatar) ?>" alt="" onerror="this.onerror=null;this.src='<?= $defaultDp ?>'">
        <div><h2 style="margin:0;color:var(--ink);font-size:1.5rem"><?= e($memberName) ?></h2>
          <div class="spd-muted"><?= e($isBn ? ($category['name_bn'] ?? '') : ($category['name_en'] ?? '')) ?> • <b style="color:#b45309"><?= e($isBn ? ($plan['name_bn'] ?? '') : ($plan['name_en'] ?? '')) ?></b></div>
          <div class="spd-muted"><?= $t('যোগদান:', 'Joined:') ?> <?= e($member['joined_at'] ?? '2024') ?> | <?= e($member['email'] ?? '') ?> | <?= e($member['phone'] ?? '') ?></div></div>
      </div>

      <form class="spd-box" action="<?= url('/membership/profile/update', $currentLocale) ?>" method="POST" enctype="multipart/form-data" id="memberProfileForm">
        <?= $csrf ?><input type="hidden" name="member_code" value="<?= e($member['member_code']) ?>">
        <h3>👤 <?= $t('প্রোফাইল হালনাগাদ', 'Edit profile') ?></h3>
        <div style="display:flex;gap:14px;align-items:center;flex-wrap:wrap;background:#f8fafc;border:1px solid var(--line);border-radius:8px;padding:12px;margin-bottom:14px">
          <img id="avatarPreviewImg" class="spd-av" style="width:72px;height:72px;border-radius:10px" src="<?= asset($resolvedAvatar) ?>" alt="" onerror="this.onerror=null;this.src='<?= $defaultDp ?>'">
          <div style="flex:1;min-width:220px"><label for="mem_avatar_file">📷 <?= $t('সদস্য ছবি আপলোড', 'Upload member photo') ?></label>
            <div class="spd-muted" style="margin-bottom:6px"><?= $t('JPG, PNG, WebP — সর্বোচ্চ ৫MB। কার্ডে প্রদর্শিত হবে।', 'JPG, PNG, WebP — max 5MB. Shown on your ID card.') ?></div>
            <input type="file" id="mem_avatar_file" name="avatar_file" accept="image/png,image/jpeg,image/webp" onchange="previewAvatar(this)">
            <input type="hidden" name="avatar" value="<?= e($member['avatar'] ?? '') ?>"></div>
        </div>
        <div class="spd-grid2" style="margin-bottom:12px">
          <div><label for="mem_name_bn"><?= $t('পূর্ণ নাম (বাংলা) *', 'Full name (Bengali) *') ?></label><input id="mem_name_bn" name="name_bn" required value="<?= e($member['name_bn'] ?? '') ?>"></div>
          <div><label for="mem_name_en"><?= $t('পূর্ণ নাম (English) *', 'Full name (English) *') ?></label><input id="mem_name_en" name="name_en" required value="<?= e($member['name_en'] ?? '') ?>"></div>
          <div><label for="mem_phone"><?= $t('মোবাইল *', 'Mobile *') ?></label><input id="mem_phone" name="phone" required value="<?= e($member['phone'] ?? '') ?>"></div>
          <div><label for="mem_email"><?= $t('ইমেইল *', 'Email *') ?></label><input id="mem_email" type="email" name="email" required value="<?= e($member['email'] ?? '') ?>"></div>
          <div><label for="mem_district"><?= $t('জেলা', 'District') ?></label><input id="mem_district" name="district" value="<?= e($member['district'] ?? '') ?>"></div>
          <div><label for="mem_upazila"><?= $t('উপজেলা / এলাকা', 'Upazila / Area') ?></label><input id="mem_upazila" name="upazila" value="<?= e($member['upazila'] ?? '') ?>"></div>
          <div><label for="mem_blood_group"><?= $t('রক্তের গ্রুপ', 'Blood group') ?></label>
            <select id="mem_blood_group" name="blood_group"><option value=""><?= $t('-- নির্বাচন --', '-- Select --') ?></option>
              <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $b): ?><option <?= ($member['blood_group'] ?? '') === $b ? 'selected' : '' ?>><?= $b ?></option><?php endforeach; ?></select></div>
          <div><label for="mem_address"><?= $t('বর্তমান ঠিকানা', 'Present address') ?></label><input id="mem_address" name="address" value="<?= e($member['address'] ?? '') ?>"></div>
          <div><label for="mem_institution"><?= $t('শিক্ষা প্রতিষ্ঠান / কর্মস্থল', 'Institution / workplace') ?></label><input id="mem_institution" name="institution" value="<?= e($member['education']['institution'] ?? ($member['profession']['institution'] ?? '')) ?>"></div>
          <div><label for="mem_designation"><?= $t('বিভাগ / পদবি', 'Department / designation') ?></label><input id="mem_designation" name="designation" value="<?= e($member['education']['department'] ?? ($member['profession']['designation'] ?? '')) ?>"></div>
        </div>
        <label for="mem_bio"><?= $t('সংক্ষিপ্ত পরিচিতি ও সেবামূলক আগ্রহ', 'Bio & seva interests') ?></label>
        <textarea id="mem_bio" name="bio" rows="3"><?= e($member['bio'] ?? ($member['notes'] ?? '')) ?></textarea>
        <div style="text-align:right;margin-top:12px"><button type="submit" id="saveMemberProfileBtn" class="spd-btn pri">💾 <?= $t('পরিবর্তন সংরক্ষণ', 'Save changes') ?></button></div>
      </form>
    </div>

    <!-- MEMBERSHIP & FEES -->
    <div class="spd-sec" id="sec-membership">
      <div class="spd-card" id="printableCard">
        <img src="<?= asset('assets/images/brand/sps-logo-white.png') ?>" alt="" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);width:150px;opacity:.12">
        <div style="display:flex;align-items:center;gap:10px;position:relative">
          <img src="<?= asset('assets/images/brand/sps-logo-white.png') ?>" alt="SPS" style="height:36px">
          <div><div style="font-weight:700;font-family:'Noto Sans Bengali',sans-serif">সনাতন ফিলোসফি এন্ড স্ক্রিপচার</div><div style="font-size:.64rem;color:#cbd5e1;letter-spacing:.8px">SANATAN PHILOSOPHY &amp; SCRIPTURE</div></div>
        </div>
        <div style="display:flex;gap:14px;align-items:center;margin:14px 0;position:relative">
          <img src="<?= asset($resolvedAvatar) ?>" alt="" style="width:74px;height:74px;border-radius:12px;border:2.5px solid #c59b27;object-fit:cover" onerror="this.onerror=null;this.src='<?= $defaultDp ?>'">
          <div style="min-width:0"><div style="font-size:1.25rem;font-weight:800;font-family:'Noto Sans Bengali',sans-serif"><?= e($member['name_bn']) ?></div>
            <div style="color:#e2e8f0"><?= e($member['name_en']) ?></div>
            <span style="display:inline-block;margin-top:4px;background:#9fc6e2;color:#082136;font-family:monospace;font-weight:800;padding:2px 10px;border-radius:6px">ID : <?= e($member['member_code']) ?></span></div>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:flex-end;position:relative">
          <div><div style="font-weight:700"><?= e($category['name_en'] ?? ($member['category_id'] === 'STUDENT' ? 'Student Member' : 'Earning Member')) ?></div>
            <div style="font-weight:800;color:#22c55e"><?= empty($member['expiry_date']) ? 'VALID: LIFETIME' : 'VALID: ' . e($member['expiry_date']) ?></div></div>
          <a href="<?= url('/membership/verify?code=' . e($member['member_code']), $currentLocale) ?>" target="_blank" style="background:#fff;padding:4px;border-radius:8px;width:56px;height:56px;display:block"><img id="dashboardCardQrImg" class="sps-qr-code-img" src="" alt="SPS Verified QR" style="width:100%;height:100%;object-fit:contain"></a>
        </div>
      </div>
      <div style="display:flex;gap:8px">
        <a class="spd-btn" style="flex:1" target="_blank" href="<?= url('/membership/card/print?code=' . e($member['member_code']), $currentLocale) ?>">🖨️ <?= $t('কার্ড প্রিন্ট / ডাউনলোড', 'Print / download card') ?></a>
        <a class="spd-btn" style="flex:1" target="_blank" href="<?= url('/membership/verify?code=' . e($member['member_code']), $currentLocale) ?>">🔗 <?= $t('কিউআর যাচাই', 'QR verify') ?></a>
      </div>

      <?php if ($member['category_id'] === 'STUDENT'): ?>
      <div class="spd-box" style="background:#fff7ed;border-color:#fdba74">
        <h3 style="color:#9a3412">🎓 ➔ 💼 <?= $t('উপার্জনশীল সদস্যপদে রূপান্তর', 'Transition to Earning Member') ?></h3>
        <p class="spd-muted"><?= $t('আপনার স্থায়ী আইডি (' . e($member['member_code']) . ') অক্ষুণ্ণ রেখে ক্যাটাগরি পরিবর্তন করুন।', 'Keep your permanent ID (' . e($member['member_code']) . ') while switching category.') ?></p>
        <button type="button" class="spd-btn pri" onclick="var b=document.getElementById('transition-form-box');b.style.display=b.style.display==='none'?'block':'none'"><?= $t('রূপান্তর আবেদন', 'Apply transition') ?></button>
        <form id="transition-form-box" style="display:none;margin-top:12px" action="<?= url('/membership/transition', $currentLocale) ?>" method="POST">
          <?= $csrf ?><input type="hidden" name="member_id" value="<?= e($member['id']) ?>"><input type="hidden" name="new_category" value="EARNING">
          <label><?= $t('পরবর্তী প্ল্যান', 'New plan') ?></label>
          <select name="new_plan"><option value="EARNING_MONTHLY"><?= $t('মাসিক (৳১০০/মাস)', 'Monthly (৳100/mo)') ?></option><option value="YEARLY"><?= $t('বাৎসরিক (৳১,০০০)', 'Yearly (৳1,000)') ?></option><option value="LIFETIME"><?= $t('আজীবন (৳১০,০০০)', 'Lifetime (৳10,000)') ?></option></select>
          <label style="margin-top:8px"><?= $t('কারণ / বর্তমান কর্মবিবরণ', 'Reason / professional update') ?></label><textarea name="reason" rows="2"></textarea>
          <button type="submit" class="spd-btn pri" style="margin-top:10px"><?= $t('রূপান্তর নিশ্চিত করুন', 'Confirm transition') ?></button>
        </form>
      </div>
      <?php endif; ?>

      <form class="spd-box" action="<?= url('/membership/payment', $currentLocale) ?>" method="POST" enctype="multipart/form-data" id="dashPaymentForm">
        <?= $csrf ?><input type="hidden" name="member_id" value="<?= e($member['id']) ?>">
        <input type="hidden" name="payment_screenshot_url" id="dash_preset_screenshot" value="assets/images/payments/bkash-success-sample.svg">
        <h3>💳 <?= $t('ফি প্রদান / নবায়ন / আপগ্রেড', 'Pay fee / renew / upgrade') ?></h3>
        <div style="background:#fdf2f8;border:1px solid #fbcfe8;border-radius:8px;padding:12px;margin-bottom:12px;font-size:.82rem;color:#831843">
          <strong>📱 <?= $t('অফিসিয়াল বিকাশ নম্বর:', 'Official bKash:') ?></strong> <code>01700-000000</code>
          <button type="button" onclick="navigator.clipboard&&navigator.clipboard.writeText('01700-000000')" class="spd-btn" style="padding:1px 8px;font-size:.72rem">📋</button>
          <div style="margin-top:4px"><?= $t('Send Money করার পর TrxID ও Sent SS আপলোড করুন। ফাইন্যান্স অফিসার (কোষাধ্যক্ষ) প্রতিটি পেমেন্ট যাচাই করবেন।', 'Send money, then enter the TrxID and upload the Sent SS. The Finance Officer verifies every payment.') ?></div>
        </div>
        <div class="spd-grid2" style="margin-bottom:10px">
          <div><label><?= $t('পেমেন্ট ধরন', 'Payment type') ?></label><select name="payment_type"><option value="monthly"><?= $t('মাসিক নবায়ন', 'Monthly renewal') ?></option><option value="yearly"><?= $t('বাৎসরিক (৳১,০০০)', 'Yearly (৳1,000)') ?></option><option value="lifetime"><?= $t('আজীবন (৳১০,০০০)', 'Lifetime (৳10,000)') ?></option></select></div>
          <div><label><?= $t('মাধ্যম', 'Method') ?></label><select name="payment_method" id="dash_payment_method"><option value="bKash">bKash</option><option value="Nagad">Nagad</option><option value="Rocket">Rocket</option><option value="Bank">Bank Transfer</option></select></div>
          <div><label><?= $t('টাকার পরিমাণ', 'Amount') ?></label><input type="number" name="amount" placeholder="<?= $member['category_id'] === 'STUDENT' ? '50' : '100' ?>"></div>
          <div><label><?= $t('প্রেরক নম্বর *', 'Sender phone *') ?></label><input name="sender_number" required value="<?= e($member['phone'] ?? '') ?>"></div>
          <div><label><?= $t('প্রেরকের নাম', 'Sender name') ?></label><input name="sender_name"></div>
          <div><label><?= $t('ট্রানজেকশন আইডি (TrxID) *', 'Transaction ID (TrxID) *') ?></label><input name="trx_id" id="dash_trx_id" required placeholder="BKA8X92JQK" style="text-transform:uppercase;font-family:monospace;font-weight:800"></div>
          <div><label><?= $t('রেফারেন্স', 'Reference') ?></label><input name="payment_reference"></div>
          <div><label><?= $t('পেমেন্ট সময়', 'Time') ?></label><input name="payment_time" value="<?= date('Y-m-d H:i') ?>"></div>
        </div>
        <label>📸 <?= $t('সেন্ট মানি স্ক্রিনশট (Sent SS) *', 'Sent money screenshot (Sent SS) *') ?></label>
        <div style="border:2px dashed #f472b6;border-radius:8px;padding:12px;text-align:center;background:#fdf2f8">
          <input type="file" name="payment_screenshot" id="dash_screenshot_file" accept="image/*" onchange="previewDashScreenshot(this)" style="display:none">
          <img id="dash_screenshot_preview_img" src="<?= asset('assets/images/payments/bkash-success-sample.svg') ?>" alt="" style="max-height:120px;max-width:100%;border-radius:6px;background:#fff">
          <div style="margin-top:8px;display:flex;gap:6px;justify-content:center;flex-wrap:wrap">
            <button type="button" class="spd-btn" style="padding:4px 10px;font-size:.76rem" onclick="document.getElementById('dash_screenshot_file').click()">🔄 <?= $t('ছবি আপলোড', 'Upload image') ?></button>
            <button type="button" class="spd-btn" style="padding:4px 10px;font-size:.76rem" onclick="setDashPresetScreenshot('assets/images/payments/bkash-success-sample.svg')">🌸 bKash</button>
            <button type="button" class="spd-btn" style="padding:4px 10px;font-size:.76rem" onclick="setDashPresetScreenshot('assets/images/payments/nagad-success-sample.svg')">🔥 Nagad</button>
            <button type="button" class="spd-btn" style="padding:4px 10px;font-size:.76rem" onclick="document.getElementById('dash_trx_id').value='BKA'+Math.random().toString(36).substring(2,8).toUpperCase()">🎲 <?= $t('টেস্ট TrxID', 'Demo TrxID') ?></button>
          </div>
        </div>
        <button type="submit" class="spd-btn pri" style="width:100%;margin-top:12px">🚀 <?= $t('পেমেন্ট রসিদ সাবমিট করুন', 'Submit payment') ?></button>
      </form>

      <?php if (!empty($history)): ?>
      <div class="spd-box"><h3>📜 <?= $t('সদস্যপদ রূপান্তর ইতিহাস', 'Membership history') ?></h3>
        <div style="border-left:2px solid var(--line);padding-left:16px;display:flex;flex-direction:column;gap:12px">
          <?php foreach ($history as $h): ?><div><div class="spd-muted"><?= e($h['changed_at'] ?? '') ?></div>
            <strong><?= e($h['old_category'] ?? '') ?> ➔ <span style="color:#c2410c"><?= e($h['new_category'] ?? '') ?></span></strong> (<?= e($h['old_plan'] ?? '') ?> ➔ <?= e($h['new_plan'] ?? '') ?>)
            <div class="spd-muted"><?= e($h['reason'] ?? '') ?></div></div><?php endforeach; ?></div></div>
      <?php endif; ?>

      <div class="spd-box"><h3>💰 <?= $t('পেমেন্ট ইতিহাস', 'Payment history') ?> <span class="spd-muted">(<?= count($payments) ?>)</span></h3>
        <?php if (empty($payments)): ?><div class="spd-empty"><?= $t('এখনো কোনো পেমেন্ট রেকর্ড নেই।', 'No payments yet.') ?></div>
        <?php else: ?><div style="overflow-x:auto"><table class="spd-table">
          <thead><tr><th>TrxID</th><th><?= $t('তারিখ', 'Date') ?></th><th><?= $t('ধরন', 'Type') ?></th><th><?= $t('পরিমাণ', 'Amount') ?></th><th><?= $t('মাধ্যম', 'Method') ?></th><th><?= $t('রসিদ', 'Receipt') ?></th><th><?= $t('স্ট্যাটাস', 'Status') ?></th><th><?= $t('মানি রসিদ', 'Invoice') ?></th></tr></thead>
          <tbody><?php foreach ($payments as $p):
            $shot = $p['payment_screenshot'] ?? '';
            $imgSrc = !empty($shot) ? (str_starts_with($shot, 'http') ? $shot : asset($shot)) : asset('assets/images/payments/bkash-success-sample.svg');
            $ps = $p['status'] ?? '';
            [$pb, $pc, $pl] = $ps === 'Verified' ? ['#dcfce7', '#15803d', '✓ ' . $t('যাচাইকৃত', 'Verified')] : ($ps === 'Rejected' ? ['#fee2e2', '#991b1b', '✕ ' . $t('বাতিল', 'Rejected')] : ['#fef3c7', '#b45309', '⌛ ' . $t('অপেক্ষমাণ', 'Pending')]);
          ?><tr>
            <td><code><?= e($p['transaction_id'] ?? '') ?></code><?php if (!empty($p['trx_id'])): ?><div class="spd-muted">TrxID: <b><?= e($p['trx_id']) ?></b></div><?php endif; ?></td>
            <td class="spd-muted" style="white-space:nowrap"><?= e($p['payment_date'] ?? $p['created_at'] ?? '') ?></td>
            <td><?= e($p['payment_type'] ?? '') ?></td>
            <td><b>৳<?= number_format((float)($p['amount'] ?? 0)) ?></b></td>
            <td><?= e($p['payment_method'] ?? 'bKash') ?><div class="spd-muted"><?= e($p['sender_number'] ?? '') ?></div></td>
            <td><button type="button" style="border:0;background:none;padding:0;cursor:pointer" onclick="openReceiptZoomModal(<?= e(json_encode($imgSrc)) ?>, <?= e(json_encode($p['trx_id'] ?? $p['transaction_id'] ?? '')) ?>)"><img src="<?= $imgSrc ?>" alt="Receipt" style="width:40px;height:40px;object-fit:cover;border-radius:6px;border:1px solid var(--line)"></button></td>
            <td><span class="spd-pill" style="background:<?= $pb ?>;color:<?= $pc ?>"><?= $pl ?></span><?php if ($ps === 'Rejected' && !empty($p['notes'])): ?><div class="spd-muted"><?= e($p['notes']) ?></div><?php endif; ?></td>
            <td><a class="spd-btn" style="padding:4px 10px;font-size:.76rem" target="_blank" href="<?= url('/invoice/' . e($p['transaction_id']), $currentLocale) ?>">📄 <?= $t('রসিদ', 'Receipt') ?></a></td>
          </tr><?php endforeach; ?></tbody></table></div><?php endif; ?></div>
    </div>

    <!-- SETTINGS -->
    <div class="spd-sec" id="sec-settings">
      <form class="spd-box" action="<?= url('/membership/password/update', $currentLocale) ?>" method="POST" id="memberPasswordUpdateForm">
        <?= $csrf ?>
        <h3>🔒 <?= $t('নিরাপত্তা ও পাসওয়ার্ড', 'Account security & password') ?> <span class="spd-pill" style="background:#ecfdf5;color:#047857">🛡️ Email 2FA</span></h3>
        <p class="spd-muted"><?= $t('প্রতিটি লগইনে আপনার ইমেইলে (' . e($member['email'] ?? '') . ') একটি ৬-সংখ্যার ওটিপি পাঠানো হবে।', 'Each login sends a 6-digit OTP to ' . e($member['email'] ?? '') . '.') ?></p>
        <div class="spd-grid2">
          <?php if (!empty($member['password_hash'])): ?><div><label for="mem_curr_password"><?= $t('বর্তমান পাসওয়ার্ড *', 'Current password *') ?></label><input type="password" id="mem_curr_password" name="current_password" required></div><?php endif; ?>
          <div><label for="mem_new_password"><?= $t('নতুন পাসওয়ার্ড *', 'New password *') ?></label><input type="password" id="mem_new_password" name="new_password" required minlength="6" placeholder="<?= $t('কমপক্ষে ৬ অক্ষর', 'At least 6 characters') ?>"></div>
          <div><label for="mem_confirm_password"><?= $t('নিশ্চিত করুন *', 'Confirm password *') ?></label><input type="password" id="mem_confirm_password" name="confirm_password" required></div>
        </div>
        <div style="text-align:right;margin-top:12px"><button type="submit" id="updateMemberPasswordBtn" class="spd-btn">🔐 <?= $t('পাসওয়ার্ড হালনাগাদ', 'Update password') ?></button></div>
      </form>
    </div>
  </main>

  <!-- ============ RIGHT PANEL ============ -->
  <aside class="spd-right">
    <div class="spd-box">
      <h4><?= $t('আপনার সদস্যপদ', 'Your membership') ?></h4>
      <span class="spd-pill" style="background:<?= $stBg ?>;color:<?= $stColor ?>">● <?= $stLabel ?></span>
      <div style="margin-top:8px;font-weight:700;color:#b45309"><?= e($isBn ? ($plan['name_bn'] ?? '') : ($plan['name_en'] ?? '')) ?></div>
      <div class="spd-muted"><?= $t('মেয়াদ:', 'Valid until:') ?> <b style="color:var(--ink)"><?= e($expiry) ?></b></div>
    </div>
    <div class="spd-box">
      <h4><?= $t('আসন্ন', 'Upcoming') ?></h4>
      <?php if (empty($events)): ?><div class="spd-muted"><?= $t('কোনো আসন্ন ইভেন্ট নেই।', 'No upcoming events.') ?></div>
      <?php else: foreach (array_slice($events, 0, 3) as $ev): ?><div style="margin-bottom:6px"><b style="color:var(--sun)"><?= e($ev['date'] ?? '') ?></b> <?= e($ev['title'] ?? '') ?></div><?php endforeach; endif; ?>
    </div>
    <div class="spd-box spd-nav" style="padding:10px">
      <h4 style="padding:0 6px"><?= $t('দ্রুত অ্যাক্সেস', 'Quick access') ?></h4>
      <a href="<?= url('/library', $currentLocale) ?>" style="text-decoration:none"><button type="button">📚 <?= $t('গ্রন্থাগার', 'Library') ?></button></a>
      <a href="<?= url('/blog/write', $currentLocale) ?>" style="text-decoration:none"><button type="button">✍️ <?= $t('লিখুন', 'Write') ?></button></a>
      <button data-go="membership">💳 <?= $t('ফি প্রদান', 'Pay fee') ?></button>
      <a href="<?= url('/activities', $currentLocale) ?>" style="text-decoration:none"><button type="button">🚩 <?= $t('কার্যক্রম ও প্রকল্প', 'Activities & projects') ?></button></a>
    </div>
  </aside>
</div>

<!-- Mobile bottom nav -->
<nav class="spd-bottom" aria-label="<?= $t('দ্রুত মেনু', 'Quick menu') ?>">
  <button data-go="feed" class="on"><span>🏠</span><?= $t('হোম', 'Home') ?></button>
  <button data-go="notices"><span>📰</span><?= $t('আপডেট', 'Updates') ?></button>
  <button data-go="writing"><span>✍️</span><?= $t('লেখা', 'Writing') ?></button>
  <button data-go="membership"><span>💳</span><?= $t('সদস্যপদ', 'Member') ?></button>
  <button data-go="profile"><span>👤</span><?= $t('প্রোফাইল', 'Profile') ?></button>
</nav>
<?php endif; ?>

</div>
</section>

<!-- Receipt zoom modal -->
<div id="receiptZoomModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.75);z-index:9999;align-items:center;justify-content:center;padding:20px" onclick="if(event.target===this)closeReceiptZoomModal()">
  <div style="background:#fff;border-radius:16px;max-width:480px;width:100%;padding:20px">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px"><strong id="receiptModalTitle" style="font-family:monospace"></strong>
      <button type="button" onclick="closeReceiptZoomModal()" aria-label="Close" style="background:none;border:0;font-size:1.4rem;cursor:pointer">&times;</button></div>
    <div style="text-align:center;background:#0f172a;border-radius:10px;padding:12px"><img id="receiptModalImg" src="" alt="Payment receipt" style="max-height:480px;max-width:100%;object-fit:contain"></div>
  </div>
</div>

<div id="spd-cfg" hidden
     data-asset-base="<?= e(asset('')) ?>"
     data-verify-url="<?= e($member ? \App\Services\QrCodeService::getVerificationUrl($member['member_code'], $currentLocale) : '') ?>"
     data-logo="<?= e(asset('assets/images/brand/sps-logo.png')) ?>"
     data-member="<?= e($member['member_code'] ?? '') ?>"
     data-csrf="<?= e(\App\Core\Session::getCsrfToken() ?: '') ?>"
     data-read-url="<?= e(url('/membership/notifications/read', $currentLocale)) ?>"></div>

<script src="<?= asset('assets/js/qrcode.min.js') ?>"></script>
<script src="<?= asset('assets/js/sps-qr.js') ?>"></script>
<script>
(function () {
  function show(id) {
    var sec = document.getElementById('sec-' + id);
    if (!sec) return;
    document.querySelectorAll('.spd-sec').forEach(function (s) { s.classList.toggle('on', s === sec); });
    document.querySelectorAll('[data-go]').forEach(function (b) {
      if (b.closest('.spd-nav, .spd-bottom') && !b.closest('.spd-right')) b.classList.toggle('on', b.dataset.go === id);
    });
    if (location.hash !== '#' + id) history.replaceState(null, '', '#' + id);
    window.scrollTo({ top: 0 });
  }
  document.addEventListener('click', function (e) {
    var b = e.target.closest('[data-go]');
    if (b) { e.preventDefault(); show(b.dataset.go); }
  });
  if (location.hash) show(location.hash.slice(1));

  document.addEventListener('DOMContentLoaded', function () {
    var cfg = document.getElementById('spd-cfg');
    var qrEl = document.getElementById('dashboardCardQrImg');
    if (cfg && qrEl && typeof renderSpsQrCode === 'function') {
      renderSpsQrCode('dashboardCardQrImg', cfg.dataset.verifyUrl, cfg.dataset.logo);
    }
  });
})();

(function () {
  var cfg = document.getElementById('spd-cfg');
  if (!cfg) return;
  var key = 'spd_read_' + cfg.dataset.member, read = [];
  try { read = JSON.parse(localStorage.getItem(key) || '[]'); } catch (e) {}
  var items = document.querySelectorAll('.spd-notif');
  function refresh() {
    var n = 0;
    items.forEach(function (el) {
      var r = el.dataset.read === '1' || read.indexOf(el.dataset.nid) > -1;
      el.classList.toggle('unread', !r);
      if (!r) n++;
    });
    var b = document.getElementById('spd-unread-badge');
    if (b) { b.textContent = n; b.hidden = n === 0; }
    var all = document.getElementById('spd-mark-all');
    if (all) all.hidden = n === 0;
  }
  function mark(ids) {
    var fresh = ids.filter(function (i) { return read.indexOf(i) < 0; });
    if (!fresh.length) return;
    read = read.concat(fresh);
    try { localStorage.setItem(key, JSON.stringify(read)); } catch (e) {}
    refresh();
    if (cfg.dataset.readUrl && window.fetch) {
      var f = new FormData();
      fresh.forEach(function (i) { f.append('ids[]', i); });
      f.append('_csrf', cfg.dataset.csrf || '');
      fetch(cfg.dataset.readUrl, { method: 'POST', body: f, credentials: 'same-origin' }).catch(function () {});
    }
  }
  items.forEach(function (el) { el.addEventListener('click', function () { mark([el.dataset.nid]); }); });
  var all = document.getElementById('spd-mark-all');
  if (all) all.addEventListener('click', function () {
    mark(Array.prototype.map.call(items, function (el) { return el.dataset.nid; }));
  });
  refresh();
})();

function previewAvatar(input) {
  if (input.files && input.files[0]) {
    var r = new FileReader();
    r.onload = function (e) { var i = document.getElementById('avatarPreviewImg'); if (i) i.src = e.target.result; };
    r.readAsDataURL(input.files[0]);
  }
}
function previewDashScreenshot(input) {
  if (input.files && input.files[0]) {
    var r = new FileReader();
    r.onload = function (e) {
      document.getElementById('dash_screenshot_preview_img').src = e.target.result;
      document.getElementById('dash_preset_screenshot').value = '';
    };
    r.readAsDataURL(input.files[0]);
  }
}
function setDashPresetScreenshot(path) {
  document.getElementById('dash_screenshot_preview_img').src = document.getElementById('spd-cfg').dataset.assetBase + path;
  document.getElementById('dash_preset_screenshot').value = path;
  document.getElementById('dash_screenshot_file').value = '';
}
function openReceiptZoomModal(src, id) {
  document.getElementById('receiptModalImg').src = src;
  document.getElementById('receiptModalTitle').textContent = 'Trx: ' + id;
  document.getElementById('receiptZoomModal').style.display = 'flex';
}
function closeReceiptZoomModal() { document.getElementById('receiptZoomModal').style.display = 'none'; }
document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeReceiptZoomModal(); });
</script>