<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
?>

<div class="container" style="padding-top:var(--space-2xl); padding-bottom:var(--space-4xl);">
    <!-- Breadcrumb & Role Bar -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:var(--space-sm); margin-bottom:var(--space-xl); border-bottom:1px solid var(--border-subtle); padding-bottom:var(--space-md);">
        <div class="breadcrumb" style="margin-bottom:0;">
            <a href="<?= e(url('/', $currentLocale)) ?>"><?= e(__('common.nav.home')) ?></a>
            <span class="breadcrumb-separator">/</span>
            <a href="<?= e(url('/library', $currentLocale)) ?>"><?= e(__('common.nav.library')) ?></a>
            <span class="breadcrumb-separator">/</span>
            <span style="color:var(--text-main); font-weight:600;"><?= e($isBn ? $book['title_bn'] : $book['title_en']) ?></span>
        </div>

        <!-- Role Simulator Toolbar -->
        <div style="display:flex; align-items:center; gap:var(--space-xs); background:var(--bg-subtle); padding:4px 8px; border-radius:var(--radius-sm); border:1px solid var(--border-medium); font-size:0.82rem;">
            <span style="font-weight:600; color:var(--text-muted); margin-right:4px;">
                <?= $isBn ? 'মোড প্রিভিউ:' : 'View as:' ?>
            </span>
            <a href="<?= e(url('/library/role?role=viewer', $currentLocale)) ?>" 
               class="btn btn-sm <?= $currentRole === 'viewer' ? 'btn-primary' : 'btn-ghost' ?>" 
               style="padding:3px 8px; font-size:0.78rem;">
                👤 <?= $isBn ? 'সাধারণ দর্শক' : 'Viewer' ?>
            </a>
            <a href="<?= e(url('/library/role?role=paid_member', $currentLocale)) ?>" 
               class="btn btn-sm <?= $currentRole === 'paid_member' ? 'btn-primary' : 'btn-ghost' ?>" 
               style="padding:3px 8px; font-size:0.78rem;">
                ⭐ <?= $isBn ? 'পেইড সদস্য' : 'Paid Member' ?>
            </a>
            <a href="<?= e(url('/library/role?role=admin', $currentLocale)) ?>" 
               class="btn btn-sm <?= $currentRole === 'admin' ? 'btn-primary' : 'btn-ghost' ?>" 
               style="padding:3px 8px; font-size:0.78rem;">
                🛡️ <?= $isBn ? 'প্রশাসক' : 'Admin' ?>
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if ($msg = \App\Core\Session::getFlash('success')): ?>
        <div class="alert alert-success" style="margin-bottom:var(--space-xl);">
            <strong>✓ <?= $isBn ? 'সফল হয়েছে:' : 'Success:' ?></strong> <?= e($msg) ?>
        </div>
    <?php endif; ?>

    <?php if ($msg = \App\Core\Session::getFlash('warning')): ?>
        <div class="alert alert-warning" style="margin-bottom:var(--space-xl);">
            <strong>▲ <?= $isBn ? 'লক্ষ্য করুন:' : 'Notice:' ?></strong> <?= e($msg) ?>
        </div>
    <?php endif; ?>

    <?php if ($msg = \App\Core\Session::getFlash('error')): ?>
        <div class="alert alert-danger" style="margin-bottom:var(--space-xl);">
            <strong>✕ <?= $isBn ? 'ত্রুটি:' : 'Error:' ?></strong> <?= e($msg) ?>
        </div>
    <?php endif; ?>

    <!-- Main Detail Layout -->
    <div style="display:grid; grid-template-columns:360px 1fr; gap:var(--space-3xl); align-items:start;">
        <!-- Left Column: Cover & Primary Action -->
        <div>
            <div style="background:#EDE7DF; border-radius:var(--radius-sm); border:1px solid var(--border-medium); padding:var(--space-xl); display:flex; justify-content:center; align-items:center; margin-bottom:var(--space-lg); box-shadow:var(--shadow-sm);">
                <img src="<?= asset($book['cover_image']) ?>" 
                     alt="<?= e($isBn ? $book['title_bn'] : $book['title_en']) ?>"
                     style="max-width:100%; max-height:460px; object-fit:contain; border-radius:2px; box-shadow:-6px 8px 24px rgba(0,0,0,0.25), 0 0 0 1px rgba(0,0,0,0.1);">
            </div>

            <!-- Reading Action Box -->
            <div class="card" style="margin-bottom:var(--space-lg); border-color:var(--border-medium);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-sm);">
                    <span style="font-size:0.82rem; font-weight:600; text-transform:uppercase; color:var(--text-muted);">
                        <?= $isBn ? 'পাঠাধিকার স্থিতি' : 'Access Status' ?>
                    </span>
                    <?php if ($hasAccess): ?>
                        <span class="badge badge-success">✓ <?= $isBn ? 'উন্মুক্ত' : 'Authorized' ?></span>
                    <?php else: ?>
                        <span class="badge" style="background:#5C4334; color:#FFFFFF;">🔒 <?= $isBn ? 'সীমাবদ্ধ' : 'Restricted' ?></span>
                    <?php endif; ?>
                </div>

                <?php if ($hasAccess): ?>
                    <p style="font-size:0.88rem; color:var(--text-body); line-height:1.6; margin-bottom:var(--space-md);">
                        <?= $isBn 
                            ? 'আপনার অ্যাকাউন্ট থেকে এই গ্রন্থটি অনলাইনে পাঠ করার পূর্ণ অনুমতি রয়েছে।' 
                            : 'Your account is authorized to launch and read this full edition in the browser reader.' ?>
                    </p>
                    <a href="<?= e(url('/library/reader/' . $book['slug'], $currentLocale)) ?>" class="btn btn-primary btn-lg" style="width:100%; justify-content:center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="margin-right:8px;">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                        <span><?= $isBn ? 'ই-বুক পড়ুন (অনলাইন রিডার)' : 'Launch E-Book Reader' ?></span>
                    </a>
                <?php else: ?>
                    <p style="font-size:0.88rem; color:var(--text-body); line-height:1.6; margin-bottom:var(--space-md);">
                        <?= $isBn 
                            ? 'গ্রন্থটি সম্পূর্ণ পড়তে সক্রিয় পেইড সাধারণ সদস্যপদ প্রয়োজন। আপনি চাইলে নিচে প্রশাসক বরাবরে গ্রন্থটি অধ্যয়নের কারণ উল্লেখ করে পাঠাধিকারের অনুরোধ জানাতে পারেন।' 
                            : 'Reading access is reserved for active paid general members. You may submit an access request below for administrator approval.' ?>
                    </p>
                    <a href="#request-access" class="btn btn-secondary" style="width:100%; justify-content:center; border-color:var(--accent-saffron); color:var(--accent-saffron);">
                        <span><?= $isBn ? 'পাঠের অনুরোধ জানান ↓' : 'Request Access Below ↓' ?></span>
                    </a>
                <?php endif; ?>

                <div style="font-size:0.78rem; color:var(--text-muted); text-align:center; margin-top:var(--space-sm); border-top:1px solid var(--border-subtle); padding-top:var(--space-xs);">
                    🛡️ <?= $isBn ? 'কপিরাইট সুরক্ষিত • ডাউনলোড সম্পূর্ণ নিষিদ্ধ' : 'Protected Canvas Stream • Downloads Disabled' ?>
                </div>
            </div>

            <!-- Technical Attributes Card -->
            <div class="card" style="background:var(--bg-subtle); border-color:var(--border-subtle); font-size:0.86rem;">
                <h4 style="font-size:0.95rem; margin-bottom:var(--space-sm); border-bottom:1px solid var(--border-medium); padding-bottom:var(--space-2xs);">
                    <?= $isBn ? 'কারিগরি বিবরণ' : 'Technical Specifications' ?>
                </h4>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-muted);"><?= $isBn ? 'মূল ফাইল:' : 'File:' ?></span>
                        <code style="font-size:0.8rem; background:rgba(0,0,0,0.04); padding:1px 4px;"><?= e($book['original_filename']) ?></code>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-muted);"><?= $isBn ? 'ফাইল সাইজ:' : 'File Size:' ?></span>
                        <strong><?= e($book['file_size']) ?></strong>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-muted);"><?= $isBn ? 'পৃষ্ঠা সংখ্যা:' : 'Total Pages:' ?></span>
                        <strong><?= e($book['pages_count']) ?></strong>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-muted);"><?= $isBn ? 'প্রকাশকাল:' : 'Year:' ?></span>
                        <strong><?= e($book['publication_year']) ?></strong>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-muted);"><?= $isBn ? 'অ্যাক্সেস স্তর:' : 'Access Tier:' ?></span>
                        <span style="color:var(--accent-brown); font-weight:600;"><?= $isBn ? 'পেইড সদস্য বা অনুমোদিত পাঠক' : 'Paid Members / Approved' ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Content, Synopsis, and Access Form -->
        <div>
            <!-- Header Metadata -->
            <div style="margin-bottom:var(--space-xl);">
                <div style="display:flex; gap:var(--space-xs); align-items:center; margin-bottom:var(--space-xs);">
                    <span class="badge badge-scholarly"><?= e($isBn ? $book['category_bn'] : $book['category_en']) ?></span>
                    <span class="badge" style="background:var(--bg-subtle); color:var(--text-muted); border:1px solid var(--border-medium);">
                        <?= e($book['publication_year']) ?>
                    </span>
                </div>

                <h1 style="font-size:clamp(1.8rem, 3.2vw, 2.6rem); margin-bottom:var(--space-xs); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;">
                    <?= e($isBn ? $book['title_bn'] : $book['title_en']) ?>
                </h1>

                <div style="font-size:1.05rem; color:var(--accent-saffron); font-weight:600; margin-bottom:var(--space-md);">
                    <?= e($isBn ? $book['author_bn'] : $book['author_en']) ?>
                </div>

                <div class="editorial-quote" style="margin:var(--space-md) 0 var(--space-xl); background:var(--bg-canvas); border-left-color:var(--accent-saffron);">
                    <p style="font-size:1.08rem; line-height:1.75; font-style:normal; margin-bottom:0;">
                        <?= e($isBn ? $book['synopsis_bn'] : $book['synopsis_en']) ?>
                    </p>
                </div>
            </div>

            <!-- Discussion Themes & Philosophical Scope -->
            <div style="margin-bottom:var(--space-2xl);">
                <h3 style="font-size:1.2rem; margin-bottom:var(--space-sm); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;">
                    <?= $isBn ? 'প্রধান আলোচ্য বিষয় ও সূচি সংক্ষেপ' : 'Key Curated Topics & Highlights' ?>
                </h3>
                <div style="display:flex; flex-wrap:wrap; gap:var(--space-xs); margin-bottom:var(--space-lg);">
                    <?php foreach ($book['topics'] as $topic): ?>
                        <span class="badge badge-gold" style="font-size:0.86rem; padding:6px 12px;">
                            # <?= e($topic) ?>
                        </span>
                    <?php endforeach; ?>
                </div>

                <p style="font-size:0.95rem; line-height:1.7; color:var(--text-body);">
                    <?= $isBn 
                        ? 'এই প্রকাশনাটি সনাতন দর্শন ও শাস্ত্র প্রতিষ্ঠানের ডিজিটাল সংরক্ষণ কর্মসূচির অংশ হিসেবে অন্তর্ভুক্ত করা হয়েছে। মূল মুদ্রণ থেকে উচ্চমানের রেজল্যুশনে ডিজিটাল আর্কাইভে সাজানো হয়েছে যাতে গবেষক ও আগ্রহী পাঠকগণ মূল তথ্যসূত্রের প্রত্যক্ষ নির্যাস গ্রহণ করতে পারেন।' 
                        : 'This volume is preserved under the SPS Archival Digitization Initiative. Direct facsimile scans ensure scholarly authenticity, allowing researchers to study source materials directly.' ?>
                </p>
            </div>

            <!-- Access Request Form Section -->
            <div id="request-access" style="margin-top:var(--space-3xl); padding-top:var(--space-2xl); border-top:2px solid var(--border-subtle);">
                <span class="section-tag"><?= $isBn ? 'পাঠাধিকার আবেদন' : 'Academic Access Gateway' ?></span>
                <h3 style="font-size:1.35rem; margin-bottom:var(--space-xs); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;">
                    <?= $isBn ? 'অনলাইন ই-বুক পড়ার অনুরোধ' : 'Request Scholar / Viewer Reading Access' ?>
                </h3>
                <p style="font-size:0.92rem; color:var(--text-muted); line-height:1.65; margin-bottom:var(--space-lg);">
                    <?= $isBn 
                        ? 'আপনি যদি এসপিএস-এর পেইড সদস্য না হয়ে থাকেন, তবে গবেষণা বা জ্ঞানানুশীলনের স্বার্থে নির্দিষ্ট এই গ্রন্থটি পড়ার জন্য আবেদন করতে পারেন। প্রশাসক আপনার আবেদন পর্যালোচনা করে পাঠের অনুমতি প্রদান করবেন।' 
                        : 'If you are not an active paid general member, you may submit this form for academic review. The administrator will grant access based on scholarly merit.' ?>
                </p>

                <?php if ($pendingRequest): ?>
                    <div class="card" style="background:var(--bg-subtle); border-left:4px solid var(--status-warning);">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-xs);">
                            <strong><?= $isBn ? 'আপনার পূর্ববর্তী আবেদনের স্থিতি:' : 'Your Active Request Status:' ?></strong>
                            <span class="badge <?= $pendingRequest['status'] === 'approved' ? 'badge-success' : 'badge-warning' ?>">
                                <?= strtoupper($pendingRequest['status']) ?>
                            </span>
                        </div>
                        <p style="font-size:0.88rem; color:var(--text-body); margin-bottom:var(--space-xs);">
                            <?= $isBn ? 'আবেদনের তারিখ:' : 'Date:' ?> <?= e($pendingRequest['created_at']) ?> • 
                            <?= $isBn ? 'নাম:' : 'Name:' ?> <?= e($pendingRequest['user_name']) ?> (<?= e($pendingRequest['user_email']) ?>)
                        </p>
                        <?php if (!empty($pendingRequest['admin_note'])): ?>
                            <div style="background:#FFF; padding:8px 12px; border-radius:var(--radius-xs); border:1px solid var(--border-subtle); font-size:0.86rem; color:var(--accent-brown); margin-top:8px;">
                                <strong><?= $isBn ? 'প্রশাসকের মন্তব্য:' : 'Admin Note:' ?></strong> <?= e($pendingRequest['admin_note']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <form action="<?= e(url('/library/request/' . $book['slug'], $currentLocale)) ?>" method="POST" class="card" style="background:var(--bg-surface); border-color:var(--border-medium);">
                        <?= csrf_field() ?>
                        
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-md); margin-bottom:var(--space-md);">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label"><?= $isBn ? 'আপনার পূর্ণ নাম' : 'Full Name' ?> *</label>
                                <input type="text" name="user_name" class="form-control" placeholder="<?= $isBn ? 'যেমন: শ্রীকান্ত রায়' : 'e.g. Anand Sharma' ?>" required>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label"><?= $isBn ? 'ইমেইল ঠিকানা' : 'Email Address' ?> *</label>
                                <input type="email" name="user_email" class="form-control" placeholder="reader@example.com" required>
                            </div>
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-md); margin-bottom:var(--space-md);">
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label"><?= $isBn ? 'ফোন নম্বর (ঐচ্ছিক)' : 'Phone (Optional)' ?></label>
                                <input type="text" name="user_phone" class="form-control" placeholder="+880 17...">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label"><?= $isBn ? 'শিক্ষা বা প্রাতিষ্ঠানিক পরিচয়' : 'Institution / Background' ?></label>
                                <input type="text" name="institution" class="form-control" placeholder="<?= $isBn ? 'যেমন: সনাতন বিদ্যার্থী / শিক্ষক' : 'e.g. University / Independent Researcher' ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?= $isBn ? 'এই গ্রন্থটি পড়ার প্রয়োজনীয়তা / উদ্দেশ্য' : 'Reason for Requesting Reading Access' ?> *</label>
                            <textarea name="reason" class="form-control" rows="3" placeholder="<?= $isBn ? 'আপনি কেন এই গ্রন্থটি অধ্যয়ন করতে চান সংক্ষেপে লিখুন...' : 'Briefly describe your scholarly or devotional interest in studying this volume...' ?>" required></textarea>
                        </div>

                        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:var(--space-md); margin-top:var(--space-sm);">
                            <div style="font-size:0.8rem; color:var(--text-muted);">
                                🔒 <?= $isBn ? 'তথ্যসমূহ কেবলমাত্র এসপিএস পাঠাধিকার অনুমোদনের কাজে ব্যবহৃত হবে।' : 'Information is strictly used for evaluating academic reading entitlement.' ?>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <?= $isBn ? 'পাঠাধিকারের আবেদন পাঠান' : 'Submit Access Request' ?> →
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
