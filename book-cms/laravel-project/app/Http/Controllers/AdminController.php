<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Book;
use App\Models\Category;
use App\Models\Author;
use App\Models\Chapter;
use DOMDocument;

class AdminController extends Controller {
    public function dashboard() {
        $books = Book::with(['category', 'author'])
            ->withCount(['chapters', 'reviews'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total_books' => Book::count(),
            'total_chapters' => Chapter::count(),
            'total_categories' => Category::count(),
            'average_rating' => round(Book::avg('rating') ?: 5.0, 1)
        ];

        return view('admin.dashboard', compact('books', 'stats'));
    }

    public function create() {
        $categories = Category::orderBy('name')->get();
        $authors = Author::orderBy('name')->get();
        return view('admin.book-form', compact('categories', 'authors'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'author_id' => 'required|exists:authors,id',
            'isbn' => 'required|string|unique:books,isbn',
            'published_year' => 'required|integer|between:1800,2099',
            'price' => 'required|numeric|min:0',
            'language' => 'required|string|max:50',
            'summary' => 'required|string|min:10',
            'cover_image' => 'nullable|string',
            'featured' => 'nullable|boolean'
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . time();
        $validated['featured'] = $request->has('featured');

        $book = Book::create($validated);

        return redirect()->route('admin.dashboard')->with('success', "Book '{$book->title}' created successfully.");
    }

    public function edit(Book $book) {
        $categories = Category::orderBy('name')->get();
        $authors = Author::orderBy('name')->get();
        return view('admin.book-form', compact('book', 'categories', 'authors'));
    }

    public function update(Request $request, Book $book) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'author_id' => 'required|exists:authors,id',
            'isbn' => 'required|string|unique:books,isbn,' . $book->id,
            'published_year' => 'required|integer|between:1800,2099',
            'price' => 'required|numeric|min:0',
            'language' => 'required|string|max:50',
            'summary' => 'required|string|min:10',
            'cover_image' => 'nullable|string',
            'featured' => 'nullable|boolean'
        ]);

        $validated['featured'] = $request->has('featured');
        $book->update($validated);

        return redirect()->route('admin.dashboard')->with('success', "Book '{$book->title}' updated successfully.");
    }

    public function destroy(Book $book) {
        $title = $book->title;
        $book->delete();
        return redirect()->route('admin.dashboard')->with('success', "Book '{$title}' removed from catalog.");
    }

    public function chapters(Book $book) {
        $chapters = $book->chapters()->orderBy('chapter_number')->get();
        return view('admin.chapters', compact('book', 'chapters'));
    }

    public function storeChapter(Request $request, Book $book) {
        $validated = $request->validate([
            'chapter_number' => 'required|integer|min:1',
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10'
        ]);

        $book->chapters()->updateOrCreate(
            ['book_id' => $book->id, 'chapter_number' => $validated['chapter_number']],
            ['title' => $validated['title'], 'content' => $validated['content']]
        );

        return back()->with('success', 'Chapter saved successfully.');
    }

    public function destroyChapter(Chapter $chapter) {
        $bookId = $chapter->book_id;
        $chapter->delete();
        return back()->with('success', 'Chapter deleted successfully.');
    }

    public function xmlStudio() {
        return view('admin.xml-studio');
    }

    public function xmlImport(Request $request) {
        $xmlContent = $request->input('xml_content');
        if ($request->hasFile('xml_file')) {
            $xmlContent = file_get_contents($request->file('xml_file')->getRealPath());
        }

        if (empty(trim($xmlContent))) {
            return back()->withErrors(['xml' => 'Please provide valid XML markup or upload an XML file.']);
        }

        libxml_use_internal_errors(true);
        $dom = new DOMDocument('1.0', 'UTF-8');
        if (!@$dom->loadXML($xmlContent)) {
            $errors = array_map(fn($e) => "Line {$e->line}: " . trim($e->message), libxml_get_errors());
            libxml_clear_errors();
            return back()->withErrors(['xml' => implode(' | ', $errors)]);
        }

        // Import books
        $booksNodes = $dom->getElementsByTagName('book');
        $importedCount = 0;

        foreach ($booksNodes as $node) {
            $title = $node->getElementsByTagName('title')->item(0)->textContent ?? 'Untitled';
            $isbn = $node->getElementsByTagName('isbn')->item(0)->textContent ?? ('978-' . rand(100000000, 999999999));
            $genre = $node->getElementsByTagName('genre')->item(0)->textContent ?? 'General';
            $authorName = $node->getElementsByTagName('author')->item(0)->textContent ?? 'Unknown Author';
            $year = (int)($node->getElementsByTagName('year')->item(0)->textContent ?? date('Y'));
            $price = (float)($node->getElementsByTagName('price')->item(0)->textContent ?? 0.00);
            $summary = $node->getElementsByTagName('summary')->item(0)->textContent ?? '';

            $category = Category::firstOrCreate(['slug' => Str::slug($genre)], ['name' => $genre]);
            $author = Author::firstOrCreate(['name' => $authorName]);

            Book::updateOrCreate(
                ['isbn' => $isbn],
                [
                    'category_id' => $category->id,
                    'author_id' => $author->id,
                    'title' => $title,
                    'slug' => Str::slug($title) . '-' . rand(100, 999),
                    'published_year' => $year,
                    'price' => $price,
                    'language' => 'English',
                    'summary' => $summary,
                    'featured' => false
                ]
            );
            $importedCount++;
        }

        return redirect()->route('admin.dashboard')->with('success', "Imported {$importedCount} books successfully from XML.");
    }
}
