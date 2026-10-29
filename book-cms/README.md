# LibrisCMS — Responsive Modern Book Content Management System
> **A Comprehensive Full-Stack Web Development Capstone Project**  
> *Engineered with Semantic HTML5, Fluid CSS3, ES6+ JavaScript, Modern PHP 8.1 OOP, Laravel MVC Architecture, and Enterprise XML Data Interchange.*

---

## 📖 Project Overview

**LibrisCMS** is an enterprise-grade, fully functional Responsive Book Content Management System designed to demonstrate core and advanced concepts across the modern web development spectrum. Built with zero external framework dependencies for its primary runtime while including a complete **Laravel 11/10 MVC companion blueprint**, the platform integrates book catalog curation, chapter authoring, an ergonomic distraction-free reading mode, user reviews, RESTful Web Services, and rigorous XML Schema (XSD) validation and XSLT transformations.

---

## 🌟 Key Features & Capabilities

- **Responsive Multi-Device Layout**: Fluid adaptation from 320px mobile displays to 4K ultra-wide monitors using CSS Grid subgrids and Flexbox.
- **Distraction-Free Chapter Reader**:
  - Live scroll-position reading progress bar.
  - Reader atmosphere palette: Light (Daytime), Sepia (Paper Warmth), and Dark (Midnight Slate).
  - Typographic font toggling: Serif (*Merriweather*), Sans-serif (*Plus Jakarta Sans*), and Monospace (*Fira Code*).
  - Fluid text resizing ($85\% \to 140\%$) with real-time UI synchronization.
  - Chapter bookmarking persisted via browser `localStorage`.
  - Fullscreen API toggle for immersive reading.
- **Interactive Storefront & Catalog**:
  - Instant client-side live search by title, author, ISBN, or synopsis.
  - Dynamic sorting (Newest, Rating, Price low-to-high/high-to-low, Title A-Z).
  - Multi-criteria category pills with live inventory counters.
  - Layout switcher: CSS Grid View vs. Compact List View.
  - AJAX Quick Preview Modal with chapter listings.
- **Editorial CMS & Administration Dashboard**:
  - Inventory metrics: Total Books, Published Chapters, Categories, and Average Ratings.
  - Complete CRUD operations: Add Book, Edit Book, Delete Book, Manage Chapters.
  - Live card preview synchronized with form inputs in real-time.
- **XML Engineering & Validation Studio**:
  - Master XML catalog (`xml/catalog.xml`) storing complete book metadata.
  - Strict XML Schema Definition (`xml/schema.xsd`) validating data types, element hierarchies, publication year bounds ($1800 - 2099$), and ISBN regex patterns.
  - Server-side XML validation reporting exact line numbers and column errors.
  - Bidirectional data exchange: Export database to XML and Import validated XML files into SQLite.
  - Dynamic XSLT transformation (`xml/transform.xsl`) converting raw XML into styled HTML via PHP's `XSLTProcessor`.
  - Syndicated RSS 2.0 XML Feed (`/feed.xml`) and Google-compliant XML Sitemap (`/sitemap.xml`).
- **RESTful & XML Web Services**:
  - `GET /api/books` (JSON with pagination and filtering).
  - `GET /api/book?id={id}` (Detailed JSON with chapters and reviews).
  - `GET /api/books/xml` (Direct XML Web Service export).
  - `POST /api/validate-xml` (AJAX schema validation diagnostics).

---

## 🛠️ Topic-by-Topic Technical Architecture

### 1. Semantic HTML5
- **Semantic Structure**: Built exclusively with contextual tags: `<header>`, `<nav>`, `<main>`, `<article>`, `<section>`, `<aside>`, `<footer>`, `<figure>`, `<time>`, and `<dialog>`.
- **Accessibility & ARIA**:
  - Skip-to-content accessibility anchor (`.skip-to-content`).
  - ARIA attributes: `aria-expanded`, `aria-label`, `aria-current="page"`, `aria-live="polite"` for toast messages, and `role="tablist"` for tabbed panels.
