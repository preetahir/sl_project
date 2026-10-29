<?php
namespace Libris\Controllers;

use Libris\Models\Book;
use Libris\Models\Category;

class HomeController {
    public function index(): void {
        $categorySlug = $_GET['category'] ?? '';
        $searchTerm = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'newest';
        $minRating = $_GET['min_rating'] ?? '';
        $featuredOnly = isset($_GET['featured']) && $_GET['featured'] === '1';

        $filters = [
            'category' => $categorySlug,
            'search' => $searchTerm,
            'sort' => $sort,
            'min_rating' => $minRating,
            'featured' => $featuredOnly
        ];

        $books = Book::all($filters);
        $categories = Category::all();
        $featuredBooks = Book::all(['featured' => true]);
        $stats = Book::getStats();

        $pageTitle = "LibrisCMS - Responsive Book Content Management System";
        require __DIR__ . '/../../views/home/index.php';
    }
}

