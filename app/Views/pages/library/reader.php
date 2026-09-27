<?php
$currentLocale = current_locale();
$isBn = $currentLocale === 'bn';
$bookTitle = $isBn ? $book['title_bn'] : $book['title_en'];
?>

<div class="ebook-reader-container" id="ebook-reader-container">
    <!-- Reader Master Toolbar -->
    <header class="reader-toolbar">
        <div class="reader-toolbar-left">
            <a href="<?= e(url('/library/book/' . $book['slug'], $currentLocale)) ?>" class="btn btn-ghost btn-sm" title="<?= $isBn ? 'গ্রন্থাগারে ফিরুন' : 'Back to Library' ?>">
                ← <span class="hide-mobile"><?= $isBn ? 'গ্রন্থাগার' : 'Library' ?></span>
            </a>
            <div class="reader-title-badge">
                <span class="reader-book-title" title="<?= e($bookTitle) ?>"><?= e($bookTitle) ?></span>
                <span class="badge badge-success hide-mobile" style="font-size:0.72rem; padding:2px 6px;">
                    ⭐ <?= $isBn ? 'সদস্য রিডার' : 'Member Access' ?>
                </span>
            </div>
        </div>

        <!-- Center: Pagination Controls -->
        <div class="reader-toolbar-center">
            <button id="prev-page" class="reader-btn" aria-label="Previous Page" title="<?= $isBn ? 'পূর্ববর্তী পৃষ্ঠা' : 'Previous Page' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </button>
            <div class="reader-page-indicator">
                <span><?= $isBn ? 'পৃষ্ঠা' : 'Page' ?></span>
                <input type="number" id="page-num" value="1" min="1" max="<?= e($book['pages_count']) ?>" aria-label="Current Page">
                <span>/ <span id="page-count"><?= e($book['pages_count']) ?></span></span>
            </div>
            <button id="next-page" class="reader-btn" aria-label="Next Page" title="<?= $isBn ? 'পরবর্তী পৃষ্ঠা' : 'Next Page' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>
        </div>

        <!-- Right: Zoom, Theme, and Fullscreen Controls -->
        <div class="reader-toolbar-right">
            <!-- Zoom Controls -->
            <div class="reader-zoom-group hide-mobile">
                <button id="zoom-out" class="reader-btn" title="<?= $isBn ? 'ছোট করুন' : 'Zoom Out' ?>" aria-label="Zoom Out">−</button>
                <span id="zoom-level" class="reader-zoom-text">100%</span>
                <button id="zoom-in" class="reader-btn" title="<?= $isBn ? 'বড় করুন' : 'Zoom In' ?>" aria-label="Zoom In">+</button>
                <button id="zoom-fit" class="reader-btn btn-sm" title="<?= $isBn ? 'স্ক্রিনে ফিট করুন' : 'Fit to Width' ?>" style="font-size:0.75rem; padding:2px 6px;"><?= $isBn ? 'ফিট' : 'Fit' ?></button>
            </div>

            <!-- Reading Canvas Theme Switcher -->
            <div class="reader-theme-selector">
                <button class="theme-btn theme-parchment active" data-theme="parchment" title="<?= $isBn ? 'পাণ্ডুলিপি কালার (আইভরি)' : 'Parchment Ivory' ?>" aria-label="Parchment theme"></button>
                <button class="theme-btn theme-white" data-theme="white" title="<?= $isBn ? 'উজ্জ্বল সাদা' : 'Crisp White' ?>" aria-label="White theme"></button>
                <button class="theme-btn theme-night" data-theme="night" title="<?= $isBn ? 'রাত্রিকালীন পাঠ (ডার্ক)' : 'Night Dark' ?>" aria-label="Night theme"></button>
            </div>

            <!-- Fullscreen Button -->
            <button id="fullscreen-toggle" class="reader-btn" title="<?= $isBn ? 'ফুলস্ক্রিন' : 'Toggle Fullscreen' ?>" aria-label="Toggle Fullscreen">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path></svg>
            </button>
        </div>
    </header>

    <!-- Reader Security & Protection Notice -->
    <div class="reader-security-bar">
        <span>🔒 <strong><?= $isBn ? 'এসপিএস সংরক্ষিত ই-বুক সংস্করণ:' : 'SPS Protected E-Book Archive:' ?></strong> <?= $isBn ? 'ডাউনলোড সম্পূর্ণ নিষিদ্ধ • অননুমোদিত অনুলিপিকরণ বা প্রিন্টিং সংরক্ষিত।' : 'Downloads strictly restricted • Canvas stream with anti-piracy protection.' ?></span>
    </div>

    <!-- Main Reading Viewport -->
    <main class="reader-stage" id="reader-stage">
        <!-- Loading State -->
        <div id="reader-loading" class="reader-loading-overlay">
            <div class="reader-spinner"></div>
            <div style="font-weight:600; margin-top:var(--space-md); color:var(--text-main);">
                <?= $isBn ? 'ই-বুকের পৃষ্ঠা প্রস্তুত হচ্ছে...' : 'Rendering high-resolution e-book canvas...' ?>
            </div>
            <div style="font-size:0.84rem; color:var(--text-muted); margin-top:4px;">
                <?= e($book['file_size']) ?> • <?= e($book['pages_count']) ?> <?= $isBn ? 'পৃষ্ঠা' : 'pages' ?>
            </div>
        </div>

        <!-- Canvas Container -->
        <div class="reader-canvas-wrapper" id="reader-canvas-wrapper">
            <canvas id="pdf-render-canvas"></canvas>
            <!-- Watermark Overlay -->
            <div class="reader-watermark">SPS SCHOLARLY ARCHIVE • NO DOWNLOAD PERMITTED</div>
        </div>
    </main>