- **Modern Form Elements**: `<input type="search">`, `type="email"`, `type="number"`, `type="file"`, alongside pattern matching attributes.

### 2. Modern Fluid CSS3
- **Design System Tokens (CSS Custom Properties)**: Complete palette and layout tokens defined in `:root` (primary, accents, surfaces, elevations, font families, radii).
- **Dark & Light Mode**: First-class dark theme tokens (`[data-theme="dark"]`) with automatic persistence and OS preference detection (`prefers-color-scheme`).
- **Responsive Layout Engines**:
  - **CSS Grid**: `grid-template-columns: repeat(auto-fill, minmax(280px, 1fr))` for fluid auto-wrapping without rigid media queries.
  - **Flexbox**: Multi-axis alignment for sticky toolbars, navigation headers, metric counters, and card footers.
- **Fluid Typography**: Dynamic headings calculated via `clamp(1.85rem, 3.5vw, 2.75rem)` for seamless cross-device scaling.
- **Print Optimization (`print.css`)**: Dedicated `@media print` rules hiding interactive UI, navigation, and badges while optimizing typographic contrast for physical printing or PDF generation.

### 3. Asynchronous JavaScript (ES6+)
- **DOM & Event Architecture**: Debounced live input search listeners, off-canvas navigation drawer, and view mode persistence.
- **Fetch API & AJAX**: Asynchronous Quick Preview loading, live XML validation testing, and review submission without full page reloads.
- **Local Storage API**: Persistent client-side state for theme preference, catalog layout view, reader atmosphere, font size, and chapter bookmarks.
- **Syllabus Algorithmic Functions (`validation.js`)**:
  - *Practical 1*: Regex validation for mobile numbers (starting with 9, 8, 7, 6) and email address structure.
  - *Practical 2*: Array word search loop returning element index.
  - *Practical 3*: String extraction between first and last occurrence of vowel `'a'`.

### 4. Modern PHP 8.1 Backend & OOP MVC
- **Architecture**: Strict Separation of Concerns with PSR-4 autoloading (`src/Config`, `src/Models`, `src/Controllers`, `src/Services`).
- **Database Layer**: SQLite PDO engine (`src/Config/Database.php`) featuring automatic schema generation (`schema.sql`), foreign key enforcement, indexing, and transactional XML seeder.
- **Security & Hardening**:
  - Prepared statements with bound parameters preventing SQL injection.
  - CSRF token generation and verification (`Auth::csrfField()`, `Auth::validateCsrf()`).
  - Input sanitization via `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` guarding against Cross-Site Scripting (XSS).
- **Models**: Active-Record style query methods (`Book::all()`, `Book::find()`, `Book::create()`, `Book::update()`, `Book::delete()`, `Book::getStats()`).

### 5. Laravel Framework Integration (`laravel-project/`)
- Complete companion Laravel 11/10 project structure:
  - **Routing**: `routes/web.php` with Route Groups, Named Routes, and Route Model Binding (`/books/{book:slug}`).
  - **Eloquent ORM**: `app/Models/Book.php` and `Category.php` establishing `belongsTo`, `hasMany` relationships and attribute casting.
  - **Blade Templating**: Clean layouts with `@extends('layouts.app')`, `@section('content')`, `@yield()`, `@auth`, `@foreach`, and `@include`.
  - **Database Migrations**: Declarative schema migrations with foreign key cascades and seeders.

### 6. Enterprise XML Interoperability
- **Catalog Document (`xml/catalog.xml`)**: Hierarchical, well-formed representation of books, authors, genres, and chapter contents.
- **Schema Validation (`xml/schema.xsd`)**: Comprehensive XSD schema enforcing structural integrity, mandatory nodes, and regex constraints:
  ```xml
  <xs:element name="isbn">
    <xs:simpleType>
      <xs:restriction base="xs:string">
        <xs:pattern value="(97[89]-?[0-9]{1,5}-?[0-9]{1,7}-?[0-9]{1,6}-?[0-9])|[0-9]{9}[0-9X]"/>
      </xs:restriction>
    </xs:simpleType>
  </xs:element>
  ```
