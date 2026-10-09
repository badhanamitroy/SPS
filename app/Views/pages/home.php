<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$sections = $sections ?? [];
$isSectionActive = function(string $key) use ($sections) {
    if (empty($sections)) {
        return true;
    }
    return !empty($sections[$key]['enabled']);
};

$stats = $statistics ?? [];
$scripture = $featuredScripture ?? [];
$transparencyData = $transparency ?? [];
$socials = $socialLinks ?? [];
$liveActivities = $activities ?? [];
$books = $featuredBooks ?? [];
$blogs = $featuredBlogs ?? [];
?>

<!-- ==========================================
     01. HERO SECTION (Sanatan Philosophy and Scripture)
     ========================================== -->
<?php if ($isSectionActive('hero')): ?>
<section class="hero-section" id="hero" style="position:relative; overflow:hidden;">
    <div class="container hero-inner">
        <div class="hero-content">
            <h1 class="hero-title" style="font-size:clamp(2.1rem, 4vw, 3.4rem); line-height:1.2; margin-top:0; margin-bottom:var(--space-2xs); font-family:var(--font-display, serif);">
                Sanatan Philosophy and Scripture
            </h1>

            <div class="hero-motto" style="font-size:clamp(1.1rem, 2vw, 1.3rem); font-weight:700; color:var(--accent-saffron); margin-bottom:var(--space-md); letter-spacing:0.02em;">
                সনাতনী ঐক্য, প্রচার ও কল্যাণে অবিচল
            </div>

            <p class="hero-description" style="font-size:1.1rem; line-height:1.75; color:var(--text-body); max-width:620px; margin-bottom:var(--space-xl);">
                <?= $isBn 
                    ? 'সনাতন দর্শন, শাস্ত্র, গবেষণা ও মানবসেবাকে একত্র করে জ্ঞানচর্চা, ঐতিহ্য সংরক্ষণ এবং সমাজকল্যাণে কাজ করে SPS।'
                    : 'Uniting authentic Sanatan philosophy, scriptural exegesis, research, and selfless seva for knowledge preservation and societal welfare.' ?>
            </p>

            <div class="hero-ctas" style="display:flex; flex-wrap:wrap; gap:var(--space-md); align-items:center;">
                <a href="<?= e(url('/library', $currentLocale)) ?>" class="btn btn-primary btn-lg" style="gap:8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                    </svg>
                    <span><?= $isBn ? 'শাস্ত্রাগার দেখুন' : 'Explore Library' ?></span>
                </a>
                <a href="<?= e(url('/activities', $currentLocale)) ?>" class="btn btn-secondary btn-lg" style="gap:8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polygon points="10 8 16 12 10 16 10 8"></polygon>
                    </svg>
                    <span><?= $isBn ? 'আমাদের কার্যক্রম' : 'Our Activities' ?></span>
                </a>
            </div>
        </div>

        <!-- Ambient Brahma Sutra Visual Panel -->
        <div class="hero-visual">
            <div class="hero-panel" style="background:var(--bg-surface); border:1px solid var(--border-medium); border-radius:var(--radius-md); padding:var(--space-2xl); box-shadow:var(--shadow-md); position:relative; overflow:hidden;">
                <!-- Decorative Watermark Background -->
                <div style="position:absolute; right:-20px; bottom:-30px; font-size:9rem; font-family:serif; color:rgba(212,175,55,0.06); line-height:1; pointer-events:none; user-select:none;">
                    ॐ
                </div>

                <div class="panel-verse-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-md); border-bottom:1px solid var(--border-subtle); padding-bottom:var(--space-xs);">
                    <span class="panel-verse-tag" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:0.08em; color:var(--accent-saffron); font-weight:700;">
                        <?= $isBn ? 'শাশ্বত দর্শন ও ব্রত' : 'Philosophical Identity' ?>
                    </span>
                    <span class="badge badge-scholarly">
                        <?= $isBn ? 'ব্রহ্মসূত্র ১.১.১-২' : 'Brahma Sutra 1.1.1-2' ?>
                    </span>
                </div>

                <div style="margin: var(--space-md) 0;">
                    <div class="sanskrit-verse" style="font-size:1.25rem; font-weight:700; line-height:1.75; margin-bottom:var(--space-sm); color:var(--text-main); font-family:var(--font-display, serif);">
                        अथातो ब्रह्मजिज्ञासा ।<br>
                        जन्माद्यस्य यतः ॥
                    </div>
                    <?php if ($isBn): ?>
                    <p style="font-size:0.92rem; color:var(--text-body); line-height:1.65; margin-top:var(--space-xs); font-style:italic;">
                        "অতএব এখন ব্রহ্মজিজ্ঞাসার সময়। এই দৃশ্যমান নিখিল বিশ্বজগতের উৎপত্তি, স্থিতি ও লয় যাঁর থেকে..."
                    </p>
                    <?php else: ?>
                    <p style="font-size:0.9rem; color:var(--text-body); line-height:1.65; margin-top:var(--space-xs); font-style:italic;">
                        "Now therefore begins the deep inquiry into Brahman. From Whom originates, endures, and dissolves this cosmic expanse..."
                    </p>
                    <?php endif; ?>
                </div>

                <div style="border-top:1px solid var(--border-subtle); padding-top:var(--space-sm); display:flex; justify-content:space-between; align-items:center; font-size:0.82rem; color:var(--text-muted);">
                    <span><?= $isBn ? 'প্রস্থানত্রয়ী ও বেদান্ত দর্শন' : 'Vedanta & Prasthanatrayi' ?></span>
                    <a href="<?= e(url('/knowledge', $currentLocale)) ?>" style="color:var(--accent-saffron); font-weight:600; text-decoration:none;">
                        <?= $isBn ? 'দার্শনিক ভিত্তি জানুন →' : 'Read Exegesis →' ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- ==========================================
     02. QUICK IMPACT NUMBERS (Live Statistics)
     ========================================== -->
<?php if ($isSectionActive('stats')): ?>
<section class="editorial-section" id="stats" style="padding-top:var(--space-xl); padding-bottom:var(--space-xl); background:var(--bg-subtle); border-top:1px solid var(--border-subtle); border-bottom:1px solid var(--border-subtle);">
    <div class="container">
        <div class="stats-grid">
            <?php foreach ($stats as $st): ?>
                <div class="card stat-card" style="border:1px solid var(--border-medium); background:var(--bg-surface); border-radius:var(--radius-md); display:flex; align-items:center; gap:var(--space-md); transition:transform var(--transition-fast);">
                    <div class="stat-icon-box" style="font-size:1.8rem; width:44px; height:44px; border-radius:10px; background:rgba(198,90,30,0.08); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <?= e($st['icon'] ?? '📊') ?>
                    </div>
                    <div>
                        <div class="stat-num-val" style="font-size:1.85rem; font-weight:800; color:var(--accent-saffron); line-height:1.1; font-family:var(--font-display, serif);">
                            <?= e($isBn ? ($st['number_bn'] ?? $st['number']) : $st['number']) ?>
                        </div>
                        <div class="stat-num-lbl" style="font-size:0.86rem; color:var(--text-muted); font-weight:600; margin-top:3px;">
                            <?= e($isBn ? $st['label_bn'] : $st['label_en']) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- ==========================================
     03. WHAT IS SPS? (SPS পরিচিতি — compact version)
     ========================================== -->
