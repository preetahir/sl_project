<?php
namespace Libris\Models;

use Libris\Config\Database;
use PDO;

class Book {
    public static function all(array $filters = []): array {
        $pdo = Database::getConnection();
        $sql = "
            SELECT b.*, c.name AS category_name, c.slug AS category_slug, 
                   a.name AS author_name, a.bio AS author_bio,
                   (SELECT COUNT(*) FROM chapters ch WHERE ch.book_id = b.id) AS chapters_count,
                   (SELECT COUNT(*) FROM reviews r WHERE r.book_id = b.id) AS reviews_count
            FROM books b
            JOIN categories c ON b.category_id = c.id
            JOIN authors a ON b.author_id = a.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (b.title LIKE ? OR a.name LIKE ? OR b.isbn LIKE ? OR b.summary LIKE ?)";
            $term = '%' . $filters['search'] . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        if (!empty($filters['category'])) {
            $sql .= " AND c.slug = ?";
            $params[] = $filters['category'];
        }

        if (!empty($filters['featured'])) {
            $sql .= " AND b.featured = 1";
        }

        if (!empty($filters['min_rating'])) {
            $sql .= " AND b.rating >= ?";
            $params[] = (float)$filters['min_rating'];
        }

        // Sorting
        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'rating':
                $sql .= " ORDER BY b.rating DESC, b.id DESC";
                break;
            case 'title_asc':
                $sql .= " ORDER BY b.title ASC";
                break;
            case 'price_asc':
                $sql .= " ORDER BY b.price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY b.price DESC";
                break;
            case 'year':
                $sql .= " ORDER BY b.published_year DESC";
                break;
            case 'newest':
            default:
                $sql .= " ORDER BY b.id DESC";
                break;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array {
        $pdo = Database::getConnection();
        $sql = "
            SELECT b.*, c.name AS category_name, c.slug AS category_slug, 
                   a.name AS author_name, a.email AS author_email, a.bio AS author_bio
            FROM books b
            JOIN categories c ON b.category_id = c.id
            JOIN authors a ON b.author_id = a.id
            WHERE b.id = ?
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $book = $stmt->fetch();
        return $book ?: null;
    }

    public static function findBySlug(string $slug): ?array {
        $pdo = Database::getConnection();
        $sql = "
            SELECT b.*, c.name AS category_name, c.slug AS category_slug, 
                   a.name AS author_name, a.email AS author_email, a.bio AS author_bio
            FROM books b
            JOIN categories c ON b.category_id = c.id
            JOIN authors a ON b.author_id = a.id
            WHERE b.slug = ?
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$slug]);
        $book = $stmt->fetch();
        return $book ?: null;
    }

    public static function incrementViews(int $id): void {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE books SET views_count = views_count + 1 WHERE id = ?");
        $stmt->execute([$id]);
    }

    public static function create(array $data): int {
        $pdo = Database::getConnection();
        $sql = "
            INSERT INTO books (category_id, author_id, title, slug, isbn, published_year, price, rating, language, cover_image, summary, featured)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        $slug = $data['slug'] ?? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['title'])));
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['category_id'],
            $data['author_id'],
            $data['title'],
            $slug,
            $data['isbn'],
            $data['published_year'],
            $data['price'] ?? 0.00,
            $data['rating'] ?? 5.0,
            $data['language'] ?? 'English',
            $data['cover_image'] ?? 'default_book.svg',
            $data['summary'],
            isset($data['featured']) ? 1 : 0
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $pdo = Database::getConnection();
        $sql = "
            UPDATE books SET 
                category_id = ?,
                author_id = ?,
                title = ?,
                slug = ?,
                isbn = ?,
                published_year = ?,
                price = ?,
                rating = ?,
                language = ?,
                cover_image = ?,
                summary = ?,
                featured = ?
            WHERE id = ?
        ";
        $slug = $data['slug'] ?? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['title'])));
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $data['category_id'],
            $data['author_id'],
            $data['title'],
            $slug,
            $data['isbn'],
            $data['published_year'],
            $data['price'] ?? 0.00,
            $data['rating'] ?? 5.0,
            $data['language'] ?? 'English',
            $data['cover_image'] ?? 'default_book.svg',
            $data['summary'],
            isset($data['featured']) ? 1 : 0,
            $id
        ]);
    }

    public static function delete(int $id): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function getStats(): array {
        $pdo = Database::getConnection();
        $booksCount = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
        $chaptersCount = $pdo->query("SELECT COUNT(*) FROM chapters")->fetchColumn();
        $categoriesCount = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
        $reviewsCount = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
        $avgRating = $pdo->query("SELECT ROUND(AVG(rating), 2) FROM books")->fetchColumn();

        return [
            'total_books' => (int)$booksCount,
            'total_chapters' => (int)$chaptersCount,
            'total_categories' => (int)$categoriesCount,
            'total_reviews' => (int)$reviewsCount,
            'average_rating' => (float)$avgRating
        ];
    }
}

