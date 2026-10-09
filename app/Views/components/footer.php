<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
?>
<footer class="site-footer" id="siteFooter">
    <div class="footer-inner">
        
        <!-- Pre-Footer: Institutional Trust, Mission & Transparency Strip -->
        <div class="footer-trust-strip">
            <div class="footer-trust-grid">
                <!-- Trust 1: Transparency -->
                <div class="footer-trust-card">
                    <div class="footer-trust-icon" aria-hidden="true">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <div class="footer-trust-title">
                            <?= $isBn ? '১০০% আর্থিক স্বচ্ছতা ও উন্মুক্ত অডিট' : '100% Financial Transparency & Open Audit' ?>
                        </div>
                        <p class="footer-trust-desc">
                            <?= $isBn 
                                ? 'প্রতিটি অনুদান ও চাঁদার মাসিক আয়-ব্যয়ের হিসাব সর্বজনীনভাবে প্রকাশিত ও সার্বক্ষণিক নিরীক্ষিত।' 
                                : 'All public donations, membership fees and welfare expenditures are audited and openly published.' ?>
                        </p>
                    </div>
                </div>

                <!-- Trust 2: Scripture Preservation -->
                <div class="footer-trust-card">
                    <div class="footer-trust-icon" aria-hidden="true">
                        <i class="fa-solid fa-book-open-reader"></i>
                    </div>
                    <div>
                        <div class="footer-trust-title">
                            <?= $isBn ? 'প্রামাণিক শাস্ত্রীয় জ্ঞান ও মহাফেজখানা' : 'Authentic Scriptures & Digital Archives' ?>
                        </div>
                        <p class="footer-trust-desc">
                            <?= $isBn 
                                ? 'শ্রীমদ্ভগবদ্গীতা, প্রধান উপনিষদ ও দুর্লভ প্রাচীন পাণ্ডুলিপির প্রামাণিক ডিজিটাল সংরক্ষণ ও উন্মুক্ত গবেষণা।' 
                                : 'Preserving Vedic texts, Gita commentaries and ancient manuscripts for universal digital study.' ?>
                        </p>
                    </div>
                </div>

                <!-- Trust 3: Selfless Seva & Welfare -->
                <div class="footer-trust-card">
                    <div class="footer-trust-icon" aria-hidden="true">
                        <i class="fa-solid fa-hand-holding-heart"></i>
                    </div>
                    <div>
                        <div class="footer-trust-title">
                            <?= $isBn ? 'নিঃস্বার্থ মানবসেবা ও জরুরি ত্রাণ' : 'Selfless Seva & Humanitarian Relief' ?>
                        </div>
                        <p class="footer-trust-desc">
                            <?= $isBn 
                                ? 'সনাতনী ১০ টাকার প্রজেক্ট ও ওয়েলফেয়ার ট্রাস্টের মাধ্যমে বন্যা, দুর্যোগ ও দরিদ্র রোগীর সরাসরি চিকিৎসা ফান্ড।' 
                                : 'Direct grassroots food distribution, flood relief, youth student stipends, and emergency healthcare.' ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Institutional Brand & Secretariat Strip -->
        <div class="footer-brand-contact-strip">
            <div class="footer-brand-contact-left">
                <div style="display:flex; align-items:center; gap:var(--space-sm); margin-bottom:var(--space-xs);">
                    <img src="<?= asset('assets/images/brand/sps-logo-white.png') ?>" alt="SPS Brand Crest" class="footer-brand-mark" width="48" height="34" loading="lazy">
                    <div>
                        <div class="footer-brand-title">SPS</div>
                        <div class="footer-brand-subtitle">SANATAN PHILOSOPHY & SCRIPTURE</div>
                    </div>
                </div>

                <div class="footer-motto-badge">
                    <span>🚩</span>
                    <span><?= $isBn ? 'সনাতনী ঐক্য, প্রচার ও কল্যাণে অবিচল' : 'Steadfast in Sanatan Unity, Dissemination & Welfare' ?></span>
                </div>

                <p class="footer-brand-desc" style="margin-top:4px; margin-bottom:12px;">
                    <?= $isBn 
                        ? 'সনাতন দর্শন, বেদান্ত, উপনিষদ ও ভারতীয় জ্ঞানপরম্পরার প্রামাণিক গবেষণা, সংরক্ষণ ও মানবতার নিঃস্বার্থ সেবায় নিবেদিত একটি উন্মুক্ত প্রাতিষ্ঠানিক প্ল্যাটফর্ম।' 
                        : 'An open institutional platform dedicated to authentic Vedic research, preservation of sacred scriptures, and selfless humanitarian service.' ?>
                </p>
            </div>

            <div class="footer-brand-contact-right">
                <div style="display:flex; flex-direction:column; gap:6px; font-size:0.85rem; color:var(--text-on-dark-muted);">
                    <div>
                        <strong style="color:#ffffff;"><i class="fa-solid fa-location-dot" style="color:var(--accent-gold); margin-right:6px;"></i><?= $isBn ? 'সচিবালয় ও কার্যালয়:' : 'Headquarters:' ?></strong>
                        <span><?= e(__('common.footer.address_lines')) ?></span>
                    </div>
                    <div>
                        <strong style="color:#ffffff;"><i class="fa-solid fa-phone" style="color:var(--accent-gold); margin-right:6px;"></i><?= $isBn ? 'সহায়তা ডেস্ক:' : 'Helpline:' ?></strong>
                        <a href="tel:+8801736360041" style="color:var(--text-on-dark); text-decoration:none; font-family:monospace; font-weight:700;">+880 1736-360041</a>, 
                        <a href="tel:+8801782009415" style="color:var(--text-on-dark); text-decoration:none; font-family:monospace; font-weight:700;">+880 1782-009415</a>
                    </div>
                    <div>
                        <strong style="color:#ffffff;"><i class="fa-solid fa-envelope" style="color:var(--accent-gold); margin-right:6px;"></i><?= $isBn ? 'ইমেইল:' : 'Email:' ?></strong>
                        <a href="mailto:contact@sps-platform.org" style="color:#fbd38d; text-decoration:none; font-family:monospace;">contact@sps-platform.org</a>
                    </div>
                </div>

                <!-- Official SPS Social Channels -->
                <div class="footer-social-row" aria-label="Official Social Links" style="margin-top:6px;">
                    <a href="https://www.facebook.com/bewithsps?utm_source=chatgpt.com" target="_blank" rel="noopener noreferrer" class="footer-social-btn btn-facebook" title="SPS Official Facebook Page" aria-label="Facebook Page">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <a href="https://www.facebook.com/groups/278337526756568?utm_source=chatgpt.com" target="_blank" rel="noopener noreferrer" class="footer-social-btn btn-facebook" title="SPS Official Facebook Group" aria-label="Facebook Group" style="background:#2E89FF; border-color:#2E89FF;">
                        <i class="fa-solid fa-users"></i>
                    </a>
                    <a href="https://www.youtube.com/@spsofficial1529" target="_blank" rel="noopener noreferrer" class="footer-social-btn btn-youtube" title="SPS Official YouTube" aria-label="YouTube">
                        <i class="fa-brands fa-youtube"></i>
                    </a>
                    <a href="https://sanatanphilosophyandscripture.blogspot.com" target="_blank" rel="noopener noreferrer" class="footer-social-btn btn-blog" title="SPS Official Blogspot" aria-label="Blog">
                        <i class="fa-solid fa-feather-pointed"></i>
                    </a>
                    <a href="https://www.instagram.com/bewithsps/" target="_blank" rel="noopener noreferrer" class="footer-social-btn btn-instagram" title="SPS Official Instagram" aria-label="Instagram">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <a href="https://wa.me/8801736360041" target="_blank" rel="noopener noreferrer" class="footer-social-btn btn-whatsapp" title="SPS Official WhatsApp Helpdesk" aria-label="WhatsApp">
                        <i class="fa-brands fa-whatsapp"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main 4-Column Institutional Grid: জ্ঞান | SPS | যুক্ত হোন | স্বচ্ছতা -->
        <div class="footer-grid">
            
            <!-- Column 1: জ্ঞান (Knowledge) -->
            <div>
                <h4 class="footer-heading"><?= $isBn ? 'জ্ঞান' : 'Knowledge' ?></h4>
                <div class="footer-links">
                    <a href="<?= e(url('/knowledge', $currentLocale)) ?>#gita">
                        <span class="footer-link-icon"><i class="fa-solid fa-om"></i></span>
                        <span><?= $isBn ? 'ভগবদ্গীতা' : 'Bhagavad Gita' ?></span>
                    </a>
                    <a href="<?= e(url('/knowledge', $currentLocale)) ?>#upanishads">
                        <span class="footer-link-icon"><i class="fa-solid fa-scroll"></i></span>
                        <span><?= $isBn ? 'উপনিষদ' : 'Upanishads' ?></span>
                    </a>
                    <a href="<?= e(url('/knowledge', $currentLocale)) ?>#vedanta">
                        <span class="footer-link-icon"><i class="fa-solid fa-fire-flame-curved"></i></span>
                        <span><?= $isBn ? 'বেদান্ত' : 'Vedanta' ?></span>
                    </a>
                    <a href="<?= e(url('/library', $currentLocale)) ?>">
                        <span class="footer-link-icon"><i class="fa-solid fa-box-archive"></i></span>
                        <span><?= $isBn ? 'পাণ্ডুলিপি' : 'Manuscripts' ?></span>
                    </a>
                    <a href="<?= e(url('/knowledge', $currentLocale)) ?>">
                        <span class="footer-link-icon"><i class="fa-solid fa-chevron-right"></i></span>
                        <span><?= $isBn ? 'সম্পূর্ণ জ্ঞানভাণ্ডার' : 'Complete Knowledge Base' ?></span>
                    </a>
                </div>
            </div>

            <!-- Column 2: SPS -->
            <div>
                <h4 class="footer-heading">SPS</h4>
                <div class="footer-links">
                    <a href="<?= e(url('/about', $currentLocale)) ?>">
                        <span class="footer-link-icon"><i class="fa-solid fa-landmark"></i></span>
                        <span><?= $isBn ? 'পরিচিতি' : 'About SPS' ?></span>
                    </a>
                    <a href="<?= e(url('/activities', $currentLocale)) ?>">
                        <span class="footer-link-icon"><i class="fa-solid fa-hands-holding-circle"></i></span>
                        <span><?= $isBn ? 'কার্যক্রম' : 'Activities' ?></span>
                    </a>
                    <a href="<?= e(url('/knowledge', $currentLocale)) ?>#research">
                        <span class="footer-link-icon"><i class="fa-solid fa-microscope"></i></span>
                        <span><?= $isBn ? 'গবেষণা' : 'Research' ?></span>
                    </a>
                    <a href="<?= e(url('/library', $currentLocale)) ?>">
                        <span class="footer-link-icon"><i class="fa-solid fa-book-open"></i></span>
                        <span><?= $isBn ? 'প্রকাশনা' : 'Publications' ?></span>
                    </a>
                    <a href="<?= e(url('/blog', $currentLocale)) ?>">
                        <span class="footer-link-icon"><i class="fa-solid fa-pen-nib"></i></span>
                        <span><?= $isBn ? 'সদস্য ব্লগ ও চিন্তাধারা' : 'Member Thought Journal' ?></span>
                    </a>
                </div>
            </div>

            <!-- Column 3: যুক্ত হোন (Join Us) -->
            <div>
                <h4 class="footer-heading"><?= $isBn ? 'যুক্ত হোন' : 'Join Us' ?></h4>
                <div class="footer-links">
                    <a href="<?= e(url('/membership/apply', $currentLocale)) ?>">
                        <span class="footer-link-icon"><i class="fa-solid fa-id-card"></i></span>
                        <span><?= $isBn ? 'সদস্য হোন' : 'Become a Member' ?></span>
                    </a>
                    <a href="<?= e(url('/get-involved', $currentLocale)) ?>#volunteer">
                        <span class="footer-link-icon"><i class="fa-solid fa-hand-holding-heart"></i></span>
                        <span><?= $isBn ? 'স্বেচ্ছাসেবক' : 'Volunteer' ?></span>
                    </a>
                    <a href="<?= e(url('/transparency', $currentLocale)) ?>#donate">
                        <span class="footer-link-icon"><i class="fa-solid fa-heart"></i></span>
                        <span><?= $isBn ? 'সহযোগিতা' : 'Donate & Support' ?></span>
                    </a>
                    <a href="https://www.facebook.com/bewithsps?utm_source=chatgpt.com" target="_blank" rel="noopener noreferrer">
                        <span class="footer-link-icon" style="color:#1877F2;"><i class="fa-brands fa-facebook"></i></span>
                        <span>Facebook Page</span>
                    </a>
                    <a href="https://www.facebook.com/groups/278337526756568?utm_source=chatgpt.com" target="_blank" rel="noopener noreferrer">
                        <span class="footer-link-icon" style="color:#2E89FF;"><i class="fa-solid fa-users"></i></span>
                        <span>Facebook Group</span>
                    </a>
                </div>
            </div>

            <!-- Column 4: স্বচ্ছতা (Transparency & Governance) -->
            <div>
                <h4 class="footer-heading"><?= $isBn ? 'স্বচ্ছতা' : 'Transparency' ?></h4>
                <div class="footer-links">
                    <a href="<?= e(url('/transparency', $currentLocale)) ?>">
                        <span class="footer-link-icon"><i class="fa-solid fa-chart-pie"></i></span>
                        <span><?= $isBn ? 'আর্থিক প্রতিবেদন' : 'Financial Reports' ?></span>
                    </a>
                    <a href="<?= e(url('/transparency', $currentLocale)) ?>#audit">
                        <span class="footer-link-icon"><i class="fa-solid fa-scale-balanced"></i></span>
                        <span><?= $isBn ? 'উন্মুক্ত অডিট' : 'Open Audit' ?></span>
                    </a>
                    <a href="<?= e(url('/invoice', $currentLocale)) ?>">
                        <span class="footer-link-icon"><i class="fa-solid fa-file-invoice-dollar"></i></span>
                        <span><?= $isBn ? 'মানি রসিদ যাচাই' : 'Verify Invoice' ?></span>
                    </a>
                    <a href="<?= e(url('/transparency', $currentLocale)) ?>#privacy">
                        <span class="footer-link-icon"><i class="fa-solid fa-shield-halved"></i></span>
                        <span><?= $isBn ? 'গোপনীয়তা নীতি (Privacy)' : 'Privacy Policy' ?></span>
                    </a>
                    <a href="<?= e(url('/transparency', $currentLocale)) ?>#terms">
                        <span class="footer-link-icon"><i class="fa-solid fa-gavel"></i></span>
                        <span><?= $isBn ? 'শর্তাবলী (Terms)' : 'Terms of Service' ?></span>
                    </a>
                </div>
            </div>

        </div>

        <!-- Secondary Legal & Institutional Navigation Bar -->
        <div class="footer-bottom">
            <div style="font-size:0.82rem; color:var(--text-on-dark-muted);">
                <em><?= e(__('common.footer.editorial_note')) ?></em>
            </div>

            <div class="footer-policy-links">
                <a href="#!"><?= e(__('common.footer.privacy_policy')) ?></a>
                <span>•</span>
                <a href="#!"><?= e(__('common.footer.terms_of_service')) ?></a>
                <span>•</span>
                <a href="<?= e(url('/transparency', $currentLocale)) ?>"><?= e(__('common.footer.financial_ethics')) ?></a>
                <span>•</span>
                <a href="<?= e(url('/invoice', $currentLocale)) ?>" title="<?= $isBn ? 'অফিসিয়াল মানি রসিদ ও ইনভয়েস' : 'Official Money Receipt & Invoice' ?>">
                    <i class="fa-solid fa-receipt" style="color:var(--accent-gold);"></i> 
                    <?= $isBn ? 'রসিদ যাচাই' : 'Verify Invoice' ?>
                </a>
            </div>
        </div>

    </div>

    <!-- Master Bottom Developer & Copyright Bar -->
    <div class="footer-developer-bar">
        <div class="footer-developer-bar-inner">
            <div class="footer-copy-text">
                <i class="fa-regular fa-copyright" style="color:var(--accent-gold);"></i>
                <span><?= date('Y') ?></span>
                <span>Copyright Reserved by <strong>Sanatan Philosophy and Scripture (SPS)</strong> • <?= $isBn ? 'সর্বস্বত্ব সংরক্ষিত' : 'All Rights Reserved' ?></span>
            </div>

            <div class="footer-dev-text-group">
                <div class="footer-dev-pill">
                    <i class="fa-solid fa-code" style="color:var(--accent-gold);" aria-hidden="true"></i>
                    <span class="footer-dev-label">
                        <span><?= $isBn ? 'কারিগরি পরিচালনায় ও উন্নয়ন:' : 'Designed & Developed by:' ?></span>
                        <strong class="footer-dev-name">Badhan Amit Roy</strong>
                    </span>
                    <span class="dev-social-divider">|</span>
                    <div class="footer-social-icons" aria-label="Developer Social Profiles">
                        <a href="https://www.facebook.com/BadhanAmitRoy.25/" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="dev-social-link link-facebook" 
                           title="Badhan Amit Roy on Facebook"
                           aria-label="Facebook Profile">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="https://www.linkedin.com/in/badhan-roy-444a32250/" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="dev-social-link link-linkedin" 
                           title="Badhan Amit Roy on LinkedIn"
                           aria-label="LinkedIn Profile">
                            <i class="fa-brands fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
