<?php
use Libris\Services\Auth;
ob_start();
?>
<section class="admin-dashboard-section">
  <div class="section-container">
    <!-- Admin Top Header -->
    <div class="admin-header-row">
      <div>
        <span class="badge badge-admin">Editorial Control Panel</span>
        <h1 class="admin-title">Book Content Management Dashboard</h1>
        <p class="admin-subtitle">Create, organize, update, and validate your publication catalog.</p>
      </div>
      <div class="admin-actions">
        <a href="/admin/book/new" class="btn btn-primary">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Add New Book
        </a>
        <a href="/xml/studio" class="btn btn-outline">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
          XML Studio
        </a>
        <a href="/xml/export" class="btn btn-outline">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Export XML
        </a>
      </div>
    </div>

    <!-- Metrics Cards Grid -->
    <div class="admin-stats-grid">
      <div class="stat-card">
        <div class="stat-icon bg-indigo">
          <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        </div>
        <div class="stat-info">
          <span class="stat-number"><?= $stats['total_books'] ?></span>
          <span class="stat-label">Total Books</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon bg-cyan">
          <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </div>
        <div class="stat-info">
          <span class="stat-number"><?= $stats['total_chapters'] ?></span>
          <span class="stat-label">Active Chapters</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon bg-emerald">
          <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        </div>
        <div class="stat-info">
          <span class="stat-number"><?= $stats['total_categories'] ?></span>
          <span class="stat-label">Categories</span>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon bg-amber">
          <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        </div>
        <div class="stat-info">
          <span class="stat-number">⭐ <?= $stats['average_rating'] ?></span>
          <span class="stat-label">Catalog Rating</span>
        </div>
      </div>
    </div>

    <!-- Management Data Table Card -->
    <div class="admin-table-card">
      <div class="table-card-header">
        <div>
          <h3 class="table-card-title">Catalog Inventory</h3>
          <p class="table-card-sub">Manage titles, chapter hierarchies, and editorial metadata.</p>
        </div>
        <div class="table-search-box">
          <input type="text" id="admin-table-search" class="form-input form-input-sm" placeholder="Filter inventory..." onkeyup="filterAdminTable(this.value)">
        </div>
      </div>

      <div class="table-responsive">
        <table class="data-table" id="books-inventory-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Cover &amp; Title</th>
              <th>Category</th>
              <th>Author</th>
              <th>ISBN</th>
              <th>Year</th>
              <th>Price</th>
              <th>Rating</th>
              <th>Chapters</th>
              <th class="text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($books as $b): ?>
              <tr>
                <td><code>#<?= $b['id'] ?></code></td>
                <td>
                  <div class="table-title-cell">
                    <img src="/images/covers/<?= htmlspecialchars($b['cover_image'] ?? 'default_book.svg') ?>" alt="" class="table-mini-cover">
                    <div>
                      <a href="/book?id=<?= $b['id'] ?>" class="table-book-link" target="_blank">
                        <strong><?= htmlspecialchars($b['title']) ?></strong>
                      </a>
                      <?php if ($b['featured']): ?>
                        <span class="badge badge-featured">★ Featured</span>
                      <?php endif; ?>
                    </div>
                  </div>
                </td>
                <td><span class="badge badge-category"><?= htmlspecialchars($b['category_name']) ?></span></td>
                <td><?= htmlspecialchars($b['author_name']) ?></td>
                <td><code><?= htmlspecialchars($b['isbn']) ?></code></td>
                <td><?= $b['published_year'] ?></td>
                <td><?= (float)$b['price'] > 0 ? '$' . number_format($b['price'], 2) : 'Free' ?></td>
                <td><span class="rating-pill">⭐ <?= number_format($b['rating'], 1) ?></span></td>
                <td>
                  <a href="/admin/chapters?book_id=<?= $b['id'] ?>" class="badge badge-info" title="Manage Chapters">
                    <?= $b['chapters_count'] ?> chapters &rarr;
                  </a>
                </td>
                <td class="text-right table-actions">
                  <div class="btn-group">
                    <a href="/admin/book/edit?id=<?= $b['id'] ?>" class="btn btn-xs btn-outline" title="Edit book details">
                      Edit
                    </a>
                    <a href="/admin/chapters?book_id=<?= $b['id'] ?>" class="btn btn-xs btn-secondary" title="Edit book chapters">
                      Chapters
                    </a>
                    <form action="/admin/book/delete" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to permanently delete this book and all its chapters?');">
                      <?= Auth::csrfField() ?>
                      <input type="hidden" name="id" value="<?= $b['id'] ?>">
                      <button type="submit" class="btn btn-xs btn-danger" title="Delete book">
                        Delete
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<script>
function filterAdminTable(query) {
  const q = query.toLowerCase();
  const rows = document.querySelectorAll('#books-inventory-table tbody tr');
  rows.forEach(row => {
    const text = row.innerText.toLowerCase();
    row.style.display = text.includes(q) ? '' : 'none';
  });
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';

