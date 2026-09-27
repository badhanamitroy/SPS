<?php
/**
 * Admin Blog Moderation Portal (Super Admin, Admin, and Literature-Admin)
 */
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
?>

<div class="admin-page-header">
    <div class="header-left">
        <h2 class="admin-page-title">
            <span>✍️</span>
            <span><?= $isBn ? 'ব্লগ মডারেশন ও সাহিত্য প্রকাশনা' : 'Blog Moderation & Editorial Desk' ?></span>
        </h2>
        <p class="admin-page-desc">
            <?= $isBn 
                ? 'পেইড সদস্যদের প্রেরিত ব্লগের পাণ্ডুলিপি পাঠ ও পর্যালোচনা, অনুমোদন অথবা পরিবর্তন অনুরোধ। অনুমোদনের পর তা জনসম্মুখে প্রকাশিত হয়।' 
                : 'Review, approve, or reject blog submissions from paid members. Published posts immediately appear live.' ?>
        </p>
    </div>
    <div class="header-right">
        <a href="<?= url('/blog', $currentLocale) ?>" target="_blank" class="btn btn-sm btn-ghost" style="border:1px solid var(--border-medium);">
            🌐 <?= $isBn ? 'পাবলিক ব্লগ ফিড দেখুন ↗' : 'View Public Blog Feed ↗' ?>
        </a>
    </div>
</div>

<!-- Authority Badge -->
<div class="moderation-auth-banner">
    <div class="auth-icon">🛡️</div>
    <div class="auth-info">
        <strong><?= $isBn ? 'অনুমোদন ও নিয়ন্ত্রণ এক্তিয়ার:' : 'Editorial Authority Governance:' ?></strong>
        <span>
            <?= $isBn 
                ? 'শুধুমাত্র সুপার-অ্যাডমিন, অ্যাডমিন এবং সাহিত্য বিষয়ক সম্পাদক (Literature-Admin) এই ব্লগের পাণ্ডুলিপি অনুমোদন ও বাতিল করার ক্ষমতা রাখেন।' 
                : 'Strictly restricted to Super Administrator, Administrator, and Literature-Admin (Scripture Affairs Secretary).' ?>
        </span>
    </div>
    <div class="auth-user-pill">
        <?= $isBn ? 'বর্তমান প্রশাসক:' : 'Active Reviewer:' ?> 
        <strong><?= e($isBn ? ($currentUser['name_bn'] ?? '') : ($currentUser['name_en'] ?? '')) ?></strong> 
        (<?= e($currentUser['designation_bn'] ?? $currentUser['adminship'] ?? 'Admin') ?>)
    </div>
</div>