<?php if ($isSectionActive('about')): ?>
<section class="editorial-section" id="about">
    <div class="container">
        <div class="about-grid" style="display:grid; grid-template-columns:1.1fr 0.9fr; gap:var(--space-3xl); align-items:center;">
            <div>
                <span class="section-tag"><?= $isBn ? 'SPS পরিচিতি' : 'About SPS' ?></span>
                <h2 class="section-title" style="font-size:clamp(1.7rem, 2.8vw, 2.3rem); line-height:1.3; margin-bottom:var(--space-md);">
                    <?= $isBn 
                        ? 'শাশ্বত জ্ঞান ও সমকালীন মানবিক কর্মের মিলনস্থল' 
                        : 'Where Timeless Wisdom Meets Contemporary Humanitarian Action' ?>
                </h2>
                
                <p class="lead" style="font-size:1.05rem; line-height:1.75; color:var(--text-body); margin-bottom:var(--space-lg);">
                    <?= $isBn 
                        ? 'সনাতন দর্শন, বেদান্ত, উপনিষদ, গীতা ও ভারতীয় জ্ঞানপরম্পরার প্রামাণিক উৎস অনুসন্ধান, সংরক্ষণ ও সহজবোধ্য উপস্থাপনার পাশাপাশি শিক্ষা, স্বাস্থ্য ও মানবিক সেবায় কাজ করে SPS।'
                        : 'Sanatan Philosophy and Scripture (SPS) is an institutional platform dedicated to preserving Vedic treatises, Upanishads, and classical Indian thought, while translating timeless philosophical ethics into direct education, healthcare, and social welfare.' ?>
                </p>

                <div class="about-features" style="display:flex; flex-direction:column; gap:var(--space-md); margin-bottom:var(--space-xl);">
                    <div style="display:flex; gap:12px; align-items:flex-start;">
                        <span style="color:var(--status-success); font-weight:800; font-size:1.1rem; background:rgba(46,125,50,0.1); width:24px; height:24px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0;">✓</span>
                        <div>
                            <strong style="color:var(--text-main); font-size:0.95rem;"><?= $isBn ? 'প্রামাণিক ও অবিকৃত শাস্ত্রজ্ঞান:' : 'Authentic Canonical Scholarship:' ?></strong>
                            <span style="color:var(--text-muted); font-size:0.9rem;"> <?= $isBn ? 'কোনো অপব্যাখ্যা নয়, মূল সংস্কৃত ও দার্শনিক বিশ্লেষণের সমন্বয়ে বিশুদ্ধ শাস্ত্র প্রচার।' : 'Direct fidelity to primary Sanskrit recensions without sectarian distortion.' ?></span>
                        </div>
                    </div>
                    <div style="display:flex; gap:12px; align-items:flex-start;">
                        <span style="color:var(--status-success); font-weight:800; font-size:1.1rem; background:rgba(46,125,50,0.1); width:24px; height:24px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0;">✓</span>
                        <div>
                            <strong style="color:var(--text-main); font-size:0.95rem;"><?= $isBn ? 'নিঃস্বার্থ সামাজিক দায়বদ্ধতা:' : 'Direct Humanitarian Accountability:' ?></strong>
                            <span style="color:var(--text-muted); font-size:0.9rem;"> <?= $isBn ? 'পাঠশালা, ভ্রাম্যমাণ স্বাস্থ্য ক্যাম্প ও দুর্যোগকালীন ত্রাণে সরাসরি সেবামূলক উপস্থিতি।' : 'Vidyapeeth free schools, mobile clinic camps, and emergency flood and medical relief.' ?></span>
                        </div>
                    </div>
                </div>

                <div>
                    <a href="<?= e(url('/about', $currentLocale)) ?>" class="btn btn-secondary" style="font-weight:600;">
                        <span><?= $isBn ? 'SPS সম্পর্কে বিস্তারিত জানুন' : 'Learn More About SPS' ?></span>
                        <span style="margin-left:6px;">→</span>
                    </a>
                </div>
            </div>

            <!-- Credo Card -->
            <div>
                <div class="card card-tinted" style="padding:var(--space-2xl); border-radius:var(--radius-md); border:1px solid var(--border-medium); background:linear-gradient(135deg, rgba(237,231,223,0.6) 0%, rgba(255,255,255,0.95) 100%);">
                    <div style="font-size:0.78rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--accent-gold); margin-bottom:var(--space-xs);">
                        <?= $isBn ? 'প্রাতিষ্ঠানিক ব্রতবাক্য' : 'Institutional Credo' ?>
                    </div>
                    <blockquote style="font-size:1.15rem; font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>; line-height:1.7; color:var(--text-main); margin-bottom:var(--space-md); border-left:3px solid var(--accent-saffron); padding-left:var(--space-md);">
                        <?= $isBn 
                            ? '“জ্ঞান যেখানে মুক্ত, সেবা সেখানে শর্তহীন। সনাতন ঐতিহ্যের আলোকবর্তিকা হাতে নিয়ে আর্তমানবতার সেবায় নিবেদিত এক মানবিক ঐক্যপীঠ।”'
                            : '“Where wisdom is liberated, service becomes unconditional. An egalitarian sanctuary carrying the torch of Sanatan ethics for universal welfare.”' ?>
                    </blockquote>
                    <p style="font-size:0.86rem; color:var(--text-muted); line-height:1.65; margin-bottom:var(--space-lg);">
                        <?= $isBn 
                            ? 'এসপিএস কোনো ব্যক্তিপূজা বা দলগত সংকীর্ণতায় বিশ্বাসী নয়। এটি শাশ্বত জ্ঞান ও নিঃস্বার্থ মানবসেবায় একনিষ্ঠ সার্বজনীন প্রাতিষ্ঠানিক অঙ্গন।'
                            : 'SPS rejects personality cults and sectarian dogma, operating strictly as an institutional platform for classical inquiry and accountable humanitarian service.' ?>
                    </p>
                    <div style="display:flex; align-items:center; gap:var(--space-sm); border-top:1px solid var(--border-medium); padding-top:var(--space-md);">
                        <img src="<?= asset('assets/images/brand/sps-logo.png') ?>" alt="SPS" width="38" height="26" style="object-fit:contain;">
                        <div>
                            <div style="font-size:0.84rem; font-weight:700; color:var(--accent-brown);">
                                <?= $isBn ? 'এসপিএস পরিচালনা ও গবেষণা সংসদ' : 'SPS Academic & Governing Council' ?>
                            </div>
                            <div style="font-size:0.75rem; color:var(--text-muted);">
                                <?= $isBn ? 'প্রতিষ্ঠিত ২০২০ • সনাতনী ঐক্য ও মানবকল্যাণ' : 'Est. 2020 • Unity, Propagation & Welfare' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- ==========================================
     04. WHAT WE DO (আমরা কী করি? — ৪টি Pillar)
     ========================================== -->
