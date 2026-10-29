<?php
ob_start();
?>
<!-- Hero Section -->
<section class="hero-section">
  <div class="hero-container">
    <div class="hero-content">
      <div class="hero-badge">
        <span class="badge-dot"></span>
        <span>LibrisCMS 2.5 • Unified Book Management &amp; Reader</span>
      </div>
      <h1 class="hero-title">
        Discover, Manage &amp; Read <span class="gradient-text">World-Class Literature</span>
      </h1>
      <p class="hero-subtitle">
        A high-performance responsive Content Management System powered by semantic HTML5, fluid CSS3, asynchronous ES6 JavaScript, robust PHP 8.1 MVC architecture, and enterprise XML data interchange.
      </p>

      <!-- Search & Filter Bar -->
      <form action="/" method="GET" class="hero-search-form" id="hero-search-form">
        <div class="search-input-wrapper">
          <svg class="search-icon" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          <input 
            type="search" 
            name="search" 
            id="live-search-input" 
            class="search-input" 
            placeholder="Search books by title, author, ISBN, or topic..." 
            value="<?= htmlspecialchars($searchTerm ?? '') ?>"
            autocomplete="off"
            aria-label="Search books"
          >
          <?php if (!empty($searchTerm)): ?>
            <a href="/" class="search-clear-btn" aria-label="Clear search">&times;</a>
          <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary search-submit-btn">Search Books</button>
      </form>

      <!-- Quick Action Buttons -->
      <div class="hero-actions">
        <a href="#catalog-section" class="btn btn-secondary">Browse All Books</a>
        <a href="/xml/studio" class="btn btn-outline-light">XML Studio &amp; XSD Validator</a>
        <a href="/admin" class="btn btn-outline-light">Admin CMS</a>
      </div>
    </div>

    <!-- Hero Stats Floating Card -->
    <div class="hero-metrics-card">
      <div class="metric-row">
        <div class="metric-item">
          <span class="metric-val" id="metric-books"><?= $stats['total_books'] ?></span>
          <span class="metric-lbl">Total Books</span>
        </div>
        <div class="metric-divider"></div>
        <div class="metric-item">
          <span class="metric-val" id="metric-chapters"><?= $stats['total_chapters'] ?></span>
          <span class="metric-lbl">Chapters Published</span>
        </div>
      </div>
      <div class="metric-row">
        <div class="metric-item">
          <span class="metric-val" id="metric-categories"><?= $stats['total_categories'] ?></span>
          <span class="metric-lbl">Curated Genres</span>
        </div>
        <div class="metric-divider"></div>
        <div class="metric-item">
          <span class="metric-val text-amber">⭐ <?= $stats['average_rating'] ?></span>
          <span class="metric-lbl">Average Rating</span>
        </div>
      </div>
      <div class="metric-footer">
        <span class="xml-badge">XML 1.0 &bull; XSD Validated</span>
        <span class="rss-link-pill"><a href="/feed.xml" target="_blank">📡 RSS Feed</a></span>
      </div>
    </div>
  </div>
</section>

<!-- Featured Masterpieces Banner -->
<?php if (!empty($featuredBooks) && empty($searchTerm) && empty($categorySlug)): ?>
<section class="featured-section">
  <div class="section-container">
    <div class="section-header">
      <div>
        <span class="section-badge">Editor's Choice</span>
        <h2 class="section-title">Featured Books of the Month</h2>
      </div>
      <a href="/?featured=1" class="section-link">View all featured &rarr;</a>
    </div>

    <div class="featured-grid">
      <?php foreach (array_slice($featuredBooks, 0, 3) as $fb): ?>
        <article class="featured-card">
          <div class="featured-cover-wrap">
            <img src="/images/covers/<?= htmlspecialchars($fb['cover_image'] ?? 'default_book.svg') ?>" alt="<?= htmlspecialchars($fb['title']) ?> Cover" class="featured-cover" loading="lazy">
          </div>
          <div class="featured-details">
            <div class="card-meta">
              <span class="badge badge-category"><?= htmlspecialchars($fb['category_name']) ?></span>
              <span class="rating-pill">⭐ <?= number_format($fb['rating'], 1) ?></span>
            </div>
            <h3 class="featured-title">
              <a href="/book?id=<?= $fb['id'] ?>"><?= htmlspecialchars($fb['title']) ?></a>
            </h3>
            <p class="featured-author">by <strong><?= htmlspecialchars($fb['author_name']) ?></strong> (<?= $fb['published_year'] ?>)</p>
            <p class="featured-summary"><?= htmlspecialchars(substr($fb['summary'], 0, 140)) ?>...</p>
            <div class="featured-actions">
              <a href="/reader?book_id=<?= $fb['id'] ?>" class="btn btn-sm btn-primary">Start Reading</a>
              <a href="/book?id=<?= $fb['id'] ?>" class="btn btn-sm btn-outline">Book Details</a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Main Catalog Section -->