<!-- Metrics Counters -->
<div class="admin-metrics-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin-bottom:24px;">
    <div class="metric-card metric-warning" style="background:#fff; border:1px solid #fde68a; border-radius:8px; padding:18px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <div style="font-size:0.85rem; font-weight:700; color:#b45309; text-transform:uppercase;">
            ⏳ <?= $isBn ? 'অপেক্ষমাণ পাণ্ডুলিপি' : 'Pending Submissions' ?>
        </div>
        <div style="font-size:2rem; font-weight:800; color:#92400e; margin-top:6px;">
            <?= e($isBn ? \App\Core\I18n::formatNumber((string)count($pendingBlogs)) : count($pendingBlogs)) ?>
        </div>
        <div style="font-size:0.78rem; color:#78350f; margin-top:4px;">
            <?= $isBn ? 'অনুমোদনের অপেক্ষায় রয়েছে' : 'Awaiting editorial review' ?>
        </div>
    </div>

    <div class="metric-card metric-success" style="background:#fff; border:1px solid #bbf7d0; border-radius:8px; padding:18px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <div style="font-size:0.85rem; font-weight:700; color:#15803d; text-transform:uppercase;">
            ✓ <?= $isBn ? 'প্রকাশিত ও লাইভ ব্লগ' : 'Published Live Posts' ?>
        </div>
        <div style="font-size:2rem; font-weight:800; color:#166534; margin-top:6px;">
            <?= e($isBn ? \App\Core\I18n::formatNumber((string)count($publishedBlogs)) : count($publishedBlogs)) ?>
        </div>
        <div style="font-size:0.78rem; color:#14532d; margin-top:4px;">
            <?= $isBn ? 'ওয়েবসাইটে পাঠকদের জন্য উন্মুক্ত' : 'Publicly accessible to readers' ?>
        </div>
    </div>

    <div class="metric-card metric-danger" style="background:#fff; border:1px solid #fecaca; border-radius:8px; padding:18px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <div style="font-size:0.85rem; font-weight:700; color:#b91c1c; text-transform:uppercase;">
            ✕ <?= $isBn ? 'বাতিলকৃত ব্লগ' : 'Rejected Manuscripts' ?>
        </div>
        <div style="font-size:2rem; font-weight:800; color:#991b1b; margin-top:6px;">
            <?= e($isBn ? \App\Core\I18n::formatNumber((string)count($rejectedBlogs)) : count($rejectedBlogs)) ?>
        </div>
        <div style="font-size:0.78rem; color:#7f1d1d; margin-top:4px;">
            <?= $isBn ? 'সংশোধন প্রয়োজন বা নীতিমালা বহির্ভূত' : 'Did not meet editorial standard' ?>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="moderation-tabs-nav">
    <button type="button" class="tab-btn active" id="tabPendingBtn" onclick="switchModTab('pending')">
        <span>⏳</span>
        <span><?= $isBn ? 'অপেক্ষমাণ ব্লগ' : 'Pending Review' ?></span>
        <span class="badge-count"><?= count($pendingBlogs) ?></span>
    </button>
    <button type="button" class="tab-btn" id="tabPublishedBtn" onclick="switchModTab('published')">
        <span>✓</span>
        <span><?= $isBn ? 'প্রকাশিত ব্লগ' : 'Published' ?></span>
        <span class="badge-count"><?= count($publishedBlogs) ?></span>
    </button>
    <button type="button" class="tab-btn" id="tabRejectedBtn" onclick="switchModTab('rejected')">
        <span>✕</span>
        <span><?= $isBn ? 'বাতিলকৃত' : 'Rejected' ?></span>
        <span class="badge-count"><?= count($rejectedBlogs) ?></span>
    </button>
</div>