<?php if ($isSectionActive('pillars')): ?>
<section class="editorial-section editorial-section-alt" id="pillars" style="background:var(--bg-subtle);">
    <div class="container">
        <div class="section-header text-center" style="max-width:720px; margin:0 auto var(--space-3xl);">
            <span class="section-tag"><?= $isBn ? 'আমরা কী করি' : 'What We Do' ?></span>
            <h2 class="section-title"><?= $isBn ? 'SPS-এর ৪টি স্তম্ভ' : 'The Four Pillars of SPS' ?></h2>
            <p class="section-subtitle">
                <?= $isBn 
                    ? 'তাত্ত্বিক গবেষণা থেকে শুরু করে প্রান্তিক সমাজসেবা পর্যন্ত এসপিএস-এর সমন্বিত কর্মধারা' 
                    : 'The integrated institutional pillars bridging classical philosophical inquiry to frontline field service.' ?>
            </p>
        </div>

        <div class="pillars-grid">
            <!-- Pillar 1 -->
            <div class="card pillar-card" style="padding:var(--space-xl); border:1px solid var(--border-medium); background:var(--bg-surface); border-radius:var(--radius-md); display:flex; flex-direction:column;">
                <div style="font-size:0.75rem; font-weight:800; color:var(--accent-gold); letter-spacing:0.08em; margin-bottom:var(--space-2xs);">PILLAR 01</div>
                <div style="font-size:1.8rem; margin-bottom:var(--space-xs);">🔱</div>
                <h3 style="font-size:1.2rem; margin-bottom:var(--space-xs); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;">
                    <?= $isBn ? 'জ্ঞান ও দর্শন' : 'Knowledge & Philosophy' ?>
                </h3>
                <p style="font-size:0.9rem; color:var(--text-muted); line-height:1.65; margin-bottom:var(--space-lg); flex-grow:1;">
                    <?= $isBn 
                        ? 'শাস্ত্র, দর্শন ও ভারতীয় জ্ঞানপরম্পরার গবেষণা। বেদান্ত, গীতা ও উপনিষদের মূল সংস্কৃত ও প্রাঞ্জল বিশ্লেষণ।' 
                        : 'Scholarly research into Vedanta, Gita, and ancient Indian epistemic traditions with rigorous textual fidelity.' ?>
                </p>
                <a href="<?= e(url('/knowledge', $currentLocale)) ?>" style="color:var(--accent-saffron); font-size:0.86rem; font-weight:700; text-decoration:none;">
                    <?= $isBn ? 'আরও জানুন →' : 'Learn More →' ?>
                </a>
            </div>

            <!-- Pillar 2 -->
            <div class="card pillar-card" style="padding:var(--space-xl); border:1px solid var(--border-medium); background:var(--bg-surface); border-radius:var(--radius-md); display:flex; flex-direction:column;">
                <div style="font-size:0.75rem; font-weight:800; color:var(--accent-gold); letter-spacing:0.08em; margin-bottom:var(--space-2xs);">PILLAR 02</div>
                <div style="font-size:1.8rem; margin-bottom:var(--space-xs);">📜</div>
                <h3 style="font-size:1.2rem; margin-bottom:var(--space-xs); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;">
                    <?= $isBn ? 'শাস্ত্র সংরক্ষণ' : 'Scripture Preservation' ?>
                </h3>
                <p style="font-size:0.9rem; color:var(--text-muted); line-height:1.65; margin-bottom:var(--space-lg); flex-grow:1;">
                    <?= $isBn 
                        ? 'মূল উৎস, অনুবাদ, পাণ্ডুলিপি ও ডিজিটাল আর্কাইভ। সুরক্ষিত ই-বুক ক্যানভাস রিডারে শাস্ত্রীয় প্রকাশনা সংরক্ষণ।' 
                        : 'Facsimile scans, verified editions, and DRM-protected canvas e-books safeguarding sacred manuscripts.' ?>
                </p>
                <a href="<?= e(url('/library', $currentLocale)) ?>" style="color:var(--accent-saffron); font-size:0.86rem; font-weight:700; text-decoration:none;">
                    <?= $isBn ? 'আরও জানুন →' : 'Learn More →' ?>
                </a>
            </div>

            <!-- Pillar 3 -->
            <div class="card pillar-card" style="padding:var(--space-xl); border:1px solid var(--border-medium); background:var(--bg-surface); border-radius:var(--radius-md); display:flex; flex-direction:column;">
                <div style="font-size:0.75rem; font-weight:800; color:var(--accent-gold); letter-spacing:0.08em; margin-bottom:var(--space-2xs);">PILLAR 03</div>
                <div style="font-size:1.8rem; margin-bottom:var(--space-xs);">🤝</div>
                <h3 style="font-size:1.2rem; margin-bottom:var(--space-xs); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;">
                    <?= $isBn ? 'শিক্ষা ও মানবসেবা' : 'Education & Seva' ?>
                </h3>
                <p style="font-size:0.9rem; color:var(--text-muted); line-height:1.65; margin-bottom:var(--space-lg); flex-grow:1;">
                    <?= $isBn 
                        ? 'পাঠশালা, স্বাস্থ্যসেবা ও মানবিক সহায়তা। সুবিধাবঞ্চিত শিশুদের মুক্ত শিক্ষা ও অসহায় সনাতনীদের জরুরি চিকিৎসা।' 
                        : 'Free Vidyapeeth community schools, mobile medical camps, and direct emergency relief for the underprivileged.' ?>
                </p>
                <a href="<?= e(url('/activities', $currentLocale)) ?>" style="color:var(--accent-saffron); font-size:0.86rem; font-weight:700; text-decoration:none;">
                    <?= $isBn ? 'আরও জানুন →' : 'Learn More →' ?>
                </a>
            </div>

            <!-- Pillar 4 -->
            <div class="card pillar-card" style="padding:var(--space-xl); border:1px solid var(--border-medium); background:var(--bg-surface); border-radius:var(--radius-md); display:flex; flex-direction:column;">
                <div style="font-size:0.75rem; font-weight:800; color:var(--accent-gold); letter-spacing:0.08em; margin-bottom:var(--space-2xs);">PILLAR 04</div>
                <div style="font-size:1.8rem; margin-bottom:var(--space-xs);">🛕</div>
                <h3 style="font-size:1.2rem; margin-bottom:var(--space-xs); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;">
                    <?= $isBn ? 'সমাজ ও সংস্কৃতি' : 'Community & Heritage' ?>
                </h3>
                <p style="font-size:0.9rem; color:var(--text-muted); line-height:1.65; margin-bottom:var(--space-lg); flex-grow:1;">
                    <?= $isBn 
                        ? 'ঐতিহ্য, নৈতিকতা ও সামাজিক সচেতনতা। স্থানীয় মন্দির সংস্কার, অধিকার রক্ষা ও ভ্রাতৃত্ববোধ সুসংহতকরণ।' 
                        : 'Cultural revitalization, ethical awareness, heritage preservation, and collective solidarity.' ?>
                </p>
                <a href="<?= e(url('/about', $currentLocale)) ?>" style="color:var(--accent-saffron); font-size:0.86rem; font-weight:700; text-decoration:none;">
                    <?= $isBn ? 'আরও জানুন →' : 'Learn More →' ?>
                </a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- ==========================================
     05. LIVE ACTIVITIES SLIDER (চলমান কার্যক্রম)
     ========================================== -->
