<?php
/**
 * SPS Public Membership Landing Page
 * Complete Category & Plan Presentation
 */

$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
?>

<!-- Membership Hero Section -->
<section class="section section-hero" style="background: radial-gradient(circle at 50% 20%, rgba(217, 119, 6, 0.08) 0%, rgba(248, 250, 252, 0) 70%), var(--bg-surface); padding: var(--space-3xl) 0 var(--space-2xl); border-bottom: 1px solid var(--border-subtle);">
    <div class="container" style="max-width: 1040px; text-align: center;">
        <div class="hero-badge" style="display: inline-flex; align-items: center; gap: 8px; background: rgba(217, 119, 6, 0.12); color: #b45309; padding: 6px 16px; border-radius: var(--radius-full); font-size: 0.85rem; font-weight: 700; margin-bottom: var(--space-md); border: 1px solid rgba(217, 119, 6, 0.25);">
            <span>🏛️</span>
            <span><?= $isBn ? 'সনাতনী সমাজ বিনির্মাণে সংগঠিত পদযাত্রা' : 'Institutional Membership Architecture' ?></span>
        </div>
        
        <h1 style="font-size: clamp(2rem, 4vw, 2.85rem); font-weight: 800; color: var(--primary-deep); line-height: 1.25; margin-bottom: var(--space-md);">
            <?= $isBn ? 'এসপিএস প্রাতিষ্ঠানিক সদস্যপদ ব্যবস্থা' : 'SPS Complete Membership System' ?>
        </h1>
        
        <p style="font-size: 1.12rem; color: var(--text-secondary); max-width: 780px; margin: 0 auto var(--space-xl); line-height: 1.65;">
            <?= $isBn 
                ? 'সনাতন দর্শন ও শাস্ত্র (SPS)-এর জ্ঞানচর্চা, সমাজসেবা ও সনাতনী ঐক্যের মহাযজ্ঞে যুক্ত হোন। ছাত্র বা পেশাজীবী হিসেবে আপনার সামর্থ্য অনুযায়ী ক্যাটাগরি ও প্ল্যান নির্বাচন করুন।'
                : 'Join the mission of Sanatan Philosophy and Scripture (SPS). Transparent categories, dynamic plans, lifetime immutable Member IDs, and digital membership cards.' ?>
        </p>

        <!-- Live Global Metrics Counters -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: var(--space-md); max-width: 860px; margin: 0 auto var(--space-xl);">
            <div style="background: #ffffff; border: 1px solid var(--border-medium); border-radius: var(--radius-md); padding: var(--space-md); box-shadow: var(--shadow-sm);">
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--primary-deep);"><?= $isBn ? \App\Core\I18n::formatNumber($stats['total_members'] ?? 0) : ($stats['total_members'] ?? 0) ?></div>
                <div style="font-size: 0.82rem; color: var(--text-muted); font-weight: 600;"><?= $isBn ? 'মোট নিবন্ধিত সদস্য' : 'Total Members' ?></div>
            </div>
            <div style="background: #ffffff; border: 1px solid var(--border-medium); border-radius: var(--radius-md); padding: var(--space-md); box-shadow: var(--shadow-sm);">
                <div style="font-size: 1.75rem; font-weight: 800; color: #16a34a;"><?= $isBn ? \App\Core\I18n::formatNumber($stats['active_members'] ?? 0) : ($stats['active_members'] ?? 0) ?></div>
                <div style="font-size: 0.82rem; color: var(--text-muted); font-weight: 600;"><?= $isBn ? 'সক্রিয় সদস্যপদ' : 'Active Members' ?></div>
            </div>
            <div style="background: #ffffff; border: 1px solid var(--border-medium); border-radius: var(--radius-md); padding: var(--space-md); box-shadow: var(--shadow-sm);">
                <div style="font-size: 1.75rem; font-weight: 800; color: #0284c7;"><?= $isBn ? \App\Core\I18n::formatNumber($stats['student_members'] ?? 0) : ($stats['student_members'] ?? 0) ?></div>
                <div style="font-size: 0.82rem; color: var(--text-muted); font-weight: 600;"><?= $isBn ? 'শিক্ষার্থী সদস্য' : 'Student Members' ?></div>
            </div>
            <div style="background: #ffffff; border: 1px solid var(--border-medium); border-radius: var(--radius-md); padding: var(--space-md); box-shadow: var(--shadow-sm);">
                <div style="font-size: 1.75rem; font-weight: 800; color: #7c3aed;"><?= $isBn ? \App\Core\I18n::formatNumber($stats['earning_members'] ?? 0) : ($stats['earning_members'] ?? 0) ?></div>
                <div style="font-size: 0.82rem; color: var(--text-muted); font-weight: 600;"><?= $isBn ? 'উপার্জনশীল সদস্য' : 'Earning Members' ?></div>
            </div>
            <div style="background: #ffffff; border: 1px solid var(--border-medium); border-radius: var(--radius-md); padding: var(--space-md); box-shadow: var(--shadow-sm);">
                <div style="font-size: 1.75rem; font-weight: 800; color: #d97706;"><?= $isBn ? \App\Core\I18n::formatNumber($stats['lifetime_members'] ?? 0) : ($stats['lifetime_members'] ?? 0) ?></div>
                <div style="font-size: 0.82rem; color: var(--text-muted); font-weight: 600;"><?= $isBn ? 'আজীবন সদস্য' : 'Lifetime Members' ?></div>
            </div>
        </div>

        <div style="display: flex; justify-content: center; gap: var(--space-md); flex-wrap: wrap;">
            <a href="<?= url('/membership/apply', $currentLocale) ?>" class="btn btn-primary btn-lg" style="box-shadow: 0 4px 14px rgba(180, 83, 9, 0.25);">
                <span>📝</span>
                <span><?= $isBn ? 'অনলাইনে সদস্যপদ আবেদন করুন' : 'Apply for Membership' ?></span>
            </a>
            <a href="<?= url('/membership/dashboard', $currentLocale) ?>" class="btn btn-secondary btn-lg">
                <span>🪪</span>
                <span><?= $isBn ? 'সদস্য ড্যাশবোর্ড ও কার্ড দেখুন' : 'Member Dashboard & Card' ?></span>
            </a>
        </div>
    </div>
