<?php
/**
 * Paid Member Blog Post Composition & Submission
 */
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
?>

<div class="container blog-page-container" style="max-width: var(--container-max); padding-top: var(--space-xl); padding-bottom: var(--space-4xl);">
    <!-- Header Hero Card (Consistent with Activities and About pages) -->
    <div class="blog-masthead-card" style="background: linear-gradient(135deg, #1b263b 0%, #0d1b2a 100%); color: #ffffff; border-radius: var(--radius-xl); padding: var(--space-2xl) var(--space-xl); margin-bottom: var(--space-2xl); position: relative; overflow: hidden; box-shadow: var(--shadow-lg); border-top: 4px solid var(--accent-gold); text-align: center;">
        <div style="position: absolute; right: -30px; bottom: -30px; font-size: 14rem; opacity: 0.03; user-select: none; pointer-events: none;">
            ✍️
        </div>
        <div style="position: relative; z-index: 1;">
            <div class="blog-brand-mark" style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 12px;">
                <span class="blog-om-symbol" style="color: #e5a93c; font-size: 1.5rem; font-weight: 800;">✍️</span>
                <span class="blog-tagline-pill" style="background: rgba(229, 169, 60, 0.15); border: 1px solid rgba(229, 169, 60, 0.4); color: #f7d28b; padding: 4px 14px; border-radius: 20px; font-size: 0.82rem; font-weight: 600;">
                    <?= $isBn ? 'এসপিএস সাহিত্য ও গবেষণাধর্মী ব্লগ ডেস্ক' : 'SPS Scholarly Authorship Desk' ?>
                </span>
            </div>
            <h1 class="blog-main-title" style="font-size: 2.15rem; font-weight: 800; color: #ffffff; margin: 0 0 10px; line-height: 1.25;">
                <?= $isBn ? 'পেইড সদস্য ব্লগ রচনা ও পাণ্ডুলিপি সাবমিট' : 'Author a Blog Post (Paid Member Portal)' ?>
            </h1>
            <p class="blog-main-subtitle" style="font-size: 1.05rem; line-height: 1.6; color: #cbd5e1; max-width: 820px; margin: 0 auto;">
                <?= $isBn 
                    ? 'আপনার লেখা গবেষণাধর্মী নিবন্ধ, শাস্ত্রীয় ভাবনা ও ঐতিহ্যের পাণ্ডুলিপি জমা দিন। সাহিত্য বিষয়ক সম্পাদক, অ্যাডমিন বা সুপার-অ্যাডমিন পর্যালোচনার পর এটি প্রকাশ করবেন।' 
                    : 'Submit your philosophical treatise, scriptural commentary, or heritage essay. Manuscripts undergo editorial review before live publication.' ?>
            </p>
        </div>
    </div>

    <div style="max-width: 960px; margin: 0 auto;">
        
        <!-- Membership Status & Testing Mode Simulator -->
        <div class="membership-tier-card">
            <div class="tier-card-header">
                <div>
                    <h3 class="tier-title">
                        <span>★</span>
                        <span><?= $isBn ? 'লেখক সদস্যপদ যাচাইকরণ ও মোড' : 'Author Membership Verification' ?></span>
                    </h3>
                    <p class="tier-desc">
                        <?= $isBn 
                            ? 'নিয়মানুযায়ী শুধুমাত্র এসপিএস পেইড সদস্যবৃন্দ ব্লগে লিখতে পারেন। পরীক্ষার সুবিধার জন্য নিচে আপনার সদস্যপদ মোড পরিবর্তন করতে পারেন:' 
                            : 'Policy: Only verified SPS Paid Members may publish blog posts. Toggle below to test the access control:' ?>
                    </p>
                </div>
                <div class="tier-badge-pill" id="tierBadge">
                    ★ <?= $isBn ? 'পেইড সদস্য মোড সক্রিয়' : 'Paid Member Mode Active' ?>
                </div>
            </div>

            <div class="tier-toggle-buttons">
                <button type="button" class="btn-tier-toggle active" id="btnModePaid" onclick="setMemberMode('paid_member')">
                    <span>👑</span>
                    <span><?= $isBn ? 'পেইড সদস্য মোড (অনুমোদিত)' : 'Paid Member Mode (Authorized)' ?></span>
                </button>
                <button type="button" class="btn-tier-toggle" id="btnModeFree" onclick="setMemberMode('free_visitor')">
                    <span>👤</span>
                    <span><?= $isBn ? 'সাধারণ ভিজিটর মোড (সীমাবদ্ধ)' : 'Free Visitor Mode (Restricted)' ?></span>
                </button>
            </div>

            <div id="unpaidWarningBox" class="unpaid-warning-box" style="display:none;">
                <span class="warn-icon">⚠️</span>
                <div>
                    <strong><?= $isBn ? 'সদস্যপদ সীমাবদ্ধতা সতর্কতা:' : 'Membership Restriction:' ?></strong>
                    <span><?= $isBn ? 'সাধারণ দর্শনার্থী বা ফ্রি মেম্বাররা ব্লগ পোস্ট জমা দিতে পারেন না। লিখতে চাইলে অনুগ্রহ করে ‘পেইড সদস্য মোড’ নির্বাচন করুন।' : 'Unpaid visitors cannot submit blog posts. Please switch back to Paid Member Mode to submit.' ?></span>
                </div>
            </div>
        </div>

        <!-- Composition Form -->
        <div class="blog-form-card">
            <form action="<?= url('/blog/write', $currentLocale) ?>" method="POST" id="blogWriteForm">
                <?= \App\Core\Session::getCsrfToken() ? '<input type="hidden" name="_csrf" value="'.\App\Core\Session::getCsrfToken().'">' : '' ?>
                <input type="hidden" name="membership_tier" id="membershipTierInput" value="paid_member">
                <input type="hidden" name="simulate_mode" id="simulateModeInput" value="paid_member">

                <!-- Author Identity Bar -->
                <div class="form-section-header">
                    <h4><?= $isBn ? '১. লেখকের পরিচিতি' : '1. Author Information' ?></h4>
                </div>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label"><?= $isBn ? 'লেখকের নাম (বাংলা)*' : 'Author Name (Bangla)*' ?></label>
                        <input type="text" name="author_name_bn" required 
                               value="<?= e($currentUser['name_bn'] ?? 'শ্রী সুমিত কুমার রায়') ?>" 
                               class="form-control" id="authorNameBn">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $isBn ? 'লেখকের নাম (ইংরেজি)*' : 'Author Name (English)*' ?></label>
                        <input type="text" name="author_name_en" required 
                               value="<?= e($currentUser['name_en'] ?? 'Sumit Kumar Roy') ?>" 
                               class="form-control" id="authorNameEn">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label"><?= $isBn ? 'যোগাযোগ ইমেইল (সদস্য একাউন্ট)' : 'Contact Email (Member ID)' ?></label>
                    <input type="email" name="author_email" 
                           value="<?= e($currentUser['email'] ?? 'member@sps.org') ?>" 
                           class="form-control" id="authorEmail">
                </div>

                <!-- Post Titles & Category -->
                <div class="form-section-header" style="margin-top: 24px;">
                    <h4><?= $isBn ? '২. ব্লগের শিরোনাম ও বিষয়শ্রেণী' : '2. Article Title & Category' ?></h4>
                </div>
                <div class="form-group">
                    <label class="form-label"><?= $isBn ? 'ব্লগের শিরোনাম (বাংলায়)*' : 'Blog Title (Bangla)*' ?></label>
                    <input type="text" name="title_bn" required 
                           placeholder="<?= $isBn ? 'যেমন: শ্রীমদ্ভগবদ্গীতায় ভক্তিযোগ ও আধুনিক সমাজভাবনা' : 'e.g., Bhakti Yoga in Daily Life' ?>" 
                           class="form-control title-input" id="titleBn">
                </div>
                <div class="form-group">
                    <label class="form-label"><?= $isBn ? 'ব্লগের শিরোনাম (ইংরেজিতে)' : 'Blog Title (English - Optional)' ?></label>
                    <input type="text" name="title_en" 
                           placeholder="e.g., Philosophical Insights of Upanishadic Meditation" 
                           class="form-control" id="titleEn">
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label"><?= $isBn ? 'বিষয়শ্রেণী (Category)*' : 'Category*' ?></label>
                        <select name="category" class="form-control" id="categorySelect" required>
                            <option value="vedanta">🕉️ <?= $isBn ? 'বেদান্ত ও দর্শন' : 'Vedanta & Philosophy' ?></option>
                            <option value="gita">📖 <?= $isBn ? 'ভগবদগীতা ও আত্মউন্নয়ন' : 'Bhagavad Gita & Growth' ?></option>
                            <option value="history">🏛️ <?= $isBn ? 'সনাতন ইতিহাস ও ঐতিহ্য' : 'Sanatan History & Heritage' ?></option>
                            <option value="seva">🤝 <?= $isBn ? 'সেবা ও মানবকল্যাণ' : 'Seva & Welfare' ?></option>
                            <option value="scripture">🪔 <?= $isBn ? 'শাস্ত্র ও আধ্যাত্মিকতা' : 'Scriptures & Spirituality' ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $isBn ? 'লেবেল / ট্যাগস (কমা দিয়ে পৃথক)' : 'Tags / Labels (Comma separated)' ?></label>
                        <input type="text" name="tags" placeholder="যেমন: গীতা, কর্মযোগ, শান্তি, দর্শন" class="form-control" id="tagsInput">
                    </div>
                </div>

                <!-- Featured Image Attachment -->
                <div class="form-section-header" style="margin-top: 24px;">
                    <h4><?= $isBn ? '৩. প্রচ্ছদ ছবি সংযুক্তি (Featured Image)' : '3. Featured Image Attachment' ?></h4>
                </div>
                <div class="form-group">
                    <label class="form-label"><?= $isBn ? 'ছবির লিংক / পাথ (Image URL or Path)*' : 'Image URL or Asset Path*' ?></label>
                    <div class="image-input-wrap">
                        <input type="text" name="featured_image" id="featuredImageInput" 
                               value="assets/images/library/covers/sps-samachar-feb.jpg" 
                               class="form-control" oninput="updateImagePreview()">
                    </div>
                    
                    <!-- Preset sample images -->
                    <div class="image-preset-bar">
                        <span class="preset-label"><?= $isBn ? 'দ্রুত নির্বাচন করুন:' : 'Sample presets:' ?></span>
                        <button type="button" class="btn-preset" onclick="setPresetImage('assets/images/library/covers/sps-samachar-feb.jpg')">
                            📰 সমাচার প্রচ্ছদ
                        </button>
                        <button type="button" class="btn-preset" onclick="setPresetImage('assets/images/library/covers/sps-ramnavami.jpg')">
                            🚩 রাম নবমী ও সংস্কৃতি
                        </button>
                        <button type="button" class="btn-preset" onclick="setPresetImage('assets/images/library/covers/sps-jagannath-rathyatra.jpg')">
                            🛕 রথযাত্রা ও স্থাপত্য
                        </button>
                    </div>

                    <!-- Live Image Preview -->
                    <div class="image-preview-container" id="imagePreviewContainer">
                        <div class="preview-label"><?= $isBn ? 'ছবির প্রিভিউ:' : 'Image Preview:' ?></div>
                        <img id="imagePreview" src="<?= asset('assets/images/library/covers/sps-samachar-feb.jpg') ?>" 
                             alt="Preview" class="attached-image-preview">
                    </div>
                </div>

                <!-- Excerpt & Full Content -->
                <div class="form-section-header" style="margin-top: 24px;">
                    <h4><?= $isBn ? '৪. সারসংক্ষেপ ও বিস্তারিত পাণ্ডুলিপি' : '4. Excerpt & Full Manuscript' ?></h4>
                </div>
                <div class="form-group">
                    <label class="form-label"><?= $isBn ? 'সংক্ষিপ্ত সারসংক্ষেপ (Excerpt)*' : 'Short Excerpt*' ?></label>
                    <textarea name="excerpt_bn" rows="2" required 
                              placeholder="<?= $isBn ? 'নিবন্ধের মূল বক্তব্যের ২-৩ লাইনের সংক্ষিপ্ত রূপ যা ব্লগের হোমপেজে প্রদর্শিত হবে...' : 'Brief 2-3 line summary of your article...' ?>" 
                              class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; flex-wrap: wrap; gap: 8px;">
                        <label class="form-label" style="margin-bottom:0; font-weight: 700;"><?= $isBn ? 'সম্পূর্ণ নিবন্ধের বিস্তারিত বিবরণ (Content)*' : 'Full Manuscript Body (Content)*' ?></label>
                        <div id="wordCounterBadge" class="word-counter-badge" style="font-size: 0.85rem; font-weight: 700; background: #f8fafc; padding: 4px 12px; border-radius: 16px; border: 1px solid #cbd5e1; color: #1e293b; display: inline-flex; align-items: center; gap: 6px;">
                            <span style="color:#64748b;"><?= $isBn ? 'শব্দসংখ্যা:' : 'Words:' ?></span>
                            <span id="wordCounter" style="font-family: monospace; font-size: 0.95rem; font-weight: 800;">0 / 10000</span>
                        </div>
                    </div>
                    <div class="editor-tips-box">
                        💡 <strong><?= $isBn ? 'লেখার নির্দেশিকা ও শব্দসীমা:' : 'Writing Guidelines & Word Limit:' ?></strong> 
                        <?= $isBn 
                            ? 'প্রামাণিক তথ্য, শাস্ত্রীয় শ্লোকের বঙ্গানুবাদ ও যৌক্তিক বিশ্লেষণ উপস্থাপন করুন। ব্লগের সর্বোচ্চ শব্দসীমা ১০,০০০ শব্দ (Max limit: 10,000 words)।' 
                            : 'Ensure authentic sourcing, scriptural references, and objective scholarly analysis. Maximum limit is 10,000 words.' ?>
                    </div>
                    <textarea name="content_bn" rows="12" required 
                              placeholder="<?= $isBn ? 'এখানে আপনার বিস্তারিত প্রবন্ধ বা গবেষণা লিখুন...' : 'Write your full essay or philosophical commentary here...' ?>" 
                              class="form-control content-textarea" id="contentBn"></textarea>
                    <div id="wordLimitAlert" style="display:none; color: #b91c1c; font-size: 0.88rem; font-weight: 700; margin-top: 8px; padding: 8px 14px; background: #fee2e2; border: 1px solid #f87171; border-radius: 6px;">
                        ⚠️ <?= $isBn ? 'সতর্কতা: আপনার পাণ্ডুলিপির শব্দসংখ্যা ১০,০০০ শব্দের সীমা অতিক্রম করেছে! প্রকাশনার জন্য সর্বোচ্চ ১০,০০০ শব্দের মধ্যে রাখা আবশ্যক।' : 'Warning: Manuscript exceeds 10,000 words limit! Please reduce length before submitting.' ?>
                    </div>
                </div>

                <!-- Workflow Notice -->
                <div class="moderation-notice-card">
                    <div class="notice-icon">🛡️</div>
                    <div class="notice-text">
                        <strong><?= $isBn ? 'সম্পাদকীয় মডারেশন ও অনুমোদন ব্যবস্থা:' : 'Editorial Moderation Workflow:' ?></strong>
                        <p><?= $isBn 
                            ? 'আপনার পাণ্ডুলিপি জমা দেওয়ার সাথে সাথে এটি “Pending Review” অবস্থায় থাকবে। এসপিএস-এর সুপার-অ্যাডমিন, অ্যাডমিন অথবা সাহিত্য বিষয়ক সম্পাদক (Literature-Admin) এটি পাঠ করে অনুমোদন করার পর তা জনসম্মুখে প্রকাশিত হবে।' 
                            : 'Submitted blogs are stored with "Pending Review" status. Super Admin, Admin, or Literature-Admin will review and approve or reject with feedback.' ?></p>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="form-action-footer">
                    <a href="<?= url('/blog', $currentLocale) ?>" class="btn btn-secondary">
                        <?= $isBn ? 'ফিরে যান' : 'Cancel' ?>
                    </a>
                    <button type="submit" class="btn btn-gold btn-submit-lg" id="btnSubmitPost">
                        <span>📤</span>
                        <span><?= $isBn ? 'পাণ্ডুলিপি পর্যালোচনার জন্য জমা দিন' : 'Submit Manuscript for Review' ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function setPresetImage(path) {
    const input = document.getElementById('featuredImageInput');
    if (input) {
        input.value = path;
        updateImagePreview();
    }
}