<?php if ($isSectionActive('activities')): ?>
<section class="editorial-section" id="activities" style="overflow:hidden;">
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:var(--space-md); margin-bottom:var(--space-2xl);">
            <div>
                <div style="display:inline-flex; align-items:center; gap:6px; margin-bottom:var(--space-2xs);">
                    <span style="display:inline-block; width:8px; height:8px; background:#10B981; border-radius:50%; box-shadow:0 0 8px rgba(16,185,129,0.8);"></span>
                    <span class="section-tag" style="margin-bottom:0; color:#059669; font-weight:700;"><?= $isBn ? '● LIVE ACTIVITIES' : '● LIVE ACTIVITIES' ?></span>
                </div>
                <h2 class="section-title" style="margin-bottom:4px;"><?= $isBn ? 'জ্ঞান থেকে কর্মে — SPS-এর চলমান কার্যক্রম' : 'From Wisdom to Action — SPS Live Activities' ?></h2>
                <p class="section-subtitle" style="margin-bottom:0;">
                    <?= $isBn 
                        ? 'প্রান্তিক পর্যায়ে মাঠকর্ম, মুক্ত পাঠশালা ও জরুরি চিকিৎসা সেবা কার্যক্রম' 
                        : 'Field dispatches, community Vidyapeeths, and emergency medical camps.' ?>
                </p>
            </div>

            <!-- Slider Control Arrows (Min 44x44px Touch Targets) -->
            <div style="display:flex; align-items:center; gap:8px;">
                <button type="button" id="activity-slide-prev" class="btn btn-ghost btn-sm slider-arrow-btn" aria-label="Previous Slide" style="border:1px solid var(--border-medium); border-radius:50%; width:44px; height:44px; min-width:44px; min-height:44px; padding:0; display:flex; align-items:center; justify-content:center; font-size:1.15rem;">
                    ←
                </button>
                <button type="button" id="activity-slide-next" class="btn btn-ghost btn-sm slider-arrow-btn" aria-label="Next Slide" style="border:1px solid var(--border-medium); border-radius:50%; width:44px; height:44px; min-width:44px; min-height:44px; padding:0; display:flex; align-items:center; justify-content:center; font-size:1.15rem;">
                    →
                </button>
                <a href="<?= e(url('/activities', $currentLocale)) ?>" class="btn btn-secondary btn-sm" style="margin-left:8px; min-height:44px;">
                    <?= $isBn ? 'সকল কার্যক্রম' : 'All Activities' ?> →
                </a>
            </div>
        </div>

        <!-- Horizontal Scroll / Slider Wrapper -->
        <div id="activities-slider" class="activities-slider-track" style="display:flex; gap:var(--space-xl); overflow-x:auto; scroll-behavior:smooth; padding-bottom:var(--space-lg); -webkit-overflow-scrolling:touch; scrollbar-width:thin;">
            <?php foreach ($liveActivities as $act): ?>
                <?php 
                    $isOngoing = in_array($act['status'] ?? '', ['in_progress', 'ongoing', 'active'], true);
                ?>
                <div class="card activity-slide-card" style="display:flex; flex-direction:column; border:1px solid var(--border-medium); background:var(--bg-surface); border-radius:var(--radius-md); box-shadow:var(--shadow-sm); transition:transform var(--transition-fast);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-sm);">
                        <span class="badge" style="background:<?= $isOngoing ? 'rgba(16,185,129,0.1)' : 'var(--bg-subtle)' ?>; color:<?= $isOngoing ? '#065F46' : 'var(--text-muted)' ?>; font-weight:700; font-size:0.75rem; border:1px solid <?= $isOngoing ? 'rgba(16,185,129,0.3)' : 'var(--border-subtle)' ?>;">
                            <?= $isOngoing ? '● ' . ($isBn ? 'চলমান' : 'Ongoing') : ($isBn ? 'সম্পন্ন' : 'Completed') ?>
                        </span>
                        <span style="font-size:0.78rem; font-weight:600; color:var(--accent-gold);">
                            <?= e($isBn ? ($act['category_bn'] ?? '') : ($act['category_en'] ?? '')) ?>
                        </span>
                    </div>

                    <h3 style="font-size:1.18rem; line-height:1.4; margin-bottom:var(--space-xs); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;">
                        <a href="<?= e(url('/activities', $currentLocale)) ?>" style="color:var(--text-main); text-decoration:none;">
                            <?= e($isBn ? ($act['title_bn'] ?? '') : ($act['title_en'] ?? '')) ?>
                        </a>
                    </h3>

                    <p style="font-size:0.88rem; color:var(--text-muted); line-height:1.6; margin-bottom:var(--space-md); flex-grow:1; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;">
                        <?= e($isBn ? ($act['description_bn'] ?? '') : ($act['description_en'] ?? '')) ?>
                    </p>

                    <div style="padding-top:var(--space-xs); border-top:1px solid var(--border-subtle); margin-bottom:var(--space-md); font-size:0.8rem; color:var(--text-muted); display:flex; justify-content:space-between;">
                        <span>📍 <?= e($isBn ? ($act['location_bn'] ?? '') : ($act['location_en'] ?? '')) ?></span>
                        <span><?= e($act['date'] ?? $act['year'] ?? '') ?></span>
                    </div>

                    <a href="<?= e(url('/activities', $currentLocale)) ?>" class="btn btn-secondary btn-sm" style="width:100%; justify-content:center;">
                        <?= $isBn ? 'বিস্তারিত দেখুন →' : 'View Details →' ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- ==========================================
     06. KNOWLEDGE & SCRIPTURE (জ্ঞানভাণ্ডার)
     ========================================== -->
<?php if ($isSectionActive('knowledge')): ?>
<section class="editorial-section editorial-section-alt" id="knowledge" style="background:var(--bg-subtle);">
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:var(--space-md); margin-bottom:var(--space-2xl);">
            <div>
                <span class="section-tag"><?= $isBn ? 'ডিজিটাল মহাফেজখানা ও গবেষণা' : 'Scriptural Archive & Research' ?></span>
                <h2 class="section-title" style="margin-bottom:4px;"><?= $isBn ? 'এসপিএস জ্ঞানভাণ্ডার' : 'SPS Knowledge & Scripture Gateway' ?></h2>
                <p class="section-subtitle" style="margin-bottom:0;">
                    <?= $isBn 
                        ? 'ভগবদ্গীতা, উপনিষদ, বেদান্ত দর্শন এবং দুর্লভ পাণ্ডুলিপির প্রামাণ্য ডিজিটাল সংকলন' 
                        : 'Authentic digital corpus of Bhagavad Gita, Upanishads, Vedanta treatises, and ancient manuscripts.' ?>
                </p>
            </div>
            <a href="<?= e(url('/knowledge', $currentLocale)) ?>" class="btn btn-primary">
                <?= $isBn ? 'সম্পূর্ণ জ্ঞানভাণ্ডারে যান →' : 'Enter Knowledge Repository →' ?>
            </a>
        </div>

        <div class="knowledge-grid">
            <!-- 1. Bhagavad Gita -->
            <a href="<?= e(url('/knowledge', $currentLocale)) ?>" class="card" style="text-decoration:none; padding:var(--space-xl); border:1px solid var(--border-medium); background:var(--bg-surface); border-radius:var(--radius-md); transition:transform var(--transition-fast), border-color var(--transition-fast);">
                <div style="font-size:2.2rem; margin-bottom:var(--space-xs);">📖</div>
                <h3 style="font-size:1.25rem; margin-bottom:var(--space-2xs); color:var(--text-main); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;">
                    <?= $isBn ? 'শ্রীমদ্ভগবদ্গীতা' : 'Srimad Bhagavad Gita' ?>
                </h3>
                <div style="font-size:0.8rem; font-weight:700; color:var(--accent-saffron); margin-bottom:var(--space-xs);">
                    <?= $isBn ? 'মূল শ্লোক, পদচ্ছেদ ও বঙ্গানুবাদ' : 'Original Sanskrit, Word Index & Translation' ?>
                </div>
                <p style="font-size:0.88rem; color:var(--text-muted); line-height:1.6; margin-bottom:0;">
                    <?= $isBn 
                        ? 'গীতার ১৮টি অধ্যায়ের প্রতিটি শ্লোকের বিশুদ্ধ পাঠ ও নিষ্কাম কর্মযোগের সমকালীন দার্শনিক প্রাসঙ্গিকতা।' 
                        : 'All 18 chapters of Bhagavad Gita with exact Sanskrit grammar and contemporary philosophical insights.' ?>
                </p>
            </a>

            <!-- 2. Principal Upanishads -->
            <a href="<?= e(url('/knowledge', $currentLocale)) ?>" class="card" style="text-decoration:none; padding:var(--space-xl); border:1px solid var(--border-medium); background:var(--bg-surface); border-radius:var(--radius-md); transition:transform var(--transition-fast), border-color var(--transition-fast);">
                <div style="font-size:2.2rem; margin-bottom:var(--space-xs);">📜</div>
                <h3 style="font-size:1.25rem; margin-bottom:var(--space-2xs); color:var(--text-main); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;">
                    <?= $isBn ? 'প্রধান উপনিষদাবলী' : 'Principal Upanishads' ?>
                </h3>
                <div style="font-size:0.8rem; font-weight:700; color:var(--accent-saffron); margin-bottom:var(--space-xs);">
                    <?= $isBn ? 'ঈশ, কঠ, মুণ্ডক ও কেন উপনিষদ' : 'Isha, Katha, Mundaka & Kena' ?>
                </div>
                <p style="font-size:0.88rem; color:var(--text-muted); line-height:1.6; margin-bottom:0;">
                    <?= $isBn 
                        ? 'আত্মতত্ত্ব, পরব্রহ্ম ভাব ও বৈদিক ঋষিদের গভীর অধ্যাত্ম জিজ্ঞাসার প্রামাণ্য অনুবাদ ও বিশদ ব্যাখ্যা।' 
                        : 'Canonical Upanishadic dialogues exploring the nature of Consciousness (Atman) and Brahman.' ?>
                </p>
            </a>

            <!-- 3. Vedanta Philosophy -->
            <a href="<?= e(url('/knowledge', $currentLocale)) ?>" class="card" style="text-decoration:none; padding:var(--space-xl); border:1px solid var(--border-medium); background:var(--bg-surface); border-radius:var(--radius-md); transition:transform var(--transition-fast), border-color var(--transition-fast);">
                <div style="font-size:2.2rem; margin-bottom:var(--space-xs);">🔱</div>
                <h3 style="font-size:1.25rem; margin-bottom:var(--space-2xs); color:var(--text-main); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;">
                    <?= $isBn ? 'বেদান্ত দর্শন ও সূত্রাবলী' : 'Vedanta & Canonical Sutras' ?>
                </h3>
                <div style="font-size:0.8rem; font-weight:700; color:var(--accent-saffron); margin-bottom:var(--space-xs);">
                    <?= $isBn ? 'অদ্বৈত, বিশিষ্টাদ্বৈত ও দ্বৈত প্রস্থান' : 'Advaita, Vishishtadvaita & Dvaita' ?>
                </div>
                <p style="font-size:0.88rem; color:var(--text-muted); line-height:1.6; margin-bottom:0;">
                    <?= $isBn 
                        ? 'ব্রহ্মসূত্র ও আচার্যবৃন্দের দার্শনিক সিদ্ধান্তসমূহের নিরপেক্ষ ও পদ্ধতিগত একাডেমিক সমীক্ষা।' 
                        : 'Comparative academic exegesis on Brahma Sutras, Shankara Bhashya, and post-classical schools.' ?>
                </p>
            </a>

            <!-- 4. Digital Manuscripts Archive -->
            <a href="<?= e(url('/library', $currentLocale)) ?>" class="card" style="text-decoration:none; padding:var(--space-xl); border:1px solid var(--border-medium); background:var(--bg-surface); border-radius:var(--radius-md); transition:transform var(--transition-fast), border-color var(--transition-fast);">
                <div style="font-size:2.2rem; margin-bottom:var(--space-xs);">🗃️</div>
                <h3 style="font-size:1.25rem; margin-bottom:var(--space-2xs); color:var(--text-main); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;">
                    <?= $isBn ? 'পাণ্ডুলিপি ডিজিটাল মহাফেজখানা' : 'Digital Manuscript Archive' ?>
                </h3>
                <div style="font-size:0.8rem; font-weight:700; color:var(--accent-saffron); margin-bottom:var(--space-xs);">
                    <?= $isBn ? 'সুরক্ষিত ই-বুক ক্যানভাস রিডার' : 'Protected High-Res E-Book Reader' ?>
                </div>
                <p style="font-size:0.88rem; color:var(--text-muted); line-height:1.6; margin-bottom:0;">
                    <?= $isBn 
                        ? 'এসপিএস নিজস্ব প্রকাশনা এবং প্রাচীন আকর শাস্ত্রগ্রন্থের সার্বক্ষণিক সুরক্ষিত অনলাইন পঠনশালা।' 
                        : 'High-resolution digital reader for SPS archival journals and critical Sanskrit editions.' ?>
                </p>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- ==========================================
     07. TODAY'S SCRIPTURE / FEATURED KNOWLEDGE
     ========================================== -->