</section>

<!-- Section 1: Primary Member Categories -->
<section class="section" style="padding: var(--space-3xl) 0; background: #ffffff;">
    <div class="container" style="max-width: 1040px;">
        <div style="text-align: center; margin-bottom: var(--space-2xl);">
            <h2 style="font-size: 1.85rem; font-weight: 800; color: var(--primary-deep); margin-bottom: var(--space-xs);">
                <?= $isBn ? '১. সদস্যপদের প্রাথমিক ক্যাটাগরি' : '1. Primary Member Categories' ?>
            </h2>
            <p style="color: var(--text-muted); max-width: 680px; margin: 0 auto;">
                <?= $isBn 
                    ? 'আর্থিক স্বচ্ছতা ও ন্যায়সঙ্গত অংশগ্রহণের জন্য ক্যাটাগরি ও প্ল্যানকে সম্পূর্ণ স্বতন্ত্রভাবে সাজানো হয়েছে।' 
                    : 'Structured cleanly to distinguish member eligibility from payment frequency.' ?>
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: var(--space-xl);">
            <!-- Student Category Card -->
            <div style="background: var(--bg-surface); border: 2px solid #bae6fd; border-radius: var(--radius-lg); padding: var(--space-xl); position: relative; display: flex; flex-direction: column;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-md);">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 2rem;">🎓</span>
                        <h3 style="font-size: 1.4rem; font-weight: 800; color: #0369a1; margin: 0;">
                            <?= $isBn ? 'শিক্ষার্থী সদস্য (Student Member)' : 'Student Member' ?>
                        </h3>
                    </div>
                    <span style="background: #e0f2fe; color: #0284c7; padding: 4px 10px; border-radius: var(--radius-full); font-size: 0.78rem; font-weight: 700;">
                        CODE: STUDENT
                    </span>
                </div>

                <p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6; margin-bottom: var(--space-lg);">
                    <?= $isBn 
                        ? 'যে-সকল সনাতনী তরুণ বর্তমানে স্কুল, কলেজ, বিশ্ববিদ্যালয় বা যে-কোনো স্বীকৃত শিক্ষা প্রতিষ্ঠানে নিয়মিত শিক্ষার্থী হিসেবে অধ্যয়নরত আছেন।' 
                        : 'For youth currently enrolled as bona fide students in academic institutions.' ?>
                </p>

                <div style="background: #ffffff; border: 1px solid #e0f2fe; border-radius: var(--radius-md); padding: var(--space-md); margin-bottom: var(--space-lg);">
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed var(--border-subtle); font-size: 0.9rem;">
                        <span style="color: var(--text-muted);"><?= $isBn ? 'এককালীন এন্ট্রি ফি:' : 'One-time Entry Fee:' ?></span>
                        <strong style="color: var(--primary-deep); font-size: 1.05rem;">৳৫০</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed var(--border-subtle); font-size: 0.9rem;">
                        <span style="color: var(--text-muted);"><?= $isBn ? 'মাসিক সদস্যপদ ফি:' : 'Monthly Membership Fee:' ?></span>
                        <strong style="color: #0284c7; font-size: 1.05rem;">৳৫০ / মাস</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; font-size: 0.9rem;">
                        <span style="color: var(--text-muted);"><?= $isBn ? 'প্রথম মাসের মোট দেয়:' : 'First Month Total:' ?></span>
                        <strong style="color: #16a34a; font-size: 1.1rem;">৳১০০ (৫০ + ৫০)</strong>
                    </div>
                </div>

                <div style="margin-top: auto;">
                    <a href="<?= url('/membership/apply?category=STUDENT&plan=STUDENT_MONTHLY', $currentLocale) ?>" class="btn btn-outline" style="width: 100%; border-color: #0284c7; color: #0284c7; font-weight: 700;">
                        <?= $isBn ? 'শিক্ষার্থী হিসেবে আবেদন করুন →' : 'Apply as Student Member →' ?>
                    </a>
                </div>
            </div>

            <!-- Earning Category Card -->
            <div style="background: var(--bg-surface); border: 2px solid #fed7aa; border-radius: var(--radius-lg); padding: var(--space-xl); position: relative; display: flex; flex-direction: column;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-md);">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 2rem;">💼</span>
                        <h3 style="font-size: 1.4rem; font-weight: 800; color: #c2410c; margin: 0;">
                            <?= $isBn ? 'উপার্জনশীল সদস্য (Earning Member)' : 'Earning Member' ?>
                        </h3>
                    </div>
                    <span style="background: #ffedd5; color: #c2410c; padding: 4px 10px; border-radius: var(--radius-full); font-size: 0.78rem; font-weight: 700;">
                        CODE: EARNING
                    </span>
                </div>

                <p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6; margin-bottom: var(--space-lg);">
                    <?= $isBn 
                        ? 'চাকরিজীবী, ব্যবসায়ী, উদ্যোক্তা, ফ্রিল্যান্সার, ডাক্তার, ইঞ্জিনিয়ার ও সকল স্বাবলম্বী উপার্জনশীল সুধীজন।' 
                        : 'For working professionals, businesspersons, entrepreneurs, freelancers, and earning individuals.' ?>
                </p>

                <div style="background: #ffffff; border: 1px solid #fed7aa; border-radius: var(--radius-md); padding: var(--space-md); margin-bottom: var(--space-lg);">
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed var(--border-subtle); font-size: 0.9rem;">
                        <span style="color: var(--text-muted);"><?= $isBn ? 'এককালীন এন্ট্রি ফি:' : 'One-time Entry Fee:' ?></span>
                        <strong style="color: var(--primary-deep); font-size: 1.05rem;">৳১০০</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed var(--border-subtle); font-size: 0.9rem;">
                        <span style="color: var(--text-muted);"><?= $isBn ? 'মাসিক সদস্যপদ ফি:' : 'Monthly Membership Fee:' ?></span>
                        <strong style="color: #c2410c; font-size: 1.05rem;">৳১০০ / মাস</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; font-size: 0.9rem;">
                        <span style="color: var(--text-muted);"><?= $isBn ? 'প্রথম মাসের মোট দেয়:' : 'First Month Total:' ?></span>
                        <strong style="color: #16a34a; font-size: 1.1rem;">৳২০০ (১০০ + ১০০)</strong>
                    </div>
                </div>

                <div style="margin-top: auto;">
                    <a href="<?= url('/membership/apply?category=EARNING&plan=EARNING_MONTHLY', $currentLocale) ?>" class="btn btn-outline" style="width: 100%; border-color: #c2410c; color: #c2410c; font-weight: 700;">
                        <?= $isBn ? 'উপার্জনশীল সদস্য হিসেবে আবেদন →' : 'Apply as Earning Member →' ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section 2: Membership Plans Matrix -->
