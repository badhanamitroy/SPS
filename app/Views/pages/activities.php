<?php
/**
 * SPS Activities & Grassroots Seva Chronicle Page (2020 - 2026)
 * Backed by official SPS Notion Project Logs.
 */

$isBn = ($locale ?? 'bn') === 'bn';
?>

<div class="container" style="padding-top: var(--space-2xl); padding-bottom: var(--space-4xl);">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" style="margin-bottom: var(--space-lg);">
        <ol style="display:flex; list-style:none; padding:0; margin:0; font-size:0.875rem; color:var(--text-muted); gap:var(--space-xs); align-items:center;">
            <li><a href="<?= url('/', $locale) ?>" style="color:var(--text-muted); text-decoration:none;"><?= $isBn ? 'নীড়পাতা' : 'Home' ?></a></li>
            <li>/</li>
            <li style="color:var(--primary-deep); font-weight:600;"><?= $isBn ? 'কার্যক্রম ও সেবা মহাযজ্ঞ' : 'Activities & Grassroots Seva' ?></li>
        </ol>
    </nav>

    <!-- Hero Header -->
    <div style="background: linear-gradient(135deg, #1b263b 0%, #0d1b2a 100%); color: #ffffff; border-radius: var(--radius-xl); padding: var(--space-3xl) var(--space-xl); margin-bottom: var(--space-3xl); position: relative; overflow: hidden; box-shadow: var(--shadow-lg);">
        <!-- Subtle sacred background motif -->
        <div style="position: absolute; right: -40px; bottom: -40px; font-size: 16rem; opacity: 0.04; user-select: none; pointer-events: none;">
            🪷
        </div>

        <div style="max-width: 820px; position: relative; z-index: 1;">
            <div style="display:inline-flex; align-items:center; gap:8px; background:rgba(230,81,0,0.2); border:1px solid rgba(254,215,170,0.3); padding:4px 14px; border-radius:var(--radius-full); margin-bottom:var(--space-md);">
                <span style="font-size:0.9rem;">🚩</span>
                <span style="font-size:0.8rem; font-weight:700; color:#fed7aa; text-transform:uppercase; letter-spacing:1px;">
                    <?= $isBn ? 'প্রামাণ্য সেবা ইতিবৃত্ত (২০২০ – ২০২৬)' : 'Verified Grassroots Chronicle (2020 – 2026)' ?>
                </span>
            </div>

            <h1 style="font-size: 2.35rem; font-weight: 800; line-height: 1.25; margin: 0 0 var(--space-sm); color: #ffffff;">
                <?= $isBn 
                    ? 'সেবাই সনাতন ধর্ম — তৃণমূল থেকে মহাতীর্থের সেবা মহাযজ্ঞ' 
                    : 'Sanatan Dharma in Action — Field Seva, Scriptures & Human Dignity' ?>
            </h1>

            <p style="font-size: 1.05rem; line-height: 1.65; color: #cbd5e1; margin: 0 0 var(--space-xl);">
                <?= $isBn 
                    ? 'সনাতন ফিলোসফি এন্ড স্ক্রিপচার (SPS)-এর মূল ভিত্তি কেবল তত্ত্বালোচনা নয়, বরং আর্তপীড়িতের পাশে দাঁড়ানো, দেবালয় সংস্কার, মেধা ও প্রযুক্তি বিকাশ, শিশু শিক্ষা এবং প্রকৃতির প্রতি বৈদিক দায়িত্ব পালন। নিচে আমাদের বিগত ৬ বছরের সকল কার্যক্রমের স্বচ্ছ ও প্রামাণ্য হিসাব তুলে ধরা হলো।' 
                    : 'SPS bridges philosophical study with transformative social seva across Bangladesh—restoring historic shrines, distributing children\'s scriptures, funding university scholars, creating livelihoods, and extending emergency disaster relief.' ?>
            </p>

            <div style="display:flex; flex-wrap:wrap; gap:var(--space-md); align-items:center;">
                <a href="#timeline-section" class="btn btn-primary" style="background:var(--accent-saffron); border-color:var(--accent-saffron); font-weight:700;">
                    <?= $isBn ? '📜 বছরভিত্তিক কার্যক্রম অনুসন্ধান করুন' : '📜 Explore Year-by-Year Logs' ?>
                </a>
                <a href="#flagship-section" class="btn btn-secondary" style="background:rgba(255,255,255,0.12); color:#ffffff; border-color:rgba(255,255,255,0.25);">
                    <?= $isBn ? '✨ আমাদের স্থায়ী প্রকল্পসমূহ' : '✨ Flagship Initiatives' ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Impact Counter Stats Bar -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: var(--space-md); margin-bottom: var(--space-3xl);">
        <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); padding:var(--space-lg); text-align:center; box-shadow:var(--shadow-xs);">
            <div style="font-size:2rem; font-weight:800; color:var(--primary-deep); line-height:1;"><?= $stats['years_active'] ?></div>
            <div style="font-size:0.8rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; margin-top:6px;">
                <?= $isBn ? 'বছরের অবিচল সেবা' : 'Years of Seva' ?>
            </div>
        </div>
        <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); padding:var(--space-lg); text-align:center; box-shadow:var(--shadow-xs);">
            <div style="font-size:2rem; font-weight:800; color:#b45309; line-height:1;"><?= $stats['districts_covered'] ?></div>
            <div style="font-size:0.8rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; margin-top:6px;">
                <?= $isBn ? 'জেলায় পদচারণা' : 'Districts Covered' ?>
            </div>
        </div>
        <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); padding:var(--space-lg); text-align:center; box-shadow:var(--shadow-xs);">
            <div style="font-size:2rem; font-weight:800; color:#047857; line-height:1;"><?= $stats['trees_planted'] ?></div>
            <div style="font-size:0.8rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; margin-top:6px;">
                <?= $isBn ? 'বৃক্ষরোপণ কর্মসূচি' : 'Trees Planted' ?>
            </div>
        </div>
        <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); padding:var(--space-lg); text-align:center; box-shadow:var(--shadow-xs);">
            <div style="font-size:2rem; font-weight:800; color:#1d4ed8; line-height:1;"><?= $stats['gita_reciters_served'] ?></div>
            <div style="font-size:0.8rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; margin-top:6px;">
                <?= $isBn ? 'কান্তজিউতে ভক্ত সেবা' : 'Pilgrims Served' ?>
            </div>
        </div>
        <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); padding:var(--space-lg); text-align:center; box-shadow:var(--shadow-xs);">
            <div style="font-size:2rem; font-weight:800; color:#7c3aed; line-height:1;"><?= $stats['scholarships_awarded'] ?></div>
            <div style="font-size:0.8rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; margin-top:6px;">
                <?= $isBn ? 'শীর্ষ বিশ্ববিদ্যালয় মেধা বৃত্তি' : 'Merit Scholarships' ?>
            </div>
        </div>
        <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); padding:var(--space-lg); text-align:center; box-shadow:var(--shadow-xs);">
            <div style="font-size:2rem; font-weight:800; color:#b91c1c; line-height:1;"><?= $stats['temples_assisted'] ?></div>
            <div style="font-size:0.8rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; margin-top:6px;">
                <?= $isBn ? 'মন্দির ও তীর্থ সংস্কার' : 'Temples Restored' ?>
            </div>
        </div>
    </div>

    <!-- Dual Role Orientation Banner: For Visitors vs. For Registered Members -->
    <div style="background: var(--bg-surface); border: 2px dashed var(--border-medium); border-radius: var(--radius-xl); padding: var(--space-xl); margin-bottom: var(--space-3xl);">
        <div style="display:flex; align-items:center; gap:var(--space-xs); margin-bottom:var(--space-sm);">
            <span style="font-size:1.3rem;">🧭</span>
            <h3 style="font-size:1.15rem; font-weight:800; color:var(--primary-deep); margin:0;">
                <?= $isBn ? 'পরিদর্শক ও সদস্যদের জন্য নির্দেশিকা (Community Guide)' : 'Orientation for General Visitors & Active Members' ?>
            </h3>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:var(--space-lg); margin-top:var(--space-md);">
            <!-- Box 1: For General Public / Non-Members -->
            <div style="background:#ffffff; border:1px solid var(--border-subtle); border-radius:var(--radius-md); padding:var(--space-lg); box-shadow:var(--shadow-xs);">
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:var(--space-xs);">
                    <span style="font-size:1.1rem; color:#1d4ed8;">🌐</span>
                    <h4 style="font-size:1rem; font-weight:700; color:#1e293b; margin:0;">
                        <?= $isBn ? 'আপনি যদি সাধারণ পরিদর্শক বা শুভানুধ্যায়ী হন:' : 'For General Visitors & Well-Wishers:' ?>
                    </h4>
                </div>
                <ul style="font-size:0.88rem; color:var(--text-secondary); line-height:1.6; padding-left:20px; margin:0 0 var(--space-md);">
                    <li><?= $isBn ? 'এসপিএস কোনো বাণিজ্যিক বা রাজনৈতিক তহবিল নেয় না; সকল কাজ সাধারণ সনাতনী ভাই-বোনদের স্বতঃস্ফূর্ত অংশগ্রহণে পরিচালিত।' : 'SPS accepts no corporate or partisan funds; all initiatives are community-driven and micro-funded.' ?></li>
                    <li><?= $isBn ? 'আপনার অঞ্চলে কোনো মন্দির নির্মাণ, শিশু গীতা শিক্ষা বা দুর্যোগের ক্ষেত্রে আমাদের সরাসরি অবহিত করতে পারেন।' : 'You can request field support, scripture distribution, or report local distress to our helpline.' ?></li>
                    <li><?= $isBn ? '"সনাতনী ১০ টাকা প্রজেক্ট"-এ অংশ নিয়ে আপনিও একজন প্রকৌশল বা বিশ্ববিদ্যালয়ের মেধাবী ছাত্রের পড়ার খরচ বহন করতে পারেন।' : 'Participate in the "Sanatani 10 Taka Project" to sponsor higher education for indigent students.' ?></li>
                </ul>
                <div style="display:flex; gap:var(--space-sm); flex-wrap:wrap;">
                    <a href="<?= url('/get-involved', $locale) ?>" class="btn btn-secondary" style="font-size:0.82rem; padding:6px 14px;">
                        <?= $isBn ? '🙋‍♂️ স্বেচ্ছাসেবক হিসেবে যুক্ত হোন' : '🙋‍♂️ Join as Volunteer' ?>
                    </a>
                    <a href="tel:+8801736360041" class="btn btn-outline" style="font-size:0.82rem; padding:6px 14px; border:1px solid var(--border-medium);">
                        <?= $isBn ? '📞 হটলাইনে যোগাযোগ করুন' : '📞 Call Helpline' ?>
                    </a>
                </div>
            </div>

            <!-- Box 2: For Registered Members & Units -->
            <div style="background:#ffffff; border:1px solid var(--border-subtle); border-radius:var(--radius-md); padding:var(--space-lg); box-shadow:var(--shadow-xs);">
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:var(--space-xs);">
                    <span style="font-size:1.1rem; color:#b45309;">🪷</span>
                    <h4 style="font-size:1rem; font-weight:700; color:#1e293b; margin:0;">
                        <?= $isBn ? 'এসপিএস সাধারণ সদস্য ও জেলা ইউনিটের জন্য:' : 'For Registered Members & District Units:' ?>
                    </h4>
                </div>
                <ul style="font-size:0.88rem; color:var(--text-secondary); line-height:1.6; padding-left:20px; margin:0 0 var(--space-md);">
                    <li><?= $isBn ? 'নিচের "লাইভ প্রজেক্ট ট্র্যাকিং" প্যানেলে চলতি ও পর্যালোচনাধীন প্রকল্পের অগ্রগতি সরাসরি পর্যবেক্ষণ করুন।' : 'Track live real-time execution status (In Progress, Done, Needs Review) via the tracker table below.' ?></li>
                    <li><?= $isBn ? 'আপনার জেলার স্থানীয় শাখার মাধ্যমে নতুন প্রজেক্ট প্রস্তাবনা ও ভেরিফিকেশন রিপোর্ট সাবমিট করুন।' : 'Submit local project proposals, emergency requests, or field verification through your regional wing.' ?></li>
                    <li><?= $isBn ? 'অনুমোদিত প্রকল্পের ভাউচার, অডিট লগ এবং স্বচ্ছ হিসাব সংরক্ষণ সর্বদা এসপিএস আর্থিক নীতিমালার বাধ্যবাধকতা।' : 'All disbursements adhere to strict Maker-Checker financial validation and public audit trails.' ?></li>
                </ul>
                <div style="display:flex; gap:var(--space-sm); flex-wrap:wrap;">
                    <a href="#tracking-section" class="btn btn-secondary" style="font-size:0.82rem; padding:6px 14px; background:#fef3c7; color:#92400e; border-color:#fde68a;">
                        <?= $isBn ? '📊 লাইভ প্রজেক্ট ট্র্যাকার দেখুন' : '📊 View Live Project Tracker' ?>
                    </a>
                    <a href="<?= url('/admin', $locale) ?>" class="btn btn-outline" style="font-size:0.82rem; padding:6px 14px; border:1px solid var(--border-medium);">
                        <?= $isBn ? '🔐 কার্যনির্বাহী পোর্টাল লগইন' : '🔐 Admin & Audit Portal' ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Flagship Initiatives Section -->
    <div id="flagship-section" style="margin-bottom: var(--space-4xl);">
        <div style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:var(--space-sm); margin-bottom:var(--space-xl); border-bottom:2px solid var(--border-subtle); padding-bottom:var(--space-sm);">
            <div>
                <span style="font-size:0.8rem; font-weight:700; color:var(--accent-saffron); text-transform:uppercase; letter-spacing:1px;">
                    <?= $isBn ? 'এসপিএস স্থায়ী সেবাকর্ম' : 'Pillars of Impact' ?>
                </span>
                <h2 style="font-size:1.85rem; font-weight:800; color:var(--primary-deep); margin:4px 0 0;">
                    <?= $isBn ? 'এসপিএস-এর স্থায়ী ও ধারাবাহিক প্রকল্পসমূহ' : 'Our Flagship & Recurring Initiatives' ?>
                </h2>
            </div>
            <div style="font-size:0.9rem; color:var(--text-muted);">
                <?= $isBn ? 'সারাদেশে নিরবচ্ছিন্নভাবে পরিচালিত সেবামূলক কার্যক্রম' : 'Continuous institutional programs across Bangladesh' ?>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(340px, 1fr)); gap:var(--space-lg);">
            <?php foreach ($flagships as $flag): ?>
                <div style="background:#ffffff; border:1px solid <?= $flag['border'] ?>; border-radius:var(--radius-lg); padding:var(--space-xl); box-shadow:var(--shadow-xs); display:flex; flex-direction:column; justify-content:space-between; transition:transform 0.2s ease, box-shadow 0.2s ease;">
                    <div>
                        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:var(--space-sm); margin-bottom:var(--space-md);">
                            <div style="width:48px; height:48px; border-radius:var(--radius-md); background:<?= $flag['bg'] ?>; border:1px solid <?= $flag['border'] ?>; display:flex; align-items:center; justify-content:center; font-size:1.6rem;">
                                <?= $flag['icon'] ?>
                            </div>
                            <span style="font-size:0.75rem; font-weight:700; color:<?= $flag['color'] ?>; background:<?= $flag['bg'] ?>; border:1px solid <?= $flag['border'] ?>; padding:3px 10px; border-radius:var(--radius-full);">
                                <?= $isBn ? 'স্থায়ী প্রকল্প' : 'Flagship' ?>
                            </span>
                        </div>

                        <h3 style="font-size:1.25rem; font-weight:800; color:var(--primary-deep); margin:0 0 var(--space-2xs);">
                            <?= e($isBn ? $flag['title_bn'] : $flag['title_en']) ?>
                        </h3>

                        <div style="font-size:0.84rem; font-weight:600; color:<?= $flag['color'] ?>; margin-bottom:var(--space-sm); line-height:1.4;">
                            <?= e($isBn ? $flag['tagline_bn'] : $flag['tagline_en']) ?>
                        </div>

                        <p style="font-size:0.9rem; color:var(--text-secondary); line-height:1.6; margin:0 0 var(--space-md);">
                            <?= e($isBn ? $flag['description_bn'] : $flag['description_en']) ?>
                        </p>
                    </div>

                    <div style="background:var(--bg-surface); border-top:1px dashed var(--border-subtle); padding:var(--space-sm) var(--space-md); border-radius:var(--radius-sm); font-size:0.8rem; font-weight:700; color:var(--text-muted); display:flex; align-items:center; gap:6px;">
                        <span>📌</span>
                        <span><?= e($isBn ? $flag['metrics_bn'] : $flag['metrics_en']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Timeline & Year-wise Project Logs Section -->
    <div id="timeline-section" style="margin-bottom: var(--space-4xl);">
        
        <div style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:var(--space-md); margin-bottom:var(--space-xl); border-bottom:2px solid var(--border-subtle); padding-bottom:var(--space-sm);">
            <div>
                <span style="font-size:0.8rem; font-weight:700; color:var(--accent-saffron); text-transform:uppercase; letter-spacing:1px;">
                    <?= $isBn ? 'প্রামাণ্য সময়রেখা' : 'Comprehensive Archives' ?>
                </span>
                <h2 style="font-size:1.85rem; font-weight:800; color:var(--primary-deep); margin:4px 0 0;">
                    <?= $isBn ? 'বছরভিত্তিক কার্যক্রমের পূর্ণাঙ্গ ইতিবৃত্ত (২০২০ – ২০২৬)' : 'Year-by-Year Project Logs (2020 – 2026)' ?>
                </h2>
            </div>
            
            <!-- Quick Search Bar -->
            <div style="position:relative; width:100%; max-width:280px;">
                <input type="text" id="activitySearchInput" placeholder="<?= $isBn ? 'স্থান, জেলা বা প্রজেক্ট খুঁজুন...' : 'Search by district or title...' ?>" style="width:100%; padding:8px 12px 8px 34px; font-size:0.88rem; border:1px solid var(--border-medium); border-radius:var(--radius-full); outline:none; background:#ffffff;">
                <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:0.9rem; color:var(--text-muted);">🔍</span>
            </div>
        </div>

        <!-- Filter Controls -->
        <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); padding:var(--space-md) var(--space-lg); margin-bottom:var(--space-2xl); box-shadow:var(--shadow-xs);">
            <!-- Year Selector Tabs -->
            <div style="display:flex; align-items:center; gap:var(--space-xs); flex-wrap:wrap; margin-bottom:var(--space-sm);">
                <span style="font-size:0.82rem; font-weight:700; color:var(--text-muted); margin-right:4px;">
                    <?= $isBn ? 'সাল নির্বাচন:' : 'Filter Year:' ?>
                </span>
                <button type="button" class="year-tab active" data-year="all" style="background:var(--primary-deep); color:#ffffff; border:none; padding:4px 14px; border-radius:var(--radius-full); font-size:0.82rem; font-weight:700; cursor:pointer;">
                    <?= $isBn ? 'সকল সাল' : 'All Years' ?>
                </button>
                <?php foreach (array_keys($activitiesByYear) as $y): ?>
                    <button type="button" class="year-tab" data-year="<?= $y ?>" style="background:var(--bg-subtle); color:var(--text-secondary); border:1px solid var(--border-medium); padding:4px 14px; border-radius:var(--radius-full); font-size:0.82rem; font-weight:600; cursor:pointer;">
                        <?= $y ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Category Selector Pills -->
            <div style="display:flex; align-items:center; gap:var(--space-xs); flex-wrap:wrap;">
                <span style="font-size:0.82rem; font-weight:700; color:var(--text-muted); margin-right:4px;">
                    <?= $isBn ? 'বিভাগ:' : 'Category:' ?>
                </span>
                <button type="button" class="cat-pill active" data-cat="all" style="background:#f1f5f9; color:#1e293b; border:1px solid var(--border-medium); padding:3px 12px; border-radius:var(--radius-sm); font-size:0.8rem; font-weight:600; cursor:pointer;">
                    <?= $isBn ? 'সব' : 'All' ?>
                </button>
                <button type="button" class="cat-pill" data-cat="shastra" style="background:#ffffff; color:var(--text-secondary); border:1px solid var(--border-medium); padding:3px 12px; border-radius:var(--radius-sm); font-size:0.8rem; cursor:pointer;">
                    📖 <?= $isBn ? 'শাস্ত্র ও শিক্ষা' : 'Scripture & Education' ?>
                </button>
                <button type="button" class="cat-pill" data-cat="temple" style="background:#ffffff; color:var(--text-secondary); border:1px solid var(--border-medium); padding:3px 12px; border-radius:var(--radius-sm); font-size:0.8rem; cursor:pointer;">
                    🛕 <?= $isBn ? 'মন্দির ও তীর্থ' : 'Temple & Pilgrimage' ?>
                </button>
                <button type="button" class="cat-pill" data-cat="humanitarian" style="background:#ffffff; color:var(--text-secondary); border:1px solid var(--border-medium); padding:3px 12px; border-radius:var(--radius-sm); font-size:0.8rem; cursor:pointer;">
                    🤝 <?= $isBn ? 'মানবিক ও দুর্যোগ ত্রাণ' : 'Humanitarian & Disaster' ?>
                </button>
                <button type="button" class="cat-pill" data-cat="livelihood" style="background:#ffffff; color:var(--text-secondary); border:1px solid var(--border-medium); padding:3px 12px; border-radius:var(--radius-sm); font-size:0.8rem; cursor:pointer;">
                    💼 <?= $isBn ? 'স্বাবলম্বীকরণ ও জীবিকা' : 'Livelihood & Rehabilitation' ?>
                </button>
                <button type="button" class="cat-pill" data-cat="nature" style="background:#ffffff; color:var(--text-secondary); border:1px solid var(--border-medium); padding:3px 12px; border-radius:var(--radius-sm); font-size:0.8rem; cursor:pointer;">
                    🌱 <?= $isBn ? 'পরিবেশ ও প্রকৃতি' : 'Nature & Trees' ?>
                </button>
                <button type="button" class="cat-pill" data-cat="health" style="background:#ffffff; color:var(--text-secondary); border:1px solid var(--border-medium); padding:3px 12px; border-radius:var(--radius-sm); font-size:0.8rem; cursor:pointer;">
                    🩺 <?= $isBn ? 'চিকিৎসা সহায়তা' : 'Health Aid' ?>
                </button>
            </div>
        </div>

        <!-- Timeline Cards Container -->
        <div id="activitiesList" style="display:flex; flex-direction:column; gap:var(--space-3xl);">
            <?php foreach ($activitiesByYear as $yr => $items): ?>
                <div class="year-block" data-year="<?= $yr ?>">
                    
                    <!-- Year Header Milestone Marker -->
                    <div style="display:flex; align-items:center; gap:var(--space-md); margin-bottom:var(--space-xl);">
                        <div style="background:var(--primary-deep); color:#ffffff; font-size:1.35rem; font-weight:800; padding:6px 20px; border-radius:var(--radius-full); box-shadow:var(--shadow-sm); display:inline-flex; align-items:center; gap:8px;">
                            <span>🗓️</span>
                            <span><?= $yr ?></span>
                        </div>
                        <div style="height:2px; background:linear-gradient(90deg, var(--border-medium), transparent); flex-grow:1;"></div>
                        <span style="font-size:0.85rem; font-weight:600; color:var(--text-muted);">
                            <?= sprintf($isBn ? '%dটি নথিভুক্ত কার্যক্রম' : '%d Documented Projects', count($items)) ?>
                        </span>
                    </div>

                    <!-- Cards in this year -->
                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(350px, 1fr)); gap:var(--space-lg);">
                        <?php foreach ($items as $item): ?>
                            <div class="activity-card" 
                                 data-year="<?= $item['year'] ?>" 
                                 data-cat="<?= $item['category'] ?>"
                                 data-text="<?= strtolower(e(($item['title_bn'] ?? '') . ' ' . ($item['title_en'] ?? '') . ' ' . ($item['location_bn'] ?? '') . ' ' . ($item['location_en'] ?? ''))) ?>"
                                 style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); padding:var(--space-xl); box-shadow:var(--shadow-xs); display:flex; flex-direction:column; justify-content:space-between; position:relative; overflow:hidden;">
                                
                                <div>
                                    <!-- Card Header Meta -->
                                    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:var(--space-sm); margin-bottom:var(--space-sm);">
                                        <div style="display:flex; align-items:center; gap:8px;">
                                            <span style="font-size:1.4rem;"><?= $item['icon'] ?></span>
                                            <div>
                                                <span style="font-size:0.75rem; font-weight:700; color:#b45309; background:#fffbeb; border:1px solid #fde68a; padding:2px 8px; border-radius:var(--radius-full);">
                                                    <?= e($isBn ? $item['category_bn'] : $item['category_en']) ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div style="font-size:0.78rem; font-weight:700; color:var(--text-muted); background:var(--bg-subtle); padding:2px 10px; border-radius:var(--radius-full); border:1px solid var(--border-subtle);">
                                            <?= e($item['date']) ?>
                                        </div>
                                    </div>

                                    <!-- Title -->
                                    <h3 style="font-size:1.15rem; font-weight:800; color:var(--primary-deep); margin:var(--space-2xs) 0 var(--space-xs); line-height:1.4;">
                                        <?= e($isBn ? $item['title_bn'] : $item['title_en']) ?>
                                    </h3>

                                    <!-- Location Marker -->
                                    <div style="display:flex; align-items:center; gap:5px; font-size:0.82rem; font-weight:600; color:var(--accent-saffron); margin-bottom:var(--space-sm);">
                                        <span>📍</span>
                                        <span><?= e($isBn ? $item['location_bn'] : $item['location_en']) ?></span>
                                    </div>

                                    <!-- Description -->
                                    <p style="font-size:0.88rem; line-height:1.6; color:var(--text-secondary); margin:0 0 var(--space-md);">
                                        <?= e($isBn ? $item['description_bn'] : $item['description_en']) ?>
                                    </p>

                                    <!-- Highlights / Deliverables -->
                                    <?php if (!empty($item['highlights_bn'])): ?>
                                        <div style="margin-bottom:var(--space-md);">
                                            <div style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; margin-bottom:4px; letter-spacing:0.5px;">
                                                <?= $isBn ? 'মূল সাফল্য ও ফলাফল:' : 'Key Outcomes:' ?>
                                            </div>
                                            <ul style="margin:0; padding-left:18px; font-size:0.82rem; color:var(--text-primary); line-height:1.5;">
                                                <?php 
                                                    $hl = $isBn ? $item['highlights_bn'] : $item['highlights_en'];
                                                    foreach ($hl as $pt): 
                                                ?>
                                                    <li><?= e($pt) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Card Footer: Status and Badge -->
                                <div style="display:flex; align-items:center; justify-content:space-between; border-top:1px solid var(--border-subtle); padding-top:var(--space-sm); margin-top:var(--space-sm);">
                                    <div style="display:inline-flex; align-items:center; gap:6px; font-size:0.78rem; font-weight:700; color:<?= $item['status'] === 'in_progress' ? '#2563eb' : '#047857' ?>;">
                                        <span style="width:8px; height:8px; border-radius:50%; background:<?= $item['status'] === 'in_progress' ? '#2563eb' : '#047857' ?>;"></span>
                                        <span><?= e($isBn ? $item['status_bn'] : $item['status_en']) ?></span>
                                    </div>

                                    <span style="font-size:0.75rem; font-weight:600; color:var(--text-muted); background:var(--bg-surface); padding:2px 8px; border-radius:var(--radius-sm); border:1px solid var(--border-subtle);">
                                        <?= e($item['badge']) ?>
                                    </span>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>

        <!-- No Results Fallback -->
        <div id="noResultsNotice" style="display:none; text-align:center; padding:var(--space-3xl); background:#ffffff; border:1px dashed var(--border-medium); border-radius:var(--radius-lg); margin-top:var(--space-xl);">
            <div style="font-size:2.5rem; margin-bottom:var(--space-xs);">🔍</div>
            <h4 style="font-size:1.15rem; font-weight:700; color:var(--primary-deep); margin:0 0 6px;">
                <?= $isBn ? 'কোনো কার্যক্রম খুঁজে পাওয়া যায়নি' : 'No activities matched your search' ?>
            </h4>
            <p style="font-size:0.88rem; color:var(--text-muted); margin:0 0 var(--space-md);">
                <?= $isBn ? 'অন্য কোনো সাল, বিভাগ বা ভিন্ন কী-ওয়ার্ড দিয়ে চেষ্টা করুন।' : 'Please adjust your year, category, or search keywords.' ?>
            </p>
            <button type="button" id="resetFiltersBtn" class="btn btn-secondary" style="font-size:0.85rem;">
                <?= $isBn ? '🔄 ফিল্টার রিসেট করুন' : '🔄 Reset All Filters' ?>
            </button>
        </div>

    </div>

    <!-- Live Project Tracking Table (Synced from Official SPS Notion Database) -->
    <div id="tracking-section" style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-xl); padding:var(--space-2xl) var(--space-xl); margin-bottom:var(--space-4xl); box-shadow:var(--shadow-sm);">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:var(--space-md); margin-bottom:var(--space-xl);">
            <div>
                <div style="display:inline-flex; align-items:center; gap:6px; background:#eff6ff; color:#1d4ed8; padding:3px 10px; border-radius:var(--radius-full); font-size:0.75rem; font-weight:700; margin-bottom:var(--space-2xs); border:1px solid #bfdbfe;">
                    <span>⚡</span>
                    <span><?= $isBn ? 'এসপিএস কেন্দ্রীয় প্রজেক্ট ট্র্যাকার' : 'SPS Central Project Tracker' ?></span>
                </div>
                <h3 style="font-size:1.45rem; font-weight:800; color:var(--primary-deep); margin:0 0 4px;">
                    <?= $isBn ? 'চলমান ও পর্যালোচনাধীন প্রকল্প পর্যবেক্ষণ (SPS Project Tracking)' : 'Real-time Project Tracking & Execution Status' ?>
                </h3>
                <p style="font-size:0.88rem; color:var(--text-muted); margin:0;">
                    <?= $isBn ? 'প্রাতিষ্ঠানিক স্বচ্ছতার অংশ হিসেবে প্রতিটি প্রকল্পের বর্তমান অবস্থা, অগ্রাধিকার এবং সমন্বয়কের দায়িত্ব সরাসরি প্রকাশ করা হলো।' : 'Institutional transparency: real-time breakdown of internal status, priority, and coordination desk.' ?>
                </p>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; text-align:left; font-size:0.88rem;">
                <thead>
                    <tr style="background:var(--bg-subtle); border-bottom:2px solid var(--border-medium); color:var(--text-muted); font-size:0.8rem; text-transform:uppercase; letter-spacing:0.5px;">
                        <th style="padding:12px 14px; font-weight:700;"><?= $isBn ? 'প্রকল্পের নাম (Project Name)' : 'Project Name' ?></th>
                        <th style="padding:12px 14px; font-weight:700; width:170px;"><?= $isBn ? 'অবস্থা (Status)' : 'Status' ?></th>
                        <th style="padding:12px 14px; font-weight:700; width:130px;"><?= $isBn ? 'অগ্রাধিকার (Priority)' : 'Priority' ?></th>
                        <th style="padding:12px 14px; font-weight:700; width:180px;"><?= $isBn ? 'ধরন (Type)' : 'Type' ?></th>
                        <th style="padding:12px 14px; font-weight:700; width:160px;"><?= $isBn ? 'সমন্বয়ক (Lead)' : 'Coordinator' ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trackedProjects as $proj): 
                        $statusStyle = match($proj['status']) {
                            'done' => 'background:#dcfce7; color:#15803d; border:1px solid #86efac;',
                            'in_progress' => 'background:#dbeafe; color:#1d4ed8; border:1px solid #93c5fd;',
                            'in_review' => 'background:#f3e8ff; color:#7e22ce; border:1px solid #d8b4fe;',
                            default => 'background:#fef3c7; color:#b45309; border:1px solid #fde68a;',
                        };

                        $prioStyle = match($proj['priority']) {
                            'high' => 'background:#fee2e2; color:#b91c1c; border:1px solid #fca5a5;',
                            'medium' => 'background:#ffedd5; color:#c2410c; border:1px solid #fed7aa;',
                            default => 'background:#f1f5f9; color:#475569; border:1px solid #cbd5e1;',
                        };
                    ?>
                        <tr style="border-bottom:1px solid var(--border-subtle); transition:background 0.15s ease;">
                            <td style="padding:14px; font-weight:700; color:var(--primary-deep);">
                                <?= e($isBn ? $proj['name_bn'] : $proj['name_en']) ?>
                            </td>
                            <td style="padding:14px;">
                                <span style="display:inline-block; font-size:0.75rem; font-weight:700; padding:3px 10px; border-radius:var(--radius-full); <?= $statusStyle ?>">
                                    <?= e($isBn ? $proj['status_bn'] : $proj['status_en']) ?>
                                </span>
                            </td>
                            <td style="padding:14px;">
                                <span style="display:inline-block; font-size:0.75rem; font-weight:700; padding:2px 8px; border-radius:var(--radius-sm); <?= $prioStyle ?>">
                                    <?= e($isBn ? $proj['priority_bn'] : $proj['priority_en']) ?>
                                </span>
                            </td>
                            <td style="padding:14px; color:var(--text-secondary); font-size:0.84rem;">
                                <?= e($isBn ? $proj['type_bn'] : $proj['type_en']) ?>
                            </td>
                            <td style="padding:14px; font-size:0.82rem; color:var(--text-muted); font-weight:600;">
                                <?= e($proj['lead']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Call to Action: Get Involved / Support -->
    <div style="background:linear-gradient(135deg, #b3391b 0%, #e65100 100%); color:#ffffff; border-radius:var(--radius-xl); padding:var(--space-2xl) var(--space-xl); text-align:center; box-shadow:var(--shadow-lg);">
        <div style="max-width:700px; margin:0 auto;">
            <span style="font-size:2rem; margin-bottom:var(--space-2xs); display:inline-block;">🪷</span>
            <h3 style="font-size:1.85rem; font-weight:800; margin:0 0 var(--space-xs); color:#ffffff;">
                <?= $isBn ? 'আপনার অঞ্চলে এসপিএস সেবাকর্ম সম্প্রসারণে এগিয়ে আসুন' : 'Join or Partner with SPS to Expand Grassroots Seva' ?>
            </h3>
            <p style="font-size:0.95rem; color:#fed7aa; margin:0 0 var(--space-xl); line-height:1.6;">
                <?= $isBn 
                    ? 'আপনি একজন ছাত্র, শিক্ষক, কর্মজীবী বা প্রবাসী যাই হোন না কেন — আপনার সক্রিয় অংশগ্রহণ, স্থানীয় পরামর্শ বা ১০ টাকার ক্ষুদ্র অনুদানও বাংলাদেশের প্রত্যন্ত অঞ্চলের একজন অসহায় সনাতনী ভাইয়ের মুখে হাসি ফোটাতে পারে।' 
                    : 'Whether you are a student, teacher, professional, or diaspora member—your volunteer involvement, district guidance, or daily micro-contributions help rebuild lives and preserve timeless Sanatan heritage.' ?>
            </p>
            <div style="display:flex; justify-content:center; gap:var(--space-md); flex-wrap:wrap;">
                <a href="<?= url('/get-involved', $locale) ?>" class="btn btn-secondary" style="background:#ffffff; color:var(--primary-deep); font-weight:700; border:none; box-shadow:var(--shadow-md);">
                    <?= $isBn ? '🤝 সদস্য / স্বেচ্ছাসেবক নিবন্ধন' : '🤝 Register as Member / Volunteer' ?>
                </a>
                <a href="tel:+8801736360041" class="btn btn-outline" style="background:rgba(255,255,255,0.15); color:#ffffff; border-color:rgba(255,255,255,0.4); font-weight:600;">
                    <?= $isBn ? '📞 হটলাইন: +৮৮০ ১৭৩৬-৩৬০০৪১' : '📞 Call Hotline: +880 1736-360041' ?>
                </a>
            </div>
        </div>
    </div>

</div>

<!-- Client-side Interactive Filtering Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const yearTabs = document.querySelectorAll('.year-tab');
    const catPills = document.querySelectorAll('.cat-pill');
    const searchInput = document.getElementById('activitySearchInput');
    const cards = document.querySelectorAll('.activity-card');
    const yearBlocks = document.querySelectorAll('.year-block');
    const noResults = document.getElementById('noResultsNotice');
    const resetBtn = document.getElementById('resetFiltersBtn');

    let currentYear = 'all';
    let currentCat = 'all';
    let currentQuery = '';

    function applyFilters() {
        let totalVisible = 0;

        yearBlocks.forEach(block => {
            const blockYear = block.getAttribute('data-year');
            const blockCards = block.querySelectorAll('.activity-card');
            let blockVisibleCards = 0;

            blockCards.forEach(card => {
                const cardYear = card.getAttribute('data-year');
                const cardCat = card.getAttribute('data-cat');
                const cardText = card.getAttribute('data-text') || '';

                const matchesYear = (currentYear === 'all' || currentYear === cardYear);
                const matchesCat = (currentCat === 'all' || currentCat === cardCat);
                const matchesSearch = (!currentQuery || cardText.includes(currentQuery));

                if (matchesYear && matchesCat && matchesSearch) {
                    card.style.display = 'flex';
                    blockVisibleCards++;
                    totalVisible++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show or hide whole year block based on whether any cards in it are visible
            if (blockVisibleCards > 0 && (currentYear === 'all' || currentYear === blockYear)) {
                block.style.display = 'block';
            } else {
                block.style.display = 'none';
            }
        });

        if (totalVisible === 0) {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
    }

    // Year tab clicks
    yearTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            yearTabs.forEach(t => {
                t.style.background = 'var(--bg-subtle)';
                t.style.color = 'var(--text-secondary)';
                t.style.borderColor = 'var(--border-medium)';
                t.classList.remove('active');
            });
            this.style.background = 'var(--primary-deep)';
            this.style.color = '#ffffff';
            this.style.borderColor = 'var(--primary-deep)';
            this.classList.add('active');

            currentYear = this.getAttribute('data-year');
            applyFilters();
        });
    });

    // Category pill clicks
    catPills.forEach(pill => {
        pill.addEventListener('click', function() {
            catPills.forEach(p => {
                p.style.background = '#ffffff';
                p.style.color = 'var(--text-secondary)';
                p.classList.remove('active');
            });
            this.style.background = '#f1f5f9';
            this.style.color = '#1e293b';
            this.classList.add('active');

            currentCat = this.getAttribute('data-cat');
            applyFilters();
        });
    });

    // Realtime search input
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            currentQuery = this.value.trim().toLowerCase();
            applyFilters();
        });
    }

    // Reset button
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            currentYear = 'all';
            currentCat = 'all';
            currentQuery = '';
            if (searchInput) searchInput.value = '';

            // Reset tab styles
            yearTabs.forEach((t, i) => {
                if (i === 0) {
                    t.style.background = 'var(--primary-deep)';
                    t.style.color = '#ffffff';
                    t.classList.add('active');
                } else {
                    t.style.background = 'var(--bg-subtle)';
                    t.style.color = 'var(--text-secondary)';
                    t.classList.remove('active');
                }
            });

            // Reset pill styles
            catPills.forEach((p, i) => {
                if (i === 0) {
                    p.style.background = '#f1f5f9';
                    p.style.color = '#1e293b';
                    p.classList.add('active');
                } else {
                    p.style.background = '#ffffff';
                    p.style.color = 'var(--text-secondary)';
                    p.classList.remove('active');
                }
            });

            applyFilters();
        });
    }
});
</script>
