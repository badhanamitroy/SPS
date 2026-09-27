<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
?>

<!-- 1. Hero Section -->
<section class="hero-section" id="hero">
    <div class="container hero-inner">
        <div class="hero-content">
            <div class="hero-pretitle">
                <?= e(__('home.hero.pre_title')) ?>
            </div>
            <h1 class="hero-title">
                <?= e(__('home.hero.title')) ?>
            </h1>
            <div class="hero-motto">
                <?= e(__('home.hero.subtitle')) ?>
            </div>
            <p class="hero-description">
                <?= e(__('home.hero.description')) ?>
            </p>
            <div class="hero-ctas">
                <a href="#about" class="btn btn-primary btn-lg">
                    <?= e(__('home.hero.cta_explore')) ?>
                </a>
                <a href="<?= e(url('/get-involved', $currentLocale)) ?>" class="btn btn-secondary btn-lg">
                    <?= e(__('home.hero.cta_member')) ?>
                </a>
                <a href="<?= e(url('/transparency', $currentLocale)) ?>" class="btn btn-ghost btn-lg">
                    <?= e(__('home.hero.cta_donate')) ?> →
                </a>
            </div>

            <!-- Factual Metrics -->
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-num"><?= $isBn ? '১২৫০+' : '1,250+' ?></span>
                    <span class="stat-label"><?= e(__('home.hero.meta_stat_members')) ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-num"><?= $isBn ? '৪৫০+' : '450+' ?></span>
                    <span class="stat-label"><?= e(__('home.hero.meta_stat_books')) ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-num"><?= $isBn ? '১৮,০০০+' : '18,000+' ?></span>
                    <span class="stat-label"><?= e(__('home.hero.meta_stat_beneficiaries')) ?></span>
                </div>
            </div>
        </div>

        <div class="hero-visual">
            <div class="hero-panel">
                <div class="panel-verse-header">
                    <span class="panel-verse-tag"><?= $isBn ? 'শাশ্বত প্রজ্ঞা' : 'Timeless Inscription' ?></span>
                    <span class="badge badge-scholarly"><?= $isBn ? 'বেদান্ত সূত্র' : 'Vedanta Sutra' ?></span>
                </div>
                <div style="margin: var(--space-md) 0;">
                    <div class="sanskrit-verse" style="font-size:1.1rem; line-height:1.75; margin-bottom:var(--space-xs);">
                        अथातो ब्रह्मजिज्ञासा ।<br>
                        जन्माद्यस्य यतः ॥
                    </div>
                    <?php if ($isBn): ?>
                    <div style="font-size:0.92rem; color:var(--text-body); line-height:1.6; margin-top:var(--space-xs);">
                        <em>"অতএব এখন ব্রহ্মজিজ্ঞাসার সময়। এই দৃশ্যমান নিখিল বিশ্বজগতের উৎপত্তি, স্থিতি ও লয় যাঁর থেকে..."</em>
                    </div>
                    <?php else: ?>
                    <div style="font-size:0.9rem; color:var(--text-body); line-height:1.6; margin-top:var(--space-xs);">
                        <em>"Now therefore the inquiry into Brahman. From Whom originates, endures, and dissolves this cosmic expanse..."</em>
                    </div>
                    <?php endif; ?>
                </div>
                <div style="border-top:1px solid var(--border-subtle); padding-top:var(--space-sm); display:flex; justify-content:space-between; align-items:center; font-size:0.8rem; color:var(--text-muted);">
                    <span><?= $isBn ? 'ব্রহ্মসূত্র ১.১.১-২' : 'Brahma Sutras 1.1.1-2' ?></span>
                    <a href="<?= e(url('/knowledge', $currentLocale)) ?>" style="color:var(--accent-saffron); font-weight:600;">
                        <?= e(__('common.actions.read_more')) ?> →
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. About SPS Section -->
<section class="editorial-section" id="about">
    <div class="container">
        <div class="about-grid">
            <div>
                <span class="section-tag"><?= e(__('home.about.tag')) ?></span>
                <h2 class="section-title"><?= e(__('home.about.title')) ?></h2>
                <p class="lead" style="margin-top:var(--space-md);">
                    <?= e(__('home.about.p1')) ?>
                </p>
                <p>
                    <?= e(__('home.about.p2')) ?>
                </p>
                <div class="about-features">
                    <div class="about-feature-item">
                        <div class="feature-bullet">✓</div>
                        <div>
                            <strong><?= $isBn ? 'প্রামাণিক ও অবিকৃত শাস্ত্রজ্ঞান' : 'Authentic & Unadulterated Exegesis' ?></strong>
                            <p style="font-size:0.9rem; color:var(--text-muted); margin-bottom:0;">
                                <?= $isBn ? 'মূল সংস্কৃত উৎসের প্রামাণ্য অনুবাদ ও নিরপেক্ষ দার্শনিক পর্যালোচনা।' : 'Critical study directly anchored in primary Sanskrit treatises.' ?>
                            </p>
                        </div>
                    </div>
                    <div class="about-feature-item">
                        <div class="feature-bullet">✓</div>
                        <div>
                            <strong><?= $isBn ? 'সরাসরি মানবিক ও সামাজিক দায়বদ্ধতা' : 'Unconditional Humanitarian Accountability' ?></strong>
                            <p style="font-size:0.9rem; color:var(--text-muted); margin-bottom:0;">
                                <?= $isBn ? 'শিক্ষা, শিশু স্বাস্থ্য ও দুর্যোগ সহায়তায় সরাসরি ফিল্ডওয়ার্ক।' : 'Direct field initiatives across literacy, nutrition, and relief.' ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div style="margin-top:var(--space-xl);">
                    <a href="<?= e(url('/about', $currentLocale)) ?>" class="btn btn-secondary">
                        <?= e(__('home.about.learn_more')) ?> →
                    </a>
                </div>
            </div>

            <div>
                <div class="card card-tinted" style="padding:var(--space-2xl);">
                    <div style="font-size:0.8rem; font-weight:600; text-transform:uppercase; letter-spacing:0.08em; color:var(--accent-gold); margin-bottom:var(--space-sm);">
                        <?= $isBn ? 'প্রতিষ্ঠানিক ব্রতবাক্য' : 'Institutional Credo' ?>
                    </div>
                    <blockquote style="font-size:1.15rem; font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-en-serif)' ?>; line-height:1.7; color:var(--text-main); margin-bottom:var(--space-md);">
                        <?= e(__('home.about.quote')) ?>
                    </blockquote>
                    <p style="font-size:0.88rem; color:var(--text-muted); line-height:1.65; margin-bottom:var(--space-md);">
                        <?= $isBn 
                            ? 'এসপিএস কোনো ব্যক্তিপূজা বা দলগত সংকীর্ণতায় বিশ্বাসী নয়। এটি শাশ্বত জ্ঞান ও নিঃস্বার্থ মানবসেবায় একনিষ্ঠ সার্বজনীন অঙ্গন।'
                            : 'SPS rejects personality cults and dogmatic sectarianism, functioning strictly as an egalitarian sanctuary for timeless wisdom and compassionate service.' ?>
                    </p>
                    <div style="display:flex; align-items:center; gap:var(--space-sm); border-top:1px solid var(--border-medium); padding-top:var(--space-sm);">
                        <img src="<?= asset('assets/images/brand/sps-logo.png') ?>" alt="SPS" width="34" height="24" style="object-fit:contain;">
                        <span style="font-size:0.82rem; font-weight:600; color:var(--accent-brown);">
                            <?= $isBn ? 'এসপিএস পরিচালনা পরিষদ ও গবেষণা সংসদ' : 'SPS Academic & Governing Council' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. Mission / Vision / Values (4 Pillars) -->
