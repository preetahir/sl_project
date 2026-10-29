<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;

class BookController extends Controller {
    public function index(Request $request) {
        $query = Book::with(['category', 'author']);

        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function($q) use ($term) {
                $q->where('title', 'like', $term)
                  ->orWhere('isbn', 'like', $term);
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $sort = $request->get('sort', 'newest');
        if ($sort === 'rating') {
            $query->orderBy('rating', 'desc');
        } elseif ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } else {
            $query->latest();
        }

        $books = $query->paginate(9);
        $categories = Category::withCount('books')->get();
        $featuredBooks = Book::where('featured', true)->take(3)->get();

        return view('books.index', compact('books', 'categories', 'featuredBooks'));
    }

    public function show(Book $book) {
        $book->increment('views_count');
        $book->load(['category', 'author', 'chapters', 'reviews']);
        $relatedBooks = Book::where('category_id', $book->category_id)
                            ->where('id', '!=', $book->id)
                            ->take(4)->get();

        return view('books.show', compact('book', 'relatedBooks'));
    }

    public function reader(Book $book, $chapterNumber = 1) {
        $allChapters = $book->chapters;
        $currentChapter = $allChapters->firstWhere('chapter_number', (int)$chapterNumber) ?? $allChapters->first();

        return view('books.reader', compact('book', 'allChapters', 'currentChapter'));
    }

    public function storeReview(Request $request, Book $book) {
        $validated = $request->validate([
            'user_name' => 'required|string|max:100',
            'user_email' => 'nullable|email|max:150',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5'
        ]);

        $book->reviews()->create($validated);

        // Recalculate book rating
        $avgRating = $book->reviews()->avg('rating');
        $book->update(['rating' => round($avgRating, 1)]);

        return back()->with('success', 'Thank you! Your book review has been published.');
    }
}