<section class="catalog-section" id="catalog-section">
  <div class="section-container">
    <div class="section-header">
      <div>
        <span class="section-badge">Complete Library</span>
        <h2 class="section-title">Explore Catalog</h2>
        <p class="section-subtitle">Browse books by category, rating, publication year, or keyword.</p>
      </div>

      <!-- View Switcher (Grid vs List) -->
      <div class="view-switcher" role="group" aria-label="Catalog View Mode">
        <button id="view-grid-btn" class="btn-icon view-btn active" title="Grid View" aria-label="Grid view">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
            <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
          </svg>
        </button>
        <button id="view-list-btn" class="btn-icon view-btn" title="List View" aria-label="List view">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/>
            <line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Category Filter Pills -->
    <nav class="category-filter-nav" aria-label="Filter books by genre">
      <a href="/" class="cat-pill <?= empty($categorySlug) ? 'active' : '' ?>">
        All Categories <span class="cat-count"><?= $stats['total_books'] ?></span>
      </a>
      <?php foreach ($categories as $cat): ?>
        <a href="/?category=<?= urlencode($cat['slug']) ?>" class="cat-pill <?= ($categorySlug === $cat['slug']) ? 'active' : '' ?>">
          <?= htmlspecialchars($cat['name']) ?>
          <span class="cat-count"><?= $cat['books_count'] ?></span>
        </a>
      <?php endforeach; ?>
    </nav>

    <!-- Filter & Sort Toolbar -->
    <div class="filter-toolbar">
      <div class="filter-group">
        <label for="sort-select" class="filter-label">Sort by:</label>
        <select id="sort-select" class="form-select" onchange="window.location.href=this.value;">
          <option value="/?<?= http_build_query(array_merge($_GET, ['sort' => 'newest'])) ?>" <?= ($sort === 'newest') ? 'selected' : '' ?>>Newest Additions</option>
          <option value="/?<?= http_build_query(array_merge($_GET, ['sort' => 'rating'])) ?>" <?= ($sort === 'rating') ? 'selected' : '' ?>>Highest Rated</option>
          <option value="/?<?= http_build_query(array_merge($_GET, ['sort' => 'title_asc'])) ?>" <?= ($sort === 'title_asc') ? 'selected' : '' ?>>Title (A-Z)</option>
          <option value="/?<?= http_build_query(array_merge($_GET, ['sort' => 'price_asc'])) ?>" <?= ($sort === 'price_asc') ? 'selected' : '' ?>>Price: Low to High</option>
          <option value="/?<?= http_build_query(array_merge($_GET, ['sort' => 'price_desc'])) ?>" <?= ($sort === 'price_desc') ? 'selected' : '' ?>>Price: High to Low</option>
          <option value="/?<?= http_build_query(array_merge($_GET, ['sort' => 'year'])) ?>" <?= ($sort === 'year') ? 'selected' : '' ?>>Publication Year</option>
        </select>
      </div>

      <div class="filter-group">
        <label for="rating-select" class="filter-label">Rating Filter:</label>
        <select id="rating-select" class="form-select" onchange="window.location.href=this.value;">
          <option value="/?<?= http_build_query(array_merge($_GET, ['min_rating' => ''])) ?>" <?= empty($minRating) ? 'selected' : '' ?>>All Ratings</option>
          <option value="/?<?= http_build_query(array_merge($_GET, ['min_rating' => '4.8'])) ?>" <?= ($minRating === '4.8') ? 'selected' : '' ?>>4.8+ Stars</option>
          <option value="/?<?= http_build_query(array_merge($_GET, ['min_rating' => '4.5'])) ?>" <?= ($minRating === '4.5') ? 'selected' : '' ?>>4.5+ Stars</option>
          <option value="/?<?= http_build_query(array_merge($_GET, ['min_rating' => '4.0'])) ?>" <?= ($minRating === '4.0') ? 'selected' : '' ?>>4.0+ Stars</option>
        </select>
      </div>

      <div class="filter-results-count">
        Showing <strong><?= count($books) ?></strong> book<?= count($books) === 1 ? '' : 's' ?>
      </div>
    </div>

    <!-- Books Presentation Grid -->
    <?php if (empty($books)): ?>
      <div class="empty-state">
        <div class="empty-icon">
          <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            <line x1="8" y1="11" x2="14" y2="11"/>
          </svg>
        </div>
        <h3 class="empty-title">No books found</h3>
        <p class="empty-desc">We couldn't find any books matching your selected filters or search query.</p>
        <a href="/" class="btn btn-primary">Reset Filters</a>
      </div>
    <?php else: ?>
      <div class="catalog-grid" id="catalog-grid">
        <?php foreach ($books as $book): ?>
          <article class="book-card" data-id="<?= $book['id'] ?>" data-title="<?= htmlspecialchars(strtolower($book['title'])) ?>" data-author="<?= htmlspecialchars(strtolower($book['author_name'])) ?>" data-category="<?= htmlspecialchars($book['category_slug']) ?>">
            <div class="book-cover-container">
              <a href="/book?id=<?= $book['id'] ?>" class="cover-link">
                <img src="/images/covers/<?= htmlspecialchars($book['cover_image'] ?? 'default_book.svg') ?>" alt="<?= htmlspecialchars($book['title']) ?> Cover" class="book-cover-img" loading="lazy">
              </a>
              <?php if ($book['featured']): ?>
                <span class="badge badge-featured book-ribbon">Featured</span>
              <?php endif; ?>
              <button class="quick-view-btn" onclick="openQuickPreview(<?= $book['id'] ?>)" aria-label="Quick preview <?= htmlspecialchars($book['title']) ?>">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                Quick View
              </button>
            </div>

            <div class="book-info">
              <div class="book-meta-top">
                <a href="/?category=<?= urlencode($book['category_slug']) ?>" class="badge badge-category">
                  <?= htmlspecialchars($book['category_name']) ?>
                </a>
                <span class="rating-pill">⭐ <?= number_format($book['rating'], 1) ?></span>
              </div>

              <h3 class="book-title">
                <a href="/book?id=<?= $book['id'] ?>"><?= htmlspecialchars($book['title']) ?></a>
              </h3>

              <div class="book-author-row">
                <span class="author-icon">✍️</span>
                <span class="author-name"><?= htmlspecialchars($book['author_name']) ?></span>
                <span class="meta-dot">&bull;</span>
                <span class="book-year"><?= $book['published_year'] ?></span>
              </div>

              <p class="book-summary">
                <?= htmlspecialchars(substr($book['summary'], 0, 120)) ?>...
              </p>

              <div class="book-specs-row">
                <span class="isbn-tag" title="Standard Book Number">ISBN: <?= htmlspecialchars($book['isbn']) ?></span>
                <span class="chapter-pill"><?= $book['chapters_count'] ?> chapters</span>
              </div>

              <div class="book-card-footer">
                <div class="book-price">
                  <?php if ((float)$book['price'] > 0): ?>
                    <span class="price-val">$<?= number_format($book['price'], 2) ?></span>
                  <?php else: ?>
                    <span class="price-free">Free Reading</span>
                  <?php endif; ?>
                </div>

                <div class="card-btn-group">
                  <a href="/reader?book_id=<?= $book['id'] ?>" class="btn btn-sm btn-primary" title="Open Chapter Reader">
                    Read Now
                  </a>
                  <a href="/book?id=<?= $book['id'] ?>" class="btn btn-sm btn-outline" title="View Full Details">
                    Details
                  </a>
                </div>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- XML Interoperability Teaser Section -->
