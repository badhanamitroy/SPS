<?php
$currentLocale = current_locale();
?>
<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-grid">
            <!-- Brand & Purpose -->
            <div>
                <div style="display:flex; align-items:center; gap:var(--space-sm); margin-bottom:var(--space-xs);">
                    <svg width="34" height="34" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <circle cx="50" cy="50" r="46" stroke="#C65A1E" stroke-width="1.5" stroke-opacity="0.6" />
                        <path d="M50 12 C54 26, 68 34, 88 50 C68 66, 54 74, 50 88 C46 74, 32 66, 12 50 C32 34, 46 26, 50 12 Z" stroke="#A37E36" stroke-width="1.5" fill="#FAF6F0" fill-opacity="0.9" />
                        <polygon points="50,34 62,50 50,66 38,50" stroke="#C65A1E" stroke-width="1.5" fill="#FFFFFF" />
                        <circle cx="50" cy="50" r="3.5" fill="#A37E36" />
                    </svg>
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

            <!-- Contact & Transparency Column -->
            <div>
                <h4 class="footer-heading"><?= e(__('common.footer.address_title')) ?></h4>
                <p style="font-size:0.88rem; color:var(--text-on-dark-muted); line-height:1.6; margin-bottom:var(--space-xs);">
                    <?= e(__('common.footer.address_lines')) ?>
                </p>
                <div style="font-size:0.86rem; color:var(--text-on-dark-muted); display:flex; flex-direction:column; gap:4px; margin-top:var(--space-xs);">
                    <span><strong><?= e(__('common.footer.email_label')) ?>:</strong> contact@sps-platform.org</span>
                    <span><strong><?= e(__('common.footer.phone_label')) ?>:</strong> +880 1712-345678</span>
                </div>
                <div style="margin-top:var(--space-md);">
                    <a href="<?= e(url('/transparency', $currentLocale)) ?>" class="btn btn-secondary btn-sm" style="border-color:rgba(255,255,255,0.2); color:#FFFFFF !important;">
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
            </div>
        </div>
    </div>
</footer>
