<?php
use Libris\Services\Auth;
ob_start();
?>

<!-- Breadcrumbs -->
<div class="breadcrumbs-container">
  <nav class="breadcrumbs" aria-label="Breadcrumb">
    <a href="/">Home</a>
    <span class="crumb-separator">/</span>
    <a href="/?category=<?= urlencode($book['category_slug']) ?>"><?= htmlspecialchars($book['category_name']) ?></a>
    <span class="crumb-separator">/</span>
    <span class="crumb-current" aria-current="page"><?= htmlspecialchars($book['title']) ?></span>
  </nav>
</div>

<!-- Book Details Container -->
<section class="book-details-section">
  <div class="section-container">
    <div class="book-details-layout">
      <!-- Left Column: Cover & Quick Actions -->
      <aside class="book-sidebar">
        <div class="book-cover-stage">
          <img src="/images/covers/<?= htmlspecialchars($book['cover_image'] ?? 'default_book.svg') ?>" alt="<?= htmlspecialchars($book['title']) ?> Cover" class="detail-cover-img">
          <?php if ($book['featured']): ?>
            <span class="badge badge-featured cover-badge">★ Featured Title</span>
          <?php endif; ?>
        </div>

        <div class="detail-actions">
          <?php if (!empty($chapters)): ?>
            <a href="/reader?book_id=<?= $book['id'] ?>&chapter=1" class="btn btn-primary btn-block btn-lg">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
              Start Reading Chapter 1
            </a>
          <?php else: ?>
            <button class="btn btn-secondary btn-block btn-lg" disabled>No Chapters Available</button>
          <?php endif; ?>
          <a href="/api/book?id=<?= $book['id'] ?>" target="_blank" class="btn btn-outline btn-block">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            View JSON Web Service
          </a>
        </div>

        <!-- Book Specifications Card -->
        <div class="specs-card">
          <h4 class="specs-heading">Publication Details</h4>
          <dl class="specs-list">
            <div class="spec-row">
              <dt>ISBN-13</dt>
              <dd><code><?= htmlspecialchars($book['isbn']) ?></code></dd>
            </div>
            <div class="spec-row">
              <dt>Category</dt>
              <dd><a href="/?category=<?= urlencode($book['category_slug']) ?>" class="badge badge-category"><?= htmlspecialchars($book['category_name']) ?></a></dd>
            </div>
            <div class="spec-row">
              <dt>Published</dt>
              <dd><?= $book['published_year'] ?></dd>
            </div>
            <div class="spec-row">
              <dt>Language</dt>
              <dd><?= htmlspecialchars($book['language'] ?? 'English') ?></dd>
            </div>
            <div class="spec-row">
              <dt>Price</dt>
              <dd><strong><?= (float)$book['price'] > 0 ? '$' . number_format($book['price'], 2) : 'Free Access' ?></strong></dd>
            </div>
            <div class="spec-row">
              <dt>Total Views</dt>
              <dd><?= $book['views_count'] ?> reads</dd>
            </div>
          </dl>
        </div>
      </aside>

      <!-- Right Column: Info, Chapters, Author & Reviews -->
      <article class="book-main-info">
        <header class="detail-header">
          <div class="detail-meta-pills">
            <span class="badge badge-category"><?= htmlspecialchars($book['category_name']) ?></span>
            <span class="rating-pill large">⭐ <?= number_format($book['rating'], 1) ?> (<?= count($reviews) ?> reviews)</span>
          </div>

          <h1 class="detail-title"><?= htmlspecialchars($book['title']) ?></h1>

          <div class="author-byline">
            <span class="author-avatar">✍️</span>
            <div>
              <span class="byline-prefix">Written by</span>
              <h3 class="author-name-lg"><?= htmlspecialchars($book['author_name']) ?></h3>
            </div>
          </div>
        </header>

        <!-- Synopsis / Summary -->
        <section class="synopsis-section">
          <h3 class="section-subheading">Synopsis</h3>
          <div class="synopsis-content">
            <p><?= nl2br(htmlspecialchars($book['summary'])) ?></p>
          </div>
        </section>

        <!-- Author Biography -->
        <?php if (!empty($book['author_bio'])): ?>
        <section class="author-bio-card">
          <div class="author-bio-header">
            <h4 class="bio-heading">About the Author</h4>
            <?php if (!empty($book['author_email'])): ?>
              <span class="author-email-link">📧 <?= htmlspecialchars($book['author_email']) ?></span>
            <?php endif; ?>
          </div>
          <p class="author-bio-text"><?= htmlspecialchars($book['author_bio']) ?></p>
        </section>
        <?php endif; ?>

        <!-- Table of Contents / Chapters -->
        <section class="chapters-directory-section" id="chapters">
          <div class="directory-header">
            <h3 class="section-subheading">Table of Contents</h3>
            <span class="chapter-count-pill"><?= count($chapters) ?> Chapter<?= count($chapters) === 1 ? '' : 's' ?> Available</span>
          </div>

          <?php if (empty($chapters)): ?>
            <div class="empty-chapters-notice">
              <p>No chapters have been published for this book yet.</p>
              <?php if (Auth::isAdmin()): ?>
                <a href="/admin/chapters?book_id=<?= $book['id'] ?>" class="btn btn-sm btn-primary">Add First Chapter</a>
              <?php endif; ?>
            </div>
          <?php else: ?>
            <div class="chapter-list">
              <?php foreach ($chapters as $ch): ?>
                <div class="chapter-item">
                  <div class="chapter-num-badge">Ch. <?= $ch['chapter_number'] ?></div>
                  <div class="chapter-info">
                    <h4 class="chapter-title"><?= htmlspecialchars($ch['title']) ?></h4>
                    <p class="chapter-excerpt"><?= htmlspecialchars(substr($ch['content'], 0, 110)) ?>...</p>
                  </div>
                  <div class="chapter-action">
                    <a href="/reader?book_id=<?= $book['id'] ?>&chapter=<?= $ch['chapter_number'] ?>" class="btn btn-sm btn-outline">
                      Read &rarr;
                    </a>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </section>

        <!-- Reader Community Reviews Section -->
        <section class="reviews-section" id="reviews">
          <div class="reviews-header">
            <div>
              <h3 class="section-subheading">Community Reviews &amp; Ratings</h3>
              <p class="reviews-sub">Insights from verified readers.</p>
            </div>
            <div class="overall-rating-card">
              <span class="overall-score"><?= number_format($book['rating'], 1) ?></span>
              <div class="stars-display">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                  <span class="star <?= $i <= round($book['rating']) ? 'filled' : '' ?>">★</span>
                <?php endfor; ?>
              </div>
              <span class="overall-count"><?= count($reviews) ?> total ratings</span>
            </div>
          </div>

          <!-- Add Review Form -->
          <div class="add-review-card">
            <h4 class="add-review-title">Leave a Review</h4>
            <form action="/review/submit" method="POST" id="review-form" class="review-form">
              <?= Auth::csrfField() ?>
              <input type="hidden" name="book_id" value="<?= $book['id'] ?>">

              <div class="form-row">
                <div class="form-group flex-1">
                  <label for="review-user-name" class="form-label">Your Name <span class="text-danger">*</span></label>
                  <input type="text" id="review-user-name" name="user_name" class="form-input" placeholder="e.g. Preet Ahir" required>
                </div>
                <div class="form-group flex-1">
                  <label for="review-user-email" class="form-label">Email (Optional)</label>
                  <input type="email" id="review-user-email" name="user_email" class="form-input" placeholder="e.g. preet@student.edu">
                </div>
                <div class="form-group">
                  <label for="review-rating" class="form-label">Your Rating <span class="text-danger">*</span></label>
                  <select id="review-rating" name="rating" class="form-select" required>
                    <option value="5">⭐⭐⭐⭐⭐ (5 - Masterpiece)</option>
                    <option value="4">⭐⭐⭐⭐ (4 - Very Good)</option>
                    <option value="3">⭐⭐⭐ (3 - Average)</option>
                    <option value="2">⭐⭐ (2 - Below Expectations)</option>
                    <option value="1">⭐ (1 - Poor)</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label for="review-comment" class="form-label">Your Review Comments <span class="text-danger">*</span></label>
                <textarea id="review-comment" name="comment" rows="4" class="form-textarea" placeholder="Share your thoughts on the narrative, chapter pacing, or technical insights..." required></textarea>
              </div>

              <button type="submit" class="btn btn-primary">Submit Reader Review</button>
            </form>
          </div>

          <!-- Existing Reviews Feed -->
          <div class="reviews-feed">
            <?php if (empty($reviews)): ?>
              <p class="no-reviews-txt">No reviews yet for this title. Be the first reader to share your feedback!</p>
            <?php else: ?>
              <?php foreach ($reviews as $rev): ?>
                <div class="review-item">
                  <div class="review-top">
                    <div class="reviewer-meta">
                      <span class="reviewer-avatar"><?= strtoupper(substr($rev['user_name'], 0, 1)) ?></span>
                      <div>
                        <strong class="reviewer-name"><?= htmlspecialchars($rev['user_name']) ?></strong>
                        <span class="review-date"><?= date('M d, Y', strtotime($rev['created_at'])) ?></span>
                      </div>
                    </div>
                    <div class="stars-row">
                      <?php for ($s = 1; $s <= 5; $s++): ?>
                        <span class="star <?= $s <= $rev['rating'] ? 'filled' : '' ?>">★</span>
                      <?php endfor; ?>
                    </div>
                  </div>
                  <p class="review-body"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </section>
      </article>
    </div>

    <!-- Related Books Row -->
    <?php if (!empty($relatedBooks)): ?>
      <section class="related-books-section">
        <h3 class="section-subheading">More in <?= htmlspecialchars($book['category_name']) ?></h3>
        <div class="related-grid">
          <?php foreach (array_slice($relatedBooks, 0, 4) as $rb): ?>
            <a href="/book?id=<?= $rb['id'] ?>" class="related-card">
              <img src="/images/covers/<?= htmlspecialchars($rb['cover_image'] ?? 'default_book.svg') ?>" alt="<?= htmlspecialchars($rb['title']) ?> Cover" class="related-cover">
              <div class="related-info">
                <h4 class="related-title"><?= htmlspecialchars($rb['title']) ?></h4>
                <span class="related-author"><?= htmlspecialchars($rb['author_name']) ?></span>
                <span class="rating-pill small">⭐ <?= number_format($rb['rating'], 1) ?></span>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>
  </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';

