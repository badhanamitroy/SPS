<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
?>
<!-- SPS Theme Toggle (Device-Aware Dark/Light Switcher) -->
<button type="button" 
        class="theme-toggle-btn" 
        id="spsThemeToggle" 
        data-theme-toggle 
        aria-label="<?= $isBn ? 'থিম পরিবর্তন (ডার্ক / লাইট)' : 'Toggle Dark / Light Theme' ?>" 
        title="<?= $isBn ? 'থিম পরিবর্তন (ডার্ক / লাইট)' : 'Toggle Dark / Light Theme' ?>"
        aria-pressed="false">
    <!-- Moon Icon for Light Mode (Click to switch to dark) -->
    <span class="theme-icon icon-moon" aria-hidden="true">
        <i class="fa-solid fa-moon"></i>
        <!-- Fallback SVG if fontawesome is delayed -->
        <svg class="icon-fallback" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
        </svg>
    </span>
    <!-- Sun Icon for Dark Mode (Click to switch to light) -->
    <span class="theme-icon icon-sun" aria-hidden="true" style="display:none;">
        <i class="fa-solid fa-sun"></i>
        <!-- Fallback SVG if fontawesome is delayed -->
        <svg class="icon-fallback" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
            <circle cx="12" cy="12" r="5"></circle>
            <line x1="12" y1="1" x2="12" y2="3"></line>
            <line x1="12" y1="21" x2="12" y2="23"></line>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
            <line x1="1" y1="12" x2="3" y2="12"></line>
            <line x1="21" y1="12" x2="23" y2="12"></line>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
        </svg>
    </span>
</button>
