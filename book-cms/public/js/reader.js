/**
 * LibrisCMS - Reader Engine
 * Scroll Progress, Typographic Sizing, Reading Themes, Fullscreen & Bookmarking
 */

document.addEventListener('DOMContentLoaded', () => {
  initReadingProgress();
  initReaderControls();
  initBookmark();
  initFullscreen();
});

/* ==========================================================================
   1. Reading Progress Bar
   ========================================================================== */
function initReadingProgress() {
  const progressBar = document.getElementById('reading-progress-bar');
  if (!progressBar) return;

  window.addEventListener('scroll', () => {
    const totalHeight = document.documentElement.scrollHeight - window.innerHeight;
    if (totalHeight <= 0) {
      progressBar.style.width = '100%';
      return;
    }
    const progress = (window.scrollY / totalHeight) * 100;
    progressBar.style.width = `${Math.min(100, Math.max(0, progress))}%`;
  }, { passive: true });
}

/* ==========================================================================
   2. Reader Controls (Font Size, Theme, Font Family)
   ========================================================================== */
function initReaderControls() {
  const root = document.documentElement;

  // Restore saved reader preferences
  const savedTheme = localStorage.getItem('libris_reader_theme') || 'sepia';
  const savedFont = localStorage.getItem('libris_reader_font') || 'serif';
  const savedSize = localStorage.getItem('libris_reader_size') || 'medium';

  root.setAttribute('data-reader-theme', savedTheme);
  root.setAttribute('data-reader-font', savedFont);
  root.setAttribute('data-reader-size', savedSize);

  updateThemeSwatches(savedTheme);
  updateFontButtons(savedFont);
  updateSizeLabel(savedSize);

  // Theme Swatches
  document.querySelectorAll('.theme-swatch').forEach(swatch => {
    swatch.addEventListener('click', () => {
      const theme = swatch.getAttribute('data-theme');
      root.setAttribute('data-reader-theme', theme);
      localStorage.setItem('libris_reader_theme', theme);
      updateThemeSwatches(theme);
    });
  });

  // Font Family Toggles
  document.querySelectorAll('.font-toggle-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const font = btn.getAttribute('data-font');
      root.setAttribute('data-reader-font', font);
      localStorage.setItem('libris_reader_font', font);
      updateFontButtons(font);
    });
  });

  // Font Size Steps: small (85%), medium (100%), large (115%), xlarge (130%)
  const sizes = ['small', 'medium', 'large', 'xlarge'];
  const decBtn = document.getElementById('font-dec-btn');
  const incBtn = document.getElementById('font-inc-btn');

  if (decBtn && incBtn) {
    decBtn.addEventListener('click', () => {
      const cur = root.getAttribute('data-reader-size') || 'medium';
      const idx = sizes.indexOf(cur);
      if (idx > 0) {
        const next = sizes[idx - 1];
        root.setAttribute('data-reader-size', next);
        localStorage.setItem('libris_reader_size', next);
        updateSizeLabel(next);
      }
    });

    incBtn.addEventListener('click', () => {
      const cur = root.getAttribute('data-reader-size') || 'medium';
      const idx = sizes.indexOf(cur);
      if (idx < sizes.length - 1) {
        const next = sizes[idx + 1];
        root.setAttribute('data-reader-size', next);
        localStorage.setItem('libris_reader_size', next);
        updateSizeLabel(next);
      }
    });
  }
}

function updateThemeSwatches(theme) {
  document.querySelectorAll('.theme-swatch').forEach(s => {
    s.classList.toggle('active', s.getAttribute('data-theme') === theme);
  });
}

function updateFontButtons(font) {
  document.querySelectorAll('.font-toggle-btn').forEach(b => {
    b.classList.toggle('active', b.getAttribute('data-font') === font);
  });
}

function updateSizeLabel(size) {
  const lbl = document.getElementById('font-size-label');
  if (!lbl) return;
  const labels = { small: '85%', medium: '100%', large: '120%', xlarge: '140%' };
  lbl.textContent = labels[size] || '100%';
}

/* ==========================================================================
   3. Bookmarking (Local Storage)
   ========================================================================== */
function initBookmark() {
  const bookmarkBtn = document.getElementById('bookmark-btn');
  if (!bookmarkBtn) return;

  const urlParams = new URLSearchParams(window.location.search);
  const bookId = urlParams.get('book_id') || '0';
  const chapter = urlParams.get('chapter') || '1';
  const key = `bookmark_book_${bookId}`;

  // Check if bookmarked
  const saved = localStorage.getItem(key);
  if (saved && JSON.parse(saved).chapter === chapter) {
    bookmarkBtn.style.color = '#f59e0b';
  }

  bookmarkBtn.addEventListener('click', () => {
    const currentScroll = window.scrollY;
    const title = document.querySelector('.reader-chapter-title')?.textContent || 'Chapter';

    const bookmarkData = {
      chapter,
      scrollY: currentScroll,
      timestamp: new Date().toISOString(),
      title
    };

    localStorage.setItem(key, JSON.stringify(bookmarkData));
    bookmarkBtn.style.color = '#f59e0b';
    showToast(`Bookmark saved for Chapter ${chapter}!`, 'success');
  });
}

/* ==========================================================================
   4. Fullscreen Mode
   ========================================================================== */
function initFullscreen() {
  const fsBtn = document.getElementById('fullscreen-btn');
  if (!fsBtn) return;

  fsBtn.addEventListener('click', () => {
    if (!document.fullscreenElement) {
      document.documentElement.requestFullscreen().catch(() => {});
    } else {
      if (document.exitFullscreen) {
        document.exitFullscreen();
      }
    }
  });
}

function showToast(message, type = 'info') {
  const container = document.getElementById('toast-container');
  if (!container) return;

  const toast = document.createElement('div');
  toast.className = 'toast';
  toast.innerHTML = `
    <span>${message}</span>
    <button style="background:none; border:none; color:inherit; cursor:pointer; margin-left:auto; font-size:1.1rem;" onclick="this.parentElement.remove()">&times;</button>
  `;

  container.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(10px)';
    toast.style.transition = 'all 0.3s ease';
    setTimeout(() => toast.remove(), 300);
  }, 3500);
}

