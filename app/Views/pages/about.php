<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$leadership = $executivesGrouped['leadership'] ?? [];
$scholarly = $executivesGrouped['scholarly'] ?? [];
$publishing = $executivesGrouped['publishing'] ?? [];
$secretariat = $executivesGrouped['secretariat'] ?? [];
?>

<div class="container" style="padding-top:var(--space-2xl); padding-bottom:var(--space-4xl);">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="<?= e(url('/', $currentLocale)) ?>"><?= e(__('common.nav.home')) ?></a>
        <span class="breadcrumb-separator">/</span>
        <span style="color:var(--text-main); font-weight:600;"><?= $isBn ? 'পরিচিতি ও কার্যনির্বাহী পরিষদ' : 'About & Executive Committee' ?></span>
    </div>

    <!-- Institutional Hero -->
    <div style="text-align:center; max-width:880px; margin:0 auto var(--space-3xl);">
        <div style="display:inline-block; font-size:0.82rem; font-weight:700; color:var(--accent-saffron); text-transform:uppercase; letter-spacing:1.5px; margin-bottom:var(--space-xs); background:var(--bg-subtle); padding:4px 12px; border-radius:var(--radius-full); border:1px solid var(--border-medium);">
            <?= $isBn ? 'সনাতন দর্শন ও শাস্ত্র (SPS)' : 'Sanatan Philosophy & Scripture' ?>
        </div>
        <h1 style="font-size:2.4rem; font-weight:800; color:var(--primary-deep); margin:0 0 var(--space-sm); line-height:1.25;">
            <?= $isBn ? 'প্রতিষ্ঠানের পরিচিতি ও আদর্শ' : 'Institutional Framework & Purpose' ?>
        </h1>
        <p style="font-size:1.15rem; color:var(--text-muted); line-height:1.6; margin:0 0 var(--space-md);">
            <?= $isBn 
                ? 'সনাতনী ঐক্য, প্রচার ও কল্যাণে অবিচল — এই শাশ্বত মূলমন্ত্রে প্রতিষ্ঠিত একটি অরাজনৈতিক, প্রামাণিক আধ্যাত্মিক, শিক্ষামূলক ও মানবিক সংগঠন।' 
                : 'Steadfast in Sanatan Unity, Propagation & Welfare — an authentic non-political spiritual, academic, and humanitarian institution.' ?>
        </p>
        <div style="width:80px; height:3px; background:var(--accent-saffron); margin:0 auto; border-radius:2px;"></div>
    </div>

    <!-- Four Core Pillars -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:var(--space-lg); margin-bottom:var(--space-4xl);">
        <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-md); padding:var(--space-lg); border-top:4px solid var(--primary-deep); box-shadow:var(--shadow-sm);">
            <div style="font-size:1.6rem; margin-bottom:var(--space-xs);">📖</div>
            <h3 style="font-size:1.15rem; font-weight:700; color:var(--primary-deep); margin:0 0 6px;">
                <?= $isBn ? 'জ্ঞান (Knowledge)' : 'Knowledge' ?>
            </h3>
            <p style="font-size:0.88rem; color:var(--text-muted); line-height:1.6; margin:0;">
                <?= $isBn 
                    ? 'বেদ, উপনিষদ, গীতা ও প্রামাণ্য শাস্ত্রের বিকৃতিমুক্ত খাঁটি দার্শনিক তত্ত্ব সাধারণের বোধগম্য করে উপস্থাপন।' 
                    : 'Authentic presentation of Vedas, Upanishads, and Gita free from distortion for universal contemplation.' ?>
            </p>
        </div>

        <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-md); padding:var(--space-lg); border-top:4px solid var(--accent-saffron); box-shadow:var(--shadow-sm);">
            <div style="font-size:1.6rem; margin-bottom:var(--space-xs);">🕉️</div>
            <h3 style="font-size:1.15rem; font-weight:700; color:var(--primary-deep); margin:0 0 6px;">
                <?= $isBn ? 'ধর্ম (Dharma)' : 'Dharma' ?>
            </h3>
            <p style="font-size:0.88rem; color:var(--text-muted); line-height:1.6; margin:0;">
                <?= $isBn 
                    ? 'সত্য, অহিংসা, সংযম, সদাচার ও আধ্যাত্মিক মূল্যবোধ চর্চার মাধ্যমে আত্মশুদ্ধি অর্জন।' 
                    : 'Cultivating universal virtue, truthfulness, ahimsa, moral integrity, and inner spiritual purification.' ?>
            </p>
        </div>

        <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-md); padding:var(--space-lg); border-top:4px solid #16a34a; box-shadow:var(--shadow-sm);">
            <div style="font-size:1.6rem; margin-bottom:var(--space-xs);">🤝</div>
            <h3 style="font-size:1.15rem; font-weight:700; color:var(--primary-deep); margin:0 0 6px;">
                <?= $isBn ? 'সেবা (Service)' : 'Seva' ?>
            </h3>
            <p style="font-size:0.88rem; color:var(--text-muted); line-height:1.6; margin:0;">
                <?= $isBn 
                    ? 'জীবসেবা ও আর্তমানবতার কল্যাণ: বস্ত্র বিতরণ, শিক্ষা সহায়তা ও দুর্যোগকালীন ত্রাণ কার্যক্রম।' 
                    : 'Compassionate humanitarian relief: winter clothing, education stipends, and disaster response.' ?>
            </p>
        </div>

        <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-md); padding:var(--space-lg); border-top:4px solid #d97706; box-shadow:var(--shadow-sm);">
            <div style="font-size:1.6rem; margin-bottom:var(--space-xs);">🏛️</div>
            <h3 style="font-size:1.15rem; font-weight:700; color:var(--primary-deep); margin:0 0 6px;">
                <?= $isBn ? 'সমাজ (Community)' : 'Community' ?>
            </h3>
            <p style="font-size:0.88rem; color:var(--text-muted); line-height:1.6; margin:0;">
                <?= $isBn 
                    ? 'সনাতনী সমাজকে ঐক্যবদ্ধ, আত্মমর্যাদাশীল ও আদর্শ জীবনচর্যায় সচেতন করে গড়ে তোলা।' 
                    : 'Fostering solidarity, self-reliance, ethical values, and mutual empowerment across society.' ?>
            </p>
        </div>
    </div>

    <!-- Executive Committee Section Header -->
    <div style="text-align:center; max-width:750px; margin:0 auto var(--space-2xl);">
        <span style="font-size:0.82rem; font-weight:700; color:var(--accent-saffron); text-transform:uppercase; letter-spacing:1px;">
            <?= $isBn ? 'নেতৃত্ব ও পরিচালনা' : 'Governance & Leadership' ?>
        </span>
        <h2 style="font-size:2rem; font-weight:800; color:var(--primary-deep); margin:var(--space-2xs) 0 var(--space-xs);">
            <?= $isBn ? 'এসপিএস কেন্দ্রীয় কার্যনির্বাহী পরিষদ' : 'SPS Central Executive Committee' ?>
        </h2>
        <p style="font-size:0.95rem; color:var(--text-muted); margin:0;">
            <?= $isBn 
                ? 'এসপিএস-এর সকল সামাজিক, শাস্ত্রীয়, প্রকাশনা ও সাংগঠনিক কার্যক্রম সুচারুরূপে পরিচালনার দায়িত্বে নিয়োজিত কার্যনির্বাহী পর্ষদ।' 
                : 'The executive committee dedicated to steering educational, scriptural, publishing, and social programs of SPS.' ?>
        </p>
    </div>

    <!-- Tier 1: Leadership (সভাপতি, সাধারণ সম্পাদক, সাংগঠনিক সম্পাদক, কোষাধ্যক্ষ) -->
    <div style="margin-bottom:var(--space-3xl);">
        <div style="display:flex; align-items:center; gap:var(--space-sm); margin-bottom:var(--space-lg); border-bottom:2px solid var(--border-subtle); padding-bottom:var(--space-xs);">
            <span style="font-size:1.2rem;">⭐</span>
            <h3 style="font-size:1.25rem; font-weight:800; color:var(--primary-deep); margin:0;">
                <?= $isBn ? 'শীর্ষ পরিচালনা পরিষদ (Central Leadership)' : 'Central Leadership Board' ?>
            </h3>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(270px, 1fr)); gap:var(--space-xl);">
            <?php foreach ($leadership as $member): ?>
                <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-lg); padding:var(--space-xl); box-shadow:var(--shadow-sm); display:flex; flex-direction:column; align-items:center; text-align:center; transition:transform 0.2s ease, box-shadow 0.2s ease;">
                    
                    <!-- Portrait Photo -->
                    <div style="width:120px; height:120px; border-radius:50%; overflow:hidden; border:4px solid #ffffff; box-shadow:0 4px 14px rgba(0,0,0,0.12); margin-bottom:var(--space-md); background:var(--bg-subtle);">
                        <img src="<?= asset($member['photo']) ?>" alt="<?= e($member['name_en']) ?>" style="width:100%; height:100%; object-fit:cover;">
                    </div>

                    <!-- Name -->
                    <h4 style="font-size:1.15rem; font-weight:800; color:var(--primary-deep); margin:0 0 2px;">
                        <?= e($isBn ? $member['name_bn'] : $member['name_en']) ?>
                    </h4>
                    <div style="font-size:0.85rem; color:var(--text-muted); margin-bottom:var(--space-xs); font-family:var(--font-english);">
                        <?= e($member['name_en']) ?>
                    </div>

                    <!-- Designation Badge -->
                    <div style="display:inline-block; background:rgba(179,57,27,0.08); color:var(--primary-deep); font-weight:700; font-size:0.84rem; padding:4px 12px; border-radius:var(--radius-full); border:1px solid rgba(179,57,27,0.2);">
                        <?= e($isBn ? $member['designation_bn'] : $member['designation_en']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Tier 2: Scholarly & Publishing Councils (শাস্ত্র ও প্রকাশনা পরিষদ) -->
    <div style="margin-bottom:var(--space-3xl);">
        <div style="display:flex; align-items:center; gap:var(--space-sm); margin-bottom:var(--space-lg); border-bottom:2px solid var(--border-subtle); padding-bottom:var(--space-xs);">
            <span style="font-size:1.2rem;">📜</span>
            <h3 style="font-size:1.25rem; font-weight:800; color:var(--primary-deep); margin:0;">
                <?= $isBn ? 'শাস্ত্রীয় জ্ঞানপীঠ ও প্রকাশনা পরিষদ (Scripture & Publications Council)' : 'Scripture & Publications Council' ?>
            </h3>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:var(--space-lg);">
            <!-- Scholarly Secretaries (Pranto Saha, Goutam Dandapat, Rahul Kumar Sutradhar) -->
            <?php foreach ($scholarly as $member): ?>
                <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-md); padding:var(--space-lg); display:flex; flex-direction:column; align-items:center; text-align:center; box-shadow:var(--shadow-sm);">
                    <div style="width:80px; height:80px; border-radius:50%; background:linear-gradient(135deg, #fef3c7, #fed7aa); display:flex; align-items:center; justify-content:center; font-size:2rem; margin-bottom:var(--space-sm); border:2px solid #fcd34d;">
                        📖
                    </div>
                    <h4 style="font-size:1.05rem; font-weight:700; color:var(--primary-deep); margin:0 0 2px;">
                        <?= e($isBn ? $member['name_bn'] : $member['name_en']) ?>
                    </h4>
                    <div style="font-size:0.8rem; color:var(--text-muted); margin-bottom:var(--space-xs);">
                        <?= e($member['name_en']) ?>
                    </div>
                    <div style="font-size:0.8rem; font-weight:700; color:#b45309; background:#fffbeb; padding:3px 10px; border-radius:var(--radius-full); border:1px solid #fde68a;">
                        <?= e($isBn ? $member['designation_bn'] : $member['designation_en']) ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Publishing Secretaries (Badhan Roy, Partha Pratim Dutta) -->
            <?php foreach ($publishing as $member): ?>
                <div style="background:#ffffff; border:1px solid var(--border-medium); border-radius:var(--radius-md); padding:var(--space-lg); display:flex; flex-direction:column; align-items:center; text-align:center; box-shadow:var(--shadow-sm);">
                    <div style="width:80px; height:80px; border-radius:50%; overflow:hidden; border:2px solid var(--primary-deep); margin-bottom:var(--space-sm); box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                        <img src="<?= asset($member['photo']) ?>" alt="<?= e($member['name_en']) ?>" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    <h4 style="font-size:1.05rem; font-weight:700; color:var(--primary-deep); margin:0 0 2px;">
                        <?= e($isBn ? $member['name_bn'] : $member['name_en']) ?>
                    </h4>
                    <div style="font-size:0.8rem; color:var(--text-muted); margin-bottom:var(--space-xs);">
                        <?= e($member['name_en']) ?>
                    </div>
                    <div style="font-size:0.8rem; font-weight:700; color:#1d4ed8; background:#eff6ff; padding:3px 10px; border-radius:var(--radius-full); border:1px solid #bfdbfe;">
                        <?= e($isBn ? $member['designation_bn'] : $member['designation_en']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Tier 3: Secretariat & Departmental Secretaries -->
    <div style="margin-bottom:var(--space-4xl);">
        <div style="display:flex; align-items:center; gap:var(--space-sm); margin-bottom:var(--space-lg); border-bottom:2px solid var(--border-subtle); padding-bottom:var(--space-xs);">
            <span style="font-size:1.2rem;">🏛️</span>
            <h3 style="font-size:1.25rem; font-weight:800; color:var(--primary-deep); margin:0;">
                <?= $isBn ? 'বিভাগীয় ও সাংগঠনিক সম্পাদকবৃন্দ (Secretarial Board)' : 'Secretarial & Departmental Board' ?>
            </h3>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(230px, 1fr)); gap:var(--space-md);">
            <?php foreach ($secretariat as $member): ?>
                <div style="background:#ffffff; border:1px solid var(--border-subtle); border-radius:var(--radius-md); padding:var(--space-md); display:flex; flex-direction:column; align-items:center; text-align:center;">
                    <div style="width:50px; height:50px; border-radius:50%; background:var(--bg-subtle); display:flex; align-items:center; justify-content:center; font-size:1.4rem; margin-bottom:var(--space-xs); border:1px solid var(--border-medium);">
                        🪷
                    </div>
                    <h5 style="font-size:0.95rem; font-weight:700; color:var(--primary-deep); margin:0 0 2px;">
                        <?= e($isBn ? $member['name_bn'] : $member['name_en']) ?>
                    </h5>
                    <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:4px;">
                        <?= e($member['name_en']) ?>
                    </div>
                    <div style="font-size:0.78rem; font-weight:600; color:var(--accent-saffron);">
                        <?= e($isBn ? $member['designation_bn'] : $member['designation_en']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Official Communication & Social Channels Section -->
    <div style="background:linear-gradient(135deg, #0d1b2a, #1b263b); color:#ffffff; border-radius:var(--radius-xl); padding:var(--space-2xl) var(--space-xl); box-shadow:var(--shadow-lg);">
        <div style="text-align:center; max-width:700px; margin:0 auto var(--space-xl);">
            <span style="font-size:0.8rem; font-weight:700; color:#fcd34d; text-transform:uppercase; letter-spacing:1px;">
                <?= $isBn ? 'যোগাযোগ ও সম্প্রদায়' : 'Connect with SPS' ?>
            </span>
            <h3 style="font-size:1.85rem; font-weight:800; color:#ffffff; margin:var(--space-2xs) 0 var(--space-xs);">
                <?= $isBn ? 'অফিসিয়াল যোগাযোগ ও সামাজিক মাধ্যম' : 'Official Channels & Helpline' ?>
            </h3>
            <p style="font-size:0.92rem; color:#cbd5e1; margin:0;">
                <?= $isBn 
                    ? 'এসপিএস-এর যেকোনো কার্যক্রম, প্রকাশনা, সদস্যপদ বা সহযোগিতার জন্য নিম্নের মাধ্যমগুলোতে সরাসরি সংযুক্ত থাকুন।' 
                    : 'Stay connected through our official digital platforms, scholarly blog, and direct contact numbers.' ?>
            </p>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:var(--space-md); margin-bottom:var(--space-xl);">
            <!-- Facebook -->
            <a href="https://www.facebook.com/bewithsps" target="_blank" rel="noopener noreferrer" style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.15); border-radius:var(--radius-md); padding:16px; text-decoration:none; color:#ffffff; display:flex; align-items:center; gap:var(--space-sm); transition:all 0.2s ease;">
                <span style="font-size:1.8rem;">📘</span>
                <div>
                    <div style="font-weight:700; font-size:0.95rem;">Facebook Page</div>
                    <div style="font-size:0.75rem; color:#94a3b8;">fb.com/bewithsps</div>
                </div>
            </a>

            <!-- YouTube -->
            <a href="https://www.youtube.com/@spsofficial1529" target="_blank" rel="noopener noreferrer" style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.15); border-radius:var(--radius-md); padding:16px; text-decoration:none; color:#ffffff; display:flex; align-items:center; gap:var(--space-sm); transition:all 0.2s ease;">
                <span style="font-size:1.8rem; color:#ef4444;">▶</span>
                <div>
                    <div style="font-weight:700; font-size:0.95rem;">YouTube Channel</div>
                    <div style="font-size:0.75rem; color:#94a3b8;">@spsofficial1529</div>
                </div>
            </a>

            <!-- Official Blog -->
            <a href="https://sanatanphilosophyandscripture.blogspot.com" target="_blank" rel="noopener noreferrer" style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.15); border-radius:var(--radius-md); padding:16px; text-decoration:none; color:#ffffff; display:flex; align-items:center; gap:var(--space-sm); transition:all 0.2s ease;">
                <span style="font-size:1.8rem; color:#f59e0b;">✍</span>
                <div>
                    <div style="font-weight:700; font-size:0.95rem;">Official Blog</div>
                    <div style="font-size:0.75rem; color:#94a3b8;">sanatanphilosophy...</div>
                </div>
            </a>

            <!-- Instagram -->
            <a href="https://www.instagram.com/bewithsps/" target="_blank" rel="noopener noreferrer" style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.15); border-radius:var(--radius-md); padding:16px; text-decoration:none; color:#ffffff; display:flex; align-items:center; gap:var(--space-sm); transition:all 0.2s ease;">
                <span style="font-size:1.8rem; color:#ec4899;">📷</span>
                <div>
                    <div style="font-weight:700; font-size:0.95rem;">Instagram</div>
                    <div style="font-size:0.75rem; color:#94a3b8;">@bewithsps</div>
                </div>
            </a>
        </div>

        <!-- Phone Contacts Bar -->
        <div style="background:rgba(0,0,0,0.25); border:1px solid rgba(255,255,255,0.1); border-radius:var(--radius-md); padding:16px 20px; display:flex; justify-content:space-around; align-items:center; flex-wrap:wrap; gap:var(--space-md); text-align:center;">
            <div>
                <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:1px; color:#94a3b8;"><?= $isBn ? 'প্রধান হটলাইন' : 'Primary Contact' ?></div>
                <div style="font-size:1.1rem; font-weight:700; color:#fcd34d; font-family:monospace;">+880 1736-360041</div>
            </div>
            <div>
                <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:1px; color:#94a3b8;"><?= $isBn ? 'সহকারী হটলাইন' : 'Alternate Helpline' ?></div>
                <div style="font-size:1.1rem; font-weight:700; color:#fcd34d; font-family:monospace;">+880 1782-009415</div>
            </div>
            <div>
                <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:1px; color:#94a3b8;"><?= $isBn ? 'ইমেইল যোগাযোগ' : 'Official Email' ?></div>
                <div style="font-size:1.1rem; font-weight:700; color:#38bdf8; font-family:monospace;">contact@sps-platform.org</div>
            </div>
        </div>
    </div>
</div>