<?php if ($isSectionActive('scripture')): ?>
<section class="editorial-section" id="scripture">
    <div class="container">
        <div class="section-header text-center" style="max-width:680px; margin:0 auto var(--space-2xl);">
            <span class="section-tag"><?= $isBn ? 'আজকের শাস্ত্রীয় আলোকপাত' : 'Featured Daily Scripture' ?></span>
            <h2 class="section-title"><?= e($isBn ? ($scripture['source_book_bn'] ?? 'শ্রীমদ্ভগবদ্গীতা') : ($scripture['source_book_en'] ?? 'Srimad Bhagavad Gita')) ?> — <?= e($scripture['verse_no'] ?? '২.৪৭') ?></h2>
        </div>

        <div class="scripture-card scripture-card-compact" style="max-width:880px; margin:0 auto; background:var(--bg-surface); border:1px solid var(--border-medium); border-radius:var(--radius-md); padding:var(--space-2xl); box-shadow:var(--shadow-md);">
            <div style="text-align:center; margin-bottom:var(--space-lg);">
                <span class="badge badge-scholarly" style="font-size:0.8rem; padding:4px 12px;">
                    🕉️ <?= e($isBn ? ($scripture['source_book_bn'] ?? '') : ($scripture['source_book_en'] ?? '')) ?> • <?= e($scripture['verse_no'] ?? '') ?>
                </span>
            </div>

            <!-- Sacred Verse Display -->
            <div style="text-align:center; margin-bottom:var(--space-xl);">
                <div style="font-size:1.35rem; font-weight:700; line-height:1.8; color:var(--text-main); font-family:var(--font-display, serif); margin-bottom:var(--space-xs);">
                    <?= nl2br(e($scripture['sanskrit_devanagari'] ?? '')) ?>
                </div>
                <?php if ($isBn && !empty($scripture['sanskrit_bengali'])): ?>
                <div style="font-size:1.15rem; color:var(--accent-brown); line-height:1.75; font-family:var(--font-bn-serif, serif);">
                    <?= nl2br(e($scripture['sanskrit_bengali'])) ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Authoritative Translation -->
            <div style="background:var(--bg-subtle); border-left:3px solid var(--accent-saffron); border-radius:var(--radius-sm); padding:var(--space-lg); margin-bottom:var(--space-lg);">
                <div style="font-size:0.78rem; text-transform:uppercase; font-weight:700; color:var(--accent-saffron); margin-bottom:4px;">
                    <?= $isBn ? 'প্রামাণ্য বঙ্গানুবাদ' : 'Authoritative Translation' ?>
                </div>
                <p style="font-size:1.02rem; line-height:1.7; color:var(--text-body); margin-bottom:0; font-style:normal;">
                    "<?= e($isBn ? ($scripture['translation_bn'] ?? '') : ($scripture['translation_en'] ?? '')) ?>"
                </p>
            </div>

            <!-- Philosophical Exegesis -->
            <div style="padding:var(--space-md) var(--space-lg); border-top:1px dashed var(--border-medium); font-size:0.88rem; color:var(--text-muted); line-height:1.65; display:flex; align-items:flex-start; gap:10px;">
                <span style="font-size:1.1rem; color:var(--accent-gold);">💡</span>
                <div>
                    <strong style="color:var(--text-main);"><?= $isBn ? 'দার্শনিক তাৎপর্য:' : 'Philosophical Significance:' ?></strong>
                    <span><?= e($isBn ? ($scripture['exegesis_bn'] ?? '') : ($scripture['exegesis_en'] ?? '')) ?></span>
                </div>
            </div>

            <div style="text-align:center; margin-top:var(--space-lg); padding-top:var(--space-sm); border-top:1px solid var(--border-subtle);">
                <a href="<?= e(url('/knowledge', $currentLocale)) ?>" class="btn btn-secondary btn-sm">
                    <?= $isBn ? 'জ্ঞানভাণ্ডারে আরও শ্লোক পড়ুন' : 'Explore More Scriptures' ?> →
                </a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- ==========================================
     08. LATEST FROM SPS (সর্বশেষ প্রকাশনা ও গবেষণা)
     ========================================== -->
