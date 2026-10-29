<?php
use Libris\Services\Auth;
ob_start();
?>
<section class="admin-chapters-section">
  <div class="section-container">
    <div class="form-header">
      <a href="/admin" class="back-link">&larr; Return to Dashboard</a>
      <h1 class="admin-title">Manage Chapters</h1>
      <p class="admin-subtitle">Organize and publish chapters for <strong><?= htmlspecialchars($book['title']) ?></strong></p>
    </div>

    <!-- Book Context Card -->
    <div class="book-context-card">
      <img src="/images/covers/<?= htmlspecialchars($book['cover_image'] ?? 'default_book.svg') ?>" alt="" class="context-cover">
      <div class="context-details">
        <h3 class="context-title"><?= htmlspecialchars($book['title']) ?></h3>
        <p class="context-author">by <?= htmlspecialchars($book['author_name']) ?> &bull; <?= htmlspecialchars($book['category_name']) ?> &bull; <?= count($chapters) ?> Chapter<?= count($chapters) === 1 ? '' : 's' ?> Published</p>
        <div class="context-links">
          <a href="/book?id=<?= $book['id'] ?>" target="_blank" class="btn btn-xs btn-outline">View Storefront</a>
          <a href="/reader?book_id=<?= $book['id'] ?>&chapter=1" target="_blank" class="btn btn-xs btn-primary">Open in Reader</a>
        </div>
      </div>
    </div>

    <div class="chapters-admin-grid">
      <!-- Existing Chapters List -->
      <div class="chapters-list-card">
        <h3 class="card-heading">Current Chapters</h3>
        <?php if (empty($chapters)): ?>
          <div class="empty-state-sm">
            <p>No chapters added yet for this book. Use the form on the right to add Chapter 1.</p>
          </div>
        <?php else: ?>
          <div class="admin-chapters-list">
            <?php foreach ($chapters as $ch): ?>
              <div class="admin-chapter-item">
                <div class="ch-left">
                  <span class="ch-badge">Ch. <?= $ch['chapter_number'] ?></span>
                  <div class="ch-title-wrap">
                    <strong class="ch-name"><?= htmlspecialchars($ch['title']) ?></strong>
                    <span class="ch-meta"><?= str_word_count(strip_tags($ch['content'])) ?> words &bull; Added <?= date('M d, Y', strtotime($ch['created_at'])) ?></span>
                  </div>
                </div>
                <div class="ch-right">
                  <a href="/reader?book_id=<?= $book['id'] ?>&chapter=<?= $ch['chapter_number'] ?>" target="_blank" class="btn btn-xs btn-outline">Preview</a>
                  <form action="/admin/chapter/delete" method="POST" style="display:inline;" onsubmit="return confirm('Delete Chapter <?= $ch['chapter_number'] ?>?');">
                    <?= Auth::csrfField() ?>
                    <input type="hidden" name="id" value="<?= $ch['id'] ?>">
                    <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                    <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                  </form>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- Add New Chapter Form -->
      <div class="add-chapter-card">
        <h3 class="card-heading">Add New Chapter</h3>
        <form action="/admin/chapter/save" method="POST" class="chapter-form">
          <?= Auth::csrfField() ?>
          <input type="hidden" name="book_id" value="<?= $book['id'] ?>">

          <div class="form-group">
            <label for="ch-number" class="form-label">Chapter Number <span class="text-danger">*</span></label>
            <input 
              type="number" 
              id="ch-number" 
              name="chapter_number" 
              class="form-input" 
              min="1" 
              value="<?= count($chapters) + 1 ?>" 
              required
            >
          </div>

          <div class="form-group">
            <label for="ch-title" class="form-label">Chapter Title <span class="text-danger">*</span></label>
            <input 
              type="text" 
              id="ch-title" 
              name="title" 
              class="form-input" 
              placeholder="e.g. Asymptotic Complexity and Memory Hierarchies" 
              required
            >
          </div>

          <div class="form-group">
            <label for="ch-content" class="form-label">Chapter Content / Body <span class="text-danger">*</span></label>
            <textarea 
              id="ch-content" 
              name="content" 
              rows="10" 
              class="form-textarea" 
              placeholder="Enter formatted chapter text, paragraphs, and literary or technical material..." 
              required
            ></textarea>
          </div>

          <button type="submit" class="btn btn-primary btn-block">Publish Chapter</button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';