<!-- TAB 1: PENDING SUBMISSIONS -->
<div class="moderation-tab-pane" id="panePending">
    <?php if (empty($pendingBlogs)): ?>
        <div class="empty-pane-box">
            <span class="empty-pane-icon">🎉</span>
            <h4><?= $isBn ? 'বর্তমানে কোনো অপেক্ষমাণ ব্লগ নেই' : 'No Pending Blog Submissions' ?></h4>
            <p><?= $isBn ? 'সকল সদস্যের প্রেরিত পাণ্ডুলিপি পর্যালোচনা সম্পন্ন হয়েছে।' : 'All member submissions have been reviewed and processed.' ?></p>
        </div>
    <?php else: ?>
        <div class="pending-blogs-list">
            <?php foreach ($pendingBlogs as $b): 
                $author = $b['author'] ?? [];
            ?>
                <div class="moderation-blog-card" id="mod-card-<?= e($b['id']) ?>">
                    <div class="mod-card-header">
                        <div class="mod-post-badge-row">
                            <span class="status-pill status-pending">⏳ <?= $isBn ? 'অপেক্ষমাণ' : 'Pending' ?></span>
                            <span class="cat-pill"><?= e($b['category_bn'] ?? ucfirst($b['category'])) ?></span>
                            <span class="date-text">📅 <?= date('d M Y, h:i A', strtotime($b['created_at'])) ?></span>
                        </div>
                        <h3 class="mod-blog-title"><?= e($b['title_bn'] ?? $b['title_en']) ?></h3>
                        <?php if (!empty($b['title_en']) && ($b['title_bn'] ?? '') !== $b['title_en']): ?>
                            <div class="mod-blog-subtitle"><?= e($b['title_en']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mod-card-body">
                        <!-- Author Meta -->
                        <div class="mod-author-row">
                            <img src="<?= e($author['avatar'] ?? 'https://api.dicebear.com/7.x/bottts/svg?seed=author') ?>" 
                                 alt="<?= e($author['name_bn'] ?? '') ?>" class="mod-author-avatar">
                            <div>
                                <div class="mod-author-name">
                                    <strong><?= e($author['name_bn'] ?? $author['name_en'] ?? 'পেইড সদস্য') ?></strong>
                                    <span class="author-pill">★ <?= e($author['tier_bn'] ?? 'পেইড সদস্য') ?></span>
                                </div>
                                <div class="mod-author-email"><?= e($author['email'] ?? '') ?></div>
                            </div>
                        </div>

                        <!-- Featured Thumbnail & Excerpt -->
                        <div class="mod-content-preview">
                            <?php if (!empty($b['featured_image'])): ?>
                                <img src="<?= asset($b['featured_image']) ?>" alt="Cover" class="mod-thumb-img">
                            <?php endif; ?>
                            <div class="mod-excerpt-text">
                                <p><strong><?= $isBn ? 'সারসংক্ষেপ:' : 'Excerpt:' ?></strong> <?= e($b['excerpt_bn'] ?? $b['excerpt_en'] ?? '') ?></p>
                                
                                <details class="mod-full-details">
                                    <summary><?= $isBn ? '📖 সম্পূর্ণ পাণ্ডুলিপি পড়ুন (Expand Full Manuscript)' : '📖 Read Full Manuscript' ?></summary>
                                    <div class="full-content-body">
                                        <?= $b['content_bn'] ?? $b['content_en'] ?? '' ?>
                                    </div>
                                </details>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mod-card-actions">
                        <div class="action-left">
                            <a href="<?= url('/blog/' . $b['slug'], $currentLocale) ?>" target="_blank" class="btn btn-sm btn-ghost" style="border:1px solid #d4c4ab;">
                                👁️ <?= $isBn ? 'প্রিভিউ দেখুন' : 'Preview' ?>
                            </a>
                        </div>
                        <div class="action-right">
                            <!-- Approve Form -->
                            <form action="<?= url('/admin/blogs/approve/' . $b['id'], $currentLocale) ?>" method="POST" style="margin:0;">
                                <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>
                                <button type="submit" class="btn btn-sm btn-approve" id="approveBtn-<?= e($b['id']) ?>" 
                                        onclick="return confirm('<?= $isBn ? "আপনি কি নিশ্চিত যে এই ব্লগটি অনুমোদন ও সবার জন্য লাইভ প্রকাশ করতে চান?" : "Are you sure you want to approve and publish this blog post?" ?>');">
                                    <span>✓</span>
                                    <span><?= $isBn ? 'অনুমোদন ও প্রকাশ' : 'Approve & Publish' ?></span>
                                </button>
                            </form>

                            <!-- Reject Button opening Modal -->
                            <button type="button" class="btn btn-sm btn-reject" onclick="openRejectModal('<?= e($b['id']) ?>', '<?= e(addslashes($b['title_bn'])) ?>')">
                                <span>✕</span>
                                <span><?= $isBn ? 'বাতিল করুন' : 'Reject' ?></span>
                            </button>

                            <!-- Delete Form -->
                            <form action="<?= url('/admin/blogs/delete/' . $b['id'], $currentLocale) ?>" method="POST" style="margin:0;">
                                <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>
                                <button type="submit" class="btn btn-sm btn-delete" 
                                        onclick="return confirm('<?= $isBn ? "আপনি কি স্থায়ীভাবে এই ব্লগটি মুছে ফেলতে চান?" : "Are you sure you want to permanently delete this blog post?" ?>');">
                                    <span>🗑️</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- TAB 2: PUBLISHED LIVE POSTS -->
<div class="moderation-tab-pane" id="panePublished" style="display:none;">
    <div class="published-table-wrap">
        <table class="table-admin" style="width:100%; border-collapse:collapse; background:#fff; border:1px solid #e2e8f0; border-radius:8px; overflow:hidden;">
            <thead>
                <tr style="background:#f8fafc; border-bottom:1px solid #cbd5e1; text-align:left; font-size:0.84rem;">
                    <th style="padding:12px 16px;"><?= $isBn ? 'শিরোনাম ও লেখক' : 'Title & Author' ?></th>
                    <th style="padding:12px 16px;"><?= $isBn ? 'বিভাগ' : 'Category' ?></th>
                    <th style="padding:12px 16px;"><?= $isBn ? 'প্রকাশের তারিখ' : 'Published At' ?></th>
                    <th style="padding:12px 16px;"><?= $isBn ? 'অনুমোদনকারী' : 'Approved By' ?></th>
                    <th style="padding:12px 16px;"><?= $isBn ? 'লাইক ও ভিউ' : 'Reactions' ?></th>
                    <th style="padding:12px 16px; text-align:right;"><?= $isBn ? 'একশন' : 'Actions' ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($publishedBlogs as $pb): 
                    $pAuthor = $pb['author'] ?? [];
                ?>
                    <tr style="border-bottom:1px solid #f1f5f9; font-size:0.88rem;">
                        <td style="padding:12px 16px;">
                            <strong>
                                <a href="<?= url('/blog/' . $pb['slug'], $currentLocale) ?>" target="_blank" style="color:#0f172a; text-decoration:none;">
                                    <?= e($pb['title_bn'] ?? $pb['title_en']) ?> ↗
                                </a>
                            </strong>
                            <div style="font-size:0.78rem; color:#64748b; margin-top:2px;">
                                ✍️ <?= e($pAuthor['name_bn'] ?? $pAuthor['name_en'] ?? '') ?> 
                                (<?= e($pAuthor['tier_bn'] ?? 'পেইড সদস্য') ?>)
                            </div>
                        </td>
                        <td style="padding:12px 16px;">
                            <span class="cat-pill"><?= e($pb['category_bn'] ?? ucfirst($pb['category'])) ?></span>
                        </td>
                        <td style="padding:12px 16px; font-size:0.82rem; color:#475569;">
                            <?= date('d M Y', strtotime($pb['published_at'] ?? $pb['created_at'])) ?>
                        </td>
                        <td style="padding:12px 16px; font-size:0.82rem; color:#059669; font-weight:600;">
                            ✓ <?= e($pb['approved_by'] ?? 'এডমিন') ?>
                        </td>
                        <td style="padding:12px 16px; font-size:0.82rem; color:#475569;">
                            ❤️ <?= e($pb['likes_count'] ?? 0) ?> • 👁️ <?= e($pb['views_count'] ?? 0) ?>
                        </td>
                        <td style="padding:12px 16px; text-align:right;">
                            <form action="<?= url('/admin/blogs/delete/' . $pb['id'], $currentLocale) ?>" method="POST" style="display:inline;">
                                <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>
                                <button type="submit" class="btn btn-sm btn-delete" 
                                        onclick="return confirm('<?= $isBn ? "আপনি কি নিশ্চিত যে এই প্রকাশিত ব্লগটি মুছে ফেলতে চান?" : "Are you sure you want to remove this published blog?" ?>');">
                                    🗑️
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- TAB 3: REJECTED MANUSCRIPTS -->
<div class="moderation-tab-pane" id="paneRejected" style="display:none;">
    <?php if (empty($rejectedBlogs)): ?>
        <div class="empty-pane-box">
            <span class="empty-pane-icon">✓</span>
            <h4><?= $isBn ? 'কোনো বাতিলকৃত ব্লগ নেই' : 'No Rejected Submissions' ?></h4>
        </div>
    <?php else: ?>
        <div class="pending-blogs-list">
            <?php foreach ($rejectedBlogs as $rb): 
                $rAuthor = $rb['author'] ?? [];
            ?>
                <div class="moderation-blog-card" style="border-left:4px solid #ef4444;">
                    <div class="mod-card-header">
                        <div class="mod-post-badge-row">
                            <span class="status-pill status-rejected">✕ <?= $isBn ? 'বাতিলকৃত' : 'Rejected' ?></span>
                            <span class="cat-pill"><?= e($rb['category_bn'] ?? ucfirst($rb['category'])) ?></span>
                            <span class="date-text">📅 <?= date('d M Y, h:i A', strtotime($rb['created_at'])) ?></span>
                        </div>
                        <h3 class="mod-blog-title"><?= e($rb['title_bn'] ?? $rb['title_en']) ?></h3>
                        <div style="font-size:0.84rem; color:#64748b; margin-top:4px;">
                            লেখক: <strong><?= e($rAuthor['name_bn'] ?? '') ?></strong> (<?= e($rAuthor['email'] ?? '') ?>)
                        </div>
                    </div>

                    <div class="mod-card-body">
                        <div class="rejection-reason-box">
                            <strong><?= $isBn ? 'বাতিলের কারণ / সম্পাদকীয় মতামত:' : 'Rejection Reason / Feedback:' ?></strong>
                            <p><?= e($rb['rejection_reason'] ?? 'সম্পাদকীয় নীতিমালার সাথে সামঞ্জস্যপূর্ণ নয়।') ?></p>
                        </div>
                    </div>

                    <div class="mod-card-actions">
                        <form action="<?= url('/admin/blogs/approve/' . $rb['id'], $currentLocale) ?>" method="POST" style="margin:0;">
                            <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>
                            <button type="submit" class="btn btn-sm btn-approve" onclick="return confirm('পুনরায় অনুমোদন করতে চান?');">
                                <span>↺</span>
                                <span><?= $isBn ? 'পুনরায় বিবেচনা ও অনুমোদন' : 'Reconsider & Approve' ?></span>
                            </button>
                        </form>

                        <form action="<?= url('/admin/blogs/delete/' . $rb['id'], $currentLocale) ?>" method="POST" style="margin:0;">
                            <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>
                            <button type="submit" class="btn btn-sm btn-delete" onclick="return confirm('স্থায়ীভাবে মুছে ফেলতে চান?');">
                                🗑️ <?= $isBn ? 'স্থায়ীভাবে মুছুন' : 'Delete Permanently' ?>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Rejection Modal -->
<div id="rejectModal" class="admin-modal" style="display:none;">
    <div class="admin-modal-backdrop" onclick="closeRejectModal()"></div>
    <div class="admin-modal-dialog">
        <form action="" method="POST" id="rejectForm">
            <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>
            <div class="modal-header">
                <h4 style="margin:0; font-size:1.1rem; color:#991b1b;">
                    ✕ <?= $isBn ? 'ব্লগ পাণ্ডুলিপি বাতিল ও মতামত প্রদান' : 'Reject Blog Manuscript' ?>
                </h4>
                <button type="button" class="btn-close" onclick="closeRejectModal()">✕</button>
            </div>
            <div class="modal-body" style="padding:20px;">
                <p style="font-size:0.88rem; color:#475569; margin-top:0;">
                    <?= $isBn ? 'নির্বাচিত ব্লগ:' : 'Selected Blog:' ?> <strong id="modalBlogTitle"></strong>
                </p>
                <div class="form-group">
                    <label style="display:block; font-weight:700; font-size:0.85rem; margin-bottom:6px;">
                        <?= $isBn ? 'বাতিলের কারণ বা পরিবর্তন অনুরোধ (লেখকের জন্য)*' : 'Reason for Rejection or Edit Request*' ?>
                    </label>
                    <textarea name="reason" rows="4" required class="form-control" 
                              placeholder="<?= $isBn ? 'যেমন: পাণ্ডুলিপিতে শাস্ত্রীয় প্রমাণের ঘাটতি রয়েছে অথবা ভাষা পরিমার্জন প্রয়োজন...' : 'Explain why this submission was rejected or what changes are required...' ?>" 
                              style="width:100%; border:1px solid #cbd5e1; border-radius:6px; padding:10px; font-family:inherit;"></textarea>
                </div>
            </div>
            <div class="modal-footer" style="padding:14px 20px; background:#f8fafc; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-secondary" onclick="closeRejectModal()"><?= $isBn ? 'বাতিল' : 'Cancel' ?></button>
                <button type="submit" class="btn btn-reject">
                    <?= $isBn ? 'নিশ্চিত বাতিল করুন' : 'Confirm Rejection' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function switchModTab(tabKey) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.moderation-tab-pane').forEach(p => p.style.display = 'none');

    if (tabKey === 'pending') {
        document.getElementById('tabPendingBtn').classList.add('active');
        document.getElementById('panePending').style.display = 'block';
    } else if (tabKey === 'published') {
        document.getElementById('tabPublishedBtn').classList.add('active');
        document.getElementById('panePublished').style.display = 'block';
    } else if (tabKey === 'rejected') {
        document.getElementById('tabRejectedBtn').classList.add('active');
        document.getElementById('paneRejected').style.display = 'block';
    }
}

