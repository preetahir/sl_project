<?php
use Libris\Services\Auth;
ob_start();
?>
<section class="admin-form-section">
  <div class="section-container">
    <div class="form-header">
      <a href="/admin" class="back-link">&larr; Return to Dashboard</a>
      <h1 class="admin-title"><?= $book ? 'Edit Book Record' : 'Add New Book to Catalog' ?></h1>
      <p class="admin-subtitle">Fill in the metadata below. Inputs are sanitized and validated against standard schema rules.</p>
    </div>

    <div class="form-preview-grid">
      <!-- Left Column: Form -->
      <div class="form-card">
        <form action="/admin/book/save" method="POST" id="book-editor-form" class="editor-form">
          <?= Auth::csrfField() ?>
          <?php if ($book): ?>
            <input type="hidden" name="id" value="<?= $book['id'] ?>">
          <?php endif; ?>

          <div class="form-group">
            <label for="book-title" class="form-label">Book Title <span class="text-danger">*</span></label>
            <input 
              type="text" 
              id="book-title" 
              name="title" 
              class="form-input" 
              placeholder="e.g. Clean Code and Architecture Patterns" 
              value="<?= htmlspecialchars($book['title'] ?? '') ?>" 
              required
            >
          </div>

          <div class="form-row">
            <div class="form-group flex-1">
              <label for="book-isbn" class="form-label">ISBN-13 / ISBN-10 <span class="text-danger">*</span></label>
              <input 
                type="text" 
                id="book-isbn" 
                name="isbn" 
                class="form-input" 
                placeholder="e.g. 978-0-13-468599-1" 
                value="<?= htmlspecialchars($book['isbn'] ?? '') ?>" 
                pattern="(97[89]-?[0-9]{1,5}-?[0-9]{1,7}-?[0-9]{1,6}-?[0-9])|[0-9]{9}[0-9X]"
                title="Please enter a valid 10 or 13-digit ISBN format"
                required
              >
              <span class="field-hint">Format: 978-X-XX-XXXXXX-X</span>
            </div>

            <div class="form-group flex-1">
              <label for="book-category" class="form-label">Category / Genre <span class="text-danger">*</span></label>
              <select id="book-category" name="category_id" class="form-select" required>
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?= $cat['id'] ?>" data-name="<?= htmlspecialchars($cat['name']) ?>" <?= (isset($book['category_id']) && (int)$book['category_id'] === (int)$cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group flex-1">
              <label for="book-author" class="form-label">Author <span class="text-danger">*</span></label>
              <select id="book-author" name="author_id" class="form-select" required>
                <option value="">-- Select Author --</option>
                <?php foreach ($authors as $a): ?>
                  <option value="<?= $a['id'] ?>" data-name="<?= htmlspecialchars($a['name']) ?>" <?= (isset($book['author_id']) && (int)$book['author_id'] === (int)$a['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($a['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group flex-1">
              <label for="book-year" class="form-label">Publication Year <span class="text-danger">*</span></label>
              <input 
                type="number" 
                id="book-year" 
                name="published_year" 
                class="form-input" 
                min="1800" 
                max="2099" 
                value="<?= htmlspecialchars($book['published_year'] ?? date('Y')) ?>" 
                required
              >
            </div>
          </div>

          <div class="form-row">
            <div class="form-group flex-1">
              <label for="book-price" class="form-label">Price (USD)</label>
              <input 
                type="number" 
                step="0.01" 
                id="book-price" 
                name="price" 
                class="form-input" 
                placeholder="0.00 for free" 
                value="<?= htmlspecialchars($book['price'] ?? '0.00') ?>"
              >
            </div>

            <div class="form-group flex-1">
              <label for="book-rating" class="form-label">Rating (1.0 to 5.0)</label>
              <input 
                type="number" 
                step="0.1" 
                min="1.0" 
                max="5.0" 
                id="book-rating" 
                name="rating" 
                class="form-input" 
                value="<?= htmlspecialchars($book['rating'] ?? '5.0') ?>"
              >
            </div>

            <div class="form-group flex-1">
              <label for="book-cover" class="form-label">Cover SVG File</label>
              <select id="book-cover" name="cover_image" class="form-select">
                <option value="web_arch.svg" <?= (isset($book['cover_image']) && $book['cover_image'] === 'web_arch.svg') ? 'selected' : '' ?>>web_arch.svg (Indigo)</option>
                <option value="algo_think.svg" <?= (isset($book['cover_image']) && $book['cover_image'] === 'algo_think.svg') ? 'selected' : '' ?>>algo_think.svg (Emerald)</option>
                <option value="laravel_playbook.svg" <?= (isset($book['cover_image']) && $book['cover_image'] === 'laravel_playbook.svg') ? 'selected' : '' ?>>laravel_playbook.svg (Crimson)</option>
                <option value="starward.svg" <?= (isset($book['cover_image']) && $book['cover_image'] === 'starward.svg') ? 'selected' : '' ?>>starward.svg (Purple)</option>
                <option value="typography.svg" <?= (isset($book['cover_image']) && $book['cover_image'] === 'typography.svg') ? 'selected' : '' ?>>typography.svg (Amber)</option>
                <option value="cloud_microservices.svg" <?= (isset($book['cover_image']) && $book['cover_image'] === 'cloud_microservices.svg') ? 'selected' : '' ?>>cloud_microservices.svg (Sky)</option>
                <option value="default_book.svg" <?= (isset($book['cover_image']) && $book['cover_image'] === 'default_book.svg') ? 'selected' : '' ?>>default_book.svg (Slate)</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="book-summary" class="form-label">Book Summary &amp; Synopsis <span class="text-danger">*</span></label>
            <textarea 
              id="book-summary" 
              name="summary" 
              rows="5" 
              class="form-textarea" 
              placeholder="Provide a thorough, comprehensive overview of this book's content, intended audience, and chapters..." 
              required
            ><?= htmlspecialchars($book['summary'] ?? '') ?></textarea>
          </div>

          <div class="form-checkbox-row">
            <label class="custom-checkbox-label">
              <input type="checkbox" name="featured" value="1" <?= (!empty($book['featured'])) ? 'checked' : '' ?>>
              <span class="checkbox-txt">Pin this title as a <strong>Featured Book</strong> on the homepage</span>
            </label>
          </div>

          <div class="form-actions-bar">
            <button type="submit" class="btn btn-primary btn-lg">
              <?= $book ? 'Save Book Changes' : 'Create &amp; Publish Book' ?>
            </button>
            <a href="/admin" class="btn btn-outline">Cancel</a>
          </div>
        </form>
      </div>

      <!-- Right Column: Live Card Preview -->
      <aside class="preview-sidebar">
        <h4 class="preview-title">Live Storefront Card Preview</h4>
        <div class="preview-card-wrap">
          <article class="book-card preview-card">
            <div class="book-cover-container">
              <img id="preview-img" src="/images/covers/<?= htmlspecialchars($book['cover_image'] ?? 'web_arch.svg') ?>" alt="Preview Cover" class="book-cover-img">
              <span id="preview-ribbon" class="badge badge-featured book-ribbon" style="<?= empty($book['featured']) ? 'display:none;' : '' ?>">Featured</span>
            </div>
            <div class="book-info">
              <div class="book-meta-top">
                <span id="preview-cat" class="badge badge-category"><?= htmlspecialchars($book['category_name'] ?? 'Web Development') ?></span>
                <span id="preview-rating" class="rating-pill">⭐ <?= number_format($book['rating'] ?? 5.0, 1) ?></span>
              </div>
              <h3 id="preview-title" class="book-title"><?= htmlspecialchars($book['title'] ?? 'Book Title Preview') ?></h3>
              <div class="book-author-row">
                <span class="author-icon">✍️</span>
                <span id="preview-author" class="author-name"><?= htmlspecialchars($book['author_name'] ?? 'Author Name') ?></span>
                <span class="meta-dot">&bull;</span>
                <span id="preview-year" class="book-year"><?= $book['published_year'] ?? date('Y') ?></span>
              </div>
              <p id="preview-summary" class="book-summary"><?= htmlspecialchars(substr($book['summary'] ?? 'Provide a synopsis in the form on the left to see live rendering here...', 0, 120)) ?></p>
              <div class="book-card-footer">
                <span id="preview-price" class="price-val"><?= (isset($book['price']) && (float)$book['price'] > 0) ? '$' . number_format($book['price'], 2) : 'Free Reading' ?></span>
                <button type="button" class="btn btn-sm btn-primary">Read Now</button>
              </div>
            </div>
          </article>
        </div>
      </aside>
    </div>
  </div>
</section>

<script>
// Live Preview Synchronizer
document.addEventListener('DOMContentLoaded', () => {
  const titleInput = document.getElementById('book-title');
  const summaryInput = document.getElementById('book-summary');
  const catSelect = document.getElementById('book-category');
  const authorSelect = document.getElementById('book-author');
  const yearInput = document.getElementById('book-year');
  const priceInput = document.getElementById('book-price');
  const ratingInput = document.getElementById('book-rating');
  const coverSelect = document.getElementById('book-cover');
  const featuredBox = document.querySelector('input[name="featured"]');

  const pTitle = document.getElementById('preview-title');
  const pSummary = document.getElementById('preview-summary');
  const pCat = document.getElementById('preview-cat');
  const pAuthor = document.getElementById('preview-author');
  const pYear = document.getElementById('preview-year');
  const pPrice = document.getElementById('preview-price');
  const pRating = document.getElementById('preview-rating');
  const pImg = document.getElementById('preview-img');
  const pRibbon = document.getElementById('preview-ribbon');

  titleInput.addEventListener('input', () => { pTitle.textContent = titleInput.value || 'Book Title Preview'; });
  summaryInput.addEventListener('input', () => { pSummary.textContent = summaryInput.value.slice(0, 120) || 'Book synopsis snippet...'; });
  yearInput.addEventListener('input', () => { pYear.textContent = yearInput.value || '2026'; });
  ratingInput.addEventListener('input', () => { pRating.textContent = '⭐ ' + (parseFloat(ratingInput.value) || 5.0).toFixed(1); });
  priceInput.addEventListener('input', () => {
    const val = parseFloat(priceInput.value);
    pPrice.textContent = (val > 0) ? '$' + val.toFixed(2) : 'Free Reading';
  });

  catSelect.addEventListener('change', () => {
    const opt = catSelect.options[catSelect.selectedIndex];
    if (opt && opt.value) pCat.textContent = opt.getAttribute('data-name');
  });

  authorSelect.addEventListener('change', () => {
    const opt = authorSelect.options[authorSelect.selectedIndex];
    if (opt && opt.value) pAuthor.textContent = opt.getAttribute('data-name');
  });

  coverSelect.addEventListener('change', () => {
    pImg.src = '/images/covers/' + coverSelect.value;
  });

  featuredBox.addEventListener('change', () => {
    pRibbon.style.display = featuredBox.checked ? '' : 'none';
  });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';

