<?php
$currentLocale = current_locale();
?>
<div class="lang-toggle" role="group" aria-label="Language selection">
    <a href="<?= e(route_switch_url('bn')) ?>" 
       class="lang-toggle-link <?= $currentLocale === 'bn' ? 'active' : '' ?>" 
       data-lang="bn"
       hreflang="bn"
       aria-current="<?= $currentLocale === 'bn' ? 'true' : 'false' ?>">
       বাংলা
    </a>
    <span style="color:var(--border-medium); font-size:0.75rem; user-select:none;">|</span>
    <a href="<?= e(route_switch_url('en')) ?>" 
       class="lang-toggle-link <?= $currentLocale === 'en' ? 'active' : '' ?>" 
       data-lang="en"
       hreflang="en"
       aria-current="<?= $currentLocale === 'en' ? 'true' : 'false' ?>">
       English
    </a>
</div>
