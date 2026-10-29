<?php
namespace Libris\Services;

use DOMDocument;
use SimpleXMLElement;
use XSLTProcessor;
use Libris\Config\Database;
use Libris\Models\Book;
use Libris\Models\Chapter;
use Libris\Models\Category;
use Exception;

class XmlService {
    private static string $schemaPath = __DIR__ . '/../../xml/schema.xsd';
    private static string $xsltPath = __DIR__ . '/../../xml/transform.xsl';

    /**
     * Validate an XML string against our XSD schema using DOMDocument
     */
    public static function validateXml(string $xmlContent): array {
        if (empty(trim($xmlContent))) {
            return ['valid' => false, 'errors' => ['XML content cannot be empty.']];
        }

        libxml_use_internal_errors(true);
        libxml_clear_errors();

        $dom = new DOMDocument('1.0', 'UTF-8');
        if (!@$dom->loadXML($xmlContent)) {
            $errors = [];
            foreach (libxml_get_errors() as $error) {
                $errors[] = sprintf("Line %d: %s", $error->line, trim($error->message));
            }
            libxml_clear_errors();
            return ['valid' => false, 'errors' => $errors];
        }

        if (!file_exists(self::$schemaPath)) {
            return ['valid' => false, 'errors' => ['Schema file not found on server.']];
        }

        if (!$dom->schemaValidate(self::$schemaPath)) {
            $errors = [];
            foreach (libxml_get_errors() as $error) {
                $errors[] = sprintf("Line %d, Column %d: %s", $error->line, $error->column, trim($error->message));
            }
            libxml_clear_errors();
            return ['valid' => false, 'errors' => $errors];
        }

        return ['valid' => true, 'errors' => []];
    }

    /**
     * Export database books into formatted XML conforming to schema.xsd
     */
    public static function exportCatalogToXml(?array $bookIds = null): string {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $root = $dom->createElement('bookCatalog');
        $dom->appendChild($root);

        // Metadata node
        $meta = $dom->createElement('metadata');
        $meta->appendChild($dom->createElement('systemName', 'LibrisCMS Book Content Management System'));
        $meta->appendChild($dom->createElement('version', '2.5.0'));
        $meta->appendChild($dom->createElement('generatedAt', date('c')));

        $books = Book::all();
        if ($bookIds !== null) {
            $books = array_filter($books, fn($b) => in_array((int)$b['id'], $bookIds));
        }

        $meta->appendChild($dom->createElement('totalBooks', (string)count($books)));
        $root->appendChild($meta);

        foreach ($books as $b) {
            $bookElem = $dom->createElement('book');
            $bookElem->setAttribute('id', (string)$b['id']);
            $bookElem->setAttribute('featured', $b['featured'] ? 'true' : 'false');

            $bookElem->appendChild($dom->createElement('title', htmlspecialchars($b['title'], ENT_XML1)));
            $bookElem->appendChild($dom->createElement('slug', htmlspecialchars($b['slug'], ENT_XML1)));
            $bookElem->appendChild($dom->createElement('isbn', htmlspecialchars($b['isbn'], ENT_XML1)));

            // Author
            $authorElem = $dom->createElement('author');
            $authorElem->setAttribute('id', (string)$b['author_id']);
            $authorElem->appendChild($dom->createElement('name', htmlspecialchars($b['author_name'], ENT_XML1)));
            if (!empty($b['author_email'])) {
                $authorElem->appendChild($dom->createElement('email', htmlspecialchars($b['author_email'], ENT_XML1)));
            }
            if (!empty($b['author_bio'])) {
                $authorElem->appendChild($dom->createElement('bio', htmlspecialchars($b['author_bio'], ENT_XML1)));
            }
            $bookElem->appendChild($authorElem);

            // Category
            $catElem = $dom->createElement('category', htmlspecialchars($b['category_name'], ENT_XML1));
            $catElem->setAttribute('slug', htmlspecialchars($b['category_slug'], ENT_XML1));
            $bookElem->appendChild($catElem);

            // Published Year & Metadata
            $bookElem->appendChild($dom->createElement('publishedYear', (string)$b['published_year']));
            $bookElem->appendChild($dom->createElement('price', number_format((float)$b['price'], 2, '.', '')));
            $bookElem->appendChild($dom->createElement('rating', number_format((float)$b['rating'], 1, '.', '')));
            $bookElem->appendChild($dom->createElement('language', htmlspecialchars($b['language'] ?? 'English', ENT_XML1)));
            $bookElem->appendChild($dom->createElement('coverImage', htmlspecialchars($b['cover_image'] ?? 'default_book.svg', ENT_XML1)));
            $bookElem->appendChild($dom->createElement('summary', htmlspecialchars($b['summary'], ENT_XML1)));

            // Chapters
            $chapters = Chapter::forBook((int)$b['id']);
            if (!empty($chapters)) {
                $chaptersElem = $dom->createElement('chapters');
                foreach ($chapters as $ch) {
                    $chapterElem = $dom->createElement('chapter');
                    $chapterElem->setAttribute('id', (string)$ch['id']);
                    $chapterElem->appendChild($dom->createElement('chapterNumber', (string)$ch['chapter_number']));
                    $chapterElem->appendChild($dom->createElement('title', htmlspecialchars($ch['title'], ENT_XML1)));
                    $chapterElem->appendChild($dom->createElement('content', htmlspecialchars($ch['content'], ENT_XML1)));
                    $chaptersElem->appendChild($chapterElem);
                }
                $bookElem->appendChild($chaptersElem);
            }

            $root->appendChild($bookElem);
        }

        return $dom->saveXML();
    }