function openRejectModal(blogId, blogTitle) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    const titleElem = document.getElementById('modalBlogTitle');
    
    form.action = '<?= url("/admin/blogs/reject/", $currentLocale) ?>' + blogId;
    titleElem.textContent = blogTitle;
    modal.style.display = 'flex';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}

// Admin Manuscript Reader Anti-Copy & Anti-Screenshot Handlers
document.addEventListener('contextmenu', function(e) {
    if (e.target.closest('.full-content-body, .mod-card, .sps-protected-content')) {
        e.preventDefault();
        alert('<?= $isBn ? "⚠️ কপিরাইট সুরক্ষা: এই পাণ্ডুলিপির উপাদান কপি করা সম্পূর্ণ নিষিদ্ধ।" : "⚠️ Copyright Protected: Manuscript content copying is disabled." ?>');
    }
});

document.addEventListener('copy', function(e) {
    if (e.target.closest('.full-content-body, .mod-card, .sps-protected-content')) {
        e.preventDefault();
        if (e.clipboardData) e.clipboardData.setData('text/plain', '⚠️ SPS Protected Manuscript - Copying Prohibited.');
    }
});

window.addEventListener('keyup', function(e) {
    if (e.key === 'PrintScreen' || e.keyCode === 44) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText('⚠️ SPS Protected Literature - Screenshot Prohibited.');
        }
    }
});
</script>