<section class="editorial-section editorial-section-alt" id="values">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-tag"><?= e(__('home.pillars.tag')) ?></span>
            <h2 class="section-title"><?= e(__('home.pillars.title')) ?></h2>
            <p class="section-subtitle"><?= e(__('home.pillars.subtitle')) ?></p>
        </div>

        <div class="grid-4">
            <!-- Pillar 1 -->
            <div class="pillar-card">
                <div class="pillar-num">PILLAR 01</div>
                <h3 class="pillar-title"><?= e(__('home.pillars.pillar1_title')) ?></h3>
                <p class="pillar-desc"><?= e(__('home.pillars.pillar1_desc')) ?></p>
            </div>
            <!-- Pillar 2 -->
            <div class="pillar-card">
                <div class="pillar-num">PILLAR 02</div>
                <h3 class="pillar-title"><?= e(__('home.pillars.pillar2_title')) ?></h3>
                <p class="pillar-desc"><?= e(__('home.pillars.pillar2_desc')) ?></p>
            </div>
            <!-- Pillar 3 -->
            <div class="pillar-card">
                <div class="pillar-num">PILLAR 03</div>
                <h3 class="pillar-title"><?= e(__('home.pillars.pillar3_title')) ?></h3>
                <p class="pillar-desc"><?= e(__('home.pillars.pillar3_desc')) ?></p>
            </div>
            <!-- Pillar 4 -->
            <div class="pillar-card">
                <div class="pillar-num">PILLAR 04</div>
                <h3 class="pillar-title"><?= e(__('home.pillars.pillar4_title')) ?></h3>
                <p class="pillar-desc"><?= e(__('home.pillars.pillar4_desc')) ?></p>
            </div>
        </div>
    </div>
</section>

