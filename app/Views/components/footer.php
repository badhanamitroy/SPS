<?php
$currentLocale = current_locale();
?>
<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-grid">
            <!-- Brand & Purpose -->
            <div>
                <div style="display:flex; align-items:center; gap:var(--space-sm); margin-bottom:var(--space-xs);">
                    <img src="<?= asset('assets/images/brand/sps-logo-white.png') ?>" alt="<?= e(__('common.brand_name')) ?>" class="footer-brand-mark" width="48" height="34" loading="lazy">
                    <span style="font-size:1.1rem; font-weight:700; color:#FFFFFF;"><?= e(__('common.brand_name')) ?></span>
                </div>
                <p class="footer-brand-desc">
                    <?= e(__('common.footer.about_text')) ?>
                </p>
                <div style="margin-top:var(--space-md); font-size:0.84rem; color:var(--accent-gold-light);">
                    <em><?= e(__('common.footer.editorial_note')) ?></em>
                </div>
            </div>

            <!-- Knowledge & Library Column -->
            <div>
                <h4 class="footer-heading"><?= $currentLocale === 'bn' ? 'জ্ঞান ও শাস্ত্র' : 'Knowledge & Scriptures' ?></h4>
                <div class="footer-links">
                    <a href="<?= e(url('/knowledge', $currentLocale)) ?>"><?= e(__('common.nav.knowledge')) ?></a>
                    <a href="<?= e(url('/library', $currentLocale)) ?>"><?= e(__('common.nav.library')) ?></a>
                    <a href="<?= e(url('/knowledge', $currentLocale)) ?>"><?= $currentLocale === 'bn' ? 'শ্রীমদ্ভগবদ্গীতা' : 'Bhagavad Gita' ?></a>
                    <a href="<?= e(url('/knowledge', $currentLocale)) ?>"><?= $currentLocale === 'bn' ? 'প্রধান উপনিষদাবলী' : 'Principal Upanishads' ?></a>
                    <a href="<?= e(url('/library', $currentLocale)) ?>"><?= $currentLocale === 'bn' ? 'পাণ্ডুলিপি ডিজিটাল সংগ্রহ' : 'Digital Manuscript Archive' ?></a>
                </div>
            </div>

            <!-- Community & Seva Column -->
            <div>
                <h4 class="footer-heading"><?= $currentLocale === 'bn' ? 'সেবা ও সমাজ' : 'Seva & Programs' ?></h4>
                <div class="footer-links">
                    <a href="<?= e(url('/activities', $currentLocale)) ?>"><?= e(__('common.nav.activities')) ?></a>
                    <a href="<?= e(url('/blog', $currentLocale)) ?>"><?= e(__('common.nav.blog')) ?></a>
                    <a href="<?= e(url('/get-involved', $currentLocale)) ?>"><?= e(__('common.nav.get_involved')) ?></a>
                    <a href="<?= e(url('/transparency', $currentLocale)) ?>"><?= e(__('common.nav.transparency')) ?></a>
                    <a href="<?= e(url('/components', $currentLocale)) ?>" style="color:var(--accent-gold);"><?= e(__('common.actions.view_components')) ?></a>
                </div>
            </div>

            <!-- Contact & Social Column -->
            <div>
                <h4 class="footer-heading"><?= e(__('common.footer.address_title')) ?></h4>
                <p style="font-size:0.88rem; color:var(--text-on-dark-muted); line-height:1.6; margin-bottom:var(--space-xs);">
                    <?= e(__('common.footer.address_lines')) ?>
                </p>
                <div style="font-size:0.86rem; color:var(--text-on-dark-muted); display:flex; flex-direction:column; gap:4px; margin-top:var(--space-xs);">
                    <span><strong><?= e(__('common.footer.phone_label')) ?>:</strong> +880 1736-360041, +880 1782-009415</span>
                    <span><strong><?= e(__('common.footer.email_label')) ?>:</strong> contact@sps-platform.org</span>
                </div>

                <!-- Official Social Links -->
                <div style="margin-top:var(--space-sm); display:flex; flex-wrap:wrap; gap:8px;">
                    <a href="https://www.facebook.com/bewithsps" target="_blank" rel="noopener noreferrer" style="display:inline-flex; align-items:center; gap:4px; font-size:0.78rem; background:rgba(255,255,255,0.08); color:#cbd5e1; padding:3px 8px; border-radius:var(--radius-sm); text-decoration:none; border:1px solid rgba(255,255,255,0.12);">
                        <span>📘 Facebook</span>
                    </a>
                    <a href="https://www.youtube.com/@spsofficial1529" target="_blank" rel="noopener noreferrer" style="display:inline-flex; align-items:center; gap:4px; font-size:0.78rem; background:rgba(255,255,255,0.08); color:#cbd5e1; padding:3px 8px; border-radius:var(--radius-sm); text-decoration:none; border:1px solid rgba(255,255,255,0.12);">
                        <span>▶ YouTube</span>
                    </a>
                    <a href="https://sanatanphilosophyandscripture.blogspot.com" target="_blank" rel="noopener noreferrer" style="display:inline-flex; align-items:center; gap:4px; font-size:0.78rem; background:rgba(255,255,255,0.08); color:#cbd5e1; padding:3px 8px; border-radius:var(--radius-sm); text-decoration:none; border:1px solid rgba(255,255,255,0.12);">
                        <span>✍ Blog</span>
                    </a>
                    <a href="https://www.instagram.com/bewithsps/" target="_blank" rel="noopener noreferrer" style="display:inline-flex; align-items:center; gap:4px; font-size:0.78rem; background:rgba(255,255,255,0.08); color:#cbd5e1; padding:3px 8px; border-radius:var(--radius-sm); text-decoration:none; border:1px solid rgba(255,255,255,0.12);">
                        <span>📷 Instagram</span>
                    </a>
                </div>

                <div style="margin-top:var(--space-sm);">
                    <a href="<?= e(url('/transparency', $currentLocale)) ?>" class="btn btn-secondary btn-sm" style="border-color:rgba(255,255,255,0.2); color:#FFFFFF !important; font-size:0.78rem; padding:4px 10px;">
                        <span><?= e(__('common.nav.transparency')) ?></span>
                    </a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div>
                <?= e(__('common.footer.copyright')) ?>
            </div>
            <div style="display:flex; gap:var(--space-md);">
                <a href="#!"><?= e(__('common.footer.privacy_policy')) ?></a>
                <span>•</span>
                <a href="#!"><?= e(__('common.footer.terms_of_service')) ?></a>
                <span>•</span>
                <a href="<?= e(url('/transparency', $currentLocale)) ?>"><?= e(__('common.footer.financial_ethics')) ?></a>
                <span>•</span>
                <a href="<?= e(url('/admin/login', $currentLocale)) ?>" style="opacity: 0.75;" title="<?= $currentLocale === 'bn' ? 'প্রশাসনিক লগইন পোর্টাল' : 'Admin Login Portal' ?>">
                    🔒 <?= $currentLocale === 'bn' ? 'অ্যাডমিন পোর্টাল' : 'Admin Portal' ?>
                </a>
            </div>
        </div>
    </div>
</footer>