    /**
     * Import books from an XML string into SQLite after XSD validation
     */
    public static function importFromXml(string $xmlContent): array {
        $validation = self::validateXml($xmlContent);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'errors' => $validation['errors'],
                'imported_count' => 0
            ];
        }

        $xml = simplexml_load_string($xmlContent);
        if ($xml === false) {
            return [
                'success' => false,
                'errors' => ['Failed to parse XML content.'],
                'imported_count' => 0
            ];
        }

        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        $imported = 0;
        try {
            foreach ($xml->book as $b) {
                // Category
                $catName = (string)$b->category;
                $catSlug = (string)($b->category['slug'] ?? strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $catName)));
                $catId = Category::findOrCreate($catName, $catSlug);

                // Author
                $authName = (string)$b->author->name;
                $authEmail = (string)($b->author->email ?? '');
                $authBio = (string)($b->author->bio ?? '');

                $stmt = $pdo->prepare("SELECT id FROM authors WHERE name = ?");
                $stmt->execute([$authName]);
                $auth = $stmt->fetch();
                if (!$auth) {
                    $insAuth = $pdo->prepare("INSERT INTO authors (name, email, bio) VALUES (?, ?, ?)");
                    $insAuth->execute([$authName, $authEmail, $authBio]);
                    $authorId = (int)$pdo->lastInsertId();
                } else {
                    $authorId = (int)$auth['id'];
                }

                // Check existing book by ISBN
                $isbn = (string)$b->isbn;
                $title = (string)$b->title;
                $slug = (string)($b->slug ?? strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $title)));
                $year = (int)$b->publishedYear;
                $price = (float)($b->price ?? 0);
                $rating = (float)($b->rating ?? 5.0);
                $language = (string)($b->language ?? 'English');
                $cover = (string)($b->coverImage ?? 'default_book.svg');
                $summary = (string)$b->summary;
                $featured = ((string)($b['featured'] ?? 'false') === 'true') ? 1 : 0;

                $check = $pdo->prepare("SELECT id FROM books WHERE isbn = ?");
                $check->execute([$isbn]);
                $existing = $check->fetch();

                if ($existing) {
                    $bookId = (int)$existing['id'];
                    $upd = $pdo->prepare("
                        UPDATE books SET 
                            category_id = ?, author_id = ?, title = ?, slug = ?, published_year = ?,
                            price = ?, rating = ?, language = ?, cover_image = ?, summary = ?, featured = ?
                        WHERE id = ?
                    ");
                    $upd->execute([$catId, $authorId, $title, $slug, $year, $price, $rating, $language, $cover, $summary, $featured, $bookId]);
                } else {
                    $ins = $pdo->prepare("
                        INSERT INTO books (category_id, author_id, title, slug, isbn, published_year, price, rating, language, cover_image, summary, featured)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $ins->execute([$catId, $authorId, $title, $slug, $isbn, $year, $price, $rating, $language, $cover, $summary, $featured]);
                    $bookId = (int)$pdo->lastInsertId();
                }

                // Chapters
                if (isset($b->chapters->chapter)) {
                    // Remove existing chapters for clean sync
                    $pdo->prepare("DELETE FROM chapters WHERE book_id = ?")->execute([$bookId]);
                    $insChap = $pdo->prepare("INSERT INTO chapters (book_id, chapter_number, title, content) VALUES (?, ?, ?, ?)");
                    foreach ($b->chapters->chapter as $ch) {
                        $insChap->execute([
                            $bookId,
                            (int)$ch->chapterNumber,
                            (string)$ch->title,
                            (string)$ch->content
                        ]);
                    }
                }
                $imported++;
            }
            $pdo->commit();
            return ['success' => true, 'errors' => [], 'imported_count' => $imported];
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['success' => false, 'errors' => [$e->getMessage()], 'imported_count' => 0];
        }
    }

    /**
     * Transform XML using XSLTProcessor into HTML
     */
    public static function transformWithXslt(string $xmlContent): string {
        if (!class_exists('XSLTProcessor')) {
            return '<div class="alert alert-danger">PHP XSL extension is not enabled.</div>';
        }

        $xmlDom = new DOMDocument();
        if (!@$xmlDom->loadXML($xmlContent)) {
            return '<div class="alert alert-danger">Invalid XML for XSL transformation.</div>';
        }

        $xslDom = new DOMDocument();
        if (!file_exists(self::$xsltPath) || !@$xslDom->load(self::$xsltPath)) {
            return '<div class="alert alert-danger">Could not load XSLT stylesheet.</div>';
        }

        $proc = new XSLTProcessor();
        $proc->importStylesheet($xslDom);
        $result = $proc->transformToXml($xmlDom);

        return $result ?: '<div class="alert alert-warning">XSLT transformation returned empty result.</div>';
    }

    /**
     * Generate standard RSS 2.0 XML Feed
     */
    public static function generateRssFeed(string $baseUrl = ''): string {
        $books = Book::all(['featured' => 0]);
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"></rss>');
        $channel = $xml->addChild('channel');
        $channel->addChild('title', 'LibrisCMS - New Book Releases &amp; Chapters');
        $channel->addChild('link', $baseUrl . '/');
        $channel->addChild('description', 'Latest publications and updates from the Libris Content Management System.');
        $channel->addChild('language', 'en-us');
        $channel->addChild('pubDate', date(DATE_RSS));

        foreach (array_slice($books, 0, 10) as $b) {
            $item = $channel->addChild('item');
            $item->addChild('title', htmlspecialchars($b['title'], ENT_XML1, 'UTF-8'));
            $item->addChild('link', $baseUrl . '/book?slug=' . urlencode($b['slug']));
            $item->addChild('description', htmlspecialchars($b['summary'], ENT_XML1, 'UTF-8'));
            $item->addChild('author', htmlspecialchars($b['author_name'], ENT_XML1, 'UTF-8'));
            $item->addChild('category', htmlspecialchars($b['category_name'], ENT_XML1, 'UTF-8'));
            $item->addChild('guid', $baseUrl . '/book?id=' . $b['id']);
            $item->addChild('pubDate', date(DATE_RSS, strtotime($b['created_at'])));
        }

        return $xml->asXML();
    }

    /**
     * Generate standard SEO XML Sitemap
     */
    public static function generateSitemap(string $baseUrl = ''): string {
        $books = Book::all();
        $categories = Category::all();

        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');

        // Homepage
        $url = $xml->addChild('url');
        $url->addChild('loc', $baseUrl . '/');
        $url->addChild('changefreq', 'daily');
        $url->addChild('priority', '1.0');

        // Categories
        foreach ($categories as $cat) {
            $url = $xml->addChild('url');
            $url->addChild('loc', $baseUrl . '/?category=' . urlencode($cat['slug']));
            $url->addChild('changefreq', 'weekly');
            $url->addChild('priority', '0.8');
        }

        // Books
        foreach ($books as $b) {
            $url = $xml->addChild('url');
            $url->addChild('loc', $baseUrl . '/book?slug=' . urlencode($b['slug']));
            $url->addChild('changefreq', 'monthly');
            $url->addChild('priority', '0.9');
        }

        return $xml->asXML();
    }
}