<section class="section" style="padding: var(--space-3xl) 0; background: var(--bg-surface); border-top: 1px solid var(--border-subtle);">
    <div class="container" style="max-width: 1040px;">
        <div style="text-align: center; margin-bottom: var(--space-2xl);">
            <div style="color: #b45309; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px;">
                <?= $isBn ? 'নমনীয় ও স্বচ্ছ পেমেন্ট প্যাকেজ' : 'Transparent Payment Packages' ?>
            </div>
            <h2 style="font-size: 1.85rem; font-weight: 800; color: var(--primary-deep); margin-bottom: var(--space-xs);">
                <?= $isBn ? '২. মেম্বারশিপ প্ল্যান ও ফি কাঠামো' : '2. Membership Plans & Fee Structure' ?>
            </h2>
            <p style="color: var(--text-muted); max-width: 680px; margin: 0 auto;">
                <?= $isBn 
                    ? 'টাকার অঙ্ক দিয়ে সরাসরি রোল না দিয়ে ডাটাবেজ সুরক্ষায় প্রতিটি অফার একটি নির্দিষ্ট মেম্বারশিপ প্ল্যান হিসেবে কার্যকর।' 
                    : 'Plans are defined independently from categories so future adjustments preserve historical records.' ?>
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--space-lg);">
            <!-- Monthly Plan Card -->
            <div style="background: #ffffff; border: 1px solid var(--border-medium); border-radius: var(--radius-lg); padding: var(--space-xl); display: flex; flex-direction: column; box-shadow: var(--shadow-sm);">
                <div style="font-size: 0.82rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">
                    <?= $isBn ? 'মাসিক সদস্যপদ' : 'Monthly Plan' ?>
                </div>
                <h3 style="font-size: 1.35rem; font-weight: 800; color: var(--primary-deep); margin: var(--space-xs) 0 var(--space-md);">
                    <?= $isBn ? 'মাসিক কন্ট্রিবিউশন' : 'Monthly Recurring' ?>
                </h3>
                <div style="margin-bottom: var(--space-md); padding-bottom: var(--space-md); border-bottom: 1px solid var(--border-subtle);">
                    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 6px;">
                        <span style="font-size: 0.9rem; color: var(--text-secondary);"><?= $isBn ? 'শিক্ষার্থী:' : 'Student:' ?></span>
                        <span style="font-size: 1.3rem; font-weight: 800; color: #0369a1;">৳৫০<small style="font-size: 0.8rem; font-weight: normal;">/মাস</small></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: baseline;">
                        <span style="font-size: 0.9rem; color: var(--text-secondary);"><?= $isBn ? 'উপার্জনশীল:' : 'Earning Member:' ?></span>
                        <span style="font-size: 1.3rem; font-weight: 800; color: #c2410c;">৳১০০<small style="font-size: 0.8rem; font-weight: normal;">/মাস</small></span>
                    </div>
                </div>

                <ul style="list-style: none; padding: 0; margin: 0 0 var(--space-xl); font-size: 0.88rem; color: var(--text-secondary); display: flex; flex-direction: column; gap: 10px;">
                    <li style="display: flex; gap: 8px;"><span>✓</span><span><?= $isBn ? 'এককালীন এন্ট্রি ফি প্রযোজ্য (ছাত্র ৳৫০, উপার্জনশীল ৳১০০)' : 'One-time entry fee on first month' ?></span></li>
                    <li style="display: flex; gap: 8px;"><span>✓</span><span><?= $isBn ? 'স্বয়ংক্রিয় মাসিক ইনভয়েস ও গ্রেস পিরিয়ড ট্র্যাকিং' : 'Automated monthly due & grace period tracking' ?></span></li>
                    <li style="display: flex; gap: 8px;"><span>✓</span><span><?= $isBn ? 'ডিজিটাল সদস্যপদ কার্ড ও কিউআর যাচাইকরণ' : 'Digital card & verified QR access' ?></span></li>
                    <li style="display: flex; gap: 8px;"><span>✓</span><span><?= $isBn ? 'ব্লগ লেখার সুযোগ ও লাইব্রেরি অ্যাক্সেস' : 'Blog author rights & library bookmarks' ?></span></li>
                </ul>

                <a href="<?= url('/membership/apply', $currentLocale) ?>" class="btn btn-secondary" style="margin-top: auto; width: 100%;">
                    <?= $isBn ? 'মাসিক প্ল্যানে যুক্ত হন' : 'Choose Monthly Plan' ?>
                </a>
            </div>

            <!-- Yearly Plan Card (RECOMMENDED) -->
            <div style="background: #ffffff; border: 2px solid #d97706; border-radius: var(--radius-lg); padding: var(--space-xl); display: flex; flex-direction: column; position: relative; box-shadow: 0 8px 24px rgba(217, 119, 6, 0.12);">
                <div style="position: absolute; top: -13px; left: 50%; transform: translateX(-50%); background: #d97706; color: #ffffff; padding: 2px 14px; border-radius: var(--radius-full); font-size: 0.75rem; font-weight: 800; letter-spacing: 0.5px;">
                    <?= $isBn ? '★ সর্বাধিক জনপ্রিয় (এককালীন)' : '★ MOST POPULAR' ?>
                </div>

                <div style="font-size: 0.82rem; font-weight: 700; color: #b45309; text-transform: uppercase;">
                    <?= $isBn ? 'বাৎসরিক সদস্যপদ' : 'Yearly Plan' ?>
                </div>
                <h3 style="font-size: 1.35rem; font-weight: 800; color: var(--primary-deep); margin: var(--space-xs) 0 var(--space-md);">
                    <?= $isBn ? 'বার্ষিক প্যাকেজ (১২ মাস)' : 'Annual Package (12 Months)' ?>
                </h3>

                <div style="margin-bottom: var(--space-md); padding-bottom: var(--space-md); border-bottom: 1px solid var(--border-subtle); text-align: center;">
                    <div style="font-size: 2.2rem; font-weight: 800; color: #b45309; line-height: 1;">
                        ৳১,০০০
                    </div>
                    <div style="font-size: 0.85rem; color: #16a34a; font-weight: 700; margin-top: 4px;">
                        <?= $isBn ? '🎉 কোনো বাড়তি এন্ট্রি ফি নেই (সম্পূর্ণ অন্তর্ভুক্ত)' : '🎉 No separate entry fee (Included)' ?>
                    </div>
                </div>

                <ul style="list-style: none; padding: 0; margin: 0 0 var(--space-xl); font-size: 0.88rem; color: var(--text-secondary); display: flex; flex-direction: column; gap: 10px;">
                    <li style="display: flex; gap: 8px;"><span>✓</span><span><?= $isBn ? 'এক বছর (৩৬৫ দিন) নিরবচ্ছিন্ন সক্রিয় সদস্যপদ' : 'Full 12 months continuous active status' ?></span></li>
                    <li style="display: flex; gap: 8px;"><span>✓</span><span><?= $isBn ? 'মাসিক পেমেন্টের ঝামেলা নেই' : 'Hassle-free, no recurring monthly billing' ?></span></li>
                    <li style="display: flex; gap: 8px;"><span>✓</span><span><?= $isBn ? 'বাৎসরিক বৈধতার ডিজিটাল সদস্যপদ কার্ড' : 'Yearly validity digital member ID card' ?></span></li>
                    <li style="display: flex; gap: 8px;"><span>✓</span><span><?= $isBn ? 'স্বেচ্ছাসেবক প্রকল্পে অগ্রাধিকার ও অংশগ্রহণ' : 'Priority access to volunteer projects' ?></span></li>
                </ul>

                <a href="<?= url('/membership/apply?plan=YEARLY', $currentLocale) ?>" class="btn btn-primary" style="margin-top: auto; width: 100%; font-weight: 800;">
                    <?= $isBn ? 'বাৎসরিক সদস্য হন (৳১,০০০)' : 'Get Yearly Membership (৳1,000)' ?>
                </a>
            </div>

            <!-- Lifetime Plan Card -->
            <div style="background: linear-gradient(180deg, #ffffff 0%, #fffbeb 100%); border: 2px solid #f59e0b; border-radius: var(--radius-lg); padding: var(--space-xl); display: flex; flex-direction: column; box-shadow: var(--shadow-sm);">
                <div style="font-size: 0.82rem; font-weight: 700; color: #b45309; text-transform: uppercase;">
                    <?= $isBn ? 'আজীবন সদস্যপদ' : 'Lifetime Membership' ?>
                </div>
                <h3 style="font-size: 1.35rem; font-weight: 800; color: var(--primary-deep); margin: var(--space-xs) 0 var(--space-md);">
                    <?= $isBn ? 'আজীবন পৃষ্ঠপোষকতা' : 'Lifetime Benefactor' ?>
                </h3>

                <div style="margin-bottom: var(--space-md); padding-bottom: var(--space-md); border-bottom: 1px solid var(--border-subtle); text-align: center;">
                    <div style="font-size: 2.2rem; font-weight: 800; color: var(--primary-deep); line-height: 1;">
                        ৳১০,০০০
                    </div>
                    <div style="font-size: 0.85rem; color: #b45309; font-weight: 700; margin-top: 4px;">
                        <?= $isBn ? '👑 আজীবনের জন্য এককালীন — কোনো নবায়ন নেই' : '👑 One-time for life — Never expires' ?>
                    </div>
                </div>

                <ul style="list-style: none; padding: 0; margin: 0 0 var(--space-xl); font-size: 0.88rem; color: var(--text-secondary); display: flex; flex-direction: column; gap: 10px;">
                    <li style="display: flex; gap: 8px;"><span>✓</span><span><?= $isBn ? 'আজীবন সক্রিয় মর্যাদা (Expiry: None)' : 'Lifetime Active status (Never expires)' ?></span></li>
                    <li style="display: flex; gap: 8px;"><span>✓</span><span><?= $isBn ? 'এন্ট্রি ফি সম্পূর্ণ মওকুফ' : 'Entry fee fully absorbed' ?></span></li>
                    <li style="display: flex; gap: 8px;"><span>✓</span><span><?= $isBn ? 'বিশেষ সোনালী আজীবন সদস্যপদ ডিজিটাল কার্ড' : 'Exclusive Gold Lifetime Member Digital Card' ?></span></li>
                    <li style="display: flex; gap: 8px;"><span>✓</span><span><?= $isBn ? 'ব্লগ লেখক প্রোফাইলে ঐচ্ছিক আজীবন ব্যাজ' : 'Optional Lifetime Member badge on author profile' ?></span></li>
                </ul>

                <a href="<?= url('/membership/apply?plan=LIFETIME', $currentLocale) ?>" class="btn btn-outline" style="margin-top: auto; width: 100%; border-color: #b45309; color: #b45309; font-weight: 800;">
                    <?= $isBn ? 'আজীবন সদস্যপদ গ্রহণ করুন (৳১০,০০০)' : 'Become Lifetime Member (৳10,000)' ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Architecture Highlights: ID, Transition, & Card Preview -->
