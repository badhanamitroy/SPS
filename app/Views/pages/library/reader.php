<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$bookTitle = $isBn ? $book['title_bn'] : $book['title_en'];
$author = $isBn ? $book['author_bn'] : $book['author_en'];
$publisher = $isBn ? ($book['publisher_bn'] ?? 'এসপিএস') : ($book['publisher_en'] ?? 'SPS');
$categoryName = $isBn ? $book['category_bn'] : $book['category_en'];
$year = $book['publication_year'] ?? '';
$slug = $book['slug'];

$readingLevel = $readingLevel ?? 'full';
$readingScope = $readingScope ?? 'full';
$previewStart = (int)($previewStart ?? 1);
$previewEnd = (int)($previewEnd ?? 20);
$totalPages = (int)($totalPages ?? ($book['pages_count'] ?? 1));

$downloadInfo = $downloadEligibility ?? ['status' => 'disabled'];
?>

<div class="ebook-reader-container" id="ebook-reader-container">
    <!-- Reader Master Toolbar -->
    <header class="reader-toolbar" id="reader-toolbar">
        <div class="reader-toolbar-left">
            <a href="<?= e(url('/library/book/' . $slug, $currentLocale)) ?>" class="btn btn-ghost btn-sm" title="<?= $isBn ? 'গ্রন্থাগারে ফিরুন' : 'Back to Library' ?>" style="padding:4px 8px;">
                ← <span class="hide-mobile"><?= $isBn ? 'গ্রন্থাগার' : 'Library' ?></span>
            </a>
            
            <div class="reader-doc-meta" title="<?= e($bookTitle) ?> • <?= e($author) ?> • <?= e($publisher) ?>">
                <span class="reader-book-title"><?= e($bookTitle) ?></span>
                <div class="reader-submeta hide-mobile">
                    <span><?= e($author) ?></span>
                    <span>•</span>
                    <span><?= e($publisher) ?></span>
                </div>
            </div>

            <!-- Reading Access Badge -->
            <div class="reader-access-tag hide-mobile">
                <?php if ($readingLevel === 'partial'): ?>
                    <span class="badge badge-warning" style="font-size:0.72rem; padding:2px 8px;">
                        📖 <?= $isBn ? "পাবলিক প্রিভিউ · পৃষ্ঠা {$previewStart}–{$previewEnd}" : "Public Preview · Pages {$previewStart}–{$previewEnd}" ?>
                    </span>
                <?php else: ?>
                    <span class="badge badge-success" style="font-size:0.72rem; padding:2px 8px;">
                        ⭐ <?= $isBn ? 'সদস্য রিডার' : 'Member Access' ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Center: Pagination Controls -->
        <div class="reader-toolbar-center">
            <button id="prev-page" class="reader-btn" aria-label="Previous Page" title="<?= $isBn ? 'পূর্ববর্তী পৃষ্ঠা' : 'Previous Page' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </button>
            <div class="reader-page-indicator">
                <span><?= $isBn ? 'পৃষ্ঠা' : 'Page' ?></span>
                <input type="number" id="page-num" value="1" min="1" max="<?= $totalPages ?>" aria-label="Current Page">
                <span>/ <span id="page-count"><?= $totalPages ?></span></span>
            </div>
            <button id="next-page" class="reader-btn" aria-label="Next Page" title="<?= $isBn ? 'পরবর্তী পৃষ্ঠা' : 'Next Page' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>
        </div>

        <!-- Right: Zoom, Theme, and Display Controls -->
        <div class="reader-toolbar-right">
            <!-- Zoom Controls (Approx 60% default) -->
            <div class="reader-zoom-group hide-mobile">
                <button id="zoom-out" class="reader-btn" title="<?= $isBn ? 'ছোট করুন' : 'Zoom Out' ?>" aria-label="Zoom Out">−</button>
                <span id="zoom-level" class="reader-zoom-text">60%</span>
                <button id="zoom-in" class="reader-btn" title="<?= $isBn ? 'বড় করুন' : 'Zoom In' ?>" aria-label="Zoom In">+</button>
                <button id="zoom-fit-width" class="reader-btn btn-sm" title="<?= $isBn ? 'প্রস্থে ফিট' : 'Fit Width' ?>" style="font-size:0.75rem; padding:3px 7px;">
                    <?= $isBn ? 'প্রস্থ' : 'Fit' ?>
                </button>
                <button id="zoom-fit-page" class="reader-btn btn-sm" title="<?= $isBn ? 'এ৪ পৃষ্ঠায় ফিট' : 'Fit Page' ?>" style="font-size:0.75rem; padding:3px 7px;">
                    A4
                </button>
            </div>

            <!-- Reading Canvas Theme Switcher -->
            <div class="reader-theme-selector hide-mobile">
                <button class="theme-btn theme-parchment active" data-theme="parchment" title="<?= $isBn ? 'পাণ্ডুলিপি আইভরি' : 'Parchment Ivory' ?>" aria-label="Parchment theme"></button>
                <button class="theme-btn theme-white" data-theme="white" title="<?= $isBn ? 'উজ্জ্বল সাদা' : 'Crisp White' ?>" aria-label="White theme"></button>
                <button class="theme-btn theme-night" data-theme="night" title="<?= $isBn ? 'রাত্রিকালীন পাঠ' : 'Night Dark' ?>" aria-label="Night theme"></button>
            </div>

            <!-- Focus / Clean Mode Toggle -->
            <button id="clean-mode-toggle" class="reader-btn" title="<?= $isBn ? 'ক্লিন রিডিং মোড' : 'Clean Reading Mode' ?>" aria-label="Toggle Clean Reading Mode">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            </button>

            <!-- Fullscreen Button -->
            <button id="fullscreen-toggle" class="reader-btn" title="<?= $isBn ? 'ফুলস্ক্রিন' : 'Toggle Fullscreen' ?>" aria-label="Toggle Fullscreen">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path></svg>
            </button>
        </div>
    </header>

    <!-- Reader Security & Document Status Sub-bar -->
    <div class="reader-security-bar">
        <div class="reader-security-info">
            <span>🔒 <strong><?= $isBn ? 'এসপিএস ডিজিটাল প্রকাশনা:' : 'SPS Digital Publishing:' ?></strong> <?= e($bookTitle) ?> (<?= e($categoryName) ?>)</span>
            <span class="hide-mobile">•</span>
            <span class="hide-mobile">
                <?php if ($readingLevel === 'partial'): ?>
                    <strong style="color:#FDE68A;"><?= $isBn ? "উন্মুক্ত প্রিভিউ · পৃষ্ঠা {$previewStart}–{$previewEnd}" : "Public Preview · Pages {$previewStart}–{$previewEnd}" ?></strong>
                <?php else: ?>
                    <strong style="color:#86EFAC;"><?= $isBn ? 'সম্পূর্ণ গ্রন্থ পড়ার অনুমতি সক্রিয়' : 'Full Book Reading Authorized' ?></strong>
                <?php endif; ?>
            </span>
            <span class="hide-mobile">•</span>
            <span class="hide-mobile"><?= $isBn ? 'অনুমতি ছাড়া ডাউনলোড বা মুদ্রণ নিষিদ্ধ' : 'Casual downloads & redistribution restricted' ?></span>
        </div>
        <div class="reader-download-indicator hide-mobile">
            <?php if ($downloadInfo['status'] === 'direct' || $downloadInfo['status'] === 'approved'): ?>
                <a href="<?= e(url('/library/download/' . $slug . (!empty($downloadInfo['token']) ? '?token=' . urlencode($downloadInfo['token']) : ''), $currentLocale)) ?>" class="btn btn-xs" style="background:#166534; color:#FFF; font-size:0.74rem; padding:2px 8px;">
                    📥 <?= $isBn ? 'ডাউনলোড প্রস্তুত' : 'Download Ready' ?>
                </a>
            <?php else: ?>
                <span style="font-size:0.74rem; color:#9CA3AF;">
                    📥 <?= e($isBn ? $downloadInfo['label_bn'] : $downloadInfo['label_en']) ?>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Reading Viewport -->
    <main class="reader-stage theme-parchment" id="reader-stage">
        <!-- Loading State -->
        <div id="reader-loading" class="reader-loading-overlay">
            <div class="reader-spinner"></div>
            <div style="font-weight:600; margin-top:var(--space-md); color:var(--text-main); font-size:0.95rem;">
                <?= $isBn ? 'ই-বুকের পৃষ্ঠা প্রস্তুত হচ্ছে...' : 'Rendering high-resolution e-book canvas...' ?>
            </div>
            <div style="font-size:0.84rem; color:var(--text-muted); margin-top:4px;">
                <?= e($book['file_size'] ?? '') ?> • <?= $totalPages ?> <?= $isBn ? 'পৃষ্ঠা' : 'pages' ?> • A4
            </div>
        </div>

        <!-- A4 Reading Presentation Wrapper -->
        <div class="reader-a4-viewport" id="reader-a4-viewport">
            <!-- Active Canvas for readable pages -->
            <div class="reader-canvas-wrapper" id="reader-canvas-wrapper">
                <canvas id="pdf-render-canvas"></canvas>

                <!-- Anti-Screen Capture Dynamic Watermark Overlay -->
                <div class="reader-watermark-grid" id="reader-watermark-grid">
                    <div class="watermark-item"><?= e($userWatermark) ?></div>
                    <div class="watermark-item watermark-secondary"><?= e($userWatermark) ?></div>
                    <div class="watermark-item watermark-tertiary"><?= e($userWatermark) ?></div>
                </div>

                <!-- Subtle Bottom Footer Tag -->
                <div class="reader-watermark">
                    SPS SCHOLARLY ARCHIVE • NO DOWNLOAD PERMITTED
                </div>
            </div>

            <!-- Page-Level Restricted / Locked Section Screen -->
            <div class="reader-locked-overlay" id="reader-locked-overlay" style="display:none;">
                <div class="locked-card">
                    <div class="locked-icon">🔒</div>
                    <h2 class="locked-title">
                        <?= $isBn ? 'এই অংশটি কেবলমাত্র সাধারণ সদস্যদের জন্য উন্মুক্ত' : 'This section is available to members' ?>
                    </h2>
                    <p class="locked-desc">
                        <?= $isBn 
                            ? "আপনি এই গ্রন্থটির উন্মুক্ত প্রিভিউ অংশ (পৃষ্ঠা ১–{$previewEnd}) সম্পন্ন করেছেন। পৃষ্ঠা {$previewEnd}+ থেকে পরবর্তী অংশ পড়তে সক্রিয় সাধারণ সদস্যপদ প্রয়োজন।" 
                            : "You have reached the end of the public preview (pages 1–{$previewEnd}). Pages " . ($previewEnd + 1) . "–{$totalPages} require an active SPS Membership." ?>
                    </p>
                    <div class="locked-actions">
                        <a href="<?= e(url('/membership/apply', $currentLocale)) ?>" class="btn btn-primary">
                            ⭐ <?= $isBn ? 'সদস্যপদ গ্রহণ করুন' : 'Become a Member' ?>
                        </a>
                        <a href="<?= e(url('/membership/login', $currentLocale)) ?>" class="btn btn-secondary">
                            🔑 <?= $isBn ? 'লগইন করুন' : 'Member Login' ?>
                        </a>
                        <button type="button" id="back-to-preview-btn" class="btn btn-ghost">
                            ← <?= $isBn ? "প্রিভিউতে ফিরুন (পৃষ্ঠা {$previewEnd})" : "Back to Preview (Page {$previewEnd})" ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Reader Custom Stylesheet -->
