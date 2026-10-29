<?php
namespace Libris\Models;

use Libris\Config\Database;

class Category {
    public static function all(): array {
        $pdo = Database::getConnection();
        $sql = "
            SELECT c.*, COUNT(b.id) AS books_count
            FROM categories c
            LEFT JOIN books b ON b.category_id = c.id
            GROUP BY c.id
            ORDER BY c.name ASC
        ";
        return $pdo->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $cat = $stmt->fetch();
        return $cat ?: null;
    }

    public static function findOrCreate(string $name, ?string $slug = null): int {
        $pdo = Database::getConnection();
        $slug = $slug ?: strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
        $stmt->execute([$slug]);
        $row = $stmt->fetch();
        if ($row) {
            return (int)$row['id'];
        }
        $insert = $pdo->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
        $insert->execute([$name, $slug, "Books in {$name}"]);
        return (int)$pdo->lastInsertId();
    }
}