<!-- 4. Current Activities -->
<section class="editorial-section" id="activities">
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:var(--space-2xl);">
            <div>
                <span class="section-tag"><?= e(__('home.activities.tag')) ?></span>
                <h2 class="section-title"><?= e(__('home.activities.title')) ?></h2>
                <p class="section-subtitle"><?= e(__('home.activities.subtitle')) ?></p>
            </div>
            <a href="<?= e(url('/activities', $currentLocale)) ?>" class="btn btn-secondary" style="display:none; @media(min-width:768px){display:inline-flex;}">
                <?= e(__('common.actions.view_all')) ?> →
            </a>
        </div>

        <div class="grid-4">
            <div class="card">
                <span class="badge badge-ongoing" style="margin-bottom:var(--space-xs);"><?= e(__('common.badges.ongoing')) ?></span>
                <h4 style="margin-bottom:var(--space-2xs); font-size:1.15rem;"><?= e(__('home.activities.item1_title')) ?></h4>
                <p style="font-size:0.88rem; color:var(--text-muted); margin-bottom:var(--space-sm);"><?= e(__('home.activities.item1_desc')) ?></p>
                <div style="font-size:0.78rem; font-weight:600; color:var(--accent-gold); border-top:1px solid var(--border-subtle); padding-top:var(--space-xs);">
                    <?= e(__('home.activities.item1_meta')) ?>
                </div>
            </div>

            <div class="card">
                <span class="badge badge-ongoing" style="margin-bottom:var(--space-xs);"><?= e(__('common.badges.ongoing')) ?></span>
                <h4 style="margin-bottom:var(--space-2xs); font-size:1.15rem;"><?= e(__('home.activities.item2_title')) ?></h4>
                <p style="font-size:0.88rem; color:var(--text-muted); margin-bottom:var(--space-sm);"><?= e(__('home.activities.item2_desc')) ?></p>
                <div style="font-size:0.78rem; font-weight:600; color:var(--accent-gold); border-top:1px solid var(--border-subtle); padding-top:var(--space-xs);">
                    <?= e(__('home.activities.item2_meta')) ?>
                </div>
            </div>

            <div class="card">
                <span class="badge badge-ongoing" style="margin-bottom:var(--space-xs);"><?= e(__('common.badges.ongoing')) ?></span>
                <h4 style="margin-bottom:var(--space-2xs); font-size:1.15rem;"><?= e(__('home.activities.item3_title')) ?></h4>
                <p style="font-size:0.88rem; color:var(--text-muted); margin-bottom:var(--space-sm);"><?= e(__('home.activities.item3_desc')) ?></p>
                <div style="font-size:0.78rem; font-weight:600; color:var(--accent-gold); border-top:1px solid var(--border-subtle); padding-top:var(--space-xs);">
                    <?= e(__('home.activities.item3_meta')) ?>
                </div>
            </div>

            <div class="card">
                <span class="badge badge-scholarly" style="margin-bottom:var(--space-xs);"><?= e(__('common.badges.scholarly')) ?></span>
                <h4 style="margin-bottom:var(--space-2xs); font-size:1.15rem;"><?= e(__('home.activities.item4_title')) ?></h4>
                <p style="font-size:0.88rem; color:var(--text-muted); margin-bottom:var(--space-sm);"><?= e(__('home.activities.item4_desc')) ?></p>
                <div style="font-size:0.78rem; font-weight:600; color:var(--accent-gold); border-top:1px solid var(--border-subtle); padding-top:var(--space-xs);">
                    <?= e(__('home.activities.item4_meta')) ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 5. Targeted Projects -->
<section class="editorial-section editorial-section-alt" id="projects">
    <div class="container">
        <div class="section-header">
            <span class="section-tag"><?= e(__('home.projects.tag')) ?></span>
            <h2 class="section-title"><?= e(__('home.projects.title')) ?></h2>
            <p class="section-subtitle"><?= e(__('home.projects.subtitle')) ?></p>
        </div>

        <div class="grid-3">
            <!-- Project 1 -->
            <div class="project-card">
                <div class="project-header">
                    <span class="badge badge-ongoing"><?= e(__('common.badges.ongoing')) ?></span>
                    <span style="font-size:0.8rem; color:var(--accent-gold); font-weight:600;"><?= e(__('home.projects.project1_category')) ?></span>
                </div>
                <h3 class="project-title"><?= e(__('home.projects.project1_title')) ?></h3>
                <p style="font-size:0.9rem; color:var(--text-muted); line-height:1.6; margin-top:var(--space-xs); margin-bottom:auto;">
                    <?= e(__('home.projects.project1_desc')) ?>
                </p>
                <div class="project-metrics">
                    <div>
                        <span class="metric-label"><?= e(__('home.projects.project1_budget')) ?></span>
                    </div>
                    <div>
                        <span class="metric-label"><?= e(__('home.projects.project1_spent')) ?></span>
                    </div>
                    <div style="grid-column: span 2; padding-top:4px; border-top:1px dashed var(--border-medium);">
                        <span class="metric-value"><?= e(__('home.projects.project1_impact')) ?></span>
                    </div>
                </div>
                <a href="<?= e(url('/activities', $currentLocale)) ?>" class="btn btn-secondary btn-sm" style="width:100%; margin-top:var(--space-xs);">
                    <?= e(__('common.actions.read_more')) ?>
                </a>
            </div>

            <!-- Project 2 -->
            <div class="project-card">
                <div class="project-header">
                    <span class="badge badge-ongoing"><?= e(__('common.badges.ongoing')) ?></span>
                    <span style="font-size:0.8rem; color:var(--accent-gold); font-weight:600;"><?= e(__('home.projects.project2_category')) ?></span>
                </div>
                <h3 class="project-title"><?= e(__('home.projects.project2_title')) ?></h3>
                <p style="font-size:0.9rem; color:var(--text-muted); line-height:1.6; margin-top:var(--space-xs); margin-bottom:auto;">
                    <?= e(__('home.projects.project2_desc')) ?>
                </p>
                <div class="project-metrics">
                    <div>
                        <span class="metric-label"><?= e(__('home.projects.project2_budget')) ?></span>
                    </div>
                    <div>
                        <span class="metric-label"><?= e(__('home.projects.project2_spent')) ?></span>
                    </div>
                    <div style="grid-column: span 2; padding-top:4px; border-top:1px dashed var(--border-medium);">
                        <span class="metric-value"><?= e(__('home.projects.project2_impact')) ?></span>
                    </div>
                </div>
                <a href="<?= e(url('/activities', $currentLocale)) ?>" class="btn btn-secondary btn-sm" style="width:100%; margin-top:var(--space-xs);">
                    <?= e(__('common.actions.read_more')) ?>
                </a>
            </div>

            <!-- Project 3 -->
            <div class="project-card">
                <div class="project-header">
                    <span class="badge badge-planned"><?= e(__('common.badges.planned')) ?></span>
                    <span style="font-size:0.8rem; color:var(--accent-gold); font-weight:600;"><?= e(__('home.projects.project3_category')) ?></span>
                </div>
                <h3 class="project-title"><?= e(__('home.projects.project3_title')) ?></h3>
                <p style="font-size:0.9rem; color:var(--text-muted); line-height:1.6; margin-top:var(--space-xs); margin-bottom:auto;">
                    <?= e(__('home.projects.project3_desc')) ?>
                </p>
                <div class="project-metrics">
                    <div>
                        <span class="metric-label"><?= e(__('home.projects.project3_budget')) ?></span>
                    </div>
                    <div>
                        <span class="metric-label"><?= e(__('home.projects.project3_spent')) ?></span>
                    </div>
                    <div style="grid-column: span 2; padding-top:4px; border-top:1px dashed var(--border-medium);">
                        <span class="metric-value"><?= e(__('home.projects.project3_impact')) ?></span>
                    </div>
                </div>
                <a href="<?= e(url('/activities', $currentLocale)) ?>" class="btn btn-secondary btn-sm" style="width:100%; margin-top:var(--space-xs);">
                    <?= e(__('common.actions.read_more')) ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- 6. Latest Updates Dispatch -->
