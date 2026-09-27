<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
?>

<div class="container" style="padding-top:var(--space-3xl); padding-bottom:var(--space-4xl);">
    <div style="border-bottom:2px solid var(--accent-gold); padding-bottom:var(--space-md); margin-bottom:var(--space-3xl);">
        <div class="breadcrumb">
            <a href="<?= e(url('/', $currentLocale)) ?>"><?= e(__('common.nav.home')) ?></a>
            <span class="breadcrumb-separator">/</span>
            <span style="color:var(--text-main); font-weight:600;"><?= $isBn ? 'ডিজাইন সিস্টেম উপাদানসমূহ' : 'Design System Components' ?></span>
        </div>
        <h1 style="margin-bottom:var(--space-xs);"><?= $isBn ? 'এসপিএস ডিজাইন সিস্টেম ও উপাদান ক্যাটালগ' : 'SPS Design System & Component Library' ?></h1>
        <p class="section-subtitle">
            <?= $isBn 
                ? 'অনন্য মানবিক ও প্রাতিষ্ঠানিক সৌন্দর্যে নির্মিত পুনর্ব্যবহারযোগ্য UI উপাদানসমূহ। কোনো কৃত্রিম বা এআই-ক্লিপআর্ট নয়; প্রামাণ্য গ্রন্থালয় ও পাণ্ডুলিপির নান্দনিকতায় গঠিত।'
                : 'Reusable editorial UI components crafted for scholarly institutional elegance, avoiding generic AI tropes and flashy neons.' ?>
        </p>
    </div>

    <!-- 1. Color System Palette -->
    <section style="margin-bottom:var(--space-3xl);">
        <span class="section-tag"><?= $isBn ? 'রং ও টোকেন' : 'Color System Tokens' ?></span>
        <h2 style="font-size:1.5rem; margin-bottom:var(--space-md);"><?= $isBn ? 'পরিমিত ও অর্থবহ বর্ণচ্ছটা' : 'Restrained Color Architecture' ?></h2>
        
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:var(--space-md);">
            <div style="background:#FAF8F5; border:1px solid #D6CCC0; padding:var(--space-md); border-radius:var(--radius-sm);">
                <div style="height:36px; background:#FAF8F5; border:1px solid #E8E2D8; border-radius:2px; margin-bottom:8px;"></div>
                <strong style="font-size:0.88rem; display:block;">Warm Ivory</strong>
                <code style="font-size:0.75rem; color:var(--text-muted);">#FAF8F5 (--bg-canvas)</code>
            </div>
            <div style="background:#1A1F1C; color:#fff; border:1px solid #1A1F1C; padding:var(--space-md); border-radius:var(--radius-sm);">
                <div style="height:36px; background:#1A1F1C; border-radius:2px; margin-bottom:8px;"></div>
                <strong style="font-size:0.88rem; display:block;">Deep Charcoal</strong>
                <code style="font-size:0.75rem; color:#A8B5AC;">#1A1F1C (--text-main)</code>
            </div>
            <div style="background:#FAF8F5; border:1px solid #D6CCC0; padding:var(--space-md); border-radius:var(--radius-sm);">
                <div style="height:36px; background:#C65A1E; border-radius:2px; margin-bottom:8px;"></div>
                <strong style="font-size:0.88rem; display:block;">Muted Saffron</strong>
                <code style="font-size:0.75rem; color:var(--text-muted);">#C65A1E (--accent-saffron)</code>
            </div>
            <div style="background:#FAF8F5; border:1px solid #D6CCC0; padding:var(--space-md); border-radius:var(--radius-sm);">
                <div style="height:36px; background:#A37E36; border-radius:2px; margin-bottom:8px;"></div>
                <strong style="font-size:0.88rem; display:block;">Antique Gold</strong>
                <code style="font-size:0.75rem; color:var(--text-muted);">#A37E36 (--accent-gold)</code>
            </div>
            <div style="background:#FAF8F5; border:1px solid #D6CCC0; padding:var(--space-md); border-radius:var(--radius-sm);">
                <div style="height:36px; background:#5C4334; border-radius:2px; margin-bottom:8px;"></div>
                <strong style="font-size:0.88rem; display:block;">Warm Brown</strong>
                <code style="font-size:0.75rem; color:var(--text-muted);">#5C4334 (--accent-brown)</code>
            </div>
            <div style="background:#FAF8F5; border:1px solid #D6CCC0; padding:var(--space-md); border-radius:var(--radius-sm);">
                <div style="height:36px; background:#286846; border-radius:2px; margin-bottom:8px;"></div>
                <strong style="font-size:0.88rem; display:block;">Leaf Green</strong>
                <code style="font-size:0.75rem; color:var(--text-muted);">#286846 (--status-success)</code>
            </div>
        </div>
    </section>

    <!-- 2. Typography Showcase -->
    <section style="margin-bottom:var(--space-3xl);">
        <span class="section-tag"><?= $isBn ? 'টাইপোগ্রাফি' : 'Typography Hierarchy' ?></span>
        <h2 style="font-size:1.5rem; margin-bottom:var(--space-md);"><?= $isBn ? 'দ্বিভাষিক ফন্ট সংকলন' : 'Bilingual Font Specimens' ?></h2>
        
        <div class="grid-2">
            <div class="card">
                <span class="badge badge-scholarly" style="margin-bottom:var(--space-sm);">বাংলা টাইপোগ্রাফি (Bengali)</span>
                <h1 style="font-size:1.8rem; margin-bottom:var(--space-xs);">হেডিং ১: সনাতন ফিলোসফি এন্ড স্ক্রিপচার</h1>
                <h2 style="font-size:1.4rem; margin-bottom:var(--space-xs);">হেডিং ২: জ্ঞানের প্রামাণিক সংরক্ষণ</h2>
                <h3 style="font-size:1.15rem; margin-bottom:var(--space-xs);">হেডিং ৩: নিষ্কাম কর্মযোগ ও মানবিক সেবা</h3>
                <p style="font-size:0.95rem; line-height:1.7; color:var(--text-body);">
                    সাধারণ বডি টেক্সট: প্রাচীন ভারতীয় মননশীলতার গভীরে প্রবেশ করে শাশ্বত সত্যের সন্ধান এবং সমকালীন সমাজের নৈতিক উৎকর্ষ সাধনই আমাদের মূল লক্ষ্য।
                </p>
                <div class="editorial-quote" style="margin:var(--space-md) 0 0;">
                    <p style="font-size:1rem; margin-bottom:0;">“সত্যমেব জয়তে নানৃতং সত্যেন পন্থা বিততো দেবযানঃ”</p>
                    <cite>— মুণ্ডক উপনিষদ ৩.১.৬</cite>
                </div>
            </div>

            <div class="card">
                <span class="badge badge-scholarly" style="margin-bottom:var(--space-sm);">English & Sanskrit Typography</span>
                <h1 style="font-size:1.8rem; margin-bottom:var(--space-xs); font-family:var(--font-display);">H1: Sanatan Philosophy & Scripture</h1>
                <h2 style="font-size:1.4rem; margin-bottom:var(--space-xs); font-family:var(--font-display);">H2: Scholarly Digital Archive</h2>
                <h3 style="font-size:1.15rem; margin-bottom:var(--space-xs);">H3: Epistemology, Dharma & Community</h3>
                <p style="font-size:0.95rem; line-height:1.7; color:var(--text-body);">
                    Standard body copy: Grounded in primary sources and reasoned inquiry, illuminating timeless philosophical perspectives alongside direct compassionate action.
                </p>
                <div class="editorial-quote" style="margin:var(--space-md) 0 0;">
                    <div class="sanskrit-verse" style="font-size:1rem; line-height:1.6; color:var(--accent-saffron);">
                        सत्यमेव जयते नानृतं सत्येन पन्था विततो देवयानः ।
                    </div>
                    <cite>— Mundaka Upanishad 3.1.6</cite>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Button Component Family -->
    <section style="margin-bottom:var(--space-3xl);">
        <span class="section-tag"><?= $isBn ? 'বোতাম উপাদান' : 'Buttons & Interactive States' ?></span>
        <h2 style="font-size:1.5rem; margin-bottom:var(--space-md);"><?= $isBn ? 'বাটন ভ্যারিয়েশন' : 'Button Variations' ?></h2>
        
        <div class="card" style="display:flex; flex-wrap:wrap; align-items:center; gap:var(--space-md);">
            <button class="btn btn-primary">Primary Terracotta</button>
            <button class="btn btn-secondary">Secondary Outline</button>
            <button class="btn btn-gold">Antique Gold</button>
            <button class="btn btn-ghost">Ghost Button</button>
            <button class="btn btn-primary btn-sm">Small Button</button>
            <button class="btn btn-primary btn-lg">Large Action</button>
            <button class="btn btn-secondary btn-icon" aria-label="Icon button">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </button>
        </div>
    </section>

    <!-- 4. Badges & Status Indicators -->
    <section style="margin-bottom:var(--space-3xl);">
        <span class="section-tag"><?= $isBn ? 'স্ট্যাটাস ব্যাজ' : 'Badges & Statuses' ?></span>
        <h2 style="font-size:1.5rem; margin-bottom:var(--space-md);"><?= $isBn ? 'ব্যাজ ও লেবেল' : 'Semantic Status Badges' ?></h2>
        
        <div class="card" style="display:flex; flex-wrap:wrap; gap:var(--space-sm);">
            <span class="badge badge-ongoing">● <?= e(__('common.badges.ongoing')) ?></span>
            <span class="badge badge-planned">▲ <?= e(__('common.badges.planned')) ?></span>
            <span class="badge badge-success">✓ <?= e(__('common.badges.completed')) ?></span>
            <span class="badge badge-scholarly">◈ <?= e(__('common.badges.scholarly')) ?></span>
            <span class="badge badge-info">ℹ <?= e(__('common.badges.public_domain')) ?></span>
            <span class="badge badge-danger">✕ Attention</span>
            <span class="badge badge-neutral">Category Tag</span>
        </div>
    </section>

    <!-- 5. Interactive Tabs & Alerts -->
    <section style="margin-bottom:var(--space-3xl);">
        <span class="section-tag"><?= $isBn ? 'ট্যাব ও নোটিফিকেশন' : 'Tabs & Alerts' ?></span>
        <h2 style="font-size:1.5rem; margin-bottom:var(--space-md);"><?= $isBn ? 'ট্যাব এবং অ্যালার্ট বক্স' : 'Accessible Tabs & Context Alerts' ?></h2>

        <div class="card">
            <!-- Tabs Nav -->
            <div class="tabs-nav" data-tabs-group="demo-tabs">
                <button class="tab-btn active" data-tab-target="tab-philosophy"><?= $isBn ? 'দর্শন ও তত্ত্ব' : 'Philosophy' ?></button>
                <button class="tab-btn" data-tab-target="tab-seva"><?= $isBn ? 'মানবসেবা' : 'Seva & Relief' ?></button>
                <button class="tab-btn" data-tab-target="tab-transparency"><?= $isBn ? 'স্বচ্ছতা নীতি' : 'Financial Ethics' ?></button>
            </div>

            <!-- Tab Panes -->
            <div id="demo-tabs">
                <div id="tab-philosophy" data-tab-pane style="display:block;">
                    <p style="font-size:0.92rem; color:var(--text-body);">
                        <?= $isBn 
                            ? 'বেদান্ত ও ষড়দর্শনের শাশ্বত জ্ঞানকে কেবল পুঁথিতে সীমাবদ্ধ না রেখে তা জীবনের প্রতিটি পদক্ষেপে প্রয়োগ করাই প্রকৃত দর্শনচর্চা।'
                            : 'Authentic philosophical inquiry synthesizes transcendent realization with direct, ethical responsibility in empirical life.' ?>
                    </p>
                </div>
                <div id="tab-seva" data-tab-pane style="display:none;">
                    <p style="font-size:0.92rem; color:var(--text-body);">
                        <?= $isBn 
                            ? '“জীবের মধ্যে শিবের অধিষ্ঠান” — ঈশ্বরজ্ঞানে আর্ত ও বঞ্চিত মানুষের সেবা করাই আমাদের সর্বোচ্চ সাধনা।'
                            : 'Serving humanity with the veneration of the Divine constitutes our highest institutional discipline.' ?>
                    </p>
                </div>
                <div id="tab-transparency" data-tab-pane style="display:none;">
                    <p style="font-size:0.92rem; color:var(--text-body);">
                        <?= $isBn 
                            ? 'প্রতিটি পাইপয়সার হিসাব পাবলিক লেজারে সংরক্ষণ ও নিরীক্ষা করা হয়। গোপনীয়তা কেবল দাতার ব্যক্তিগত তথ্যে প্রযোজ্য, তহবিলে নয়।'
                            : 'Every unit of public contribution is recorded and publicly audited without exception. Confidentiality protects donor privacy, never institutional expenditures.' ?>
                    </p>
                </div>
            </div>

            <!-- Context Alerts -->
            <div style="margin-top:var(--space-xl);">
                <div class="alert alert-info">
                    <strong>ℹ Information:</strong>
                    <span><?= $isBn ? 'এসপিএস দ্বিভাষিক প্ল্যাটফর্মের ফেজ ১ সফলভাবে স্থাপিত হয়েছে।' : 'SPS Bilingual Platform Phase 1 foundation is operational.' ?></span>
                </div>
                <div class="alert alert-success">
                    <strong>✓ Verified:</strong>
                    <span><?= $isBn ? 'স্বেচ্ছাসেবী ও গবেষক দলের অডিট হিসাব হালনাগাদ করা হয়েছে।' : 'Verified financial summaries updated and synchronized.' ?></span>
                </div>
                <div class="alert alert-warning">
                    <strong>▲ Notice:</strong>
                    <span><?= $isBn ? 'পাণ্ডুলিপি স্ক্যানিংয়ের জন্য নতুন স্বেচ্ছাসেবক নিবন্ধন চলছে।' : 'Volunteer intake currently active for manuscript scanning.' ?></span>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. Form Controls -->
    <section style="margin-bottom:var(--space-3xl);">
        <span class="section-tag"><?= $isBn ? 'ফরম ও ইনপুট' : 'Form Inputs' ?></span>
        <h2 style="font-size:1.5rem; margin-bottom:var(--space-md);"><?= $isBn ? 'ইনপুট এলিমেন্টস' : 'Refined Input Controls' ?></h2>

        <div class="card" style="max-width:640px;">
            <div class="form-group">
                <label class="form-label"><?= $isBn ? 'আপনার পূর্ণ নাম' : 'Full Name' ?></label>
                <input type="text" class="form-control" placeholder="<?= $isBn ? 'যেমন: শ্রীমৎ অমলকৃষ্ণ দেব' : 'e.g. Anand Sharma' ?>">
            </div>
            <div class="form-group">
                <label class="form-label"><?= $isBn ? 'আগ্রহের ক্ষেত্র' : 'Area of Interest' ?></label>
                <select class="form-control">
                    <option><?= $isBn ? 'বেদান্ত ও উপনিষদ গবেষণা' : 'Vedanta & Upanishad Study' ?></option>
                    <option><?= $isBn ? 'বিনামূল্যে পাঠশালা শিক্ষকতা' : 'Vidyapeeth Free Teaching' ?></option>
                    <option><?= $isBn ? 'চিকিৎসা শিবির সমন্বয়' : 'Medical Camp Coordination' ?></option>
                    <option><?= $isBn ? 'ডিজিটাল লাইব্রেরি ও পাণ্ডুলিপি সংরক্ষণ' : 'Digital Library & Archiving' ?></option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label"><?= $isBn ? 'মন্তব্য বা সংক্ষিপ্ত বার্তা' : 'Message or Motivation' ?></label>
                <textarea class="form-control" rows="3" placeholder="<?= $isBn ? 'আপনার আগ্রহ সম্পর্কে লিখুন...' : 'Briefly describe your background or inquiry...' ?>"></textarea>
            </div>
            <button type="button" class="btn btn-primary"><?= $isBn ? 'বার্তা প্রেরণ করুন' : 'Submit Inquiries' ?></button>
        </div>
    </section>

    <!-- 7. Empty State Primitive -->
    <section>
        <span class="section-tag"><?= $isBn ? 'খালি অবস্থা' : 'Empty State Primitive' ?></span>
        <h2 style="font-size:1.5rem; margin-bottom:var(--space-md);"><?= $isBn ? 'এম্পটি স্টেট কম্পোনেন্ট' : 'Graceful Empty State Container' ?></h2>

        <div class="empty-state">
            <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
            <h4 style="margin-bottom:var(--space-2xs);"><?= $isBn ? 'কোনো নতুন রেকর্ড নেই' : 'No Records Found' ?></h4>
            <p style="font-size:0.88rem; color:var(--text-muted); max-width:400px; margin:0 auto var(--space-md);">
                <?= $isBn ? 'এই মুহূর্তে প্রদর্শনের জন্য কোনো তথ্য পাওয়া যায়নি।' : 'There are currently no items to display in this collection.' ?>
            </p>
            <a href="<?= e(url('/', $currentLocale)) ?>" class="btn btn-secondary btn-sm"><?= e(__('common.actions.explore')) ?></a>
        </div>
    </section>
</div>