<?php if ($isSectionActive('publications')): ?>
<section class="editorial-section editorial-section-alt" id="publications" style="background:var(--bg-subtle);">
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:var(--space-md); margin-bottom:var(--space-2xl);">
            <div>
                <span class="section-tag"><?= $isBn ? 'সাম্প্রতিক সংযোজন' : 'Recent Additions' ?></span>
                <h2 class="section-title" style="margin-bottom:4px;"><?= $isBn ? 'সর্বশেষ প্রকাশনা ও গবেষণা' : 'Latest from SPS Publications' ?></h2>
                <p class="section-subtitle" style="margin-bottom:0;">
                    <?= $isBn 
                        ? 'এসপিএস মিডিয়া লাইব্রেরি থেকে সংকলিত প্রামাণ্য স্মারক গ্রন্থ, কালানুক্রমিক গবেষণা ও শাস্ত্রসংকলন' 
                        : 'Peer-reviewed commemorative editions, chronological research, and sacred critical recensions.' ?>
                </p>
            </div>
            <a href="<?= e(url('/library', $currentLocale)) ?>" class="btn btn-secondary">
                <?= $isBn ? 'সম্পূর্ণ লাইব্রেরি দেখুন' : 'View Full Library' ?> →
            </a>
        </div>

        <div class="publications-grid">
            <?php foreach ($books as $slug => $b): ?>
                <?php 
                    $isSpsFolder = ($b['category_group'] ?? 'sps') === 'sps';
                ?>
                <div class="card" style="display:flex; flex-direction:column; padding:0; overflow:hidden; border:1px solid var(--border-medium); background:var(--bg-surface); border-radius:var(--radius-md); transition:transform var(--transition-fast);">
                    <!-- Book Cover Container -->
                    <div style="height:220px; background:#EDE7DF; display:flex; align-items:center; justify-content:center; padding:var(--space-md); position:relative; overflow:hidden; border-bottom:1px solid var(--border-subtle);">
                        <a href="<?= e(url('/library/book/' . $slug, $currentLocale)) ?>">
                            <img src="<?= asset($b['cover_image']) ?>" 
                                 alt="<?= e($isBn ? $b['title_bn'] : $b['title_en']) ?>"
                                 style="max-height:190px; max-width:85%; object-fit:contain; box-shadow:-3px 4px 12px rgba(0,0,0,0.18);"
                                 loading="lazy">
                        </a>
                        <div style="position:absolute; top:8px; left:8px;">
                            <span class="badge" style="background:<?= $isSpsFolder ? 'var(--accent-saffron)' : '#4A5568' ?>; color:#FFF; font-size:0.7rem; font-weight:700;">
                                <?= $isSpsFolder ? '🏛️ SPS' : '📜 Other' ?>
                            </span>
                        </div>
                        <div style="position:absolute; top:8px; right:8px;">
                            <span class="badge badge-scholarly" style="font-size:0.7rem; background:rgba(255,255,255,0.92); color:var(--text-main);">
                                PDF • <?= e($b['file_size']) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Book Content -->
                    <div style="padding:var(--space-lg); display:flex; flex-direction:column; flex-grow:1;">
                        <div style="font-size:0.74rem; font-weight:600; text-transform:uppercase; color:var(--accent-gold); margin-bottom:2px;">
                            <?= e($isBn ? $b['category_bn'] : $b['category_en']) ?>
                        </div>
                        <h4 style="font-size:1.05rem; line-height:1.35; margin-bottom:var(--space-2xs); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;">
                            <a href="<?= e(url('/library/book/' . $slug, $currentLocale)) ?>" style="color:var(--text-main); text-decoration:none;">
                                <?= e($isBn ? $b['title_bn'] : $b['title_en']) ?>
                            </a>
                        </h4>
                        <div style="font-size:0.8rem; color:var(--accent-brown); margin-bottom:var(--space-sm);">
                            ✍️ <?= e($isBn ? $b['author_bn'] : $b['author_en']) ?>
                        </div>
                        <div style="margin-top:auto; padding-top:var(--space-xs); border-top:1px solid var(--border-subtle); display:flex; gap:6px;">
                            <a href="<?= e(url('/library/reader/' . $slug, $currentLocale)) ?>" class="btn btn-primary btn-sm" style="flex:1; justify-content:center; font-size:0.78rem; padding:4px 8px;">
                                📖 <?= $isBn ? 'অনলাইনে পড়ুন' : 'Read E-Book' ?>
                            </a>
                            <a href="<?= e(url('/library/book/' . $slug, $currentLocale)) ?>" class="btn btn-ghost btn-sm" style="font-size:0.78rem; padding:4px 8px;" title="View details">
                                <?= $isBn ? 'বিবরণ' : 'Details' ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- ==========================================
     09. MEMBERS' BLOG (সদস্যদের চিন্তাধারা)
     ========================================== -->
<?php if ($isSectionActive('blog')): ?>
<section class="editorial-section" id="blog">
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:var(--space-md); margin-bottom:var(--space-2xl);">
            <div>
                <span class="section-tag"><?= $isBn ? 'সদস্য ব্লগ ও চিন্তাধারা' : 'Scholarly Journal & Community' ?></span>
                <h2 class="section-title" style="margin-bottom:4px;"><?= $isBn ? 'সদস্যদের চিন্তাধারা ও প্রবন্ধ' : 'Members\' Voices & Articles' ?></h2>
                <p class="section-subtitle" style="margin-bottom:0;">
                    <?= $isBn 
                        ? 'দর্শন, সমাজচিন্তা ও শাস্ত্রীয় বিশ্লেষণ নিয়ে গবেষক ও সদস্যদের নির্বাচিত লেখা' 
                        : 'Thoughtful essays on philosophy, ethics, and social conscience written by members.' ?>
                </p>
            </div>
            <a href="<?= e(url('/blog', $currentLocale)) ?>" class="btn btn-secondary">
                <?= $isBn ? 'সব লেখা দেখুন' : 'View All Articles' ?> →
            </a>
        </div>

        <div class="blog-grid">
            <?php foreach ($blogs as $post): ?>
                <?php 
                    $authorData = $post['author'] ?? null;
                    if (is_array($authorData)) {
                        $authorName = $isBn 
                            ? ($authorData['name_bn'] ?? $authorData['name_en'] ?? 'সদস্য গবেষক') 
                            : ($authorData['name_en'] ?? $authorData['name_bn'] ?? 'Member Scholar');
                        $authorAvatar = $authorData['avatar'] ?? null;
                    } else {
                        $authorName = $isBn 
                            ? ($post['author_name_bn'] ?? (is_string($authorData) ? $authorData : 'সদস্য গবেষক')) 
                            : ($post['author_name_en'] ?? (is_string($authorData) ? $authorData : 'Member Scholar'));
                        $authorAvatar = null;
                    }
                    $initial = mb_substr((string)$authorName, 0, 1, 'UTF-8');
                ?>
                <article class="card" style="display:flex; flex-direction:column; padding:var(--space-xl); border:1px solid var(--border-medium); background:var(--bg-surface); border-radius:var(--radius-md); box-shadow:var(--shadow-sm); transition:transform var(--transition-fast);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-xs); font-size:0.78rem;">
                        <span class="badge badge-neutral" style="font-size:0.74rem;">
                            <?= e($isBn ? ($post['category_bn'] ?? $post['category'] ?? '') : ($post['category_en'] ?? $post['category'] ?? '')) ?>
                        </span>
                        <span style="color:var(--text-muted);">
                            ⏱️ <?= e($post['reading_time'] ?? '৫ মিনিট') ?>
                        </span>
                    </div>

                    <h3 style="font-size:1.15rem; line-height:1.4; margin-bottom:var(--space-xs); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;">
                        <a href="<?= e(url('/blog/' . ($post['slug'] ?? ''), $currentLocale)) ?>" style="color:var(--text-main); text-decoration:none;">
                            <?= e($isBn ? ($post['title_bn'] ?? $post['title'] ?? '') : ($post['title_en'] ?? $post['title'] ?? '')) ?>
                        </a>
                    </h3>

                    <p style="font-size:0.88rem; color:var(--text-muted); line-height:1.6; margin-bottom:var(--space-lg); flex-grow:1; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;">
                        <?= e($isBn ? ($post['excerpt_bn'] ?? $post['excerpt'] ?? '') : ($post['excerpt_en'] ?? $post['excerpt'] ?? '')) ?>
                    </p>

                    <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border-subtle); padding-top:var(--space-sm); font-size:0.82rem;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div style="width:28px; height:28px; border-radius:50%; background:var(--accent-saffron); color:#FFF; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.8rem;">
                                <?= e($initial) ?>
                            </div>
                            <span style="font-weight:600; color:var(--text-main);"><?= e($authorName) ?></span>
                        </div>
                        <a href="<?= e(url('/blog/' . ($post['slug'] ?? ''), $currentLocale)) ?>" style="color:var(--accent-saffron); font-weight:700; text-decoration:none;">
                            <?= $isBn ? 'পড়ুন →' : 'Read →' ?>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- ==========================================
     10. FINANCIAL TRANSPARENCY (আমাদের প্রভাব ও স্বচ্ছতা)
     ========================================== -->
