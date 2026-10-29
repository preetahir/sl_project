<?php
/**
 * LibrisCMS - Front Controller and Router
 * Demonstrates Modern PHP MVC & Laravel Routing Concepts
 */

// If running in built-in PHP web server, serve static files directly
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $file = __DIR__ . $path;
    if ($path !== '/' && file_exists($file) && !is_dir($file)) {
        return false;
    }
}

// Register PSR-4 Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'Libris\\';
    $baseDir = __DIR__ . '/../src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Initialize session and database
use Libris\Services\Auth;
use Libris\Config\Database;
use Libris\Controllers\HomeController;
use Libris\Controllers\BookController;
use Libris\Controllers\AdminController;
use Libris\Controllers\XmlController;
use Libris\Controllers\ApiController;

Auth::startSession();
Database::getConnection();

// Parse Request URI
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Route Matching (Laravel-style Router)
switch ($requestUri) {
    // Public Web Routes
    case '':
    case '/':
        (new HomeController())->index();
        break;

    case '/book':
        (new BookController())->show();
        break;

    case '/reader':
        (new BookController())->reader();
        break;

    case '/review/submit':
        (new BookController())->submitReview();
        break;

    // XML Tools & Feeds
    case '/xml/studio':
        (new XmlController())->studio();
        break;

    case '/xml/export':
        (new XmlController())->export();
        break;

    case '/xml/schema.xsd':
        (new XmlController())->downloadXsd();
        break;

    case '/xml/import':
        (new XmlController())->import();
        break;

    case '/feed.xml':
    case '/rss':
        (new XmlController())->rss();
        break;

    case '/sitemap.xml':
        (new XmlController())->sitemap();
        break;

    // Admin CMS Routes
    case '/admin':
        (new AdminController())->dashboard();
        break;

    case '/admin/book/new':
    case '/admin/book/edit':
        (new AdminController())->bookForm();
        break;

    case '/admin/book/save':
        (new AdminController())->saveBook();
        break;

    case '/admin/book/delete':
        (new AdminController())->deleteBook();
        break;

    case '/admin/chapters':
        (new AdminController())->chapters();
        break;

    case '/admin/chapter/save':
        (new AdminController())->saveChapter();
        break;

    case '/admin/chapter/delete':
        (new AdminController())->deleteChapter();
        break;

    case '/admin/toggle':
        (new AdminController())->toggleAdmin();
        break;

    // RESTful & XML API Web Services
    case '/api/books':
        (new ApiController())->books();
        break;

    case '/api/book':
        (new ApiController())->book();
        break;

    case '/api/books/xml':
        (new ApiController())->booksXml();
        break;

    case '/api/validate-xml':
        (new ApiController())->validateXml();
        break;

    case '/api/reviews':
        (new ApiController())->postReview();
        break;

    default:
        http_response_code(404);
        $pageTitle = "404 - Page Not Found";
        require __DIR__ . '/../views/layouts/404.php';
        break;
}