<section class="section" style="padding: var(--space-3xl) 0; background: #ffffff;">
    <div class="container" style="max-width: 1040px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: var(--space-2xl); align-items: center;">
            
            <!-- Left: Structural Governance Features -->
            <div>
                <div style="color: #b45309; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; margin-bottom: 6px;">
                    <?= $isBn ? 'ক্লিন আর্কিটেকচার ও ডেটা গভর্নেন্স' : 'Clean Enterprise Architecture' ?>
                </div>
                <h2 style="font-size: 1.85rem; font-weight: 800; color: var(--primary-deep); margin-bottom: var(--space-md); line-height: 1.3;">
                    <?= $isBn ? 'স্থায়ী মেম্বার আইডি এবং স্বচ্ছ ট্রানজিশন হিস্টোরি' : 'Immutable Member ID & Transparent Transition' ?>
                </h2>
                
                <div style="display: flex; flex-direction: column; gap: var(--space-md);">
                    <div style="display: flex; gap: 12px;">
                        <span style="font-size: 1.4rem;">🆔</span>
                        <div>
                            <strong style="color: var(--primary-deep); font-size: 0.98rem;"><?= $isBn ? 'স্থায়ী অপরিবর্তনীয় মেম্বার আইডি (e.g. SPS-000872)' : 'Lifetime Permanent ID' ?></strong>
                            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 4px 0 0;">
                                <?= $isBn 
                                    ? 'ছাত্রাবস্থা থেকে কর্মজীবনে গেলেও আপনার আইডি পরিবর্তন হবে না। সারা জীবনের জন্য আপনার একটিই অনন্য পরিচিতি থাকবে।' 
                                    : 'Your Member ID remains constant across your entire life, ensuring unbroken continuity.' ?>
                            </p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px;">
                        <span style="font-size: 1.4rem;">🔄</span>
                        <div>
                            <strong style="color: var(--primary-deep); font-size: 0.98rem;"><?= $isBn ? 'ছাত্র → উপার্জনশীল সদস্য রূপান্তর (Transition)' : 'Student to Earning Transition' ?></strong>
                            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 4px 0 0;">
                                <?= $isBn 
                                    ? 'গ্র্যাজুয়েশন শেষে ড্যাশবোর্ড থেকে এক ক্লিকে ক্যাটাগরি আপডেট করা যায়। সিস্টেম পুরনো ইতিহাস (যেমন: ২০২৪-২০২৬ শিক্ষার্থী সদস্য) অক্ষুণ্ণ রাখে।' 
                                    : 'Transition seamlessly with complete preservation of past membership logs.' ?>
                            </p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px;">
                        <span style="font-size: 1.4rem;">🛡️</span>
                        <div>
                            <strong style="color: var(--primary-deep); font-size: 0.98rem;"><?= $isBn ? 'মেম্বারশিপ বনাম অ্যাডমিন সিকিউরিটি পৃথকীকরণ' : 'Strict Member vs Admin Role Separation' ?></strong>
                            <p style="color: var(--text-muted); font-size: 0.88rem; margin: 4px 0 0;">
                                <?= $isBn 
                                    ? 'সদস্যপদ কখনো প্রশাসনিক বা আর্থিক ক্ষমতা প্রদান করে না। অ্যাডমিন রোল (RBAC) সম্পূর্ণ আলাদা কাঠামোগত সুরক্ষায় পরিচালিত।' 
                                    : 'Membership status never automatically confers administrative privileges.' ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Interactive Digital Membership Card Sample -->
            <div>
                <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-radius: 16px; padding: 24px; color: #ffffff; box-shadow: 0 16px 36px rgba(15, 23, 42, 0.25); border: 1px solid rgba(255,255,255,0.1); position: relative; overflow: hidden;">
                    <!-- Watermark -->
                    <div style="position: absolute; right: -20px; bottom: -20px; font-size: 140px; color: rgba(255,255,255,0.03); pointer-events: none; font-weight: 900;">
                        SPS
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <img src="<?= asset('assets/images/brand/sps-logo.png') ?>" alt="SPS" style="height: 38px; width: auto; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.4));">
                            <div>
                                <div style="font-size: 0.92rem; font-weight: 800; letter-spacing: 0.5px; color: #f8fafc;">সনাতন দর্শন ও শাস্ত্র</div>
                                <div style="font-size: 0.68rem; color: #94a3b8; letter-spacing: 0.8px;">SANATAN PHILOSOPHY & SCRIPTURE</div>
                            </div>
                        </div>
                        <span style="background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid rgba(74, 222, 128, 0.4); padding: 3px 10px; border-radius: var(--radius-full); font-size: 0.72rem; font-weight: 700;">
                            ● ACTIVE
                        </span>
                    </div>

                    <div style="display: flex; gap: 18px; align-items: center; margin-bottom: 20px;">
                        <div style="width: 64px; height: 64px; border-radius: 12px; background: #334155; display: flex; align-items: center; justify-content: center; font-size: 2rem; border: 2px solid rgba(255,255,255,0.2);">
                            👤
                        </div>
                        <div>
                            <div style="font-size: 1.15rem; font-weight: 800; color: #ffffff;">অমিত সেন (Amit Sen)</div>
                            <div style="font-size: 0.85rem; color: #38bdf8; font-weight: 700; font-family: monospace;">SPS-000872</div>
                            <div style="font-size: 0.78rem; color: #cbd5e1; margin-top: 2px;">শিক্ষার্থী সদস্য • বাৎসরিক প্ল্যান</div>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: flex-end; padding-top: 14px; border-top: 1px solid rgba(255,255,255,0.12);">
                        <div>
                            <div style="font-size: 0.68rem; color: #94a3b8; text-transform: uppercase;"><?= $isBn ? 'মেয়াদ উত্তীর্ণ:' : 'Valid Until:' ?></div>
                            <div style="font-size: 0.88rem; font-weight: 700; color: #fde047;">২৫ সেপ্টেম্বর ২০২৭</div>
                        </div>

                        <!-- Simulated Safe QR Code -->
                        <div style="text-align: right;">
                            <a href="<?= url('/membership/verify?code=SPS-000872', $currentLocale) ?>" target="_blank" style="display: inline-block; background: #ffffff; padding: 6px; border-radius: 6px; text-decoration: none;" title="<?= $isBn ? 'কিউআর যাচাইকরণ প্রিভিউ' : 'Test QR Verification' ?>">
                                <div style="width: 44px; height: 44px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 2px;">
                                    <div style="background: #0f172a;"></div><div style="background: #0f172a;"></div><div style="background: #0f172a;"></div>
                                    <div style="background: #0f172a;"></div><div style="background: #fff;"></div><div style="background: #0f172a;"></div>
                                    <div style="background: #0f172a;"></div><div style="background: #0f172a;"></div><div style="background: #0f172a;"></div>
                                </div>
                            </a>
                            <div style="font-size: 0.65rem; color: #94a3b8; margin-top: 4px;">Public QR Verify</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Final Call to Action -->
