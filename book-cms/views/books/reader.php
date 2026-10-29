<?php
$wordCount = str_word_count(strip_tags($currentChapter['content'] ?? ''));
$readMinutes = max(1, ceil($wordCount / 200));
?>
<!DOCTYPE html>
<html lang="en" data-reader-theme="sepia" data-reader-font="serif" data-reader-size="medium">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($currentChapter['title'] ?? 'Chapter') ?> - <?= htmlspecialchars($book['title']) ?> | LibrisCMS Reader</title>
  
  <link rel="icon" type="image/svg+xml" href="/images/covers/<?= htmlspecialchars($book['cover_image'] ?? 'default_book.svg') ?>">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/reader.css">
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Merriweather:ital,wght@0,300;0,400;0,700;1,300&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
</head>
<body class="reader-body">
  <!-- Top Reading Progress Bar -->
  <div class="reading-progress-track">
    <div id="reading-progress-bar" class="reading-progress-fill"></div>
  </div>

  <!-- Sticky Reader Toolbar -->
  <header class="reader-toolbar">
    <div class="reader-toolbar-left">
      <a href="/book?id=<?= $book['id'] ?>" class="btn-icon" title="Return to Book Details" aria-label="Exit Reader">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
      </a>
      <div class="reader-book-identity">
        <span class="reader-book-title"><?= htmlspecialchars($book['title']) ?></span>
        <span class="reader-chapter-title">Ch. <?= $currentChapter['chapter_number'] ?>: <?= htmlspecialchars($currentChapter['title']) ?></span>
      </div>
    </div>

    <!-- Chapter Quick Dropdown -->
    <div class="reader-toolbar-center">
      <select id="reader-chapter-select" class="reader-chapter-select" onchange="window.location.href='/reader?book_id=<?= $book['id'] ?>&chapter='+this.value">
        <?php foreach ($allChapters as $ch): ?>
          <option value="<?= $ch['chapter_number'] ?>" <?= (int)$ch['chapter_number'] === (int)$currentChapter['chapter_number'] ? 'selected' : '' ?>>
            Chapter <?= $ch['chapter_number'] ?>: <?= htmlspecialchars($ch['title']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Reader Controls: Font Size, Theme, Font Family, Fullscreen -->
    <div class="reader-toolbar-right">
      <!-- Font Family Switcher -->
      <div class="reader-control-group" title="Font Style">
        <button class="font-toggle-btn active" data-font="serif" title="Serif (Merriweather)">Serif</button>
        <button class="font-toggle-btn" data-font="sans" title="Sans-serif (Modern)">Sans</button>
        <button class="font-toggle-btn" data-font="mono" title="Monospace (Code)">Mono</button>
      </div>

      <!-- Font Size Controls -->
      <div class="reader-control-group" title="Text Size">
        <button id="font-dec-btn" class="size-btn" aria-label="Decrease text size">A-</button>
        <span id="font-size-label" class="size-label">100%</span>
        <button id="font-inc-btn" class="size-btn" aria-label="Increase text size">A+</button>
      </div>

      <!-- Reader Theme Palette -->
      <div class="reader-theme-swatches" title="Reading Atmosphere">
        <button class="theme-swatch swatch-light" data-theme="light" title="Day (White)"></button>
        <button class="theme-swatch swatch-sepia active" data-theme="sepia" title="Paper (Sepia)"></button>
        <button class="theme-swatch swatch-dark" data-theme="dark" title="Night (Dark)"></button>
      </div>

      <!-- Bookmark Button -->
      <button id="bookmark-btn" class="btn-icon" title="Save Bookmark" aria-label="Save Bookmark">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
        </svg>
      </button>

      <!-- Fullscreen Toggle -->
      <button id="fullscreen-btn" class="btn-icon" title="Toggle Fullscreen" aria-label="Toggle Fullscreen">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
        </svg>
      </button>
    </div>
  </header>

  <!-- Reading Article Canvas -->
  <main class="reader-canvas">
    <article class="reader-article">
      <header class="reader-article-header">
        <span class="chapter-number-pill">Chapter <?= $currentChapter['chapter_number'] ?></span>
        <h1 class="chapter-headline"><?= htmlspecialchars($currentChapter['title']) ?></h1>
        <div class="chapter-stats-row">
          <span>📖 <?= $wordCount ?> words</span>
          <span>&bull;</span>
          <span>⏱️ Approx. <?= $readMinutes ?> min read</span>
          <span>&bull;</span>
          <span>Author: <?= htmlspecialchars($book['author_name']) ?></span>
        </div>
      </header>

      <div class="reader-content" id="reader-content">
        <?= nl2br(htmlspecialchars($currentChapter['content'])) ?>
      </div>

      <!-- Chapter Navigation Bottom Footer -->
      <nav class="chapter-nav-footer" aria-label="Chapter Pagination">
        <div class="nav-prev">
          <?php if (!empty($nav['prev'])): ?>
            <a href="/reader?book_id=<?= $book['id'] ?>&chapter=<?= $nav['prev']['chapter_number'] ?>" class="btn btn-outline">
              &larr; Chapter <?= $nav['prev']['chapter_number'] ?>: <?= htmlspecialchars(substr($nav['prev']['title'], 0, 22)) ?>...
            </a>
          <?php else: ?>
            <span class="nav-disabled">&larr; First Chapter</span>
          <?php endif; ?>
        </div>

        <a href="/book?id=<?= $book['id'] ?>#chapters" class="btn btn-secondary">
          Table of Contents
        </a>

        <div class="nav-next">
          <?php if (!empty($nav['next'])): ?>
            <a href="/reader?book_id=<?= $book['id'] ?>&chapter=<?= $nav['next']['chapter_number'] ?>" class="btn btn-primary">
              Chapter <?= $nav['next']['chapter_number'] ?>: <?= htmlspecialchars(substr($nav['next']['title'], 0, 22)) ?>... &rarr;
            </a>
          <?php else: ?>
            <a href="/book?id=<?= $book['id'] ?>" class="btn btn-primary">Complete Book Review &rarr;</a>
          <?php endif; ?>
        </div>
      </nav>
    </article>
  </main>

  <div id="toast-container" class="toast-container" aria-live="polite"></div>

  <!-- Reader JavaScript Engine -->
  <script src="/js/reader.js"></script>
</body>
</html>

