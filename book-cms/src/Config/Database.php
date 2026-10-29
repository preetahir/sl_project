<?php
namespace Libris\Config;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;
    private static string $dbPath = __DIR__ . '/../../database/libris.sqlite';

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dir = dirname(self::$dbPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            $firstInit = !file_exists(self::$dbPath) || filesize(self::$dbPath) === 0;

            try {
                self::$instance = new PDO('sqlite:' . self::$dbPath);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$instance->exec('PRAGMA foreign_keys = ON;');

                if ($firstInit) {
                    self::initSchema();
                    self::seedFromXml();
                }
            } catch (PDOException $e) {
                die("Database connection failed: " . htmlspecialchars($e->getMessage()));
            }
        }
        return self::$instance;
    }

    public static function initSchema(): void {
        $pdo = self::$instance;
        $schema = "
        CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            slug TEXT NOT NULL UNIQUE,
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS authors (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT,
            bio TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS books (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            category_id INTEGER NOT NULL,
            author_id INTEGER NOT NULL,
            title TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            isbn TEXT NOT NULL UNIQUE,
            published_year INTEGER NOT NULL,
            price REAL DEFAULT 0.00,
            rating REAL DEFAULT 5.0,
            language TEXT DEFAULT 'English',
            cover_image TEXT,
            summary TEXT NOT NULL,
            featured INTEGER DEFAULT 0,
            views_count INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
            FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS chapters (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            book_id INTEGER NOT NULL,
            chapter_number INTEGER NOT NULL,
            title TEXT NOT NULL,
            content TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS reviews (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            book_id INTEGER NOT NULL,
            user_name TEXT NOT NULL,
            user_email TEXT,
            rating INTEGER NOT NULL CHECK (rating >= 1 AND rating <= 5),
            comment TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
        );

        CREATE INDEX IF NOT EXISTS idx_books_slug ON books(slug);
        CREATE INDEX IF NOT EXISTS idx_books_isbn ON books(isbn);
        CREATE INDEX IF NOT EXISTS idx_books_category ON books(category_id);
        CREATE INDEX IF NOT EXISTS idx_chapters_book ON chapters(book_id, chapter_number);
        ";
        $pdo->exec($schema);
    }

    public static function seedFromXml(): void {
        $xmlFile = __DIR__ . '/../../xml/catalog.xml';
        if (!file_exists($xmlFile)) {
            return;
        }

        $xml = simplexml_load_file($xmlFile);
        if ($xml === false) {
            return;
        }

        $pdo = self::$instance;
        $pdo->beginTransaction();

        try {
            foreach ($xml->book as $b) {
                // Category
                $catName = (string)$b->category;
                $catSlug = (string)($b->category['slug'] ?? strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $catName)));
                
                $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
                $stmt->execute([$catSlug]);
                $cat = $stmt->fetch();
                if (!$cat) {
                    $insertCat = $pdo->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
                    $insertCat->execute([$catName, $catSlug, "Books related to {$catName}"]);
                    $catId = $pdo->lastInsertId();
                } else {
                    $catId = $cat['id'];
                }

                // Author
                $authName = (string)$b->author->name;
                $authEmail = (string)($b->author->email ?? '');
                $authBio = (string)($b->author->bio ?? '');

                $stmt = $pdo->prepare("SELECT id FROM authors WHERE name = ?");
                $stmt->execute([$authName]);
                $author = $stmt->fetch();
                if (!$author) {
                    $insertAuth = $pdo->prepare("INSERT INTO authors (name, email, bio) VALUES (?, ?, ?)");
                    $insertAuth->execute([$authName, $authEmail, $authBio]);
                    $authorId = $pdo->lastInsertId();
                } else {
                    $authorId = $author['id'];
                }

                // Book
                $title = (string)$b->title;
                $slug = (string)($b->slug ?? strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $title)));
                $isbn = (string)$b->isbn;
                $year = (int)$b->publishedYear;
                $price = (float)($b->price ?? 0);
                $rating = (float)($b->rating ?? 5.0);
                $language = (string)($b->language ?? 'English');
                $cover = (string)($b->coverImage ?? 'default_book.svg');
                $summary = (string)$b->summary;
                $featured = ((string)($b['featured'] ?? 'false') === 'true') ? 1 : 0;

                $stmt = $pdo->prepare("SELECT id FROM books WHERE isbn = ?");
                $stmt->execute([$isbn]);
                $existingBook = $stmt->fetch();
                if (!$existingBook) {
                    $insertBook = $pdo->prepare("
                        INSERT INTO books (category_id, author_id, title, slug, isbn, published_year, price, rating, language, cover_image, summary, featured)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $insertBook->execute([$catId, $authorId, $title, $slug, $isbn, $year, $price, $rating, $language, $cover, $summary, $featured]);
                    $bookId = $pdo->lastInsertId();

                    // Chapters
                    if (isset($b->chapters->chapter)) {
                        $insertChap = $pdo->prepare("INSERT INTO chapters (book_id, chapter_number, title, content) VALUES (?, ?, ?, ?)");
                        foreach ($b->chapters->chapter as $ch) {
                            $insertChap->execute([
                                $bookId,
                                (int)$ch->chapterNumber,
                                (string)$ch->title,
                                (string)$ch->content
                            ]);
                        }
                    }

                    // Sample Review
                    $insertReview = $pdo->prepare("INSERT INTO reviews (book_id, user_name, user_email, rating, comment) VALUES (?, ?, ?, ?, ?)");
                    $insertReview->execute([$bookId, 'Elena Vance', 'elena@reader.org', 5, 'Exceptional content organization and clarity. Highly recommended!']);
                }
            }
            $pdo->commit();
        } catch (\Exception $e) {
            $pdo->rollBack();
        }
    }
}