- **XSLT 1.0 Transformation (`xml/transform.xsl`)**: Dynamically transforms catalog XML into a responsive HTML report using PHP's `XSLTProcessor`.
- **Syndication Feeds**: Automated RSS 2.0 (`/feed.xml`) and XML Sitemap (`/sitemap.xml`) generation.

---

## 📁 Project Directory Structure

```text
book-cms/
├── public/                     # Public Webroot
│   ├── css/
│   │   ├── style.css           # Core responsive stylesheet, variables & grid
│   │   ├── reader.css          # Distraction-free reader mode & themes
│   │   ├── admin.css           # Admin dashboard, tables & XML studio
│   │   └── print.css           # Print & PDF export stylesheet
│   ├── js/
│   │   ├── app.js              # Theme, drawer, search & AJAX modal
│   │   ├── reader.js           # Reading progress, font controls & bookmark
│   │   └── validation.js       # Client validation & syllabus algorithms
│   ├── images/
│   │   └── covers/             # Curated SVG artwork book covers
│   └── index.php               # Front controller & request router
├── src/                        # Core PHP MVC Backend
│   ├── Config/
│   │   └── Database.php        # SQLite PDO connection & auto-seeder
│   ├── Models/
│   │   ├── Book.php            # Book model with CRUD & search filters
│   │   ├── Category.php        # Category model & relations
│   │   ├── Chapter.php         # Chapter model & navigation
│   │   └── Review.php          # Community review model
│   ├── Controllers/
│   │   ├── HomeController.php  # Public storefront controller
│   │   ├── BookController.php  # Details, reader & review controller
│   │   ├── AdminController.php # CMS management & CRUD controller
│   │   ├── XmlController.php   # XML studio, export, import, XSLT, RSS
│   │   └── ApiController.php   # RESTful JSON & XML API endpoints
│   └── Services/
│       ├── XmlService.php      # DOMDocument, XSD validator, XSLT engine
│       └── Auth.php            # Session auth, CSRF tokens & flash alerts
├── views/                      # Presentation Views
│   ├── layouts/
│   │   ├── main.php            # Master responsive layout & header/footer
│   │   └── 404.php             # Custom 404 error template
│   ├── home/
│   │   └── index.php           # Hero, featured titles & catalog grid
│   ├── books/
│   │   ├── show.php            # Book details, TOC & review submission
│   │   └── reader.php          # Fullscreen chapter reading view
│   ├── admin/
│   │   ├── dashboard.php       # Operational dashboard & inventory table
│   │   ├── book-form.php       # Add/edit book form with live preview
│   │   ├── chapters.php        # Chapter authoring & sequence manager
│   │   └── xml-tools.php       # Interactive XML validation studio
├── xml/                        # XML Data & Schemas
│   ├── catalog.xml             # Master XML book catalog
│   ├── schema.xsd              # XML Schema Definition (XSD)
│   ├── sample_import.xml       # Valid sample XML for import testing
│   ├── sample_invalid.xml      # Invalid sample XML for error diagnostics
│   └── transform.xsl           # XSLT transformation stylesheet
├── database/                   # Database Storage & DDL
│   ├── schema.sql              # Database DDL schema
│   └── libris.sqlite           # SQLite database file
├── laravel-project/            # Dedicated Laravel 11/10 Blueprint
│   ├── app/                    # Models & Controllers
│   ├── routes/                 # Web & API routes
│   ├── database/               # Migrations & Seeders
│   ├── resources/views/        # Blade templates
│   └── README_LARAVEL.md       # Laravel execution guide
├── start-server.bat            # 1-Click launcher using portable PHP
├── run_server.py               # Universal Python runner
└── README.md                   # Complete documentation
```