<style>
/* CSS Print Blocker: Renders blank on browser print */
@media print {
  body { display: none !important; }
}

.ebook-reader-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  width: 100%;
  background-color: #EDE7DF;
  overflow: hidden;
  user-select: none;
  -webkit-user-select: none;
  position: relative;
}

.reader-toolbar {
  height: 60px;
  background-color: var(--bg-surface);
  border-bottom: 1px solid var(--border-medium);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 var(--space-md);
  flex-shrink: 0;
  z-index: 100;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06);
  transition: transform 0.25s ease, opacity 0.25s ease;
}

.reader-toolbar.clean-hidden {
  transform: translateY(-100%);
  opacity: 0;
  pointer-events: none;
}

.reader-toolbar-left, .reader-toolbar-center, .reader-toolbar-right {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
}

.reader-doc-meta {
  display: flex;
  flex-direction: column;
  margin-left: 6px;
  max-width: 260px;
}

.reader-book-title {
  font-weight: 700;
  font-size: 0.88rem;
  color: var(--text-main);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.reader-submeta {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.74rem;
  color: var(--text-muted);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.reader-btn {
  background: var(--bg-subtle);
  border: 1px solid var(--border-medium);
  color: var(--text-main);
  border-radius: var(--radius-xs);
  padding: 6px 10px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 0.88rem;
  transition: background var(--transition-fast), border-color var(--transition-fast);
}

.reader-btn:hover {
  background: var(--border-subtle);
  border-color: var(--border-strong);
}

.reader-page-indicator {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.86rem;
  color: var(--text-muted);
  font-weight: 500;
}

.reader-page-indicator input {
  width: 48px;
  height: 28px;
  text-align: center;
  border: 1px solid var(--border-medium);
  border-radius: var(--radius-xs);
  background: #FFF;
  font-weight: 700;
  font-size: 0.88rem;
  color: var(--text-main);
}

.reader-zoom-group {
  display: flex;
  align-items: center;
  gap: 4px;
}

.reader-zoom-text {
  font-size: 0.8rem;
  font-weight: 700;
  color: var(--text-muted);
  width: 44px;
  text-align: center;
}

.reader-theme-selector {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 0 6px;
}

.theme-btn {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 2px solid transparent;
  cursor: pointer;
  padding: 0;
}

.theme-btn.active {
  border-color: var(--accent-saffron);
}

.theme-parchment { background: #FAF5EE; box-shadow: 0 0 0 1px #D5CCC0; }
.theme-white { background: #FFFFFF; box-shadow: 0 0 0 1px #D5CCC0; }
.theme-night { background: #101522; box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.15); }

.reader-security-bar {
  background: #0F172A;
  color: #94A3B8;
  font-size: 0.76rem;
  padding: 5px var(--space-md);
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid rgba(255,255,255,0.08);
  flex-shrink: 0;
  z-index: 90;
}

.reader-security-info {
  display: flex;
  align-items: center;
  gap: 8px;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}

.reader-stage {
  flex-grow: 1;
  overflow: auto;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding: var(--space-lg);
  position: relative;
  transition: background-color var(--transition-normal);
}

.reader-stage.theme-parchment { background-color: #EDE7DF; }
.reader-stage.theme-white { background-color: #E2E6E9; }
.reader-stage.theme-night { background-color: #090B10; }

.reader-a4-viewport {
  position: relative;
  margin: 0 auto;
  transition: transform 0.2s ease;
}

/* Dedicated A4 Canvas Box */
.reader-canvas-wrapper {
  position: relative;
  box-shadow: 0 8px 32px rgba(0,0,0,0.22), 0 0 0 1px rgba(0,0,0,0.08);
  border-radius: 2px;
  background: #FFFFFF;
  margin: 0 auto;
  display: block;
}

#pdf-render-canvas {
  display: block;
  max-width: 100%;
}

/* Dynamic Anti-Capture Attribution Watermark Layer */
.reader-watermark-grid {
  position: absolute;
  inset: 0;
  pointer-events: none;
  display: flex;
  flex-direction: column;
  justify-content: space-around;
  padding: 15% 10%;
  overflow: hidden;
}

.watermark-item {
  transform: rotate(-25deg);
  font-size: 1.1rem;
  font-weight: 700;
  color: rgba(15, 23, 42, 0.08);
  text-align: center;
  white-space: nowrap;
  letter-spacing: 0.12em;
  user-select: none;
}

.watermark-secondary {
  color: rgba(198, 90, 30, 0.07);
  transform: rotate(-25deg) translateX(40px);
}

.watermark-tertiary {
  color: rgba(15, 23, 42, 0.06);
  transform: rotate(-25deg) translateX(-40px);
}

.reader-stage.theme-night .watermark-item {
  color: rgba(255, 255, 255, 0.06);
}

.reader-watermark {
  position: absolute;
  bottom: 8px;
  right: 12px;
  font-size: 0.65rem;
  letter-spacing: 0.1em;
  color: rgba(0,0,0,0.25);
  font-weight: 700;
  pointer-events: none;
}

/* Locked Section Overlay */
.reader-locked-overlay {
  width: 580px;
  min-height: 520px;
  background: #FFFFFF;
  border-radius: 4px;
  box-shadow: 0 12px 36px rgba(0,0,0,0.25), 0 0 0 1px rgba(0,0,0,0.08);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: var(--space-2xl);
  text-align: center;
  margin: 0 auto;
}

.locked-card {
  max-width: 440px;
}

.locked-icon {
  font-size: 3.2rem;
  margin-bottom: var(--space-sm);
}

.locked-title {
  font-size: 1.35rem;
  color: var(--text-main);
  margin-bottom: var(--space-sm);
  font-family: <?= $isBn ? 'var(--font-bn-serif)' : 'var(--font-display)' ?>;
}

.locked-desc {
  font-size: 0.92rem;
  line-height: 1.65;
  color: var(--text-muted);
  margin-bottom: var(--space-xl);
}

.locked-actions {
  display: flex;
  flex-direction: column;
  gap: var(--space-xs);
}

.reader-loading-overlay {
  position: absolute;
  inset: 0;
  background: rgba(250, 248, 245, 0.92);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 50;
  transition: opacity 0.3s ease;
}

.reader-spinner {
  width: 42px;
  height: 42px;
  border: 3px solid var(--border-medium);
  border-top-color: var(--accent-saffron);
  border-radius: 50%;
  animation: reader-spin 0.8s linear infinite;
}

@keyframes reader-spin {
  to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
  .hide-mobile { display: none !important; }
  .reader-book-title { max-width: 140px; }
  .reader-stage { padding: var(--space-sm); }
  .reader-locked-overlay { width: 92vw; min-height: 400px; padding: var(--space-lg); }
}
</style>

<!-- PDF.js Engine with Anti-Download Script & Touch Navigation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
(function() {
  'use strict';

  // 1. Practical Anti-Download: Prevent context menu
  document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
  });

  // 2. Prevent Save & Print shortcuts (Ctrl+S, Ctrl+P, Ctrl+U)
  document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'p' || e.key === 'u')) {
      e.preventDefault();
      alert('<?= $isBn ? 'এসপিএস নিরাপত্তা নীতি অনুযায়ী প্রকাশনাটি ডাউনলোড বা প্রিন্ট করা নিষিদ্ধ।' : 'Downloading or printing this publication is strictly prohibited by SPS copyright policy.' ?>');
    }
  });

  // Configure PDF.js Worker
  pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

  const pdfUrl = <?= json_encode($pdfStreamUrl) ?>;
  const isPartial = <?= json_encode($readingLevel === 'partial') ?>;
  const previewEnd = <?= (int)$previewEnd ?>;
  const totalBookPages = <?= $totalPages ?>;

  let pdfDoc = null;
  let pageNum = 1;
  let pageRendering = false;
  let pageNumPending = null;

  // Default Zoom: Approximately 60% as required by specification!
  let scale = 0.65;
  const baseScale = 0.65;

  const canvas = document.getElementById('pdf-render-canvas');
  const ctx = canvas.getContext('2d');
  const canvasWrapper = document.getElementById('reader-canvas-wrapper');
  const lockedOverlay = document.getElementById('reader-locked-overlay');
  const loadingOverlay = document.getElementById('reader-loading');
  const pageNumInput = document.getElementById('page-num');
  const pageCountSpan = document.getElementById('page-count');
  const zoomLevelSpan = document.getElementById('zoom-level');
  const stage = document.getElementById('reader-stage');
  const toolbar = document.getElementById('reader-toolbar');

  function updateZoomLabel() {
    zoomLevelSpan.textContent = Math.round((scale / baseScale) * 60) + '%';
  }

  function renderPage(num) {
    // Check if user is navigating past allowed preview
    if (isPartial && num > previewEnd) {
      canvasWrapper.style.display = 'none';
      lockedOverlay.style.display = 'flex';
      pageNumInput.value = num;
      if (loadingOverlay) loadingOverlay.style.display = 'none';
      return;
    }

    canvasWrapper.style.display = 'block';
    lockedOverlay.style.display = 'none';

    if (!pdfDoc) return;

    pageRendering = true;
    pdfDoc.getPage(num).then(function(page) {
      const viewport = page.getViewport({ scale: scale });
      
      // Support High-DPI screens
      const outputScale = window.devicePixelRatio || 1;
      canvas.width = Math.floor(viewport.width * outputScale);
      canvas.height = Math.floor(viewport.height * outputScale);
      canvas.style.width = Math.floor(viewport.width) + "px";
      canvas.style.height = Math.floor(viewport.height) + "px";

      const transform = outputScale !== 1 ? [outputScale, 0, 0, outputScale, 0, 0] : null;

      const renderContext = {
        canvasContext: ctx,
        transform: transform,
        viewport: viewport
      };

      const renderTask = page.render(renderContext);
      renderTask.promise.then(function() {
        pageRendering = false;
        if (loadingOverlay) {
          loadingOverlay.style.opacity = '0';
          setTimeout(() => { loadingOverlay.style.display = 'none'; }, 250);
        }
        if (pageNumPending !== null) {
          renderPage(pageNumPending);
          pageNumPending = null;
        }
      });
    });

    pageNumInput.value = num;
  }

  function queueRenderPage(num) {
    if (pageRendering) {
      pageNumPending = num;
    } else {
      renderPage(num);
    }
  }

  // Navigation handlers
  document.getElementById('prev-page').addEventListener('click', function() {
    if (pageNum <= 1) return;
    pageNum--;
    queueRenderPage(pageNum);
  });

  document.getElementById('next-page').addEventListener('click', function() {
    const maxAllowed = isPartial ? (previewEnd + 1) : (pdfDoc ? pdfDoc.numPages : totalBookPages);
    if (pageNum >= maxAllowed) return;
    pageNum++;
    queueRenderPage(pageNum);
  });

  document.getElementById('back-to-preview-btn').addEventListener('click', function() {
    pageNum = previewEnd;
    queueRenderPage(pageNum);
  });

  pageNumInput.addEventListener('change', function() {
    let desired = parseInt(this.value, 10);
    if (isNaN(desired)) desired = 1;
    if (desired < 1) desired = 1;
    const maxPage = isPartial ? (previewEnd + 1) : totalBookPages;
    if (desired > maxPage) desired = maxPage;
    pageNum = desired;
    queueRenderPage(pageNum);
  });

  // Keyboard Navigation: Arrow Left/Right, PageUp/PageDown, Home/End
  document.addEventListener('keydown', function(e) {
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
    if (e.key === 'ArrowRight' || e.key === 'PageDown') {
      const maxAllowed = isPartial ? (previewEnd + 1) : (pdfDoc ? pdfDoc.numPages : totalBookPages);
      if (pageNum < maxAllowed) {
        pageNum++;
        queueRenderPage(pageNum);
      }
    } else if (e.key === 'ArrowLeft' || e.key === 'PageUp') {
      if (pageNum > 1) {
        pageNum--;
        queueRenderPage(pageNum);
      }
    } else if (e.key === 'Home') {
      pageNum = 1;
      queueRenderPage(pageNum);
    } else if (e.key === 'Escape') {
      toolbar.classList.remove('clean-hidden');
    }
  });

  // Touch Swipe Navigation for Android & iOS
  let touchStartX = 0;
  let touchStartY = 0;
  stage.addEventListener('touchstart', function(e) {
    touchStartX = e.changedTouches[0].screenX;
    touchStartY = e.changedTouches[0].screenY;
  }, { passive: true });

  stage.addEventListener('touchend', function(e) {
    const touchEndX = e.changedTouches[0].screenX;
    const touchEndY = e.changedTouches[0].screenY;
    const diffX = touchEndX - touchStartX;
    const diffY = touchEndY - touchStartY;

    if (Math.abs(diffX) > 50 && Math.abs(diffX) > Math.abs(diffY)) {
      if (diffX < 0) {
        // Swiped Left -> Next Page
        const maxAllowed = isPartial ? (previewEnd + 1) : (pdfDoc ? pdfDoc.numPages : totalBookPages);
        if (pageNum < maxAllowed) {
          pageNum++;
          queueRenderPage(pageNum);
        }
      } else {
        // Swiped Right -> Previous Page
        if (pageNum > 1) {
          pageNum--;
          queueRenderPage(pageNum);
        }
      }
    }
  }, { passive: true });

  // Zoom Controls: Initial ~60%, Step 0.15
  document.getElementById('zoom-in').addEventListener('click', function() {
    if (scale >= 2.2) return;
    scale += 0.15;
    updateZoomLabel();
    queueRenderPage(pageNum);
  });

  document.getElementById('zoom-out').addEventListener('click', function() {
    if (scale <= 0.35) return;
    scale -= 0.15;
    updateZoomLabel();
    queueRenderPage(pageNum);
  });

  // Fit Width
  document.getElementById('zoom-fit-width').addEventListener('click', function() {
    if (!stage || !pdfDoc) return;
    const availableWidth = stage.clientWidth - 40;
    const renderNum = Math.min(pageNum, pdfDoc.numPages);
    pdfDoc.getPage(renderNum).then(function(page) {
      const naturalViewport = page.getViewport({ scale: 1.0 });
      scale = availableWidth / naturalViewport.width;
      updateZoomLabel();
      queueRenderPage(pageNum);
    });
  });

  // Fit Page (A4 natural ratio)
  document.getElementById('zoom-fit-page').addEventListener('click', function() {
    scale = baseScale;
    updateZoomLabel();
    queueRenderPage(pageNum);
  });

  // Clean / Focus Mode Toggle
  document.getElementById('clean-mode-toggle').addEventListener('click', function() {
    toolbar.classList.toggle('clean-hidden');
  });

  // Theme switchers
  const themeBtns = document.querySelectorAll('.theme-btn');
  themeBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      themeBtns.forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      const theme = this.getAttribute('data-theme');
      stage.className = 'reader-stage theme-' + theme;
    });
  });

  // Fullscreen toggle
  document.getElementById('fullscreen-toggle').addEventListener('click', function() {
    const el = document.getElementById('ebook-reader-container');
    if (!document.fullscreenElement) {
      if (el.requestFullscreen) el.requestFullscreen();
      else if (el.webkitRequestFullscreen) el.webkitRequestFullscreen();
    } else {
      if (document.exitFullscreen) document.exitFullscreen();
    }
  });

  // Load PDF Stream
  const loadingTask = pdfjsLib.getDocument({
    url: pdfUrl,
    withCredentials: true
  });

  loadingTask.promise.then(function(pdfDoc_) {
    pdfDoc = pdfDoc_;
    if (!isPartial) {
      pageCountSpan.textContent = pdfDoc.numPages;
      pageNumInput.max = pdfDoc.numPages;
    }
    updateZoomLabel();
    renderPage(pageNum);
  }).catch(function(error) {
    console.error('PDF loading error:', error);
    loadingOverlay.innerHTML = '<div style="color:var(--status-danger); font-weight:600; text-align:center; padding:20px;"><?= $isBn ? 'ই-বুক লোড করতে সমস্যা হয়েছে। অনুগ্রহ করে পৃষ্ঠা রিফ্রেশ করুন।' : 'Failed to stream protected e-book. Please refresh or verify permissions.' ?></div>';
  });

})();
</script>