<section class="editorial-section" id="updates">
    <div class="container">
        <div style="border-left: 3px solid var(--accent-saffron); padding-left: var(--space-lg); margin-bottom: var(--space-2xl);">
            <span class="section-tag"><?= $isBn ? 'সংবাদ ও বিজ্ঞপ্তি' : 'Latest Updates & Notices' ?></span>
            <h2 class="section-title" style="margin-bottom:0;"><?= $isBn ? 'প্রাতিষ্ঠানিক ঘোষণা ও বিবরণী' : 'Organizational Dispatches' ?></h2>
        </div>

        <div class="grid-3">
            <div class="card">
                <div style="font-size:0.82rem; color:var(--accent-gold); font-weight:600; margin-bottom:var(--space-xs);">
                    <?= $isBn ? '১৫ সেপ্টেম্বর ২০২৬ • প্রকাশনা ঘোষণা' : 'Sep 15, 2026 • Publication Release' ?>
                </div>
                <h4 style="margin-bottom:var(--space-xs); font-size:1.12rem;">
                    <?= $isBn ? 'উপনিষদ রত্নাবলী দ্বিভাষিক দ্বিতীয় মুদ্রণ প্রকাশিত' : 'Second Bilingual Edition of Principal Upanishads Released' ?>
                </h4>
                <p style="font-size:0.88rem; color:var(--text-muted); line-height:1.6;">
                    <?= $isBn ? 'গবেষক ও সাধারণ পাঠকদের জন্য প্রস্তুতকৃত এই গ্রন্থটিতে মূল সংস্কৃত, পদচ্ছেদ ও সহজবোধ্য ব্যাখ্যা সন্নিবেশিত হয়েছে।' : 'Prepared for scholars and thoughtful lay readers with critical word separation and explanatory exegesis.' ?>
                </p>
                <a href="<?= e(url('/library', $currentLocale)) ?>" style="font-size:0.84rem; color:var(--accent-saffron); font-weight:600;">
                    <?= e(__('common.actions.read_more')) ?> →
                </a>
            </div>

            <div class="card">
                <div style="font-size:0.82rem; color:var(--accent-gold); font-weight:600; margin-bottom:var(--space-xs);">
                    <?= $isBn ? '০৮ সেপ্টেম্বর ২০২৬ • সেবামূলক প্রতিবেদন' : 'Sep 08, 2026 • Seva Dispatch' ?>
                </div>
                <h4 style="margin-bottom:var(--space-xs); font-size:1.12rem;">
                    <?= $isBn ? 'কুড়িগ্রাম চর অঞ্চলে তৃতীয় পাঠশালার শিক্ষা উপকরণ বিতরণ' : 'Educational Supplies Distributed at Kurigram 3rd Vidyapeeth' ?>
                </h4>
                <p style="font-size:0.88rem; color:var(--text-muted); line-height:1.6;">
                    <?= $isBn ? '১৮০ জন শিক্ষার্থীর মাঝে নতুন খাতা, কলম ও প্রাথমিক পুষ্টিকর খাদ্য সহায়তা পৌঁছে দেওয়া হয়েছে।' : '180 students received notebooks, writing essentials, and supplementary nutritional sustenance.' ?>
                </p>
                <a href="<?= e(url('/activities', $currentLocale)) ?>" style="font-size:0.84rem; color:var(--accent-saffron); font-weight:600;">
                    <?= e(__('common.actions.read_more')) ?> →
                </a>
            </div>

            <div class="card">
                <div style="font-size:0.82rem; color:var(--accent-gold); font-weight:600; margin-bottom:var(--space-xs);">
                    <?= $isBn ? '০১ সেপ্টেম্বর ২০২৬ • আর্থিক অডিট' : 'Sep 01, 2026 • Financial Audit' ?>
                </div>
                <h4 style="margin-bottom:var(--space-xs); font-size:1.12rem;">
                    <?= $isBn ? 'আগস্ট ২০২৬ মাসিক আর্থিক বিবরণী প্রকাশ' : 'August 2026 Verified Financial Statement Published' ?>
                </h4>
                <p style="font-size:0.88rem; color:var(--text-muted); line-height:1.6;">
                    <?= $isBn ? 'গত মাসের মোট অনুদান গ্রহণ ও প্রকল্পভিত্তিক ব্যয়ের সম্পূর্ণ তালিকা ওয়েবসাইটে উন্মুক্ত করা হয়েছে।' : 'Complete monthly ledger of donations received and project disbursements made fully accessible on the transparency portal.' ?>
                </p>
                <a href="<?= e(url('/transparency', $currentLocale)) ?>" style="font-size:0.84rem; color:var(--accent-saffron); font-weight:600;">
                    <?= e(__('common.actions.read_more')) ?> →
                </a>
            </div>
        </div>
    </div>