<style>
.moderation-auth-banner {
    display: flex;
    align-items: center;
    gap: 14px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    padding: 12px 18px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}
.auth-icon {
    font-size: 1.5rem;
}
.auth-info {
    flex: 1;
    font-size: 0.86rem;
    color: #166534;
}
.auth-user-pill {
    background: #dcfce7;
    border: 1px solid #86efac;
    color: #14532d;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
}

.moderation-tabs-nav {
    display: flex;
    gap: 8px;
    border-bottom: 2px solid #e2e8f0;
    margin-bottom: 20px;
}
.tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: none;
    border: none;
    padding: 10px 18px;
    font-size: 0.92rem;
    font-weight: 700;
    color: #64748b;
    cursor: pointer;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    transition: all 0.15s ease;
}
.tab-btn.active {
    color: #b45309;
    border-bottom-color: #b45309;
}
.badge-count {
    background: #e2e8f0;
    color: #334155;
    font-size: 0.72rem;
    padding: 2px 7px;
    border-radius: 10px;
}
.tab-btn.active .badge-count {
    background: #fef3c7;
    color: #92400e;
}

.empty-pane-box {
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    padding: 40px;
    text-align: center;
    color: #64748b;
}
.empty-pane-icon {
    font-size: 2.2rem;
    display: block;
    margin-bottom: 10px;
}

