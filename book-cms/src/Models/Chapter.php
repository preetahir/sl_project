<?php
namespace Libris\Models;

use Libris\Config\Database;

class Chapter {
    public static function forBook(int $bookId): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM chapters WHERE book_id = ? ORDER BY chapter_number ASC");
        $stmt->execute([$bookId]);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM chapters WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByNumber(int $bookId, int $chapterNumber): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM chapters WHERE book_id = ? AND chapter_number = ?");
        $stmt->execute([$bookId, $chapterNumber]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(int $bookId, int $chapterNumber, string $title, string $content): int {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO chapters (book_id, chapter_number, title, content) VALUES (?, ?, ?, ?)");
        $stmt->execute([$bookId, $chapterNumber, $title, $content]);
        return (int)$pdo->lastInsertId();
    }

    public static function delete(int $id): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM chapters WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function getNavigation(int $bookId, int $currentNumber): array {
        $pdo = Database::getConnection();
        $prev = $pdo->prepare("SELECT id, chapter_number, title FROM chapters WHERE book_id = ? AND chapter_number < ? ORDER BY chapter_number DESC LIMIT 1");
        $prev->execute([$bookId, $currentNumber]);
        
        $next = $pdo->prepare("SELECT id, chapter_number, title FROM chapters WHERE book_id = ? AND chapter_number > ? ORDER BY chapter_number ASC LIMIT 1");
        $next->execute([$bookId, $currentNumber]);

        return [
            'prev' => $prev->fetch() ?: null,
            'next' => $next->fetch() ?: null
        ];
    }
}