---

## ⚡ How to Run the Website

### Method 1: One-Click Windows Batch Launcher (Recommended)
Simply double-click:
```bat
start-server.bat
```
*This script automatically locates the pre-configured portable PHP runtime, initializes the SQLite database, boots the web server on `http://127.0.0.1:8000`, and opens your default browser automatically.*

### Method 2: Python Universal Launcher
```powershell
python run_server.py
```

### Method 3: Direct PHP Command
```powershell
& "C:\Users\PREET AHIR\Downloads\php-portable\php.exe" -S 127.0.0.1:8000 -t public public/index.php
```
Open **[http://127.0.0.1:8000](http://127.0.0.1:8000)** in any browser.

---

## 🌐 API & Web Service Endpoints

| HTTP Method | Route | Description | Response Type |
|---|---|---|---|
| `GET` | `/api/books` | Paginated and filtered book catalog | `application/json` |
| `GET` | `/api/book?id={id}` | Detailed book record with chapters and reviews | `application/json` |
| `GET` | `/api/books/xml` | Direct XML Web Service export of catalog | `application/xml` |
| `POST` | `/api/validate-xml` | AJAX XSD schema validation diagnostics | `application/json` |
| `GET` | `/feed.xml` | Syndicated RSS 2.0 XML Feed | `application/rss+xml` |
| `GET` | `/sitemap.xml` | Standard Search Engine XML Sitemap | `application/xml` |
| `GET` | `/xml/export` | Download full catalog as `.xml` attachment | `application/xml` |
| `GET` | `/xml/schema.xsd` | Download official XSD validation schema | `application/xml` |

---

## 🎓 Academic Coursework Practical Mapping (1IT305)

| Practical # | Subject Practical Aim | Implementation in LibrisCMS |
|---|---|---|
| **Prac 1** | JavaScript validation (Username/Password, Mobile starting with 9/8, Email checking `@` and `.`) | Implemented in `public/js/validation.js` with regex functions and real-time form event interception. |
| **Prac 2** | JavaScript loop searching a word in sentence array returning index | Implemented in `validation.js` (`searchWordInSentence()`). |
| **Prac 3** | JavaScript finding vowel `'a'` first & last occurrence and string between | Implemented in `validation.js` (`extractBetweenVowels()`). |
| **Prac 4** | PHP DOM parser & web scraping | Implemented in `src/Services/XmlService.php` using `DOMDocument` and XPath queries. |
| **Prac 5** | PHP GET and POST methods | Demonstrated across `HomeController.php`, `BookController.php`, search query filtering, and review submission. |
| **Prac 6** | PHP CSV file parsing & processing | Implemented alongside data interchange utilities. |
| **Prac 7** | PHP Database CRUD with UI (Insert, Delete, Update, Retrieve, Schema alteration) | Implemented in `AdminController.php` and `Book.php` with SQLite transactional CRUD and management tables. |
| **Prac 8 & 9** | Laravel web pages & CRUD operations | Implemented in `laravel-project/` featuring named routes, Eloquent relationships, Blade templates, and form requests. |
| **Prac 10** | PHP application to store and display records using XML | Implemented in `xml/catalog.xml`, `Database::seedFromXml()`, and `XmlController.php`. |
| **Prac 11** | PHP Web Services | Implemented via RESTful endpoints (`/api/books`, `/api/book`) and XML web service endpoints (`/api/books/xml`, `/feed.xml`). |
| **Prac 12** | AJAX application with dynamic DOM update | Implemented in `public/js/app.js` with live book preview modals and interactive XSD validation in `views/admin/xml-tools.php`. |

---

## 🛡️ License & Academic Integrity
Developed as a showcase of modern web engineering principles. All code follows clean coding conventions, semantic standards, and responsive web design best practices.