/* Card view for moderation */
.pending-blogs-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}
.moderation-blog-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 22px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
}
.mod-card-header {
    margin-bottom: 16px;
}
.mod-post-badge-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    flex-wrap: wrap;
}
.status-pill {
    font-size: 0.75rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 4px;
}
.status-pending {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
}
.status-rejected {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}
.cat-pill {
    background: #f1f5f9;
    color: #334155;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 4px;
}
.date-text {
    font-size: 0.78rem;
    color: #64748b;
}
.mod-blog-title {
    font-size: 1.3rem;
    font-weight: 800;
    color: #0f172a;
    margin: 4px 0 2px;
}
.mod-blog-subtitle {
    font-size: 0.9rem;
    color: #64748b;
}

.mod-author-row {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    padding: 10px 14px;
    border-radius: 6px;
    margin-bottom: 16px;
}
.mod-author-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1px solid #cbd5e1;
}
.mod-author-name strong {
    color: #0f172a;
    font-size: 0.88rem;
}
.author-pill {
    background: #fef3c7;
    color: #92400e;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 8px;
    margin-left: 6px;
}
.mod-author-email {
    font-size: 0.78rem;
    color: #64748b;
}

.mod-content-preview {
    display: flex;
    gap: 16px;
    margin-bottom: 18px;
}
.mod-thumb-img {
    width: 140px;
    height: 100px;
    border-radius: 6px;
    object-fit: cover;
    border: 1px solid #e2e8f0;
    flex-shrink: 0;
}
.mod-excerpt-text {
    flex: 1;
    font-size: 0.9rem;
    color: #334155;
    line-height: 1.55;
}
.mod-full-details {
    margin-top: 10px;
    background: #fdfaf4;
    border: 1px dashed #d5c8b5;
    padding: 10px 14px;
    border-radius: 6px;
}
.mod-full-details summary {
    font-weight: 700;
    color: #b45309;
    cursor: pointer;
    font-size: 0.85rem;
}
.full-content-body {
    margin-top: 12px;
    padding-top: 10px;
    border-top: 1px solid #ede3d4;
    font-size: 0.92rem;
    line-height: 1.65;
    max-height: 300px;
    overflow-y: auto;
}