<?php if ($isSectionActive('transparency')): ?>
<section class="editorial-section editorial-section-alt" id="transparency" style="background:var(--bg-subtle);">
    <div class="container">
        <div class="section-header text-center" style="max-width:700px; margin:0 auto var(--space-2xl);">
            <span class="section-tag"><?= $isBn ? 'নৈতিক দায়বদ্ধতা ও হিসাব' : 'Financial Governance' ?></span>
            <h2 class="section-title"><?= $isBn ? 'স্বচ্ছতার সঙ্গে প্রতিটি অবদান' : 'Every Contribution Accounted With 100% Transparency' ?></h2>
            <p class="section-subtitle">
                <?= $isBn 
                    ? 'তহবিল প্রাপ্তি, প্রত্যক্ষ প্রকল্প ব্যয় এবং জরুরি তহবিলের সর্বজনীন প্রকাশ্য খতিয়ান' 
                    : 'Audited monthly records of funds received, frontline humanitarian disbursements, and emergency reserves.' ?>
            </p>
        </div>

        <!-- 3 Big Numbers -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:var(--space-lg); margin-bottom:var(--space-2xl);">
            <div class="card" style="padding:var(--space-xl); background:var(--bg-surface); border:1px solid var(--border-medium); border-left:4px solid var(--status-success); border-radius:var(--radius-md);">
                <div style="font-size:0.82rem; text-transform:uppercase; font-weight:700; color:var(--text-muted); letter-spacing:0.04em;">
                    <?= $isBn ? 'মোট প্রাপ্ত অনুদান' : 'Total Funds Received' ?>
                </div>
                <div style="font-size:2rem; font-weight:800; color:var(--status-success); margin-top:4px; font-family:var(--font-display, serif);">
                    <?= e($isBn ? ($transparencyData['total_received_bn'] ?? '৳ ৪৫,৮০,০০০') : ($transparencyData['total_received_en'] ?? '৳ 4,580,000')) ?>
                </div>
                <div style="font-size:0.78rem; color:var(--text-muted); margin-top:2px;">
                    <?= $isBn ? 'যাচাইকৃত ব্যাংক ও ইলেক্ট্রনিক চ্যানেল' : 'Audited electronic receipts' ?>
                </div>
            </div>

            <div class="card" style="padding:var(--space-xl); background:var(--bg-surface); border:1px solid var(--border-medium); border-left:4px solid var(--accent-saffron); border-radius:var(--radius-md);">
                <div style="font-size:0.82rem; text-transform:uppercase; font-weight:700; color:var(--text-muted); letter-spacing:0.04em;">
                    <?= $isBn ? 'প্রকল্প ও সেবায় ব্যয়' : 'Direct Project Expenditure' ?>
                </div>
                <div style="font-size:2rem; font-weight:800; color:var(--accent-saffron); margin-top:4px; font-family:var(--font-display, serif);">
                    <?= e($isBn ? ($transparencyData['total_spent_bn'] ?? '৳ ৩৮,১০,০০০') : ($transparencyData['total_spent_en'] ?? '৳ 3,810,000')) ?>
                </div>
                <div style="font-size:0.78rem; color:var(--text-muted); margin-top:2px;">
                    <?= $isBn ? 'শিক্ষা, চিকিৎসা ও ত্রাণে প্রত্যক্ষ প্রয়োগ' : 'Frontline healthcare, schools & relief' ?>
                </div>
            </div>

            <div class="card" style="padding:var(--space-xl); background:var(--bg-surface); border:1px solid var(--border-medium); border-left:4px solid var(--accent-gold); border-radius:var(--radius-md);">
                <div style="font-size:0.82rem; text-transform:uppercase; font-weight:700; color:var(--text-muted); letter-spacing:0.04em;">
                    <?= $isBn ? 'জরুরি সংরক্ষিত তহবিল' : 'Emergency Dedicated Reserve' ?>
                </div>
                <div style="font-size:2rem; font-weight:800; color:var(--accent-gold); margin-top:4px; font-family:var(--font-display, serif);">
                    <?= e($isBn ? ($transparencyData['emergency_reserve_bn'] ?? '৳ ৭,৭০,০০০') : ($transparencyData['emergency_reserve_en'] ?? '৳ 770,000')) ?>
                </div>
                <div style="font-size:0.78rem; color:var(--text-muted); margin-top:2px;">
                    <?= $isBn ? 'ভবিষ্যৎ দুর্যোগ ও চলমান প্রকল্পের জন্য' : 'Committed for ongoing obligations' ?>
                </div>
            </div>
        </div>

        <!-- Allocation Breakdown Bar -->
        <div class="card" style="padding:var(--space-xl); background:var(--bg-surface); border:1px solid var(--border-medium); border-radius:var(--radius-md); margin-bottom:var(--space-xl);">
            <h3 style="font-size:1.1rem; margin-bottom:var(--space-md); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;">
                <?= $isBn ? 'খাতভিত্তিক ব্যয় বণ্টন অনুপাত' : 'Fund Expenditure Distribution Ratio' ?>
            </h3>

            <div style="display:flex; flex-direction:column; gap:12px;">
                <?php foreach (($transparencyData['allocations'] ?? []) as $alloc): ?>
                    <div>
                        <div style="display:flex; justify-content:space-between; font-size:0.88rem; margin-bottom:4px;">
                            <span><?= e($isBn ? $alloc['name_bn'] : $alloc['name_en']) ?></span>
                            <span style="font-weight:700; color:var(--text-main);"><?= e($alloc['percentage']) ?>%</span>
                        </div>
                        <div style="height:10px; background:var(--bg-subtle); border-radius:6px; overflow:hidden;">
                            <div style="height:100%; width:<?= e($alloc['percentage']) ?>%; background:<?= e($alloc['color']) ?>; border-radius:6px;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div style="text-align:center;">
            <a href="<?= e(url('/transparency', $currentLocale)) ?>" class="btn btn-secondary">
                <?= $isBn ? 'সম্পূর্ণ আর্থিক বিবরণী ও অডিট প্রতিবেদন দেখুন' : 'Inspect Full Financial Reports & Audit' ?> →
            </a>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- ==========================================
     11. SOCIAL COMMUNITY (Facebook Page & Group)
     ========================================== -->