function updateImagePreview() {
    const input = document.getElementById('featuredImageInput');
    const preview = document.getElementById('imagePreview');
    if (input && preview) {
        let val = input.value.trim();
        if (val.startsWith('http://') || val.startsWith('https://')) {
            preview.src = val;
        } else {
            preview.src = '<?= asset('') ?>' + val.replace(/^\//, '');
        }
    }
}

function setMemberMode(mode) {
    const btnPaid = document.getElementById('btnModePaid');
    const btnFree = document.getElementById('btnModeFree');
    const tierBadge = document.getElementById('tierBadge');
    const warnBox = document.getElementById('unpaidWarningBox');
    const tierInput = document.getElementById('membershipTierInput');
    const simulateInput = document.getElementById('simulateModeInput');
    const submitBtn = document.getElementById('btnSubmitPost');

    if (mode === 'paid_member') {
        btnPaid.classList.add('active');
        btnFree.classList.remove('active');
        tierBadge.textContent = '★ <?= $isBn ? "পেইড সদস্য মোড সক্রিয়" : "Paid Member Mode Active" ?>';
        tierBadge.style.background = '#fef3c7';
        tierBadge.style.color = '#92400e';
        warnBox.style.display = 'none';
        tierInput.value = 'paid_member';
        simulateInput.value = 'paid_member';
        submitBtn.disabled = false;
        submitBtn.style.opacity = '1';
        submitBtn.style.cursor = 'pointer';
    } else {
        btnFree.classList.add('active');
        btnPaid.classList.remove('active');
        tierBadge.textContent = '👤 <?= $isBn ? "সাধারণ ভিজিটর মোড (লকড)" : "Free Visitor Mode (Locked)" ?>';
        tierBadge.style.background = '#fee2e2';
        tierBadge.style.color = '#991b1b';
        warnBox.style.display = 'flex';
        tierInput.value = 'free_visitor';
        simulateInput.value = 'free_visitor';
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.5';
        submitBtn.style.cursor = 'not-allowed';
    }
}

// Live 10,000 Words Counter
const contentTextarea = document.getElementById('contentBn');
const wordCounter = document.getElementById('wordCounter');
const wordCounterBadge = document.getElementById('wordCounterBadge');
const wordLimitAlert = document.getElementById('wordLimitAlert');
const btnSubmitPost = document.getElementById('btnSubmitPost');

function updateWordCounter() {
    if (!contentTextarea || !wordCounter) return;
    const text = contentTextarea.value || '';
    const words = text.trim().match(/[\p{L}\p{N}]+/gu);
    const count = words ? words.length : 0;
    const maxLimit = 10000;

    wordCounter.textContent = count + ' / ' + maxLimit;

    if (count > maxLimit) {
        wordCounterBadge.style.background = '#fee2e2';
        wordCounterBadge.style.borderColor = '#ef4444';
        wordCounterBadge.style.color = '#b91c1c';
        wordLimitAlert.style.display = 'block';
        if (btnSubmitPost) {
            btnSubmitPost.disabled = true;
            btnSubmitPost.style.opacity = '0.5';
            btnSubmitPost.style.cursor = 'not-allowed';
        }
    } else {
        wordCounterBadge.style.background = '#f8fafc';
        wordCounterBadge.style.borderColor = '#cbd5e1';
        wordCounterBadge.style.color = '#1e293b';
        wordLimitAlert.style.display = 'none';
        const currentTier = document.getElementById('membershipTierInput')?.value;
        if (btnSubmitPost && currentTier === 'paid_member') {
            btnSubmitPost.disabled = false;
            btnSubmitPost.style.opacity = '1';
            btnSubmitPost.style.cursor = 'pointer';
        }
    }
}

if (contentTextarea) {
    contentTextarea.addEventListener('input', updateWordCounter);
    contentTextarea.addEventListener('paste', () => setTimeout(updateWordCounter, 60));
    updateWordCounter();
}
</script>

<style>
/* Composition Desk Styling */
.membership-tier-card {
    background: #ffffff;
    border: 1px solid #e7ded0;
    border-radius: 10px;
    padding: 22px 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 10px rgba(43, 29, 12, 0.04);
}
.tier-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}
.tier-title {
    font-size: 1.15rem;
    font-weight: 800;
    color: #2b1d0c;
    margin: 0 0 4px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.tier-desc {
    font-size: 0.85rem;
    color: #786b5c;
    margin: 0;
}
.tier-badge-pill {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
    font-size: 0.78rem;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 20px;
}
.tier-toggle-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.btn-tier-toggle {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f9f6f0;
    border: 1px solid #ded5c6;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 0.86rem;
    font-weight: 700;
    color: #554a3e;
    cursor: pointer;
    transition: all 0.15s ease;
}
.btn-tier-toggle.active {
    background: #b45309;
    border-color: #92400e;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(180, 83, 9, 0.3);
}
.unpaid-warning-box {
    margin-top: 14px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
    padding: 10px 14px;
    border-radius: 6px;
    font-size: 0.84rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.blog-form-card {
    background: #ffffff;
    border: 1px solid #e7ded0;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 2px 14px rgba(43, 29, 12, 0.05);
}
.form-section-header {
    border-bottom: 2px solid #f4eee2;
    padding-bottom: 8px;
    margin-bottom: 18px;
}
.form-section-header h4 {
    font-size: 1.05rem;
    font-weight: 800;
    color: #451a03;
    margin: 0;
}
.form-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
.form-group {
    margin-bottom: 18px;
}
.form-label {
    display: block;
    font-weight: 700;
    font-size: 0.88rem;
    color: #2b1d0c;
    margin-bottom: 6px;
}
.form-control {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #d4c8b7;
    border-radius: 6px;
    font-size: 0.92rem;
    background: #fff;
    outline: none;
    font-family: inherit;
}
.form-control:focus {
    border-color: #b45309;
    box-shadow: 0 0 0 3px rgba(180, 83, 9, 0.1);
}
.title-input {
    font-size: 1.05rem;
    font-weight: 700;
}
.image-preset-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 8px;
    flex-wrap: wrap;
}
.preset-label {
    font-size: 0.78rem;
    color: #786b5c;
    font-weight: 600;
}
.btn-preset {
    background: #f7f2ea;
    border: 1px solid #dfd6c7;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 0.78rem;
    color: #554a3e;
    cursor: pointer;
    font-weight: 600;
}
.btn-preset:hover {
    background: #eadecb;
    color: #2b1d0c;
}
.image-preview-container {
    margin-top: 14px;
    background: #fbf8f3;
    border: 1px dashed #d5c8b5;
    padding: 12px;
    border-radius: 8px;
    display: inline-block;
    max-width: 100%;
}
.preview-label {
    font-size: 0.76rem;
    font-weight: 700;
    color: #786b5c;
    margin-bottom: 6px;
}
.attached-image-preview {
    max-height: 180px;
    max-width: 100%;
    border-radius: 6px;
    object-fit: cover;
    border: 1px solid #e7ded0;
    display: block;
}
.editor-tips-box {
    background: #fffbeb;
    border: 1px solid #fef3c7;
    color: #92400e;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 0.8rem;
    margin-bottom: 8px;
}
.content-textarea {
    font-family: inherit;
    line-height: 1.6;
}

.moderation-notice-card {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    padding: 14px 18px;
    margin: 24px 0;
}
.notice-icon {
    font-size: 1.4rem;
}
.notice-text strong {
    display: block;
    color: #166534;
    font-size: 0.9rem;
    margin-bottom: 2px;
}
.notice-text p {
    margin: 0;
    font-size: 0.82rem;
    color: #15803d;
    line-height: 1.5;
}

.form-action-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 24px;
    padding-top: 18px;
    border-top: 1px solid #ede3d4;
}
.btn-submit-lg {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 24px;
    font-size: 0.96rem;
    font-weight: 800;
    border-radius: 6px;
    cursor: pointer;
}

@media (max-width: 768px) {
    .form-grid-2 {
        grid-template-columns: 1fr;
    }
}
</style>
