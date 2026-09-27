<?php
/**
 * SPS Membership Application Form
 * Interactive Category & Plan Selector with Dynamic Fee Breakdown
 */

$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';

$selectedCategory = $selectedCategory ?? 'STUDENT';
$selectedPlan = $selectedPlan ?? 'STUDENT_MONTHLY';
?>

<section class="section" style="padding: var(--space-3xl) 0; background: var(--bg-surface);">
    <div class="container" style="max-width: 860px;">

        <!-- Header -->
        <div style="text-align: center; margin-bottom: var(--space-2xl);">
            <a href="<?= url('/membership', $currentLocale) ?>" style="display: inline-flex; align-items: center; gap: 6px; color: var(--text-muted); text-decoration: none; font-size: 0.85rem; font-weight: 600; margin-bottom: var(--space-sm);">
                ← <?= $isBn ? 'সদস্যপদ বিবরণী ও প্ল্যানসমূহ' : 'Back to Membership Hub' ?>
            </a>
            <h1 style="font-size: clamp(1.8rem, 3.5vw, 2.4rem); font-weight: 800; color: var(--primary-deep); margin: 0 0 var(--space-xs);">
                <?= $isBn ? 'এসপিএস সদস্যপদ আবেদন ফরম' : 'SPS Membership Application Form' ?>
            </h1>
            <p style="color: var(--text-muted); font-size: 0.95rem; max-width: 600px; margin: 0 auto;">
                <?= $isBn 
                    ? 'আপনার সঠিক তথ্য প্রদান করে সনাতন ফিলোসফি এন্ড স্ক্রিপচার (SPS)-এর প্রাতিষ্ঠানিক সদস্যপদ গ্রহণ করুন।' 
                    : 'Fill in accurate details to register your verified membership in Sanatan Philosophy & Scripture.' ?>
            </p>
        </div>

        <!-- Flash messages -->
        <?php if ($success = \App\Core\Session::getFlash('success')): ?>
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: var(--space-md) var(--space-lg); border-radius: var(--radius-md); margin-bottom: var(--space-xl); display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 1.3rem;">✓</span>
                <div><?= e($success) ?></div>
            </div>
        <?php endif; ?>

        <?php if ($error = \App\Core\Session::getFlash('error')): ?>
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: var(--space-md) var(--space-lg); border-radius: var(--radius-md); margin-bottom: var(--space-xl); display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 1.3rem;">⚠️</span>
                <div><?= e($error) ?></div>
            </div>
        <?php endif; ?>

        <form action="<?= url('/membership/apply', $currentLocale) ?>" method="POST" enctype="multipart/form-data" id="membershipApplyForm" style="background: #ffffff; border: 1px solid var(--border-medium); border-radius: var(--radius-xl); padding: var(--space-2xl); box-shadow: var(--shadow-sm);">
            <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>

            <!-- Step 1: Category Selection -->
            <div style="margin-bottom: var(--space-2xl);">
                <label style="display: block; font-weight: 800; font-size: 1.05rem; color: var(--primary-deep); margin-bottom: var(--space-sm);">
                    <?= $isBn ? '১. সদস্যপদের ক্যাটাগরি নির্বাচন করুন *' : '1. Select Member Category *' ?>
                </label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                    <label style="cursor: pointer;">
                        <input type="radio" name="category_id" value="STUDENT" class="sr-only" <?= $selectedCategory === 'STUDENT' ? 'checked' : '' ?> onchange="onCategoryChange('STUDENT')">
                        <div id="cat-card-STUDENT" class="category-pill-card" style="border: 2px solid <?= $selectedCategory === 'STUDENT' ? '#0284c7' : 'var(--border-medium)' ?>; background: <?= $selectedCategory === 'STUDENT' ? '#f0f9ff' : '#ffffff' ?>; padding: var(--space-md); border-radius: var(--radius-lg); text-align: center; transition: all 0.2s ease;">
                            <div style="font-size: 1.8rem; margin-bottom: 4px;">🎓</div>
                            <div style="font-weight: 800; font-size: 1rem; color: #0369a1;"><?= $isBn ? 'শিক্ষার্থী সদস্য' : 'Student Member' ?></div>
                            <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 4px;"><?= $isBn ? 'স্কুল/কলেজ/বিশ্ববিদ্যালয়ের ছাত্রছাত্রী' : 'Academic students' ?></div>
                            <div style="font-size: 0.82rem; font-weight: 700; color: #0284c7; margin-top: 6px;">৳৫০ এন্ট্রি + ৳৫০/মাস</div>
                        </div>
                    </label>

                    <label style="cursor: pointer;">
                        <input type="radio" name="category_id" value="EARNING" class="sr-only" <?= $selectedCategory === 'EARNING' ? 'checked' : '' ?> onchange="onCategoryChange('EARNING')">
                        <div id="cat-card-EARNING" class="category-pill-card" style="border: 2px solid <?= $selectedCategory === 'EARNING' ? '#c2410c' : 'var(--border-medium)' ?>; background: <?= $selectedCategory === 'EARNING' ? '#fff7ed' : '#ffffff' ?>; padding: var(--space-md); border-radius: var(--radius-lg); text-align: center; transition: all 0.2s ease;">
                            <div style="font-size: 1.8rem; margin-bottom: 4px;">💼</div>
                            <div style="font-weight: 800; font-size: 1rem; color: #c2410c;"><?= $isBn ? 'উপার্জনশীল সদস্য' : 'Earning Member' ?></div>
                            <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 4px;"><?= $isBn ? 'চাকরিজীবী, ব্যবসায়ী, উদ্যোক্তা, ফ্রিল্যান্সার' : 'Professionals & businessmen' ?></div>
                            <div style="font-size: 0.82rem; font-weight: 700; color: #c2410c; margin-top: 6px;">৳১০০ এন্ট্রি + ৳১০০/মাস</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Step 2: Plan Selection -->
            <div style="margin-bottom: var(--space-2xl);">
                <label style="display: block; font-weight: 800; font-size: 1.05rem; color: var(--primary-deep); margin-bottom: var(--space-sm);">
                    <?= $isBn ? '২. সদস্যপদ মেয়াদের প্ল্যান নির্বাচন করুন *' : '2. Select Membership Plan *' ?>
                </label>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: var(--space-md);">
                    
                    <!-- Monthly Option -->
                    <label style="cursor: pointer;">
                        <input type="radio" name="plan_id" id="plan-monthly-radio" value="<?= $selectedCategory === 'STUDENT' ? 'STUDENT_MONTHLY' : 'EARNING_MONTHLY' ?>" class="sr-only" <?= in_array($selectedPlan, ['STUDENT_MONTHLY', 'EARNING_MONTHLY']) ? 'checked' : '' ?> onchange="onPlanChange('monthly')">
                        <div id="plan-card-monthly" class="plan-pill-card" style="border: 2px solid var(--border-medium); background: #ffffff; padding: var(--space-md); border-radius: var(--radius-lg); text-align: center; transition: all 0.2s ease;">
                            <div style="font-weight: 700; font-size: 0.95rem; color: var(--primary-deep);"><?= $isBn ? 'মাসিক সদস্যপদ' : 'Monthly Plan' ?></div>
                            <div id="monthly-fee-label" style="font-size: 1.3rem; font-weight: 800; color: #b45309; margin: 4px 0;">৳৫০/মাস</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);"><?= $isBn ? 'মাসিক ভিত্তিতে নবায়নযোগ্য' : 'Monthly recurring' ?></div>
                        </div>
                    </label>

                    <!-- Yearly Option -->
                    <label style="cursor: pointer;">
                        <input type="radio" name="plan_id" id="plan-yearly-radio" value="YEARLY" class="sr-only" <?= $selectedPlan === 'YEARLY' ? 'checked' : '' ?> onchange="onPlanChange('yearly')">
                        <div id="plan-card-yearly" class="plan-pill-card" style="border: 2px solid var(--border-medium); background: #ffffff; padding: var(--space-md); border-radius: var(--radius-lg); text-align: center; transition: all 0.2s ease; position: relative;">
                            <span style="position: absolute; top: -10px; right: 10px; background: #d97706; color: #fff; font-size: 0.68rem; font-weight: 800; padding: 1px 8px; border-radius: var(--radius-full);"><?= $isBn ? 'জনপ্রিয়' : 'Best' ?></span>
                            <div style="font-weight: 700; font-size: 0.95rem; color: var(--primary-deep);"><?= $isBn ? 'বাৎসরিক সদস্যপদ' : 'Yearly Plan' ?></div>
                            <div style="font-size: 1.3rem; font-weight: 800; color: #b45309; margin: 4px 0;">৳১,০০০/বছর</div>
                            <div style="font-size: 0.75rem; color: #16a34a; font-weight: 600;"><?= $isBn ? 'এন্ট্রি ফি নেই (১২ মাস)' : 'No entry fee (12 mo)' ?></div>
                        </div>
                    </label>

                    <!-- Lifetime Option -->
                    <label style="cursor: pointer;">
                        <input type="radio" name="plan_id" id="plan-lifetime-radio" value="LIFETIME" class="sr-only" <?= $selectedPlan === 'LIFETIME' ? 'checked' : '' ?> onchange="onPlanChange('lifetime')">
                        <div id="plan-card-lifetime" class="plan-pill-card" style="border: 2px solid var(--border-medium); background: #ffffff; padding: var(--space-md); border-radius: var(--radius-lg); text-align: center; transition: all 0.2s ease;">
                            <div style="font-weight: 700; font-size: 0.95rem; color: var(--primary-deep);"><?= $isBn ? 'আজীবন সদস্যপদ' : 'Lifetime Plan' ?></div>
                            <div style="font-size: 1.3rem; font-weight: 800; color: #b45309; margin: 4px 0;">৳১০,০০০</div>
                            <div style="font-size: 0.75rem; color: #b45309; font-weight: 600;"><?= $isBn ? 'আজীবন কোনো নবায়ন নেই' : 'Permanent Active' ?></div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Dynamic Live Fee Calculation Box -->
            <div id="fee-summary-box" style="background: #fffbeb; border: 1px solid #fde68a; border-radius: var(--radius-lg); padding: var(--space-md) var(--space-lg); margin-bottom: var(--space-2xl);">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--space-md);">
                    <div>
                        <div style="font-size: 0.85rem; color: #92400e; font-weight: 700;"><?= $isBn ? 'পেমেন্ট সারসংক্ষেপ (Fee Breakdown):' : 'Payment Calculation Summary:' ?></div>
                        <div id="fee-breakdown-text" style="font-size: 0.9rem; color: var(--text-secondary); margin-top: 2px;">
                            <?= $isBn ? 'এককালীন এন্ট্রি ফি ৳৫০ + প্রথম মাসের ফি ৳৫০' : 'Entry fee ৳50 + 1st month ৳50' ?>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.78rem; color: #92400e; text-transform: uppercase; font-weight: 700;"><?= $isBn ? 'মোট প্রদেয়' : 'Total Payable' ?></div>
                        <div id="total-payable-amount" style="font-size: 1.8rem; font-weight: 800; color: #b45309; line-height: 1;">
                            ৳১০০
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Member Basic Information -->
            <div style="margin-bottom: var(--space-2xl);">
                <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--primary-deep); margin: 0 0 var(--space-md); padding-bottom: 6px; border-bottom: 1px solid var(--border-subtle);">
                    <?= $isBn ? '৩. ব্যক্তিগত পরিচিতি' : '3. Personal Information' ?>
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md); margin-bottom: var(--space-md);">
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                            <?= $isBn ? 'পূর্ণ নাম (বাংলায়) *' : 'Full Name (Bengali) *' ?>
                        </label>
                        <input type="text" name="name_bn" required placeholder="উদাঃ অমিত সেন" class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-medium); border-radius: var(--radius-md);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                            <?= $isBn ? 'পূর্ণ নাম (ইংরেজিতে) *' : 'Full Name (English) *' ?>
                        </label>
                        <input type="text" name="name_en" required placeholder="e.g. Amit Sen" class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-medium); border-radius: var(--radius-md);">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md); margin-bottom: var(--space-md);">
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                            <?= $isBn ? 'ইমেইল ঠিকানা *' : 'Email Address *' ?>
                        </label>
                        <input type="email" name="email" required placeholder="name@example.com" class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-medium); border-radius: var(--radius-md);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                            <?= $isBn ? 'মোবাইল নম্বর *' : 'Phone Number *' ?>
                        </label>
                        <input type="tel" name="phone" required placeholder="01XXXXXXXXX" class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-medium); border-radius: var(--radius-md);">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md); margin-bottom: var(--space-md);">
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                            <?= $isBn ? 'জেলা' : 'District' ?>
                        </label>
                        <input type="text" name="district" placeholder="<?= $isBn ? 'উদাঃ ঢাকা / চট্টগ্রাম' : 'e.g. Dhaka' ?>" class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-medium); border-radius: var(--radius-md);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                            <?= $isBn ? 'উপজেলা / এলাকা' : 'Upazila / Area' ?>
                        </label>
                        <input type="text" name="upazila" placeholder="<?= $isBn ? 'উদাঃ ধানমন্ডি' : 'e.g. Dhanmondi' ?>" class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-medium); border-radius: var(--radius-md);">
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                        <?= $isBn ? 'বর্তমান ঠিকানা' : 'Address' ?>
                    </label>
                    <input type="text" name="address" placeholder="<?= $isBn ? 'বাসা/রোড/এলাকার বিবরণ' : 'Full address' ?>" class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-medium); border-radius: var(--radius-md);">
                </div>
            </div>

            <!-- Conditional Section A: Student Details -->
            <div id="student-fields-sec" style="margin-bottom: var(--space-2xl); <?= $selectedCategory === 'STUDENT' ? '' : 'display: none;' ?>">
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0369a1; margin: 0 0 var(--space-md); padding-bottom: 6px; border-bottom: 1px solid #bae6fd;">
                    <?= $isBn ? '৪. শিক্ষাগত তথ্য (Student Details)' : '4. Educational Information' ?>
                </h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md); margin-bottom: var(--space-md);">
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                            <?= $isBn ? 'শিক্ষা প্রতিষ্ঠান *' : 'Educational Institution *' ?>
                        </label>
                        <input type="text" name="institution" placeholder="উদাঃ ঢাকা বিশ্ববিদ্যালয়" class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-medium); border-radius: var(--radius-md);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                            <?= $isBn ? 'বিভাগ / বিষয়' : 'Department / Subject' ?>
                        </label>
                        <input type="text" name="department" placeholder="উদাঃ দর্শন / কম্পিউটার সায়েন্স" class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-medium); border-radius: var(--radius-md);">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                            <?= $isBn ? 'শ্রেণি / শিক্ষাবর্ষ' : 'Class / Academic Year' ?>
                        </label>
                        <input type="text" name="class_year" placeholder="উদাঃ ৩য় বর্ষ (অনার্স)" class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-medium); border-radius: var(--radius-md);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                            <?= $isBn ? 'শিক্ষার্থী আইডি (ঐচ্ছিক)' : 'Student ID (Optional)' ?>
                        </label>
                        <input type="text" name="student_id" placeholder="ID Number" class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-medium); border-radius: var(--radius-md);">
                    </div>
                </div>
            </div>

            <!-- Conditional Section B: Earning Details -->
            <div id="earning-fields-sec" style="margin-bottom: var(--space-2xl); <?= $selectedCategory === 'EARNING' ? '' : 'display: none;' ?>">
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #c2410c; margin: 0 0 var(--space-md); padding-bottom: 6px; border-bottom: 1px solid #fed7aa;">
                    <?= $isBn ? '৪. পেশাগত তথ্য (Professional Details)' : '4. Professional Details' ?>
                </h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md); margin-bottom: var(--space-md);">
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                            <?= $isBn ? 'পেশা / পদবি *' : 'Profession / Designation *' ?>
                        </label>
                        <input type="text" name="profession_title" placeholder="উদাঃ সফটওয়্যার ইঞ্জিনিয়ার / ব্যবসায়ী" class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-medium); border-radius: var(--radius-md);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                            <?= $isBn ? 'প্রতিষ্ঠান / ব্যবসার নাম' : 'Organization / Business Name' ?>
                        </label>
                        <input type="text" name="organization" placeholder="উদাঃ টেক লিমিটেড / নিজস্ব ব্যবসা" class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-medium); border-radius: var(--radius-md);">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                            <?= $isBn ? 'কর্মক্ষেত্রের ধরন' : 'Category / Industry' ?>
                        </label>
                        <select name="business_category" class="form-select" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-medium); border-radius: var(--radius-md);">
                            <option value="IT & Tech">আইটি ও প্রযুক্তি (IT & Tech)</option>
                            <option value="Business & Trade">ব্যবসা ও বাণিজ্য (Business & Trade)</option>
                            <option value="Medical & Healthcare">চিকিৎসা সেবা (Healthcare)</option>
                            <option value="Education & Academia">শিক্ষা ও শিক্ষকতা (Education)</option>
                            <option value="Legal & Law">আইন ও বিচার (Legal)</option>
                            <option value="Engineering & Construction">প্রকৌশল ও নির্মাণ (Engineering)</option>
                            <option value="Finance & Banking">ব্যাংকিং ও ফাইন্যান্স (Finance)</option>
                            <option value="Media & Journalism">গণমাধ্যম ও প্রকাশনা (Media)</option>
                            <option value="Other">অন্যান্য (Other)</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                            <?= $isBn ? 'বিশেষ দক্ষতা (Skills)' : 'Key Skills' ?>
                        </label>
                        <input type="text" name="skills" placeholder="উদাঃ ওয়েব ডেভেলপমেন্ট, লেখালেখি, হিসাবরক্ষণ" class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-medium); border-radius: var(--radius-md);">
                    </div>
                </div>
            </div>

            <!-- Step 5: Volunteer Participation (Optional) -->
            <div style="margin-bottom: var(--space-2xl); background: var(--bg-surface); padding: var(--space-lg); border-radius: var(--radius-lg); border: 1px solid var(--border-subtle);">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 700; color: var(--primary-deep); font-size: 1rem; margin-bottom: var(--space-sm);">
                    <input type="checkbox" name="apply_volunteer" value="1" id="volunteerToggle" onchange="document.getElementById('vol-options').style.display = this.checked ? 'block' : 'none';">
                    <span><?= $isBn ? 'আমি এসপিএস-এর সেবামূলক কাজে ‘স্বেচ্ছাসেবক (Volunteer)’ হিসেবে অংশ নিতে ইচ্ছুক' : 'I would like to participate as an active SPS Volunteer' ?></span>
                </label>

                <div id="vol-options" style="display: none; margin-top: var(--space-md); padding-top: var(--space-md); border-top: 1px dashed var(--border-medium);">
                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: var(--space-sm);">
                        <?= $isBn ? 'আপনার আগ্রহের সেবা খাতসমূহ নির্বাচন করুন:' : 'Select volunteer service categories:' ?>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 8px; font-size: 0.85rem;">
                        <label style="display: flex; align-items: center; gap: 6px;"><input type="checkbox" name="volunteer_interests[]" value="Education"> শিক্ষা বিস্তার (Education)</label>
                        <label style="display: flex; align-items: center; gap: 6px;"><input type="checkbox" name="volunteer_interests[]" value="Food Distribution"> খাদ্য বিতরণ (Food)</label>
                        <label style="display: flex; align-items: center; gap: 6px;"><input type="checkbox" name="volunteer_interests[]" value="Medical Assistance"> চিকিৎসা সেবা (Medical)</label>
                        <label style="display: flex; align-items: center; gap: 6px;"><input type="checkbox" name="volunteer_interests[]" value="IT & Web"> আইটি ও ডিজিটাল (IT)</label>
                        <label style="display: flex; align-items: center; gap: 6px;"><input type="checkbox" name="volunteer_interests[]" value="Scripture & Dharma"> শাস্ত্র প্রচার (Scripture)</label>
                        <label style="display: flex; align-items: center; gap: 6px;"><input type="checkbox" name="volunteer_interests[]" value="Events & Logistics"> অনুষ্ঠান ব্যবস্থাপনা (Events)</label>
                    </div>
                </div>
            </div>

            <!-- Step 6: Payment Details -->
            <div style="margin-bottom: var(--space-2xl);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-md); padding-bottom: 6px; border-bottom: 1px solid var(--border-subtle); flex-wrap: wrap; gap: 8px;">
                    <h3 style="font-size: 1.18rem; font-weight: 800; color: var(--primary-deep); margin: 0; display: flex; align-items: center; gap: 8px;">
                        <span>💳</span>
                        <span><?= $isBn ? '৫. পেমেন্ট ও ট্রানজেকশন তথ্য (Payment Details)' : '5. Payment & Transaction Info' ?></span>
                    </h3>
                    <span style="font-size: 0.76rem; background: #fef3c7; color: #b45309; padding: 3px 10px; border-radius: var(--radius-full); font-weight: 800; border: 1px solid #fde68a;">
                        🪙 <?= $isBn ? 'ফাইন্যান্স অফিসার কর্তৃক যাচাইযোগ্য' : 'Verified by Finance Officer' ?>
                    </span>
                </div>

                <!-- bKash Send Money Step-by-Step Instruction Box -->
                <div style="background: linear-gradient(135deg, #fdf2f8 0%, #fff1f2 100%); border: 1.5px solid #fbcfe8; border-radius: var(--radius-lg); padding: 16px 20px; margin-bottom: var(--space-lg); box-shadow: 0 2px 6px rgba(190, 24, 93, 0.05);">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-bottom: 12px; border-bottom: 1px dashed #f472b6; padding-bottom: 10px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 1.3rem;">📱</span>
                            <div>
                                <div style="font-weight: 800; color: #9d174d; font-size: 0.95rem;">
                                    <?= $isBn ? 'বিকাশ পেমেন্ট নির্দেশিকা (bKash Send Money Instructions)' : 'bKash Send Money Payment Guide' ?>
                                </div>
                                <div style="font-size: 0.78rem; color: #be185d;">
                                    <?= $isBn ? 'সদস্যপদ ফি পাঠানোর পর TrxID ও সফল লেনদেনের স্ক্রিনশট (Sent SS) প্রদান বাধ্যতামূলক।' : 'Providing TrxID & Sent Money screenshot is strictly mandatory.' ?>
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; background: #ffffff; border: 1px solid #f472b6; padding: 4px 12px; border-radius: var(--radius-md);">
                            <span style="font-size: 0.78rem; font-weight: 700; color: #9d174d;"><?= $isBn ? 'অফিসিয়াল বিকাশ নম্বর:' : 'Official bKash:' ?></span>
                            <code id="bkashNumberText" style="font-family: monospace; font-size: 0.92rem; font-weight: 900; color: #be185d;">01700-000000</code>
                            <button type="button" onclick="copyBkashNumber(this)" style="background: #fdf2f8; border: 1px solid #fbcfe8; border-radius: 4px; padding: 2px 6px; font-size: 0.72rem; cursor: pointer; color: #9d174d; font-weight: 700;">
                                📋 <?= $isBn ? 'কপি' : 'Copy' ?>
                            </button>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; font-size: 0.82rem; color: #831843;">
                        <div style="background: rgba(255,255,255,0.7); padding: 8px 12px; border-radius: 6px; border: 1px solid #fce7f3;">
                            <strong>১. বিকাশ অ্যাপে যান:</strong> Send Money অপশন নির্বাচন করে ওপরের নম্বরে নির্ধারিত ফি পাঠান।
                        </div>
                        <div style="background: rgba(255,255,255,0.7); padding: 8px 12px; border-radius: 6px; border: 1px solid #fce7f3;">
                            <strong>২. রেফারেন্স দিন:</strong> রেফারেন্সে আপনার ফোন নম্বর বা নাম উল্লেখ করুন।
                        </div>
                        <div style="background: rgba(255,255,255,0.7); padding: 8px 12px; border-radius: 6px; border: 1px solid #fce7f3;">
                            <strong>৩. TrxID সংগ্রহ করুন:</strong> সফল লেনদেনের পর প্রাপ্ত ১০ ডিজিটের ট্রানজেকশন কোড কপি করুন।
                        </div>
                        <div style="background: rgba(255,255,255,0.7); padding: 8px 12px; border-radius: 6px; border: 1px solid #fce7f3;">
                            <strong>৪. Sent SS তুলুন:</strong> সফল ট্রানজেকশন স্ক্রিনের একটি স্ক্রিনশট নিয়ে নিচে আপলোড করুন।
                        </div>
                    </div>
                </div>

                <!-- Payment Method & Sender Phone -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md); margin-bottom: var(--space-md);">
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                            <?= $isBn ? 'পেমেন্ট মাধ্যম *' : 'Payment Method *' ?>
                        </label>
                        <select name="payment_method" id="payment_method_select" onchange="onPaymentMethodChange(this.value)" class="form-select" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-medium); border-radius: var(--radius-md); font-weight: 700;">
                            <option value="bKash" selected>🌸 bKash (বিকাশ - সেন্ড মানি)</option>
                            <option value="Nagad">🔥 Nagad (নগদ)</option>
                            <option value="Rocket">🚀 Rocket (রকেট)</option>
                            <option value="Bank">🏦 Bank Transfer (ব্যাংক ট্রান্সফার)</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                            <?= $isBn ? 'যে নম্বর থেকে টাকা পাঠিয়েছেন (Sender Number) *' : 'Sender Mobile Number *' ?>
                        </label>
                        <input type="text" name="sender_number" id="sender_number_input" required placeholder="01XXXXXXXXX" class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-medium); border-radius: var(--radius-md); font-family: monospace; font-weight: 700;">
                        <div style="font-size: 0.74rem; color: var(--text-muted); margin-top: 3px;">
                            <?= $isBn ? 'প্রেরক বিকাশ একাউন্ট নম্বর যা স্টেটমেন্টে প্রদর্শিত হবে।' : 'Sender mobile wallet number from which fee was sent.' ?>
                        </div>
                    </div>
                </div>

                <!-- Sender Name & Payment Time -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md); margin-bottom: var(--space-md);">
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                            <?= $isBn ? 'বিকাশ একাউন্টধারীর নাম / কার একাউন্ট' : 'Sender Account Name / Owner' ?>
                        </label>
                        <input type="text" name="sender_name" placeholder="<?= $isBn ? 'উদাঃ অমিত সেন (নিজের) অথবা এজেন্টের নাম' : 'e.g. Amit Sen (Personal) or Agent' ?>" class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-medium); border-radius: var(--radius-md);">
                        <div style="font-size: 0.74rem; color: var(--text-muted); margin-top: 3px;">
                            <?= $isBn ? 'বন্ধুবান্ধব বা এজেন্টের নম্বর থেকে পাঠালে তার নাম উল্লেখ করুন।' : 'Helps Finance Officer match the transaction quickly.' ?>
                        </div>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                            <?= $isBn ? 'পেমেন্ট সম্পন্ন করার তারিখ ও সময়' : 'Payment Date & Approximate Time' ?>
                        </label>
                        <input type="text" name="payment_time" value="<?= date('Y-m-d H:i') ?>" placeholder="YYYY-MM-DD HH:MM" class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-medium); border-radius: var(--radius-md); font-family: monospace;">
                        <div style="font-size: 0.74rem; color: var(--text-muted); margin-top: 3px;">
                            <?= $isBn ? 'আজকের তারিখ বা টাকা পাঠানোর সঠিক সময়।' : 'Approximate timestamp of transaction execution.' ?>
                        </div>
                    </div>
                </div>

                <!-- TrxID & Reference Note -->
                <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: var(--space-md); margin-bottom: var(--space-md);">
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                            <?= $isBn ? 'ট্রানজেকশন আইডি (TrxID) *' : 'Transaction ID (TrxID) *' ?>
                            <span style="font-size: 0.75rem; color: #dc2626; font-weight: 800;"><?= $isBn ? '(বাধ্যতামূলক)' : '(Required)' ?></span>
                        </label>
                        <input type="text" name="trx_id" id="trx_id_input" required placeholder="উদাঃ BKA8X92JQK বা 7G8K9L1M" class="form-input" style="width: 100%; padding: 10px 14px; border: 2px solid #fdba74; border-radius: var(--radius-md); font-family: monospace; font-weight: 900; text-transform: uppercase; font-size: 1rem; color: #9a3412; background: #fffaf5;">
                        <div style="font-size: 0.74rem; color: var(--text-muted); margin-top: 3px;">
                            <?= $isBn ? 'বিকাশ কনফার্মেশন এসএমএস বা অ্যাপের ইনবক্সে পাওয়া ট্রানজেকশন আইডি।' : 'Unique TrxID received in SMS or App confirmation.' ?>
                        </div>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--primary-deep); margin-bottom: 4px;">
                            <?= $isBn ? 'রেফারেন্স নোট (Reference)' : 'Payment Reference' ?>
                        </label>
                        <input type="text" name="payment_reference" placeholder="<?= $isBn ? 'উদাঃ সদস্যপদ ফি / নিজের নাম' : 'e.g. Member Fee / Name' ?>" class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-medium); border-radius: var(--radius-md);">
                        <div style="font-size: 0.74rem; color: var(--text-muted); margin-top: 3px;">
                            <?= $isBn ? 'বিকাশে রেফারেন্সে যা লিখেছেন।' : 'Optional payment reference note.' ?>
                        </div>
                    </div>
                </div>

                <!-- Sent SS Screenshot Upload Section (Prominent & Mandatory for bKash) -->
                <div style="background: #ffffff; border: 2px solid #f472b6; border-radius: var(--radius-lg); padding: 16px; margin-bottom: var(--space-md); box-shadow: 0 2px 8px rgba(244, 114, 182, 0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                        <label style="font-size: 0.92rem; font-weight: 800; color: #9d174d; margin: 0; display: flex; align-items: center; gap: 6px;">
                            <span>📸</span>
                            <span><?= $isBn ? 'বিকাশ সেন্ট মানি স্ক্রিনশট (Sent Money SS) *' : 'bKash Sent Money Screenshot (Sent SS) *' ?></span>
                        </label>
                        <span id="ss_required_badge" style="font-size: 0.72rem; background: #be185d; color: #fff; padding: 2px 10px; border-radius: var(--radius-full); font-weight: 800; text-transform: uppercase;">
                            <?= $isBn ? 'বিকাশে সেন্ট এসএস আবশ্যক' : 'Sent SS Mandatory' ?>
                        </span>
                    </div>

                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 0 0 10px; line-height: 1.4;">
                        <?= $isBn 
                            ? 'টাকা পাঠানোর পর বিকাশ অ্যাপের সফল ট্রানজেকশন স্ক্রিনের একটি স্পষ্ট স্ক্রিনশট ফাইল নির্বাচন করুন। ফাইন্যান্স অফিসার এটি দেখে আপনার সদস্যপদ ভেরিফাই করবেন।' 
                            : 'Upload a clear screenshot of the successful Send Money transaction screen. Required for Finance Officer audit.' ?>
                    </p>

                    <div style="display: grid; grid-template-columns: 1fr auto; gap: 14px; align-items: center;">
                        <div>
                            <input type="file" name="payment_screenshot" accept="image/*" id="paymentScreenshotInput" onchange="previewScreenshot(event)" class="form-input" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-medium); border-radius: var(--radius-md); font-size: 0.85rem; background: #fafafa;">
                            <input type="hidden" name="payment_screenshot_url" id="paymentScreenshotUrl" value="assets/images/payments/bkash-success-sample.svg">
                            <div style="font-size: 0.74rem; color: var(--text-muted); margin-top: 4px;">
                                <?= $isBn ? 'সমর্থিত ফরম্যাট: PNG, JPG, JPEG, WebP (সর্বোচ্চ ৫ মেগাবাইট)' : 'Supported: PNG, JPG, WebP (Max 5MB)' ?>
                            </div>
                        </div>

                        <!-- Live Screenshot Preview Thumbnail -->
                        <div style="display: flex; align-items: center; gap: 10px; background: #fdf2f8; border: 1px solid #fbcfe8; border-radius: 8px; padding: 6px 12px;">
                            <img id="screenshotPreviewImg" src="<?= asset('assets/images/payments/bkash-success-sample.svg') ?>" alt="Preview" style="height: 52px; width: 52px; border-radius: 6px; border: 1px solid var(--border-medium); object-fit: cover; background: #fff;">
                            <div>
                                <div style="font-size: 0.76rem; font-weight: 800; color: #9d174d;" id="screenshotPreviewLabel">
                                    <?= $isBn ? 'নমুনা স্ক্রিনশট প্রস্তুত' : 'Sample SS Ready' ?>
                                </div>
                                <div style="display: flex; gap: 4px; margin-top: 4px;">
                                    <button type="button" onclick="setPresetScreenshot('assets/images/payments/bkash-success-sample.svg', 'bKash')" class="btn btn-xs btn-ghost" style="border: 1px solid #fbcfe8; color: #be185d; font-size: 0.68rem; padding: 1px 6px;">
                                        🌸 bKash SS
                                    </button>
                                    <button type="button" onclick="setPresetScreenshot('assets/images/payments/nagad-success-sample.svg', 'Nagad')" class="btn btn-xs btn-ghost" style="border: 1px solid #fed7aa; color: #c2410c; font-size: 0.68rem; padding: 1px 6px;">
                                        🔥 Nagad SS
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Finance Officer Verification Protocol Advisory -->
                <div style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border: 1.5px solid #86efac; border-radius: var(--radius-md); padding: 14px 18px; font-size: 0.84rem; color: #166534; display: flex; align-items: flex-start; gap: 12px; box-shadow: 0 2px 6px rgba(22, 101, 52, 0.05);">
                    <span style="font-size: 1.4rem; line-height: 1;">🪙</span>
                    <div>
                        <div style="font-weight: 800; color: #14532d; font-size: 0.9rem; margin-bottom: 2px;">
                            <?= $isBn ? 'ফাইন্যান্স অফিসার (কোষাধ্যক্ষ) ভেরিফিকেশন প্রোটোকল:' : 'Finance Officer (Treasurer) Verification Protocol:' ?>
                        </div>
                        <div style="line-height: 1.45;">
                            <?= $isBn 
                                ? 'আপনার প্রেরিত TrxID এবং সেন্ট মানি স্ক্রিনশট (Sent SS) এসপিএস-এর ফাইন্যান্স অফিসার (কোষাধ্যক্ষ - জয় চক্রবর্তী) স্বয়ং ব্যাংক/বিকাশ স্টেটমেন্টের সাথে যাচাই করে মেম্বারশিপ সক্রিয় করবেন। সুপার-অ্যাডমিন (অনিক কুমার সাহা) ও অন্য ২ জন অ্যাডমিন (রবিন দে ও লিখন ঘোষ) সার্বক্ষণিক এটি পর্যবেক্ষণ ও অডিট করছেন।' 
                                : 'Your submitted TrxID and Sent SS are audited exclusively by the Finance Officer (Joy Chakraborty) to activate your membership, with live supervisory monitoring by Super Admin & Admins.' ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div style="text-align: center;">
                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; max-width: 420px; font-weight: 800; font-size: 1.05rem; padding: 14px 28px; box-shadow: 0 4px 14px rgba(180, 83, 9, 0.25);">
                    <span>📝</span>
                    <span><?= $isBn ? 'আবেদনপত্র জমা দিন' : 'Submit Membership Application' ?></span>
                </button>
                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 10px;">
                    <?= $isBn ? 'আবেদন জমার পর অ্যাডমিন প্যানেল থেকে পেমেন্ট যাচাই শেষে ডিজিটাল কার্ড সক্রিয় হবে।' : 'Membership will be activated upon administrative payment verification.' ?>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