</section>

<!-- 7. Scripture & Philosophy Spotlight -->
<section class="editorial-section editorial-section-alt" id="scripture">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-tag"><?= e(__('home.scripture.tag')) ?></span>
            <h2 class="section-title"><?= e(__('home.scripture.title')) ?></h2>
            <p class="section-subtitle"><?= e(__('home.scripture.subtitle')) ?></p>
        </div>

        <div class="scripture-card container-reading">
            <div class="scripture-header-row">
                <div class="scripture-reference"><?= e(__('home.scripture.book')) ?></div>
                <span class="badge badge-scholarly"><?= e(__('home.scripture.verse_no')) ?></span>
            </div>

            <div style="text-align:center; padding:var(--space-md) 0;">
                <div class="sanskrit-verse">
                    <?= nl2br(e(__('home.scripture.sanskrit_devanagari'))) ?>
                </div>
                <?php if ($isBn): ?>
                <div class="sanskrit-bengali" style="margin-top:var(--space-sm); font-size:1.15rem; color:var(--text-secondary);">
                    <?= nl2br(e(__('home.scripture.sanskrit_bengali'))) ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="scripture-translation-box">
                <h4 style="font-size:0.88rem; text-transform:uppercase; letter-spacing:0.08em; color:var(--accent-gold); margin-bottom:var(--space-2xs);">
                    <?= $isBn ? 'সরলার্থ ও প্রামাণ্য অনুবাদ' : 'Authoritative Translation' ?>
                </h4>
                <p style="font-size:1.08rem; line-height:1.75; color:var(--text-main); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-en-serif)' ?>;">
                    “<?= e(__('home.scripture.translation')) ?>”
                </p>
            </div>

            <div class="scripture-exegesis-box">
                <strong style="color:var(--accent-brown); display:block; margin-bottom:4px; font-size:0.88rem;">
                    <?= $isBn ? 'দার্শনিক তাৎপর্য ও মনস্তাত্ত্বিক প্রাসঙ্গিকতা' : 'Philosophical Exegesis & Contemporary Relevance' ?>
                </strong>
                <p style="margin-bottom:0; font-size:0.92rem; color:var(--text-body);">
                    <?= e(__('home.scripture.exegesis')) ?>
                </p>
            </div>

            <div style="margin-top:var(--space-lg); text-align:center;">
                <a href="<?= e(url('/knowledge', $currentLocale)) ?>" class="btn btn-secondary btn-sm">
                    <?= e(__('home.scripture.view_scriptures')) ?> →
                </a>
            </div>
        </div>
    </div>
</section>