<section class="xml-banner-section">
  <div class="section-container xml-banner-grid">
    <div class="xml-banner-content">
      <span class="section-badge">Data Standards &amp; Interoperability</span>
      <h2 class="xml-banner-title">Complete XML &amp; XSD Validation Engine</h2>
      <p class="xml-banner-desc">
        Seamlessly bridge relational data and structured markup. LibrisCMS supports bidirectional catalog exports, XSD schema validation, XSLT document transformation, and syndicated RSS 2.0 feeds.
      </p>
      <div class="xml-btn-group">
        <a href="/xml/studio" class="btn btn-primary">Open XML Studio</a>
        <a href="/xml/export" class="btn btn-outline">Export Catalog (XML)</a>
        <a href="/xml/schema.xsd" target="_blank" class="btn btn-outline">View XSD Schema</a>
      </div>
    </div>
    <div class="xml-snippet-preview">
      <div class="code-window">
        <div class="code-window-header">
          <span class="window-dot red"></span>
          <span class="window-dot yellow"></span>
          <span class="window-dot green"></span>
          <span class="code-window-title">schema.xsd (Validated)</span>
        </div>
        <pre class="code-block"><code>&lt;xs:element name="bookCatalog"&gt;
  &lt;xs:complexType&gt;
    &lt;xs:sequence&gt;
      &lt;xs:element name="book" maxOccurs="unbounded"&gt;
        &lt;xs:complexType&gt;
          &lt;xs:sequence&gt;
            &lt;xs:element name="title" type="xs:string"/&gt;
            &lt;xs:element name="isbn"&gt;
              &lt;xs:simpleType&gt;
                &lt;xs:restriction base="xs:string"&gt;
                  &lt;xs:pattern value="(97[89]...)"/&gt;
                &lt;/xs:restriction&gt;
              &lt;/xs:simpleType&gt;
            &lt;/xs:element&gt;
            &lt;xs:element name="publishedYear"&gt;
              &lt;xs:restriction base="xs:integer"&gt;
                &lt;xs:minInclusive value="1800"/&gt;
                &lt;xs:maxInclusive value="2099"/&gt;
              &lt;/xs:restriction&gt;
            &lt;/xs:element&gt;
          &lt;/xs:sequence&gt;
        &lt;/xs:complexType&gt;
      &lt;/xs:element&gt;
    &lt;/xs:sequence&gt;
  &lt;/xs:complexType&gt;
&lt;/xs:element&gt;</code></pre>
      </div>
    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';