let currentCategory = '<?= $selectedCategory ?>';
let currentPlanType = '<?= str_contains($selectedPlan, 'MONTHLY') ? 'monthly' : (str_contains($selectedPlan, 'YEARLY') ? 'yearly' : 'lifetime') ?>';

function updateUI() {
    // Update Category Pills
    const stuCard = document.getElementById('cat-card-STUDENT');
    const earnCard = document.getElementById('cat-card-EARNING');
    const stuSec = document.getElementById('student-fields-sec');
    const earnSec = document.getElementById('earning-fields-sec');

    if (currentCategory === 'STUDENT') {
        stuCard.style.borderColor = '#0284c7';
        stuCard.style.background = '#f0f9ff';
        earnCard.style.borderColor = 'var(--border-medium)';
        earnCard.style.background = '#ffffff';
        stuSec.style.display = 'block';
        earnSec.style.display = 'none';
        document.getElementById('monthly-fee-label').textContent = '৳৫০/মাস';
    } else {
        earnCard.style.borderColor = '#c2410c';
        earnCard.style.background = '#fff7ed';
        stuCard.style.borderColor = 'var(--border-medium)';
        stuCard.style.background = '#ffffff';
        stuSec.style.display = 'none';
        earnSec.style.display = 'block';
        document.getElementById('monthly-fee-label').textContent = '৳১০০/মাস';
    }

    // Update Plan Radio Values & Borders
    const monthlyRadio = document.getElementById('plan-monthly-radio');
    monthlyRadio.value = currentCategory === 'STUDENT' ? 'STUDENT_MONTHLY' : 'EARNING_MONTHLY';

    const cardMonthly = document.getElementById('plan-card-monthly');
    const cardYearly = document.getElementById('plan-card-yearly');
    const cardLifetime = document.getElementById('plan-card-lifetime');

    cardMonthly.style.borderColor = currentPlanType === 'monthly' ? '#b45309' : 'var(--border-medium)';
    cardMonthly.style.background = currentPlanType === 'monthly' ? '#fffbeb' : '#ffffff';

    cardYearly.style.borderColor = currentPlanType === 'yearly' ? '#b45309' : 'var(--border-medium)';
    cardYearly.style.background = currentPlanType === 'yearly' ? '#fffbeb' : '#ffffff';

    cardLifetime.style.borderColor = currentPlanType === 'lifetime' ? '#b45309' : 'var(--border-medium)';
    cardLifetime.style.background = currentPlanType === 'lifetime' ? '#fffbeb' : '#ffffff';

    // Calculate Fees
    let entryFee = 0;
    let planFee = 0;
    let breakdown = '';

    if (currentPlanType === 'monthly') {
        if (currentCategory === 'STUDENT') {
            entryFee = 50;
            planFee = 50;
            breakdown = 'এককালীন এন্ট্রি ফি ৳৫০ + প্রথম মাসের সদস্যপদ ফি ৳৫০';
        } else {
            entryFee = 100;
            planFee = 100;
            breakdown = 'এককালীন এন্ট্রি ফি ৳১০০ + প্রথম মাসের সদস্যপদ ফি ৳১০০';
        }
    } else if (currentPlanType === 'yearly') {
        entryFee = 0;
        planFee = 1000;
        breakdown = 'বাৎসরিক প্ল্যান (১২ মাস) — এন্ট্রি ফি মওকুফ (অন্তর্ভুক্ত)';
    } else if (currentPlanType === 'lifetime') {
        entryFee = 0;
        planFee = 10000;
        breakdown = 'আজীবন সদস্যপদ — আজীবনের জন্য এককালীন পরিশোধ';
    }

    const total = entryFee + planFee;
    document.getElementById('fee-breakdown-text').textContent = breakdown;
    document.getElementById('total-payable-amount').textContent = '৳' + total.toLocaleString('bn-BD');
}