<!-- 8. From the SPS Community / Member Blog -->
<section class="editorial-section" id="blog">
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:var(--space-2xl);">
            <div>
                <span class="section-tag"><?= e(__('home.blog.tag')) ?></span>
                <h2 class="section-title"><?= e(__('home.blog.title')) ?></h2>
                <p class="section-subtitle"><?= e(__('home.blog.subtitle')) ?></p>
            </div>
            <a href="<?= e(url('/blog', $currentLocale)) ?>" class="btn btn-secondary">
                <?= e(__('home.blog.view_all_posts')) ?> →
            </a>
        </div>

        <div class="grid-3">
            <!-- Article 1 -->
            <article class="article-card">
                <div class="article-card-header">
                    <span class="badge badge-neutral"><?= e(__('home.blog.post1_category')) ?></span>
                    <span><?= e(__('home.blog.post1_time')) ?></span>
                </div>
                <div class="article-card-body">
                    <h3 class="article-card-title">
                        <a href="<?= e(url('/blog', $currentLocale)) ?>"><?= e(__('home.blog.post1_title')) ?></a>
                    </h3>
                    <p class="article-card-excerpt">
                        <?= e(__('home.blog.post1_excerpt')) ?>
                    </p>
                </div>
                <div class="article-card-footer">
                    <div class="author-meta">
                        <div class="author-avatar">স</div>
                        <div>
                            <div style="font-weight:600; color:var(--text-main); font-size:0.82rem;"><?= e(__('home.blog.post1_author')) ?></div>
                            <div style="font-size:0.72rem; color:var(--text-faint);"><?= e(__('home.blog.post1_role')) ?></div>
                        </div>
                    </div>
                    <span style="font-size:0.8rem; color:var(--accent-saffron); font-weight:600;"><?= e(__('common.actions.read_more')) ?></span>
                </div>
            </article>

            <!-- Article 2 -->
            <article class="article-card">
                <div class="article-card-header">
                    <span class="badge badge-neutral"><?= e(__('home.blog.post2_category')) ?></span>
                    <span><?= e(__('home.blog.post2_time')) ?></span>
                </div>
                <div class="article-card-body">
                    <h3 class="article-card-title">
                        <a href="<?= e(url('/blog', $currentLocale)) ?>"><?= e(__('home.blog.post2_title')) ?></a>
                    </h3>
                    <p class="article-card-excerpt">
                        <?= e(__('home.blog.post2_excerpt')) ?>
                    </p>
                </div>
                <div class="article-card-footer">
                    <div class="author-meta">
                        <div class="author-avatar">অ</div>
                        <div>
                            <div style="font-weight:600; color:var(--text-main); font-size:0.82rem;"><?= e(__('home.blog.post2_author')) ?></div>
                            <div style="font-size:0.72rem; color:var(--text-faint);"><?= e(__('home.blog.post2_role')) ?></div>
                        </div>
                    </div>
                    <span style="font-size:0.8rem; color:var(--accent-saffron); font-weight:600;"><?= e(__('common.actions.read_more')) ?></span>
                </div>
            </article>

            <!-- Article 3 -->
            <article class="article-card">
                <div class="article-card-header">
                    <span class="badge badge-neutral"><?= e(__('home.blog.post3_category')) ?></span>
                    <span><?= e(__('home.blog.post3_time')) ?></span>
                </div>
                <div class="article-card-body">
                    <h3 class="article-card-title">
                        <a href="<?= e(url('/blog', $currentLocale)) ?>"><?= e(__('home.blog.post3_title')) ?></a>
                    </h3>
                    <p class="article-card-excerpt">
                        <?= e(__('home.blog.post3_excerpt')) ?>
                    </p>
                </div>
                <div class="article-card-footer">
                    <div class="author-meta">
                        <div class="author-avatar">স</div>
                        <div>
                            <div style="font-weight:600; color:var(--text-main); font-size:0.82rem;"><?= e(__('home.blog.post3_author')) ?></div>
                            <div style="font-size:0.72rem; color:var(--text-faint);"><?= e(__('home.blog.post3_role')) ?></div>
                        </div>
                    </div>
                    <span style="font-size:0.8rem; color:var(--accent-saffron); font-weight:600;"><?= e(__('common.actions.read_more')) ?></span>
                </div>
            </article>
        </div>

        <div style="margin-top:var(--space-2xl); text-align:center; background-color:var(--bg-subtle); padding:var(--space-lg); border-radius:var(--radius-sm); border:1px solid var(--border-subtle);">
            <span style="font-size:0.95rem; color:var(--text-body); margin-right:var(--space-md);">
                <?= $isBn ? 'আপনিও কি দর্শন, শাস্ত্র বা সমাজচিন্তা নিয়ে লিখতে চান?' : 'Interested in sharing research, philosophy, or field dispatches?' ?>
            </span>
            <a href="<?= e(url('/get-involved', $currentLocale)) ?>" class="btn btn-primary btn-sm">
                <?= e(__('home.blog.submit_post')) ?>
            </a>
        </div>
    </div>
</section>

