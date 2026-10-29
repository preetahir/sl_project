<?php
namespace Libris\Models;

use Libris\Config\Database;

class Review {
    public static function forBook(int $bookId): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM reviews WHERE book_id = ? ORDER BY created_at DESC");
        $stmt->execute([$bookId]);
        return $stmt->fetchAll();
    }

    public static function create(int $bookId, string $userName, ?string $userEmail, int $rating, string $comment): int {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO reviews (book_id, user_name, user_email, rating, comment) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$bookId, $userName, $userEmail, $rating, $comment]);

        // Recalculate average rating for book
        $avgStmt = $pdo->prepare("SELECT ROUND(AVG(rating), 1) FROM reviews WHERE book_id = ?");
        $avgStmt->execute([$bookId]);
        $avg = $avgStmt->fetchColumn();

        if ($avg) {
            $upd = $pdo->prepare("UPDATE books SET rating = ? WHERE id = ?");
            $upd->execute([(float)$avg, $bookId]);
        }

        return (int)$pdo->lastInsertId();
    }
}

