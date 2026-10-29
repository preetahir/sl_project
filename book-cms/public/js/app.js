/**
 * LibrisCMS - Core Application JavaScript
 * ES6+ Features, Asynchronous DOM Manipulation, AJAX & Theming
 */

document.addEventListener('DOMContentLoaded', () => {
  initTheme();
  initMobileNav();
  initViewSwitcher();
  initLiveSearch();
  initModalListeners();
});

/* ==========================================================================
   1. Theme Management (Dark / Light Mode)
   ========================================================================== */
function initTheme() {
  const toggleBtn = document.getElementById('theme-toggle');
  const currentTheme = localStorage.getItem('libris_theme') || 
    (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

  document.documentElement.setAttribute('data-theme', currentTheme);

  if (toggleBtn) {
    toggleBtn.addEventListener('click', () => {
      const activeTheme = document.documentElement.getAttribute('data-theme');
      const newTheme = activeTheme === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', newTheme);
      localStorage.setItem('libris_theme', newTheme);
      showToast(`Switched to ${newTheme} theme`, 'info');
    });
  }
}

/* ==========================================================================
   2. Mobile Drawer Navigation
   ========================================================================== */
function initMobileNav() {
  const toggleBtn = document.getElementById('mobile-menu-toggle');
  const closeBtn = document.getElementById('mobile-close');
  const drawer = document.getElementById('mobile-drawer');
  const backdrop = document.getElementById('drawer-backdrop');

  if (!toggleBtn || !drawer || !backdrop) return;

  function openDrawer() {
    drawer.classList.add('open');
    backdrop.classList.add('active');
    toggleBtn.setAttribute('aria-expanded', 'true');
    drawer.setAttribute('aria-hidden', 'false');
  }

  function closeDrawer() {
    drawer.classList.remove('open');
    backdrop.classList.remove('active');
    toggleBtn.setAttribute('aria-expanded', 'false');
    drawer.setAttribute('aria-hidden', 'true');
  }

  toggleBtn.addEventListener('click', openDrawer);
  if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
  backdrop.addEventListener('click', closeDrawer);

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && drawer.classList.contains('open')) {
      closeDrawer();
    }
  });
}

/* ==========================================================================
   3. View Switcher (Grid vs. List View)
   ========================================================================== */
function initViewSwitcher() {
  const gridBtn = document.getElementById('view-grid-btn');
  const listBtn = document.getElementById('view-list-btn');
  const catalogGrid = document.getElementById('catalog-grid');

  if (!gridBtn || !listBtn || !catalogGrid) return;

  const savedView = localStorage.getItem('libris_catalog_view') || 'grid';
  setView(savedView);

  gridBtn.addEventListener('click', () => setView('grid'));
  listBtn.addEventListener('click', () => setView('list'));

  function setView(view) {
    if (view === 'list') {
      catalogGrid.classList.add('list-view');
      listBtn.classList.add('active');
      gridBtn.classList.remove('active');
    } else {
      catalogGrid.classList.remove('list-view');
      gridBtn.classList.add('active');
      listBtn.classList.remove('active');
    }
    localStorage.setItem('libris_catalog_view', view);
  }
}

/* ==========================================================================
   4. Live Client-Side Book Search & Filter
   ========================================================================== */
function initLiveSearch() {
  const searchInput = document.getElementById('live-search-input');
  if (!searchInput) return;

  searchInput.addEventListener('input', debounce((e) => {
    const query = e.target.value.toLowerCase().trim();
    const cards = document.querySelectorAll('.catalog-grid .book-card');
    let matchCount = 0;

    cards.forEach(card => {
      const title = card.getAttribute('data-title') || '';
      const author = card.getAttribute('data-author') || '';
      const content = card.innerText.toLowerCase();

      if (title.includes(query) || author.includes(query) || content.includes(query)) {
        card.style.display = '';
        matchCount++;
      } else {
        card.style.display = 'none';
      }
    });

    const countElem = document.querySelector('.filter-results-count');
    if (countElem) {
      countElem.innerHTML = `Showing <strong>${matchCount}</strong> match${matchCount === 1 ? '' : 'es'}`;
    }
  }, 200));
}