.mod-card-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 14px;
    border-top: 1px solid #f1f5f9;
    flex-wrap: wrap;
    gap: 10px;
}
.action-right {
    display: flex;
    align-items: center;
    gap: 8px;
}
.btn-approve {
    background: #10b981;
    color: #fff;
    border: none;
    font-weight: 700;
    padding: 6px 14px;
    border-radius: 6px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-approve:hover {
    background: #059669;
}
.btn-reject {
    background: #fee2e2;
    color: #b91c1c;
    border: 1px solid #fca5a5;
    font-weight: 700;
    padding: 6px 14px;
    border-radius: 6px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-reject:hover {
    background: #fecaca;
}
.btn-delete {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #64748b;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
}
.btn-delete:hover {
    background: #fee2e2;
    color: #ef4444;
}

.rejection-reason-box {
    background: #fef2f2;
    border: 1px solid #fecaca;
    padding: 12px 16px;
    border-radius: 6px;
    color: #991b1b;
    font-size: 0.88rem;
    margin-bottom: 12px;
}
.rejection-reason-box p {
    margin: 4px 0 0;
    line-height: 1.5;
}

/* Modal */
.admin-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}
.admin-modal-backdrop {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5);
}
.admin-modal-dialog {
    position: relative;
    background: #fff;
    width: 90%;
    max-width: 520px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.25);
    z-index: 1001;
}
.modal-header {
    padding: 16px 20px;
    background: #fef2f2;
    border-bottom: 1px solid #fee2e2;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.btn-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    color: #64748b;
}
</style>