<!-- 9. Library Highlights -->
<section class="editorial-section editorial-section-alt" id="library">
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:var(--space-2xl);">
            <div>
                <span class="section-tag"><?= e(__('home.library.tag')) ?></span>
                <h2 class="section-title"><?= e(__('home.library.title')) ?></h2>
                <p class="section-subtitle"><?= e(__('home.library.subtitle')) ?></p>
            </div>
            <a href="<?= e(url('/library', $currentLocale)) ?>" class="btn btn-secondary">
                <?= e(__('home.library.enter_library')) ?> →
            </a>
        </div>

        <div class="grid-3">
            <!-- Book 1 -->
            <div class="book-card">
                <div class="book-cover">
                    <div style="font-size:0.55rem; color:var(--accent-gold); font-weight:600; text-transform:uppercase; margin-bottom:4px;">SPS Publication</div>
                    <div class="book-cover-title"><?= e(__('home.library.book1_title')) ?></div>
                    <div style="font-size:0.6rem; color:var(--text-muted); margin-top:auto;"><?= e(__('home.library.book1_author')) ?></div>
                </div>
                <div class="book-info">
                    <div>
                        <span class="badge badge-scholarly" style="margin-bottom:var(--space-2xs);"><?= e(__('home.library.book1_category')) ?></span>
                        <h4 class="book-title"><?= e(__('home.library.book1_title')) ?></h4>
                        <div class="book-author"><?= e(__('home.library.book1_author')) ?></div>
                        <div class="book-meta">
                            <span><?= e(__('home.library.book1_pages')) ?></span>
                            <span>•</span>
                            <span style="color:var(--status-success);"><?= $isBn ? 'অনলাইন ও মুদ্রিত' : 'Print & Online' ?></span>
                        </div>
                    </div>
                    <div style="display:flex; gap:var(--space-xs); margin-top:var(--space-xs);">
                        <a href="<?= e(url('/library', $currentLocale)) ?>" class="btn btn-secondary btn-sm" style="flex:1;">
                            <?= e(__('common.actions.read_online')) ?>
                        </a>
                        <a href="<?= e(url('/library', $currentLocale)) ?>" class="btn btn-primary btn-sm" style="flex:1;">
                            <?= e(__('common.actions.order_book')) ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Book 2 -->
            <div class="book-card">
                <div class="book-cover" style="background-color:#F5EFE6;">
                    <div style="font-size:0.55rem; color:var(--accent-saffron); font-weight:600; text-transform:uppercase; margin-bottom:4px;">Research Edition</div>
                    <div class="book-cover-title"><?= e(__('home.library.book2_title')) ?></div>
                    <div style="font-size:0.6rem; color:var(--text-muted); margin-top:auto;"><?= e(__('home.library.book2_author')) ?></div>
                </div>
                <div class="book-info">
                    <div>
                        <span class="badge badge-scholarly" style="margin-bottom:var(--space-2xs);"><?= e(__('home.library.book2_category')) ?></span>
                        <h4 class="book-title"><?= e(__('home.library.book2_title')) ?></h4>
                        <div class="book-author"><?= e(__('home.library.book2_author')) ?></div>
                        <div class="book-meta">
                            <span><?= e(__('home.library.book2_pages')) ?></span>
                            <span>•</span>
                            <span style="color:var(--status-info);"><?= $isBn ? 'মুক্ত ডিজিটাল সংস্করণ' : 'Free Open Access' ?></span>
                        </div>
                    </div>
                    <div style="display:flex; gap:var(--space-xs); margin-top:var(--space-xs);">
                        <a href="<?= e(url('/library', $currentLocale)) ?>" class="btn btn-secondary btn-sm" style="width:100%;">
                            <?= e(__('common.actions.read_online')) ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Book 3 -->
            <div class="book-card">
                <div class="book-cover" style="background-color:#F7F4EB;">
                    <div style="font-size:0.55rem; color:var(--accent-gold); font-weight:600; text-transform:uppercase; margin-bottom:4px;">Anthology</div>
                    <div class="book-cover-title"><?= e(__('home.library.book3_title')) ?></div>
                    <div style="font-size:0.6rem; color:var(--text-muted); margin-top:auto;"><?= e(__('home.library.book3_author')) ?></div>
                </div>
                <div class="book-info">
                    <div>
                        <span class="badge badge-scholarly" style="margin-bottom:var(--space-2xs);"><?= e(__('home.library.book3_category')) ?></span>
                        <h4 class="book-title"><?= e(__('home.library.book3_title')) ?></h4>
                        <div class="book-author"><?= e(__('home.library.book3_author')) ?></div>
                        <div class="book-meta">
                            <span><?= e(__('home.library.book3_pages')) ?></span>
                            <span>•</span>
                            <span style="color:var(--accent-saffron);"><?= $isBn ? 'মুদ্রিত প্রকাশনা' : 'Print Available' ?></span>
                        </div>
                    </div>
                    <div style="display:flex; gap:var(--space-xs); margin-top:var(--space-xs);">
                        <a href="<?= e(url('/library', $currentLocale)) ?>" class="btn btn-primary btn-sm" style="width:100%;">
                            <?= e(__('common.actions.order_book')) ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 10. Financial Transparency Overview -->