function onCategoryChange(cat) {
    currentCategory = cat;
    updateUI();
}

function onPlanChange(planType) {
    currentPlanType = planType;
    updateUI();
}

function previewScreenshot(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('screenshotPreviewImg').src = e.target.result;
            document.getElementById('screenshotPreviewLabel').textContent = 'নির্বাচিত ফাইল: ' + file.name;
        };
        reader.readAsDataURL(file);
    }
}

function copyBkashNumber(btn) {
    const num = '01700-000000';
    if (navigator.clipboard) {
        navigator.clipboard.writeText(num).then(function() {
            const orig = btn.innerHTML;
            btn.innerHTML = '✓ কপিড!';
            setTimeout(function() { btn.innerHTML = orig; }, 1500);
        });
    } else {
        alert('bKash: ' + num);
    }
}

function setPresetScreenshot(url, method) {
    document.getElementById('screenshotPreviewImg').src = '/' + url.replace(/^\//, '');
    document.getElementById('paymentScreenshotUrl').value = url;
    document.getElementById('screenshotPreviewLabel').textContent = method + ' নমুনা স্ক্রিনশট সংযুক্ত';
    const select = document.getElementById('payment_method_select');
    if (select && select.value !== method) select.value = method;
}

function onPaymentMethodChange(val) {
    const badge = document.getElementById('ss_required_badge');
    if (val === 'bKash') {
        setPresetScreenshot('assets/images/payments/bkash-success-sample.svg', 'bKash');
        if (badge) {
            badge.textContent = 'বিকাশে সেন্ট এসএস আবশ্যক';
            badge.style.background = '#be185d';
        }
    } else if (val === 'Nagad') {
        setPresetScreenshot('assets/images/payments/nagad-success-sample.svg', 'Nagad');
        if (badge) {
            badge.textContent = 'নগদ স্ক্রিনশট আবশ্যক';
            badge.style.background = '#c2410c';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateUI();
    const appForm = document.querySelector('form');
    if (appForm) {
        appForm.addEventListener('submit', function(e) {
            const method = document.getElementById('payment_method_select').value;
            const trx = document.getElementById('trx_id_input').value.trim();
            const fileInput = document.getElementById('paymentScreenshotInput');
            const presetUrl = document.getElementById('paymentScreenshotUrl').value.trim();

            if (!trx) {
                alert('অনুগ্রহ করে ট্রানজেকশন আইডি (TrxID) প্রদান করুন।');
                e.preventDefault();
                document.getElementById('trx_id_input').focus();
                return false;
            }

            if (method.toLowerCase().includes('bkash')) {
                const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
                if (!hasFile && !presetUrl) {
                    alert('বিকাশ পেমেন্টের ক্ষেত্রে সফল লেনদেনের সেন্ট মানি স্ক্রিনশট (Sent SS) আপলোড করা বাধ্যতামূলক।');
                    e.preventDefault();
                    if (fileInput) fileInput.focus();
                    return false;
                }
            }
        });
    }
});
</script>
