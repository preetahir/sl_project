<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Author;
use App\Models\Book;
use App\Models\Chapter;
use DOMDocument;

class BookDatabaseSeeder extends Seeder {
    public function run(): void {
        $xmlPath = base_path('../xml/catalog.xml');
        if (!file_exists($xmlPath)) {
            $xmlPath = base_path('xml/catalog.xml');
        }

        if (!file_exists($xmlPath)) {
            return;
        }

        $dom = new DOMDocument();
        $dom->load($xmlPath);
        $books = $dom->getElementsByTagName('book');

        foreach ($books as $b) {
            $title = $b->getElementsByTagName('title')->item(0)->textContent ?? '';
            $authorName = $b->getElementsByTagName('author')->item(0)->textContent ?? 'Unknown';
            $isbn = $b->getElementsByTagName('isbn')->item(0)->textContent ?? '';
            $genre = $b->getElementsByTagName('genre')->item(0)->textContent ?? 'General';
            $year = (int)($b->getElementsByTagName('year')->item(0)->textContent ?? 2024);
            $price = (float)($b->getElementsByTagName('price')->item(0)->textContent ?? 0.0);
            $rating = (float)($b->getElementsByTagName('rating')->item(0)->textContent ?? 5.0);
            $summary = $b->getElementsByTagName('summary')->item(0)->textContent ?? '';

            $category = Category::firstOrCreate(
                ['slug' => Str::slug($genre)],
                ['name' => $genre, 'description' => "Books in {$genre}"]
            );

            $author = Author::firstOrCreate(
                ['name' => $authorName],
                ['bio' => "Acclaimed author of {$title}"]
            );

            $book = Book::firstOrCreate(
                ['isbn' => $isbn],
                [
                    'category_id' => $category->id,
                    'author_id' => $author->id,
                    'title' => $title,
                    'slug' => Str::slug($title),
                    'published_year' => $year,
                    'price' => $price,
                    'rating' => $rating,
                    'language' => 'English',
                    'cover_image' => 'default_book.svg',
                    'summary' => $summary,
                    'featured' => ($rating >= 4.7)
                ]
            );

            // Chapters
            $chapters = $b->getElementsByTagName('chapter');
            foreach ($chapters as $ch) {
                $num = (int)$ch->getAttribute('number');
                $chTitle = $ch->getAttribute('title');
                $content = $ch->textContent;

                Chapter::firstOrCreate(
                    ['book_id' => $book->id, 'chapter_number' => $num],
                    ['title' => $chTitle ?: "Chapter {$num}", 'content' => $content]
                );
            }
        }
    }
}