<section class="editorial-section" id="transparency">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-tag"><?= e(__('home.transparency.tag')) ?></span>
            <h2 class="section-title"><?= e(__('home.transparency.title')) ?></h2>
            <p class="section-subtitle"><?= e(__('home.transparency.subtitle')) ?></p>
        </div>

        <!-- 3 Big Aggregated Numbers -->
        <div class="transparency-summary-grid">
            <div class="transparency-card">
                <span style="font-size:0.85rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.04em;">
                    <?= e(__('home.transparency.total_received_label')) ?>
                </span>
                <div class="transparency-num" style="color:var(--status-success);">
                    <?= e(__('home.transparency.total_received_amount')) ?>
                </div>
                <div style="font-size:0.75rem; color:var(--text-faint); margin-top:4px;">
                    <?= $isBn ? 'যাচাইকৃত ব্যাংক ও ডিজিটাল চ্যানেলে প্রাপ্ত' : 'Verified via banking & electronic receipts' ?>
                </div>
            </div>

            <div class="transparency-card">
                <span style="font-size:0.85rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.04em;">
                    <?= e(__('home.transparency.total_spent_label')) ?>
                </span>
                <div class="transparency-num" style="color:var(--accent-saffron);">
                    <?= e(__('home.transparency.total_spent_amount')) ?>
                </div>
                <div style="font-size:0.75rem; color:var(--text-faint); margin-top:4px;">
                    <?= $isBn ? 'প্রত্যক্ষ সেবামূলক প্রকল্প ও মানবিক সহায়তা' : 'Direct humanitarian projects & field operations' ?>
                </div>
            </div>

            <div class="transparency-card transparency-card-highlight">
                <span style="font-size:0.85rem; color:var(--accent-brown); text-transform:uppercase; letter-spacing:0.04em; font-weight:600;">
                    <?= e(__('home.transparency.balance_label')) ?>
                </span>
                <div class="transparency-num" style="color:var(--accent-gold-hover);">
                    <?= e(__('home.transparency.balance_amount')) ?>
                </div>
                <div style="font-size:0.75rem; color:var(--text-muted); margin-top:4px;">
                    <?= $isBn ? 'ভবিষ্যৎ জরুরি ত্রাণ ও চলমান কার্যক্রমের জন্য রক্ষিত' : 'Dedicated emergency reserve & ongoing commitments' ?>
                </div>
            </div>
        </div>

        <!-- Fund Distribution Breakdown -->
        <div class="transparency-bars">
            <h4 style="font-size:1.05rem; margin-bottom:var(--space-md); color:var(--text-main);">
                <?= $isBn ? 'তহবিল বণ্টন ও ব্যবহারের অনুপাত' : 'Fund Allocation & Expenditure Ratio' ?>
            </h4>

            <div class="bar-row">
                <div class="bar-label-group">
                    <span><?= e(__('home.transparency.fund_education')) ?></span>
                    <span style="font-weight:600;">35%</span>
                </div>
                <div class="bar-track">
                    <div class="bar-fill" style="width:35%;"></div>
                </div>
            </div>

            <div class="bar-row">
                <div class="bar-label-group">
                    <span><?= e(__('home.transparency.fund_health')) ?></span>
                    <span style="font-weight:600;">28%</span>
                </div>
                <div class="bar-track">
                    <div class="bar-fill bar-fill-green" style="width:28%;"></div>
                </div>
            </div>

            <div class="bar-row">
                <div class="bar-label-group">
                    <span><?= e(__('home.transparency.fund_scripture')) ?></span>
                    <span style="font-weight:600;">24%</span>
                </div>
                <div class="bar-track">
                    <div class="bar-fill bar-fill-gold" style="width:24%;"></div>
                </div>
            </div>

            <div class="bar-row">
                <div class="bar-label-group">
                    <span><?= e(__('home.transparency.fund_ops')) ?></span>
                    <span style="font-weight:600;">13%</span>
                </div>
                <div class="bar-track">
                    <div class="bar-fill bar-fill-brown" style="width:13%;"></div>
                </div>
            </div>
        </div>

        <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:var(--space-md); background-color:var(--bg-subtle); padding:var(--space-md) var(--space-lg); border-radius:var(--radius-sm); border:1px solid var(--border-subtle);">
            <div style="font-size:0.88rem; color:var(--text-muted); max-width:720px;">
                <em><?= e(__('home.transparency.sheets_note')) ?></em>
            </div>
            <a href="<?= e(url('/transparency', $currentLocale)) ?>" class="btn btn-secondary btn-sm">
                <?= e(__('home.transparency.view_transparency')) ?> →
            </a>
        </div>
    </div>
</section>

<!-- 11. Join SPS Callout Banner -->
<section class="editorial-section" id="join" style="padding-bottom:var(--space-4xl);">
    <div class="container">
        <div class="join-banner">
            <h2 class="join-banner-title"><?= e(__('home.join.title')) ?></h2>
            <p class="join-banner-desc"><?= e(__('home.join.desc')) ?></p>
            <div class="join-banner-ctas">
                <a href="<?= e(url('/get-involved', $currentLocale)) ?>" class="btn btn-primary btn-lg">
                    <?= e(__('home.join.cta_membership')) ?>
                </a>
                <a href="<?= e(url('/get-involved', $currentLocale)) ?>" class="btn btn-gold btn-lg">
                    <?= e(__('home.join.cta_volunteer')) ?>
                </a>
                <a href="<?= e(url('/contact', $currentLocale)) ?>" class="btn btn-secondary btn-lg" style="border-color:rgba(255,255,255,0.3); color:#FFFFFF !important;">
                    <?= e(__('home.join.cta_inquire')) ?>
                </a>
            </div>
        </div>
    </div>
</section>