<?php if ($isSectionActive('community')): ?>
<section class="editorial-section" id="community" style="background:var(--bg-surface);">
    <div class="container">
        <div style="background:linear-gradient(135deg, rgba(24,119,242,0.06) 0%, rgba(212,175,55,0.08) 100%); border:1px solid rgba(24,119,242,0.2); border-radius:var(--radius-md); padding:var(--space-3xl) var(--space-2xl); text-align:center;">
            <span class="section-tag" style="background:rgba(24,119,242,0.12); color:#1877F2; font-weight:700;">
                <?= $isBn ? 'অনলাইন সমাজ ও সম্প্রদায়' : 'Online Community & Discourse' ?>
            </span>
            <h2 class="section-title" style="margin-top:var(--space-xs); margin-bottom:var(--space-sm);">
                <?= $isBn ? 'SPS-এর সঙ্গে যুক্ত থাকুন' : 'Stay Connected With SPS Community' ?>
            </h2>
            <p style="font-size:1.02rem; color:var(--text-body); max-width:680px; margin:0 auto var(--space-2xl); line-height:1.7;">
                <?= $isBn 
                    ? 'নিয়মিত শাস্ত্রীয় আলোচনা, প্রাতিষ্ঠানিক কার্যক্রম, প্রকাশনা এবং সমাজকল্যাণমূলক প্রকল্পের সর্বশেষ আপডেট পেতে আমাদের অফিশিয়াল ফেসবুক পেজ ও গ্রুপে যুক্ত থাকুন।' 
                    : 'Join our official online social channels to participate in scriptural discussions, track ongoing field projects, and stay updated with new research releases.' ?>
            </p>

            <div style="display:flex; justify-content:center; flex-wrap:wrap; gap:var(--space-lg);">
                <!-- Facebook Page Button -->
                <a href="<?= e($socials['facebook_page'] ?? 'https://www.facebook.com/bewithsps?utm_source=chatgpt.com') ?>" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="card"
                   style="display:flex; align-items:center; gap:var(--space-md); padding:var(--space-lg) var(--space-xl); background:#FFFFFF; border:1px solid #1877F2; border-radius:var(--radius-md); text-decoration:none; box-shadow:var(--shadow-sm); min-width:280px; transition:transform var(--transition-fast);">
                    <div style="width:48px; height:48px; border-radius:50%; background:#1877F2; color:#FFFFFF; display:flex; align-items:center; justify-content:center; font-size:1.4rem; flex-shrink:0;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </div>
                    <div style="text-align:left;">
                        <div style="font-weight:700; font-size:1rem; color:#1877F2;"><?= $isBn ? 'SPS ফেসবুক পেজ' : 'SPS Facebook Page' ?></div>
                        <div style="font-size:0.8rem; color:var(--text-muted);"><?= $isBn ? 'অফিশিয়াল পেজ ফলো করুন' : 'Official News & Announcements' ?></div>
                    </div>
                </a>

                <!-- Facebook Group Button -->
                <a href="<?= e($socials['facebook_group'] ?? 'https://www.facebook.com/groups/278337526756568?utm_source=chatgpt.com') ?>" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="card"
                   style="display:flex; align-items:center; gap:var(--space-md); padding:var(--space-lg) var(--space-xl); background:#FFFFFF; border:1px solid #0084FF; border-radius:var(--radius-md); text-decoration:none; box-shadow:var(--shadow-sm); min-width:280px; transition:transform var(--transition-fast);">
                    <div style="width:48px; height:48px; border-radius:50%; background:#0084FF; color:#FFFFFF; display:flex; align-items:center; justify-content:center; font-size:1.4rem; flex-shrink:0;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5v-9l7 4.5-7 4.5z"/>
                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                        </svg>
                    </div>
                    <div style="text-align:left;">
                        <div style="font-weight:700; font-size:1rem; color:#0084FF;"><?= $isBn ? 'SPS ফেসবুক গ্রুপ' : 'SPS Facebook Group' ?></div>
                        <div style="font-size:0.8rem; color:var(--text-muted);"><?= $isBn ? 'মুক্ত আলোচনায় যোগ দিন' : 'Discussions & Fellow Sanatanis' ?></div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- ==========================================
     12. JOIN / PARTICIPATE CTA (Join SPS)
     ========================================== -->
<?php if ($isSectionActive('join')): ?>
<section class="editorial-section" id="join" style="padding-bottom:var(--space-4xl); background:var(--bg-subtle);">
    <div class="container">
        <div class="section-header text-center" style="max-width:700px; margin:0 auto var(--space-3xl);">
            <span class="section-tag"><?= $isBn ? 'সনাতনী ঐক্য ও অন্তর্ভুক্তি' : 'Participate & Join' ?></span>
            <h2 class="section-title"><?= $isBn ? 'আপনি কীভাবে যুক্ত হতে পারেন?' : 'How Can You Participate with SPS?' ?></h2>
            <p class="section-subtitle">
                <?= $isBn 
                    ? 'জ্ঞানচর্চা, সরাসরি সেবাকর্ম অথবা প্রকল্প সহযোগিতার মাধ্যমে এসপিএস পরিবারের অংশ হোন' 
                    : 'Choose your pathway to engage with our scholarly community and humanitarian missions.' ?>
            </p>
        </div>

        <div class="join-grid">
            <!-- Option 1: Member -->
            <div class="card" style="padding:var(--space-2xl); border:1px solid var(--border-medium); background:var(--bg-surface); border-radius:var(--radius-md); display:flex; flex-direction:column; text-align:center; transition:transform var(--transition-fast);">
                <div style="font-size:2.4rem; margin-bottom:var(--space-sm);">⭐</div>
                <h3 style="font-size:1.3rem; margin-bottom:var(--space-xs); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;">
                    <?= $isBn ? 'সদস্য হোন' : 'Become a Member' ?>
                </h3>
                <p style="font-size:0.9rem; color:var(--text-muted); line-height:1.65; margin-bottom:var(--space-xl); flex-grow:1;">
                    <?= $isBn 
                        ? 'SPS-এর জ্ঞান ও কার্যক্রমের অঙ্গনে নিয়মিত সদস্য হিসেবে যুক্ত হন। সার্বক্ষণিক ই-বুক পড়ার সুযোগ ও সাংবাৎসরিক ভোটাধিকার লাভ করুন।' 
                        : 'Join as a verified member. Gain all-time reading access to archival e-books and participation in governance.' ?>
                </p>
                <a href="<?= e(url('/membership', $currentLocale)) ?>" class="btn btn-primary" style="justify-content:center;">
                    <?= $isBn ? 'সদস্যপদ গ্রহণ করুন' : 'Apply for Membership' ?> →
                </a>
            </div>

            <!-- Option 2: Volunteer -->
            <div class="card" style="padding:var(--space-2xl); border:1px solid var(--border-medium); background:var(--bg-surface); border-radius:var(--radius-md); display:flex; flex-direction:column; text-align:center; transition:transform var(--transition-fast);">
                <div style="font-size:2.4rem; margin-bottom:var(--space-sm);">🤝</div>
                <h3 style="font-size:1.3rem; margin-bottom:var(--space-xs); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;">
                    <?= $isBn ? 'স্বেচ্ছাসেবক হোন' : 'Become a Volunteer' ?>
                </h3>
                <p style="font-size:0.9rem; color:var(--text-muted); line-height:1.65; margin-bottom:var(--space-xl); flex-grow:1;">
                    <?= $isBn 
                        ? 'আপনার সময় ও দক্ষতা দিয়ে পাঠশালা পরিচালনা, চিকিৎসা ক্যাম্প ও দুর্যোগকালীন ত্রাণে সরাসরি সেবামূলক দায়িত্ব পালন করুন।' 
                        : 'Contribute your time and skills for free school teaching, mobile clinics, and disaster relief campaigns.' ?>
                </p>
                <a href="<?= e(url('/get-involved', $currentLocale)) ?>#volunteer" class="btn btn-secondary" style="justify-content:center;">
                    <?= $isBn ? 'স্বেচ্ছাসেবক হিসেবে যোগ দিন' : 'Join as Volunteer' ?> →
                </a>
            </div>

            <!-- Option 3: Support / Donate -->
            <div class="card" style="padding:var(--space-2xl); border:1px solid var(--border-medium); background:var(--bg-surface); border-radius:var(--radius-md); display:flex; flex-direction:column; text-align:center; transition:transform var(--transition-fast);">
                <div style="font-size:2.4rem; margin-bottom:var(--space-sm);">❤️</div>
                <h3 style="font-size:1.3rem; margin-bottom:var(--space-xs); font-family:<?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;">
                    <?= $isBn ? 'সহযোগিতা করুন' : 'Support Projects' ?>
                </h3>
                <p style="font-size:0.9rem; color:var(--text-muted); line-height:1.65; margin-bottom:var(--space-xl); flex-grow:1;">
                    <?= $isBn 
                        ? 'শিক্ষা, স্বাস্থ্য ও মানবিক সেবাকাজে সনাতনী ১০ টাকার প্রজেক্ট বা সরাসরি অনুদানের মাধ্যমে কল্যাণমূলক কাজে অংশীদার হোন।' 
                        : 'Sponsor a student, support our 10-Taka daily seva drive, or fund critical medical aid transparently.' ?>
                </p>
                <a href="<?= e(url('/get-involved', $currentLocale)) ?>#donate" class="btn btn-primary" style="justify-content:center; background:var(--accent-saffron); border-color:var(--accent-saffron);">
                    <?= $isBn ? 'প্রকল্পে সহায়তা করুন' : 'Support Frontline Seva' ?> →
                </a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Slider Interaction Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var slider = document.getElementById('activities-slider');
    var prevBtn = document.getElementById('activity-slide-prev');
    var nextBtn = document.getElementById('activity-slide-next');

    if (slider && prevBtn && nextBtn) {
        prevBtn.addEventListener('click', function() {
            slider.scrollBy({ left: -340, behavior: 'smooth' });
        });
        nextBtn.addEventListener('click', function() {
            slider.scrollBy({ left: 340, behavior: 'smooth' });
        });
    }
});
</script>
