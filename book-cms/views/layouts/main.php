<?php
use Libris\Services\Auth;
$flash = Auth::getFlash();
$isAdmin = Auth::isAdmin();
$curPage = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="LibrisCMS - Responsive Modern Book Content Management System demonstrating HTML5, CSS3, JavaScript, PHP 8, Laravel Architecture, and XML.">
  <meta name="author" content="LibrisCMS">
  <title><?= $pageTitle ?? 'LibrisCMS - Book Content Management System' ?></title>
  
  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="/images/covers/web_arch.svg">

  <!-- Core & Component Stylesheets -->
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/admin.css">
  <link rel="stylesheet" href="/css/print.css" media="print">

  <!-- Google Font Preconnect for Typography -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Merriweather:ital,wght@0,300;0,400;0,700;1,300&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
</head>
<body class="site-body">
  <!-- Top Accessibility Bar / Skip link -->
  <a href="#main-content" class="skip-to-content">Skip to main content</a>

  <!-- Global Header Navigation -->
  <header class="site-header" id="site-header">
    <div class="header-container">
      <!-- Brand Logo -->
      <a href="/" class="brand-logo" aria-label="LibrisCMS Home">
        <div class="logo-icon">
          <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
            <path d="M8 7h8M8 11h6"/>
          </svg>
        </div>
        <div class="logo-text">
          <span class="logo-title">Libris<strong>CMS</strong></span>
          <span class="logo-badge">v2.5 • Full Stack</span>
        </div>
      </a>

      <!-- Desktop Navigation Links -->
      <nav class="desktop-nav" aria-label="Primary Navigation">
        <ul class="nav-links">
          <li><a href="/" class="nav-link <?= ($curPage === '/' || $curPage === '') ? 'active' : '' ?>">Explore Catalog</a></li>
          <li><a href="/xml/studio" class="nav-link <?= strpos($curPage, '/xml') !== false ? 'active' : '' ?>">XML Studio</a></li>
          <li><a href="/admin" class="nav-link <?= strpos($curPage, '/admin') !== false ? 'active' : '' ?>">CMS Admin</a></li>
          <li><a href="/feed.xml" target="_blank" class="nav-link nav-link-rss" title="RSS 2.0 Feed">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><circle cx="6.18" cy="17.82" r="2.18"/><path d="M4 4.44v2.83c7.03 0 12.73 5.7 12.73 12.73h2.83c0-8.59-6.97-15.56-15.56-15.56zm0 5.66v2.83c3.9 0 7.07 3.17 7.07 7.07h2.83c0-5.47-4.43-9.9-9.9-9.9z"/></svg>
            RSS Feed
          </a></li>
        </ul>
      </nav>

      <!-- Header Action Controls -->
      <div class="header-actions">
        <!-- Theme Toggle Button -->
        <button id="theme-toggle" class="btn-icon" aria-label="Toggle dark/light mode" title="Switch Theme">
          <svg class="sun-icon" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="5"/>
            <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
            <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
          </svg>
          <svg class="moon-icon" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
          </svg>
        </button>

        <!-- Admin Quick Mode Toggle -->
        <a href="/admin/toggle" class="btn btn-sm <?= $isAdmin ? 'btn-danger' : 'btn-outline' ?>" title="Toggle Admin Mode">
          <?= $isAdmin ? 'Admin Mode (Active)' : 'Enable Admin' ?>
        </a>

        <!-- Mobile Menu Toggle Button -->
        <button id="mobile-menu-toggle" class="btn-icon mobile-only" aria-label="Toggle mobile menu" aria-expanded="false">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="12" x2="21" y2="12"/>
            <line x1="3" y1="6" x2="21" y2="6"/>
            <line x1="3" y1="18" x2="21" y2="18"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile Slide-out Drawer Navigation -->
    <div id="mobile-drawer" class="mobile-drawer" aria-hidden="true">
      <div class="mobile-drawer-header">
        <span class="drawer-title">Navigation Menu</span>
        <button id="mobile-close" class="btn-icon" aria-label="Close menu">&times;</button>
      </div>
      <nav class="mobile-nav">
        <a href="/" class="mobile-nav-link">Explore Catalog</a>
        <a href="/xml/studio" class="mobile-nav-link">XML Studio</a>
        <a href="/admin" class="mobile-nav-link">CMS Admin</a>
        <a href="/xml/export" class="mobile-nav-link">Download XML Catalog</a>
        <a href="/feed.xml" class="mobile-nav-link" target="_blank">RSS 2.0 Feed</a>
        <a href="/sitemap.xml" class="mobile-nav-link" target="_blank">XML Sitemap</a>
      </nav>
      <div class="mobile-drawer-footer">
        <a href="/admin/toggle" class="btn btn-sm btn-block <?= $isAdmin ? 'btn-danger' : 'btn-primary' ?>">
          <?= $isAdmin ? 'Exit Admin Mode' : 'Enter Admin Mode' ?>
        </a>
      </div>
    </div>
    <div id="drawer-backdrop" class="drawer-backdrop"></div>
  </header>

  <!-- Global Flash Alerts Container -->
  <?php if ($flash): ?>
    <div class="flash-container">
      <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible" role="alert">
        <div class="alert-icon">
          <?php if ($flash['type'] === 'success'): ?>
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          <?php elseif ($flash['type'] === 'danger'): ?>
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
          <?php else: ?>
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <?php endif; ?>
        </div>
        <div class="alert-content"><?= $flash['message'] ?></div>
        <button class="alert-close" onclick="this.parentElement.remove()" aria-label="Close alert">&times;</button>
      </div>
    </div>
  <?php endif; ?>

  <!-- Main Body Content Block -->
  <main id="main-content" class="main-content">
    <?= $content ?? '' ?>
  </main>

  <!-- Global Footer -->
  <footer class="site-footer">
    <div class="footer-container">
      <div class="footer-grid">
        <div class="footer-col brand-col">
          <div class="footer-logo">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
              <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
            </svg>
            <span>Libris<strong>CMS</strong></span>
          </div>
          <p class="footer-desc">
            A next-generation Responsive Book Content Management System engineered for seamless reading, robust XML schema validation, and complete editorial workflows.
          </p>
          <div class="tech-pills">
            <span class="tech-pill">HTML5</span>
            <span class="tech-pill">CSS3 Fluid Grid</span>
            <span class="tech-pill">ES6+ AJAX</span>
            <span class="tech-pill">PHP 8.1 OOP</span>
            <span class="tech-pill">Laravel MVC</span>
            <span class="tech-pill">XML / XSD / XSLT</span>
          </div>
        </div>

        <div class="footer-col">
          <h4 class="footer-heading">Catalog Navigation</h4>
          <ul class="footer-links">
            <li><a href="/">All Books</a></li>
            <li><a href="/?featured=1">Featured Masterpieces</a></li>
            <li><a href="/admin/book/new">Add New Book (CMS)</a></li>
            <li><a href="/api/books" target="_blank">REST API Endpoint (JSON)</a></li>
            <li><a href="/api/books/xml" target="_blank">XML Web Service Endpoint</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <h4 class="footer-heading">XML Engineering</h4>
          <ul class="footer-links">
            <li><a href="/xml/studio">XML Studio &amp; Validator</a></li>
            <li><a href="/xml/export">Export Catalog to XML</a></li>
            <li><a href="/xml/schema.xsd">Download XSD Schema</a></li>
            <li><a href="/feed.xml" target="_blank">RSS 2.0 Feed</a></li>
            <li><a href="/sitemap.xml" target="_blank">XML Sitemap</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <h4 class="footer-heading">Syllabus Practical Coverage</h4>
          <ul class="footer-specs">
            <li><strong>Prac 1-3:</strong> Semantic HTML, Regex Validation, Array Search</li>
            <li><strong>Prac 4-6:</strong> PHP DOM Parsing, GET/POST Handling, CSV</li>
            <li><strong>Prac 7:</strong> Full DB CRUD, Transactions, Alterations</li>
            <li><strong>Prac 8-9:</strong> Laravel MVC Architecture, Eloquent, Blade</li>
            <li><strong>Prac 10:</strong> XML Storage, XSD Validation, XSLT Transform</li>
            <li><strong>Prac 11-12:</strong> REST &amp; XML Web Services, AJAX UI</li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> LibrisCMS Project. Demonstrating Full-Stack Web Technologies (HTML, CSS, JS, PHP, Laravel, XML).</p>
        <div class="footer-bottom-links">
          <a href="#site-header" class="back-to-top">Back to top &uarr;</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Quick Preview Modal Dialog -->
  <dialog id="quick-preview-modal" class="custom-dialog">
    <div class="dialog-card">
      <div class="dialog-header">
        <h3 id="modal-title" class="dialog-title">Book Quick Preview</h3>
        <button id="modal-close" class="btn-icon" aria-label="Close dialog">&times;</button>
      </div>
      <div id="modal-body" class="dialog-body">
        <!-- Injected via JavaScript AJAX -->
        <div class="loading-spinner"></div>
      </div>
    </div>
  </dialog>

  <!-- Toast Notification Container -->
  <div id="toast-container" class="toast-container" aria-live="polite"></div>

  <!-- Client-side Scripts -->
  <script src="/js/app.js"></script>
  <script src="/js/validation.js"></script>
</body>
</html>