</div>

<!-- Reader Custom Styles -->
<style>
.ebook-reader-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  width: 100%;
  background-color: #EDE7DF;
  overflow: hidden;
  user-select: none;
  -webkit-user-select: none;
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
}

.reader-toolbar-left, .reader-toolbar-center, .reader-toolbar-right {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
}

.reader-title-badge {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
  margin-left: var(--space-xs);
}

.reader-book-title {
  font-weight: 600;
  font-size: 0.92rem;
  color: var(--text-main);
  max-width: 280px;
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
  transition: background var(--transition-fast);
}

.reader-btn:hover {
  background: var(--border-subtle);
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
  font-weight: 600;
  font-size: 0.88rem;
}

.reader-zoom-group {
  display: flex;
  align-items: center;
  gap: 4px;
}

.reader-zoom-text {
  font-size: 0.8rem;
  font-weight: 600;
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
.theme-night { background: #1F2421; box-shadow: 0 0 0 1px #444; }

.reader-security-bar {
  background: #252D28;
  color: #B8C2BC;
  font-size: 0.76rem;
  padding: 4px var(--space-md);
  text-align: center;
  letter-spacing: 0.02em;
  border-bottom: 1px solid rgba(0,0,0,0.1);
}

.reader-stage {
  flex-grow: 1;
  overflow: auto;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding: var(--space-xl);
  position: relative;
  transition: background-color var(--transition-normal);
}

.reader-stage.theme-parchment { background-color: #EDE7DF; }
.reader-stage.theme-white { background-color: #E2E6E9; }
.reader-stage.theme-night { background-color: #121614; }

.reader-canvas-wrapper {
  position: relative;
  box-shadow: 0 8px 32px rgba(0,0,0,0.22), 0 0 0 1px rgba(0,0,0,0.08);
  border-radius: 2px;
  background: #FFFFFF;
  margin: 0 auto;
}

#pdf-render-canvas {
  display: block;
  max-width: 100%;
}

.reader-watermark {
  position: absolute;
  bottom: 12px;
  right: 16px;
  font-size: 0.7rem;
  letter-spacing: 0.1em;
  color: rgba(0,0,0,0.25);
  font-weight: 700;
  pointer-events: none;
}

.reader-loading-overlay {
  position: absolute;
  inset: 0;
  background: rgba(250, 248, 245, 0.9);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 50;
  transition: opacity 0.3s ease;
}

.reader-spinner {
  width: 40px;
  height: 40px;
  border: 3px solid var(--border-medium);
  border-top-color: var(--accent-saffron);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
  .hide-mobile { display: none !important; }
  .reader-book-title { max-width: 130px; }
  .reader-stage { padding: var(--space-md); }
}
</style>

<!-- PDF.js Engine with Anti-Download Script -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
(function() {
  'use strict';

  // Prevent right-click / context menu (No Download)
  document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
  });

  // Prevent save & print shortcuts (Ctrl+S, Ctrl+P, Ctrl+U)
  document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'p' || e.key === 'u')) {
      e.preventDefault();
      alert('<?= $isBn ? 'এসপিএস নিরাপত্তা নীতি অনুযায়ী প্রকাশনাটি ডাউনলোড বা প্রিন্ট করা নিষিদ্ধ।' : 'Downloading or printing this publication is strictly prohibited by SPS copyright policy.' ?>');
    }
  });

  // Configure PDF.js Worker
  pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

  const pdfUrl = <?= json_encode($pdfStreamUrl) ?>;
  let pdfDoc = null;
  let pageNum = 1;
  let pageRendering = false;
  let pageNumPending = null;
  let scale = 1.25;

  const canvas = document.getElementById('pdf-render-canvas');
  const ctx = canvas.getContext('2d');
  const loadingOverlay = document.getElementById('reader-loading');
  const pageNumInput = document.getElementById('page-num');
  const pageCountSpan = document.getElementById('page-count');
  const zoomLevelSpan = document.getElementById('zoom-level');
  const stage = document.getElementById('reader-stage');

  function renderPage(num) {
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
          setTimeout(() => { loadingOverlay.style.display = 'none'; }, 300);
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
    if (!pdfDoc || pageNum >= pdfDoc.numPages) return;
    pageNum++;
    queueRenderPage(pageNum);
  });

  pageNumInput.addEventListener('change', function() {
    let desired = parseInt(this.value, 10);
    if (isNaN(desired)) desired = 1;
    if (desired < 1) desired = 1;
    if (pdfDoc && desired > pdfDoc.numPages) desired = pdfDoc.numPages;
    pageNum = desired;
    queueRenderPage(pageNum);
  });

  // Keyboard Arrow Page Flip
  document.addEventListener('keydown', function(e) {
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
    if (e.key === 'ArrowRight' || e.key === 'PageDown') {
      if (pdfDoc && pageNum < pdfDoc.numPages) {
        pageNum++;
        queueRenderPage(pageNum);
      }
    } else if (e.key === 'ArrowLeft' || e.key === 'PageUp') {
      if (pageNum > 1) {
        pageNum--;
        queueRenderPage(pageNum);
      }
    }
  });

  // Zoom handlers
  document.getElementById('zoom-in').addEventListener('click', function() {
    if (scale >= 3.0) return;
    scale += 0.25;
    zoomLevelSpan.textContent = Math.round((scale / 1.25) * 100) + '%';
    queueRenderPage(pageNum);
  });

  document.getElementById('zoom-out').addEventListener('click', function() {
    if (scale <= 0.6) return;
    scale -= 0.25;
    zoomLevelSpan.textContent = Math.round((scale / 1.25) * 100) + '%';
    queueRenderPage(pageNum);
  });

  document.getElementById('zoom-fit').addEventListener('click', function() {
    if (!stage) return;
    const availableWidth = stage.clientWidth - 60;
    pdfDoc.getPage(pageNum).then(function(page) {
      const naturalViewport = page.getViewport({ scale: 1.0 });
      scale = availableWidth / naturalViewport.width;
      zoomLevelSpan.textContent = Math.round((scale / 1.25) * 100) + '%';
      queueRenderPage(pageNum);
    });
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
    pageCountSpan.textContent = pdfDoc.numPages;
    pageNumInput.max = pdfDoc.numPages;
    renderPage(pageNum);
  }).catch(function(error) {
    console.error('PDF loading error:', error);
    loadingOverlay.innerHTML = '<div style="color:var(--status-danger); font-weight:600;"><?= $isBn ? 'ই-বুক লোড করতে সমস্যা হয়েছে। অনুগ্রহ করে পৃষ্ঠা রিফ্রেশ করুন।' : 'Failed to stream protected e-book. Please refresh.' ?></div>';
  });

})();
</script>
