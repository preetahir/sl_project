<?php
namespace Libris\Controllers;

use Libris\Models\Book;
use Libris\Models\Chapter;
use Libris\Models\Review;
use Libris\Services\Auth;

class BookController {
    public function show(): void {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $slug = $_GET['slug'] ?? null;

        $book = null;
        if ($id) {
            $book = Book::find($id);
        } elseif ($slug) {
            $book = Book::findBySlug($slug);
        }

        if (!$book) {
            http_response_code(404);
            $pageTitle = "Book Not Found - LibrisCMS";
            require __DIR__ . '/../../views/layouts/404.php';
            return;
        }

        Book::incrementViews((int)$book['id']);
        $chapters = Chapter::forBook((int)$book['id']);
        $reviews = Review::forBook((int)$book['id']);
        $relatedBooks = Book::all(['category' => $book['category_slug']]);

        // Filter out self from related
        $relatedBooks = array_filter($relatedBooks, fn($b) => (int)$b['id'] !== (int)$book['id']);

        $pageTitle = htmlspecialchars($book['title']) . " - LibrisCMS";
        require __DIR__ . '/../../views/books/show.php';
    }

    public function reader(): void {
        $bookId = (int)($_GET['book_id'] ?? 0);
        $chapterNumber = (int)($_GET['chapter'] ?? 1);

        $book = Book::find($bookId);
        if (!$book) {
            header("Location: /");
            exit;
        }

        $allChapters = Chapter::forBook($bookId);
        $currentChapter = Chapter::findByNumber($bookId, $chapterNumber);

        if (!$currentChapter && !empty($allChapters)) {
            $currentChapter = $allChapters[0];
            $chapterNumber = (int)$currentChapter['chapter_number'];
        }

        $nav = Chapter::getNavigation($bookId, $chapterNumber);

        $pageTitle = "Reading: " . htmlspecialchars($currentChapter['title'] ?? $book['title']) . " - LibrisCMS";
        require __DIR__ . '/../../views/books/reader.php';
    }

    public function submitReview(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /");
            exit;
        }

        if (!Auth::validateCsrf($_POST['csrf_token'] ?? '')) {
            Auth::setFlash('danger', 'Security validation (CSRF token) failed. Please try again.');
            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/'));
            exit;
        }

        $bookId = (int)($_POST['book_id'] ?? 0);
        $userName = trim($_POST['user_name'] ?? '');
        $userEmail = trim($_POST['user_email'] ?? '');
        $rating = (int)($_POST['rating'] ?? 5);
        $comment = trim($_POST['comment'] ?? '');

        // Validation
        if (empty($userName) || empty($comment) || $bookId <= 0) {
            Auth::setFlash('danger', 'Please provide your name, a rating, and your review comments.');
            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/'));
            exit;
        }

        // Email validation
        if (!empty($userEmail) && !filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            Auth::setFlash('danger', 'Please provide a valid email address.');
            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/'));
            exit;
        }

        Review::create($bookId, $userName, $userEmail, $rating, $comment);
        Auth::setFlash('success', 'Thank you! Your book review has been published.');
        header("Location: /book?id=" . $bookId . "#reviews");
        exit;
    }
}

