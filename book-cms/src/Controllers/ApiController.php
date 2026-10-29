<?php
namespace Libris\Controllers;

use Libris\Models\Book;
use Libris\Models\Chapter;
use Libris\Models\Review;
use Libris\Services\XmlService;
use Libris\Services\Auth;

class ApiController {
    public function books(): void {
        header('Content-Type: application/json; charset=utf-8');
        $filters = [
            'search' => $_GET['search'] ?? '',
            'category' => $_GET['category'] ?? '',
            'sort' => $_GET['sort'] ?? 'newest',
            'min_rating' => $_GET['min_rating'] ?? ''
        ];

        $books = Book::all($filters);
        echo json_encode([
            'status' => 'success',
            'count' => count($books),
            'data' => $books
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public function book(): void {
        header('Content-Type: application/json; charset=utf-8');
        $id = (int)($_GET['id'] ?? 0);
        $book = Book::find($id);

        if (!$book) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Book not found']);
            exit;
        }

        $chapters = Chapter::forBook($id);
        $reviews = Review::forBook($id);
        $book['chapters'] = $chapters;
        $book['reviews'] = $reviews;

        echo json_encode(['status' => 'success', 'data' => $book], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public function booksXml(): void {
        header('Content-Type: application/xml; charset=utf-8');
        echo XmlService::exportCatalogToXml();
        exit;
    }

    public function validateXml(): void {
        header('Content-Type: application/json; charset=utf-8');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $xmlContent = $input['xml'] ?? '';

        if (empty($xmlContent)) {
            echo json_encode(['valid' => false, 'errors' => ['XML payload was empty.']]);
            exit;
        }

        $res = XmlService::validateXml($xmlContent);
        echo json_encode($res);
        exit;
    }

    public function postReview(): void {
        header('Content-Type: application/json; charset=utf-8');
        
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $bookId = (int)($input['book_id'] ?? 0);
        $name = trim($input['user_name'] ?? '');
        $email = trim($input['user_email'] ?? '');
        $rating = (int)($input['rating'] ?? 5);
        $comment = trim($input['comment'] ?? '');

        if ($bookId <= 0 || empty($name) || empty($comment)) {
            http_response_code(422);
            echo json_encode(['status' => 'error', 'message' => 'Name, rating and comments are mandatory.']);
            exit;
        }

        $reviewId = Review::create($bookId, $name, $email, $rating, $comment);
        $updatedReviews = Review::forBook($bookId);

        echo json_encode([
            'status' => 'success',
            'message' => 'Review successfully recorded!',
            'review_id' => $reviewId,
            'reviews' => $updatedReviews
        ]);
        exit;
    }
}