function debounce(func, delay) {
  let timeout;
  return function (...args) {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, args), delay);
  };
}

/* ==========================================================================
   5. Quick Preview Modal (AJAX Integration)
   ========================================================================== */
const previewModal = document.getElementById('quick-preview-modal');
const modalTitle = document.getElementById('modal-title');
const modalBody = document.getElementById('modal-body');
const modalClose = document.getElementById('modal-close');

function initModalListeners() {
  if (modalClose && previewModal) {
    modalClose.addEventListener('click', () => previewModal.close());
    previewModal.addEventListener('click', (e) => {
      const rect = previewModal.getBoundingClientRect();
      const isInDialog = (rect.top <= e.clientY && e.clientY <= rect.top + rect.height &&
        rect.left <= e.clientX && e.clientX <= rect.left + rect.width);
      if (!isInDialog) previewModal.close();
    });
  }
}

async function openQuickPreview(bookId) {
  if (!previewModal) return;
  previewModal.showModal();
  modalBody.innerHTML = '<div class="loading-spinner"></div>';
  modalTitle.textContent = 'Loading Book Details...';

  try {
    const res = await fetch(`/api/book?id=${bookId}`);
    if (!res.ok) throw new Error('Could not fetch book details');
    const { data: book } = await res.json();

    modalTitle.textContent = book.title;

    let chaptersHtml = '<p class="text-muted">No chapters listed yet.</p>';
    if (book.chapters && book.chapters.length > 0) {
      chaptersHtml = '<ul style="padding-left:1.25rem; font-size:0.9rem;">' + 
        book.chapters.map(ch => `<li><strong>Ch. ${ch.chapter_number}:</strong> ${escapeHtml(ch.title)}</li>`).join('') +
        '</ul>';
    }

    modalBody.innerHTML = `
      <div style="display:grid; grid-template-columns:130px 1fr; gap:1.25rem; margin-bottom:1.25rem;">
        <img src="/images/covers/${escapeHtml(book.cover_image || 'default_book.svg')}" style="width:100%; border-radius:6px; box-shadow:0 4px 10px rgba(0,0,0,0.1);">
        <div>
          <span class="badge badge-category">${escapeHtml(book.category_name)}</span>
          <p style="font-size:0.85rem; color:var(--text-muted); margin:0.35rem 0;">by <strong>${escapeHtml(book.author_name)}</strong> &bull; ${book.published_year}</p>
          <p style="font-size:0.95rem; line-height:1.5;">${escapeHtml(book.summary)}</p>
          <div style="margin-top:0.75rem; font-size:0.85rem;">
            <span>ISBN: <code>${escapeHtml(book.isbn)}</code></span> &bull; 
            <span>Rating: ⭐ ${book.rating}</span>
          </div>
        </div>
      </div>
      <h4 style="font-size:0.95rem; margin-bottom:0.5rem; font-weight:700;">Chapters Overview:</h4>
      ${chaptersHtml}
      <div style="display:flex; justify-content:flex-end; gap:0.5rem; margin-top:1.5rem; padding-top:1rem; border-top:1px solid var(--border);">
        <a href="/reader?book_id=${book.id}" class="btn btn-primary btn-sm">Open in Reader</a>
        <a href="/book?id=${book.id}" class="btn btn-outline btn-sm">Full Book Page</a>
      </div>
    `;
  } catch (err) {
    modalBody.innerHTML = `<div class="alert alert-danger">Error: ${err.message}</div>`;
  }
}

/* ==========================================================================
   6. Toast Notifications
   ========================================================================== */
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
  }, 4000);
}

function escapeHtml(str) {
  if (!str) return '';
  return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

