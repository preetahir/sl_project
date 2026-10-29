<?php
namespace Libris\Controllers;

use Libris\Models\Book;
use Libris\Models\Category;
use Libris\Models\Chapter;
use Libris\Services\Auth;
use Libris\Config\Database;

class AdminController {
    public function dashboard(): void {
        Auth::startSession();
        $stats = Book::getStats();
        $books = Book::all();
        $categories = Category::all();

        $pageTitle = "Admin CMS Dashboard - LibrisCMS";
        require __DIR__ . '/../../views/admin/dashboard.php';
    }

    public function bookForm(): void {
        Auth::startSession();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $book = $id ? Book::find($id) : null;
        $categories = Category::all();

        // Get authors
        $pdo = Database::getConnection();
        $authors = $pdo->query("SELECT * FROM authors ORDER BY name ASC")->fetchAll();

        $pageTitle = ($book ? "Edit Book: " . htmlspecialchars($book['title']) : "Add New Book") . " - Admin";
        require __DIR__ . '/../../views/admin/book-form.php';
    }

    public function saveBook(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /admin");
            exit;
        }

        if (!Auth::validateCsrf($_POST['csrf_token'] ?? '')) {
            Auth::setFlash('danger', 'Security validation (CSRF token) failed.');
            header("Location: /admin");
            exit;
        }

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $title = trim($_POST['title'] ?? '');
        $isbn = trim($_POST['isbn'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $authorId = (int)($_POST['author_id'] ?? 0);
        $year = (int)($_POST['published_year'] ?? date('Y'));
        $price = (float)($_POST['price'] ?? 0.00);
        $rating = (float)($_POST['rating'] ?? 5.0);
        $language = trim($_POST['language'] ?? 'English');
        $coverImage = trim($_POST['cover_image'] ?? 'default_book.svg');
        $summary = trim($_POST['summary'] ?? '');
        $featured = isset($_POST['featured']) ? 1 : 0;

        // Backend validation
        if (empty($title) || empty($isbn) || empty($summary) || $categoryId <= 0) {
            Auth::setFlash('danger', 'Please complete all required fields (Title, ISBN, Category, Summary).');
            header("Location: " . ($id ? "/admin/book/edit?id=" . $id : "/admin/book/new"));
            exit;
        }

        // Validate ISBN format
        if (!preg_match('/^(97[89]-?[0-9]{1,5}-?[0-9]{1,7}-?[0-9]{1,6}-?[0-9])|[0-9]{9}[0-9X]$/', $isbn)) {
            Auth::setFlash('warning', 'Notice: The ISBN format does not follow standard ISBN-10 or ISBN-13 patterns.');
        }

        $data = [
            'category_id' => $categoryId,
            'author_id' => $authorId,
            'title' => $title,
            'isbn' => $isbn,
            'published_year' => $year,
            'price' => $price,
            'rating' => $rating,
            'language' => $language,
            'cover_image' => $coverImage,
            'summary' => $summary,
            'featured' => $featured
        ];

        if ($id) {
            Book::update($id, $data);
            Auth::setFlash('success', "Book '{$title}' updated successfully!");
        } else {
            $newId = Book::create($data);
            Auth::setFlash('success', "Book '{$title}' created successfully!");
        }

        header("Location: /admin");
        exit;
    }

    public function deleteBook(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /admin");
            exit;
        }

        if (!Auth::validateCsrf($_POST['csrf_token'] ?? '')) {
            Auth::setFlash('danger', 'CSRF verification failed.');
            header("Location: /admin");
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            Book::delete($id);
            Auth::setFlash('success', 'Book and associated chapters deleted successfully.');
        }

        header("Location: /admin");
        exit;
    }

    public function chapters(): void {
        $bookId = (int)($_GET['book_id'] ?? 0);
        $book = Book::find($bookId);

        if (!$book) {
            Auth::setFlash('danger', 'Specified book was not found.');
            header("Location: /admin");
            exit;
        }

        $chapters = Chapter::forBook($bookId);
        $pageTitle = "Manage Chapters - " . htmlspecialchars($book['title']);
        require __DIR__ . '/../../views/admin/chapters.php';
    }

    public function saveChapter(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /admin");
            exit;
        }

        if (!Auth::validateCsrf($_POST['csrf_token'] ?? '')) {
            Auth::setFlash('danger', 'CSRF verification failed.');
            header("Location: /admin");
            exit;
        }

        $bookId = (int)($_POST['book_id'] ?? 0);
        $chapterNumber = (int)($_POST['chapter_number'] ?? 1);
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if ($bookId <= 0 || empty($title) || empty($content)) {
            Auth::setFlash('danger', 'Chapter title and content are mandatory.');
            header("Location: /admin/chapters?book_id=" . $bookId);
            exit;
        }

        Chapter::create($bookId, $chapterNumber, $title, $content);
        Auth::setFlash('success', "Chapter {$chapterNumber} added successfully!");
        header("Location: /admin/chapters?book_id=" . $bookId);
        exit;
    }

    public function deleteChapter(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /admin");
            exit;
        }

        if (!Auth::validateCsrf($_POST['csrf_token'] ?? '')) {
            Auth::setFlash('danger', 'CSRF verification failed.');
            header("Location: /admin");
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $bookId = (int)($_POST['book_id'] ?? 0);

        if ($id > 0) {
            Chapter::delete($id);
            Auth::setFlash('success', 'Chapter deleted successfully.');
        }

        header("Location: /admin/chapters?book_id=" . $bookId);
        exit;
    }

    public function toggleAdmin(): void {
        Auth::startSession();
        if (Auth::isAdmin()) {
            Auth::logout();
            Auth::setFlash('info', 'Logged out of Admin mode.');
        } else {
            Auth::loginAsAdmin();
            Auth::setFlash('success', 'Switched to Admin mode. You now have full CMS write privileges.');
        }
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/admin'));
        exit;
    }
}