<section class="section" style="padding: var(--space-3xl) 0; background: var(--bg-surface); text-align: center; border-top: 1px solid var(--border-subtle);">
    <div class="container" style="max-width: 720px;">
        <h2 style="font-size: 1.85rem; font-weight: 800; color: var(--primary-deep); margin-bottom: var(--space-xs);">
            <?= $isBn ? 'আজই যুক্ত হোন সনাতনী ঐক্য ও কল্যাণের মহাযজ্ঞে' : 'Join the SPS Family Today' ?>
        </h2>
        <p style="color: var(--text-muted); font-size: 1rem; margin-bottom: var(--space-xl); line-height: 1.6;">
            <?= $isBn 
                ? 'অনলাইনে আবেদন পূরণ করুন, ডিজিটাল ট্রানজেকশন সম্পন্ন করুন এবং সক্রিয় সদস্যপদ ডিজিটাল কার্ড গ্রহণ করুন।' 
                : 'Complete the short online form, verify your payment, and get your instant verifiable digital card.' ?>
        </p>
        <div style="display: flex; justify-content: center; gap: var(--space-md); flex-wrap: wrap;">
            <a href="<?= url('/membership/apply', $currentLocale) ?>" class="btn btn-primary btn-lg">
                <?= $isBn ? 'সদস্যপদ ফরম পূরণ করুন →' : 'Complete Application Form →' ?>
            </a>
            <a href="<?= url('/membership/dashboard', $currentLocale) ?>" class="btn btn-outline btn-lg">
                <?= $isBn ? 'সদস্য ড্যাশবোর্ডে প্রবেশ করুন' : 'Member Dashboard Login' ?>
            </a>
        </div>
    </div>
</section>
